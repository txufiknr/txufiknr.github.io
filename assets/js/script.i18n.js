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
  particlesJS.load('particles-js', 'assets/vendor/particles/particlesjs-config.json')
  particlesJS.load('particles-js2', 'assets/vendor/particles/particlesjs-config.json')
}
function initTypewriters() {
  document.querySelectorAll('.typewrite').forEach((t) => {
    if (t.dataset.type) new TxtType(t, JSON.parse(t.dataset.type), t.dataset.period)
  })
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

  // Internationalization
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

const language = document.documentElement.lang // $lang
const pageName = document.body.dataset.page // $page
const pageHref = document.body.dataset.href // $href

let counted = false
let ticking = false

document.addEventListener('DOMContentLoaded', onPageLoaded)
document.getElementById('scroll-to-top').addEventListener('click', scrollToTop)
document.getElementById('scroll-down').addEventListener('click', scrollDown)

window.addEventListener('load', onPageScroll)
// window.addEventListener('scroll', onPageScroll)

// Use requestAnimationFrame for smoother scroll handling
window.addEventListener('scroll', () => {
  if (ticking) return
  requestAnimationFrame(() => {
    onPageScroll()
    ticking = false
  })
  ticking = true
})

// active menus
document.querySelector('#lang .menu a[data-value="' + language + '"]')?.classList.add('active')
// document.querySelectorAll('#navbar a[href="' + pageHref + '"]').forEach((a) => a.classList.add('active'))

// enable css
document.querySelectorAll('link[rel="stylesheet"][disabled]').forEach((s) => { s.disabled = false })