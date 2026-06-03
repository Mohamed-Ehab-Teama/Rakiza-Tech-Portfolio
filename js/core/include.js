/* ==========================================================================
   include.js — minimal HTML "include" loader.
   Replaces every  <div data-include="html/x.html"></div>  with the contents of
   that file, in place. Runs ONCE before the section modules initialise.

   NOTE: uses fetch(), which browsers block on the file:// protocol — so the
   site must be opened through a server (Laragon, or `node serve.mjs`).
   ========================================================================== */
export async function loadIncludes(scope = document) {
  const slots = Array.from(scope.querySelectorAll("[data-include]"));
  if (!slots.length) return;

  if (location.protocol === "file:") {
    console.error(
      "[include] Sections are loaded with fetch(), which the browser blocks on " +
      "file://. Open the site through a server — Laragon (http://teamora-apps.test/) " +
      "or run `node serve.mjs` and visit http://localhost:8080/."
    );
  }

  await Promise.all(
    slots.map(async (slot) => {
      const url = slot.getAttribute("data-include");
      if (!url) return;
      try {
        const res = await fetch(url, { cache: "no-cache" });
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        const html = await res.text();
        slot.insertAdjacentHTML("beforebegin", html); // preserve document order
        slot.remove();                                  // drop the placeholder
      } catch (err) {
        console.error("[include] could not load", url, err);
        slot.remove();
      }
    })
  );
}
