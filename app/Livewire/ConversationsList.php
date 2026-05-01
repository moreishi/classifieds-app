<?php

namespace App\Livewire;

use App\Models\Conversation;
use Livewire\Component;
use Livewire\WithPagination;

class ConversationsList extends Component
{
    use WithPagination;

    public function render()
    {
        $conversations = Conversation::where(function ($q) {
            $q->where('buyer_id', auth()->id())
              ->orWhere('seller_id', auth()->id());
        })
            ->with(['listing', 'buyer', 'seller', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('livewire.conversations-list', [
            'conversations' => $conversations,
        ])->layout('layouts.app');
    }
}
