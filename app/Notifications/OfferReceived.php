<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Offer $offer,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Offer on {$this->offer->listing->title}")
            ->greeting("Hi {$notifiable->publicName()}!")
            ->line("You've received a new offer of ₱" . number_format($this->offer->amount / 100) . " on your listing:")
            ->line("**{$this->offer->listing->title}**")
            ->line("From: {$this->offer->buyer->publicName()}")
            ->when($this->offer->message, fn($msg) => $msg->line("Message: \"{$this->offer->message}\""))
            ->action('View Offer', url('/offers'))
            ->line('Log in to accept, decline, or counter the offer.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id,
            'listing_id' => $this->offer->listing_id,
            'listing_title' => $this->offer->listing->title,
            'amount' => $this->offer->amount,
            'buyer_name' => $this->offer->buyer->publicName(),
            'type' => 'offer_received',
        ];
    }
}
