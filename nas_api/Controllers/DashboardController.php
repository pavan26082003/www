<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;
use Throwable;

final class DashboardController
{
    public function index(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $data = SharedNasFactory::dashboardService()
                ->getDashboardData($context['nas_user_id']);

            JsonResponse::send(array_merge(
                [
                    'success' => true,
                    'message' => 'Dashboard data retrieved',
                ],
                $data
            ));
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (Throwable $e) {
            $this->serverError($e);
        }
    }

    public function recent(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $items = SharedNasFactory::dashboardService()
                ->getRecentItems($context['nas_user_id']);

            JsonResponse::send([
                'success' => true,
                'message' => 'Recent items retrieved',
                'items' => $items,
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (Throwable $e) {
            $this->serverError($e);
        }
    }

    public function stats(): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $stats = SharedNasFactory::dashboardService()
                ->getQuickStats($context['nas_user_id']);

            JsonResponse::send([
                'success' => true,
                'message' => 'Stats retrieved',
                'stats' => $stats,
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (Throwable $e) {
            $this->serverError($e);
        }
    }

    private function apiError(ApiException $e): void
    {
        JsonResponse::send([
            'success' => false,
            'error' => $e->errorCode(),
            'message' => $e->getMessage(),
        ], $e->statusCode());
    }

    private function serverError(Throwable $e): void
    {
        error_log('[nas_api][dashboard] ' . get_class($e) . ': ' . $e->getMessage());

        $status = (int) $e->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        JsonResponse::send([
            'success' => false,
            'error' => 'dashboard_error',
            'message' => NAS_API_DEBUG ? $e->getMessage() : 'Unable to load dashboard data.',
        ], $status);
    }
}
