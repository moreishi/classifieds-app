<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Listing;
use Livewire\Component;

class SellerDashboard extends Component
{
    public function render()
    {
        $userId = auth()->id();

        $listings = Listing::with('media')
            ->where('user_id', $userId)
            ->get();

        $activeCount = $listings->where('status', 'active')->count();
        $soldCount = $listings->where('status', 'sold')->count();
        $totalViews = (int) $listings->sum('total_views');
        $totalInquiries = Conversation::where('seller_id', $userId)->count();

        // Listing performance with thumbnails — eager-loaded via with('media')
        $listingStats = $listings->sortByDesc('created_at')->map(function ($listing) {
            return [
                'id' => $listing->id,
                'slug' => $listing->slug,
                'title' => $listing->title,
                'price' => $listing->price,
                'status' => $listing->status,
                'views' => $listing->total_views ?? 0,
                'unique_views' => $listing->unique_views ?? 0,
                'inquiries' => $listing->conversations()->count(),
                'thumb' => $listing->getFirstMediaUrl('photos', 'thumb'),
                'created_at' => $listing->created_at,
            ];
        });

        // Recent inquiries to seller's listings
        $recentInquiries = Conversation::with(['buyer', 'listing'])
            ->where('seller_id', $userId)
            ->orderBy('last_message_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.seller-dashboard', [
            'activeCount' => $activeCount,
            'soldCount' => $soldCount,
            'totalViews' => $totalViews,
            'totalInquiries' => $totalInquiries,
            'totalListings' => $listings->count(),
            'listingStats' => $listingStats,
            'recentInquiries' => $recentInquiries,
        ])->layout('layouts.app');
    }
}
