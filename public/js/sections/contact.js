/* ==========================================================================
   contact.js — contact form behaviour.

   Responsibilities:
     1. Validate Name (non-empty), Email (simple regex), Message (non-empty)
        on submit. Mark invalid fields (aria-invalid) and attach a per-field
        error message tied to the input via aria-describedby, plus a short
        summary in the live status region.
     2. Send the form to the server (POST /contact → ContactController) with
        fetch, as JSON-accepting request; the CSRF token (_token) and the
        hidden honeypot field travel inside the FormData.
     3. On success: play a short terminal-style typewriter, then reset the
        form. On failure: show the server's field errors, or a clear message
        for "too many messages" (429), "session expired" (419) and offline.
   Without JavaScript the form still works as a normal POST + page reload.

   Works without GSAP and honours prefers-reduced-motion (instant success).
   A per-submit generation token invalidates any in-flight typewriter timers.
   Contract: ES module exporting init(); called once on DOMContentLoaded.
   ========================================================================== */

export function init() {
  const root = document.getElementById("contact");
  if (!root) return;

  const form = root.querySelector(".contact__form");
  const statusEl = root.querySelector(".contact__status");
  const submitBtn = root.querySelector(".contact__submit");
  if (!form || !statusEl || !submitBtn) return;

  const nameEl = form.querySelector("#contact-name");
  const emailEl = form.querySelector("#contact-email");
  const messageEl = form.querySelector("#contact-message");
  if (!nameEl || !emailEl || !messageEl) return;

  const app = window.app || {};
  const gsap = window.gsap;
  const reduced = !!app.reduced;

  // Reasonable, intentionally simple email check (front-end UX only).
  const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  const fields = [
    { el: nameEl, label: "name", test: (v) => v.length > 0, msg: "name is required", errId: "contact-err-name" },
    { el: emailEl, label: "email", test: (v) => EMAIL_RE.test(v), msg: "a valid email is required", errId: "contact-err-email" },
    { el: messageEl, label: "message", test: (v) => v.length > 0, msg: "message can't be empty", errId: "contact-err-message" },
  ];

  let busy = false;
  let resetTimer = 0;
  let gen = 0; // increments per submit; stale async work checks against it
  const btnDefaultHTML = submitBtn.innerHTML;

  /* ---- status helpers --------------------------------------------------- */
  function showStatus(state) {
    statusEl.classList.add("is-visible");
    statusEl.classList.toggle("is-error", state === "error");
    statusEl.classList.toggle("is-ok", state === "ok");
  }
  function hideStatus() {
    statusEl.classList.remove("is-visible", "is-error", "is-ok");
    statusEl.innerHTML = "";
  }
  function lineEl(text, cls) {
    const span = document.createElement("span");
    span.className = "contact__line" + (cls ? " " + cls : "");
    span.textContent = text;
    return span;
  }

  /* ---- per-field error wiring ------------------------------------------- */
  function fieldWrap(f) {
    return f.el.closest(".field") || f.el.parentElement;
  }
  function setFieldError(f) {
    f.el.setAttribute("aria-invalid", "true");
    const wrap = fieldWrap(f);
    let err = wrap.querySelector(".contact__field-err");
    if (!err) {
      err = document.createElement("span");
      err.className = "contact__field-err";
      err.id = f.errId;
      wrap.appendChild(err);
    }
    err.textContent = f.msg;
    f.el.setAttribute("aria-describedby", ("contact-status " + f.errId).trim());
  }
  function clearFieldError(f) {
    f.el.removeAttribute("aria-invalid");
    f.el.setAttribute("aria-describedby", "contact-status");
    const err = fieldWrap(f).querySelector(".contact__field-err");
    if (err) err.textContent = "";
  }
  function clearAllInvalid() {
    fields.forEach(clearFieldError);
  }

  /* ---- validation ------------------------------------------------------- */
  function validate() {
    clearAllInvalid();
    const invalid = [];
    fields.forEach((f) => {
      if (!f.test(f.el.value.trim())) { setFieldError(f); invalid.push(f); }
    });
    return invalid;
  }

  function renderErrors(invalid) {
    statusEl.innerHTML = "";
    showStatus("error");
    const n = invalid.length;
    statusEl.appendChild(
      lineEl(`✗ couldn't send — fix the ${n} highlighted field${n > 1 ? "s" : ""}.`, "term-err")
    );
    if (invalid[0]) invalid[0].el.focus();
  }

  /* ---- success sequence ------------------------------------------------- */
  const SUCCESS_STEPS = [
    { text: "> packaging message…", cls: "term-out" },
    { text: "> sending…", cls: "term-out" },
    { text: "✓ message sent. We'll get back to you within 24h.", cls: "term-ok" },
  ];

  function finishSuccess(myGen) {
    if (myGen !== gen) return;
    setBusy(false);
    submitBtn.innerHTML = btnDefaultHTML;
    resetTimer = window.setTimeout(() => {
      if (myGen !== gen) return;
      form.reset();
      clearAllInvalid();
      hideStatus();
    }, 4200);
  }

  // Instant render (no GSAP / reduced motion): print all lines at once.
  function renderSuccessInstant(myGen) {
    if (myGen !== gen) return;
    statusEl.innerHTML = "";
    showStatus("ok");
    SUCCESS_STEPS.forEach((s) => statusEl.appendChild(lineEl(s.text, s.cls)));
    finishSuccess(myGen);
  }

  // Typewriter: reveal each step's characters, then the next line. All timers
  // guarded by the submit generation so a fresh submit cancels stale output.
  function renderSuccessTyped(myGen) {
    if (myGen !== gen) return;
    statusEl.innerHTML = "";
    showStatus("ok");

    let stepIndex = 0;
    const caret = document.createElement("span");
    caret.className = "caret";

    function typeStep() {
      if (myGen !== gen) return;
      if (stepIndex >= SUCCESS_STEPS.length) {
        if (caret.parentNode) caret.parentNode.removeChild(caret);
        finishSuccess(myGen);
        return;
      }
      const step = SUCCESS_STEPS[stepIndex];
      const line = lineEl("", step.cls);
      statusEl.appendChild(line);
      statusEl.appendChild(caret);

      let i = 0;
      const speed = 26; // ms per character
      (function typeChar() {
        if (myGen !== gen) return;
        line.textContent = step.text.slice(0, i);
        i += 1;
        if (i <= step.text.length) window.setTimeout(typeChar, speed);
        else { stepIndex += 1; window.setTimeout(typeStep, 380); }
      })();
    }
    typeStep();
  }

  /* ---- busy / button state --------------------------------------------- */
  function setBusy(state) {
    busy = state;
    submitBtn.disabled = state;
    submitBtn.setAttribute("aria-busy", state ? "true" : "false");
  }
  function enterSendingState() {
    submitBtn.textContent = "$ sending…";
    if (gsap && !reduced) {
      gsap.fromTo(
        submitBtn,
        { scale: 1 },
        { scale: 0.97, duration: 0.18, ease: "power2.out", yoyo: true, repeat: 1 }
      );
    }
  }

  /* ---- server errors ---------------------------------------------------- */
  function renderServerError(text) {
    statusEl.innerHTML = "";
    showStatus("error");
    statusEl.appendChild(lineEl("✗ " + text, "term-err"));
  }

  // 422 from Laravel: { errors: { email: ["a valid email is required"], … } }
  function renderServerFieldErrors(errors) {
    clearAllInvalid();
    const invalid = fields.filter((f) => errors[f.label]);
    invalid.forEach((f) => {
      const ownMessage = f.msg;            // keep the field's normal message
      f.msg = String(errors[f.label][0] || ownMessage);
      setFieldError(f);
      f.msg = ownMessage;
    });
    if (invalid.length) renderErrors(invalid);
    else renderServerError("please check the form and try again.");
  }

  /* ---- send to the server ------------------------------------------------ */
  async function send() {
    const response = await fetch(form.action, {
      method: "POST",
      headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: new FormData(form),
      credentials: "same-origin",
    });
    let data = {};
    try { data = await response.json(); } catch (err) { /* not JSON */ }
    return { ok: response.ok, status: response.status, data };
  }

  function stopSending() {
    setBusy(false);
    submitBtn.innerHTML = btnDefaultHTML;
  }

  /* ---- submit ----------------------------------------------------------- */
  function onSubmit(e) {
    e.preventDefault();
    if (busy) return;

    gen += 1;                  // invalidate any in-flight typewriter timers
    const myGen = gen;
    window.clearTimeout(resetTimer);

    const invalid = validate();
    if (invalid.length) { renderErrors(invalid); return; }

    setBusy(true);
    enterSendingState();

    send()
      .then(({ ok, status, data }) => {
        if (myGen !== gen) return;
        if (ok) {
          if (!gsap || reduced) renderSuccessInstant(myGen);
          else renderSuccessTyped(myGen);
          return;
        }
        stopSending();
        if (status === 422 && data.errors) renderServerFieldErrors(data.errors);
        else if (status === 429) renderServerError("too many messages — please try again in a few minutes.");
        else if (status === 419) renderServerError("this page expired — refresh it and send again.");
        else renderServerError("something went wrong on our side — please email us instead.");
      })
      .catch(() => {
        if (myGen !== gen) return;
        stopSending();
        renderServerError("couldn't reach the server — check your connection and try again.");
      });
  }

  form.addEventListener("submit", onSubmit);

  // Clear a field's error as the user corrects it.
  fields.forEach((f) => {
    f.el.addEventListener("input", () => {
      if (f.el.getAttribute("aria-invalid") === "true" && f.test(f.el.value.trim())) {
        clearFieldError(f);
      }
    });
  });
}
