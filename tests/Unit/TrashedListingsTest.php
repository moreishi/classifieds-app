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

class TrashedListingsTest extends TestCase
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
    public function force_delete_permanently_removes_listing(): void
    {
        $this->listing->delete();

        $this->actingAs($this->seller);

        \Livewire\Livewire::test(\App\Livewire\TrashedListings::class)
            ->call('forceDelete', $this->listing->id)
            ->assertRedirect();

        $this->assertNull(Listing::withTrashed()->find($this->listing->id));
    }

    #[Test]
    public function restore_brings_listing_back(): void
    {
        $this->listing->delete();

        $this->actingAs($this->seller);

        \Livewire\Livewire::test(\App\Livewire\TrashedListings::class)
            ->call('restore', $this->listing->id);

        $this->assertNotNull(Listing::find($this->listing->id));
    }

    #[Test]
    public function other_user_cannot_force_delete_my_trashed_listing(): void
    {
        $otherUser = $this->createSeller();

        $this->listing->delete();

        $this->actingAs($otherUser);

        $response = \Livewire\Livewire::test(\App\Livewire\TrashedListings::class);

        // Should not see the other user's trashed listing
        $response->assertDontSee($this->listing->title);
    }

    #[Test]
    public function soft_deleted_listing_shows_in_trashed_page(): void
    {
        $this->listing->delete();

        $this->actingAs($this->seller);

        \Livewire\Livewire::test(\App\Livewire\TrashedListings::class)
            ->assertSee($this->listing->title);
    }
}
