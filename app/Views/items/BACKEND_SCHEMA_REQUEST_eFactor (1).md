# eFactor SDUI — Backend / Schema Change Request

**From:** Flutter client team  
**App:** `efactor_sdui` (Home · Grid · IoT · Device SDUI)  
**Date:** 22 Sep 2026  
**Purpose:** Schema/API updates needed so offline UI, small-phone Home layout, and Grid in-app navigation work without client hardcoding.

---

## Summary

The Flutter app already:

- Caches Home / Grid / IoT / Device schemas on disk for offline use  
- Prefetches and disk-caches schema images (30-day cache)  
- Opens Grid HTTPS destinations **in-app** by default (WebView), not Chrome  
- Shrinks Home metro grids on narrow screens when schema provides responsive rules  

Several behaviours still depend on **schema fields**. Please add or fix the items below so we do not invent URLs, layout numbers, or navigation modes on the client.

---

## 1. Grid — keep destinations inside the app (not the system browser)

### Problem
If a Grid `open-url` action omits `openIn`, older clients opened Chrome. Current clients default to in-app WebView, but the contract should be explicit.

### Required schema shape

```json
{
  "type": "open-url",
  "url": "https://example.com/path?sso=…",
  "openIn": "inAppWebView",
  "title": "Professional Space"
}
```

Also valid:

```json
{
  "type": "open-url",
  "destination": {
    "url": "https://example.com/path",
    "openIn": "inAppWebView",
    "title": "Circle Connect"
  }
}
```

### Rules
| `openIn` value | Client behaviour |
|----------------|------------------|
| `inAppWebView` (recommended for Grid) | Flutter in-app WebView |
| `external` / `browser` / `externalApplication` | System browser (only when intentional) |
| omitted | Client currently treats as in-app; **please still send `inAppWebView`** |

### Also check Home → Grid entry
- Use host action `open-grid` / `open-grid-module` (or navigate to the live Grid route).  
- Do **not** use `external-navigate` with a Grid HTTPS URL if Grid should stay inside the app.

### Feature cards
If the card has `url` / `route` / `title` / `openIn` on the **component**, also put the same `openIn` on the linked **action** in `actionRegistry` so all clients agree.

---

## 2. Grid — offline QR images (no third-party QR CDN on the client)

### Problem
The client **no longer** invents `https://api.qrserver.com/...`. Offline QR only works if the schema provides an image URL.

### Please provide one of:

**A. Static image (simplest)**  
```json
"shareQr": {
  "qrPayload": "https://…",
  "qrImageUrl": "https://cdn.example.com/grid/qr.png"
}
```

**B. Per-mode images**  
```json
"shareQr": {
  "qrPayload": "https://…",
  "qrImageUrlsByMode": {
    "professional": "https://cdn.example.com/qr-pro.png",
    "personal": "https://cdn.example.com/qr-personal.png"
  }
}
```

**C. Template (your host only — no fixed third-party host in the app)**  
```json
"shareQr": {
  "qrPayload": "https://…",
  "qrImageUrlTemplate": "https://your-api.example.com/qr?data={data}&mode={mode}"
}
```

Placeholders supported by the client: `{data}`, `{payload}`, `{mode}` (URL-encoded).

---

## 3. Home — responsive layouts for all phone sizes

### Problem
On some narrow phones the Home grid selected breakpoint `narrow` (or similar) while the schema only published `compact` / `medium`. That produced layout errors or empty grids.

### Required
For every Home `responsiveGrid` / metro component, publish complete layouts for **each breakpoint** you declare in `responsive.breakpoints`, at least:

- `narrow` (phones &lt; ~360 logical px, if you use that name)  
- `compact`  
- `medium`  
- `expanded` (if used)

Each layout must include:

```json
{
  "columns": 4,
  "columnGap": 8,
  "rowGap": 8,
  "rowHeight": 72
}
```

Optional but recommended:

```json
"minimumCellWidthDp": 140,
"minRowHeight": 64,
"maximumContentWidth": 720
```

### Recommended responsive.grid policy

```json
"responsive": {
  "defaultBreakpoint": "compact",
  "breakpoints": {
    "narrow": { "maxWidth": 359 },
    "compact": { "minWidth": 360, "maxWidth": 599 },
    "medium": { "minWidth": 600, "maxWidth": 839 },
    "expanded": { "minWidth": 840 }
  },
  "grid": {
    "narrowScreenThresholdDp": 360,
    "narrowScreenMaxColumns": 2,
    "minimumCellWidthDp": 140,
    "clampColumnSpanToColumns": true,
    "preserveMetroColumnsOnNarrow": false
  },
  "overflowPolicy": {
    "headerText": "ellipsis",
    "default": "ellipsis"
  }
}
```

Notes for the client:

- If `preserveMetroColumnsOnNarrow` is **true**, metro keeps full column count on small phones (can look cramped).  
- Default client behaviour shrinks metro when `narrowScreen*` is present unless that flag is true.

Also set `overflowPolicy: "clip"` (or equivalent) on dense metro cards when children must not paint outside the tile.

---

## 4. Offline — schemas and images

### Already expected from APIs
| Surface | Cache key idea | Must decode successfully offline |
|---------|----------------|----------------------------------|
| Home | per `user_id` | Full Home schema document |
| Grid | per `user_id` | Root must expose `page` + `components` (or nested `schema` with those) |
| IoT / Device | per source / model+node | Full schema JSON |

### Image / asset contracts
1. Use **stable HTTPS** URLs for logos, banners, avatars, QR images.  
2. Prefer domains listed in `security.allowedAssetDomains` **or** declare image URLs inside the schema (client also allows hosts found on schema image fields).  
3. Avoid rotating query strings on every response if offline cache should hit the same file.  
4. After login, the client prefetches Home + Grid + IoT schemas and warms image URLs from those documents — first successful online session is required before full offline fidelity.

### Grid response shape reminder
`get_grid` (or equivalent) must return a JSON object that, after unwrap, has:

- `page` (object)  
- `components` (object)  

Otherwise disk cache decode fails and offline Grid stays empty.

---

## 5. What the Flutter client will **not** hardcode

Please do **not** rely on the app to invent:

- Layout numbers (`columns`, `rowHeight`, gaps) when missing from schema  
- Third-party QR image hosts  
- Product colors / IoT endpoints / device names  
- Which Grid links are “in-app” vs “external” beyond the documented `openIn` values  

If something is missing, we will surface a clear gap to your team rather than guessing.

---

## 6. Acceptance checklist (backend + QA)

- [ ] All Grid in-app destinations send `"openIn": "inAppWebView"`.  
- [ ] Home does not open Grid via `external-navigate` unless external is intentional.  
- [ ] Share QR provides `qrImageUrl` and/or `qrImageUrlsByMode` and/or `qrImageUrlTemplate`.  
- [ ] Home publishes `layouts.narrow` (and other used breakpoints) with full metrics.  
- [ ] `responsive.grid.narrowScreen*` values are set for small phones.  
- [ ] Grid API always returns `page` + `components`.  
- [ ] Image URLs are HTTPS and stable enough to cache.  
- [ ] QA: open Home + Grid once **online**, then airplane mode — shells and cached images still render; Grid taps stay in-app WebView (need network for remote HTML pages themselves).

---

## 7. Contact / reference

- Flutter module: `efactor_sdui_flutter/efactor_sdui`  
- Related client areas: `grid_sdui/*`, `home_sdui/*`, `services/schema_prefetch.dart`, `sdui/sdui_network_image_cache.dart`  
- Full local setup notes (optional): `SETUP_REPORT.md` in the same module  

Please reply with ETA or questions on any field naming differences so we can align the client parsers if needed.
