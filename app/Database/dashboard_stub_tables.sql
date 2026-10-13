-- Stub tables required for Security_Controller + Dashboard on a partial DB import.
USE factor_db2;

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

CREATE TABLE IF NOT EXISTS `dashboards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `title` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `data` longtext,
  `sort` int NOT NULL DEFAULT 0,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `custom_widgets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `currency_symbol` varchar(20) DEFAULT NULL,
  `star_value` tinyint DEFAULT NULL,
  `payment_id` int DEFAULT NULL,
  `lead_status_id` int NOT NULL DEFAULT 0,
  `owner_id` int NOT NULL DEFAULT 0,
  `created_by` int NOT NULL DEFAULT 0,
  `sort` int NOT NULL DEFAULT 0,
  `lead_source_id` int NOT NULL DEFAULT 0,
  `last_lead_status` varchar(100) DEFAULT NULL,
  `client_groups` text,
  `currency` varchar(10) DEFAULT NULL,
  `disable_online_payment` tinyint NOT NULL DEFAULT 0,
  `vat_number` varchar(50) DEFAULT NULL,
  `is_lead` tinyint NOT NULL DEFAULT 0,
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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `notify_to` text,
  `read_by` text,
  `event` varchar(250) DEFAULT NULL,
  `project_id` int NOT NULL DEFAULT 0,
  `task_id` int NOT NULL DEFAULT 0,
  `project_comment_id` int NOT NULL DEFAULT 0,
  `ticket_id` int NOT NULL DEFAULT 0,
  `ticket_comment_id` int NOT NULL DEFAULT 0,
  `project_file_id` int NOT NULL DEFAULT 0,
  `leave_id` int NOT NULL DEFAULT 0,
  `post_id` int NOT NULL DEFAULT 0,
  `to_user_id` int NOT NULL DEFAULT 0,
  `activity_log_id` int NOT NULL DEFAULT 0,
  `client_id` int NOT NULL DEFAULT 0,
  `invoice_payment_id` int NOT NULL DEFAULT 0,
  `invoice_id` int NOT NULL DEFAULT 0,
  `estimate_id` int NOT NULL DEFAULT 0,
  `actual_message_id` int NOT NULL DEFAULT 0,
  `parent_message_id` int NOT NULL DEFAULT 0,
  `estimate_request_id` int NOT NULL DEFAULT 0,
  `order_id` int NOT NULL DEFAULT 0,
  `event_id` int NOT NULL DEFAULT 0,
  `announcement_id` int NOT NULL DEFAULT 0,
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
  `labels` text,
  `status` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text,
  `description` mediumtext,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 0,
  `location` mediumtext,
  `labels` text,
  `share_with` text,
  `deleted` tinyint NOT NULL DEFAULT 0,
  `color` varchar(15) DEFAULT NULL,
  `recurring` int NOT NULL DEFAULT 0,
  `repeat_every` int NOT NULL DEFAULT 0,
  `repeat_type` varchar(20) DEFAULT NULL,
  `no_of_cycles` int NOT NULL DEFAULT 0,
  `last_start_date` date DEFAULT NULL,
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
