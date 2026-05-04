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
    public function canPostListing(User $user, ?Listing $listing = null): bool
    {
        // Check free listings first
        if ($this->hasFreeListingAvailable($user)) {
            return true;
        }

        // Check category-specific pricing override (or default fee if no listing yet)
        if ($listing) {
            $category = $listing->category;
            $price = $category->pricingOverride?->post_price
                ?? $category->post_price
                ?? self::LISTING_FEE;
        } else {
            $price = self::LISTING_FEE;
        }

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
     * Handle referral link for a new user — just records who referred them.
     * Bonus is credited only after the referred user makes their first purchase.
     */
    public function processReferral(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return;
        }

        $newUser->update(['referred_by' => $referrer->id]);

        // No bonus yet — will be credited on first purchase via creditReferrer()
    }

    /**
     * Credit the referrer bonus when the referred user makes their first purchase.
     * Returns true if bonus was awarded, false if already paid or no referral.
     */
    public function creditReferrer(User $user): bool
    {
        if (!$user->referred_by) {
            return false;
        }

        $referrer = User::find($user->referred_by);

        if (!$referrer) {
            return false;
        }

        // Check if the referred user already triggered a bonus
        $alreadyPaid = CreditTransaction::where('user_id', $referrer->id)
            ->where('type', 'referral_bonus')
            ->where('reference_type', User::class)
            ->where('reference_id', $user->id)
            ->exists();

        if ($alreadyPaid) {
            return false;
        }

        $this->deposit($referrer, self::REFERRAL_BONUS, 'referral_bonus', $user,
            "Referral bonus for referring {$user->name}"
        );

        return true;
    }

    /**
     * Generate a unique referral code for a user.
     */
    public static function generateReferralCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function freeListingsLimit(?string $reputationTier): int
    {
        return match ($reputationTier) {
            'pro' => 5,
            'trusted' => 3,
            'verified' => 2,
            default => 1, // 'newbie'
        };
    }

    public function freeListingsRemaining(User $user): int
    {
        $limit = self::freeListingsLimit($user->reputation_tier);
        return max(0, $limit - $user->free_listings_used);
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

        return $this->freeListingsRemaining($user) > 0;
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
