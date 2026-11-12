{{-- /projects — every published project (ProjectController@index). --}}
@extends('layouts.site')

@section('title', 'Projects — '.setting('site_name'))
@section('description', 'All projects by '.setting('site_name').': '.Str::limit(setting('work_intro'), 120))

@section('content')
  <section class="page section" aria-labelledby="projects-title">
    <div class="container">
      <nav class="page__crumbs mono" aria-label="Breadcrumb">
        <a class="link-underline" href="{{ route('home') }}">Home</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Projects</span>
      </nav>

      <header class="section-head page__head">
        <span class="section-label"><span class="idx">{{ sprintf('%02d', $projects->count()) }}</span> PROJECTS</span>
        <h1 class="section-title page__title" id="projects-title" data-reveal="lines">{{ setting('work_title') }}</h1>
        @if (setting('work_intro'))
          <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ setting('work_intro') }}</p>
        @endif
      </header>

      @if ($projects->isEmpty())
        <p class="page__empty mono">No projects published yet — check back soon.</p>
      @else
        <div class="page__grid" data-reveal-group data-reveal-stagger="0.06">
          @foreach ($projects as $project)
            <article class="card card--spotlight work__item page__card">
              <div class="work__visual" data-type="{{ $project->display_visual }}" aria-hidden="true">
                @include('sections.work.visual', ['project' => $project])
              </div>
              <div class="work__body">
                <header class="work__top">
                  <span class="work__num tnum">{{ sprintf('%02d', $loop->iteration) }}</span>
                  @if ($project->year)
                    <span class="work__year mono">{{ $project->year }}</span>
                  @endif
                </header>
                <h2 class="work__name">{{ $project->title }}</h2>
                @if ($project->role)
                  <p class="work__role">{{ $project->role }}</p>
                @endif
                @if (! empty($project->tags))
                  <ul class="work__tags" aria-label="Tech stack">
                    @foreach ($project->tags as $tag)
                      <li class="tag{{ $loop->first ? ' tag--accent' : '' }}">{{ $tag }}</li>
                    @endforeach
                  </ul>
                @endif
                <a class="link-underline work__link" href="{{ route('projects.show', $project) }}">
                  View case <span aria-hidden="true">&rarr;</span><span class="sr-only"> — {{ $project->title }}</span>
                </a>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>
@endsection
