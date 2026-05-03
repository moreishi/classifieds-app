<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\CreditTransaction;
use Livewire\Component;

class BumpListing extends Component
{
    public Listing $listing;
    public string $selectedPlan = 'bump_7d';
    public string $message = '';
    public string $error = '';

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
    }

    public function bump(): void
    {
        $this->error = '';
        $this->message = '';

        $user = auth()->user();

        // Must own the listing
        if ($user->id !== $this->listing->user_id) {
            $this->error = 'You can only bump your own listings.';
            return;
        }

        // Must not already have an active promotion
        if ($this->listing->activePromotion()->exists()) {
            $this->error = 'This listing is already promoted.';
            return;
        }

        $plan = ListingPromotion::PLANS[$this->selectedPlan] ?? null;
        if (!$plan) {
            $this->error = 'Invalid plan selected.';
            return;
        }

        // Check balance
        if ($user->credit_balance < $plan['price']) {
            $this->error = 'Insufficient credits. You need ₱' . number_format($plan['price'] / 100, 2) . ' for this plan.';
            return;
        }

        $startsAt = now();
        $expiresAt = now()->addDays($plan['days']);

        \DB::transaction(function () use ($user, $plan, $startsAt, $expiresAt) {
            // Charge the user
            $user->decrement('credit_balance', $plan['price']);

            // Log the credit transaction
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$plan['price'],
                'type' => 'listing_bump',
                'reference_type' => Listing::class,
                'reference_id' => $this->listing->id,
                'notes' => "Bump {$plan['label']} for {$this->listing->title}",
            ]);

            // Create the promotion
            ListingPromotion::create([
                'listing_id' => $this->listing->id,
                'user_id' => $user->id,
                'plan' => $this->selectedPlan,
                'amount_paid' => $plan['price'],
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]);

            // Extend listing expiry so bump time isn't wasted on near-expired listings
            $currentExpiry = $this->listing->expires_at;
            $newExpiry = $currentExpiry && $currentExpiry->isFuture()
                ? $currentExpiry->addDays($plan['days'])
                : $expiresAt;

            // Update the listing
            $this->listing->update([
                'featured_until' => $expiresAt,
                'expires_at' => $newExpiry,
            ]);
        });

        $this->message = 'Your listing has been bumped! It will appear at the top of search results for ' . $plan['label'] . '.';
        $this->dispatch('bump-completed');
    }

    public function render()
    {
        return view('livewire.bump-listing', [
            'plans' => ListingPromotion::PLANS,
            'balance' => auth()->user()->credit_balance,
            'hasActivePromotion' => $this->listing->activePromotion()->exists(),
        ]);
    }
}
