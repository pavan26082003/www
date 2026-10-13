<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\ClusterService;
use App\Repositories\ClusterRepository;

class ClusterController extends BaseController
{
    private ClusterService $clusterService;

    public function __construct()
    {
        $this->clusterService = new ClusterService(
            new ClusterRepository()
        );
    }

    /**
     * GET - List Clusters with counts
     * Route: /api/v1/clusters
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $clusters = $this->clusterService->getClusters($userId);
            $this->success(['clusters' => $clusters], 'Clusters retrieved');
        } catch (\Exception $e) {
            // ✅ FIX: Pass int as status code
            $this->error($e->getMessage(), 500);
        }
    }
}