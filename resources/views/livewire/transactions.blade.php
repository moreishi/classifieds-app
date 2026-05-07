<div>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Transactions</h1>
            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('tab', 'all')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $tab === 'all' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All
                </button>
                <button wire:click="$set('tab', 'as_buyer')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $tab === 'as_buyer' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    As Buyer
                </button>
                <button wire:click="$set('tab', 'as_seller')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $tab === 'as_seller' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    As Seller
                </button>
                <button wire:click="$set('showForm', true)"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
                    + New Receipt
                </button>
            </div>
        </div>

        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        {{-- Generate receipt form --}}
        @if($showForm)
            <div class="bg-white rounded-xl border p-6 max-w-lg mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Generate Transaction Receipt</h2>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>

                <form wire:submit="generateReceipt" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Listing</label>
                        <select wire:model="listingId"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow">
                            <option value="">Select listing</option>
                            @foreach($listings as $listing)
                                <option value="{{ $listing->id }}">{{ $listing->title }} (₱{{ number_format($listing->price / 100) }})</option>
                            @endforeach
                        </select>
                        @error('listingId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Buyer Email</label>
                        <input type="email" wire:model="buyerEmail"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow"
                               placeholder="buyer@example.com" />
                        @error('buyerEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Buyer Name (optional)</label>
                        <input type="text" wire:model="buyerName"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount (₱)</label>
                        <input type="number" wire:model="amount" min="1"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-shadow" />
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        Generate Receipt
                    </button>
                </form>
            </div>
        @endif

        {{-- Receipts --}}
        @if($receipts->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-lg">No transactions yet</p>
                <p class="text-gray-400 text-sm mt-1">
                    @if($tab === 'as_buyer')
                        You haven't bought anything yet. Make an offer on a listing!
                    @elseif($tab === 'as_seller')
                        No sales yet. Accept an offer to create a transaction.
                    @else
                        Your completed transactions will appear here.
                    @endif
                </p>
            </div>
        @else
            {{-- Desktop table --}}
            <div class="hidden md:block bg-white rounded-xl border overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Reference</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Listing</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Buyer</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-700">Amount</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Role</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Date</th>
                            <th class="text-center px-4 py-3 font-medium text-gray-700">Review</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-700">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($receipts as $receipt)
                            @php
                                $isBuyer = $receipt->buyer_email === auth()->user()->email;
                                $existingReview = $receipt->reviews->where('reviewer_id', auth()->id())->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs">{{ $receipt->reference_number }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('listing.show', $receipt->listing->slug) }}"
                                       class="text-blue-600 hover:text-blue-800">{{ $receipt->listing->title }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $receipt->buyer_name ?: $receipt->buyer_email }}
                                    <span class="text-gray-400 text-xs block">{{ $receipt->buyer_email }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">₱{{ number_format($receipt->amount / 100) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $isBuyer ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $isBuyer ? 'Buyer' : 'Seller' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $receipt->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($isBuyer)
                                        @if($existingReview)
                                            <span class="text-yellow-400 text-sm">
                                                @for($i = 0; $i < $existingReview->rating; $i++)&#9733;@endfor
                                            </span>
                                        @else
                                            <button x-on:click="$el.closest('tr').nextElementSibling.classList.toggle('hidden')"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                Leave Review
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('receipt.download', $receipt->id) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Download PDF
                                    </a>
                                </td>
                            </tr>
                            @if($isBuyer && !$existingReview)
                                <tr class="hidden bg-yellow-50">
                                    <td colspan="8" class="px-4 py-4">
                                        <div class="max-w-lg mx-auto">
                                            <livewire:leave-review :receiptId="$receipt->id" :key="'review-'.$receipt->id" />
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden space-y-3">
                @foreach($receipts as $receipt)
                    @php
                        $isBuyer = $receipt->buyer_email === auth()->user()->email;
                        $existingReview = $receipt->reviews->where('reviewer_id', auth()->id())->first();
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        {{-- Card header --}}
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <span class="font-mono text-xs text-gray-500">{{ $receipt->reference_number }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $isBuyer ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                {{ $isBuyer ? 'Buyer' : 'Seller' }}
                            </span>
                        </div>

                        {{-- Card body --}}
                        <div class="px-4 py-3 space-y-2">
                            <a href="{{ route('listing.show', $receipt->listing->slug) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                {{ $receipt->listing->title }}
                            </a>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Buyer</span>
                                <span class="text-sm text-gray-900">{{ $receipt->buyer_name ?: $receipt->buyer_email }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Amount</span>
                                <span class="font-semibold text-gray-900">₱{{ number_format($receipt->amount / 100) }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Date</span>
                                <span class="text-sm text-gray-500">{{ $receipt->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        {{-- Card actions --}}
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-t border-gray-100">
                            @if($isBuyer && !$existingReview)
                                <button x-on:click="$el.closest('.md\\\\:hidden').querySelector('.review-form').classList.toggle('hidden')"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-1.5 border border-blue-200 rounded-lg">
                                    Leave Review
                                </button>
                            @elseif($isBuyer && $existingReview)
                                <span class="text-yellow-400 text-sm">
                                    @for($i = 0; $i < $existingReview->rating; $i++)&#9733;@endfor
                                </span>
                            @endif
                            <a href="{{ route('receipt.download', $receipt->id) }}"
                               class="ml-auto text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-1.5 border border-blue-200 rounded-lg">
                                Download PDF
                            </a>
                        </div>

                        {{-- Review form (hidden) --}}
                        @if($isBuyer && !$existingReview)
                            <div class="review-form hidden px-4 py-3 bg-yellow-50 border-t border-yellow-100">
                                <livewire:leave-review :receiptId="$receipt->id" :key="'review-'.$receipt->id" />
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</div>
