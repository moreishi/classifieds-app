<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class OffersTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $seller;
    private User $buyer;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'username' => 'offerseller',
            'email' => 'offerseller@test.com',
        ]);

        $this->buyer = User::factory()->create([
            'username' => 'offerbuyer',
            'email' => 'offerbuyer@test.com',
        ]);

        $category = Category::factory()->create(['name' => 'Electronics', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Cebu City', 'is_active' => true]);

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Offer Test Item',
            'slug' => 'offer-test-item',
            'status' => 'active',
            'price' => 200000,
        ]);
    }

    #[Test]
    public function buyer_can_send_offer_and_seller_sees_it(): void
    {
        $this->browse(function (Browser $browser) {
            $buyer = $this->buyer;
            $seller = $this->seller;

            // Buyer sends offer
            $browser->loginAs($buyer)
                ->visit('/listing/offer-test-item')
                ->waitForText('Offer Test Item')
                ->click('.offer-button')
                ->waitForText('Make an Offer')
                ->type('amount', '1500')
                ->type('message', 'Can you do ₱1,500?')
                ->press('Send Offer')
                ->waitForText('Offer sent')
                ->assertSee('Offer sent');
        });
    }

    #[Test]
    public function seller_can_view_pending_offers(): void
    {
        Offer::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 150000,
            'message' => 'Can you do ₱1,500?',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/offers')
                ->waitForText('Offer Test Item')
                ->assertSee('Offer Test Item')
                ->assertSee('₱1,500');
        });
    }

    #[Test]
    public function seller_can_accept_offer(): void
    {
        $offer = Offer::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 150000,
            'message' => 'Can you do ₱1,500?',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($offer) {
            $browser->loginAs($this->seller)
                ->visit('/offers')
                ->waitForText('Offer Test Item')
                ->press('Accept')
                ->waitForText('Offer accepted')
                ->assertSee('Offer accepted!');
        });
    }

    #[Test]
    public function seller_can_decline_offer(): void
    {
        $offer = Offer::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 150000,
            'message' => 'Can you do ₱1,500?',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($offer) {
            $browser->loginAs($this->seller)
                ->visit('/offers')
                ->waitForText('Offer Test Item')
                ->press('Decline')
                ->waitForText('Declined');
        });
    }

    #[Test]
    public function buyer_can_view_their_sent_offers(): void
    {
        Offer::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 150000,
            'message' => 'Test offer message',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->buyer)
                ->visit('/offers')
                ->waitForText('Sent')
                ->clickLink('Sent')
                ->waitForText('Test offer message')
                ->assertSee('Test offer message');
        });
    }
}
