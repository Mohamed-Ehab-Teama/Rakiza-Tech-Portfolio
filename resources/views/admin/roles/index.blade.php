@extends('admin.layouts.app')

@section('title', 'Roles')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Roles &amp; permissions</h1>
      <p class="page-head__text">A role is a set of permissions. Every user has one role. The Super Admin role can do everything and cannot be changed.</p>
    </div>
    @can('roles.create')
      <a class="btn btn--primary" href="{{ route('admin.roles.create') }}">@include('admin.partials.icon', ['name' => 'plus']) New role</a>
    @endcan
  </header>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Role</th>
          <th scope="col">Permissions</th>
          <th scope="col">Users</th>
          <th class="table__actions" scope="col"><span class="sr-only">Actions</span></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($roles as $role)
          @php($protected = $role->name === $superAdmin)
          <tr>
            <td class="table__primary" data-label="Role">
              @if (! $protected && auth()->user()->can('roles.update'))
                <a href="{{ route('admin.roles.edit', $role) }}">{{ $role->name }}</a>
              @else
                {{ $role->name }}
              @endif
              @if ($protected)<span class="badge badge--accent">protected</span>@endif
            </td>
            <td data-label="Permissions">{{ $protected ? 'Everything' : $role->permissions_count.' of '.$totalPermissions }}</td>
            <td data-label="Users">{{ $role->users_count }}</td>
            <td class="table__actions">
              @unless ($protected)
                @can('roles.update')
                  <a class="icon-btn" href="{{ route('admin.roles.edit', $role) }}" title="Edit" aria-label="Edit {{ $role->name }}">@include('admin.partials.icon', ['name' => 'edit'])</a>
                @endcan
                @can('roles.delete')
                  <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" data-confirm="Delete the role {{ $role->name }}?">
                    @csrf
                    @method('DELETE')
                    <button class="icon-btn icon-btn--danger" type="submit" title="Delete" aria-label="Delete {{ $role->name }}">@include('admin.partials.icon', ['name' => 'trash'])</button>
                  </form>
                @endcan
              @endunless
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
