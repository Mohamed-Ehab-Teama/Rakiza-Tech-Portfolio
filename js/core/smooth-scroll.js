/* ==========================================================================
   smooth-scroll.js — Lenis smooth scrolling + GSAP/ScrollTrigger sync,
   scroll-progress bar, and anchor navigation.
   Globals expected: window.Lenis, window.gsap, window.ScrollTrigger
   ========================================================================== */
import { qs, qsa, prefersReducedMotion } from "./utils.js";

let lenis = null;

export function initSmoothScroll() {
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const progressBar = qs(".scroll-progress");
  const reduced = prefersReducedMotion();

  const setProgress = (p) => {
    if (progressBar) progressBar.style.transform = `scaleX(${p})`;
  };

  if (!window.Lenis || reduced) {
    // Fallback: native scroll + progress only.
    const onScroll = () => {
      const h = document.documentElement;
      const max = h.scrollHeight - h.clientHeight;
      setProgress(max > 0 ? h.scrollTop / max : 0);
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
    bindAnchors(null);
    if (ScrollTrigger) ScrollTrigger.refresh();
    return null;
  }

  lenis = new window.Lenis({
    lerp: 0.12,                    // a touch snappier while staying smooth
    wheelMultiplier: 1.1,          // default feels slightly under-powered
    smoothWheel: true,
    syncTouch: false,
    gestureOrientation: "vertical", // ignore horizontal trackpad gestures
  });
  window.lenis = lenis;

  lenis.on("scroll", (e) => {
    setProgress(e.progress || 0);
    if (ScrollTrigger) ScrollTrigger.update();
  });

  if (gsap) {
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
  } else {
    const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
  }

  bindAnchors(lenis);
  return lenis;
}

/* In-page anchor links (nav + buttons) use [data-scroll-to="#id"] or href="#id" */
function bindAnchors(lenisInstance) {
  qsa('[data-scroll-to], a[href^="#"]').forEach((el) => {
    const target = el.dataset.scrollTo || el.getAttribute("href");
    if (!target || target === "#") return;
    el.addEventListener("click", (e) => {
      const node = document.querySelector(target);
      if (!node) return;
      e.preventDefault();
      if (lenisInstance) {
        lenisInstance.scrollTo(node, {
          offset: -10,
          duration: 1.2,
          easing: (t) => 1 - Math.pow(1 - t, 3), // cubic ease-out, lands softly
        });
      } else {
        node.scrollIntoView({ behavior: "smooth" });
      }
      // Close mobile menu if open
      document.body.classList.remove("menu-open");
    });
  });
}

export const getLenis = () => lenis;
export const stopScroll = () => { lenis ? lenis.stop() : (document.body.style.overflow = "hidden"); };
export const startScroll = () => { lenis ? lenis.start() : (document.body.style.overflow = ""); };
