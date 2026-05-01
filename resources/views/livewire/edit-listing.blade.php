<div>
    <div class="max-w-2xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('listing.show', $listing->slug) }}"
               class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Listing</h1>
        </div>

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

            {{-- Existing Photos --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Current Photos ({{ count($existingPhotos) }}/5)
                </label>
                @if(count($existingPhotos) > 0)
                    <div class="flex flex-wrap gap-3">
                        @foreach($existingPhotos as $photo)
                            <div class="relative group">
                                <img src="{{ $photo['url'] }}"
                                     alt="{{ $photo['name'] }}"
                                     class="w-24 h-24 object-cover rounded-lg border border-gray-200" />
                                <button type="button"
                                        wire:click="removePhoto({{ $photo['id'] }})"
                                        wire:confirm="Remove this photo?"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center
                                               opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-red-600 text-sm font-bold">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">No photos yet. Add at least one below.</p>
                @endif
            </div>

            {{-- New Photos --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Add More Photos{{ count($existingPhotos) > 0 ? ' (optional)' : '' }}
                </label>
                <p class="text-xs text-gray-400 mb-1">
                    Total must be 1-5 photos. You can add {{ max(0, 5 - count($existingPhotos) - count($newPhotos)) }} more.
                </p>
                <input type="file" wire:model="newPhotos" multiple accept="image/jpeg,image/png,image/webp"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                @error('newPhotos') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('newPhotos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                @if($newPhotos)
                    <div class="flex gap-2 mt-2">
                        @foreach($newPhotos as $photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg"/>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex gap-4 pt-2">
                <a href="{{ route('listing.show', $listing->slug) }}"
                   class="w-full text-center py-3 rounded-xl font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
