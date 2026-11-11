/* ==========================================================================
   cursor.js — custom cursor (dot + lagging ring) and magnetic buttons.
   Disabled automatically on touch / coarse pointers.
   All pointer handlers are rAF-coalesced + passive so high-Hz mice never flood
   the main thread, and layout reads are cached.
   Global expected: window.gsap
   ========================================================================== */
import { qsa, isTouch } from "./utils.js";

export function initCursor() {
  if (isTouch()) return;
  const gsap = window.gsap;
  const dot  = document.querySelector(".cursor__dot");
  const ring = document.querySelector(".cursor__ring");
  if (!dot || !ring) return;

  // One position update per frame regardless of how often mousemove fires.
  let mx = 0, my = 0, rafMove = 0;

  if (!gsap) {
    const apply = () => {
      rafMove = 0;
      dot.style.transform = `translate(${mx}px, ${my}px)`;
      ring.style.transform = `translate(${mx}px, ${my}px)`;
    };
    window.addEventListener("mousemove", (e) => {
      mx = e.clientX; my = e.clientY;
      if (!rafMove) rafMove = requestAnimationFrame(apply);
    }, { passive: true });
  } else {
    const dotX = gsap.quickTo(dot, "x", { duration: 0.12, ease: "power3" });
    const dotY = gsap.quickTo(dot, "y", { duration: 0.12, ease: "power3" });
    const ringX = gsap.quickTo(ring, "x", { duration: 0.5, ease: "power3" });
    const ringY = gsap.quickTo(ring, "y", { duration: 0.5, ease: "power3" });
    const apply = () => { rafMove = 0; dotX(mx); dotY(my); ringX(mx); ringY(my); };
    window.addEventListener("mousemove", (e) => {
      mx = e.clientX; my = e.clientY;
      if (!rafMove) rafMove = requestAnimationFrame(apply);
    }, { passive: true });
  }

  // Hover state — only toggle the class when the hover state actually changes,
  // and read the destination element on mouseout (relatedTarget) for correctness.
  const hoverSel = 'a, button, input, textarea, label, [data-cursor], .magnetic, .btn, .term-window';
  let lastHover = false;
  const evalHover = (node) => {
    const want = !!(node && node.closest && node.closest(hoverSel));
    if (want === lastHover) return;
    lastHover = want;
    document.body.classList.toggle("cursor-hover", want);
  };
  document.addEventListener("mouseover", (e) => evalHover(e.target), { passive: true });
  document.addEventListener("mouseout", (e) => evalHover(e.relatedTarget), { passive: true });
  document.addEventListener("mousedown", () => document.body.classList.add("cursor-down"), { passive: true });
  document.addEventListener("mouseup", () => document.body.classList.remove("cursor-down"), { passive: true });

  // Hide cursor when it leaves the window.
  document.addEventListener("mouseleave", () => gsap && gsap.to([dot, ring], { opacity: 0, duration: 0.2 }));
  document.addEventListener("mouseenter", () => gsap && gsap.to([dot, ring], { opacity: 1, duration: 0.2 }));

  initMagnetic();
}

/* Magnetic pull for [data-magnetic] elements — rect cached on enter, rAF-coalesced. */
export function initMagnetic() {
  const gsap = window.gsap;
  if (!gsap || isTouch()) return;
  qsa("[data-magnetic]").forEach((el) => {
    const strength = parseFloat(el.dataset.magnetic) || 0.35;
    const xTo = gsap.quickTo(el, "x", { duration: 0.5, ease: "power3" });
    const yTo = gsap.quickTo(el, "y", { duration: 0.5, ease: "power3" });
    let rect = null, raf = 0, ex = 0, ey = 0;

    el.addEventListener("mouseenter", () => { rect = el.getBoundingClientRect(); });
    el.addEventListener("mousemove", (e) => {
      ex = e.clientX; ey = e.clientY;
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = 0;
        const r = rect || (rect = el.getBoundingClientRect());
        xTo((ex - (r.left + r.width / 2)) * strength);
        yTo((ey - (r.top + r.height / 2)) * strength);
      });
    }, { passive: true });
    el.addEventListener("mouseleave", () => {
      if (raf) { cancelAnimationFrame(raf); raf = 0; }
      rect = null; xTo(0); yTo(0);
    });
  });
}
