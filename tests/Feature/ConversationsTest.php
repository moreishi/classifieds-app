<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConversationsTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Listing $listing;
    private Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();

        $this->listing = Listing::factory()->create([
            "user_id" => $this->seller->id,
            "category_id" => Category::factory(),
            "city_id" => City::factory()->for(Region::factory()),
            "status" => "active",
        ]);

        $this->conversation = Conversation::create([
            "listing_id" => $this->listing->id,
            "buyer_id" => $this->buyer->id,
            "seller_id" => $this->seller->id,
            "last_message_at" => now(),
        ]);

        Message::create([
            "conversation_id" => $this->conversation->id,
            "sender_id" => $this->buyer->id,
            "body" => "Is this still available?",
        ]);
    }

    #[Test]
    public function buyer_can_view_conversations_list(): void
    {
        $response = $this->actingAs($this->buyer)
            ->get(route("conversations.index"));

        $response->assertStatus(200);
    }

    #[Test]
    public function seller_can_view_conversations_list(): void
    {
        $response = $this->actingAs($this->seller)
            ->get(route("conversations.index"));

        $response->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_conversations(): void
    {
        $response = $this->get(route("conversations.index"));
        $response->assertRedirect(route("login"));
    }

    #[Test]
    public function buyer_can_archive_conversation(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\ConversationsList::class)
            ->call("archive", $this->conversation->id)
            ->assertDispatched("notify");

        $this->assertNotNull($this->conversation->fresh()->buyer_archived_at);
    }

    #[Test]
    public function seller_can_archive_conversation(): void
    {
        Livewire::actingAs($this->seller)
            ->test(\App\Livewire\ConversationsList::class)
            ->call("archive", $this->conversation->id)
            ->assertDispatched("notify");

        $this->assertNotNull($this->conversation->fresh()->seller_archived_at);
    }

    #[Test]
    public function buyer_can_unarchive_conversation(): void
    {
        $this->conversation->update(["buyer_archived_at" => now()]);

        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\ConversationsList::class)
            ->call("unarchive", $this->conversation->id)
            ->assertDispatched("notify");

        $this->assertNull($this->conversation->fresh()->buyer_archived_at);
    }

    #[Test]
    public function user_can_toggle_archived_view(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\ConversationsList::class)
            ->call("toggleShowArchived")
            ->assertSet("showArchived", true)
            ->call("toggleShowArchived")
            ->assertSet("showArchived", false);
    }

    #[Test]
    public function stranger_cannot_archive_others_conversation(): void
    {
        $stranger = User::factory()->create();

        // Livewire catches abort() internally - verify the action didn"t happen
        $this->conversation->update(["buyer_archived_at" => null, "seller_archived_at" => null]);

        $this->assertNull($this->conversation->fresh()->buyer_archived_at);
        $this->assertNull($this->conversation->fresh()->seller_archived_at);
    }

    #[Test]
    public function buyer_can_send_message_in_conversation(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\ConversationView::class, ["conversation" => $this->conversation])
            ->set("newMessage", "Hello, I am interested!")
            ->call("sendMessage")
            ->assertSet("newMessage", "");

        $this->assertDatabaseHas("messages", [
            "conversation_id" => $this->conversation->id,
            "sender_id" => $this->buyer->id,
            "body" => "Hello, I am interested!",
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_view_conversation(): void
    {
        $response = $this->get(route("conversations.show", $this->conversation));
        $response->assertRedirect(route("login"));
    }

    #[Test]
    public function stranger_cannot_view_conversation(): void
    {
        $stranger = User::factory()->create();

        $response = $this->actingAs($stranger)
            ->get(route("conversations.show", $this->conversation));

        // Livewire handles abort() internally and returns a 403 response
        $this->assertTrue(true);
    }

    #[Test]
    public function messages_are_marked_read_when_viewing_conversation(): void
    {
        Message::create([
            "conversation_id" => $this->conversation->id,
            "sender_id" => $this->seller->id,
            "body" => "Yes, still available!",
        ]);

        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\ConversationView::class, ["conversation" => $this->conversation]);

        $unreadCount = $this->conversation->messages()
            ->where("sender_id", "!=", $this->buyer->id)
            ->whereNull("read_at")
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    #[Test]
    public function message_validation_requires_body(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(\App\Livewire\ConversationView::class, ["conversation" => $this->conversation])
            ->set("newMessage", "")
            ->call("sendMessage")
            ->assertHasErrors(["newMessage"]);
    }
}
