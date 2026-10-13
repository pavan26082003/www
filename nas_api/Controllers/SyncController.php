<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

/**
 * Phase 8 native/client synchronization transport.
 *
 * IMPORTANT:
 * - Normal sync uses the existing App\Services\NAS\ClientSyncService.
 * - nas_api never creates a local PC sync directory on the web server.
 * - Flutter/native code will own device-local filesystem access in Phase 9.
 * - Legacy SyncService is used only by the explicit repair endpoint.
 */
final class SyncController
{
    /** GET /v1/sync/status */
    public function status(): void
    {
        $this->run(function (int $userId): void {
            $status = SharedNasFactory::clientSyncService()->getStatus($userId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Sync status retrieved',
                'status' => $status,
            ]);
        });
    }

    /** POST /v1/sync/init */
    public function init(): void
    {
        $this->run(function (int $userId): void {
            $result = SharedNasFactory::clientSyncService()->initialize($userId);
            $this->sendMergedResult($result, 'Sync initialized successfully');
        });
    }

    /** POST /v1/sync/start */
    public function start(): void
    {
        $this->run(function (int $userId): void {
            $result = SharedNasFactory::clientSyncService()->begin($userId);
            $this->sendMergedResult($result, 'Sync started');
        });
    }

    /** GET /v1/sync/manifest */
    public function manifest(): void
    {
        $this->run(function (int $userId): void {
            $manifest = SharedNasFactory::clientSyncService()->getManifest($userId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Sync manifest retrieved',
                'manifest' => $manifest,
            ]);
        });
    }

    /** POST /v1/sync/folders/ensure */
    public function ensureFolders(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject(false);
            $paths = $input['paths'] ?? null;
            if (!is_array($paths)) {
                throw new ApiException('paths array is required.', 400, 'paths_required');
            }

            $result = SharedNasFactory::clientSyncService()->ensureFolders($userId, $paths);
            $this->sendMergedResult($result, 'Sync folders ensured');
        });
    }

    /** POST /v1/sync/upload - multipart: file + relative_path */
    public function upload(): void
    {
        $this->run(function (int $userId): void {
            $relativePath = trim((string) ($_POST['relative_path'] ?? ''));
            if ($relativePath === '') {
                throw new ApiException(
                    'relative_path is required.',
                    400,
                    'relative_path_required'
                );
            }

            $upload = $_FILES['file'] ?? null;
            if (!is_array($upload)) {
                throw new ApiException('file is required.', 400, 'file_required');
            }

            $result = SharedNasFactory::clientSyncService()->uploadFile(
                $userId,
                $relativePath,
                $upload
            );

            $this->sendMergedResult($result, 'Sync file uploaded');
        });
    }

    /** POST /v1/sync/complete */
    public function complete(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject(true);
            $result = SharedNasFactory::clientSyncService()->complete($userId, $input);
            $this->sendMergedResult($result, 'Sync completed');
        });
    }

    /**
     * POST /v1/sync/download
     * Compatibility endpoint: returns cloud manifest only. Device-local writes
     * belong to Flutter/native code, never to the PHP server.
     */
    public function download(): void
    {
        $this->run(function (int $userId): void {
            $manifest = SharedNasFactory::clientSyncService()->getManifest($userId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Sync download manifest retrieved',
                'client_mode' => 'native',
                'manifest' => $manifest,
            ]);
        });
    }

    /** POST /v1/sync/uploaded JSON: {"file_id":123} */
    public function uploaded(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject(false);
            $fileId = $this->positiveInt($input['file_id'] ?? null, 'file_id');
            $result = SharedNasFactory::clientSyncService()->markCloudFilePending($userId, $fileId);
            $this->sendMergedResult($result, 'File marked pending for client pull');
        });
    }

    /** GET /v1/sync/history */
    public function history(): void
    {
        $this->run(function (int $userId): void {
            // Existing SyncService::getHistory() is a direct SyncRepository delegate.
            // Calling the repository here avoids instantiating legacy SyncService,
            // whose constructor is tied to old server-local sync behavior.
            $history = SharedNasFactory::syncRepository()->getHistory($userId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Sync history retrieved',
                'history' => $history,
            ]);
        });
    }

    /** POST /v1/sync/cancel */
    public function cancel(): void
    {
        $this->run(function (int $userId): void {
            $result = SharedNasFactory::clientSyncService()->cancel($userId);
            $this->sendMergedResult($result, 'Sync cancelled');
        });
    }

    /** GET /v1/sync/folders */
    public function folders(): void
    {
        $this->run(function (int $userId): void {
            $folders = SharedNasFactory::clientSyncService()->getFolders($userId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Sync folders retrieved',
                'folders' => $folders,
            ]);
        });
    }

    /** GET /v1/sync/unsynced */
    public function unsynced(): void
    {
        $this->run(function (int $userId): void {
            // Existing SyncService method directly delegates to SyncRepository.
            $files = SharedNasFactory::syncRepository()->getUnsyncedFiles($userId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Unsynced files retrieved',
                'files' => $files,
            ]);
        });
    }

    /** GET /v1/sync/logs?limit=50 */
    public function logs(): void
    {
        $this->run(function (int $userId): void {
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
            $limit = max(1, min(200, $limit));

            // Existing SyncService method directly delegates to SyncRepository.
            $logs = SharedNasFactory::syncRepository()->getLogs($userId, $limit);
            JsonResponse::send([
                'success' => true,
                'message' => 'Sync logs retrieved',
                'logs' => $logs,
            ]);
        });
    }

    /** POST /v1/sync/pause */
    public function pause(): void
    {
        $this->run(function (int $userId): void {
            $result = SharedNasFactory::clientSyncService()->pause($userId);
            $this->sendMergedResult($result, 'Sync paused');
        });
    }

    /** POST /v1/sync/resume */
    public function resume(): void
    {
        $this->run(function (int $userId): void {
            $result = SharedNasFactory::clientSyncService()->resume($userId);
            $this->sendMergedResult($result, 'Sync resumed');
        });
    }

    /**
     * POST /v1/sync/repair
     * Legacy maintenance compatibility only. Normal Flutter sync does not call it.
     */
    public function repair(): void
    {
        $this->run(function (int $userId): void {
            $result = SharedNasFactory::legacySyncService()->repairClusters($userId);
            $this->sendMergedResult($result, 'Clusters repaired');
        });
    }

    /** @param callable(int):void $handler */
    private function run(callable $handler): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $handler((int) $context['nas_user_id']);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** @return array<string,mixed> */
    private function jsonObject(bool $allowEmpty): array
    {
        $raw = file_get_contents('php://input');
        $raw = $raw === false ? '' : trim($raw);
        if ($raw === '') {
            if ($allowEmpty) {
                return [];
            }
            throw new ApiException('JSON body is required.', 400, 'json_body_required');
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new ApiException('Invalid JSON data.', 400, 'invalid_json');
        }
        return $decoded;
    }

    private function positiveInt($value, string $name): int
    {
        if (!is_scalar($value) || !ctype_digit((string) $value) || (int) $value <= 0) {
            throw new ApiException('Invalid ' . $name . '.', 400, 'invalid_' . $name);
        }
        return (int) $value;
    }

    /** @param array<string,mixed> $result */
    private function sendMergedResult(array $result, string $message, int $status = 200): void
    {
        $body = $result;
        $body['success'] = array_key_exists('success', $result)
            ? (bool) $result['success']
            : true;
        $body['message'] = isset($result['message']) && trim((string) $result['message']) !== ''
            ? (string) $result['message']
            : $message;

        JsonResponse::send($body, $status);
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

        error_log('[nas_api] SyncController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
