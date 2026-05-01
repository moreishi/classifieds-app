<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

class ConversationView extends Component
{
    public Conversation $conversation;
    public string $newMessage = '';
    public int $latestMessageId = 0;

    public function mount(): void
    {
        // Authorize: only participants can view
        abort_unless(
            auth()->id() === $this->conversation->buyer_id ||
            auth()->id() === $this->conversation->seller_id,
            403
        );

        // Mark other user's messages as read
        foreach ($this->conversation->messages()->where('sender_id', '!=', auth()->id())->whereNull('read_at')->get() as $message) {
            $message->markAsRead();
        }

        // Track latest message ID for appendMessages
        $latest = $this->conversation->messages()->latest()->first();
        $this->latestMessageId = $latest?->id ?? 0;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:5000',
        ]);

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => auth()->id(),
            'body' => $this->newMessage,
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        $latest = $this->conversation->messages()->latest()->first();
        $this->latestMessageId = $latest->id;

        $this->newMessage = '';
    }

    public function refreshMessages(): void
    {
        // Mark unread messages as read
        $this->conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getMessagesProperty()
    {
        return $this->conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();
    }

    public function getOtherUserProperty()
    {
        return $this->conversation->otherUser(auth()->user());
    }

    public function render()
    {
        return view('livewire.conversation-view', [
            'messages' => $this->messages,
            'otherUser' => $this->otherUser,
        ])->layout('layouts.app');
    }
}
