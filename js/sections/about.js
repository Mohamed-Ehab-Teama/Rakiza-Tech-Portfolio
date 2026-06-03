/* ==========================================================================
   about.js — "01 ABOUT"
   Bespoke motion only: a one-shot typewriter that re-types the code card's
   string values the first time the card scrolls into view. Everything else
   (reveals, count-up stats) is handled by the global engines — do NOT touch.

   Contract: export function init(); called once on DOMContentLoaded.
   Must render a correct final state without JS and honor reduced motion.
   ========================================================================== */

export function init() {
  const root = document.getElementById("about");
  if (!root) return;

  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const reduced = !!(window.app && window.app.reduced);

  const types = Array.from(root.querySelectorAll(".about__type[data-type]"));
  if (!types.length) return;

  // No JS-anim path: ensure the literal final values are present and bail.
  // (HTML already ships the final text, so this is just belt-and-braces.)
  if (!gsap || !ScrollTrigger || reduced) {
    types.forEach((el) => {
      el.textContent = el.getAttribute("data-type");
      el.classList.remove("is-typing");
    });
    return;
  }

  // Sequentially type each value, then move to the next. One-shot.
  let started = false;

  const typeAll = () => {
    if (started) return;
    started = true;

    const queue = types.map((el) => ({
      el,
      full: el.getAttribute("data-type") || el.textContent,
    }));

    // Start every value blank so the retype reads as "rendering".
    queue.forEach(({ el }) => {
      el.textContent = "";
    });

    const speedPerChar = 0.022; // seconds
    const gap = 0.12;           // pause between values
    let at = 0;

    const tl = gsap.timeline({ defaults: { ease: "none" } });

    queue.forEach(({ el, full }) => {
      const state = { n: 0 };
      const dur = Math.max(0.18, full.length * speedPerChar);

      tl.call(() => el.classList.add("is-typing"), null, at);
      tl.to(
        state,
        {
          n: full.length,
          duration: dur,
          onUpdate: () => {
            el.textContent = full.slice(0, Math.round(state.n));
          },
        },
        at
      );
      tl.call(
        () => {
          el.textContent = full; // guarantee exact final text
          el.classList.remove("is-typing");
        },
        null,
        at + dur
      );

      at += dur + gap;
    });

    return tl;
  };

  ScrollTrigger.create({
    trigger: root.querySelector(".about__card") || root,
    start: "top 78%",
    once: true,
    onEnter: typeAll,
  });

  // If the section is already in view at load (e.g. deep link), run it.
  const card = root.querySelector(".about__card");
  if (card) {
    const rect = card.getBoundingClientRect();
    if (rect.top < window.innerHeight * 0.78 && rect.bottom > 0) typeAll();
  }
}
