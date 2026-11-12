{{--
    admin/layouts/app.blade.php — the dashboard shell: sidebar, top bar, flash
    messages. Pages extend it and fill @section('title') and @section('content').
    Sidebar links only appear when the user has the matching "….view" permission.
--}}
@php
    $defaultTheme = setting('default_theme') ?: config('themes.default');
    $user = auth()->user();
    $nav = [
        'Overview' => [
            ['Dashboard', 'admin.dashboard', 'dashboard', null, 'admin.dashboard'],
        ],
        'Content' => [
            ['Projects', 'admin.projects.index', 'folder', 'projects.view', 'admin.projects.*'],
            ['Websites', 'admin.websites.index', 'globe', 'websites.view', 'admin.websites.*'],
            ['Services', 'admin.services.index', 'layers', 'services.view', 'admin.services.*'],
            ['Skill categories', 'admin.skill_categories.index', 'cpu', 'skills.view', 'admin.skill_categories.*'],
            ['Skills', 'admin.skills.index', 'cpu', 'skills.view', 'admin.skills.*'],
            ['Numbers', 'admin.metrics.index', 'chart', 'metrics.view', 'admin.metrics.*'],
        ],
        'Inbox' => [
            ['Messages', 'admin.messages.index', 'mail', 'messages.view', 'admin.messages.*'],
        ],
        'Administration' => [
            ['Users', 'admin.users.index', 'users', 'users.view', 'admin.users.*'],
            ['Roles', 'admin.roles.index', 'shield', 'roles.view', 'admin.roles.*'],
            ['Settings', 'admin.settings.edit', 'settings', 'settings.view', 'admin.settings.*'],
            ['Activity', 'admin.activity.index', 'activity', 'activity.view', 'admin.activity.*'],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $defaultTheme === 'system' ? 'dark' : $defaultTheme }}" data-theme-default="{{ $defaultTheme }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="color-scheme" content="dark light" />
    <meta name="theme-color" content="#08090d" />
    @include('partials.theme-init')
    <title>@yield('title') — {{ setting('site_name') }} admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/brand/favicon-32.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&family=IBM+Plex+Sans+Arabic:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/components.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
  </head>
  <body class="admin">
    <a class="sr-only" href="#admin-main">Skip to content</a>

    <div class="admin-shell">
      <aside class="admin-sidebar" id="admin-sidebar" aria-label="Dashboard navigation">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
          <img src="{{ asset('assets/brand/rakiza-mark-96.png') }}" alt="" width="36" height="36" />
          <span class="admin-brand__text">
            <span class="admin-brand__name">{{ setting('site_name') }}</span>
            <span class="admin-brand__sub mono">dashboard</span>
          </span>
        </a>

        <nav class="admin-nav">
          @foreach ($nav as $group => $items)
            @php($visible = array_filter($items, fn ($item) => $item[3] === null || $user->can($item[3])))
            @if ($visible)
              <p class="admin-nav__group mono">{{ $group }}</p>
              <ul>
                @foreach ($visible as [$label, $route, $icon, $permission, $pattern])
                  @php($active = request()->routeIs($pattern))
                  <li>
                    <a href="{{ route($route) }}" class="admin-nav__link{{ $active ? ' is-active' : '' }}" @if ($active) aria-current="page" @endif>
                      @include('admin.partials.icon', ['name' => $icon])
                      <span>{{ $label }}</span>
                      @if ($route === 'admin.messages.index' && ($unreadMessages ?? 0) > 0)
                        <span class="admin-count" aria-label="{{ $unreadMessages }} unread">{{ $unreadMessages }}</span>
                      @endif
                    </a>
                  </li>
                @endforeach
              </ul>
            @endif
          @endforeach
        </nav>

        <div class="admin-sidebar__foot">
          <a class="admin-nav__link{{ request()->routeIs('admin.help') ? ' is-active' : '' }}" href="{{ route('admin.help') }}">
            @include('admin.partials.icon', ['name' => 'help'])<span>Help &amp; guide</span>
          </a>
          <a class="admin-nav__link" href="{{ route('home') }}" target="_blank" rel="noopener">
            @include('admin.partials.icon', ['name' => 'external'])<span>View website</span>
          </a>
        </div>
      </aside>
      <div class="admin-overlay" data-sidebar-close hidden></div>

      <div class="admin-main">
        <header class="admin-topbar">
          <button type="button" class="admin-iconbtn admin-topbar__menu" data-sidebar-toggle aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open navigation">
            @include('admin.partials.icon', ['name' => 'menu'])
          </button>
          <p class="admin-topbar__title">@yield('title')</p>

          <div class="admin-topbar__actions">
            @include('partials.theme-switch')

            <details class="admin-user">
              <summary class="admin-user__toggle" aria-label="Account menu">
                <span class="admin-avatar" aria-hidden="true">{{ $user->initials() }}</span>
                <span class="admin-user__name">{{ $user->name }}</span>
              </summary>
              <div class="admin-user__menu">
                <p class="admin-user__meta">
                  <span>{{ $user->email }}</span>
                  <span class="mono">{{ $user->getRoleNames()->implode(', ') ?: 'No role' }}</span>
                </p>
                <a href="{{ route('admin.profile.edit') }}">@include('admin.partials.icon', ['name' => 'user']) My profile</a>
                <a href="{{ route('admin.help') }}">@include('admin.partials.icon', ['name' => 'help']) Help &amp; guide</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                  @csrf
                  <button type="submit">@include('admin.partials.icon', ['name' => 'logout']) Sign out</button>
                </form>
              </div>
            </details>
          </div>
        </header>

        <main class="admin-content" id="admin-main" tabindex="-1">
          @include('admin.partials.flash')
          @yield('content')
        </main>
      </div>
    </div>

    <div class="admin-toast" role="status" aria-live="polite" hidden></div>

    <script type="module" src="{{ asset('js/admin/admin.js') }}"></script>
  </body>
</html>
