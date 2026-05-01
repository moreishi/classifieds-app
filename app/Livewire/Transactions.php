<?php

namespace App\Livewire;

use App\Models\TransactionReceipt;
use App\Models\Listing;
use App\Models\Offer;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public string $tab = 'receipts'; // receipts | generate

    // Generate receipt form
    public int $listingId = 0;
    public string $buyerEmail = '';
    public string $buyerName = '';
    public int $amount = 0;

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
            'amount' => $this->amount * 100, // convert to centavos
            'receipt_sent_at' => now(),
        ]);

        $this->dispatch('receipt-created', receiptId: $receipt->id);
        $this->reset('listingId', 'buyerEmail', 'buyerName', 'amount');
        session()->flash('message', 'Receipt generated!');
    }

    public function render()
    {
        $receipts = TransactionReceipt::with('listing')
            ->where('seller_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        $listings = Listing::where('user_id', auth()->id())
            ->where('status', 'sold')
            ->orWhere('status', 'active')
            ->get();

        return view('livewire.transactions', [
            'receipts' => $receipts,
            'listings' => $listings,
        ])->layout('layouts.app');
    }
}
