// service-worker.js
const CACHE_NAME = 'weather-app-cache-v1';
const CACHE_FILES = [
  '/',
  '/index.html',
  '/script.js',
  '/style.css',
  '/img/RijanNeupane_2330765clouds.jpg',
  '/img/RijanNeupane_2330765rainy.jpg',
  '/img/RijanNeupane_2330765clear.jpg',
  '/img/RijanNeupane_2330765snow.jpg',
  '/img/RijanNeupane_2330765sunny.jpg',
  '/img/RijanNeupane_2330765thunderstrom.jpg',
  '/img/RijanNeupane_2330765drizzle.jpg',
  '/img/RijanNeupane_2330765mist.jpg',
  '/img/RijanNeupane_2330765bg.jpg',
];

self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        return cache.addAll(CACHE_FILES);
      })
  );
});

self.addEventListener('activate', function(event) {
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.filter(function(cacheName) {
          return cacheName !== CACHE_NAME;
        }).map(function(cacheName) {
          return caches.delete(cacheName);
        })
      );
    })
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        if (response) {
          return response;
        }
        return fetch(event.request);
      })
  );
});

self.addEventListener('message', function(event) {
  if (event.data && event.data.command === 'CACHE_SIZE') {
    calculateCacheSize();
  }
});

function calculateCacheSize() {
  caches.open(CACHE_NAME)
    .then(function(cache) {
      return cache.keys();
    })
    .then(function(keys) {
      let cacheSize = 0;
      const urlPromises = keys.map(function(request) {
        return caches.match(request).then(function(response) {
          if (response) {
            const contentLength = response.headers.get('content-length');
            cacheSize += Number(contentLength);
          }
        });
      });

      Promise.all(urlPromises).then(function() {
        let cacheSizeInMB = (cacheSize / (1024 * 1024)).toFixed(2);
        self.clients.matchAll().then(function(clients) {
          clients.forEach(function(client) {
            client.postMessage({
              command: 'CACHE_SIZE_RESULT',
              size: cacheSizeInMB,
            });
          });
        });
      });
    });
}