<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiry extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Conversation $conversation,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_new_inquiry ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $listing = $this->conversation->listing;
        $buyer = $this->conversation->buyer;
        $firstMessage = $this->conversation->messages()->oldest()->first();

        return (new MailMessage)
            ->subject("New inquiry about: {$listing->title}")
            ->greeting("Hi {$notifiable->publicName()}!")
            ->line("**{$buyer->publicName()}** is interested in your listing:")
            ->line("**{$listing->title}**")
            ->when($firstMessage?->body, fn($msg) => $msg->line("Message: \"{$firstMessage->body}\""))
            ->line("Price: ₱" . number_format($listing->price / 100))
            ->when($listing->city, fn($msg) => $msg->line("Location: {$listing->city->name}"))
            ->action('Reply to Inquiry', url("/conversation/{$this->conversation->id}"))
            ->line('Quick replies get better results. Respond promptly to close the deal!');
    }

    public function toArray(object $notifiable): array
    {
        $listing = $this->conversation->listing;

        return [
            'conversation_id' => $this->conversation->id,
            'listing_id' => $listing->id,
            'listing_title' => $listing->title,
            'buyer_name' => $this->conversation->buyer->publicName(),
            'type' => 'new_inquiry',
        ];
    }
}
