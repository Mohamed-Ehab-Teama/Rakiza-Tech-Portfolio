@extends('admin.layouts.app')

@section('title', 'Message from '.$message->name)

@section('content')
  <header class="page-head">
    <div>
      <p class="page-head__crumbs mono"><a href="{{ route('admin.messages.index') }}">Messages</a> / Message</p>
      <h1 class="page-head__title">{{ $message->name }}</h1>
      <p class="page-head__text">
        <a class="link-accent" href="mailto:{{ $message->email }}">{{ $message->email }}</a>
        · {{ $message->created_at->format('l, j F Y \a\t H:i') }} ({{ $message->created_at->diffForHumans() }})
      </p>
    </div>
    <div class="page-head__actions">
      <a class="btn btn--primary" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: your message to '.setting('site_name')) }}">
        @include('admin.partials.icon', ['name' => 'mail']) Reply by email
      </a>
      @can('messages.update')
        <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}">
          @csrf
          @method('PATCH')
          <button class="btn btn--ghost" type="submit">Mark as {{ $message->isRead() ? 'unread' : 'read' }}</button>
        </form>
      @endcan
      @can('messages.delete')
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message from {{ $message->name }}? This cannot be undone.">
          @csrf
          @method('DELETE')
          <button class="btn btn--danger" type="submit">@include('admin.partials.icon', ['name' => 'trash']) Delete</button>
        </form>
      @endcan
    </div>
  </header>

  <article class="panel message">
    <div class="message__body">{!! nl2br(e($message->message)) !!}</div>
    <footer class="message__meta mono">
      <span>IP {{ $message->ip_address ?? 'unknown' }}</span>
      @if ($message->user_agent)
        <span title="{{ $message->user_agent }}">{{ Str::limit($message->user_agent, 80) }}</span>
      @endif
    </footer>
  </article>
@endsection
