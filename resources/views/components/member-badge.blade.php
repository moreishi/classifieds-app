@props(['user' => null, 'size' => 'sm', 'showPoints' => false])

{{--
Usage:
    <x-member-badge :user="$seller" size="sm" />
    <x-member-badge :user="$seller" size="lg" :showPoints="true" />

Tiers:
    newbie   (0-99)     — No badge, just "New"
    verified (100-499)  — Verified
    trusted  (500-999)  — Trusted
    pro      (1000+)    — Pro

Points are max of seller_points + buyer_points.
Anti-cheat: Account must be 7+ days + GCash verified for points to count.
--}}

@php
    if (!$user) return;

    $tier = $user->reputation_tier ?? 'newbie';
    $maxPoints = max($user->reputation_points ?? 0, $user->buyer_points ?? 0);

    $config = match ($tier) {
        'pro' => [
            'label' => 'Pro',
            'bg' => 'bg-gradient-to-r from-yellow-400 to-amber-500',
            'text' => 'text-white',
            'icon' => '🏆',
            'border' => 'border-yellow-300',
        ],
        'trusted' => [
            'label' => 'Trusted',
            'bg' => 'bg-green-500',
            'text' => 'text-white',
            'icon' => '✅',
            'border' => 'border-green-300',
        ],
        'verified' => [
            'label' => 'Verified',
            'bg' => 'bg-blue-500',
            'text' => 'text-white',
            'icon' => '✓',
            'border' => 'border-blue-300',
        ],
        default => [
            'label' => 'New',
            'bg' => 'bg-gray-200',
            'text' => 'text-gray-600',
            'icon' => '○',
            'border' => 'border-gray-300',
        ],
    };

    $sizeClasses = $size === 'lg'
        ? 'text-xs px-2.5 py-1 rounded-lg'
        : 'text-[10px] px-1.5 py-0.5 rounded-md';
@endphp

<span class="inline-flex items-center gap-1 font-semibold {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }} {{ $sizeClasses }} border leading-none whitespace-nowrap" title="{{ $maxPoints }} pts">
    <span>{{ $config['icon'] }}</span>
    <span>{{ $config['label'] }}</span>
    @if($showPoints)
        <span class="opacity-75 ml-0.5">· {{ $maxPoints }} pts</span>
    @endif
</span>
