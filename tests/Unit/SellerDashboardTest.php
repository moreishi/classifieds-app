<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\ListingViewLog;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $region = Region::factory()->create();
        $city = City::factory()->for($region)->create(['is_active' => true]);
        $category = Category::factory()->create(['is_active' => true]);

        $this->seller = User::factory()->create(['username' => 'seller-dash']);
        $this->buyer = User::factory()->create(['username' => 'buyer-dash']);

        // Create 3 active + 2 sold listings with some views and inquiries
        foreach (range(1, 3) as $i) {
            $listing = Listing::factory()->create([
                'user_id' => $this->seller->id,
                'category_id' => $category->id,
                'city_id' => $city->id,
                'status' => 'active',
                'title' => "Active Listing $i",
                'total_views' => $i * 10,
                'unique_views' => $i * 5,
            ]);
            // Add a view log entry for each
            ListingViewLog::create([
                'listing_id' => $listing->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'TestAgent',
                'viewed_at' => now(),
                'is_unique' => true,
            ]);
        }

        foreach (range(1, 2) as $i) {
            $listing = Listing::factory()->create([
                'user_id' => $this->seller->id,
                'category_id' => $category->id,
                'city_id' => $city->id,
                'status' => 'sold',
                'title' => "Sold Listing $i",
                'total_views' => $i * 20,
                'unique_views' => $i * 10,
            ]);
        }

        // Create some conversations (inquiries) to the seller
        $activeListings = Listing::where('user_id', $this->seller->id)->where('status', 'active')->get();
        foreach ($activeListings as $listing) {
            Conversation::create([
                'listing_id' => $listing->id,
                'buyer_id' => $this->buyer->id,
                'seller_id' => $this->seller->id,
                'last_message_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }

    #[Test]
    public function it_shows_stats_cards(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSeeInOrder([
            '3',   // active count
            '2',   // sold count
            '120', // total views: (10+20+30) + (20+40) = 120
            '3',   // inquiries (conversations)
        ]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->get(route('seller.dashboard'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_shows_listing_performance_table(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee('Active Listing 1');
        $response->assertSee('Active Listing 2');
        $response->assertSee('Active Listing 3');
        $response->assertSee('Sold Listing 1');
        $response->assertSee('Sold Listing 2');
    }

    #[Test]
    public function it_shows_recent_inquiries(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee($this->buyer->publicName());
    }

    #[Test]
    public function it_shows_empty_state_when_no_listings(): void
    {
        $freshSeller = User::factory()->create(['username' => 'fresh-seller']);
        $response = $this->actingAs($freshSeller)
            ->get(route('seller.dashboard'));

        $response->assertOk();
        $response->assertSee('No listings yet');
    }
}
