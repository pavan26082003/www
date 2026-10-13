# 📋 GRID Chat — Architecture & Component Registry (`Change.md`)

> **Purpose:** Compact, high-density architecture reference and mutation history for GRID Chat. Designed for zero-overhead context synchronization and minimal token utilization.

---

## 1. System Architecture & Layout Hierarchy

- **Stack:** React 19 + Vite + Vanilla CSS (Design Tokens). Dual theme: Light & `[data-theme="dark"]`.
- **Desktop Layout (>1024px):**
  - Left: `Sidebar` (default collapsed 68px icon tiles; expands to 210px on toggle).
  - Center: `ChatList` (380px–430px) or active Workstation (Channels, Live, Moments, Calls, Calendar, Profile, Settings).
  - Right: `Conversation` (1fr flex).
- **Mobile/Tablet Layout (<=1024px):**
  - Single active panel: `ChatList` OR `Conversation` with `.mobile-back-btn`.
  - Bottom: `MobileNav` (5 tabs: Chats, Moments, Live, Channels, Profile). Desktop Sidebar hidden.

---

## 2. Core File & Component Registry

| Component / File | Path | Role & Key Features |
| :--- | :--- | :--- |
| `App.jsx` | `src/App.jsx` | Root state coordinator: active chat, workstations, toast, theme, message mutations. |
| `Header.jsx` | `src/components/Header/` | Top branding, global search, notifications, profile shortcut. |
| `Sidebar.jsx` | `src/components/Sidebar/` | Collapsible navigation with squircle icon tiles and hover tooltips. |
| `MobileNav.jsx` | `src/components/Mobile/` | Responsive bottom navigation bar (5 primary tabs). |
| `ChatList.jsx` | `src/components/ChatList/` | Search, filter pills, 3-dots settings dropdown, chat cards, new chat trigger. |
| `ChatCard.jsx` | `src/components/ChatList/` | Individual chat row: avatar, unread badges, typing/voice status, dark-theme name styling. |
| `Conversation.jsx` | `src/components/Conversation/` | Active chat room: header, selection toolbar, messages feed, composer, actions backdrop. |
| `Message.jsx` | `src/components/Conversation/` | Bubble renderer: text, media, poll, event, contact, file; hover trigger chevron; long-press; right-click contextmenu; reaction pills; quoted card; deleted tombstone. |
| `Composer.jsx` | `src/components/Conversation/` | Input bar: reply quote banner, edit mode banner, 7-item attachment drawer, emoji picker, auto-focus. |
| `MessageReactionBar.jsx` | `src/components/Conversation/` | Floating reaction bar (`👍 ❤️ 😂 😮 😢 🙏 ＋`) + expandable 24-emoji palette. |
| `MessageContextMenu.jsx` | `src/components/Conversation/` | WhatsApp action menu: Reply, React, Copy, Forward, Star/Unstar, Pin/Unpin, Edit, Info, Delete. |
| `DeleteMessageModal.jsx` | `src/components/Conversation/` | Dialog titled **"Deleting Messages?"** with 3 vertical column buttons: `Delete For Every One`, `Delete for me`, `Cancel`. |
| `Channels.jsx` | `src/components/Channels/` | Channel discovery, broadcast feed, post reactions, comments, channel creation modal. |
| `LiveStreaming.jsx` | `src/components/LiveStreaming/`| Live streams grid, full player, live chat drawer, virtual gifts, Go Live modal. |
| `Moments.jsx` | `src/components/Moments/` | Ephemeral stories/status carousel, media viewer, creation drawer. |
| `Calls.jsx` | `src/components/Calls/` | Voice/video call logs, direct dial actions (3-dots removed). |
| `Calendar.jsx` | `src/components/Calendar/` | Social communication calendar: Month, Week, Day timeline, Relationship milestones, Event modals. |
| `Settings.jsx` | `src/components/Settings/` | 7-section workstation: Appearance, Profile, Notifications, Privacy, Chats, Storage, About. |
| `Profile.jsx` | `src/components/Profile/` | Profile management: device photo upload, avatar presets, interactive inline bio editing. |
| `avatar.js` | `src/utils/avatar.js` | Unified avatar URL resolver for base64 data URLs, blobs, presets, and fallbacks. |

---

## 3. Design System & CSS Tokens

- **Fonts:** `Inter, Roboto, system-ui, sans-serif`. Sizing: title 18-20px, body 15px, meta 12-13px.
- **Key Variables (`App.css`, `index.css`):**
  - Brand: `--color-primary: #7047eb` (Indigo), hover `#5b32d9`, light `#ede9fe`.
  - WhatsApp Accents: Emerald `#10b981`, Teal `#06b6d4`, Danger `#ef4444`, Star `#f59e0b`.
  - Surfaces (Light): Background `#f8fafc`, Card `#ffffff`, Border `#e2e8f0`, Text `#1e293b`.
  - Surfaces (Dark - `[data-theme="dark"]`): Background `#0b1120`, Surface `#111827`, Card `#1e293b`, Border `rgba(255,255,255,0.12)`, Text `#f8fafc`.
- **Responsive Breakpoint:** `--mobile-breakpoint: 1024px` for single-panel mobile transition.

---

## 4. State & Event Flows

- **Active Chat & Workstation:** Managed in `App.jsx` (`selectedChat`, `activeWorkstation`). Switching chat resets selection mode and attachments.
- **Message Selection & Bulk Actions:**
  - `isSelectionMode` (boolean) & `selectedMessageIds` (array) in `Conversation.jsx`.
  - Header switches to green selection bar with count, Star, Copy, and Trash buttons.
  - Delete opens `DeleteMessageModal` with 3 vertical column buttons.
- **Reaction Flow:**
  - Selecting emoji updates `message.reactions` array (`{ emoji, count, userReacted }`).
  - Interactive pills display below bubble; clicking toggles user reaction.
- **Quoted Reply & Edit:**
  - `replyingTo` state in `Conversation.jsx` passes quote to `Composer.jsx`.
  - Sending embeds `replyTo: { sender, text }` in outgoing message payload.
  - `editingMessage` pre-populates draft in composer for inline update.

---

## 5. Incremental Mutation History

### [Backend Namespace Overhaul & Sync Bug Fixes] - 2026-09-18
- Renamed backend directories to uppercase: `GRID_CHAT_Controllers` → `GRID_CHAT_CONTROLLERS`, `GRID_CHAT_Config` → `GRID_CHAT_CONFIG`, `GRID_CHAT_Models` → `GRID_CHAT_MODELS`.
- Updated `namespace` and `use` import declarations across 13 PHP files (Controllers, Models, Routes) to reflect the new uppercase folder structure.
- Fixed a silent failure in `GridChatBaseController::formatMessages()` where `$pollVotesByMessage` was missing from the `array_map` closure scope, which caused "Something went wrong" errors during recipient message sync.
- Repaired database inconsistencies in `conversations` table where `last_message_id` pointers were out of sync with actual stored messages.

### [Poll Votes Persistence: Backend & Frontend Full Integration] - 2026-09-18
- Resolved missing poll vote persistence in `grid_chat_db.poll_votes` (`message_id`, `user_id`, `option_index`, `voted_at`).
- Created `PollVoteModel.php` in `app/Models/GRID_CHAT_Models/` with atomic upsert query (`INSERT ... ON DUPLICATE KEY UPDATE`) and message batch vote aggregator.
- Added `ChatController::vote_poll()` action with membership verification, option boundary validation, and real-time poll summary response.
- Registered `/grid-chat/api/chat/vote_poll` and `/grid_chat/api/chat/vote_poll` POST routes in `Config/GRID_CHAT_Config/Routes.php`.
- Integrated `GridChatBaseController::formatMessages()` to automatically hydrate all poll messages with live vote counts, total votes, and current user vote index (`userVotedIndex`).
- Connected frontend `chatService.js` (`votePoll`), `App.jsx` (`handleVotePoll`), `Conversation.jsx`, and `Message.jsx` with optimistic UI and live sync.

### [Contact Profile 3-Dots Menu & Edit Contact View] - 2026-09-18
- Added 3-dots action button (`⋮`) to Contact Profile header matching WhatsApp Reference Image 1 with popup dropdown showing **Share** and **Edit** only.
- Created `EditContact.jsx` & `EditContact.css` matching Reference Image 2: First/Last name, Username (`@`), Country (`IN +91`) & Phone, Sync to phone toggle with green switch & checkmark, Sync to email dropdown, and bottom full-width green "Save" button.
- Integrated `ShareContactModal.jsx`: opens member list to share contact with existing chat members.
- Added support for both **Light** and **Dark** themes (`[data-theme="dark"]`) across web and mobile layouts. Optimistically updates contact details in `App.jsx` and `ContactProfile.jsx`.

### [Message Spacing & Reaction Bar Collision Fix] - 2026-09-18
- Tightened `.message-row` margin to `1.5px 0` (matching WhatsApp zero-gap between consecutive messages; 8px between conversation turns). Removed 1-on-1 mini-avatar offset.
- Fixed Reaction Bar and Context Menu positioning in `Conversation.jsx`: preserved middle placement while solving top and bottom message overflows/overlaps so the menu never covers the reaction bar.

### [Camera Capture: Added Video Recording Mode] - 2026-09-18
- Upgraded `CameraCaptureModal.jsx` and `CameraCaptureModal.css` to support both **PHOTO** and **VIDEO** modes via segmented tab switcher.
- Video Mode: Records camera feed with audio using `MediaRecorder`, displays live pulsing recording badge (`REC 00:05 / 01:00`), and features stop/retake controls.
- Video Preview: HTML5 video player preview with full controls before sending.
- Propagated `messageType: "video"` payload through `ConversationAttachmentLayer.jsx` and `App.jsx`, rendering interactive `.video-bubble` messages with video preview player.
- Added front/back camera flip button and device fallback.

### [WhatsApp Message Interactions & 3-Button Delete Modal] - 2026-09-17
- Added desktop hover chevron trigger (`.msg-action-trigger-btn`), right-click contextmenu, and 450ms mobile long-press on message bubbles.
- Created `MessageReactionBar.jsx` (6 default reactions + `＋` expandable 24-emoji grid) with interactive pills below bubbles.
- Created `MessageContextMenu.jsx` (Reply, React, Copy, Forward, Star, Pin, Edit, Info, Delete).
- Integrated multi-select mode with header toolbar (count, Star, Copy, Trash) and circular checkboxes.
- Created `DeleteMessageModal.jsx`: dialog titled **"Deleting Messages?"** with 3 column buttons stacked vertically:
  1. `Delete For Every One` (replaces bubble text with tombstone `🚫 This message was deleted`)
  2. `Delete for me` (hides message locally)
  3. `Cancel`
- Implemented reply quote preview banner and edit message banner in `Composer.jsx`.
- Verified on desktop & mobile viewports under light and dark themes.

### [Profile Workstation: Device Photo Upload & Interactive Bio] - 2026-09-17
- Added "Choose from device" file upload (up to 8MB) via FileReader base64 data URL in photo selection modal.
- Created `src/utils/avatar.js` (`getAvatarUrl`) to normalize data URLs, blobs, URLs, and preset IDs across all components.
- Removed static online status line; added interactive Bio card with inline editing, 160-char counter, and dark mode styling.

### [Calls Workstation: 3-Dots Action Button Removed] - 2026-09-17
- Removed secondary 3-dots button from call cards in `Calls.jsx`, preserving dedicated voice/video action buttons.

### [Local Network Exposure Configuration] - 2026-09-16
- Configured `server.host: true` in `vite.config.js` and `--host` in `package.json` for LAN access (`0.0.0.0`).

### [Calendar Ecosystem, Breakpoint & Dark Theme Updates] - 2026-09-11
- Added unified social communication calendar (`src/components/Calendar/`): Month, Week, Day, Relationship, Availability views.
- Upgraded mobile breakpoint across stylesheets to `<= 1024px` for seamless window resizing.
- Added 3-dots WhatsApp menu in `ChatList.jsx` linking directly to Settings sections.
- Set sidebar default state to collapsed with themed squircle icon tiles and hover tooltips.
- Fixed dark theme contact name contrast and unified typography tokens (`Inter`).
