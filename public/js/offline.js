// IndexedDB setup
const DB_NAME = 'ownbus-offline';
const DB_VERSION = 1;

async function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        
        request.onupgradeneeded = event => {
            const db = event.target.result;
            
            // Queue for offline requests
            if (!db.objectStoreNames.contains('offlineQueue')) {
                const store = db.createObjectStore(
                    'offlineQueue', 
                    { keyPath: 'id', autoIncrement: true }
                );
                store.createIndex('url', 'url', { unique: false });
                store.createIndex('timestamp', 'timestamp');
            }
            
            // Offline data stores
            if (!db.objectStoreNames.contains('rentals')) {
                db.createObjectStore('rentals', 
                    { keyPath: 'id', autoIncrement: true });
            }
            
            if (!db.objectStoreNames.contains('vehicles')) {
                db.createObjectStore('vehicles', 
                    { keyPath: 'id' });
            }
            
            if (!db.objectStoreNames.contains('customers')) {
                db.createObjectStore('customers', 
                    { keyPath: 'id' });
            }
            
            if (!db.objectStoreNames.contains('drivers')) {
                db.createObjectStore('drivers', 
                    { keyPath: 'id' });
            }
        };
    });
}

// Save request to offline queue
async function saveToOfflineQueue(url, method, data) {
    const db = await openDB();
    const tx = db.transaction('offlineQueue', 'readwrite');
    
    await tx.objectStore('offlineQueue').add({
        url: url,
        method: method,
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name=csrf-token]')
                ?.getAttribute('content')
        },
        body: JSON.stringify(data),
        timestamp: new Date().toISOString(),
        status: 'pending'
    });
    
    console.log('Saved to offline queue:', url);
}

// Save data locally for offline viewing
async function saveLocalData(storeName, data) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    
    if (Array.isArray(data)) {
        for (const item of data) {
            await store.put(item);
        }
    } else {
        await store.put(data);
    }
}

// Get local data when offline
async function getLocalData(storeName) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readonly');
    return tx.objectStore(storeName).getAll();
}

// OwnBus Offline Manager
window.OwnBusOffline = {
    isOnline: navigator.onLine,
    
    init() {
        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.onOnline();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.onOffline();
        });
        
        // Register service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker
                .register('/sw.js')
                .then(reg => {
                    console.log('SW registered:', reg.scope);
                })
                .catch(err => {
                    console.log('SW failed:', err);
                });
        }
        
        // Show initial status
        this.updateStatusIndicator();
    },
    
    onOnline() {
        this.updateStatusIndicator();
        this.showNotification(
            '✅ Back Online!', 
            'Syncing your offline data...',
            'success'
        );
        this.syncOfflineQueue();
    },
    
    onOffline() {
        this.updateStatusIndicator();
        this.showNotification(
            '📡 You are Offline', 
            'Data will be saved and synced when online',
            'warning'
        );
    },
    
    updateStatusIndicator() {
        const indicator = document
            .getElementById('connection-status');
        if (!indicator) return;
        
        if (this.isOnline) {
            indicator.innerHTML = 
                '<span class="online-dot"></span> Online';
            indicator.className = 'status-online';
        } else {
            indicator.innerHTML = 
                '<span class="offline-dot"></span> Offline';
            indicator.className = 'status-offline';
        }
    },
    
    showNotification(title, message, type) {
        const notification = document
            .createElement('div');
        notification.className = 
            `sync-notification ${type}`;
        notification.innerHTML = `
            <strong>${title}</strong>
            <p>${message}</p>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 4000);
    },
    
    async syncOfflineQueue() {
        const db = await openDB();
        const tx = db.transaction('offlineQueue', 'readonly');
        const queue = await tx.objectStore('offlineQueue')
            .getAll();
        
        if (queue.length === 0) return;
        
        this.showNotification(
            '🔄 Syncing Data',
            `Syncing ${queue.length} offline records...`,
            'info'
        );
        
        let synced = 0;
        let failed = 0;
        
        for (const item of queue) {
            try {
                const response = await fetch(item.url, {
                    method: item.method,
                    headers: item.headers,
                    body: item.body,
                });
                
                if (response.ok) {
                    // Remove from queue
                    const deleteTx = db.transaction(
                        'offlineQueue', 'readwrite');
                    await deleteTx
                        .objectStore('offlineQueue')
                        .delete(item.id);
                    synced++;
                } else {
                    failed++;
                }
            } catch (error) {
                failed++;
            }
        }
        
        this.showNotification(
            '✅ Sync Complete',
            `${synced} records synced, ${failed} failed`,
            'success'
        );
        
        // Reload page to show synced data
        if (synced > 0) {
            setTimeout(() => location.reload(), 2000);
        }
    },
    
    // Intercept form submissions when offline
    handleOfflineForm(form, url, data) {
        if (!this.isOnline) {
            saveToOfflineQueue(url, 'POST', data);
            this.showNotification(
                '💾 Saved Offline',
                'Will sync automatically when online',
                'warning'
            );
            return true; // Handled offline
        }
        return false; // Let it submit normally
    },
    
    // Cache important data for offline use
    async cacheData() {
        try {
            // Cache vehicles list
            const vehiclesRes = await fetch('/api/vehicles/list');
            if (vehiclesRes.ok) {
                const vehicles = await vehiclesRes.json();
                await saveLocalData('vehicles', vehicles);
            }
            
            // Cache customers list
            const customersRes = await fetch('/api/customers/list');
            if (customersRes.ok) {
                const customers = await customersRes.json();
                await saveLocalData('customers', customers);
            }
            
            // Cache drivers list
            const driversRes = await fetch('/api/drivers/list');
            if (driversRes.ok) {
                const drivers = await driversRes.json();
                await saveLocalData('drivers', drivers);
            }
        } catch (error) {
            console.log('Cache failed:', error);
        }
    }
};

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.OwnBusOffline.init();
    
    // Cache data every 30 minutes
    window.OwnBusOffline.cacheData();
    setInterval(() => {
        if (window.OwnBusOffline.isOnline) {
            window.OwnBusOffline.cacheData();
        }
    }, 30 * 60 * 1000);
});
