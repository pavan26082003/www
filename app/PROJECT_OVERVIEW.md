# eFactor — Project Overview

> A quick-start guide for new developers joining this project.

---

## What Is This Project?

**eFactor** is a web-based platform that combines two things into one application:

1. **IoT Device Management** — Manage smart hardware modules (WiFi, Gateway, RF/radio), configure device UI pages, link node IDs, control devices via WebSocket/MQTT, and generate device stickers/QR forms.
2. **Business CRM** — A full client/vendor portal covering projects, invoices, estimates, orders, support tickets, HR (attendance, leaves), expenses, and internal messaging.

The platform is built for a company that sells and manages IoT hardware to vendors and clients. Those vendors/clients log in to configure and monitor their devices through this web portal.

---

## Is This a Full Stack Project?

**Yes — classic PHP full stack.** Everything runs in PHP on the server. The browser receives finished HTML. This is called **server-side rendering (SSR)**.

```
Browser request
      ↓
Apache (AMPPS)
      ↓
index.php → CodeIgniter Router
      ↓
Controller (PHP) — handles logic
      ↓
Model (PHP) — queries MySQL
      ↓
View (PHP + HTML) — rendered on server
      ↓
HTML sent back to browser
      ↓
jQuery / Bootstrap runs in browser
```

**What it is NOT:**
- Not a REST API + separate frontend (like Laravel + React)
- Not a SPA (Single Page Application)
- No Node.js, no npm build pipeline, no TypeScript

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Language** | PHP 7.4+ |
| **Framework** | CodeIgniter 4 |
| **Database** | MySQL (`factor_db2`) |
| **Server** | AMPPS (Apache + MySQL + PHP) |
| **Frontend** | Server-rendered PHP/HTML + jQuery + Bootstrap 5 |
| **Charts** | Chart.js |
| **Tables** | DataTables |
| **Icons** | Feather Icons |
| **PDF Generation** | TCPDF (ThirdParty) |
| **Payments** | PayPal, Paytm, Stripe |
| **Real-time** | WebSocket client, MQTT, Server-Sent Events (SSE) |
| **Other libs** | Google API client, php-zip, scssphp |

---

## How to Run the Project

### Prerequisites

- **AMPPS** installed and running (Apache + MySQL services must be ON)
- PHP 7.4+ (comes with AMPPS)
- Project folder must be inside `C:\Program Files\Ampps\www\`

### Steps

1. **Start AMPPS** — Open the AMPPS control panel and start both **Apache** and **MySQL**.

2. **Set up the database**
   - Open **phpMyAdmin**: `http://localhost/phpmyadmin`
   - Create a database named `factor_db2`
   - Import the SQL dump file (ask your team for the latest DB export)

3. **Verify database config** — Open `app/Config/Database.php` and confirm:
   ```php
   'hostname' => 'localhost',
   'username' => 'root',
   'password' => 'mysql',
   'database' => 'factor_db2',
   'port'     => 3306,
   ```

4. **Install Composer dependencies** (if not already done):
   ```bash
   cd "C:\Program Files\Ampps\www\app"
   composer install
   ```

5. **Open the app** — Log in first, then navigate freely:
   ```
   http://localhost/index.php/signin
   ```

---

## How URLs Work

Every URL follows this pattern:

```
http://  localhost  /  index.php  /  dashboard
  │          │              │            │
protocol   host        entry point    route
```

| Part | What it is | Where it lives |
|---|---|---|
| `localhost` | Your AMPPS Apache server | AMPPS control panel |
| `index.php` | Single entry point for the entire app | `C:\Program Files\Ampps\www\index.php` |
| `dashboard` | Route → maps to `Dashboard` controller → `Views/dashboards/index.php` | `app/Config/Routes.php` |

### Every Major Page URL

```
http://localhost/index.php/signin          ← Login (start here)
http://localhost/index.php/dashboard       ← Dashboard
http://localhost/index.php/items           ← Products / IoT Devices
http://localhost/index.php/orders          ← Orders
http://localhost/index.php/clients         ← Clients / Vendors
http://localhost/index.php/invoices        ← Invoices
http://localhost/index.php/estimates       ← Estimates
http://localhost/index.php/projects        ← Projects
http://localhost/index.php/tickets         ← Support Tickets
http://localhost/index.php/messages        ← Internal Messages
http://localhost/index.php/team_members    ← Team / Staff
http://localhost/index.php/leaves          ← Leave Applications
http://localhost/index.php/attendance      ← Attendance
http://localhost/index.php/expenses        ← Expenses
http://localhost/index.php/events          ← Events / Calendar
http://localhost/index.php/leads           ← Leads
http://localhost/index.php/timeline        ← Timeline / Feed
http://localhost/index.php/settings        ← App Settings
```

> You must be logged in to access any page. Always start at `/signin`.

---

## Project Structure

```
C:\Program Files\Ampps\www\
├── index.php                  ← Single entry point (do not edit)
└── app/
    ├── Config/
    │   ├── Routes.php         — URL routing (auto-routing + explicit routes)
    │   ├── Database.php       — DB connection settings
    │   ├── App.php            — Base URL, session, app config
    │   └── Rise.php           — Custom app settings store
    ├── Controllers_working/   — ✅ ACTIVE controllers (~110 files, use these)
    ├── Controllers/           — ⚠️  Dated backup/WIP versions (not active)
    ├── Models/                — ~50 models (Users, Items, Orders, Invoices, etc.)
    ├── Views/                 — All HTML/PHP view files
    ├── Libraries/             — Template engine, Google, PayPal, Paytm, etc.
    ├── Helpers/               — general, currency, date_time, widget, etc.
    ├── ThirdParty/            — TCPDF, Stripe, Google API, php-zip, scssphp
    └── Common.php             — Global helper functions
```

> **Important**: Always work in `Controllers_working/`. The `Controllers/` folder contains old dated backups and is NOT the active code.

---

## How the UI Renders (Mental Model)

```
Request → Controller → Template::rander("module/view", $data)
                              ↓
                    Views/layout/index.php     ← master HTML shell
                    ├── includes/head.php      ← CSS + JS bundles
                    ├── includes/topbar.php    ← top nav bar
                    ├── includes/left_menu.php ← sidebar
                    └── [module view]          ← e.g. dashboards/index.php
```

---

## UI Files — What to Touch and What to Leave Alone

### ✅ Files You SHOULD Edit for UI Changes

| File | What it controls |
|---|---|
| `public/assets/css/custom-style.css` | **Your main CSS file.** Loaded last on every page — your rules always win. Override colors, fonts, spacing here. |
| `Views/includes/custom_head.php` | Empty placeholder for global `<link>` / `<style>` additions. Safe to add anything here. |
| `Views/includes/topbar.php` | Top navigation bar (logo, user dropdown, search, notifications) |
| `Views/includes/left_menu.php` | Sidebar navigation (colors, labels, icons already customized here) |
| `Views/layout/index.php` | Master HTML shell wrapping every page |
| `Views/includes/head.php` | Global CSS/JS bundle loading |
| `Views/includes/footer.php` | Footer on public-facing pages |
| `Views/<module>/index.php` | Page-specific content for each section |

#### Page-specific view files

| What you want to change | File to edit |
|---|---|
| Dashboard | `Views/dashboards/index.php` |
| Login page | `Views/signin/index.php` + `Views/signin/signin_form.php` |
| Products / IoT Devices | `Views/items/index.php` |
| Orders | `Views/orders/index.php` |
| Clients | `Views/clients/index.php` |
| Invoices | `Views/invoices/index.php` |
| Messages / Chat | `Views/messages/index.php` |
| Settings | `Views/settings/general.php` |

### ❌ Files You Should NOT Touch

| File/Folder | Why |
|---|---|
| `public/assets/css/app.all.css` | Auto-generated merged file — changes get overwritten |
| `public/assets/js/app.all.js` | Auto-generated merged file — changes get overwritten |
| `public/assets/bootstrap/` | Bootstrap framework — don't edit directly |
| `ThirdParty/` | Third-party libraries — never edit |
| `vendor/` | Composer packages — never edit |
| `Libraries/Template.php` | Core rendering engine — changing this breaks all views |
| `Views_13-05/` | Old backup snapshot — not active |
| `Views/items_18-01-25/`, `Views/Items_22-07-25_1/`, `Views/items_27-05/` | Dated backup folders — not active |
| `Views/messages_*/` (dated folders) | Backup versions — not active |
| `Views/includes_30-07-24/` | Old backup of includes — not active |
| `Views/includes/topbar_original.php`, `topbar_28-02-25.php` | Backup files — not active |
| `Views/includes/left_menu_original.php` | Backup file — not active |
| `Controllers/` dated files (e.g. `Items_10-3-25.php`) | Backup files — not active |
| `Models/` dated files (e.g. `Items_model_9-11-24.php`) | Backup files — not active |
| `Config/` | Framework config — only touch if you know what you're changing |

---

## Key Controllers (Controllers_working/)

| Controller | What It Does |
|---|---|
| `App_Controller.php` | Base controller — loads all models, helpers, settings, session. All controllers extend this. |
| `Dashboard.php` | Main landing page. IoT stats, monthly charts, role-specific dashboards. |
| `Items.php` | Core IoT controller. Device models, UI page configs, node IDs, sticker forms, WebSocket/MQTT pages. |
| `Orders.php` | Order lifecycle — create, view, edit, delete, PDF, client portal. |
| `OrderController.php` | REST API for orders — save, update status, SSE streaming. |
| `Clients.php` | Client/vendor CRUD, contact profiles, per-client tabs. |
| `Invoices.php` | Full invoice management — create, edit, recurring, PDF, PayPal/Paytm. |
| `Projects.php` | Project management with milestones, tasks, timesheets, files. |
| `Tickets.php` | Support ticket system. |
| `Messages.php` | Internal messaging between users. |
| `Team_members.php` | Staff management, attendance, leave applications. |
| `Settings.php` | App-wide configuration. |
| `Signin.php` / `Signup.php` | Authentication — login and registration. |
| `DeviceController.php` | Device page display routing. |
| `Forms.php` | Dynamic form builder for device UI. |

---

## User Types & Roles

| User Type | Description |
|---|---|
| `User` | Internal staff/team members |
| `client` | External clients accessing their portal |
| `manufacturer` | Hardware manufacturers |
| `devloper` | Developers (typo in codebase — keep as-is) |
| `Brand` | Brand-level users |

Access control is handled by `Security_Controller` using `init_permission_checker()` and module-level feature flags stored in the settings DB.

---

## Important Routes

Auto-routing is enabled, so `http://localhost/index.php/ControllerName/methodName` works by default. Key explicit routes:

| URL | Controller Method |
|---|---|
| `GET /` | `Dashboard::index` |
| `GET /orders` | `OrderController::getOrders` |
| `POST /orders/save` | `OrderController::save` |
| `GET /orders/stream` | `OrderController::streamOrders` (SSE) |
| `GET /items/sticker_form` | `Items::sticker_form` |
| `GET /items/view/:id` | `Items::viewStickerForm` |
| `GET /dashboard/notifications` | `Dashboard::notifications` |
| `GET /device/page/:id/:page` | `DeviceController::display` |

---

## Notes for New Developers

- This file is the only README — it didn't exist before.
- Always work in `Controllers_working/` and `Models/` (without date suffixes). Dated files are backups.
- The codebase is Rise CRM (open-source) with heavy custom IoT features layered on top.
- Real-time device communication uses WebSocket and MQTT — see `Items.php` and `OrderController.php`.
- External API calls go to `efactor.tech/v8/newapi.php` for live device node data.
- Ask the team for a MySQL dump of `factor_db2` to get the DB schema.
- PHP files cannot be opened directly in a browser — they must go through AMPPS/Apache.
