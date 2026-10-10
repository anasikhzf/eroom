// Service Worker for Eroom PWA
const CACHE_NAME = 'eroom-cache-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/icons/icon-48.png',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Network first, falling back to cache strategy for fast response while keeping boards up-to-date
self.addEventListener('fetch', (event) => {
  // Only handle GET requests and skip non-http schemes
  if (event.request.method !== 'GET' || !event.request.url.startsWith('http')) {
    return;
  }

  // Skip dynamic mutating API routes from caching
  const url = new URL(event.request.url);
  if (url.pathname.includes('/unlock') || url.pathname.includes('/lock') || url.pathname.includes('/delete')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Cache static fonts or icon assets
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          (url.pathname.startsWith('/icons/') || url.origin.includes('fonts.gstatic.com'))
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => caches.match(event.request))
  );
});
