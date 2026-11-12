      <section class="work section section--alt" id="work" aria-labelledby="work-title">
        <span class="ghost-index work__ghost" aria-hidden="true">03</span>

        <div class="container work__head-wrap">
          <div class="section-head work__head">
            <span class="section-label"><span class="idx">03</span> WORK</span>
            <h2 class="section-title work__title" id="work-title" data-reveal="lines">{{ setting('work_title') }}</h2>
            @if (setting('work_intro'))
              <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ setting('work_intro') }}</p>
            @endif
          </div>

          <div class="work__meta" data-reveal="fade" data-reveal-delay="0.15">
            <div class="work__progress" id="work-progress" aria-hidden="true">
              <span class="work__progress-cur tnum">01</span>
              <span class="work__progress-sep">/</span>
              <span class="work__progress-total tnum">{{ sprintf('%02d', $projects->count()) }}</span>
            </div>
            <p class="work__hint mono">
              <span class="work__hint-desk">Scroll to explore the lineup</span>
              <span class="work__hint-touch">Swipe to browse</span>
            </p>
          </div>
        </div>

        <div class="work__viewport">
          <div class="work__track" id="work-track">
            @foreach ($projects as $project)
              <article class="card card--spotlight work__item">
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
                  <h3 class="work__name">{{ $project->title }}</h3>
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
                  <a class="link-underline work__link" href="{{ route('projects.show', $project) }}" data-magnetic="0.25">
                    View case <span aria-hidden="true">&rarr;</span><span class="sr-only"> — {{ $project->title }}</span>
                  </a>
                </div>
              </article>
            @endforeach

            <a class="work__more" href="{{ route('projects.index') }}" data-magnetic="0.3">
              <span class="work__more-inner">
                View all
                <span class="work__more-arrow" aria-hidden="true">&rarr;</span>
              </span>
              <span class="work__more-sub mono">{{ $projectCount }} {{ Str::plural('project', $projectCount) }} in the archive</span>
            </a>
          </div>
        </div>
      </section>
