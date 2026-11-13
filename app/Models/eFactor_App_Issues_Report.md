# eFactor App: Full Issues Report

**Date:** 9 November 2026
**Tested on:** Samsung SM-A146B, Android 15, mobile data (LTE)
**App tested:** `com.example.efactor_sdui` v0.1.0 (debug build), logged in
**Also reviewed:** PHP backend (`app/`, `v8/`), device schema, `factor_db2.sql`, storage
**Project files changed:** None

> **Keep this report private.** It describes open security weaknesses. Do not store it in the web folder. Tokens, phone number, email and location are left out on purpose.

**Labels**

- **Reproduced:** seen on the phone during this test.
- **Confirmed:** seen in the code. Nothing was exploited.
- **Likely:** the evidence points here, but it still needs proof.
- **Check on server:** true in this local copy; the live server may differ.

---

## 1. Top problems

| # | Problem | Severity | Status |
|---|---|---|---|
| S1 | Login tokens can be forged | Critical | Confirmed |
| S2 | Uploaded PHP files can probably run on the server | Critical | Confirmed |
| A1 | Professional Space is stuck on sign-in and stays blank | Blocker | Reproduced |
| A2 | Edit Profile does not work (unhandled error) | High | Reproduced |
| A3 | Enable alerts says "enabled" but the save crashes | High | Reproduced |
| A4 | Almost every Home tile opens a placeholder page | High | Reproduced |
| A5 | Grid API returns HTTP 500, so Grid runs on built-in fallback data | High | Reproduced |
| A6 | Fan page 1 is 52 MB; the first open timed out after 3 minutes | High | Reproduced |
| A7 | Home re-downloads 421 KB every 10 seconds (about 150 MB/hour) | High | Reproduced |
| A8 | Grid Search, See all, Find Talent, Join Circles, avatar do not work | High | Reproduced |
| S3 | Access and push tokens are written to phone logs; token in URLs | High | Reproduced |
| S4 | Device schema APIs have no login check | High | Confirmed |

---

## 2. What works

| Feature | Result |
|---|---|
| Fan power from the device page | **Works.** Delivered through cloud in about 3 s; state updated in about 9 s |
| Fan pages 1-6 | Open and scroll once downloaded |
| Offline device page | Opens, shows Offline |
| Sending a chat message | Works, delivered ticks shown |
| Voice call | Works, connected and ended cleanly |
| Video call | Works, two-way video and audio |
| Circle Connect tabs, Settings, New chat, My Profile | Open correctly |
| Circle Connect bell | Opens Activity, no login page |
| Rating page | Opens signed in (not submitted) |
| Scrolling | Smooth, 0.42% janky frames |

---

## 3. Coverage

**Tested:** Home tiles, IoT list, search, Add Device, online fan pages 1-6, an offline device, Grid tab, Professional Space, Circle Connect (Chats, Moments, Live, Channels, Calls, chat menu, New chat, My Profile, Settings: Storage, Notifications, Privacy, Help), message, voice call, video call, app Profile pages.

**Not tested**

| Item | Reason |
|---|---|
| Tapping a real push notification | Needs an incoming message while the app is closed |
| Going Live, posting a Moment, creating channels or groups | Would publish content |
| Clear chat, Delete chat, Block, Delete device, Logout | Destructive or would end the session |
| Saving rating or profile edits | Not submitted; Edit Profile fails before a form opens |
| Other fan controls (Speed, Direction, name, motor registers) | Could affect the hardware |
| Switch on offline devices, Disable alerts | Not pressed |
| Wi-Fi, no network, rotation, background behaviour | Not covered in this run |
| Installed app `tech.efactor.ioe` v2.1.63 | Tested the debug build that was open |
| Flutter source, live database, `grid_db`, `grid_chat_db` | Not available |

Home tiles rotate, so every tile face could not be targeted individually.

---

## 4. IoT devices

**Correction to the earlier report:** fan control does work. The online fan was switched Off and back On successfully. Only 1 of 5 devices was online.

| # | Issue | Severity | Status |
|---|---|---|---|
| I1 | Fan page 1 is **52 MB** (`contentLength=52019998`). The first open timed out at 3 minutes with an unhandled exception and restarted the download. It opened on a later attempt. Later pages are 2.5-6 MB | High | Reproduced |
| I2 | Switches stay active on the 4 offline devices (`online=false ... enabled=true`) | High | Reproduced |
| I3 | Direction value is rejected on every load: `FormatException: enum value is not in options` | High | Reproduced |
| I4 | Status is unreliable: the fan showed Off for 2-3 minutes with no command sent, then On again. The page first shows OFF, then ON about 30 s later | High | Reproduced |
| I5 | Offline fan still displays "On" | Medium | Reproduced |
| I6 | Add Device shows raw `MobileScannerException(permissionDenied)` with no way to grant camera access | High | Reproduced |
| I7 | Customer-facing app exposes raw motor tuning (`Rs`, `Ld`, `Lq`, `Ke`, ramp, current limits) with Send buttons | High | Reproduced |
| I8 | Remote Address page shows wrong data: Speed "false", Direction "COMMAND: false", slider max 4294967295 | Medium | Reproduced |
| I9 | One device can overwrite its model's shared schema (`DeviceUiSchemaApi.php:207-211`) | High | Confirmed |
| I10 | A missing `controls.json` is skipped silently (`DeviceUiSchemaGenerator.php:83-100`) | High | Confirmed |

**Earlier concerns not seen for this device:** wrong node ID (the command went to the correct node) and missing MQTT details (commands use `CLOUD_FALLBACK` and were delivered). The schema slicer is missing from this local copy, but paging works on the live server.

**If a specific device still cannot be controlled, likely reasons in order:** it is offline; page 1 is still downloading; or it uses the Direction control (I3).

---

## 5. Login page and notifications

### A1. Professional Space stuck on sign-in (Blocker, Reproduced)
The screen stays blank and dark after 25 seconds. The web view is on `index.php/signin?redirect=...grid&grid_sso=***`.

**Cause (Confirmed against this code; Check on server):** the app sends a **3-part JWT** as `grid_sso`. The server only accepts its own **2-part** token (`app/Libraries/GridWebSso.php:367-370`), so it rejects the login. Circle Connect works because it opens with `access_token=` instead.

**Fix:** use one token format on both sides.

### N1. Notification opens the login page (High, Likely same cause)
Not yet proven, because a real push tap is still untested. The push link carries no sign-in token (`DevicePushService.php:1226-1242`), and web sessions last 2 hours (`app/Config/App.php:214`).

| Notification button | Result |
|---|---|
| Home "Live Alerts" bell and row | Do nothing |
| Profile > Notifications | Settings only, no list of notifications |
| Circle Connect bell | Opens Activity normally |

### N2. Related
- Only one Android phone per user receives pushes (`DevicePushService.php:34-38`). Confirmed.
- Push setup and web socket registration run several times each time Circle Connect opens. Reproduced.
- Chat web view logs "This browser does not support desktop notification". Reproduced.

---

## 6. Profile and settings

| # | Issue | Severity | Status |
|---|---|---|---|
| A2 | Edit Profile rows do nothing. Log: `Unhandled Exception: Unsupported Home host action "open-edit-profile"` | High | Reproduced |
| A3 | Enable alerts shows "Notifications enabled", but logs `Unsupported Home host action "save-profile-preferences"`. The setting may not be saved | High | Reproduced |
| P1 | Name shows the phone number; Circle Connect shows the real name | Medium | Reproduced |
| P2 | Email appears on app Profile but is "Not set" in Circle Connect | Medium | Reproduced |
| P3 | Enable and Disable both listed with no clear current state | Medium | Reproduced |
| P4 | Privacy text omits location, though GPS is sent at startup; it mentions a language option that does not exist | High | Reproduced |
| P5 | Wrong icons (bell on Email); Member ID has an edit arrow | Low | Reproduced |

---

## 7. Home

| Issue | Severity | Status |
|---|---|---|
| Tiles tested open "Generated detail page for Front/Back/Left/Right" (Leave, Profile, hotspot, Hot, image tiles) | High | Reproduced |
| Tiles rotate under the finger: tapped "Notify", opened "Hot News" | High | Reproduced |
| Date showed "Wed, 4 Nov" with a "real-time / now" badge; it changed to 9 Nov only later | High | Reproduced |
| Blank coloured tiles | Medium | Reproduced |
| "Network 12 nodes" conflicts with IoT "5 products" | Medium | Reproduced |
| Duplicate tiles (hotspot, Network, Setup) | Low | Reproduced |
| Admin-style tiles ("AppID Generation", "Keys", "Verify Key"): confirm they should be visible | Medium | Reproduced |
| Food images feel unrelated to an IoT app | Low | Reproduced |

---

## 8. Grid

| Issue | Severity | Status |
|---|---|---|
| Grid API fails: `get_grid` HTTP 500 every 10 s, labelled "OFFLINE" | High | Reproduced |
| Screen falls back to built-in data (`OFFLINE FALLBACK TO ASSET BUNDLE`), so 248 / 36 / 12 are not live | High | Reproduced |
| Search and See all show developer errors: `Grid host action "open-grid-search" has no destination in the schema yet` | High | Reproduced |
| The error banner stays on screen | Medium | Reproduced |
| Find Talent and Join Circles do nothing | High | Reproduced |
| "S" avatar does nothing, and differs from "PN" on Profile | Medium | Reproduced |

---

## 9. Circle Connect, chat and calls

| Issue | Severity | Status |
|---|---|---|
| Call starts ringing **before** microphone permission is granted | High | Reproduced |
| Permission prompt uses the ring time: call ended "Cancelled" at 30 s | High | Reproduced |
| No TURN relay (`hasTurn=false`). Calls worked here, but often fail across networks | High | Reproduced |
| Tapping a past call bubble redials with no confirmation | High | Reproduced |
| Android Back walks through previously opened tabs instead of leaving | Medium | Reproduced |
| Back in a Moment moves to the next story instead of closing | Medium | Reproduced |
| Go Live form turns on the camera as soon as it opens | Medium | Reproduced |
| Storage totals do not add up: 315 + 210 + 280 = 805 MB, shown as 700 MB. Figures may be placeholders | Medium | Reproduced |
| Help page shows desktop keyboard shortcuts and "GRID MyChat Web" | Low | Reproduced |
| Contacts and usernames show raw IDs such as `28FBF1778D...` | Medium | Reproduced |
| Own account appears as a chat, "(You)" | Low | Reproduced |

---

## 10. Device page UI

| Issue | Severity |
|---|---|
| Debug "Tap Inspector" button covers page 4 and the next arrow | High |
| Developer labels shown to users: "1 UI", "22 UI", `[00][01]` | High |
| Header shows "0" instead of the device name on the fan | Medium |
| Page names such as "Page1.Operation" | Medium |
| Page subtitle mismatch: "Configure.Config" with "Page 2.Operation" | Low |
| Fan details are titled "Gateway Details" | Medium |
| Loader shows no size or error | High |

## 11. General UI

| Issue | Severity |
|---|---|
| Status bar icons are dark on a dark background on most screens | High |
| Bottom bar highlights Home while on Profile sub-pages | Medium |
| Mixed light and dark screens even in Dark mode | Medium |
| IoT header date stuck on 2026-11-06 | High |
| Raw device names such as `face terminal_1A136427`; thermometer icon on a face terminal | Medium |
| Pale, hard-to-read model text on device cards | Medium |
| Back from Home exits the app with no warning | Low |

---

## 12. Performance and data

| Issue | Evidence |
|---|---|
| Home schema re-downloaded every 10 s even when unchanged | 421 KB each, about 150 MB/hour |
| Cache rewritten every 10 s | `CACHE SAVED bytes=420866` |
| Grid request fails every 10 s | HTTP 500 |
| Device status polled every 15 s | `getDevices`, `getAllNodesStatus` |
| Memory use about 790 MB, swap up to 177 MB | `dumpsys meminfo` |
| Hidden web view keeps redrawing on other screens | Likely the Grid globe |
| Cold start 3.9 s | Debug build |

---

## 13. Security

### S1. Login tokens can be forged (Critical, Confirmed)
The signing secret is a fixed string plus the website name. Every logged-in page accepts these tokens, so accounts can be taken over.
`app/Libraries/GridWebSso.php:14,781`, `Security_Controller.php:19`

### S2. Uploaded PHP files can probably run (Critical, Confirmed in code)
`.php`, `.sh` and `.html` uploads are allowed, the extension is kept, and `uploads/` has no execution block. `.php` files already exist there.
`file-manager-v3/app/Services/FileService.php:34`, `StorageService.php:53`

### S3. Secrets on the phone (High, Reproduced)
The access token and full push token are printed to device logs, the access token is in web view URLs, and this is a `DEBUGGABLE` build.

### S4. Device APIs have no login check (High, Confirmed)
`v8/api_schema.php` and `v8/api_scan.php` have no user check and allow any website. `DeviceUiSchemaApi::store` can overwrite schemas.

### S5. Other
| Issue | Location | Label |
|---|---|---|
| GPS location sent at startup, not in the Privacy text | `user_currentLocation` | Reproduced |
| Database dump and capture files in the web folder | `factor_db2.sql`, `capture.pcap`, `rawchat.jsonl` | Check on server |
| CSRF off for many modules | `app/Config/Filters.php:37-51` | Confirmed |
| Session cookie not HTTPS-only or HttpOnly | `app/Config/App.php:315,326` | Confirmed |
| API tokens stored as plain text, no index | `users.access_token` | Confirmed |
| Client-sent IDs widen who receives a push | `DevicePushService.php:514-542` | Confirmed |
| Error responses show server file paths | `DeviceUiSchemaApi.php:80-90` | Confirmed |
| Delete routes accept GET | `app/Config/Routes.php:427-430` | Confirmed |
| Old and test scripts in `v8/` | `api_previous_working.php`, `api_demo.php`, `.log` | Check on server |

---

## 14. Schema and database

| Issue | Detail |
|---|---|
| Page 1 schema is 52 MB | Compare 2.5-6 MB for later pages |
| Schema rebuilt on every read, up to 2 GB memory | `v8/api_schema.php:6,87-94` |
| Home and IoT schemas have no change check | `no metadataEndpoint/deltaEndpoint` |
| Missing schema still returns HTTP 200 | `v8/api_schema.php:158` |
| One schema row per model | `model_name` UNIQUE, but the table has `node_id` |
| `device_scan` missing from the dump | Used by `v8/api_scan.php:37` |
| Almost no foreign keys | Only 3 matches across 149 tables |
| One table per model with awkward names | `efcker v1.0`, `bajaj-001fmn01` |
| `mydevices` poorly structured | MyISAM, latin1, duplicates, dates as text |
| Mixed character sets | `latin1`, `utf8mb3`, `utf8mb4` |
| Leftover tables | `demo`, `mytable`, `product_api_old`, `sticker_fields_backup` |
| Email limited to 25 characters in several tables | Long addresses cut or rejected |

## 15. Storage

| Issue | Label |
|---|---|
| Executable file types accepted (S2) | Confirmed |
| Filenames use `uniqid()`, which can be guessed | Confirmed |
| Large schema files can fail to save when folders are not writable | Confirmed |
| Storage plans set to activate with no payment gateway | Setting confirmed, flow not traced |

NAS was only partly reviewed.

## 16. Project

| Issue | Label |
|---|---|
| Debug build, package `com.example.efactor_sdui`, v0.1.0; app reports `v5.2` to the server | Reproduced |
| Files missing here: `DeviceUiSchemaPageSlicer.php`, `newapi.php`, `grid_api.php`, `grid_api_creation.php` | Check on server |
| Routes point to missing `OrderController`, `AttendAttendanceController`, `WorkHiveAuditController` | Check on server |
| QR login route points to a missing method | Check on server |
| About 10 old `Items` controllers beside live code, with auto-routing on | Confirmed |
| Duplicate routes; `attend/*` maps to two controllers | Confirmed |
| `routes/Api.php` is Laravel code in a CodeIgniter project | Confirmed |
| Docs are out of date; no git | Confirmed |

---

## 17. Suggested fix order

1. **Security:** S1 and S2, then reset tokens and sessions.
2. **Sign-in:** one token format (A1, N1).
3. **Broken actions:** Edit Profile, Enable alerts, Grid actions, Home bell (A2, A3, A8).
4. **Grid API:** fix the HTTP 500 (A5).
5. **Device page:** shrink page 1, fix retries, Direction, offline switches, Add Device (I1-I6).
6. **Remove debug UI:** Tap Inspector, "1 UI" labels, restrict motor tuning (I7).
7. **Data use:** stop the 10-second full downloads (A7).
8. **Calls:** ask for permission before ringing, add TURN.
9. **Replace placeholder pages**, then fix UI items.
10. **Protect APIs and logs**, then clean the database, routes and backups.

---

## 18. Actions on the account during testing

- 1 test message sent to Sai Tulasi Ram ("App test message please ignore").
- 4 calls to Sai Tulasi Ram: 1 cancelled, 2 voice (about 20 s each), 1 video (51 s). One of the voice calls was unintended, caused by tapping a call bubble.
- Fan switched Off, then On again.
- Microphone allowed "Only this time". Camera permission changed from not granted to granted during the test; the exact step is unknown.
- A Moment story was viewed. Nothing was posted, deleted or broadcast.
