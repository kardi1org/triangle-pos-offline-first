const POS_CACHE = 'triangle-pos-offline-v4';
const POS_ASSETS = [
    '/app/pos',
    '/favicon.ico',
    '/images/favicon.png',
    '/js/pos-offline.js',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(POS_CACHE)
            .then((cache) => cache.addAll(POS_ASSETS))
            .catch(() => null)
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== POS_CACHE).map((key) => caches.delete(key))
        ))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (url.pathname === '/app/pos') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open(POS_CACHE).then((cache) => cache.put(request, copy));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match('/app/pos')))
        );
        return;
    }

    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/storage/') ||
        url.pathname.startsWith('/vendor/')
    ) {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request).then((response) => {
                const copy = response.clone();
                caches.open(POS_CACHE).then((cache) => cache.put(request, copy));
                return response;
            }))
        );
    }
});
