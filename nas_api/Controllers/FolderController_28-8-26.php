<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

final class FolderController
{
    /** GET /v1/folders?parent_id= */
    public function index(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $parentId = $this->nullablePositiveInt($_GET['parent_id'] ?? null, 'parent_id');

            $result = SharedNasFactory::folderService()->getFolders(
                (int) $context['nas_user_id'],
                $parentId,
                null
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Folders retrieved',
                'folders' => is_array($result['folders'] ?? null)
                    ? $result['folders']
                    : [],
            ]);
        } catch (ApiException $e) {
            JsonResponse::send([
                'success' => false,
                'error' => $e->errorCode(),
                'message' => $e->getMessage(),
            ], $e->statusCode());
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }



    /** POST /v1/folders */
    public function store(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];

            $raw = file_get_contents('php://input');
            $input = json_decode($raw === false ? '' : $raw, true);
            if (!is_array($input)) {
                throw new ApiException('Invalid JSON data.', 400, 'invalid_json');
            }

            $name = trim((string) ($input['name'] ?? ''));
            if ($name === '') {
                throw new ApiException('Folder name required.', 400, 'folder_name_required');
            }

            $parentId = $this->nullablePositiveInt($input['parent_id'] ?? null, 'parent_id');
            $cluster = isset($input['cluster']) ? trim((string) $input['cluster']) : null;
            if ($cluster === '') {
                $cluster = null;
            }

            // Do not implement naming/duplicate/storage rules here. The installed
            // App\\Services\\NAS\\FolderService remains the source of truth.
            $result = SharedNasFactory::folderService()->createFolder(
                $userId,
                $name,
                $parentId,
                $cluster
            );

            JsonResponse::send([
                'success' => true,
                'message' => (string) ($result['message'] ?? 'Folder created successfully'),
                'folder' => $result,
            ], 201);
        } catch (ApiException $e) {
            JsonResponse::send([
                'success' => false,
                'error' => $e->errorCode(),
                'message' => $e->getMessage(),
            ], $e->statusCode());
        } catch (\InvalidArgumentException $e) {
            JsonResponse::send([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** GET /v1/folders/{id}/info */
    public function info(int $folderId): void
    {
        try {
            if ($folderId <= 0) {
                throw new ApiException('Invalid folder ID.', 400, 'invalid_folder_id');
            }

            $context = AuthenticatedNasContext::resolve();
            $info = SharedNasFactory::folderService()->getFolderInfo(
                (int) $context['nas_user_id'],
                $folderId
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Folder info retrieved',
                'folder' => $info,
            ]);
        } catch (ApiException $e) {
            JsonResponse::send([
                'success' => false,
                'error' => $e->errorCode(),
                'message' => $e->getMessage(),
            ], $e->statusCode());
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /**
     * PUT /v1/folders/{id}
     *
     * Supported Phase 6 actions:
     *   {"action":"rename","new_name":"New Folder"}
     *   {"action":"star"}
     */
    public function update(int $folderId): void
    {
        try {
            $this->assertPositiveId($folderId);
            $input = $this->requiredJsonObject();
            $action = strtolower(trim((string) ($input['action'] ?? 'rename')));

            if (!in_array($action, ['rename', 'star'], true)) {
                throw new ApiException(
                    'Unsupported folder action for this endpoint.',
                    400,
                    'unsupported_folder_action'
                );
            }

            if ($action === 'rename' && trim((string) ($input['new_name'] ?? '')) === '') {
                throw new ApiException('New name required.', 400, 'new_name_required');
            }

            $context = AuthenticatedNasContext::resolve();
            $result = SharedNasFactory::folderService()->updateFolder(
                (int) $context['nas_user_id'],
                $folderId,
                $action,
                $input
            );

            $this->sendServiceResult($result, 'Folder updated successfully');
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** PUT /v1/folders/{id}/restore */
    public function restore(int $folderId): void
    {
        try {
            $this->assertPositiveId($folderId);
            $context = AuthenticatedNasContext::resolve();

            $result = SharedNasFactory::folderService()->updateFolder(
                (int) $context['nas_user_id'],
                $folderId,
                'restore',
                []
            );

            $this->sendServiceResult($result, 'Folder restored successfully');
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** DELETE /v1/folders/{id}; optional permanent=true query/body */
    public function destroy(int $folderId): void
    {
        try {
            $this->assertPositiveId($folderId);
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];
            $input = $this->optionalJsonObject();
            $permanent = $this->booleanValue(
                $input['permanent'] ?? ($_GET['permanent'] ?? false)
            );

            $folderService = SharedNasFactory::folderService();
            $result = $permanent
                ? $folderService->deleteFolderPermanently($userId, $folderId)
                : $folderService->deleteFolder($userId, $folderId);

            $this->sendServiceResult($result, 'Folder deleted successfully');
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    private function assertPositiveId(int $folderId): void
    {
        if ($folderId <= 0) {
            throw new ApiException('Invalid folder ID.', 400, 'invalid_folder_id');
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

    private function apiError(ApiException $e): void
    {
        JsonResponse::send([
            'success' => false,
            'error' => $e->errorCode(),
            'message' => $e->getMessage(),
        ], $e->statusCode());
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

    private function serviceError(\Throwable $e): void
    {
        $status = (int) $e->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        error_log('[nas_api] FolderController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
