USE factor_db2;

CREATE TABLE IF NOT EXISTS `vendorproducts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT 0,
  `brand` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `product_type` varchar(255) DEFAULT NULL,
  `modelname` varchar(255) DEFAULT NULL,
  `modelno` varchar(255) DEFAULT NULL,
  `description` text,
  `model_version` varchar(100) DEFAULT NULL,
  `Batch` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `modelname` (`modelname`),
  KEY `modelno` (`modelno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
