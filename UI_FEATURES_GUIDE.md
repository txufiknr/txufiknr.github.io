# Portfolio UI Features — Implementation Guide

A single-source guide to every interactive UI feature in this portfolio, with
copy-paste code snippets and the reasoning behind each decision. All techniques
are **CSS-first**; the few JavaScript bits only set CSS custom properties or
call `scrollBy`/`scrollIntoView` (no animation logic lives in JS).

> **Conventions used in this project**
> - Styles live in `assets/css/index2.min.css`
> - Behavior is wired in the deferred `<script>` at the bottom of `index.html`
> - Custom properties (CSS variables) are the bridge between JS and CSS

---

## Table of Contents
1. [Animated FAQ accordion (`<details>`/`<summary>`)](#1-animated-faq-accordion)
2. [Button smooth drift on mouse move](#2-button-smooth-drift)
3. [Radial light backdrop following the mouse](#3-radial-light-backdrop)
4. [3D tilt perspective on cards](#4-3d-tilt-perspective)
5. [Animated gradient text (hero name)](#5-animated-gradient-text)
6. [Pure-CSS stats counter](#6-pure-css-stats-counter)
7. [Responsive pure-CSS slider carousel](#7-responsive-pure-css-slider)

---

## 1. Animated FAQ accordion (`<details>`/`<summary>`)

Uses the **modern** `interpolate-size: allow-keywords` + `::details-content`
approach so the browser can animate to/from `height: auto`. A `grid`-rows
fallback covers older engines.

### HTML
```html
<section id="faq">
  <h2 class="section-title">Frequently Asked Questions</h2>
  <div class="faq-list">
    <details class="faq-item">
      <summary class="faq-q">
        <span>What's your strongest stack?</span>
        <span class="faq-icon" aria-hidden="true"></span>
      </summary>
      <div class="faq-a"><p>Answer text goes here.</p></div>
    </details>
    <!-- more <details> items… -->
  </div>
</section>
```

### CSS
```css
/* Allow the browser to animate to/from auto sizing keywords */
html { interpolate-size: allow-keywords; }

.faq-item {
  background: linear-gradient(160deg, rgba(124,58,237,.04), rgba(20,0,46,.8));
  border: 1px solid rgba(124,58,237,.08);
  border-radius: var(--radius-xl);
  overflow: hidden;
}
.faq-q {
  display: flex; align-items: center; justify-content: space-between; gap: 1rem;
  padding: 1.25rem 1.5rem; cursor: pointer; list-style: none;
}
.faq-q::-webkit-details-marker { display: none }      /* hide default triangle */
.faq-item[open] .faq-q { color: var(--clr-primary-light) }

/* + → × rotating icon */
.faq-icon { position: relative; width: 22px; height: 22px; transition: transform .4s; }
.faq-icon::before, .faq-icon::after {
  content: ''; position: absolute; top: 50%; left: 50%;
  background: var(--clr-primary-light); border-radius: 2px;
  transform: translate(-50%,-50%);
}
.faq-icon::before { width: 16px; height: 2px }
.faq-icon::after  { width: 2px;  height: 16px }
.faq-item[open] .faq-icon { transform: rotate(135deg) }

/* Modern: animate native details-content to/from auto height */
.faq-item::details-content {
  height: 0; overflow: hidden;
  transition: height .35s cubic-bezier(.22,1,.36,1), content-visibility .35s allow-discrete;
}
.faq-item[open]::details-content { height: auto; }

/* Fallback for browsers without ::details-content (grid-rows trick) */
@supports not (height: calc(0px + 1lh)) {
  .faq-a { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .45s; }
  .faq-item[open] .faq-a { grid-template-rows: 1fr }
}
.faq-a > p { overflow: hidden; margin: 0; padding: 0 1.5rem; }
.faq-item[open] .faq-a > p { padding-bottom: 1.5rem }
```

**Why `interpolate-size`?** Without it, `height: auto` is not interpolable, so
the transition snaps. `allow-keywords` lets the browser tween `0 → auto`.

---

## 2. Button smooth drift on mouse move

Buttons translate slightly toward the cursor inside their own bounds. JS only
writes `--drift-x` / `--drift-y`; CSS does the smooth `translate3d` + transition.

### HTML
```html
<a href="#portfolio" class="btn btn-primary portfolio-btn">View Portfolio</a>
<a href="#testimonials" class="btn btn-outline about-btn">About Me</a>
```

### CSS
```css
.btn {
  --drift-x: 0px; --drift-y: 0px;
  transform: translate3d(var(--drift-x), var(--drift-y), 0);
  transition: background .3s ease, box-shadow .3s ease, color .3s ease,
              border-color .3s ease, transform .4s cubic-bezier(.22,1,.36,1);
  will-change: transform;
}
.btn:hover { transform: translate3d(var(--drift-x), calc(var(--drift-y) - 2px), 0) }
```

### JS (inside the IIFE)
```js
function setupParallax() {
  var hero  = document.getElementById("hero");
  var pBtn  = document.querySelector(".portfolio-btn");
  var aBtn  = document.querySelector(".about-btn");
  if (!hero) return;

  // 3D tilt on the portfolio button (hero-wide), driven by cursor
  if (pBtn) {
    hero.addEventListener("mousemove", function (e) {
      var x = (e.clientX / window.innerWidth - 0.5) * 20;
      var y = (e.clientY / window.innerHeight - 0.5) * -20;
      pBtn.style.setProperty("--rotate-x", y + "deg");
      pBtn.style.setProperty("--rotate-y", x + "deg");
    });
  }

  // Per-button drift: each button drifts toward the cursor within its bounds
  var MAX = 10; // max drift in px
  [pBtn, aBtn].forEach(function (btn) {
    if (!btn) return;
    btn.addEventListener("mousemove", function (e) {
      var r  = btn.getBoundingClientRect();
      var dx = ((e.clientX - r.left) / r.width  - 0.5) * 2; // -1 .. 1
      var dy = ((e.clientY - r.top)  / r.height - 0.5) * 2;
      btn.style.setProperty("--drift-x", (dx * MAX) + "px");
      btn.style.setProperty("--drift-y", (dy * MAX) + "px");
    });
    btn.addEventListener("mouseleave", function () {
      btn.style.setProperty("--drift-x", "0px");
      btn.style.setProperty("--drift-y", "0px");
    });
  });
}
```

**Why CSS-first?** Animating `transform` via a CSS `transition` is GPU-friendly
and smooth. JS never touches `transform` directly — it only updates two numbers.

**Combining with the 3D tilt** (portfolio button): both effects share one
`transform` so they don't conflict:
```css
.portfolio-btn {
  transform: translate3d(var(--drift-x,0px), var(--drift-y,0px), 0)
             perspective(800px) rotateX(var(--rotate-x,0deg)) rotateY(var(--rotate-y,0deg));
}
```

---

## 3. Radial light backdrop following the mouse

A glow that tracks the cursor over a card. Implemented as a `::after` overlay
positioned at `--mx` / `--my`, revealed on hover.

### CSS (shared across card types)
```css
/* picks up .skill-category, .project-card, .testimonial-card */
[data-stagger] > .skill-category,
[data-stagger] > .project-card,
[data-stagger] > .testimonial-card {
  --mx: 50%; --my: 50%;
  position: relative;
}

.skill-category::after,
.project-card::after,
.testimonial-card::after {
  content: ''; position: absolute; inset: 0; z-index: 1; pointer-events: none;
  border-radius: inherit; opacity: 0; transition: opacity .35s ease;
  background: radial-gradient(220px circle at var(--mx) var(--my),
              rgba(124,58,237,.22), transparent 65%);
}
/* keep real content above the glow */
.skill-category > *, .project-card > *, .testimonial-card > * {
  position: relative; z-index: 2;
}
.skill-category:hover::after,
.project-card:hover::after,
.testimonial-card:hover::after { opacity: 1 }
```

### JS (the same function drives the 3D tilt — see §4)
```js
function setupCardSpotlight() {
  var MAX = 8; // tilt degrees
  var cards = document.querySelectorAll(".skill-category, .project-card, .testimonial-card");
  cards.forEach(function (card) {
    card.addEventListener("mousemove", function (e) {
      var r  = card.getBoundingClientRect();
      var px = (e.clientX - r.left) / r.width;   // 0..1
      var py = (e.clientY - r.top)  / r.height;  // 0..1
      card.style.setProperty("--mx", (px * 100) + "%");
      card.style.setProperty("--my", (py * 100) + "%");
      card.style.setProperty("--tilt-y", ((px - 0.5) * 2 * MAX) + "deg");
      card.style.setProperty("--tilt-x", ((0.5 - py) * 2 * MAX) + "deg");
    });
    card.addEventListener("mouseleave", function () {
      card.style.setProperty("--tilt-x", "0deg");
      card.style.setProperty("--tilt-y", "0deg");
    });
  });
}
```

---

## 4. 3D tilt perspective on cards

Tilt is driven by `--tilt-x` / `--tilt-y` (set in §3's `mousemove`). The key
architectural rule: **stagger entrance animates `translate`, tilt animates
`transform`** — separate properties, so they never clash.

### CSS
```css
/* Resting state: no transform here, so the stagger's `translate` owns entrance */
[data-stagger] > .skill-category,
[data-stagger] > .project-card,
[data-stagger] > .testimonial-card {
  --tilt-x: 0deg; --tilt-y: 0deg;
  transform-style: preserve-3d;
  will-change: transform;
}

/* Tilt only on hover — transform is independent from the stagger's `translate` */
[data-stagger] > .skill-category:hover,
[data-stagger] > .project-card:hover,
[data-stagger] > .testimonial-card:hover {
  transform: perspective(900px)
             rotateX(var(--tilt-x)) rotateY(var(--tilt-y))
             translateY(var(--lift, 0));
}
.skill-category:hover { --lift: -4px }
.project-card:hover  { --lift: -8px }
.testimonial-card:hover { --lift: -4px }
```

### Stagger entrance (uses `translate`, NOT `transform`)
```css
[data-stagger] > * {
  opacity: 0; translate: 0 30px; filter: blur(8px);
  transition: opacity .6s cubic-bezier(.22,1,.36,1) calc(var(--i,0) * 0.1s),
              translate .6s cubic-bezier(.22,1,.36,1) calc(var(--i,0) * 0.1s),
              filter  .6s cubic-bezier(.22,1,.36,1) calc(var(--i,0) * 0.1s),
              transform .25s cubic-bezier(.22,1,.36,1);
}
[data-stagger] > *.visible { opacity: 1; translate: 0 0; filter: blur(0) }
```

> **Pitfall we hit:** putting `transform` on the card's base rule made its
> specificity (`0,2,1`) beat the stagger's `translateY(30px)` (`0,1,1`), so cards
> showed immediately. Solution — never set `transform` on the resting card; let
> the stagger fully own the entrance, and apply tilt only via `:hover`.

---

## 5. Animated gradient text (hero name)

A looping `background-position` sweep clipped to the text. Reuses the site
palette.

### HTML
```html
<h1 class="hero-name">Hi, I'm <span class="highlight">Taufik Nur Rahmanda</span></h1>
```

### CSS
```css
.hero-name .highlight {
  background: linear-gradient(110deg,
              var(--clr-primary) 0%, var(--clr-accent) 25%,
              var(--clr-white) 50%, var(--clr-accent) 75%,
              var(--clr-primary) 100%);
  background-size: 200% auto;
  -webkit-background-clip: text; background-clip: text;
  -webkit-text-fill-color: transparent; color: transparent;
  animation: nameShine 5s linear infinite;
}
@keyframes nameShine {
  0%   { background-position: 0% 50% }
  100% { background-position: 200% 50% }
}
```

`background-size: 200% auto` + sweeping `background-position` to `200%` makes the
gradient scroll seamlessly. `prefers-reduced-motion` users get a static gradient
via the global reduced-motion rule.

---

## 6. Pure-CSS stats counter

Counts up using an animatable `@property` integer rendered through `counter()`.
The target is set per element via `--target`; a scroll-driven timeline triggers
it on entry. **No JS, no `setInterval`.**

### HTML
```html
<section id="stats" data-stagger>
  <div class="stat-item" style="--target: 50">
    <div class="stat-number"><span class="stat-value"></span><span class="stat-plus">+</span></div>
    <p class="stat-label">Projects Completed</p>
  </div>
  <!-- more .stat-item with --target: 15 / 8 / 9 … -->
</section>
```

### CSS
```css
/* 1. Register an animatable integer */
@property --num {
  syntax: "<integer>";
  initial-value: 0;
  inherits: true;          /* so .stat-value can read it via counter-reset */
}
@keyframes statCount {
  from { --num: 0 }
  to   { --num: var(--target) }
}

.stat-number .stat-value {
  /* gradient text (palette) */
  background: linear-gradient(135deg, var(--clr-white), var(--clr-primary-light));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  /* render the animating integer as text */
  counter-reset: num var(--num);
}
.stat-number .stat-value::after { content: counter(num) }

.stat-item {
  --num: var(--target);   /* default to target so it's correct even if animation never runs */
  animation: statCount 2.4s cubic-bezier(.22,1,.36,1) forwards;
  animation-timeline: view();             /* scroll-driven */
  animation-range: entry 5% cover 55%;    /* finishes near mid-viewport */
}
/* Fallback: browsers without scroll-driven animations count on load */
@supports not (animation-timeline: view()) {
  .stat-item { animation: statCount 2.4s cubic-bezier(.22,1,.36,1) forwards; }
}
```

**Corrections that made it work:**
- `inherits: true` — the animated `--num` lives on `.stat-item` but
  `counter-reset` is on `.stat-value`; without inheritance the child reads the
  initial `0` forever → "always 0".
- Default `--num: var(--target)` — because `@property` gives `--num` an
  `initial-value: 0`, a `var(--num, var(--target))` fallback **never** triggers.
  Setting the element default to the target guarantees the right number shows
  even if the animation can't run.
- `animation-range: entry 5% cover 55%` — extends the count across more scroll
  distance so increments stay visible even on fast scroll (previously finished
  too early at `cover 40%`).

> Requires Chromium for the scroll-timed version; Firefox/Safari fall back to the
> time-based animation (counts on load) or show the static target.

---

## 7. Responsive pure-CSS slider carousel (testimonials)

Horizontal `scroll-snap` carousel with auto-generated dot navigation
(`scroll-marker`), prev/next arrow buttons, mouse-wheel scrolling, and
responsive slide counts (3 / 2 / 1).

### HTML
```html
<section id="testimonials">
  <h2 class="section-title">What People Say</h2>
  <p class="section-subtitle">Kind words from colleagues and collaborators</p>

  <div class="testimonial-slider">
    <button class="slider-arrow slider-prev" type="button" aria-label="Previous">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    <div class="testimonial-track" data-stagger>
      <blockquote class="testimonial-card">
        <div class="card-content">
          <q>Great to work with…</q>
          <div class="attribution">
            <img src="assets/images/testimony/avatar.webp" alt="Name" width="40" height="40" loading="lazy">
            <div><div class="name">Name</div><div class="role">Role</div></div>
          </div>
        </div>
        <a href="https://linkedin.com/in/…" class="card-linkedin"><svg>…</svg><span>LinkedIn</span></a>
      </blockquote>
      <!-- more .testimonial-card … -->
    </div>

    <button class="slider-arrow slider-next" type="button" aria-label="Next">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
  </div>
</section>
```

### CSS
```css
.testimonial-slider {
  position: relative;
  display: flex; align-items: center; gap: 1rem;
  padding-bottom: 2.75rem;     /* room for the dots below */
}
.testimonial-track {
  flex: 1 1 0; min-width: 0;   /* shrink to fit BETWEEN the arrows (fixes truncation) */
  display: flex;
  overflow-x: auto; scroll-snap-type: x mandatory;
  scroll-marker-group: after;
  padding-bottom: 1rem; scroll-padding: 0;
  scrollbar-width: none; -ms-overflow-style: none;   /* hide scrollbar */
}
.testimonial-track::-webkit-scrollbar { display: none; width: 0; height: 0 }

.testimonial-card {
  --per-view: 3;                                  /* 3 up on desktop */
  flex: 0 0 calc(100% / var(--per-view));          /* EXACT % — no gap, no truncation */
  scroll-snap-align: start;
  padding: 2rem 1.5rem 1rem;                       /* horizontal padding = visual gutter */
  /* …background, border, etc. */
}

/* Dot navigation (Chrome 129+) */
.testimonial-card::scroll-marker {
  content: ""; width: .7em; height: .7em; aspect-ratio: 1; flex: 0 0 auto;
  border-radius: 50%;
  background: rgba(167,139,250,.25); border: 2px solid rgba(167,139,250,.4);
  transition: transform .25s, background .25s, border-color .25s;
}
.testimonial-card::scroll-marker:hover { transform: scale(1.15); border-color: var(--clr-primary-light) }
::scroll-marker:target-current {
  background: var(--clr-primary-light); border-color: var(--clr-primary-light); transform: scale(1.35);
}
/* Pin the marker group to bottom-center (it's injected as the track's last flex child) */
::scroll-marker-group {
  position: absolute; left: 0; right: 0; bottom: 0;
  display: flex; flex-wrap: wrap; gap: .6em; justify-content: center; align-items: center;
}

/* Arrows */
.slider-arrow {
  flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: rgba(124,58,237,.12); border: 1px solid rgba(124,58,237,.25);
  color: var(--clr-primary-light); cursor: pointer;
  transition: background .3s, transform .3s, box-shadow .3s;
}
.slider-arrow:hover { background: var(--clr-primary); color: #fff; transform: scale(1.08); box-shadow: 0 6px 20px var(--clr-glow); }
.slider-arrow:active { transform: scale(.94) }

/* Responsive slide counts */
@media (max-width: 768px) { .testimonial-card { --per-view: 2 } }
@media (max-width: 480px) {
  .testimonial-card { --per-view: 1 }
  .slider-arrow { display: none }   /* swipe on mobile instead */
}
```

### JS (arrows + wheel scroll)
```js
function setupTestimonialSlider() {
  var slider = document.querySelector(".testimonial-slider");
  var track  = slider && slider.querySelector(".testimonial-track");
  if (!track) return;
  var prev = slider.querySelector(".slider-prev");
  var next = slider.querySelector(".slider-next");

  function step() {
    var card = track.querySelector(".testimonial-card");
    return card ? card.getBoundingClientRect().width : track.clientWidth * 0.8;
  }
  if (prev) prev.addEventListener("click", function () { track.scrollBy({ left: -step(), behavior: "smooth" }); });
  if (next) next.addEventListener("click", function () { track.scrollBy({ left:  step(), behavior: "smooth" }); });

  // Mouse-wheel over the slider scrolls it horizontally (no page-scroll trap at ends)
  track.addEventListener("wheel", function (e) {
    if (e.deltaY === 0) return;
    var max     = track.scrollWidth - track.clientWidth;
    var atStart = track.scrollLeft <= 0;
    var atEnd   = track.scrollLeft >= max - 1;
    var canScroll = (e.deltaY < 0 && !atStart) || (e.deltaY > 0 && !atEnd);
    if (canScroll) {
      e.preventDefault();
      track.scrollLeft += e.deltaY;
    }
  }, { passive: false });
}
```

**Key fixes we applied:**
- **Truncation:** used exact percentage widths (`calc(100% / --per-view)`) with
  *no* flex `gap` (gap + percentage exceeded 100% by subpixels and clipped the
  last card). Visual spacing now comes from the card's horizontal `padding`.
  Also `flex: 1 1 0; min-width: 0` on the track so it shrinks to fit *between*
  the arrows.
- **Dots on the right / oval:** the marker group is injected as the track's last
  flex child, so it landed inline at the row's end and got stretched. We pinned
  it `position: absolute; bottom: 0` under the slider and forced `aspect-ratio:
  1` on markers for perfect circles.

> Dot navigation (`scroll-marker`) needs Chrome 129+. Firefox/Safari keep the
> working scroll/arrow/wheel slider but without auto dots (graceful).

---

## Browser-support cheat sheet

| Feature | Modern support | Fallback |
|---|---|---|
| FAQ `interpolate-size` + `::details-content` | Chromium 129+ | `grid-template-rows: 0fr→1fr` |
| Button drift / 3D tilt | All modern | none needed (progressive) |
| Radial spotlight | All modern | none needed |
| Gradient text | All modern | static gradient |
| Stats `@property` counter | Chromium/FF/Safari (recent) | `@supports` time-based animation |
| Slider `scroll-snap` | All modern | native scroll |
| Slider `scroll-marker` dots | Chromium 129+ | hidden; arrows/wheel still work |
| Slider scroll-driven stats | Chromium 115+ | time-based on load |

All features respect `prefers-reduced-motion` via the global rule at the bottom
of the stylesheet.
