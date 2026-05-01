<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class ToggleFavorite extends Component
{
    public Listing $listing;
    public bool $isFavorited;

    public function mount(Listing $listing): void
    {
        $this->listing = $listing;
        $this->isFavorited = $listing->isFavoritedByAuth();
    }

    public function toggle(): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        if ($this->isFavorited) {
            auth()->user()->favoriteListings()->detach($this->listing->id);
            $this->isFavorited = false;
        } else {
            auth()->user()->favoriteListings()->attach($this->listing->id);
            $this->isFavorited = true;
        }
    }

    public function render()
    {
        return view('livewire.toggle-favorite');
    }
}
