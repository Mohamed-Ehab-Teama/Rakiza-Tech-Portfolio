# Rakiza Tech — Digital Solutions

The website of **Rakiza Tech** (ركيزة تك), a digital solutions company from Egypt that builds
websites, systems and apps. *Rakiza* (ركيزة) means **pillar / foundation**.

A dark, animated **IDE / terminal / dashboard** style landing page, served by **Laravel 13**.
The front end is plain **HTML (Blade), CSS and JavaScript** — no front-end framework and
**no build step**. Animation uses GSAP + ScrollTrigger, Lenis smooth scroll and SplitType (from CDN).

> 🚧 Work in progress: a database and an admin dashboard (projects, websites, services, skills,
> users & permissions, themes, SEO) are being added step by step.

## ▶️ Run it locally

Requirements: PHP 8.3+, Composer, MySQL (Laragon has all three).

```bash
composer install
cp .env.example .env        # then check the DB_* values
php artisan key:generate
php artisan migrate
```

Then either:

- **Laragon:** open `http://rakiza-tech-landing-page.test` (the site's document root must be the
  `public/` folder), or
- **Artisan's server:** `php artisan serve` and open `http://127.0.0.1:8000`.

## 🗂 Project structure

```
routes/web.php                    ← URLs  ("/" → the home page)
resources/views/
  layouts/site.blade.php          ← page shell: <head>, overlays, preloader, nav, footer, scripts
  home.blade.php                  ← the landing page: lists the sections in order
  sections/                       ← EACH SECTION IS ITS OWN FILE — edit content here
    preloader  nav  hero  about  services  work  metrics  stack  terminal  contact  footer
public/                           ← the only folder visitors can reach
  css/
    base.css                      ← colors, fonts, spacing (the theme)
    components.css                ← shared bits: buttons, cards, terminal, cursor, forms, wordmark…
    sections/                     ← one stylesheet per section
  js/
    main.js                       ← boots everything
    core/                         ← smooth-scroll · cursor · brand (wordmark) · reveal · utils
    sections/                     ← one module per section
  assets/brand/                   ← logo files (original, round mark, favicon, touch icon)
```

Each section has three files with the same name, e.g. **hero** =
`resources/views/sections/hero.blade.php` + `public/css/sections/hero.css` + `public/js/sections/hero.js`.

## ✏️ How to edit common things

| I want to… | Edit |
|---|---|
| Change any text | the section's file in `resources/views/sections/` |
| Change the page title / description | `resources/views/layouts/site.blade.php` (`<title>`, `<meta>`) |
| Add or remove a project | `resources/views/sections/work.blade.php` → an `<article class="work__item">` block |
| Update the terminal answers | `public/js/sections/terminal.js` (top: `PROJECTS`, `SKILLS`, `SOCIALS`, `EMAIL`…) |
| Change the wordmark words | `public/js/core/brand.js` → the `WORDS` array |
| Change the rotating hero phrases | `public/js/sections/hero.js` → the `phrases` array |
| Recolor the site | `public/css/base.css` → `--accent`, `--accent-2`, `--bg` |
| Contact details (email, phone, GitHub, LinkedIn) | `nav`, `contact` and `footer` in `resources/views/sections/`, plus `public/js/sections/terminal.js` |

## 🎨 Design system (quick reference)

All colors, spacing and type are CSS custom properties in `public/css/base.css`.

- **Fonts:** Space Grotesk (display), Inter (body), JetBrains Mono (code) and IBM Plex Sans Arabic
  (Arabic text) — via Google Fonts.
- **Motion:** reveal anything with `data-reveal="up|fade|scale|lines|chars"`; count numbers up with
  `data-count`; magnetic hover with `data-magnetic`.
- **Accessibility:** respects `prefers-reduced-motion` and hides the custom cursor on touch devices.
