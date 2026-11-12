      <section class="metrics section" id="metrics" aria-labelledby="metrics-title">
        <span class="ghost-index metrics__ghost" aria-hidden="true">//</span>

        <div class="container">
          <header class="section-head">
            <span class="section-label"><span class="idx">//</span> DASHBOARD</span>
            <h2 class="section-title" id="metrics-title" data-reveal="lines">{{ setting('metrics_title') }}</h2>
            @if (setting('metrics_intro'))
              <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ setting('metrics_intro') }}</p>
            @endif
          </header>

          <div class="metrics__grid" data-reveal-group data-reveal-stagger="0.07">

            {{-- KPI cards: Dashboard → Numbers (group "Dashboard cards") --}}
            @php($sparks = ["0,30 15,26 30,28 45,18 60,21 75,12 90,15 105,7 120,4", "0,10 15,8 30,11 45,7 60,9 75,6 90,8 105,5 120,6", "0,32 15,29 30,24 45,25 60,18 75,16 90,17 105,10 120,8", "0,20 15,14 30,22 45,16 60,24 75,15 90,21 105,13 120,18"])
            @php($sparkClass = ["accent" => "", "term" => " metrics__spark--term", "violet" => " metrics__spark--violet", "amber" => " metrics__spark--amber"])
            @foreach ($kpis as $kpi)
              <article class="card metrics__card metrics__kpi" aria-label="{{ $kpi->label }}">
                <header class="metrics__kpi-head">
                  <span class="metrics__kpi-label">{{ $kpi->label }}</span>
                  @if ($kpi->badge)
                    <span class="metrics__badge mono">{{ $kpi->badge }}</span>
                  @endif
                </header>
                <div class="metrics__kpi-num display tnum">
                  <span class="tnum" data-count="{{ $kpi->value }}" data-count-decimals="{{ $kpi->decimals }}" data-count-prefix="{{ $kpi->prefix }}" data-count-suffix="{{ $kpi->suffix }}">{{ $kpi->formatted }}</span>
                </div>
                <svg class="metrics__spark{{ $sparkClass[$kpi->color] ?? "" }}" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                  <polyline class="metrics__spark-line" points="{{ $sparks[$loop->index % 4] }}"></polyline>
                </svg>
                @if ($kpi->note)
                  <p class="metrics__delta metrics__delta--{{ $kpi->trend === "down" ? "down" : "up" }} mono">
                    @if ($kpi->trend !== "flat")
                      <span class="metrics__delta-arrow" aria-hidden="true">{!! $kpi->trend === "down" ? "&#9660;" : "&#9650;" !!}</span>
                    @endif
                    {{ $kpi->note }}
                  </p>
                @endif
              </article>
            @endforeach

            <!-- BAR CHART — Commits / month -->
            <article class="card metrics__card metrics__bars" aria-label="Commits per month, last twelve months">
              <header class="metrics__card-head">
                <h3 class="metrics__card-title">Commits / month</h3>
                <span class="metrics__badge mono">12M</span>
              </header>
              <div class="metrics__bars-plot" role="img"
                   aria-label="Bar chart of monthly commits trending upward over the last twelve months.">
                <ul class="metrics__bars-track" aria-hidden="true">
                  <li class="metrics__bar" style="--v:0.42"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">J</span></li>
                  <li class="metrics__bar" style="--v:0.55"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">F</span></li>
                  <li class="metrics__bar" style="--v:0.48"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">M</span></li>
                  <li class="metrics__bar" style="--v:0.68"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">A</span></li>
                  <li class="metrics__bar" style="--v:0.61"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">M</span></li>
                  <li class="metrics__bar" style="--v:0.80"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">J</span></li>
                  <li class="metrics__bar" style="--v:0.72"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">J</span></li>
                  <li class="metrics__bar" style="--v:0.88"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">A</span></li>
                  <li class="metrics__bar" style="--v:0.76"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">S</span></li>
                  <li class="metrics__bar metrics__bar--peak" style="--v:1"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">O</span></li>
                  <li class="metrics__bar" style="--v:0.84"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">N</span></li>
                  <li class="metrics__bar" style="--v:0.92"><span class="metrics__bar-fill"></span><span class="metrics__bar-x">D</span></li>
                </ul>
              </div>
              <footer class="metrics__card-foot mono">
                <span class="metrics__foot-key"><span class="metrics__swatch metrics__swatch--accent" aria-hidden="true"></span> commits</span>
                <span class="metrics__foot-val">peak 1,612 / Oct</span>
              </footer>
            </article>

            <!-- LINE / AREA CHART — Performance -->
            <article class="card metrics__card metrics__line" aria-label="Performance score over time">
              <header class="metrics__card-head">
                <h3 class="metrics__card-title">Performance</h3>
                <span class="metrics__badge mono">P95</span>
              </header>
              <div class="metrics__line-plot">
                <svg class="metrics__line-svg" viewBox="0 0 320 140" preserveAspectRatio="none" role="img"
                     aria-label="Area chart showing performance score climbing from the low nineties to ninety-nine.">
                  <defs>
                    <linearGradient id="metricsAreaFill" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" style="stop-color: rgba(var(--accent-rgb), 0.28)"></stop>
                      <stop offset="100%" style="stop-color: rgba(var(--accent-rgb), 0)"></stop>
                    </linearGradient>
                  </defs>
                  <!-- gridlines -->
                  <g class="metrics__line-grid" aria-hidden="true">
                    <line x1="0" y1="35" x2="320" y2="35"></line>
                    <line x1="0" y1="70" x2="320" y2="70"></line>
                    <line x1="0" y1="105" x2="320" y2="105"></line>
                  </g>
                  <!-- area fill (under the stroke) -->
                  <path class="metrics__line-area"
                        d="M0,104 L29,96 L58,99 L87,82 L116,86 L145,64 L174,70 L203,48 L232,52 L261,33 L290,38 L320,22 L320,140 L0,140 Z"
                        fill="url(#metricsAreaFill)"></path>
                  <!-- the line that draws in -->
                  <path class="metrics__line-stroke"
                        d="M0,104 L29,96 L58,99 L87,82 L116,86 L145,64 L174,70 L203,48 L232,52 L261,33 L290,38 L320,22"
                        fill="none"></path>
                </svg>
                <div class="metrics__line-ticks mono" aria-hidden="true">
                  <span>Jan</span><span>Apr</span><span>Jul</span><span>Oct</span>
                </div>
              </div>
              <footer class="metrics__card-foot mono">
                <span class="metrics__foot-key"><span class="metrics__swatch metrics__swatch--accent" aria-hidden="true"></span> Lighthouse</span>
                <span class="metrics__foot-val">now <span class="text-accent">99</span> / 100</span>
              </footer>
            </article>

            @if ($proficiency->isNotEmpty())
              <!-- DONUTS — average skill level of the first two stack categories -->
              <article class="card metrics__card metrics__donuts" aria-label="Discipline proficiency">
                <header class="metrics__card-head">
                  <h3 class="metrics__card-title">Proficiency</h3>
                  <span class="metrics__badge mono">SKILL</span>
                </header>

                <div class="metrics__donuts-row">
                  @foreach ($proficiency as $item)
                    <figure class="metrics__donut" data-pct="{{ $item['value'] }}">
                      <svg class="metrics__donut-svg" viewBox="0 0 120 120" role="img" aria-label="{{ $item['name'] }} {{ $item['value'] }} percent">
                        <circle class="metrics__donut-track" cx="60" cy="60" r="52"></circle>
                        <circle class="metrics__donut-ring metrics__donut-ring--{{ $loop->first ? 'accent' : 'violet' }}" cx="60" cy="60" r="52"></circle>
                      </svg>
                      <figcaption class="metrics__donut-cap">
                        <span class="metrics__donut-pct tnum" data-count="{{ $item['value'] }}" data-count-suffix="%">{{ $item['value'] }}%</span>
                        <span class="metrics__donut-name">{{ $item['name'] }}</span>
                      </figcaption>
                    </figure>
                  @endforeach
                </div>
              </article>
            @endif

            <!-- ACTIVITY LOG -->
            <article class="card metrics__card metrics__log" aria-label="Recent activity">
              <header class="metrics__card-head">
                <h3 class="metrics__card-title">Activity</h3>
                <span class="metrics__badge mono">
                  <span class="metrics__live-dot" aria-hidden="true"></span> LIVE
                </span>
              </header>
              <ul class="metrics__log-list mono">
                <li class="metrics__log-item">
                  <span class="metrics__log-time text-faint">09:42</span>
                  <span class="metrics__log-msg"><span class="text-term">deploy</span> &middot; api-gateway &rarr; prod</span>
                  <span class="metrics__log-ok" aria-label="success">&#10003;</span>
                </li>
                <li class="metrics__log-item">
                  <span class="metrics__log-time text-faint">09:31</span>
                  <span class="metrics__log-msg"><span class="text-accent">build</span> &middot; #4821 passed in 38s</span>
                  <span class="metrics__log-ok" aria-label="success">&#10003;</span>
                </li>
                <li class="metrics__log-item">
                  <span class="metrics__log-time text-faint">08:57</span>
                  <span class="metrics__log-msg"><span class="text-accent2">merge</span> &middot; feat/dashboard-bento</span>
                  <span class="metrics__log-ok" aria-label="success">&#10003;</span>
                </li>
                <li class="metrics__log-item">
                  <span class="metrics__log-time text-faint">08:12</span>
                  <span class="metrics__log-msg"><span class="text-term">test</span> &middot; 312 passed, 0 failed</span>
                  <span class="metrics__log-ok" aria-label="success">&#10003;</span>
                </li>
              </ul>
            </article>

          </div>
        </div>
      </section>
