<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\SellerReplied;
use Livewire\Component;

class ConversationView extends Component
{
    public Conversation $conversation;
    public string $newMessage = '';
    public int $latestMessageId = 0;
    public bool $isTyping = false;
    public int $lastSeenMessageId = 0;

    public function mount(): void
    {
        abort_unless(
            auth()->id() === $this->conversation->buyer_id ||
            auth()->id() === $this->conversation->seller_id,
            403
        );

        $this->markMessagesRead();
        $this->latestMessageId = $this->conversation->messages()->latest()->first()?->id ?? 0;
        $this->lastSeenMessageId = $this->latestMessageId;
    }

    public function markMessagesRead(): void
    {
        $this->conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:5000',
        ]);

        $isFirstMessageFromSeller = $this->conversation->messages()->where('sender_id', $this->conversation->seller_id)->doesntExist();

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => auth()->id(),
            'body' => $this->newMessage,
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        $this->latestMessageId = $message->id;
        $this->lastSeenMessageId = $message->id;
        $this->newMessage = '';
        $this->isTyping = false;

        // If this is the seller's first reply, notify the buyer via email
        if ($isFirstMessageFromSeller && auth()->id() === $this->conversation->seller_id) {
            $this->conversation->buyer->notify(new SellerReplied($this->conversation));
        }
    }

    public function setTyping(): void
    {
        $this->isTyping = true;
    }

    public function stopTyping(): void
    {
        $this->isTyping = false;
    }

    public function refreshMessages(): void
    {
        $this->markMessagesRead();
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

    public function getUnreadCountProperty()
    {
        return $this->conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.conversation-view', [
            'messages' => $this->messages,
            'otherUser' => $this->otherUser,
            'unreadCount' => $this->unreadCount,
        ])->layout('layouts.app');
    }
}
