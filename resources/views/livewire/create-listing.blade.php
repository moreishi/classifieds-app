<div>
    <div class="max-w-2xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create a Listing</h1>

        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="submit" class="space-y-6">
            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select wire:model="categoryId"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">
                    @php $remaining = app(\App\Services\CreditService::class)->freeListingsRemaining(auth()->user()); @endphp
                    Posting fee applies. You have <strong>{{ $remaining }} / {{ \App\Services\CreditService::freeListingsLimit(auth()->user()->reputation_tier) }}</strong> free listings remaining this month.
                </p>
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" wire:model="title" maxlength="100"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g. iPhone 14 Pro Max 256GB"/>
                <p class="mt-1 text-xs text-gray-400">{{ strlen($title) }}/100</p>
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="description" rows="5"
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describe your item..."></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Price --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Price (₱)</label>
                <input type="number" wire:model="price" min="1"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="45000"/>
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Condition --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Condition</label>
                <select wire:model="condition"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select condition</option>
                    <option value="brand_new">Brand New</option>
                    <option value="like_new">Like New</option>
                    <option value="used">Used</option>
                    <option value="for_parts">For Parts</option>
                </select>
            </div>

            {{-- City --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">City</label>
                <select wire:model="cityId"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
                @error('cityId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Photos --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Photos (1-5, max 5MB each)</label>
                <input type="file" wire:model="photos" multiple accept="image/jpeg,image/png,image/webp"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                @if($photos)
                    <div class="flex gap-2 mt-2">
                        @foreach($photos as $photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg"/>
                        @endforeach
                    </div>
                @endif
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                Post Listing
            </button>
        </form>
    </div>
</div>
