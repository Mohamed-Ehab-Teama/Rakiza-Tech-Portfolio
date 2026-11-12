/* ==========================================================================
   terminal.js — interactive REPL for the #terminal "playground" section.

   A small, dependency-free shell: a COMMANDS registry maps a command name to
   a function returning an HTML string, which is appended to #term-output and
   scrolled into view. The real <input> drives everything; ArrowUp/Down recall
   history; clicking anywhere in the window refocuses the input.

   Contract: ES module exporting init(); called once on DOMContentLoaded.
   - Works fully without GSAP/ScrollTrigger (typewriter degrades to instant).
   - Respects window.app.reduced (prefers-reduced-motion).
   - Defensive: returns early if the root/input/output is missing; never
     throws on empty or odd input.
   ========================================================================== */

/* The answers come from the database: HomeController::terminalData() prints
   them as JSON in <script id="terminal-data"> (sections/terminal.blade.php). */
function readData() {
  try {
    const el = document.getElementById("terminal-data");
    return el ? JSON.parse(el.textContent) : {};
  } catch (e) {
    return {};
  }
}

export function init() {
  const root = document.getElementById("terminal");
  if (!root) return;

  const DATA = readData();
  const NAME = DATA.name || "Rakiza Tech";
  const EMAIL = DATA.email || "";
  const PHONE = DATA.phone || "";
  const LOCATION = DATA.location || "";
  const AVAILABILITY = DATA.availability || "";

  const output = root.querySelector("#term-output");
  const input = root.querySelector("#term-input");
  const windowEl = root.querySelector(".terminal__window");
  if (!output || !input || !windowEl) return;

  const app = window.app || {};
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  const reduced = !!app.reduced;

  /* ---- tiny HTML escape so echoed user input is always safe ------------- */
  const esc = (s) =>
    String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");

  /* ---- content -------------------------------------------------------------
     Centralised so several commands can share it (ls/cat/projects/etc).
     ------------------------------------------------------------------------ */
  const SOCIALS = (DATA.socials || []).map((s) => [s.label, s.url]);

  const PROJECTS = (DATA.projects || []).map((p) => [p.title, p.stack, p.year, p.url]);

  const SERVICES = (DATA.services || []).map((s) => [s.name.toLowerCase(), s.description]);

  const SKILLS = (DATA.skills || []).map((g) => [g.group, g.items]);

  const ABOUT_MD = [
    `# ${NAME}`,
    `${DATA.tagline || "Digital Solutions"}.`,
    "We build websites, systems and apps end to end — database, APIs and the screens on top.",
    [LOCATION, AVAILABILITY.toLowerCase()].filter(Boolean).join(" — "),
  ].filter(Boolean);

  // "label   value" lines; the label column fits the longest label + a space.
  const labelWidth = Math.max(8, ...SOCIALS.map(([name]) => name.length + 1));
  const pad = (label) => label.toLowerCase().padEnd(labelWidth);
  const CONTACT_TXT = [
    EMAIL && pad("email") + EMAIL,
    PHONE && pad("phone") + PHONE,
    ...SOCIALS.map(([name, href]) => pad(name) + href.replace(/^https?:\/\/(www\.)?/, "")),
    AVAILABILITY && pad("status") + AVAILABILITY.toLowerCase(),
  ].filter(Boolean);

  const FILES = {
    "about.md": () =>
      `<div class="term-line term-out">${ABOUT_MD.map(esc).join("\n")}</div>`,
    "skills.json": () => {
      const lines = ["{"];
      SKILLS.forEach(([group, items], i) => {
        const comma = i < SKILLS.length - 1 ? "," : "";
        lines.push(
          `  <span class="terminal__key">"${esc(group.toLowerCase())}"</span>: [${items
            .map((t) => `<span class="tok-str">"${esc(t)}"</span>`)
            .join(", ")}]${comma}`
        );
      });
      lines.push("}");
      return `<div class="term-line term-out">${lines.join("\n")}</div>`;
    },
    "contact.txt": () =>
      `<div class="term-line term-out">${CONTACT_TXT.map(esc).join("\n")}</div>`,
    "company-profile.pdf": () =>
      line(
        "warn",
        "company-profile.pdf is a binary file. Run <span class=\"term-path\">contact</span> to request the latest copy."
      ),
  };

  /* ---- output helpers ---------------------------------------------------- */
  const line = (kind, html) =>
    `<div class="term-line term-${kind}">${html}</div>`;

  const list = (items) =>
    `<div class="term-line">${items
      .map((h) => `<div>${h}</div>`)
      .join("")}</div>`;

  /* ---- COMMANDS registry ------------------------------------------------- */
  const COMMANDS = {
    help() {
      return (
        line("ok", "Available commands:") +
        `<div class="term-line"><div class="terminal__cols">${HELP.map(
          ([name, desc]) =>
            `<span class="term-path">${esc(name)}</span><span class="term-out">${esc(
              desc
            )}</span>`
        ).join("")}</div></div>` +
        line(
          "out",
          'Tip: try <span class="term-path">about</span>, <span class="term-path">projects</span> or <span class="term-path">cat about.md</span>.'
        )
      );
    },

    about() {
      return (
        line("ok", `<span class="terminal__title">${esc(NAME)}</span> — ${esc(DATA.tagline || "Digital Solutions")}`) +
        line("out", "We build websites, systems and apps end to end — the database, the APIs and the logic underneath, then the screens on top. We care about clean, maintainable code and fast data models.") +
        line("out", `<span class="terminal__muted">${esc([LOCATION, "PHP · Laravel · MySQL", AVAILABILITY.toLowerCase()].filter(Boolean).join(" · "))}</span>`) +
        line("out", 'Run <span class="term-path">skills</span>, <span class="term-path">projects</span> or <span class="term-path">contact</span> to dig deeper.')
      );
    },

    skills() {
      const groups = SKILLS.map(
        ([name, items]) =>
          `<div class="terminal__group"><span class="terminal__group-name">${esc(
            name
          )}</span> <span class="terminal__chips">${items
            .map(esc)
            .join(" · ")}</span></div>`
      ).join("");
      return line("ok", "Tech stack") + `<div class="term-line">${groups}</div>`;
    },

    services() {
      const rows = SERVICES.map(
        ([name, desc]) =>
          `<span class="term-path">${esc(name)}</span><span class="term-out">${esc(
            desc
          )}</span>`
      ).join("");
      return (
        line("ok", "What we build, end to end:") +
        `<div class="term-line"><div class="terminal__cols">${rows}</div></div>`
      );
    },

    projects() {
      if (!PROJECTS.length) return line("out", "No projects published yet.");
      const rows = PROJECTS.map(
        ([name, stack, year, url], i) =>
          `<div><span class="term-out tnum">${String(i + 1).padStart(
            2,
            "0"
          )}</span> <a class="terminal__title" href="${esc(url)}">${esc(
            name
          )}</a> <span class="terminal__muted">— ${esc(
            stack
          )}</span> <span class="term-path">${esc(year)}</span></div>`
      ).join("");
      return (
        line("ok", "Selected work:") +
        `<div class="term-line">${rows}</div>` +
        line("out", 'Click a title to open its case study · run <span class="term-path">contact</span> to get in touch.')
      );
    },

    contact() {
      const tel = PHONE.replace(/[^0-9+]/g, "");
      return (
        line("ok", "Let's build something.") +
        (EMAIL ? line("out", `email   <a href="mailto:${esc(EMAIL)}">${esc(EMAIL)}</a>`) : "") +
        (PHONE ? line("out", `phone   <a href="tel:${esc(tel)}">${esc(PHONE)}</a>`) : "") +
        (SOCIALS.length
          ? line(
              "out",
              "socials " +
                SOCIALS.map(
                  ([name, href]) =>
                    `<a href="${esc(href)}" target="_blank" rel="noopener">${esc(name)}</a>`
                ).join(" · ")
            )
          : "") +
        line("out", `<span class="terminal__muted">${esc([LOCATION, "usually replies within a day"].filter(Boolean).join(" · "))}</span>`)
      );
    },

    social() {
      return (
        line("ok", "Find us online:") +
        list(
          SOCIALS.map(
            ([name, href]) =>
              `<a href="${esc(href)}" rel="noopener">${esc(
                name
              )}</a> <span class="terminal__muted">${esc(href)}</span>`
          )
        )
      );
    },

    ls() {
      return line(
        "out",
        '<span class="terminal__ls">' +
          '<span class="terminal__file">about.md</span>' +
          '<span class="terminal__dir">projects/</span>' +
          '<span class="terminal__file">skills.json</span>' +
          '<span class="terminal__file">company-profile.pdf</span>' +
          '<span class="terminal__file">contact.txt</span>' +
          "</span>"
      );
    },

    cat(args) {
      const name = (args[0] || "").trim();
      if (!name) {
        return line("err", "cat: missing operand. Try <span class=\"term-path\">cat about.md</span>.");
      }
      if (name === "projects/" || name === "projects") {
        return line("err", "cat: projects: Is a directory. Try <span class=\"term-path\">projects</span>.");
      }
      const file = FILES[name];
      if (!file) {
        return line(
          "err",
          `cat: ${esc(name)}: No such file. Run <span class="term-path">ls</span> to list files.`
        );
      }
      return file();
    },

    date() {
      const now = new Date();
      return line("out", esc(now.toString()));
    },

    echo(args, raw) {
      return line("out", esc(raw) || "");
    },

    banner() {
      // "RAKIZA" in ASCII art (each \\ is one backslash inside a JS string).
      const art =
        " ___     _     _  __  ___   ____    _   \n" +
        "| _ \\   /_\\   | |/ / |_ _| |_  /   /_\\  \n" +
        "|   /  / _ \\  | ' <   | |   / /   / _ \\ \n" +
        "|_|_\\ /_/ \\_\\ |_|\\_\\ |___| /___| /_/ \\_\\";
      return (
        `<div class="term-line"><span class="terminal__banner">${esc(art)}</span></div>` +
        line("out", `<span class="terminal__muted">${esc(NAME)} — digital solutions. Type</span> <span class="term-path">help</span> <span class="terminal__muted">for commands.</span>`)
      );
    },

    sudo(args, raw) {
      return line(
        "warn",
        `nice try 😏 — permission denied${raw ? `: <span class="terminal__muted">${esc(raw)}</span>` : ""}`
      );
    },

    clear() {
      output.innerHTML = "";
      return null; // sentinel: nothing to append
    },
  };

  /* aliases */
  COMMANDS.whoami = COMMANDS.about;
  COMMANDS.stack = COMMANDS.skills;
  COMMANDS.work = COMMANDS.projects;
  COMMANDS.ascii = COMMANDS.banner;
  COMMANDS.cls = COMMANDS.clear;

  /* help listing (name, description) — drives the `help` table */
  const HELP = [
    ["help", "show this list"],
    ["about", `who is ${NAME}`],
    ["skills", "grouped tech stack"],
    ["services", "what we build"],
    ["projects", "selected work"],
    ["contact", "email & socials"],
    ["social", "social links"],
    ["ls", "list files"],
    ["cat <file>", "print a file"],
    ["date", "current date & time"],
    ["echo <text>", "print text"],
    ["banner", "ASCII Rakiza banner"],
    ["clear", "clear the screen"],
  ];

  /* ---- render an emitted block ------------------------------------------ */
  function append(html) {
    if (html == null) return; // e.g. clear
    const block = document.createElement("div");
    block.className = "terminal__block";
    block.innerHTML = html;
    output.appendChild(block);
  }

  function echoCommand(raw) {
    const block = document.createElement("div");
    block.className = "terminal__block terminal__echo";
    block.innerHTML =
      `<div class="term-line"><span class="term-prompt">visitor@rakiza</span>` +
      `<span class="term-path">:~$</span> ` +
      `<span class="terminal__cmd">${esc(raw)}</span></div>`;
    output.appendChild(block);
  }

  function scrollToEnd() {
    output.scrollTop = output.scrollHeight;
  }

  /* ---- parse + run ------------------------------------------------------- */
  function run(rawInput) {
    const raw = String(rawInput == null ? "" : rawInput).trim();
    echoCommand(raw);

    if (!raw) {
      scrollToEnd();
      return;
    }

    const parts = raw.split(/\s+/);
    const name = parts[0].toLowerCase();
    const args = parts.slice(1);
    const rest = raw.slice(parts[0].length).trim(); // raw remainder (echo/sudo)

    const fn = COMMANDS[name];
    let out;
    if (typeof fn === "function") {
      try {
        out = fn(args, rest);
      } catch (err) {
        out = line("err", "internal error while running that command.");
      }
    } else {
      out = line(
        "err",
        `command not found: ${esc(name)}. Type <span class="term-path">help</span> for options.`
      );
    }
    append(out);
    scrollToEnd();
  }

  /* ---- command history --------------------------------------------------- */
  const history = [];
  let histIndex = -1; // -1 = editing fresh line
  let draft = "";

  function recall(dir) {
    if (!history.length) return;
    if (histIndex === -1) {
      draft = input.value;
      histIndex = history.length;
    }
    histIndex += dir;
    if (histIndex < 0) histIndex = 0;
    if (histIndex >= history.length) {
      histIndex = -1;
      input.value = draft;
    } else {
      input.value = history[histIndex];
    }
    // move caret to end
    requestAnimationFrame(() => {
      const end = input.value.length;
      try { input.setSelectionRange(end, end); } catch (_) {}
    });
  }

  /* ---- input wiring ------------------------------------------------------ */
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      const value = input.value;
      const trimmed = value.trim();
      if (trimmed && history[history.length - 1] !== trimmed) {
        history.push(trimmed);
      }
      histIndex = -1;
      draft = "";
      input.value = "";
      run(value);
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      recall(-1);
    } else if (e.key === "ArrowDown") {
      e.preventDefault();
      recall(1);
    } else if (e.key === "l" && e.ctrlKey) {
      // Ctrl+L clears, like a real shell
      e.preventDefault();
      output.innerHTML = "";
    }
  });

  // Keep focus on the input when clicking inside the window (but let the user
  // click links / select output text without yanking focus away mid-selection).
  // Listen on mouseup so any text selection has already formed; only steal
  // focus on a plain click, and never scroll the page to do it.
  windowEl.addEventListener("mouseup", (e) => {
    const target = e.target;
    if (target.closest("a")) return; // allow link clicks
    if (target === input) return;
    const sel = window.getSelection && window.getSelection();
    if (sel && String(sel).length) return; // user selected text — keep it
    input.focus({ preventScroll: true });
  });

  /* ---- welcome + auto-demo ---------------------------------------------- */
  let demoStarted = false;

  function clearStatic() {
    output.querySelectorAll("[data-static]").forEach((n) => n.remove());
  }

  // Print instantly (reduced motion / no GSAP): welcome banner + help.
  function welcomeInstant() {
    clearStatic();
    append(COMMANDS.banner());
    echoCommand("help");
    append(COMMANDS.help());
    scrollToEnd();
  }

  // Typewriter: type "help" into the input char-by-char, then run it.
  function welcomeTyped() {
    clearStatic();
    append(COMMANDS.banner());
    scrollToEnd();

    const text = "help";
    let i = 0;
    input.disabled = false;
    input.value = "";
    input.focus();

    const tl = gsap.timeline({ delay: 0.45 });
    tl.to(
      {},
      {
        duration: text.length * 0.11,
        ease: "none",
        onUpdate() {
          const want = Math.round(this.progress() * text.length);
          if (want !== i) {
            i = want;
            input.value = text.slice(0, i);
          }
        },
        onComplete() {
          input.value = "";
          run("help");
        },
      }
    );
  }

  function startWelcome() {
    if (demoStarted) return;
    demoStarted = true;
    if (!gsap || reduced) welcomeInstant();
    else welcomeTyped();
  }

  // Trigger the demo only when the section scrolls into view (once).
  // Without ScrollTrigger, run immediately so the section is never empty.
  if (gsap && ScrollTrigger) {
    ScrollTrigger.create({
      trigger: root,
      start: "top 70%",
      once: true,
      onEnter: startWelcome,
    });
    // Safety: if already in view on load (deep link / short page), kick it off.
    const rect = root.getBoundingClientRect();
    if (rect.top < window.innerHeight * 0.7) startWelcome();
  } else {
    startWelcome();
  }
}
