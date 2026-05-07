<div>
    <div class="max-w-2xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create a Listing</h1>
        <p class="text-gray-500 mb-8">Fill in the details below to post your listing.</p>

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

        <form wire:submit="submit" class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6 shadow-sm">
                <h2 class="font-semibold text-gray-900 text-lg">Listing Details</h2>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select wire:model="categoryId"
                            class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1.5 text-xs text-gray-500">
                        @php $remaining = app(\App\Services\CreditService::class)->freeListingsRemaining(auth()->user()); @endphp
                        Posting fee applies. You have <strong>{{ $remaining }} / {{ \App\Services\CreditService::freeListingsLimit(auth()->user()->reputation_tier) }}</strong> free listings remaining this month.
                    </p>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" wire:model="title" maxlength="100"
                           class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow"
                           placeholder="e.g. iPhone 14 Pro Max 256GB"/>
                    <div class="flex justify-between mt-1.5">
                        @error('title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 ml-auto">{{ strlen($title) }}/100</p>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" rows="5"
                              class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow"
                              placeholder="Describe your item..."></textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price (₱)</label>
                        <input type="number" wire:model="price" min="1"
                               class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow"
                               placeholder="45000"/>
                        @error('price') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Condition --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Condition</label>
                        <select wire:model="condition"
                                class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                            <option value="">Select condition</option>
                            <option value="brand_new">Brand New</option>
                            <option value="like_new">Like New</option>
                            <option value="used">Used</option>
                            <option value="for_parts">For Parts</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6 shadow-sm">
                <h2 class="font-semibold text-gray-900 text-lg">Location</h2>

                {{-- Province --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Province</label>
                    <select wire:model.live="provinceId"
                            class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                        <option value="">Select province</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">City / Municipality</label>
                    <select wire:model="cityId"
                            class="mt-1.5 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                        <option value="">Select city</option>
                        @foreach($allCities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('cityId') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4 shadow-sm">
                <h2 class="font-semibold text-gray-900 text-lg">Photos</h2>

                {{-- Photos --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Photos (1-5, max 5MB each)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                        <input type="file" wire:model="photos" multiple accept="image/jpeg,image/png,image/webp"
                               wire:loading.attr="disabled" wire:target="photos"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:disabled:opacity-50 cursor-pointer"/>
                    </div>
                    @error('photos.*') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror

                    <div wire:loading wire:target="photos" class="mt-3">
                        <div class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 rounded-lg px-4 py-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading photos...
                        </div>
                    </div>

                    @if($photos)
                        <div class="flex gap-2 mt-3">
                            @foreach($photos as $photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg ring-1 ring-gray-200"/>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submit,photos"
                    class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="submit,photos">Post Listing</span>
                <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Posting...
                </span>
                <span wire:loading wire:target="photos" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Uploading photos...
                </span>
            </button>
        </form>
    </div>
</div>
