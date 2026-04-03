var CACHE_NAME = 'touristik-v2';
var OFFLINE_PAGE = '/tourism/offline.html';
var STATIC_ASSETS = [
    '/tourism/',
    '/tourism/css/styles.css',
    '/tourism/js/script.js',
    '/tourism/manifest.json',
    '/tourism/img/icon-192.svg',
    OFFLINE_PAGE
];

// Install — cache static assets
self.addEventListener('install', function (e) {
    e.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate — clean old caches
self.addEventListener('activate', function (e) {
    e.waitUntil(
        caches.keys().then(function (names) {
            return Promise.all(
                names.filter(function (n) { return n !== CACHE_NAME; })
                     .map(function (n) { return caches.delete(n); })
            );
        })
    );
    self.clients.claim();
});

// Fetch — network first, fallback to cache
self.addEventListener('fetch', function (e) {
    // Skip non-GET requests
    if (e.request.method !== 'GET') return;

    e.respondWith(
        fetch(e.request).then(function (response) {
            // Cache successful responses
            if (response.ok) {
                var clone = response.clone();
                caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(e.request, clone);
                });
            }
            return response;
        }).catch(function () {
            return caches.match(e.request).then(function (cached) {
                if (cached) return cached;
                // If it's a navigation request, show offline page
                if (e.request.mode === 'navigate') {
                    return caches.match(OFFLINE_PAGE);
                }
            });
        })
    );
});
