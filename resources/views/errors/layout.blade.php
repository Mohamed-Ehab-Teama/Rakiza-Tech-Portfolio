{{--
    errors/layout.blade.php — shared look for error pages (404, 403, 419, 429,
    500, 503). Kept simple on purpose: it uses config('app.name') instead of
    database settings, so it still works when the database is the problem.
--}}
<!DOCTYPE html>
<html lang="en" data-theme="dark" data-theme-default="system">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex" />
    <meta name="color-scheme" content="dark light" />
    @include('partials.theme-init')
    <title>@yield('code') · @yield('title') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/brand/favicon-32.png') }}" />
    <link rel="stylesheet" href="{{ asset('css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/components.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
  </head>
  <body class="admin admin--auth">
    <main class="auth-card error-card">
      <a class="auth-brand" href="{{ url('/') }}">
        <img src="{{ asset('assets/brand/rakiza-mark.png') }}" alt="" width="52" height="52" />
        <span>{{ config('app.name') }}</span>
      </a>
      <p class="error-card__code mono">@yield('code')</p>
      <h1 class="auth-title">@yield('title')</h1>
      <p class="auth-intro">@yield('message')</p>
      <div class="form-row">
        <a class="btn btn--primary" href="{{ url('/') }}">Go to the home page</a>
        <a class="btn btn--ghost" href="javascript:history.back()">Go back</a>
      </div>
    </main>
  </body>
</html>
