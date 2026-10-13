<?php

declare(strict_types=1);

namespace NasApi\Auth;

use NasApi\Bridge\NasUserBridge;

final class AuthenticatedNasContext
{
    /**
     * Resolve the current mobile/API caller from the Bearer token.
     *
     * @return array{
     *   efactor_user_id:int,
     *   nas_user_id:int,
     *   efactor:array{efactor_user_id:int,email:?string,phone:?string},
     *   nas_user:array{id:int,efactor_user_id:int,username:string,email:string,storage_limit:int,storage_used:int,is_active:int}
     * }
     */
    public static function resolve(): array
    {
        $efactor = (new EfactorBearerAuth())->authenticate();
        $nasUser = (new NasUserBridge())->resolve(
            $efactor['efactor_user_id'],
            $efactor['email'] ?? null
        );

        return [
            'efactor_user_id' => $efactor['efactor_user_id'],
            'nas_user_id' => $nasUser['id'],
            'efactor' => $efactor,
            'nas_user' => $nasUser,
        ];
    }
}
