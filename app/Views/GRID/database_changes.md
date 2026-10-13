  # Database Changes & Schema Evolution Log

This document tracks all database modifications, migrations, new tables, column additions, and architectural indexing in structured chronological order.

---

## 1. System Overview & Conventions
- **Database Engine**: MySQL 5.7+ / MariaDB (`InnoDB`)
- **Default Database**: `factor_db2`
- **Charset / Collation**: `utf8mb4` / `utf8mb4_unicode_ci`
- **Primary Keys**: `INT UNSIGNED AUTO_INCREMENT` (or `BIGINT UNSIGNED`)
- **Foreign Key Indexing**: Explicit B-Tree indexes on all relational columns
- **Time Complexity Standard**: $O(1)$ on unique handles and primary keys, $O(\log N)$ on indexed searches.

---

## 2. Table Index & Purpose

| Table Name | Primary Role | Domain |
| :--- | :--- | :--- |
| **`users`** *(Legacy)* | Core Authentication & Session Credentials | Shared CRM / Global Auth |
| **`grid_profiles`** | Professional bio, headline, role, badges, and universal `grid_id` | Professional Space / Global Hub |
| **`grid_skills`** | Normalized master dictionary of tech & business skills (43 seeds) | Taxonomy & Autocomplete |
| **`grid_user_skills`** | Pivot table associating users to skills with endorsement counts | Career Profile & Matching |
| **`grid_experiences`** | Work history, company associations, and job titles | Profile Timeline |
| **`grid_education`** | Educational institutions, degrees, and graduation years | Profile Timeline |
| **`grid_connections`** | 2-Way network graph with canonical ordering `(LEAST, GREATEST)` | Verified Peer Graph |
| **`grid_follows`** | 1-Way subscriptions to peers, companies, newsletters, and spaces | Social Feed Graph |
| **`grid_companies`** | Organization branding, company size, and employee links | Corporate Pages |
| **`grid_jobs`** | Active job postings with filters for discipline, location, workplace | Jobs Hub |
| **`grid_job_applications`** | Applicant tracking system pipeline (`applied` &rarr; `interview` &rarr; `offered`) | Recruiter Pipeline & Candidate Tracker |
| **`grid_conversations`** | Canonical 1-on-1 conversation registry (`user_one_id` &lt; `user_two_id`) | Circle Connect Direct Messaging |
| **`grid_messages`** | Individual messages within a conversation, with read-state | Circle Connect Direct Messaging |
| **`grid_posts`** | Home Feed posts (content, tags, media, engagement counters) | Home Feed / Timeline |

---

## 3. Migration Changelog (Chronological)

### [Migration 001] Initial GRID Schema Provisioning
- **Date**: September 15, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **Changes**:
  - Created 11 core tables listed above.
  - Seeded 43 master industry skills in `grid_skills`.
  - Added unique constraint on `grid_connections (user_one_id, user_two_id)` to prevent reciprocal duplicate records.

---

### [Migration 002] Universal `grid_id` Anchor Migration
- **Date**: September 16, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **Target Table**: `grid_profiles`
- **SQL Statement**:
  ```sql
  ALTER TABLE `grid_profiles` 
  ADD COLUMN `grid_id` VARCHAR(48) NULL UNIQUE AFTER `user_id`,
  ADD INDEX `idx_grid_id` (`grid_id`);
  ```
- **Rationale**:
  - Provides a permanent, URL-safe vanity handle (e.g. `pavan-kumar-8421`) for deep linking, QR visiting cards, and direct message indexing between Professional Space and Circle Connect.
- **Application Layer**:
  - `Grid_profile_model::generate_unique_grid_id($fullName)` — slugifies a display name and appends a random 4-digit discriminator, retrying on collision.
  - `Grid_profile_model::get_profile_by_user_id()` — self-heals any existing profile row that predates this column by generating and persisting a `grid_id` on first read.
  - `Grid_profile_model::get_profile_by_grid_id()` — $O(1)$ lookup by handle via the new unique index.
  - `GridApiController::wizardSetup()` — accepts an optional user-supplied `grid_id` (sanitized to lowercase alphanumeric + hyphens; falls back to auto-generation on collision with another user), reuses an existing handle on repeat submissions, persists it, and returns it in the response.
  - `GridApiController::getProfile($targetIdentifier)` — resolves a numeric `user_id` or a string `grid_id` (via `ctype_digit` dispatch), returning `404` if the handle doesn't resolve.
  - `Config/Routes.php` — `profile/(:num)` widened to `profile/(:any)` on the `GET /grid/api/profile/...` route.
- **Verified**: `php -l` clean on all touched files; live-tested `POST /grid/api/profile/wizard-setup` (grid_id generated + persisted), `GET /grid/api/profile/{grid_id}`, `GET /grid/api/profile/{user_id}`, and `GET /grid/api/profile/{unknown-handle}` → `404`.

---

### [Migration 003] Recruiter Mode & Job Posting Pipeline (ATS)
- **Date**: September 16, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **Target Tables**: `grid_jobs`, `grid_job_applications`
- **Note**: `grid_jobs`/`grid_job_applications` already had everything the task's abstract column/index list asked for, just under the established names from Migration 001 (`recruiter_user_id` not `poster_user_id`, `candidate_user_id` not `user_id`, `stage` not `status`, `salary_min`/`salary_max`/`salary_currency` for structured pay, `required_skills_csv` not `skills_required`). Reused those rather than introducing duplicate columns. Two real gaps were found and fixed:
- **SQL Statements Executed**:
  ```sql
  ALTER TABLE grid_jobs ADD COLUMN salary_range VARCHAR(255) NULL AFTER salary_currency;

  ALTER TABLE grid_job_applications
    MODIFY COLUMN stage ENUM('applied','reviewing','interview','offered','rejected') NOT NULL DEFAULT 'applied';
  ```
- **Rationale**:
  - `salary_range`: the already-built `PostJobModal.jsx` collects compensation as one free-text field (e.g. `"$150k - $190k • 0.1% Equity"`), which doesn't fit the structured `salary_min`/`salary_max` columns. Added as a new nullable column rather than trying to parse free text into numeric bounds.
  - `stage` ENUM: the original enum (`applied, reviewing, screened, interviewing, offered, rejected`) didn't match the 5-stage pipeline (`applied, reviewing, interview, offered, rejected`) already hardcoded in the existing `RecruiterDashboardView.jsx`. Table had 0 rows, so this was a safe, lossless narrowing to match the real frontend contract.
  - No new indexes were needed — `idx_recruiter_jobs`, `idx_job_status_date`, `idx_job_pipeline`, and `idx_candidate_tracker` (all from Migration 001) already cover every access pattern this feature needs.
- **Application Layer**:
  - `Grid_jobs_model::create_job()`, `get_recruiter_jobs()`, `get_job_applicants()`, `update_application_status()`, `normalize_workplace_type()` (maps PostJobModal's lowercase `remote`/`hybrid`/`onsite` to the DB enum's `Remote`/`Hybrid`/`Onsite` casing).
  - `GridApiController::createJob()`, `getRecruiterJobs()`, `getJobApplicants()`, `updateApplicationStatus()` — response shapes matched directly against `gridApi.js`, `PostJobModal.jsx`, and `RecruiterDashboardView.jsx` (flat applicant objects keyed `status` with nested `user`/`profile`, job objects keyed `applicant_count`), not the task queue's abstract description, since the frontend code is the real contract.
  - Routes: `POST jobs/create`, `GET recruiter/jobs`, `GET recruiter/jobs/(:num)/applicants`, `POST recruiter/applications/status` added to the `grid/api` group in `Config/Routes.php`.
- **Frontend Layer** (Antigravity):
  - `PostJobModal.jsx` — glassmorphic job publishing modal with live database skills autocomplete (`gridApi.suggestSkills`).
  - `RecruiterDashboardView.jsx` — ATS pipeline view with stage filters and one-click status advancement; this component's hardcoded stage ids (`applied`/`reviewing`/`interview`/`offered`/`rejected`) are what the `stage` ENUM migration above was aligned to.
  - `JobsRightRail.jsx` & `JobsTopChipsBar.jsx` — Recruiter mode entry points.
- **Verified**: `php -l` clean on all touched files; live-tested `createJob` → `getRecruiterJobs` → `applyJob` → `getJobApplicants` → `updateApplicationStatus` → re-fetched applicants to confirm the stage change persisted. Error paths confirmed: invalid status → `400`, unknown/unowned job → `404`, missing required create fields → `400`.

---

### [Migration 004] Circle Connect Direct Messaging & `grid_id` Integration
- **Date**: September 16, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **New Tables**: `grid_conversations`, `grid_messages` — created exactly per the task spec (both brand-new, no prior naming convention to reconcile with, unlike Migrations 002/003).
- **SQL Statements Executed**:
  ```sql
  CREATE TABLE grid_conversations (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_one_id INT UNSIGNED NOT NULL,
      user_two_id INT UNSIGNED NOT NULL,
      last_message TEXT NULL,
      last_message_at DATETIME NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY uk_canonical_conv (user_one_id, user_two_id),
      INDEX idx_user_one (user_one_id),
      INDEX idx_user_two (user_two_id),
      INDEX idx_last_message_at (last_message_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  CREATE TABLE grid_messages (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      conversation_id INT UNSIGNED NOT NULL,
      sender_id INT UNSIGNED NOT NULL,
      recipient_id INT UNSIGNED NOT NULL,
      message_body TEXT NOT NULL,
      is_read TINYINT(1) NOT NULL DEFAULT 0,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_conversation (conversation_id, id),
      INDEX idx_unread (recipient_id, is_read)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  ```
- **Application Layer**:
  - `Grid_messages_model::get_or_create_conversation()` — canonicalizes the pair as `(LEAST, GREATEST)`, same pattern as `Grid_connections_model`.
  - `Grid_messages_model::get_user_conversations()` — joins the opponent's identity (`users` + `grid_profiles`) via a raw `IF(user_one_id = ?, user_two_id, user_one_id)` join condition (safe: the user id is cast to `int` before interpolation), with per-conversation unread counts.
  - `Grid_messages_model::get_conversation_messages()` — verifies the requesting user is a participant (returns `null` otherwise, surfaced as a generic `404` rather than distinguishing "not found" from "forbidden", to avoid leaking which conversation ids exist), marks incoming unread messages as read, returns the opponent's identity plus the message list.
  - `Grid_messages_model::send_message()` — creates the conversation if needed and bumps `last_message`/`last_message_at`.
  - `GridApiController::getConversations()`, `getThread()`, `sendMessage()`, `startConversation()` — `sendMessage` resolves the recipient by `recipient_id` or, if absent, by `recipient_grid_id` (reuses `Grid_profile_model::get_profile_by_grid_id()`); `startConversation` dispatches numeric `user_id` vs. string `grid_id` the same way `getProfile()` does.
  - Routes: `GET messages/conversations`, `GET messages/thread/(:num)`, `POST messages/send`, `GET messages/start/(:any)` added to the `grid/api` group.
- **Note**: The messaging UI (`GridMessagingApp.jsx`, `ConversationList.jsx`, `ActiveChatPane.jsx`) is still driven entirely by local mock state (`messagingState.js`) — it hasn't been wired to `gridApi`'s messaging methods yet, even though those client methods already exist in `gridApi.js`. Response shapes here were built to match `gridApi.js`'s call signatures and the task spec directly; Antigravity will need to wire the UI to consume this snake_case `{success, data}` shape (`opponent`, `conversation_id`, `unread_count`, etc.) when that integration happens.
- **Verified**: `php -l` clean on all touched files; live-tested `startConversation` (numeric id) → `sendMessage` → `getConversations` → `getThread`, plus re-fetching the thread to confirm read-marking doesn't duplicate/mutate messages. Error paths confirmed: self-conversation → `400`, nonexistent thread → `404`, empty message body → `400`, unknown `grid_id` → `404`, `recipient_grid_id` resolving to self → `400`.

---

### [Migration 005] Home Feed Posts Backend & Persistence
- **Date**: September 16, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **New Table**: `grid_posts` — created exactly per the task spec.
- **SQL Statement Executed**:
  ```sql
  CREATE TABLE grid_posts (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      content TEXT NOT NULL,
      tags VARCHAR(255) DEFAULT NULL,
      media_url VARCHAR(500) DEFAULT NULL,
      type ENUM('general', 'tech_artifact', 'opportunity', 'marketplace') NOT NULL DEFAULT 'general',
      likes_count INT NOT NULL DEFAULT 0,
      comments_count INT NOT NULL DEFAULT 0,
      reposts_count INT NOT NULL DEFAULT 0,
      created_at DATETIME NOT NULL,
      updated_at DATETIME NOT NULL,
      deleted TINYINT(1) NOT NULL DEFAULT 0,
      INDEX idx_grid_posts_user (user_id),
      INDEX idx_grid_posts_created (created_at DESC)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  ```
- **Application Layer**:
  - `Grid_posts_model::create_post($user_id, $data)` — validates `content` is non-empty, stores `tags` as CSV, re-selects the row joined with `users` + `grid_profiles` and hydrates it into the exact feed-ready shape the task spec (and `PostComposer.jsx`'s local optimistic object) both use.
  - `Grid_posts_model::get_feed_posts($limit, $offset)` — same join/hydration, `deleted = 0`, newest first.
  - `hydrate_post()` / `format_time_ago()` (private helpers) — build the `author`/`metrics` blocks and a coarse relative-time label (`Just now` / `Nm ago` / `Nh ago` / `Nd ago`).
  - `GridApiController::createPost()`, `getPosts()` (clamps `limit` to 1–100) — response shapes match the task spec verbatim.
  - Routes: `POST posts/create`, `GET posts` added to the `grid/api` group.
- **Note**: `PostComposer.jsx` currently builds its post object fully client-side (`onPostCreated` is a local callback, no `gridApi` call yet) and uses `author.gridId` (camelCase) where this backend returns `author.grid_id` (snake_case, matching the task spec verbatim). Flagged for whoever wires `PostComposer`/`GridHomeApp` to `POST /grid/api/posts/create` next — full details in `CLAUDE_OUTPUT.md`.
- **Verified**: `php -l` clean on all touched files; live-tested `createPost` → `getPosts` (created post appears, correctly hydrated) and missing-content → `400`.

---

## 006. Universal Media & File Upload Endpoint
- **Date**: September 16, 2026
- **Status**: ✅ **Implemented, Routed & Verified**
- **Endpoint**: `POST /grid/api/upload` (Route: `POST upload` in `grid/api` group)
- **Controller Method**: `GridApiController::upload()`
- **Storage Target**: `FCPATH . 'files/timeline_files/'` (serves at `/files/timeline_files/<filename>`)
- **Capabilities**:
  - Validates MIME type (`image/png`, `image/jpeg`, `image/jpg`, `image/webp`, `image/gif`) and file size (&le; 10MB).
  - Sanitizes and randomizes filename with `bin2hex(random_bytes(8))` and timestamp.
  - Returns `{ success: true, file_url: "/files/timeline_files/...", file_name: "...", file_size: ... }`.
- **Client & UI Integration**:
  - `gridApi.js`: Added `uploadFile(file, type)` using native `FormData`.
  - `PostComposer.jsx`: Hidden universal HTML5 `<input type="file" accept="image/*">` triggered by the image button. Supports Android / iOS native bottom sheet (Camera, Photos, Files) and PC File Explorer. Includes an image preview card with a remove ("✕") button and attaches `media_url` to `POST /grid/api/posts/create`.
  - `TechPostCard.jsx`: Displays attached image via `post.media_url || post.image`.
- **Verified**: Live test script uploaded sample PNG (`HTTP 200`), verified HTTP image accessibility, and verified post creation with attached `media_url`.

---

## 007. Post Deletion Pipeline (Three-Dot Menu & Database Soft Delete)
- **Date**: September 16, 2026
- **Status**: ✅ **Implemented, Routed & Verified**
- **Endpoint**: `POST /grid/api/posts/delete` & `DELETE /grid/api/posts/(:segment)`
- **Controller Method**: `GridApiController::deletePost($id)`
- **Model Method**: `Grid_posts_model::delete_post($postId, $userId, $isAdmin)`
- **Database Action**: Sets `deleted = 1` in `grid_posts` table (ensuring it is excluded from `get_feed_posts` queries).
- **Client & UI Integration**:
  - `gridApi.js`: Added `deletePost(postId)`.
  - `TechPostCard.jsx`: Interactive dropdown menu on the existing three-dot (`more_horiz`) button with a red "Delete post" option and click-away dismissal.
  - `GridHomeApp.jsx`: `handleDeletePost` removes the deleted post from state and shows a feedback toast.
- **Verified**: Live test created post `id=9`, called delete API (`HTTP 200`), and verified `deleted = 1` in MySQL `factor_db2`.

---

## 008. Schema Optimization & Dead Table Pruning
- **Date**: September 16, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **Changes**:
  - Dropped `grid_user_profiles` (0 rows, dead duplicate of `grid_profiles`).
  - Dropped `grid_education` (0 rows, not hooked up to any active backend model).
  - Dropped `grid_experiences` (0 rows, not hooked up to any active backend model).
  - Dropped `grid_follows` (0 rows, not hooked up to any active backend model).
- **Architecture Principle**: Keep database lean and purposeful; create each table and relation incrementally when building its corresponding feature.

---

## 009. Multi-Image Attachments (1:N), Likes, and Comments Engine
- **Date**: September 17, 2026
- **Status**: ✅ **Executed, Routed & Live-Verified**
- **New Tables in MySQL `factor_db2`**:
  1. `grid_post_media` (1:N Post Attachments / LinkedIn-style Carousel):
     ```sql
     CREATE TABLE `grid_post_media` (
       `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
       `post_id` BIGINT UNSIGNED NOT NULL,
       `media_url` VARCHAR(500) NOT NULL,
       `media_type` VARCHAR(20) NOT NULL DEFAULT 'image',
       `sort_order` INT NOT NULL DEFAULT 0,
       `created_at` DATETIME NOT NULL,
       PRIMARY KEY (`id`),
       KEY `idx_post_id` (`post_id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     ```
  2. `grid_post_likes` (Atomic Like Toggle & Persistence):
     ```sql
     CREATE TABLE `grid_post_likes` (
       `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
       `post_id` BIGINT UNSIGNED NOT NULL,
       `user_id` INT NOT NULL,
       `created_at` DATETIME NOT NULL,
       PRIMARY KEY (`id`),
       UNIQUE KEY `uniq_post_user` (`post_id`, `user_id`),
       KEY `idx_post_id` (`post_id`),
       KEY `idx_user_id` (`user_id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     ```
  3. `grid_post_comments` (Threaded Post Discussion):
     ```sql
     CREATE TABLE `grid_post_comments` (
       `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
       `post_id` BIGINT UNSIGNED NOT NULL,
       `user_id` INT NOT NULL,
       `comment_text` TEXT NOT NULL,
       `created_at` DATETIME NOT NULL,
       `updated_at` DATETIME NOT NULL,
       `deleted` TINYINT(1) NOT NULL DEFAULT 0,
       PRIMARY KEY (`id`),
       KEY `idx_post_id` (`post_id`),
       KEY `idx_user_id` (`user_id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     ```
- **Backend & Application Layer**:
  - `Grid_posts_model.php`:
    - `create_post()`: Supports `media_urls[]` array (1 to 10 images/videos) and stores them in `grid_post_media`.
    - `get_feed_posts()`: Hydrates each post with full `media` array and `has_liked` boolean for current user.
    - `toggle_like()`: Atomic toggle in `grid_post_likes` with increment/decrement on `grid_posts.likes_count`.
    - `add_comment()`, `get_comments()`, `delete_comment()`: Threaded comments connected to author identity.
    - `delete_post()`: Soft-deletes post AND unlinks orphaned physical image files from `files/timeline_files/` on disk!
  - `GridApiController.php`: Added `toggleLike()`, `getComments()`, `addComment()`, `deleteComment()` endpoints.
  - `Config/Routes.php`: Registered `posts/like`, `posts/(:num)/comments`, and `posts/comment` endpoints under `grid/api`.
- **Frontend & UI Layer**:
  - `gridApi.js`: Added `toggleLike()`, `getComments()`, `addComment()`, `deleteComment()`.
  - `PostComposer.jsx`: Multi-image file selector (`multiple`), multi-image preview gallery with individual remove buttons, and batch parallel upload via `Promise.all`.
  - `TechPostCard.jsx`: Interactive live like toggle connected to `gridApi.toggleLike()`, responsive 2x2 multi-image grid gallery with "+X more" counter.
  - `CommentsDrawer.jsx`: Live comment fetch and post persistence via `gridApi.addComment()`.
- **Verification**:
  - `scratch/test_live_post_api.php`: Executed via cURL &rarr; All 6 tests passed with HTTP 200 (multi-media create, feed hydration, like toggle, comment save, comment thread fetch, and delete post).
  - React bundle compiled clean: `dist/bundle.js` (869.4kb in 227ms).

---

## 010. Dedicated `grid_db` Database & Profile Column Expansion
- **Date**: September 17, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **Scope**: Migrated all GRID profile tables to a dedicated `grid_db` database; added 12+ columns to `grid_profiles`.
- **New Database**: `grid_db` (separate from `factor_db2`)
  - `Grid_base_model` configured with `$DBGroup = 'grid_db'`
  - `Config/Database.php` contains connection group `'grid_db'` pointing to `grid_db` database
- **New Columns on `grid_profiles`**:
  ```
  pronoun VARCHAR(32), about_skills TEXT, about_json TEXT,
  otw_roles VARCHAR(255), otw_note VARCHAR(255),
  banner_type ENUM('gradient','image','video'), banner_video_src VARCHAR(255),
  banner_image_src VARCHAR(255), banner_gradient VARCHAR(255),
  banner_tag VARCHAR(255), banner_title VARCHAR(255), banner_url VARCHAR(255)
  ```
- **Application Layer**:
  - `Grid_profile_model::save_profile()` — upserts all new fields
  - `GridApiController::saveProfile()` — parses 20+ payload keys including `banner_*`, `about_*`, `otw_*`, full name sync to `factor_db2.users`
- **Key Lines** (GridApiController.php):
  - `saveProfile()`: Lines 181–351
  - `getProfile()`: Lines 110–174

---

## 011. Relational 1:N Profile Tables (grid_db)
- **Date**: September 17, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **New Tables in `grid_db`**:
  - `grid_education` (id, user_id, school_name, degree, dates, activities, icon, icon_class)
  - `grid_experiences` (id, user_id, title, company, dates, is_current, bullets JSON, tags JSON, icon, icon_class)
  - `grid_certifications` (id, user_id, name, issuer, credential_id, credential_url, dates, icon, icon_class)
  - `grid_projects` (id, user_id, name, dates, badge, badge_class, description, tags JSON, project_url)
  - `grid_honors` (id, user_id, title, issuer, year, description, icon)
  - `grid_languages` (id, user_id, name, level)
  - `grid_artifacts` (id, user_id, name, type, title, description, link_text, link_url, badge, img_src, cover_class, tag_class)
  - `grid_skills` (id PK, name UNIQUE VARCHAR(128), category VARCHAR(64))
  - `grid_user_skills` (id PK, user_id INT, skill_id INT FK, endorsements_count INT)
- **Application Layer**:
  - `Grid_sections_model` — CRUD for all 7 relational tables:
    - `get_user_experiences()`, `save_user_experience()`, `delete_user_experience()`
    - `get_user_educations()`, `save_user_education()`, `delete_user_education()`
    - `get_user_certifications()`, `save_user_certification()`, `delete_user_certification()`
    - `get_user_projects()`, `save_user_project()`, `delete_user_project()`
    - `get_user_honors()`, `save_user_honor()`, `delete_user_honor()`
    - `get_user_languages()`, `save_user_language()`, `delete_user_language()`
    - `get_user_artifacts()`, `save_user_artifact()`, `delete_user_artifact()`
  - `Grid_skills_model` — `suggest_skills()`, `associate_user_skills()`, `remove_user_skill()`, `get_user_skills()`
  - `GridApiController::saveSectionItem()` (lines 358–419) — Routes to correct model method via `switch($section)`
  - `GridApiController::deleteSectionItem()` (lines 425–484) — Same routing for deletes
- **Endpoints**:
  - `POST /grid/api/profile/section/save` — `{ section, item, id }`
  - `POST /grid/api/profile/section/delete` — `{ section, id }`
  - `GET /grid/api/skills/suggest?q={query}` — prefix autocomplete
- **Server Hydration**: `profile.php` queries all section models and injects into `window.__GRID_DATA__` (lines 62–73, 298–305)
- **Verified**: E2E scripts tested all 8 section types; 100% database persistence confirmed

---

## 012. SPA Navigation API Data Loading
- **Date**: September 17, 2026
- **Status**: ✅ **Implemented & Bundled**
- **Problem**: `index.php` (Home) only injects basic user info into `window.__GRID_DATA__` — no section arrays. When navigating Home→Profile via SPA, `GridProfileApp` had no data.
- **Fix**: `GridProfileApp.jsx` lines 63–148 — `useEffect` detects missing sections and calls `gridApi.getProfile()` to fetch full profile from API.
- **API Endpoint Used**: `GET /grid/api/profile` (GridApiController::getProfile, lines 110–174)
  - Returns: `{ success, data: { isOwner, user, profile, skills, experiences, education, certifications, projects, honors, languages, artifacts, relationship } }`
- **Skip Condition**: If `window.__GRID_DATA__` already has sections (direct `profile.php` load), the fetch is skipped — zero overhead.
- **Bundle**: Rebuilt `dist/bundle.js` (909.6kb)

---

## 013. Real Repost Feature (Feed Attribution + Profile Activity Feed)
- **Date**: September 22, 2026
- **Status**: ✅ **Executed & Verified in MySQL**
- **Problem**: Repost was fake — `TechPostCard.jsx`'s `toggleRepost` was pure local `useState` math with no network call (nothing persisted, not idempotent), no `gridApi` method/endpoint/table existed at all, and `JobPostCard`/`MarketPostCard` had no Repost button. Separately, the profile's `ActivityFeed.jsx` (Posts tab) was not wired to any real backend data — it rendered a permanent mock array (`initialPosts` from `initialState.js`) in a different shape than the real post model.
- **New Table** `grid_post_reposts` (mirrors `grid_post_likes`):
  ```sql
  CREATE TABLE grid_post_reposts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_post_user (post_id, user_id),
    INDEX idx_user_reposts (user_id, created_at),
    INDEX idx_post_reposts (post_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  ```
- **Semantics** (confirmed with user): LinkedIn-style — reposting shares the original post into the reposter's own feed/profile as a distinct, attributed entry (not just a counter); the original post's own entry (under its real author) is untouched and keeps appearing normally too. Idempotent toggle, mirrors `toggle_like()`/`grid_post_likes` exactly.
- **`Grid_posts_model.php`**:
  - `toggle_repost($postId, $userId)` — insert/delete in `grid_post_reposts`, keeps `grid_posts.reposts_count` in sync (`GREATEST(0, ...)` floor on undo).
  - `hydrate_post()` gained `has_reposted` (mirrors `has_liked`) and, when the source row came from a repost join, `is_repost`, `repost_id`, `reposted_at`, `reposted_by: {id, name, avatar, grid_id, headline}` — the `author` block always stays the *original* post's author.
  - `get_feed_posts()` and new `get_user_posts($userId)` both delegate to a shared private `build_merged_feed()`: fetches a bounded window of original posts and a bounded window of reposts independently (standard k-way-merge bound), hydrates both against the same prefetched media/likes/reposts, sorts by each entry's own effective timestamp (post `created_at` vs. repost `created_at`), then slices to the requested page.
  - New private helpers: `fetch_original_post_rows()`, `fetch_repost_rows()` (joins `grid_post_reposts` → `grid_posts` → both the original author's and reposter's identity), `prefetch_media()`, `prefetch_likes()`, `prefetch_reposts()` (the last three extracted from the old inline `get_feed_posts()` body so `build_merged_feed()` can reuse them across both original and repost rows).
  - A repost's feed-item `id` is `repost-{repost_id}` (distinct from the original's own `db-{post_id}` entry) so both can appear in the same feed page without a duplicate React key; `db_id` always stays the underlying original post's real id for like/comment/repost actions.
- **`GridApiController.php`**: `toggleRepost($id)` (mirrors `toggleLike`), `getUserPosts($userId = null)` (defaults to the authenticated user).
- **Routes** (`Config/GRID_Config/Routes.php`): `POST posts/repost`, `POST posts/(:num)/repost`, `GET posts/user`, `GET posts/user/(:num)`.
- **Frontend**:
  - `gridApi.js`: added `toggleRepost(postId)`, `getUserPosts(userId, limit, offset)`.
  - `TechPostCard.jsx`: replaced the fake counter-only `toggleRepost` with the real optimistic-update-then-reconcile pattern (mirrors its own `toggleLike`), added active/inactive styling (emerald when reposted, matching the blue Like active state convention), and a "🔁 [reposter] reposted this" attribution banner above the author header.
  - `JobPostCard.jsx` / `MarketPostCard.jsx`: neither had any social action row before (just a primary CTA + bookmark for Job). Added a matching repost icon-toggle button next to the existing bookmark button (Job) / MARKETPLACE badge (Market) — a minimal, consistent addition following the existing icon-button pattern rather than bolting on an unrelated full action bar — plus the same attribution banner.
  - `ActivityFeed.jsx` (profile Posts tab) — rewritten to consume the real `hydrate_post()` shape (`post.content`/`post.author.name`/`post.metrics.likes` instead of the old mock's `post.body`/`post.authorName`/`post.likesCount`), wired Like and the new Repost action to `gridApi.toggleLike`/`toggleRepost` (previously both were fake local-only state), added the same repost banner, and reused the existing `CommentsDrawer.jsx` component (already used by `TechPostCard`) instead of a "coming soon" toast placeholder.
  - `GridProfileApp.jsx`: added a dedicated `useEffect` calling `gridApi.getUserPosts(userIdToFetch)` on mount/profile-target-change and storing the result in `state.feed`, replacing the permanent `initialPosts` mock that was passed to `ActivityFeed` before.
- **Verified**: `php -l` clean on all touched PHP files. Live-tested: repost toggle on/off (idempotent, `grid_post_reposts` row + `reposts_count` correct in both directions), reposting another user's post shows the *original* author in `author` and the *reposter* in `reposted_by` (verified they differ), `GET /grid/api/posts` shows the repost merged in by recency, `GET /grid/api/posts/user/{id}` includes both a user's own posts and their reposts, `/grid` and `/grid/profile` both load (`200`), bundle rebuilt clean (`dist/bundle.js`, 937.8kb).

---

## 014. Fix: Activity Feed Missing Images & Self-Repost Duplication
- **Date**: September 22, 2026
- **Status**: ✅ **Executed & Verified**
- **Bug 1 — no images on the profile Activity Feed**: `ActivityFeed.jsx`'s rewrite (#013) ported content/author/likes/comments/repost rendering from `TechPostCard.jsx` but never carried over media rendering. Fixed by importing and rendering `PostMediaCarousel` (`media`, `singleUrl`, `authorName` props) between the post body and actions row, matching `TechPostCard.jsx` exactly.
- **Bug 2 — duplicate/confusing entry after 2 posts on a profile**: root-caused via direct DB query to a genuine self-repost — user `357` had reposted their own post (`grid_post_reposts.id=8`, `post_id=34`, both authored and reposted by `user_id=357`). `get_user_posts()` correctly merges a user's own posts with their own reposts as independent sources, so that one post legitimately appeared twice — once normally, once again with a "reposted this" banner attributing it to themselves.
  - **Data cleanup**: deleted `grid_post_reposts.id=8`, decremented `grid_posts.id=34`'s `reposts_count` by 1.
  - **`Grid_posts_model::toggle_repost()`**: now checks the post's author before inserting a new repost; if it's the caller's own post, returns `{error: 'self_repost', reposted: false, reposts_count: <unchanged>}` instead of creating a row. Un-reposting an existing row (including a stray pre-fix self-repost) is still always allowed.
  - **`GridApiController::toggleRepost()`**: returns `400` with a clear message when the model reports `self_repost`.
  - **Frontend**: `TechPostCard.jsx`, `JobPostCard.jsx`, `MarketPostCard.jsx`, `ActivityFeed.jsx` all resolve the viewer's id via `window.__GRID_DATA__?.user?.id` (matching `GridMessagingApp.jsx`'s existing convention) and hide/disable the Repost action on the viewer's own posts — belt-and-suspenders on top of the backend check, and better UX than click-then-error. `TechPostCard`'s repost button (a fixed 4-column action row) is disabled+grayed rather than removed to avoid an awkward layout gap; the icon-only repost buttons in `JobPostCard`/`MarketPostCard`/`ActivityFeed` are conditionally hidden instead, since removing one from a loose flex row doesn't disrupt layout.
- **Verified**: Live-tested self-repost attempt → `400`, no row created, counter unchanged. Re-fetched user 357's `GET /grid/api/posts/user/357` — post 34 now appears exactly once; legitimate reposts of other users' posts (31, 33) still show correctly. Confirmed posts with real attached media exist in `grid_post_media` for the carousel fix to render. `php -l` clean; `/grid` and `/grid/profile` both load (`200`); bundle rebuilt clean (`dist/bundle.js`, 938.7kb).

---

## 015. Header Search Bar — People Search
- **Date**: September 22, 2026
- **Status**: ✅ **Executed & Verified**
- **Problem**: `Header.jsx`'s search input already had a `searchQuery` state and called an `onSearch` prop on change, but `<Header>` is never actually given an `onSearch` prop anywhere in the codebase (confirmed by grep) — the callback was permanently a no-op. There was also no backend search endpoint at all (only `suggest_skills()` existed, for skills autocomplete).
- **`Grid_profile_model::search_users($query, $limit, $excludeUserId)`** (new): matches `users.first_name`/`last_name`/full-name against the query (min 2 chars), left-joins `grid_profiles` for `grid_id`/`headline`, excludes the current user, returns id/grid_id/name/raw avatar/headline.
- **`GridApiController::searchUsers()`** (new): `GET /grid/api/search/users?q=...`, resolves the raw avatar via the existing `resolveAvatarUrl()` helper before returning.
- **Route**: `GET search/users` added to `Config/GRID_Config/Routes.php`.
- **Frontend**: `gridApi.searchUsers(query)` added. `Header.jsx` made the search bar self-contained (no longer relies on an unwired `onSearch` prop): 250ms-debounced calls to the new endpoint, a results dropdown reusing the existing `.g-popover-menu` styling (same visual language as the Notifications/Me dropdowns already in `Header.jsx`) showing avatar + name + headline per match, click-outside-to-close on desktop, and selecting a result navigates straight to that person's profile via the existing `onNavigate('profile', {targetUser: {...}})` pattern. Wired for both the desktop `.g-search-wrap` bar and the full-screen mobile search overlay (which needed its input row wrapped in its own `position: relative` container so the dropdown anchors under the input rather than the full-screen overlay).
- **Verified**: `php -l` clean. Live-tested `GET /grid/api/search/users?q=Pavan` and `?q=Koushik` → correct single match each with resolved avatar URL; `?q=a` (below the 2-char minimum) → empty array. Bundle rebuilt clean (`dist/bundle.js`, 941.3kb); `/grid` loads (`200`).

---

## 016. `GET /api/me` — Main Hub Identity Handoff Endpoint
- **Date**: September 22, 2026
- **Status**: ✅ **Executed & Verified**
- **Why**: GRID is one of several apps inside a larger "Main Hub" super-app and has no login of its own. `users.grid_id` (a hex code, e.g. `A06DC592858C3B857E7E`) — **not** `grid_profiles.grid_id`, the unrelated vanity-handle used everywhere else in GRID — is assigned at hub login and carried into GRID as the identity token when a user clicks through. This endpoint is how GRID recognizes who arrived, built from a detailed external spec (`GRID_api_me_Antigravity_Prompt (1).txt`) that explicitly forbade trusting any `user_id` from the frontend, forbade a new login system, and forbade duplicating the `users` table.
- **Deviations from the spec's literal example, both explicitly licensed by the spec itself ("investigate and reuse existing conventions, don't guess")**:
  - DB: spec said `factor_db1`; that connection group doesn't exist anywhere in `Config/Database.php` (only `default` → `factor_db2` and `grid` → `grid_db` do). Used `db_connect('default')`.
  - Controller: spec's example was a fresh `App\Controllers\GridApiController`; added `me()` to the real, established `App\Controllers\GRID_Controllers\GridApiController` instead.
- **`GridApiController::me()`** (new): resolves `user_id` strictly from `$this->login_user` / the CI4 session (never from the request) — no fallback to a default user, unlike `getAuthUserId()` elsewhere in this file, since a fallback here would defeat the endpoint's purpose. No session → `401`. Queries `users` for `id, grid_id, first_name, last_name` filtered to `status='active' AND deleted=0` → no match `404`. Match → `200 {"status": true, "data": {user_id, grid_id, first_name, last_name}}` (note: `status`/`data` shape, not this controller's usual `success`/`data`, matching the spec's frontend integration snippet exactly).
- **Route**: `Config/Routes.php` — added `$routes->get('me', 'GRID_Controllers\GridApiController::me');` **inside** the existing top-level `api` route group (used by the unrelated device/IoT API), positioned before that group's catch-all `$routes->add('(:any)', 'ApiErrors::notFound')`. Registering it anywhere else would have been silently swallowed by that catch-all.
- **No new tables** — matches the spec's explicit prohibition on `grid_users`/`users_copy`/duplicate identity tables.
- **Verified**:
  - No session → `GET /index.php/api/me` → `401 {"status":false,"message":"Unauthorized"}`.
  - Authenticated as user 356 (`pavankumar@gmail.com`, via `POST /index.php/signin/authenticate` + persisted cookie) → `200 {"status":true,"data":{"user_id":356,"grid_id":null,"first_name":"Pavan","last_name":"Kumar K"}}` — `grid_id` correctly `null` since 356 wasn't part of the `users.sql` batch that had hex `grid_id`s (Migration entry for the users merge, above).
  - Authenticated as user 1 (`admin@efactor.io`, who *is* in that batch) → `200` with `"grid_id":"A06DC592858C3B857E7E"`, matching the source data exactly.
  - `php -l` clean on both touched files.
- **⚠️ For whoever integrates GRID into the real Main Hub**: this endpoint assumes GRID and the hub share the same CI4 session/cookie — confirmed only against this app's own local sign-in, since no real hub or real hub-issued login exists in this dev environment. Full integration notes, the exact assumption, and the exact code block to adapt if it doesn't hold are in [`CLAUDE_OUTPUT.md`](./CLAUDE_OUTPUT.md) (Task 9) — read that before wiring this up for real.

---

## Backend File Registry & Line Map

### Controllers

| File | Lines | Key Methods & Line Ranges |
|---|:---:|---|
| [`GridApiController.php`](file:///c:/Program%20Files/Ampps/www/app/Controllers/GRID_Controllers/GridApiController.php) | 1579 | `getAuthUserId()` L50–60, `suggestSkills()` L91–100, `getProfile()` **L110–174**, `saveProfile()` **L181–351**, `saveSectionItem()` **L358–419**, `deleteSectionItem()` **L425–484**, `uploadAvatar()` L491–536, `uploadBanner()` L543–590, `wizardSetup()` L627–732, `getConnections()` L741–762, `createJob()` L163–168, `toggleLike()` / `getComments()` / `addComment()` / `deleteComment()` |
| [`GridController.php`](file:///c:/Program%20Files/Ampps/www/app/Controllers/GRID_Controllers/GridController.php) | 51 | `index()` L11–15 → `GRID/index`, `profile()` L17–21 → `GRID/profile`, `messaging()` L23–27, `team()` L29–33, `demo_loader()` L35–38, `network()` L40–44, `jobs()` L46–50 |

### Models (all in `app/Models/GRID/`)

| File | Lines | Key Methods |
|---|:---:|---|
| [`Grid_profile_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_profile_model.php) | — | `get_profile_by_user_id()`, `get_profile_by_grid_id()`, `save_profile()`, `provision_grid_id()`, `generate_unique_grid_id()` |
| [`Grid_sections_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_sections_model.php) | — | `get_user_experiences/educations/certifications/projects/honors/languages/artifacts()`, `save_user_*()`, `delete_user_*()` |
| [`Grid_skills_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_skills_model.php) | — | `suggest_skills()`, `associate_user_skills()`, `remove_user_skill()`, `get_user_skills()` |
| [`Grid_posts_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_posts_model.php) | — | `create_post()`, `get_feed_posts()`, `toggle_like()`, `add_comment()`, `get_comments()`, `delete_comment()`, `delete_post()` |
| [`Grid_connections_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_connections_model.php) | — | `get_accepted_connections()`, `send_request()`, `respond_request()`, `get_connection_status()` |
| [`Grid_jobs_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_jobs_model.php) | — | `create_job()`, `get_recruiter_jobs()`, `get_job_applicants()`, `update_application_status()` |
| [`Grid_messages_model.php`](file:///c:/Program%20Files/Ampps/www/app/Models/GRID/Grid_messages_model.php) | — | `get_or_create_conversation()`, `get_user_conversations()`, `get_conversation_messages()`, `send_message()` |

### Views (PHP Host Shells)

| File | Lines | Hydration Scope |
|---|:---:|---|
| [`profile.php`](file:///c:/Program%20Files/Ampps/www/app/Views/GRID/profile.php) | 319 | **Full** — injects user, about, banner, experiences, education, certifications, projects, honors, languages, artifacts, skills into `window.__GRID_DATA__` (lines 243–307) |
| [`index.php`](file:///c:/Program%20Files/Ampps/www/app/Views/GRID/index.php) | 249 | **Minimal** — injects user basics only (name, avatar, headline, location, bio, gridId, openToWork) into `window.__GRID_DATA__` (lines 196–237). **No section arrays.** |

### API Routes (`Config/Routes.php` — `grid/api` group)

| Method | Route | Controller Method |
|---|---|---|
| GET | `profile/(:any)` | `getProfile($id)` |
| POST | `profile/save` | `saveProfile()` |
| POST | `profile/section/save` | `saveSectionItem()` |
| POST | `profile/section/delete` | `deleteSectionItem()` |
| POST | `profile/upload-avatar` | `uploadAvatar()` |
| POST | `profile/upload-banner` | `uploadBanner()` |
| POST | `profile/wizard-setup` | `wizardSetup()` |
| GET | `skills/suggest` | `suggestSkills()` |
| GET | `connections` | `getConnections()` |
| POST | `connections/request` | `requestConnection()` |
| POST | `connections/respond` | `respondConnection()` |
| POST | `jobs/create` | `createJob()` |
| GET | `posts` | `getPosts()` |
| POST | `posts/create` | `createPost()` |
| POST | `posts/delete` | `deletePost()` |
| POST | `posts/like` | `toggleLike()` |
| GET | `posts/(:num)/comments` | `getComments()` |
| POST | `posts/comment` | `addComment()` |
| POST | `upload` | `upload()` |
| GET | `messages/conversations` | `getConversations()` |
| GET | `messages/thread/(:num)` | `getThread()` |
| POST | `messages/send` | `sendMessage()` |
