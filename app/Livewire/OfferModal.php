<?php

namespace App\Livewire;

use App\Models\Offer;
use App\Notifications\OfferReceived;
use Livewire\Component;

class OfferModal extends Component
{
    public int $listingId = 0;
    public int $amount = 0;
    public string $message = '';
    public int $listingPrice = 0;
    public bool $show = false;

    protected $listeners = ['openOfferModal' => 'open'];

    public function open(int $listingId): void
    {
        $listing = \App\Models\Listing::findOrFail($listingId);

        // Prevent offers on sold listings
        if ($listing->status === 'sold') {
            $this->dispatch('offer-error', message: 'This item has been sold and is no longer available.');
            return;
        }

        $this->listingId = $listingId;
        $this->listingPrice = $listing->price;
        $this->amount = $listing->price / 100; // show as pesos
        $this->show = true;
    }

    public function close(): void
    {
        $this->reset(['listingId', 'amount', 'message', 'listingPrice', 'show']);
    }

    public function submit(): void
    {
        $this->validate([
            'listingId' => 'required|exists:listings,id',
            'amount' => 'required|integer|min:1',
            'message' => 'nullable|max:500',
        ]);

        $amountInCentavos = $this->amount * 100;

        if ($amountInCentavos > $this->listingPrice) {
            $this->addError('amount', 'Offer cannot exceed the listing price.');
            return;
        }

        $listing = \App\Models\Listing::with('user')->findOrFail($this->listingId);

        $offer = Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => auth()->id(),
            'seller_id' => $listing->user_id,
            'amount' => $amountInCentavos,
            'message' => $this->message,
        ]);

        // Notify the seller
        $listing->user->notify(new OfferReceived($offer));

        $this->close();
        $this->dispatch('offer-sent');
        $this->dispatch('offer-sent-toast');
    }

    public function render()
    {
        return view('livewire.offer-modal');
    }
}
