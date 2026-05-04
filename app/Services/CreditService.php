<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\Listing;
use App\Models\ReferralSignup;
use App\Models\User;
use App\Notifications\ReferralBonus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class CreditService
{
    const int LISTING_FEE = 0;          // 100 centavos = ₱1
    const int REFERRAL_BONUS_TIER1 = 200; // 200 centavos = ₱2 (instant on signup)
    const int REFERRAL_BONUS_TIER2 = 500; // 500 centavos = ₱5 (on first purchase)

    const int DAILY_REFERRAL_LIMIT = 10;

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
     * Handle referral link for a new user.
     *
     * Records who referred them and awards an instant Tier 1 bonus if anti-fraud
     * checks pass (email verified referrer, daily limit not hit, different IP).
     * The referral relationship (referred_by) is always recorded regardless of bonus.
     */
    public function processReferral(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return;
        }

        // Always record the referral relationship
        $newUser->update(['referred_by' => $referrer->id]);

        // Record the signup attempt
        $signup = ReferralSignup::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'bonus_awarded' => false,
        ]);

        // Anti-fraud checks before awarding Tier 1 bonus
        if (! $this->canAwardReferralBonus($referrer, $newUser)) {
            Log::info('[Referral] Anti-fraud skipped Tier 1 bonus', [
                'referrer_id' => $referrer->id,
                'referred_id' => $newUser->id,
            ]);
            return;
        }

        // Award Tier 1 bonus (instant signup)
        $this->deposit($referrer, self::REFERRAL_BONUS_TIER1, 'referral_bonus', $newUser,
            "Referral signup bonus for referring {$newUser->publicName()}"
        );

        $signup->update(['bonus_awarded' => true]);

        // Send notification
        $referrer->notify(new ReferralBonus($newUser, self::REFERRAL_BONUS_TIER1, 1));

        Log::info('[Referral] Tier 1 bonus awarded', [
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'amount' => self::REFERRAL_BONUS_TIER1,
        ]);
    }

    /**
     * Anti-fraud checks for referral bonus eligibility.
     */
    private function canAwardReferralBonus(User $referrer, User $newUser): bool
    {
        // 1. Referrer must have verified email
        if (! $referrer->email_verified_at) {
            return false;
        }

        // 2. Check daily referral limit
        $todayCount = ReferralSignup::where('referrer_id', $referrer->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= self::DAILY_REFERRAL_LIMIT) {
            return false;
        }

        // 3. Check the referred user's IP isn't the same as the referrer's known IPs
        //    We check using the new user's session/IP vs the referrer's recent sessions
        $newUserIp = request()->ip();

        if ($newUserIp === '127.0.0.1' || $newUserIp === '::1') {
            // Allow localhost — dev/testing
            return true;
        }

        // Check if the referrer has active sessions with the same IP
        $referrerHasSameIp = \DB::table('sessions')
            ->where('user_id', $referrer->id)
            ->where('ip_address', $newUserIp)
            ->exists();

        if ($referrerHasSameIp) {
            return false;
        }

        return true;
    }

    /**
     * Credit the referrer Tier 2 bonus when the referred user makes their first purchase.
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

        $this->deposit($referrer, self::REFERRAL_BONUS_TIER2, 'referral_bonus', $user,
            "Referral purchase bonus for referring {$user->publicName()}"
        );

        // Send notification
        $referrer->notify(new ReferralBonus($user, self::REFERRAL_BONUS_TIER2, 2));

        Log::info('[Referral] Tier 2 bonus awarded', [
            'referrer_id' => $referrer->id,
            'referred_id' => $user->id,
            'amount' => self::REFERRAL_BONUS_TIER2,
        ]);

        return true;
    }

    /**
     * Get referral stats for a user.
     */
    public function getReferralStats(User $user): array
    {
        $signups = ReferralSignup::where('referrer_id', $user->id)->get();

        $invitesSent = $signups->count();
        $bonusesAwarded = $signups->where('bonus_awarded', true)->count();
        $pendingBonuses = $signups->where('bonus_awarded', false)->count();

        // Total earned from all referral bonuses
        $totalEarned = CreditTransaction::where('user_id', $user->id)
            ->where('type', 'referral_bonus')
            ->sum('amount');

        return [
            'invites_sent' => $invitesSent,
            'bonuses_awarded' => $bonusesAwarded,
            'pending_bonuses' => $pendingBonuses,
            'total_earned' => $totalEarned,
        ];
    }

    /**
     * Get recent referral activity for a user.
     */
    public function getRecentReferrals(User $user, int $limit = 10)
    {
        return ReferralSignup::with('referred')
            ->where('referrer_id', $user->id)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get()
            ->map(function ($signup) {
                return [
                    'id' => $signup->id,
                    'name' => $signup->referred?->publicName() ?? 'Unknown',
                    'email' => $signup->referred?->email ?? '—',
                    'bonus_awarded' => $signup->bonus_awarded,
                    'signed_up_at' => $signup->created_at,
                ];
            });
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
