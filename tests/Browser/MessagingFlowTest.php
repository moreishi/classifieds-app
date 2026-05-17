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

class MessagingFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $seller;
    private User $buyer;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'username' => 'msgseller',
            'email' => 'msgseller@test.com',
        ]);

        $this->buyer = User::factory()->create([
            'username' => 'msgbuyer',
            'email' => 'msgbuyer@test.com',
        ]);

        $category = Category::factory()->create(['name' => 'Phones', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Cebu City', 'is_active' => true]);

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Messaging Test',
            'slug' => 'messaging-test',
            'status' => 'active',
            'price' => 5000000,
        ]);
    }

    #[Test]
    public function seller_sees_new_conversation_from_buyer(): void
    {
        $conversation = Conversation::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'Is this item still available?',
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/conversations')
                ->waitForText('Messaging Test')
                ->assertSee('Messaging Test')
                ->assertSee('msgbuyer')
                ->assertSee('Is this item still available?');
        });
    }

    #[Test]
    public function buyer_can_send_message_in_conversation(): void
    {
        $conversation = Conversation::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'Is this still available?',
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->browse(function (Browser $browser) use ($conversation) {
            $browser->loginAs($this->seller)
                ->visit('/conversation/' . $conversation->id)
                ->waitForText('Is this still available?')
                ->assertSee('Is this still available?')
                ->assertSee('Messaging Test');
        });
    }

    #[Test]
    public function user_can_archive_conversation(): void
    {
        $conversation = Conversation::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'Hello!',
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->seller)
                ->visit('/conversations')
                ->waitForText('Messaging Test')
                ->press('Archive')
                ->waitForText('Conversation archived')
                ->assertSee('Conversation archived');
        });
    }
}
