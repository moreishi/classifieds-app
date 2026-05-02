<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;

/**
 * Unified reputation system with anti-cheat protections.
 *
 * Seller points = earned via reviews (quality × volume)
 * Buyer points = earned via completed purchases (volume only)
 * Final tier  = determined by whichever is higher
 *
 * Anti-cheat:
 *  - GCash verification required for reputation
 *  - Account age floor (7 days) before points count
 *  - Review trust multiplier based on account age
 *  - Buyer cap: max 50 purchases per unique seller
 */
class ReputationService
{
    const int ACCOUNT_AGE_DAYS = 7;
    const int MAX_PURCHASES_PER_SELLER = 50;

    /**
     * Calculate the reputation tier based on total reputation points.
     */
    public static function calculateTier(int $points): string
    {
        return match (true) {
            $points >= 1000 => 'pro',
            $points >= 500  => 'trusted',
            $points >= 100  => 'verified',
            default         => 'newbie',
        };
    }

    /**
     * Trust multiplier based on account age.
     * Users under 7 days old get zero reputation weight.
     * Gradually scales up to full weight at 30 days.
     */
    public static function trustMultiplier(User $user): float
    {
        $ageDays = $user->created_at->diffInDays(now());

        if ($ageDays < self::ACCOUNT_AGE_DAYS) {
            return 0.0;
        }

        return min(1.0, $ageDays / 30);
    }

    /**
     * Check if a user is eligible to earn reputation.
     */
    public static function isEligible(User $user): bool
    {
        // Must have verified GCash
        if (!$user->gcash_verified_at) {
            return false;
        }

        // Must be at least 7 days old
        if ($user->created_at->diffInDays(now()) < self::ACCOUNT_AGE_DAYS) {
            return false;
        }

        return true;
    }

    /**
     * Recalculate and update a seller's reputation based on all their reviews.
     * Applies trust multiplier for anti-cheat.
     */
    public function recalculateSellerReputation(User $seller): void
    {
        $reviews = Review::where('seller_id', $seller->id)->get();
        $count = $reviews->count();

        if ($count === 0) {
            $seller->update([
                'reputation_points' => 0,
                'reputation_tier' => self::calculateTier(0),
            ]);
            return;
        }

        // Weighted average — trust multiplier per reviewer
        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($reviews as $review) {
            $reviewer = $review->reviewer;
            $weight = self::trustMultiplier($reviewer);
            $weightedSum += $review->rating * $weight;
            $totalWeight += $weight;
        }

        $effectiveAvg = $totalWeight > 0 ? $weightedSum / $totalWeight : 0;

        // points = effective avg * 20 * sqrt(count) — rewards consistency and volume
        $points = (int) round($effectiveAvg * 20 * sqrt(max($count, 1)));

        $seller->update([
            'reputation_points' => $points,
            'reputation_tier' => self::calculateTier($points),
        ]);
    }

    /**
     * Award buyer points for a completed purchase.
     * Caps at MAX_PURCHASES_PER_SELLER per unique seller.
     */
    public function awardBuyerPoints(User $buyer, User $seller): void
    {
        if (!self::isEligible($buyer)) {
            return;
        }

        // Load buyer_points from fresh query
        $buyer->refresh();

        // Check per-seller cap
        $purchasesFromSeller = \App\Models\TransactionReceipt::where('buyer_email', $buyer->email)
            ->where('seller_id', $seller->id)
            ->count();

        if ($purchasesFromSeller > self::MAX_PURCHASES_PER_SELLER) {
            return;
        }

        // Scale: first few purchases give more points, then diminish
        $pointsToAward = match (true) {
            $purchasesFromSeller <= 5  => 50,   // first 5: 50 pts each
            $purchasesFromSeller <= 20 => 25,   // next 15: 25 pts each
            default                    => 10,   // after 20: 10 pts each
        };

        $buyer->increment('buyer_points', $pointsToAward);

        // Recalculate overall tier (max of seller vs buyer)
        $this->recalculateTier($buyer);
    }

    /**
     * Determine the user's final tier based on max of seller vs buyer points.
     */
    public function recalculateTier(User $user): void
    {
        $sellerPoints = $user->reputation_points;
        $buyerPoints = $user->buyer_points;
        $maxPoints = max($sellerPoints, $buyerPoints);

        $user->update([
            'reputation_tier' => self::calculateTier($maxPoints),
        ]);
    }

    /**
     * Get comprehensive stats for a user.
     */
    public function userStats(User $user): array
    {
        return [
            'seller_points' => $user->reputation_points,
            'buyer_points' => $user->buyer_points,
            'total_points' => max($user->reputation_points, $user->buyer_points),
            'tier' => $user->reputation_tier,
            'avg_rating' => (float) Review::where('seller_id', $user->id)->avg('rating') ?? 0,
            'total_reviews' => Review::where('seller_id', $user->id)->count(),
            'eligible' => self::isEligible($user),
        ];
    }

    /**
     * Render star HTML for a given rating.
     */
    public static function starHtml(float $rating, int $max = 5): string
    {
        $full = floor($rating);
        $half = ($rating - $full) >= 0.5;
        $empty = $max - $full - ($half ? 1 : 0);

        $html = '';
        for ($i = 0; $i < $full; $i++) {
            $html .= '<span class="text-yellow-400">&#9733;</span>';
        }
        if ($half) {
            $html .= '<span class="text-yellow-400">&#9734;</span>';
        }
        for ($i = 0; $i < $empty; $i++) {
            $html .= '<span class="text-gray-300">&#9733;</span>';
        }

        return $html;
    }
}
