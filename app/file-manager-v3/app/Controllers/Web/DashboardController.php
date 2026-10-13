<?php
// app/Controllers/Web/DashboardController.php

namespace App\Controllers\Web;

use App\Services\AuthService;

class DashboardController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show dashboard with specific view
     */
    public function showDashboard(): void
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /public/index.php');
            exit();
        }

        $view = $_GET['view'] ?? 'my-drive';
        
        // Pass user data to view
        $_SESSION['username'] = $_SESSION['username'] ?? 'User';
        $_SESSION['email'] = $_SESSION['email'] ?? '';
        
        require_once __DIR__ . '/../../Views/dashboard/index.php';
    }
}