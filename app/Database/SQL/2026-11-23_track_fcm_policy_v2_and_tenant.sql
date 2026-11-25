-- GPS tracking: walk-test background policy + real tenant_id for existing accounts.
-- Safe to re-run. Does not change track_* columns.

-- 1) users.tenant_id (skip if the column already exists)
SET @col := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'tenant_id'
);
SET @sql := IF(@col = 0,
  'ALTER TABLE `users` ADD COLUMN `tenant_id` VARCHAR(191) NULL',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Client accounts must not stay on the demo placeholder.
UPDATE `users`
SET `tenant_id` = CONCAT('tenant_client_', `client_id`)
WHERE IFNULL(`client_id`, 0) > 0
  AND (`tenant_id` IS NULL OR `tenant_id` = '' OR `tenant_id` = 'tenant_demo_001');

-- No client: the assigned tenant is tenant_demo_001. Do not overwrite an explicit tenant.
UPDATE `users`
SET `tenant_id` = 'tenant_demo_001'
WHERE IFNULL(`client_id`, 0) = 0
  AND (`tenant_id` IS NULL OR `tenant_id` = '');

-- 2) Walk-test policy. Foreground interval_seconds stays as-is (5).
-- Background is its own 30s interval so a cached v1 (enabled=false) refetches.
UPDATE `track_policies`
SET
  `policy_json` = JSON_SET(
    COALESCE(NULLIF(`policy_json`, ''), '{}'),
    '$.background',
    JSON_OBJECT(
      'enabled', CAST(true AS JSON),
      'interval_seconds', 30,
      'distance_meters', CAST(NULL AS JSON),
      'mode', 'interval'
    )
  ),
  `version` = IF(
    CAST(`version` AS UNSIGNED) < 2,
    '2',
    CAST(CAST(`version` AS UNSIGNED) + 1 AS CHAR)
  ),
  `updated_at` = NOW()
WHERE `app_id` = 'efactor_field_tracker'
  AND `tenant_id` = 'tenant_demo_001'
  AND `is_active` = 1
  AND (
    `policy_json` IS NULL
    OR `policy_json` = ''
    OR JSON_EXTRACT(`policy_json`, '$.background.enabled') IS NULL
    OR JSON_EXTRACT(`policy_json`, '$.background.enabled') = CAST(false AS JSON)
    OR JSON_UNQUOTE(JSON_EXTRACT(`policy_json`, '$.background.mode')) <> 'interval'
    OR JSON_EXTRACT(`policy_json`, '$.background.interval_seconds') IS NULL
    OR JSON_EXTRACT(`policy_json`, '$.background.interval_seconds') <> 30
    OR CAST(`version` AS UNSIGNED) < 2
  );
