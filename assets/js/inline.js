function initJS(mainJS = 'assets/js/script.min.js') {
  const fallbackTimeout = setTimeout(lazyLoadScripts, 5000);
  let scriptsLoaded = false;

  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = src;
      s.async = true; // Executes as soon as it finishes
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

    // Cancel timeout fallback
    clearTimeout(fallbackTimeout);

    // Load third-party libraries in parallel
    await Promise.allSettled([
      loadScript('assets/vendor/counterup2/counterup2.min.js'),
      loadScript('assets/vendor/typewrite/typewrite.min.js')
    ]);

    // Load the main script after dependencies are ready
    await loadScript(mainJS);
  }

  // Trigger on first interaction
  window.addEventListener('scroll', lazyLoadScripts, { once: true, passive: true });
  window.addEventListener('mousemove', lazyLoadScripts, { once: true });
  window.addEventListener('keydown', lazyLoadScripts, { once: true });

  // Fallback triggers
  if ('requestIdleCallback' in window) {
    requestIdleCallback(lazyLoadScripts);
    // requestIdleCallback(lazyLoadScripts, { timeout: 5000 });
  }

  // Service Worker registration
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js')
    .then(reg => {
      console.log('Service worker registered:', reg.scope);

      // Wait until the SW is active and controlling
      return navigator.serviceWorker.ready;
    })
    .then(reg => {
      if (!navigator.serviceWorker.controller) {
        console.warn('Service worker not controlling this page yet!');
      } else {
        console.log('Service worker active:', reg.scope);
      }
    })
    .catch(err => console.error('Service worker failed:', err));
  }
}