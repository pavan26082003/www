CREATE TABLE IF NOT EXISTS `virtual_machines` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `os_type` varchar(50) NOT NULL DEFAULT 'linux',
  `cpu_cores` int(11) NOT NULL DEFAULT '2',
  `memory_mb` int(11) NOT NULL DEFAULT '2048',
  `disk_size_gb` decimal(10,2) NOT NULL DEFAULT '20.00',
  `disk_path` varchar(255) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `server_ip` varchar(45) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `state` varchar(20) NOT NULL DEFAULT 'stopped',
  `application_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_vm_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `virtual_disks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vm_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `disk_path` varchar(255) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `format` varchar(20) NOT NULL DEFAULT 'qcow2',
  `allocated_gb` decimal(10,2) NOT NULL DEFAULT '20.00',
  `used_gb` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `uq_disk_path` (`disk_path`), KEY `idx_vm_id` (`vm_id`),
  CONSTRAINT `fk_disk_vm` FOREIGN KEY (`vm_id`) REFERENCES `virtual_machines` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
