<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralBonus extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $referredUser,
        public int  $bonusAmount,  // in centavos
        public int  $tier,         // 1 or 2
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pesos = number_format($this->bonusAmount / 100, 2);

        if ($this->tier === 1) {
            return (new MailMessage)
                ->subject('🎉 Referral Bonus Earned!')
                ->greeting("Hi {$notifiable->publicName()}!")
                ->line("Great news! **{$this->referredUser->publicName()}** just signed up using your referral link.")
                ->line("You've earned **₱{$pesos}** as a signup bonus!")
                ->line('Your credits have been updated. Keep sharing your referral link to earn more.');
        }

        return (new MailMessage)
            ->subject('🎉 Referral Purchase Bonus!')
            ->greeting("Hi {$notifiable->publicName()}!")
            ->line("**{$this->referredUser->publicName()}** just made their first credit purchase.")
            ->line("You've earned **₱{$pesos}** as a referral purchase bonus!")
            ->line('Your credits have been updated. Share your referral link to earn more.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'referred_user_id' => $this->referredUser->id,
            'referred_user_name' => $this->referredUser->publicName(),
            'bonus_amount' => $this->bonusAmount,
            'bonus_pesos' => number_format($this->bonusAmount / 100, 2),
            'tier' => $this->tier,
            'type' => 'referral_bonus',
        ];
    }
}
