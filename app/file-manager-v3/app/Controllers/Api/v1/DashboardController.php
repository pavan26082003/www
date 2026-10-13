<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\DashboardService;
use App\Repositories\UserRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\ShareRepository;

class DashboardController extends BaseController
{
    private DashboardService $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService(
            new UserRepository(),
            new FileRepository(),
            new FolderRepository(),
            new ShareRepository()
        );
    }

    /**
     * GET - Dashboard Data
     * Route: /api/v1/dashboard
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $data = $this->dashboardService->getDashboardData($userId);
            $this->success($data, 'Dashboard data retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Recent Items
     * Route: /api/v1/dashboard/recent
     */
    public function recent(): void
    {
        try {
            $userId = $this->getUserId();
            $items = $this->dashboardService->getRecentItems($userId);
            $this->success(['items' => $items], 'Recent items retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Quick Stats
     * Route: /api/v1/dashboard/stats
     */
    public function stats(): void
    {
        try {
            $userId = $this->getUserId();
            $stats = $this->dashboardService->getQuickStats($userId);
            $this->success(['stats' => $stats], 'Stats retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}