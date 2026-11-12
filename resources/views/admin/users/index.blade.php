@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Users</h1>
      <p class="page-head__text">Everyone who can sign in to this dashboard. What they may do depends on their role.</p>
    </div>
    @can('users.create')
      <a class="btn btn--primary" href="{{ route('admin.users.create') }}">@include('admin.partials.icon', ['name' => 'plus']) New user</a>
    @endcan
  </header>

  <form class="toolbar" method="GET" role="search">
    <label class="toolbar__search">
      <span class="sr-only">Search users</span>
      @include('admin.partials.icon', ['name' => 'search'])
      <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search name or email…">
    </label>
    <button class="btn btn--ghost btn--sm" type="submit">Search</button>
    @if ($search !== '')<a class="link-accent" href="{{ route('admin.users.index') }}">Clear</a>@endif
  </form>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Role</th>
          <th scope="col">Status</th>
          <th scope="col">Last sign-in</th>
          <th class="table__actions" scope="col"><span class="sr-only">Actions</span></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          @php($manageable = ! $user->isSuperAdmin() || auth()->user()->isSuperAdmin())
          <tr>
            <td class="table__primary" data-label="Name">
              <span class="user-cell">
                <span class="admin-avatar admin-avatar--sm" aria-hidden="true">{{ $user->initials() }}</span>
                @if ($manageable && auth()->user()->can('users.update'))
                  <a href="{{ route('admin.users.edit', $user) }}">{{ $user->name }}</a>
                @else
                  {{ $user->name }}
                @endif
                @if (auth()->user()->is($user))<span class="badge">you</span>@endif
              </span>
            </td>
            <td data-label="Email">{{ $user->email }}</td>
            <td data-label="Role">{{ $user->getRoleNames()->implode(', ') ?: '—' }}</td>
            <td data-label="Status">
              <span class="badge {{ $user->is_active ? 'badge--live' : 'badge--draft' }}">{{ $user->is_active ? 'Active' : 'Switched off' }}</span>
            </td>
            <td data-label="Last sign-in">{{ $user->last_login_at?->diffForHumans() ?? 'never' }}</td>
            <td class="table__actions">
              @if ($manageable)
                @can('users.update')
                  <a class="icon-btn" href="{{ route('admin.users.edit', $user) }}" title="Edit" aria-label="Edit {{ $user->name }}">@include('admin.partials.icon', ['name' => 'edit'])</a>
                @endcan
                @can('users.delete')
                  @unless (auth()->user()->is($user))
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Delete the account of {{ $user->name }}? They will no longer be able to sign in.">
                      @csrf
                      @method('DELETE')
                      <button class="icon-btn icon-btn--danger" type="submit" title="Delete" aria-label="Delete {{ $user->name }}">@include('admin.partials.icon', ['name' => 'trash'])</button>
                    </form>
                  @endunless
                @endcan
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="empty empty--inline">No users match this search.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $users->links() }}
@endsection
