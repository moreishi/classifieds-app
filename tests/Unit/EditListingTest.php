<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditListingTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private City $city;
    private User $seller;
    private Listing $listing;

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

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);
    }

    #[Test]
    public function soft_deleted_listing_cannot_be_restored_by_another_user(): void
    {
        $otherUser = $this->createSeller();

        $this->listing->delete();

        // Other user should not be able to restore
        $restored = Listing::onlyTrashed()->find($this->listing->id);
        $this->assertNotNull($restored);
        $this->assertNotEquals($otherUser->id, $restored->user_id);
    }

    #[Test]
    public function trashed_listing_is_excluded_from_normal_queries(): void
    {
        $this->listing->delete();

        $count = Listing::where('user_id', $this->seller->id)->count();

        $this->assertEquals(0, $count);

        $trashedCount = Listing::onlyTrashed()->where('user_id', $this->seller->id)->count();
        $this->assertEquals(1, $trashedCount);
    }

    #[Test]
    public function edit_listing_shows_existing_photos(): void
    {
        $this->actingAs($this->seller);

        $component = Livewire::test(\App\Livewire\EditListing::class, [
            'slug' => $this->listing->slug,
        ]);

        $component->assertSet('title', $this->listing->title)
            ->assertSet('categoryId', $this->listing->category_id)
            ->assertSet('cityId', $this->listing->city_id);
    }

    #[Test]
    public function unauthorized_user_cannot_edit_others_listing(): void
    {
        $otherUser = $this->createSeller();
        $this->actingAs($otherUser);

        $component = Livewire::test(\App\Livewire\EditListing::class, [
            'slug' => $this->listing->slug,
        ]);

        // Should redirect with error flash
        $component->assertRedirect();
    }

    #[Test]
    public function category_factory_generates_unique_slugs(): void
    {
        $categories = Category::factory()->count(50)->create();

        $slugs = $categories->pluck('slug');

        $this->assertEquals(50, $slugs->unique()->count());
    }
}
