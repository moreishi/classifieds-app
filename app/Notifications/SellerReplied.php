<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Conversation $conversation,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_seller_reply ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $listing = $this->conversation->listing;
        $reply = $this->conversation->messages()->latest()->first();

        return (new MailMessage)
            ->subject("{$listing->title}: seller replied to your inquiry")
            ->greeting("Hi {$notifiable->publicName()}!")
            ->line("The seller of **{$listing->title}** just replied to your message.")
            ->when($reply?->body, fn($msg) => $msg->line("Reply: \"{$reply->body}\""))
            ->action('View Reply', url("/conversation/{$this->conversation->id}"))
            ->line('Keep the conversation going — reply now!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'listing_id' => $this->conversation->listing_id,
            'listing_title' => $this->conversation->listing->title,
            'seller_name' => $this->conversation->seller->publicName(),
            'type' => 'seller_replied',
        ];
    }
}
