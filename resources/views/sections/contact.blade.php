      <section class="contact section section--alt" id="contact" aria-labelledby="contact-title">
        <span class="ghost-index contact__ghost" aria-hidden="true">05</span>

        <div class="container contact__grid">
          <!-- LEFT — closing CTA -->
          <div class="contact__lead">
            <header class="section-head">
              <span class="section-label"><span class="idx">05</span> CONTACT</span>
              <h2 class="section-title contact__title" id="contact-title" data-reveal="lines">
                {{ setting('contact_title') }}
              </h2>
              @if (setting('contact_intro'))
                <p class="section-intro contact__say" data-reveal="up" data-reveal-delay="0.1">{{ setting('contact_intro') }}</p>
              @endif
            </header>

            @if (setting('contact_email'))
              <a class="contact__email link-underline mono"
                 href="mailto:{{ setting('contact_email') }}"
                 data-reveal="up" data-reveal-delay="0.16"
                 data-magnetic="0.2">{{ setting('contact_email') }}</a>
            @endif

            @if (setting('contact_phone') || setting('contact_email_alt'))
              <p class="contact__alt mono" data-reveal="up" data-reveal-delay="0.2">
                @if (setting('contact_phone'))
                  <a class="link-underline" href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}">{{ setting('contact_phone') }}</a>
                @endif
                @if (setting('contact_phone') && setting('contact_email_alt'))
                  <span class="text-faint" aria-hidden="true"> · </span>
                @endif
                @if (setting('contact_email_alt'))
                  <a class="link-underline" href="mailto:{{ setting('contact_email_alt') }}">{{ setting('contact_email_alt') }}</a>
                @endif
              </p>
            @endif

            @if (setting('availability') || setting('location'))
              <p class="contact__avail" data-reveal="up" data-reveal-delay="0.22">
                <span class="status">
                  <span class="dot" aria-hidden="true"></span>
                  {{ collect([setting('availability'), setting('location')])->filter()->implode(' · ') }}
                </span>
              </p>
            @endif

            @if ($socials = social_links())
              <nav class="contact__socials" aria-label="Social profiles"
                   data-reveal="up" data-reveal-delay="0.28">
                @foreach ($socials as $social)
                  <a class="contact__social" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}">
                    @include('partials.social-icon', ['network' => $social['key']])
                    <span>{{ $social['label'] }}</span>
                  </a>
                @endforeach
              </nav>
            @endif
          </div>

          <!-- RIGHT — contact form: saved to Dashboard → Messages (ContactController) -->
          <div class="contact__panel" data-reveal="scale" data-reveal-delay="0.12">
            <form class="contact__form" method="POST" action="{{ route('contact.store') }}" novalidate aria-describedby="contact-status">
              @csrf
              <div class="contact__form-head mono" aria-hidden="true">
                <span class="term-prompt">~/contact</span>
                <span class="term-out">new message</span>
              </div>

              {{-- Honeypot: hidden from people, but spam bots fill it in. --}}
              <div class="contact__hp" aria-hidden="true">
                <label for="contact-website">Leave this field empty</label>
                <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
              </div>

              <div class="field contact__field">
                <input type="text" id="contact-name" name="name" value="{{ old('name') }}"
                       placeholder=" " autocomplete="name" maxlength="100"
                       aria-describedby="contact-status" required>
                <label for="contact-name">Name</label>
              </div>

              <div class="field contact__field">
                <input type="email" id="contact-email" name="email" value="{{ old('email') }}"
                       placeholder=" " autocomplete="email" inputmode="email" maxlength="150"
                       aria-describedby="contact-status" required>
                <label for="contact-email">Email</label>
              </div>

              <div class="field contact__field">
                <textarea id="contact-message" name="message"
                          placeholder=" " rows="5" maxlength="5000"
                          aria-describedby="contact-status" required>{{ old('message') }}</textarea>
                <label for="contact-message">Message</label>
              </div>

              <button type="submit" class="contact__submit btn btn--primary" data-magnetic="0.25" aria-label="Send message">
                $ send --message <span class="arrow" aria-hidden="true">&rarr;</span>
              </button>

              {{-- Result of a form sent without JavaScript (after the page reloads) --}}
              <div class="contact__status{{ session('contact_status') || $errors->any() ? ' is-visible' : '' }}{{ $errors->any() ? ' is-error' : '' }}{{ session('contact_status') ? ' is-ok' : '' }}" id="contact-status" role="status" aria-live="polite">
                @if (session('contact_status'))
                  <span class="contact__line term-ok">✓ {{ session('contact_status') }}</span>
                @endif
                @foreach ($errors->all() as $error)
                  <span class="contact__line term-err">✗ {{ $error }}</span>
                @endforeach
              </div>
            </form>
          </div>
        </div>
      </section>
