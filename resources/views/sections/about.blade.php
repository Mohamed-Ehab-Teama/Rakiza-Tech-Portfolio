      <section class="about section section--alt" id="about" aria-labelledby="about-title">
        <span class="ghost-index about__ghost" aria-hidden="true">01</span>

        <div class="container about__grid">
          <!-- LEFT — section head + bio -->
          <div class="about__intro">
            <div class="section-head">
              <span class="section-label"><span class="idx">01</span> ABOUT</span>
              <h2 class="section-title about__title" id="about-title" data-reveal="lines">
                Engineers who treat the whole stack as one craft.
              </h2>
            </div>

            <div class="about__bio flow-lg">
              <p data-reveal="up">
                <strong>Rakiza Tech</strong> is a digital solutions company from Egypt.
                We build websites, systems and apps end to end — designing the database,
                the APIs and the logic underneath, then the screens on top.
              </p>
              <p data-reveal="up" data-reveal-delay="0.12">
                We've shipped real systems including a garage parking platform, a learning-
                management system and a student-observation app. We care about clean,
                maintainable code and data models that stay fast as they grow — a solid
                foundation, which is exactly what <em>rakiza</em> (<span lang="ar">ركيزة</span>) means.
              </p>

              <p class="about__avail" data-reveal="up" data-reveal-delay="0.18">
                <span class="status"><span class="dot" aria-hidden="true"></span> Available anytime · Cairo / Menofia, Egypt</span>
              </p>
            </div>
          </div>

          <!-- RIGHT — code "about card" -->
          <div class="about__card-wrap" data-reveal="scale" data-reveal-delay="0.1">
            <div class="term-window about__card" aria-label="company profile, as source code">
              <div class="term-bar">
                <span class="term-dots" aria-hidden="true">
                  <span class="term-dot term-dot--r"></span>
                  <span class="term-dot term-dot--y"></span>
                  <span class="term-dot term-dot--g"></span>
                </span>
                <span class="term-title">company.ts</span>
              </div>

              <div class="term-body about__code">
                <ol class="about__lines mono" aria-hidden="true">
                  <li><span class="tok-key">const</span> <span class="tok-fn">company</span> <span class="tok-punc">=</span> <span class="tok-punc">{</span></li>
                  <li>  <span class="about__prop">name</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="&quot;Rakiza Tech&quot;">"Rakiza Tech"</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">role</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="&quot;Digital Solutions&quot;">"Digital Solutions"</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">focus</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="&quot;websites · systems · apps&quot;">"websites · systems · apps"</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">stack</span><span class="tok-punc">:</span> <span class="tok-punc">[</span></li>
                  <li>    <span class="tok-str">"PHP"</span><span class="tok-punc">,</span> <span class="tok-str">"Laravel"</span><span class="tok-punc">,</span> <span class="tok-str">"MySQL"</span><span class="tok-punc">,</span></li>
                  <li>    <span class="tok-str">"JavaScript"</span><span class="tok-punc">,</span> <span class="tok-str">"Livewire"</span></li>
                  <li>  <span class="tok-punc">]</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">location</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="&quot;Cairo · Egypt&quot;">"Cairo · Egypt"</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">available</span><span class="tok-punc">:</span> <span class="tok-key">true</span><span class="tok-punc">,</span></li>
                  <li>  <span class="tok-com">// front to back, design to deploy</span></li>
                  <li><span class="tok-punc">}</span></li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats strip -->
        <div class="container about__stats-wrap">
          <dl class="about__stats" data-reveal-group data-reveal-stagger="0.1">
            <div class="about__stat">
              <dt class="about__stat-num tnum display"><span class="tnum" data-count="6" data-count-suffix="+">6+</span></dt>
              <dd class="about__stat-label">Years experience</dd>
            </div>
            <div class="about__stat">
              <dt class="about__stat-num tnum display"><span class="tnum" data-count="80" data-count-suffix="+">80+</span></dt>
              <dd class="about__stat-label">Projects shipped</dd>
            </div>
            <div class="about__stat">
              <dt class="about__stat-num tnum display"><span class="tnum" data-count="40" data-count-suffix="+">40+</span></dt>
              <dd class="about__stat-label">Happy clients</dd>
            </div>
            <div class="about__stat">
              <dt class="about__stat-num tnum display"><span class="tnum" data-count="12400" data-count-suffix="+">12,400+</span></dt>
              <dd class="about__stat-label">Commits pushed</dd>
            </div>
          </dl>
        </div>
      </section>
