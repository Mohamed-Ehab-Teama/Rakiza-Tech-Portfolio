@extends('admin.layouts.app')

@section('title', 'My profile')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">My profile</h1>
      <p class="page-head__text">Your role: {{ $user->getRoleNames()->implode(', ') ?: 'none' }}. Last sign-in {{ $user->last_login_at?->diffForHumans() ?? 'never' }}.</p>
    </div>
  </header>

  <form class="form panel" method="POST" action="{{ route('admin.profile.update') }}" novalidate>
    @csrf
    @method('PUT')

    <fieldset class="form-section">
      <legend class="form-section__title">Your details</legend>
      <div class="form-grid">
        <div class="form-field form-field--half">
          <label class="form-label" for="name">Name</label>
          <input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required
                 @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
          @error('name')<p class="form-error" id="name-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field form-field--half">
          <label class="form-label" for="email">Email</label>
          <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                 @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
          @error('email')<p class="form-error" id="email-error">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <fieldset class="form-section">
      <legend class="form-section__title">Change password (optional)</legend>
      <div class="form-grid">
        <div class="form-field form-field--half">
          <label class="form-label" for="password">New password</label>
          <div class="input-group">
            <input class="input @error('password') is-invalid @enderror" type="password" id="password" name="password" autocomplete="new-password"
                   aria-describedby="password-help @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror>
            <button type="button" class="input-group__btn" data-toggle-password="password" aria-label="Show password">@include('admin.partials.icon', ['name' => 'eye'])</button>
          </div>
          <p class="form-help" id="password-help">At least 8 characters, with letters and numbers. Leave empty to keep your password.</p>
          @error('password')<p class="form-error" id="password-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field form-field--half">
          <label class="form-label" for="password_confirmation">Repeat the new password</label>
          <input class="input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
        </div>
      </div>
    </fieldset>

    <fieldset class="form-section">
      <legend class="form-section__title">Confirm it's you</legend>
      <div class="form-grid">
        <div class="form-field form-field--half">
          <label class="form-label" for="current_password">Current password</label>
          <input class="input @error('current_password') is-invalid @enderror" type="password" id="current_password" name="current_password" autocomplete="current-password"
                 aria-describedby="current-help @error('current_password') current-error @enderror" @error('current_password') aria-invalid="true" @enderror>
          <p class="form-help" id="current-help">Needed only when you change your email or password.</p>
          @error('current_password')<p class="form-error" id="current-error">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">Save my profile</button>
    </div>
  </form>
@endsection
