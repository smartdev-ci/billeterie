const CACHE_NAME = 'billetterie-pwa-v1';
const urlsToCache = [
  '/',
  '/scanner',
  '/img/icons/icon-192x192.png',
  '/img/icons/icon-512x512.png'
];

// Installation du Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache ouvert');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activation et nettoyage des anciens caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Suppression ancien cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Interception des requêtes réseau
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - retourne la réponse depuis le cache
        if (response) {
          return response;
        }
        
        // Clone la requête car les streams ne peuvent être consommés qu'une fois
        const fetchRequest = event.request.clone();
        
        return fetch(fetchRequest).then(response => {
          // Vérifie si c'est une réponse valide
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }
          
          // Clone la réponse
          const responseToCache = response.clone();
          
          caches.open(CACHE_NAME)
            .then(cache => {
              cache.put(event.request, responseToCache);
            });
          
          return response;
        });
      })
  );
});

// Gestion des notifications push (optionnel pour futur)
self.addEventListener('push', event => {
  const data = event.data ? event.data.json() : {};
  const title = data.title || 'Billetterie';
  const options = {
    body: data.body || 'Nouvelle notification',
    icon: '/img/icons/icon-192x192.png',
    badge: '/img/icons/icon-192x192.png'
  };
  
  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});
