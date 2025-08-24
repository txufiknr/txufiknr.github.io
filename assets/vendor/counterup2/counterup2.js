function counterUp(el, { duration = 1000 } = {}) {
  // Extract number + prefix/suffix
  const match = el.textContent.trim().match(/^([^0-9]*)([0-9.,]+)([^0-9]*)$/);
  if (!match) return;

  const prefix = match[1];
  const numberPart = match[2].replace(/,/g, '');
  const suffix = match[3];
  const target = parseFloat(numberPart);

  const startTime = performance.now();

  function update(now) {
    const progress = Math.min((now - startTime) / duration, 1);
    const value = Math.floor(progress * target);
    el.textContent = prefix + value.toLocaleString() + suffix;

    if (progress < 1) requestAnimationFrame(update);
  }

  requestAnimationFrame(update);
}

// Lazy init when visible
document.querySelectorAll('.counter').forEach(el => {
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      counterUp(el, { duration: 1200 });
      observer.disconnect();
    }
  });
  observer.observe(el);
});