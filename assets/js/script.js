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
function startUI() {
  if (document.getElementById('cookie-alert')) {
    document.getElementById('lang').addEventListener('click', showCookieAlert)
    document.getElementById('lang').addEventListener('mouseenter', showCookieAlert)
    document.getElementById('lang').addEventListener('mouseleave', hideCookieAlert)
    document.getElementById('lang').addEventListener('blur', hideCookieAlert)
  }
  document.querySelectorAll('#lang .menu a[data-value]').forEach((a) => {
    a.addEventListener('click', (e) => {
      e.preventDefault()
      let languageCode = e.currentTarget.dataset.value
      console.log('set language', languageCode)
      setCookie('language', languageCode)
      document.querySelector('#lang .btn').innerText = languageCode.toUpperCase()
      let redirect = `${baseURL}${languageCode}${pageName == 'home' ? '' : '/' + pageName}`
      window.location.href = redirect
    })
  })
  initParticles()
  initTypewriters()
}

function onPageLoaded() {
  onPageScroll()
  startUI()
}

let portfolio1 = document.querySelector('#portfolio .item.birvee')
let portfolio2 = document.querySelector('#portfolio .item.foom')
let portfolio3 = document.querySelector('#portfolio .item.leapverse')
let portfolio4 = document.querySelector('#portfolio .item.buangduit')

function onPageScroll() {
  doScrollToggleClass(portfolio1, 'active', portfolio1.getBoundingClientRect().top + window.scrollY)
  doScrollToggleClass(portfolio2, 'active', portfolio2.getBoundingClientRect().top + window.scrollY)
  doScrollToggleClass(portfolio3, 'active', portfolio3.getBoundingClientRect().top + window.scrollY)
  doScrollToggleClass(portfolio4, 'active', portfolio4.getBoundingClientRect().top + window.scrollY)
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

const baseURL = document.querySelector('base').href // $baseURL
const language = document.documentElement.lang // $lang
const pageName = document.body.dataset.page // $page
const pageHref = document.body.dataset.href // $href

let counted = false

document.documentElement.scrollLeft = 0
document.body.scrollLeft = 0

document.addEventListener('DOMContentLoaded', onPageLoaded)
document.addEventListener('scroll', onPageScroll)
document.getElementById('scroll-to-top').addEventListener('click', scrollToTop)
document.getElementById('scroll-down').addEventListener('click', scrollDown)

// active menus
document.querySelector('#lang .menu a[data-value="' + language + '"]')?.classList.add('active')
// document.querySelectorAll('#navbar a[href="' + pageHref + '"]').forEach((a) => a.classList.add('active'))

// enable css
document.querySelectorAll('link[rel="stylesheet"][disabled]').forEach((s) => { s.disabled = false })