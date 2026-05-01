<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditListing extends Component
{
    use WithFileUploads;

    public ?Listing $listing = null;
    public int $categoryId;
    public string $title = '';
    public string $description = '';
    public int $price = 0;
    public ?string $condition = null;
    public int $cityId;
    public $newPhotos = [];
    public array $existingPhotos = [];

    protected function rules(): array
    {
        return [
            'categoryId' => 'required|exists:categories,id',
            'title' => 'required|max:100',
            'description' => 'required|min:20',
            'price' => 'required|integer|min:1',
            'condition' => 'nullable|in:brand_new,like_new,used,for_parts',
            'cityId' => 'required|exists:cities,id',
            'newPhotos' => 'nullable|array|max:5',
            'newPhotos.*' => 'image|max:5120',
        ];
    }

    public function mount(string $slug): void
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();

        if (auth()->id() !== $listing->user_id) {
            session()->flash('error', 'You can only edit your own listings.');
            $this->redirectRoute('listing.show', $listing->slug, navigate: true);
            return;
        }

        if ($listing->status === 'sold') {
            session()->flash('error', 'This listing has been sold and can no longer be edited.');
            $this->redirectRoute('listing.show', $listing->slug, navigate: true);
            return;
        }

        $this->listing = $listing;
        $this->categoryId = $listing->category_id;
        $this->title = $listing->title;
        $this->description = $listing->description;
        $this->price = intdiv($listing->price, 100);
        $this->condition = $listing->condition;
        $this->cityId = $listing->city_id;

        $this->existingPhotos = $listing->getMedia('photos')
            ->map(fn ($m) => [
                'id' => $m->id,
                'url' => $m->getUrl('thumb') ?: $m->getUrl(),
                'name' => $m->name,
            ])
            ->toArray();
    }

    public function removePhoto(int $mediaId): void
    {
        $media = $this->listing->media()->findOrFail($mediaId);

        abort_unless(auth()->id() === $this->listing->user_id, 403);

        $media->delete();

        $this->existingPhotos = $this->listing->refresh()->getMedia('photos')
            ->map(fn ($m) => [
                'id' => $m->id,
                'url' => $m->getUrl('thumb') ?: $m->getUrl(),
                'name' => $m->name,
            ])
            ->toArray();
    }

    public function delete(): void
    {
        abort_unless(auth()->id() === $this->listing->user_id, 403);

        $this->listing->delete();

        session()->flash('message', 'Listing moved to trash. You can restore it within 30 days.');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function submit(): void
    {
        $this->validate();

        $totalPhotos = count($this->existingPhotos) + count($this->newPhotos);

        if ($totalPhotos < 1) {
            $this->addError('newPhotos', 'Your listing must have at least 1 photo.');
            return;
        }

        if ($totalPhotos > 5) {
            $this->addError('newPhotos', 'Your listing can have at most 5 photos.');
            return;
        }

        $slug = Str::slug($this->title);
        if ($slug !== $this->listing->slug) {
            $slug = $slug . '-' . Str::random(6);
        } else {
            $slug = $this->listing->slug;
        }

        $this->listing->update([
            'category_id' => $this->categoryId,
            'city_id' => $this->cityId,
            'title' => $this->title,
            'slug' => $slug,
            'description' => $this->description,
            'price' => $this->price * 100,
            'condition' => $this->condition,
        ]);

        foreach ($this->newPhotos as $photo) {
            $tmpPath = tempnam(sys_get_temp_dir(), 'listing_');
            file_put_contents($tmpPath, $photo->get());
            $this->listing->addMedia($tmpPath)
                ->usingName($photo->getClientOriginalName())
                ->toMediaCollection('photos');
        }

        session()->flash('message', 'Listing updated successfully!');

        $this->redirectRoute('listing.show', $this->listing->slug, navigate: true);
    }

    public function render()
    {
        return view('livewire.edit-listing', [
            'categories' => Category::where('is_active', true)->get(),
            'cities' => City::where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
