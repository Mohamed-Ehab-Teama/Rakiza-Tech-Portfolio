      <section class="services section" id="services" aria-labelledby="services-title">
        <span class="ghost-index services__ghost" aria-hidden="true">02</span>

        <div class="container">
          <header class="section-head">
            <span class="section-label"><span class="idx">02</span> SERVICES</span>
            <h2 class="section-title" id="services-title" data-reveal="lines">What we build, end to end.</h2>
            <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">
              From the first line of code to the database under it and the pipeline that ships it —
              five disciplines we bring to every product.
            </p>
          </header>

          <div class="services__grid" data-reveal-group data-reveal-stagger="0.08" role="list">

            <!-- 01 CODE (wide) -->
            <article class="card card--spotlight services__card services__card--wide" role="listitem">
              <div class="services__head">
                <span class="services__num mono">01</span>
                <span class="services__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                       stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="8 7 3 12 8 17"></polyline>
                    <polyline points="16 7 21 12 16 17"></polyline>
                    <line x1="13.5" y1="5" x2="10.5" y2="19"></line>
                  </svg>
                </span>
              </div>
              <h3 class="services__title">Code</h3>
              <p class="services__desc">Clean, tested, maintainable code — typed, reviewed and built to outlive the sprint it shipped in.</p>

              <div class="services__motif services__motif--code" aria-hidden="true">
                <pre class="services__code mono"><code><span class="services__line"><span class="tok-com">// ship with confidence</span></span>
      <span class="services__line"><span class="tok-key">export const</span> <span class="tok-fn">build</span> <span class="tok-punc">=</span> <span class="tok-key">async</span> <span class="tok-punc">(</span>src<span class="tok-punc">)</span> <span class="tok-punc">=&gt;</span> <span class="tok-punc">{</span></span>
      <span class="services__line">  <span class="tok-key">const</span> out <span class="tok-punc">=</span> <span class="tok-key">await</span> <span class="tok-fn">compile</span><span class="tok-punc">(</span>src<span class="tok-punc">)</span><span class="tok-punc">;</span></span>
      <span class="services__line">  <span class="tok-fn">assert</span><span class="tok-punc">(</span>out.tests <span class="tok-punc">===</span> <span class="tok-str">"pass"</span><span class="tok-punc">)</span><span class="tok-punc">;</span></span>
      <span class="services__line">  <span class="tok-key">return</span> out.<span class="tok-fn">deploy</span><span class="tok-punc">(</span><span class="tok-num">2</span><span class="tok-punc">)</span><span class="tok-punc">;</span></span>
      <span class="services__line"><span class="tok-punc">}</span><span class="services__cursor"></span></span></code></pre>
              </div>

              <ul class="services__tags" aria-label="Stack">
                <li class="tag">TypeScript</li>
                <li class="tag">React</li>
                <li class="tag">Node</li>
                <li class="tag">Laravel</li>
              </ul>
            </article>

            <!-- 02 APPS -->
            <article class="card card--spotlight services__card" role="listitem">
              <div class="services__head">
                <span class="services__num mono">02</span>
                <span class="services__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                       stroke-linecap="round" stroke-linejoin="round">
                    <rect x="6" y="2.5" width="12" height="19" rx="2.5"></rect>
                    <line x1="10" y1="18.5" x2="14" y2="18.5"></line>
                  </svg>
                </span>
              </div>
              <h3 class="services__title">Apps</h3>
              <p class="services__desc">Web &amp; product apps, built end to end — from auth to the final pixel.</p>

              <div class="services__motif services__motif--app" aria-hidden="true">
                <div class="services__phone">
                  <span class="services__phone-notch"></span>
                  <div class="services__phone-screen">
                    <span class="services__row services__row--bar"></span>
                    <span class="services__row services__row--w70"></span>
                    <span class="services__row services__row--w90"></span>
                    <span class="services__row services__row--w50"></span>
                    <span class="services__row services__row--w80"></span>
                  </div>
                </div>
              </div>

              <ul class="services__tags" aria-label="Stack">
                <li class="tag">SPA</li>
                <li class="tag">PWA</li>
                <li class="tag">REST</li>
                <li class="tag">Auth</li>
              </ul>
            </article>

            <!-- 03 DASHBOARDS -->
            <article class="card card--spotlight services__card" role="listitem">
              <div class="services__head">
                <span class="services__num mono">03</span>
                <span class="services__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                       stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="21" x2="21" y2="21"></line>
                    <rect x="5" y="12" width="3" height="6"></rect>
                    <rect x="10.5" y="8" width="3" height="10"></rect>
                    <rect x="16" y="4" width="3" height="14"></rect>
                  </svg>
                </span>
              </div>
              <h3 class="services__title">Dashboards</h3>
              <p class="services__desc">Analytics &amp; admin dashboards that turn raw data into decisions.</p>

              <div class="services__motif services__motif--chart" aria-hidden="true">
                <div class="services__chart">
                  <span class="services__bar" style="--h:42%"></span>
                  <span class="services__bar" style="--h:66%"></span>
                  <span class="services__bar" style="--h:38%"></span>
                  <span class="services__bar" style="--h:82%"></span>
                  <span class="services__bar" style="--h:55%"></span>
                  <span class="services__bar" style="--h:94%"></span>
                  <span class="services__bar" style="--h:48%"></span>
                </div>
              </div>

              <ul class="services__tags" aria-label="Stack">
                <li class="tag">Charts</li>
                <li class="tag">Realtime</li>
                <li class="tag">Reports</li>
              </ul>
            </article>

            <!-- 04 DATABASES -->
            <article class="card card--spotlight services__card" role="listitem">
              <div class="services__head">
                <span class="services__num mono">04</span>
                <span class="services__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                       stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="5.5" rx="7.5" ry="3"></ellipse>
                    <path d="M4.5 5.5v6c0 1.66 3.36 3 7.5 3s7.5-1.34 7.5-3v-6"></path>
                    <path d="M4.5 11.5v6c0 1.66 3.36 3 7.5 3s7.5-1.34 7.5-3v-6"></path>
                  </svg>
                </span>
              </div>
              <h3 class="services__title">Databases</h3>
              <p class="services__desc">Modeling, queries &amp; performance — schemas that scale and stay fast.</p>

              <div class="services__motif services__motif--db" aria-hidden="true">
                <div class="services__db">
                  <span class="services__db-row"><i class="services__db-key"></i><i class="services__db-cell"></i></span>
                  <span class="services__db-row"><i class="services__db-key"></i><i class="services__db-cell"></i></span>
                  <span class="services__db-row"><i class="services__db-key"></i><i class="services__db-cell"></i></span>
                  <span class="services__db-row"><i class="services__db-key"></i><i class="services__db-cell"></i></span>
                  <span class="services__db-pulse"></span>
                </div>
              </div>

              <ul class="services__tags" aria-label="Stack">
                <li class="tag">PostgreSQL</li>
                <li class="tag">MySQL</li>
                <li class="tag">Redis</li>
                <li class="tag">ORM</li>
              </ul>
            </article>

            <!-- 05 CLI & AUTOMATION -->
            <article class="card card--spotlight services__card" role="listitem">
              <div class="services__head">
                <span class="services__num mono">05</span>
                <span class="services__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                       stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2.5" y="4" width="19" height="16" rx="2.5"></rect>
                    <polyline points="7 9 10 12 7 15"></polyline>
                    <line x1="12.5" y1="15" x2="16.5" y2="15"></line>
                  </svg>
                </span>
              </div>
              <h3 class="services__title">CLI &amp; Automation</h3>
              <p class="services__desc">Tooling, scripts &amp; CI/CD — repeatable pipelines that do the boring work.</p>

              <div class="services__motif services__motif--cli" aria-hidden="true">
                <div class="services__term">
                  <span class="services__term-line"><span class="term-prompt">$</span> <span class="term-out">ci run --deploy</span></span>
                  <span class="services__term-line"><span class="term-out">build</span> <span class="term-ok">ok</span></span>
                  <span class="services__term-line"><span class="term-out">tests</span> <span class="term-ok">142 passed</span></span>
                  <span class="services__term-line"><span class="term-prompt">$</span> <span class="caret"></span></span>
                </div>
              </div>

              <ul class="services__tags" aria-label="Stack">
                <li class="tag">Bash</li>
                <li class="tag">Node</li>
                <li class="tag">CI/CD</li>
                <li class="tag">Cron</li>
              </ul>
            </article>

          </div>
        </div>
      </section>
