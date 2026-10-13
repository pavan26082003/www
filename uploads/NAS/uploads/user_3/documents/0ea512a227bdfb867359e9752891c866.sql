-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 05, 2026 at 07:13 AM
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
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `cluster_id` int DEFAULT NULL,
  `item_type` enum('file','folder') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `cluster_id` int NOT NULL,
  `folder_id` int DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `file_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_starred` tinyint(1) DEFAULT '0',
  `is_shared` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_opened` timestamp NULL DEFAULT NULL,
  `file_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reference` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `user_id`, `cluster_id`, `folder_id`, `filename`, `original_name`, `file_path`, `file_size`, `file_type`, `file_category`, `is_starred`, `is_shared`, `created_at`, `updated_at`, `deleted_at`, `last_opened`, `file_hash`, `is_reference`) VALUES
(4, 2, 7, NULL, '6a7088cf577b2.pdf', 'Alma Linux Installations.pdf', 'user_2/documents/6a7088cf577b2.pdf', 357914, 'application/pdf', 'pdf', 0, 0, '2026-08-03 12:25:51', '2026-08-05 05:02:20', NULL, '2026-08-03 12:25:53', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(6, 2, 8, NULL, '6a708bedc3222.zip', 'bye (1).zip', 'user_2/archives/6a708bedc3222.zip', 53791, 'application/x-zip-compressed', 'other', 0, 0, '2026-08-03 12:39:09', '2026-08-05 07:09:57', NULL, NULL, '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(7, 2, 4, 4, '6a7161e2235b9.zip', 'bye.zip', 'user_2/archives/6a7161e2235b9.zip', 53791, 'application/x-zip-compressed', 'other', 0, 0, '2026-08-04 03:52:02', '2026-08-05 05:02:20', NULL, NULL, '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(8, 2, 4, 4, '6a7165eda2567.pdf', 'Alma Linux Installation(1).pdf', 'user_2/documents/6a7165eda2567.pdf', 357914, 'application/pdf', 'pdf', 0, 0, '2026-08-04 04:09:17', '2026-08-05 05:02:20', NULL, '2026-08-04 07:33:02', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(9, 2, 5, NULL, '6a7175c86471e.PNG', 's1.PNG', 'user_2/images/6a7175c86471e.PNG', 22712, 'image/png', 'image', 0, 0, '2026-08-04 05:16:56', '2026-08-05 05:02:20', NULL, NULL, '8c6028dfe7fbb4c67ee38c5ee1f19a814d62e37ce6b9974553116b376642d846', 0),
(10, 2, 4, 6, '6a7175dcdbd9d.php', 'brand.php', 'user_2/projects/modalbrand/6a7175dcdbd9d.php', 63452, 'application/octet-stream', 'other', 0, 0, '2026-08-04 05:17:16', '2026-08-05 05:02:20', NULL, NULL, '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 0),
(11, 2, 4, 6, '6a7175dce05a9.php', 'modal_formbrand.php', 'user_2/projects/modalbrand/6a7175dce05a9.php', 31016, 'application/octet-stream', 'other', 0, 0, '2026-08-04 05:17:16', '2026-08-05 05:02:20', NULL, NULL, '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 0),
(12, 2, 6, NULL, '6a719f5964df0.mp4', 'KVM Manager · Dark - Google Chrome 2026-07-03 13-05-00.mp4', 'user_2/videos/6a719f5964df0.mp4', 1330812, 'video/mp4', 'video', 0, 0, '2026-08-04 08:14:17', '2026-08-05 05:02:20', NULL, NULL, 'c62527bb784dcbf4836c602861b41f8f5074cd4f10cb062f069bdcc486822d8e', 0),
(13, 2, 7, NULL, '6a719fc000c10.docx', 'alma linux server  version details.docx', 'user_2/documents/6a719fc000c10.docx', 56252, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'other', 0, 0, '2026-08-04 08:16:00', '2026-08-05 05:02:20', NULL, '2026-08-04 10:47:53', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 0),
(15, 2, 7, NULL, '6a71af133ce3b.pptx', 'final_ppt.pptx', 'user_2/documents/6a71af133ce3b.pptx', 7763161, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'other', 0, 0, '2026-08-04 09:21:23', '2026-08-05 05:02:20', NULL, '2026-08-04 10:47:42', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 0),
(18, 2, 1, NULL, '6a71d0d2d2454.zip', 'bye (1).zip', 'user_2/archives/6a71d0d2d2454.zip', 53791, 'application/zip', 'archive', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(19, 2, 1, NULL, '6a71d0d2d6a9b.pdf', 'Alma Linux Installations.pdf', 'user_2/documents/6a71d0d2d6a9b.pdf', 357914, 'application/pdf', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(20, 2, 1, NULL, '6a71d0d2dab08.docx', 'alma linux server  version details.docx', 'user_2/documents/6a71d0d2dab08.docx', 56252, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 0),
(21, 2, 1, NULL, '6a71d0d2de072.pptx', 'final_ppt.pptx', 'user_2/documents/6a71d0d2de072.pptx', 7763161, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 0),
(22, 2, 1, NULL, '6a71d0d2e2ee7.PNG', 's1.PNG', 'user_2/images/6a71d0d2e2ee7.PNG', 22712, 'image/png', 'image', 0, 0, '2026-08-04 11:45:22', '2026-08-05 07:10:20', NULL, NULL, '8c6028dfe7fbb4c67ee38c5ee1f19a814d62e37ce6b9974553116b376642d846', 0),
(23, 2, 1, 8, '6a71d0d2e903f.pdf', 'Alma Linux Installation(1).pdf', 'user_2/projects/6a71d0d2e903f.pdf', 357914, 'application/pdf', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(24, 2, 1, 8, '6a71d0d2ecc4a.zip', 'bye.zip', 'user_2/projects/6a71d0d2ecc4a.zip', 53791, 'application/zip', 'archive', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(25, 2, 1, 9, '6a71d0d2f1d1c.php', 'brand.php', 'user_2/projects/6a71d0d2f1d1c.php', 63452, 'text/html', 'code', 0, 0, '2026-08-04 11:45:23', '2026-08-05 05:02:20', NULL, NULL, '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 0),
(26, 2, 1, 9, '6a71d0d308cdd.php', 'modal_formbrand.php', 'user_2/projects/6a71d0d308cdd.php', 31016, 'text/html', 'code', 0, 0, '2026-08-04 11:45:23', '2026-08-05 05:02:20', NULL, NULL, '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 0),
(27, 2, 1, NULL, '6a71d0d30d309.mp4', 'KVM Manager · Dark - Google Chrome 2026-07-03 13-05-00.mp4', 'user_2/videos/6a71d0d30d309.mp4', 1330812, 'video/mp4', 'video', 0, 0, '2026-08-04 11:45:23', '2026-08-05 05:02:20', NULL, NULL, 'c62527bb784dcbf4836c602861b41f8f5074cd4f10cb062f069bdcc486822d8e', 0),
(29, 2, 1, NULL, '6a71d257a7b14.jpg', 's2.jpg', 'user_2/documents/6a71d257a7b14.jpg', 23131, 'image/jpeg', 'image', 0, 0, '2026-08-04 11:51:51', '2026-08-05 05:02:20', NULL, NULL, '10212480e4e6dc03c9704b30a7c74d1cbfac30ef05beb4b606671551994f4003', 0),
(30, 2, 1, NULL, '6a71d49a148f6.txt', 'commands.txt', 'user_2/computers/6a71d49a148f6.txt', 105, 'text/plain', 'document', 0, 0, '2026-08-04 12:01:30', '2026-08-05 05:02:20', NULL, NULL, '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 0),
(31, 2, 1, NULL, '6a71d49a19fe5.jpg', 's2 (1).jpg', 'user_2/my-drive/6a71d49a19fe5.jpg', 23131, 'image/jpeg', 'image', 0, 0, '2026-08-04 12:01:30', '2026-08-05 05:02:20', NULL, NULL, '10212480e4e6dc03c9704b30a7c74d1cbfac30ef05beb4b606671551994f4003', 0),
(32, 2, 1, NULL, '6a71da228b4e0.jpg', 's2 (2).jpg', 'user_2/images/6a71da228b4e0.jpg', 23131, 'image/jpeg', 'image', 0, 0, '2026-08-04 12:25:06', '2026-08-05 05:02:20', NULL, NULL, '10212480e4e6dc03c9704b30a7c74d1cbfac30ef05beb4b606671551994f4003', 0),
(33, 2, 3, NULL, '6a71da228b4e0.jpg', 's2 (2).jpg', 'user_2/computers/6a71da228b4e0.jpg', 23131, 'image/jpeg', 'image', 0, 0, '2026-08-04 12:25:06', '2026-08-05 05:02:20', NULL, NULL, '10212480e4e6dc03c9704b30a7c74d1cbfac30ef05beb4b606671551994f4003', 0),
(35, 2, 3, NULL, '6a71da60c6597.txt', 'commands.txt', 'user_2/computers/6a71da60c6597.txt', 105, 'text/plain', 'document', 0, 0, '2026-08-04 12:26:08', '2026-08-05 05:02:20', NULL, NULL, '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 0),
(52, 3, 7, NULL, '6a72d4305674c.pdf', 'alma linux vm sever.pdf', 'user_3/documents/6a72d4305674c.pdf', 395406, 'application/pdf', 'pdf', 0, 0, '2026-08-05 06:12:00', '2026-08-05 06:12:00', NULL, NULL, NULL, 0),
(54, 3, 4, 11, '6a72d50c01282.docx', 'Alma Linux Installations.docx', 'user_3/documents/6a72d50c01282.docx', 58134, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'other', 0, 0, '2026-08-05 06:15:40', '2026-08-05 06:15:40', NULL, NULL, NULL, 0),
(55, 3, 5, NULL, '6a72d51de0b68.jpg', 's2.jpg', 'user_3/images/6a72d51de0b68.jpg', 23131, 'image/jpeg', 'image', 0, 0, '2026-08-05 06:15:57', '2026-08-05 06:15:57', NULL, NULL, NULL, 0),
(56, 3, 7, NULL, '6a72d525197dc.xlsx', 'IOT  SIM LIST new.xlsx', 'user_3/documents/6a72d525197dc.xlsx', 3032384, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'other', 0, 0, '2026-08-05 06:16:05', '2026-08-05 06:16:05', NULL, NULL, NULL, 0),
(57, 3, 7, NULL, '6a72d52de1cc7.php', 'view_page.php', 'user_3/documents/6a72d52de1cc7.php', 19227, 'application/octet-stream', 'other', 0, 0, '2026-08-05 06:16:13', '2026-08-05 06:16:13', NULL, NULL, NULL, 0),
(58, 3, 7, NULL, '6a72d53ab78f9.pptx', 'final_ppt (1).pptx', 'user_3/documents/6a72d53ab78f9.pptx', 7763161, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'other', 0, 0, '2026-08-05 06:16:26', '2026-08-05 06:16:26', NULL, NULL, NULL, 0);

--
-- Triggers `files`
--
DELIMITER $$
CREATE TRIGGER `update_storage_used_delete` AFTER DELETE ON `files` FOR EACH ROW BEGIN
    UPDATE users 
    SET storage_used = storage_used - OLD.file_size 
    WHERE id = OLD.user_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_storage_used_insert` AFTER INSERT ON `files` FOR EACH ROW BEGIN
    UPDATE users 
    SET storage_used = storage_used + NEW.file_size 
    WHERE id = NEW.user_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_storage_used_update` AFTER UPDATE ON `files` FOR EACH ROW BEGIN
    IF OLD.file_size != NEW.file_size THEN
        UPDATE users 
        SET storage_used = storage_used + (NEW.file_size - OLD.file_size)
        WHERE id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `file_categories`
--

CREATE TABLE `file_categories` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fa-file',
  `mime_types` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `extensions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `file_categories`
--

INSERT INTO `file_categories` (`id`, `name`, `slug`, `icon`, `mime_types`, `extensions`, `created_at`) VALUES
(1, 'Images', 'images', 'fa-image', 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml', 'jpg,jpeg,png,gif,webp,svg', '2026-07-27 11:22:48'),
(2, 'Videos', 'videos', 'fa-video', 'video/mp4,video/webm,video/avi,video/mkv', 'mp4,webm,avi,mkv,mov,wmv', '2026-07-27 11:22:48'),
(3, 'Documents', 'documents', 'fa-file-alt', 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain', 'pdf,doc,docx,txt,rtf,odt', '2026-07-27 11:22:48'),
(4, 'Spreadsheets', 'spreadsheets', 'fa-file-excel', 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'xls,xlsx,csv,ods', '2026-07-27 11:22:48'),
(5, 'Presentations', 'presentations', 'fa-file-powerpoint', 'application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation', 'ppt,pptx,odp', '2026-07-27 11:22:48'),
(6, 'Archives', 'archives', 'fa-file-archive', 'application/zip,application/x-rar-compressed,application/x-7z-compressed', 'zip,rar,7z,tar,gz', '2026-07-27 11:22:48'),
(7, 'Audio', 'audio', 'fa-file-audio', 'audio/mpeg,audio/wav,audio/flac', 'mp3,wav,flac,aac,ogg', '2026-07-27 11:22:48'),
(8, 'Code', 'code', 'fa-code', 'text/javascript,text/css,text/html,application/json', 'js,css,html,json,py,java,cpp,php', '2026-07-27 11:22:48');

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `cluster_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `is_starred` tinyint(1) DEFAULT '0',
  `is_shared` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_opened` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `user_id`, `cluster_id`, `name`, `parent_id`, `is_starred`, `is_shared`, `created_at`, `updated_at`, `deleted_at`, `last_opened`) VALUES
(2, 2, 4, 'bye2', NULL, 0, 0, '2026-08-03 10:58:01', '2026-08-04 04:22:26', NULL, NULL),
(4, 2, 4, 'b1', 2, 0, 0, '2026-08-04 03:51:52', '2026-08-04 04:22:26', NULL, NULL),
(6, 2, 4, 'modalbrand', NULL, 0, 0, '2026-08-04 05:17:16', '2026-08-04 08:24:04', NULL, NULL),
(7, 2, 1, 'bye2', NULL, 0, 0, '2026-08-04 11:45:22', '2026-08-04 11:45:22', NULL, NULL),
(8, 2, 1, 'b1', 7, 0, 0, '2026-08-04 11:45:22', '2026-08-04 11:45:22', NULL, NULL),
(9, 2, 1, 'modalbrand', NULL, 0, 0, '2026-08-04 11:45:22', '2026-08-04 11:45:22', NULL, NULL),
(11, 3, 4, 'hi', NULL, 0, 0, '2026-08-05 06:15:30', '2026-08-05 06:15:30', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shared_items`
--

CREATE TABLE `shared_items` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_type` enum('file','folder') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int NOT NULL,
  `shared_with_user_id` int NOT NULL,
  `permissions` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'view',
  `share_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shared_items`
--

INSERT INTO `shared_items` (`id`, `user_id`, `item_type`, `item_id`, `shared_with_user_id`, `permissions`, `share_link`, `expires_at`, `created_at`) VALUES
(1, 2, 'file', 4, 3, 'view', NULL, NULL, '2026-08-04 05:52:57'),
(2, 2, 'folder', 2, 3, 'view', NULL, NULL, '2026-08-04 06:01:47'),
(3, 2, 'folder', 6, 3, 'view', NULL, NULL, '2026-08-04 06:21:54'),
(4, 2, 'file', 10, 3, 'view', NULL, NULL, '2026-08-04 06:21:54'),
(5, 2, 'file', 11, 3, 'view', NULL, NULL, '2026-08-04 06:21:54'),
(10, 2, 'file', 2, 2, 'view', NULL, NULL, '2026-08-04 07:14:58'),
(11, 2, 'folder', 3, 3, 'full', NULL, NULL, '2026-08-04 08:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `storage_clusters`
--

CREATE TABLE `storage_clusters` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fa-folder',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) DEFAULT '0',
  `is_default` tinyint(1) DEFAULT '0',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_clusters`
--

INSERT INTO `storage_clusters` (`id`, `user_id`, `name`, `slug`, `icon`, `description`, `is_system`, `is_default`, `sort_order`, `created_at`) VALUES
(1, 0, 'My Drive', 'my-drive', 'fa-cloud', 'Your personal storage space', 1, 1, 1, '2026-07-27 11:22:48'),
(2, 0, 'Shared with Me', 'shared', 'fa-share-alt', 'Files and folders shared with you', 1, 0, 2, '2026-07-27 11:22:48'),
(3, 0, 'Computers', 'computers', 'fa-desktop', 'Files from your connected devices', 1, 0, 3, '2026-07-27 11:22:48'),
(4, 0, 'Projects', 'projects', 'fa-folder-open', 'Your project files', 0, 0, 4, '2026-07-27 11:22:48'),
(5, 0, 'Images', 'images', 'fa-image', 'All your images', 0, 0, 5, '2026-07-27 11:22:48'),
(6, 0, 'Videos', 'videos', 'fa-video', 'All your videos', 0, 0, 6, '2026-07-27 11:22:48'),
(7, 0, 'Documents', 'documents', 'fa-file-alt', 'All your documents', 0, 0, 7, '2026-07-27 11:22:48'),
(8, 0, 'Archives', 'archives', 'fa-file-archive', 'ZIP, RAR and other archives', 0, 0, 8, '2026-07-27 11:22:48'),
(9, 0, 'Starred', 'starred', 'fa-star', 'Your starred items', 1, 0, 9, '2026-07-27 11:22:48'),
(10, 0, 'Trash', 'trash', 'fa-trash', 'Deleted items', 1, 0, 10, '2026-07-27 11:22:48');

-- --------------------------------------------------------

--
-- Table structure for table `sync_folders`
--

CREATE TABLE `sync_folders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `local_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `folder_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sync_enabled` tinyint(1) DEFAULT '1',
  `is_auto` tinyint(1) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sync_logs`
--

CREATE TABLE `sync_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `sync_type` enum('upload','download','skip','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sync_queue`
--

CREATE TABLE `sync_queue` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_type` enum('file','folder') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int NOT NULL,
  `action` enum('create','update','delete','skip') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','completed','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sync_queue`
--

INSERT INTO `sync_queue` (`id`, `user_id`, `session_id`, `item_type`, `item_id`, `action`, `status`, `error_message`, `created_at`, `processed_at`) VALUES
(1, 2, 'sync_6a707461669eb2.86078031', 'file', 2, 'create', 'pending', NULL, '2026-08-03 10:58:41', NULL),
(2, 2, 'sync_6a707461669eb2.86078031', 'file', 1, 'create', 'pending', NULL, '2026-08-03 10:58:41', NULL),
(3, 2, 'sync_6a707461669eb2.86078031', 'folder', 2, 'create', 'pending', NULL, '2026-08-03 10:58:41', NULL),
(4, 2, 'sync_6a707461669eb2.86078031', 'folder', 1, 'create', 'pending', NULL, '2026-08-03 10:58:41', NULL),
(5, 2, 'sync_6a707469e887d4.57745813', 'file', 2, 'create', 'pending', NULL, '2026-08-03 10:58:49', NULL),
(6, 2, 'sync_6a707469e887d4.57745813', 'file', 1, 'create', 'pending', NULL, '2026-08-03 10:58:49', NULL),
(7, 2, 'sync_6a707469e887d4.57745813', 'folder', 2, 'create', 'pending', NULL, '2026-08-03 10:58:49', NULL),
(8, 2, 'sync_6a707469e887d4.57745813', 'folder', 1, 'create', 'pending', NULL, '2026-08-03 10:58:49', NULL),
(9, 2, 'sync_6a7168eed35d87.86059467', 'file', 2, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(10, 2, 'sync_6a7168eed35d87.86059467', 'file', 7, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(11, 2, 'sync_6a7168eed35d87.86059467', 'file', 8, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(12, 2, 'sync_6a7168eed35d87.86059467', 'file', 4, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(13, 2, 'sync_6a7168eed35d87.86059467', 'file', 6, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(14, 2, 'sync_6a7168eed35d87.86059467', 'folder', 2, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(15, 2, 'sync_6a7168eed35d87.86059467', 'folder', 4, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(16, 2, 'sync_6a7168eed35d87.86059467', 'folder', 3, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(17, 2, 'sync_6a7168eed35d87.86059467', 'folder', 1, 'create', 'pending', NULL, '2026-08-04 04:22:06', NULL),
(18, 2, 'sync_6a71735cebf273.73329455', 'file', 2, 'create', 'pending', NULL, '2026-08-04 05:06:36', NULL),
(19, 2, 'sync_6a71735cebf273.73329455', 'file', 7, 'create', 'pending', NULL, '2026-08-04 05:06:36', NULL),
(20, 2, 'sync_6a71735cebf273.73329455', 'file', 8, 'create', 'pending', NULL, '2026-08-04 05:06:36', NULL),
(21, 2, 'sync_6a71735cebf273.73329455', 'file', 4, 'create', 'pending', NULL, '2026-08-04 05:06:37', NULL),
(22, 2, 'sync_6a71735cebf273.73329455', 'file', 6, 'create', 'pending', NULL, '2026-08-04 05:06:37', NULL),
(23, 2, 'sync_6a71735cebf273.73329455', 'folder', 2, 'create', 'pending', NULL, '2026-08-04 05:06:37', NULL),
(24, 2, 'sync_6a71735cebf273.73329455', 'folder', 4, 'create', 'pending', NULL, '2026-08-04 05:06:37', NULL),
(25, 2, 'sync_6a71735cebf273.73329455', 'folder', 3, 'create', 'pending', NULL, '2026-08-04 05:06:37', NULL),
(26, 2, 'sync_6a71735cebf273.73329455', 'folder', 1, 'create', 'pending', NULL, '2026-08-04 05:06:37', NULL),
(27, 2, 'sync_6a71763ae688b6.35818286', 'file', 10, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(28, 2, 'sync_6a71763ae688b6.35818286', 'file', 11, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(29, 2, 'sync_6a71763ae688b6.35818286', 'file', 9, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(30, 2, 'sync_6a71763ae688b6.35818286', 'file', 2, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(31, 2, 'sync_6a71763ae688b6.35818286', 'file', 7, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(32, 2, 'sync_6a71763ae688b6.35818286', 'file', 8, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(33, 2, 'sync_6a71763ae688b6.35818286', 'file', 4, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(34, 2, 'sync_6a71763ae688b6.35818286', 'file', 6, 'create', 'pending', NULL, '2026-08-04 05:18:50', NULL),
(35, 2, 'sync_6a71763ae688b6.35818286', 'folder', 6, 'create', 'pending', NULL, '2026-08-04 05:18:51', NULL),
(36, 2, 'sync_6a71763ae688b6.35818286', 'folder', 5, 'create', 'pending', NULL, '2026-08-04 05:18:51', NULL),
(37, 2, 'sync_6a71763ae688b6.35818286', 'folder', 2, 'create', 'pending', NULL, '2026-08-04 05:18:51', NULL),
(38, 2, 'sync_6a71763ae688b6.35818286', 'folder', 4, 'create', 'pending', NULL, '2026-08-04 05:18:51', NULL),
(39, 2, 'sync_6a71763ae688b6.35818286', 'folder', 3, 'create', 'pending', NULL, '2026-08-04 05:18:51', NULL),
(40, 2, 'sync_6a71763ae688b6.35818286', 'folder', 1, 'create', 'pending', NULL, '2026-08-04 05:18:51', NULL),
(41, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 15, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(42, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 10, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(43, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 11, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(44, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 13, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(45, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 12, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(46, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 8, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(47, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 9, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(48, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 7, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(49, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 4, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(50, 2, 'sync_6a71bb00d1aec7.39304054', 'file', 6, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(51, 2, 'sync_6a71bb00d1aec7.39304054', 'folder', 6, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(52, 2, 'sync_6a71bb00d1aec7.39304054', 'folder', 2, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(53, 2, 'sync_6a71bb00d1aec7.39304054', 'folder', 4, 'create', 'pending', NULL, '2026-08-04 10:12:16', NULL),
(54, 2, 'sync_6a71bb16ec7630.16108271', 'file', 15, 'create', 'pending', NULL, '2026-08-04 10:12:38', NULL),
(55, 2, 'sync_6a71bb16ec7630.16108271', 'file', 10, 'create', 'pending', NULL, '2026-08-04 10:12:38', NULL),
(56, 2, 'sync_6a71bb16ec7630.16108271', 'file', 11, 'create', 'pending', NULL, '2026-08-04 10:12:38', NULL),
(57, 2, 'sync_6a71bb16ec7630.16108271', 'file', 13, 'create', 'pending', NULL, '2026-08-04 10:12:38', NULL),
(58, 2, 'sync_6a71bb16ec7630.16108271', 'file', 12, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(59, 2, 'sync_6a71bb16ec7630.16108271', 'file', 8, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(60, 2, 'sync_6a71bb16ec7630.16108271', 'file', 9, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(61, 2, 'sync_6a71bb16ec7630.16108271', 'file', 7, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(62, 2, 'sync_6a71bb16ec7630.16108271', 'file', 4, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(63, 2, 'sync_6a71bb16ec7630.16108271', 'file', 6, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(64, 2, 'sync_6a71bb16ec7630.16108271', 'folder', 6, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(65, 2, 'sync_6a71bb16ec7630.16108271', 'folder', 2, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(66, 2, 'sync_6a71bb16ec7630.16108271', 'folder', 4, 'create', 'pending', NULL, '2026-08-04 10:12:39', NULL),
(67, 2, 'sync_6a71beaa152029.23072089', 'file', 15, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(68, 2, 'sync_6a71beaa152029.23072089', 'file', 10, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(69, 2, 'sync_6a71beaa152029.23072089', 'file', 11, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(70, 2, 'sync_6a71beaa152029.23072089', 'file', 13, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(71, 2, 'sync_6a71beaa152029.23072089', 'file', 12, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(72, 2, 'sync_6a71beaa152029.23072089', 'file', 8, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(73, 2, 'sync_6a71beaa152029.23072089', 'file', 9, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(74, 2, 'sync_6a71beaa152029.23072089', 'file', 7, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(75, 2, 'sync_6a71beaa152029.23072089', 'file', 4, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(76, 2, 'sync_6a71beaa152029.23072089', 'file', 6, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(77, 2, 'sync_6a71beaa152029.23072089', 'folder', 6, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(78, 2, 'sync_6a71beaa152029.23072089', 'folder', 2, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL),
(79, 2, 'sync_6a71beaa152029.23072089', 'folder', 4, 'create', 'pending', NULL, '2026-08-04 10:27:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sync_sessions`
--

CREATE TABLE `sync_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('running','completed','failed','idle') COLLATE utf8mb4_unicode_ci DEFAULT 'idle',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `files_synced` int DEFAULT '0',
  `total_files` int DEFAULT '0',
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sync_sessions`
--

INSERT INTO `sync_sessions` (`id`, `user_id`, `session_id`, `status`, `started_at`, `completed_at`, `files_synced`, `total_files`, `last_sync_at`, `error_message`) VALUES
(1, 2, 'sync_6a704f6dd31701.87876713', 'completed', '2026-08-03 08:21:01', '2026-08-03 08:21:01', 0, 0, '2026-08-03 08:21:01', NULL),
(2, 2, 'sync_6a7059e5375217.60138712', 'completed', '2026-08-03 09:05:41', '2026-08-03 09:05:41', 0, 0, '2026-08-03 09:05:41', NULL),
(3, 2, 'sync_6a7059e99b61b3.87935404', 'completed', '2026-08-03 09:05:45', '2026-08-03 09:05:45', 0, 0, '2026-08-03 09:05:45', NULL),
(4, 2, 'sync_6a7067dc5e6250.72496650', 'completed', '2026-08-03 10:05:16', '2026-08-03 10:05:16', 0, 0, '2026-08-03 10:05:16', NULL),
(5, 2, 'sync_6a707461669eb2.86078031', 'completed', '2026-08-03 10:58:41', '2026-08-03 10:58:41', 0, 0, '2026-08-03 10:58:41', NULL),
(6, 2, 'sync_6a707469e887d4.57745813', 'completed', '2026-08-03 10:58:49', '2026-08-03 10:58:49', 0, 0, '2026-08-03 10:58:49', NULL),
(7, 2, 'sync_6a7168eed35d87.86059467', 'completed', '2026-08-04 04:22:06', '2026-08-04 04:22:06', 0, 0, '2026-08-04 04:22:06', NULL),
(8, 2, 'sync_6a71735cebf273.73329455', 'completed', '2026-08-04 05:06:36', '2026-08-04 05:06:37', 0, 0, '2026-08-04 05:06:37', NULL),
(9, 2, 'sync_6a71763ae688b6.35818286', 'completed', '2026-08-04 05:18:50', '2026-08-04 05:18:51', 0, 0, '2026-08-04 05:18:51', NULL),
(10, 2, 'sync_6a71bb00d1aec7.39304054', 'completed', '2026-08-04 10:12:16', '2026-08-04 10:12:16', 0, 0, '2026-08-04 10:12:16', NULL),
(11, 2, 'sync_6a71bb16ec7630.16108271', 'completed', '2026-08-04 10:12:38', '2026-08-04 10:12:39', 0, 0, '2026-08-04 10:12:39', NULL),
(12, 2, 'sync_6a71beaa152029.23072089', 'completed', '2026-08-04 10:27:54', '2026-08-04 10:27:54', 0, 0, '2026-08-04 10:27:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sync_status`
--

CREATE TABLE `sync_status` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `file_id` int DEFAULT NULL,
  `local_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `modified_time` datetime NOT NULL,
  `sync_status` enum('pending','synced','uploading','downloading','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `sync_direction` enum('upload','download','none') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'none',
  `last_sync` datetime DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `retry_count` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
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

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `storage_limit`, `storage_used`, `created_at`, `last_login`, `is_active`) VALUES
(2, 'admin', 'admin@example.com', '$2y$10$atDnBaiAO5HhVIhG6l8rI.JyaPpU5bkQpln9iNf/fCBuXFV/dsVGW', 10737418240, 40525492, '2026-08-03 05:02:58', NULL, 1),
(3, 'prafulsshah', 'prafulsshah@yahoo.com', '$2y$10$OZwjn8mjRWcnAw80amvUOeIb0HHqzEQRm48S.M5OkuWzPbk9b9B3q', 10737418240, 22450718, '2026-08-04 05:33:47', NULL, 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `user_clusters`
--

CREATE TABLE `user_clusters` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `cluster_id` int NOT NULL,
  `root_folder_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_clusters`
--

INSERT INTO `user_clusters` (`id`, `user_id`, `cluster_id`, `root_folder_id`, `is_active`, `created_at`, `updated_at`) VALUES
(11, 2, 8, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(12, 2, 3, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(13, 2, 7, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(14, 2, 5, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(15, 2, 1, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(16, 2, 4, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(17, 2, 2, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(18, 2, 9, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(19, 2, 10, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(20, 2, 6, NULL, 1, '2026-08-03 05:02:58', '2026-08-03 05:02:58'),
(23, 3, 1, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(24, 3, 2, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(25, 3, 3, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(26, 3, 4, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(27, 3, 5, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(28, 3, 6, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(29, 3, 7, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(30, 3, 8, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(31, 3, 9, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(32, 3, 10, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_starred_items`
--

CREATE TABLE `user_starred_items` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_type` enum('file','folder') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_starred_items`
--

INSERT INTO `user_starred_items` (`id`, `user_id`, `item_type`, `item_id`, `created_at`) VALUES
(2, 3, 'folder', 2, '2026-08-04 07:02:26'),
(4, 3, 'file', 10, '2026-08-04 07:18:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cluster_id` (`cluster_id`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `idx_user_cluster` (`user_id`,`cluster_id`),
  ADD KEY `idx_user_deleted` (`user_id`,`deleted_at`),
  ADD KEY `idx_category` (`file_category`);

--
-- Indexes for table `file_categories`
--
ALTER TABLE `file_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cluster_id` (`cluster_id`),
  ADD KEY `idx_user_cluster` (`user_id`,`cluster_id`),
  ADD KEY `idx_user_deleted` (`user_id`,`deleted_at`),
  ADD KEY `idx_parent` (`parent_id`);

--
-- Indexes for table `shared_items`
--
ALTER TABLE `shared_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_shared_with` (`shared_with_user_id`);

--
-- Indexes for table `storage_clusters`
--
ALTER TABLE `storage_clusters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `sync_folders`
--
ALTER TABLE `sync_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_path` (`user_id`,`local_path`(255)),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `sync_queue`
--
ALTER TABLE `sync_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_status` (`session_id`,`status`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `sync_sessions`
--
ALTER TABLE `sync_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `sync_status`
--
ALTER TABLE `sync_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_file_id` (`file_id`),
  ADD KEY `idx_sync_status` (`sync_status`),
  ADD KEY `idx_file_hash` (`file_hash`),
  ADD KEY `idx_local_path` (`local_path`(255));

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_clusters`
--
ALTER TABLE `user_clusters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_cluster` (`user_id`,`cluster_id`),
  ADD KEY `cluster_id` (`cluster_id`);

--
-- Indexes for table `user_starred_items`
--
ALTER TABLE `user_starred_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_item` (`user_id`,`item_type`,`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `file_categories`
--
ALTER TABLE `file_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shared_items`
--
ALTER TABLE `shared_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `storage_clusters`
--
ALTER TABLE `storage_clusters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

--
-- AUTO_INCREMENT for table `sync_folders`
--
ALTER TABLE `sync_folders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sync_logs`
--
ALTER TABLE `sync_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sync_queue`
--
ALTER TABLE `sync_queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `sync_sessions`
--
ALTER TABLE `sync_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sync_status`
--
ALTER TABLE `sync_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_clusters`
--
ALTER TABLE `user_clusters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_starred_items`
--
ALTER TABLE `user_starred_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`cluster_id`) REFERENCES `storage_clusters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_3` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_2` FOREIGN KEY (`cluster_id`) REFERENCES `storage_clusters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shared_items`
--
ALTER TABLE `shared_items`
  ADD CONSTRAINT `shared_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shared_items_ibfk_2` FOREIGN KEY (`shared_with_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sync_queue`
--
ALTER TABLE `sync_queue`
  ADD CONSTRAINT `sync_queue_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sync_sessions`
--
ALTER TABLE `sync_sessions`
  ADD CONSTRAINT `sync_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_clusters`
--
ALTER TABLE `user_clusters`
  ADD CONSTRAINT `user_clusters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_clusters_ibfk_2` FOREIGN KEY (`cluster_id`) REFERENCES `storage_clusters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_starred_items`
--
ALTER TABLE `user_starred_items`
  ADD CONSTRAINT `user_starred_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
