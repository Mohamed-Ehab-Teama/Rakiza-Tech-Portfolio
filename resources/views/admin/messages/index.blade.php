@extends('admin.layouts.app')

@section('title', 'Messages')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Messages</h1>
      <p class="page-head__text">Sent from the contact form on the website. {{ $unread }} unread.</p>
    </div>
    @can('messages.update')
      @if ($unread > 0)
        <form method="POST" action="{{ route('admin.messages.read-all') }}">
          @csrf
          <button class="btn btn--ghost" type="submit">@include('admin.partials.icon', ['name' => 'check']) Mark all as read</button>
        </form>
      @endif
    @endcan
  </header>

  <form class="toolbar" method="GET" role="search">
    <label class="toolbar__search">
      <span class="sr-only">Search messages</span>
      @include('admin.partials.icon', ['name' => 'search'])
      <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search name, email or text…">
    </label>
    <label>
      <span class="sr-only">Show</span>
      <select class="select" name="filter">
        <option value="">All messages</option>
        <option value="unread" @selected($filter === 'unread')>Unread</option>
        <option value="read" @selected($filter === 'read')>Read</option>
      </select>
    </label>
    <button class="btn btn--ghost btn--sm" type="submit">Filter</button>
    @if ($search !== '' || $filter)
      <a class="link-accent" href="{{ route('admin.messages.index') }}">Clear</a>
    @endif
  </form>

  @if ($messages->isEmpty())
    <div class="empty"><p>{{ $search !== '' || $filter ? 'No messages match this filter.' : 'No messages yet. They arrive here from the contact form.' }}</p></div>
  @else
    <div class="panel panel--flush">
      @foreach ($messages as $message)
        <a class="list-item list-item--wide{{ $message->isRead() ? '' : ' is-unread' }}" href="{{ route('admin.messages.show', $message) }}">
          <span class="list-item__title">
            @unless ($message->isRead())<span class="dot-unread" aria-label="Unread"></span>@endunless
            {{ $message->name }} <span class="list-item__sub">&lt;{{ $message->email }}&gt;</span>
          </span>
          <span class="list-item__text">{{ Str::limit($message->message, 140) }}</span>
          <span class="list-item__meta mono" title="{{ $message->created_at->format('Y-m-d H:i') }}">{{ $message->created_at->diffForHumans() }}</span>
        </a>
      @endforeach
    </div>
    {{ $messages->links() }}
  @endif
@endsection
