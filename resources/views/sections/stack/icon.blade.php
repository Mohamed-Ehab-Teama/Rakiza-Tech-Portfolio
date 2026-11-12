{{-- Stack category icon. $icon: see SkillCategory::ICONS --}}
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
  @switch($icon)
    @case('frontend')
      <rect x="2.5" y="3.5" width="19" height="14" rx="2.5"></rect>
      <line x1="2.5" y1="8" x2="21.5" y2="8"></line>
      <line x1="9" y1="20.5" x2="15" y2="20.5"></line>
      <line x1="12" y1="17.5" x2="12" y2="20.5"></line>
      @break
    @case('backend')
      <polyline points="7 8 3.5 12 7 16"></polyline>
      <polyline points="13 8 16.5 12 13 16"></polyline>
      <line x1="20.5" y1="6" x2="20.5" y2="18"></line>
      @break
    @case('database')
      <ellipse cx="12" cy="5.5" rx="7.5" ry="3"></ellipse>
      <path d="M4.5 5.5v6c0 1.66 3.36 3 7.5 3s7.5-1.34 7.5-3v-6"></path>
      <path d="M4.5 11.5v6c0 1.66 3.36 3 7.5 3s7.5-1.34 7.5-3v-6"></path>
      @break
    @case('devops')
      <circle cx="12" cy="12" r="3"></circle>
      <path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.2 5.2l2.1 2.1M16.7 16.7l2.1 2.1M18.8 5.2l-2.1 2.1M7.3 16.7l-2.1 2.1"></path>
      @break
    @case('design')
      <path d="M12 20.5h8.5"></path>
      <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path>
      @break
    @case('mobile')
      <rect x="6" y="2.5" width="12" height="19" rx="2.5"></rect>
      <line x1="10" y1="18.5" x2="14" y2="18.5"></line>
      @break
    @case('cloud')
      <path d="M17.5 19a4.5 4.5 0 1 0-1.2-8.84A6 6 0 0 0 4.5 12.5 3.5 3.5 0 0 0 6 19z"></path>
      @break
    @default
      <polygon points="12 2.5 14.9 8.6 21.5 9.4 16.6 13.9 17.9 20.5 12 17.2 6.1 20.5 7.4 13.9 2.5 9.4 9.1 8.6"></polygon>
  @endswitch
</svg>
