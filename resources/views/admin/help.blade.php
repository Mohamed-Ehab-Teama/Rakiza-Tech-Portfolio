@extends('admin.layouts.app')

@section('title', 'Help & guide')

@section('content')
  <header class="page-head">
    <div>
      <h1 class="page-head__title">Help &amp; guide</h1>
      <p class="page-head__text">How to manage the website from this dashboard. (The same text is in docs/USER_GUIDE.md.)</p>
    </div>
  </header>

  <div class="help">
    @if ($toc)
      <nav class="help__toc panel" aria-label="Contents">
        <p class="panel__title">Contents</p>
        <ul>
          @foreach ($toc as $item)
            <li><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>
          @endforeach
        </ul>
      </nav>
    @endif
    <article class="panel prose help__body">
      {!! $html !!}
    </article>
  </div>
@endsection
