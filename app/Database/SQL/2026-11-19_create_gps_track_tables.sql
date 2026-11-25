-- GPS Tracking Phase 0 — factor_db2 (local) / production equivalent.
-- Safe to re-run: CREATE TABLE IF NOT EXISTS + seed uses INSERT IGNORE.

CREATE TABLE IF NOT EXISTS `track_policies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` VARCHAR(191) NOT NULL,
  `tenant_id` VARCHAR(191) NOT NULL,
  `name` VARCHAR(191) NOT NULL DEFAULT 'default',
  `version` VARCHAR(64) NOT NULL DEFAULT '1',
  `mode` VARCHAR(32) NOT NULL DEFAULT 'interval',
  `interval_seconds` INT UNSIGNED NOT NULL DEFAULT 5,
  `geofence_json` LONGTEXT NULL,
  `upload_json` LONGTEXT NULL,
  `events_json` LONGTEXT NULL,
  `retention_days` INT UNSIGNED NOT NULL DEFAULT 90,
  `policy_json` LONGTEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_track_policies_app_tenant_name` (`app_id`, `tenant_id`, `name`),
  KEY `idx_track_policies_scope` (`app_id`, `tenant_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `track_policy_bindings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` VARCHAR(191) NOT NULL,
  `tenant_id` VARCHAR(191) NOT NULL,
  `policy_id` BIGINT UNSIGNED NOT NULL,
  `device_id` VARCHAR(191) NULL,
  `user_id` VARCHAR(191) NULL,
  `priority` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_track_bindings_lookup` (`app_id`, `tenant_id`, `device_id`, `user_id`),
  KEY `idx_track_bindings_policy` (`policy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `track_breadcrumbs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` VARCHAR(191) NOT NULL,
  `tenant_id` VARCHAR(191) NOT NULL,
  `device_id` VARCHAR(191) NOT NULL,
  `user_id` VARCHAR(191) NOT NULL,
  `client_uuid` VARCHAR(64) NOT NULL,
  `recorded_at` DATETIME NOT NULL,
  `lat` DOUBLE NOT NULL,
  `lng` DOUBLE NOT NULL,
  `accuracy_m` DOUBLE NULL,
  `speed_mps` DOUBLE NULL,
  `heading` DOUBLE NULL,
  `altitude_m` DOUBLE NULL,
  `battery_pct` INT NULL,
  `provider` VARCHAR(32) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_track_breadcrumbs_idem` (`app_id`, `tenant_id`, `client_uuid`),
  KEY `idx_track_breadcrumbs_history` (`app_id`, `tenant_id`, `user_id`, `recorded_at`),
  KEY `idx_track_breadcrumbs_device` (`app_id`, `tenant_id`, `device_id`, `recorded_at`),
  KEY `idx_track_breadcrumbs_purge` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `track_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` VARCHAR(191) NOT NULL,
  `tenant_id` VARCHAR(191) NOT NULL,
  `device_id` VARCHAR(191) NOT NULL,
  `user_id` VARCHAR(191) NOT NULL,
  `client_uuid` VARCHAR(64) NOT NULL,
  `type` VARCHAR(64) NOT NULL,
  `recorded_at` DATETIME NOT NULL,
  `lat` DOUBLE NULL,
  `lng` DOUBLE NULL,
  `accuracy_m` DOUBLE NULL,
  `site_id` VARCHAR(191) NULL,
  `note` VARCHAR(512) NULL,
  `payload_json` LONGTEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_track_events_idem` (`app_id`, `tenant_id`, `client_uuid`),
  KEY `idx_track_events_lookup` (`app_id`, `tenant_id`, `user_id`, `recorded_at`),
  KEY `idx_track_events_type` (`app_id`, `tenant_id`, `type`, `recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `track_live_positions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` VARCHAR(191) NOT NULL,
  `tenant_id` VARCHAR(191) NOT NULL,
  `device_id` VARCHAR(191) NOT NULL,
  `user_id` VARCHAR(191) NOT NULL,
  `lat` DOUBLE NOT NULL,
  `lng` DOUBLE NOT NULL,
  `accuracy_m` DOUBLE NULL,
  `speed_mps` DOUBLE NULL,
  `heading` DOUBLE NULL,
  `altitude_m` DOUBLE NULL,
  `battery_pct` INT NULL,
  `provider` VARCHAR(32) NULL,
  `recorded_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_track_live_device` (`app_id`, `tenant_id`, `device_id`),
  KEY `idx_track_live_user` (`app_id`, `tenant_id`, `user_id`),
  KEY `idx_track_live_scope` (`app_id`, `tenant_id`, `updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default Phase 0 policy for efactor_field_tracker (id=1 when empty).
INSERT IGNORE INTO `track_policies`
(`id`, `app_id`, `tenant_id`, `name`, `version`, `mode`, `interval_seconds`, `geofence_json`, `upload_json`, `events_json`, `retention_days`, `policy_json`, `is_active`, `created_at`, `updated_at`)
VALUES
(1, 'efactor_field_tracker', 'tenant_demo_001', 'default_field_tracker', '1', 'interval', 5,
 NULL,
 '{"batch_max_points":50,"batch_max_interval_seconds":60,"wifi_only":false}',
 '{"check_in_out_enabled":true}',
 90,
 NULL,
 1,
 NOW(),
 NOW());

INSERT IGNORE INTO `track_policy_bindings`
(`id`, `app_id`, `tenant_id`, `policy_id`, `device_id`, `user_id`, `priority`, `created_at`, `updated_at`)
VALUES
(1, 'efactor_field_tracker', 'tenant_demo_001', 1, NULL, NULL, 0, NOW(), NOW());
