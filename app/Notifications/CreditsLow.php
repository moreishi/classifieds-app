<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditsLow extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public int $currentBalance,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Credits Are Running Low')
            ->greeting("Hi {$notifiable->name}!")
            ->line("Your current credit balance is **₱" . number_format($this->currentBalance / 100) . "**.")
            ->line('You need credits to post listings. Earn more by referring friends or purchasing credits.')
            ->action('Top Up Credits', url('/dashboard'))
            ->line('Referral bonus: ₱5 per friend who signs up!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'current_balance' => $this->currentBalance,
            'type' => 'credits_low',
        ];
    }
}
