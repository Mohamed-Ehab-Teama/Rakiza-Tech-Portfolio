/* ==========================================================================
   footer.js — live local-time clock in the footer bottom bar.

   The footer is fully functional without JS (links, CTA, back-to-top all use
   the global anchor handler). This module only powers the live clock at
   #footer-clock, updating it once per second. This is not animation, so it
   runs even under prefers-reduced-motion.

   Contract: ES module exporting init(); called once on DOMContentLoaded.
   ========================================================================== */

export function init() {
  const root = document.getElementById("footer");
  if (!root) return;

  const clock = document.getElementById("footer-clock");
  if (!clock) return; // nothing dynamic to do — static footer still works.

  // Format HH:MM:SS in the visitor's local time, 24h clock.
  const fmt = (() => {
    try {
      const f = new Intl.DateTimeFormat(undefined, {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      });
      return (d) => f.format(d);
    } catch (_) {
      // Fallback if Intl options are unsupported.
      return (d) => d.toLocaleTimeString();
    }
  })();

  // Short local timezone abbreviation (e.g. "GMT+2"), best-effort.
  let tzLabel = "";
  try {
    const parts = new Intl.DateTimeFormat(undefined, {
      hour: "2-digit",
      timeZoneName: "short",
    }).formatToParts(new Date());
    const tz = parts.find((p) => p.type === "timeZoneName");
    if (tz && tz.value) tzLabel = tz.value;
  } catch (_) {
    /* ignore — label stays empty */
  }

  let lastText = "";
  const tick = () => {
    const text = fmt(new Date());
    if (text !== lastText) {
      lastText = text;
      clock.textContent = text;
    }
  };

  // Reflect timezone in the accessible label / title without animating it.
  if (tzLabel) {
    clock.setAttribute("title", `Local time (${tzLabel})`);
    clock.setAttribute("aria-label", `Local time ${tzLabel}`);
  }

  tick(); // paint immediately so there's no "--:--:--" flash.
  const id = window.setInterval(tick, 1000);

  // Be tidy if the page lifecycle tears things down (bfcache / SPA-ish nav).
  window.addEventListener(
    "pagehide",
    () => window.clearInterval(id),
    { once: true }
  );
}
