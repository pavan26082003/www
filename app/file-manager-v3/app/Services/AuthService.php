<?php
// app/Services/AuthService.php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Login via API
     */
    public function login(string $username, string $password): array
    {
        // Try username first, then email
        $user = $this->userRepository->findByUsername($username);
        if (!$user) {
            $user = $this->userRepository->findByEmail($username);
        }

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ];
    }

    /**
     * Register via API
     */
    public function register(string $username, string $email, string $password): array
    {
        // Check if user exists
        $existing = $this->userRepository->findByUsername($username);
        if ($existing) {
            return ['success' => false, 'message' => 'Username already taken'];
        }

        $existing = $this->userRepository->findByEmail($email);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Create user
        $userId = $this->userRepository->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            return [
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email
                ]
            ];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * Check if logged in
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public function getCurrentUser(): ?array
    {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? 'User',
                'email' => $_SESSION['email'] ?? ''
            ];
        }
        return null;
    }
}