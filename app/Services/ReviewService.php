<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;

class ReviewService
{
    const int MAX_RATING = 5;

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
     * Recalculate and update a seller's reputation based on all their reviews.
     */
    public function recalculateReputation(User $seller): void
    {
        $avg = Review::where('seller_id', $seller->id)->avg('rating') ?? 0;
        $count = Review::where('seller_id', $seller->id)->count();

        // points = avg rating * 20 * sqrt(count)  — rewards consistency and volume
        $points = (int) round($avg * 20 * sqrt(max($count, 1)));

        $seller->update([
            'reputation_points' => $points,
            'reputation_tier' => self::calculateTier($points),
        ]);
    }

    /**
     * Get the average rating and review count for a seller.
     */
    public function sellerStats(User $seller): array
    {
        return [
            'avg_rating' => (float) Review::where('seller_id', $seller->id)->avg('rating') ?? 0,
            'total_reviews' => Review::where('seller_id', $seller->id)->count(),
            'tier' => $seller->reputation_tier,
            'points' => $seller->reputation_points,
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
