<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use Throwable;

final class AuthController
{
    public function me(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $identity = $context['efactor'];
            $nasUser = $context['nas_user'];

            JsonResponse::send([
                'success' => true,
                'auth_type' => 'bearer',
                'efactor_user_id' => $identity['efactor_user_id'],
                'nas_user_id' => $nasUser['id'],
                'user' => [
                    'id' => $nasUser['id'],
                    'username' => $nasUser['username'],
                    'email' => $nasUser['email'],
                    'storage_limit' => $nasUser['storage_limit'],
                    'storage_used' => $nasUser['storage_used'],
                    'is_active' => $nasUser['is_active'],
                ],
            ]);
        } catch (ApiException $e) {
            JsonResponse::send([
                'success' => false,
                'error' => $e->errorCode(),
                'message' => $e->getMessage(),
            ], $e->statusCode());
        } catch (Throwable $e) {
            error_log('[nas_api][auth/me] ' . get_class($e) . ': ' . $e->getMessage());

            JsonResponse::send([
                'success' => false,
                'error' => 'auth_context_error',
                'message' => NAS_API_DEBUG ? $e->getMessage() : 'Unable to resolve NAS user.',
            ], 500);
        }
    }
}
