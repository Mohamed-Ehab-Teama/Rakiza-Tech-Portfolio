      <section class="about section section--alt" id="about" aria-labelledby="about-title">
        <span class="ghost-index about__ghost" aria-hidden="true">01</span>

        <div class="container about__grid">
          <!-- LEFT — section head + bio -->
          <div class="about__intro">
            <div class="section-head">
              <span class="section-label"><span class="idx">01</span> ABOUT</span>
              <h2 class="section-title about__title" id="about-title" data-reveal="lines">
                {{ setting('about_title') }}
              </h2>
            </div>

            <div class="about__bio flow-lg">
              {{-- Settings → Home page texts → "About — text" (Markdown, made safe) --}}
              <div class="about__text flow-lg" data-reveal="up">
                {!! safe_markdown(setting('about_body')) !!}
              </div>

              @if (setting('availability') || setting('location'))
                <p class="about__avail" data-reveal="up" data-reveal-delay="0.18">
                  <span class="status"><span class="dot" aria-hidden="true"></span> {{ collect([setting('availability'), setting('location')])->filter()->implode(' · ') }}</span>
                </p>
              @endif
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

              @php($quoted = fn ($text) => '"'.$text.'"')
              <div class="term-body about__code">
                <ol class="about__lines mono" aria-hidden="true">
                  <li><span class="tok-key">const</span> <span class="tok-fn">company</span> <span class="tok-punc">=</span> <span class="tok-punc">{</span></li>
                  <li>  <span class="about__prop">name</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="{{ $quoted(setting('site_name')) }}">{{ $quoted(setting('site_name')) }}</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">role</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="&quot;Digital Solutions&quot;">"Digital Solutions"</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">focus</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="&quot;websites · systems · apps&quot;">"websites · systems · apps"</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">stack</span><span class="tok-punc">:</span> <span class="tok-punc">[</span></li>
                  <li>    <span class="tok-str">"PHP"</span><span class="tok-punc">,</span> <span class="tok-str">"Laravel"</span><span class="tok-punc">,</span> <span class="tok-str">"MySQL"</span><span class="tok-punc">,</span></li>
                  <li>    <span class="tok-str">"JavaScript"</span><span class="tok-punc">,</span> <span class="tok-str">"Livewire"</span></li>
                  <li>  <span class="tok-punc">]</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">location</span><span class="tok-punc">:</span> <span class="tok-str about__type" data-type="{{ $quoted(setting('location') ?: 'Egypt') }}">{{ $quoted(setting('location') ?: 'Egypt') }}</span><span class="tok-punc">,</span></li>
                  <li>  <span class="about__prop">available</span><span class="tok-punc">:</span> <span class="tok-key">{{ setting('availability') ? 'true' : 'false' }}</span><span class="tok-punc">,</span></li>
                  <li>  <span class="tok-com">// front to back, design to deploy</span></li>
                  <li><span class="tok-punc">}</span></li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        @if ($highlights->isNotEmpty())
          <!-- Stats strip -->
          <div class="container about__stats-wrap">
            <dl class="about__stats" data-reveal-group data-reveal-stagger="0.1">
              @foreach ($highlights as $metric)
                <div class="about__stat">
                  <dt class="about__stat-num tnum display"><span class="tnum" data-count="{{ $metric->value }}" data-count-decimals="{{ $metric->decimals }}" data-count-prefix="{{ $metric->prefix }}" data-count-suffix="{{ $metric->suffix }}">{{ $metric->formatted }}</span></dt>
                  <dd class="about__stat-label">{{ $metric->label }}</dd>
                </div>
              @endforeach
            </dl>
          </div>
        @endif
      </section>
