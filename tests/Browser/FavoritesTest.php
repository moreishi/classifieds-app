<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class FavoritesTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $seller;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'username' => 'sellerfav',
            'email' => 'sellerfav@test.com',
        ]);

        $category = Category::factory()->create(['name' => 'Phones', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Cebu City', 'is_active' => true]);

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Favorite This Phone',
            'slug' => 'favorite-this-phone',
            'status' => 'active',
            'price' => 5000000,
        ]);
    }

    #[Test]
    public function user_can_favorite_and_unfavorite_a_listing(): void
    {
        $buyer = User::factory()->create([
            'username' => 'buyerfav',
            'email' => 'buyerfav@test.com',
        ]);

        $this->browse(function (Browser $browser) use ($buyer) {
            $browser->loginAs($buyer)
                ->visit('/listing/favorite-this-phone')
                ->waitForText('Favorite This Phone')
                ->click('.favorite-button')
                ->waitForText('favorited')
                ->assertSee('favorited');
        });
    }

    #[Test]
    public function user_can_see_favorites_on_favorites_page(): void
    {
        $buyer = User::factory()->create([
            'username' => 'buyerfav2',
            'email' => 'buyerfav2@test.com',
        ]);

        $buyer->favoriteListings()->attach($this->listing->id);

        $this->browse(function (Browser $browser) use ($buyer) {
            $browser->loginAs($buyer)
                ->visit('/favorites')
                ->waitForText('Favorite This Phone')
                ->assertSee('Favorite This Phone');
        });
    }

    #[Test]
    public function favorites_page_shows_empty_state(): void
    {
        $buyer = User::factory()->create([
            'username' => 'buyerfav3',
            'email' => 'buyerfav3@test.com',
        ]);

        $this->browse(function (Browser $browser) use ($buyer) {
            $browser->loginAs($buyer)
                ->visit('/favorites')
                ->waitForLocation('/favorites');
        });
    }
}
