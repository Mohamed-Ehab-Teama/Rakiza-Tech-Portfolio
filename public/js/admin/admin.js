/* ==========================================================================
   admin.js — small enhancements for the dashboard (resources/views/admin).
   Everything still works without JavaScript; this only makes it nicer:
     1. color-theme menu (shared with the website: ../core/theme.js)
     2. sidebar open/close on small screens
     3. "Are you sure?" before deleting   (forms with data-confirm)
     4. show / hide password               (buttons with data-toggle-password)
     5. image preview before upload       (file inputs with data-preview)
     6. permission matrix "all" boxes     (roles form)
     7. drag & drop + arrow-button ordering of list rows (tables with
        data-reorder-url) — saved right away with fetch()
   ========================================================================== */
import { initTheme } from "../core/theme.js";

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || "";

/* Small status message in the corner (also read out by screen readers). */
function toast(message, isError = false) {
  const el = document.querySelector(".admin-toast");
  if (!el) return;
  el.textContent = message;
  el.classList.toggle("is-error", isError);
  el.hidden = false;
  clearTimeout(toast.timer);
  toast.timer = setTimeout(() => { el.hidden = true; }, 3200);
}

/* 2 ----------------------------------------------------------------------- */
function initSidebar() {
  const toggle = document.querySelector("[data-sidebar-toggle]");
  const overlay = document.querySelector("[data-sidebar-close]");
  if (!toggle) return;

  const setOpen = (open) => {
    document.body.classList.toggle("sidebar-open", open);
    toggle.setAttribute("aria-expanded", String(open));
    if (overlay) overlay.hidden = !open;
    if (open) document.querySelector(".admin-nav__link")?.focus();
  };

  toggle.addEventListener("click", () => setOpen(!document.body.classList.contains("sidebar-open")));
  overlay?.addEventListener("click", () => setOpen(false));
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && document.body.classList.contains("sidebar-open")) {
      setOpen(false);
      toggle.focus();
    }
  });
}

/* 3 ----------------------------------------------------------------------- */
function initConfirm() {
  document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (e) => {
      if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    });
  });
}

/* 4 ----------------------------------------------------------------------- */
function initPasswordToggles() {
  document.querySelectorAll("[data-toggle-password]").forEach((button) => {
    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) return;
    button.addEventListener("click", () => {
      const show = input.type === "password";
      input.type = show ? "text" : "password";
      button.setAttribute("aria-label", show ? "Hide password" : "Show password");
    });
  });
}

/* 5 ----------------------------------------------------------------------- */
function initImagePreviews() {
  document.querySelectorAll("input[type=file][data-preview]").forEach((input) => {
    input.addEventListener("change", () => {
      const file = input.files && input.files[0];
      let preview = input.parentElement.querySelector(".image-preview");
      if (!file || !file.type.startsWith("image/")) {
        preview?.remove();
        return;
      }
      if (!preview) {
        preview = document.createElement("img");
        preview.className = "image-preview";
        preview.alt = "Preview of the new image";
        input.insertAdjacentElement("afterend", preview);
      }
      preview.src = URL.createObjectURL(file);
    });
  });
}

/* 6 ----------------------------------------------------------------------- */
function initPermissionMatrix() {
  document.querySelectorAll("[data-select-area]").forEach((all) => {
    const area = all.dataset.selectArea;
    const boxes = Array.from(document.querySelectorAll(`input[name="permissions[]"][data-area="${area}"]`));
    const sync = () => {
      const checked = boxes.filter((b) => b.checked).length;
      all.checked = checked === boxes.length;
      all.indeterminate = checked > 0 && checked < boxes.length;
    };
    all.addEventListener("change", () => {
      boxes.forEach((b) => { b.checked = all.checked; });
      sync();
    });
    boxes.forEach((b) => b.addEventListener("change", sync));
    sync();
  });
}

/* 7 ----------------------------------------------------------------------- */
function initReorder() {
  document.querySelectorAll("table[data-reorder-url]").forEach((table) => {
    const tbody = table.tBodies[0];
    if (!tbody) return;
    let dragged = null;

    const rows = () => Array.from(tbody.rows);
    const clearMarks = () => rows().forEach((r) => r.classList.remove("is-drop-above", "is-drop-below"));

    async function save() {
      const ids = rows().map((r) => Number(r.dataset.id));
      try {
        const response = await fetch(table.dataset.reorderUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken(),
          },
          body: JSON.stringify({ ids }),
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || "Could not save the new order.");
        toast(data.message || "New order saved.");
      } catch (err) {
        toast(err.message + " Reload the page and try again.", true);
      }
    }

    // Mouse / touchpad: drag the handle.
    tbody.addEventListener("dragstart", (e) => {
      const handle = e.target.closest(".drag-handle");
      if (!handle) { e.preventDefault(); return; }
      dragged = handle.closest("tr");
      dragged.classList.add("is-dragging");
      e.dataTransfer.effectAllowed = "move";
      e.dataTransfer.setData("text/plain", dragged.dataset.id);
      e.dataTransfer.setDragImage(dragged, 20, 20);
    });

    tbody.addEventListener("dragover", (e) => {
      if (!dragged) return;
      const row = e.target.closest("tr");
      if (!row || row === dragged) return;
      e.preventDefault();
      const after = e.clientY > row.getBoundingClientRect().top + row.offsetHeight / 2;
      clearMarks();
      row.classList.add(after ? "is-drop-below" : "is-drop-above");
    });

    tbody.addEventListener("drop", (e) => {
      if (!dragged) return;
      const row = e.target.closest("tr");
      e.preventDefault();
      if (row && row !== dragged) {
        const after = row.classList.contains("is-drop-below");
        row.insertAdjacentElement(after ? "afterend" : "beforebegin", dragged);
        save();
      }
      clearMarks();
    });

    tbody.addEventListener("dragend", () => {
      dragged?.classList.remove("is-dragging");
      dragged = null;
      clearMarks();
    });

    // Keyboard / touch: the up and down arrow buttons.
    tbody.addEventListener("click", (e) => {
      const button = e.target.closest("[data-move]");
      if (!button) return;
      const row = button.closest("tr");
      const target = button.dataset.move === "up" ? row.previousElementSibling : row.nextElementSibling;
      if (!target) return;
      target.insertAdjacentElement(button.dataset.move === "up" ? "beforebegin" : "afterend", row);
      button.focus();
      save();
    });
  });
}

/* ------------------------------------------------------------------------- */
function boot() {
  try { initTheme(); } catch (err) { console.error("[theme]", err); }
  initSidebar();
  initConfirm();
  initPasswordToggles();
  initImagePreviews();
  initPermissionMatrix();
  initReorder();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", boot);
} else {
  boot();
}
