<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Notifications\InquiryFollowUp;
use Illuminate\Console\Command;

class SendUnansweredInquiryReminders extends Command
{
    protected $signature = 'inquiries:remind-unanswered';
    protected $description = 'Send follow-up reminders to sellers who have unanswered inquiries older than 24 hours';

    public function handle(): int
    {
        $cutoff = now()->subHours(24);

        // Conversations with no seller reply yet
        $conversations = Conversation::query()
            ->where('last_message_at', '<=', $cutoff)
            ->whereHas('messages', function ($q) {
                // Buyer sent at least one message
                $q->whereColumn('sender_id', 'buyer_id');
            })
            ->whereDoesntHave('messages', function ($q) {
                // Seller has never replied
                $q->whereColumn('sender_id', 'seller_id');
            })
            ->with(['seller', 'listing', 'buyer', 'messages'])
            ->cursor();

        $sent = 0;

        foreach ($conversations as $conversation) {
            // Guard: seller shouldn't notify themselves
            if ($conversation->seller_id === $conversation->buyer_id) {
                continue;
            }

            $hoursSince = (int) $conversation->last_message_at->diffInHours(now());
            $conversation->seller->notify(new InquiryFollowUp($conversation, $hoursSince));
            $sent++;

            $this->line("  [{$sent}] Reminded {$conversation->seller->name} about inquiry on {$conversation->listing->title}");
        }

        $this->info("Sent {$sent} unanswered inquiry reminders.");

        return self::SUCCESS;
    }
}
