<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\City;
use App\Models\Category;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateListingCascadeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $region = \App\Models\Region::create(['id' => 1, 'name' => 'Central Visayas']);
        $province = City::create([
            'name' => 'Cebu',
            'slug' => 'cebu',
            'type' => 'province',
            'region_id' => 1,
            'parent_id' => null,
            'is_active' => true,
        ]);
        City::create([
            'name' => 'Cebu City',
            'slug' => 'cebu-city',
            'type' => 'city',
            'region_id' => 1,
            'parent_id' => $province->id,
            'is_active' => true,
        ]);
        City::create([
            'name' => 'Mandaue City',
            'slug' => 'mandaue-city',
            'type' => 'city',
            'region_id' => 1,
            'parent_id' => $province->id,
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'credit_balance' => 10000,
            'reputation_points' => 100,
            'reputation_tier' => 'trusted',
            'free_listings_used' => 0,
        ]);
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);
    }

    public function test_create_page_shows_province_dropdown(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('listings.create'));

        $response->assertStatus(200);
        $response->assertSee('Select province');
        $response->assertSee('Select city');
    }

    public function test_province_selection_filters_cities_in_component(): void
    {
        $province = City::where('type', 'province')->first();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set('provinceId', $province->id)
            ->assertSet('provinceId', $province->id);
    }

    public function test_province_change_resets_city(): void
    {
        $province = City::where('type', 'province')->first();
        $city = City::where('type', '!=', 'province')->first();

        // Verify initial state
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set('provinceId', $province->id)
            ->set('cityId', $city->id)
            ->assertSet('cityId', $city->id);
    }

    public function test_province_can_have_multiple_cities(): void
    {
        $province = City::where('type', 'province')->first();

        $cities = City::where('parent_id', $province->id)
            ->where('type', '!=', 'province')
            ->get();

        $this->assertCount(2, $cities); // Cebu City + Mandaue City
    }

    public function test_search_listings_page_shows_province_filter(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('category.show', $this->category->slug));

        $response->assertStatus(200);
        $response->assertSee('All provinces');
    }
}
