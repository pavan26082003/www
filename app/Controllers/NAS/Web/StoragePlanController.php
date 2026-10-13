<?php

namespace App\Controllers\NAS\Web;

use App\Repositories\NAS\UserRepository;
use Config\Services;

class StoragePlanController
{
    public function __construct()
    {
    }

    private function purchaseService()
    {
        return new \App\Services\NAS\StoragePurchaseService();
    }

    public function index()
    {
        $session = Services::session();
        $userId = (int) $session->get('nas_user_id');

        if ($userId <= 0) {
            return redirect()->to(site_url('signin'));
        }

        try {
            $purchaseService = $this->purchaseService();

            $plans = $purchaseService->getPlans();
            $storage = $purchaseService->getCurrentStorage($userId);
            $history = $purchaseService->getPurchaseHistory($userId);

            $username = trim((string) $session->get('nas_display_name'));

            if ($username === '') {
                $username = (string) ($session->get('nas_username') ?: 'User');
            }

            return view('NAS/storage_plans/index', [
                'basePath' => rtrim(site_url('NAS'), '/'),
                'assetsBase' => rtrim(base_url('assets/NAS'), '/'),
                'username' => $username,
                'userInitial' => strtoupper(substr($username ?: 'U', 0, 1)),
                'plans' => $plans,
                'storage' => $storage,
                'purchaseHistory' => $history,
                'temporaryActivationEnabled' =>
                    $purchaseService->isTemporaryActivationEnabled(),
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'NAS storage plans GET failed: '
                . get_class($e) . ': ' . $e->getMessage()
                . ' in ' . $e->getFile() . ':' . $e->getLine()
            );

            return view('NAS/storage_plans/setup_error', [
                'homeUrl' => site_url('NAS/home'),
                'checkUrl' => site_url('NAS/storage-plans/check'),
                'errorType' => get_class($e),
                'errorMessage' => $e->getMessage(),
                'showTechnicalError' =>
                    defined('ENVIRONMENT') && ENVIRONMENT === 'development',
            ]);
        }
    }

    public function purchase()
    {
        $session = Services::session();
        $userId = (int) $session->get('nas_user_id');

        if ($userId <= 0) {
            return redirect()->to(site_url('signin'));
        }

        $planCode = trim((string) ($_POST['plan_code'] ?? ''));

        try {
            $result = $this->purchaseService()->purchasePlan(
                $userId,
                $planCode
            );

            $session->setFlashdata(
                'nas_storage_success',
                !empty($result['temporary_activation'])
                    ? 'Storage plan activated successfully. Your new total quota is '
                        . $result['storage_after_formatted'] . '.'
                    : 'Storage purchase created and is waiting for payment.'
            );
        } catch (\Throwable $e) {
            log_message(
                'error',
                'NAS storage purchase failed for user '
                . $userId . ': ' . get_class($e) . ': ' . $e->getMessage()
            );

            $session->setFlashdata(
                'nas_storage_error',
                $e->getMessage()
            );
        }

        return redirect()->to(site_url('NAS/storage-plans'));
    }

    public function check()
    {
        $response = service('response');
        $session = Services::session();

        $result = [
            'success' => true,
            'message' => 'NAS storage purchase framework diagnostic',
            'checks' => [],
        ];

        $result['checks']['session'] = [
            'nas_user_id' => (int) $session->get('nas_user_id'),
            'has_nas_user' => (int) $session->get('nas_user_id') > 0,
        ];

        $requiredClasses = [
            'App\\Repositories\\NAS\\UserRepository',
            'App\\Repositories\\NAS\\StoragePlanRepository',
            'App\\Repositories\\NAS\\StoragePurchaseRepository',
            'App\\Services\\NAS\\StoragePurchaseService',
            'Config\\NasStoragePurchase',
        ];

        foreach ($requiredClasses as $className) {
            try {
                $exists = class_exists($className);

                $result['checks']['classes'][$className] = [
                    'exists' => $exists,
                ];

                if (!$exists) {
                    $result['success'] = false;
                }
            } catch (\Throwable $e) {
                $result['checks']['classes'][$className] = [
                    'exists' => false,
                    'error' => $e->getMessage(),
                ];
                $result['success'] = false;
            }
        }

        try {
            $pdo = (new UserRepository())->getPdo();

            $result['checks']['database_connection'] = [
                'connected' => true,
            ];

            foreach (['users', 'nas_storage_plans', 'nas_storage_purchases'] as $tableName) {
                $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
                $stmt->execute([$tableName]);

                $exists = (bool) $stmt->fetchColumn();

                $result['checks']['tables'][$tableName] = [
                    'exists' => $exists,
                ];

                if (!$exists) {
                    $result['success'] = false;
                }
            }

            if (!empty($result['checks']['tables']['nas_storage_plans']['exists'])) {
                $stmt = $pdo->query(
                    'SELECT COUNT(*) FROM nas_storage_plans WHERE is_active = 1'
                );

                $activePlanCount = (int) $stmt->fetchColumn();

                $result['checks']['plans'] = [
                    'active_plan_count' => $activePlanCount,
                    'has_active_plans' => $activePlanCount > 0,
                ];

                if ($activePlanCount <= 0) {
                    $result['success'] = false;
                }
            }
        } catch (\Throwable $e) {
            $result['checks']['database_connection'] = [
                'connected' => false,
                'error_type' => get_class($e),
                'error' => $e->getMessage(),
            ];
            $result['success'] = false;
        }

        if (!$result['success']) {
            $result['message'] =
                'Storage purchase setup is incomplete. Check failed items below.';
        }

        return $response->setJSON($result);
    }
}
