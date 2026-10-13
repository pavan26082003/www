-- Recreate core tables a partial dump often omits.
USE factor_db2;

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
SELECT 'language', 'english', 'app', 0
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `setting_name` = 'language');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `user_type` varchar(20) NOT NULL DEFAULT 'staff',
  `brand_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `user_type_1` varchar(50) DEFAULT NULL,
  `is_admin` tinyint NOT NULL DEFAULT 0,
  `role_id` int NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `email_verified` tinyint NOT NULL DEFAULT 0,
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
  `first_login_at` datetime DEFAULT NULL,
  `requested_account_removal` tinyint NOT NULL DEFAULT 0,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `permissions` mediumtext,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `members` text,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `item_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text,
  `description` mediumtext,
  `project_id` int NOT NULL DEFAULT 0,
  `milestone_id` int NOT NULL DEFAULT 0,
  `assigned_to` int NOT NULL DEFAULT 0,
  `deadline` date DEFAULT NULL,
  `labels` text,
  `points` tinyint NOT NULL DEFAULT 1,
  `status` varchar(20) DEFAULT NULL,
  `status_id` int NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `collaborators` text,
  `sort` int NOT NULL DEFAULT 0,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `project_time` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL DEFAULT 0,
  `user_id` int NOT NULL DEFAULT 0,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `note` text,
  `task_id` int NOT NULL DEFAULT 0,
  `hours` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `start_date` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `client_id` int NOT NULL DEFAULT 0,
  `created_date` date DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT NULL,
  `labels` text,
  `price` double NOT NULL DEFAULT 0,
  `starred_by` text,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `efactorproduct` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `module_type` varchar(50) DEFAULT NULL,
  `internet` varchar(10) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `category_id` int NOT NULL DEFAULT 0,
  `show_in_client_portal` tinyint NOT NULL DEFAULT 0,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `notify_to` text,
  `read_by` text,
  `event` varchar(250) DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `created_at` datetime DEFAULT NULL,
  `from_user_id` int NOT NULL DEFAULT 0,
  `to_user_id` int NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT NULL,
  `message_id` int NOT NULL DEFAULT 0,
  `files` text,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `to_do` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `title` text,
  `description` text,
  `status` varchar(20) DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text,
  `description` mediumtext,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 0,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
