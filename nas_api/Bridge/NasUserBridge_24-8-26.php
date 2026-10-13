<?php

declare(strict_types=1);

namespace NasApi\Bridge;

use NasApi\Core\ApiException;
use NasApi\Integration\SharedNasFactory;

/**
 * Mobile-auth identity bridge only.
 *
 * NAS data access is delegated to the existing
 * App\Repositories\NAS\UserRepository. No NAS SQL is duplicated in nas_api.
 */
final class NasUserBridge
{
    /**
     * @return array{id:int,efactor_user_id:int,username:string,email:string,storage_limit:int,storage_used:int,is_active:int}
     */
    public function resolve(int $efactorUserId): array
    {
        if ($efactorUserId <= 0) {
            throw new ApiException(
                'A valid Efactor user ID is required for NAS mapping.',
                401,
                'invalid_efactor_user'
            );
        }

        $repository = SharedNasFactory::userRepository();
        $user = $repository->findByEfactorUserId($efactorUserId);

        if (!$user) {
            throw new ApiException(
                'No NAS account is linked to this Efactor user.',
                404,
                'nas_user_not_linked'
            );
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            throw new ApiException(
                'The linked NAS account is inactive.',
                403,
                'nas_user_inactive'
            );
        }

        return [
            'id' => (int) $user['id'],
            'efactor_user_id' => (int) ($user['efactor_user_id'] ?? 0),
            'username' => (string) ($user['username'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'storage_limit' => (int) ($user['storage_limit'] ?? 0),
            'storage_used' => (int) ($user['storage_used'] ?? 0),
            'is_active' => (int) ($user['is_active'] ?? 0),
        ];
    }
}
