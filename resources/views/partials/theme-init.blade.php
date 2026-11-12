{{--
    partials/theme-init.blade.php — applies the visitor's color theme BEFORE the
    page is painted (so there is no flash of the wrong colors).

    Order of preference:
      1. the theme the visitor picked before (saved in localStorage by theme.js)
      2. the site default from <html data-theme-default="…"> (Dashboard → Settings)
    "system" is resolved to "dark" or "light" from the device setting.
    It must stay small and inline: an external file would load too late.
--}}
<script>
  (function () {
    var root = document.documentElement;
    var pref = null;
    try { pref = localStorage.getItem("rakiza-theme"); } catch (e) {}
    pref = pref || root.getAttribute("data-theme-default") || "system";
    var theme = pref === "system"
      ? (window.matchMedia("(prefers-color-scheme: light)").matches ? "light" : "dark")
      : pref;
    root.setAttribute("data-theme-pref", pref);
    root.setAttribute("data-theme", theme);
  })();
</script>
