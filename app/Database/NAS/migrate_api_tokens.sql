-- Efactor NAS API Bearer-token support
-- Run this against the SAME NAS database used by App\Repositories\NAS\UserRepository.
-- Safe to run more than once.

CREATE TABLE IF NOT EXISTS `nas_api_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token_hash` char(64) NOT NULL,
  `efactor_user_id` bigint NOT NULL,
  `nas_user_id` int NOT NULL,
  `token_name` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nas_api_tokens_hash` (`token_hash`),
  KEY `idx_nas_api_tokens_nas_user` (`nas_user_id`),
  KEY `idx_nas_api_tokens_efactor_user` (`efactor_user_id`),
  KEY `idx_nas_api_tokens_expiry` (`expires_at`),
  KEY `idx_nas_api_tokens_revoked` (`revoked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `nas_api_auth_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `identity_hash` char(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `succeeded` tinyint(1) NOT NULL DEFAULT '0',
  `attempted_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_nas_api_attempt_identity_time` (`identity_hash`,`attempted_at`),
  KEY `idx_nas_api_attempt_ip_time` (`ip_address`,`attempted_at`),
  KEY `idx_nas_api_attempt_result_time` (`succeeded`,`attempted_at`),
  KEY `idx_nas_api_attempt_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
