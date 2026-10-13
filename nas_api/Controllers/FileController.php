<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use App\Helpers\NAS\FileHelper;
use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

final class FileController
{
    /** GET /v1/files?view=my-drive&folder_id=&search= */
    public function index(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];

            $view = trim((string) ($_GET['view'] ?? 'my-drive'));
            $view = $view === '' ? 'my-drive' : strtolower($view);

            // Phase 10 exposes every drive view already implemented by the
            // canonical App\Services\NAS\FileService. nas_api only validates
            // the public API vocabulary and never reimplements view/query logic.
            $allowedViews = [
                'my-drive', 'drive', 'home',
                'shared', 'shared-with-me',
                'starred', 'trash', 'computers',
                'projects', 'images', 'videos', 'documents', 'archives',
            ];

            if (!in_array($view, $allowedViews, true)) {
                throw new ApiException(
                    'Unsupported file view.',
                    400,
                    'unsupported_view'
                );
            }

            $folderId = $this->nullablePositiveInt($_GET['folder_id'] ?? null, 'folder_id');
            $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
            if ($search === '') {
                $search = null;
            }

            if (in_array($view, ['drive', 'home'], true)) {
                $serviceView = 'my-drive';
            } elseif ($view === 'shared-with-me') {
                $serviceView = 'shared';
            } else {
                $serviceView = $view;
            }

            $items = SharedNasFactory::fileService()->getFilesByView(
                $userId,
                $serviceView,
                $folderId,
                $search
            );

            $storage = SharedNasFactory::userRepository()->getStorageInfo($userId);
            $used = (int) ($storage['storage_used'] ?? 0);
            $limit = (int) ($storage['storage_limit'] ?? 0);

            JsonResponse::send([
                'success' => true,
                'cluster_id' => null,
                'cluster_slug' => $serviceView,
                'items' => $items,
                'storage' => [
                    'used' => $used,
                    'limit' => $limit,
                    'used_formatted' => FileHelper::formatSize($used),
                    'limit_formatted' => FileHelper::formatSize($limit),
                    'percentage' => $limit > 0 ? ($used / $limit) * 100 : 0,
                ],
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }



    /**
     * POST /v1/files
     * POST /v1/files/upload
     *
     * multipart/form-data fields:
     *   files[] / file
     *   cluster (optional)
     *   folder_id (optional)
     *   root_folder (optional; enables folder-tree upload)
     *   paths[] (optional; relative folder path per uploaded file)
     *   conflict_action (optional; file: replace|keep_both|cancel)
     *   folder_conflict_action (optional; folder upload: merge|keep_both|cancel)
     *   conflict_actions (optional; JSON/object map of file-name => action)
     */
    public function store(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];

            if (empty($_FILES)) {
                throw new ApiException('No files uploaded.', 400, 'no_files_uploaded');
            }

            $cluster = isset($_POST['cluster']) ? trim((string) $_POST['cluster']) : null;
            if ($cluster === '') {
                $cluster = null;
            }

            $folderId = $this->nullablePositiveInt($_POST['folder_id'] ?? null, 'folder_id');
            $rootFolder = isset($_POST['root_folder']) ? trim((string) $_POST['root_folder']) : null;
            if ($rootFolder === '') {
                $rootFolder = null;
            }

            $paths = $this->normalizePathsField($_POST['paths'] ?? []);

            // Conflict actions are transport-only values. The shared NAS
            // FileService remains authoritative for duplicate detection and
            // the actual replace / keep-both / cancel / merge behavior.
            $singleConflictAction = isset($_POST['conflict_action'])
                ? strtolower(trim((string) $_POST['conflict_action']))
                : null;

            $folderConflictAction = isset($_POST['folder_conflict_action'])
                ? strtolower(trim((string) $_POST['folder_conflict_action']))
                : null;

            $conflictActions = $this->normalizeConflictActionsField(
                $_POST['conflict_actions'] ?? []
            );

            $files = $this->processUploadedFiles($_FILES, $paths);
            if ($files === []) {
                throw new ApiException('No valid files to upload.', 400, 'no_valid_files');
            }

            // Extra API-layer owner preflight. The shared FileService also checks
            // this itself, but this gives mobile clients a clean 403 instead of
            // turning an ownership RuntimeException into an internal error.
            if ($folderId !== null) {
                $folderInfo = SharedNasFactory::folderService()->getFolderInfo($userId, $folderId);
                if (empty($folderInfo['is_owner'])) {
                    throw new ApiException(
                        'Uploading directly into another user\'s folder is not allowed.',
                        403,
                        'upload_target_not_owned'
                    );
                }
            }

            $isFolderUpload = $rootFolder !== null;

            // For folder-tree uploads, accept conflict_action as a mobile-friendly
            // alias when folder_conflict_action was not explicitly supplied.
            if (
                $isFolderUpload
                && ($folderConflictAction === null || $folderConflictAction === '')
                && $singleConflictAction !== null
                && $singleConflictAction !== ''
            ) {
                $folderConflictAction = $singleConflictAction;
                $singleConflictAction = null;
            }

            $result = SharedNasFactory::fileService()->uploadFiles(
                $userId,
                $files,
                $cluster,
                $folderId,
                $isFolderUpload,
                $paths,
                $rootFolder,
                $conflictActions,
                $folderConflictAction,
                $singleConflictAction
            );

            if (!empty($result['requires_resolution'])) {
                JsonResponse::send([
                    'success' => false,
                    'message' => 'An item with the same name already exists in this location',
                    'data' => [
                        'code' => 'NAME_CONFLICT',
                        'conflict_type' => $result['conflict_type'] ?? null,
                        'conflicts' => is_array($result['conflicts'] ?? null)
                            ? $result['conflicts']
                            : [],
                    ],
                ], 409);
            }

            if (!empty($result['canceled'])) {
                JsonResponse::send([
                    'success' => true,
                    'message' => 'Upload canceled',
                    'canceled' => true,
                    'uploaded' => [],
                    'uploaded_files' => [],
                    'folders_created' => [],
                ]);
            }

            $uploaded = is_array($result['uploaded'] ?? null) ? $result['uploaded'] : [];
            $fileIds = is_array($result['file_ids'] ?? null) ? $result['file_ids'] : [];
            $foldersCreated = is_array($result['folders_created'] ?? null) ? $result['folders_created'] : [];
            $errors = is_array($result['errors'] ?? null) ? $result['errors'] : [];

            if ($uploaded === []) {
                JsonResponse::send([
                    'success' => false,
                    'message' => 'No files were uploaded successfully.',
                    'errors' => $errors,
                ], 400);
            }

            JsonResponse::send([
                'success' => true,
                'message' => count($uploaded) . ' file(s) uploaded successfully.',
                'uploaded' => $uploaded,
                'uploaded_files' => $fileIds,
                'folders_created' => $foldersCreated,
                'root_folder_id' => $result['root_folder_id'] ?? $folderId,
                'cluster_id' => $result['cluster_id'] ?? null,
                'cluster_slug' => $result['cluster_slug'] ?? $cluster,
                'total_files' => isset($result['total_files'])
                    ? (int) $result['total_files']
                    : count($uploaded),
                'total_folders' => isset($result['total_folders'])
                    ? (int) $result['total_folders']
                    : count($foldersCreated),
                'replaced' => is_array($result['replaced'] ?? null)
                    ? $result['replaced']
                    : [],
                'kept_both' => is_array($result['kept_both'] ?? null)
                    ? $result['kept_both']
                    : [],
                'errors' => $errors,
            ], 201);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\InvalidArgumentException $e) {
            JsonResponse::send([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** @return string[] */
    private function normalizePathsField($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_map(static fn ($v): string => trim((string) $v), $value));
        }

        if (!is_scalar($value)) {
            throw new ApiException('Invalid paths field.', 400, 'invalid_paths');
        }

        $text = trim((string) $value);
        if ($text === '') {
            return [];
        }

        // Flutter clients may send paths as a JSON-encoded array in one form field.
        if ($text !== '' && $text[0] === '[') {
            $decoded = json_decode($text, true);
            if (is_array($decoded)) {
                return array_values(array_map(static fn ($v): string => trim((string) $v), $decoded));
            }
        }

        return [$text];
    }

    /** @return array<string,string> */
    private function normalizeConflictActionsField($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return [];
            }

            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                throw new ApiException(
                    'Invalid conflict_actions field.',
                    400,
                    'invalid_conflict_actions'
                );
            }
            $value = $decoded;
        }

        if (!is_array($value)) {
            throw new ApiException(
                'Invalid conflict_actions field.',
                400,
                'invalid_conflict_actions'
            );
        }

        $normalized = [];
        foreach ($value as $name => $action) {
            $name = trim((string) $name);
            $action = strtolower(trim((string) $action));

            if ($name === '') {
                continue;
            }

            if (!in_array($action, ['replace', 'keep_both', 'cancel'], true)) {
                throw new ApiException(
                    'Invalid file conflict action.',
                    400,
                    'invalid_conflict_action'
                );
            }

            $normalized[$name] = $action;
        }

        return $normalized;
    }

    /**
     * Convert PHP's $_FILES shape to the exact upload array expected by the
     * existing App\\Services\\NAS\\FileService::uploadFiles().
     *
     * @param array<string,mixed> $input
     * @param string[] $paths
     * @return array<int,array{name:string,type:string,tmp_name:string,error:int,size:int,path:string}>
     */
    private function processUploadedFiles(array $input, array $paths): array
    {
        $bucket = $input['files'] ?? $input['file'] ?? null;
        if (!is_array($bucket)) {
            return [];
        }

        $names = $bucket['name'] ?? null;
        $types = $bucket['type'] ?? null;
        $tmps = $bucket['tmp_name'] ?? null;
        $errors = $bucket['error'] ?? null;
        $sizes = $bucket['size'] ?? null;

        $processed = [];

        if (is_array($names)) {
            foreach ($names as $i => $name) {
                $error = (int) ($errors[$i] ?? UPLOAD_ERR_NO_FILE);
                if ($error !== UPLOAD_ERR_OK) {
                    continue;
                }

                $processed[] = [
                    'name' => (string) $name,
                    'type' => (string) ($types[$i] ?? 'application/octet-stream'),
                    'tmp_name' => (string) ($tmps[$i] ?? ''),
                    'error' => $error,
                    'size' => (int) ($sizes[$i] ?? 0),
                    'path' => (string) ($paths[$i] ?? ''),
                ];
            }

            return $processed;
        }

        $error = (int) ($errors ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            return [];
        }

        return [[
            'name' => (string) $names,
            'type' => (string) ($types ?? 'application/octet-stream'),
            'tmp_name' => (string) ($tmps ?? ''),
            'error' => $error,
            'size' => (int) ($sizes ?? 0),
            'path' => (string) ($paths[0] ?? ''),
        ]];
    }

    /** GET /v1/files/download-folder?folder_id={id} */
    public function downloadFolder(): void
    {
        try {
            $folderId = $this->nullablePositiveInt($_GET['folder_id'] ?? null, 'folder_id');
            if ($folderId === null) {
                throw new ApiException('Folder ID required.', 400, 'folder_id_required');
            }

            $context = AuthenticatedNasContext::resolve();
            $result = SharedNasFactory::fileService()->downloadFolderAsZip(
                (int) $context['nas_user_id'],
                $folderId
            );

            // FileService performs the canonical owner/canDownload permission
            // check and returns a temporary ZIP produced by StorageService.
            $this->streamFile($result, false);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** GET /v1/files/{id}/info */
    public function info(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);

            $context = AuthenticatedNasContext::resolve();
            $info = SharedNasFactory::fileService()->getFileInfo(
                (int) $context['nas_user_id'],
                $fileId
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'File info retrieved',
                'file' => $info,
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** GET /v1/files/{id}/download */
    public function download(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);

            $context = AuthenticatedNasContext::resolve();
            $result = SharedNasFactory::fileService()->downloadFile(
                (int) $context['nas_user_id'],
                $fileId
            );

            $this->streamFile($result, false);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** GET /v1/files/{id}/preview */
    public function preview(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);

            $context = AuthenticatedNasContext::resolve();
            $result = SharedNasFactory::fileService()->previewFile(
                (int) $context['nas_user_id'],
                $fileId
            );

            $this->streamFile($result, true);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /**
     * GET /v1/files/{id}/content
     *
     * The existing App\Services\NAS\FileService::getContentPreview() has no
     * userId argument, so nas_api MUST perform an authenticated permission gate
     * before calling it. getFileInfo(userId, fileId) uses PermissionService::canView().
     */
    public function content(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);

            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];
            $fileService = SharedNasFactory::fileService();

            // Security gate. Do not remove or reorder this call.
            $fileService->getFileInfo($userId, $fileId);

            // Only after authorization succeeds may the existing content reader run.
            $result = $fileService->getContentPreview($fileId);
            $this->sendTextContent($result);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /**
     * PUT /v1/files/{id}
     *
     * Supported Phase 6 actions:
     *   {"action":"rename","new_name":"new-name.txt"}
     *   {"action":"star"}
     *
     * The shared FileService remains authoritative for ownership/share rules.
     */
    public function update(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);
            $input = $this->requiredJsonObject();

            $action = strtolower(trim((string) ($input['action'] ?? 'rename')));
            if (!in_array($action, ['rename', 'star'], true)) {
                throw new ApiException(
                    'Unsupported file action for this endpoint.',
                    400,
                    'unsupported_file_action'
                );
            }

            if ($action === 'rename' && trim((string) ($input['new_name'] ?? '')) === '') {
                throw new ApiException('New name required.', 400, 'new_name_required');
            }

            $context = AuthenticatedNasContext::resolve();
            $result = SharedNasFactory::fileService()->updateFile(
                (int) $context['nas_user_id'],
                $fileId,
                $action,
                $input
            );

            $this->sendServiceResult($result, 'File updated successfully');
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** PUT /v1/files/{id}/restore */
    public function restore(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);
            $context = AuthenticatedNasContext::resolve();

            // updateFile() loads with trash and delegates restore to the existing
            // FileService::restoreFile(), preserving owner/parent-folder checks.
            $result = SharedNasFactory::fileService()->updateFile(
                (int) $context['nas_user_id'],
                $fileId,
                'restore',
                []
            );

            $this->sendServiceResult($result, 'File restored successfully');
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /**
     * DELETE /v1/files/{id}
     * Optional query/body: permanent=true
     *
     * Existing FileService behavior is preserved. In particular, deleting an
     * owner file that is already in trash is treated as a permanent delete by
     * the shared service even when permanent is omitted.
     */
    public function destroy(int $fileId): void
    {
        try {
            $this->assertPositiveId($fileId);
            $context = AuthenticatedNasContext::resolve();
            $input = $this->optionalJsonObject();

            $permanent = $this->booleanValue(
                $input['permanent'] ?? ($_GET['permanent'] ?? false)
            );

            $result = SharedNasFactory::fileService()->deleteFile(
                (int) $context['nas_user_id'],
                $fileId,
                $permanent
            );

            $this->sendServiceResult($result, 'File deleted successfully');
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** @return array<string,mixed> */
    private function requiredJsonObject(): array
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw === false ? '' : $raw, true);
        if (!is_array($decoded)) {
            throw new ApiException('Invalid JSON data.', 400, 'invalid_json');
        }

        return $decoded;
    }

    /** @return array<string,mixed> */
    private function optionalJsonObject(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new ApiException('Invalid JSON data.', 400, 'invalid_json');
        }

        return $decoded;
    }

    private function booleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

    /** @param array<string,mixed> $result */
    private function sendServiceResult(array $result, string $defaultMessage): void
    {
        $body = $result;
        $body['success'] = true;
        $body['message'] = (string) ($result['message'] ?? $defaultMessage);
        JsonResponse::send($body);
    }

    private function assertPositiveId(int $fileId): void
    {
        if ($fileId <= 0) {
            throw new ApiException('Invalid file ID.', 400, 'invalid_file_id');
        }
    }

    private function nullablePositiveInt($value, string $name): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (!is_scalar($value) || !ctype_digit((string) $value) || (int) $value <= 0) {
            throw new ApiException('Invalid ' . $name . '.', 400, 'invalid_' . $name);
        }

        return (int) $value;
    }

    /** @param array{path:mixed,name?:mixed,size?:mixed,mime_type?:mixed,is_temp?:mixed} $result */
    private function streamFile(array $result, bool $inline): void
    {
        $path = isset($result['path']) ? (string) $result['path'] : '';
        if ($path === '' || !is_file($path) || !is_readable($path)) {
            throw new \Exception('File not found on server', 404);
        }

        $mimeType = trim((string) ($result['mime_type'] ?? ''));
        if ($mimeType === '' || preg_match('/[\r\n]/', $mimeType)) {
            $mimeType = 'application/octet-stream';
        }

        $name = $this->safeFilename((string) ($result['name'] ?? ($inline ? 'preview' : 'download')));
        $disposition = $inline ? 'inline' : 'attachment';
        $size = filesize($path);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (headers_sent()) {
            throw new \RuntimeException('Unable to stream file because response headers were already sent.', 500);
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: ' . $this->contentDisposition($disposition, $name));
        if ($size !== false) {
            header('Content-Length: ' . (string) $size);
        }
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');

        readfile($path);

        // Folder ZIP exports are temporary files created by StorageService.
        // Clean them only after the response bytes have been streamed.
        if (!empty($result['is_temp']) && is_file($path)) {
            @unlink($path);
        }

        exit;
    }

    /** @param array{content?:mixed,name?:mixed} $result */
    private function sendTextContent(array $result): void
    {
        $content = (string) ($result['content'] ?? '');
        $name = $this->safeFilename((string) ($result['name'] ?? 'content.txt'));

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (headers_sent()) {
            throw new \RuntimeException('Unable to return content because response headers were already sent.', 500);
        }

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: ' . $this->contentDisposition('inline', $name));
        header('Content-Length: ' . (string) strlen($content));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');

        echo $content;
        exit;
    }

    private function safeFilename(string $name): string
    {
        $name = str_replace(["\r", "\n", "\0"], '', $name);
        $name = basename(str_replace('\\', '/', $name));
        $name = trim($name);

        return $name !== '' ? $name : 'file';
    }

    private function contentDisposition(string $disposition, string $name): string
    {
        $ascii = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name) ?: 'file';
        $ascii = str_replace(['"', '\\'], '_', $ascii);

        return $disposition
            . '; filename="' . $ascii . '"'
            . "; filename*=UTF-8''" . rawurlencode($name);
    }

    private function apiError(ApiException $e): void
    {
        JsonResponse::send([
            'success' => false,
            'error' => $e->errorCode(),
            'message' => $e->getMessage(),
        ], $e->statusCode());
    }

    private function serviceError(\Throwable $e): void
    {
        $status = (int) $e->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        error_log('[nas_api] FileController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
