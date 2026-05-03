<?php

namespace App\Livewire;

use App\Models\Conversation;
use Livewire\Component;
use Livewire\WithPagination;

class ConversationsList extends Component
{
    use WithPagination;

    public bool $showArchived = false;

    public function toggleShowArchived(): void
    {
        $this->showArchived = !$this->showArchived;
        $this->resetPage();
    }

    public function archive(int $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = auth()->user();

        abort_unless(
            $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id,
            403
        );

        $column = $conversation->buyer_id === $user->id ? 'buyer_archived_at' : 'seller_archived_at';
        $conversation->update([$column => now()]);

        $this->dispatch('notify', message: 'Conversation archived.', variant: 'success');
    }

    public function unarchive(int $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = auth()->user();

        abort_unless(
            $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id,
            403
        );

        $column = $conversation->buyer_id === $user->id ? 'buyer_archived_at' : 'seller_archived_at';
        $conversation->update([$column => null]);

        $this->dispatch('notify', message: 'Conversation restored.', variant: 'success');
    }

    public function render()
    {
        $query = Conversation::where(function ($q) {
            $q->where('buyer_id', auth()->id())
              ->orWhere('seller_id', auth()->id());
        })
            ->with(['listing', 'buyer', 'seller', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc');

        if ($this->showArchived) {
            $userId = auth()->id();
            $query->where(function ($q) use ($userId) {
                $q->whereNotNull('buyer_archived_at')->where('buyer_id', $userId)
                  ->orWhereNotNull('seller_archived_at')->where('seller_id', $userId);
            });
        } else {
            $query->where(function ($q) {
                $q->whereNull('buyer_archived_at')->where('buyer_id', auth()->id())
                  ->orWhereNull('seller_archived_at')->where('seller_id', auth()->id());
            });
        }

        $conversations = $query->paginate(20);

        return view('livewire.conversations-list', [
            'conversations' => $conversations,
        ])->layout('layouts.app');
    }
}
