/* ==========================================================================
   skills.js — tech-stack section (#stack).

   The two marquees are 100% CSS (shared .marquee component), so they need no
   JS. The only bespoke motion here is the per-card proficiency bars: they grow
   from zero width as each card scrolls into view.

   Strategy for graceful degradation:
     - Bars are rendered at their FULL width by CSS (width: var(--lvl)).
     - We only animate when GSAP + ScrollTrigger exist AND motion is allowed.
       In that case we add `.js-anim` to each bar list (which CSS uses to reset
       the fill to scaleX(0)) and animate scaleX -> 1 on a one-shot trigger.
     - Without GSAP, or under reduced motion, `.js-anim` is never added, so the
       bars simply appear full. The section is fully functional with no JS.

   Contract: ES module exporting init(); called once on DOMContentLoaded.
   ========================================================================== */

export function init() {
  const root = document.getElementById("stack");
  if (!root) return;

  const app = window.app || {};
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const reduced = !!app.reduced;

  // Without GSAP/ScrollTrigger or under reduced motion: bars already render
  // full via CSS — nothing to do.
  if (!gsap || !ScrollTrigger || reduced) return;

  const barGroups = Array.from(root.querySelectorAll(".stack__bars"));
  if (!barGroups.length) return;

  barGroups.forEach((group) => {
    const fills = Array.from(group.querySelectorAll(".stack__bar-fill"));
    if (!fills.length) return;

    // Switch to animated state (CSS collapses fills to scaleX(0)).
    group.classList.add("js-anim");

    gsap.fromTo(
      fills,
      { scaleX: 0 },
      {
        scaleX: 1,
        duration: 1.1,
        ease: "power3.out",
        stagger: 0.12,
        scrollTrigger: {
          trigger: group,
          start: "top 88%",
          once: true,
        },
      }
    );
  });
}
