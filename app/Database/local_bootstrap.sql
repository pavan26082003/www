-- Local bootstrap schema so CodeIgniter can start after a fresh AMPPS MySQL install.
-- This is NOT a full factor_db2 dump. Import the team dump when available.

CREATE DATABASE IF NOT EXISTS factor_db2 CHARACTER SET utf8 COLLATE utf8_general_ci;
USE factor_db2;

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` mediumtext,
  `type` varchar(20) NOT NULL DEFAULT 'app',
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `settings` (`setting_name`, `setting_value`, `type`, `deleted`)
SELECT * FROM (
  SELECT 'language' AS setting_name, 'english' AS setting_value, 'app' AS type, 0 AS deleted
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `settings` WHERE `setting_name` = 'language'
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `user_type` varchar(20) NOT NULL DEFAULT 'staff',
  `is_admin` tinyint NOT NULL DEFAULT 0,
  `role_id` int NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `message_checked_at` datetime DEFAULT NULL,
  `client_id` int NOT NULL DEFAULT 0,
  `notification_checked_at` datetime DEFAULT NULL,
  `is_primary_contact` tinyint NOT NULL DEFAULT 0,
  `job_title` varchar(100) DEFAULT NULL,
  `disable_login` tinyint NOT NULL DEFAULT 0,
  `note` mediumtext,
  `address` text,
  `alternative_address` text,
  `phone` varchar(20) DEFAULT NULL,
  `alternative_phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `ssn` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `sticky_note` text,
  `skype` text,
  `enable_web_notification` tinyint NOT NULL DEFAULT 1,
  `enable_email_notification` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `last_online` datetime DEFAULT NULL,
  `requested_account_removal` tinyint NOT NULL DEFAULT 0,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
