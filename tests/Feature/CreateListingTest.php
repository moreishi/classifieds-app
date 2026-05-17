<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateListingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private City $province;
    private City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $region = Region::factory()->create(["name" => "Central Visayas"]);

        $this->province = City::create([
            "name" => "Cebu",
            "slug" => "cebu",
            "type" => "province",
            "region_id" => $region->id,
            "is_active" => true,
        ]);

        $this->city = City::create([
            "name" => "Cebu City",
            "slug" => "cebu-city",
            "type" => "city",
            "region_id" => $region->id,
            "parent_id" => $this->province->id,
            "is_active" => true,
        ]);

        $this->user = User::factory()->create([
            "credit_balance" => 10000,
        ]);

        $this->category = Category::factory()->create([
            "name" => "Phones",
            "slug" => "phones",
            "is_active" => true,
            "post_price" => 100,
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_create_page(): void
    {
        $response = $this->get(route("listings.create"));
        $response->assertRedirect(route("login"));
    }

    #[Test]
    public function user_can_view_create_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route("listings.create"));

        $response->assertStatus(200);
    }

    #[Test]
    public function province_change_resets_city_selection(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("provinceId", $this->province->id)
            ->set("cityId", $this->city->id)
            ->set("provinceId", City::factory()->create([
                "name" => "Negros",
                "slug" => "negros",
                "type" => "province",
                "region_id" => 1,
                "is_active" => true,
            ])->id)
            ->assertSet("cityId", 0);
    }

    #[Test]
    public function title_is_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("title", "")
            ->set("cityId", $this->city->id)
            ->call("submit")
            ->assertHasErrors(["title"]);
    }

    #[Test]
    public function title_has_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("title", str_repeat("a", 101))
            ->set("cityId", $this->city->id)
            ->call("submit")
            ->assertHasErrors(["title"]);
    }

    #[Test]
    public function description_is_required_with_min_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("title", "iPhone 15 Pro")
            ->set("description", "Too short")
            ->set("cityId", $this->city->id)
            ->call("submit")
            ->assertHasErrors(["description"]);
    }

    #[Test]
    public function price_is_required_and_must_be_positive(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("title", "iPhone 15 Pro")
            ->set("description", "A brand new iPhone 15 Pro in excellent condition.")
            ->set("price", 0)
            ->set("cityId", $this->city->id)
            ->call("submit")
            ->assertHasErrors(["price"]);
    }

    #[Test]
    public function photos_are_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("title", "iPhone 15 Pro")
            ->set("description", "A brand new iPhone 15 Pro in excellent condition.")
            ->set("price", 45000)
            ->set("cityId", $this->city->id)
            ->call("submit")
            ->assertHasErrors(["photos"]);
    }

    #[Test]
    public function condition_must_be_valid_option(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("condition", "invalid_condition")
            ->call("submit")
            ->assertHasErrors(["condition"]);
    }

    #[Test]
    public function insufficient_credits_shows_error(): void
    {
        $poorUser = User::factory()->create([
            "credit_balance" => 0,
        ]);

        Livewire::actingAs($poorUser)
            ->test(\App\Livewire\CreateListing::class)
            ->set("categoryId", $this->category->id)
            ->set("title", "iPhone 15 Pro")
            ->set("description", "A brand new iPhone 15 Pro in excellent condition.")
            ->set("price", 45000)
            ->set("cityId", $this->city->id)
            ->call("submit");
    }

    #[Test]
    public function inactive_category_is_not_shown(): void
    {
        $inactiveCategory = Category::factory()->create([
            "name" => "Hidden",
            "slug" => "hidden",
            "is_active" => false,
        ]);

        $categories = Category::getAllActive();

        $this->assertTrue($categories->contains("id", $this->category->id));
        $this->assertFalse($categories->contains("id", $inactiveCategory->id));
    }
}
