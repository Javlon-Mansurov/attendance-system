// Сервери хурди таълимӣ: файлҳои public/ + stub-и API (/api/records?q=...)
import http from 'node:http';
import { readFile } from 'node:fs/promises';
import { extname, join, normalize } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('.', import.meta.url));
const types = { '.html': 'text/html; charset=utf-8', '.css': 'text/css', '.js': 'text/javascript', '.json': 'application/json' };

http.createServer(async (req, res) => {
  const url = new URL(req.url, 'http://localhost');

  if (url.pathname === '/api/records') {
    const q = (url.searchParams.get('q') ?? '').toLowerCase();
    const all = JSON.parse(await readFile(join(root, 'api/records.json'), 'utf8'));
    const found = all.filter((r) => r.full_name.toLowerCase().includes(q));
    // сунъӣ: таъхир, то debounce/abort намоён шавад
    setTimeout(() => {
      res.writeHead(200, { 'Content-Type': 'application/json; charset=utf-8' });
      res.end(JSON.stringify(found));
    }, 300);
    return;
  }

  const rel = url.pathname === '/' ? 'index.html' : normalize(url.pathname).replace(/^(\.\.[/\\])+/, '');
  try {
    const body = await readFile(join(root, 'public', rel));
    res.writeHead(200, { 'Content-Type': types[extname(rel)] ?? 'application/octet-stream' });
    res.end(body);
  } catch {
    res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
    res.end('Not found');
  }
}).listen(3000, () => console.log('http://localhost:3000'));
