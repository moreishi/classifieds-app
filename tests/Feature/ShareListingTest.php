<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShareListingTest extends TestCase
{
    use RefreshDatabase;

    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create();
        $this->listing = Listing::factory()->create([
            'user_id' => $seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function listing_page_has_share_section(): void
    {
        $response = $this->get(route('listing.show', $this->listing->slug));

        $response->assertStatus(200);
        $response->assertSee('Share');
    }

    #[Test]
    public function listing_page_has_facebook_share_link(): void
    {
        $response = $this->get(route('listing.show', $this->listing->slug));

        $response->assertStatus(200);
        $response->assertSee('facebook.com/sharer');
    }

    #[Test]
    public function listing_page_has_twitter_share_link(): void
    {
        $response = $this->get(route('listing.show', $this->listing->slug));

        $response->assertStatus(200);
        $response->assertSee('twitter.com/intent/tweet');
    }

    #[Test]
    public function listing_page_has_copy_link_button(): void
    {
        $response = $this->get(route('listing.show', $this->listing->slug));

        $response->assertStatus(200);
        $response->assertSee("Copy");
    }
}
