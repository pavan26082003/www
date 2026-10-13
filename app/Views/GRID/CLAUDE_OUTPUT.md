# Claude CLI ➔ Antigravity Handoff Log

> **Instructions for Claude CLI**:
> Whenever you finish executing a task from [`CLAUDE_TASK_QUEUE.md`](./CLAUDE_TASK_QUEUE.md):
> 1. Record your execution summary in this file.
> 2. Document the exact table schemas, model methods, endpoint URLs, and JSON response shapes.
> 3. Antigravity IDE reads this file immediately after your run to wire the frontend seamlessly.

---

## Active Handoff Log

### Task 6: Home Feed Posts Backend & Persistence — ✅ Complete (Sep 16, 2026)

Full details also logged in [`database_changes.md`](./database_changes.md) (Migration 005). Summary for wiring the frontend:

#### 1. Database
New table `grid_posts` (`factor_db2`), created exactly per the task spec — see Migration 005 in `database_changes.md` for the full `CREATE TABLE` statement. Columns: `id`, `user_id`, `content`, `tags` (CSV string), `media_url`, `type` (`general`|`tech_artifact`|`opportunity`|`marketplace`), `likes_count`, `comments_count`, `reposts_count`, `created_at`, `updated_at`, `deleted`.

#### 2. Model — `App\Models\GRID\Grid_posts_model`
- `create_post($user_id, $data)` — `$data` accepts `content` (required — returns `null` if blank), `tags` (array of strings, stored as CSV), `media_url` (string, optional), `type` (one of the 4 enum values, defaults to `general` if invalid/missing). Returns the hydrated post array (shape below), or `null` on validation failure.
- `get_feed_posts($limit = 20, $offset = 0)` — returns an array of hydrated posts, `deleted = 0`, newest first.

#### 3. Endpoints
| Method | URL | Auth | Body / Query |
|---|---|---|---|
| `POST` | `/grid/api/posts/create` | current session user (`getAuthUserId()`) | JSON: `{"content": "...", "tags": ["#Tag1"], "media_url": null, "type": "general"}` |
| `GET` | `/grid/api/posts` | — | Query: `?limit=20&offset=0` (limit clamped to 1–100) |

#### 4. Response Shapes

**`POST /grid/api/posts/create`** success (`200`):
```json
{
  "success": true,
  "message": "Post published successfully.",
  "data": {
    "id": "db-1",
    "db_id": 1,
    "type": "general",
    "content": "Excited to launch GRID's new posts pipeline!",
    "tags": ["#GRID", "#Launch"],
    "media_url": null,
    "created_at": "2026-09-16 02:46:35",
    "author": {
      "id": 1,
      "name": "Pavan Kumar",
      "role": "Admin",
      "avatar": "...",
      "grid_id": "pavan-kumar-2433",
      "timeAgo": "Just now",
      "discipline": "General"
    },
    "metrics": { "likes": 0, "comments": 0, "reposts": 0 }
  }
}
```
Error (`400`, empty `content`): `{"success": false, "message": "Post content is required."}`

**`GET /grid/api/posts`** success (`200`): `{"success": true, "data": [ <post>, <post>, ... ]}` — same post shape as above, newest first.

#### 5. Field-naming heads-up for whoever wires this
- `PostComposer.jsx` currently builds its post object **entirely client-side** (`onPostCreated` is a local callback — no `gridApi` call exists yet for posts). This backend was built to match that component's local shape as closely as possible, but there's one deliberate deviation: `PostComposer.jsx` uses `author.gridId` (camelCase); this API returns `author.grid_id` (snake_case), matching the task spec verbatim and this codebase's established snake_case JSON convention everywhere else. Whichever side wires the integration should normalize this one key.
- `author.avatar` — for users without a proper uploaded avatar, `users.image` can contain a serialized PHP array string (a pre-existing data quirk in this dev DB, unrelated to this task) instead of a URL. Not something this endpoint can safely "fix" without knowing the intended avatar-serving convention — flagging so it isn't mistaken for a bug in `create_post`/`get_feed_posts`.
- `timeAgo` is computed server-side at read time (`Just now` / `Nm ago` / `Nh ago` / `Nd ago`) from `created_at` — no need to compute it client-side.
- `tags` round-trips as a JSON array in both directions (stored as CSV internally, never exposed as CSV in responses).

#### 6. Verification performed
`php -l` clean on `Grid_posts_model.php`, `GridApiController.php`, `Routes.php`. Live-tested: `createPost` → `getPosts` (new post appears correctly hydrated) → `createPost` with missing `content` → `400`.

---

### Task 7: Universal File & Media Upload (Completed by Antigravity IDE)

- **Route**: `POST /grid/api/upload`
- **Controller**: `GridApiController::upload()`
- **Storage Target**: `FCPATH . 'files/timeline_files/'` (serves at `/files/timeline_files/<filename>`)
- **Frontend Wiring**:
  - `gridApi.uploadFile(file, type)`
  - `PostComposer.jsx` connected to hidden file input, preview card, and post creation with `media_url`.
  - `TechPostCard.jsx` renders `post.media_url || post.image`.
- **Verified**: Live image upload test returned `HTTP 200` with public URL; `createPost` persisted post with `media_url`. React bundle recompiled cleanly (0 errors).

---

### Task 8: Post Deletion Pipeline (Completed by Antigravity IDE)

- **Routes**: `POST /grid/api/posts/delete`, `DELETE /grid/api/posts/(:segment)`
- **Model**: `Grid_posts_model::delete_post($postId, $userId, $isAdmin)`
- **Controller**: `GridApiController::deletePost()`
- **Frontend**:
  - `TechPostCard.jsx`: Three-dot button opens dropdown with "Delete post".
  - `GridHomeApp.jsx`: Removes post from feed stream in real-time.
  - `gridApi.js`: `deletePost(postId)`.
- **Database**: Sets `deleted = 1` in `grid_posts`.
- **Verified**: Created test post `id=9`, deleted via API, verified `deleted = 1` in MySQL `factor_db2`.

---

### Task 9: `GET /api/me` — Main Hub Identity Handoff Endpoint — ✅ Complete (Sep 22, 2026)

**⚠️ Read this before integrating GRID into the real Main Hub.**

Full technical detail also logged in [`database_changes.md`](./database_changes.md) (Migration 016). This entry is specifically for whoever wires GRID into the real Main Hub super-app.

#### What it does
`GET /index.php/api/me` — `GridApiController::me()` in `Controllers/GRID_Controllers/GridApiController.php`. Resolves the current user strictly from the server-side session (never trusts anything from the frontend), looks them up in `factor_db2.users`, and returns their identity:

```json
{ "status": true, "data": { "user_id": 356, "grid_id": "A06DC592858C3B857E7E", "first_name": "Pavan", "last_name": "Kumar" } }
```
- No session → `401 { "status": false, "message": "Unauthorized" }`
- Session present but user inactive/deleted/missing → `404 { "status": false, "message": "User not found." }`
- `grid_id` here is `users.grid_id` (a hex device-style code assigned at hub login) — **not** `grid_profiles.grid_id` (the vanity handle like `pavan-kumar-2433` used by every other GRID feature). Two unrelated identifiers that happen to share a column name; don't conflate them.

#### The one assumption that needs verifying during real integration
**There is no real Main Hub and no real hub-issued login in this dev environment.** GRID was built and tested standalone — `me()` was verified by logging into *this app's own* local eFactor sign-in and confirming the resulting session was recognized. That sign-in is standing in for "however the real hub authenticates users," and this endpoint currently assumes GRID and the hub **share the same CodeIgniter PHP session/cookie** (same app, same domain).

**If the real hub instead hands off via a token, a different session store, or a different domain, this assumption breaks** — and `me()`'s user-resolution logic is the *only* place that needs to change. It's isolated at the very top of the method:
```php
// Controllers/GRID_Controllers/GridApiController.php, inside me()
if (isset($this->login_user) && !empty($this->login_user->id)) {
    $userId = (int) $this->login_user->id;
} else {
    $session = \Config\Services::session();
    if ($session->has('user_id') && !empty($session->get('user_id'))) {
        $userId = (int) $session->get('user_id');
    }
}
```
Nothing else in GRID depends on *how* this resolution happens internally — swap this block for whatever the real hub's handoff mechanism turns out to be (reading a signed token/header, validating against the hub's own session store, etc.) and everything downstream of it (the `users` lookup, the response shape, every GRID feature that already consumes `window.__GRID_DATA__`/the authenticated session) keeps working unchanged.

#### Verified (standalone, this dev environment only)
- No session → `401`.
- Authenticated as user 356 (no `grid_id` in this dev DB) → `200` with `"grid_id": null` — correct given the data, not a bug.
- Authenticated as user 1 (has a `grid_id`) → `200` with `"grid_id": "A06DC592858C3B857E7E"`.
- **Not verified**: behavior when actually embedded in the real Main Hub. Confirm the shared-session assumption holds before relying on this as-is.

