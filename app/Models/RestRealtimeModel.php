<?php
require_once __DIR__ . '/../Config/Database.php';

function realtime_publish(
    string $brandId,
    string $branchId,
    string $eventType,
    string $entityType = '',
    string $entityId = '',
    array $payload = []
): bool {
    $conn = db();

    $stmt = $conn->prepare("
        INSERT INTO realtime_events
        (brand_id, branch_id, event_type, entity_type, entity_id, payload_json, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    if (!$stmt) return false;

    $json = json_encode($payload);

    $stmt->bind_param(
        'ssssss',
        $brandId,
        $branchId,
        $eventType,
        $entityType,
        $entityId,
        $json
    );

    return $stmt->execute();
}