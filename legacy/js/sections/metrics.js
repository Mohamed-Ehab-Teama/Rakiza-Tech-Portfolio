/* ==========================================================================
   dashboard.js — "04 DASHBOARD" / #metrics — "By the numbers."
   Bespoke chart entrance only. Count-up numbers (data-count) are owned by the
   global counter engine — we do NOT touch them. We DO draw:
     • KPI sparklines  → stroke draw-in
     • Bar chart       → bars grow from baseline (--grow 0→1)
     • Line/area chart → path draws via stroke-dashoffset + area fades in
     • Donut gauges    → ring stroke-dashoffset animates to target %
   All animations are one-shot on ScrollTrigger enter.

   Contract: ES module, only export is init(); called once on DOMContentLoaded.
   MUST render a correct final state without JS and honor reduced motion.
   ========================================================================== */

export function init() {
  const root = document.getElementById("metrics");
  if (!root) return;

  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const reduced = !!(window.app && window.app.reduced);

  const DONUT_R = 52;
  const CIRC = 2 * Math.PI * DONUT_R; // ≈ 326.726

  // Collect targets up front.
  const bars = Array.from(root.querySelectorAll(".metrics__bar"));
  const sparks = Array.from(root.querySelectorAll(".metrics__spark-line"));
  const lineStroke = root.querySelector(".metrics__line-stroke");
  const lineArea = root.querySelector(".metrics__line-area");
  const donutRings = Array.from(root.querySelectorAll(".metrics__donut-ring"));

  /* ----------------------------------------------------------------------
     Helper: pre-measure every donut so dasharray/offset are exact regardless
     of the static CSS estimate. We always normalise to the measured length.
     ---------------------------------------------------------------------- */
  const donutData = donutRings.map((ring) => {
    let len = CIRC;
    try {
      if (typeof ring.getTotalLength === "function") {
        const measured = ring.getTotalLength();
        if (measured && isFinite(measured)) len = measured;
      }
    } catch (_) {
      /* getTotalLength can throw if not yet laid out; fall back to CIRC */
    }
    const fig = ring.closest(".metrics__donut");
    const pct = fig ? parseFloat(fig.getAttribute("data-pct")) : NaN;
    const target = isFinite(pct) ? Math.max(0, Math.min(100, pct)) / 100 : 0;
    return { ring, len, target };
  });

  /* ----------------------------------------------------------------------
     Helper: prepare a stroke for draw-in (returns its length).
     ---------------------------------------------------------------------- */
  const measureStroke = (el) => {
    let len = 0;
    try {
      if (el && typeof el.getTotalLength === "function") {
        const m = el.getTotalLength();
        if (m && isFinite(m)) len = m;
      }
    } catch (_) {
      len = 0;
    }
    return len;
  };

  /* ----------------------------------------------------------------------
     FINAL-STATE renderer (no gsap / reduced motion / measurement failure).
     Bars: --grow already defaults to 1 in CSS. Sparks/line/area: visible by
     default. Donuts: set the exact measured dashoffset for crispness.
     ---------------------------------------------------------------------- */
  const renderFinal = () => {
    bars.forEach((b) => b.style.setProperty("--grow", "1"));

    sparks.forEach((s) => {
      s.style.strokeDasharray = "";
      s.style.strokeDashoffset = "0";
      s.style.opacity = "1";
    });

    if (lineStroke) {
      lineStroke.style.strokeDasharray = "";
      lineStroke.style.strokeDashoffset = "0";
    }
    if (lineArea) lineArea.style.opacity = "1";

    donutData.forEach(({ ring, len, target }) => {
      ring.style.strokeDasharray = String(len);
      ring.style.strokeDashoffset = String(len * (1 - target));
    });
  };

  if (!gsap || !ScrollTrigger || reduced) {
    renderFinal();
    return;
  }

  /* ----------------------------------------------------------------------
     ARMED initial state — set everything to "empty" so the reveal reads as a
     dashboard booting up. Guarded so a partial failure still looks correct.
     ---------------------------------------------------------------------- */
  bars.forEach((b) => b.style.setProperty("--grow", "0"));

  const sparkData = sparks.map((s) => {
    const len = measureStroke(s);
    if (len > 0) {
      s.style.strokeDasharray = String(len);
      s.style.strokeDashoffset = String(len);
    }
    return { el: s, len };
  });

  const lineLen = measureStroke(lineStroke);
  if (lineStroke && lineLen > 0) {
    lineStroke.style.strokeDasharray = String(lineLen);
    lineStroke.style.strokeDashoffset = String(lineLen);
  }
  if (lineArea) lineArea.style.opacity = "0";

  donutData.forEach(({ ring, len }) => {
    ring.style.strokeDasharray = String(len);
    ring.style.strokeDashoffset = String(len); // empty ring
  });

  /* ----------------------------------------------------------------------
     PLAY — one-shot timeline on first enter.
     ---------------------------------------------------------------------- */
  let played = false;

  const play = () => {
    if (played) return;
    played = true;

    const tl = gsap.timeline({ defaults: { ease: "power3.out" } });

    // Bars grow from baseline, left → right.
    if (bars.length) {
      tl.to(
        bars,
        {
          duration: 0.9,
          ease: "power4.out",
          stagger: 0.05,
          "--grow": 1,
        },
        0
      );
    }

    // KPI sparklines draw in.
    sparkData.forEach((sp, i) => {
      if (sp.len > 0) {
        tl.to(
          sp.el,
          { strokeDashoffset: 0, duration: 0.9, ease: "power2.out" },
          0.1 + i * 0.06
        );
      }
    });

    // Performance line draws in, area fades up underneath it.
    if (lineStroke && lineLen > 0) {
      tl.to(
        lineStroke,
        { strokeDashoffset: 0, duration: 1.4, ease: "power2.inOut" },
        0.15
      );
    }
    if (lineArea) {
      tl.to(lineArea, { opacity: 1, duration: 1.2, ease: "power2.out" }, 0.5);
    }

    // Donut rings sweep to their target percentage.
    donutData.forEach((d, i) => {
      tl.to(
        d.ring,
        {
          strokeDashoffset: d.len * (1 - d.target),
          duration: 1.2,
          ease: "power3.out",
        },
        0.2 + i * 0.12
      );
    });

    return tl;
  };

  ScrollTrigger.create({
    trigger: root.querySelector(".metrics__grid") || root,
    start: "top 80%",
    once: true,
    onEnter: play,
  });

  // Deep-link / already-in-view safety net.
  const grid = root.querySelector(".metrics__grid") || root;
  const rect = grid.getBoundingClientRect();
  if (rect.top < window.innerHeight * 0.8 && rect.bottom > 0) play();
}
