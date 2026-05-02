<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferAccepted extends Notification implements ShouldQueue
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
            ->subject("Offer Accepted: {$this->offer->listing->title}")
            ->greeting("Hi {$notifiable->name}!")
            ->line('Your offer has been accepted!')
            ->line("**{$this->offer->listing->title}**")
            ->line("Amount: ₱" . number_format($this->offer->amount / 100))
            ->line("Seller: {$this->offer->seller->name}")
            ->action('View Transaction', url('/transactions'))
            ->line('Contact the seller on GCash to arrange payment and pick-up. You can message them through the app.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id,
            'listing_id' => $this->offer->listing_id,
            'listing_title' => $this->offer->listing->title,
            'amount' => $this->offer->amount,
            'seller_name' => $this->offer->seller->name,
            'type' => 'offer_accepted',
        ];
    }
}
