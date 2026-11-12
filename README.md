# Rakiza Tech — Digital Solutions

The website and dashboard of **Rakiza Tech** (ركيزة تك), a digital solutions company from Egypt that builds
websites, systems and apps. *Rakiza* (ركيزة) means **pillar / foundation**.

- **Public website** — an animated one-page landing site in an IDE / terminal style, plus project case-study pages.
- **Dashboard** (`/admin`) — manage projects, websites, services, skills, numbers, site texts, contact messages,
  users, roles & permissions, and see an activity log.
- **Color themes** — System, Dark, Light, Dim, Sepia and the brand theme Rakiza, on the site and in the dashboard.
- **SEO-ready** — per-page meta tags, Open Graph images, structured data, sitemap and robots.txt.

Built with **Laravel 13**, Blade, plain CSS and native JavaScript modules — **no front-end build step**.

## 📚 Guides

| Guide | For |
|---|---|
| [**docs/USER_GUIDE.md**](docs/USER_GUIDE.md) | The people who run the website: everything in the dashboard, step by step. Also available inside the dashboard under *Help & guide*. |
| [**docs/DEVELOPER_GUIDE.md**](docs/DEVELOPER_GUIDE.md) | Developers: setup, architecture, data model, themes, permissions, testing, deployment, troubleshooting, recipes. |

## ▶️ Quick start

Requirements: PHP 8.3+, Composer, MySQL (Laragon has everything).

```bash
cp .env.example .env      # set DB_* and ADMIN_EMAIL / ADMIN_PASSWORD
composer setup            # install, key, migrate, seed, storage:link
php artisan serve         # http://127.0.0.1:8000  — dashboard at /admin
```

If `ADMIN_PASSWORD` is empty, `composer setup` prints a generated password for the first Super Admin.
Locked out? `php artisan rakiza:make-admin you@example.com` creates or resets a Super Admin.

## ✅ Tests

```bash
php artisan test                      # 88 server-side tests (PHPUnit)
node tests/browser/check-page.mjs     # real-browser check (site must be running)
```

## 🗂 Where things are

```
routes/web.php, routes/admin.php      public site and dashboard URLs
app/Http/Controllers(/Admin)          page logic
app/Models                            Project, Website, Service, Skill(Category), Metric, ContactMessage, …
config/settings.php                   every editable site setting
config/access.php                     permissions and default roles
config/themes.php                     the color themes
resources/views/sections              the home-page sections (Blade)
resources/views/admin                 the dashboard
public/css, public/js                 styles and scripts (no build step)
docs/                                 the guides
```
