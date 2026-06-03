/* ==========================================================================
   nav.js — fixed navigation behavior
   · adds .nav--scrolled past a threshold
   · hides on scroll-down / shows on scroll-up (.nav--hidden)
   · toggles the full-screen overlay menu (burger ↔ X, scroll lock, a11y)
   · highlights the active section link via IntersectionObserver
   Works with or without GSAP; the menu always toggles via classes.
   Anchor smooth-scroll itself is handled globally (href="#id").
   ========================================================================== */

export function init() {
  const nav = document.getElementById("nav");
  if (!nav) return;

  const app = window.app || {};
  const burger = nav.querySelector(".nav__burger");
  const menu = nav.querySelector(".nav__menu");
  const menuLinks = Array.from(nav.querySelectorAll(".nav__menu-link"));
  const allLinks = Array.from(nav.querySelectorAll(".nav__link, .nav__menu-link"));
  const reduced = !!app.reduced;

  // Background regions made inert while the overlay is open (true focus containment).
  const bgEls = [document.getElementById("main"), document.getElementById("footer")].filter(Boolean);

  const SCROLL_THRESHOLD = 40; // px past which the nav gets its scrolled chrome
  const NEAR_TOP = 120;        // px within which we never hide the nav

  let lastScroll = window.scrollY || 0;
  let isOpen = false;
  let ticking = false;

  /* ----------------------------------------------------------------------
     Scroll chrome + hide-on-scroll-down
     ---------------------------------------------------------------------- */
  function onScrollUpdate(yRaw) {
    const y = Math.max(0, yRaw);

    nav.classList.toggle("nav--scrolled", y > SCROLL_THRESHOLD);

    // Never hide while the overlay is open or while near the top.
    if (isOpen || y < NEAR_TOP) {
      nav.classList.remove("nav--hidden");
    } else {
      const goingDown = y > lastScroll + 4;
      const goingUp = y < lastScroll - 4;
      if (goingDown) nav.classList.add("nav--hidden");
      else if (goingUp) nav.classList.remove("nav--hidden");
    }

    lastScroll = y;
    ticking = false;
  }

  function requestScroll(y) {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => onScrollUpdate(y));
  }

  // Prefer Lenis' scroll event when available, fall back to native scroll.
  const lenis = app.lenis || null;
  if (lenis && typeof lenis.on === "function") {
    lenis.on("scroll", (e) => requestScroll((e && e.scroll) || window.scrollY || 0));
  } else {
    window.addEventListener(
      "scroll",
      () => requestScroll(window.scrollY || window.pageYOffset || 0),
      { passive: true }
    );
  }
  // Prime initial state.
  onScrollUpdate(window.scrollY || 0);

  /* ----------------------------------------------------------------------
     Overlay menu
     ---------------------------------------------------------------------- */
  const gsap = window.gsap;
  let menuTween = null;

  function lockScroll() {
    if (app.stopScroll) app.stopScroll();
    else document.body.style.overflow = "hidden";
  }
  function unlockScroll() {
    if (app.startScroll) app.startScroll();
    else document.body.style.overflow = "";
  }

  function openMenu() {
    if (isOpen) return;
    isOpen = true;
    if (menu) menu.removeAttribute("hidden");
    document.body.classList.add("menu-open");
    nav.classList.add("is-open", "nav--scrolled");
    nav.classList.remove("nav--hidden");
    if (burger) {
      burger.setAttribute("aria-expanded", "true");
      burger.setAttribute("aria-label", "Close menu");
    }
    lockScroll();
    bgEls.forEach((el) => el.setAttribute("inert", ""));

    // Staggered entrance of the big links (skip under reduced motion / no gsap).
    if (gsap && !reduced && menuLinks.length) {
      if (menuTween) menuTween.kill();
      menuTween = gsap.fromTo(
        menuLinks,
        { y: 40, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.7,
          ease: "power4.out",
          stagger: 0.06,
          delay: 0.08,
          clearProps: "transform",
        }
      );
    }

    // Move focus into the dialog for keyboard users.
    if (menuLinks[0]) {
      requestAnimationFrame(() => menuLinks[0].focus({ preventScroll: true }));
    }
  }

  function closeMenu(restoreFocus) {
    if (!isOpen) return;
    isOpen = false;
    document.body.classList.remove("menu-open");
    nav.classList.remove("is-open");
    if (burger) {
      burger.setAttribute("aria-expanded", "false");
      burger.setAttribute("aria-label", "Open menu");
    }
    unlockScroll();
    bgEls.forEach((el) => el.removeAttribute("inert"));
    if (menuTween) { menuTween.kill(); menuTween = null; }

    // Hide after the fade-out so it stays out of the a11y tree.
    if (menu) {
      window.setTimeout(() => {
        if (!isOpen) menu.setAttribute("hidden", "");
      }, reduced ? 0 : 480);
    }

    if (restoreFocus && burger) burger.focus({ preventScroll: true });
  }

  if (burger) {
    burger.addEventListener("click", () => {
      isOpen ? closeMenu(true) : openMenu();
    });
  }

  // Any menu link click closes the overlay (global handler does the scroll).
  menuLinks.forEach((link) => {
    link.addEventListener("click", () => closeMenu(false));
  });

  // Escape closes; basic focus trap inside the open dialog.
  document.addEventListener("keydown", (e) => {
    if (!isOpen) return;
    if (e.key === "Escape") {
      e.preventDefault();
      closeMenu(true);
      return;
    }
    if (e.key === "Tab" && menu) {
      const focusables = menu.querySelectorAll(
        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
      );
      if (!focusables.length) return;
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });

  // Keep state coherent if the viewport grows past the mobile breakpoint.
  const mq = window.matchMedia("(min-width: 860px)");
  const onMq = (e) => { if (e.matches && isOpen) closeMenu(false); };
  if (mq.addEventListener) mq.addEventListener("change", onMq);
  else if (mq.addListener) mq.addListener(onMq);

  /* ----------------------------------------------------------------------
     Active link via IntersectionObserver
     ---------------------------------------------------------------------- */
  const targets = allLinks
    .map((link) => {
      const id = (link.getAttribute("href") || "").slice(1);
      return id ? document.getElementById(id) : null;
    })
    .filter(Boolean);

  const uniqueIds = Array.from(new Set(targets.map((t) => t.id)));
  const sections = uniqueIds.map((id) => document.getElementById(id)).filter(Boolean);

  if (sections.length && "IntersectionObserver" in window) {
    const visible = new Map();

    const setActive = (id) => {
      allLinks.forEach((link) => {
        const match = (link.getAttribute("href") || "") === "#" + id;
        link.classList.toggle("is-active", match);
        if (match) link.setAttribute("aria-current", "true");
        else link.removeAttribute("aria-current");
      });
    };

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) visible.set(entry.target.id, entry.intersectionRatio);
          else visible.delete(entry.target.id);
        });
        if (!visible.size) return;
        // Choose the most prominent section currently in view.
        let bestId = null;
        let bestRatio = -1;
        visible.forEach((ratio, id) => {
          if (ratio > bestRatio) { bestRatio = ratio; bestId = id; }
        });
        if (bestId) setActive(bestId);
      },
      { rootMargin: "-45% 0px -45% 0px", threshold: [0, 0.25, 0.5, 0.75, 1] }
    );

    sections.forEach((sec) => io.observe(sec));
  }
}
