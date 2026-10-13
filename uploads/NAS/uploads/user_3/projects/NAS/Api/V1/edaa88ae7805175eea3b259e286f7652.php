<?php
namespace App\Controllers\NAS\Api\V1;

use App\Controllers\NAS\BaseController;
use App\Services\NAS\ClusterService;
use App\Repositories\NAS\ClusterRepository;

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
     * Route: /NAS/api/v1/clusters
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $clusters = $this->clusterService->getClusters($userId);
            $this->success(['clusters' => $clusters], 'Clusters retrieved');
        } catch (\Throwable $e) {
            // ✅ FIX: Pass int as status code
            $this->error($e->getMessage(), 500);
        }
    }
}