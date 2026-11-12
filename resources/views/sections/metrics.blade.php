      <section class="metrics section" id="metrics" aria-labelledby="metrics-title">
        <span class="ghost-index metrics__ghost" aria-hidden="true">//</span>

        <div class="container">
          <header class="section-head">
            <span class="section-label"><span class="idx">//</span> DASHBOARD</span>
            <h2 class="section-title" id="metrics-title" data-reveal="lines">By the numbers.</h2>
            <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">
              A live-feeling snapshot of the work — deploys shipped, uptime held, and the
              habits behind them. Real instrumentation, rendered like the dashboards we build.
            </p>
          </header>

          <div class="metrics__grid" data-reveal-group data-reveal-stagger="0.07">

            <!-- KPI 1 — Deploys -->
            <article class="card metrics__card metrics__kpi" aria-label="Deploys this year">
              <header class="metrics__kpi-head">
                <span class="metrics__kpi-label">Deploys</span>
                <span class="metrics__badge mono">YTD</span>
              </header>
              <div class="metrics__kpi-num display tnum">
                <span class="tnum" data-count="1240" data-count-suffix="">1,240</span>
              </div>
              <svg class="metrics__spark" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                <polyline class="metrics__spark-line" points="0,30 15,26 30,28 45,18 60,21 75,12 90,15 105,7 120,4"></polyline>
              </svg>
              <p class="metrics__delta metrics__delta--up mono">
                <span class="metrics__delta-arrow" aria-hidden="true">&#9650;</span> +12.4% this month
              </p>
            </article>

            <!-- KPI 2 — Uptime -->
            <article class="card metrics__card metrics__kpi" aria-label="Service uptime">
              <header class="metrics__kpi-head">
                <span class="metrics__kpi-label">Uptime</span>
                <span class="metrics__badge mono">30d</span>
              </header>
              <div class="metrics__kpi-num display tnum">
                <span class="tnum" data-count="99.98" data-count-decimals="2" data-count-suffix="%">99.98%</span>
              </div>
              <svg class="metrics__spark metrics__spark--term" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                <polyline class="metrics__spark-line" points="0,10 15,8 30,11 45,7 60,9 75,6 90,8 105,5 120,6"></polyline>
              </svg>
              <p class="metrics__delta metrics__delta--up mono">
                <span class="metrics__delta-arrow" aria-hidden="true">&#9650;</span> +0.03% this month
              </p>
            </article>

            <!-- KPI 3 — Projects -->
            <article class="card metrics__card metrics__kpi" aria-label="Projects shipped">
              <header class="metrics__kpi-head">
                <span class="metrics__kpi-label">Projects</span>
                <span class="metrics__badge mono">ALL</span>
              </header>
              <div class="metrics__kpi-num display tnum">
                <span class="tnum" data-count="80" data-count-suffix="+">80+</span>
              </div>
              <svg class="metrics__spark metrics__spark--violet" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                <polyline class="metrics__spark-line" points="0,32 15,29 30,24 45,25 60,18 75,16 90,17 105,10 120,8"></polyline>
              </svg>
              <p class="metrics__delta metrics__delta--up mono">
                <span class="metrics__delta-arrow" aria-hidden="true">&#9650;</span> +3 this month
              </p>
            </article>

            <!-- KPI 4 — Coffees -->
            <article class="card metrics__card metrics__kpi" aria-label="Coffees consumed">
              <header class="metrics__kpi-head">
                <span class="metrics__kpi-label">Coffees</span>
                <span class="metrics__badge mono">&#8734;</span>
              </header>
              <div class="metrics__kpi-num display tnum">
                <span class="tnum" data-count="9800" data-count-suffix="">9,800</span>
              </div>
              <svg class="metrics__spark metrics__spark--amber" viewBox="0 0 120 36" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                <polyline class="metrics__spark-line" points="0,20 15,14 30,22 45,16 60,24 75,15 90,21 105,13 120,18"></polyline>
              </svg>
              <p class="metrics__delta metrics__delta--up mono">
                <span class="metrics__delta-arrow" aria-hidden="true">&#9650;</span> +7.1% this month
              </p>
            </article>

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

            <!-- DONUTS — Frontend / Backend proficiency -->
            <article class="card metrics__card metrics__donuts" aria-label="Discipline proficiency">
              <header class="metrics__card-head">
                <h3 class="metrics__card-title">Proficiency</h3>
                <span class="metrics__badge mono">SKILL</span>
              </header>

              <div class="metrics__donuts-row">
                <figure class="metrics__donut" data-pct="92">
                  <svg class="metrics__donut-svg" viewBox="0 0 120 120" role="img" aria-label="Frontend 92 percent">
                    <circle class="metrics__donut-track" cx="60" cy="60" r="52"></circle>
                    <circle class="metrics__donut-ring metrics__donut-ring--accent" cx="60" cy="60" r="52"></circle>
                  </svg>
                  <figcaption class="metrics__donut-cap">
                    <span class="metrics__donut-pct tnum" data-count="92" data-count-suffix="%">92%</span>
                    <span class="metrics__donut-name">Frontend</span>
                  </figcaption>
                </figure>

                <figure class="metrics__donut" data-pct="88">
                  <svg class="metrics__donut-svg" viewBox="0 0 120 120" role="img" aria-label="Backend 88 percent">
                    <circle class="metrics__donut-track" cx="60" cy="60" r="52"></circle>
                    <circle class="metrics__donut-ring metrics__donut-ring--violet" cx="60" cy="60" r="52"></circle>
                  </svg>
                  <figcaption class="metrics__donut-cap">
                    <span class="metrics__donut-pct tnum" data-count="88" data-count-suffix="%">88%</span>
                    <span class="metrics__donut-name">Backend</span>
                  </figcaption>
                </figure>
              </div>
            </article>

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
