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
function initParticles() {
  particlesJS.load('particles-js', 'assets/vendor/particles/particlesjs-config.json')
  particlesJS.load('particles-js2', 'assets/vendor/particles/particlesjs-config.json')
}
function initTypewriters() {
  document.querySelectorAll('.typewrite').forEach((t) => {
    if (t.dataset.type) new TxtType(t, JSON.parse(t.dataset.type), t.dataset.period)
  })
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
  if (window.scrollY > 0) {
    if (!counted) {
      counted = true
      document.querySelectorAll('.counter').forEach((c) => {
        window.counterUp.default(c, {
          duration: 2000,
          delay: 16,
        })
      })
    }
  } else {
    counted = false
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

// Safely handle if DOM/window has already loaded
// if (document.readyState === 'complete') {
//   initScripts();
// } else {
//   document.addEventListener('DOMContentLoaded', onPageLoaded);
//   window.addEventListener('load', onPageScroll);
// }

// Use requestAnimationFrame for smoother scroll handling
window.addEventListener('scroll', () => {
  if (ticking) return
  requestAnimationFrame(() => {
    onPageScroll()
    ticking = false
  })
  ticking = true
})