/* ==========================================================================
   hero.js — landing hero.
   • Code-rain <canvas> background (DPR-aware, fps-capped, reduced-motion safe)
   • Staged intro timeline triggered on the preloader:done event (with a
     guarded timeout fallback so it always runs once)
   • Rotating typewriter strip into #hero-type
   • Subtle "live" coordinate ticker in the corner meta
   ES module — only export is init().
   ========================================================================== */

export function init() {
  const root = document.getElementById("hero");
  if (!root) return;

  const app = window.app || {};
  const gsap = window.gsap;
  const reduced = !!app.reduced;

  const canvas = root.querySelector(".hero__canvas");
  const typeEl = root.querySelector("#hero-type");
  const coordsEl = root.querySelector("#hero-coords");
  const titleEl = root.querySelector(".hero__title");

  /* ----------------------------------------------------------------------
     1. CODE-RAIN CANVAS
     ---------------------------------------------------------------------- */
  if (canvas && canvas.getContext) startCodeRain(canvas, reduced);

  /* ----------------------------------------------------------------------
     2. TYPEWRITER
     ---------------------------------------------------------------------- */
  if (typeEl) startTypewriter(typeEl, reduced);

  /* ----------------------------------------------------------------------
     3. LIVE COORDINATE TICKER (skipped under reduced motion)
     ---------------------------------------------------------------------- */
  if (coordsEl && !reduced) startCoords(coordsEl);

  /* ----------------------------------------------------------------------
     4. INTRO TIMELINE
     ---------------------------------------------------------------------- */
  let introPlayed = false;

  const playIntro = () => {
    if (introPlayed) return;
    introPlayed = true;

    // No gsap or reduced motion → content is already visible; nothing to do.
    if (!gsap || reduced) return;

    const status = root.querySelector(".hero__intro .status");
    const id = root.querySelector(".hero__id");
    const lead = root.querySelector(".hero__lead");
    const typed = root.querySelector(".hero__typed");
    const cta = root.querySelectorAll(".hero__cta .btn");
    const stats = root.querySelectorAll(".hero__stat");
    const meta = root.querySelector(".hero__meta");
    const scroll = root.querySelector(".hero__scroll");

    // Split the title into lines for a staged reveal (fallback: whole node).
    let titleTargets = [titleEl];
    if (window.SplitType && titleEl) {
      try {
        const split = new window.SplitType(titleEl, {
          types: "lines",
          lineClass: "hero__title-line",
        });
        if (split.lines && split.lines.length) titleTargets = split.lines;
      } catch (_) { /* keep fallback */ }
    }

    // Hidden initial states (only now that we know gsap exists).
    gsap.set([status, id, lead, typed].filter(Boolean), { autoAlpha: 0, y: 24 });
    gsap.set(titleTargets, { yPercent: 115, autoAlpha: 0 });
    if (cta.length) gsap.set(cta, { autoAlpha: 0, y: 20 });
    if (stats.length) gsap.set(stats, { autoAlpha: 0, y: 20 });
    gsap.set([meta, scroll].filter(Boolean), { autoAlpha: 0 });

    const tl = gsap.timeline({ defaults: { ease: "power4.out" } });
    tl.to([status, id].filter(Boolean), { autoAlpha: 1, y: 0, duration: 0.7, stagger: 0.08 })
      .to(titleTargets, { yPercent: 0, autoAlpha: 1, duration: 1.1, stagger: 0.1 }, "-=0.35")
      .to(lead, { autoAlpha: 1, y: 0, duration: 0.8 }, "-=0.7")
      .to(typed, { autoAlpha: 1, y: 0, duration: 0.6 }, "-=0.55");

    if (cta.length) {
      tl.to(cta, { autoAlpha: 1, y: 0, duration: 0.6, stagger: 0.08 }, "-=0.4");
    }
    if (stats.length) {
      tl.to(stats, { autoAlpha: 1, y: 0, duration: 0.6, stagger: 0.06 }, "-=0.4");
    }
    tl.to([meta, scroll].filter(Boolean), { autoAlpha: 1, duration: 0.8 }, "-=0.5");
  };

  window.addEventListener("preloader:done", playIntro, { once: true });
  // Fallback: never let a missing/slow preloader strand the hero hidden.
  setTimeout(playIntro, 3800);
}

/* ==========================================================================
   Code-rain canvas
   ========================================================================== */
function startCodeRain(canvas, reduced) {
  const ctx = canvas.getContext("2d", { alpha: true, desynchronized: true });
  if (!ctx) return;

  const GLYPHS = ["0", "1", "{", "}", "<", ">", "/", ";", "$", "#",
                  "( )", "=>", "[]", "=", "+", "*", ".", "_", "::", "λ"];

  let dpr = Math.min(window.devicePixelRatio || 1, 2);
  let w = 0, h = 0;
  let cols = 0;
  let drops = [];
  let fontSize = 16;

  const accent = "110, 231, 255";   // --accent
  const term = "74, 222, 128";      // --term

  const resize = () => {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    const rect = canvas.getBoundingClientRect();
    w = Math.max(1, Math.floor(rect.width));
    h = Math.max(1, Math.floor(rect.height));
    canvas.width = Math.floor(w * dpr);
    canvas.height = Math.floor(h * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    fontSize = Math.max(13, Math.round(w / 110));
    const gap = fontSize * 1.7;
    cols = Math.ceil(w / gap);
    drops = new Array(cols).fill(0).map(() => ({
      x: 0,
      y: Math.random() * -h,
      speed: 0.6 + Math.random() * 1.1,
      tint: Math.random() < 0.22 ? term : accent,
    }));
    drops.forEach((d, i) => { d.x = i * gap + gap * 0.25; });
  };

  resize();

  /* Reduced motion: paint one faint static field, then stop. */
  if (reduced) {
    const paintStatic = () => {
      ctx.clearRect(0, 0, w, h);
      ctx.font = `${fontSize}px "JetBrains Mono", monospace`;
      ctx.textBaseline = "top";
      const gap = fontSize * 1.7;
      for (let x = 0; x < w; x += gap) {
        for (let y = 0; y < h; y += gap * 1.4) {
          if (Math.random() > 0.5) continue;
          const tint = Math.random() < 0.22 ? term : accent;
          ctx.fillStyle = `rgba(${tint}, ${0.05 + Math.random() * 0.05})`;
          ctx.fillText(rand(GLYPHS), x + gap * 0.25, y);
        }
      }
    };
    paintStatic();
    let rT;
    window.addEventListener("resize", () => {
      clearTimeout(rT);
      rT = setTimeout(() => { resize(); paintStatic(); }, 200);
    });
    return;
  }

  /* Animated rain — capped ~30fps to stay light on the CPU. */
  let raf = 0;
  let last = 0;
  const frameMs = 1000 / 30;
  let running = true;

  const draw = (now) => {
    if (!running) return;
    raf = requestAnimationFrame(draw);
    if (now - last < frameMs) return;
    last = now;

    // Fade the previous frame for trailing tails.
    ctx.fillStyle = "rgba(8, 9, 13, 0.16)";
    ctx.fillRect(0, 0, w, h);

    ctx.font = `${fontSize}px "JetBrains Mono", monospace`;
    ctx.textBaseline = "top";

    const step = fontSize * 1.25;
    for (let i = 0; i < drops.length; i++) {
      const d = drops[i];
      const glyph = rand(GLYPHS);

      // Bright leading glyph, dim trailing ones.
      ctx.fillStyle = `rgba(${d.tint}, 0.16)`;
      ctx.fillText(glyph, d.x, d.y);
      ctx.fillStyle = `rgba(${d.tint}, 0.05)`;
      ctx.fillText(rand(GLYPHS), d.x, d.y - step);

      d.y += step * d.speed;
      if (d.y > h + step) {
        d.y = -step * (2 + Math.random() * 8);
        d.speed = 0.6 + Math.random() * 1.1;
        d.tint = Math.random() < 0.22 ? term : accent;
      }
    }
  };

  raf = requestAnimationFrame(draw);

  // Pause when the hero scrolls out of view / tab hidden (save CPU).
  const setRunning = (on) => {
    if (on && !running) { running = true; last = 0; raf = requestAnimationFrame(draw); }
    else if (!on && running) { running = false; cancelAnimationFrame(raf); }
  };

  document.addEventListener("visibilitychange", () => setRunning(!document.hidden));

  if ("IntersectionObserver" in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => setRunning(e.isIntersecting && !document.hidden));
    }, { threshold: 0 });
    io.observe(canvas);
  }

  let resizeT;
  window.addEventListener("resize", () => {
    clearTimeout(resizeT);
    resizeT = setTimeout(resize, 180);
  });
}

/* ==========================================================================
   Typewriter
   ========================================================================== */
function startTypewriter(el, reduced) {
  const phrases = [
    "building Laravel REST APIs",
    "designing MySQL schemas",
    "shipping full-stack web apps",
    "writing clean, testable PHP",
    "automating with artisan & CLIs",
  ];

  if (reduced) {
    el.textContent = phrases[0];
    return;
  }

  let p = 0;     // phrase index
  let c = 0;     // char index
  let deleting = false;
  let timer = 0;

  const tick = () => {
    const full = phrases[p];
    c += deleting ? -1 : 1;
    el.textContent = full.slice(0, c);

    let delay = deleting ? 38 : 65;

    if (!deleting && c === full.length) {
      delay = 1700;            // hold the completed phrase
      deleting = true;
    } else if (deleting && c === 0) {
      deleting = false;
      p = (p + 1) % phrases.length;
      delay = 320;             // brief pause before next phrase
    }

    timer = window.setTimeout(tick, delay);
  };

  timer = window.setTimeout(tick, 700);

  // Pause typing while the tab is hidden.
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      clearTimeout(timer);
    } else {
      clearTimeout(timer);
      timer = window.setTimeout(tick, 400);
    }
  });
}

/* ==========================================================================
   Live-ish coordinate ticker (cosmetic)
   ========================================================================== */
function startCoords(el) {
  const baseLat = 30.0444;
  const baseLng = 31.2357;
  let id = 0;

  const render = () => {
    const lat = (baseLat + (Math.random() - 0.5) * 0.006).toFixed(2);
    const lng = (baseLng + (Math.random() - 0.5) * 0.006).toFixed(2);
    el.textContent = `lat ${lat} // lng ${lng}`;
    id = window.setTimeout(render, 2400);
  };
  render();

  document.addEventListener("visibilitychange", () => {
    clearTimeout(id);
    if (!document.hidden) id = window.setTimeout(render, 1000);
  });
}

/* -------------------------------------------------------------------------- */
function rand(arr) { return arr[(Math.random() * arr.length) | 0]; }
