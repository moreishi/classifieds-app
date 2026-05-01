<div>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Transactions</h1>

            <div class="flex gap-2">
                <button wire:click="$set('tab', 'receipts')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ $tab === 'receipts' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Receipts
                </button>
                <button wire:click="$set('tab', 'generate')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                               {{ $tab === 'generate' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Generate Receipt
                </button>
            </div>
        </div>

        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        @if($tab === 'generate')
            <div class="bg-white rounded-xl border p-6 max-w-lg">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate Transaction Receipt</h2>

                <form wire:submit="generateReceipt" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Listing</label>
                        <select wire:model="listingId"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="buyer@example.com" />
                        @error('buyerEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Buyer Name (optional)</label>
                        <input type="text" wire:model="buyerName"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount (₱)</label>
                        <input type="number" wire:model="amount" min="1"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        Generate Receipt
                    </button>
                </form>
            </div>
        @else
            @if($receipts->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-xl">
                    <p class="text-gray-500 text-lg">No receipts yet</p>
                    <p class="text-gray-400 text-sm mt-1">Generate receipts for completed transactions.</p>
                </div>
            @else
                <div class="bg-white rounded-xl border overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Reference</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Listing</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Buyer</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-700">Amount</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Status</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Date</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-700">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($receipts as $receipt)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-xs">{{ $receipt->reference_number }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('listing.show', $receipt->listing->slug) }}"
                                           class="text-blue-600 hover:text-blue-800">{{ $receipt->listing->title }}</a>
                                    </td>
                                    <td class="px-4 py-3">{{ $receipt->buyer_email }}<br/><span class="text-gray-400 text-xs">{{ $receipt->buyer_name }}</span></td>
                                    <td class="px-4 py-3 text-right font-semibold">₱{{ number_format($receipt->amount / 100) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $receipt->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($receipt->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $receipt->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('receipt.download', $receipt->id) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Download PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $receipts->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
