# Taufik Nur Rahmanda — Portfolio

A modern, animated personal portfolio website replicating the structure and design language of [Codex](https://chatgpt.com/codex/) with a distinctive **purple + black** theme.

## Overview

| Section (Codex) | My Adaptation |
|---|---|
| Hero | Name, title, subtitle, social links |
| Trusted by top teams | Tech stack marquee (React, Next.js, Flutter...) |
| Ways to use Codex | Counter stats (projects, languages, years, AI providers) |
| Get work done faster | Portfolio project cards with filtering (staggered masonry) |
| Choose a plan | Tech Stack (Frontend, Backend, Mobile, AI/LLM, Cloud/Architecture, DevOps/Tools) |
| Same agent everywhere | Testimonials from colleagues |
| Try today / Footer | CTA + contact + copyright |

## Design

- **Shape**: Rounded elements (`border-radius: 8px–36px`)
- **Color**: Purple primary (`#7c3aed`), black background (`#0a0015`)
- **Font**: Inter (Google Fonts, preloaded)
- **Layout**: CSS Grid + Flexbox, fully responsive

## Features

### Modern CSS (zero JS for layout/animation)
- `animation-timeline: view()` for scroll-driven entrance animations (Chrome 115+)
- `animation-timeline: scroll()` for scroll-progress bar
- `@supports` progressive enhancement
- `clamp()` for fluid typography
- CSS custom properties for theming
- `backdrop-filter: blur()` for glass nav
- `prefers-reduced-motion` support

### Animations & Interactivity
- **Counters**: Animate on scroll into viewport (IntersectionObserver, `data-counter` on elements)
- **Scroll entrance**: Fade-in-up via IntersectionObserver + CSS `animation-timeline: view()`
- **Staggered entrance**: `data-stagger` attribute cascades children with `--i` index delays
- **Parallax**: Mouse-follow parallax on hero (JS + CSS custom properties), scroll-based parallax on sections via `animation-timeline`
- **Blur reveal**: `data-anim="blur"` for smooth blur-to-clear entrance
- **Zoom/rotate**: `data-anim="zoom"` for scale+rotate entrance
- **Particle float**: CSS-only animated background particles on hero via `@keyframes`
- **Tech marquee**: Infinite horizontal scroll, pauses on hover
- **Nav**: Mobile hamburger toggle, active section highlighting
- **Portfolio filter**: Tab-based filtering by category

### Performance
- Preconnect to Google Fonts
- Preload critical font + hero image
- Prefetch linked profiles
- Deferred non-critical CSS (`index2-defer.min.css`)
- Deferred inline JS
- Service worker (stale-while-revalidate, offline fallback)
- Responsive images via `loading="lazy"`
- `fetchpriority="high"` on LCP elements

### SEO
- Semantic HTML (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`)
- JSON-LD structured data (Person, WebSite)
- Open Graph + Twitter Card meta
- Canonical URL
- Meta description + keywords
- `aria-label`, `role`, skip-to-content link

### PWA
- Service worker (`sw2.js`)
- `apple-mobile-web-app-capable`
- `theme-color`
- Favicon + apple-touch-icon

## Files

| File | Purpose |
|---|---|
| `index2.html` | Main HTML document (SEO, preloads, inline CSS, defer JS) |
| `assets/css/index2.min.css` | Full stylesheet (complete CSS) |
| `assets/css/index2-defer.min.css` | Non-critical styles (loaded async) |
| `sw2.js` | Service worker for caching + offline |

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- View-timeline features are progressive — fall back to IntersectionObserver JS
- `prefers-reduced-motion` respected

## License

MIT © 2026 Taufik Nur Rahmanda
