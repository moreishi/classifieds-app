const CACHE_NAME = 'iskina-v1';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/logo.png',
    '/favicon.ico',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
];

// Install: cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate: clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch: network first, cache fallback for HTML; cache first for assets
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin requests
    if (url.origin !== self.location.origin) return;

    // Skip non-GET and non-browser requests
    if (request.method !== 'GET' && request.method !== 'HEAD') return;

    // API and dynamic routes: network only
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/livewire/')) return;

    // Static assets: cache-first
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?)$/i)
    ) {
        event.respondWith(
            caches.match(request).then((cached) => {
                return cached || fetch(request).then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    return response;
                });
            })
        );
        return;
    }

    // HTML pages: network-first, cache fallback
    event.respondWith(
        fetch(request).then((response) => {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
            return response;
        }).catch(() => {
            return caches.match(request).then((cached) => {
                return cached || caches.match('/');
            });
        })
    );
});
