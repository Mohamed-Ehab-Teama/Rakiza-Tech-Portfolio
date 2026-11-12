{{--
    The create / edit form shared by every content type. The fields come from
    the controller's fields() and are grouped by their "section".
--}}
@extends('admin.layouts.app')

@php
    $route = 'admin.'.$labels['route'];
    $isEdit = $record !== null;
    $sections = collect($fields)->groupBy(fn ($field) => $field['section'] ?? '', true);
@endphp

@section('title', ($isEdit ? 'Edit ' : 'New ').Str::lower($labels['singular']))

@section('content')
  <header class="page-head">
    <div>
      <p class="page-head__crumbs mono">
        <a href="{{ route($route.'.index') }}">{{ $labels['plural'] }}</a> / {{ $isEdit ? 'Edit' : 'New' }}
      </p>
      <h1 class="page-head__title">{{ $isEdit ? $record->activityLabel() : 'New '.Str::lower($labels['singular']) }}</h1>
    </div>
    @if ($publicUrl)
      <a class="btn btn--ghost" href="{{ $publicUrl }}" target="_blank" rel="noopener">
        View on the website @include('admin.partials.icon', ['name' => 'external'])
      </a>
    @endif
  </header>

  <form class="form panel" method="POST" enctype="multipart/form-data" novalidate
        action="{{ $isEdit ? route($route.'.update', $record->getKey()) : route($route.'.store') }}">
    @csrf
    @if ($isEdit)
      @method('PUT')
    @endif

    @foreach ($sections as $section => $sectionFields)
      <fieldset class="form-section">
        @if ($section !== '')
          <legend class="form-section__title">{{ $section }}</legend>
        @endif
        <div class="form-grid">
          @foreach ($sectionFields as $name => $field)
            @include('admin.resources.field', ['name' => $name, 'field' => $field, 'record' => $record])
          @endforeach
        </div>
      </fieldset>
    @endforeach

    @unless (collect($fields)->contains(fn ($field) => $field['publish'] ?? false))
      @if (collect(['projects', 'websites', 'services', 'skills', 'metrics'])->contains($area))
        <p class="hint">You can save drafts; someone with the "publish" permission makes them visible on the website.</p>
      @endif
    @endunless

    <div class="form-actions">
      <button class="btn btn--primary" type="submit">{{ $isEdit ? 'Save changes' : 'Create '.Str::lower($labels['singular']) }}</button>
      <a class="btn btn--ghost" href="{{ route($route.'.index') }}">Cancel</a>
    </div>
  </form>
@endsection
