<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Listing;
use App\Models\City;
use Livewire\Component;
use App\Helpers\LocationDetector;

class Homepage extends Component
{
    public string $search = '';

    public ?int $regionId = null;
    public ?int $provinceId = null;
    public ?int $cityId = null;

    public ?string $locationLabel = null;

    public function mount(): void
    {
        $detector = new LocationDetector();

        $city = $detector->detectCity();
        $province = $detector->detectProvince();
        $region = $detector->detectRegion() ?? $detector->defaultRegion();

        $this->cityId = $city?->id;
        $this->provinceId = $province?->id;
        $this->regionId = $region?->id;

        // Build a human-readable location label
        if ($city) {
            $this->locationLabel = $city->name;
            if ($province && $province->id !== $city->id) {
                $this->locationLabel .= ', ' . $province->name;
            }
        } elseif ($province) {
            $this->locationLabel = $province->name;
        } elseif ($region) {
            $this->locationLabel = $region->name;
        } else {
            $this->locationLabel = 'Cebu';
        }
    }

    public function searchListings(): void
    {
        $query = http_build_query(array_filter(['q' => $this->search]));
        $this->redirect('/search?' . $query);
    }

    /**
     * Apply location filter to a listing query.
     * Priority: City → Province → Region
     */
    protected function applyLocationFilter($query): void
    {
        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        } elseif ($this->provinceId) {
            $query->whereHas('city', fn($q) => $q->where('parent_id', $this->provinceId));
        } elseif ($this->regionId) {
            $query->whereHas('city', fn($q) => $q->where('region_id', $this->regionId));
        }
    }

    public function render()
    {
        $detector = new LocationDetector();
        $region = $detector->detectRegion() ?? $detector->defaultRegion();

        // Start with base queries
        $promotedQuery = Listing::where('status', 'active')
            ->where('featured_until', '>', now());

        $latestQuery = Listing::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '<=', now());
            });

        // Apply location filter to BOTH queries
        $this->applyLocationFilter($promotedQuery);
        $this->applyLocationFilter($latestQuery);

        return view('livewire.homepage', [
            'categories' => Category::getActiveParents(),
            'promotedListings' => $promotedQuery
                ->with(['category', 'city', 'user'])
                ->latest('featured_until')
                ->limit(4)
                ->get(),
            'latestListings' => $latestQuery
                ->with(['category', 'city', 'user'])
                ->latest()
                ->limit(8)
                ->get(),
            'cities' => City::where('is_active', true)->where('type', '!=', 'province')->orderBy('name')->get(),
            'locationLabel' => $this->locationLabel,
            'regionName' => $region?->name ?? 'Cebu',
        ])->layout('layouts.app');
    }
}
