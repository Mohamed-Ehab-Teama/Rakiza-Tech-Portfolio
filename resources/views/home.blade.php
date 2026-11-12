{{--
    home.blade.php — the one-page landing site served at "/" (HomeController).

    It extends the public layout (layouts/site.blade.php) and puts the
    sections, in order, inside that layout's <main> element.
    Each section is its own file in resources/views/sections/, and has a
    matching stylesheet (public/css/sections/) and script (public/js/sections/).
    Sections with nothing published are left out.
--}}
@extends('layouts.site', ['withPreloader' => true])

@push('jsonld')
  @include('partials.jsonld-home')
@endpush

@section('content')
  @include('sections.hero')

  @include('sections.about')

  @if ($services->isNotEmpty())
    @include('sections.services')
  @endif

  @if ($projects->isNotEmpty())
    @include('sections.work')
  @endif

  @if ($websites->isNotEmpty())
    @include('sections.websites')
  @endif

  @include('sections.metrics')

  @if ($categories->isNotEmpty())
    @include('sections.stack')
  @endif

  @include('sections.terminal')

  @include('sections.contact')
@endsection
