<div>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if(session('message'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
                {{ session('message') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Offers</h1>

            <div class="flex gap-2">
                <button wire:click="$set('tab', 'received')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $tab === 'received' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Received ({{ auth()->user()->receivedOffers()->count() }})
                </button>
                <button wire:click="$set('tab', 'sent')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $tab === 'sent' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Sent
                </button>
            </div>
        </div>

        @if($offers->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No offers yet</p>
                <p class="text-gray-400 text-sm mt-1">
                    @if($tab === 'sent')
                        You haven't made any offers.
                    @else
                        Wait for buyers to send you offers on your listings.
                    @endif
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($offers as $offer)
                    <div class="bg-white rounded-xl border p-5 {{ $offer->status === 'pending' ? 'ring-2 ring-blue-100' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('listing.show', $offer->listing->slug) }}"
                                       class="font-semibold text-gray-900 hover:text-blue-600">
                                        {{ $offer->listing->title }}
                                    </a>
                                    <x-offer-badge :status="$offer->status" />
                                </div>
                                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                    @if($tab === 'sent')
                                        To: <img src="{{ $offer->seller->avatar }}" alt="" class="w-4 h-4 rounded-full" /> {{ $offer->seller->publicName() }}
                                    @else
                                        From: <img src="{{ $offer->buyer->avatar }}" alt="" class="w-4 h-4 rounded-full" /> {{ $offer->buyer->publicName() }}
                                    @endif
                                    · {{ $offer->created_at->diffForHumans() }}
                                </p>
                                <div class="flex items-center gap-4 mt-2">
                                    <p class="text-xl font-bold text-blue-600">₱{{ number_format($offer->amount / 100) }}</p>
                                    @if($offer->status === 'countered' && $offer->counter_amount)
                                        <p class="text-sm text-gray-500">Counter: <span class="font-semibold text-orange-600">₱{{ number_format($offer->counter_amount / 100) }}</span></p>
                                    @endif
                                </div>
                                @if($offer->message)
                                    <p class="text-sm text-gray-600 mt-1 italic">"{{ $offer->message }}"</p>
                                @endif
                                @if($offer->counter_message)
                                    <p class="text-sm text-orange-600 mt-1 italic">Counter: "{{ $offer->counter_message }}"</p>
                                @endif
                            </div>

                            @if($tab === 'received' && $offer->status === 'pending')
                                <div class="flex gap-2 ml-4 shrink-0">
                                    <button wire:click="accept({{ $offer->id }})"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                        Accept
                                    </button>
                                    <button wire:click="decline({{ $offer->id }})"
                                            class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors">
                                        Decline
                                    </button>
                                    <button x-data
                                            x-on:click="$el.closest('div').querySelector('.counter-form').classList.toggle('hidden')"
                                            class="bg-orange-100 text-orange-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-200 transition-colors">
                                        Counter
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Counter form --}}
                        <div class="counter-form hidden mt-4 pt-4 border-t">
                            <form wire:submit="counter({{ $offer->id }}, counterAmount, counterMessage)">
                                <div class="flex gap-3 items-end">
                                    <div>
                                        <label class="text-sm text-gray-600">Counter offer (₱)</label>
                                        <input type="number" x-model="counterAmount"
                                               class="mt-1 block w-32 rounded-lg border-gray-300 text-sm"
                                               value="{{ $offer->amount / 100 }}">
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-sm text-gray-600">Message</label>
                                        <input type="text" x-model="counterMessage"
                                               class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                               placeholder="Optional message...">
                                    </div>
                                    <button type="submit"
                                            class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700">
                                        Send Counter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $offers->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Alpine data for counter form --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('counterForm', () => ({
            counterAmount: 0,
            counterMessage: '',
        }));
    });
</script>
