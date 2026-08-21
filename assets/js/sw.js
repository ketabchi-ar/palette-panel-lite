
/**
 * Serene PWA Service Worker
 */
const CACHE_NAME = 'serene-pwa-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/panel/'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
