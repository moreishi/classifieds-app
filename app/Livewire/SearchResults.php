<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

class SearchResults extends Component
{
    use WithPagination;

    public string $q = '';
    public string $citySlug = '';
    public string $sort = 'newest';
    public ?int $minPrice = null;
    public ?int $maxPrice = null;
    public ?string $condition = null;

    protected $queryString = ['q', 'citySlug', 'sort', 'minPrice', 'maxPrice', 'condition'];

    public function render()
    {
        $listings = collect();

        if (strlen($this->q) >= 2) {
            $listings = Listing::active()
                ->search($this->q)
                ->when($this->citySlug, fn($q) => $q->inCity($this->citySlug))
                ->priceBetween($this->minPrice, $this->maxPrice)
                ->withCondition($this->condition)
                ->sortBy($this->sort)
                ->with(['city', 'user', 'category'])
                ->paginate(20);
        }

        return view('livewire.search-listings', [
            'listings' => $listings,
            'cities' => City::where('is_active', true)->get(),
            'searchTerm' => $this->q,
        ])->layout('layouts.app');
    }

    public function updatingQ(): void { $this->resetPage(); }
    public function updatingCitySlug(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }
    public function updatingMinPrice(): void { $this->resetPage(); }
    public function updatingMaxPrice(): void { $this->resetPage(); }
    public function updatingCondition(): void { $this->resetPage(); }
}
