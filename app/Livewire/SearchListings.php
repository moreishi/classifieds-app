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
    public ?int $provinceId = null;
    public string $sort = 'newest';
    public ?int $minPrice = null;
    public ?int $maxPrice = null;
    public ?string $condition = null;

    protected $queryString = ['citySlug', 'provinceId', 'sort', 'minPrice', 'maxPrice', 'condition'];

    public function mount(string $slug): void
    {
        $this->category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function updatedProvinceId(): void
    {
        $this->citySlug = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Listing::active()
            ->inCategory($this->category->slug)
            ->when($this->provinceId, fn($q) => $q->inProvinceSlug($this->provinceId))
            ->when($this->citySlug, fn($q) => $q->inCity($this->citySlug))
            ->priceBetween($this->minPrice, $this->maxPrice)
            ->withCondition($this->condition)
            ->sortBy($this->sort)
            ->with(['city', 'user']);

        $listings = $query->paginate(20);

        $subcategories = $this->category->children()->where('is_active', true)->get();

        $provinces = City::where('is_active', true)
            ->where('type', 'province')
            ->orderBy('name')
            ->get();

        $cities = collect();
        if ($this->provinceId) {
            $cities = City::where('is_active', true)
                ->where('type', '!=', 'province')
                ->where('parent_id', $this->provinceId)
                ->orderBy('name')
                ->get();
        }

        return view('livewire.search-listings', [
            'listings' => $listings,
            'subcategories' => $subcategories,
            'provinces' => $provinces,
            'allCities' => $cities,
            'searchTerm' => '',
        ])->layout('layouts.app');
    }

    public function updatingCitySlug(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }
    public function updatingMinPrice(): void { $this->resetPage(); }
    public function updatingMaxPrice(): void { $this->resetPage(); }
    public function updatingCondition(): void { $this->resetPage(); }
}
