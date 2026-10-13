# Master Documentation Index & Overview Guide

This document provides a consolidated catalog of all Markdown (`.md`) documentation files across the **GRID (LinkedIn-style Professional Platform)** codebase. Use this guide to quickly understand what each file contains, its primary purpose, and when to reference it.

---

## Quick Navigation Table

| File Path | Category | Primary Purpose & Use Case |
|---|---|---|
| `changes.md` | **Audit & Changelog** | Complete chronological ledger of every single feature, bug fix, file modification, and line-by-line code map. |
| `CLAUDE.md` | **System Architecture** | Master architectural blueprint for GRID: CodeIgniter 4 backend, React 18 frontend, file paths, and coding standards. |
| `PROJECT_OVERVIEW.md` | **High-Level Specs** | Business and technical high-level vision for the unified professional networking platform. |
| `plan.md` | **Feature Plan** | Original specification and execution roadmap for the **Profile Space** (Header, Hero, Relational Sections, Modals). |
| `plan_home.md` | **Feature Plan** | Specification and roadmap for the **Home Feed & Network Dashboard** (Post Composer, Filters, Visiting Cards). |
| `QUERIES.md` | **Database & SQL** | Reference collection of database queries, table relationships, and data inspection scripts. |
| `grid_schema.sql` | **Database Schema** | Pure SQL DDL script to create all 12+ tables in `grid_db` (Profiles, Messages, Posts, Sections, Jobs, Skills). |
| `database_architecture_reference.md` | **Database Architecture** | Deep-dive technical breakdown of `grid_db` tables, composite indexes, primary keys, and O(1) performance guarantees. |
| `database_changes.md` | **Database & API Mapping** | Detailed technical audit mapping MySQL tables to their corresponding PHP models, API endpoints, and React components. |
| `API_SETUP_WIZARD.md` | **API Documentation** | Developer guide for the Onboarding Profile Setup Wizard endpoint (`/profile/wizard-setup`), payload formats, and status flags. |
| `CLAUDE_TASK_QUEUE.md` | **Task Tracking** | Milestones, backlog queue, and completion status of platform features. |
| `CLAUDE_OUTPUT.md` | **Testing & Output** | Verification output, API contract testing results, and feature sign-off reports. |
| `react/DESIGN_SYSTEM.md` | **UI / UX Design** | Complete visual design system: glassmorphic refraction tokens, Aurora gradient animations, typography, and colors. |
| `react/PROFILES_AND_COMPANY_ARCHITECTURE.md` | **Component Specs** | Architecture for Candidate vs. Recruiter modes, Company Hubs, and 1:N relational portfolio sections. |

---

## Detailed File Summaries

### 1. Root-Level Core Documentation

#### 📜 `changes.md`
* **What it does**: Functions as the platform's permanent chronological changelog and source-of-truth ledger.
* **Key Contents**:
  - Over 415 structured entries detailing every code edit since inception.
  - Detailed line numbers, file paths, root-cause analyses, and resolutions for all bug fixes.
  - Verification logs (e.g. real-time WebSocket setup, home feed 500 fixes, model renames, and avatar isolation).
* **When to use**: Whenever you need to track what was changed, find when a specific feature was introduced, or trace line-by-line diffs.

#### 🏛️ `CLAUDE.md`
* **What it does**: The definitive architectural guide for developers working on the GRID system.
* **Key Contents**:
  - Full ASCII stack diagram illustrating the flow between React 18, CodeIgniter 4, REST APIs, and MySQL.
  - Directory mapping for backend controllers (`GRID_Controllers/`), models (`GRID_MODEL/`), and React views (`react/`).
  - Database schema rules for `grid_db` vs. the main CRM `users` table.
  - Step-by-step instructions for compiling bundles (`npm run build`).
* **When to use**: Onboarding new developers or reviewing the overall system structure.

#### 📋 `PROJECT_OVERVIEW.md`
* **What it does**: Outlines the high-level business vision and user personas.
* **Key Contents**:
  - The purpose of transforming the application into a high-performance LinkedIn-style professional ecosystem.
  - User personas (candidates looking for jobs, recruiters running ATS pipelines, engineers sharing artifacts).
* **When to use**: High-level reviews and presentations to product leads or stakeholders.

#### 🗺️ `plan.md` & `plan_home.md`
* **What it does**: The implementation blueprints for the two primary pages of GRID.
* **Key Contents**:
  - `plan.md`: Blueprint for Profile Identity, Banner Customization, Relational Sections (Experience, Education, Projects, Skills), and Modal Interactions.
  - `plan_home.md`: Blueprint for the Three-Column Home Layout, Post Composer, Feed Filter Tabs (All, Tech Artifacts, Opportunities, Marketplace), and Visiting Card widgets.
* **When to use**: Reviewing planned component hierarchies and modal behavior.

#### 🔍 `QUERIES.md`
* **What it does**: Contains essential SQL queries and database testing snippets.
* **Key Contents**:
  - Table join examples between `factor_db2.users` and `grid_db.grid_profiles`.
  - Scripts for seeding test users, testing connections, and inspecting unread messages.
* **When to use**: Database administration, querying test data, or creating database seed scripts.

---

### 2. GRID Module Documentation (`app/Views/GRID/`)

#### 💾 `database_architecture_reference.md`
* **What it does**: Explains the technical and performance rationale behind the database architecture.
* **Key Contents**:
  - Target complexity metrics: $O(1)$ hash/primary key lookups, $O(\log N)$ composite B-tree indexes.
  - Normalization strategy: strict 3NF for 1:N relations (education, experiences, projects) alongside lean JSON caching columns.
  - Cross-database foreign key handling between `grid_db` and the main CRM database.
* **When to use**: Performance tuning, index optimization, and database reviews.

#### 🗄️ `database_changes.md`
* **What it does**: Detailed technical mapping between MySQL tables, PHP Models, REST Endpoints, and React UI components.
* **Key Contents**:
  - Exact column listings for all tables.
  - Cross-reference table showing which React component reads or updates which database table and controller method.
* **When to use**: Tracing the full end-to-end data flow from a UI click down to the database row.

#### 🧙 `API_SETUP_WIZARD.md`
* **What it does**: Dedicated documentation for the First-Time User Onboarding Wizard API.
* **Key Contents**:
  - Route: `POST /grid/api/profile/wizard-setup`.
  - JSON payload parameters (`headline`, `pronoun`, `bio`, `role`, `is_open_to_work`, `location`, `about_skills`).
  - Handling of automatic `grid_id` generation and `is_wizard_completed` flag toggling.
* **When to use**: Testing or modifying the onboarding flow.

#### 🎨 `react/DESIGN_SYSTEM.md`
* **What it does**: Establishes the visual language, branding, and design tokens of the platform.
* **Key Contents**:
  - CSS glassmorphism formulas (`backdrop-filter: blur(16px) saturate(190%)`, frosted specular rim highlights).
  - Aceternity-inspired Aurora background animation keyframes.
  - Typography scales (`Plus Jakarta Sans`, `Inter`, and `JetBrains Mono`).
  - Dark mode and light mode color palette tokens.
* **When to use**: Styling new components or adjusting UI visual aesthetics.

#### 🏢 `react/PROFILES_AND_COMPANY_ARCHITECTURE.md`
* **What it does**: Technical design for user profile views, recruiter pipelines, and company workspace pages.
* **Key Contents**:
  - Specification for candidate view vs. recruiter view.
  - Job application tracking pipeline (ATS) state transitions (`Applied`, `Reviewing`, `Interviewing`, `Offered`, `Rejected`).
  - Separation between the logged-in user's own profile and external peer profiles (`GridPeerProfileApp.jsx`).
* **When to use**: Extending job posting, candidate tracking, or company profile features.

---

### 3. Agent & Developer Guidelines (`.agents/`)

* [`.agents/rules/grid-design-system.md`](file:///c:/Program%20Files/Ampps/www/app/.agents/rules/grid-design-system.md): Coding rules enforcing premium visual polish, prohibiting plain default HTML colors, and mandating rich micro-animations.
* [`.agents/skills/backend-security-coder/SKILL.md`](file:///c:/Program%20Files/Ampps/www/app/.agents/skills/backend-security-coder/SKILL.md): Best practices for input sanitization, CSRF, SQL injection prevention, and secure API coding.
* [`.agents/skills/database-admin/SKILL.md`](file:///c:/Program%20Files/Ampps/www/app/.agents/skills/database-admin/SKILL.md): Database performance engineering, indexing guidelines, and schema migrations.
* [`.agents/skills/code-reviewer/SKILL.md`](file:///c:/Program%20Files/Ampps/www/app/.agents/skills/code-reviewer/SKILL.md): Guidelines for algorithmic complexity checks, memory leak prevention, and CodeIgniter/React code reviews.

---

### 4. Package Delivery Checklist for Team Lead

When packaging the GRID module for deployment on the team lead's server or another environment, include these 4 folders and the SQL schema:

| Directory / File | Destination on Target Server | What It Contains |
| :--- | :--- | :--- |
| **`app/Config/GRID_Config/`** | `app/Config/GRID_Config/` | All web page routes and 25+ REST API endpoint mappings. |
| **`app/Controllers/GRID_Controllers/`** | `app/Controllers/GRID_Controllers/` | Web controllers (`GridController.php`) and API controllers (`GridApiController.php`). |
| **`app/Models/GRID_MODEL/`** | `app/Models/GRID_MODEL/` | All 9 high-performance database models (`Grid_posts_model.php`, `Grid_profile_model.php`, etc.). |
| **`app/Views/GRID/`** | `app/Views/GRID/` | All React components, production build (`dist/bundle.js`), and server view templates (`index.php`, `profile.php`, etc.). |
| **`app/Views/GRID/grid_schema.sql`** | Import into MySQL | DDL script to create the `grid_db` database and all required tables. |

#### Activation Step on Lead Server:
Add this 3-line loader at the bottom of the target server's `app/Config/Routes.php`:
```php
$gridRoutesFile = APPPATH . 'Config/GRID_Config/Routes.php';
if (is_file($gridRoutesFile)) {
    require $gridRoutesFile;
}
```

