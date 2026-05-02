@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'url' => null,
    'type' => 'website',
    'noindex' => false,
    'jsonLd' => null,
])

{{--
Usage:
    @push('head')
        <x-seo
            title="iPhone 14 Pro Max 256GB"
            description="Brand new iPhone 14 Pro Max in Deep Purple. ₱45,000. Seller in Cebu City."
            image="{{ $listing->getFirstMediaUrl('photos') }}"
            :url="route('listing.show', $listing->slug)"
            type="product"
        />
    @endpush

For listing detail, structured data is added separately via @jsonLd push.
For internal pages, pass noindex="true" to suppress indexing.
--}}

@php
    $appName = config('app.name', 'Iskina.ph');
    $finalTitle = $title ? "{$title} — {$appName}" : $appName;
    $finalDescription = $description ?? 'Buy and sell locally in Cebu. The marketplace for everything near you.';
    $finalUrl = $url ?? url()->current();
    $finalImage = $image ?? asset('img/og-default.png');
@endphp

{{-- Canonical URL — tells Google which URL is the authoritative one --}}
<link rel="canonical" href="{{ $finalUrl }}">

{{-- noindex for internal/auth pages --}}
@if($noindex)
    <meta name="robots" content="noindex, nofollow">
@endif

{{-- Standard meta --}}
<meta name="description" content="{{ $finalDescription }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $finalTitle }}">
<meta property="og:description" content="{{ $finalDescription }}">
<meta property="og:url" content="{{ $finalUrl }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:image" content="{{ $finalImage }}">
<meta property="og:site_name" content="{{ $appName }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $finalTitle }}">
<meta name="twitter:description" content="{{ $finalDescription }}">
<meta name="twitter:image" content="{{ $finalImage }}">

{{-- Structured data (JSON-LD) — passed directly as string --}}
@if($jsonLd)
    <script type="application/ld+json">
        {!! $jsonLd !!}
    </script>
@endif

{{-- Default title fallback when no seo component is used --}}
<title>{{ $finalTitle }}</title>
