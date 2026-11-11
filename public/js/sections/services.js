/* ==========================================================================
   services.js — pointer spotlight + in-view motif activation.

   The bento motifs are CSS-driven (hover + .is-active), so the section is
   fully functional without JS. This module only:
     1. feeds each .card--spotlight the pointer position via --mx/--my so the
        shared radial glow follows the cursor;
     2. (when motion is allowed) toggles an .is-active class as each card
        scrolls into view, starting its idle motif loop, and removing it on
        exit so loops don't run off-screen.

   Contract: ES module exporting init(); called once on DOMContentLoaded.
   ========================================================================== */

export function init() {
  const root = document.getElementById("services");
  if (!root) return;

  const cards = Array.from(root.querySelectorAll(".services__card"));
  if (!cards.length) return;

  const app = window.app || {};
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const reduced = !!app.reduced;

  /* ---- 1. Pointer spotlight (cheap, no GSAP dependency) ----------------- */
  // Skip on coarse/touch pointers where hover spotlight is meaningless.
  const fine = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  if (fine) {
    cards.forEach((card) => {
      let raf = 0;
      let lastE = null;
      const move = (e) => {
        lastE = e;
        if (raf) return;
        raf = requestAnimationFrame(() => {
          raf = 0;
          const r = card.getBoundingClientRect();
          card.style.setProperty("--mx", `${lastE.clientX - r.left}px`);
          card.style.setProperty("--my", `${lastE.clientY - r.top}px`);
        });
      };
      card.addEventListener("pointermove", move, { passive: true });
      card.addEventListener("pointerleave", () => {
        if (raf) { cancelAnimationFrame(raf); raf = 0; }
      });
    });
  }

  /* ---- 2. In-view motif activation ------------------------------------- */
  // Without GSAP or under reduced motion, render the final/idle state and
  // let CSS hover handle any motion. Mark active so static motifs look set.
  if (!gsap || !ScrollTrigger || reduced) {
    if (reduced) cards.forEach((c) => c.classList.add("is-active"));
    return;
  }

  cards.forEach((card) => {
    ScrollTrigger.create({
      trigger: card,
      start: "top 85%",
      end: "bottom 15%",
      onEnter:     () => card.classList.add("is-active"),
      onEnterBack: () => card.classList.add("is-active"),
      onLeave:     () => card.classList.remove("is-active"),
      onLeaveBack: () => card.classList.remove("is-active"),
    });
  });
}
