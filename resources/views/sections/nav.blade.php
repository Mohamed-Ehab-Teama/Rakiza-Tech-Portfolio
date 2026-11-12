@php
    // On the home page the links jump to sections (#about); on other pages they
    // go back to the home page first (/#about).
    $onHome = request()->routeIs('home');
    $base = $onHome ? '' : route('home');
    $links = [['hero', 'Home'], ['about', 'About'], ['services', 'Services'], ['work', 'Work'], ['stack', 'Stack'], ['contact', 'Contact']];
    $brandWords = array_values(array_filter([setting('site_name'), setting('site_name_ar')]));
@endphp
    <header class="nav" id="nav">
      <div class="nav__inner container container--wide">
        <a href="{{ $base }}#hero" class="nav__logo mono" aria-label="{{ setting('site_name') }} — back to top">
          <img class="nav__mark" src="{{ asset('assets/brand/rakiza-mark-96.png') }}" alt="" width="34" height="34" />
          <span class="brand-type"><span class="nav__brand" data-brand-type data-brand-words="{{ json_encode($brandWords, JSON_UNESCAPED_UNICODE) }}">{{ setting('site_name') }}</span><span class="nav__caret" aria-hidden="true">_</span></span>
        </a>

        <nav class="nav__links" aria-label="Primary">
          @foreach ($links as $i => [$id, $label])
            <a href="{{ $base }}#{{ $id }}" class="nav__link{{ $onHome && $i === 0 ? ' is-active' : '' }}"><span class="nav__idx mono">{{ sprintf('%02d', $i) }}</span> {{ $label }}</a>
          @endforeach
        </nav>

        <div class="nav__actions">
          @include('partials.theme-switch')
          <a href="{{ $base }}#contact" class="btn btn--primary nav__cta" data-magnetic="0.3">Let's talk</a>
          <button
            type="button"
            class="nav__burger"
            aria-label="Open menu"
            aria-expanded="false"
            aria-controls="nav-menu"
          >
            <span class="nav__burger-lines" aria-hidden="true">
              <span></span><span></span><span></span>
            </span>
          </button>
        </div>
      </div>

      <div class="nav__menu" id="nav-menu" role="dialog" aria-modal="true" aria-label="Site menu" hidden>
        <div class="nav__menu-inner container">
          <span class="section-label nav__menu-label"><span class="idx">//</span> Navigation</span>

          <nav class="nav__menu-links" aria-label="Mobile">
            @foreach ($links as $i => [$id, $label])
              <a href="{{ $base }}#{{ $id }}" class="nav__menu-link"><span class="nav__idx mono">{{ sprintf('%02d', $i) }}</span><span class="nav__menu-text">{{ $label }}</span></a>
            @endforeach
          </nav>

          <div class="nav__menu-foot">
            @if (setting('contact_email'))
              <a href="mailto:{{ setting('contact_email') }}" class="nav__menu-email mono link-underline">{{ setting('contact_email') }}</a>
            @endif
            @if ($socials = social_links())
              <ul class="nav__menu-socials" aria-label="Social links">
                @foreach ($socials as $social)
                  <li><a href="{{ $social['url'] }}" class="nav__menu-social mono link-underline" target="_blank" rel="noopener noreferrer">{{ $social['label'] }}</a></li>
                @endforeach
              </ul>
            @endif
            @if (setting('location'))
              <span class="nav__menu-meta mono">{{ setting('location') }}</span>
            @endif
          </div>
        </div>
      </div>
    </header>
