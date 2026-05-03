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
use App\Http\Controllers\SitemapController;
use App\Models\Listing;
use App\Notifications\NewInquiry;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::get('/category/{slug}', SearchListings::class)->name('category.show');
Route::get('/search', SearchResults::class)->name('search');

Route::get('/listing/{slug}', ListingDetail::class)->name('listing.show');

// Sitemaps
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/static.xml', [SitemapController::class, 'static'])->name('sitemap.static');
Route::get('/sitemap/categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap/listings/{page}.xml', [SitemapController::class, 'listings'])->whereNumber('page')->name('sitemap.listings');

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

        // Prevent messages on sold listings
        if ($listing->status === 'sold') {
            return redirect()->route('listing.show', $listing->slug)
                ->with('error', 'This item has been sold and is no longer available.');
        }

        $existing = App\Models\Conversation::where([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $listing->user_id,
        ])->first();

        if ($existing) {
            $conversation = $existing;
        } else {
            $conversation = App\Models\Conversation::create([
                'listing_id' => $listing->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $listing->user_id,
            ]);

            // Send email notification to seller on first inquiry
            $conversation->seller->notify(new NewInquiry($conversation));
        }

        return redirect()->route('conversations.show', $conversation);
    })->name('conversations.start');

    Route::get('/listings/create', CreateListing::class)->name('listings.create');
Route::get('/listing/{slug}/edit', EditListing::class)->name('listings.edit');
    Route::get('/my-listings', \App\Livewire\MyListings::class)->name('listings.my');
    Route::get('/offers', OffersInbox::class)->name('offers.index');
    Route::get('/transactions', Transactions::class)->name('transactions.index');
    Route::get('/favorites', \App\Livewire\FavoriteListings::class)->name('favorites.index');
    Route::get('/seller/dashboard', \App\Livewire\SellerDashboard::class)->name('seller.dashboard');
    Route::get('/notifications', NotificationsPage::class)->name('notifications.index');
    Route::get('/listings/trashed', \App\Livewire\TrashedListings::class)->name('listings.trashed');
    Route::get('/receipt/{receipt}/download', [ReceiptController::class, 'download'])->name('receipt.download');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/buy-credits', \App\Livewire\BuyCredits::class)->name('buy-credits');
    Route::get('/verify-account', \App\Livewire\VerifyAccount::class)->name('verify-account');
    Route::get('/settings', \App\Livewire\UserSettings::class)->name('settings');
});

require __DIR__.'/auth.php';

/*
 * PayMongo webhook handler for GCash verification.
 * Must NOT be behind auth middleware — PayMongo sends requests directly.
 * Must NOT be behind CSRF — PayMongo signs requests with its API key, not our session.
 */
Route::post('/webhooks/paymongo', \App\Http\Controllers\PayMongoWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
