<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class FavoriteListings extends Component
{
    use WithPagination;

    public function render()
    {
        $listings = auth()->user()->favoriteListings()
            ->with(['city', 'user'])
            ->paginate(20);

        return view('livewire.favorite-listings', [
            'listings' => $listings,
        ])->layout('layouts.app');
    }
}
