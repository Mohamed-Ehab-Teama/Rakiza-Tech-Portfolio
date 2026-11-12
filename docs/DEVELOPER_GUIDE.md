# Rakiza Tech — Developer Guide

Everything a developer needs to run, understand, change, test and deploy the Rakiza Tech website and its dashboard.

- **Public website** — a one-page landing site (`/`), a project list (`/projects`) and project pages (`/projects/{slug}`).
- **Dashboard** — `/admin`: content management, inbox, users, roles & permissions, settings, activity log.
- For the people who edit the site, see [`USER_GUIDE.md`](USER_GUIDE.md) (also shown in the dashboard under *Help & guide*).

---

## 1. Stack

| Layer | Choice |
|---|---|
| Backend | **Laravel 13** (PHP 8.3+) |
| Database | **MySQL 8** in development/production (tests use in-memory SQLite) |
| Views | **Blade** templates — no front-end framework |
| CSS / JS | Plain CSS files and **native ES modules** in `public/` — **no build step**, no Node needed |
| Animation (public site) | GSAP + ScrollTrigger, Lenis, SplitType — loaded from the jsDelivr CDN |
| Roles & permissions | **spatie/laravel-permission** 8 |
| Fonts | Google Fonts: Space Grotesk, Inter, JetBrains Mono, IBM Plex Sans Arabic |

---

## 2. Getting started (local)

Requirements: PHP 8.3+ with `gd`, `pdo_mysql`, `mbstring`, `fileinfo`; Composer; MySQL. **Laragon** has all of them.

```bash
git clone https://github.com/Mohamed-Ehab-Teama/Rakiza-Tech-Portfolio.git
cd Rakiza-Tech-Portfolio
cp .env.example .env            # then edit DB_* and ADMIN_* (see below)
composer setup                  # install, key, migrate, seed, storage:link
php artisan serve               # http://127.0.0.1:8000
```

`composer setup` runs: `composer install` → creates `.env` if missing → `key:generate` → `migrate --force` → `db:seed --force` → `storage:link`.

### `.env` keys you will touch

| Key | Purpose |
|---|---|
| `APP_URL` | The site's address (used in emails, sitemap and links). |
| `DB_*` | MySQL connection (`DB_DATABASE=rakiza_tech`). |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | The first Super Admin, created by `db:seed`. **Empty password → a random one is generated and printed once.** An existing account is never overwritten. |
| `MAIL_*` | Email sending (password resets, contact notifications). Locally `MAIL_MAILER=log` writes emails to `storage/logs/laravel.log`. |
| `SESSION_DRIVER`, `CACHE_STORE` | `database` by default (tables exist). |

### Laragon

Laragon creates the site `http://rakiza-tech-landing-page.test`. Its **document root must be `public/`**. Laragon writes a site's config file only once (when it first sees the folder); if it points at the project root you will see an "Index of /" listing. Fix it in `laragon/etc/apache2/sites-enabled/auto.<folder>.test.conf` (both paths must end in `/public`) and **Menu → Apache → Reload**.

### Seeded data

| Seeder | What it adds | When |
|---|---|---|
| `RolesAndPermissionsSeeder` | All permissions from `config/access.php`, roles Super Admin / Editor / Viewer | always |
| `AdminUserSeeder` | The first Super Admin from `.env` | always |
| `ContentSeeder` | Services, projects, skill categories + skills, numbers (the original site's content) — only into **empty** tables | always |
| `DemoSeeder` | 3 sample websites, 2 sample messages | **local environment only** |

All seeders are safe to run again (`php artisan db:seed`).

---

## 3. Project structure

```
app/
  Http/Controllers/
    HomeController.php            "/" — loads every section's data
    ProjectController.php         /projects and /projects/{slug}
    ContactController.php         POST /contact
    SeoController.php             /sitemap.xml and /robots.txt
    Admin/                        the dashboard (see §8)
  Http/Middleware/EnsureUserIsActive.php
  Http/Requests/StoreContactMessageRequest.php
  Mail/NewContactMessage.php
  Models/                         Eloquent models (+ Concerns/: Sortable, Publishable, RecordsActivity)
  Support/Settings.php            settings storage (see §6)
  helpers.php                     setting(), social_links(), safe_markdown()
config/
  access.php                      permission areas, default roles, first admin
  settings.php                    every editable setting (type, default, rules)
  themes.php                      the list of color themes
  permission.php                  spatie/laravel-permission's own config
database/migrations, seeders, factories
docs/                             this guide + the user guide
public/
  css/base.css                    design tokens + the 5 color themes
  css/components.css              shared UI (buttons, cards, terminal, theme menu…)
  css/sections/*.css              one file per home-page section
  css/pages.css                   /projects pages
  css/admin.css                   the dashboard
  js/main.js                      boots the public site
  js/core/*.js                    smooth scroll, cursor, wordmark, theme, reveal
  js/sections/*.js                one module per section
  js/admin/admin.js               dashboard enhancements
  assets/brand/                   logo files, favicon, share image
resources/views/
  layouts/site.blade.php          public HTML shell (head, SEO tags, nav, footer, scripts)
  home.blade.php                  the landing page (list of sections)
  sections/*.blade.php            each home-page section (+ sub-partials in folders)
  projects/                       /projects pages
  partials/                       theme-init, theme-switch, JSON-LD, social icons
  admin/                          dashboard views (see §8)
  errors/                         404, 403, 419, 429, 500, 503
routes/
  web.php                         public routes
  admin.php                       dashboard routes (prefix /admin, names admin.*)
  console.php                     rakiza:make-admin
tests/Feature, tests/Unit
```

**Section = three files with the same name:** e.g. *services* = `resources/views/sections/services.blade.php` + `public/css/sections/services.css` + `public/js/sections/services.js`.

---

## 4. How the public site works

1. `GET /` → `HomeController` loads **published** records, already ordered: services, featured projects, websites, skill categories with skills, numbers; plus data for the terminal.
2. `home.blade.php` extends `layouts/site.blade.php` and includes the sections. **A section with nothing published is left out.**
3. Texts, contact details and social links come from **settings** via `setting('key')`.
4. In the browser, `public/js/main.js` boots every section module. JavaScript reads its data from the page rather than hard-coding it:
   - the wordmark words → `data-brand-words` on the logo,
   - the hero's typed phrases → `data-phrases` on `#hero-type`,
   - the preloader lines → the lines already in the HTML,
   - the terminal's answers → JSON in `<script id="terminal-data">` (built by `HomeController::terminalData()`).
5. Inner pages (`/projects…`) reuse the layout without the preloader; nav links point back to `/#section`.

Text typed in the dashboard is always escaped with `{{ }}`. The two Markdown fields (project write-up, About text) go through `safe_markdown()`, which strips raw HTML and blocks `javascript:` links before printing with `{!! !!}`.

---

## 5. Database

| Table | Model | Notes |
|---|---|---|
| `projects` | `Project` | `slug` (unique, auto from title), `tags` JSON, `visual` (card artwork), `cover_path`, `is_featured`, `is_published`, `sort_order` |
| `websites` | `Website` | `url`, `status` (live / in_progress / archived), `screenshot_path`, `launched_on` |
| `services` | `Service` | `visual` (code / app / chart / db / cli), `tags`, `is_wide` |
| `skill_categories`, `skills` | `SkillCategory`, `Skill` | skill: `level` (bar), `marquee_row` (0 / 1 / 2), `show_in_card`; deleting a category cascades |
| `metrics` | `Metric` | `group` highlight / dashboard, `value`, `decimals`, `prefix`, `suffix`, `badge`, `note`, `trend`, `color` |
| `contact_messages` | `ContactMessage` | `read_at` null = unread |
| `settings` | `Setting` | key → value (see §6) |
| `activity_logs` | `ActivityLog` | `user_id`, `action`, morph `subject`, `description`, `properties.changed` |
| `users` | `User` | + `is_active`, `last_login_at`; roles via spatie (`model_has_roles`…) |

Shared model concerns (`app/Models/Concerns`):

- **`Sortable`** — new rows go to the end (`sort_order = max + 1`); `->ordered()` sorts by `sort_order`, then `id`.
- **`Publishable`** — `->published()` scope.
- **`RecordsActivity`** — writes "Created / Updated / Deleted …" to the activity log when a **signed-in** user changes the record (seeders and visitors are never logged; `updated_at`, `remember_token`, `last_login_at` changes are ignored).

Uploaded images are stored on the `public` disk (`storage/app/public/{projects,websites,settings}`) and served through the `public/storage` link (`php artisan storage:link`). Models expose `cover_url` / `screenshot_url`. Old files are deleted when an image is replaced, removed or its record deleted.

---

## 6. Settings

- **`config/settings.php`** declares every setting in groups (the dashboard tabs): label, **type**, **default**, validation **rules**, help text.
  Types: `text`, `email`, `url`, `textarea`, `markdown`, `lines` (one per line → array), `select` (`options` or `options_from => 'themes'`), `boolean`, `image`.
- **`App\Support\Settings`** (a singleton) reads saved rows from the `settings` table — cached forever under `site.settings` and flushed on every save — and casts them by type. Missing rows fall back to the defaults, so a fresh install works before anything is saved.
- **`setting('key')`** (in `app/helpers.php`) is what views use.
- Dashboard → Settings (`Admin\SettingController`) builds its form from the same config and validates with the same rules.

**Add a setting:** add it to `config/settings.php` → use `setting('your_key')` in a view. Nothing else is needed (no migration).

---

## 7. Color themes

- **`config/themes.php`** lists the themes (key, label, description, menu swatch). `system` is not a palette: it resolves to `dark` or `light` from the device and follows changes live.
- **`public/css/base.css`**: `:root` holds the default *dark* tokens; each theme overrides only colors under `:root[data-theme="…"]` (`light`, `dim`, `sepia`, `rakiza`). Components never use raw colors — only tokens such as `--bg`, `--text`, `--accent`, `--accent-rgb`, `--tint-rgb` (subtle overlays), `--bg-rgb`, `--shadow-rgb` / `--shadow-a`, `--on-accent`, `--term-bg-1/2`.
- **`partials/theme-init.blade.php`** — a tiny inline script in `<head>` that applies the saved theme **before the first paint** (no flash). The site default comes from `<html data-theme-default>` = *Settings → General → Default color theme*.
- **`partials/theme-switch.blade.php` + `public/js/core/theme.js`** — the accessible menu (menuitemradio, arrow keys, Escape), saves the choice in `localStorage` (`rakiza-theme`), updates `<meta name="theme-color">` and fires a `themechange` event (the hero canvas listens to recolor).
- The dashboard uses the same tokens, partials and module.

**Add a theme:** add an entry to `config/themes.php` and a `:root[data-theme="key"] { … }` block in `base.css` defining the same variables as the others (copy one and adjust). Check text contrast (at least 4.5:1 for normal text).

---

## 8. The dashboard

Routes: `routes/admin.php` (loaded in `bootstrap/app.php` with prefix `/admin`, names `admin.*`). Guests are redirected to `admin.login`; signed-in pages use the `auth` + `active` middleware (`EnsureUserIsActive` signs out switched-off accounts).

### Content types — `Admin\ResourceController`

Projects, Websites, Services, Skill categories, Skills and Numbers share **one base controller** and **two views** (`admin/resources/index.blade.php`, `form.blade.php`, plus `field.blade.php`). A child controller only *describes* its content:

```php
class WebsiteController extends ResourceController
{
    protected static string $area = 'websites';          // → websites.view / create / update / delete / publish

    protected function model(): string { return Website::class; }

    protected function labels(): array
    {
        return ['singular' => 'Website', 'plural' => 'Websites', 'route' => 'websites'];
    }

    protected function columns(): array                    // list columns: heading => attribute | closure
    {
        return ['Name' => 'name', 'Status' => 'status_label'];
    }

    protected function fields(?Model $record): array      // the form
    {
        return [
            'name' => ['section' => 'Website', 'label' => 'Name', 'type' => 'text',
                       'rules' => ['required', 'string', 'max:120'], 'width' => 'half'],
            // …
            'is_published' => ['label' => 'Published', 'type' => 'boolean', 'publish' => true, 'default' => true],
        ];
    }
}
```

The base class provides: list with search / status filter / pagination, create & edit forms grouped by `section`, validation from each field's `rules`, image upload / replace / remove (`type => image`, `directory`), comma-separated tags → array, the publish switch, drag & drop ordering (`POST …/reorder` with `{ids: […]}`), delete, flash messages, and **permission middleware for every action** (`HasMiddleware`). Optional hooks: `searchable()`, `listQuery()`, `beforeSave()`, `publicUrl()`, `publishable()`, `sortable()`.

Field types: `text`, `textarea`, `markdown`, `number` (`step`), `url`, `email`, `date`, `select` (`options`), `boolean`, `tags`, `image`. Widths: `full` (default), `half`, `quarter`. A field with `'publish' => true` is only shown to users with `{area}.publish`; without it, new records are saved as drafts and edits never change `is_published`.

### Other areas

| Area | Controller | Notes |
|---|---|---|
| Sign in / out, password reset | `Admin\Auth\LoginController`, `PasswordResetController` | 5 failed attempts per email+IP → 60 s lock; inactive accounts refused; reset links point to `/admin/reset-password/{token}` (`ResetPassword::createUrlUsing` in `AppServiceProvider`). |
| Dashboard home | `DashboardController` | Only shows blocks the user may see. |
| Messages | `MessageController` | Opening marks read (needs `messages.update`). |
| Users | `UserController` | Safety rules: only Super Admins grant/touch Super Admin; no self role change / self switch-off / self delete; last active Super Admin protected. |
| Roles | `RoleController` | Permission matrix; Super Admin role protected (403); roles in use cannot be deleted. |
| Settings | `SettingController` | Tabs from `config/settings.php`; `settings.view` = read-only. |
| Activity | `ActivityController` | Search + per-user filter. |
| Profile, Help | `ProfileController`, `HelpController` | Help renders `docs/USER_GUIDE.md`. |

Views live in `resources/views/admin/`; styles in `public/css/admin.css`; behaviour in `public/js/admin/admin.js` (theme menu, mobile sidebar, delete confirmations, password show/hide, image preview, permission "All" boxes, drag & drop + arrow-button ordering). Everything works without JavaScript except ordering.

**Add a content type** (example: *Testimonials*):

1. Migration + model (use `Sortable`, `Publishable`, `RecordsActivity`) + factory.
2. Add an area to `config/access.php` (`'testimonials' => ['label' => 'Testimonials', 'actions' => [...]]`) and run `php artisan db:seed --class=RolesAndPermissionsSeeder`. Add it to the roles in `default_roles` if wanted.
3. `app/Http/Controllers/Admin/TestimonialController.php` extending `ResourceController`.
4. Register it in the `$resources` array in `routes/admin.php`.
5. Add a sidebar link in `resources/views/admin/layouts/app.blade.php` (`$nav`).
6. Show it on the site: load it in `HomeController`, add a section view, CSS and (if needed) JS.
7. Write tests.

---

## 9. Permissions

- Permission names are `area.action` (e.g. `projects.publish`), defined in **`config/access.php`** and created by `RolesAndPermissionsSeeder`.
- spatie/laravel-permission registers them on Laravel's **Gate**, so checks are plain Laravel: `can:projects.update` middleware, `@can('projects.update')` in Blade, `$user->can(...)`.
- **Super Admin bypass:** `Gate::before` in `AppServiceProvider` returns `true` for Super Admins, so they pass every check without holding permissions.
- After changing permissions in code, re-run the roles seeder; the dashboard clears spatie's permission cache whenever roles change.

---

## 10. Contact form & email

- `POST /contact` → `ContactController` with `StoreContactMessageRequest` (validation + honeypot field `website`) and the `throttle:contact` limiter (5 per 10 minutes per IP, defined in `AppServiceProvider`).
- `public/js/sections/contact.js` posts with `fetch` (JSON) and shows field errors (422), "too many" (429), "expired" (419) and network errors. Without JavaScript it is a normal POST that redirects back to `/#contact`.
- A saved message optionally sends `App\Mail\NewContactMessage` (plain text, reply-to = visitor) to the main email; a mail failure is logged and never loses the message.

---

## 11. SEO

- Per page: `<title>`, meta description, canonical, Open Graph + Twitter tags (`layouts/site.blade.php`; pages override with `@section('title')`, `description`, `og_image`, `og_type`).
- Structured data: `Organization` + `WebSite` on the home page (`partials/jsonld-home.blade.php`), `CreativeWork` on project pages.
- `/sitemap.xml` (home, /projects, every published project with `lastmod`) and `/robots.txt` (disallows `/admin`, links the sitemap) — `SeoController`.
- Draft previews and the dashboard send `noindex`.
- Share image: `public/assets/brand/og-image.png` (1200 × 630) unless one is uploaded in *Settings → SEO*. Optional Search Console verification and Google Analytics from settings.
- Performance: no build step but small files; images have width/height; below-the-fold images lazy-load; the small logo uses `rakiza-mark-96.png`.

---

## 12. Testing

```bash
php artisan test                      # everything
php artisan test --filter=Permissions # one class
```

Tests use SQLite in memory (`phpunit.xml`), `RefreshDatabase`, fake storage/mail/notifications. Helpers in `tests/TestCase.php`: `superAdmin()`, `userWithRole('Editor')`, `userWithPermissions([...])`, `seedPermissions()`.

| File | Covers |
|---|---|
| `Feature/PublicSiteTest` | home page from the database, hidden drafts, settings-driven texts, project pages, safe Markdown, SEO tags, sitemap, robots |
| `Feature/ContactFormTest` | saving, validation, honeypot, no-JS redirect, rate limit, notification email |
| `Feature/SettingsAndModelsTest` | settings defaults/casting/cache, slugs, ordering, display helpers |
| `Feature/Admin/AuthTest` | sign in/out, lockout, inactive users, password reset |
| `Feature/Admin/PermissionsTest` | Super Admin bypass, Viewer/Editor limits, publish permission, sidebar |
| `Feature/Admin/ContentManagementTest` | CRUD for every content type, image upload/replace/remove, reorder, filters |
| `Feature/Admin/UserManagementTest` | user rules (no self-promotion, last Super Admin) |
| `Feature/Admin/AdminAreasTest` | roles, settings (incl. image), messages, profile, help, activity |
| `Feature/MakeAdminCommandTest` | the rescue command |
| `Unit/SafeMarkdownTest` | Markdown sanitising |

Code style: `vendor/bin/pint`.

### Real-browser check

PHPUnit cannot see animations, so `tests/browser/check-page.mjs` opens the running site in headless Chrome / Edge / Chromium (no packages to install; needs Node 22+) at a desktop and a phone size and checks that:

- nothing stays invisible — reveal animations, heading words and count-up numbers — after scrolling with the mouse wheel, pressing **End**, or **reloading half-way down** the page;
- `/projects` and a project page render;
- there are no JavaScript errors, console errors or failed requests.

```bash
php artisan serve                                  # in one terminal
node tests/browser/check-page.mjs                  # in another (default http://127.0.0.1:8000)
node tests/browser/check-page.mjs http://rakiza-tech-landing-page.test
node tests/browser/check-page.mjs --headed          # watch it run in a visible window
BROWSER="/path/to/chrome" node tests/browser/check-page.mjs   # if the browser is not found
```

It prints ✔ / ✘ per check and exits with code 1 when something fails. Run it after changing anything in `public/js` or the section views. (Do not run it with the browser pane of an IDE hidden in the background: hidden pages get no animation frames, so animations cannot finish there — the script uses its own headless browser for that reason.)

---

## 13. Deployment

Works on any PHP 8.3 host with MySQL (VPS, cPanel…).

1. Upload the project (or `git clone`) **outside** the public web folder, and point the domain's **document root to `public/`**. (On cPanel without that option, put the project one level up and make `public_html` a symlink to `public/`.)
2. `composer install --no-dev --optimize-autoloader`
3. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://yourdomain.com`, database, `MAIL_*` (SMTP), `ADMIN_*`.
4. `php artisan key:generate` (first time only)
5. `php artisan migrate --force && php artisan db:seed --force` (DemoSeeder is skipped in production)
6. `php artisan storage:link`
7. `php artisan optimize` (caches config, routes, views). Run it again after every update; `php artisan optimize:clear` undoes it.
8. Make `storage/` and `bootstrap/cache/` writable by the web server.
9. Use **HTTPS**. Sign in, change the admin password, fill in *Settings*, then submit `/sitemap.xml` in Google Search Console.

Updating later: pull the code → `composer install --no-dev -o` → `php artisan migrate --force` → `php artisan optimize`.

No queue worker or cron is required.

---

## 14. Troubleshooting

| Problem | Fix |
|---|---|
| "Index of /" instead of the site | The document root is not `public/` (see §2 Laragon / §13). |
| Uploaded images do not show | `php artisan storage:link`; check `APP_URL`. |
| A setting change does not show | It is cached until saved — `php artisan cache:clear` if you edited the `settings` table by hand. |
| Permission changes in code have no effect | `php artisan db:seed --class=RolesAndPermissionsSeeder` and `php artisan permission:cache-reset`. |
| "This page expired" (419) | The CSRF token/session expired: refresh. Check `SESSION_DOMAIN` / HTTPS settings in production. |
| Emails not sent | Check `MAIL_*`; locally they are in `storage/logs/laravel.log`. Contact messages are saved regardless. |
| Nobody can sign in | `php artisan rakiza:make-admin you@example.com` — creates/resets an active Super Admin and prints a new password. |
| Changes in `.env` ignored in production | `php artisan optimize:clear` then `php artisan optimize` (config is cached). |

---

## 15. Recipes

- **Change a text on the site** → *Dashboard → Settings → Home page texts* (or the section's Blade file for fixed wording).
- **New setting** → `config/settings.php` + `setting('key')` (§6).
- **New theme** → `config/themes.php` + `base.css` block (§7).
- **New content type** → §8 "Add a content type".
- **New permission action** → add it to the area's `actions` in `config/access.php`, re-seed roles, then check it (`can:area.action`).
- **New home-page section** → create `sections/x.blade.php` + `css/sections/x.css` (+ `js/sections/x.js` exporting `init()`, registered in `public/js/main.js`), link the CSS in `layouts/site.blade.php`, include it in `home.blade.php`.
- **Brand images** → replace files in `public/assets/brand/` (`rakiza-mark.png` round logo, `rakiza-mark-96.png` small logo, `favicon-32.png`, `apple-touch-icon.png`, `og-image.png`) and `public/favicon.ico`. A higher-resolution logo makes all of them sharper.
