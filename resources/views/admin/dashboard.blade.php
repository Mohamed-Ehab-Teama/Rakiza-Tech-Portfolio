@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Welcome back, {{ Str::before(auth()->user()->name, ' ') }}</h1>
      <p class="page-head__text">Here is what is on {{ setting('site_name') }} right now.</p>
    </div>
    <a class="btn btn--ghost" href="{{ route('home') }}" target="_blank" rel="noopener">
      View the website @include('admin.partials.icon', ['name' => 'external'])
    </a>
  </header>

  @if ($stats || $unread !== null)
    <section class="stats" aria-label="Summary">
      @foreach ($stats as $stat)
        <a class="stat" href="{{ route($stat['route']) }}">
          <span class="stat__label">{{ $stat['label'] }}</span>
          <span class="stat__value tnum">{{ $stat['total'] }}</span>
          <span class="stat__meta mono">{{ $stat['published'] }} published</span>
        </a>
      @endforeach
      @if ($unread !== null)
        <a class="stat{{ $unread ? ' stat--alert' : '' }}" href="{{ route('admin.messages.index', ['filter' => 'unread']) }}">
          <span class="stat__label">Unread messages</span>
          <span class="stat__value tnum">{{ $unread }}</span>
          <span class="stat__meta mono">{{ $unread ? 'waiting for a reply' : 'inbox zero' }}</span>
        </a>
      @endif
    </section>
  @endif

  <div class="dash-grid">
    @can('messages.view')
      <section class="panel" aria-labelledby="latest-messages">
        <header class="panel__head">
          <h2 class="panel__title" id="latest-messages">Latest messages</h2>
          <a class="link-accent" href="{{ route('admin.messages.index') }}">All messages</a>
        </header>
        @forelse ($messages as $message)
          <a class="list-item{{ $message->isRead() ? '' : ' is-unread' }}" href="{{ route('admin.messages.show', $message) }}">
            <span class="list-item__title">{{ $message->name }} @unless ($message->isRead())<span class="badge badge--accent">new</span>@endunless</span>
            <span class="list-item__text">{{ Str::limit($message->message, 90) }}</span>
            <span class="list-item__meta mono">{{ $message->created_at->diffForHumans() }}</span>
          </a>
        @empty
          <p class="empty empty--inline">No messages yet. They arrive here from the contact form.</p>
        @endforelse
      </section>
    @endcan

    @can('activity.view')
      <section class="panel" aria-labelledby="recent-activity">
        <header class="panel__head">
          <h2 class="panel__title" id="recent-activity">Recent activity</h2>
          <a class="link-accent" href="{{ route('admin.activity.index') }}">Full log</a>
        </header>
        @forelse ($activity as $entry)
          <div class="list-item">
            <span class="list-item__title">{{ $entry->description }}</span>
            <span class="list-item__meta mono">{{ $entry->user?->name ?? 'Someone' }} · {{ $entry->created_at->diffForHumans() }}</span>
          </div>
        @empty
          <p class="empty empty--inline">Nothing has been changed yet.</p>
        @endforelse
      </section>
    @endcan

    <section class="panel" aria-labelledby="quick-links">
      <header class="panel__head">
        <h2 class="panel__title" id="quick-links">Quick actions</h2>
      </header>
      <div class="quick-links">
        @can('projects.create')<a class="btn btn--ghost" href="{{ route('admin.projects.create') }}">@include('admin.partials.icon', ['name' => 'plus']) New project</a>@endcan
        @can('websites.create')<a class="btn btn--ghost" href="{{ route('admin.websites.create') }}">@include('admin.partials.icon', ['name' => 'plus']) New website</a>@endcan
        @can('services.create')<a class="btn btn--ghost" href="{{ route('admin.services.create') }}">@include('admin.partials.icon', ['name' => 'plus']) New service</a>@endcan
        @can('settings.view')<a class="btn btn--ghost" href="{{ route('admin.settings.edit') }}">@include('admin.partials.icon', ['name' => 'settings']) Site settings</a>@endcan
        <a class="btn btn--ghost" href="{{ route('admin.help') }}">@include('admin.partials.icon', ['name' => 'help']) How do I…?</a>
      </div>
    </section>
  </div>
@endsection
