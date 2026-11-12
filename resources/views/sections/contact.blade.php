      <section class="contact section section--alt" id="contact" aria-labelledby="contact-title">
        <span class="ghost-index contact__ghost" aria-hidden="true">05</span>

        <div class="container contact__grid">
          <!-- LEFT — closing CTA -->
          <div class="contact__lead">
            <header class="section-head">
              <span class="section-label"><span class="idx">05</span> CONTACT</span>
              <h2 class="section-title contact__title" id="contact-title" data-reveal="lines">
                Let's build something.
              </h2>
              <p class="section-intro contact__say" data-reveal="up" data-reveal-delay="0.1">
                Have a product, a dashboard, an API or a tool that needs building?
                Tell us what you're working on — we read every message and reply personally.
              </p>
            </header>

            <a class="contact__email link-underline mono"
               href="mailto:mohamed111.me111@gmail.com"
               data-reveal="up" data-reveal-delay="0.16"
               data-magnetic="0.2">mohamed111.me111@gmail.com</a>

            <p class="contact__alt mono" data-reveal="up" data-reveal-delay="0.2">
              <a class="link-underline" href="tel:+201090166589">+20 109 016 6589</a>
              <span class="text-faint" aria-hidden="true"> · </span>
              <a class="link-underline" href="mailto:katchan111.me111@gmail.com">katchan111.me111@gmail.com</a>
            </p>

            <p class="contact__avail" data-reveal="up" data-reveal-delay="0.22">
              <span class="status">
                <span class="dot" aria-hidden="true"></span>
                Available anytime · Cairo / Menofia, Egypt
              </span>
            </p>

            <nav class="contact__socials" aria-label="Social profiles"
                 data-reveal="up" data-reveal-delay="0.28">
              <a class="contact__social" href="https://github.com/Mohamed-Ehab-Teama" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49
                           0-.24-.01-.88-.01-1.73-2.78.62-3.37-1.37-3.37-1.37-.45-1.18-1.11-1.49-1.11-1.49
                           -.91-.64.07-.63.07-.63 1 .07 1.53 1.06 1.53 1.06.89 1.57 2.34 1.12 2.91.86
                           .09-.66.35-1.12.63-1.38-2.22-.26-4.56-1.14-4.56-5.06 0-1.12.39-2.03 1.03-2.75
                           -.1-.26-.45-1.3.1-2.7 0 0 .84-.28 2.75 1.05A9.3 9.3 0 0 1 12 6.84c.85 0 1.71.12
                           2.51.34 1.91-1.33 2.75-1.05 2.75-1.05.55 1.4.2 2.44.1 2.7.64.72 1.03 1.63 1.03
                           2.75 0 3.93-2.34 4.8-4.57 5.05.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.81 0
                           .27.18.59.69.49A10.02 10.02 0 0 0 22 12.25C22 6.58 17.52 2 12 2z"/>
                </svg>
                <span>GitHub</span>
              </a>
              <a class="contact__social" href="https://www.linkedin.com/in/mohamed-ehab-teama-267154250/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14
                           2.94v5.67H9.35V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37
                           4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zM7.12
                           20.45H3.55V9h3.57v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.22.79
                           24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/>
                </svg>
                <span>LinkedIn</span>
              </a>
            </nav>
          </div>

          <!-- RIGHT — front-end-only contact form -->
          <div class="contact__panel" data-reveal="scale" data-reveal-delay="0.12">
            <form class="contact__form" novalidate aria-describedby="contact-status">
              <div class="contact__form-head mono" aria-hidden="true">
                <span class="term-prompt">~/contact</span>
                <span class="term-out">new message</span>
              </div>

              <div class="field contact__field">
                <input type="text" id="contact-name" name="name"
                       placeholder=" " autocomplete="name"
                       aria-describedby="contact-status" required>
                <label for="contact-name">Name</label>
              </div>

              <div class="field contact__field">
                <input type="email" id="contact-email" name="email"
                       placeholder=" " autocomplete="email" inputmode="email"
                       aria-describedby="contact-status" required>
                <label for="contact-email">Email</label>
              </div>

              <div class="field contact__field">
                <textarea id="contact-message" name="message"
                          placeholder=" " rows="5"
                          aria-describedby="contact-status" required></textarea>
                <label for="contact-message">Message</label>
              </div>

              <button type="submit" class="contact__submit btn btn--primary" data-magnetic="0.25" aria-label="Send message">
                $ send --message <span class="arrow" aria-hidden="true">&rarr;</span>
              </button>

              <div class="contact__status" id="contact-status" role="status" aria-live="polite"></div>
            </form>
          </div>
        </div>
      </section>
