# Closed-app notifications & calls not delivered — server/FCM audit

**For:** back-end developer (server + FCM owner). *(Madam only uploads files; these are code/server/config checks, not deploy steps.)*
**Date:** 2026-11-19
**Device audited:** Redmi/iQOO I2202, logged in as **user 716**, app `com.example.efactor_sdui`, backend **efactor.tech (live)**.
**Audited by:** on-device ADB + logcat, app put in a real "closed" state (backgrounded + process killed, **not** force-stopped, so FCM can still wake it).

---

## Symptom (reproduced on device)

With the app fully closed:

- **Message** (web → user 716): no app wake, no notification — even after 3+ minutes.
- **Call** (web → user 716): no app wake, no ring, no full-screen incoming-call screen.
- **Zero FCM messages were delivered to the app** in the whole capture window. (Only Chrome's own GCM traffic appeared.)

Both the message path and the call path fail **identically**.

## The phone is healthy — the push dies before reaching it

All device-side requirements for closed-app delivery are correctly in place:

- App is **doze/battery whitelisted**, app-standby bucket **EXEMPTED**.
- `POST_NOTIFICATIONS` granted; notifications enabled on the main profile.
- `USE_FULL_SCREEN_INTENT`, `WAKE_LOCK`, `FOREGROUND_SERVICE` granted.
- On launch the app **re-registered its FCM token successfully (HTTP 200)** to **both** endpoints:
  - `POST https://efactor.tech/index.php/api/register_device_token` → `{"success":true,"message":"Device token saved","device_key":"","voip_token_saved":false}`
  - `POST https://efactor.tech/index.php/grid-chat/api/push/native-token` → same response.

So the server **has a fresh, valid token** for user 716. The failure is on the **server → Google FCM send**, not the device.

## Most likely root cause: Firebase project mismatch

Token *registration* only stores a string on our server, so it "succeeds" regardless. The actual **send** goes `our server → fcm.googleapis.com/v1/projects/<project_id>/messages:send` using the service-account key at `/etc/efactor/firebase-service-account.json`. If that key belongs to a **different Firebase project** than the app's token, every send returns `SenderId mismatch` / `UNREGISTERED` and **nothing is delivered** — which matches both failures exactly.

### The app's real Firebase project (extracted from the installed APK)

| Field | Value |
|---|---|
| **project_id** | **`grid-factor`** |
| project number / sender id | **`870386246990`** |
| app id | `1:870386246990:android:1d874dc0d427fa40b52a1f` |

## Back-end developer: please verify on the live server (in order)

1. **Service-account project must be `grid-factor`.**
   ```
   grep project_id /etc/efactor/firebase-service-account.json
   # expected: "project_id": "grid-factor"
   ```
   If it is any other project, this is the bug — replace it with the service-account key generated from the **grid-factor** Firebase project (Project settings → Service accounts → Generate new private key). The service-account `project_id` must equal **grid-factor** / number **870386246990**.

2. **Confirm the send is actually invoked and log the FCM response.**
   When a web→716 message/call is sent while 716 is offline, confirm our code calls `messages:send` and **log the HTTP status + response body** from FCM. A 200 with a message name = delivered; a 400/403/404 with `UNREGISTERED`, `SENDER_ID_MISMATCH`, or `invalid_grant` pinpoints the cause.

3. **Confirm the token the sender reads matches what was registered.**
   Check `native_push_tokens` for user 716 holds the token that was just registered (prefix `eGmU4tzVTVadMxao3wXo_O:APA91b...`) and that the send path reads from that table.

## Secondary notes (not the current blocker)

- `voip_token_saved:false` on both endpoints — harmless for Android (data FCM), but the VoIP token path should be saved for iOS calls later.
- On the phone, **"Display over other apps" (SYSTEM_ALERT_WINDOW) is OFF**. Not the current issue (delivery fails first), but once delivery works, enable it so the incoming-call screen reliably shows over the lock screen on iQOO/Vivo.

## What is NOT the cause (ruled out on device)

- Battery optimization / doze — app is exempted.
- Notification permission / channels — granted and present.
- Force-stop state — app was **not** force-stopped during the test.
- "Recently online" grace — the message was still undelivered after 3+ minutes closed.
