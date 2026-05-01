<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\CreditTransaction;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    private CreditService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CreditService::class);
    }

    private function makeListing(User $user): Listing
    {
        return Listing::factory()->create([
            'user_id' => $user->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function it_can_deposit_credits(): void
    {
        $user = $this->createSeller();
        $this->assertEquals(0, $user->credit_balance);

        $this->service->deposit($user, 500, 'test_deposit');

        $this->assertEquals(500, $user->fresh()->credit_balance);
        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $user->id,
            'amount' => 500,
            'type' => 'test_deposit',
        ]);
    }

    #[Test]
    public function it_can_charge_for_a_listing(): void
    {
        $user = $this->createSeller();
        $listing = $this->makeListing($user);

        $this->service->deposit($user, 500, 'test_deposit');

        $this->service->chargeForListing($user, $listing);

        $this->assertEquals(1, $user->fresh()->free_listings_used);
        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $user->id,
            'amount' => 0,
            'type' => 'listing_fee',
        ]);

        $tx = \App\Models\CreditTransaction::where('user_id', $user->id)
            ->where('type', 'listing_fee')
            ->where('amount', 0)
            ->first();
        $this->assertNotNull($tx);
        $this->assertStringContainsString('Free listing used', $tx->notes);
    }

    #[Test]
    public function it_uses_free_listing_when_available(): void
    {
        $user = $this->createSeller();
        $listing = $this->makeListing($user);

        $this->assertTrue($this->service->canPostListing($user));
        $this->assertEquals(1, $this->service->freeListingsRemaining($user));

        $this->service->chargeForListing($user, $listing);

        $this->assertEquals(1, $user->fresh()->free_listings_used);
        $this->assertEquals(0, $user->fresh()->credit_balance);
    }

    #[Test]
    public function it_can_post_when_user_has_enough_credits(): void
    {
        $user = $this->createSeller(['credit_balance' => CreditService::LISTING_FEE]);

        $this->assertTrue($this->service->canPostListing($user));
    }

    #[Test]
    public function it_blocks_posting_when_user_has_no_credits_and_no_free_listings(): void
    {
        $user = $this->createSeller([
            'free_listings_used' => 1,
            'credit_balance' => 0,
        ]);

        $this->assertFalse($this->service->canPostListing($user));
    }

    #[Test]
    public function it_respects_reputation_tier_free_listing_limits(): void
    {
        $this->assertEquals(1, CreditService::freeListingsLimit('newbie'));
        $this->assertEquals(2, CreditService::freeListingsLimit('verified'));
        $this->assertEquals(3, CreditService::freeListingsLimit('trusted'));
        $this->assertEquals(5, CreditService::freeListingsLimit('pro'));
    }

    #[Test]
    public function it_calculates_free_listings_remaining(): void
    {
        $user = $this->createSeller([
            'reputation_tier' => 'trusted',
            'free_listings_used' => 1,
        ]);

        $this->assertEquals(2, $this->service->freeListingsRemaining($user));
    }

    #[Test]
    public function it_processes_referral_bonus(): void
    {
        $referrer = $this->createSeller();
        $referrer->update(['referral_code' => 'TESTCODE1']);
        $newUser = $this->createSeller();

        $this->service->processReferral($newUser, 'TESTCODE1');

        $this->assertEquals($referrer->id, $newUser->fresh()->referred_by);
        $this->assertEquals(CreditService::REFERRAL_BONUS, $referrer->fresh()->credit_balance);
        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $referrer->id,
            'amount' => CreditService::REFERRAL_BONUS,
            'type' => 'referral_bonus',
        ]);
    }

    #[Test]
    public function it_does_not_self_refer(): void
    {
        $user = $this->createSeller();
        $user->update(['referral_code' => 'SELFCODE']);

        $this->service->processReferral($user, 'SELFCODE');

        $this->assertNull($user->fresh()->referred_by);
        $this->assertEquals(0, $user->fresh()->credit_balance);
    }

    #[Test]
    public function it_generates_unique_referral_codes(): void
    {
        $code1 = CreditService::generateReferralCode();
        $code2 = CreditService::generateReferralCode();

        $this->assertNotEmpty($code1);
        $this->assertNotEmpty($code2);
        $this->assertNotEquals($code1, $code2);
        $this->assertEquals(8, strlen($code1));
    }
}
