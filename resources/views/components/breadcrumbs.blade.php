@props(['items' => []])

{{-- Usage: <x-breadcrumbs :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'Current Page']]" /> --}}

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex items-center gap-1.5 text-sm text-gray-500">
        @foreach($items as $index => $item)
            <li class="flex items-center gap-1.5">
                @if($index > 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif

                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-blue-600 transition-colors truncate max-w-[200px]" {{ $index < count($items) - 1 ? '' : 'aria-current="page"' }}>
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-900 font-medium truncate max-w-[200px]" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
