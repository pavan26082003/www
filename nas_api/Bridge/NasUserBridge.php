<?php

declare(strict_types=1);

namespace NasApi\Bridge;

use NasApi\Core\ApiException;
use NasApi\Integration\SharedNasFactory;
use Throwable;

/**
 * Mobile-auth identity bridge only.
 *
 * NAS data access is delegated to the existing
 * App\Repositories\NAS\UserRepository. No NAS SQL is duplicated in nas_api.
 *
 * First-access provisioning rules:
 * 1. Existing efactor_user_id mapping -> reuse that NAS account.
 * 2. Same trusted email on an unlinked NAS account -> link and reuse it.
 * 3. No NAS account -> automatically create one using trusted Efactor data.
 * 4. Same email already linked to another Efactor user -> reject the request.
 */
final class NasUserBridge
{
    /**
     * @return array{id:int,efactor_user_id:int,username:string,email:string,storage_limit:int,storage_used:int,is_active:int}
     */
    public function resolve(int $efactorUserId, ?string $efactorEmail = null): array
    {
        if ($efactorUserId <= 0) {
            throw new ApiException(
                'A valid Efactor user ID is required for NAS mapping.',
                401,
                'invalid_efactor_user'
            );
        }

        $repository = SharedNasFactory::userRepository();

        // Preferred path: this Efactor identity is already linked.
        $user = $repository->findByEfactorUserId($efactorUserId);
        if ($user) {
            return $this->normalizeActiveUser($user);
        }

        $email = trim((string) $efactorEmail);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ApiException(
                'A valid Efactor email is required to provision a NAS account.',
                422,
                'nas_provisioning_email_required'
            );
        }

        // Preserve old NAS data when an account with the same email already exists.
        $emailUser = $repository->findByEmail($email);
        if ($emailUser) {
            $linkedEfactorUserId = isset($emailUser['efactor_user_id'])
                ? (int) $emailUser['efactor_user_id']
                : 0;

            if ($linkedEfactorUserId > 0 && $linkedEfactorUserId !== $efactorUserId) {
                throw new ApiException(
                    'This NAS account is already linked to another Efactor user.',
                    409,
                    'nas_account_link_conflict'
                );
            }

            if ($linkedEfactorUserId !== $efactorUserId) {
                $linked = $repository->linkEfactorUser(
                    (int) $emailUser['id'],
                    $efactorUserId
                );

                if (!$linked) {
                    throw new ApiException(
                        'Unable to link the existing NAS account to this Efactor user.',
                        409,
                        'nas_account_link_failed'
                    );
                }
            }

            $user = $repository->findByEfactorUserId($efactorUserId);
            if (!$user) {
                throw new ApiException(
                    'The NAS account was linked but could not be reloaded.',
                    500,
                    'nas_account_reload_failed'
                );
            }

            return $this->normalizeActiveUser($user);
        }

        // No mapped or same-email NAS account exists: provision one automatically.
        $username = $this->generateUniqueUsername($repository, $email, $efactorUserId);
        $passwordHash = password_hash(
            bin2hex(random_bytes(32)),
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            throw new ApiException(
                'Unable to initialize the NAS account credentials.',
                500,
                'nas_account_password_init_failed'
            );
        }

        try {
            $nasUserId = $repository->create([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
                'efactor_user_id' => $efactorUserId,
            ]);
        } catch (Throwable $e) {
            // Protect against a concurrent first-login request creating/linking
            // the account between our lookup and insert.
            $user = $repository->findByEfactorUserId($efactorUserId);
            if (!$user) {
                $emailUser = $repository->findByEmail($email);
                if ($emailUser) {
                    $linkedEfactorUserId = isset($emailUser['efactor_user_id'])
                        ? (int) $emailUser['efactor_user_id']
                        : 0;

                    if ($linkedEfactorUserId === $efactorUserId) {
                        $user = $emailUser;
                    }
                }
            }

            if (!$user) {
                throw new ApiException(
                    'Unable to automatically provision the NAS account.',
                    500,
                    'nas_account_provision_failed'
                );
            }

            return $this->normalizeActiveUser($user);
        }

        if ($nasUserId <= 0) {
            throw new ApiException(
                'Unable to automatically provision the NAS account.',
                500,
                'nas_account_provision_failed'
            );
        }

        $user = $repository->findByEfactorUserId($efactorUserId);
        if (!$user) {
            $user = $repository->find($nasUserId);
        }

        if (!$user) {
            throw new ApiException(
                'The NAS account was created but could not be reloaded.',
                500,
                'nas_account_reload_failed'
            );
        }

        return $this->normalizeActiveUser($user);
    }

    /**
     * @param object $repository Existing App\Repositories\NAS\UserRepository instance.
     */
    private function generateUniqueUsername($repository, string $email, int $efactorUserId): string
    {
        $emailParts = explode('@', $email, 2);
        $base = strtolower((string) ($emailParts[0] ?? ''));
        $base = preg_replace('/[^a-z0-9._-]+/', '', $base) ?: '';
        $base = trim($base, '._-');

        if (strlen($base) < 3) {
            $base = 'user' . $efactorUserId;
        }

        // Keep generated usernames to a conservative length for legacy schemas.
        $base = substr($base, 0, 40);

        if (!$repository->findByUsername($base)) {
            return $base;
        }

        $candidateBase = substr($base, 0, 28) . '_e' . $efactorUserId;
        if (!$repository->findByUsername($candidateBase)) {
            return $candidateBase;
        }

        for ($suffix = 2; $suffix <= 100; $suffix++) {
            $candidate = substr($candidateBase, 0, 44) . '_' . $suffix;
            if (!$repository->findByUsername($candidate)) {
                return $candidate;
            }
        }

        throw new ApiException(
            'Unable to generate a unique NAS username for this Efactor account.',
            500,
            'nas_username_generation_failed'
        );
    }

    /**
     * @param array<string,mixed> $user
     * @return array{id:int,efactor_user_id:int,username:string,email:string,storage_limit:int,storage_used:int,is_active:int}
     */
    private function normalizeActiveUser(array $user): array
    {
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
