<?php
// app/Controllers/Web/HomeController.php

namespace App\Controllers\Web;

use App\Services\AuthService;

class HomeController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show home page
     */
    public function showHome(): void
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /public/index.php');
            exit();
        }

        $userData = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? 'User',
            'email' => $_SESSION['email'] ?? '',
            'initial' => strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1))
        ];

        require_once __DIR__ . '/../../Views/home/index.php';
    }
}