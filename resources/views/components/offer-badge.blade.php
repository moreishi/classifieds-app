@props(['status'])

@php
    $classes = match ($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'accepted' => 'bg-green-100 text-green-800',
        'declined' => 'bg-red-100 text-red-800',
        'countered' => 'bg-orange-100 text-orange-800',
        'expired' => 'bg-gray-100 text-gray-600',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ ucfirst($status) }}
</span>
