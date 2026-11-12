/* ==========================================================================
   reveal.js — generic scroll-reveal engine + count-up numbers.
   Drives any element with a [data-reveal] attribute and any [data-count].
   Globals expected: window.gsap, window.ScrollTrigger, window.SplitType
   Reveal types: up | fade | scale | left | right | lines | chars
   Modifiers (data-* on the element):
     data-reveal-delay   (seconds)
     data-reveal-stagger (seconds, for grouped children via data-reveal-group)

   Safety net ("catch-up"): a scroll trigger only fires when the page scrolls
   past it. Some jumps never fire it — e.g. the browser restoring the scroll
   position after a reload or Back, before the triggers exist. So whenever
   scrolling settles, after a layout refresh and when the preloader finishes,
   every animation that has not started yet is checked: if its element is
   already on screen it plays now, if it is above the screen it jumps to its
   end. Content can therefore never stay invisible (or a number stay at 0).
   ========================================================================== */
import { qsa, prefersReducedMotion } from "./utils.js";

/** Animations waiting for their element: { element, start(), finish(), started() } */
const waiting = [];
let catchUpInstalled = false;

function register(element, start, finish, started) {
  waiting.push({ element, start, finish, started, handled: false });
}

/* A GSAP tween controlled by a ScrollTrigger. */
function registerTween(element, tween) {
  register(element, () => tween.play(), () => tween.progress(1), () => tween.progress() > 0 || tween.isActive());
}

function catchUp() {
  const screenBottom = window.innerHeight * 0.92;
  waiting.forEach((item) => {
    if (item.handled) return;
    if (item.started()) { item.handled = true; return; }
    const rect = item.element.getBoundingClientRect();
    if (rect.bottom <= 0) { item.finish(); item.handled = true; }        // above the screen: show at once
    else if (rect.top < screenBottom) { item.start(); item.handled = true; } // on screen: animate now
  });
}

function installCatchUp() {
  const ScrollTrigger = window.ScrollTrigger;
  if (catchUpInstalled || !ScrollTrigger) return;
  catchUpInstalled = true;
  ScrollTrigger.addEventListener("scrollEnd", catchUp);
  ScrollTrigger.addEventListener("refresh", () => requestAnimationFrame(catchUp));
  window.addEventListener("preloader:done", () => requestAnimationFrame(catchUp));
  window.addEventListener("load", () => setTimeout(catchUp, 300));
}

export function initReveals() {
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const reduced = prefersReducedMotion();

  if (!gsap || !ScrollTrigger || reduced) {
    qsa("[data-reveal]").forEach((el) => el.classList.add("is-in"));
    return;
  }

  const start = "top 86%";

  // --- Text reveals (lines / chars) via SplitType -------------------------
  const textEls = qsa('[data-reveal="lines"], [data-reveal="chars"]');
  if (window.SplitType && textEls.length) {
    const splits = [];
    textEls.forEach((el) => {
      const isChars = el.dataset.reveal === "chars";
      const split = isChars
        ? new window.SplitType(el, { types: "chars", charClass: "st-char" })
        : new window.SplitType(el, { types: "lines, words", lineClass: "st-line", wordClass: "st-word" });
      splits.push(split);
      el.style.opacity = "1";
      const tween = gsap.from(isChars ? split.chars : split.words, {
        yPercent: isChars ? 110 : 120,
        opacity: 0,
        duration: isChars ? 0.7 : 1,
        ease: isChars ? "power3.out" : "power4.out",
        stagger: isChars ? 0.02 : 0.045,
        delay: parseFloat(el.dataset.revealDelay) || 0,
        scrollTrigger: { trigger: el, start },
      });
      registerTween(el, tween);
    });

    // Re-split on resize so line breaks track the new layout (debounced).
    let splitT;
    window.addEventListener("resize", () => {
      clearTimeout(splitT);
      splitT = setTimeout(() => {
        splits.forEach((s) => { try { s.split(); } catch (e) { /* noop */ } });
        ScrollTrigger.refresh();
      }, 220);
    });
  } else {
    // No SplitType → reveal the text directly; never leave it hidden.
    textEls.forEach((el) => { el.style.opacity = "1"; el.classList.add("is-in"); });
  }

  // --- Transform/fade reveals --------------------------------------------
  const simple = qsa("[data-reveal]").filter((el) => {
    const t = el.dataset.reveal;
    return t !== "lines" && t !== "chars";
  });

  simple.forEach((el) => {
    const delay = parseFloat(el.dataset.revealDelay) || 0;
    el.classList.add("is-in"); // ensures final state if trigger removed
    const tween = gsap.fromTo(
      el,
      revealFrom(el.dataset.reveal),
      {
        opacity: 1, x: 0, y: 0, scale: 1,
        duration: 1, ease: "power3.out", delay,
        scrollTrigger: { trigger: el, start, once: true },
      }
    );
    registerTween(el, tween);
  });

  // --- Grouped staggered reveals -----------------------------------------
  qsa("[data-reveal-group]").forEach((group) => {
    const children = Array.from(group.children);
    const stagger = parseFloat(group.dataset.revealStagger) || 0.1;
    const tween = gsap.fromTo(
      children,
      { opacity: 0, y: 44 },
      {
        opacity: 1, y: 0, duration: 0.9, ease: "power3.out",
        stagger: { each: stagger, ease: "power2.out" },
        scrollTrigger: { trigger: group, start, once: true },
      }
    );
    registerTween(group, tween);
  });

  installCatchUp();
}

function revealFrom(type) {
  switch (type) {
    case "fade":  return { opacity: 0 };
    case "scale": return { opacity: 0, scale: 0.94 };
    case "left":  return { opacity: 0, x: -40 };
    case "right": return { opacity: 0, x: 40 };
    case "up":
    default:      return { opacity: 0, y: 40 };
  }
}

/* --------------------------------------------------------------------------
   Count-up numbers: <span data-count="1280" data-count-suffix="+"></span>
   Optional: data-count-decimals, data-count-duration, data-count-prefix
   -------------------------------------------------------------------------- */
export function initCounters() {
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  qsa("[data-count]").forEach((el) => {
    const to = parseFloat(el.dataset.count);
    const decimals = parseInt(el.dataset.countDecimals || "0", 10);
    const dur = parseFloat(el.dataset.countDuration || "2");
    const prefix = el.dataset.countPrefix || "";
    const suffix = el.dataset.countSuffix || "";
    const render = (v) =>
      (el.textContent = prefix +
        Number(v).toLocaleString("en-US", {
          minimumFractionDigits: decimals,
          maximumFractionDigits: decimals,
        }) + suffix);

    if (!gsap || !ScrollTrigger || prefersReducedMotion()) { render(to); return; }

    const obj = { v: 0 };
    let started = false;
    const count = () => {
      if (started) return;
      started = true;
      gsap.to(obj, { v: to, duration: dur, ease: "expo.out", onUpdate: () => render(obj.v) });
    };
    const finish = () => { started = true; render(to); };

    render(0);
    ScrollTrigger.create({ trigger: el, start: "top 90%", once: true, onEnter: count });
    register(el, count, finish, () => started);
  });

  installCatchUp();
}
