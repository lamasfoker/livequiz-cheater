const cacheName = "livequizcheater",
    filesToCache = [
        "/",
        "/index.html",
        "/app.js"
    ];

self.addEventListener("install", function (e) {
    e.waitUntil(
        caches.open(cacheName).then(function (cache) {
            return cache.addAll(filesToCache);
        })
    );
});

self.addEventListener('fetch', async event => {
    event.respondWith(
        caches.match(event.request).then(function (response) {
            return response || fetch(event.request);
        })
    );
});