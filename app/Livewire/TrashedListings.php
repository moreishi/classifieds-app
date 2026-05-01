<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

class TrashedListings extends Component
{
    use WithPagination;

    public function restore(int $id): void
    {
        $listing = Listing::onlyTrashed()->findOrFail($id);

        abort_unless(auth()->id() === $listing->user_id, 403);

        $listing->restore();

        session()->flash('message', 'Listing restored successfully!');
    }

    public function forceDelete(int $id): void
    {
        $listing = Listing::onlyTrashed()->findOrFail($id);

        abort_unless(auth()->id() === $listing->user_id, 403);

        $listing->forceDelete();

        session()->flash('message', 'Listing permanently deleted.');

        $this->redirectRoute('listings.trashed');
    }

    public function render()
    {
        $listings = Listing::onlyTrashed()
            ->where('user_id', auth()->id())
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('livewire.trashed-listings', [
            'listings' => $listings,
        ])->layout('layouts.app');
    }
}
