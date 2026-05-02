<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use App\Livewire\Homepage;
use App\Livewire\SearchResults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchResultsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $region = Region::factory()->create();
        $this->city = City::factory()->for($region)->create(['slug' => 'cebu-city', 'is_active' => true]);
        $this->category = Category::factory()->create(['is_active' => true, 'name' => 'Gadgets']);
        $this->user = User::factory()->create();
    }

    private function createListing(array $overrides = []): Listing
    {
        return Listing::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
            'title' => 'iPhone 14 Pro Max',
        ], $overrides));
    }

    #[Test]
    public function homepage_shows_4_latest_listings(): void
    {
        // Create 6 listings, 4 should show
        for ($i = 0; $i < 6; $i++) {
            $this->createListing(['title' => "Listing {$i}"]);
        }

        $component = Livewire::test(Homepage::class);

        $component->assertViewHas('featuredListings', function ($listings) {
            return $listings->count() === 4;
        });
    }

    #[Test]
    public function homepage_does_not_show_sold_listings(): void
    {
        $this->createListing(['title' => 'Active Item']);
        $this->createListing(['title' => 'Sold Item', 'status' => 'sold']);

        $component = Livewire::test(Homepage::class);

        $component->assertViewHas('featuredListings', function ($listings) {
            return $listings->count() === 1
                && $listings->first()->title === 'Active Item';
        });
    }

    #[Test]
    public function homepage_shows_categories(): void
    {
        Category::factory()->count(3)->create(['is_active' => true, 'parent_id' => null]);

        $component = Livewire::test(Homepage::class);

        $component->assertViewHas('categories', function ($categories) {
            return $categories->count() === 4; // 3 new + 1 from setUp
        });
    }

    #[Test]
    public function search_requires_at_least_2_characters(): void
    {
        Livewire::test(SearchResults::class, ['q' => 'i'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() === 0;
            });
    }

    #[Test]
    public function search_finds_matching_listings(): void
    {
        $this->createListing(['title' => 'iPhone 14 Pro Max 256GB']);
        $this->createListing(['title' => 'Samsung Galaxy S23']);

        // Search for "iphone"
        Livewire::test(SearchResults::class, ['q' => 'iphone'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() === 1
                    && str_contains($listings->first()->title, 'iPhone');
            });
    }

    #[Test]
    public function search_finds_normalized_matches_without_spaces(): void
    {
        $this->createListing(['title' => 'iPhone 13']);

        // Typing "iphone13" (no space) should match "iPhone 13" (with space)
        Livewire::test(SearchResults::class, ['q' => 'iphone13'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() === 1
                    && $listings->first()->title === 'iPhone 13';
            });
    }

    #[Test]
    public function search_finds_normalized_matches_with_spaces(): void
    {
        $this->createListing(['title' => 'iPhone13']);

        // Typing "iphone 13" (with space) should match "iPhone13" (no space)
        Livewire::test(SearchResults::class, ['q' => 'iphone 13'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() === 1
                    && $listings->first()->title === 'iPhone13';
            });
    }

    #[Test]
    public function search_returns_empty_state_when_no_results(): void
    {
        $this->createListing(['title' => 'iPhone 14']);

        Livewire::test(SearchResults::class, ['q' => 'nonexistent'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() === 0;
            })
            ->assertSee('No listings found');
    }

    #[Test]
    public function search_filters_by_city(): void
    {
        $region = Region::factory()->create();
        $city1 = City::factory()->for($region)->create(['slug' => 'test-filter-city-1', 'is_active' => true]);
        $city2 = City::factory()->for($region)->create(['slug' => 'test-filter-city-2', 'is_active' => true]);

        $listing1 = $this->createListing(['title' => 'iPhone in Cebu', 'city_id' => $city1->id]);
        $listing2 = $this->createListing(['title' => 'iPhone in Danao', 'city_id' => $city2->id]);

        Livewire::test(SearchResults::class, ['q' => 'iphone', 'citySlug' => 'test-filter-city-1'])
            ->assertViewHas('listings', function ($listings) {
                return $listings->count() === 1
                    && $listings->first()->title === 'iPhone in Cebu';
            });
    }

    #[Test]
    public function homepage_search_redirects_to_search_page(): void
    {
        Livewire::test(Homepage::class)
            ->set('search', 'iphone')
            ->call('searchListings')
            ->assertRedirect('/search?q=iphone');
    }
}
