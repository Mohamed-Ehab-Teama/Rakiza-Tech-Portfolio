{{--
    layouts/site.blade.php — the HTML shell of the PUBLIC website.

    Every public page "extends" this file and only fills in the
    @section('content') part. Everything shared by all pages lives here:
    the <head>, the decorative overlays, the preloader, nav, footer and scripts.

    asset('css/base.css') builds the full URL of a file inside public/,
    so links keep working on any page (/ or /projects/x) and on any domain.

    Blade comments (like this block) are removed on the server and never
    reach the browser. Normal HTML comments are sent to the browser as usual.
--}}
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mohamed Ehab Teama — PHP / Laravel Developer · Teamora</title>
    <meta name="description" content="Mohamed Ehab Teama (Teamora) — PHP and Laravel developer from Egypt. I build web applications end to end: databases, APIs, and the screens on top." />
    <meta name="author" content="Mohamed Ehab Teama" />
    <meta name="color-scheme" content="dark" />
    <meta name="theme-color" content="#08090d" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon.svg') }}" />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="Mohamed Ehab Teama — PHP / Laravel Developer · Teamora" />
    <meta property="og:description" content="Mohamed Ehab Teama (Teamora) — PHP and Laravel developer from Egypt. I build web applications end to end: databases, APIs, and the screens on top." />
    <meta name="twitter:card" content="summary_large_image" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net" />
    <link
      href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="{{ asset('css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/components.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/preloader.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/nav.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/hero.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/about.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/services.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/work.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/metrics.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/stack.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/terminal.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/contact.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/footer.css') }}" />

    <noscript><style>.preloader{display:none!important}</style></noscript>
    <script>document.documentElement.classList.add("js");</script>
  </head>
  <body>
    <div class="grain" aria-hidden="true"></div>
    <div class="scroll-progress" aria-hidden="true"></div>
    <div class="cursor" aria-hidden="true">
      <div class="cursor__dot"></div>
      <div class="cursor__ring"></div>
    </div>
    <a class="sr-only" href="#main">Skip to content</a>

    @include('sections.preloader')

    @include('sections.nav')

    <main id="main">
      @yield('content')
    </main>

    @include('sections.footer')

    <!-- Animation libraries (CDN, UMD globals) -->
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.14/dist/lenis.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/split-type@0.3.4/umd/index.min.js"></script>
    <script type="module" src="{{ asset('js/main.js') }}"></script>
  </body>
</html>
