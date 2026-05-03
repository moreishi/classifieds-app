<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\City;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class ConversationsTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $buyer;
    private User $seller;
    private Listing $listing;
    private Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'username' => 'seller1',
            'email' => 'seller@test.com',
        ]);

        $this->buyer = User::factory()->create([
            'username' => 'buyer1',
            'email' => 'buyer@test.com',
        ]);

        $category = Category::factory()->create(['name' => 'Phones']);
        $city = City::factory()->create(['name' => 'Manila']);

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'iPhone 15 Pro',
            'price' => 5000000,
            'status' => 'active',
        ]);

        $this->conversation = Conversation::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'Is this still available?',
        ]);

        $this->conversation->update(['last_message_at' => now()]);
    }

    #[Test]
    public function buyer_sees_conversation_in_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->buyer)
                ->visit('/conversations')
                ->waitForText('Messages')
                ->assertSee('seller1')
                ->assertSee('iPhone 15 Pro');
        });
    }
}
