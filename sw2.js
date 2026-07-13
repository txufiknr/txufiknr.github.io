/**
 * Service Worker for index2.html (Portfolio v2)
 * - Precaches core assets on install
 * - Stale-while-revalidate strategy for static assets
 * - Network-first for navigation
 * - Graceful offline fallback
 * - Cache versioned for easy updates
 */

const CACHE = "portfolio-v2-static";
const CACHE_FONT = "portfolio-v2-fonts";
const FALLBACK_IMAGE = "favicon.png";

const FONT_ORIGINS = [
  "https://fonts.googleapis.com",
  "https://fonts.gstatic.com",
];

const PRECACHE_URLS = [
  "/",
  "index2.html",
  "assets/css/index2.min.css",
  "assets/css/index2-defer.min.css",
  "favicon.png",
  "apple-touch-icon.png",
  "assets/images/me/taufik-nur-rahmanda.webp",
];

// Install: precache static shell
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE).then((cache) =>
      Promise.all(
        PRECACHE_URLS.map((url) =>
          fetch(url, { cache: "reload" })
            .then((res) => {
              if (res.ok) cache.put(url, res.clone());
            })
            .catch(() => {})
        )
      )
    )
  );
  self.skipWaiting();
});

// Activate: remove old caches
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((k) => ![CACHE, CACHE_FONT].includes(k))
          .map((k) => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

// Fetch: strategy per resource type
self.addEventListener("fetch", (event) => {
  const { request } = event;
  const url = new URL(request.url);

  if (request.method !== "GET" || url.protocol === "chrome-extension:") return;

  // Navigate: network first, fallback to cache
  if (request.mode === "navigate") {
    event.respondWith(
      fetch(request).catch(() =>
        caches.match("/").then((r) => r || caches.match("index2.html") || new Response("Offline", { status: 503 }))
      )
    );
    return;
  }

  // Static assets & fonts: stale-while-revalidate
  const isStatic = /\.(css|js|webp|png|svg|woff2?)$/i.test(url.pathname);
  const isImage = /\.(webp|png|svg)$/i.test(url.pathname);
  const isFont = FONT_ORIGINS.includes(url.origin);

  if (isStatic || isFont) {
    event.respondWith(
      (async () => {
        const cache = await caches.open(isFont ? CACHE_FONT : CACHE);
        const cached = await cache.match(request);

        const networkFetch = fetch(request)
          .then((res) => {
            if (res.ok) cache.put(request, res.clone());
            return res;
          })
          .catch(() => null);

        if (cached) return cached;
        if (isImage) return (await networkFetch) || caches.match(FALLBACK_IMAGE);
        return networkFetch;
      })()
    );
  }
});
