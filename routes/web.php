<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Livewire\CreateListing;
use App\Livewire\EditListing;
use App\Livewire\Homepage;
use App\Livewire\Notifications as NotificationsPage;
use App\Livewire\ListingDetail;
use App\Livewire\OffersInbox;
use App\Livewire\SearchListings;
use App\Livewire\SearchResults;
use App\Livewire\Transactions;
use App\Livewire\ConversationsList;
use App\Livewire\ConversationView;
use App\Models\Listing;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::get('/category/{slug}', SearchListings::class)->name('category.show');
Route::get('/search', SearchResults::class)->name('search');

Route::get('/listing/{slug}', ListingDetail::class)->name('listing.show');

// Health check for Docker / Coolify
Route::get('/up', fn () => response()->json(['status' => 'ok']));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/conversations', ConversationsList::class)->name('conversations.index');
    Route::get('/conversation/{conversation}', ConversationView::class)->name('conversations.show');

    Route::get('/listing/{listing}/start-conversation', function (Listing $listing) {
        $buyer = auth()->user();
        if ($buyer->id === $listing->user_id) {
            return redirect()->route('listing.show', $listing->slug);
        }

        $conversation = App\Models\Conversation::firstOrCreate([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $listing->user_id,
        ]);

        return redirect()->route('conversations.show', $conversation);
    })->name('conversations.start');

    Route::get('/listings/create', CreateListing::class)->name('listings.create');
Route::get('/listing/{slug}/edit', EditListing::class)->name('listings.edit');
    Route::get('/my-listings', \App\Livewire\MyListings::class)->name('listings.my');
    Route::get('/offers', OffersInbox::class)->name('offers.index');
    Route::get('/transactions', Transactions::class)->name('transactions.index');
    Route::get('/notifications', NotificationsPage::class)->name('notifications.index');
    Route::get('/listings/trashed', \App\Livewire\TrashedListings::class)->name('listings.trashed');
    Route::get('/receipt/{receipt}/download', [ReceiptController::class, 'download'])->name('receipt.download');
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
