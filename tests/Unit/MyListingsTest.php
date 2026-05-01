<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MyListingsTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private City $city;
    private User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = $this->createSeller();
        $region = Region::factory()->create();

        $this->category = Category::factory()->create(['is_active' => true]);
        $this->city = City::factory()->create([
            'region_id' => $region->id,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function my_listings_shows_only_own_listings(): void
    {
        $otherUser = $this->createSeller();

        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
            'title' => 'My Unique Item',
        ]);
        Listing::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
            'title' => 'Their Item',
        ]);

        $this->actingAs($this->seller);

        Livewire::test(\App\Livewire\MyListings::class)
            ->assertSee('My Unique Item')
            ->assertDontSee('Their Item');
    }

    #[Test]
    public function my_listings_includes_active_and_sold(): void
    {
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
            'title' => 'My Active Item',
        ]);
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'sold',
            'title' => 'My Sold Item',
        ]);

        $this->actingAs($this->seller);

        Livewire::test(\App\Livewire\MyListings::class)
            ->assertSee('My Active Item')
            ->assertSee('My Sold Item');
    }

    #[Test]
    public function my_listings_shows_empty_state(): void
    {
        $this->actingAs($this->seller);

        Livewire::test(\App\Livewire\MyListings::class)
            ->assertSee('No listings yet');
    }
}
