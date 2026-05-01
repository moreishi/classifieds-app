<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

class SearchListings extends Component
{
    use WithPagination;

    public Category $category;
    public string $citySlug = '';
    public string $sort = 'newest';
    public ?int $minPrice = null;
    public ?int $maxPrice = null;
    public ?string $condition = null;

    protected $queryString = ['citySlug', 'sort', 'minPrice', 'maxPrice', 'condition'];

    public function mount(string $slug): void
    {
        $this->category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function render()
    {
        $query = Listing::where('category_id', $this->category->id)
            ->where('status', 'active')
            ->with(['city', 'user']);

        if ($this->citySlug) {
            $query->whereHas('city', fn($q) => $q->where('slug', $this->citySlug));
        }

        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice * 100);
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice * 100);
        }

        if ($this->condition) {
            $query->where('condition', $this->condition);
        }

        $query->orderBy(match($this->sort) {
            'price_asc' => 'price',
            'price_desc' => 'price',
            default => 'created_at',
        }, match($this->sort) {
            'price_asc' => 'asc',
            'price_desc' => 'desc',
            'oldest' => 'asc',
            default => 'desc',
        });

        return view('livewire.search-listings', [
            'listings' => $query->paginate(20),
            'cities' => City::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }

    public function updatingCitySlug(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }
    public function updatingMinPrice(): void { $this->resetPage(); }
    public function updatingMaxPrice(): void { $this->resetPage(); }
    public function updatingCondition(): void { $this->resetPage(); }
}
