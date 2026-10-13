<?php

declare(strict_types=1);

namespace App\Controllers;

require_once APPPATH . 'Models/RestMainModel.php';

use App\Models\RestMainModel;
use Throwable;

final class RestMainController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/main.php';

    private RestMainModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new RestMainModel();
    }

    public function index(): void
    {
        try {
            $context = $this->model->getMainContext($this->restaurantContext());

            $brand = is_array($context['brand'] ?? null) ? $context['brand'] : [];
            $branch = is_array($context['branch'] ?? null) ? $context['branch'] : [];
            $user = is_array($context['user'] ?? null) ? $context['user'] : [];
            $stats = is_array($context['stats'] ?? null) ? $context['stats'] : [];
            $visibleModules = is_array($context['visible_modules'] ?? null) ? $context['visible_modules'] : [];

            $brandId = (string) ($context['brand_id'] ?? $this->currentBrandId());
            $branchId = (string) ($context['branch_id'] ?? $this->currentBranchId());
            $userId = (string) ($context['user_id'] ?? $this->currentUserId());
            $role = (string) ($context['role'] ?? $this->currentUserRole());

            $brandProfile = [
                'brand_name' => trim((string) ($brand['name'] ?? 'Restaurant System')),
                'platform_name' => 'Restaurant Management System',
                'status' => ucfirst(strtolower(trim((string) ($brand['status'] ?? 'active')))),
                'scope' => 'Role-based restaurant access',
                'legal_name' => trim((string) ($brand['legal_name'] ?? '')),
                'logo' => trim((string) ($brand['logo'] ?? '')),
                'favicon' => trim((string) ($brand['favicon'] ?? '')),
                'mobile' => trim((string) ($brand['mobile'] ?? '')),
                'mail_id' => trim((string) ($brand['mail_id'] ?? '')),
                'subscription_plan' => strtolower(trim((string) ($brand['subscription_plan'] ?? 'starter'))),
                'timezone' => trim((string) ($brand['timezone'] ?? 'Asia/Kolkata')),
                'currency' => strtoupper(trim((string) ($brand['currency'] ?? 'INR'))),
                'is_setup_completed' => (int) ($brand['is_setup_completed'] ?? 0),

                'branch_name' => trim((string) ($branch['name'] ?? 'Main Branch')),
                'branch_code' => trim((string) ($branch['code'] ?? '')),
                'branch_city' => trim((string) ($branch['city'] ?? '')),
                'branch_address' => trim((string) ($branch['address'] ?? '')),
                'branch_phone' => trim((string) ($branch['phone'] ?? '')),
                'branch_email' => trim((string) ($branch['email'] ?? '')),
                'branch_status' => ucfirst(strtolower(trim((string) ($branch['status'] ?? 'active')))),

                'user_id' => $userId,
                'user_name' => trim((string) ($user['name'] ?? '')),
                'user_email' => trim((string) ($user['email'] ?? $this->currentUserEmail())),
                'user_role' => strtolower(trim((string) ($user['role'] ?? $role))),
                'employee_code' => trim((string) ($user['employee_code'] ?? '')),
                'profile_pic' => trim((string) ($user['profile_pic'] ?? '')),

                'branches_count' => (int) ($stats['branches_count'] ?? 0),
                'today_orders' => (int) ($stats['today_orders'] ?? 0),
                'total_tables' => (int) ($stats['total_tables'] ?? 0),
                'active_tables' => (int) ($stats['active_tables'] ?? 0),
                'pending_requests' => (int) ($stats['pending_requests'] ?? 0),
                'live_kitchen_tickets' => (int) ($stats['live_kitchen_tickets'] ?? 0),
            ];

            $moduleUrls = [
                'brand' => $this->buildModuleUrl('brand'),
                'customer' => $this->buildModuleUrl('customer'),
                'waiter' => $this->buildModuleUrl('waiter'),
                'kitchen' => $this->buildModuleUrl('kitchen'),
                'manager' => $this->buildModuleUrl('manager'),
            ];

            $this->renderView(self::VIEW_PATH, [
                'pageTitle' => 'Restaurant Management System',
                'brandId' => $brandId,
                'branchId' => $branchId,
                'brandProfile' => $brandProfile,
                'moduleUrls' => $moduleUrls,
                'visibleModules' => $visibleModules,
                'pageError' => '',
            ]);
        } catch (Throwable $e) {
            http_response_code(500);

            $this->renderView(self::VIEW_PATH, [
                'pageTitle' => 'Restaurant Management System',
                'brandId' => '',
                'branchId' => '',
                'brandProfile' => [],
                'moduleUrls' => [],
                'visibleModules' => [],
                'pageError' => trim($e->getMessage()) !== ''
                    ? trim($e->getMessage())
                    : 'Unable to load restaurant access hub right now.',
            ]);
        }
    }

    private function buildModuleUrl(string $module): string
    {
        $module = strtolower(trim($module));

        $allowed = ['main', 'brand', 'customer', 'waiter', 'kitchen', 'manager'];
        if (!in_array($module, $allowed, true)) {
            $module = 'main';
        }

        $base = $this->baseUrl();

        if ($module === 'main') {
            return $base . '/restaurant-system/main';
        }

        return $base . '/restaurant-system/' . $module;
    }

    private function baseUrl(): string
    {
        if (function_exists('base_url')) {
            return rtrim((string) base_url(), '/');
        }

        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host;
    }
}
