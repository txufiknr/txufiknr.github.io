function doScrollToggleClass(el, className, offset) {
  if (window.scrollY >= offset - 1) {
    el.classList.add(className)
  } else {
    el.classList.remove(className)
  }
}
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
function scrollDown() {
  window.scrollTo({ top: window.innerHeight, behavior: 'smooth' })
}
function setCookie(cname, cvalue, cdays = 365) {
  const expDate = new Date
  expDate.setDate(expDate.getDate() + cdays)
  document.cookie = `${cname}=${cvalue}; expires=${expDate.toUTCString()}; path=/`
}
function simpleParticles(containerId, options = {}) {
  const container = document.getElementById(containerId);
  const canvas = document.createElement("canvas");
  canvas.style.display = "block";
  canvas.style.width = "100%";
  canvas.style.height = "100%";
  container.appendChild(canvas);
  const ctx = canvas.getContext("2d");

  let width, height, dpr;

  const settings = Object.assign({
    count: 20,
    speed: .6,
    size: 2,
    lineColor: "#ffffff",
    lineDistance: 150,
    lineOpacity: 0.1,
    lineWidth: .75,
    // baseColor: "#48485b",
    // opacity: 0.5,
    strokeColor: "#7d7d98",
    strokeWidth: 3,
    randomSize: true,
    randomSpeed: true,
    retinaDetect: true
  }, options);

  function resize() {
    dpr = (settings.retinaDetect ? (window.devicePixelRatio || 1) : 1) * 1.5;

    // CSS size
    const cssWidth = container.clientWidth;
    const cssHeight = container.clientHeight;
  
    // Set internal buffer higher resolution
    canvas.width = cssWidth * dpr;
    canvas.height = cssHeight * dpr;
  
    // Force CSS size back to normal
    canvas.style.width = cssWidth + "px";
    canvas.style.height = cssHeight + "px";
  
    // Reset transform then scale drawing units
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    width = cssWidth;
    height = cssHeight;
  }
  
  window.addEventListener("resize", resize);
  resize();

  // Convert hex to rgba with alpha
  function rgba(hex, alpha) {
    const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    if (!m) return hex;
    return `rgba(${parseInt(m[1],16)},${parseInt(m[2],16)},${parseInt(m[3],16)},${alpha})`;
  }

  const particles = Array.from({ length: settings.count }, () => {
    const r = settings.randomSize
      ? settings.size * (0.5 + Math.random())
      : settings.size;
    const speed = settings.randomSpeed
      ? (Math.random() * settings.speed)
      : settings.speed;
    const angle = Math.random() * 2 * Math.PI;
    return {
      x: Math.random() * width,
      y: Math.random() * height,
      vx: Math.cos(angle) * speed,
      vy: Math.sin(angle) * speed,
      r
    };
  });

  const mouse = { x: null, y: null };
  canvas.addEventListener("mousemove", e => {
    const rect = canvas.getBoundingClientRect();
    mouse.x = e.clientX - rect.left;
    mouse.y = e.clientY - rect.top;
  });
  canvas.addEventListener("mouseleave", () => { mouse.x = null; mouse.y = null; });
  canvas.addEventListener("click", e => {
    const rect = canvas.getBoundingClientRect();
    particles.push({
      x: e.clientX - rect.left,
      y: e.clientY - rect.top,
      vx: (Math.random() - 0.5) * settings.speed,
      vy: (Math.random() - 0.5) * settings.speed,
      r: settings.size
    });
  });

  function draw() {
    ctx.clearRect(0, 0, width, height);

    for (let p of particles) {
      // move
      p.x += p.vx;
      p.y += p.vy;
      if (p.x < 0 || p.x > width) p.vx *= -1;
      if (p.y < 0 || p.y > height) p.vy *= -1;

      // circle with fill+stroke
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      // ctx.fillStyle = rgba(settings.baseColor, settings.opacity);
      ctx.fillStyle = rgba(0,0,0,0);
      ctx.fill();
      ctx.lineWidth = settings.strokeWidth;
      ctx.strokeStyle = settings.strokeColor;
      ctx.stroke();

      // links
      for (let q of particles) {
        let dx = p.x - q.x, dy = p.y - q.y;
        let dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < settings.lineDistance) {
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(q.x, q.y);
          ctx.strokeStyle = rgba(settings.lineColor, settings.lineOpacity);
          ctx.lineWidth = 1;
          ctx.stroke();
        }
      }

      // hover grab
      if (mouse.x !== null) {
        let dx = p.x - mouse.x, dy = p.y - mouse.y;
        let dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < settings.lineDistance) {
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(mouse.x, mouse.y);
          ctx.strokeStyle = rgba(settings.lineColor, 1);
          ctx.stroke();
        }
      }
    }

    requestAnimationFrame(draw);
  }

  draw();
}
function initParticles() {
  const target = document.getElementById("particles-js2");
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      simpleParticles('particles-js2');
      observer.disconnect();
    }
  });
  observer.observe(target);
  simpleParticles('particles-js');
}
function initTypewriters() {
  const a = document.querySelector("#hero .typewrite");
  const b = document.querySelector("#portfolio .typewrite");
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      new TxtType(b, JSON.parse(b.dataset.type), b.dataset.period);
      observer.disconnect();
    }
  });
  observer.observe(b);
  new TxtType(a, JSON.parse(a.dataset.type), b.dataset.period)
}
function showCookieAlert() {
  document.getElementById('cookie-alert').classList.add('show')
}
function hideCookieAlert() {
  document.getElementById('cookie-alert').classList.remove('show')
}
function onPageLoaded() {
  // Prevent unwanted horizontal scroll
  document.documentElement.scrollLeft = 0
  document.body.scrollLeft = 0

  // Language menu
  if (document.getElementById('cookie-alert')) {
    document.getElementById('lang').addEventListener('click', showCookieAlert)
    document.getElementById('lang').addEventListener('mouseenter', showCookieAlert)
    document.getElementById('lang').addEventListener('mouseleave', hideCookieAlert)
    document.getElementById('lang').addEventListener('blur', hideCookieAlert)
  }
  document.querySelectorAll('#lang .menu a[data-value]').forEach((a) => {
    a.addEventListener('click', (e) => {
      e.preventDefault()
      const languageCode = e.currentTarget.dataset.value
      console.log('set language', languageCode)
      setCookie('language', languageCode)
      document.querySelector('#lang .btn').innerText = languageCode.toUpperCase()
      window.location.href = `${location.origin}${location.pathname}${languageCode}${pageName == 'home' ? '' : '/' + pageName}`
    })
  })

  // Load animations
  initParticles()
  initTypewriters()
}

const portfolio1 = document.getElementById('portfolio-1')
const portfolio2 = document.getElementById('portfolio-2')
const portfolio3 = document.getElementById('portfolio-3')
const portfolio4 = document.getElementById('portfolio-4')

function onPageScroll() {
  doScrollToggleClass(portfolio1, 'active', portfolio1.getBoundingClientRect().top + window.scrollY - window.innerHeight / 4)
  doScrollToggleClass(portfolio2, 'active', portfolio2.getBoundingClientRect().top + window.scrollY - window.innerHeight / 4)
  doScrollToggleClass(portfolio3, 'active', portfolio3.getBoundingClientRect().top + window.scrollY - window.innerHeight / 4)
  doScrollToggleClass(portfolio4, 'active', portfolio4.getBoundingClientRect().top + window.scrollY - window.innerHeight / 4)
  doScrollToggleClass(document.body, 'scrolled', document.body.offsetHeight - window.innerHeight)

  // Initialize counters
  if (window.scrollY == 0) {
    counted = false
  } else if (!counted) {
    counted = true
    document.querySelectorAll('.counter').forEach((c) => {
      counterUp.default(c, { duration: 2000, delay: 16 })
    })
  }
}

const language = document.documentElement.lang // $lang
const pageName = document.body.dataset.page // $page
const pageHref = document.body.dataset.href // $href

let counted = false
let ticking = false

document.getElementById('scroll-to-top').addEventListener('click', scrollToTop)
document.getElementById('scroll-down').addEventListener('click', scrollDown)

function initScripts() {
  onPageLoaded();
  onPageScroll();
}

// All dependencies are guaranteed to be loaded
initScripts();

// Use requestAnimationFrame for smoother scroll handling
window.addEventListener('scroll', () => {
  if (ticking) return
  requestAnimationFrame(() => {
    onPageScroll()
    ticking = false
  })
  ticking = true
})

// Language active menu
document.querySelector('#lang .menu a[data-value="' + language + '"]')?.classList.add('active')