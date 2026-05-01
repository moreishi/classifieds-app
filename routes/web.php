<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\CreateListing;
use App\Livewire\Homepage;
use App\Livewire\ListingDetail;
use App\Livewire\OfferModal;
use App\Livewire\SearchListings;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::get('/category/{slug}', SearchListings::class)->name('category.show');

Route::get('/listing/{slug}', ListingDetail::class)->name('listing.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/listings/create', CreateListing::class)->name('listings.create');

    Route::get('/offers', OfferModal::class)->name('offers.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
