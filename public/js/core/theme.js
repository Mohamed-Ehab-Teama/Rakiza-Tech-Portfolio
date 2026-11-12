/* ==========================================================================
   theme.js — color themes (System, Dark, Light, Dim, Sepia, Rakiza).

   partials/theme-init already applied the saved theme in <head> before the
   first paint. This module:
     - wires every [data-theme-switch] menu (click, keyboard, outside click)
     - saves the visitor's choice in localStorage ("rakiza-theme")
     - follows the device setting live while "System" is selected
     - fires a "themechange" event on window so canvas code can recolor
   Works on the public site and in the dashboard (no other dependencies).
   ========================================================================== */
const STORAGE_KEY = "rakiza-theme";
const root = document.documentElement;
const lightQuery = window.matchMedia("(prefers-color-scheme: light)");

/* The theme keys that exist on this page (taken from the menu options). */
const knownThemes = () =>
  Array.from(document.querySelectorAll("[data-theme-option]")).map((el) => el.dataset.themeOption);

/* "system" → "dark" or "light"; any other key is used as-is. */
const resolve = (pref) =>
  pref === "system" ? (lightQuery.matches ? "light" : "dark") : pref;

function save(pref) {
  try { localStorage.setItem(STORAGE_KEY, pref); } catch (e) { /* private mode: ignore */ }
}

/* Apply a theme preference to the page and tell everyone about it. */
export function applyTheme(pref) {
  const theme = resolve(pref);
  root.setAttribute("data-theme-pref", pref);
  root.setAttribute("data-theme", theme);

  // Mobile browsers color their toolbar with <meta name="theme-color">.
  const meta = document.querySelector('meta[name="theme-color"]');
  if (meta) meta.setAttribute("content", getComputedStyle(root).getPropertyValue("--bg").trim());

  document.querySelectorAll("[data-theme-option]").forEach((option) => {
    option.setAttribute("aria-checked", String(option.dataset.themeOption === pref));
  });

  window.dispatchEvent(new CustomEvent("themechange", { detail: { pref, theme } }));
}

/* One dropdown menu: open/close + keyboard support (WAI-ARIA menu pattern). */
function setupMenu(wrapper) {
  const button = wrapper.querySelector(".theme-switch__btn");
  const menu = wrapper.querySelector(".theme-switch__menu");
  if (!button || !menu) return;
  const options = Array.from(menu.querySelectorAll("[data-theme-option]"));

  const isOpen = () => !menu.hidden;
  const open = () => {
    menu.hidden = false;
    button.setAttribute("aria-expanded", "true");
    const current = options.find((o) => o.getAttribute("aria-checked") === "true") || options[0];
    current.focus();
  };
  const close = (returnFocus) => {
    if (!isOpen()) return;
    menu.hidden = true;
    button.setAttribute("aria-expanded", "false");
    if (returnFocus) button.focus();
  };

  button.addEventListener("click", () => (isOpen() ? close(false) : open()));

  options.forEach((option) => {
    option.addEventListener("click", () => {
      const pref = option.dataset.themeOption;
      save(pref);
      applyTheme(pref);
      close(true);
    });
  });

  menu.addEventListener("keydown", (e) => {
    const i = options.indexOf(document.activeElement);
    let next = null;
    if (e.key === "ArrowDown") next = options[(i + 1) % options.length];
    else if (e.key === "ArrowUp") next = options[(i - 1 + options.length) % options.length];
    else if (e.key === "Home") next = options[0];
    else if (e.key === "End") next = options[options.length - 1];
    else if (e.key === "Escape") { e.preventDefault(); close(true); return; }
    else if (e.key === "Tab") { close(false); return; }
    if (next) { e.preventDefault(); next.focus(); }
  });

  // Click anywhere else closes the menu.
  document.addEventListener("click", (e) => {
    if (isOpen() && !wrapper.contains(e.target)) close(false);
  });
}

export function initTheme() {
  // Use the preference theme-init picked; fall back to "system" if it is unknown
  // (e.g. a theme that was removed after the visitor saved it).
  let pref = root.getAttribute("data-theme-pref") || "system";
  const known = knownThemes();
  if (known.length && !known.includes(pref)) pref = root.getAttribute("data-theme-default") || "system";
  applyTheme(pref);

  // Follow the device's light/dark setting live while "System" is selected.
  lightQuery.addEventListener("change", () => {
    if (root.getAttribute("data-theme-pref") === "system") applyTheme("system");
  });

  document.querySelectorAll("[data-theme-switch]").forEach(setupMenu);
}
