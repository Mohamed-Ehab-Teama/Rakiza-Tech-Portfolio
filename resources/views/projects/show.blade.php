{{-- /projects/{slug} — one project's case study (ProjectController@show). --}}
@extends('layouts.site')

@php
    $metaDescription = $project->seo_description
        ?: Str::limit($project->summary ?: strip_tags(safe_markdown($project->description)), 160);
@endphp

@section('title', ($project->seo_title ?: $project->title).' — '.setting('site_name'))
@section('description', $metaDescription)
@section('og_type', 'article')
@if ($project->cover_url)
  @section('og_image', $project->cover_url)
@endif

@push('jsonld')
  @php
      $creativeWork = array_filter([
          '@context' => 'https://schema.org',
          '@type' => 'CreativeWork',
          'name' => $project->title,
          'headline' => $project->role,
          'description' => $metaDescription,
          'url' => route('projects.show', $project),
          'image' => $project->cover_url,
          'dateCreated' => $project->year ? (string) $project->year : null,
          'keywords' => $project->tags ? implode(', ', $project->tags) : null,
          'creator' => ['@type' => 'Organization', 'name' => setting('site_name'), 'url' => route('home')],
      ]);
  @endphp
  <script type="application/ld+json">@json($creativeWork, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG)</script>
@endpush

@section('content')
  <article class="page section project" aria-labelledby="project-title">
    @if ($isPreview)
      <div class="page__banner mono" role="note">
        Draft preview — visitors can't see this project until it is published in the dashboard.
      </div>
    @endif

    <div class="container">
      <nav class="page__crumbs mono" aria-label="Breadcrumb">
        <a class="link-underline" href="{{ route('home') }}">Home</a>
        <span aria-hidden="true">/</span>
        <a class="link-underline" href="{{ route('projects.index') }}">Projects</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $project->title }}</span>
      </nav>

      <header class="section-head page__head">
        <span class="section-label"><span class="idx">{{ $project->year ?: '—' }}</span> CASE STUDY</span>
        <h1 class="section-title page__title" id="project-title" data-reveal="lines">{{ $project->title }}</h1>
        @if ($project->role)
          <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ $project->role }}</p>
        @endif
      </header>

      <dl class="project__facts mono" data-reveal="up" data-reveal-delay="0.12">
        @if ($project->client)
          <div><dt>Client</dt><dd>{{ $project->client }}</dd></div>
        @endif
        @if ($project->year)
          <div><dt>Year</dt><dd>{{ $project->year }}</dd></div>
        @endif
        @if (! empty($project->tags))
          <div class="project__facts-stack">
            <dt>Stack</dt>
            <dd>
              <ul class="work__tags">
                @foreach ($project->tags as $tag)
                  <li class="tag{{ $loop->first ? ' tag--accent' : '' }}">{{ $tag }}</li>
                @endforeach
              </ul>
            </dd>
          </div>
        @endif
      </dl>

      @if ($project->live_url || $project->repo_url)
        <p class="project__links" data-reveal="up" data-reveal-delay="0.14">
          @if ($project->live_url)
            <a class="btn btn--primary" href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer">
              Visit live site <span aria-hidden="true">&#8599;</span>
            </a>
          @endif
          @if ($project->repo_url)
            <a class="btn btn--ghost" href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer">
              Source code <span aria-hidden="true">&#8599;</span>
            </a>
          @endif
        </p>
      @endif

      <div class="work__visual project__visual" data-type="{{ $project->display_visual }}" data-reveal="scale" aria-hidden="true">
        @include('sections.work.visual', ['project' => $project])
      </div>
    </div>

    <div class="container container--narrow">
      <div class="prose" data-reveal="up">
        @if ($project->summary)
          <p class="project__summary">{{ $project->summary }}</p>
        @endif
        {!! safe_markdown($project->description) !!}
      </div>

      <nav class="page__pager mono" aria-label="More projects">
        @if ($previous)
          <a class="page__pager-link" href="{{ route('projects.show', $previous) }}" rel="prev">
            <span class="page__pager-dir">&larr; Previous</span>
            <span class="page__pager-title">{{ $previous->title }}</span>
          </a>
        @else
          <span></span>
        @endif
        @if ($next)
          <a class="page__pager-link page__pager-link--next" href="{{ route('projects.show', $next) }}" rel="next">
            <span class="page__pager-dir">Next &rarr;</span>
            <span class="page__pager-title">{{ $next->title }}</span>
          </a>
        @endif
      </nav>

      <p class="project__cta">
        <a class="btn btn--ghost" href="{{ route('projects.index') }}">&larr; All projects</a>
        <a class="btn btn--primary" href="{{ route('home') }}#contact">Start a project like this <span aria-hidden="true">&rarr;</span></a>
      </p>
    </div>
  </article>
@endsection
