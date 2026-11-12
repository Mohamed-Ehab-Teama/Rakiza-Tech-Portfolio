/* ==========================================================================
   brand.js — animated wordmark.
   Any element with [data-brand-type] cycles through its words (the JSON in
   data-brand-words, i.e. the company name and its Arabic name from Settings):
   types "Rakiza Tech", pauses, erases it, types "ركيزة تك", pauses, erases,
   and repeats — with the adjacent .nav__caret / .footer__caret giving the
   blinking-cursor feel.

   Arabic is written right-to-left, so while an Arabic word is on screen the
   element gets dir="rtl" and its .brand-type wrapper gets .is-rtl, which
   (in components.css) moves the caret to the left, where the next letter
   appears.
   Honors prefers-reduced-motion (renders the static brand "Rakiza Tech").
   ========================================================================== */
const FALLBACK_WORDS = ["Rakiza Tech", "ركيزة تك"];
const ARABIC = /[؀-ۿ]/; // any character from the Arabic Unicode block

/* The element's words from data-brand-words (JSON), or the fallback. */
function wordsOf(el) {
  try {
    const words = JSON.parse(el.dataset.brandWords || "[]").filter(Boolean);
    return words.length ? words : FALLBACK_WORDS;
  } catch (e) {
    return FALLBACK_WORDS;
  }
}

export function initBrand() {
  const els = Array.from(document.querySelectorAll("[data-brand-type]"));
  if (!els.length) return;

  const reduced = (window.app && window.app.reduced) ||
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  els.forEach((el, offset) => {
    const WORDS = wordsOf(el);
    if (reduced || WORDS.length === 1) { el.textContent = WORDS[0]; return; }

    const wrapper = el.closest(".brand-type");
    let wordIndex = offset % WORDS.length;
    let chars = 0;
    let deleting = false;

    const tick = () => {
      const word = WORDS[wordIndex];
      const rtl = ARABIC.test(word);
      el.dir = rtl ? "rtl" : "ltr";
      if (wrapper) wrapper.classList.toggle("is-rtl", rtl);
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
