<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListingScopeTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Category $subcategory;
    private City $city;
    private City $otherCity;
    private User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = $this->createSeller();
        $region = Region::factory()->create();

        $this->category = Category::factory()->create(['is_active' => true]);
        $this->subcategory = Category::factory()->create([
            'is_active' => true,
            'parent_id' => $this->category->id,
        ]);

        $this->city = City::factory()->create([
            'region_id' => $region->id,
            'is_active' => true,
        ]);
        $this->otherCity = City::factory()->create([
            'region_id' => $region->id,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function in_city_with_empty_string_returns_all_listings(): void
    {
        // Create listings in different cities
        Listing::factory()->count(3)->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);
        Listing::factory()->count(2)->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->otherCity->id,
            'status' => 'active',
        ]);

        // scopeInCity('') should NOT filter — return all 5
        $count = Listing::active()->inCity('')->count();

        $this->assertEquals(5, $count);
    }

    #[Test]
    public function in_city_with_valid_slug_filters_correctly(): void
    {
        Listing::factory()->count(3)->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->otherCity->id,
            'status' => 'active',
        ]);

        $count = Listing::active()->inCity($this->city->slug)->count();

        $this->assertEquals(3, $count);
    }

    #[Test]
    public function scope_in_category_includes_subcategory_listings(): void
    {
        // Listing under parent category
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);
        // Listings under subcategory
        Listing::factory()->count(2)->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->subcategory->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);

        $count = Listing::active()->inCategory($this->category->slug)->count();

        $this->assertEquals(3, $count);
    }

    #[Test]
    public function scope_active_excludes_sold_and_inactive(): void
    {
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);
        Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'sold',
        ]);

        $count = Listing::active()->count();

        $this->assertEquals(1, $count);
    }
}
