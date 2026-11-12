      <section class="stack section section--alt" id="stack" aria-labelledby="stack-title">
        <span class="ghost-index stack__ghost" aria-hidden="true">04</span>

        @if (! empty($marqueeRows))
          <!-- ====================================================================
               PART A — infinite marquees of the tech we work with daily
               (skills whose "scrolling row" is top or bottom)
               ==================================================================== -->
          <div class="stack__marquees" aria-label="Technologies we work with" role="group">
            @foreach ($marqueeRows as $row)
              <div class="marquee stack__marquee" @if ($loop->odd) style="--marquee-dur:42s" @else data-direction="right" style="--marquee-dur:54s" @endif>
                {{-- The list is printed twice; the copy makes the loop seamless. --}}
                @foreach ([false, true] as $isCopy)
                  <div class="marquee__track" @if ($isCopy) aria-hidden="true" @endif>
                    @foreach ($row as $name)
                      <span class="stack__chip mono">{{ $name }}</span>
                    @endforeach
                  </div>
                @endforeach
              </div>
            @endforeach
          </div>
        @endif

        <!-- ====================================================================
             PART B — categorized toolkit
             ==================================================================== -->
        <div class="container">
          <header class="section-head">
            <span class="section-label"><span class="idx">04</span> STACK</span>
            <h2 class="section-title" id="stack-title" data-reveal="lines">{{ setting('stack_title') }}</h2>
            @if (setting('stack_intro'))
              <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ setting('stack_intro') }}</p>
            @endif
          </header>

          <div class="stack__grid" data-reveal-group data-reveal-stagger="0.08" role="list">
            @foreach ($categories as $category)
              @php
                $tags = $category->skills->where('show_in_card', true);
                $bars = $category->skills->whereNotNull('level');
              @endphp
              <article class="card stack__card" role="listitem">
                <div class="stack__card-head">
                  <span class="stack__icon" aria-hidden="true">
                    @include('sections.stack.icon', ['icon' => $category->icon])
                  </span>
                  <h3 class="stack__card-title">{{ $category->name }}</h3>
                  @if ($category->meta)
                    <span class="stack__card-meta mono" aria-hidden="true">{{ $category->meta }}</span>
                  @endif
                </div>

                @if ($tags->isNotEmpty())
                  <ul class="stack__tags" aria-label="{{ $category->name }} technologies">
                    @foreach ($tags as $skill)
                      <li class="tag">{{ $skill->name }}</li>
                    @endforeach
                  </ul>
                @endif

                @if ($bars->isNotEmpty())
                  <ul class="stack__bars" aria-label="{{ $category->name }} proficiency">
                    @foreach ($bars as $skill)
                      <li class="stack__bar">
                        <span class="stack__bar-label mono">{{ $skill->name }}</span>
                        <span class="stack__bar-track"><i class="stack__bar-fill" style="--lvl:{{ $skill->level }}%"></i></span>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </article>
            @endforeach
          </div>
        </div>
      </section>
