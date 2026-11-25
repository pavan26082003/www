-- Additive only. Does NOT change track_* table structure/columns.
-- Run once on production (skip steps that already applied).

-- 1) users.tenant_id for login/profile (skip if column already exists)
-- ALTER TABLE `users` ADD COLUMN `tenant_id` VARCHAR(191) NULL AFTER `client_id`;
-- CREATE INDEX `idx_users_tenant_id` ON `users` (`tenant_id`);

-- 2) Backfill tenant_id when empty
UPDATE `users`
SET `tenant_id` = 'tenant_demo_001'
WHERE (`tenant_id` IS NULL OR `tenant_id` = '')
  AND (IFNULL(`client_id`, 0) = 0);

UPDATE `users`
SET `tenant_id` = CONCAT('tenant_client_', `client_id`)
WHERE (`tenant_id` IS NULL OR `tenant_id` = '')
  AND IFNULL(`client_id`, 0) > 0;

-- 3) Background block in policy_json only (no new track columns); bump version for device refetch
UPDATE `track_policies`
SET
  `policy_json` = JSON_OBJECT(
    'background', JSON_OBJECT(
      'enabled', true,
      'interval_seconds', NULL,
      'distance_meters', NULL,
      'mode', 'interval'
    )
  ),
  `version` = CAST(CAST(`version` AS UNSIGNED) + 1 AS CHAR),
  `updated_at` = NOW()
WHERE `is_active` = 1
  AND (
    `policy_json` IS NULL
    OR `policy_json` = ''
    OR JSON_EXTRACT(`policy_json`, '$.background') IS NULL
  );
