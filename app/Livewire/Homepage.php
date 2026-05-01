<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Listing;
use App\Models\City;
use Livewire\Component;

class Homepage extends Component
{
    public string $search = '';

    public function render()
    {
        return view('livewire.homepage', [
            'categories' => Category::where('is_active', true)->get(),
            'featuredListings' => Listing::where('is_featured', true)
                ->where('status', 'active')
                ->with(['category', 'city', 'user'])
                ->latest()
                ->limit(3)
                ->get(),
            'cities' => City::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
