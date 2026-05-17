<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\TransactionReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class TransactionsFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $seller;
    private User $buyer;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'username' => 'txseller',
            'email' => 'txseller@test.com',
        ]);

        $this->buyer = User::factory()->create([
            'username' => 'txbuyer',
            'email' => 'txbuyer@test.com',
        ]);

        $category = Category::factory()->create(['name' => 'Phones', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Cebu City', 'is_active' => true]);

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Transaction Test Item',
            'slug' => 'transaction-test-item',
            'status' => 'sold',
            'price' => 100000,
        ]);
    }

    #[Test]
    public function seller_can_generate_receipt(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/transactions')
                ->waitForText('Generate Receipt')
                ->assertSee('Generate Receipt');
        });
    }

    #[Test]
    public function seller_sees_their_transactions(): void
    {
        TransactionReceipt::create([
            'listing_id' => $this->listing->id,
            'seller_id' => $this->seller->id,
            'buyer_email' => $this->buyer->email,
            'buyer_name' => $this->buyer->name,
            'reference_number' => 'ISK-BROWSER-TEST',
            'amount' => 50000,
            'status' => 'completed',
            'receipt_sent_at' => now(),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/transactions')
                ->waitForText('Transaction Test Item')
                ->assertSee('Transaction Test Item')
                ->assertSee('ISK-BROWSER-TEST');
        });
    }

    #[Test]
    public function buyer_sees_their_transactions(): void
    {
        TransactionReceipt::create([
            'listing_id' => $this->listing->id,
            'seller_id' => $this->seller->id,
            'buyer_email' => $this->buyer->email,
            'buyer_name' => $this->buyer->name,
            'reference_number' => 'ISK-BUYER-TEST',
            'amount' => 50000,
            'status' => 'completed',
            'receipt_sent_at' => now(),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->buyer)
                ->visit('/transactions')
                ->waitForText('Transaction Test Item')
                ->assertSee('Transaction Test Item')
                ->assertSee('ISK-BUYER-TEST');
        });
    }
}
