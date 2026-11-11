<div id="preloader" class="preloader" role="status" aria-live="polite" aria-label="Loading portfolio">
  <!-- Curtain panels (slide up on exit for a staged reveal) -->
  <div class="preloader__curtains" aria-hidden="true">
    <span></span><span></span><span></span><span></span>
  </div>

  <div class="preloader__inner">
    <div class="term-window preloader__window">
      <div class="term-bar">
        <div class="term-dots">
          <span class="term-dot term-dot--r"></span>
          <span class="term-dot term-dot--y"></span>
          <span class="term-dot term-dot--g"></span>
        </div>
        <span class="term-title">boot — teamora@portfolio</span>
      </div>

      <!-- Boot log (typed in by JS; final state below as the no-JS fallback) -->
      <div class="term-body preloader__log" id="preloader-log" aria-hidden="true">
        <p class="preloader__line"><span class="term-prompt">$</span> whoami</p>
        <p class="preloader__line"><span class="term-out">mohamed ehab teama — php / laravel developer</span></p>
        <p class="preloader__line"><span class="term-prompt">$</span> load --modules</p>
        <p class="preloader__line"><span class="term-out">&gt; code <span class="term-ok">&#10003;</span>&nbsp;&nbsp;apps <span class="term-ok">&#10003;</span>&nbsp;&nbsp;dashboards <span class="term-ok">&#10003;</span>&nbsp;&nbsp;databases <span class="term-ok">&#10003;</span>&nbsp;&nbsp;cli <span class="term-ok">&#10003;</span></span></p>
        <p class="preloader__line"><span class="term-prompt">$</span> build --portfolio</p>
        <p class="preloader__line"><span class="term-out">&gt; compiling assets&#8230;</span></p>
        <p class="preloader__line"><span class="term-ok">&gt; ready.</span></p>
      </div>
    </div>

    <div class="preloader__meter">
      <div class="preloader__meter-row">
        <span class="preloader__label mono">LOADING</span>
        <span class="preloader__count mono tnum" id="preloader-count" aria-hidden="true">100</span>
      </div>
      <div class="preloader__bar" role="presentation">
        <span class="preloader__fill" id="preloader-fill"></span>
      </div>
    </div>
  </div>
</div>
