      <section class="hero" id="hero" aria-label="Introduction">
        <canvas class="hero__canvas" aria-hidden="true"></canvas>

        <!-- Corner meta -->
        <div class="hero__meta hero__meta--tr mono" aria-hidden="true">
          <span class="hero__ver">v{{ date('Y') }}.1</span>
          <span class="hero__coords" id="hero-coords">lat 30.04 // lng 31.23</span>
        </div>

        <a class="hero__scroll mono" href="#about" aria-label="Scroll to next section">
          <span class="hero__scroll-label">scroll</span>
          <span class="hero__scroll-line" aria-hidden="true"></span>
        </a>

        <div class="container hero__inner">
          <div class="hero__intro flex items-center wrap gap-4">
            @if (setting('availability'))
              <span class="status"><span class="dot"></span> {{ setting('availability') }}</span>
            @endif
            @if (setting('hero_badge'))
              <span class="hero__id mono">{{ setting('hero_badge') }}</span>
            @endif
          </div>

          <h1 class="hero__title">{{ setting('hero_title') }}@if (setting('hero_title_highlight'))<br><span class="gradient-text">{{ setting('hero_title_highlight') }}</span>@endif</h1>

          <p class="hero__lead fs-lead">{{ setting('hero_lead') }}</p>

          {{-- hero.js types these phrases one after another --}}
          <div class="hero__typed mono">
            <span class="term-prompt">~ $</span>
            <span id="hero-type" data-phrases="{{ json_encode(setting('hero_phrases'), JSON_UNESCAPED_UNICODE) }}"></span><span class="caret"></span>
          </div>

          <div class="hero__cta flex wrap gap-4">
            @if ($projects->isNotEmpty())
              <a href="#work" class="btn btn--primary" data-magnetic="0.3">
                View work <span class="arrow" aria-hidden="true">&rarr;</span>
              </a>
              <a href="#contact" class="btn btn--ghost" data-magnetic="0.3">Get in touch</a>
            @else
              <a href="#contact" class="btn btn--primary" data-magnetic="0.3">
                Get in touch <span class="arrow" aria-hidden="true">&rarr;</span>
              </a>
            @endif
          </div>

          @if ($highlights->isNotEmpty())
            <ul class="hero__stats mono" aria-label="At a glance">
              @foreach ($highlights as $metric)
                <li class="hero__stat"><span class="hero__stat-n">{{ $metric->formatted }}</span><span class="hero__stat-l">{{ $metric->short_label ?: $metric->label }}</span></li>
              @endforeach
            </ul>
          @endif
        </div>
      </section>
