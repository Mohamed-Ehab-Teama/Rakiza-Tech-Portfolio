@extends('admin.layouts.app')

@php($canEdit = auth()->user()->can('settings.update'))

@section('title', 'Settings')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Settings</h1>
      <p class="page-head__text">{{ $groups[$group]['description'] }}</p>
    </div>
  </header>

  <nav class="tabs" aria-label="Settings sections">
    @foreach ($groups as $key => $definition)
      <a class="tabs__tab{{ $key === $group ? ' is-active' : '' }}" href="{{ route('admin.settings.edit', $key) }}"
         @if ($key === $group) aria-current="page" @endif>{{ $definition['label'] }}</a>
    @endforeach
  </nav>

  <form class="form panel" method="POST" enctype="multipart/form-data" novalidate action="{{ route('admin.settings.update', $group) }}">
    @csrf
    @method('PUT')

    <fieldset class="form-section" @disabled(! $canEdit)>
      <legend class="sr-only">{{ $groups[$group]['label'] }}</legend>
      <div class="form-grid">
        @foreach ($fields as $name => $field)
          @php($field['options'] = $field['type'] === 'select' ? $settings->options($name) : ($field['options'] ?? []))
          @php($field['width'] = $field['width'] ?? (in_array($field['type'], ['text', 'email', 'url', 'select', 'boolean'], true) ? 'half' : 'full'))
          @include('admin.resources.field', ['name' => $name, 'field' => $field, 'record' => null, 'value' => $values[$name]])
        @endforeach
      </div>
    </fieldset>

    @if ($canEdit)
      <div class="form-actions">
        <button class="btn btn--primary" type="submit">Save {{ Str::lower($groups[$group]['label']) }}</button>
      </div>
    @else
      <p class="hint">You can look at the settings, but changing them needs the "Edit settings" permission.</p>
    @endif
  </form>
@endsection
