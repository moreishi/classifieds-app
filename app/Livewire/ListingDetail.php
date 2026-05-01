<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class ListingDetail extends Component
{
    public Listing $listing;

    public function mount(string $slug): void
    {
        $this->listing = Listing::where('slug', $slug)
            ->with(['user', 'category', 'city', 'reviews', 'offers'])
            ->firstOrFail();
    }

    public function markAsSold(): void
    {
        if (auth()->id() !== $this->listing->user_id) {
            return;
        }

        $this->listing->update([
            'status' => 'sold',
            'sold_at' => now(),
        ]);

        $this->dispatch('listing-sold');
    }

    public function render()
    {
        return view('livewire.listing-detail', [
            'seller' => $this->listing->user,
        ])->layout('layouts.app');
    }
}
