<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\ListingViewLog;
use Illuminate\Http\Request;

class ListingViewService
{
    /**
     * Record a view for a listing. Deduplicates unique views per IP per day.
     */
    public function recordView(Listing $listing, Request $request): void
    {
        $ip = $request->ip();
        $today = now()->startOfDay();

        // Check if this IP already viewed this listing today
        $existing = ListingViewLog::where('listing_id', $listing->id)
            ->where('ip_address', $ip)
            ->where('viewed_at', '>=', $today)
            ->exists();

        ListingViewLog::create([
            'listing_id' => $listing->id,
            'ip_address' => $ip,
            'user_agent' => mb_substr($request->userAgent() ?? '', 0, 500),
            'user_id' => $request->user()?->id,
            'viewed_at' => now(),
            'is_unique' => !$existing,
        ]);

        // Increment counters
        $listing->increment('total_views');
        if (!$existing) {
            $listing->increment('unique_views');
        }
    }
}
