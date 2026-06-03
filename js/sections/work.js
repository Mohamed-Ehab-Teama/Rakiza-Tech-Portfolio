/* ==========================================================================
   projects.js — #work : pinned horizontal-scroll showcase (desktop only)
   Contract: export function init(); called once on DOMContentLoaded.
   Globals: window.gsap, window.ScrollTrigger, window.app
   - Desktop (>=860px, motion ok): pin section, translate track on scroll,
     drive the "NN / 05" progress counter, add gentle per-card parallax.
   - Small screens / reduced motion: do nothing — CSS shows a native
     horizontal strip (tablet) / vertical stack (phone). Fully reachable.
   ========================================================================== */

export function init() {
  const section = document.getElementById("work");
  if (!section) return;

  const track = document.getElementById("work-track");
  const progressEl = document.getElementById("work-progress");
  if (!track) return;

  const items = Array.from(track.querySelectorAll(".work__item"));
  const total = items.length;
  const curEl = progressEl ? progressEl.querySelector(".work__progress-cur") : null;
  const totalEl = progressEl ? progressEl.querySelector(".work__progress-total") : null;

  const pad2 = (n) => String(n).padStart(2, "0");
  if (totalEl) totalEl.textContent = pad2(total);
  if (curEl) curEl.textContent = pad2(1);

  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;

  // No GSAP → CSS fallback already shows everything (native scroll strip).
  if (!gsap || !ScrollTrigger || !gsap.matchMedia) return;

  const reduced = window.app && window.app.reduced;

  const mm = gsap.matchMedia();

  mm.add(
    {
      // Only build the pin on real desktop widths with motion allowed.
      isDesktop: "(min-width: 860px)",
      isReduced: "(prefers-reduced-motion: reduce)",
    },
    (ctx) => {
      const { isDesktop, isReduced } = ctx.conditions;
      if (!isDesktop || isReduced || reduced) return; // fallback handles rest

      const viewport = track.parentElement;
      const getAmount = () =>
        Math.max(0, track.scrollWidth - viewport.clientWidth);

      // Nothing to scroll horizontally → skip pin entirely.
      if (getAmount() <= 0) return;

      section.classList.add("is-pinned");

      let lastIndex = 0;
      const setProgress = (progress) => {
        if (!curEl) return;
        // progress 0..1 → card index 1..total
        const idx = Math.min(total, Math.max(1, Math.round(progress * (total - 1)) + 1));
        if (idx !== lastIndex) {
          lastIndex = idx;
          curEl.textContent = pad2(idx);
        }
      };

      const tween = gsap.to(track, {
        x: () => -getAmount(),
        ease: "none",
        scrollTrigger: {
          trigger: section,
          start: "top top",
          end: () => "+=" + getAmount(),
          pin: true,
          scrub: 0.7,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          onUpdate: (self) => setProgress(self.progress),
          onRefresh: (self) => setProgress(self.progress),
        },
      });

      // Gentle per-card parallax on the visual mock as the track moves.
      const parallaxTweens = items.map((item) => {
        const visual = item.querySelector(".work__mock");
        if (!visual) return null;
        return gsap.fromTo(
          visual,
          { xPercent: -4 },
          {
            xPercent: 4,
            ease: "none",
            scrollTrigger: {
              trigger: item,
              containerAnimation: tween,
              start: "left right",
              end: "right left",
              scrub: true,
              invalidateOnRefresh: true,
            },
          }
        );
      });

      // Cleanup when this media query no longer matches.
      return () => {
        section.classList.remove("is-pinned");
        if (tween.scrollTrigger) tween.scrollTrigger.kill();
        tween.kill();
        parallaxTweens.forEach((t) => {
          if (!t) return;
          if (t.scrollTrigger) t.scrollTrigger.kill();
          t.kill();
        });
        gsap.set(track, { clearProps: "transform" });
        items.forEach((item) => {
          const v = item.querySelector(".work__mock");
          if (v) gsap.set(v, { clearProps: "transform" });
        });
        if (curEl) curEl.textContent = pad2(1);
      };
    }
  );

  // Spotlight (--mx/--my) follow for .card--spotlight visuals.
  // rAF-coalesced and gated to fine pointers so it never runs on touch and
  // never reads layout more than once per frame (matters during the scrub).
  const finePointer = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  if (!reduced && finePointer) {
    items.forEach((card) => {
      let rafId = 0;
      let lastE = null;
      card.addEventListener("pointermove", (e) => {
        lastE = e;
        if (rafId) return;
        rafId = requestAnimationFrame(() => {
          rafId = 0;
          const r = card.getBoundingClientRect();
          card.style.setProperty("--mx", `${lastE.clientX - r.left}px`);
          card.style.setProperty("--my", `${lastE.clientY - r.top}px`);
        });
      });
    });
  }
}
