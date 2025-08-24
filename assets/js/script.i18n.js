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
function initParticles() {
  const particlesConfig = {
    particles: {
      number: { value: 40 },
      color: { value: "#48485b" },
      shape: { type: "circle", "stroke": { "width": 5, "color": "#7d7d98" } },
      opacity: { value: 0.5 },
      size: { value: 3, random: true },
      line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
      move: { enable: true, speed: 2, random: true, out_mode: "out" }
    },
    interactivity: {
      events: {
        onhover: { enable: true, mode: "grab" },
        onclick: { enable: true, mode: "push" },
        resize: true
      },
      modes: {
        grab: { distance: 100, line_linked: { opacity: 1 } },
        push: { particles_nb: 1 }
      }
    },
    retina_detect: true
  }

  const target = document.getElementById("particles-js2");
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      particlesJS('particles-js2', particlesConfig);
      observer.disconnect();
    }
  });
  observer.observe(target);
  particlesJS('particles-js', particlesConfig);
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