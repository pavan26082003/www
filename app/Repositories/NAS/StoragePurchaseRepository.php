<?php

namespace App\Repositories\NAS;

use PDO;

class StoragePurchaseRepository extends Repository
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO nas_storage_purchases
             (
                user_id,
                plan_id,
                order_reference,
                plan_code,
                plan_name,
                amount,
                currency,
                storage_before,
                storage_after,
                purchase_status,
                payment_status,
                payment_provider,
                provider_order_id,
                provider_payment_id
             )
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            (int) $data['user_id'],
            (int) $data['plan_id'],
            (string) $data['order_reference'],
            (string) $data['plan_code'],
            (string) $data['plan_name'],
            $data['amount'],
            (string) $data['currency'],
            (int) $data['storage_before'],
            (int) $data['storage_after'],
            (string) $data['purchase_status'],
            (string) $data['payment_status'],
            $data['payment_provider'],
            $data['provider_order_id'],
            $data['provider_payment_id'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function markActivated(
        int $purchaseId,
        string $paymentStatus = 'not_integrated'
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE nas_storage_purchases
             SET purchase_status = ?,
                 payment_status = ?,
                 activated_at = NOW()
             WHERE id = ?
               AND purchase_status <> ?'
        );

        return $stmt->execute([
            'activated',
            $paymentStatus,
            $purchaseId,
            'activated',
        ]);
    }

    public function getRecentForUser(int $userId, int $limit = 8): array
    {
        $limit = max(1, min(25, $limit));

        $sql =
            'SELECT
                id,
                order_reference,
                plan_code,
                plan_name,
                amount,
                currency,
                storage_before,
                storage_after,
                purchase_status,
                payment_status,
                payment_provider,
                provider_order_id,
                provider_payment_id,
                activated_at,
                created_at
             FROM nas_storage_purchases
             WHERE user_id = ?
             ORDER BY id DESC
             LIMIT ' . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
