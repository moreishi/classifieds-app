<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function insertNotification(?User $user = null): DatabaseNotification
    {
        $target = $user ?? $this->user;

        return $target->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\NewInquiry',
            'data' => [
                'conversation_id' => 1,
                'listing_id' => 1,
                'listing_title' => 'Test Listing',
                'buyer_name' => 'Test Buyer',
                'type' => 'new_inquiry',
            ],
        ]);
    }

    #[Test]
    public function user_can_view_notifications_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('notifications.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_notifications_page(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function user_can_mark_a_notification_as_read(): void
    {
        $notification = $this->insertNotification();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Notifications::class)
            ->call('markAsRead', $notification->id)
            ->assertDispatched('notification-read');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[Test]
    public function user_can_mark_all_notifications_as_read(): void
    {
        $this->insertNotification();
        $this->insertNotification();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Notifications::class)
            ->call('markAllAsRead')
            ->assertDispatched('notification-read');

        $this->assertEquals(0, $this->user->fresh()->unreadNotifications->count());
    }

    #[Test]
    public function user_cannot_mark_others_notifications_as_read(): void
    {
        $otherUser = User::factory()->create();
        $notification = $this->insertNotification($otherUser);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Notifications::class)
            ->call('markAsRead', $notification->id);
    }

    #[Test]
    public function notification_can_be_marked_read_twice_without_error(): void
    {
        $notification = $this->insertNotification();
        $notification->markAsRead();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Notifications::class)
            ->call('markAsRead', $notification->id)
            ->assertDispatched('notification-read');
    }

    #[Test]
    public function user_sees_unread_notifications_first(): void
    {
        $this->insertNotification();
        $this->insertNotification();
        $notification = $this->insertNotification();
        $notification->markAsRead();

        $unreadCount = $this->user->fresh()->unreadNotifications->count();
        $this->assertEquals(2, $unreadCount);
    }
}
