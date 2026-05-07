<button wire:click="toggle"
        class="p-2 rounded-lg transition-all {{ $isFavorited ? 'text-red-500 bg-red-50 hover:bg-red-100' : 'text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50' }}"
        title="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
    </svg>
</button>
