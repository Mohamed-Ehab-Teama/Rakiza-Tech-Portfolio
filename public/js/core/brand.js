/* ==========================================================================
   brand.js — animated wordmark.
   Any element with [data-brand-type] cycles: types "Mohamed Ehab Teama",
   pauses, erases it, types "Teamora", pauses, erases, and repeats — with the
   adjacent .nav__caret / .footer__caret giving the blinking-cursor feel.
   Honors prefers-reduced-motion (renders the static brand "Teamora").
   ========================================================================== */
export function initBrand() {
  const els = Array.from(document.querySelectorAll("[data-brand-type]"));
  if (!els.length) return;

  const WORDS = ["Mohamed Ehab Teama", "Teamora"];
  const reduced = (window.app && window.app.reduced) ||
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  els.forEach((el, offset) => {
    if (reduced) { el.textContent = "Teamora"; return; }

    let wordIndex = offset % WORDS.length;
    let chars = 0;
    let deleting = false;

    const tick = () => {
      const word = WORDS[wordIndex];
      el.textContent = word.slice(0, chars);

      let delay;
      if (!deleting) {
        if (chars < word.length) { chars += 1; delay = 95; }
        else { deleting = true; delay = 1700; }            // hold full word
      } else {
        if (chars > 0) { chars -= 1; delay = 45; }
        else { deleting = false; wordIndex = (wordIndex + 1) % WORDS.length; delay = 400; }
      }
      window.setTimeout(tick, delay);
    };

    tick();
  });
}
