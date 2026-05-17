<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfferModalTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
            'price' => 100000, // ₱1,000 in centavos = price/100 = 10
        ]);
    }

    #[Test]
    public function buyer_can_open_offer_modal(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->assertSet('show', true)
            ->assertSet('listingId', $this->listing->id);
    }

    #[Test]
    public function offer_cannot_exceed_listing_price(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->set('amount', 2000)
            ->call('submit')
            ->assertHasErrors(['amount']);
    }

    #[Test]
    public function buyer_can_submit_offer(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->set('amount', 800)
            ->set('message', 'Can you do ₱800?')
            ->call('submit')
            ->assertDispatched('offer-sent')
            ->assertDispatched('offer-sent-toast');

        $this->assertDatabaseHas('offers', [
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 80000,
            'message' => 'Can you do ₱800?',
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function offer_modal_closes_after_submit(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->set('amount', 800)
            ->call('submit')
            ->assertSet('show', false);
    }

    #[Test]
    public function offer_amount_defaults_to_listing_price(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->assertSet('amount', 1000); // listingPrice / 100 = 100000 / 100 = 1000
    }

    #[Test]
    public function offer_on_sold_listing_shows_error(): void
    {
        $this->listing->update(['status' => 'sold']);

        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->assertDispatched('offer-error');
    }

    #[Test]
    public function offer_requires_minimum_amount(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->set('amount', 0)
            ->call('submit')
            ->assertHasErrors(['amount']);
    }

    #[Test]
    public function message_is_optional_in_offer(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->set('amount', 800)
            ->set('message', '')
            ->call('submit')
            ->assertDispatched('offer-sent');
    }

    #[Test]
    public function offer_can_be_cancelled(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->listing->id)
            ->call('close')
            ->assertSet('show', false)
            ->assertSet('listingId', 0);
    }
}
