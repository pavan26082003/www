-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 13, 2026 at 11:49 AM
-- Server version: 8.0.46
-- PHP Version: 8.2.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `file_manager_v4`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `efactor_user_id` bigint DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_limit` bigint DEFAULT '10737418240',
  `storage_used` bigint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `efactor_user_id`, `username`, `email`, `password_hash`, `storage_limit`, `storage_used`, `created_at`, `last_login`, `is_active`) VALUES
(2, NULL, 'admin', 'admin@example.com', '$2y$10$atDnBaiAO5HhVIhG6l8rI.JyaPpU5bkQpln9iNf/fCBuXFV/dsVGW', 10737418240, 54260738, '2026-08-03 05:02:58', NULL, 1),
(3, 177, 'prafulsshah', 'prafulsshah@yahoo.com', '$2y$10$OZwjn8mjRWcnAw80amvUOeIb0HHqzEQRm48S.M5OkuWzPbk9b9B3q', 10737418240, 64211280, '2026-08-04 05:33:47', '2026-08-13 10:06:58', 1),
(4, NULL, 'nastest1', 'nastest1@example.com', '$2y$10$1IP2jiTSg.M.qkSZThiwr..yuWILztB.34VLxNwbp2RjitNWJVGza', 10737418240, 0, '2026-08-12 04:05:38', NULL, 1),
(5, 2, 'kumar', 'balasai006@gmail.com', '$2y$10$OOZmBWQZYr3fcUPUWZoT2eUnlq7/tEcHGgyxmvYqoND.Jx7OJboWa', 10737418240, 832740, '2026-08-13 04:03:24', '2026-08-13 04:11:53', 1);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `auto_add_user_clusters` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO user_clusters (user_id, cluster_id)
    SELECT NEW.id, sc.id
    FROM storage_clusters sc;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_users_efactor_user_id` (`efactor_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
