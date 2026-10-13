<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\RestBaseModel;
use Config\RestDatabase;
use RuntimeException;
use Throwable;

abstract class RestBaseController extends Security_Controller
{
    protected \mysqli $restDb;

    protected string $brandId = '';
    protected string $branchId = '';
    protected string $userId = '';
    protected string $userRole = '';
    protected string $userEmail = '';

    /** @var array<string, string> */
    protected array $authContext = [];

    public function __construct()
    {
        parent::__construct(true);

        date_default_timezone_set('Asia/Kolkata');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $this->restDb = RestDatabase::connect();

            $email = $this->resolveAuthenticatedEmail();
            if ($email === '') {
                throw new RuntimeException(
                    'Restaurant access denied. Logged-in user email was not found in session or request.'
                );
            }

            /** @var RestBaseModel $resolver */
            $resolver = new class() extends RestBaseModel {};

            $requestedBranchId = $this->resolveRequestedBranchId();

            $context = $resolver->resolveActiveUserContextByEmail($email, $requestedBranchId);

            if ($context === []) {
                throw new RuntimeException(
                    'Restaurant access denied. No active restaurant user was found for email: ' . $email
                );
            }

            $this->applyAuthContext($context);
            $this->persistAuthContextToSession();
            $this->applyDefaultTimezone();




            // ===== TEMP DEBUG START =====

            // echo '<pre style="
            //                     background:#0f172a;
            //                     color:#22c55e;
            //                     padding:16px;
            //                     margin:20px;
            //                     border-radius:12px;
            //                     font:14px/1.6 Consolas, monospace;
            //                 ">';

            // echo "RESTAURANT AUTH DEBUG\n";
            // echo "=====================\n\n";

            // echo "EMAIL     : " . htmlspecialchars($this->userEmail) . "\n";
            // echo "USER_ID   : " . htmlspecialchars($this->userId) . "\n";
            // echo "BRAND_ID  : " . htmlspecialchars($this->brandId) . "\n";
            // echo "BRANCH_ID : " . htmlspecialchars($this->branchId) . "\n";
            // echo "ROLE      : " . htmlspecialchars($this->userRole) . "\n";

            // echo "\n[SESSION]\n";
            // echo "rest_user_id   : " . ($_SESSION['rest_user_id'] ?? '') . "\n";
            // echo "rest_brand_id  : " . ($_SESSION['rest_brand_id'] ?? '') . "\n";
            // echo "rest_branch_id : " . ($_SESSION['rest_branch_id'] ?? '') . "\n";
            // echo "rest_user_role : " . ($_SESSION['rest_user_role'] ?? '') . "\n";

            // echo "</pre>";
            // exit;
            // ===== TEMP DEBUG END =====
        } catch (Throwable $e) {
            $this->handleBootstrapFailure($e);
        }
    }

    protected function renderView(string $viewPath, array $data = []): void
    {
        if (!is_file($viewPath)) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Restaurant view file not found.';
            exit;
        }

        extract($data, EXTR_SKIP);
        require $viewPath;
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function requestData(): array
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        return $_POST ?: [];
    }

    protected function safeApi(callable $callback): void
    {
        try {
            $result = $callback();

            $this->json([
                'ok' => true,
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            $code = (int) $e->getCode();
            if ($code < 400 || $code > 599) {
                $code = 500;
            }

            $this->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], $code);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function restaurantContext(): array
    {
        return $this->authContext;
    }

    protected function currentBrandId(): string
    {
        return $this->brandId;
    }

    protected function currentBranchId(): string
    {
        return $this->branchId;
    }

    protected function currentUserId(): string
    {
        return $this->userId;
    }

    protected function currentUserRole(): string
    {
        return $this->userRole;
    }

    protected function currentUserEmail(): string
    {
        return $this->userEmail;
    }

    protected function requireRestaurantRole(array $allowedRoles): void
    {
        $allowed = array_map(
            static function ($value): string {
                return strtolower(trim((string) $value));
            },
            array_filter($allowedRoles, 'is_string')
        );

        $current = strtolower($this->userRole);

        if ($current === '' || !in_array($current, $allowed, true)) {
            throw new RuntimeException('You are not authorized to access this restaurant module.', 403);
        }
    }

    private function resolveAuthenticatedEmail(): string
    {
        $candidates = [
            $_SESSION['userEmail'] ?? null,
            $_SESSION['username'] ?? null,
            $_SESSION['appusername'] ?? null,
            $_SESSION['email'] ?? null,
            $_SESSION['rest_user_email'] ?? null,

            $_GET['userEmail'] ?? null,
            $_GET['email'] ?? null,
            $_GET['appuser'] ?? null,
            $_GET['username'] ?? null,

            $_POST['userEmail'] ?? null,
            $_POST['email'] ?? null,
            $_POST['appuser'] ?? null,
            $_POST['username'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $email = $this->normalizeEmail($candidate);
            if ($email !== '') {
                return $email;
            }
        }

        return '';
    }

    private function resolveRequestedBranchId(): ?string
    {
        $candidates = [
            $_GET['branch_id'] ?? null,
            $_GET['rest_branch_id'] ?? null,
            $_POST['branch_id'] ?? null,
            $_POST['rest_branch_id'] ?? null,
            $_SESSION['rest_requested_branch_id'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($this->isValidEntityId($value)) {
                return strtolower($value);
            }
        }

        return null;
    }

    private function normalizeEmail($value): string
    {
        $email = strtolower(trim((string) $value));
        if ($email === '') {
            return '';
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    /**
     * @param array<string, string> $context
     */
    private function applyAuthContext(array $context): void
    {
        $this->authContext = [
            'user_id' => (string) ($context['user_id'] ?? ''),
            'brand_id' => (string) ($context['brand_id'] ?? ''),
            'branch_id' => (string) ($context['branch_id'] ?? ''),
            'role' => (string) ($context['role'] ?? ''),
            'email' => (string) ($context['email'] ?? ''),
        ];

        $this->userId = $this->authContext['user_id'];
        $this->brandId = $this->authContext['brand_id'];
        $this->branchId = $this->authContext['branch_id'];
        $this->userRole = $this->authContext['role'];
        $this->userEmail = $this->authContext['email'];

        if (
            !$this->isValidEntityId($this->userId) ||
            !$this->isValidEntityId($this->brandId) ||
            !$this->isValidEntityId($this->branchId) ||
            $this->userRole === '' ||
            $this->userEmail === ''
        ) {
            throw new RuntimeException('Restaurant user context is incomplete or invalid.');
        }
    }

    private function persistAuthContextToSession(): void
    {
        $_SESSION['rest_user_email'] = $this->userEmail;
        $_SESSION['rest_user_id'] = $this->userId;
        $_SESSION['rest_brand_id'] = $this->brandId;
        $_SESSION['rest_branch_id'] = $this->branchId;
        $_SESSION['rest_user_role'] = $this->userRole;

        $_SESSION['rest_auth_context'] = [
            'user_id' => $this->userId,
            'brand_id' => $this->brandId,
            'branch_id' => $this->branchId,
            'role' => $this->userRole,
            'email' => $this->userEmail,
        ];
    }

    private function applyDefaultTimezone(): void
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    private function handleBootstrapFailure(Throwable $e): void
    {
        $status = 401;
        $message = trim($e->getMessage());

        if ($message === '') {
            $message = 'Restaurant authentication failed.';
        }

        if (
            stripos($message, 'not authorized') !== false ||
            stripos($message, 'access denied') !== false
        ) {
            $status = 403;
        } elseif (
            stripos($message, 'not found') !== false ||
            stripos($message, 'not found in session') !== false ||
            stripos($message, 'no active restaurant user') !== false
        ) {
            $status = 401;
        } elseif (
            stripos($message, 'invalid') !== false ||
            stripos($message, 'incomplete') !== false
        ) {
            $status = 500;
        }

        if ($this->isApiRequest()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => false,
                'message' => $message,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
        exit;
    }

    protected function isValidEntityId(?string $value): bool
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return false;
        }

        return preg_match('/^[a-f0-9]{32}$/i', $value) === 1
            || preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $value) === 1;
    }

    private function isApiRequest(): bool
    {
        $uri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));
        if (strpos($uri, '/api') !== false) {
            return true;
        }

        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }

        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        return $requestedWith === 'xmlhttprequest';
    }
}
