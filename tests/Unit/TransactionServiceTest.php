<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Region;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = app(TransactionService::class);
    }

    private function makeActiveListing(User $seller): Listing
    {
        return Listing::factory()->create([
            'user_id' => $seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function it_accepts_an_offer_and_creates_receipt(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = $this->makeActiveListing($seller);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $receipt = $this->transactionService->acceptOffer($offer);

        $this->assertNotNull($receipt);
        $this->assertEquals($listing->id, $receipt->listing_id);
        $this->assertEquals(500, $receipt->amount);
        $this->assertEquals('completed', $receipt->status);
        $this->assertStringStartsWith('ISK-', $receipt->reference_number);

        // No credits should be deducted — buyer pays seller directly
        $this->assertEquals(0, $buyer->fresh()->credit_balance);

        $this->assertEquals('sold', $listing->fresh()->status);
        $this->assertNotNull($listing->fresh()->sold_at);
        $this->assertEquals('accepted', $offer->fresh()->status);
    }

    #[Test]
    public function it_declines_other_pending_offers_when_one_is_accepted(): void
    {
        $seller = User::factory()->create();
        $buyer1 = User::factory()->create();
        $buyer2 = User::factory()->create();
        $listing = $this->makeActiveListing($seller);

        $offer1 = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer1->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $offer2 = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer2->id,
            'seller_id' => $seller->id,
            'amount' => 400,
            'status' => 'pending',
        ]);

        $this->transactionService->acceptOffer($offer1);

        $this->assertEquals('accepted', $offer1->fresh()->status);
        $this->assertEquals('declined', $offer2->fresh()->status);
    }

    #[Test]
    public function it_rejects_accepting_an_already_accepted_offer(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = $this->makeActiveListing($seller);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $this->transactionService->acceptOffer($offer);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->transactionService->acceptOffer($offer->fresh());
    }

    #[Test]
    public function it_accepts_offer_without_needing_buyer_credits(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = $this->makeActiveListing($seller);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        // Should not throw despite buyer having 0 credits
        $receipt = $this->transactionService->acceptOffer($offer);
        $this->assertNotNull($receipt);
    }

    #[Test]
    public function it_awards_buyer_points_on_acceptance(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create([
            'gcash_verified_at' => now()->subDays(10), // bypass anti-cheat
            'created_at' => now()->subDays(10),
        ]);
        $listing = $this->makeActiveListing($seller);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $this->transactionService->acceptOffer($offer);

        $buyer->refresh();
        $seller->refresh();

        // Buyer should have buyer_points now
        $this->assertGreaterThan(0, $buyer->buyer_points);
    }
}
