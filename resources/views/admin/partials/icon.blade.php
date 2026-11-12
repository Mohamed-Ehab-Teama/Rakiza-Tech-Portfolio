{{-- Dashboard icons (outline, 24×24). Usage: @include('admin.partials.icon', ['name' => 'folder']) --}}
<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
  @switch($name)
    @case('dashboard') <rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/> @break
    @case('folder') <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H9l2 2.5h7.5A2.5 2.5 0 0 1 21 10v7.5a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 17.5z"/> @break
    @case('globe') <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/> @break
    @case('layers') <path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/> @break
    @case('cpu') <rect x="6" y="6" width="12" height="12" rx="2"/><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"/> @break
    @case('chart') <path d="M3 21h18"/><rect x="5" y="11" width="3" height="7"/><rect x="10.5" y="6" width="3" height="12"/><rect x="16" y="13" width="3" height="5"/> @break
    @case('mail') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/> @break
    @case('users') <circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6 6 0 0 1 3.5 6"/> @break
    @case('shield') <path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.3 7.5 9.5 4.4-1.2 7.5-4.9 7.5-9.5V6z"/><path d="m9 12 2 2 4-4"/> @break
    @case('settings') <circle cx="12" cy="12" r="3"/><path d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/> @break
    @case('activity') <path d="M3 12h4l3-8 4 16 3-8h4"/> @break
    @case('help') <circle cx="12" cy="12" r="9"/><path d="M9.5 9.2a2.6 2.6 0 0 1 5 1c0 1.8-2.5 2.4-2.5 4"/><path d="M12 17.5h.01"/> @break
    @case('logout') <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="m10 16-4-4 4-4M6 12h10"/> @break
    @case('plus') <path d="M12 5v14M5 12h14"/> @break
    @case('edit') <path d="M4 20h4L19 9l-4-4L4 16z"/><path d="m13.5 6.5 4 4"/> @break
    @case('trash') <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/> @break
    @case('eye') <path d="M2.5 12S6 5 12 5s9.5 7 9.5 7S18 19 12 19 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/> @break
    @case('grip') <circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/> @break
    @case('search') <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/> @break
    @case('external') <path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/> @break
    @case('check') <path d="m5 12.5 4.5 4.5L19 7.5"/> @break
    @case('x') <path d="M6 6l12 12M18 6 6 18"/> @break
    @case('menu') <path d="M4 7h16M4 12h16M4 17h16"/> @break
    @case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/> @break
    @case('arrow-up') <path d="M12 19V5M6 11l6-6 6 6"/> @break
    @case('arrow-down') <path d="M12 5v14M6 13l6 6 6-6"/> @break
    @default <circle cx="12" cy="12" r="9"/>
  @endswitch
</svg>
