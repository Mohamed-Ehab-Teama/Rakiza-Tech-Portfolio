/* ==========================================================================
   main.js — application orchestrator
   Boots smooth scroll, cursor, every section module, the reveal engine,
   and finally the preloader. Loaded as <script type="module"> after the
   GSAP / Lenis / SplitType CDN globals.
   ========================================================================== */
import { initSmoothScroll, getLenis, stopScroll, startScroll } from "./core/smooth-scroll.js";
import { initCursor } from "./core/cursor.js";
import { initBrand } from "./core/brand.js";
import { loadIncludes } from "./core/include.js";
import { initReveals, initCounters } from "./core/reveal.js";
import { prefersReducedMotion } from "./core/utils.js";

import { init as initNav }       from "./sections/nav.js";
import { init as initHero }      from "./sections/hero.js";
import { init as initAbout }     from "./sections/about.js";
import { init as initServices }  from "./sections/services.js";
import { init as initWork }      from "./sections/work.js";
import { init as initMetrics }   from "./sections/metrics.js";
import { init as initTerminal }  from "./sections/terminal.js";
import { init as initStack }     from "./sections/stack.js";
import { init as initContact }   from "./sections/contact.js";
import { init as initFooter }    from "./sections/footer.js";
import { init as initPreloader } from "./sections/preloader.js";

document.documentElement.classList.add("js");

function boot() {
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  if (gsap && ScrollTrigger) {
    gsap.registerPlugin(ScrollTrigger);
    // Stop mobile URL-bar resizes from re-pinning (#work) every scroll, and
    // throttle redundant onUpdate/onEnter callbacks during the scrub.
    ScrollTrigger.config({ ignoreMobileResize: true, limitCallbacks: true });
  }

  // Shared app API exposed to section modules (keeps them decoupled).
  window.app = {
    gsap,
    ScrollTrigger,
    get lenis() { return getLenis(); },
    stopScroll,
    startScroll,
    reduced: prefersReducedMotion(),
  };

  initSmoothScroll();
  initCursor();
  initBrand();

  // Each section guarded so one failure never breaks the rest.
  [
    ["nav", initNav], ["hero", initHero], ["about", initAbout],
    ["services", initServices], ["work", initWork],
    ["metrics", initMetrics], ["terminal", initTerminal],
    ["stack", initStack], ["contact", initContact], ["footer", initFooter],
  ].forEach(([name, fn]) => {
    try { fn(); } catch (err) { console.error(`[section:${name}]`, err); }
  });

  initReveals();
  initCounters();

  // Preloader last: it lives on top and releases the page when done.
  try { initPreloader(); }
  catch (err) {
    console.error("[preloader]", err);
    document.body.classList.add("loaded");
    window.dispatchEvent(new CustomEvent("preloader:done"));
  }

  // Recalculate trigger positions once fonts/images settle.
  const refresh = () => ScrollTrigger && ScrollTrigger.refresh();
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(refresh);
  window.addEventListener("load", refresh);
  setTimeout(refresh, 1200);
}

// Pull the html/<section>.html partials into their placeholders, THEN boot so
// every section module finds its DOM. The inline preloader covers the load.
async function start() {
  await loadIncludes();
  boot();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", start);
} else {
  start();
}
