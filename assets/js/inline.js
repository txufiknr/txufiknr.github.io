function initJS(mainJS = 'assets/js/script.min.js') {
  // Lazy-load non-essential JS
  let scriptsLoaded = false;

  async function loadScript(src) {
    return new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = src;
      s.async = true;
      s.onload = resolve;
      s.onerror = () => reject(new Error('Failed to load ' + src));
      document.body.appendChild(s);
    });
  }

  async function lazyLoadScripts() {
    if (scriptsLoaded) return;
    scriptsLoaded = true;

    // Remove listeners to avoid re-calling
    window.removeEventListener('scroll', lazyLoadScripts);
    window.removeEventListener('mousemove', lazyLoadScripts);
    window.removeEventListener('keydown', lazyLoadScripts);

    try {
      // Load third-party libraries in parallel
      await Promise.allSettled([
        loadScript('assets/vendor/counterup2/counterup2.min.js'),
        loadScript('assets/vendor/typewrite/typewrite.min.js')
      ]);

      // Load the main script after dependencies are ready
      await loadScript(mainJS);
    } catch (err) {
      console.error('Failed to load scripts:', err);
    }
  }

  // Lazy-init: delay scripts until user starts interacting with the page
  window.addEventListener('scroll', lazyLoadScripts, { once: true });
  window.addEventListener('mousemove', lazyLoadScripts, { once: true });
  window.addEventListener('keydown', lazyLoadScripts, { once: true });

  // Only load animation libraries after the page is interactive
  if ('requestIdleCallback' in window) {
    requestIdleCallback(lazyLoadScripts);
    // requestIdleCallback(lazyLoadScripts, { timeout: 5000 });
  }
  setTimeout(lazyLoadScripts, 5000);

  // Register the Service Worker
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("sw.js")

    // Optional logging
    .then((reg) => {
      console.log("Service worker registered with scope:", reg.scope);

      // Wait until the SW is active and controlling
      return navigator.serviceWorker.ready;
    })
    .then((reg) => {
      if (!navigator.serviceWorker.controller) {
        console.warn("Service worker is not controlling this page!");
      } else {
        console.log("Service worker is active and controlling:", reg.scope);
      }
    })
    .catch((err) => {
      console.error("Service worker failed:", err);
    });
  }
}