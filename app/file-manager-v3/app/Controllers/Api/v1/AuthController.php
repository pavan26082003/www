<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Repositories\UserRepository;

class AuthController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * POST - Register
     * Route: /api/v1/auth/register
     */
    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->error('Invalid request', 400);
            return;
        }

        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        // Validate input
        if (empty($username) || strlen($username) < 3) {
            $this->error('Username must be at least 3 characters', 400);
            return;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Valid email is required', 400);
            return;
        }

        if (empty($password) || strlen($password) < 6) {
            $this->error('Password must be at least 6 characters', 400);
            return;
        }

        // Check if username exists
        $existingUser = $this->userRepository->findByUsername($username);
        if ($existingUser) {
            $this->error('Username already taken', 400);
            return;
        }

        // Check if email exists
        $existingEmail = $this->userRepository->findByEmail($email);
        if ($existingEmail) {
            $this->error('Email already registered', 400);
            return;
        }

        // Create user
        $userId = $this->userRepository->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        if (!$userId) {
            $this->error('Registration failed', 500);
            return;
        }

        // Auto-login after registration
        $_SESSION['user_id'] = (int)$userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        $this->success([
            'user' => [
                'id' => (int)$userId,
                'username' => $username,
                'email' => $email
            ]
        ], 'Registration successful');
    }

    /**
     * POST - Login
     * Route: /api/v1/auth/login
     */
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->error('Invalid request', 400);
            return;
        }

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->error('Username and password required', 400);
            return;
        }

        // Try username first, then email
        $user = $this->userRepository->findByUsername($username);
        if (!$user) {
            $user = $this->userRepository->findByEmail($username);
        }

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->error('Invalid credentials', 401);
            return;
        }

        // Set session
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        $this->success([
            'user' => [
                'id' => (int)$user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ], 'Login successful');
    }

    /**
     * POST - Logout
     * Route: /api/v1/auth/logout
     */
    public function logout(): void
    {
        try {
            // Clear session variables
            $_SESSION = [];
            
            // If using cookies, delete the session cookie
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
            
            // Destroy the session
            session_destroy();
            
            // Return success response
            $this->success(null, 'Logged out successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Get current user info
     * Route: /api/v1/auth/me
     */
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
            
            $this->success(['user' => $user], 'User info retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}