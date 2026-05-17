<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OffersInboxTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create();
        $this->buyer = User::factory()->create();

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);
    }

    private function createOffer(array $overrides = []): Offer
    {
        return Offer::create(array_merge([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 50000,
            'status' => 'pending',
        ], $overrides));
    }

    #[Test]
    public function seller_can_view_received_offers(): void
    {
        $this->createOffer();

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\OffersInbox::class)
            ->assertSee($this->listing->title);
    }

    #[Test]
    public function buyer_can_view_sent_offers(): void
    {
        $this->createOffer();

        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OffersInbox::class)
            ->set('tab', 'sent')
            ->assertSee($this->listing->title);
    }

    #[Test]
    public function seller_can_accept_offer(): void
    {
        $offer = $this->createOffer();

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\OffersInbox::class)
            ->call('accept', $offer->id)
            ->assertDispatched('offer-accepted');

        $this->assertEquals('accepted', $offer->fresh()->status);
        $this->assertEquals('sold', $this->listing->fresh()->status);
    }

    #[Test]
    public function seller_can_decline_offer(): void
    {
        $offer = $this->createOffer();

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\OffersInbox::class)
            ->call('decline', $offer->id);

        $this->assertEquals('declined', $offer->fresh()->status);
    }

    #[Test]
    public function seller_can_counter_offer(): void
    {
        $offer = $this->createOffer();

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\OffersInbox::class)
            ->call('counter', $offer->id, 450, 'How about 450?');

        $freshOffer = $offer->fresh();
        $this->assertEquals('countered', $freshOffer->status);
        $this->assertEquals(45000, $freshOffer->counter_amount);
        $this->assertEquals('How about 450?', $freshOffer->counter_message);
        $this->assertNotNull($freshOffer->countered_at);
    }

    #[Test]
    public function seller_cannot_accept_others_offers(): void
    {
        $otherSeller = User::factory()->create();
        $offer = $this->createOffer();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($otherSeller)
            ->test(\App\Livewire\OffersInbox::class)
            ->call('accept', $offer->id);
    }

    #[Test]
    public function seller_cannot_accept_already_accepted_offer(): void
    {
        $offer = $this->createOffer(['status' => 'accepted']);

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\OffersInbox::class)
            ->call('accept', $offer->id)
            ->assertHasErrors();
    }

    #[Test]
    public function offers_page_shows_correct_tab_count(): void
    {
        $this->createOffer();
        $this->createOffer(['amount' => 60000]);
        $this->createOffer(['buyer_id' => $this->seller->id, 'seller_id' => $this->buyer->id]);

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\OffersInbox::class)
            ->assertSet('tab', 'received')
            ->assertSee($this->listing->title);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_offers_page(): void
    {
        $response = $this->get(route('offers.index'));
        $response->assertRedirect(route('login'));
    }
}
