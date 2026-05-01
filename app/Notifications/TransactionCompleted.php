<?php

namespace App\Notifications;

use App\Models\TransactionReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TransactionReceipt $receipt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Transaction Complete: {$this->receipt->listing->title}")
            ->greeting("Hi {$notifiable->name}!")
            ->line('Your transaction has been completed.')
            ->line("**Listing:** {$this->receipt->listing->title}")
            ->line("**Reference:** {$this->receipt->reference_number}")
            ->line("**Amount:** ₱" . number_format($this->receipt->amount / 100))
            ->action('View Receipt', url('/transactions'))
            ->line('Thank you for using Iskina.ph!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'receipt_id' => $this->receipt->id,
            'listing_id' => $this->receipt->listing_id,
            'listing_title' => $this->receipt->listing->title,
            'reference_number' => $this->receipt->reference_number,
            'amount' => $this->receipt->amount,
            'type' => 'transaction_completed',
        ];
    }
}
