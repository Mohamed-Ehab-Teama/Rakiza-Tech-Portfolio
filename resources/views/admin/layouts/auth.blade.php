{{-- admin/layouts/auth.blade.php — centered card for sign-in and password pages. --}}
@php($defaultTheme = setting('default_theme') ?: config('themes.default'))
<!DOCTYPE html>
<html lang="en" data-theme="{{ $defaultTheme === 'system' ? 'dark' : $defaultTheme }}" data-theme-default="{{ $defaultTheme }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="color-scheme" content="dark light" />
    <meta name="theme-color" content="#08090d" />
    @include('partials.theme-init')
    <title>@yield('title') — {{ setting('site_name') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/brand/favicon-32.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/components.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
  </head>
  <body class="admin admin--auth">
    <div class="auth-theme">@include('partials.theme-switch')</div>

    <main class="auth-card" id="admin-main">
      <a class="auth-brand" href="{{ route('home') }}">
        <img src="{{ asset('assets/brand/rakiza-mark.png') }}" alt="" width="64" height="64" />
        <span>{{ setting('site_name') }}</span>
      </a>
      <h1 class="auth-title">@yield('title')</h1>
      @hasSection('intro')
        <p class="auth-intro">@yield('intro')</p>
      @endif

      @if (session('status'))
        <div class="flash flash--success" role="status">{{ session('status') }}</div>
      @endif

      @yield('content')
    </main>

    <script type="module" src="{{ asset('js/admin/admin.js') }}"></script>
  </body>
</html>
