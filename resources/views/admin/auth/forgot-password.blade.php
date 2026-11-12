@extends('admin.layouts.auth')

@section('title', 'Forgot your password?')
@section('intro', 'Enter your account email and we will send you a link to choose a new password.')

@section('content')
  <form class="form" method="POST" action="{{ route('admin.password.email') }}" novalidate>
    @csrf

    <div class="form-field">
      <label class="form-label" for="email">Email</label>
      <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email"
             value="{{ old('email') }}" autocomplete="username" required autofocus
             @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
      @error('email')<p class="form-error" id="email-error">{{ $message }}</p>@enderror
    </div>

    <button class="btn btn--primary btn--block" type="submit">Email me a reset link</button>
  </form>

  <p class="auth-foot"><a class="link-accent" href="{{ route('admin.login') }}">&larr; Back to sign in</a></p>
@endsection
