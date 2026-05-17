<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\Review;
use App\Models\TransactionReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeaveReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;
    private TransactionReceipt $receipt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create();
        $this->buyer = User::factory()->create();

        $listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'sold',
        ]);

        $this->receipt = TransactionReceipt::create([
            'listing_id' => $listing->id,
            'seller_id' => $this->seller->id,
            'buyer_email' => $this->buyer->email,
            'buyer_name' => $this->buyer->name,
            'reference_number' => 'ISK-REVIEWTEST',
            'amount' => 50000,
            'status' => 'completed',
            'receipt_sent_at' => now(),
        ]);
    }

    #[Test]
    public function buyer_can_submit_review(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id])
            ->set('rating', 5)
            ->set('comment', 'Great seller!')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('reviews', [
            'transaction_receipt_id' => $this->receipt->id,
            'reviewer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'rating' => 5,
            'comment' => 'Great seller!',
        ]);
    }

    #[Test]
    public function buyer_cannot_double_review(): void
    {
        Review::create([
            'listing_id' => $this->receipt->listing_id,
            'reviewer_id' => $this->buyer->id,
            'seller_id' => $this->receipt->seller_id,
            'transaction_receipt_id' => $this->receipt->id,
            'rating' => 5,
            'comment' => 'Great!',
            'expires_at' => now()->addMonths(6),
        ]);

        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id])
            ->assertSet('submitted', true);
    }

    #[Test]
    public function review_requires_valid_rating(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id])
            ->set('rating', 0)
            ->call('submit')
            ->assertHasErrors(['rating']);
    }

    #[Test]
    public function review_requires_rating_between_1_and_5(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id])
            ->set('rating', 6)
            ->call('submit')
            ->assertHasErrors(['rating']);
    }

    #[Test]
    public function comment_is_optional(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id])
            ->set('rating', 4)
            ->set('comment', '')
            ->call('submit')
            ->assertSet('submitted', true);
    }

    #[Test]
    public function buyer_can_only_review_their_own_receipts(): void
    {
        $otherUser = User::factory()->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($otherUser)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id]);
    }

    #[Test]
    public function review_defaults_to_5_stars(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\LeaveReview::class, ['receiptId' => $this->receipt->id])
            ->assertSet('rating', 5);
    }
}
