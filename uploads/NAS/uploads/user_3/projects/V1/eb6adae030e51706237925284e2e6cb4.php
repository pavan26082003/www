<?php
namespace App\Controllers\NAS\Api\V1;

use App\Controllers\NAS\BaseController;
use App\Services\NAS\DashboardService;
use App\Repositories\NAS\UserRepository;
use App\Repositories\NAS\FileRepository;
use App\Repositories\NAS\FolderRepository;
use App\Repositories\NAS\ShareRepository;

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
     * Route: /NAS/api/v1/dashboard
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $data = $this->dashboardService->getDashboardData($userId);
            $this->success($data, 'Dashboard data retrieved');

        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Recent Items
     * Route: /NAS/api/v1/dashboard/recent
     */
    public function recent(): void
    {
        try {
            $userId = $this->getUserId();
            $items = $this->dashboardService->getRecentItems($userId);
            $this->success(['items' => $items], 'Recent items retrieved');

        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Quick Stats
     * Route: /NAS/api/v1/dashboard/stats
     */
    public function stats(): void
    {
        try {
            $userId = $this->getUserId();
            $stats = $this->dashboardService->getQuickStats($userId);
            $this->success(['stats' => $stats], 'Stats retrieved');

        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}