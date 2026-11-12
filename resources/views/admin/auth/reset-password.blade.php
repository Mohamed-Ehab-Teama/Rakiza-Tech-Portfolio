@extends('admin.layouts.auth')

@section('title', 'Choose a new password')
@section('intro', 'At least 8 characters, with letters and numbers.')

@section('content')
  <form class="form" method="POST" action="{{ route('admin.password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-field">
      <label class="form-label" for="email">Email</label>
      <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email"
             value="{{ old('email', $email) }}" autocomplete="username" required
             @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
      @error('email')<p class="form-error" id="email-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-field">
      <label class="form-label" for="password">New password</label>
      <input class="input @error('password') is-invalid @enderror" type="password" id="password" name="password"
             autocomplete="new-password" required
             @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
      @error('password')<p class="form-error" id="password-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-field">
      <label class="form-label" for="password_confirmation">Repeat the new password</label>
      <input class="input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
    </div>

    <button class="btn btn--primary btn--block" type="submit">Save new password</button>
  </form>
@endsection
