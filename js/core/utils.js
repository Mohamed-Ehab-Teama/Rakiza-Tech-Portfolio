/* ==========================================================================
   utils.js — tiny DOM / math helpers shared across modules
   ========================================================================== */

export const qs  = (sel, ctx = document) => ctx.querySelector(sel);
export const qsa = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

export const on = (el, ev, fn, opts) => {
  if (!el) return () => {};
  el.addEventListener(ev, fn, opts);
  return () => el.removeEventListener(ev, fn, opts);
};

export const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
export const lerp  = (a, b, t) => a + (b - a) * t;
export const mapRange = (v, inMin, inMax, outMin, outMax) =>
  outMin + ((v - inMin) * (outMax - outMin)) / (inMax - inMin);

export const wait = (ms) => new Promise((r) => setTimeout(r, ms));

export const prefersReducedMotion = () =>
  window.matchMedia("(prefers-reduced-motion: reduce)").matches;

export const isTouch = () =>
  window.matchMedia("(hover: none), (pointer: coarse)").matches;

export const escapeHtml = (str = "") =>
  str.replace(/[&<>"']/g, (c) => (
    { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]
  ));

/* Format an integer with thousands separators */
export const formatNumber = (n) => n.toLocaleString("en-US");

/* requestAnimationFrame helper that resolves after one frame */
export const nextFrame = () => new Promise((r) => requestAnimationFrame(() => r()));

/* Run fn once when an element first enters the viewport (cheap, no GSAP dep) */
export const onceVisible = (el, fn, rootMargin = "0px 0px -15% 0px") => {
  if (!el) return;
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) { fn(e.target); io.unobserve(e.target); }
    });
  }, { rootMargin, threshold: 0.15 });
  io.observe(el);
};
