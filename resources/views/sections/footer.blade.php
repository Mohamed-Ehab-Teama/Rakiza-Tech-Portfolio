    <footer class="footer" id="footer" aria-labelledby="footer-cta">
      <span class="ghost-index footer__ghost" aria-hidden="true">Rakiza</span>

      <div class="container footer__inner">

        <!-- Top band: huge CTA -->
        <div class="footer__band">
          <p class="footer__kicker eyebrow" data-reveal="up">Have a project in mind?</p>
          <a href="#contact" class="footer__cta gradient-text" id="footer-cta"
             data-magnetic="0.2" data-reveal="lines">Let&rsquo;s talk &rarr;</a>
          <p class="footer__band-sub" data-reveal="up" data-reveal-delay="0.12">
            Taking on new websites, systems and apps &mdash; usually replying within a day.
          </p>
        </div>

        <!-- Columns -->
        <div class="footer__cols" data-reveal-group data-reveal-stagger="0.08">

          <div class="footer__brand-col">
            <a href="#hero" class="footer__logo mono" aria-label="Rakiza Tech — back to top">
              <img class="footer__mark" src="{{ asset('assets/brand/rakiza-mark.png') }}" alt="" width="48" height="48" loading="lazy" />
              <span class="brand-type"><span class="footer__brand" data-brand-type>Rakiza Tech</span><span class="footer__caret" aria-hidden="true">_</span></span>
            </a>
            <p class="footer__tagline">
              A digital solutions company building websites, systems and apps
              &mdash; and the solid foundations behind the screens.
            </p>
            <span class="status footer__status">
              <span class="dot" aria-hidden="true"></span>
              Available for new projects
            </span>
          </div>

          <nav class="footer__col" aria-label="Sitemap">
            <h2 class="footer__col-title">Sitemap</h2>
            <ul class="footer__links">
              <li><a class="footer__link link-underline" href="#hero">Home</a></li>
              <li><a class="footer__link link-underline" href="#about">About</a></li>
              <li><a class="footer__link link-underline" href="#services">Services</a></li>
              <li><a class="footer__link link-underline" href="#work">Work</a></li>
              <li><a class="footer__link link-underline" href="#stack">Stack</a></li>
              <li><a class="footer__link link-underline" href="#contact">Contact</a></li>
            </ul>
          </nav>

          <nav class="footer__col" aria-label="Social">
            <h2 class="footer__col-title">Social</h2>
            <ul class="footer__links">
              <li><a class="footer__link link-underline" href="https://github.com/Mohamed-Ehab-Teama" target="_blank" rel="noopener noreferrer">GitHub <span class="footer__arrow" aria-hidden="true">&#8599;</span></a></li>
              <li><a class="footer__link link-underline" href="https://www.linkedin.com/in/mohamed-ehab-teama-267154250/" target="_blank" rel="noopener noreferrer">LinkedIn <span class="footer__arrow" aria-hidden="true">&#8599;</span></a></li>
            </ul>
          </nav>

          <div class="footer__col">
            <h2 class="footer__col-title">Contact</h2>
            <ul class="footer__links">
              <li>
                <a class="footer__link footer__link--email link-underline"
                   href="mailto:mohamed111.me111@gmail.com">mohamed111.me111@gmail.com</a>
              </li>
              <li><a class="footer__link link-underline" href="tel:+201090166589">+20 109 016 6589</a></li>
              <li class="footer__meta mono">Cairo &middot; Menofia, Egypt</li>
            </ul>
          </div>

        </div>

        <!-- Bottom bar -->
        <div class="footer__bottom mono">
          {{-- date('Y') is PHP: the server prints the current year, so this never goes stale. --}}
          <p class="footer__copy">&copy; {{ date('Y') }} Rakiza Tech &mdash; Built with Laravel, HTML, CSS &amp; JS</p>

          <p class="footer__time" aria-label="Local time">
            <span class="footer__time-dot" aria-hidden="true">&#9679;</span>
            <span class="footer__time-label">local time</span>
            <span id="footer-clock" class="tnum footer__clock" role="timer" aria-live="off">--:--:--</span>
          </p>

          <a href="#hero" class="footer__top link-underline" data-magnetic="0.2">
            Back to top <span class="footer__top-arrow" aria-hidden="true">&uarr;</span>
          </a>
        </div>

      </div>
    </footer>
