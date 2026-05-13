<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Google Site Verification — for Search Console --}}
        <meta name="google-site-verification" content="{{ config('services.google.site_verification', '') }}" />

        {{-- SEO / Open Graph — individual pages push via x-seo component --}}
        @stack('head')

        {{-- Fallback title + noindex for pages without x-seo (dashboard, etc.) --}}
        @if(!$__env->yieldPushContent('head'))
            <title>{{ config('app.name', 'Iskina.ph') }}</title>
            <meta name="description" content="Buy and sell locally in Cebu. The #1 marketplace for gadgets, cars, property, jobs, services, and more near you.">
            <meta name="robots" content="noindex, nofollow">
        @endif

        {{-- Default Open Graph tags for pages without x-seo --}}
        <meta property="og:site_name" content="Iskina.ph">
        <meta property="og:locale" content="en_PH">

        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-K7V0RZWWZN"></script>
        <script>
         window.dataLayer = window.dataLayer || [];
         function gtag(){dataLayer.push(arguments);}
         gtag('js', new Date());

         gtag('config', 'G-K7V0RZWWZN');
        </script>

        {{-- Local Business JSON-LD (Organization) — Renders on all pages --}}
        @verbatim
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "@id": "https://www.iskina.ph/#organization",
            "name": "Iskina.ph",
            "url": "https://www.iskina.ph",
            "logo": {
                "@type": "ImageObject",
                "url": "https://www.iskina.ph/logo.png",
                "width": 600,
                "height": 60
            },
            "description": "Iskina.ph is the #1 marketplace in Cebu, Philippines. Buy and sell gadgets, cars, property, jobs, and services locally.",
            "sameAs": [
                "https://facebook.com/iskinaph",
                "https://twitter.com/iskinaph"
            ],
            "areaServed": {
                "@type": "City",
                "name": "Cebu",
                "sameAs": "https://en.wikipedia.org/wiki/Cebu"
            },
            "foundingLocation": {
                "@type": "Place",
                "name": "Cebu City, Philippines"
            }
        }
        </script>
        @endverbatim

        {{-- PWA: Manifest + Service Worker --}}
        <link rel="manifest" href="/manifest.json" />
        <meta name="theme-color" content="#2563eb" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="default" />
        <meta name="apple-mobile-web-app-title" content="Iskina" />
        <link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="/images/icons/icon-152x152.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="/images/icons/icon-144x144.png" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Register Service Worker --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Global broadcast modal --}}
        @auth
            @livewire('start-broadcast')
        @endauth
    </body>
</html>
