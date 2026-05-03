<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Review $review,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Review on {$this->review->listing->title}")
            ->greeting("Hi {$notifiable->publicName()}!")
            ->line('You received a new review!')
            ->line("**Listing:** {$this->review->listing->title}")
            ->line("**Rating:** {$this->review->rating}/5 " . str_repeat('⭐', $this->review->rating))
            ->when($this->review->comment, fn($msg) => $msg->line("**Comment:** \"{$this->review->comment}\""))
            ->action('View Listing', url("/listing/{$this->review->listing->slug}"))
            ->line('Good reviews build trust. Keep it up!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'listing_id' => $this->review->listing_id,
            'listing_title' => $this->review->listing->title,
            'rating' => $this->review->rating,
            'reviewer_name' => $this->review->reviewer->publicName(),
            'type' => 'review_received',
        ];
    }
}
