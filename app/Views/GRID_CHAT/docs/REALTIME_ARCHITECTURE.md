# GRID Chat — Real-Time Messaging Architecture

How a message gets from one person's keyboard to another person's screen, and every
piece of state that makes that work.

Paths are relative to `app/Views/GRID_CHAT/` for frontend files and to `app/` for
backend files. Line numbers were correct at the time of writing — treat them as
signposts, not guarantees.

---

## Contents

1. [Overview](#1-overview)
2. [Data model](#2-data-model)
3. [The sync loop (client)](#3-the-sync-loop-client)
4. [The sync endpoint (server)](#4-the-sync-endpoint-server)
5. [Visibility rules](#5-visibility-rules)
6. [Send lifecycle](#6-send-lifecycle)
7. [Delivery](#7-delivery)
8. [Read receipts and unread counts](#8-read-receipts-and-unread-counts)
9. [Idempotency](#9-idempotency)
10. [Media uploads](#10-media-uploads)
11. [Failure modes](#11-failure-modes)
12. [Limitations and scaling notes](#12-limitations-and-scaling-notes)

---

## 1. Overview

**There is no push transport.** No WebSocket, no SSE, no long polling, no message
queue, no cron, no third-party push service — on either side. The entire real-time
behaviour is one client-side timer calling one endpoint.

"Delivery" in this system means **committing rows to the database**. The sender's
request updates `conversations.last_message_id` and every other member's
`conversation_members.unread_count`; the recipients find out on their next poll.
End-to-end latency is therefore the poll interval, not network time.

```
 ┌────────── browser ──────────┐                  ┌──────── server ────────┐
 │                             │                  │                        │
 │  useChatSync (timer)        │   GET            │  ChatController::sync  │
 │      │                      │   /chat/sync     │        │               │
 │      │  cursor ────────────────────────────►   │        ▼               │
 │      │                      │                  │  newForUserSince       │
 │      │                      │                  │  changedForUserSince   │
 │      │   ◄──────────────────────────────────   │  conversationIdsRead…  │
 │      │  { messages,         │   JSON           │        │               │
 │      │    updatedMessages,  │                  │        ▼               │
 │      │    reads,            │                  │  formatMessages        │
 │      │    conversations,    │                  │                        │
 │      │    hasMore, cursor } │                  └────────────────────────┘
 │      ▼                      │
 │  handleChatSync             │
 │      ├─► setChats           │
 │      ├─► setSelectedChat    │
 │      └─► setMessagesByChatId│
 └─────────────────────────────┘
```

One timer, one endpoint, three `setState` calls. That is the whole mechanism.

The design anticipates being upgraded: the hook's own comment
(`src/hooks/useChatSync.js:11`) notes that *"A WebSocket can later replace the timer
by triggering a poll."* Because the cursor and reconciliation logic live in the sync
response rather than in the transport, swapping the timer for a socket that fires the
same `tick()` would be a small change — see [§12](#12-limitations-and-scaling-notes).

### The files that matter

| File | Role |
|---|---|
| `src/hooks/useChatSync.js` | The polling loop. The only caller of `syncChat`. |
| `src/App.jsx` | `handleChatSync` (apply), `sendToConversation` (send), conversation state. |
| `src/services/chatService.js` | HTTP transport, CSRF, the `{success, data, message}` contract. |
| `src/utils/chatAdapters.js` | `toUiMessage`, `toSendPayload`, `mergeMessages`, `applyReadReceipt`. |
| `Controllers/GRID_CHAT_Controllers/ChatController.php` | `sync`, `send`, `read`, `messages`. |
| `Controllers/GRID_CHAT_Controllers/GridChatBaseController.php` | `formatMessages`, read-tick maths. |
| `Models/GRID_CHAT_MODELS/MessageModel.php` | `VISIBLE_FROM`, sync queries, `createAndDeliver`. |
| `Models/GRID_CHAT_MODELS/ConversationMemberModel.php` | Per-user read state and watermarks. |

> The polling loop is **not** in `App.jsx`. `App.jsx:280` only mounts the hook:
> `useChatSync({ enabled: HAS_BACKEND_SESSION, onSync: handleChatSync })`.

`HAS_BACKEND_SESSION` is computed once at module scope (`src/App.jsx:59`) from the
PHP-injected `window.__APP_DATA__`. In Vite dev with no PHP session it is false, the
loop never starts, and the app runs entirely on demo fixtures from `src/data/chats.js`.
Much of what looks like live behaviour in dev is fixture data.

---

## 2. Data model

Everything lives in `grid_chat_db`. There are **no foreign keys to the CRM database** —
users are referenced by `factor_db2.users.id` as a bare integer and resolved at display
time through `UserProfileModel`.

The governing idea: **shared message facts live on `messages`; everything per-person
lives on `conversation_members`.** That is why one person clearing a chat, muting it, or
deleting it never affects anyone else.

### `messages`

```sql
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `sender_id` int NOT NULL,
  `client_id` char(36) DEFAULT NULL,
  `message_type` enum('text','image','video','file','audio','voice',
                      'location','contact','poll','event','system','call')
                 NOT NULL DEFAULT 'text',
  `body` text,
  `metadata` json DEFAULT NULL,
  `reply_to_message_id` bigint unsigned DEFAULT NULL,
  `edited_at` datetime(3) DEFAULT NULL,
  `deleted_at` datetime(3) DEFAULT NULL,
  `created_at` datetime(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sender_client` (`sender_id`,`client_id`),
  KEY `idx_conversation_id` (`conversation_id`,`id`),
  FULLTEXT KEY `ft_body` (`body`),
  CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`)
    REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_messages_reply` FOREIGN KEY (`reply_to_message_id`)
    REFERENCES `messages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;
```

Points that carry weight elsewhere in this document:

- **`id` is the global sequence.** A single auto-increment across every conversation is
  what makes the sync cursor a plain integer and read receipts an `id <=` comparison.
- **`uq_sender_client`** is the idempotency guarantee ([§9](#9-idempotency)). Scoped to
  `(sender_id, client_id)` so two users can never collide.
- **`deleted_at` is a soft delete.** The row must survive, because that is how the other
  side learns the message was deleted ([§5](#5-visibility-rules)).
- **`metadata` is `json`** and holds the structured payload for `poll`, `event`,
  `location`, `contact` and `call` messages. `body` is only ever a human-readable
  summary for chat-list previews.
- There is **no index serving the cross-conversation `id > ?` sync scan** other than the
  primary key — which is exactly what it uses.

### `conversation_members`

```sql
CREATE TABLE `conversation_members` (
  `conversation_id` bigint unsigned NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `joined_after_message_id` bigint unsigned DEFAULT NULL,
  `unread_count` int unsigned NOT NULL DEFAULT '0',
  `last_read_message_id` bigint unsigned DEFAULT NULL,
  `last_read_at` datetime(3) DEFAULT NULL,
  `cleared_before_message_id` bigint unsigned DEFAULT NULL,
  `deleted_at` datetime(3) DEFAULT NULL,
  `is_favourite` tinyint(1) NOT NULL DEFAULT '0',
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `muted_until` datetime(3) DEFAULT NULL,
  `joined_at` datetime(3) NOT NULL,
  `left_at` datetime(3) DEFAULT NULL,
  PRIMARY KEY (`conversation_id`,`user_id`),
  KEY `idx_user` (`user_id`,`deleted_at`,`conversation_id`),
  KEY `idx_read_sync` (`conversation_id`,`last_read_at`)
) ENGINE=InnoDB;
```

This is the most important table in the system. It carries four independent watermarks
per person per conversation:

| Column | Meaning |
|---|---|
| `last_read_message_id` | How far this person has read. Drives read ticks and unread counts. |
| `cleared_before_message_id` | "Clear chat" — hides everything at or below this id, for this person only. |
| `joined_after_message_id` | Set when added to a group; they cannot read anything sent before they joined. |
| `left_at` | Non-null ends access to the whole conversation immediately. |

`deleted_at` removes the chat from *this person's* list without touching the messages.

### `conversations`

```sql
CREATE TABLE `conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('direct','group') NOT NULL DEFAULT 'direct',
  `direct_key` varchar(32) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `created_by` int NOT NULL,
  `last_message_id` bigint unsigned DEFAULT NULL,
  `last_message_at` datetime(3) DEFAULT NULL,
  `created_at` datetime(3) NOT NULL,
  `updated_at` datetime(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_direct_key` (`direct_key`),
  KEY `idx_last_message_at` (`last_message_at`)
) ENGINE=InnoDB;
```

The three denormalised columns have genuinely different jobs, and confusing them causes bugs:

- **`last_message_id`** — a *pointer*, not an ordering key. `ConversationModel::listForUser`
  does `LEFT JOIN messages lm ON lm.id = c.last_message_id`, so the whole chat list renders
  its preview text in one query instead of N. It also anchors read state and seeds the
  initial sync cursor.
- **`last_message_at`** — the *ordering key*:
  `ORDER BY COALESCE(c.last_message_at, c.created_at) DESC`. The `COALESCE` keeps a newly
  created, message-less chat at the top.
- **`updated_at`** — a metadata touch only. Bumped by a group rename or photo change, and
  **never read for ordering or sent to the client**, so renaming a group deliberately does
  not re-sort anyone's list.

`uq_direct_key` holds `min(a,b):max(a,b)` and is what makes `findOrCreateDirect` safe when
two people open the same DM simultaneously — the loser catches the duplicate-key error,
rolls back and re-reads the winner's row.

### `message_hidden`

```sql
PRIMARY KEY (`message_id`, `user_id`)
CONSTRAINT `fk_hidden_message` FOREIGN KEY (`message_id`)
  REFERENCES `messages` (`id`) ON DELETE CASCADE
```

Per-user "delete for me" tombstones. The cascade means these disappear with the message.

---

## 3. The sync loop (client)

`src/hooks/useChatSync.js` — 73 lines, and the whole of the client's real-time behaviour.

```js
const VISIBLE_INTERVAL_MS = 3000;
const HIDDEN_INTERVAL_MS = 30000;
const MAX_BACKOFF_MS = 60000;
```

```js
const nextDelay = () => {
  if (failures > 0) return Math.min(MAX_BACKOFF_MS, VISIBLE_INTERVAL_MS * 2 ** failures);
  return document.hidden ? HIDDEN_INTERVAL_MS : VISIBLE_INTERVAL_MS;
};

async function tick() {
  if (inFlight) return;
  inFlight = true;
  const isInitial = cursor === null;
  const result = await syncChat(cursor || {});
  inFlight = false;
  if (cancelled) return;

  if (result.success && result.data) {
    failures = 0;
    cursor = result.data.cursor;
    onSyncRef.current?.(result.data, { isInitial });
    schedule(result.data.hasMore ? 0 : nextDelay());
  } else {
    failures += 1;
    schedule(nextDelay());
  }
}
```

**Self-scheduling `setTimeout`, not `setInterval`.** Each poll schedules the next only
after the previous one settles, so the configured delay is the gap *between responses*.
A slow server stretches the cadence instead of piling up overlapping requests.

**Cadence.** 3 s while the tab is visible, 30 s while hidden, chosen fresh on each tick
from `document.hidden`.

**In-flight guard.** `if (inFlight) return;` prevents overlap. There is a subtlety: when
`visibilitychange` fires during an in-flight request, the early return happens before any
`schedule()` call, dropping that timer chain — but the in-flight `tick()` schedules its own
continuation when it resolves, so the loop survives. It works only because `inFlight` is
cleared before every `schedule` path.

**Backoff.** `3000 × 2^failures`, capped at 60 s: 6 s, 12 s, 24 s, 48 s, 60 s… reset to
zero on the first success.

**Drain mode.** `schedule(result.data.hasMore ? 0 : nextDelay())` — when the server says
there is more, the next poll fires immediately, draining the backlog 200 messages at a time.

**Catch-up on focus.** A `visibilitychange` listener calls `schedule(0)` when the tab
becomes visible, so returning to the tab refreshes at once rather than waiting out the 30 s
hidden interval.

**Teardown.** A `cancelled` flag plus `clearTimeout` on unmount or when `enabled` flips.
The post-await `if (cancelled) return;` stops a late response from touching unmounted state.

### The cursor

The cursor is **server-defined and opaque to the client**. It is not React state — it is a
plain closure variable, starting `null` and wholesale replaced on every success
(`cursor = result.data.cursor`). The client never constructs or advances it; it only echoes
back what it was given:

```js
export const syncChat = ({ afterMessageId, readSince } = {}) =>
  getJson('chat/sync', { after_message_id: afterMessageId, read_since: readSince });
```

`getJson` strips `undefined`/`null`/`''` params, so the very first call sends a bare
`GET /chat/sync` and lets the server pick the starting point.

Because the cursor lives in a closure, **it is per-mount and not persisted** — a page reload
starts from `null` and re-bootstraps.

### Applying the result

`handleChatSync` (`src/App.jsx:236-278`) is the only consumer. It touches exactly three
pieces of state: `setChats`, `setSelectedChat`, `setMessagesByChatId`.

```js
const changed = [...data.messages, ...data.updatedMessages];
```

New messages and edits/deletes travel the **same path** — concatenated and pushed through
the identical `toUiMessage` → `mergeMessages` pipeline. An edit or a delete is just a server
row with the same `id` overwriting the old one. There is no separate delete branch.

Three behaviours worth knowing:

- **The conversation list is replaced wholesale**, not merged. The server must therefore
  send the *complete* list whenever anything changed; a partial list would make unrelated
  chats vanish. Ordering comes entirely from the server.
- **Messages are only merged into already-opened conversations**, gated on
  `loadedConversationIdsRef`. An unopened chat accumulates nothing — it shows only a preview
  and a badge, and fetches its messages when opened.
- **Unread badges are never computed client-side.** They come from `conversation.unread` on
  the server. The client only *suppresses* the badge for the chat currently on screen.

The callback is `useCallback(..., [])`, so it reads the open chat through `selectedChatRef`
rather than `selectedChat` — a stale closure would otherwise pin it to the first chat
selected after mount.

### Initial load

**`GET /chat/sync` is also the bootstrap endpoint.** `chats` and `messagesByChatId` start
empty and are filled by the first poll's `isInitial` branch.

> `fetchConversations` (`chatService.js:120`) exists but has **no callers anywhere** — it is
> not even imported by `App.jsx`. It is dead code.

Messages load lazily per conversation on first open, via `loadConversationMessages`
(`App.jsx:282-297`). The conversation id is added to `loadedConversationIdsRef` *before* the
request and removed on failure, which doubles as in-flight deduplication so a double-click
issues one request.

---

## 4. The sync endpoint (server)

`ChatController::sync()`, `ChatController.php:207-272`. `SYNC_LIMIT = 200`.

### Two modes

```php
$isInitial = $input['after_message_id'] === null || $input['after_message_id'] === '';
```

Note the subtlety: **`after_message_id=0` is not initial.** An explicit `0` means "send me
everything visible from the beginning"; only an absent or empty value means "bootstrap me".

**Initial mode** returns empty message arrays, the full chat list, and a starting cursor:

```php
'cursor' => [
    'afterMessageId' => $this->conversationModel->maxLastMessageIdForUser($me),
    'readSince'      => grid_chat_iso($serverTime),
],
```

Seeding from `MAX(last_message_id)` means the first poll never re-downloads history — the
message pane fills from `/chat/messages` instead.

**Delta mode** runs up to three queries and returns:

| Field | Contents |
|---|---|
| `messages` | New visible rows with `id > after_message_id`, oldest first. |
| `updatedMessages` | Already-delivered rows (`id <= cursor`) edited or deleted since `read_since`. |
| `reads` | `[{conversationId, readUpToMessageId}]` read-tick updates. |
| `conversations` | The whole chat list — **only if something changed**, else `[]`. |
| `hasMore` | Whether more new messages are waiting. |
| `cursor` | The two values to send next time. |

The `conversations => $changed ? ... : []` conditional is what makes an idle poll cheap: a
quiet 3-second tick runs the queries, finds nothing, and returns three empty arrays.

### Paging and the two cursors

```php
$newRows = $this->messageModel->newForUserSince($me, $afterMessageId, self::SYNC_LIMIT);
$hasMore = count($newRows) > self::SYNC_LIMIT;
$newRows = array_slice($newRows, 0, self::SYNC_LIMIT);
```

The model asks for `LIMIT 201`; getting 201 rows back proves a 201st exists. That is the
`hasMore` probe, and it costs nothing extra.

```php
'afterMessageId' => $newRows ? (int) end($newRows)['id'] : $afterMessageId,
// Keep the old read cursor until all new messages are fetched
'readSince' => $hasMore && $readSince !== null ? $input['read_since'] : grid_chat_iso($serverTime),
```

`afterMessageId` advances to the id of the **last row actually delivered**, not `MAX(id)` in
the table — so the next poll resumes exactly where this batch ended and nothing is skipped.

`readSince` is deliberately **frozen while `hasMore` is true**. Advancing it mid-drain would
skip edits and read receipts that happened in the not-yet-fetched window.

---

## 5. Visibility rules

Every read query funnels through one SQL fragment, `MessageModel::VISIBLE_FROM`:

```sql
FROM messages msg
JOIN conversation_members me
  ON me.conversation_id = msg.conversation_id AND me.user_id = ? AND me.left_at IS NULL
WHERE msg.id > GREATEST(COALESCE(me.cleared_before_message_id, 0),
                        COALESCE(me.joined_after_message_id, 0))
  AND NOT EXISTS (SELECT 1 FROM message_hidden h
                  WHERE h.message_id = msg.id AND h.user_id = ?)
```

Clause by clause:

- **The `JOIN` is the authorization check.** Membership both restricts rows to conversations
  you belong to and pulls your personal watermarks into the same row, for free.
  `left_at IS NULL` means someone removed from a group instantly loses access to the entire
  history, including messages from while they were a member.
- **`cleared_before_message_id`** — "clear chat", set to the conversation's current
  `last_message_id` and only ever moved forward.
- **`joined_after_message_id`** — set when you are added to a group, so you cannot read what
  was said before you arrived. Re-joining sets a *fresh* join point, so it does not restore
  old history.
- **`GREATEST(COALESCE(...))`** — the effective floor is whichever watermark is higher; NULL
  (never cleared, or a direct chat) degrades to 0.
- **`message_hidden`** — per-user "delete for me" tombstones.

Note that `conversation_members.deleted_at` is **not** filtered here. Deleting a chat removes
it from your list, but does not hide its messages from sync — and any new message clears
`deleted_at` anyway, bringing the chat back ([§7](#7-delivery)).

### Why new and updated never overlap

```php
// newForUserSince:      ... AND msg.id >  ? ORDER BY msg.id ASC LIMIT n+1
// changedForUserSince:  ... AND msg.id <= ? AND (msg.edited_at > ? OR msg.deleted_at > ?)
```

The two queries partition the id space at the cursor, so a message is never both "new" and
"updated" in the same response. Mutation is detected by *timestamp*, not by id — which is
precisely why the endpoint needs the separate `read_since` time cursor alongside the id cursor.

Because "delete for everyone" only stamps `deleted_at`, the row survives and
`changedForUserSince` is the delivery path for deletions. `formatMessages` then nulls the
body, metadata and attachments, so the recipient receives a tombstone rather than the content.

---

## 6. Send lifecycle

```
user hits send
      │
      ├─► createClientId()                      UUID v4, the idempotency key
      │
      ├─► optimistic row inserted               id: local-<uuid>, status: "pending"
      │   mergeMessages()                       key = client:<uuid>
      │   setChats()                             chat jumps to top, preview updates
      │
      ├─► POST /chat/send  (or XHR upload)
      │
      ├── success ─► server row merged          SAME key client:<uuid> → replaced in place
      │                                          id becomes numeric, status "sent"/"read"
      │
      └── failure ─► status: "failed" + toast   terminal; no retry wired up
```

`sendToConversation` (`App.jsx:443-528`) builds the optimistic row by spreading the composer
object first — so local preview URLs for images and video render instantly — then explicitly
nulls `file` so the `File`/`Blob` is never retained in React state.

### The reconciliation trick

This is the part worth understanding properly. `mergeMessages`
(`src/utils/chatAdapters.js`) keys by `clientId` **in preference to** `serverId`:

```js
const messageKey = (message) =>
  message.clientId ? `client:${message.clientId}` : `server:${message.serverId ?? message.id}`;
```

The server echoes `client_id` back on the saved row, so the saved copy hashes to the *same*
key as the optimistic row and replaces it in place. No flicker, no duplicate, and the id
swaps from `local-<uuid>` to the numeric server id in a single step.

It also makes the send response and the sync poll idempotent **with respect to each other**:
whichever arrives first wins the slot, and the second is an identical overwrite. This matters
because a fast poll can easily return your own message before your own POST resolves.

`compareMessages` sorts unsent rows (no `serverId`) to the bottom, so a pending message stays
visually at the end of the conversation.

### Status transitions

| Status | Set by | Renders |
|---|---|---|
| `pending` | optimistic insert | clock, plus `N%` while uploading |
| `sent` | server row, `isRead === false` | one tick |
| `read` | server row `isRead === true`, or `applyReadReceipt` | two ticks |
| `failed` | send rejection | warning circle + toast |

The move out of `pending` is a **whole-object replacement**, not a field mutation — the
server row simply takes the slot.

`updateOptimistic` guards on `m.status === "pending"`, so a late-arriving failure cannot
clobber a row the sync poll has already replaced with the server copy.

---

## 7. Delivery

`MessageModel::createAndDeliver()` — five writes, one transaction, strict order.

```
transBegin
  1. INSERT messages                      → $messageId
  2. INSERT message_attachments           (only for media)
  3. UPDATE conversations                 last_message_id, last_message_at, updated_at
  4. UPDATE conversation_members (others) unread_count + 1, deleted_at = NULL
  5. UPDATE conversation_members (sender) last_read_message_id, unread_count = 0
transCommit
```

Details that matter:

- **One timestamp for everything.** `$now = grid_chat_now()` is captured once and reused for
  every write, so the message time, the conversation time and the read time are byte-identical.
  Downstream comparisons (`id <=`, `last_read_at >`) rely on that consistency.
- **Step 4 is raw SQL** so the increment happens atomically inside the engine:
  ```sql
  UPDATE conversation_members SET unread_count = unread_count + 1, deleted_at = NULL
  WHERE conversation_id = ? AND user_id <> ? AND left_at IS NULL
  ```
  A read-modify-write in PHP would lose increments under concurrency.
- **A new message un-deletes the chat.** `deleted_at = NULL` in the same statement brings the
  conversation back for anyone who had removed it from their list. Their
  `cleared_before_message_id` is untouched, so old messages stay cleared — the chat returns
  containing only the new message.
- **Sending implies reading** (step 5). This keeps the sender's own watermark current, which
  other members' read-tick maths depends on.
- **`assertWrite()`** wraps every write, converting CodeIgniter's silent `false` return (with
  `DBDebug` off) into a `RuntimeException`. Without it, a failed write would be ignored and a
  half-delivered message could commit.

Nothing is pushed anywhere. Commit *is* delivery.

---

## 8. Read receipts and unread counts

### Marking read

`ConversationMemberModel::markRead()` is **monotonic** — `if ($messageId > $current)` — so
duplicate or out-of-order read posts are harmless.

Crucially, `unread_count` is **recomputed by `COUNT`**, not decremented:

```sql
SELECT COUNT(*) FROM messages
WHERE conversation_id = ? AND id > ? AND sender_id != ?
```

That makes the counter self-healing: any drift introduced by the `+1` increments in
`createAndDeliver` is corrected the next time the user reads the chat. The caller also clamps
the target with `min($messageId, $lastMessageId)`, so a client cannot read past the end.

All three client call sites omit the message id, letting the server use the conversation's
current last message.

### Propagating receipts

`conversationIdsReadSince()` self-joins `conversation_members` against itself — `me` proves
you are still a member, `other` finds anyone whose `last_read_at` moved after your cursor.
It is supported by `idx_read_sync (conversation_id, last_read_at)` and returns only ids; the
tick values come from a second batched query.

### The tick rule

```php
'isRead' => $isOutgoing && $id <= ($readUpTo[$conversationId] ?? 0),
```

Because ids are a monotonic auto-increment, an `id <=` comparison is a valid "everything up
to here has been read" test. **No per-message receipt table is needed** — one integer per
member per conversation carries the whole thing.

`isRead` is only ever set on outgoing messages; you never see ticks on someone else's message.

### Why `min` in groups

```php
return min(array_map(fn ($m) => (int) ($m['last_read_message_id'] ?? 0), $members));
```

Two ticks means **everyone** has read it, so the watermark is the *minimum* across all other
active members — the id the slowest reader has reached. `max` would show "read" as soon as
one person opened it.

Consequences worth knowing:

- One member who never opens the chat pins the whole group's ticks at their watermark.
- A member leaving (`left_at`) removes them from the calculation and can un-stick ticks.
- New members are seeded at their join point, so adding someone to a busy group does not drag
  everyone's ticks back to zero.
- An empty member set returns 0, so a solo conversation never shows read.

### Client-side application

`applyReadReceipt` is a **high-water-mark sweep**, not a per-message flag — every outgoing
message with `serverId <= readUpToMessageId` flips to read. It skips conversations not
currently loaded, which is harmless: reopening refetches rows whose `isRead` is already true.

---

## 9. Idempotency

The `client_id` is a browser-generated UUID v4 (`createClientId`, with a hand-rolled fallback
because `crypto.randomUUID` is unavailable on plain-HTTP LAN addresses). It is validated
server-side against a strict UUID pattern.

`ChatController::send()` uses a check-then-catch pair:

1. **Pre-check** — `findByClientId($me, $clientId)` before inserting. A retry whose first
   attempt already committed gets the existing message back with HTTP 200, not a duplicate.
2. **Post-failure check** — if `createAndDeliver` throws, re-query by `client_id`. If a
   concurrent request won the race, the duplicate-key error is swallowed and the winner's row
   is returned. Any other exception is rethrown.

The PHP pre-check alone is racy. **`uq_sender_client (sender_id, client_id)` is the real
arbiter** — the database decides, and the application code just interprets the outcome.

MySQL permits unlimited NULLs in a unique index, so a send without a `client_id` simply opts
out of idempotency rather than colliding.

---

## 10. Media uploads

Media takes a parallel path with the same delivery guarantees.

`uploadConversationMedia` uses **XMLHttpRequest rather than `fetch`**, for one reason: upload
progress events. `xhr.upload.onprogress` feeds `updateOptimistic({ progress })`, which drives
the percentage shown on the pending bubble.

The request goes to `MediaController::upload`, which validates type and size against a
per-type allow-list, derives the extension from the *detected* MIME type, stores the file
under `files/grid_chat/media/<conversationId>/<yyyy>/<mm>/` with a random 16-byte name, and
then calls the **same `createAndDeliver`** with an attachment row. So media messages get the
same transaction, the same unread increments and the same `client_id` idempotency as text.

Files are never public. `files/grid_chat/media/.htaccess` denies direct access, and
`MediaController::show` re-checks conversation membership before streaming with HTTP Range
support. It calls `session_write_close()` first so a large download does not hold the session
lock — see [§12](#12-limitations-and-scaling-notes).

---

## 11. Failure modes

| What fails | What happens | What the user sees |
|---|---|---|
| A sync poll | `failures++`, exponential backoff, retry forever | **Nothing.** Errors are logged to console only. |
| A send | Optimistic row flips to `failed` | Warning icon + toast. No retry affordance. |
| A media upload | Same as send; 413 mapped to "File is too large" | Toast. |
| `loadConversationMessages` | Id removed from the loaded set so a reopen retries | Toast. |
| `markConversationRead` | Silently ignored | Nothing; corrected on next read. |
| A delivery write | `assertWrite` throws → full rollback | "Message not sent". |

Two deliberate design points:

- **A failed send is terminal.** There is no retry, even though `clientId` was specifically
  designed to make one safe. The idempotency groundwork exists; the retry button does not.
- **Sync failures are invisible.** A user on a flaky connection sees a chat that has quietly
  stopped updating, with no indication anything is wrong.

---

## 12. Limitations and scaling notes

Recorded deliberately, so none of these are a surprise later.

### Functional gaps

- **No older-message pagination.** This is the biggest one. `before_id` is supported end to
  end — the server implements keyset pagination and `chatService.fetchConversationMessages`
  accepts the argument — but the only call site (`App.jsx:286`) passes a single argument, so
  the parameter is stripped. **A conversation shows exactly one server page and cannot scroll
  further back.** There is no scroll-to-top handler and no `loadOlder` state anywhere.
- **No retry for failed sends** (see [§11](#11-failure-modes)).
- **No typing indicators and no presence.** There is no typing implementation at all — no
  keystroke handler, no field in the sync response. For a real conversation `toChat` only ever
  produces `""`, `"N members"` or `"Account deleted"`, so the green online dot and the
  "online" status line **never appear outside demo data**. The Settings "Last Seen & Online
  Status" toggle writes to a local preference that is never transmitted.
- **`fetchConversations` is dead code.**

### Polling characteristics

- **No jitter.** Every client polls on its own 3-second phase with no randomisation, so
  clients that start together stay in lockstep — a thundering herd after a server restart is
  not mitigated.
- **Backoff ignores visibility.** The `failures > 0` branch returns before the
  `document.hidden` check, so a failing hidden tab polls *faster* than a healthy one until
  backoff exceeds 30 s.
- **The cursor is per-mount**, so every reload re-bootstraps with a full chat list fetch.
- **`updatedMessages` has no `hasMore`.** Unlike `newForUserSince`, `changedForUserSince` takes
  a hard `LIMIT 200` with no `+1` overflow probe, so a very large edit/delete burst could be
  silently truncated.
- **Latency is the interval.** Median delivery is ~1.5 s and worst case 3 s for a visible tab;
  up to 30 s for a hidden one.

### Load

Each active client issues 20 requests per minute. Each one runs a PK range scan on `messages`
joined to the `conversation_members` PK, plus — only when something changed — a chat-list
query capped at 200 conversations. The queries are indexed and cheap; the cost is their
frequency multiplied by concurrent users.

**Session lock contention is the known pressure point.** PHP's default file session handler
holds an exclusive lock per session for the duration of a request, so overlapping requests
from the same user serialise. The codebase already acknowledges this: `streamFile()` calls
`session_write_close()` before streaming a large file, with the comment *"Don't hold the
session lock while a large file streams (chat sync would wait)."* If sync latency becomes a
problem under load, closing the session early in `sync()` is the first thing to try.

### Moving to push

The current shape makes this a contained change rather than a rewrite. The cursor,
reconciliation and merge logic all live in the sync response and `mergeMessages`, not in the
transport. A socket would not need to carry message payloads at all — it only needs to say
*"something changed"* and let the existing `tick()` fetch the delta:

1. Keep `useChatSync` and its cursor exactly as they are.
2. Add a socket connection that calls `schedule(0)` on a server nudge.
3. Drop the visible interval to a slow heartbeat (say 30 s) as a safety net for missed nudges.
4. Server-side, publish a nudge after `createAndDeliver` commits.

That yields sub-second delivery while keeping the polling path as the fallback, and every
correctness property in this document — idempotency, the cursor, visibility rules, read
watermarks — continues to hold unchanged.
