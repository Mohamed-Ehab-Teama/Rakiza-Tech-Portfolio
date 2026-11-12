/* ==========================================================================
   preloader.js — terminal BOOT sequence overlay
   Types a boot log, runs a 0→100 counter + progress bar, then performs a
   staged curtain reveal and releases the page. Degrades gracefully.
   Contract: export function init(); guards on missing gsap / reduced motion.
   ========================================================================== */

export function init() {
  const root = document.getElementById("preloader");
  if (!root) {
    // Pages without the boot animation (e.g. /projects): release at once so
    // code waiting for "preloader:done" still runs.
    document.body.classList.add("loaded");
    window.dispatchEvent(new CustomEvent("preloader:done"));
    return;
  }

  const app = window.app || {};
  const gsap = window.gsap;
  const reduced = !!app.reduced;

  const logEl = root.querySelector("#preloader-log");
  const countEl = root.querySelector("#preloader-count");
  const fillEl = root.querySelector("#preloader-fill");
  const innerEl = root.querySelector(".preloader__inner");
  const curtains = Array.from(root.querySelectorAll(".preloader__curtains > span"));

  let finished = false;

  /* Release the page exactly once. */
  const release = () => {
    if (finished) return;
    finished = true;
    root.style.display = "none";
    if (app.startScroll) app.startScroll();
    document.body.classList.add("loaded");
    window.dispatchEvent(new CustomEvent("preloader:done"));
  };

  // ---- Fast path: reduced motion or no GSAP → show final state, release ----
  if (reduced || !gsap) {
    release();
    return;
  }

  if (app.stopScroll) app.stopScroll();

  /* The HTML ships the final boot text (sections/preloader.blade.php) as a
     no-JS fallback. For the animated path we copy those lines, clear the log
     and retype them line-by-line so the typing is the star. */
  const lines = logEl
    ? Array.from(logEl.querySelectorAll(".preloader__line")).map((p) => ({ html: p.innerHTML }))
    : [];

  if (logEl) logEl.innerHTML = "";
  // Reset the counter/bar so the no-JS "100" fallback doesn't flash first.
  if (countEl) countEl.textContent = "0";
  if (fillEl) fillEl.style.width = "0%";

  /* Reveal one whole line at a time. Building each line as a complete node
     (rather than char-by-char innerHTML) keeps the markup/spans intact and
     avoids layout thrash. A blinking cursor trails the most recent line. */
  const TYPE_TOTAL = 1.2; // seconds budget for the boot log (snappy)
  const step = lines.length ? TYPE_TOTAL / lines.length : 0;

  const typeTl = gsap.timeline();
  lines.forEach((line, i) => {
    typeTl.call(
      () => {
        if (!logEl) return;
        // Drop the cursor from the previous line.
        const prevCursor = logEl.querySelector(".preloader__cursor");
        if (prevCursor) prevCursor.remove();

        const p = document.createElement("p");
        p.className = "preloader__line";
        p.innerHTML = line.html;

        const cursor = document.createElement("span");
        cursor.className = "preloader__cursor";
        cursor.setAttribute("aria-hidden", "true");
        p.appendChild(cursor);

        logEl.appendChild(p);
      },
      null,
      i * step
    );
  });
  // Remove the trailing cursor a beat after the last line lands.
  typeTl.call(
    () => {
      const c = logEl && logEl.querySelector(".preloader__cursor");
      if (c) c.remove();
    },
    null,
    "+=0.12"
  );

  /* Counter + progress bar run alongside the typing. */
  const progress = { n: 0 };
  const counterTween = gsap.to(progress, {
    n: 100,
    duration: Math.max(TYPE_TOTAL, 1.2),
    ease: "power2.inOut",
    onUpdate() {
      const v = Math.round(progress.n);
      if (countEl) countEl.textContent = String(v);
      if (fillEl) fillEl.style.width = v + "%";
    },
  });

  /* Master timeline: wait for the longer of (typing, counter), then exit. */
  const master = gsap.timeline({ onComplete: release });
  master.add(typeTl, 0);
  master.add(counterTween, 0);

  // Brief hold so "ready." + 100% can register before the reveal.
  master.to({}, { duration: 0.15 });

  // Fade the inner content out…
  if (innerEl) {
    master.to(innerEl, {
      autoAlpha: 0,
      y: -18,
      duration: 0.3,
      ease: "power3.in",
    });
  }

  // …then slide the curtains up in a staggered wipe (premium reveal).
  if (curtains.length) {
    master.to(
      curtains,
      {
        yPercent: -100,
        duration: 0.5,
        ease: "power4.inOut",
        stagger: 0.06,
      },
      ">-0.05"
    );
  } else {
    // No curtains in markup → slide the whole overlay up instead.
    master.to(root, { yPercent: -100, duration: 0.5, ease: "power4.inOut" });
  }

  /* Safety net: if anything stalls, force-finish. Guarded by `finished`. */
  setTimeout(() => {
    if (finished) return;
    try { master.kill(); } catch (e) { /* noop */ }
    release();
  }, 4500);
}
