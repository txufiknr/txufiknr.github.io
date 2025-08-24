function doScrollToggleClass(el, className, offset) {
  if (window.scrollY >= offset - 1) el.classList.add(className); else el.classList.remove(className);
}
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
function scrollDown() {
  window.scrollTo({ top: window.innerHeight, behavior: 'smooth' })
}
function initParticles() {
  // const particlesConfig = 'assets/vendor/particles/particlesjs-config.min.json'
  // particlesJS.load('particles-js', particlesConfig)
  // particlesJS.load('particles-js2', particlesConfig)
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
  // document.querySelectorAll('.typewrite').forEach((t) => {
  //   if (t.dataset.type) new TxtType(t, JSON.parse(t.dataset.type), t.dataset.period)
  // })

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
function onPageLoaded() {
  // Prevent unwanted horizontal scroll
  document.documentElement.scrollLeft = 0
  document.body.scrollLeft = 0

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
      counterUp(c, { duration: 2000, delay: 16 })
    })
  }
}

let counted = false
let ticking = false

document.getElementById('scroll-to-top').addEventListener('click', scrollToTop)
document.getElementById('scroll-down').addEventListener('click', scrollDown)
// document.addEventListener('DOMContentLoaded', onPageLoaded)
// window.addEventListener('load', onPageScroll)
// window.addEventListener('scroll', onPageScroll)

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