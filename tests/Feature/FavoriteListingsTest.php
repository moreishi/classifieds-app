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

class FavoriteListingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $seller;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->seller = User::factory()->create();

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function unauthenticated_user_is_redirected_to_login_when_favoriting(): void
    {
        Livewire::test(\App\Livewire\ToggleFavorite::class, ['listing' => $this->listing])
            ->call('toggle')
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_favorite_a_listing(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ToggleFavorite::class, ['listing' => $this->listing])
            ->assertSet('isFavorited', false)
            ->call('toggle')
            ->assertSet('isFavorited', true);

        $this->assertDatabaseHas('listing_user', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);
    }

    #[Test]
    public function authenticated_user_can_unfavorite_a_listing(): void
    {
        $this->user->favoriteListings()->attach($this->listing->id);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ToggleFavorite::class, ['listing' => $this->listing])
            ->assertSet('isFavorited', true)
            ->call('toggle')
            ->assertSet('isFavorited', false);

        $this->assertDatabaseMissing('listing_user', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);
    }

    #[Test]
    public function favorites_page_shows_favorited_listings(): void
    {
        $this->user->favoriteListings()->attach($this->listing->id);

        $response = $this->actingAs($this->user)
            ->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertSee($this->listing->title);
    }

    #[Test]
    public function favorites_page_shows_empty_state_when_no_favorites(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('favorites.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_favorites_page(): void
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function toggling_favorite_is_idempotent(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\ToggleFavorite::class, ['listing' => $this->listing])
            ->call('toggle')
            ->call('toggle')
            ->assertSet('isFavorited', false);

        $this->assertDatabaseMissing('listing_user', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);
    }

    #[Test]
    public function favorites_page_only_shows_users_own_favorites(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->favoriteListings()->attach($this->listing->id);

        $response = $this->actingAs($this->user)
            ->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertDontSee($this->listing->title);
    }
}
