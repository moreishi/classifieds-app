<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Region;
use App\Models\User;
use App\Services\CreditService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $transactionService;
    private CreditService $creditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = app(TransactionService::class);
        $this->creditService = app(CreditService::class);
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
        $seller = $this->createSeller();
        $buyer = $this->createSeller();
        $this->creditService->deposit($buyer, 1000, 'test_deposit');

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

        $this->assertEquals(500, $buyer->fresh()->credit_balance);

        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $buyer->id,
            'amount' => -500,
            'type' => 'offer_accepted',
        ]);

        $this->assertEquals('sold', $listing->fresh()->status);
        $this->assertNotNull($listing->fresh()->sold_at);

        $this->assertEquals('accepted', $offer->fresh()->status);
    }

    #[Test]
    public function it_declines_other_pending_offers_when_one_is_accepted(): void
    {
        $seller = $this->createSeller();
        $buyer1 = $this->createSeller();
        $buyer2 = $this->createSeller();
        $this->creditService->deposit($buyer1, 1000, 'test_deposit');

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
        $seller = $this->createSeller();
        $buyer = $this->createSeller();
        $this->creditService->deposit($buyer, 1000, 'test_deposit');

        $listing = $this->makeActiveListing($seller);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $this->transactionService->acceptOffer($offer);

        $this->expectException(\RuntimeException::class);

        $this->transactionService->acceptOffer($offer->fresh());
    }

    #[Test]
    public function it_fails_when_buyer_has_insufficient_credits(): void
    {
        $seller = $this->createSeller();
        $buyer = $this->createSeller();

        $listing = $this->makeActiveListing($seller);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $this->expectException(\RuntimeException::class);

        $this->transactionService->acceptOffer($offer);
    }
}
