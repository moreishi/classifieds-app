<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\Review;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReputationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReputationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReputationService::class);
    }

    private function makeListing(User $seller): Listing
    {
        return Listing::factory()->create([
            'user_id' => $seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
        ]);
    }

    private function makeUserOver7Days(): User
    {
        return User::factory()->create([
            'created_at' => now()->subDays(14),
            'gcash_verified_at' => now()->subDays(7),
        ]);
    }

    #[Test]
    public function it_calculates_tiers_correctly(): void
    {
        $this->assertEquals('newbie', ReputationService::calculateTier(0));
        $this->assertEquals('newbie', ReputationService::calculateTier(99));
        $this->assertEquals('verified', ReputationService::calculateTier(100));
        $this->assertEquals('verified', ReputationService::calculateTier(499));
        $this->assertEquals('trusted', ReputationService::calculateTier(500));
        $this->assertEquals('trusted', ReputationService::calculateTier(999));
        $this->assertEquals('pro', ReputationService::calculateTier(1000));
    }

    #[Test]
    public function it_recalculates_seller_reputation(): void
    {
        $seller = $this->makeUserOver7Days();
        $buyer = $this->makeUserOver7Days();
        $listing = $this->makeListing($seller);

        Review::forceCreate([
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'reviewer_id' => $buyer->id,
            'rating' => 5,
            'comment' => 'Great seller!',
        ]);

        $this->service->recalculateSellerReputation($seller);
        $seller->refresh();

        // With one reviewer at 14 days old (multiplier 0.467):
        // weighted_sum = 5 * 0.467 = 2.33, total_weight = 0.467
        // effective_avg = 2.33 / 0.467 = 5
        // points = 5 * 20 * sqrt(1) = 100
        $this->assertEquals(100, $seller->reputation_points);
        $this->assertEquals('verified', $seller->reputation_tier);
    }

    #[Test]
    public function it_awards_buyer_points_on_completed_purchase(): void
    {
        $buyer = $this->makeUserOver7Days();
        $seller = User::factory()->create();
        $this->service->awardBuyerPoints($buyer, $seller);

        $buyer->refresh();
        $this->assertEquals(50, $buyer->buyer_points);
    }

    #[Test]
    public function it_blocks_new_accounts_from_earning_reputation(): void
    {
        $seller = User::factory()->create([
            'created_at' => now(),
            'gcash_verified_at' => now(),
        ]);
        $buyer = User::factory()->create([
            'created_at' => now(),
            'gcash_verified_at' => now(),
        ]);

        $this->assertFalse(ReputationService::isEligible($buyer));

        $listing = $this->makeListing($seller);
        Review::forceCreate([
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'reviewer_id' => $buyer->id,
            'rating' => 5,
        ]);

        $this->service->recalculateSellerReputation($seller);
        $seller->refresh();

        // trustMultiplier = 0 (under 7 days)
        // Weighted rating = 5 * 0 = 0
        // points = 0
        $this->assertEquals(0, $seller->reputation_points);
        $this->assertEquals('newbie', $seller->reputation_tier);
    }

    #[Test]
    public function it_blocks_ungcash_verified_accounts(): void
    {
        $buyer = User::factory()->create([
            'created_at' => now()->subDays(30),
            'gcash_verified_at' => null,
        ]);

        $this->assertFalse(ReputationService::isEligible($buyer));
    }

    #[Test]
    public function tier_uses_max_of_seller_and_buyer_points(): void
    {
        $user = $this->makeUserOver7Days();

        // Give high buyer points but no seller points
        $user->update(['buyer_points' => 600, 'reputation_points' => 50]);

        $this->service->recalculateTier($user);
        $user->refresh();

        // Max = 600 → trusted
        $this->assertEquals('trusted', $user->reputation_tier);

        // Give high seller points
        $user->update(['reputation_points' => 1200]);

        $this->service->recalculateTier($user);
        $user->refresh();

        // Max = 1200 → pro
        $this->assertEquals('pro', $user->reputation_tier);
    }

    #[Test]
    public function it_generates_star_html(): void
    {
        $html = ReputationService::starHtml(4.5);
        $this->assertStringContainsString('&#9733;', $html);
        $this->assertStringContainsString('&#9734;', $html);
        $this->assertStringContainsString('text-yellow-400', $html);
        $this->assertStringNotContainsString('text-gray-300', $html);

        $html0 = ReputationService::starHtml(0.0);
        $this->assertStringNotContainsString('text-yellow-400', $html0);
        $this->assertStringContainsString('text-gray-300', $html0);

        $html5 = ReputationService::starHtml(5.0);
        $this->assertStringContainsString('text-yellow-400', $html5);
        $this->assertStringNotContainsString('text-gray-300', $html5);
    }

    #[Test]
    public function buyer_points_are_capped_at_50_per_seller(): void
    {
        $buyer = $this->makeUserOver7Days();
        $seller = User::factory()->create();

        // Simulate 51 completed transactions (1 over the cap)
        for ($i = 0; $i < 51; $i++) {
            \App\Models\TransactionReceipt::forceCreate([
                'listing_id' => $this->makeListing($seller)->id,
                'seller_id' => $seller->id,
                'buyer_email' => $buyer->email,
                'buyer_name' => $buyer->name,
                'reference_number' => 'TXN-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'amount' => 100,
                'status' => 'completed',
                'receipt_sent_at' => now(),
            ]);
        }

        // Call awardBuyerPoints — should be denied because 51 > 50 cap
        $this->service->awardBuyerPoints($buyer, $seller);
        $buyer->refresh();

        // No points awarded (blocked by cap)
        $this->assertEquals(0, $buyer->buyer_points);
    }

    #[Test]
    public function trust_multiplier_scales_with_age(): void
    {
        $newUser = User::factory()->create(['created_at' => now()->subDays(3)]);
        $this->assertEquals(0.0, ReputationService::trustMultiplier($newUser));

        $youngUser = User::factory()->create(['created_at' => now()->subDays(14)]);
        $this->assertEqualsWithDelta(14 / 30, ReputationService::trustMultiplier($youngUser), 0.01);

        $oldUser = User::factory()->create(['created_at' => now()->subDays(60)]);
        $this->assertEquals(1.0, ReputationService::trustMultiplier($oldUser));
    }
}
