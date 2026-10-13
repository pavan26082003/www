<?php
// app/Controllers/Web/AuthController.php

namespace App\Controllers\Web;

use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show login page
     */
    public function showLogin(): void
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /public/home.php');
            exit();
        }
        require_once __DIR__ . '/../../Views/auth/login.php';
    }

    /**
     * Show registration page
     */
    public function showRegister(): void
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /app/file-manager-v3/dashboard');
            exit();
        }
        require_once __DIR__ . '/../../Views/auth/register.php';
    }

    /**
     * Show dashboard
     */
    public function showDashboard(): void
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /public/index.php');
            exit();
        }
        
        // Ensure session variables are set
        $_SESSION['username'] = $_SESSION['username'] ?? 'User';
        $_SESSION['email'] = $_SESSION['email'] ?? '';
        
        require_once __DIR__ . '/../../Views/dashboard/index.php';
    }

    /**
     * Process logout
     */
    public function logout(): void
    {
        $this->authService->logout();
        header('Location: /public/index.php');
        exit();
    }
}