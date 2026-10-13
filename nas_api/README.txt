Efactor NAS Mobile API - Complete Phase 10
=========================================

ARCHITECTURE
------------
- nas_api owns Bearer authentication, routing, request parsing and Flutter/Postman transport.
- Existing app/Services/NAS/* and app/Repositories/NAS/* remain the canonical NAS business/data layer.
- app/Controllers/NAS/Api/V1/* are NOT called by /nas_api/v1/*.
- This package does NOT contain or modify app/*.
- Never send user_id, efactor_user_id or nas_user_id as trusted caller identity. The verified Bearer token supplies the current Efactor identity and NasUserBridge resolves the trusted NAS user.

COMPLETED PHASES RETAINED
-------------------------
Phase 1/2: Bearer auth, Efactor -> NAS mapping, dashboard/recent/stats.
Phase 3: My Drive/folder reads, search, file/folder info.
Phase 4: secure file download/preview/content.
Phase 5: create folder, file/multi-file/folder-tree upload.
Phase 6: rename, star, trash, restore and permanent delete for files/folders.
Phase 7: user search, sharing, share permission update/revoke and shared view.
Phase 8: storage clusters and client/native sync API contract.

PHASE 8 - STORAGE CLUSTERS
--------------------------
GET /v1/clusters

Flow:
  Bearer -> trusted nas_user_id
  -> SharedNasFactory::clusterService()
  -> existing App\Services\NAS\ClusterService
  -> existing ClusterRepository

PHASE 8 - NATIVE/CLIENT SYNC
----------------------------
GET  /v1/sync/status
POST /v1/sync/init
POST /v1/sync/start
GET  /v1/sync/manifest
POST /v1/sync/folders/ensure
POST /v1/sync/upload
POST /v1/sync/complete
POST /v1/sync/download
POST /v1/sync/uploaded
GET  /v1/sync/history
POST /v1/sync/cancel
GET  /v1/sync/folders
GET  /v1/sync/unsynced
GET  /v1/sync/logs?limit=50
POST /v1/sync/pause
POST /v1/sync/resume
POST /v1/sync/repair

NORMAL SYNC SERVICE
-------------------
Normal mobile/client synchronization uses the existing:
  App\Services\NAS\ClientSyncService

This is intentional. ClientSyncService exposes cloud manifests and accepts client uploads but does NOT create a user's local-PC/device sync folder on the PHP/web server.

Flutter/device local filesystem work is NOT performed by nas_api. Client teams may use the Phase-8 manifest plus secure file APIs to implement native sync independently.

The old App\Services\NAS\SyncService has legacy server-local sync behavior. Phase 8 does not use it for normal sync. It is instantiated only for the explicit maintenance endpoint /v1/sync/repair.

SYNC REQUESTS
-------------
1) GET /v1/sync/status
   No body.

2) POST /v1/sync/init
   No body required.
   Registers/enables client sync and returns the cloud manifest.

3) POST /v1/sync/start
   No body required.
   Creates or reuses a running sync session.

4) GET /v1/sync/manifest
   No body.
   Returns logical cloud folder/file paths. Manifest download_url values are logical/relative; Flutter should resolve file downloads through the nas_api base URL.

5) POST /v1/sync/folders/ensure
   Content-Type: application/json
   Example:
     {"paths":["Projects/A","Projects/A/Docs"]}

6) POST /v1/sync/upload
   multipart/form-data
   Required fields:
     file          File
     relative_path Text
   Example relative_path:
     Projects/A/test.txt

7) POST /v1/sync/complete
   Content-Type: application/json
   Empty body is accepted, or send a summary such as:
   {
     "uploaded": 1,
     "folders_created": 1,
     "downloaded": 2,
     "skipped": 0,
     "errors": [],
     "synced_files": [
       {"file_id":123,"relative_path":"Documents/example.pdf"}
     ]
   }

8) POST /v1/sync/download
   No body required.
   Compatibility/native endpoint. Returns a cloud manifest only; it never writes a local device folder on the web server.

9) POST /v1/sync/uploaded
   Content-Type: application/json
   Example:
     {"file_id":123}
   Marks an owned cloud file pending for a client pull.

10) GET /v1/sync/history
    Reads existing sync history for the authenticated NAS user.

11) POST /v1/sync/cancel
    Cancels the current client sync session if one exists.

12) GET /v1/sync/folders
    Returns client sync folder registration/configuration.

13) GET /v1/sync/unsynced
    Returns pending/error sync file rows through the existing SyncRepository.

14) GET /v1/sync/logs?limit=50
    limit defaults to 50 and is clamped to 1..200.

15) POST /v1/sync/pause
    Disables client sync / moves active session out of running state.

16) POST /v1/sync/resume
    Re-enables client sync. Initializes registration first when needed.

17) POST /v1/sync/repair
    Maintenance compatibility only. Uses existing legacy SyncService::repairClusters(). It is NOT part of normal Flutter synchronization and should not be called routinely.

IMPORTANT SYNC NOTES
--------------------
- Current caller identity always comes from Bearer -> AuthenticatedNasContext -> trusted nas_user_id.
- ClientSyncService remains authoritative for manifest/folder/upload/session behavior.
- nas_api does not scan arbitrary server folders and does not create a local PC folder.
- /v1/sync/upload uses the existing ClientSyncService::uploadFile behavior exactly; nas_api does not add duplicate/conflict logic.
- Duplicate/conflict handling (SHA-256 conflict UI, Replace, Keep Both, Skip, folder Merge/Keep Both) remains deliberately deferred.
- Do not implement a second copy of sync, storage or conflict logic inside nas_api.

PHASE 8 CONTROLLERS ADDED
-------------------------
nas_api/Controllers/ClusterController.php
nas_api/Controllers/SyncController.php

No app/Controllers/NAS/Api/V1 controller is used by these routes.

PHASE 10 - PREVIEW DISCOVERY + OFFICE CONVERSION
------------------------------------------------
GET /v1/preview/info?file_id={id}
GET /v1/preview/convert?file_id={id}

PREVIEW ARCHITECTURE
--------------------
The authenticated mobile/Postman preview flow is now explicit:

PDF
  -> /v1/files/{id}/preview
  -> original inline PDF bytes
  -> client PDF renderer

DOC / DOCX / PPT / PPTX
  -> /v1/preview/convert?file_id={id}
  -> existing App\Services\NAS\OfficeConversionService::convertToPdf()
  -> LibreOffice headless conversion + existing server cache
  -> inline application/pdf
  -> client PDF renderer

XLS / XLSX / CSV
  -> /v1/files/{id}/preview
  -> original permission-checked spreadsheet bytes
  -> client_renderer = xlsx
  -> XLSX/SheetJS-compatible renderer parses workbook/sheets on the client

Images / video / audio
  -> /v1/files/{id}/preview
  -> original permission-checked inline bytes

Text / code
  -> /v1/files/{id}/content
  -> permission gate first, then plain text

Archives / unsupported binary
  -> no inline renderer declared by preview/info
  -> secure download endpoint may be offered when can_download=true

GET /v1/preview/info
--------------------
Required query:
  file_id=<positive integer>

Example spreadsheet response:
{
  "success": true,
  "message": "Preview info retrieved",
  "file_id": 171,
  "name": "example.xlsx",
  "extension": "xlsx",
  "mime_type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  "can_preview": true,
  "preview_type": "spreadsheet",
  "delivery_type": "binary",
  "client_renderer": "xlsx",
  "conversion_required": false,
  "converter_available": null,
  "has_cached_pdf": false,
  "preview_endpoint": "/v1/files/171/preview",
  "download_endpoint": "/v1/files/171/download",
  "can_download": true
}

Important:
- preview/info never returns a server filesystem pdf_path.
- can_download is derived from existing PermissionService.
- caller identity comes only from the verified Bearer token.

GET /v1/preview/convert
-----------------------
Required query:
  file_id=<positive integer>

Optional:
  force=true
  Forces the existing OfficeConversionService to refresh its converted PDF cache.

Supported behavior:
- PDF: pass-through inline PDF stream.
- DOC/DOCX/PPT/PPTX: existing OfficeConversionService -> PDF stream.
- XLS/XLSX/CSV: HTTP 400 spreadsheet_conversion_not_required; use the secure raw preview endpoint and an XLSX-compatible renderer.
- Other types: HTTP 400 conversion_not_supported.

On successful DOC/DOCX/PPT/PPTX conversion:
  Content-Type: application/pdf
  Content-Disposition: inline

SECURITY
--------
Every Phase-9 request uses:
  Bearer -> AuthenticatedNasContext -> trusted nas_user_id

No client user_id, efactor_user_id or nas_user_id is accepted as identity.
FileService::getFileInfo(userId,fileId) and existing PermissionService checks remain authoritative.
The existing app/Controllers/NAS/Api/V1/PreviewController.php is NOT invoked by /nas_api/v1/*.

EXCEL / XLSX NOTE
-----------------
Do NOT add XLS/XLSX to OfficeConversionService::convertToPdf() for this mobile flow.
The current NAS web UI already previews XLS/XLSX/CSV with the XLSX/SheetJS library from original file bytes. Phase 10 preserves that design for API consumers.

PHASE 10 CONTROLLER ADDED
------------------------
nas_api/Controllers/PreviewController.php

No app/* file is included or modified by this package.



PHASE 10 - COMPLETE DRIVE VIEWS + FOLDER ZIP DOWNLOAD
-----------------------------------------------------
GET /v1/files now supports the full canonical view set already implemented by
App\Services\NAS\FileService:
- my-drive (aliases: drive, home)
- shared (alias: shared-with-me)
- starred
- trash
- computers
- projects
- images
- videos
- documents
- archives

Optional query parameters remain:
- folder_id=<positive integer>
- search=<text>

New route:
GET /v1/files/download-folder?folder_id=<id>

The route delegates to FileService::downloadFolderAsZip(), which performs the
canonical ownership/canDownload permission check and StorageService ZIP creation.
The API streams application/zip as an attachment and then removes the temporary
ZIP file returned by the shared service.

No app/* files are included or modified.

SERVER COMPATIBILITY HOTFIX
---------------------------
This Phase-10 package is compatible with PHP 7.4+ in the standalone nas_api layer.
It avoids PHP 8-only str_starts_with()/str_contains(), mixed parameter declarations,
and PHP 8.1 never return declarations. No app/* business logic is included or changed.

SERVER COMPATIBILITY NOTE
-------------------------
This package targets PHP 7.4+ for the standalone nas_api layer.
NAS business logic remains in the existing App\\Services\\NAS and
App\\Repositories\\NAS classes. No duplicate nas_api DashboardService is used.
