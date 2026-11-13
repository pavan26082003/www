# eFactor: Issues for the Web Developer (Schema Generation and Backend)

**Tested:** 9 Nov 2026 against `efactor.tech` from a Samsung SM-A146B, plus local code in `app/` and `v8/`
**Release status: NOT production-ready.** Fix P0 and P1, then pass section 9.

> Internal only. Do not put this file in the web folder. A copy of the earlier report is at `app/Models/eFactor_App_Issues_Report.md`: move it out.

**Reproduced** = seen on phone or live API. **Confirmed** = seen in code, not exploited. **Check on server** = true in the local copy.

---

## 1. P0: Security

| ID | Issue | Location | Fix |
|---|---|---|---|
| WEB-1 | Login tokens can be forged: the secret is a fixed string plus the host name, and all logged-in pages accept them | `Libraries/GridWebSso.php:781`, `Security_Controller.php:19` | Random secret from environment, short expiry, then invalidate sessions and tokens |
| WEB-2 | Uploaded `.php`, `.sh`, `.html` can probably run; `.php` files already in `uploads/` | `file-manager-v3/app/Services/FileService.php:34`, `StorageService.php:53` | Remove those types, block execution, store outside web root, review existing files |
| WEB-3 | Device schema APIs have no user check and allow any origin | `v8/api_schema.php`, `v8/api_scan.php` | Require a token, check device ownership, restrict CORS |
| WEB-4 | `DeviceUiSchemaApi::store` can overwrite schemas with no login; auto-routing is on | `DeviceUiSchemaApi.php:94`, `Routes.php:39` | Require authorisation, turn auto-routing off |
| WEB-5 | Dump and capture files in the web folder | `factor_db2.sql`, `capture.pcap`, `rawchat.jsonl` (Check on server) | Remove; rotate tokens if ever reachable |
| WEB-6 | CSRF disabled under "TEMPORARY DEBUG TEST" | `Config/Filters.php:37-51` | Restore |

Also: cookie flags (`App.php:315,326`), plain-text `users.access_token` with no index, server paths in errors (`DeviceUiSchemaApi.php:80-90`), delete routes accepting GET (`Routes.php:427-430`), old scripts in `v8/`.

---

## 2. P0: Sign-in and Grid

| ID | Issue | Evidence | Fix |
|---|---|---|---|
| WEB-7 | `get_grid` HTTP 500 | Every 10 s on the phone. `v8/grid_api_creation.php` is not in the local copy | Check the live error log and fix |
| WEB-8 | Two token formats: app sends a JWT, `verify()` needs 2 parts | `GridWebSso.php:367-370` | Agree one format with the app developer |
| WEB-9 | Web sessions last 2 hours; long-session setting applies only to the token request | `App.php:214`, `GridWebSso.php:447` | Align session lifetime with the app login |

---

## 3. P0: IoT schema and control

**Live result, `EF-FAN-V2-D-18-9`:** Power, Speed and Buzzer were delivered and read back. Direction was not sent because of WEB-10. The earlier concerns about a wrong node ID and missing MQTT details were **not** reproduced: live `deviceCommands.node_id` was correct and an `mqtt` block exists.

### WEB-10. Direction option is wrong (Reproduced)
```json
{"type":"trigger","options":[{"label":"[00][01]","value":"[08][08]"}],"value":"00"}
```
- The label holds what looks like the value, the value is a placeholder, and there is only one option.
- Log on every load: `rejected 1_direction_command_2_trigger=[[00][01]]: enum value is not in options`.
- **Do not send this command until fixed:** the frame value is unverified.
- **Fix:** generate real options (for example Forward and Reverse with correct protocol values), and validate before publishing.
- **Scan all models** for the same fault, since the generator is shared.

### WEB-11. Page payload far too large (Reproduced)
| Request | Size |
|---|---|
| `page_no=1` to phone | **52,019,998 bytes**, timed out at 3 minutes |
| `page_no=1` to PC, about 45 minutes later | 8,312,325 bytes |
| Pages 4, 5, 6 | 2.5, 6.2, 5.6 MB |
| `part=meta` | 2.4 MB |

- `controls` is 6.39 of the 8.3 MB page. No base64 strings were found, so it is repeated per-control data.
- Response was **not compressed** when gzip was requested.
- The 52 MB vs 8.3 MB difference is unexplained. Find out why.
- `DeviceUiSchemaPageSlicer.php` is missing locally; if it goes missing on the server, `api_schema.php:75-77` silently returns the full schema.
- **Fix:** enable gzip, remove duplicated control data, target under 500 KB per page.

### WEB-12. Other schema and control issues
| ID | Issue | Location | Status |
|---|---|---|---|
| WEB-13 | Power API returns `success` for an offline device | `powerToggle` response, device `B3E8...` | Reproduced |
| WEB-14 | Delivered means sent, not done; no device acknowledgement | `DeviceCommandStatus.delivered response=` empty | Reproduced |
| WEB-15 | One device's scan overwrites the shared model row | `DeviceUiSchemaApi.php:207-211` | Confirmed |
| WEB-16 | `model_name` UNIQUE but table also has `node_id` | `device_ui_schemas` | Confirmed |
| WEB-17 | Missing `controls.json` skipped silently | `DeviceUiSchemaGenerator.php:83-100` | Confirmed |
| WEB-18 | Schema rebuilt on every read, up to 2 GB memory | `v8/api_schema.php:6,87-94` | Confirmed |
| WEB-19 | Served schema has `meta.productionReady:false` with no errors | Live API | Reproduced |
| WEB-20 | No `metadataEndpoint` or `deltaEndpoint` | App log | Reproduced |
| WEB-21 | Missing schema returns HTTP 200 | `api_schema.php:158` | Confirmed |
| WEB-22 | Remote Address page has wrong bindings: Speed "false", Direction "COMMAND: false", slider max 4294967295 | Fan page 4 | Reproduced |
| WEB-23 | Current, Voltage and RPM read zero while the fan is On | Fan page 1 | Reproduced, check the device |
| WEB-24 | Status changed to Off for 2-3 minutes with no command sent | IoT list | Reproduced |

### WEB-25. Device page content for customers (Reproduced)
- Developer labels: "1 UI", "22 UI", `[00][01]`.
- Header shows "0" instead of the device name.
- Page names "Page1.Operation"; title "Configure.Config" with "Page 2.Operation".
- Details titled "Gateway Details" for a fan.
- **Raw motor tuning (`Rs`, `Ld`, `Lq`, `Ke`, ramps, current limits) with Send buttons is available to customers.** Restrict to engineering roles, since wrong values could damage hardware.

---

## 4. P1: Home schema

| ID | Issue | Location |
|---|---|---|
| WEB-26 | Tiles open placeholder "Generated detail page for Front/Right/Back/Left" | `assets/js/app_home_schema.js:3553` |
| WEB-27 | `open-edit-profile` and `save-profile-preferences` are emitted, but the app does not support them | `app_home_schema.js:6131,6153,6171` |
| WEB-28 | IoT header date stuck on 2026-11-06; Home date was stale until later | Schema or cached value |
| WEB-29 | "Network 12 nodes", "Team 24 active" look static | Home schema |
| WEB-30 | Blank tiles and duplicates (hotspot, Network, Setup) | Home schema |
| WEB-31 | Admin tiles visible ("AppID Generation", "Keys", "Verify Key"): confirm per role | Home schema |
| WEB-32 | Privacy text omits location; mentions a missing language option | Profile schema |
| WEB-33 | Name field is populated with the phone number | Profile schema or API |

---

## 5. P1: Database (`factor_db2.sql`)
- `device_scan` is queried by `v8/api_scan.php:37` but absent from the dump.
- Only 3 foreign key matches across 149 tables.
- One table per model with awkward names (`efcker v1.0`, `bajaj-001fmn01`).
- `mydevices`: MyISAM, latin1, duplicates, dates as text.
- Mixed `latin1`, `utf8mb3`, `utf8mb4`.
- Email limited to 25 characters in several tables.
- Leftover tables: `demo`, `mytable`, `product_api_old`, `sticker_fields_backup`.

Live database not compared.

## 6. P1: Storage
- Filenames use `uniqid()`, which can be guessed.
- Large schema parts need a writable folder and enough quota.
- NAS plans activate with no payment gateway (`NasStoragePurchase.php:19`); flow not traced.

## 7. P2: Project clean-up
| Issue | Status |
|---|---|
| Routes point to missing `OrderController`, `AttendAttendanceController`, `WorkHiveAuditController` | Check on server |
| QR login route points to a missing method | Check on server |
| About 10 old `Items` controllers beside live code | Confirmed |
| Duplicate routes; `attend/*` maps to two controllers | Confirmed |
| `routes/Api.php` is Laravel code | Confirmed |
| Docs out of date; no git | Confirmed |

---

## 8. IoT production test plan
Use a dedicated test device for each model.

| Test | Pass condition |
|---|---|
| Every control on every model | Device acts and reports the new state |
| Direction after WEB-10 | Correct physical direction both ways |
| Offline device command | Clear failure, no false success |
| Device loses power mid-command | Shows failed, state corrected |
| Two phones control one device | Both show the same state within 5 s |
| 50 commands in a row | No loss, no wrong state |
| Wrong user requests a device | Rejected by the API |
| Model with 2 or more devices | Commands reach only the chosen device |
| First open on 4G | Controls appear in under 10 s |

---

## 9. Release checks
- [ ] WEB-1 to WEB-6 closed and tokens rotated
- [ ] `get_grid` healthy; one token format
- [ ] Schema validator blocks bad options and runs on all models
- [ ] Device page under 500 KB compressed
- [ ] Every Home tile has a real destination
- [ ] Offline commands report failure
- [ ] Motor settings hidden from customers
- [ ] Section 8 passes on real hardware
