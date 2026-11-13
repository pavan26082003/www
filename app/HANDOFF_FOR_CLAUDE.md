# eFactor / AMPPS session handoff (2026-11-04)

## Project
- **App:** eFactor (CodeIgniter 4 / Rise CRM + IoT)
- **Path:** `C:\Program Files\Ampps\www\app`
- **Web root:** `C:\Program Files\Ampps\www`
- **Entry URL:** `http://localhost/index.php/signin`
- **Stack:** AMPPS (Apache + MySQL + PHP 8.2), MySQL user `root` / pass `mysql`

## What we fixed (AMPPS / browser)
1. **Apache would not start** — `httpd-ssl.conf` still had `{$path}` placeholders; SSL certs missing.
   - Fixed placeholders → `C:/Program Files/Ampps`
   - Generated self-signed certs:
     - `C:\Program Files\Ampps\apache\conf\ssl_crt\server.crt`
     - `C:\Program Files\Ampps\apache\conf\ssl_key\server.key`
2. **403 Forbidden on `http://localhost`** — Chrome uses IPv6 (`::1`); dummy vhosts pointed at missing DocumentRoots.
   - Replaced `C:\Program Files\Ampps\apache\conf\extra\httpd-vhosts.conf` with localhost vhosts for `*:80` and `[::]:80` → DocumentRoot `C:/Program Files/Ampps/www`
   - Restart Apache by killing `httpd` and starting `C:\Program Files\Ampps\apache\bin\httpd.exe` (not a Windows service named Apache2.4)
3. Created empty CI writable tree: `C:\Program Files\Ampps\www\writable\{cache,logs,session,uploads,debugbar}`
4. Created empty DBs (no tables/data yet): `factor_db2`, `grid_db`, `grid_chat_db`

## Current status
- AMPPS Apache works; `http://localhost` → 302 → `/index.php/signin` (200)
- **App still needs real DB dumps from server** — empty DBs exist; tables/data do not
- Sign-in page loads but login/app will fail without imported schema/data
- User had open: `app\Database\iot_schema_stub.sql` (stub only, not full dump)

## Missing from server (bring these)
| Item | Why |
|------|-----|
| SQL dump `factor_db2` | Main app DB (required) |
| SQL dump `grid_db` | GRID module |
| SQL dump `grid_chat_db` | GRID chat |
| `app/Controllers/OrderController.php` | Mentioned in docs; missing locally |
| `app/Controllers_working/` | Docs say this is active set; local uses `Controllers/` instead |
| Upload contents under `files/avatar`, `files/ticket_files` | Optional media |

## Important config
- DB: `app/Config/Database.php` → `factor_db2`, `grid_db`, `grid_chat_db`
- Controllers live in `app/Controllers/` (key ones present: App_Controller, Signin, Dashboard, Items, etc.)
- Overview doc: `app/PROJECT_OVERVIEW.md` (some Controllers_working notes may be outdated vs this machine)

## Full chat transcript (raw)
`C:\Users\WorkStation\.cursor\projects\c-Program-Files-Ampps-www-app\agent-transcripts\793bc4e7-5507-46ce-93fc-ebaf49b15259\793bc4e7-5507-46ce-93fc-ebaf49b15259.jsonl`




