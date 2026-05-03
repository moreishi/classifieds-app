<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class ListingBumpsTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $seller;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'name' => 'Test Seller',
            'email' => 'seller@test.com',
            'credit_balance' => 50000,
        ]);

        $category = Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Cebu City', 'slug' => 'cebu-city', 'is_active' => true]);

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Bump Test Listing',
            'slug' => 'bump-test-listing',
            'status' => 'active',
            'price' => 50000,
            'expires_at' => now()->addDays(10),
        ]);
    }

    #[Test]
    public function seller_sees_bump_button_on_listing_detail(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/listing/bump-test-listing')
                ->waitForText('Promote This Listing', 5)
                ->assertSee('Promote This Listing')
                ->assertSee('7 Days')
                ->assertSee('14 Days')
                ->assertSee('30 Days')
                ->assertSee('Pay');
        });
    }

    #[Test]
    public function buyer_does_not_see_bump_button(): void
    {
        $buyer = User::factory()->create();

        $this->browse(function (Browser $browser) use ($buyer) {
            $browser->loginAs($buyer)
                ->visit('/listing/bump-test-listing')
                ->waitForText('Message Seller', 5)
                ->assertDontSee('Promote This Listing');
        });
    }

    #[Test]
    public function seller_can_bump_listing_for_7_days(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/listing/bump-test-listing')
                ->waitForText('Promote This Listing', 5)
                ->click('input[value="bump_7d"]')  // Click the first radio
                ->press('Pay & Promote')
                ->waitForText('bumped', 10)
                ->assertSee('bumped');

            $this->assertDatabaseHas('listing_promotions', [
                'listing_id' => $this->listing->id,
                'plan' => 'bump_7d',
                'is_active' => true,
            ]);

            $this->assertDatabaseHas('credit_transactions', [
                'user_id' => $this->seller->id,
                'amount' => -5000,
                'type' => 'listing_bump',
            ]);

            $this->assertEquals(45000, $this->seller->fresh()->credit_balance);
        });
    }

    #[Test]
    public function promoted_listing_appears_on_homepage(): void
    {
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->listing->category_id,
            'city_id' => $this->listing->city_id,
            'title' => 'Regular Listing',
            'slug' => 'regular-listing',
            'status' => 'active',
            'price' => 30000,
            'expires_at' => now()->addDays(15),
        ]);

        $this->listing->update(['featured_until' => now()->addWeek()]);
        ListingPromotion::create([
            'listing_id' => $this->listing->id,
            'user_id' => $this->seller->id,
            'plan' => 'bump_7d',
            'amount_paid' => 5000,
            'starts_at' => now(),
            'expires_at' => now()->addWeek(),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitForText('Promoted', 5)
                ->assertSee('Promoted')
                ->assertSee('Bump Test Listing');
        });
    }

    #[Test]
    public function bump_button_shows_promoted_state_after_bump(): void
    {
        $this->listing->update(['featured_until' => now()->addDays(3)]);
        ListingPromotion::create([
            'listing_id' => $this->listing->id,
            'user_id' => $this->seller->id,
            'plan' => 'bump_7d',
            'amount_paid' => 5000,
            'starts_at' => now(),
            'expires_at' => now()->addDays(3),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/listing/bump-test-listing')
                ->waitForText('currently promoted', 5)
                ->assertSee('currently promoted');
        });
    }
}
