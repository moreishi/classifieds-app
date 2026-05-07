<div>
    <div class="max-w-3xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Trashed Listings</h1>
                <p class="text-sm text-gray-500 mt-1">Deleted listings can be restored within 30 days.</p>
            </div>
            <a href="{{ route('listings.create') }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New Listing
            </a>
        </div>

        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        @if($listings->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No trashed listings</p>
                <p class="text-gray-400 text-sm mt-1">Deleted listings will appear here for 30 days.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($listings as $listing)
                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $listing->title }}</h3>
                            <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                                <span>₱{{ number_format($listing->price / 100) }}</span>
                                <span>📍 {{ $listing->city?->name ?? '—' }}</span>
                                <span class="text-red-500">Deleted {{ $listing->deleted_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-4">
                            <button wire:click="restore({{ $listing->id }})"
                                    class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                Restore
                            </button>
                            <button wire:click="forceDelete({{ $listing->id }})"
                                    wire:confirm="Permanently delete '{{ $listing->title }}'? This cannot be undone."
                                    class="px-3 py-1.5 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors">
                                Delete Forever
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>
