<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Listing;
use App\Notifications\NewInquiry;
use App\Services\ListingViewService;
use Livewire\Component;

class ListingDetail extends Component
{
    public Listing $listing;

    // Inquiry modal
    public bool $showInquiryModal = false;
    public string $inquiryMessage = '';
    public bool $alreadyContacted = false;

    protected function rules(): array
    {
        return [
            'inquiryMessage' => 'required|string|max:2000',
        ];
    }

    public function mount(string $slug, ListingViewService $viewService): void
    {
        $this->listing = Listing::where('slug', $slug)
            ->with(['user', 'category', 'city', 'reviews', 'offers'])
            ->firstOrFail();

        $viewService->recordView($this->listing, request());

        // Check if this buyer already has a convo with this seller
        if ($user = auth()->user()) {
            $this->alreadyContacted = Conversation::where('listing_id', $this->listing->id)
                ->where('buyer_id', $user->id)
                ->exists();
        }
    }

    public function openInquiry(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        if ($this->alreadyContacted) {
            // Already have a conversation, jump straight to it
            $conversation = Conversation::where('listing_id', $this->listing->id)
                ->where('buyer_id', auth()->id())
                ->first();
            $this->redirect(route('conversations.show', $conversation));
            return;
        }

        $this->showInquiryModal = true;
    }

    public function sendInquiry(): void
    {
        $this->validate();

        $buyer = auth()->user();

        if ($buyer->id === $this->listing->user_id) {
            $this->dispatch('inquiry-error', message: "You can't message your own listing.");
            return;
        }

        if ($this->listing->status === 'sold') {
            $this->dispatch('inquiry-error', message: 'This item has been sold.');
            return;
        }

        $conversation = Conversation::create([
            'listing_id' => $this->listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $this->listing->user_id,
        ]);

        // Add the inquiry message
        $conversation->messages()->create([
            'sender_id' => $buyer->id,
            'body' => $this->inquiryMessage,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Notify the seller
        $conversation->seller->notify(new NewInquiry($conversation));

        $this->showInquiryModal = false;
        $this->alreadyContacted = true;

        $this->redirect(route('conversations.show', $conversation));
    }

    public function cancelInquiry(): void
    {
        $this->showInquiryModal = false;
        $this->inquiryMessage = '';
        $this->resetErrorBag();
    }

    public function markAsSold(): void
    {
        if (auth()->id() !== $this->listing->user_id) {
            return;
        }

        $this->listing->update([
            'status' => 'sold',
            'sold_at' => now(),
        ]);

        $this->dispatch('listing-sold');
    }

    public function render()
    {
        return view('livewire.listing-detail', [
            'seller' => $this->listing->user,
        ])->layout('layouts.app');
    }
}
