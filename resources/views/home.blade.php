{{--
    home.blade.php — the one-page landing site served at "/".

    It extends the public layout (layouts/site.blade.php) and puts the
    sections, in order, inside that layout's <main> element.
    Each section is its own file in resources/views/sections/, and has a
    matching stylesheet (public/css/sections/) and script (public/js/sections/).
--}}
@extends('layouts.site')

@section('content')
  @include('sections.hero')

  @include('sections.about')

  @include('sections.services')

  @include('sections.work')

  @include('sections.metrics')

  @include('sections.stack')

  @include('sections.terminal')

  @include('sections.contact')
@endsection
