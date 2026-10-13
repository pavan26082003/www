USE factor_db2;

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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
