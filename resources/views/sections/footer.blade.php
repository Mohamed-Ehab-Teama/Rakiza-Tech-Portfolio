@php
    $onHome = request()->routeIs('home');
    $base = $onHome ? '' : route('home');
    $brandWords = array_values(array_filter([setting('site_name'), setting('site_name_ar')]));
@endphp
    <footer class="footer" id="footer" aria-labelledby="footer-cta">
      <span class="ghost-index footer__ghost" aria-hidden="true">{{ Str::before(setting('site_name'), ' ') }}</span>

      <div class="container footer__inner">

        <!-- Top band: huge CTA -->
        <div class="footer__band">
          <p class="footer__kicker eyebrow" data-reveal="up">Have a project in mind?</p>
          <a href="{{ $base }}#contact" class="footer__cta gradient-text" id="footer-cta"
             data-magnetic="0.2" data-reveal="lines">Let&rsquo;s talk &rarr;</a>
          @if (setting('footer_note'))
            <p class="footer__band-sub" data-reveal="up" data-reveal-delay="0.12">{{ setting('footer_note') }}</p>
          @endif
        </div>

        <!-- Columns -->
        <div class="footer__cols" data-reveal-group data-reveal-stagger="0.08">

          <div class="footer__brand-col">
            <a href="{{ $base }}#hero" class="footer__logo mono" aria-label="{{ setting('site_name') }} — back to top">
              <img class="footer__mark" src="{{ asset('assets/brand/rakiza-mark.png') }}" alt="" width="48" height="48" loading="lazy" />
              <span class="brand-type"><span class="footer__brand" data-brand-type data-brand-words="{{ json_encode($brandWords, JSON_UNESCAPED_UNICODE) }}">{{ setting('site_name') }}</span><span class="footer__caret" aria-hidden="true">_</span></span>
            </a>
            @if (setting('footer_tagline'))
              <p class="footer__tagline">{{ setting('footer_tagline') }}</p>
            @endif
            @if (setting('availability'))
              <span class="status footer__status">
                <span class="dot" aria-hidden="true"></span>
                {{ setting('availability') }}
              </span>
            @endif
          </div>

          <nav class="footer__col" aria-label="Sitemap">
            <h2 class="footer__col-title">Sitemap</h2>
            <ul class="footer__links">
              <li><a class="footer__link link-underline" href="{{ $base }}#hero">Home</a></li>
              <li><a class="footer__link link-underline" href="{{ $base }}#about">About</a></li>
              <li><a class="footer__link link-underline" href="{{ $base }}#services">Services</a></li>
              <li><a class="footer__link link-underline" href="{{ route('projects.index') }}">Projects</a></li>
              <li><a class="footer__link link-underline" href="{{ $base }}#stack">Stack</a></li>
              <li><a class="footer__link link-underline" href="{{ $base }}#contact">Contact</a></li>
            </ul>
          </nav>

          @if ($socials = social_links())
            <nav class="footer__col" aria-label="Social">
              <h2 class="footer__col-title">Social</h2>
              <ul class="footer__links">
                @foreach ($socials as $social)
                  <li><a class="footer__link link-underline" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['label'] }} <span class="footer__arrow" aria-hidden="true">&#8599;</span></a></li>
                @endforeach
              </ul>
            </nav>
          @endif

          <div class="footer__col">
            <h2 class="footer__col-title">Contact</h2>
            <ul class="footer__links">
              @if (setting('contact_email'))
                <li>
                  <a class="footer__link footer__link--email link-underline"
                     href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a>
                </li>
              @endif
              @if (setting('contact_phone'))
                <li><a class="footer__link link-underline" href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></li>
              @endif
              @if (setting('location'))
                <li class="footer__meta mono">{{ setting('location') }}</li>
              @endif
            </ul>
          </div>

        </div>

        <!-- Bottom bar -->
        <div class="footer__bottom mono">
          {{-- date('Y') is PHP: the server prints the current year, so this never goes stale. --}}
          <p class="footer__copy">&copy; {{ date('Y') }} {{ setting('site_name') }} &mdash; Built with Laravel, HTML, CSS &amp; JS</p>

          <p class="footer__time" aria-label="Local time">
            <span class="footer__time-dot" aria-hidden="true">&#9679;</span>
            <span class="footer__time-label">local time</span>
            <span id="footer-clock" class="tnum footer__clock" role="timer" aria-live="off">--:--:--</span>
          </p>

          <a href="{{ $base }}#hero" class="footer__top link-underline" data-magnetic="0.2">
            Back to top <span class="footer__top-arrow" aria-hidden="true">&uarr;</span>
          </a>
        </div>

      </div>
    </footer>
