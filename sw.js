const CACHE_STATIC = "txufiknr-cache-v1";
const CACHE_FONT = "google-fonts-v1";

const FONT_ORIGINS = ["https://fonts.googleapis.com", "https://fonts.gstatic.com"];

const PRECACHE_URLS = [
  // html
  "index.html",

  // styles
  "assets/css/main.min.css",
  "assets/css/defer.min.css",

  // scripts
  "assets/js/script.min.js",
  "assets/vendor/counterup2/counterup2.min.js",
  "assets/vendor/typewrite/typewrite.min.js",

  // icons
  "favicon.png",
  "apple-touch-icon.png",

  // images
  "assets/images/hero.webp",
  "assets/images/dummy/male.webp",
  "assets/images/dummy/female.webp",

  // me
  "assets/images/me/taufik-nur-rahmanda.webp",
  "assets/images/me/taufik-nur-rahmanda-64.webp",
  "assets/images/me/taufik-nur-rahmanda-250.webp",
  "assets/images/me/taufik-nur-rahmanda-550.webp",

  // portfolio
  "assets/images/portfolio/qibla.webp",
  "assets/images/portfolio/thumbs/qibla-150.webp",
  "assets/images/portfolio/thumbs/qibla-400.webp",
  "assets/images/portfolio/quran.webp",
  "assets/images/portfolio/thumbs/quran-150.webp",
  "assets/images/portfolio/thumbs/quran-400.webp",
  "assets/images/portfolio/tasbih.webp",
  "assets/images/portfolio/thumbs/tasbih-150.webp",
  "assets/images/portfolio/thumbs/tasbih-400.webp",
  "assets/images/portfolio/zakat.webp",
  "assets/images/portfolio/thumbs/zakat-150.webp",
  "assets/images/portfolio/thumbs/zakat-400.webp",

  // social icons
  "assets/icons/linkedin.svg",
  "assets/icons/instagram.svg",
  "assets/icons/twitter-x.svg",
  "assets/icons/github.svg",
  "assets/icons/google-play.svg",
];

// Install event → cache core files
// self.addEventListener("install", (event) => {
//   event.waitUntil(
//     caches.open(CACHE_STATIC).then((cache) => cache.addAll(PRECACHE_URLS))
//   );
//   self.skipWaiting();
// });

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_STATIC).then((cache) => {
      return Promise.all(
        PRECACHE_URLS.map((url) =>
          fetch(url).then((response) => {
            if (response.ok) {
              console.info("[SW] Added to cache:", url);
              return cache.put(url, response);
            }
          }).catch(() => {
            console.warn("[SW] Failed to cache:", url);
          })
        )
      );
    })
  );
  self.skipWaiting();
});

// Activate event → clean up old caches
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((cacheName) => ![CACHE_STATIC, CACHE_FONT].includes(cacheName)).map((cacheName) => caches.delete(cacheName))
      );
    })
  );
  self.clients.claim();
});

// Fetch event
self.addEventListener("fetch", (event) => {
  const url = new URL(event.request.url);
  const isFont = FONT_ORIGINS.includes(url.origin);

  console.log("[SW] Fetching", url);

  // pages: fetch from network → fallback to index.html
  if (event.request.mode === "navigate") {
    event.respondWith(
      fetch(event.request).catch(() => caches.match("/index.html"))
    );
    return;
  }

  // static assets & google fonts: get from cache → fallback to network (stale-while-revalidate)
  if (url.pathname.match(/\.(css|js|webp|png|svg|woff2?)$/) || isFont) {
    event.respondWith(
      caches.open(isFont ? CACHE_FONT : CACHE_STATIC).then(async (cache) => {
        const cached = await cache.match(event.request);
        const networkFetch = fetch(event.request).then((response) => {
          cache.put(event.request, response.clone());
          return response;
        }).catch(() => null);
        return cached || networkFetch;
      })
    );
    return;
  }
  
  // others: maybe cached but not checked on above conditions
  event.respondWith(
    caches.match(event.request).then((cached) => {
      return cached || fetch(event.request);
    })
  );
});