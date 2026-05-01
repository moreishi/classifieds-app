<?php

namespace App\Livewire;

use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    public ?string $notificationId = null;
    public bool $showAll = false;

    public function markAsRead(string $id): void
    {
        $notification = DatabaseNotification::where('notifiable_id', auth()->id())
            ->where('notifiable_type', get_class(auth()->user()))
            ->findOrFail($id);

        $notification->markAsRead();

        $this->dispatch('notification-read');
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        $this->dispatch('notification-read');
    }

    public function render()
    {
        $user = auth()->user();

        // On the full page, show paginated. On dropdown, show latest 10.
        if ($this->showAll) {
            $notifications = $user->notifications()->latest()->paginate(15);
        } else {
            $notifications = $user->notifications()->latest()->take(10)->get();
        }

        return view('livewire.notifications', [
            'unreadCount' => $user->unreadNotifications->count(),
            'notifications' => $notifications,
        ]);
    }
}
