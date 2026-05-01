<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\TransactionReceipt;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public string $tab = 'all'; // all | as_buyer | as_seller

    // Generate receipt form (legacy, for sellers who want offline receipts)
    public int $listingId = 0;
    public string $buyerEmail = '';
    public string $buyerName = '';
    public int $amount = 0;
    public bool $showForm = false;

    protected function rules(): array
    {
        return [
            'listingId' => 'required|exists:listings,id',
            'buyerEmail' => 'required|email',
            'buyerName' => 'nullable|max:100',
            'amount' => 'required|integer|min:1',
        ];
    }

    public function generateReceipt(): void
    {
        $this->validate();

        $listing = Listing::where('user_id', auth()->id())->findOrFail($this->listingId);

        $receipt = TransactionReceipt::create([
            'listing_id' => $listing->id,
            'seller_id' => auth()->id(),
            'buyer_email' => $this->buyerEmail,
            'buyer_name' => $this->buyerName,
            'reference_number' => 'ISK-' . strtoupper(Str::random(12)),
            'amount' => $this->amount * 100,
            'status' => 'completed',
            'receipt_sent_at' => now(),
        ]);

        $this->dispatch('receipt-created', receiptId: $receipt->id);
        $this->reset('listingId', 'buyerEmail', 'buyerName', 'amount');
        session()->flash('message', 'Receipt generated!');
    }

    public function render()
    {
        $receipts = TransactionReceipt::with(['listing', 'seller'])
            ->where(function ($q) {
                // User is either the seller OR the buyer
                $q->where('seller_id', auth()->id())
                  ->orWhere('buyer_email', auth()->user()->email);
            })
            ->when($this->tab === 'as_buyer', function ($q) {
                $q->where('buyer_email', auth()->user()->email);
            })
            ->when($this->tab === 'as_seller', function ($q) {
                $q->where('seller_id', auth()->id());
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        $listings = Listing::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'sold'])
            ->get();

        return view('livewire.transactions', [
            'receipts' => $receipts,
            'listings' => $listings,
        ])->layout('layouts.app');
    }
}
