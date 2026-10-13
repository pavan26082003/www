<?php
namespace App\Services\NAS;

use App\Repositories\NAS\ClusterRepository;

class ClusterService
{
    private ClusterRepository $clusterRepository;

    public function __construct(ClusterRepository $clusterRepository)
    {
        $this->clusterRepository = $clusterRepository;
    }

    /**
     * Get all clusters for user
     */
    public function getClusters(int $userId): array
    {
        $clusters = $this->clusterRepository->getClustersWithCounts($userId);
        
        if (empty($clusters)) {
            $this->clusterRepository->createDefaultClusters($userId);
            $clusters = $this->clusterRepository->getClustersWithCounts($userId);
        }

        return $clusters;
    }

    /**
     * Get cluster by slug
     */
    public function getClusterBySlug(int $userId, string $slug): ?array
    {
        return $this->clusterRepository->getBySlug($userId, $slug);
    }

    /**
     * Get default cluster
     */
    public function getDefaultCluster(int $userId): ?array
    {
        return $this->clusterRepository->getDefault($userId);
    }

    /**
     * Create default clusters for user
     */
    public function createDefaultClusters(int $userId): void
    {
        $this->clusterRepository->createDefaultClusters($userId);
    }
}