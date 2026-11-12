@extends('admin.layouts.auth')

@section('title', 'Sign in')
@section('intro', 'Sign in to manage the website.')

@section('content')
  <form class="form" method="POST" action="{{ route('admin.login.store') }}" novalidate>
    @csrf

    <div class="form-field">
      <label class="form-label" for="email">Email</label>
      <input class="input @error('email') is-invalid @enderror" type="email" id="email" name="email"
             value="{{ old('email') }}" autocomplete="username" required autofocus
             @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
      @error('email')<p class="form-error" id="email-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-field">
      <label class="form-label" for="password">Password</label>
      <div class="input-group">
        <input class="input @error('password') is-invalid @enderror" type="password" id="password" name="password"
               autocomplete="current-password" required
               @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
        <button type="button" class="input-group__btn" data-toggle-password="password" aria-label="Show password">@include('admin.partials.icon', ['name' => 'eye'])</button>
      </div>
      @error('password')<p class="form-error" id="password-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-row form-row--between">
      <label class="checkbox">
        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
        <span>Keep me signed in</span>
      </label>
      <a class="link-accent" href="{{ route('admin.password.request') }}">Forgot your password?</a>
    </div>

    <button class="btn btn--primary btn--block" type="submit">Sign in</button>
  </form>

  <p class="auth-foot"><a class="link-accent" href="{{ route('home') }}">&larr; Back to the website</a></p>
@endsection
