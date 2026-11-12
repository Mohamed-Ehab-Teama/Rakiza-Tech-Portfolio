    <header class="nav" id="nav">
      <div class="nav__inner container container--wide">
        <a href="#hero" class="nav__logo mono" aria-label="Rakiza Tech — back to top">
          <img class="nav__mark" src="{{ asset('assets/brand/rakiza-mark.png') }}" alt="" width="34" height="34" />
          <span class="brand-type"><span class="nav__brand" data-brand-type>Rakiza Tech</span><span class="nav__caret" aria-hidden="true">_</span></span>
        </a>

        <nav class="nav__links" aria-label="Primary">
          <a href="#hero" class="nav__link is-active"><span class="nav__idx mono">00</span> Home</a>
          <a href="#about" class="nav__link"><span class="nav__idx mono">01</span> About</a>
          <a href="#services" class="nav__link"><span class="nav__idx mono">02</span> Services</a>
          <a href="#work" class="nav__link"><span class="nav__idx mono">03</span> Work</a>
          <a href="#stack" class="nav__link"><span class="nav__idx mono">04</span> Stack</a>
          <a href="#contact" class="nav__link"><span class="nav__idx mono">05</span> Contact</a>
        </nav>

        <div class="nav__actions">
          @include('partials.theme-switch')
          <a href="#contact" class="btn btn--primary nav__cta" data-magnetic="0.3">Let's talk</a>
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
            <a href="#hero" class="nav__menu-link"><span class="nav__idx mono">00</span><span class="nav__menu-text">Home</span></a>
            <a href="#about" class="nav__menu-link"><span class="nav__idx mono">01</span><span class="nav__menu-text">About</span></a>
            <a href="#services" class="nav__menu-link"><span class="nav__idx mono">02</span><span class="nav__menu-text">Services</span></a>
            <a href="#work" class="nav__menu-link"><span class="nav__idx mono">03</span><span class="nav__menu-text">Work</span></a>
            <a href="#stack" class="nav__menu-link"><span class="nav__idx mono">04</span><span class="nav__menu-text">Stack</span></a>
            <a href="#contact" class="nav__menu-link"><span class="nav__idx mono">05</span><span class="nav__menu-text">Contact</span></a>
          </nav>

          <div class="nav__menu-foot">
            <a href="mailto:mohamed111.me111@gmail.com" class="nav__menu-email mono link-underline">mohamed111.me111@gmail.com</a>
            <ul class="nav__menu-socials" aria-label="Social links">
              <li><a href="https://github.com/Mohamed-Ehab-Teama" class="nav__menu-social mono link-underline" target="_blank" rel="noopener noreferrer">GitHub</a></li>
              <li><a href="https://www.linkedin.com/in/mohamed-ehab-teama-267154250/" class="nav__menu-social mono link-underline" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
            </ul>
            <span class="nav__menu-meta mono">Cairo / Menofia, Egypt</span>
          </div>
        </div>
      </div>
    </header>
