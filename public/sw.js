const CACHE_NAME = 'ownbus-v1';
const OFFLINE_URL = '/offline';

// Files to cache for offline use
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/company/dashboard',
    '/company/fleet',
    '/company/rentals',
    '/company/customers',
    '/css/app.css',
    '/js/app.js',
    'https://fonts.googleapis.com/css2?family=DM+Sans',
    'https://cdn.tailwindcss.com',
];

// Install service worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('OwnBus: Caching static assets');
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate service worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch strategy: Network first, fallback to cache
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        event.respondWith(
            fetch(event.request).catch(() => {
                // If POST fails (offline), save to IndexedDB
                return new Response(
                    JSON.stringify({
                        offline: true,
                        message: 'Saved offline, will sync when online'
                    }),
                    { 
                        headers: { 
                            'Content-Type': 'application/json' 
                        } 
                    }
                );
            })
        );
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Cache successful responses
                if (response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Return cached version if offline
                return caches.match(event.request)
                    .then(cached => {
                        if (cached) return cached;
                        
                        // Return offline page for navigation
                        if (event.request.mode === 'navigate') {
                            return caches.match(OFFLINE_URL);
                        }
                        
                        return new Response('Offline', { 
                            status: 503 
                        });
                    });
            })
    );
});

// Background sync for offline data
self.addEventListener('sync', event => {
    if (event.tag === 'sync-offline-data') {
        event.waitUntil(syncOfflineData());
    }
});

async function syncOfflineData() {
    // Get offline queue from IndexedDB
    const db = await openDB();
    const tx = db.transaction('offlineQueue', 'readonly');
    const queue = await tx.objectStore('offlineQueue').getAll();
    
    for (const item of queue) {
        try {
            await fetch(item.url, {
                method: item.method,
                headers: item.headers,
                body: item.body,
            });
            
            // Remove from queue after successful sync
            const deleteTx = db.transaction(
                'offlineQueue', 'readwrite');
            await deleteTx
                .objectStore('offlineQueue')
                .delete(item.id);
                
        } catch (error) {
            console.log('Sync failed for:', item.url);
        }
    }
}
