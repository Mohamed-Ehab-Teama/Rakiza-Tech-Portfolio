{{--
    partials/theme-switch.blade.php — the color-theme menu (website nav + dashboard).
    The options come from config/themes.php; public/js/core/theme.js makes it work
    (open/close, keyboard arrows, remembering the choice).
--}}
<div class="theme-switch" data-theme-switch>
  <button type="button" class="theme-switch__btn" aria-haspopup="true" aria-expanded="false"
          aria-label="Change color theme" title="Color theme">
    {{-- half-filled circle: the usual "appearance" icon --}}
    <svg class="theme-switch__icon" viewBox="0 0 24 24" aria-hidden="true">
      <circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.6" />
      <path d="M12 3.5a8.5 8.5 0 0 1 0 17z" fill="currentColor" />
    </svg>
  </button>

  <div class="theme-switch__menu" role="menu" aria-label="Color theme" hidden>
    @foreach (config('themes.list') as $key => $theme)
      <button type="button" class="theme-switch__option" role="menuitemradio" aria-checked="false"
              aria-label="{{ $theme['label'] }}: {{ $theme['description'] }}" data-theme-option="{{ $key }}">
        <span class="theme-switch__swatch" aria-hidden="true"
              style="--sw-a: {{ $theme['swatch'][0] }}; --sw-b: {{ $theme['swatch'][1] }}"></span>
        <span class="theme-switch__text">
          <span class="theme-switch__label">{{ $theme['label'] }}</span>
          <span class="theme-switch__desc">{{ $theme['description'] }}</span>
        </span>
      </button>
    @endforeach
  </div>
</div>
