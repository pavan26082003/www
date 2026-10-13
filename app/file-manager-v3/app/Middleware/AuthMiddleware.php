<?php
// app/Middleware/AuthMiddleware.php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        // Check if session has user_id
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            // Return false for API calls - let the controller handle the response
            return false;
        }
        return true;
    }

    /**
     * Handle authentication - returns JSON response if not authenticated
     */
    public static function handle(): bool
    {
        if (!self::check()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized - Please login first'
            ]);
            return false;
        }
        return true;
    }
}