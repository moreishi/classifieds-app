<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\Listing;
use App\Models\User;

class CreditService
{
    const int LISTING_FEE = 100;      // 100 centavos = ₱1
    const int REFERRAL_BONUS = 500;   // 500 centavos = ₱5

    /**
     * Check if a user can post a listing (has credits or free listings).
     */
    public function canPostListing(User $user, Listing $listing): bool
    {
        // Check free listings first
        if ($this->hasFreeListingAvailable($user)) {
            return true;
        }

        // Check category-specific pricing override
        $category = $listing->category;
        $price = $category->pricingOverride?->post_price
            ?? $category->post_price
            ?? self::LISTING_FEE;

        return $user->credit_balance >= $price;
    }

    /**
     * Deduct the cost of posting a listing.
     */
    public function chargeForListing(User $user, Listing $listing): void
    {
        $category = $listing->category;
        $price = $category->pricingOverride?->post_price
            ?? $category->post_price
            ?? self::LISTING_FEE;

        // Use a free listing if available
        if ($this->useFreeListing($user)) {
            $this->logTransaction($user, 0, 'listing_fee', $listing,
                "Free listing used (category: {$category->name})"
            );
            return;
        }

        $user->decrement('credit_balance', $price);

        $this->logTransaction($user, -$price, 'listing_fee', $listing,
            "Listing fee for {$category->name}"
        );
    }

    /**
     * Add credits to a user's balance.
     */
    public function deposit(User $user, int $amount, string $type, ?object $reference = null, string $notes = ''): void
    {
        $user->increment('credit_balance', $amount);

        $this->logTransaction($user, $amount, $type, $reference, $notes);
    }

    /**
     * Handle referral bonus for a new user.
     */
    public function processReferral(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return;
        }

        $newUser->update(['referred_by' => $referrer->id]);

        // Bonus for referrer
        $this->deposit($referrer, self::REFERRAL_BONUS, 'referral_bonus', $newUser,
            "Referral bonus for {$newUser->name}"
        );
    }

    /**
     * Generate a unique referral code for a user.
     */
    public static function generateReferralCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    private function hasFreeListingAvailable(User $user): bool
    {
        // Reset free listings if the month has rolled over
        if ($user->free_listings_reset_at && $user->free_listings_reset_at->isPast()) {
            $user->update([
                'free_listings_used' => 0,
                'free_listings_reset_at' => now()->addMonth(),
            ]);
        }

        $limit = match ($user->reputation_tier) {
            'pro' => 5,
            'trusted' => 3,
            'verified' => 2,
            default => 1, // 'newbie'
        };

        return $user->free_listings_used < $limit;
    }

    private function useFreeListing(User $user): bool
    {
        if (!$this->hasFreeListingAvailable($user)) {
            return false;
        }

        $user->increment('free_listings_used');

        // Set reset date on first use
        if (!$user->free_listings_reset_at) {
            $user->update(['free_listings_reset_at' => now()->addMonth()]);
        }

        return true;
    }

    private function logTransaction(User $user, int $amount, string $type, ?object $reference, string $notes): void
    {
        CreditTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
            'notes' => $notes,
        ]);
    }
}
