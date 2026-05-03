<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryFollowUp extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Conversation $conversation,
        public int $hoursSinceInquiry,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $listing = $this->conversation->listing;
        $buyer = $this->conversation->buyer;
        $lastMessage = $this->conversation->messages()->latest()->first();

        return (new MailMessage)
            ->subject("Reminder: Unanswered inquiry about {$listing->title}")
            ->greeting("Hi {$notifiable->name}!")
            ->line("**{$buyer->name}** messaged you about your listing **{$listing->title}** {$this->hoursSinceInquiry} hours ago, and it's still unanswered.")
            ->line("Quick replies get better results — buyers often move on if they don't hear back soon.")
            ->when($lastMessage?->body, fn($msg) => $msg->line("Their message: \"{$lastMessage->body}\""))
            ->action('Reply Now', url("/conversation/{$this->conversation->id}"))
            ->line("Don't let a potential sale slip away!");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'listing_id' => $this->conversation->listing_id,
            'listing_title' => $this->conversation->listing->title,
            'buyer_name' => $this->conversation->buyer->name,
            'type' => 'inquiry_follow_up',
        ];
    }
}
