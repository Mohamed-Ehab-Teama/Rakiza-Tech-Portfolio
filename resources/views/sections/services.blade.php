      <section class="services section" id="services" aria-labelledby="services-title">
        <span class="ghost-index services__ghost" aria-hidden="true">02</span>

        <div class="container">
          <header class="section-head">
            <span class="section-label"><span class="idx">02</span> SERVICES</span>
            <h2 class="section-title" id="services-title" data-reveal="lines">{{ setting('services_title') }}</h2>
            @if (setting('services_intro'))
              <p class="section-intro" data-reveal="up" data-reveal-delay="0.1">{{ setting('services_intro') }}</p>
            @endif
          </header>

          <div class="services__grid" data-reveal-group data-reveal-stagger="0.08" role="list">
            @foreach ($services as $service)
              <article class="card card--spotlight services__card{{ $service->is_wide ? ' services__card--wide' : '' }}" role="listitem">
                <div class="services__head">
                  <span class="services__num mono">{{ sprintf('%02d', $loop->iteration) }}</span>
                  <span class="services__icon" aria-hidden="true">
                    @include('sections.services.icon', ['visual' => $service->visual])
                  </span>
                </div>
                <h3 class="services__title">{{ $service->title }}</h3>
                <p class="services__desc">{{ $service->description }}</p>

                <div class="services__motif services__motif--{{ $service->visual }}" aria-hidden="true">
                  @include('sections.services.motif', ['visual' => $service->visual])
                </div>

                @if (! empty($service->tags))
                  <ul class="services__tags" aria-label="Stack">
                    @foreach ($service->tags as $tag)
                      <li class="tag">{{ $tag }}</li>
                    @endforeach
                  </ul>
                @endif
              </article>
            @endforeach
          </div>
        </div>
      </section>
