<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

class MyListings extends Component
{
    use WithPagination;

    public function render()
    {
        $listings = Listing::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.my-listings', [
            'listings' => $listings,
        ])->layout('layouts.app');
    }
}
