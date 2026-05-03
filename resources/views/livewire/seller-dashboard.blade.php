<div>
    <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Seller Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Overview of your listings and activity.</p>
            </div>
            <a href="{{ route('listings.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                + New Listing
            </a>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeCount }}</p>
                        <p class="text-xs text-gray-500">Active Listings</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $soldCount }}</p>
                        <p class="text-xs text-gray-500">Sold</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalViews) }}</p>
                        <p class="text-xs text-gray-500">Total Views</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalInquiries }}</p>
                        <p class="text-xs text-gray-500">Inquiries</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Listing Performance Table --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Your Listings</h2>
                </div>

                @if($listingStats->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <p>No listings yet.</p>
                        <a href="{{ route('listings.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-1 inline-block">
                            Create your first listing →
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-5 py-3 font-medium">Listing</th>
                                    <th class="px-3 py-3 font-medium">Status</th>
                                    <th class="px-3 py-3 font-medium text-center">Views</th>
                                    <th class="px-3 py-3 font-medium text-center">Unique</th>
                                    <th class="px-3 py-3 font-medium text-center">Inquiries</th>
                                    <th class="px-3 py-3 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($listingStats as $stat)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 shrink-0 overflow-hidden">
                                                    @if($stat['thumb'])
                                                        <img src="{{ $stat['thumb'] }}" alt="" class="w-full h-full object-cover" />
                                                    @else
                                                        <div class="flex items-center justify-center h-full text-gray-300">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-medium text-gray-900 truncate max-w-[200px]">{{ $stat['title'] }}</p>
                                                    <p class="text-xs text-gray-400">₱{{ number_format($stat['price'] / 100) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if($stat['status'] === 'sold')
                                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">Sold</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Active</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center text-gray-700">{{ number_format($stat['views']) }}</td>
                                        <td class="px-3 py-3 text-center text-gray-700">{{ number_format($stat['unique_views']) }}</td>
                                        <td class="px-3 py-3 text-center text-gray-700">{{ $stat['inquiries'] }}</td>
                                        <td class="px-3 py-3 text-right">
                                            <a href="{{ route('listing.show', $stat['slug']) }}"
                                               class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                        <a href="{{ route('listings.my') }}"
                           class="text-sm text-blue-600 hover:text-blue-800">
                            View all listings →
                        </a>
                    </div>
                @endif
            </div>

            {{-- Right sidebar: Recent Inquiries --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Recent Inquiries</h2>
                </div>

                @if($recentInquiries->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <p class="text-sm">No inquiries yet.</p>
                        <p class="text-xs text-gray-400 mt-1">They'll appear here when buyers message you.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($recentInquiries as $conv)
                            <div class="px-5 py-3 hover:bg-gray-50/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600 shrink-0">
                                        {{ strtoupper(substr($conv->buyer->publicName(), 0, 2)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $conv->buyer->publicName() }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            Re: {{ $conv->listing->title }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $conv->last_message_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if($conv->messages()->where('sender_id', '!=', auth()->id())->whereNull('read_at')->exists())
                                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                        <a href="{{ route('conversations.index') }}"
                           class="text-sm text-blue-600 hover:text-blue-800">
                            View all messages →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
