<?php

namespace App\Livewire;

use App\Models\Offer;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\WithPagination;

class OffersInbox extends Component
{
    use WithPagination;

    public string $tab = 'received'; // received | sent

    public function accept(int $offerId, TransactionService $tx): void
    {
        $offer = Offer::with(['listing', 'buyer'])
            ->where('seller_id', auth()->id())
            ->findOrFail($offerId);

        try {
            $receipt = $tx->acceptOffer($offer);
            $this->dispatch('offer-accepted', receiptId: $receipt->id);
            session()->flash('message', 'Offer accepted! Please arrange GCash payment with the buyer.');
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function decline(int $offerId): void
    {
        $offer = Offer::where('seller_id', auth()->id())->findOrFail($offerId);
        $offer->update(['status' => 'declined']);
    }

    public function counter(int $offerId, int $counterAmount, string $counterMessage = ''): void
    {
        $offer = Offer::where('seller_id', auth()->id())->findOrFail($offerId);
        $offer->update([
            'status' => 'countered',
            'counter_amount' => $counterAmount * 100,
            'counter_message' => $counterMessage,
            'countered_at' => now(),
        ]);
    }

    public function render()
    {
        $offers = match ($this->tab) {
            'sent' => Offer::with(['listing', 'seller'])
                ->where('buyer_id', auth()->id())
                ->orderByDesc('created_at')
                ->paginate(20),
            default => Offer::with(['listing', 'buyer'])
                ->where('seller_id', auth()->id())
                ->orderByDesc('created_at')
                ->paginate(20),
        };

        return view('livewire.offers-inbox', [
            'offers' => $offers,
        ])->layout('layouts.app');
    }
}
