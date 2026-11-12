{{--
    layouts/site.blade.php — the HTML shell of the PUBLIC website.

    Pages "extend" this file and fill @section('content'). They can also set:
      @section('title', …)        page title       (default: Settings → SEO)
      @section('description', …)  meta description (default: Settings → SEO)
      @section('og_image', …)     share image URL  (default: Settings → SEO)
      @section('og_type', …)      "website" or "article"
      @push('jsonld') … @endpush  structured data for Google
    and pass ['withPreloader' => true] to show the boot animation (home only).

    asset('…') builds the full URL of a file inside public/.
--}}
@php
    $defaultTheme = setting('default_theme') ?: config('themes.default');
    $siteName = setting('site_name');
    $siteTitle = setting('seo_title') ?: $siteName.' — '.setting('tagline');
    $ogImage = setting('og_image') ? asset('storage/'.setting('og_image')) : asset('assets/brand/og-image.png');

    // yieldContent() returns escaped text (a page's @section value or the default).
    $metaTitle = $__env->yieldContent('title', $siteTitle);
    $metaDescription = $__env->yieldContent('description', setting('seo_description'));
    $metaImage = $__env->yieldContent('og_image', $ogImage);
    $canonical = $__env->yieldContent('canonical', url()->current());
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $defaultTheme === 'system' ? 'dark' : $defaultTheme }}" data-theme-default="{{ $defaultTheme }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.theme-init')
    <title>{!! $metaTitle !!}</title>
    <meta name="description" content="{!! $metaDescription !!}" />
    <link rel="canonical" href="{!! $canonical !!}" />
    <meta name="author" content="{{ $siteName }}" />
    <meta name="color-scheme" content="dark light" />
    <meta name="theme-color" content="#08090d" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/brand/favicon-32.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('assets/brand/apple-touch-icon.png') }}" />
    @if (setting('google_site_verification'))
      <meta name="google-site-verification" content="{{ setting('google_site_verification') }}" />
    @endif

    {{-- Link previews (Facebook, LinkedIn, WhatsApp, X…) --}}
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta property="og:title" content="{!! $metaTitle !!}" />
    <meta property="og:description" content="{!! $metaDescription !!}" />
    <meta property="og:url" content="{!! $canonical !!}" />
    <meta property="og:image" content="{!! $metaImage !!}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{!! $metaTitle !!}" />
    <meta name="twitter:description" content="{!! $metaDescription !!}" />
    <meta name="twitter:image" content="{!! $metaImage !!}" />

    @stack('jsonld')

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net" />
    {{-- IBM Plex Sans Arabic draws the Arabic letters (ركيزة تك). Google serves it
         with a unicode-range, so the browser only downloads it when Arabic text
         is actually on screen. --}}
    <link
      href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;600&display=swap"
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
    <link rel="stylesheet" href="{{ asset('css/sections/websites.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/metrics.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/stack.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/terminal.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/contact.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sections/footer.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}" />

    <noscript><style>.preloader{display:none!important}</style></noscript>
    <script>document.documentElement.classList.add("js");</script>

    @if (setting('google_analytics_id'))
      {{-- Google Analytics (only when an ID is set in Settings → SEO) --}}
      <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json(setting('google_analytics_id')));
      </script>
    @endif
  </head>
  <body>
    <div class="grain" aria-hidden="true"></div>
    <div class="scroll-progress" aria-hidden="true"></div>
    <div class="cursor" aria-hidden="true">
      <div class="cursor__dot"></div>
      <div class="cursor__ring"></div>
    </div>
    <a class="sr-only" href="#main">Skip to content</a>

    @if ($withPreloader ?? false)
      @include('sections.preloader')
    @endif

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
