@php
    $listingUrl = route('listing.show', $listing->slug);
    $listingImage = $listing->getFirstMediaUrl('photos') ?: asset('img/og-default.png');
    $listingDescription = strip_tags(Str::limit($listing->description, 160));

    // JSON-LD structured data for Product + LocalBusiness
    $jsonLd = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $listing->title,
        'description' => $listingDescription,
        'image' => $listingImage,
        'url' => $listingUrl,
        'category' => $listing->category?->name,
        'offers' => [
            '@type' => 'Offer',
            'price' => number_format($listing->price / 100, 2),
            'priceCurrency' => 'PHP',
            'availability' => $listing->status === 'active' ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
            'seller' => [
                '@type' => 'Person',
                'name' => $listing->user->username ?? $listing->user->name,
            ],
        ],
        'contentLocation' => [
            '@type' => 'City',
            'name' => $listing->city?->name ?? 'Cebu',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
@endphp

@push('head')
    <x-seo
        title="{{ $listing->title }}"
        description="{{ $listingDescription }}. ₱{{ number_format($listing->price / 100) }} in {{ $listing->city?->name ?? 'Cebu' }}."
        image="{{ $listingImage }}"
        :url="$listingUrl"
        type="product"
        :jsonLd="$jsonLd"
    />
@endpush

<div>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $listing->category?->name ?? 'Listings', 'url' => route('category.show', $listing->category?->slug)],
            ['label' => $listing->title],
        ]" />

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Photo gallery --}}
                @php $media = $listing->getMedia('photos'); @endphp
                @if($media->count() > 0)
                    <div x-data="{ active: 0, images: [
                        @foreach($media as $img)
                            { card: '{{ $img->getUrl('card') ?: $img->getUrl() }}', thumb: '{{ $img->getUrl('thumb') ?: $img->getUrl() }}' },
                        @endforeach
                    ]}" class="space-y-2">
                        {{-- Main Image --}}
                        <div class="bg-gray-100 rounded-xl overflow-hidden">
                            <img x-bind:src="images[active].card"
                                 alt="{{ $listing->title }}"
                                 class="w-full h-80 sm:h-96 object-contain" />
                        </div>
                        {{-- Thumbnail strip --}}
                        @if($media->count() > 1)
                            <div class="flex gap-2 overflow-x-auto pb-1">
                                <template x-for="(img, i) in images" :key="i">
                                    <button x-on:click="active = i"
                                            class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 focus:outline-none"
                                            x-bind:class="active === i ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-400'">
                                        <img x-bind:src="img.thumb" class="w-full h-full object-cover" />
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-100 rounded-xl h-80 flex items-center justify-center text-gray-400">
                        <span class="text-lg">No photos yet</span>
                    </div>
                @endif

                {{-- Title & Price --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $listing->title }}</h1>
                    <p class="text-3xl font-bold text-blue-600 mt-2">₱{{ number_format($listing->price / 100) }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>📍 {{ $listing->city->name }}</span>
                        <span>📂 {{ $listing->category->name }}</span>
                        @if($listing->condition)
                            <span>🔧 {{ str_replace('_', ' ', ucfirst($listing->condition)) }}</span>
                        @endif
                        <span class="font-mono text-xs text-gray-400">Ref: {{ $listing->reference_id }}</span>
                    </div>
                </div>

                {{-- Description --}}
                <div class="prose max-w-none">
                    <h2 class="text-lg font-semibold text-gray-900">Description</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $listing->description }}</p>
                </div>

                {{-- Offer history --}}
                @if($listing->offers->count() > 0)
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Offers</h2>
                        <p class="text-sm text-gray-500">{{ $listing->offers->count() }} offer(s) received</p>
                    </div>
                @endif

                {{-- Reviews --}}
                @if($listing->reviews->count() > 0)
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Reviews</h2>
                        @foreach($listing->reviews as $review)
                            <div class="border-b py-3">
                                <div class="flex items-center gap-1 text-yellow-400">
                                    @for($i = 0; $i < $review->rating; $i++)⭐@endfor
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-700 text-sm mt-1">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-4">
                {{-- Edit Button (owner only) --}}
                @auth
                    @if(auth()->id() === $listing->user_id && $listing->status !== 'sold')
                        <a href="{{ route('listings.edit', $listing->slug) }}"
                           class="block w-full text-center bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                            Edit Listing
                        </a>

                        {{-- Bump / Promote (owner only) --}}
                        <livewire:bump-listing :listing="$listing" wire:key="bump-{{ $listing->id }}" />
                    @endif
                @endauth

                {{-- Seller Card --}}
                <div class="bg-white rounded-xl border p-5">
                    <h3 class="font-semibold text-gray-900">Seller</h3>
                    <div class="mt-3 flex items-center gap-3">
                        <img src="{{ $seller->avatar }}" alt="" class="w-10 h-10 rounded-full" />
                        <div>
                            <p class="font-medium text-gray-900 flex items-center gap-1">
                                {{ $seller->username ?? $seller->name }}
                                @if($seller->gcash_verified_at)
                                    <span title="GCash Verified" class="inline-flex items-center justify-center w-4 h-4 bg-green-500 text-white rounded-full text-[10px] font-bold">&#10003;</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">
                                <x-member-badge :user="$seller" size="sm" />
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-500 space-y-1">
                        <p>Member since {{ $seller->created_at->format('M Y') }}</p>
                        @php
                            $sellerStats = app(\App\Services\ReputationService::class)->userStats($seller);
                        @endphp
                        @if($sellerStats['total_points'] > 0)
                            <p>{{ number_format($sellerStats['seller_points']) }} seller pts · {{ number_format($sellerStats['buyer_points']) }} buyer pts</p>
                        @endif
                        {{-- Last Active --}}
                        @if($seller->last_active_at)
                            <p class="flex items-center gap-1.5">
                                @php
                                    $minutesSince = $seller->last_active_at->diffInMinutes(now());
                                @endphp
                                @if($minutesSince < 5)
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    <span class="text-green-600 font-medium">Online now</span>
                                @elseif($minutesSince < 60)
                                    <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                                    Active {{ $minutesSince }}m ago
                                @elseif($seller->last_active_at->isToday())
                                    <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                                    Active today
                                @else
                                    <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                                    Active {{ $seller->last_active_at->diffForHumans() }}
                                @endif
                            </p>
                        @else
                            <p class="text-gray-400">No recent activity</p>
                        @endif
                    </div>
                </div>

                {{-- Favorite Button --}}
                @auth
                    <div wire:key="favorite-{{ $listing->id }}">
                        <livewire:toggle-favorite :listing="$listing" />
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-2 text-gray-400 hover:text-red-400 transition-colors text-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                        <span>Log in to save</span>
                    </a>
                @endauth

                {{-- Actions --}}
                @auth
                    @if($listing->status === 'sold' && auth()->id() !== $listing->user_id)
                        {{-- Sold listing — no actions for buyers --}}
                        <div class="bg-gray-100 text-gray-500 text-center py-3 rounded-xl text-sm font-medium">
                            This item has been sold
                        </div>
                    @else
                        @if(auth()->id() !== $listing->user_id)
                            <button wire:click="openInquiry"
                                    class="w-full text-center bg-white border border-blue-600 text-blue-600 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-colors">
                                {{ $alreadyContacted ? 'Open Chat' : 'Message Seller' }}
                            </button>
                        @endif

                        <button
                            wire:click="$dispatch('openOfferModal', { listingId: {{ $listing->id }} })"
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors"
                        >
                            Send Offer
                        </button>

                        @if(auth()->id() === $listing->user_id)
                            <button
                                wire:click="markAsSold"
                                class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors"
                            >
                                Mark as Sold
                            </button>
                        @endif
                    @endif
                @else
                    @if($listing->status === 'sold')
                        <div class="bg-gray-100 text-gray-500 text-center py-3 rounded-xl text-sm font-medium">
                            This item has been sold
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                            Log in to send offer
                        </a>
                    @endif
                @endauth

                {{-- Report Listing --}}
                <livewire:report-listing :listing="$listing" :key="'report-'.$listing->id" />
            </aside>
        </div>
    </div>

    @auth
        <livewire:offer-modal />

        {{-- Inquiry Modal (Angle 2) --}}
        @if($showInquiryModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                 wire:click.self="cancelInquiry">
                <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Message Seller</h2>
                        <button wire:click="cancelInquiry" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                    </div>

                    <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl">
                        <img src="{{ $seller->avatar }}" alt="" class="w-10 h-10 rounded-full" />
                        <div>
                            <p class="font-medium text-gray-900">{{ $seller->username ?? $seller->name }}</p>
                            <p class="text-xs text-gray-500">Re: {{ $listing->title }}</p>
                        </div>
                    </div>

                    <form wire:submit="sendInquiry" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Message</label>
                            <textarea wire:model="inquiryMessage" rows="4"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Hi! Is this still available? I'm interested in buying..."
                                      maxlength="2000"></textarea>
                            @error('inquiryMessage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-400 mt-1 text-right"
                               x-text="$wire.inquiryMessage.length + '/2000'">0/2000</p>
                        </div>

                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors"
                                wire:loading.attr="disabled" wire:target="sendInquiry">
                            <span wire:loading.remove wire:target="sendInquiry">Send Message</span>
                            <span wire:loading wire:target="sendInquiry">Sending...</span>
                        </button>
                    </form>

                    <p class="text-xs text-gray-400 mt-3 text-center">
                        You'll be redirected to the conversation once sent.
                    </p>
                </div>
            </div>
        @endif
    @endauth
</div>
