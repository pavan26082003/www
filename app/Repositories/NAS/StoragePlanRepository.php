<?php

namespace App\Repositories\NAS;

use PDO;

class StoragePlanRepository extends Repository
{
    public function getActivePlans(): array
    {
        $stmt = $this->db->query(
            'SELECT
                id,
                plan_code,
                plan_name,
                storage_bytes,
                price,
                currency,
                description,
                features_json,
                is_popular,
                is_active,
                sort_order
             FROM nas_storage_plans
             WHERE is_active = 1
             ORDER BY sort_order ASC, storage_bytes ASC, id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findActiveByCode(string $planCode): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                id,
                plan_code,
                plan_name,
                storage_bytes,
                price,
                currency,
                description,
                features_json,
                is_popular,
                is_active,
                sort_order
             FROM nas_storage_plans
             WHERE plan_code = ?
               AND is_active = 1
             LIMIT 1'
        );

        $stmt->execute([$planCode]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Lock the plan row when creating a purchase.
     */
    public function findActiveByCodeForUpdate(string $planCode): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                id,
                plan_code,
                plan_name,
                storage_bytes,
                price,
                currency,
                description,
                features_json,
                is_popular,
                is_active,
                sort_order
             FROM nas_storage_plans
             WHERE plan_code = ?
               AND is_active = 1
             LIMIT 1
             FOR UPDATE'
        );

        $stmt->execute([$planCode]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function normalize(array $plan): array
    {
        $features = [];

        if (!empty($plan['features_json'])) {
            $decoded = json_decode((string) $plan['features_json'], true);

            if (is_array($decoded)) {
                foreach ($decoded as $feature) {
                    if (is_scalar($feature)) {
                        $feature = trim((string) $feature);

                        if ($feature !== '') {
                            $features[] = $feature;
                        }
                    }
                }
            }
        }

        return [
            'id' => (int) ($plan['id'] ?? 0),
            'code' => (string) ($plan['plan_code'] ?? ''),
            'name' => (string) ($plan['plan_name'] ?? ''),
            'storage_bytes' => (int) ($plan['storage_bytes'] ?? 0),
            'price' => $plan['price'] !== null
                ? (float) $plan['price']
                : null,
            'currency' => (string) ($plan['currency'] ?? 'INR'),
            'description' => (string) ($plan['description'] ?? ''),
            'features' => $features,
            'is_popular' => (int) ($plan['is_popular'] ?? 0) === 1,
        ];
    }
}
