{{-- Success / error messages after an action, and a summary of form errors. --}}
@if (session('success'))
  <div class="flash flash--success" role="status">
    @include('admin.partials.icon', ['name' => 'check'])
    <span>{{ session('success') }}</span>
  </div>
@endif
@if (session('error'))
  <div class="flash flash--error" role="alert">
    @include('admin.partials.icon', ['name' => 'x'])
    <span>{{ session('error') }}</span>
  </div>
@endif
@if ($errors->any())
  <div class="flash flash--error" role="alert">
    @include('admin.partials.icon', ['name' => 'x'])
    <span>Please fix the {{ $errors->count() === 1 ? 'highlighted field' : $errors->count().' highlighted fields' }} below.</span>
  </div>
@endif
