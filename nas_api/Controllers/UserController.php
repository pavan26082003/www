<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

final class UserController
{
    /** GET /v1/users/search?q=ab */
    public function search(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];
            $query = trim((string) ($_GET['q'] ?? ''));

            if (strlen($query) < 2) {
                JsonResponse::send([
                    'success' => true,
                    'message' => 'No search query provided',
                    'users' => [],
                ]);
            }

            $rows = SharedNasFactory::userRepository()->search($query, $userId);
            $users = [];

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                // Return only fields needed by the sharing picker. In particular,
                // never expose password/storage data or Efactor/NAS bridge data.
                $users[] = [
                    'id' => isset($row['id']) ? (int) $row['id'] : 0,
                    'username' => (string) ($row['username'] ?? ''),
                    'email' => (string) ($row['email'] ?? ''),
                ];
            }

            JsonResponse::send([
                'success' => true,
                'message' => 'Users found',
                'users' => $users,
            ]);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    private function serviceError(\Throwable $e): void
    {
        $status = (int) $e->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        error_log('[nas_api] UserController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
