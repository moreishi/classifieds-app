<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\TransactionReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create();
        $this->buyer = User::factory()->create();

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'sold',
        ]);
    }

    private function createReceipt(array $overrides = []): TransactionReceipt
    {
        return TransactionReceipt::create(array_merge([
            'listing_id' => $this->listing->id,
            'seller_id' => $this->seller->id,
            'buyer_email' => $this->buyer->email,
            'buyer_name' => $this->buyer->name,
            'reference_number' => 'ISK-TEST1234',
            'amount' => 50000,
            'status' => 'completed',
            'receipt_sent_at' => now(),
        ], $overrides));
    }

    #[Test]
    public function seller_can_view_transactions_page(): void
    {
        $this->createReceipt();

        $response = $this->actingAs($this->seller)
            ->get(route('transactions.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function buyer_can_view_transactions_page(): void
    {
        $this->createReceipt();

        $response = $this->actingAs($this->buyer)
            ->get(route('transactions.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_transactions_page(): void
    {
        $response = $this->get(route('transactions.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function seller_can_generate_receipt(): void
    {
        $activeListing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->set('listingId', $activeListing->id)
            ->set('buyerEmail', 'buyer@example.com')
            ->set('buyerName', 'Juan Dela Cruz')
            ->set('amount', 500)
            ->call('generateReceipt')
            ->assertDispatched('receipt-created');

        $this->assertDatabaseHas('transaction_receipts', [
            'listing_id' => $activeListing->id,
            'seller_id' => $this->seller->id,
            'buyer_email' => 'buyer@example.com',
            'amount' => 50000,
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function generating_receipt_requires_valid_listing(): void
    {
        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->set('listingId', 99999)
            ->set('buyerEmail', 'buyer@example.com')
            ->set('amount', 500)
            ->call('generateReceipt')
            ->assertHasErrors(['listingId']);
    }

    #[Test]
    public function generating_receipt_requires_valid_email(): void
    {
        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->set('listingId', $this->listing->id)
            ->set('buyerEmail', 'not-an-email')
            ->set('amount', 500)
            ->call('generateReceipt')
            ->assertHasErrors(['buyerEmail']);
    }

    #[Test]
    public function seller_can_switch_to_buyer_tab(): void
    {
        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->set('tab', 'as_buyer')
            ->assertSet('tab', 'as_buyer');
    }

    #[Test]
    public function receipt_shows_in_correct_tab(): void
    {
        $this->createReceipt();

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->assertSet('tab', 'all');

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->set('tab', 'as_seller')
            ->assertSet('tab', 'as_seller');
    }

    #[Test]
    public function seller_cannot_generate_receipt_for_others_listing(): void
    {
        $otherSeller = User::factory()->create();
        $otherListing = Listing::factory()->create([
            'user_id' => $otherSeller->id,
            'category_id' => Category::factory(),
            'city_id' => City::factory()->for(Region::factory()),
            'status' => 'active',
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\Transactions::class)
            ->set('listingId', $otherListing->id)
            ->set('buyerEmail', 'buyer@example.com')
            ->set('amount', 500)
            ->call('generateReceipt');
    }
}
