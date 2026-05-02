<?php

namespace App\Livewire;

use App\Models\Review;
use App\Models\TransactionReceipt;
use App\Notifications\ReviewReceived;
use App\Services\ReputationService;
use Livewire\Component;

class LeaveReview extends Component
{
    public TransactionReceipt $receipt;
    public int $rating = 5;
    public string $comment = '';
    public bool $submitted = false;

    public function mount(int $receiptId): void
    {
        $this->receipt = TransactionReceipt::with('listing')
            ->where('buyer_email', auth()->user()->email)
            ->findOrFail($receiptId);

        // Check if already reviewed
        $existing = Review::where('transaction_receipt_id', $receiptId)
            ->where('reviewer_id', auth()->id())
            ->first();

        if ($existing) {
            $this->submitted = true;
        }
    }

    public function submit(ReputationService $reputationService): void
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Prevent double-review
        if (Review::where('transaction_receipt_id', $this->receipt->id)
            ->where('reviewer_id', auth()->id())
            ->exists()) {
            session()->flash('error', 'You have already reviewed this transaction.');
            return;
        }

        Review::create([
            'listing_id' => $this->receipt->listing_id,
            'reviewer_id' => auth()->id(),
            'seller_id' => $this->receipt->seller_id,
            'transaction_receipt_id' => $this->receipt->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'expires_at' => now()->addMonths(6),
        ]);

        // Recalculate seller reputation (with anti-cheat weighting)
        $reputationService->recalculateSellerReputation($this->receipt->seller);

        // Notify seller
        $this->receipt->seller->notify(new ReviewReceived($this->receipt->reviews()->latest()->first()));

        $this->submitted = true;
        $this->dispatch('review-submitted');
        session()->flash('message', 'Review submitted! Thank you for your feedback.');
    }

    public function render()
    {
        return view('livewire.leave-review');
    }
}
