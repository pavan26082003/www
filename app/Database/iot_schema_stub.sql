USE factor_db2;

CREATE TABLE IF NOT EXISTS `productcats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `img_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `brandlogo` varchar(500) DEFAULT NULL,
  `img_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `title` text,
  `description` mediumtext,
  `project_id` int NOT NULL DEFAULT 0,
  `client_id` int NOT NULL DEFAULT 0,
  `user_id` int NOT NULL DEFAULT 0,
  `labels` text,
  `files` text,
  `is_public` tinyint NOT NULL DEFAULT 0,
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

SET @db := DATABASE();

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='vendor_name')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `vendor_name` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='vendor_type')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `vendor_type` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='access_token')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `access_token` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='node_id')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `node_id` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='device_type')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `device_type` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='name')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `name` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='power')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `power` VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='model_name')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `model_name` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='model_number')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `model_number` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='add_gateway')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `add_gateway` VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='is_added')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `is_added` VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='prod_category')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `prod_category` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='vendor_id')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `vendor_id` VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='efactorproduct' AND COLUMN_NAME='brand_logo')=0,
  'ALTER TABLE `efactorproduct` ADD COLUMN `brand_logo` VARCHAR(500) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
