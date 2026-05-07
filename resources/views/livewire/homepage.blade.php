@push('head')
    <x-seo
        title="Iskina.ph — #1 Buy & Sell Marketplace in {{ $regionName }}"
        description="The #1 classified ads marketplace in {{ $regionName }}, Philippines. Buy and sell gadgets, cars, property, jobs, services, pets, and more near you. Post free ads and find deals in {{ $locationLabel }} and across the Philippines."
        :url="route('home')"
    />
@endpush

<div>
    {{-- Search Hero --}}
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 py-20 sm:px-6 lg:px-8">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-center mb-3 tracking-tight">#1 Marketplace in {{ $regionName }}</h1>
            <p class="text-blue-200 text-center mb-8 text-lg">Buy & sell gadgets, cars, property, and services near you</p>

            <div class="max-w-xl mx-auto">
                <div class="relative group">
                    <input
                        type="text"
                        wire:model.live="search"
                        wire:keydown.enter="searchListings"
                        placeholder="Search gadgets, cars, rooms in {{ $locationLabel }}..."
                        class="w-full px-5 py-4 pr-13 rounded-xl text-gray-900 text-base shadow-xl focus:ring-4 focus:ring-blue-300/50 focus:outline-none transition-shadow"
                    />
                    <button wire:click="searchListings" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex justify-center mt-6 gap-2 text-sm text-blue-200/80">
                <span>📍</span>
                <span>{{ $locationLabel }}, Philippines</span>
            </div>
        </div>
    </div>

    {{-- Category Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Browse by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="flex flex-col items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 border border-gray-100 group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition-transform duration-200">{{ $category->icon }}</span>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-blue-600 transition-colors">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Promoted Listings --}}
    <div class="max-w-7xl mx-auto px-4 pb-10 sm:px-6 lg:px-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            @if($promotedListings->isNotEmpty())
                <span class="inline-flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Promoted Listings
                </span>
            @else
                Featured from Iskina
            @endif
        </h2>

        @if($promotedListings->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <p class="text-gray-500 text-sm">No featured listings this week.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($promotedListings as $listing)
                    <a href="{{ route('listing.show', $listing->slug) }}"
                       class="group bg-white rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden border border-gray-100">
                        @php $thumb = $listing->getFirstMediaUrl('photos', 'thumb') ?: $listing->getFirstMediaUrl('photos'); @endphp
                        @if($thumb)
                            <div class="aspect-[4/3] bg-gray-200 overflow-hidden relative">
                                <img src="{{ $thumb }}" alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                @if($listing->featured_until && $listing->featured_until->isFuture())
                                    <span class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                        Promoted
                                    </span>
                                @endif
                                @auth
                                    <div class="absolute top-2 right-2 z-10" wire:key="favorite-{{ $listing->id }}" @click.stop>
                                        <livewire:toggle-favorite :listing="$listing" />
                                    </div>
                                @endauth
                            </div>
                        @else
                            <div class="aspect-[4/3] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 relative">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="p-3">
                            <h3 class="font-semibold text-sm text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $listing->title }}</h3>
                            @if($listing->status === 'sold')
                                <p class="text-sm font-bold text-gray-400 mt-1">
                                    <s>₱{{ number_format($listing->price / 100) }}</s>
                                </p>
                            @else
                                <p class="font-bold text-blue-600 mt-1">₱{{ number_format($listing->price / 100) }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-1.5 text-xs text-gray-500">
                                <span>📍 {{ $listing->city->name }}</span>
                                <span class="flex items-center gap-1.5">
                                    <img src="{{ $listing->user->avatar }}" alt="" aria-hidden="true" class="w-4 h-4 rounded-full ring-1 ring-gray-200" />
                                    <span class="truncate max-w-[80px]">{{ $listing->user->publicName() }}</span>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Latest Listings --}}
    @if($latestListings->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 pb-10 sm:px-6 lg:px-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Latest Listings</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($latestListings as $listing)
                    <a href="{{ route('listing.show', $listing->slug) }}"
                       class="group bg-white rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden border border-gray-100">
                        @php $thumb = $listing->getFirstMediaUrl('photos', 'thumb') ?: $listing->getFirstMediaUrl('photos'); @endphp
                        @if($thumb)
                            <div class="aspect-[4/3] bg-gray-200 overflow-hidden relative">
                                <img src="{{ $thumb }}" alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            </div>
                        @else
                            <div class="aspect-[4/3] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="p-3">
                            <h3 class="font-semibold text-sm text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $listing->title }}</h3>
                            @if($listing->status === 'sold')
                                <p class="text-sm font-bold text-gray-400 mt-1">
                                    <s>₱{{ number_format($listing->price / 100) }}</s>
                                </p>
                            @else
                                <p class="font-bold text-blue-600 mt-1">₱{{ number_format($listing->price / 100) }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-1.5 text-xs text-gray-500">
                                <span>📍 {{ $listing->city->name }}</span>
                                <span class="flex items-center gap-1.5">
                                    <img src="{{ $listing->user->avatar }}" alt="" aria-hidden="true" class="w-4 h-4 rounded-full ring-1 ring-gray-200" />
                                    <span class="truncate max-w-[80px]">{{ $listing->user->publicName() }}</span>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
