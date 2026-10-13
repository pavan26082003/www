# Claude Code Backend Task Queue

> **Instructions for Claude CLI**:
> 1. Read the active task below carefully.
> 2. Execute the backend, model, controller, and migration requirements precisely.
> 3. Verify with `php -l` and live endpoint checks.
> 4. Record any schema/table changes in [`database_changes.md`](./database_changes.md).

---

## Completed Tasks

- ✅ **Task 1: Profile Setup Wizard Backend & Admin Restore** (Completed Sep 15, 2026)
- ✅ **Task 2: Universal Unique `grid_id` Generation & $O(1)$ Lookup** (Completed Sep 16, 2026)
  - `ALTER TABLE grid_profiles ADD COLUMN grid_id ...` executed.
  - `Grid_profile_model::generate_unique_grid_id` implemented.
  - Self-healing lookup and vanity URL routing verified.
- ✅ **Task 3: Recruiter Mode & Job Posting Pipeline (ATS)** (Completed Sep 16, 2026)
  - `Grid_jobs_model::create_job` and `get_recruiter_jobs` implemented.
  - `Grid_jobs_model::get_job_applicants` and `update_application_status` implemented.
  - Endpoints live tested and verified:
    - `POST /grid/api/jobs/create`
    - `GET /grid/api/recruiter/jobs`
    - `GET /grid/api/recruiter/jobs/(:num)/applicants`
    - `POST /grid/api/recruiter/applications/status`
  - Documented in `database_changes.md`.
- ✅ **Task 4: Circle Connect Direct Messaging & Universal Handle (`grid_id`) Integration** (Completed Sep 16, 2026)
  - New tables `grid_conversations` and `grid_messages` created.
  - `Grid_messages_model` implemented: `get_or_create_conversation`, `get_user_conversations`, `get_conversation_messages` (participant-verified, marks incoming as read), `send_message`.
  - Endpoints live tested and verified:
    - `GET /grid/api/messages/conversations`
    - `GET /grid/api/messages/thread/(:num)`
    - `POST /grid/api/messages/send`
    - `GET /grid/api/messages/start/(:any)` — numeric `user_id` or string `grid_id` handle
  - **Heads up for the frontend integration**: `GridMessagingApp.jsx`/`ConversationList.jsx`/`ActiveChatPane.jsx` are still on local mock state (`messagingState.js`) even though `gridApi.js` already has the client methods wired up. Response shapes (snake_case, `{success, data}`, `opponent: {user_id, grid_id, name, avatar, headline}`, `unread_count`, `conversation_id`) are documented in `database_changes.md` Migration 004 for whoever wires the UI to live data next.
  - Documented in `database_changes.md`.

- ✅ **Task 5: Automated Unique `grid_id` Provisioning on Account Creation (Approach A)** (Completed Sep 16, 2026)
  - `Grid_profile_model::provision_grid_id($user_id, $preferred_name_or_handle)` implemented: idempotent, collision-proof slug generation, auto-upserts `grid_profiles`.
  - `Users_model::ci_save` overridden: whenever an account is created anywhere across the platform (signup, invited team members, admin add, client contact), a unique `grid_id` is automatically and immediately provisioned in `grid_profiles`.
  - API endpoint `POST /grid/api/profile/provision` added for programmatic/external provisioning from the main app or microservices.
  - Verified live with real user registration flow via `Signup::create_account`.

- ✅ **Task 6: Home Feed Posts Backend & Persistence** (Completed Sep 16, 2026)
  - New table `grid_posts` created.
  - `Grid_posts_model::create_post` and `get_feed_posts` implemented, hydrating the exact author/metrics shape from the task spec.
  - Endpoints live tested and verified:
    - `POST /grid/api/posts/create`
    - `GET /grid/api/posts`
  - Documented in `database_changes.md` (Migration 005) and `CLAUDE_OUTPUT.md`.

---

## Active Task: _(none queued — awaiting next task)_
