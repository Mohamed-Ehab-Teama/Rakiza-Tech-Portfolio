{{-- Service card icon. $visual: code | app | chart | db | cli (see Service::VISUALS) --}}
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
  @switch($visual)
    @case('app')
      <rect x="6" y="2.5" width="12" height="19" rx="2.5"></rect>
      <line x1="10" y1="18.5" x2="14" y2="18.5"></line>
      @break
    @case('chart')
      <line x1="3" y1="21" x2="21" y2="21"></line>
      <rect x="5" y="12" width="3" height="6"></rect>
      <rect x="10.5" y="8" width="3" height="10"></rect>
      <rect x="16" y="4" width="3" height="14"></rect>
      @break
    @case('db')
      <ellipse cx="12" cy="5.5" rx="7.5" ry="3"></ellipse>
      <path d="M4.5 5.5v6c0 1.66 3.36 3 7.5 3s7.5-1.34 7.5-3v-6"></path>
      <path d="M4.5 11.5v6c0 1.66 3.36 3 7.5 3s7.5-1.34 7.5-3v-6"></path>
      @break
    @case('cli')
      <rect x="2.5" y="4" width="19" height="16" rx="2.5"></rect>
      <polyline points="7 9 10 12 7 15"></polyline>
      <line x1="12.5" y1="15" x2="16.5" y2="15"></line>
      @break
    @default
      <polyline points="8 7 3 12 8 17"></polyline>
      <polyline points="16 7 21 12 16 17"></polyline>
      <line x1="13.5" y1="5" x2="10.5" y2="19"></line>
  @endswitch
</svg>
