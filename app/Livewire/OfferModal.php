<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\Offer;
use Livewire\Component;

class OfferModal extends Component
{
    public ?int $listingId = null;
    public int $amount = 0;
    public string $message = '';
    public bool $show = false;

    protected $listeners = ['open-offer-modal' => 'open'];

    public function open(int $listingId): void
    {
        $this->listingId = $listingId;
        $listing = Listing::findOrFail($listingId);
        $this->amount = $listing->price;
        $this->show = true;
    }

    public function close(): void
    {
        $this->reset(['listingId', 'amount', 'message', 'show']);
    }

    public function submit()
    {
        $this->validate([
            'listingId' => 'required|exists:listings,id',
            'amount' => 'required|integer|min:1',
            'message' => 'nullable|max:500',
        ]);

        $listing = Listing::with('user')->findOrFail($this->listingId);

        if ($this->amount > $listing->price) {
            $this->addError('amount', 'Offer cannot exceed the listing price.');
            return;
        }

        Offer::create([
            'listing_id' => $listing->id,
            'buyer_id' => auth()->id(),
            'seller_id' => $listing->user_id,
            'amount' => $this->amount,
            'message' => $this->message,
        ]);

        $this->close();
        $this->dispatch('offer-sent');
    }

    public function render()
    {
        return view('livewire.offer-modal');
    }
}
