<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Services\CreditService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateListing extends Component
{
    use WithFileUploads;

    public int $categoryId;
    public string $title = '';
    public string $description = '';
    public int $price = 0;
    public ?string $condition = null;
    public ?int $provinceId = null;
    public int $cityId;
    public $photos = [];

    protected function rules(): array
    {
        return [
            'categoryId' => 'required|exists:categories,id',
            'title' => 'required|max:100',
            'description' => 'required|min:20',
            'price' => 'required|integer|min:1',
            'condition' => 'nullable|in:brand_new,like_new,used,for_parts',
            'provinceId' => 'nullable|exists:cities,id',
            'cityId' => 'required|exists:cities,id',
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'image|max:5120', // 5MB
        ];
    }

    public function mount(): void
    {
        $this->provinceId = City::where('is_active', true)->where('type', 'province')->orderBy('name')->first()?->id;
        $this->cityId = 0;
    }

    public function updatedProvinceId(): void
    {
        $this->cityId = 0; // reset city when province changes
    }

    public function submit(CreditService $credits)
    {
        $this->validate();

        $user = auth()->user();

        // Check credits first
        if (!$credits->canPostListing($user, null)) {
            session()->flash('error', 'Insufficient credits. Please top up your account.');
            return;
        }

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $this->categoryId,
            'city_id' => $this->cityId,
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . Str::random(6),
            'description' => $this->description,
            'price' => $this->price * 100,
            'condition' => $this->condition,
            'expires_at' => now()->addDays(30),
        ]);

        foreach ($this->photos as $photo) {
            $listing
                ->addMedia($photo->path())
                ->usingName($photo->getClientOriginalName())
                ->withCustomProperties(['mime_type' => $photo->getMimeType()])
                ->toMediaCollection('photos');
        }

        $credits->chargeForListing($user, $listing);

        session()->flash('message', 'Listing created successfully!');

        return redirect()->route('listing.show', $listing->slug);
    }

    public function render()
    {
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

        return view('livewire.create-listing', [
            'categories' => Category::where('is_active', true)->get(),
            'provinces' => $provinces,
            'allCities' => $cities,
        ])->layout('layouts.app');
    }
}
