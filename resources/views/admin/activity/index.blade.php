@extends('admin.layouts.app')

@section('title', 'Activity')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Activity log</h1>
      <p class="page-head__text">Every change made in the dashboard: who did it and when.</p>
    </div>
  </header>

  <form class="toolbar" method="GET" role="search">
    <label class="toolbar__search">
      <span class="sr-only">Search the log</span>
      @include('admin.partials.icon', ['name' => 'search'])
      <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search…">
    </label>
    <label>
      <span class="sr-only">User</span>
      <select class="select" name="user">
        <option value="">Everyone</option>
        @foreach ($users as $id => $name)
          <option value="{{ $id }}" @selected($userId === $id)>{{ $name }}</option>
        @endforeach
      </select>
    </label>
    <button class="btn btn--ghost btn--sm" type="submit">Filter</button>
    @if ($search !== '' || $userId)<a class="link-accent" href="{{ route('admin.activity.index') }}">Clear</a>@endif
  </form>

  @if ($entries->isEmpty())
    <div class="empty"><p>Nothing logged yet.</p></div>
  @else
    <div class="panel panel--flush">
      @foreach ($entries as $entry)
        <div class="list-item list-item--wide">
          <span class="list-item__title">
            <span class="badge badge--{{ match ($entry->action) { 'created' => 'live', 'deleted' => 'danger', 'signed_in' => 'accent', default => 'muted' } }}">{{ str_replace('_', ' ', $entry->action) }}</span>
            {{ $entry->description }}
          </span>
          @if (! empty($entry->properties['changed']))
            <span class="list-item__text">Changed: {{ implode(', ', array_map(fn ($f) => str_replace('_', ' ', $f), $entry->properties['changed'])) }}</span>
          @endif
          <span class="list-item__meta mono" title="{{ $entry->created_at->format('Y-m-d H:i:s') }}">{{ $entry->user?->name ?? 'Deleted user' }} · {{ $entry->created_at->diffForHumans() }}</span>
        </div>
      @endforeach
    </div>
    {{ $entries->links() }}
  @endif
@endsection
