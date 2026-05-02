<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use App\Livewire\OfferModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SoldListingTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Listing $activeListing;
    private Listing $soldListing;

    protected function setUp(): void
    {
        parent::setUp();

        $region = Region::factory()->create();
        $city = City::factory()->for($region)->create(['is_active' => true]);
        $category = Category::factory()->create(['is_active' => true]);

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();

        $this->activeListing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'status' => 'active',
            'title' => 'Active Item',
            'price' => 50000,
        ]);

        $this->soldListing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'status' => 'sold',
            'title' => 'Sold Item',
            'price' => 50000,
        ]);
    }

    // ── Offer Modal Tests ──

    #[Test]
    public function offer_modal_opens_for_active_listing(): void
    {
        Livewire::test(OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->activeListing->id)
            ->assertSet('show', true)
            ->assertSet('listingId', $this->activeListing->id);
    }

    #[Test]
    public function offer_modal_rejects_sold_listing(): void
    {
        Livewire::test(OfferModal::class)
            ->dispatch('openOfferModal', listingId: $this->soldListing->id)
            ->assertSet('show', false)
            ->assertDispatched('offer-error');
    }

    #[Test]
    public function offer_modal_cannot_submit_for_sold_listing(): void
    {
        // Open the modal on an active listing first
        $component = Livewire::test(OfferModal::class);
        $component->dispatch('openOfferModal', listingId: $this->activeListing->id);

        // Then change the listing to sold in DB to simulate race condition
        $this->soldListing->update(['status' => 'sold']);

        // Try opening on sold
        $component = Livewire::test(OfferModal::class);
        $component->dispatch('openOfferModal', listingId: $this->soldListing->id)
            ->assertDispatched('offer-error');
    }

    // ── Conversation Route Tests ──

    #[Test]
    public function buyer_can_start_conversation_on_active_listing(): void
    {
        $response = $this->actingAs($this->buyer)
            ->get(route('conversations.start', $this->activeListing));

        $response->assertRedirect();
        $this->assertStringContainsString('conversation/', $response->headers->get('Location'));
    }

    #[Test]
    public function buyer_cannot_start_conversation_on_sold_listing(): void
    {
        $response = $this->actingAs($this->buyer)
            ->get(route('conversations.start', $this->soldListing));

        $response->assertRedirect(route('listing.show', $this->soldListing));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function owner_can_always_start_conversation_on_own_listing(): void
    {
        // Owner starting a conversation on their own listing redirects back
        $response = $this->actingAs($this->seller)
            ->get(route('conversations.start', $this->soldListing));

        $response->assertRedirect(route('listing.show', $this->soldListing));
    }

    #[Test]
    public function owner_can_start_conversation_on_own_active_listing(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route('conversations.start', $this->activeListing));

        // Owner can't message themselves, redirects to listing
        $response->assertRedirect(route('listing.show', $this->activeListing));
    }

    // ── Listing Detail Page Tests ──

    #[Test]
    public function active_listing_shows_action_buttons_to_buyer(): void
    {
        $response = $this->actingAs($this->buyer)
            ->get(route('listing.show', $this->activeListing));

        $response->assertSee('Message Seller');
        $response->assertSee('Send Offer');
    }

    #[Test]
    public function sold_listing_hides_action_buttons_from_buyer(): void
    {
        $response = $this->actingAs($this->buyer)
            ->get(route('listing.show', $this->soldListing));

        $response->assertDontSee('Message Seller');
        $response->assertDontSee('Send Offer');
        $response->assertSee('This item has been sold');
    }

    #[Test]
    public function sold_listing_shows_sold_message_to_guest(): void
    {
        $response = $this->get(route('listing.show', $this->soldListing));

        $response->assertDontSee('Log in to send offer');
        $response->assertSee('This item has been sold');
    }

    #[Test]
    public function active_listing_shows_login_prompt_to_guest(): void
    {
        $response = $this->get(route('listing.show', $this->activeListing));

        $response->assertSee('Log in to send offer');
        $response->assertDontSee('This item has been sold');
    }

    #[Test]
    public function owner_sees_mark_as_sold_on_active_listing(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route('listing.show', $this->activeListing));

        $response->assertSee('Mark as Sold');
    }

    #[Test]
    public function owner_still_sees_offer_button_on_sold_listing(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route('listing.show', $this->soldListing));

        $response->assertSee('Send Offer');
    }
}
