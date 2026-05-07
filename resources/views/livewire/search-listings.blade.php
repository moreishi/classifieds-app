@push('head')
    @if(isset($category))
        <x-seo
            title="{{ $category->name }} for Sale in Cebu"
            description="Browse {{ $category->name }} for sale in Cebu, Philippines. {{ $category->description ?? 'Find the best deals and prices on Iskina.ph, the #1 classified ads marketplace in Cebu.' }}"
            :url="route('category.show', $category->slug)"
        />
    @elseif(strlen($searchTerm) >= 2)
        <x-seo
            title="{{ $searchTerm }} — Classified Ads in Cebu"
            description="Search results for '{{ $searchTerm }}' on Iskina.ph. Find listings, prices, and sellers in Cebu, Philippines."
        />
    @else
        <x-seo
            title="Browse All Listings in Cebu"
            description="Browse all classified ads and listings in Cebu, Philippines. Find gadgets, cars, property, jobs, services, and more on Iskina.ph."
        />
    @endif
@endpush

<div>
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            @isset($category)
                <x-breadcrumbs :items="[
                    ['label' => 'Home', 'url' => route('home')],
                    ['label' => $category->name],
                ]" />
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-5xl">{{ $category->icon }}</span>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Post price: ₱{{ number_format($category->post_price / 100) }}</p>
                    </div>
                </div>
                <div class="relative max-w-xl">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search in {{ $category->name }}..."
                        class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-shadow"
                    />
                </div>

                @if($subcategories->isNotEmpty())
                    <div class="flex flex-wrap gap-2 sm:gap-3 mt-4">
                        @foreach($subcategories as $sub)
                            <a href="{{ route('category.show', $sub->slug) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 text-sm text-gray-600 rounded-full transition-all leading-tight">
                                <span class="shrink-0">{{ $sub->icon }}</span>
                                <span class="truncate max-w-[120px] sm:max-w-none">{{ $sub->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            @else
                {{-- Search results --}}
                <h1 class="text-3xl font-bold text-gray-900 mb-3">
                    @if(strlen($searchTerm) >= 2)
                        Results for "{{ $searchTerm }}"
                    @else
                        Search Iskina.ph
                    @endif
                </h1>
                <div class="relative max-w-xl">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="q"
                        placeholder="Search gadgets, cars, rooms in Cebu..."
                        class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-shadow"
                    />
                </div>
            @endisset
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Filters sidebar --}}
            <aside class="lg:w-64 shrink-0">
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Filters</h3>
                        <button wire:click="$set('provinceId', ''); $set('citySlug', ''); $set('minPrice', ''); $set('maxPrice', ''); $set('condition', ''); $set('sort', 'newest')"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                            Reset all
                        </button>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Province</label>
                        <select wire:model.live="provinceId"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                            <option value="">All provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">City</label>
                        <select wire:model.live="citySlug"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                            <option value="">All cities</option>
                            @foreach($allCities as $city)
                                <option value="{{ $city->slug }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Price range (₱)</label>
                        <div class="flex gap-2 mt-1.5">
                            <input type="number" wire:model.live="minPrice" placeholder="Min"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow"/>
                            <input type="number" wire:model.live="maxPrice" placeholder="Max"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow"/>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Condition</label>
                        <select wire:model.live="condition"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                            <option value="">All</option>
                            <option value="brand_new">Brand New</option>
                            <option value="like_new">Like New</option>
                            <option value="used">Used</option>
                            <option value="for_parts">For Parts</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Sort by</label>
                        <select wire:model.live="sort"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                            <option value="newest">Newest first</option>
                            <option value="oldest">Oldest first</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-500 mb-4">
                    {{ method_exists($listings, 'total') ? $listings->total() : $listings->count() }}
                    listing{{ method_exists($listings, 'total') && $listings->total() !== 1 ? 's' : '' }} found
                </p>

                @if($listings->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No listings found</p>
                        <p class="text-gray-400 text-sm mt-1">Try adjusting your filters or search terms</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($listings as $listing)
                            <a href="{{ route('listing.show', $listing->slug) }}"
                               class="group bg-white rounded-xl border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
                                @php $thumb = $listing->getFirstMediaUrl('photos', 'thumb') ?: $listing->getFirstMediaUrl('photos'); @endphp
                                @if($thumb)
                                    <div class="aspect-[4/3] bg-gray-100 overflow-hidden relative">
                                        <img src="{{ $thumb }}" alt="{{ $listing->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                        @auth
                                            <div class="absolute top-2 right-2 z-10" wire:key="favorite-{{ $listing->id }}" @click.stop>
                                                <livewire:toggle-favorite :listing="$listing" />
                                            </div>
                                        @endauth
                                    </div>
                                @else
                                    <div class="aspect-[4/3] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 relative">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        @auth
                                            <div class="absolute top-2 right-2 z-10" wire:key="favorite-{{ $listing->id }}" @click.stop>
                                                <livewire:toggle-favorite :listing="$listing" />
                                            </div>
                                        @endauth
                                    </div>
                                @endif
                                <div class="p-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-gray-900 text-sm truncate group-hover:text-blue-600 transition-colors">{{ $listing->title }}</h3>
                                        @if($listing->status === 'sold')
                                            <span class="shrink-0 bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Sold</span>
                                        @endif
                                    </div>
                                    @if($listing->status === 'sold')
                                        <p class="text-lg font-bold text-gray-400 mt-0.5">
                                            <s>₱{{ number_format($listing->price / 100) }}</s>
                                        </p>
                                    @else
                                        <p class="text-lg font-bold text-blue-600 mt-0.5">₱{{ number_format($listing->price / 100) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-1.5 text-xs text-gray-500">
                                        <span>📍 {{ $listing->city->name }}</span>
                                        <span>{{ $listing->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1.5 text-xs text-gray-500">
                                        <img src="{{ $listing->user->avatar }}" alt="" class="w-4 h-4 rounded-full ring-1 ring-gray-200" />
                                        <span>{{ $listing->user->publicName() }}</span>
                                        @if($listing->user->gcash_verified_at)
                                            <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full bg-green-500 text-white text-[8px] font-bold" title="GCash Verified">✓</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
