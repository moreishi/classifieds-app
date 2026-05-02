<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\TransactionReceipt;
use App\Notifications\OfferAccepted;
use App\Services\ReputationService;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * Accept an offer: create receipt, mark listing sold, award buyer points.
     *
     * No credits are moved — buyer pays seller directly on GCash.
     * The receipt is purely for tracking and enabling reviews.
     */
    public function acceptOffer(Offer $offer): TransactionReceipt
    {
        abort_if($offer->status !== 'pending', 400, 'Offer is not pending');

        $listing = $offer->listing;

        // Create receipt — payment is done outside the platform (GCash direct)
        $receipt = TransactionReceipt::create([
            'listing_id' => $listing->id,
            'seller_id' => $offer->seller_id,
            'buyer_email' => $offer->buyer->email,
            'buyer_name' => $offer->buyer->name,
            'reference_number' => 'ISK-' . strtoupper(Str::random(12)),
            'amount' => $offer->amount,
            'status' => 'completed',
            'receipt_sent_at' => now(),
        ]);

        // Mark offer as accepted
        $offer->update(['status' => 'accepted']);

        // Mark listing as sold
        $listing->update([
            'status' => 'sold',
            'sold_at' => now(),
        ]);

        // Decline all other pending offers on this listing
        Offer::where('listing_id', $listing->id)
            ->where('id', '!=', $offer->id)
            ->where('status', 'pending')
            ->update(['status' => 'declined']);

        // Award buyer points (with anti-cheat protections)
        app(ReputationService::class)->awardBuyerPoints($offer->buyer, $offer->seller);

        // Notify buyer that their offer was accepted
        $offer->buyer->notify(new OfferAccepted($offer));

        return $receipt;
    }
}
