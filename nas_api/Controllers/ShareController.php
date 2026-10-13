<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

final class ShareController
{
    /** GET /v1/share[?item_type=file|folder&item_id=123] */
    public function index(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];

            $itemType = isset($_GET['item_type']) ? strtolower(trim((string) $_GET['item_type'])) : null;
            $itemId = $this->nullablePositiveInt($_GET['item_id'] ?? null, 'item_id');

            if (($itemType === null || $itemType === '') && $itemId === null) {
                $shares = SharedNasFactory::shareService()->getSharesForUser($userId);
                JsonResponse::send([
                    'success' => true,
                    'message' => 'Shares retrieved',
                    'shares' => $shares,
                ]);
            }

            if ($itemType === null || $itemType === '' || $itemId === null) {
                throw new ApiException(
                    'item_type and item_id must be supplied together.',
                    400,
                    'incomplete_share_filter'
                );
            }

            $this->assertItemType($itemType);
            $permissionService = SharedNasFactory::permissionService();
            $shareService = SharedNasFactory::shareService();

            if (!$permissionService->isOwner($userId, $itemType, $itemId)) {
                $share = $shareService->getShareForUser($userId, $itemType, $itemId);
                $permission = is_array($share) ? (string) ($share['permissions'] ?? '') : '';
                if ($share === null || !in_array($permission, ['share', 'full'], true)) {
                    throw new ApiException('Access denied', 403, 'access_denied');
                }
            }

            $shares = $shareService->getSharesByItem($itemType, $itemId);
            JsonResponse::send([
                'success' => true,
                'message' => 'Shares retrieved',
                'shares' => $shares,
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** POST /v1/share */
    public function store(): void
    {
        try {
            $input = $this->requiredJsonObject();
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];

            $itemType = strtolower(trim((string) ($input['item_type'] ?? '')));
            $itemId = $this->requiredPositiveInt($input['item_id'] ?? null, 'item_id');
            $sharedWithUserId = $this->requiredPositiveInt(
                $input['share_with_user_id'] ?? null,
                'share_with_user_id'
            );
            $permission = strtolower(trim((string) ($input['permission'] ?? 'view')));
            $expiresAt = isset($input['expires_at']) && trim((string) $input['expires_at']) !== ''
                ? trim((string) $input['expires_at'])
                : null;

            $this->assertItemType($itemType);
            $this->assertPermission($permission);

            if ($sharedWithUserId === $userId) {
                throw new ApiException('You cannot share with yourself', 400, 'cannot_share_with_self');
            }

            if (!SharedNasFactory::permissionService()->canShare($userId, $itemType, $itemId)) {
                throw new ApiException(
                    'You do not have permission to share this item',
                    403,
                    'share_permission_denied'
                );
            }

            $result = SharedNasFactory::shareService()->createShare(
                $userId,
                $itemType,
                $itemId,
                $sharedWithUserId,
                $permission,
                $expiresAt
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Item shared successfully',
                'share' => $result,
            ], 201);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** PUT /v1/share/{id} */
    public function update(int $shareId): void
    {
        try {
            $this->assertPositiveShareId($shareId);
            $input = $this->requiredJsonObject();
            $permission = strtolower(trim((string) ($input['permission'] ?? '')));
            if ($permission === '') {
                throw new ApiException('Permission required', 400, 'permission_required');
            }
            $this->assertPermission($permission);

            $expiresAt = array_key_exists('expires_at', $input)
                ? (trim((string) ($input['expires_at'] ?? '')) !== ''
                    ? trim((string) $input['expires_at'])
                    : null)
                : null;

            $context = AuthenticatedNasContext::resolve();
            $result = SharedNasFactory::shareService()->updateShare(
                $shareId,
                (int) $context['nas_user_id'],
                $permission,
                $expiresAt
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Share updated',
                'share' => $result,
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** DELETE /v1/share/{id} */
    public function destroy(int $shareId): void
    {
        try {
            $this->assertPositiveShareId($shareId);
            $context = AuthenticatedNasContext::resolve();

            SharedNasFactory::shareService()->revokeShare(
                $shareId,
                (int) $context['nas_user_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Share revoked successfully',
            ]);
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

    private function assertItemType(string $itemType): void
    {
        if (!in_array($itemType, ['file', 'folder'], true)) {
            throw new ApiException(
                'Invalid item_type. Must be "file" or "folder"',
                400,
                'invalid_item_type'
            );
        }
    }

    private function assertPermission(string $permission): void
    {
        if (!in_array($permission, ['view', 'download', 'edit', 'share', 'full'], true)) {
            throw new ApiException(
                'Invalid permission. Must be one of: view, download, edit, share, full',
                400,
                'invalid_permission'
            );
        }
    }

    private function assertPositiveShareId(int $shareId): void
    {
        if ($shareId <= 0) {
            throw new ApiException('Invalid share ID.', 400, 'invalid_share_id');
        }
    }

    private function requiredPositiveInt($value, string $name): int
    {
        $result = $this->nullablePositiveInt($value, $name);
        if ($result === null) {
            throw new ApiException($name . ' is required.', 400, $name . '_required');
        }
        return $result;
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

        error_log('[nas_api] ShareController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
