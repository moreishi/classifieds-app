<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Listing;
use App\Models\City;
use Livewire\Component;

class Homepage extends Component
{
    public string $search = '';

    public function searchListings(): void
    {
        $query = http_build_query(array_filter(['q' => $this->search]));
        $this->redirect('/search?' . $query);
    }

    public function render()
    {
        return view('livewire.homepage', [
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->get(),
            'featuredListings' => Listing::where('status', 'active')
                ->with(['category', 'city', 'user'])
                ->latest()
                ->limit(4)
                ->get(),
            'cities' => City::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
