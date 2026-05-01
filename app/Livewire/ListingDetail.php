<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Services\ListingViewService;
use Livewire\Component;

class ListingDetail extends Component
{
    public Listing $listing;

    public function mount(string $slug, ListingViewService $viewService): void
    {
        $this->listing = Listing::where('slug', $slug)
            ->with(['user', 'category', 'city', 'reviews', 'offers'])
            ->firstOrFail();

        // Record the view
        $viewService->recordView($this->listing, request());
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
