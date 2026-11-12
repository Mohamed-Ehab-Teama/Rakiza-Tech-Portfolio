@extends('admin.layouts.app')

@php
    $isEdit = $role !== null;
    $granted = old('permissions', $granted);
    $actions = config('access.action_labels');
@endphp

@section('title', $isEdit ? 'Edit role' : 'New role')

@section('content')
  <header class="page-head">
    <div>
      <p class="page-head__crumbs mono"><a href="{{ route('admin.roles.index') }}">Roles</a> / {{ $isEdit ? 'Edit' : 'New' }}</p>
      <h1 class="page-head__title">{{ $isEdit ? $role->name : 'New role' }}</h1>
    </div>
  </header>

  <form class="form panel" method="POST" novalidate action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
    @csrf
    @if ($isEdit)
      @method('PUT')
    @endif

    <div class="form-grid">
      <div class="form-field form-field--half">
        <label class="form-label" for="name">Role name</label>
        <input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role?->name) }}" required
               @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p class="form-error" id="name-error">{{ $message }}</p>@enderror
      </div>
    </div>

    <fieldset class="form-section">
      <legend class="form-section__title">Permissions</legend>
      <p class="form-help">Tick what people with this role may do. "Publish" lets them make content visible on the website.</p>
      @error('permissions')<p class="form-error">{{ $message }}</p>@enderror

      <div class="table-wrap">
        <table class="table permissions" data-permission-matrix>
          <thead>
            <tr>
              <th scope="col">Area</th>
              @foreach ($actions as $action => $label)
                <th scope="col" class="permissions__col">{{ $label }}</th>
              @endforeach
              <th scope="col" class="permissions__col">All</th>
            </tr>
          </thead>
          <tbody>
            @foreach (config('access.areas') as $area => $definition)
              <tr>
                <th scope="row">{{ $definition['label'] }}</th>
                @foreach ($actions as $action => $label)
                  <td class="permissions__col">
                    @if (in_array($action, $definition['actions'], true))
                      @php($permission = "{$area}.{$action}")
                      <label class="checkbox checkbox--solo">
                        <input type="checkbox" name="permissions[]" value="{{ $permission }}" data-area="{{ $area }}"
                               @checked(in_array($permission, $granted, true))>
                        <span class="sr-only">{{ $label }} {{ Str::lower($definition['label']) }}</span>
                      </label>
                    @else
                      <span class="text-faint" aria-hidden="true">—</span>
                    @endif
                  </td>
                @endforeach
                <td class="permissions__col">
                  <label class="checkbox checkbox--solo">
                    <input type="checkbox" data-select-area="{{ $area }}">
                    <span class="sr-only">All {{ Str::lower($definition['label']) }} permissions</span>
                  </label>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </fieldset>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $isEdit ? 'Save changes' : 'Create role' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.roles.index') }}">Cancel</a>
    </div>
  </form>
@endsection
