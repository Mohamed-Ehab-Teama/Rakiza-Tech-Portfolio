      <section class="websites section" id="websites" aria-labelledby="websites-title">
        <span class="ghost-index websites__ghost" aria-hidden="true">www</span>

        <div class="container">
          <header class="section-head">
            <span class="section-label"><span class="idx">//</span> WEBSITES</span>
            <h2 class="section-title" id="websites-title" data-reveal="lines">{{ setting('websites_title') }}</h2>
            @if (setting('websites_intro'))
              <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ setting('websites_intro') }}</p>
            @endif
          </header>

          <div class="websites__grid" data-reveal-group data-reveal-stagger="0.08" role="list">
            @foreach ($websites as $website)
              <article class="card card--spotlight websites__card" role="listitem">
                {{-- A small browser window: address bar + screenshot (or the site's initial) --}}
                <div class="websites__browser" aria-hidden="true">
                  <div class="websites__bar">
                    <span class="term-dots">
                      <span class="term-dot term-dot--r"></span>
                      <span class="term-dot term-dot--y"></span>
                      <span class="term-dot term-dot--g"></span>
                    </span>
                    <span class="websites__address mono">{{ $website->display_url }}</span>
                  </div>
                  <div class="websites__screen">
                    @if ($website->screenshot_url)
                      <img src="{{ $website->screenshot_url }}" alt="" loading="lazy" decoding="async" width="640" height="400" />
                    @else
                      <span class="websites__initial display">{{ mb_substr($website->name, 0, 1) }}</span>
                    @endif
                  </div>
                </div>

                <div class="websites__body">
                  <div class="websites__meta mono">
                    <span>{{ $website->category ?: 'Website' }}</span>
                    <span class="websites__status websites__status--{{ $website->status }}">{{ $website->status_label }}</span>
                  </div>
                  <h3 class="websites__name">{{ $website->name }}</h3>
                  @if ($website->description)
                    <p class="websites__desc">{{ $website->description }}</p>
                  @endif
                  <a class="link-underline websites__link mono" href="{{ $website->url }}" target="_blank" rel="noopener noreferrer">
                    Visit site <span aria-hidden="true">&#8599;</span><span class="sr-only"> {{ $website->name }} (opens in a new tab)</span>
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>
