<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

final class ClusterController
{
    /** GET /v1/clusters */
    public function index(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $clusters = SharedNasFactory::clusterService()->getClusters(
                (int) $context['nas_user_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Clusters retrieved',
                'clusters' => $clusters,
            ]);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    private function serviceError(\Throwable $e): void
    {
        $status = (int) $e->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        error_log('[nas_api] ClusterController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
