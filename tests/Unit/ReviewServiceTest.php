<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReviewService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReviewService::class);
    }

    private function makeListing(User $seller): Listing
    {
        return Listing::factory()->create([
            'user_id' => $seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
        ]);
    }

    #[Test]
    public function it_calculates_tiers_correctly(): void
    {
        $this->assertEquals('newbie', ReviewService::calculateTier(0));
        $this->assertEquals('newbie', ReviewService::calculateTier(50));
        $this->assertEquals('newbie', ReviewService::calculateTier(99));
        $this->assertEquals('verified', ReviewService::calculateTier(100));
        $this->assertEquals('verified', ReviewService::calculateTier(300));
        $this->assertEquals('verified', ReviewService::calculateTier(499));
        $this->assertEquals('trusted', ReviewService::calculateTier(500));
        $this->assertEquals('trusted', ReviewService::calculateTier(750));
        $this->assertEquals('trusted', ReviewService::calculateTier(999));
        $this->assertEquals('pro', ReviewService::calculateTier(1000));
        $this->assertEquals('pro', ReviewService::calculateTier(9999));
    }

    #[Test]
    public function it_recalculates_reputation_from_reviews(): void
    {
        $seller = $this->createSeller();
        $buyer = $this->createSeller();
        $listing = $this->makeListing($seller);

        Review::forceCreate([
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
            'reviewer_id' => $buyer->id,
            'rating' => 5,
            'comment' => 'Great seller!',
        ]);

        $this->service->recalculateReputation($seller);

        // points = 5 * 20 * sqrt(1) = 100
        $this->assertEquals(100, $seller->fresh()->reputation_points);
        $this->assertEquals('verified', $seller->fresh()->reputation_tier);
    }

    #[Test]
    public function it_recalculates_with_multiple_reviews(): void
    {
        $seller = $this->createSeller();
        $buyer1 = $this->createSeller();
        $buyer2 = $this->createSeller();

        $listing1 = $this->makeListing($seller);
        $listing2 = $this->makeListing($seller);

        Review::forceCreate(['listing_id' => $listing1->id, 'seller_id' => $seller->id, 'reviewer_id' => $buyer1->id, 'rating' => 5, 'comment' => 'Great!']);
        Review::forceCreate(['listing_id' => $listing2->id, 'seller_id' => $seller->id, 'reviewer_id' => $buyer2->id, 'rating' => 4, 'comment' => 'Good!']);

        $this->service->recalculateReputation($seller);

        // avg = 4.5, points = 4.5 * 20 * sqrt(2) ≈ 4.5 * 20 * 1.414 ≈ 127
        $this->assertEquals(127, $seller->fresh()->reputation_points);
        $this->assertEquals('verified', $seller->fresh()->reputation_tier);
    }

    #[Test]
    public function it_returns_seller_stats(): void
    {
        $seller = $this->createSeller();
        $buyer = $this->createSeller();
        $listing = $this->makeListing($seller);

        Review::forceCreate(['listing_id' => $listing->id, 'seller_id' => $seller->id, 'reviewer_id' => $buyer->id, 'rating' => 4, 'comment' => 'Nice']);

        $stats = $this->service->sellerStats($seller);

        $this->assertEquals(4.0, $stats['avg_rating']);
        $this->assertEquals(1, $stats['total_reviews']);
        $this->assertNull($stats['tier']); // no recalculation happened
        $this->assertEquals(0, $stats['points']);
    }

    #[Test]
    public function it_generates_star_html(): void
    {
        // 4.5 rating: 4 full + 1 half + 0 empty
        $html = ReviewService::starHtml(4.5);

        $this->assertStringContainsString('&#9733;', $html); // full stars present
        $this->assertStringContainsString('&#9734;', $html); // half star present
        $this->assertStringContainsString('text-yellow-400', $html); // yellow class present
        $this->assertStringNotContainsString('text-gray-300', $html); // no empty stars

        // 3.0 rating: 3 full + 0 half + 2 empty
        $html3 = ReviewService::starHtml(3.0);
        $this->assertStringContainsString('&#9733;', $html3); // stars present
        $this->assertStringNotContainsString('&#9734;', $html3); // no half star
        $this->assertStringContainsString('text-gray-300', $html3); // empty stars present

        // 0.0 rating: 0 full + 0 half + 5 empty
        $html0 = ReviewService::starHtml(0.0);
        $this->assertStringNotContainsString('text-yellow-400', $html0); // no yellow stars
        $this->assertStringContainsString('text-gray-300', $html0); // empty stars present

        // 5.0 rating: 5 full + 0 half + 0 empty
        $html5 = ReviewService::starHtml(5.0);
        $this->assertStringContainsString('text-yellow-400', $html5); // full stars
        $this->assertStringNotContainsString('&#9734;', $html5); // no half star
        $this->assertStringNotContainsString('text-gray-300', $html5); // no empty stars
    }
}
