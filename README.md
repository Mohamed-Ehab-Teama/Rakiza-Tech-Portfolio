# Teamora — Mohamed Ehab Teama · Portfolio

A premium, animated developer portfolio with a dark **IDE / terminal / dashboard** aesthetic —
**front-end only**, built with plain **HTML, CSS and JavaScript** (no framework, **no build step**).
Animation is powered by GSAP + ScrollTrigger, Lenis smooth scroll and SplitType (all from CDN).

- **Owner:** Mohamed Ehab Teama (brand: *Teamora*) — PHP / Laravel Developer
- **Animated wordmark:** types `Mohamed Ehab Teama` ⇄ `Teamora`

## ▶️ Run it

It's a static site. Just open it through any web server (recommended — works everywhere):

- **Laragon:** it lives in `www/Teamora-Apps`, so visit `http://teamora-apps.test/`
  (or `http://localhost/Teamora-Apps/`).
- **Any static server:**
  ```bash
  npx serve .
  # or
  python -m http.server 8080
  # or the tiny bundled one (zero dependencies):
  node serve.mjs 8080
  ```

> Double-clicking `index.html` (the `file://` protocol) won't work, because the sections are pulled in
> with `fetch()` (and ES modules + CDN libs are blocked on `file://`). Always use one of the servers above.

## 🗂 Project structure (simple & no-build)

```
index.html              ← page shell: <head>, the preloader, and <div data-include> slots
html/                   ← EACH SECTION IS ITS OWN FILE — edit your content here
  nav.html  hero.html  about.html  services.html  work.html
  metrics.html  stack.html  terminal.html  contact.html  footer.html
css/
  base.css              ← colors, fonts, spacing (the theme)  → recolor the whole site here
  components.css        ← shared bits: buttons, cards, terminal, cursor, forms…
  sections/             ← one stylesheet per section
    nav.css  hero.css  about.css  services.css  work.css
    metrics.css  stack.css  terminal.css  contact.css  footer.css  preloader.css
js/
  main.js               ← loads the html/ partials, then boots everything
  core/                 ← include · smooth-scroll · cursor · brand (wordmark) · reveal · utils
  sections/             ← one module per section (nav, hero, work, terminal, …)
assets/
  img/                  ← put your photo / project screenshots / resume.pdf here
  favicon.svg
serve.mjs               ← optional zero-dependency local server
```

**How the sections come together:** `index.html` contains one `<div data-include="html/hero.html">`
per section. On load, `js/core/include.js` fetches each file and drops it in — so you edit a section
in its own small file (`html/hero.html`, `html/work.html`, …) and just refresh. (The preloader is kept
inline in `index.html` so something always shows while the partials load.)

> Section ↔ file map: **Work** = `work.*`, **Metrics dashboard** = `metrics.*`, **Stack/skills** =
> `stack.*` (HTML in `html/`, styles in `css/sections/`, behavior in `js/sections/`). Others match their name.

## ✏️ How to edit common things

| I want to… | Edit |
|---|---|
| Change any text / bio / tagline | the section's file in `html/` (e.g. hero text → `html/hero.html`) |
| Add or remove a project | `html/work.html` → copy/delete an `<article class="work__item">…</article>` block |
| Update the terminal answers (`about`, `projects`, `skills`, `contact`) | `js/sections/terminal.js` (top of the file: `PROJECTS`, `SKILLS`, `SOCIALS`, `EMAIL`…) |
| Recolor the site | `css/base.css` → the `--accent`, `--accent-2`, `--bg` variables |
| Change the rotating hero phrases | `js/sections/hero.js` → the `phrases` array |
| Change the wordmark words | `js/core/brand.js` → the `WORDS` array |

## 🎨 Design system (quick reference)

All colors/spacing/type are CSS custom properties in `css/base.css`.

- **Accents:** `--accent` cyan `#6ee7ff`, `--accent-2` violet `#a78bfa`, `--term` green `#4ade80`.
- **Fonts:** Space Grotesk (display), Inter (body), JetBrains Mono (code) — via Google Fonts.
- **Motion:** Lenis smooth scroll; reveal anything by adding `data-reveal="up|fade|scale|lines|chars"`;
  count up numbers with `data-count`; magnetic hover with `data-magnetic`.
- **Accessibility:** respects `prefers-reduced-motion` and hides the custom cursor on touch devices.

## 📇 Your details (already wired in)

- **Email:** mohamed111.me111@gmail.com · katchan111.me111@gmail.com
- **Phone:** +20 109 016 6589
- **GitHub:** https://github.com/Mohamed-Ehab-Teama
- **LinkedIn:** https://www.linkedin.com/in/mohamed-ehab-teama-267154250/
- **Location:** Cairo / Menofia, Egypt

> Project details (Garage Parking System, LMS, Student Observer, …) currently use placeholder
> descriptions/stacks/years — update them in `html/work.html` and `js/sections/terminal.js`.

## 🚀 Deploy

Upload the whole folder to any static host or to cPanel's `public_html` (no PHP/DB needed).
A Laravel back-end can be added later without throwing this away.
