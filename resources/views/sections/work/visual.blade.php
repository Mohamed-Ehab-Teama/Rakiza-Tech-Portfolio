{{--
    The artwork at the top of a project card (decorative).
    Uses $project->display_visual: dashboard | store | kanban | terminal | map | image.
--}}
@switch($project->display_visual)
  @case('image')
    <div class="work__mock work__mock--image">
      <img src="{{ $project->cover_url }}" alt="" loading="lazy" decoding="async" width="640" height="400" />
    </div>
    @break

  @case('store')
    <div class="work__mock work__mock--store">
      <div class="work__store-hero">
        <span class="work__store-eyebrow mono">NEW COURSE</span>
        <span class="work__store-title">Laravel Mastery</span>
        <span class="work__store-price">Enroll</span>
      </div>
      <div class="work__store-grid">
        <span class="work__store-card"></span>
        <span class="work__store-card"></span>
        <span class="work__store-card"></span>
        <span class="work__store-card"></span>
      </div>
      <div class="work__store-cart">
        <span class="work__store-cart-label">Progress</span>
        <span class="work__store-cart-total">68%</span>
        <span class="work__store-cart-btn">Continue</span>
      </div>
    </div>
    @break

  @case('kanban')
    <div class="work__mock work__mock--kanban">
      <div class="work__kan-col">
        <span class="work__kan-head">To do</span>
        <span class="work__kan-card"></span>
        <span class="work__kan-card work__kan-card--tall"></span>
      </div>
      <div class="work__kan-col">
        <span class="work__kan-head">In progress</span>
        <span class="work__kan-card work__kan-card--accent"></span>
        <span class="work__kan-card"></span>
      </div>
      <div class="work__kan-col">
        <span class="work__kan-head">Done</span>
        <span class="work__kan-card work__kan-card--done"></span>
      </div>
    </div>
    @break

  @case('terminal')
    <div class="work__mock work__mock--term">
      <div class="work__term-bar">
        <span class="work__term-dot work__term-dot--r"></span>
        <span class="work__term-dot work__term-dot--y"></span>
        <span class="work__term-dot work__term-dot--g"></span>
        <span class="work__term-name mono">rakiza — zsh</span>
      </div>
      <pre class="work__term-body mono"><span class="work__term-line"><span class="work__p">$</span> php artisan optimize</span>
<span class="work__term-line work__o">› building 14 packages…</span>
<span class="work__term-line work__ok">✓ lint · test · bundle</span>
<span class="work__term-line work__o">› pushing to registry</span>
<span class="work__term-line work__ok">✓ shipped in 12.4s</span>
<span class="work__term-line"><span class="work__p">$</span> <span class="work__cur"></span></span></pre>
    </div>
    @break

  @case('map')
    <div class="work__mock work__mock--map">
      <svg class="work__map-grid" viewBox="0 0 320 200" preserveAspectRatio="none" role="presentation">
        <g class="work__map-lines">
          <line x1="40" y1="0" x2="40" y2="200"/><line x1="120" y1="0" x2="120" y2="200"/>
          <line x1="200" y1="0" x2="200" y2="200"/><line x1="280" y1="0" x2="280" y2="200"/>
          <line x1="0" y1="50" x2="320" y2="50"/><line x1="0" y1="100" x2="320" y2="100"/>
          <line x1="0" y1="150" x2="320" y2="150"/>
        </g>
        <path class="work__map-route" d="M28,168 C90,150 110,90 168,86 C220,82 250,40 296,36" fill="none"/>
      </svg>
      <span class="work__map-pin work__map-pin--1"></span>
      <span class="work__map-pin work__map-pin--2"></span>
      <span class="work__map-pin work__map-pin--3"></span>
      <span class="work__map-chip mono">3 units live</span>
    </div>
    @break

  @default
    @php($fillId = 'work-fill-'.$project->id)
    <div class="work__mock work__mock--dash">
      <div class="work__dash-bar">
        <span class="work__dash-tab is-active">Overview</span>
        <span class="work__dash-tab">Cohorts</span>
        <span class="work__dash-tab">Revenue</span>
      </div>
      <div class="work__dash-kpis">
        <div class="work__kpi"><span class="work__kpi-label">MRR</span><span class="work__kpi-val">$48.2k</span><span class="work__kpi-delta work__kpi-delta--up">+12.4%</span></div>
        <div class="work__kpi"><span class="work__kpi-label">Active</span><span class="work__kpi-val">9,214</span><span class="work__kpi-delta work__kpi-delta--up">+3.1%</span></div>
        <div class="work__kpi"><span class="work__kpi-label">Churn</span><span class="work__kpi-val">1.8%</span><span class="work__kpi-delta work__kpi-delta--dn">-0.4%</span></div>
      </div>
      <svg class="work__chart" viewBox="0 0 320 120" preserveAspectRatio="none" role="presentation">
        <defs>
          <linearGradient id="{{ $fillId }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" style="stop-color: rgba(var(--accent-rgb), 0.34)"/>
            <stop offset="100%" style="stop-color: rgba(var(--accent-rgb), 0)"/>
          </linearGradient>
        </defs>
        <path class="work__chart-area" d="M0,96 L0,82 C30,70 50,86 80,64 C112,40 132,58 162,44 C196,28 214,46 246,30 C276,16 296,30 320,18 L320,120 L0,120 Z" fill="url(#{{ $fillId }})"/>
        <path class="work__chart-line" d="M0,82 C30,70 50,86 80,64 C112,40 132,58 162,44 C196,28 214,46 246,30 C276,16 296,30 320,18" fill="none"/>
      </svg>
    </div>
@endswitch
