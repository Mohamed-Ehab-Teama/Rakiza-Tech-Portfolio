@extends('admin.layouts.app')

@php
    $isEdit = $user !== null;
    $isSelf = $isSelf ?? false;
    $isLastSuperAdmin = $isLastSuperAdmin ?? false;
    $currentRole = old('role', $user?->getRoleNames()->first());
@endphp

@section('title', $isEdit ? 'Edit user' : 'New user')

@section('content')
  <header class="page-head">
    <div>
      <p class="page-head__crumbs mono"><a href="{{ route('admin.users.index') }}">Users</a> / {{ $isEdit ? 'Edit' : 'New' }}</p>
      <h1 class="page-head__title">{{ $isEdit ? $user->name : 'New user' }}</h1>
    </div>
  </header>

  <form class="form panel" method="POST" novalidate action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if ($isEdit)
      @method('PUT')
    @endif

    <fieldset class="form-section">
      <legend class="form-section__title">Account</legend>
      <div class="form-grid">
        <div class="form-field form-field--half">
          <label class="form-label" for="name">Name</label>
          <input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user?->name) }}" required
                 @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
          @error('name')<p class="form-error" id="name-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field form-field--half">
          <label class="form-label" for="email">Email</label>
          <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user?->email) }}" required autocomplete="off"
                 @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
          @error('email')<p class="form-error" id="email-error">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <fieldset class="form-section">
      <legend class="form-section__title">{{ $isEdit ? 'Change password (optional)' : 'Password' }}</legend>
      <div class="form-grid">
        <div class="form-field form-field--half">
          <label class="form-label" for="password">{{ $isEdit ? 'New password' : 'Password' }}</label>
          <div class="input-group">
            <input class="input @error('password') is-invalid @enderror" type="password" id="password" name="password" autocomplete="new-password" {{ $isEdit ? '' : 'required' }}
                   aria-describedby="password-help @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror>
            <button type="button" class="input-group__btn" data-toggle-password="password" aria-label="Show password">@include('admin.partials.icon', ['name' => 'eye'])</button>
          </div>
          <p class="form-help" id="password-help">At least 8 characters, with letters and numbers.{{ $isEdit ? ' Leave empty to keep the current password.' : ' Share it with the person privately; they can change it under My profile.' }}</p>
          @error('password')<p class="form-error" id="password-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field form-field--half">
          <label class="form-label" for="password_confirmation">Repeat the password</label>
          <input class="input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
        </div>
      </div>
    </fieldset>

    <fieldset class="form-section">
      <legend class="form-section__title">Access</legend>
      <div class="form-grid">
        <div class="form-field form-field--half">
          <label class="form-label" for="role">Role</label>
          @if ($isSelf)
            <input class="input" id="role" value="{{ $currentRole }}" disabled aria-describedby="role-help">
            <p class="form-help" id="role-help">You cannot change your own role. Ask another administrator.</p>
          @else
            <select class="select @error('role') is-invalid @enderror" id="role" name="role" aria-describedby="role-help @error('role') role-error @enderror">
              @foreach ($roles as $role)
                <option value="{{ $role }}" @selected($currentRole === $role)>{{ $role }}</option>
              @endforeach
            </select>
            <p class="form-help" id="role-help">
              What the user may do. Manage roles under <a class="link-accent" href="{{ route('admin.roles.index') }}">Roles</a>.
              @if ($isLastSuperAdmin) This is the last active Super Admin, so the role cannot change. @endif
            </p>
            @error('role')<p class="form-error" id="role-error">{{ $message }}</p>@enderror
          @endif
        </div>
        <div class="form-field form-field--half">
          @if ($isSelf)
            <p class="form-help">This is your account, so it stays switched on.</p>
          @else
            <label class="switch" for="is_active">
              <input type="checkbox" id="is_active" name="is_active" value="1"
                     @checked(session()->hasOldInput() ? old('is_active') : ($user?->is_active ?? true))>
              <span class="switch__track" aria-hidden="true"></span>
              <span class="switch__label">Account switched on (can sign in)</span>
            </label>
            @error('is_active')<p class="form-error">{{ $message }}</p>@enderror
          @endif
        </div>
      </div>
    </fieldset>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $isEdit ? 'Save changes' : 'Create user' }}</button>
      <a class="btn btn--ghost" href="{{ route('admin.users.index') }}">Cancel</a>
    </div>
  </form>
@endsection
