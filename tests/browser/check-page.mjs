/* ==========================================================================
   tests/browser/check-page.mjs — real-browser check of the public site.

   PHPUnit tests the server; this checks what only a real browser can:
     - nothing stays invisible (scroll reveals, headings, count-up numbers)
       after scrolling with the mouse wheel, pressing End, or reloading
       half-way down the page
     - no JavaScript errors, console errors or failed requests on the home
       page, /projects and a project page
   It runs at a desktop (1280×800) and a phone (375×812) size.

   Needs: Node 22+ (built-in WebSocket) and Chrome, Edge or Chromium.
   Usage (with the site running, e.g. "php artisan serve"):
       node tests/browser/check-page.mjs                    # http://127.0.0.1:8000
       node tests/browser/check-page.mjs http://rakiza-tech-landing-page.test
       node tests/browser/check-page.mjs --headed           # watch it in a visible window
       BROWSER="/path/to/chrome" node tests/browser/check-page.mjs
   Exit code 0 = all good, 1 = something failed (details are printed).
   ========================================================================== */
import { spawn } from "node:child_process";
import { existsSync, mkdtempSync, rmSync } from "node:fs";
import { tmpdir } from "node:os";
import { join } from "node:path";

const args = process.argv.slice(2);
const HEADED = args.includes("--headed");
const BASE = (args.find((a) => !a.startsWith("--")) || process.env.APP_URL || "http://127.0.0.1:8000").replace(/\/$/, "");
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

/* ---- find a Chromium-based browser ------------------------------------- */
function findBrowser() {
  const candidates = [
    process.env.BROWSER,
    "C:/Program Files/Google/Chrome/Application/chrome.exe",
    "C:/Program Files (x86)/Google/Chrome/Application/chrome.exe",
    "C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe",
    "C:/Program Files/Microsoft/Edge/Application/msedge.exe",
    "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome",
    "/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge",
    "/usr/bin/google-chrome", "/usr/bin/chromium", "/usr/bin/chromium-browser", "/usr/bin/microsoft-edge",
  ].filter(Boolean);
  const found = candidates.find((path) => existsSync(path));
  if (!found) {
    console.error("No Chrome/Edge/Chromium found. Set BROWSER=/path/to/browser.");
    process.exit(1);
  }
  return found;
}

/* ---- a tiny Chrome DevTools Protocol client ------------------------------ */
class Page {
  constructor(wsUrl) { this.wsUrl = wsUrl; this.id = 0; this.pending = new Map(); this.waiters = []; this.problems = []; }

  async connect() {
    this.ws = new WebSocket(this.wsUrl);
    await new Promise((resolve, reject) => { this.ws.onopen = resolve; this.ws.onerror = reject; });
    this.ws.onmessage = (event) => this.onMessage(JSON.parse(event.data));
    for (const domain of ["Page", "Runtime", "Log", "Network"]) await this.send(`${domain}.enable`);
  }

  send(method, params = {}) {
    return new Promise((resolve, reject) => {
      const id = ++this.id;
      this.pending.set(id, { resolve, reject });
      this.ws.send(JSON.stringify({ id, method, params }));
    });
  }

  waitFor(method) { return new Promise((resolve) => this.waiters.push({ method, resolve })); }

  onMessage(msg) {
    if (msg.id && this.pending.has(msg.id)) {
      const { resolve, reject } = this.pending.get(msg.id);
      this.pending.delete(msg.id);
      msg.error ? reject(new Error(msg.error.message)) : resolve(msg.result);
      return;
    }
    const p = msg.params || {};
    if (msg.method === "Runtime.exceptionThrown") {
      this.problems.push("JS exception: " + (p.exceptionDetails.exception?.description || p.exceptionDetails.text).split("\n")[0]);
    }
    if (msg.method === "Runtime.consoleAPICalled" && p.type === "error") {
      this.problems.push("console.error: " + p.args.map((a) => a.value ?? a.description).join(" "));
    }
    if (msg.method === "Network.responseReceived" && p.response.status >= 400 && p.response.url.startsWith(BASE)) {
      this.problems.push(`HTTP ${p.response.status}: ${p.response.url}`);
    }
    if (msg.method === "Network.loadingFailed" && !p.canceled) {
      this.problems.push(`request failed (${p.errorText}): ${p.requestId}`);
    }
    for (let i = this.waiters.length - 1; i >= 0; i--) {
      if (this.waiters[i].method === msg.method) { this.waiters[i].resolve(p); this.waiters.splice(i, 1); }
    }
  }

  async evaluate(expression) {
    const result = await this.send("Runtime.evaluate", { expression, awaitPromise: true, returnByValue: true });
    if (result.exceptionDetails) throw new Error("evaluate: " + result.exceptionDetails.text);
    return result.result.value;
  }

  async goto(url) {
    const loaded = this.waitFor("Page.loadEventFired");
    await this.send("Page.navigate", { url });
    await loaded;
  }

  async reload() {
    const loaded = this.waitFor("Page.loadEventFired");
    await this.send("Page.reload");
    await loaded;
  }

  async wheel(deltaY, x = 200, y = 300) {
    await this.send("Input.dispatchMouseEvent", { type: "mouseWheel", x, y, deltaX: 0, deltaY });
  }

  async key(key, code, keyCode) {
    await this.send("Input.dispatchKeyEvent", { type: "rawKeyDown", key, code, windowsVirtualKeyCode: keyCode });
    await this.send("Input.dispatchKeyEvent", { type: "keyUp", key, code, windowsVirtualKeyCode: keyCode });
  }
}

/* Elements that animate in, and whether any visible/passed one is still hidden. */
const VISIBILITY = `(() => {
  const hidden = (n) => parseFloat(getComputedStyle(n).opacity) < 0.95;
  const onOrAbove = (n) => n.getBoundingClientRect().top < innerHeight * 0.92;
  const label = (n) => ((n.closest('section,footer') || {}).id || '?') + ' ' + (String(n.className).split(' ')[0] || n.tagName);
  const animated = [...document.querySelectorAll('[data-reveal], .st-word'),
                    ...[...document.querySelectorAll('[data-reveal-group]')].flatMap((g) => [...g.children])];
  const stuckCounters = [...document.querySelectorAll('[data-count]')]
    .filter((n) => onOrAbove(n) && parseFloat(n.dataset.count) > 0 && /^\\D*0(\\.0+)?\\D*$/.test(n.textContent.trim()));
  const stuck = animated.filter((n) => onOrAbove(n) && hidden(n)).map(label).concat(stuckCounters.map((n) => 'counter ' + label(n)));
  return { scrollY: Math.round(scrollY), atBottom: scrollY >= document.documentElement.scrollHeight - innerHeight - 2,
           released: document.body.classList.contains('loaded'), stuck, total: animated.length };
})()`;

/** Wait for the preloader, then until nothing on/above the screen is hidden (or time runs out). */
async function settled(page, timeoutMs = 9000) {
  const start = Date.now();
  let state;
  do {
    state = await page.evaluate(VISIBILITY);
    if (state.released && state.stuck.length === 0) return state;
    await sleep(150);
  } while (Date.now() - start < timeoutMs);
  return state;
}

/* ---- the checks ------------------------------------------------------------- */
const VIEWPORTS = {
  desktop: { width: 1280, height: 800, deviceScaleFactor: 1, mobile: false },
  phone: { width: 375, height: 812, deviceScaleFactor: 2, mobile: true },
};

async function runChecks(page) {
  const results = [];
  const record = (name, ok, details = "") => results.push({ name, ok, details });

  for (const [viewportName, metrics] of Object.entries(VIEWPORTS)) {
    await page.send("Emulation.setDeviceMetricsOverride", metrics);
    const tag = (name) => `${viewportName}: ${name}`;

    // 1. Scroll the whole home page with the mouse wheel.
    await page.evaluate("history.scrollRestoration = 'manual'; true");
    await page.goto(BASE + "/");
    let state = await settled(page);
    record(tag("home loads, nothing on screen hidden"), state.released && state.stuck.length === 0, state.stuck.join(", "));
    for (let i = 0; i < 160 && !state.atBottom; i++) {
      await page.wheel(metrics.height * 0.35);
      await sleep(90);
      if (i % 10 === 9) state = await page.evaluate(VISIBILITY);
    }
    await sleep(2200);
    state = await page.evaluate(VISIBILITY);
    record(tag("wheel scroll to the bottom reveals everything"), state.atBottom && state.stuck.length === 0,
      state.atBottom ? state.stuck.join(", ") : `stopped at ${state.scrollY}px`);

    // 2. Reload half-way down (the browser restores the position).
    await page.evaluate("history.scrollRestoration = 'auto'; window.lenis ? window.lenis.scrollTo(document.getElementById('websites') || document.getElementById('work'), { immediate: true }) : null; true");
    await sleep(1500);
    await page.reload();
    state = await settled(page);
    record(tag(`reload half-way down (at ${state.scrollY}px) shows everything`), state.released && state.stuck.length === 0, state.stuck.join(", "));

    // 3. Jump to the bottom with the End key.
    await page.evaluate("history.scrollRestoration = 'manual'; true");
    await page.goto(BASE + "/");
    await settled(page);
    await page.key("End", "End", 35);
    await sleep(2500);
    state = await page.evaluate(VISIBILITY);
    record(tag("End key jump shows everything on and above the screen"), state.stuck.length === 0, state.stuck.join(", "));
  }

  // 4. Inner pages load without errors.
  await page.send("Emulation.setDeviceMetricsOverride", VIEWPORTS.desktop);
  await page.goto(BASE + "/projects");
  await sleep(1500);
  const projectUrl = await page.evaluate("(document.querySelector('a.work__link') || {}).href || null");
  record("/projects lists projects", Boolean(projectUrl));
  if (projectUrl) {
    await page.goto(projectUrl);
    await sleep(1500);
    const title = await page.evaluate("(document.querySelector('h1') || {}).textContent || ''");
    record("project page renders", title.trim().length > 0, projectUrl);
  }

  // 5. Nothing went wrong in the browser on any page.
  record("no JS errors, console errors or failed requests", page.problems.length === 0, [...new Set(page.problems)].join(" | "));
  return results;
}

/* ---- main --------------------------------------------------------------------- */
const browserPath = findBrowser();
const profile = mkdtempSync(join(tmpdir(), "rakiza-browser-check-"));
const port = 9400 + Math.floor(Math.random() * 400);
const browser = spawn(browserPath, [
  ...(HEADED ? ["--window-size=1320,920"] : ["--headless=new"]),
  `--remote-debugging-port=${port}`, `--user-data-dir=${profile}`,
  "--no-first-run", "--no-default-browser-check", "--disable-extensions",
  // Keep animating even if the window is covered by other windows.
  "--disable-backgrounding-occluded-windows", "--disable-renderer-backgrounding", "--disable-background-timer-throttling",
  "about:blank",
], { stdio: "ignore" });

let exitCode = 1;
try {
  let targets;
  for (let i = 0; i < 60 && !targets; i++) {
    try { targets = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json(); } catch { await sleep(250); }
  }
  if (!targets) throw new Error("the browser did not start");
  const page = new Page(targets.find((t) => t.type === "page").webSocketDebuggerUrl);
  await page.connect();

  console.log(`Checking ${BASE} with ${browserPath} (${HEADED ? "visible window" : "headless"})\n`);
  const results = await runChecks(page);
  for (const r of results) console.log(`${r.ok ? "✔" : "✘"} ${r.name}${!r.ok && r.details ? "\n    → " + r.details : ""}`);
  const failed = results.filter((r) => !r.ok).length;
  console.log(`\n${results.length - failed}/${results.length} checks passed.`);
  exitCode = failed ? 1 : 0;
} catch (error) {
  console.error("Browser check could not run:", error.message);
} finally {
  browser.kill();
  await sleep(800);
  try { rmSync(profile, { recursive: true, force: true }); } catch { /* the OS may still hold a file; harmless */ }
  process.exit(exitCode);
}
