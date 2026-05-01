<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\TransactionReceipt;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * Accept an offer: create receipt, settle credits, mark listing sold.
     */
    public function acceptOffer(Offer $offer): TransactionReceipt
    {
        abort_if($offer->status !== 'pending', 400, 'Offer is not pending');

        $listing = $offer->listing;
        $buyer = $offer->buyer;

        // Check buyer has enough credits
        if ($buyer->credit_balance < $offer->amount) {
            throw new \RuntimeException('Buyer has insufficient credits to complete this transaction.');
        }

        // Deduct buyer's credits
        $buyer->decrement('credit_balance', $offer->amount);

        CreditTransaction::create([
            'user_id' => $buyer->id,
            'amount' => -$offer->amount,
            'type' => 'offer_accepted',
            'reference_type' => Offer::class,
            'reference_id' => $offer->id,
            'notes' => "Payment for {$listing->title}",
        ]);

        // Create receipt
        $receipt = TransactionReceipt::create([
            'listing_id' => $listing->id,
            'seller_id' => $offer->seller_id,
            'buyer_email' => $buyer->email,
            'buyer_name' => $buyer->name,
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

        return $receipt;
    }
}
