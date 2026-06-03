/* Tiny zero-dependency static server for local preview.
   Usage:  node serve.mjs [port]      (default 8080)
   Then open the printed URL. (Needed because ES modules don't run over file://) */
import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { join, dirname, extname, normalize } from "node:path";
import { fileURLToPath } from "node:url";

const root = dirname(fileURLToPath(import.meta.url));
const PORT = Number(process.argv[2]) || 8080;
const MIME = {
  ".html": "text/html", ".js": "text/javascript", ".mjs": "text/javascript",
  ".css": "text/css", ".svg": "image/svg+xml", ".json": "application/json",
  ".png": "image/png", ".jpg": "image/jpeg", ".ico": "image/x-icon",
  ".webp": "image/webp", ".woff2": "font/woff2", ".pdf": "application/pdf",
};

createServer(async (req, res) => {
  try {
    let rel = decodeURIComponent(req.url.split("?")[0]);
    if (rel === "/") rel = "/index.html";
    const path = normalize(join(root, rel));
    if (!path.startsWith(root)) { res.writeHead(403).end(); return; }
    const body = await readFile(path);
    res.writeHead(200, { "content-type": (MIME[extname(path)] || "application/octet-stream") + "; charset=utf-8" });
    res.end(body);
  } catch {
    res.writeHead(404, { "content-type": "text/plain" }).end("not found");
  }
}).listen(PORT, () => console.log(`Teamora portfolio → http://localhost:${PORT}/`));
