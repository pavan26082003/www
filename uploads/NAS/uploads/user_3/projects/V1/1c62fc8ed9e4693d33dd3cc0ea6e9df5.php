<?php

namespace App\Controllers\NAS\Api\V1;

use App\Controllers\NAS\BaseController;
use App\Repositories\NAS\UserRepository;

class AuthController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function test(): void
    {
        $this->success([
            'route' => 'NAS/test',
            'logged_in' => (int) session()->get('nas_user_id') > 0,
            'nas_user_id' => (int) session()->get('nas_user_id'),
        ], 'NAS AuthController loaded successfully');
    }

    public function register(): void
    {
        try {
            $input = service('request')->getJSON(true);

            if (!is_array($input)) {
                $this->error('Invalid request', 400);
                return;
            }

            $username = trim((string) ($input['username'] ?? ''));
            $email = trim((string) ($input['email'] ?? ''));
            $password = (string) ($input['password'] ?? '');

            if ($username === '' || strlen($username) < 3) {
                $this->error('Username must be at least 3 characters', 400);
                return;
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('Valid email is required', 400);
                return;
            }

            if ($password === '' || strlen($password) < 6) {
                $this->error('Password must be at least 6 characters', 400);
                return;
            }

            if ($this->userRepository->findByUsername($username)) {
                $this->error('Username already taken', 400);
                return;
            }

            if ($this->userRepository->findByEmail($email)) {
                $this->error('Email already registered', 400);
                return;
            }

            $userId = $this->userRepository->create([
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            if (!$userId) {
                $this->error('Registration failed', 500);
                return;
            }

            $session = session();

            $session->set([
                'nas_user_id' => (int) $userId,
                'nas_username' => $username,
                'nas_email' => $email,
            ]);

            $session->regenerate(true);

            $this->success([
                'user' => [
                    'id' => (int) $userId,
                    'username' => $username,
                    'email' => $email,
                ],
            ], 'Registration successful');

        } catch (\Throwable $e) {
            $statusCode = (int) ($e->getCode() ?: 500);
            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }
            $this->error($e->getMessage(), $statusCode);
        }
    }

    public function login(): void
    {
        try {
            $input = service('request')->getJSON(true);

            if (!is_array($input)) {
                $this->error('Invalid request', 400);
                return;
            }

            $username = trim((string) ($input['username'] ?? ''));
            $password = (string) ($input['password'] ?? '');

            if ($username === '' || $password === '') {
                $this->error('Username and password required', 400);
                return;
            }

            $user = $this->userRepository->findByUsername($username);

            if (!$user) {
                $user = $this->userRepository->findByEmail($username);
            }

            if (
                !$user
                || !isset($user['password_hash'])
                || !password_verify($password, $user['password_hash'])
            ) {
                $this->error('Invalid credentials', 401);
                return;
            }

            $session = session();

            $session->set([
                'nas_user_id' => (int) $user['id'],
                'nas_username' => (string) $user['username'],
                'nas_email' => (string) $user['email'],
            ]);

            $session->regenerate(true);

            $this->success([
                'user' => [
                    'id' => (int) $user['id'],
                    'username' => (string) $user['username'],
                    'email' => (string) $user['email'],
                ],
            ], 'Login successful');

        } catch (\Throwable $e) {
            $statusCode = (int) ($e->getCode() ?: 500);
            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }
            $this->error($e->getMessage(), $statusCode);
        }
    }

    public function logout(): void
    {
        try {
            session()->remove([
                'nas_user_id',
                'nas_username',
                'nas_email',
            ]);

            $this->success(null, 'Logged out successfully');

        } catch (\Throwable $e) {
            $statusCode = (int) ($e->getCode() ?: 500);
            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }
            $this->error($e->getMessage(), $statusCode);
        }
    }

    public function me(): void
    {
        try {
            $userId = $this->getUserId();
            $user = $this->userRepository->find($userId);

            if (!$user) {
                $this->error('User not found', 404);
                return;
            }

            unset($user['password_hash']);

            $this->success(
                ['user' => $user],
                'User info retrieved'
            );

        } catch (\Throwable $e) {
            $statusCode = (int) ($e->getCode() ?: 500);
            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }
            $this->error($e->getMessage(), $statusCode);
        }
    }
}
