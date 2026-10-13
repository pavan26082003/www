-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 19, 2026 at 05:31 AM
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
  `file_path` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(10, 2, 4, 6, '6a7175dcdbd9d.php', 'brand.php', 'user_2/projects/modalbrand/6a7175dcdbd9d.php', 63452, 'application/octet-stream', 'other', 0, 0, '2026-08-04 05:17:16', '2026-08-05 05:02:20', NULL, NULL, '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 0),
(11, 2, 4, 6, '6a7175dce05a9.php', 'modal_formbrand.php', 'user_2/projects/modalbrand/6a7175dce05a9.php', 31016, 'application/octet-stream', 'other', 0, 0, '2026-08-04 05:17:16', '2026-08-06 09:15:41', NULL, '2026-08-06 09:15:41', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 0),
(12, 2, 6, NULL, '6a719f5964df0.mp4', 'KVM Manager · Dark - Google Chrome 2026-07-03 13-05-00.mp4', 'user_2/videos/6a719f5964df0.mp4', 1330812, 'video/mp4', 'video', 0, 0, '2026-08-04 08:14:17', '2026-08-05 05:02:20', NULL, NULL, 'c62527bb784dcbf4836c602861b41f8f5074cd4f10cb062f069bdcc486822d8e', 0),
(13, 2, 7, NULL, '6a719fc000c10.docx', 'alma linux server  version details.docx', 'user_2/documents/6a719fc000c10.docx', 56252, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'other', 0, 0, '2026-08-04 08:16:00', '2026-08-05 05:02:20', NULL, '2026-08-04 10:47:53', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 0),
(15, 2, 7, NULL, '6a71af133ce3b.pptx', 'final_ppt.pptx', 'user_2/documents/6a71af133ce3b.pptx', 7763161, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'other', 0, 0, '2026-08-04 09:21:23', '2026-08-05 05:02:20', NULL, '2026-08-04 10:47:42', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 0),
(18, 2, 1, NULL, '6a71d0d2d2454.zip', 'bye (1).zip', 'user_2/archives/6a71d0d2d2454.zip', 53791, 'application/zip', 'archive', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(19, 2, 1, NULL, '6a71d0d2d6a9b.pdf', 'Alma Linux Installations.pdf', 'user_2/documents/6a71d0d2d6a9b.pdf', 357914, 'application/pdf', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(20, 2, 1, NULL, '6a71d0d2dab08.docx', 'alma linux server  version details.docx', 'user_2/documents/6a71d0d2dab08.docx', 56252, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 0),
(21, 2, 1, NULL, '6a71d0d2de072.pptx', 'final_ppt.pptx', 'user_2/documents/6a71d0d2de072.pptx', 7763161, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'document', 0, 0, '2026-08-04 11:45:22', '2026-08-05 05:02:20', NULL, NULL, 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 0),
(25, 2, 1, 9, '6a71d0d2f1d1c.php', 'brand.php', 'user_2/projects/6a71d0d2f1d1c.php', 63452, 'text/html', 'code', 0, 0, '2026-08-04 11:45:23', '2026-08-05 05:02:20', NULL, NULL, '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 0),
(26, 2, 1, 9, '6a71d0d308cdd.php', 'modal_formbrand.php', 'user_2/projects/6a71d0d308cdd.php', 31016, 'text/html', 'code', 0, 0, '2026-08-04 11:45:23', '2026-08-05 05:02:20', NULL, NULL, '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 0),
(27, 2, 1, NULL, '6a71d0d30d309.mp4', 'KVM Manager · Dark - Google Chrome 2026-07-03 13-05-00.mp4', 'user_2/videos/6a71d0d30d309.mp4', 1330812, 'video/mp4', 'video', 0, 0, '2026-08-04 11:45:23', '2026-08-05 05:02:20', NULL, NULL, 'c62527bb784dcbf4836c602861b41f8f5074cd4f10cb062f069bdcc486822d8e', 0),
(30, 2, 1, NULL, '6a71d49a148f6.txt', 'commands.txt', 'user_2/computers/6a71d49a148f6.txt', 105, 'text/plain', 'document', 0, 0, '2026-08-04 12:01:30', '2026-08-05 05:02:20', NULL, NULL, '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 0),
(35, 2, 3, NULL, '6a71da60c6597.txt', 'commands.txt', 'user_2/computers/6a71da60c6597.txt', 105, 'text/plain', 'document', 0, 0, '2026-08-04 12:26:08', '2026-08-05 05:02:20', NULL, NULL, '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 0),
(68, 2, 1, NULL, '6a732fa689339.jpg', '6a685cf66b672 (1) (1).jpg', 'user_2/images/6a732fa689339.jpg', 190842, 'image/jpeg', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 0),
(69, 2, 3, NULL, '6a732fa689339.jpg', '6a685cf66b672 (1) (1).jpg', 'user_2/computers/6a732fa689339.jpg', 190842, 'image/jpeg', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 0),
(70, 2, 1, NULL, '6a732fa697b8f.jpg', 's4 (1).jpg', 'user_2/images/6a732fa697b8f.jpg', 98370, 'image/jpeg', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '6ca8e2d6e943759401e9f5080e0518962af1321c1960ee1f0d31b610e01b7f16', 0),
(71, 2, 3, NULL, '6a732fa697b8f.jpg', 's4 (1).jpg', 'user_2/computers/6a732fa697b8f.jpg', 98370, 'image/jpeg', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '6ca8e2d6e943759401e9f5080e0518962af1321c1960ee1f0d31b610e01b7f16', 0),
(72, 2, 1, NULL, '6a732fa69f10d.jpg', '6a685cf66b672 (1).jpg', 'user_2/images/6a732fa69f10d.jpg', 190842, 'image/jpeg', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 0),
(73, 2, 3, NULL, '6a732fa69f10d.jpg', '6a685cf66b672 (1).jpg', 'user_2/computers/6a732fa69f10d.jpg', 190842, 'image/jpeg', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 0),
(74, 2, 1, NULL, '6a732fa6a7850.png', 'socket (1).png', 'user_2/images/6a732fa6a7850.png', 596, 'image/png', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '0f77bb6c2d73970a88a377c05b3d79576585846c15cf52a5ca1c13511c28d895', 0),
(75, 2, 3, NULL, '6a732fa6a7850.png', 'socket (1).png', 'user_2/computers/6a732fa6a7850.png', 596, 'image/png', 'image', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '0f77bb6c2d73970a88a377c05b3d79576585846c15cf52a5ca1c13511c28d895', 0),
(76, 2, 1, NULL, '6a732fa6ae206.xlsx', 'IOT  SIM LIST new (1).xlsx', 'user_2/documents/6a732fa6ae206.xlsx', 3032384, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'document', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 0),
(77, 2, 3, NULL, '6a732fa6ae206.xlsx', 'IOT  SIM LIST new (1).xlsx', 'user_2/computers/6a732fa6ae206.xlsx', 3032384, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'document', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 0),
(78, 2, 1, NULL, '6a732fa6bc55f.php', 'index (1).php', 'user_2/documents/6a732fa6bc55f.php', 37462, 'text/html', 'code', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, 'f49a9a37cef56fa5d35ea325ef5ce8955f61ec10134ca135c6bbde048178e6c7', 0),
(79, 2, 3, NULL, '6a732fa6bc55f.php', 'index (1).php', 'user_2/computers/6a732fa6bc55f.php', 37462, 'text/html', 'code', 0, 0, '2026-08-05 12:42:14', '2026-08-05 12:42:14', NULL, NULL, 'f49a9a37cef56fa5d35ea325ef5ce8955f61ec10134ca135c6bbde048178e6c7', 0),
(150, 3, 7, NULL, '6a74155236073.sql', 'file_manager_v4 (1).sql', 'user_3/documents/6a74155236073.sql', 45227, 'application/octet-stream', 'other', 0, 0, '2026-08-06 05:02:10', '2026-08-18 05:59:58', NULL, '2026-08-18 05:59:58', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 0),
(764, 3, 7, NULL, '6a747ffa233b2.pdf', 'Alma Linux Installations (1).pdf', 'user_3/documents/6a747ffa233b2.pdf', 357914, 'application/pdf', 'pdf', 0, 0, '2026-08-06 12:37:14', '2026-08-18 05:59:54', NULL, '2026-08-18 05:59:54', NULL, 0),
(765, 3, 6, NULL, '6a748003e95b6.mp4', 'sample-5s.mp4', 'user_3/videos/6a748003e95b6.mp4', 2848208, 'video/mp4', 'video', 0, 0, '2026-08-06 12:37:23', '2026-08-17 12:08:28', NULL, '2026-08-17 12:08:28', NULL, 0),
(767, 3, 7, NULL, '6a7558382cbf9.pptx', 'final_ppt (1).pptx', 'user_3/documents/6a7558382cbf9.pptx', 7763161, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'other', 0, 0, '2026-08-07 03:59:52', '2026-08-17 12:08:18', NULL, '2026-08-17 12:08:18', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 0),
(792, 3, 1, NULL, '6a75610b022ef.mp4', 'sample-5s.mp4', 'user_3/videos/6a75610b022ef.mp4', 2848208, 'video/mp4', 'video', 0, 0, '2026-08-07 04:37:31', '2026-08-17 12:08:24', NULL, '2026-08-17 12:08:24', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 0),
(793, 3, 1, 311, '6a756155ecc14.php', 'view_page.php', 'user_3/documents/6a756155ecc14.php', 19227, 'text/plain', 'code', 0, 0, '2026-08-07 04:38:46', '2026-08-17 12:08:22', NULL, '2026-08-17 12:08:22', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 0),
(794, 3, 1, 309, '6a7561561a435.php', 'index.php', 'user_3/documents/6a7561561a435.php', 36351, 'text/html', 'code', 0, 0, '2026-08-07 04:38:46', '2026-08-17 12:08:22', NULL, '2026-08-17 12:08:22', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 0),
(795, 3, 1, 308, '6a756156381eb.zip', 'help_and_knowledge_base.zip', 'user_3/archives/6a756156381eb.zip', 12390, 'application/zip', 'archive', 0, 0, '2026-08-07 04:38:46', '2026-08-17 12:08:22', NULL, '2026-08-17 12:08:22', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 0),
(796, 3, 1, 308, '6a7561564aac3.zip', 'modalbrand (2).zip', 'user_3/archives/6a7561564aac3.zip', 20366, 'application/zip', 'archive', 0, 0, '2026-08-07 04:38:46', '2026-08-17 12:08:23', NULL, '2026-08-17 12:08:23', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 0),
(797, 3, 1, 308, '6a756156558bd.zip', 'modalbrand.zip', 'user_3/archives/6a756156558bd.zip', 7078, 'application/zip', 'archive', 0, 0, '2026-08-07 04:38:46', '2026-08-17 12:08:23', NULL, '2026-08-17 12:08:23', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 0),
(798, 3, 7, NULL, '6a75656210698.xlsx', 'IOT  SIM LIST new.xlsx', 'user_3/documents/6a75656210698.xlsx', 3032384, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'other', 0, 0, '2026-08-07 04:56:02', '2026-08-17 12:08:18', NULL, '2026-08-17 12:08:18', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 0),
(799, 3, 7, NULL, '6a7565f89a49a.xlsx', 'Play_store_and_User_reviews_set_1.xlsx', 'user_3/documents/6a7565f89a49a.xlsx', 1381841, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'other', 0, 0, '2026-08-07 04:58:32', '2026-08-17 12:08:19', NULL, '2026-08-17 12:08:19', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 0),
(800, 2, 4, 313, '6a757da330314.pdf', 'Alma Linux Installations (1).pdf', 'user_2/projects/b1/6a757da330314.pdf', 357914, 'application/pdf', 'pdf', 0, 0, '2026-08-07 06:39:31', '2026-08-07 06:39:31', NULL, NULL, '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(801, 2, 4, 313, '6a757da33e2dc.zip', 'bye.zip', 'user_2/projects/b1/6a757da33e2dc.zip', 53791, 'application/x-zip-compressed', 'other', 0, 0, '2026-08-07 06:39:31', '2026-08-07 06:39:31', NULL, NULL, '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(802, 3, 7, NULL, '6a758cd0568ea.docx', 'Alma Linux Installations.docx', 'user_3/documents/6a758cd0568ea.docx', 58134, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'other', 0, 0, '2026-08-07 07:44:16', '2026-08-17 12:08:17', NULL, '2026-08-17 12:08:17', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 0),
(803, 3, 7, NULL, '6a7c43a00f3e6.txt', 'nastest.txt', 'user_3/documents/6a7c43a00f3e6.txt', 105, 'text/plain', 'text', 0, 0, '2026-08-12 09:57:52', '2026-08-17 12:08:18', NULL, '2026-08-17 12:08:18', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 0),
(804, 3, 4, 319, '6a7c43a9bbb94.txt', 'innernastest.txt', 'user_3/documents/6a7c43a9bbb94.txt', 105, 'text/plain', 'text', 0, 0, '2026-08-12 09:58:01', '2026-08-18 06:00:33', NULL, '2026-08-18 06:00:33', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 0),
(805, 3, 1, 311, '6a7c4ecdcb4e6.png', '1755490381_1755490381_296342e3efd59334904d.png', 'user_3/images/6a7c4ecdcb4e6.png', 46499, 'image/png', 'image', 0, 0, '2026-08-12 10:45:33', '2026-08-18 06:00:06', NULL, '2026-08-18 06:00:06', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 0),
(806, 3, 2, 325, '6a7c571240773.php', 'AuthService.php', 'user_3/shared/app/6a7c571240773.php', 3405, 'application/octet-stream', 'other', 0, 0, '2026-08-12 11:20:50', '2026-08-18 06:00:02', NULL, '2026-08-18 06:00:02', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 0),
(807, 3, 2, 325, '6a7c571256eaa.php', 'BaseController.php', 'user_3/shared/app/6a7c571256eaa.php', 3250, 'application/octet-stream', 'other', 0, 0, '2026-08-12 11:20:50', '2026-08-18 06:00:03', NULL, '2026-08-18 06:00:03', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 0),
(808, 3, 2, 325, '6a7c57125b672.php', 'AuthController.php', 'user_3/shared/app/6a7c57125b672.php', 1419, 'application/octet-stream', 'other', 0, 0, '2026-08-12 11:20:50', '2026-08-18 06:00:02', NULL, '2026-08-18 06:00:02', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 0),
(809, 3, 2, 325, '6a7c57125fd61.php', 'DashboardController.php', 'user_3/shared/app/6a7c57125fd61.php', 942, 'application/octet-stream', 'other', 0, 0, '2026-08-12 11:20:50', '2026-08-18 06:00:04', NULL, '2026-08-18 06:00:04', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 0),
(810, 3, 2, 325, '6a7c57126495e.php', 'HomeController.php', 'user_3/shared/app/6a7c57126495e.php', 950, 'application/octet-stream', 'other', 0, 0, '2026-08-12 11:20:50', '2026-08-18 06:00:05', NULL, '2026-08-18 06:00:05', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 0),
(811, 3, 2, 325, '6a7c57126e285.php', 'AuthController.php', 'user_3/shared/app/6a7c57126e285.php', 6099, 'application/octet-stream', 'other', 0, 0, '2026-08-12 11:20:50', '2026-08-17 12:08:20', NULL, '2026-08-17 12:08:20', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 0),
(812, 5, 7, NULL, '6a7d421ebe113.php', 'Api.php', 'user_5/documents/6a7d421ebe113.php', 4665, 'application/octet-stream', 'other', 0, 0, '2026-08-13 04:03:42', '2026-08-18 04:13:37', NULL, '2026-08-18 04:13:37', '79e29f51490fe7dbd6079662b67c3b6d5a52c57e2005a290002ab2d44f34d3da', 0),
(813, 5, 4, 326, '6a7d423b39a76.pdf', 'Alma Linux Installations (1).pdf', 'user_5/projects/b1/6a7d423b39a76.pdf', 357914, 'application/pdf', 'pdf', 0, 0, '2026-08-13 04:04:11', '2026-08-18 04:13:39', NULL, '2026-08-18 04:13:39', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(814, 5, 4, 326, '6a7d423b48c63.zip', 'bye.zip', 'user_5/projects/b1/6a7d423b48c63.zip', 53791, 'application/x-zip-compressed', 'other', 0, 0, '2026-08-13 04:04:11', '2026-08-18 04:13:39', NULL, '2026-08-18 04:13:39', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 0),
(817, 3, 1, NULL, '6a7d5d880a2340.03959161.xlsx', 'Play_store_and_User_reviews_set_1.xlsx', 'user_3/my-drive/6a7d5d880a2340.03959161.xlsx', 1381841, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'document', 0, 0, '2026-08-13 06:00:40', '2026-08-18 06:00:30', NULL, '2026-08-18 06:00:30', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 0),
(836, 3, 8, NULL, '6a7e94e994b45.zip', 'modalbrand.zip', 'user_3/archives/6a7e94e994b45.zip', 19746, 'application/x-zip-compressed', 'other', 0, 0, '2026-08-14 04:09:13', '2026-08-17 12:08:16', NULL, '2026-08-17 12:08:16', '95d1cc13e23b14fa248bcdd165de2b12c50c219694f55df6a23d709f7a244c15', 0),
(837, 3, 8, NULL, '6a7e952ce5e17.zip', 'modalbrand.zip', 'user_3/archives/6a7e952ce5e17.zip', 19746, 'application/x-zip-compressed', 'other', 0, 0, '2026-08-14 04:10:20', '2026-08-18 05:59:53', NULL, '2026-08-18 05:59:53', '95d1cc13e23b14fa248bcdd165de2b12c50c219694f55df6a23d709f7a244c15', 0),
(838, 3, 7, NULL, '6a7eabbe68ba3.sql', 'users.sql', 'user_3/documents/6a7eabbe68ba3.sql', 3031, 'application/octet-stream', 'other', 0, 0, '2026-08-14 05:46:38', '2026-08-17 12:08:20', NULL, '2026-08-17 12:08:20', 'a499449cca1bad9b2eb4440ed0716523b2cfc9a0ebd76748cb2ae9897e646106', 0),
(839, 3, 7, NULL, '6a7eb138df886.pdf', 'Alma Linux Installations (2).pdf', 'user_3/documents/6a7eb138df886.pdf', 357914, 'application/pdf', 'pdf', 0, 0, '2026-08-14 06:10:00', '2026-08-18 05:59:54', NULL, '2026-08-18 05:59:54', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 0),
(840, 3, 7, NULL, '0f9f8911833297b7073bea94ea74ccfd.sql', 'users.sql', 'user_3/documents/0f9f8911833297b7073bea94ea74ccfd.sql', 3031, 'application/octet-stream', 'other', 0, 0, '2026-08-14 06:15:06', '2026-08-18 06:00:01', NULL, '2026-08-18 06:00:01', 'a499449cca1bad9b2eb4440ed0716523b2cfc9a0ebd76748cb2ae9897e646106', 0),
(841, 3, 1, NULL, '373aff6be50995a054414493d11e7c02.csv', 'Play_store_and_User_reviews_set_1.csv', 'user_3/my-drive/373aff6be50995a054414493d11e7c02.csv', 349163, 'text/csv', 'text', 0, 0, '2026-08-14 06:15:30', '2026-08-18 06:28:34', NULL, '2026-08-18 06:28:34', 'dff6d9a15b109b8aea3a9be1ead5644a23a29ab60ef24bd2d04b972fdf663a44', 0),
(842, 3, 7, NULL, 'e5c683c5d3c34833556a66b735468b7b.php', 'StorageService.php', 'user_3/documents/e5c683c5d3c34833556a66b735468b7b.php', 14449, 'application/octet-stream', 'other', 0, 0, '2026-08-14 06:33:13', '2026-08-17 12:08:19', NULL, '2026-08-17 12:08:19', '06db2cbcb4edbb246e4003b702347b1be8ba9232db310f6c140e040e9cfd7ce9', 0),
(843, 3, 4, 341, '191834008cd4c281f1ddae2a8096d5de.php', 'FileService.php', 'user_3/projects/NAS/191834008cd4c281f1ddae2a8096d5de.php', 49228, 'application/octet-stream', 'other', 0, 0, '2026-08-14 06:33:26', '2026-08-18 06:00:31', NULL, '2026-08-18 06:00:31', '7ed4f20b51effd6f21a7b6bdb980ee6692a36ea8a164144b1270aa96bf636520', 0),
(844, 3, 4, 341, '300d58e04f9cf93d5c8835766a45b9bf.php', 'StorageService.php', 'user_3/projects/NAS/300d58e04f9cf93d5c8835766a45b9bf.php', 14449, 'application/octet-stream', 'other', 0, 0, '2026-08-14 06:33:26', '2026-08-18 06:00:31', NULL, '2026-08-18 06:00:31', '06db2cbcb4edbb246e4003b702347b1be8ba9232db310f6c140e040e9cfd7ce9', 0),
(845, 3, 7, NULL, '379d5793e67ee3fbd0565c262390e0c4.php', 'APIs_25-07-24.php', 'files/user/177/NAS/documents/379d5793e67ee3fbd0565c262390e0c4.php', 6659, 'application/octet-stream', 'other', 0, 0, '2026-08-14 08:24:40', '2026-08-18 05:59:55', NULL, '2026-08-18 05:59:55', '4d324759c438250741121ebb6c0356fe319bce5c2d98750443aa09d6d3de9cd9', 0),
(846, 3, 7, NULL, '2e61c0931547f1f70547febf02071fc5.php', 'FileService.php', 'files/user/177/NAS/documents/2e61c0931547f1f70547febf02071fc5.php', 51080, 'application/octet-stream', 'other', 0, 0, '2026-08-14 10:42:44', '2026-08-18 05:59:59', NULL, '2026-08-18 05:59:59', '48ffe79bd6b767a9c617edc1b7a466a40ec097cead595b7e500a2b1b9bd79fc4', 0),
(847, 3, 7, NULL, '0f3b71c552381ae2a02a34c3d3f5ae1c.php', 'StorageService.php', 'files/user/177/NAS/documents/0f3b71c552381ae2a02a34c3d3f5ae1c.php', 14926, 'application/octet-stream', 'other', 0, 0, '2026-08-14 11:12:24', '2026-08-18 06:00:00', NULL, '2026-08-18 06:00:00', '8c774631363631d755cee44f62378711b2c3dd6f0ae4fec04dad8ffa85eb5e33', 0),
(848, 3, 7, NULL, 'ac56e7ad6be44f18ae05756c3edb8849.php', 'Database.php', 'files/user/177/NAS/documents/ac56e7ad6be44f18ae05756c3edb8849.php', 2209, 'application/octet-stream', 'other', 0, 0, '2026-08-14 11:29:45', '2026-08-18 05:59:57', NULL, '2026-08-18 05:59:57', 'efdc5b99c90a8361e4581f35ab404ff06f2825d93614b3d5f8fb14f4dff50898', 0),
(849, 3, 7, NULL, '938816d751ca85f61da52b08f3f87483.php', 'ClusterService.php', 'user_3/documents/938816d751ca85f61da52b08f3f87483.php', 1335, 'application/octet-stream', 'other', 0, 0, '2026-08-17 06:46:03', '2026-08-18 05:59:56', NULL, '2026-08-18 05:59:56', '491a6958b67b1a778f90f2e625a5e303c27f9683a3d588930c6abf9c97dc8ea1', 0),
(850, 3, 4, 347, '3eae03f31d4d499ccaf60a1def75c6a1.js', 'app.js', 'user_3/projects/nasassets/NAS/js/3eae03f31d4d499ccaf60a1def75c6a1.js', 90794, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:17', '2026-08-18 06:00:34', NULL, '2026-08-18 06:00:34', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 0),
(851, 3, 4, 347, 'd01b1b243671e9c70d4359c071cb078f.js', 'app_12-8-26.js', 'user_3/projects/nasassets/NAS/js/d01b1b243671e9c70d4359c071cb078f.js', 84553, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:17', '2026-08-18 06:00:36', NULL, '2026-08-18 06:00:36', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 0),
(852, 3, 4, 347, '7a987dbf77c2e2a74c77c02b258f0c88.js', 'app_13-8-26(latest).js', 'user_3/projects/nasassets/NAS/js/7a987dbf77c2e2a74c77c02b258f0c88.js', 89157, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:17', '2026-08-18 06:00:37', NULL, '2026-08-18 06:00:37', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 0),
(853, 3, 4, 347, '5e53634731825730c1b4aaacb3e0cd4b.js', 'app_13-8-26.js', 'user_3/projects/nasassets/NAS/js/5e53634731825730c1b4aaacb3e0cd4b.js', 89683, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:17', '2026-08-18 06:00:40', NULL, '2026-08-18 06:00:40', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 0),
(854, 3, 4, 347, '531db24523e7ee64c6fb0d194c789d1c.js', 'config.js', 'user_3/projects/nasassets/NAS/js/531db24523e7ee64c6fb0d194c789d1c.js', 4278, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:17', '2026-08-18 06:00:42', NULL, '2026-08-18 06:00:42', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 0),
(855, 3, 4, 347, '91bbb1b054dda4c03574b68bf857d2e6.js', 'excelpreview.js', 'user_3/projects/nasassets/NAS/js/91bbb1b054dda4c03574b68bf857d2e6.js', 32387, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:44', NULL, '2026-08-18 06:00:44', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 0),
(856, 3, 4, 347, '2805bd834c4fd7494a41a1aebf198cb5.js', 'preview.js', 'user_3/projects/nasassets/NAS/js/2805bd834c4fd7494a41a1aebf198cb5.js', 60094, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:45', NULL, '2026-08-18 06:00:45', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 0),
(857, 3, 4, 347, '5e35531cb29a2c5cc30e1a6f6bdadc3f.js', 'share.js', 'user_3/projects/nasassets/NAS/js/5e35531cb29a2c5cc30e1a6f6bdadc3f.js', 21029, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:47', NULL, '2026-08-18 06:00:47', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 0),
(858, 3, 4, 347, 'd751fa151ba4a8f7fa45545ef9f9a8db.js', 'sync.js', 'user_3/projects/nasassets/NAS/js/d751fa151ba4a8f7fa45545ef9f9a8db.js', 14959, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:49', NULL, '2026-08-18 06:00:49', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 0),
(859, 3, 4, 347, '5992c550f981f6373f40b7a15b3b49f2.js', 'utils.js', 'user_3/projects/nasassets/NAS/js/5992c550f981f6373f40b7a15b3b49f2.js', 4520, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:52', NULL, '2026-08-18 06:00:52', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 0),
(860, 3, 4, 347, 'a1114ea8b2fdc743278a896a891df959.js', 'websocket-client.js', 'user_3/projects/nasassets/NAS/js/a1114ea8b2fdc743278a896a891df959.js', 10096, 'text/javascript', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:55', NULL, '2026-08-18 06:00:55', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 0),
(861, 3, 4, 348, '383576b3be8a4b6c5688e344bd7f22dd.png', 'efactor.png', 'user_3/projects/nasassets/NAS/images/383576b3be8a4b6c5688e344bd7f22dd.png', 41187, 'image/png', 'image', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:34', NULL, '2026-08-18 06:00:34', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 0),
(862, 3, 4, 349, '2da12d580af754625425f72a8f7e93a0.css', 'home-style.css', 'user_3/projects/nasassets/NAS/css/2da12d580af754625425f72a8f7e93a0.css', 0, 'text/css', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:33', NULL, '2026-08-18 06:00:33', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 0),
(863, 3, 4, 349, 'fe43d706d154d3db512b3fde2bd1bd96.css', 'style.css', 'user_3/projects/nasassets/NAS/css/fe43d706d154d3db512b3fde2bd1bd96.css', 133124, 'text/css', 'text', 0, 0, '2026-08-17 06:51:18', '2026-08-18 06:00:33', NULL, '2026-08-18 06:00:33', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 0),
(864, 3, 1, NULL, '72310934faa97f2b415f8f8fdb182406.txt', 'arch.txt', 'user_3/my-drive/72310934faa97f2b415f8f8fdb182406.txt', 1836, 'text/plain', 'document', 0, 0, '2026-08-17 11:45:56', '2026-08-18 06:00:06', NULL, '2026-08-18 06:00:06', '535264adba1fe527b28264cb2acd37dcfc3b557ae0c1ed5df1c3c2aed348d877', 0),
(879, 3, 1, 355, 'e87b41d0967e6a379a68bb6346f008f2.css', 'style.css', 'user_3/my-drive/NAS/css/e87b41d0967e6a379a68bb6346f008f2.css', 133124, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:19', '2026-08-18 06:00:07', NULL, '2026-08-18 06:00:07', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 0),
(880, 3, 1, 355, '0c2e71add7e245be358f6bbc3f44d17a.css', 'home-style.css', 'user_3/my-drive/NAS/css/0c2e71add7e245be358f6bbc3f44d17a.css', 0, 'application/x-empty', 'code', 0, 0, '2026-08-18 03:41:19', '2026-08-18 06:00:07', NULL, '2026-08-18 06:00:07', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 0),
(881, 3, 1, 356, '0aad30b0cfdbddc9efd35d0fc152bca9.png', 'efactor.png', 'user_3/my-drive/NAS/images/0aad30b0cfdbddc9efd35d0fc152bca9.png', 41187, 'image/png', 'image', 0, 0, '2026-08-18 03:41:19', '2026-08-18 06:00:07', NULL, '2026-08-18 06:00:07', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 0),
(882, 3, 1, 357, '8bf87529e20b3f7822b495f1af6c420f.js', 'app.js', 'user_3/my-drive/NAS/js/8bf87529e20b3f7822b495f1af6c420f.js', 90794, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:20', '2026-08-18 06:00:08', NULL, '2026-08-18 06:00:08', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 0),
(883, 3, 1, 357, '8b3d2d98e131d80071e2dd9f3afe1fcc.js', 'app_12-8-26.js', 'user_3/my-drive/NAS/js/8b3d2d98e131d80071e2dd9f3afe1fcc.js', 84553, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:20', '2026-08-18 06:00:10', NULL, '2026-08-18 06:00:10', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 0),
(884, 3, 1, 357, 'b0f354f8320440cbabd284247b30bbcd.js', 'app_13-8-26(latest).js', 'user_3/my-drive/NAS/js/b0f354f8320440cbabd284247b30bbcd.js', 89157, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:20', '2026-08-18 06:00:11', NULL, '2026-08-18 06:00:11', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 0),
(885, 3, 1, 357, '443943ac0131344474c6d45ce52c1a77.js', 'app_13-8-26.js', 'user_3/my-drive/NAS/js/443943ac0131344474c6d45ce52c1a77.js', 89683, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:21', '2026-08-18 06:00:13', NULL, '2026-08-18 06:00:13', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 0),
(886, 3, 1, 357, 'be9d7e418fb030476de68c660bfaab1a.js', 'config.js', 'user_3/my-drive/NAS/js/be9d7e418fb030476de68c660bfaab1a.js', 4278, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:21', '2026-08-18 06:00:16', NULL, '2026-08-18 06:00:16', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 0),
(887, 3, 1, 357, '2270c9a73ee177a7c2ff7b800c736dcd.js', 'preview.js', 'user_3/my-drive/NAS/js/2270c9a73ee177a7c2ff7b800c736dcd.js', 60094, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:21', '2026-08-18 06:00:20', NULL, '2026-08-18 06:00:20', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 0),
(888, 3, 1, 357, '0da13e4a9f129c6994ef5f328848c737.js', 'sync.js', 'user_3/my-drive/NAS/js/0da13e4a9f129c6994ef5f328848c737.js', 14959, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:21', '2026-08-18 06:00:24', NULL, '2026-08-18 06:00:24', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 0),
(889, 3, 1, 357, '368cce37a104af1dacae692087574f6d.js', 'share.js', 'user_3/my-drive/NAS/js/368cce37a104af1dacae692087574f6d.js', 21029, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:22', '2026-08-18 06:00:22', NULL, '2026-08-18 06:00:22', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 0),
(890, 3, 1, 357, 'dd535f9cb81eab176d250ddc4f5407ed.js', 'excelpreview.js', 'user_3/my-drive/NAS/js/dd535f9cb81eab176d250ddc4f5407ed.js', 32387, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:22', '2026-08-18 06:00:18', NULL, '2026-08-18 06:00:18', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 0),
(891, 3, 1, 357, '634b3edd63fca66026260a9685d338b4.js', 'utils.js', 'user_3/my-drive/NAS/js/634b3edd63fca66026260a9685d338b4.js', 4520, 'text/plain', 'code', 0, 0, '2026-08-18 03:41:22', '2026-08-18 06:00:26', NULL, '2026-08-18 06:00:26', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 0),
(892, 3, 1, 357, 'fb6fca4a9963a18234b12457f8154ceb.js', 'websocket-client.js', 'user_3/my-drive/NAS/js/fb6fca4a9963a18234b12457f8154ceb.js', 10096, 'text/x-c++', 'code', 0, 0, '2026-08-18 03:41:23', '2026-08-18 06:00:28', NULL, '2026-08-18 06:00:28', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 0);

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
  `path` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

INSERT INTO `folders` (`id`, `user_id`, `cluster_id`, `name`, `path`, `parent_id`, `is_starred`, `is_shared`, `created_at`, `updated_at`, `deleted_at`, `last_opened`) VALUES
(6, 2, 4, 'modalbrand', 'user_2/projects/modalbrand', NULL, 0, 0, '2026-08-04 05:17:16', '2026-08-17 06:55:52', NULL, NULL),
(9, 2, 1, 'modalbrand', 'user_2/my-drive/modalbrand', NULL, 0, 0, '2026-08-04 11:45:22', '2026-08-17 06:55:52', NULL, NULL),
(12, 2, 4, 'anm', 'user_2/projects/anm', NULL, 0, 0, '2026-08-05 10:31:56', '2026-08-17 06:55:52', NULL, NULL),
(308, 3, 1, 'folder', 'user_3/my-drive/folder', NULL, 0, 0, '2026-08-07 04:37:30', '2026-08-17 06:55:52', NULL, NULL),
(309, 3, 1, 'help_and_knowledge_base', 'user_3/my-drive/folder/help_and_knowledge_base', 308, 0, 0, '2026-08-07 04:37:30', '2026-08-17 06:55:52', NULL, NULL),
(310, 3, 1, 'modalbrand', 'user_3/my-drive/folder/modalbrand', 308, 0, 0, '2026-08-07 04:37:30', '2026-08-17 06:55:52', NULL, NULL),
(311, 3, 1, 'articles', 'user_3/my-drive/folder/help_and_knowledge_base/articles', 309, 0, 0, '2026-08-07 04:37:30', '2026-08-17 06:55:52', NULL, NULL),
(312, 3, 1, 'articles', 'user_3/my-drive/folder/modalbrand/articles', 310, 0, 0, '2026-08-07 04:37:30', '2026-08-17 06:55:52', NULL, NULL),
(313, 2, 4, 'b1', 'user_2/projects/b1', NULL, 0, 0, '2026-08-07 06:39:31', '2026-08-17 06:55:52', NULL, NULL),
(319, 3, 4, 'NAS_TEST', 'user_3/projects/NAS_TEST', NULL, 1, 0, '2026-08-12 09:56:57', '2026-08-17 06:55:52', NULL, NULL),
(320, 3, 4, 'nasInner', 'user_3/projects/NAS_TEST/nasInner', 319, 0, 0, '2026-08-12 09:58:54', '2026-08-17 06:55:52', NULL, NULL),
(325, 3, 2, 'app', 'user_3/shared/app', NULL, 0, 0, '2026-08-12 11:20:50', '2026-08-17 06:55:52', NULL, NULL),
(326, 5, 4, 'b1', 'user_5/projects/b1', NULL, 0, 0, '2026-08-13 04:04:11', '2026-08-17 06:55:52', NULL, NULL),
(339, 3, 4, 'anm1', 'user_3/projects/anm1', NULL, 0, 0, '2026-08-14 06:21:47', '2026-08-18 12:26:33', '2026-08-18 12:26:33', NULL),
(340, 3, 4, 'NAS', 'user_3/projects/anm1/NAS', 339, 0, 0, '2026-08-14 06:22:15', '2026-08-18 12:26:33', '2026-08-18 12:26:33', NULL),
(341, 3, 4, 'NAS', 'user_3/projects/NAS', NULL, 0, 0, '2026-08-14 06:33:26', '2026-08-17 06:55:52', NULL, NULL),
(342, 3, 4, 'opo', 'user_3/projects/opo', NULL, 0, 0, '2026-08-14 09:07:03', '2026-08-17 06:55:52', NULL, NULL),
(343, 3, 4, 'test', 'user_3/projects/test', NULL, 0, 0, '2026-08-14 09:23:33', '2026-08-17 06:55:52', NULL, NULL),
(344, 3, 4, 'vivo', 'user_3/projects/vivo', NULL, 0, 0, '2026-08-17 06:47:56', '2026-08-17 06:47:56', NULL, NULL),
(345, 3, 4, 'nasassets', 'user_3/projects/nasassets', NULL, 1, 0, '2026-08-17 06:51:17', '2026-08-17 06:53:34', NULL, NULL),
(346, 3, 4, 'NAS', 'user_3/projects/nasassets/NAS', 345, 0, 0, '2026-08-17 06:51:17', '2026-08-17 06:52:52', NULL, NULL),
(347, 3, 4, 'js', 'user_3/projects/nasassets/NAS/js', 346, 0, 0, '2026-08-17 06:51:17', '2026-08-17 06:52:52', NULL, NULL),
(348, 3, 4, 'images', 'user_3/projects/nasassets/NAS/images', 346, 0, 0, '2026-08-17 06:51:18', '2026-08-17 07:50:13', NULL, NULL),
(349, 3, 4, 'css', 'user_3/projects/nasassets/NAS/css', 346, 0, 0, '2026-08-17 06:51:18', '2026-08-17 06:52:52', NULL, NULL),
(354, 3, 1, 'NAS', 'user_3/my-drive/NAS', NULL, 0, 0, '2026-08-18 03:41:19', '2026-08-18 03:41:19', NULL, NULL),
(355, 3, 1, 'css', 'user_3/my-drive/NAS/css', 354, 0, 0, '2026-08-18 03:41:19', '2026-08-18 03:41:19', NULL, NULL),
(356, 3, 1, 'images', 'user_3/my-drive/NAS/images', 354, 0, 0, '2026-08-18 03:41:19', '2026-08-18 03:41:19', NULL, NULL),
(357, 3, 1, 'js', 'user_3/my-drive/NAS/js', 354, 0, 0, '2026-08-18 03:41:19', '2026-08-18 03:41:19', NULL, NULL);

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
(2, 2, 'folder', 2, 3, 'view', NULL, NULL, '2026-08-04 06:01:47'),
(3, 2, 'folder', 6, 3, 'download', NULL, NULL, '2026-08-04 06:21:54'),
(4, 2, 'file', 10, 3, 'view', NULL, NULL, '2026-08-04 06:21:54'),
(5, 2, 'file', 11, 3, 'view', NULL, NULL, '2026-08-04 06:21:54'),
(10, 2, 'file', 2, 2, 'view', NULL, NULL, '2026-08-04 07:14:58'),
(11, 2, 'folder', 3, 3, 'full', NULL, NULL, '2026-08-04 08:10:34'),
(13, 2, 'folder', 12, 3, 'view', NULL, NULL, '2026-08-05 10:33:13'),
(14, 2, 'file', 66, 3, 'view', NULL, NULL, '2026-08-05 10:33:13'),
(23, 2, 'folder', 313, 3, 'view', NULL, NULL, '2026-08-07 06:39:45'),
(24, 2, 'file', 800, 3, 'view', NULL, NULL, '2026-08-07 06:39:45'),
(25, 2, 'file', 801, 3, 'view', NULL, NULL, '2026-08-07 06:39:45'),
(26, 3, 'file', 802, 2, 'view', NULL, NULL, '2026-08-07 07:44:37'),
(27, 3, 'file', 767, 2, 'view', NULL, NULL, '2026-08-07 08:13:05'),
(28, 2, 'file', 77, 3, 'view', NULL, NULL, '2026-08-07 08:23:57'),
(29, 3, 'folder', 309, 5, 'view', NULL, NULL, '2026-08-13 05:38:53'),
(30, 3, 'folder', 311, 5, 'view', NULL, NULL, '2026-08-13 05:38:53'),
(31, 3, 'file', 805, 5, 'view', NULL, NULL, '2026-08-13 05:38:53'),
(32, 3, 'file', 793, 5, 'view', NULL, NULL, '2026-08-13 05:38:53'),
(33, 3, 'file', 794, 5, 'view', NULL, NULL, '2026-08-13 05:38:53'),
(34, 3, 'folder', 325, 2, 'download', NULL, NULL, '2026-08-14 11:53:58'),
(35, 3, 'file', 808, 2, 'view', NULL, NULL, '2026-08-14 11:53:58'),
(36, 3, 'file', 811, 2, 'view', NULL, NULL, '2026-08-14 11:53:58'),
(37, 3, 'file', 806, 2, 'view', NULL, NULL, '2026-08-14 11:53:58'),
(38, 3, 'file', 807, 2, 'view', NULL, NULL, '2026-08-14 11:53:58'),
(39, 3, 'file', 809, 2, 'view', NULL, NULL, '2026-08-14 11:53:58'),
(40, 3, 'file', 810, 2, 'view', NULL, NULL, '2026-08-14 11:53:58'),
(41, 3, 'folder', 325, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(42, 3, 'file', 808, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(43, 3, 'file', 811, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(44, 3, 'file', 806, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(45, 3, 'file', 807, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(46, 3, 'file', 809, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(47, 3, 'file', 810, 5, 'full', NULL, NULL, '2026-08-14 12:00:20'),
(48, 3, 'folder', 339, 5, 'download', NULL, '2026-08-07 12:19:00', '2026-08-14 12:19:37'),
(49, 3, 'folder', 340, 5, 'download', NULL, '2026-08-07 12:19:00', '2026-08-14 12:19:37'),
(50, 3, 'folder', 339, 5, 'edit', NULL, NULL, '2026-08-14 12:20:36'),
(51, 3, 'folder', 340, 5, 'download', NULL, '2026-08-22 12:20:00', '2026-08-14 12:20:36'),
(52, 3, 'folder', 325, 4, 'download', NULL, NULL, '2026-08-19 04:23:01'),
(53, 3, 'file', 808, 4, 'download', NULL, NULL, '2026-08-19 04:23:01'),
(54, 3, 'file', 811, 4, 'download', NULL, NULL, '2026-08-19 04:23:01'),
(55, 3, 'file', 806, 4, 'download', NULL, NULL, '2026-08-19 04:23:01'),
(56, 3, 'file', 807, 4, 'download', NULL, NULL, '2026-08-19 04:23:01'),
(57, 3, 'file', 809, 4, 'download', NULL, NULL, '2026-08-19 04:23:01'),
(58, 3, 'file', 810, 4, 'download', NULL, NULL, '2026-08-19 04:23:01');

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

--
-- Dumping data for table `sync_folders`
--

INSERT INTO `sync_folders` (`id`, `user_id`, `local_path`, `folder_name`, `sync_enabled`, `is_auto`, `last_sync`, `created_at`, `updated_at`) VALUES
(1, 2, 'C:\\Users\\ADMIN\\eFactor\\my-drive\\admin', 'admin', 1, 0, '2026-08-07 13:21:46', '2026-08-05 16:38:15', '2026-08-07 13:21:46'),
(2, 3, 'browser://Efactor/MyDrive', 'MyDrive', 1, 0, '2026-08-18 11:30:57', '2026-08-06 09:03:46', '2026-08-18 11:30:57'),
(3, 5, 'browser://Efactor/MyDrive', 'MyDrive', 1, 0, '2026-08-18 09:44:20', '2026-08-13 09:35:04', '2026-08-18 09:44:20');

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

--
-- Dumping data for table `sync_logs`
--

INSERT INTO `sync_logs` (`id`, `user_id`, `sync_type`, `file_path`, `file_hash`, `message`, `created_at`) VALUES
(25, 3, 'upload', 'sync_complete', NULL, 'Sync completed: 9 uploaded, 0 updated, 0 deleted, 3 skipped', '2026-08-06 13:46:47'),
(26, 3, 'upload', 'sync_complete', NULL, 'Sync: 9 uploaded, 0 updated, 3 skipped (deletions disabled)', '2026-08-06 13:56:54'),
(27, 3, 'upload', 'sync_complete', NULL, 'Sync: 7 uploaded, 0 updated, 12 skipped (deletions disabled)', '2026-08-06 15:36:48'),
(28, 3, 'upload', 'sync_complete', NULL, 'Sync: 7 uploaded, 0 updated, 12 skipped (deletions disabled)', '2026-08-06 15:45:42'),
(29, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 15:46:11'),
(30, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 15:47:56'),
(31, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 15:52:26'),
(32, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 15:52:46'),
(33, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 15:54:17'),
(34, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 15:54:55'),
(35, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 16:03:37'),
(36, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 16:04:00'),
(37, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 16:05:38'),
(38, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 16:14:35'),
(39, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped (deletions disabled)', '2026-08-06 16:14:52'),
(40, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 16:22:59'),
(41, 3, 'upload', 'sync_complete', NULL, 'Sync: 7 uploaded, 0 updated, 12 skipped', '2026-08-06 16:23:12'),
(42, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 16:24:43'),
(43, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 16:25:33'),
(44, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 16:34:22'),
(45, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 16:46:23'),
(46, 3, 'upload', 'sync_complete', NULL, 'Sync: 11 uploaded, 0 updated, 8 skipped', '2026-08-06 16:47:12'),
(47, 3, 'upload', 'sync_complete', NULL, 'Sync: 15 uploaded, 0 updated, 11 skipped', '2026-08-06 16:49:50'),
(48, 3, 'upload', 'sync_complete', NULL, 'Sync: 15 uploaded, 0 updated, 11 skipped', '2026-08-06 16:52:57'),
(49, 3, 'upload', 'sync_complete', NULL, 'Sync: 15 uploaded, 0 updated, 11 skipped', '2026-08-06 17:02:03'),
(50, 3, 'upload', 'sync_complete', NULL, 'Sync: 11 uploaded, 0 updated, 8 skipped', '2026-08-06 17:02:41'),
(51, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 17:09:14'),
(52, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 17:14:09'),
(53, 3, 'upload', 'sync_complete', NULL, 'Sync: 17 uploaded, 0 updated, 2 skipped', '2026-08-06 17:17:46'),
(54, 3, 'upload', 'sync_complete', NULL, 'Sync: 18 uploaded, 0 updated, 2 skipped', '2026-08-06 17:31:19'),
(55, 3, 'upload', 'sync_complete', NULL, 'Sync: 18 uploaded, 0 updated, 2 skipped', '2026-08-06 17:37:21'),
(56, 3, 'upload', 'sync_complete', NULL, 'Sync: 18 uploaded, 0 updated, 2 skipped', '2026-08-06 17:45:33'),
(57, 3, 'upload', 'sync_complete', NULL, 'Sync: 20 uploaded, 0 updated, 0 skipped', '2026-08-06 17:50:50'),
(58, 3, 'upload', 'sync_complete', NULL, 'Sync: 20 uploaded, 0 updated, 0 skipped', '2026-08-06 17:58:25'),
(59, 3, 'download', 'final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 'Downloaded successfully', '2026-08-07 09:37:07'),
(60, 3, 'download', 'alma linux server  version details (6).docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 'Downloaded successfully', '2026-08-07 09:37:07'),
(61, 3, 'download', 'view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-07 09:37:07'),
(62, 3, 'download', 'index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 'Downloaded successfully', '2026-08-07 09:37:08'),
(63, 3, 'download', 'help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 'Downloaded successfully', '2026-08-07 09:37:08'),
(64, 3, 'download', 'view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-07 09:37:08'),
(65, 3, 'download', 'brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 'Downloaded successfully', '2026-08-07 09:37:08'),
(66, 3, 'download', 'modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 'Downloaded successfully', '2026-08-07 09:37:08'),
(67, 3, 'download', 'modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 'Downloaded successfully', '2026-08-07 09:37:08'),
(68, 3, 'download', 'modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 'Downloaded successfully', '2026-08-07 09:37:08'),
(69, 3, 'download', 'file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 'Downloaded successfully', '2026-08-07 09:37:08'),
(70, 3, 'upload', 'sync_complete', NULL, 'Sync: 12 uploaded, 0 updated, 0 skipped', '2026-08-07 09:37:16'),
(71, 3, 'upload', 'sync_complete', NULL, 'Sync: 12 uploaded, 0 updated, 0 skipped', '2026-08-07 09:54:08'),
(72, 3, 'upload', 'sync_complete', NULL, 'Sync: 12 uploaded, 0 updated, 0 skipped', '2026-08-07 10:07:31'),
(73, 3, 'upload', 'sync_complete', NULL, 'Sync: 12 uploaded, 0 updated, 0 skipped', '2026-08-07 10:08:46'),
(74, 3, 'upload', 'sync_complete', NULL, 'Sync: 7 uploaded, 0 updated, 5 skipped', '2026-08-07 10:09:49'),
(75, 3, 'upload', 'sync_complete', NULL, 'Sync: 7 uploaded, 0 updated, 5 skipped', '2026-08-07 10:29:45'),
(76, 3, 'download', 'Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-07 10:29:55'),
(77, 3, 'download', 'IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 'Downloaded successfully', '2026-08-07 10:29:55'),
(78, 3, 'download', 'sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 'Downloaded successfully', '2026-08-07 10:29:55'),
(79, 3, 'upload', 'sync_complete', NULL, 'Sync: 10 uploaded, 0 updated, 5 skipped', '2026-08-07 12:22:03'),
(80, 2, 'upload', 'sync_complete', NULL, 'Sync: 28 uploaded, 0 updated, 4 skipped', '2026-08-07 13:21:46'),
(81, 3, 'upload', 'sync_complete', NULL, 'Sync: 10 uploaded, 0 updated, 5 skipped', '2026-08-07 13:22:41'),
(82, 3, 'upload', 'sync_complete', NULL, 'Sync: 10 uploaded, 0 updated, 5 skipped', '2026-08-07 14:26:58'),
(83, 3, 'download', 'Alma Linux Installations.docx', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 'Downloaded successfully', '2026-08-12 14:37:26'),
(84, 3, 'download', 'Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-12 14:37:26'),
(85, 3, 'download', 'IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 'Downloaded successfully', '2026-08-12 14:37:27'),
(86, 3, 'download', 'view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-12 14:37:27'),
(87, 3, 'download', 'index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 'Downloaded successfully', '2026-08-12 14:37:27'),
(88, 3, 'download', 'help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 'Downloaded successfully', '2026-08-12 14:37:27'),
(89, 3, 'download', 'modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 'Downloaded successfully', '2026-08-12 14:37:27'),
(90, 3, 'download', 'modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 'Downloaded successfully', '2026-08-12 14:37:27'),
(91, 3, 'download', 'sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 'Downloaded successfully', '2026-08-12 14:37:27'),
(92, 3, 'download', 'final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 'Downloaded successfully', '2026-08-12 14:37:27'),
(93, 3, 'download', 'alma linux server  version details (6).docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 'Downloaded successfully', '2026-08-12 14:37:27'),
(94, 3, 'download', 'file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 'Downloaded successfully', '2026-08-12 14:37:27'),
(95, 3, 'upload', 'sync_complete', NULL, 'Sync: 7 uploaded, 0 updated, 5 skipped', '2026-08-12 14:37:32'),
(96, 3, 'upload', 'sync_complete', NULL, 'Sync: 12 uploaded, 0 updated, 0 skipped', '2026-08-12 14:58:37'),
(97, 3, 'download', 'innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-12 15:32:45'),
(98, 3, 'download', 'nastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-12 15:32:45'),
(99, 3, 'download', 'view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-12 15:32:45'),
(100, 3, 'download', 'index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 'Downloaded successfully', '2026-08-12 15:32:45'),
(101, 3, 'download', 'help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 'Downloaded successfully', '2026-08-12 15:32:45'),
(102, 3, 'download', 'modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 'Downloaded successfully', '2026-08-12 15:32:45'),
(103, 3, 'download', 'modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 'Downloaded successfully', '2026-08-12 15:32:45'),
(104, 3, 'upload', 'sync_complete', NULL, 'Sync: 14 uploaded, 0 updated, 5 skipped', '2026-08-12 15:36:57'),
(105, 3, 'upload', 'sync_complete', NULL, 'Sync: 14 uploaded, 0 updated, 5 skipped', '2026-08-12 18:02:01'),
(106, 5, 'download', 'Alma Linux Installations (1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 'Downloaded successfully', '2026-08-13 09:35:04'),
(107, 5, 'download', 'bye.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 'Downloaded successfully', '2026-08-13 09:35:04'),
(108, 5, 'download', 'Api.php', '79e29f51490fe7dbd6079662b67c3b6d5a52c57e2005a290002ab2d44f34d3da', 'Downloaded successfully', '2026-08-13 09:35:04'),
(109, 5, 'upload', 'sync_complete', NULL, 'Sync: 3 uploaded, 0 updated, 0 skipped', '2026-08-13 09:35:47'),
(110, 3, 'download', 'AuthService.php', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 'Downloaded successfully', '2026-08-13 10:50:44'),
(111, 3, 'download', 'BaseController.php', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 'Downloaded successfully', '2026-08-13 10:50:44'),
(112, 3, 'download', 'AuthController.php', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 'Downloaded successfully', '2026-08-13 10:50:44'),
(113, 3, 'download', 'DashboardController.php', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 'Downloaded successfully', '2026-08-13 10:50:44'),
(114, 3, 'download', 'HomeController.php', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 'Downloaded successfully', '2026-08-13 10:50:44'),
(115, 3, 'download', 'AuthController.php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-13 10:50:44'),
(116, 3, 'download', '1755490381_1755490381_296342e3efd59334904d.png', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 'Downloaded successfully', '2026-08-13 10:50:44'),
(117, 3, 'upload', 'sync_complete', NULL, 'Sync: 20 uploaded, 0 updated, 6 skipped', '2026-08-13 10:50:48'),
(118, 3, 'download', 'AuthService.php', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 'Downloaded successfully', '2026-08-13 10:54:26'),
(119, 3, 'download', 'BaseController.php', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 'Downloaded successfully', '2026-08-13 10:54:26'),
(120, 3, 'download', 'AuthController.php', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 'Downloaded successfully', '2026-08-13 10:54:26'),
(121, 3, 'download', 'DashboardController.php', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 'Downloaded successfully', '2026-08-13 10:54:26'),
(122, 3, 'download', 'HomeController.php', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 'Downloaded successfully', '2026-08-13 10:54:26'),
(123, 3, 'download', 'AuthController.php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-13 10:54:26'),
(124, 3, 'download', '1755490381_1755490381_296342e3efd59334904d.png', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 'Downloaded successfully', '2026-08-13 10:54:26'),
(125, 3, 'download', 'innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-13 10:54:26'),
(126, 3, 'download', 'nastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-13 10:54:26'),
(127, 3, 'download', 'Alma Linux Installations.docx', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 'Downloaded successfully', '2026-08-13 10:54:26'),
(128, 3, 'download', 'Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-13 10:54:26'),
(129, 3, 'download', 'IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 'Downloaded successfully', '2026-08-13 10:54:26'),
(130, 3, 'download', 'view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-13 10:54:26'),
(131, 3, 'download', 'index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 'Downloaded successfully', '2026-08-13 10:54:26'),
(132, 3, 'download', 'help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 'Downloaded successfully', '2026-08-13 10:54:26'),
(133, 3, 'download', 'modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 'Downloaded successfully', '2026-08-13 10:54:26'),
(134, 3, 'download', 'modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 'Downloaded successfully', '2026-08-13 10:54:26'),
(135, 3, 'download', 'sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 'Downloaded successfully', '2026-08-13 10:54:26'),
(136, 3, 'download', 'final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 'Downloaded successfully', '2026-08-13 10:54:26'),
(137, 3, 'download', 'file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 'Downloaded successfully', '2026-08-13 10:54:26'),
(138, 3, 'download', 'My Drive/app/AuthController (2).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-13 11:29:51'),
(139, 3, 'download', 'My Drive/app/AuthController (3).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-13 11:30:37'),
(140, 3, 'upload', 'sync_complete', NULL, 'Sync: 8 folders created, 21 files uploaded, 1 files skipped', '2026-08-13 11:30:41'),
(141, 3, 'download', 'My Drive/Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-13 15:37:09'),
(142, 3, 'download', 'My Drive/app/AuthService.php', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 'Downloaded successfully', '2026-08-13 15:37:09'),
(143, 3, 'download', 'My Drive/app/BaseController.php', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 'Downloaded successfully', '2026-08-13 15:37:09'),
(144, 3, 'download', 'My Drive/app/AuthController.php', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 'Downloaded successfully', '2026-08-13 15:37:09'),
(145, 3, 'download', 'My Drive/app/DashboardController.php', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 'Downloaded successfully', '2026-08-13 15:37:09'),
(146, 3, 'download', 'My Drive/app/HomeController.php', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 'Downloaded successfully', '2026-08-13 15:37:09'),
(147, 3, 'download', 'My Drive/app/AuthController (1).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-13 15:37:09'),
(148, 3, 'download', 'My Drive/folder/help_and_knowledge_base/articles/1755490381_1755490381_296342e3efd59334904d.png', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 'Downloaded successfully', '2026-08-13 15:37:09'),
(149, 3, 'download', 'Projects/NAS_TEST/innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-13 15:37:09'),
(150, 3, 'download', 'Documents/nastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-13 15:37:09'),
(151, 3, 'download', 'Documents/Alma Linux Installations.docx', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 'Downloaded successfully', '2026-08-13 15:37:09'),
(152, 3, 'download', 'Documents/Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-13 15:37:09'),
(153, 3, 'download', 'Documents/IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 'Downloaded successfully', '2026-08-13 15:37:09'),
(154, 3, 'download', 'My Drive/folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-13 15:37:09'),
(155, 3, 'download', 'My Drive/folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 'Downloaded successfully', '2026-08-13 15:37:09'),
(156, 3, 'download', 'My Drive/folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 'Downloaded successfully', '2026-08-13 15:37:09'),
(157, 3, 'download', 'My Drive/folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 'Downloaded successfully', '2026-08-13 15:37:09'),
(158, 3, 'download', 'My Drive/folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 'Downloaded successfully', '2026-08-13 15:37:09'),
(159, 3, 'download', 'My Drive/sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 'Downloaded successfully', '2026-08-13 15:37:09'),
(160, 3, 'download', 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 'Downloaded successfully', '2026-08-13 15:37:09'),
(161, 3, 'download', 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 'Downloaded successfully', '2026-08-13 15:37:09'),
(162, 3, 'download', 'Documents/FileService.php', '48ffe79bd6b767a9c617edc1b7a466a40ec097cead595b7e500a2b1b9bd79fc4', 'Downloaded successfully', '2026-08-14 16:42:11'),
(163, 3, 'download', 'Documents/APIs_25-07-24.php', '4d324759c438250741121ebb6c0356fe319bce5c2d98750443aa09d6d3de9cd9', 'Downloaded successfully', '2026-08-14 16:42:11'),
(164, 3, 'download', 'Projects/NAS/FileService.php', '7ed4f20b51effd6f21a7b6bdb980ee6692a36ea8a164144b1270aa96bf636520', 'Downloaded successfully', '2026-08-14 16:42:11'),
(165, 3, 'download', 'Projects/NAS/StorageService.php', '06db2cbcb4edbb246e4003b702347b1be8ba9232db310f6c140e040e9cfd7ce9', 'Downloaded successfully', '2026-08-14 16:42:11'),
(166, 3, 'download', 'Documents/StorageService.php', '06db2cbcb4edbb246e4003b702347b1be8ba9232db310f6c140e040e9cfd7ce9', 'Downloaded successfully', '2026-08-14 16:42:11'),
(167, 3, 'download', 'My Drive/Play_store_and_User_reviews_set_1.csv', 'dff6d9a15b109b8aea3a9be1ead5644a23a29ab60ef24bd2d04b972fdf663a44', 'Downloaded successfully', '2026-08-14 16:42:11'),
(168, 3, 'download', 'Documents/users.sql', 'a499449cca1bad9b2eb4440ed0716523b2cfc9a0ebd76748cb2ae9897e646106', 'Downloaded successfully', '2026-08-14 16:42:11'),
(169, 3, 'download', 'Documents/Alma Linux Installations (2).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 'Downloaded successfully', '2026-08-14 16:42:11'),
(170, 3, 'download', 'Archives/modalbrand.zip', '95d1cc13e23b14fa248bcdd165de2b12c50c219694f55df6a23d709f7a244c15', 'Downloaded successfully', '2026-08-14 16:42:11'),
(171, 3, 'download', 'My Drive/Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-14 16:42:11'),
(172, 3, 'download', 'My Drive/app/AuthService.php', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 'Downloaded successfully', '2026-08-14 16:42:11'),
(173, 3, 'download', 'My Drive/app/BaseController.php', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 'Downloaded successfully', '2026-08-14 16:42:11'),
(174, 3, 'download', 'My Drive/app/AuthController.php', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 'Downloaded successfully', '2026-08-14 16:42:11'),
(175, 3, 'download', 'My Drive/app/DashboardController.php', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 'Downloaded successfully', '2026-08-14 16:42:11'),
(176, 3, 'download', 'My Drive/app/HomeController.php', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 'Downloaded successfully', '2026-08-14 16:42:11'),
(177, 3, 'download', 'My Drive/app/AuthController (1).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-14 16:42:11'),
(178, 3, 'download', 'My Drive/folder/help_and_knowledge_base/articles/1755490381_1755490381_296342e3efd59334904d.png', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 'Downloaded successfully', '2026-08-14 16:42:11'),
(179, 3, 'download', 'Projects/NAS_TEST/innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-14 16:42:11'),
(180, 3, 'download', 'Documents/nastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 'Downloaded successfully', '2026-08-14 16:42:11'),
(181, 3, 'download', 'Documents/Alma Linux Installations.docx', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 'Downloaded successfully', '2026-08-14 16:42:11'),
(182, 3, 'download', 'Documents/Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 'Downloaded successfully', '2026-08-14 16:42:11'),
(183, 3, 'download', 'Documents/IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 'Downloaded successfully', '2026-08-14 16:42:11'),
(184, 3, 'download', 'My Drive/folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 'Downloaded successfully', '2026-08-14 16:42:11'),
(185, 3, 'download', 'My Drive/folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 'Downloaded successfully', '2026-08-14 16:42:11'),
(186, 3, 'download', 'My Drive/folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 'Downloaded successfully', '2026-08-14 16:42:11'),
(187, 3, 'download', 'My Drive/folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 'Downloaded successfully', '2026-08-14 16:42:11'),
(188, 3, 'download', 'My Drive/folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 'Downloaded successfully', '2026-08-14 16:42:11'),
(189, 3, 'download', 'My Drive/sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 'Downloaded successfully', '2026-08-14 16:42:11'),
(190, 3, 'download', 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 'Downloaded successfully', '2026-08-14 16:42:11'),
(191, 3, 'download', 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 'Downloaded successfully', '2026-08-14 16:42:12'),
(192, 3, 'download', 'Documents/Database.php', 'efdc5b99c90a8361e4581f35ab404ff06f2825d93614b3d5f8fb14f4dff50898', 'Downloaded successfully', '2026-08-14 17:27:21'),
(193, 3, 'download', 'Documents/StorageService (1).php', '8c774631363631d755cee44f62378711b2c3dd6f0ae4fec04dad8ffa85eb5e33', 'Downloaded successfully', '2026-08-14 17:27:21'),
(194, 3, 'download', 'My Drive/app/AuthController (2).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 'Downloaded successfully', '2026-08-14 17:27:21'),
(195, 3, 'upload', 'My Drive/arch.txt', '535264adba1fe527b28264cb2acd37dcfc3b557ae0c1ed5df1c3c2aed348d877', 'Uploaded from browser sync client', '2026-08-17 17:15:56'),
(196, 3, 'upload', 'My Drive/NAS/css/style.css', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 'Uploaded from browser sync client', '2026-08-17 17:20:56'),
(197, 3, 'upload', 'My Drive/NAS/css/home-style.css', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 'Uploaded from browser sync client', '2026-08-17 17:20:56'),
(198, 3, 'upload', 'My Drive/NAS/images/efactor.png', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 'Uploaded from browser sync client', '2026-08-17 17:20:57'),
(199, 3, 'upload', 'My Drive/NAS/js/app.js', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 'Uploaded from browser sync client', '2026-08-17 17:20:57'),
(200, 3, 'upload', 'My Drive/NAS/js/app_12-8-26.js', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 'Uploaded from browser sync client', '2026-08-17 17:20:58'),
(201, 3, 'upload', 'My Drive/NAS/js/app_13-8-26(latest).js', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 'Uploaded from browser sync client', '2026-08-17 17:20:58'),
(202, 3, 'upload', 'My Drive/NAS/js/config.js', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 'Uploaded from browser sync client', '2026-08-17 17:20:58'),
(203, 3, 'upload', 'My Drive/NAS/js/app_13-8-26.js', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 'Uploaded from browser sync client', '2026-08-17 17:20:59'),
(204, 3, 'upload', 'My Drive/NAS/js/preview.js', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 'Uploaded from browser sync client', '2026-08-17 17:20:59'),
(205, 3, 'upload', 'My Drive/NAS/js/share.js', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 'Uploaded from browser sync client', '2026-08-17 17:20:59'),
(206, 3, 'upload', 'My Drive/NAS/js/excelpreview.js', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 'Uploaded from browser sync client', '2026-08-17 17:20:59'),
(207, 3, 'upload', 'My Drive/NAS/js/sync.js', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 'Uploaded from browser sync client', '2026-08-17 17:21:00'),
(208, 3, 'upload', 'My Drive/NAS/js/utils.js', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 'Uploaded from browser sync client', '2026-08-17 17:21:00'),
(209, 3, 'upload', 'My Drive/NAS/js/websocket-client.js', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 'Uploaded from browser sync client', '2026-08-17 17:21:00'),
(210, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 0 folders, 0 uploads, 0 downloads, 0 skipped, 96 errors', '2026-08-17 17:26:43'),
(211, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 0 folders, 0 uploads, 0 downloads, 0 skipped, 96 errors', '2026-08-17 17:28:11'),
(212, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 0 folders, 0 uploads, 0 downloads, 0 skipped, 78 errors', '2026-08-17 17:38:29'),
(213, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 26 folders, 0 uploads, 0 downloads, 50 skipped, 2 errors', '2026-08-17 17:42:19'),
(214, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 26 folders, 0 uploads, 0 downloads, 50 skipped, 2 errors', '2026-08-18 09:11:12'),
(215, 3, 'upload', 'My Drive/NAS/css/style.css', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 'Uploaded from browser sync client', '2026-08-18 09:11:19'),
(216, 3, 'upload', 'My Drive/NAS/css/home-style.css', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 'Uploaded from browser sync client', '2026-08-18 09:11:19'),
(217, 3, 'upload', 'My Drive/NAS/images/efactor.png', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 'Uploaded from browser sync client', '2026-08-18 09:11:19'),
(218, 3, 'upload', 'My Drive/NAS/js/app.js', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 'Uploaded from browser sync client', '2026-08-18 09:11:20'),
(219, 3, 'upload', 'My Drive/NAS/js/app_12-8-26.js', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 'Uploaded from browser sync client', '2026-08-18 09:11:20'),
(220, 3, 'upload', 'My Drive/NAS/js/app_13-8-26(latest).js', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 'Uploaded from browser sync client', '2026-08-18 09:11:20'),
(221, 3, 'upload', 'My Drive/NAS/js/app_13-8-26.js', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 'Uploaded from browser sync client', '2026-08-18 09:11:21'),
(222, 3, 'upload', 'My Drive/NAS/js/config.js', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 'Uploaded from browser sync client', '2026-08-18 09:11:21'),
(223, 3, 'upload', 'My Drive/NAS/js/preview.js', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 'Uploaded from browser sync client', '2026-08-18 09:11:21'),
(224, 3, 'upload', 'My Drive/NAS/js/sync.js', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 'Uploaded from browser sync client', '2026-08-18 09:11:21'),
(225, 3, 'upload', 'My Drive/NAS/js/share.js', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 'Uploaded from browser sync client', '2026-08-18 09:11:22'),
(226, 3, 'upload', 'My Drive/NAS/js/excelpreview.js', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 'Uploaded from browser sync client', '2026-08-18 09:11:22'),
(227, 3, 'upload', 'My Drive/NAS/js/utils.js', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 'Uploaded from browser sync client', '2026-08-18 09:11:22'),
(228, 3, 'upload', 'My Drive/NAS/js/websocket-client.js', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 'Uploaded from browser sync client', '2026-08-18 09:11:23'),
(229, 3, 'upload', 'sync_complete', NULL, 'Client sync complete: 4 folders, 14 uploads, 0 downloads, 48 skipped, 0 errors', '2026-08-18 09:11:23'),
(230, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 30 folders, 0 uploads, 0 downloads, 64 skipped, 2 errors', '2026-08-18 09:12:20'),
(231, 3, 'skip', 'sync_complete', NULL, 'Client sync complete: 0 folders, 0 uploads, 0 downloads, 62 skipped, 0 errors', '2026-08-18 09:13:14'),
(232, 5, 'upload', 'sync_complete', NULL, 'Client sync complete: 8 folders, 0 uploads, 3 downloads, 0 skipped, 0 errors', '2026-08-18 09:43:41'),
(233, 5, 'upload', 'sync_complete', NULL, 'Client sync complete: 8 folders, 0 uploads, 0 downloads, 3 skipped, 0 errors', '2026-08-18 09:44:18'),
(234, 5, 'skip', 'sync_complete', NULL, 'Client sync complete: 0 folders, 0 uploads, 0 downloads, 3 skipped, 0 errors', '2026-08-18 09:44:20'),
(235, 3, 'error', 'sync_complete', NULL, 'Client sync complete: 30 folders, 0 uploads, 50 downloads, 14 skipped, 2 errors', '2026-08-18 11:30:57');

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
(13, 3, 'sync_6a82f473c9cb17.63921182', 'completed', '2026-08-17 11:45:55', '2026-08-17 11:56:43', 0, 0, NULL, NULL),
(14, 3, 'sync_6a83d45e929f68.41035487', 'completed', '2026-08-18 03:41:18', '2026-08-18 03:41:23', 0, 0, NULL, NULL),
(15, 3, 'sync_6a83d4d2218c52.73253600', 'completed', '2026-08-18 03:43:14', '2026-08-18 03:43:14', 0, 0, NULL, NULL),
(16, 5, 'sync_6a83dc1c986dd2.91565291', 'completed', '2026-08-18 04:14:20', '2026-08-18 04:14:20', 0, 0, NULL, NULL),
(17, 3, 'sync_6a852b3423b8e5.62346009', 'running', '2026-08-19 04:04:04', NULL, 0, 0, NULL, NULL);

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
  `sync_status` enum('pending','synced','uploading','downloading','error','idle','running','completed','failed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `sync_direction` enum('upload','download','none') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'none',
  `last_sync` datetime DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `retry_count` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sync_status`
--

INSERT INTO `sync_status` (`id`, `user_id`, `file_id`, `local_path`, `file_hash`, `file_size`, `modified_time`, `sync_status`, `sync_direction`, `last_sync`, `error_message`, `retry_count`, `created_at`, `updated_at`) VALUES
(26, 3, 126, 'New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 09:54:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:54:45', '2026-08-14 17:27:21'),
(27, 3, 128, 'New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 09:54:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:54:45', '2026-08-14 17:27:21'),
(28, 3, 130, 'New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 09:54:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:54:45', '2026-08-14 17:27:21'),
(29, 3, 132, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 09:54:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:54:45', '2026-08-14 17:27:21'),
(30, 3, 134, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 09:56:53', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:53', '2026-08-14 17:27:21'),
(31, 3, 136, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(32, 3, 138, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(33, 3, 140, 'Projects/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(34, 3, 142, 'Projects/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(35, 3, 144, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(36, 3, 146, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(37, 3, 148, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 09:56:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 09:56:54', '2026-08-14 17:27:21'),
(38, 3, 150, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-06 10:32:10', '2026-08-18 11:30:56'),
(39, 3, 151, 'user_3/documents/6a741562ee02e.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 10:32:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:32:27', '2026-08-14 17:27:21'),
(40, 3, 152, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-06 10:34:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:50', '2026-08-14 17:27:21'),
(41, 3, 154, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 10:34:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:50', '2026-08-14 17:27:21'),
(42, 3, 156, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 10:34:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:50', '2026-08-14 17:27:21'),
(43, 3, 158, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 10:34:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:50', '2026-08-14 17:27:21'),
(44, 3, 160, 'Projects/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-06 10:34:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:50', '2026-08-14 17:27:21'),
(45, 3, 162, 'Projects/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 10:34:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:51', '2026-08-14 17:27:21'),
(46, 3, 164, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 10:34:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:51', '2026-08-14 17:27:21'),
(47, 3, 166, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 10:34:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:51', '2026-08-14 17:27:21'),
(48, 3, 168, 'Projects/bye/Alma Linux Installations (1) (1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 10:34:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:51', '2026-08-14 17:27:21'),
(49, 3, 170, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 10:34:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:34:51', '2026-08-14 17:27:21'),
(50, 3, 172, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 10:47:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(51, 3, 174, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 10:47:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(52, 3, 176, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 10:47:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(53, 3, 10, 'My Drive/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-07 14:26:58', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(54, 3, 11, 'My Drive/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-07 14:26:58', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(55, 3, 178, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 10:47:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(56, 3, 180, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 10:47:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(57, 3, 4, 'Projects/hi/Alma Linux Installations (2).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(58, 3, 182, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 10:47:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:47:13', '2026-08-14 17:27:21'),
(59, 3, 184, 'Projects/hi/Alma Linux Installations (2).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 10:51:07', '2026-08-14 17:27:21'),
(60, 3, 185, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 11:01:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:01:12', '2026-08-14 17:27:21'),
(61, 3, 187, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 11:01:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:01:12', '2026-08-14 17:27:21'),
(62, 3, 189, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 11:01:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:01:12', '2026-08-14 17:27:21'),
(63, 3, 191, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 11:01:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:01:12', '2026-08-14 17:27:21'),
(64, 3, 193, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 11:01:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:01:13', '2026-08-14 17:27:21'),
(65, 3, 195, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 11:01:13', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:01:13', '2026-08-14 17:27:21'),
(66, 3, 197, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 11:19:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:19:27', '2026-08-14 17:27:21'),
(67, 3, 199, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 11:19:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:19:27', '2026-08-14 17:27:21'),
(68, 3, 201, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 11:19:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:19:27', '2026-08-14 17:27:21'),
(69, 3, 203, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 11:19:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:19:27', '2026-08-14 17:27:21'),
(70, 3, 205, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 11:19:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:19:27', '2026-08-14 17:27:21'),
(71, 3, 207, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 11:19:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:19:27', '2026-08-14 17:27:21'),
(72, 3, 198, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 11:21:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:21:47', '2026-08-14 17:27:21'),
(73, 3, 200, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 11:21:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:21:47', '2026-08-14 17:27:21'),
(74, 3, 202, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 11:21:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:21:47', '2026-08-14 17:27:21'),
(75, 3, 204, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 11:21:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:21:47', '2026-08-14 17:27:21'),
(76, 3, 206, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 11:21:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:21:47', '2026-08-14 17:27:21'),
(77, 3, 208, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 11:21:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:21:47', '2026-08-14 17:27:21'),
(78, 2, 18, 'user_2/archives/6a71d0d2d2454.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(79, 2, 19, 'user_2/documents/6a71d0d2d6a9b.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(80, 2, 20, 'user_2/documents/6a71d0d2dab08.docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 56252, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(81, 2, 21, 'user_2/documents/6a71d0d2de072.pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(82, 2, 23, 'user_2/projects/6a71d0d2e903f.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(83, 2, 24, 'user_2/projects/6a71d0d2ecc4a.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(84, 2, 25, 'Projects/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(85, 2, 26, 'Projects/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(86, 2, 27, 'user_2/videos/6a71d0d30d309.mp4', 'c62527bb784dcbf4836c602861b41f8f5074cd4f10cb062f069bdcc486822d8e', 1330812, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(87, 2, 30, 'My Drive/commands.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(88, 2, 68, 'My Drive/6a685cf66b672 (1).jpg', '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 190842, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(89, 2, 70, 'My Drive/s4 (1).jpg', '6ca8e2d6e943759401e9f5080e0518962af1321c1960ee1f0d31b610e01b7f16', 98370, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(90, 2, 72, 'user_2/images/6a732fa69f10d.jpg', '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 190842, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(91, 2, 74, 'My Drive/socket (1).png', '0f77bb6c2d73970a88a377c05b3d79576585846c15cf52a5ca1c13511c28d895', 596, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(92, 2, 76, 'My Drive/IOT  SIM LIST new (1).xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 3032384, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(93, 2, 78, 'My Drive/index (1).php', 'f49a9a37cef56fa5d35ea325ef5ce8955f61ec10134ca135c6bbde048178e6c7', 37462, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(94, 2, 35, 'user_2/computers/6a71da60c6597.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(95, 2, 69, 'user_2/computers/6a732fa689339.jpg', '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 190842, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(96, 2, 71, 'user_2/computers/6a732fa697b8f.jpg', '6ca8e2d6e943759401e9f5080e0518962af1321c1960ee1f0d31b610e01b7f16', 98370, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(97, 2, 73, 'user_2/computers/6a732fa69f10d.jpg', '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 190842, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(98, 2, 75, 'user_2/computers/6a732fa6a7850.png', '0f77bb6c2d73970a88a377c05b3d79576585846c15cf52a5ca1c13511c28d895', 596, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(99, 2, 77, 'user_2/computers/6a732fa6ae206.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 3032384, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(100, 2, 79, 'user_2/computers/6a732fa6bc55f.php', 'f49a9a37cef56fa5d35ea325ef5ce8955f61ec10134ca135c6bbde048178e6c7', 37462, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(101, 2, 7, 'user_2/archives/6a7161e2235b9.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(102, 2, 8, 'user_2/documents/6a7165eda2567.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(103, 2, 10, 'user_2/projects/modalbrand/6a7175dcdbd9d.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(104, 2, 11, 'user_2/projects/modalbrand/6a7175dce05a9.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 11:30:06', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(105, 2, 12, 'Videos/KVM Manager · Dark - Google Chrome 2026-07-03 13-05-00.mp4', 'c62527bb784dcbf4836c602861b41f8f5074cd4f10cb062f069bdcc486822d8e', 1330812, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(106, 2, 4, 'Projects/bye2/b1/Alma Linux Installation(1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(107, 2, 13, 'My Drive/alma linux server  version details.docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 56252, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(108, 2, 15, 'My Drive/final_ppt.pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(109, 2, 6, 'Projects/bye2/b1/bye.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-07 13:21:46', 'completed', 'none', NULL, NULL, 0, '2026-08-06 11:30:06', '2026-08-07 13:21:46'),
(141, 3, 209, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 11:43:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:43:38', '2026-08-14 17:27:21'),
(142, 3, 211, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 11:43:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:43:38', '2026-08-14 17:27:21'),
(143, 3, 213, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 11:43:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:43:38', '2026-08-14 17:27:21'),
(144, 3, 215, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 11:43:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:43:38', '2026-08-14 17:27:21'),
(145, 3, 217, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 11:43:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:43:38', '2026-08-14 17:27:21'),
(146, 3, 219, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 11:43:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:43:38', '2026-08-14 17:27:21'),
(147, 3, 221, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 11:51:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:51:06', '2026-08-14 17:27:21'),
(148, 3, 223, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 11:51:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:51:06', '2026-08-14 17:27:21'),
(149, 3, 225, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 11:51:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:51:06', '2026-08-14 17:27:21'),
(150, 3, 227, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 11:51:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:51:06', '2026-08-14 17:27:21'),
(151, 3, 229, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 11:51:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:51:06', '2026-08-14 17:27:21'),
(152, 3, 231, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 11:51:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 11:51:06', '2026-08-14 17:27:21'),
(153, 3, 233, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 12:01:35', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:01:35', '2026-08-14 17:27:21'),
(154, 3, 235, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 12:01:36', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:01:36', '2026-08-14 17:27:21'),
(155, 3, 237, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 12:01:36', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:01:36', '2026-08-14 17:27:21'),
(156, 3, 239, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 12:01:36', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:01:36', '2026-08-14 17:27:21'),
(157, 3, 241, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 12:01:36', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:01:36', '2026-08-14 17:27:21'),
(158, 3, 243, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 12:01:36', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:01:36', '2026-08-14 17:27:21'),
(159, 3, 234, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 12:12:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:08:19', '2026-08-14 17:27:21'),
(160, 3, 236, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 12:12:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:08:19', '2026-08-14 17:27:21'),
(161, 3, 238, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 12:12:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:08:19', '2026-08-14 17:27:21'),
(162, 3, 240, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 12:12:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:08:19', '2026-08-14 17:27:21'),
(163, 3, 242, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 12:12:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:08:19', '2026-08-14 17:27:21'),
(164, 3, 244, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 12:12:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:08:19', '2026-08-14 17:27:21'),
(165, 3, 245, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 12:30:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:30:09', '2026-08-14 17:27:21'),
(166, 3, 247, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 12:30:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:30:09', '2026-08-14 17:27:21'),
(167, 3, 249, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 12:30:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:30:09', '2026-08-14 17:27:21'),
(168, 3, 251, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 12:30:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:30:09', '2026-08-14 17:27:21'),
(169, 3, 253, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 12:30:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:30:09', '2026-08-14 17:27:21'),
(170, 3, 255, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 12:30:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:30:09', '2026-08-14 17:27:21'),
(171, 3, 257, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 12:44:10', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:44:10', '2026-08-14 17:27:21'),
(172, 3, 258, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 12:44:10', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:44:10', '2026-08-14 17:27:21'),
(173, 3, 259, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 12:44:10', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:44:10', '2026-08-14 17:27:21'),
(174, 3, 260, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 12:44:10', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:44:10', '2026-08-14 17:27:21'),
(175, 3, 261, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 12:44:10', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:44:10', '2026-08-14 17:27:21'),
(176, 3, 262, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 12:44:10', 'idle', 'none', NULL, NULL, 0, '2026-08-06 12:44:10', '2026-08-14 17:27:21'),
(177, 3, 263, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 13:25:02', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:20:27', '2026-08-14 17:27:21'),
(178, 3, 264, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 13:25:02', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:20:28', '2026-08-14 17:27:21'),
(179, 3, 265, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 13:25:02', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:20:28', '2026-08-14 17:27:21'),
(180, 3, 266, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 13:25:02', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:20:28', '2026-08-14 17:27:21'),
(181, 3, 267, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 13:25:02', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:20:28', '2026-08-14 17:27:21'),
(182, 3, 268, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 13:25:02', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:20:28', '2026-08-14 17:27:21'),
(183, 3, 30, 'Projects/NAS_TEST/innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-13 10:50:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:25:02', '2026-08-14 17:27:21'),
(184, 3, 269, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 13:40:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:40:54', '2026-08-14 17:27:21'),
(185, 3, 270, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 13:40:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:40:54', '2026-08-14 17:27:21'),
(186, 3, 271, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 13:40:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:40:54', '2026-08-14 17:27:21'),
(187, 3, 272, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 13:40:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:40:54', '2026-08-14 17:27:21'),
(188, 3, 273, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 13:40:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:40:54', '2026-08-14 17:27:21'),
(189, 3, 274, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 13:40:54', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:40:54', '2026-08-14 17:27:21'),
(190, 3, 275, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 13:43:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:43:06', '2026-08-14 17:27:21'),
(191, 3, 276, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 13:43:06', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:43:06', '2026-08-14 17:27:21'),
(192, 3, 277, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 13:43:07', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:43:07', '2026-08-14 17:27:21'),
(193, 3, 278, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 13:43:07', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:43:07', '2026-08-14 17:27:21'),
(194, 3, 279, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 13:43:07', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:43:07', '2026-08-14 17:27:21'),
(195, 3, 280, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 13:43:07', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:43:07', '2026-08-14 17:27:21'),
(196, 3, 281, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 13:46:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:46:47', '2026-08-14 17:27:21'),
(197, 3, 282, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 13:46:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:46:47', '2026-08-14 17:27:21'),
(198, 3, 283, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 13:46:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:46:47', '2026-08-14 17:27:21'),
(199, 3, 284, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 13:46:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:46:47', '2026-08-14 17:27:21'),
(200, 3, 285, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 13:46:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:46:47', '2026-08-14 17:27:21'),
(201, 3, 286, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 13:46:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:46:47', '2026-08-14 17:27:21'),
(202, 3, 287, 'user_3/documents/6a744318eaa81.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 13:47:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:47:28', '2026-08-14 17:27:21'),
(203, 3, 288, 'user_3/images/6a744324e7f7c.jpg', '48ed531df36072e24b06fdedeb0c2fb61b1ae80c58ea687e2e55c189b8fdb632', 190842, '2026-08-06 13:47:40', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:47:40', '2026-08-14 17:27:21'),
(204, 3, 289, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:56:53', '2026-08-14 17:27:21'),
(205, 3, 290, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:56:53', '2026-08-14 17:27:21'),
(206, 3, 291, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:56:53', '2026-08-14 17:27:21'),
(207, 3, 292, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:56:53', '2026-08-14 17:27:21'),
(208, 3, 293, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:56:54', '2026-08-14 17:27:21'),
(209, 3, 294, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 13:56:54', '2026-08-14 17:27:21'),
(210, 3, 295, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.css', 'fa8fb3835f52fdff239c6f4783e48c97f085b64a276c40d0625b0808142153de', 75728, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(211, 3, 296, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.min.css', 'fa05f478ec29045fb23ebf7826a7e9ec525b05670bfdb1fc25f7269fc246d8d3', 51515, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(212, 3, 297, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.rtl.css', '17a6ae3e016f18812363099c581a60568939bdf4906467ea6b79648041c90ab7', 75801, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(213, 3, 298, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.rtl.min.css', 'bec66f48d15c96c387ecf50d2e104107a5f8e4bf3b9727724568f47b06534647', 51590, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(214, 3, 299, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.css', '4c3393cc5e4097930260a45d9a04c1a8bf3b0643120c7e125af1c3cdd16546b1', 6419, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(215, 3, 300, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.min.css', '05c355bbf68dcd59f50ac2850445fa4bb5370150d6a603c480eaa20734a35788', 4776, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(216, 3, 301, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.rtl.css', '5a65c57fab7e8f102fbf39c0888db59756ec0474b826ded261a487c45310e00c', 6393, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(217, 3, 302, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.rtl.min.css', '113aab4c7d4bc757bdf0150106c5e632fc021363b73104d7381a58bfd00430ae', 4848, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(218, 3, 303, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.css', '8c38cba666d88b6946b8ce68ff8c30b0db6f5eeff328f9f01e72e448ae1f74c4', 71446, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(219, 3, 304, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.min.css', '8740fa53f0c9fdab546bf18ca00d84dd230251dabdcefaef6d5446d7cfe1dc01', 49492, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(220, 3, 305, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.rtl.css', '9404102e12b64d1d4a9b277774988fdff751dc4ab7e094e885acb05d0008daa0', 71303, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(221, 3, 306, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.rtl.min.css', 'cb187c7b4022b62857700c7fbea6d98ae216f8a0c203bc246241ad592073bfe0', 49419, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(222, 3, 307, 'may_09_messages/assets/bootstrap/css/bootstrap.css', '852059bf5eceae541fb77a8512e352b0c5f26e3b9f27eb91c47a73e88244767c', 203731, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(223, 3, 308, 'may_09_messages/assets/bootstrap/css/bootstrap.min.css', '6a669adda63d8e1d09a6455d7dc95bb126f27aa483d10f0b54ce14c303fa466f', 153117, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(224, 3, 309, 'may_09_messages/assets/bootstrap/css/bootstrap.min.css.map', '907fdc5d5ea3bf6de43add9285f2af433d214c8dd60296fc48422a11f9539d6c', 422371, '2026-08-06 15:02:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:43', '2026-08-14 17:27:21'),
(225, 3, 310, 'may_09_messages/assets/bootstrap/css/bootstrap.rtl.css', 'a02d0cf104d191419322747bdd134ee4bccadd515dee048e528e27b416767fe2', 202974, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(226, 3, 311, 'may_09_messages/assets/bootstrap/css/bootstrap.rtl.min.css', '4af1e04118a5b53d828daa5b91f9647128ed28f85a2bed364b8db542a2873810', 153222, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(227, 3, 312, 'may_09_messages/assets/bootstrap/js/bootstrap.bundle.js', '46b44668a6a143301d12ab081019b9d48f3a7188a7f2cf6ec995c171a3bcd90d', 216787, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(228, 3, 313, 'may_09_messages/assets/bootstrap/js/bootstrap.bundle.min.js', '6fd9a7b3f4692ff54d61155781bee462c88e2989d00b40e2d7e3fe425cac736e', 80827, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(229, 3, 314, 'may_09_messages/assets/bootstrap/js/bootstrap.esm.js', '2cbc5a1149fb451f01d77f775053a12cbd9759d917fe153ca5f86a35a025fb3d', 147542, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(230, 3, 315, 'may_09_messages/assets/bootstrap/js/bootstrap.esm.min.js', '6fc41fafcb8aa8833f776f3f89e6d609b60cc38b652e37c0032b804c62288d74', 76585, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(231, 3, 316, 'may_09_messages/assets/bootstrap/js/bootstrap.js', '6fe4aaeefa2567ef0698411e7e3b5d7657cbc5a42ad55d6c05cf48daa939d9b0', 156888, '2026-08-06 15:02:44', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:44', '2026-08-14 17:27:21'),
(232, 3, 317, 'may_09_messages/assets/bootstrap/js/bootstrap.min.js', 'da5a8e7b12eb38994c867304d3325b93b8b7cb4002499e9e9547c010a1420f2a', 62417, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(233, 3, 318, 'may_09_messages/assets/css/app.all.css', 'f5ac84a855fdff6f406f8c406212a0c9f778eb566eaaebaeb6983e2f7207bdd6', 202593, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(234, 3, 319, 'may_09_messages/assets/css/color/00BCD4.css', '9954804c875a52910373071fce7014f1c1eb839cb72e7d7fa17652ab355cc142', 2877, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(235, 3, 320, 'may_09_messages/assets/css/color/17a589.css', '931768afab86a5e19a94a93f3be733e2a04895d7809a8ea84631a8fb62bb7a1b', 2969, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(236, 3, 321, 'may_09_messages/assets/css/color/1E202D.css', 'e9551cfa967a6fd7d157e5891e1790d006f95a9457b0489c5b12f761a459868e', 25590, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(237, 3, 322, 'may_09_messages/assets/css/color/1d2632.css', 'f5a53dd81a533e615f77f4121a354d5e02908c47d108e038c5fbb80f48bb6e63', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(238, 3, 323, 'may_09_messages/assets/css/color/2471a3.css', '48f1785483e76bbaa4095164463b3f340a1e360e6afc4279f0dcfa1d2f4fdf4f', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(239, 3, 324, 'may_09_messages/assets/css/color/2e4053.css', 'a177b3462fe65ca35fe3c41d83a8909d27d9cef78f733e3c368e1e7785c14052', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(240, 3, 325, 'may_09_messages/assets/css/color/2e86c1.css', '78de9821f785dcd27e13a14e240a613b96170597b7a5b48bbb29d986e4ab57a4', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(241, 3, 326, 'may_09_messages/assets/css/color/404040.css', '28f4dadbb6974ba45590246b6e606cec516f14e315fa6596045af290e658dd4d', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(242, 3, 327, 'may_09_messages/assets/css/color/555a61.css', '16ee4d27c546bb6aa374a89224a9972ae88473f5e58cb8ba0e6118a5800a1584', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(243, 3, 328, 'may_09_messages/assets/css/color/557bbb.css', 'c7f2313ce216cae6f344aac30a4782849810b8cdd1cf1e3f5fdced41a90d24a8', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(244, 3, 329, 'may_09_messages/assets/css/color/5d78ff.css', 'bfeaed9e2787eabb8b2e80ef091775ded2ee5edfd57ad39baf0764503f0127e5', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(245, 3, 330, 'may_09_messages/assets/css/color/839192.css', 'dee7d21d73228a776228e080c68b93f1ff311af8652f56f05f307a8609672515', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(246, 3, 331, 'may_09_messages/assets/css/color/83c340.css', '9070c692b0cda115d75f66143692935e6f5e6423a53daef1c4453d76e9413090', 2969, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(247, 3, 332, 'may_09_messages/assets/css/color/884ea0.css', 'aef6f86b4b5bf764a6406666b0134b5d51130b9b5e3e7355affeaaee96c09ba1', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(248, 3, 333, 'may_09_messages/assets/css/color/a6acaf.css', '4e8d42058bce652778586753b8b5c0aa99c7fdad63ed87fc36ce613d5150d790', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(249, 3, 334, 'may_09_messages/assets/css/color/a93226.css', '99959186002d473c38408a3fee9689709909cfa45b3a6e18f35f8e613db63af1', 2972, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(250, 3, 335, 'may_09_messages/assets/css/color/d68910.css', '00669d1c3e4e5f836278cdb99f24f3053e466b54245ec623503ed38d4009fe6b', 2969, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(251, 3, 336, 'may_09_messages/assets/js/index.html', 'c703afa89e7517dd5020ac2960b7e14b44d4010ef02d5e0f6e34b9629935a3ce', 123, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(252, 3, 337, 'may_09_messages/assets/css/custom-style.css', 'f8b9abeb07ab0428da89592e9f50e56965689607f0c736c873c9cf70ab0a13a9', 41, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(253, 3, 338, 'may_09_messages/assets/css/fonts/opensans/opensans-bold.woff', 'e83c0f6faf72f8dc6d60141c4fd6785d871cede7589719f368136d42b6f7c421', 21816, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(254, 3, 339, 'may_09_messages/assets/css/fonts/opensans/opensans-light.woff', 'a6a5d7cbfec632fa7463ebca7babf4b1971b785cd1a26b596d58b8a831001136', 21744, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(255, 3, 340, 'may_09_messages/assets/css/fonts/opensans/opensans.woff', 'b9876c49ea883cf289637c99b4f29fa51e66c517b31a48fde22ad18e65f2ebc2', 21080, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(256, 3, 341, 'may_09_messages/assets/css/invoice.css', '129bab36478d91786c46fdea3fad63d2c34e490b3ec115093a32888fd7bf2c7a', 2211, '2026-08-06 15:02:45', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:45', '2026-08-14 17:27:21'),
(257, 3, 342, 'may_09_messages/assets/css/message-style.css', '3ddc5518d0515ab79544e5d97a6147cb30f060da4177780e371bffdf5b6bf0aa', 23578, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(258, 3, 343, 'may_09_messages/assets/css/rtl.css', '941a5953cf8feb8e92c2561a98106dc26a6bc3aba23daaf6fa8b6f2beb37db1c', 7284, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(259, 3, 344, 'may_09_messages/assets/images/avatar-bot.jpg', '08ebf5e3648256b38fc4075f70d77c49aa3801c26b9b201dfb1e175cce824d04', 3137, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(260, 3, 345, 'may_09_messages/assets/images/avatar.jpg', '88aa41fb968a6c34acbef2ee2da4f8decfd392dd540cf05c3077261699ac24e4', 12708, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(261, 3, 346, 'may_09_messages/assets/images/bitbucket_logo.png', '5c91c0338df37e5dfc4dfe43c3e7f70a71458393bf3caefe9014e9fa1b401b08', 1050, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(262, 3, 347, 'may_09_messages/assets/images/check_mark.png', '32a3a2eef5ec581d1d2e8c70df92019b860972cddde917490fe236050249a868', 3069, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(263, 3, 348, 'may_09_messages/assets/images/checkbox-un-checked.png', '26fa1638ae618b0af46f67c254b5194bc01318e4fbfbd6762b115c32bee5c820', 231, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(264, 3, 349, 'may_09_messages/assets/images/checkbox.png', 'c47ac259971c18cc33ee9e42ab175dc451c6a5ad51a3434951d2230baad74e1c', 151, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(265, 3, 350, 'may_09_messages/assets/images/favicon.png', '03c18340660a0db289a84f456189be25193bc58cdd8ab4fa465ff2892ca2a74c', 4261, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(266, 3, 351, 'may_09_messages/assets/images/file_preview.jpg', '59370783478e088f061a1377ca512d894e160d317e98df15b979b32c2cec897f', 13986, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(267, 3, 352, 'may_09_messages/assets/images/filepreview.jpg', '3090a2af120ee1dbe7219c21d66f677975c36b7b28e509a1fe6944e02b18c8a4', 1739, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(268, 3, 353, 'may_09_messages/assets/images/github_logo.png', '8867ef04fee7b1edf840866a4312de43ac68ea43a0099235154c959afa403809', 17263, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21');
INSERT INTO `sync_status` (`id`, `user_id`, `file_id`, `local_path`, `file_hash`, `file_size`, `modified_time`, `sync_status`, `sync_direction`, `last_sync`, `error_message`, `retry_count`, `created_at`, `updated_at`) VALUES
(269, 3, 354, 'may_09_messages/assets/images/invoice_style_1.png', '504f0bc4bbc2e73f63461018673f51ebfd7630bdbb9f415243d11ba044ef4e86', 15339, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(270, 3, 355, 'may_09_messages/assets/images/invoice_style_2.png', 'cb84db297304ee141c448701e82c27516db5a403baf54c9cd40292d13a049453', 15253, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(271, 3, 356, 'may_09_messages/assets/images/loader2.gif', '917637a958ae8cffa27da24f8a22596057a3dc7403fb279c6713ca16855c8988', 1737, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(272, 3, 357, 'may_09_messages/assets/images/logo.png', '9644f66867b3c5acdb6ee74c9dd0bea2229252a8886e0d3b91f6f169249e9ae7', 1395, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(273, 3, 358, 'may_09_messages/assets/images/move.png', '89806284556d9a128224d557b1bdae0324296e1f26fc4d140938665558769eca', 3881, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(274, 3, 359, 'may_09_messages/assets/images/search.png', '48978c790592f85e43842a799366182aa39660581fc387088d44a36dad4e9f57', 3025, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(275, 3, 360, 'may_09_messages/assets/images/sort_asc.png', '28fac76596775cff97aba478da505db74cad43f2194e146d7e696cf7480f93e3', 2907, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(276, 3, 361, 'may_09_messages/assets/images/sort_desc.png', '324d3432ece78d54d53ede8690311b72e70bda428102e0b6162345279431b16d', 2931, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(277, 3, 362, 'may_09_messages/assets/images/stripe-payment-logo.png', 'f87df18c1a3b2fa8b791ff81c1ceb55043d33af62dafa8c6303b7cd484087b22', 12022, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(278, 3, 363, 'may_09_messages/assets/images/video_preview.jpg', '3a967df63367d34a3260dff76b31cc4679bfaa8c9ebd49fadcb8b2e618c658de', 10078, '2026-08-06 15:02:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:46', '2026-08-14 17:27:21'),
(279, 3, 364, 'may_09_messages/assets/js/app.all.js', 'a93c9293a4c9171743714defc476ed3d9e94d75b42c9397362989622bcc1f295', 1974732, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(280, 3, 365, 'may_09_messages/assets/js/app.js', '5c592b8d2775272f9b2e6206b831d5e5403dbb4b5d8f3c992752392736431f66', 136237, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(281, 3, 366, 'may_09_messages/assets/js/app.min.js', 'da6d5c7e80ae2ea58e5a3a88515de69da46e3313fba969ba4166eaf8f1112b40', 54418, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(282, 3, 367, 'may_09_messages/assets/js/atwho/caret/jquery.caret.js', 'fbb165850993748024040c2993c996020160af3e5e0c223804cbfac7e0acfb35', 12757, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(283, 3, 368, 'may_09_messages/assets/js/atwho/caret/jquery.caret.min.js', '51507de5e6bd66dd002c56ac7cb46c4b9cf460401d37270deba7d56eb516d8ef', 6262, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(284, 3, 369, 'may_09_messages/assets/js/atwho/css/jquery.atwho.css', 'dd330ce1c1d6f5d7a8416f34a63fd95bf4a0b86143cbd17bd0862111c50df2b9', 1384, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(285, 3, 370, 'may_09_messages/assets/js/atwho/css/jquery.atwho.min.css', '25e25d30705c212a17333d4d219aeca0d3ec1d0ae172d97a109b35b2ee6427df', 919, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(286, 3, 371, 'may_09_messages/assets/js/atwho/js/jquery.atwho.js', '30f584fef85ff365d6615ed989ea7e9d442ccfbacf0406e22ceb948a08800e20', 36334, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(287, 3, 372, 'may_09_messages/assets/js/atwho/js/jquery.atwho.min.js', '24e822f1a04368fdf6507fc218120c584adc94beaae6a5ff3ed404b16e02758f', 20504, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(288, 3, 373, 'may_09_messages/assets/js/awesomplete/awesomplete.css', '2542c339df4c4c7930a767bf5d0c5695b6c6f66a5db7e33674ca7b8afb8ea7ca', 1982, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(289, 3, 374, 'may_09_messages/assets/js/awesomplete/awesomplete.js', '1e07e6c129eb20329835f0333a99e4905369890a85bd9b9bdd90eeb9180e6b6a', 13688, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(290, 3, 375, 'may_09_messages/assets/js/awesomplete/awesomplete.min.js', 'ec7b7af99469e787353d94a94e6075df7039a293a31e8078e1d68857f97854fe', 7555, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(291, 3, 376, 'may_09_messages/assets/js/bootstrap-datepicker/README.md', 'ea48b35e039f3c821db263d2f4dc0a3ae9f24c49ef8a615f0ec30ca49e29cdbf', 1987, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(292, 3, 377, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker.css', 'bb91d3f81747be9aa123c5ef0a8d0c91658f1c3ebe5a743bc0baab038a051038', 17612, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(293, 3, 378, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker.min.css', '9f1ed28c8aba11cd26abcfb4671e4f0201ae61d71832fa72cee3c90abeeae982', 15737, '2026-08-06 15:02:47', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:47', '2026-08-14 17:27:21'),
(294, 3, 379, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker3.css', '6bca3dc478b0bc49c0783274629f2208e7b58fdc6e139f768999a71c7d4a5c33', 23462, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(295, 3, 380, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker3.min.css', '2695bf33b2936ec801798a54e5974c6406b4b0c8c9d6e630a74064671fe52831', 21106, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(296, 3, 381, 'may_09_messages/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js', '447bb8dc7271c982934c09cc7886e9f003ac4485319b53980cda780b8d8339d5', 59935, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(297, 3, 382, 'may_09_messages/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js', 'c129f5956bc8e9c3a2838069e9a3ff50ad7a540a077b42934b2c5d7c902f4efc', 33700, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(298, 3, 383, 'may_09_messages/assets/js/bootstrap-timepicker/README-important.txt', '67147b2303f70d0a2d1881e7443787b9b751e45e7daaee19dd74a0aba43acf7b', 202, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(299, 3, 384, 'may_09_messages/assets/js/bootstrap-timepicker/README.md', 'ec4814c71caa3db7136df510e2196eae502172484d00b90771310b5ce803d50c', 393, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(300, 3, 385, 'may_09_messages/assets/js/bootstrap-timepicker/css/bootstrap-responsive.css', 'ebd197278890fa172ac9fbab87988c216c85dd2f03004ae7132b0b3bccabd10d', 22843, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(301, 3, 386, 'may_09_messages/assets/js/bootstrap-timepicker/css/bootstrap-timepicker.css', 'cca1ccbcc0feb6f13272eb831ab565ecdf7329216b02f891b85c3e74a5deed4e', 3621, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(302, 3, 387, 'may_09_messages/assets/js/bootstrap-timepicker/css/bootstrap-timepicker.min.css', '3ae19392cf624b7b6c7b888f7367970f12c39b3206389b8f98e3f834a5f82a51', 3059, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(303, 3, 388, 'may_09_messages/assets/js/bootstrap-timepicker/js/bootstrap-timepicker.js', 'fb6004cae9c4c4202461677362fd6d28d58560c256f33406de0e3e8e0c2099b5', 32564, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(304, 3, 389, 'may_09_messages/assets/js/bootstrap-timepicker/js/bootstrap-timepicker.min.js', '4dda0b0bdf684510c971b55be32934fd5e2d5cb969db308ddfe9d6a4cd31c532', 17260, '2026-08-06 15:02:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:48', '2026-08-14 17:27:21'),
(305, 3, 390, 'may_09_messages/assets/js/chartjs/chart.js', 'e6543a1abf0dfc8dbeca47d299a4647f6ea41e1391322fe40aa0df7ac75da4db', 444219, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(306, 3, 391, 'may_09_messages/assets/js/chartjs/chart.min.js', '7c1fda620c27b43beee9ac8f2581db2ae14d176450232fa98f1eeba67529c388', 172819, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(307, 3, 392, 'may_09_messages/assets/js/cropbox/cropbox-min.js', '30f8560042738ec2a7d7b3f82873c6ba5b5f3d833407a217d7a0a24db34e1d4f', 2181, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(308, 3, 393, 'may_09_messages/assets/js/cropbox/cropbox.js', '4c6fd5d952fe08557fd3a36d608807b36dd5ddfae4448a1a9a300cc19facf216', 5100, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(309, 3, 394, 'may_09_messages/assets/js/datatable/TableTools/js/buttons.html5.min.js', '022e83bf6415296af2b1d871284734b5341c408f819fbd1d48992930429af9a9', 25105, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(310, 3, 395, 'may_09_messages/assets/js/datatable/TableTools/js/buttons.print.min.js', '64d78575926de2dfc6940e130efb2292422fd2c49cf894bd10c2419179f2695c', 2311, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(311, 3, 396, 'may_09_messages/assets/js/datatable/TableTools/js/dataTables.buttons.min.js', 'baba2b358274178ab65b5ef1097adc9d9d2a17647ea23f9ded987888bc84072c', 20511, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(312, 3, 397, 'may_09_messages/assets/js/datatable/TableTools/js/jszip.min.js', '7c80df229138794034d8c02a12c0586cc40ebd15e4da474ed8d2b6fb9c13819e', 99124, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(313, 3, 398, 'may_09_messages/assets/js/datatable/css/responsive.dataTables.min.css', 'bbd8cd1c4deaad335e9a3dcd0ca3e6145f7925cb6de3a6808b755e37a76cfd75', 4239, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(314, 3, 399, 'may_09_messages/assets/js/datatable/datatables.min.css', '29ceed8fb6e2f960be5260f77eb9e3cdf0966ed05116e4fd395b68866b2f5ce4', 37496, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(315, 3, 400, 'may_09_messages/assets/js/datatable/datatables.min.js', 'fe958a1e5754a8cbf57bc60a8cc06de4dac7f38640453e2e08acd14ac060b61e', 336125, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(316, 3, 401, 'may_09_messages/assets/js/datatable/js/dataTables.colReorder.min.js', '90567f443adf3742b446cb62d728f826d2b8d265357470ed2b890c9fe8434183', 13696, '2026-08-06 15:02:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:49', '2026-08-14 17:27:21'),
(317, 3, 402, 'may_09_messages/assets/js/datatable/js/dataTables.responsive.min.js', 'b3c0c4c0d1842d69dd23eea7268979972d51d9c404e4df165cf452e5472075ac', 16897, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(318, 3, 403, 'may_09_messages/assets/js/dropzone/README.md', '8eae790a4443ccaaeee522f18e9f05d90bcbb64dccae97ffe6e823510593b94e', 1369, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(319, 3, 404, 'may_09_messages/assets/js/dropzone/basic.css', 'd4ec614654ca8fed07b40f64277c622509558e4bd6796f34060f8e76d9ce5503', 1121, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(320, 3, 405, 'may_09_messages/assets/js/dropzone/basic.min.css', '34c3c9a77a061dc645fa3084b964bce8e8880f002fc4131e23bc3be7bfec1061', 752, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(321, 3, 406, 'may_09_messages/assets/js/dropzone/dropzone-amd-module.js', '63c5be6cd98049e3d3d8ac5e2fcc21f512531b8ca72485dd5817dada0b97f628', 125734, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(322, 3, 407, 'may_09_messages/assets/js/dropzone/dropzone-amd-module.min.js', '84e0a17b67fa04a8de06a273620c7fc57401d43f80e471450f6ffbc5dcaa51f8', 45177, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(323, 3, 408, 'may_09_messages/assets/js/dropzone/dropzone.css', '290a6f6d168f305f930d30d9356e827434a82f4979256d2c4ff5de1ebebd093d', 13174, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(324, 3, 409, 'may_09_messages/assets/js/dropzone/dropzone.js', '54b8e9b1b50c9b67c50abccd0e0c7b0028057eca12193389849688d229dd619d', 125332, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(325, 3, 410, 'may_09_messages/assets/js/dropzone/dropzone.min.css', 'ee3e1d0f1f0a54e3f08f02dfc8affc49562f2d27a8897d163a8480a220b2a0ab', 9709, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(326, 3, 411, 'may_09_messages/assets/js/dropzone/dropzone.min.js', '70eca65517e6af517ca61e5c27336e2320341f4f868a4263e537c0fc5b321d42', 47234, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(327, 3, 412, 'may_09_messages/assets/js/feather-icons/feather-sprite.svg', '92bf2d59bdc8d72053881e30b2e75408413774508bf8267c50af8a64ad9b7cd4', 60146, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(328, 3, 413, 'may_09_messages/assets/js/feather-icons/feather.js', '1517d5605ef51ea616b0bcb12d93180a03c024fa48f2fdb890183cce7a887131', 161077, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(329, 3, 414, 'may_09_messages/assets/js/feather-icons/feather.js.map', 'd4d9b23a4213a6853b2a372c8e64a714c942df39c68c60f7db7f61b9e1a7a4d7', 66446, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(330, 3, 415, 'may_09_messages/assets/js/feather-icons/feather.min.js', '0f5d5f7a0a9cfa25f321921ac926e23118a2af9fe884d51d98ff879f968cd290', 75750, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(331, 3, 416, 'may_09_messages/assets/js/feather-icons/feather.min.js.map', '568b7975416cbc42448d38d36948fbce3babc2b4da0d612f630a282165c34a08', 80886, '2026-08-06 15:02:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:50', '2026-08-14 17:27:21'),
(332, 3, 417, 'may_09_messages/assets/js/feather-icons/icons.json', 'f2654ea6dcafa6dba1e6bed54ab723bed467767abb3c6b98d25381391ee4eb30', 53650, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(333, 3, 418, 'may_09_messages/assets/js/fullcalendar/fullcalendar.css', '63399a693c1493d659e66f1fcf533a7d2860e67fd605a23282431b46dccecedd', 41142, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(334, 3, 419, 'may_09_messages/assets/js/fullcalendar/fullcalendar.js', 'dbf500eaf140300b02cf87a623efb7cac5fde513a51c3238f2c6a10cf9e27a78', 690093, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(335, 3, 420, 'may_09_messages/assets/js/fullcalendar/fullcalendar.min.css', 'baaf4f36533307ed61d35223f5cc7bcde136391da92c07d1c37b9450e38f29d0', 24939, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(336, 3, 421, 'may_09_messages/assets/js/fullcalendar/fullcalendar.min.js', '9ad8ffdc4fc31ff3103659cd82f91c6aa86019bfe3047b813d47f1498216127b', 249535, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(337, 3, 422, 'may_09_messages/assets/js/fullcalendar/fullcalendar.print.css', '9b5a98e4f435980bcbae1ef35ce31b8d80a747ec47f62b374cfb523aa323a84c', 5753, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(338, 3, 423, 'may_09_messages/assets/js/fullcalendar/gcal.js', 'a39ea5733626af9b8b6a61123ae39e4ad7e3a3d2dfd33a56d16b923edc28a933', 5931, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(339, 3, 424, 'may_09_messages/assets/js/fullcalendar/locales-all.min.js', 'fd9831bc38f742dc8164d2db7c96877706c717c47a396f36fb9313e7206c1fd8', 20006, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(340, 3, 425, 'may_09_messages/assets/js/fullcalendar/moment.min.js', '7b77d54e97fb3cf04c322e5c34e27e019c3dab6fdfe2558ebed571669f0e392f', 225904, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(341, 3, 426, 'may_09_messages/assets/js/gantt-chart/frappe-gantt.css', 'f32849a7fe8a42db7293c329a4308f61c794714c5a1a0b5b62eef07c79b399c6', 2524, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(342, 3, 427, 'may_09_messages/assets/js/gantt-chart/frappe-gantt.js', '7a92c5c7db9e9da3c91108ae11ee5c502d5456ab9fcd423cc7a36e2358b4d976', 71452, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(343, 3, 428, 'may_09_messages/assets/js/general_helper.js', '9d160e020afae5d2fe3bd7ad299d659a3a254f86606dedfdf9f6b9f5717f086a', 30416, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(344, 3, 429, 'may_09_messages/assets/js/jquery-3.5.1.min.js', '9a2723c21fb1b7dff0e2aa5dc6be24a9670220a17ae21f70fdbc602d1f8acd38', 89476, '2026-08-06 15:02:51', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:02:51', '2026-08-14 17:27:21'),
(345, 3, 430, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:45:42', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:17', '2026-08-14 17:27:21'),
(346, 3, 431, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:45:42', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(347, 3, 432, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:45:42', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(348, 3, 433, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:45:42', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(349, 3, 434, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:45:42', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(350, 3, 435, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.css', 'fa8fb3835f52fdff239c6f4783e48c97f085b64a276c40d0625b0808142153de', 75728, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(351, 3, 436, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.min.css', 'fa05f478ec29045fb23ebf7826a7e9ec525b05670bfdb1fc25f7269fc246d8d3', 51515, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(352, 3, 437, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.rtl.css', '17a6ae3e016f18812363099c581a60568939bdf4906467ea6b79648041c90ab7', 75801, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(353, 3, 438, 'may_09_messages/assets/bootstrap/css/bootstrap-grid.rtl.min.css', 'bec66f48d15c96c387ecf50d2e104107a5f8e4bf3b9727724568f47b06534647', 51590, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(354, 3, 439, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.css', '4c3393cc5e4097930260a45d9a04c1a8bf3b0643120c7e125af1c3cdd16546b1', 6419, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(355, 3, 440, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.min.css', '05c355bbf68dcd59f50ac2850445fa4bb5370150d6a603c480eaa20734a35788', 4776, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(356, 3, 441, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.rtl.css', '5a65c57fab7e8f102fbf39c0888db59756ec0474b826ded261a487c45310e00c', 6393, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(357, 3, 442, 'may_09_messages/assets/bootstrap/css/bootstrap-reboot.rtl.min.css', '113aab4c7d4bc757bdf0150106c5e632fc021363b73104d7381a58bfd00430ae', 4848, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(358, 3, 443, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.css', '8c38cba666d88b6946b8ce68ff8c30b0db6f5eeff328f9f01e72e448ae1f74c4', 71446, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(359, 3, 444, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.min.css', '8740fa53f0c9fdab546bf18ca00d84dd230251dabdcefaef6d5446d7cfe1dc01', 49492, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(360, 3, 445, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.rtl.css', '9404102e12b64d1d4a9b277774988fdff751dc4ab7e094e885acb05d0008daa0', 71303, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(361, 3, 446, 'may_09_messages/assets/bootstrap/css/bootstrap-utilities.rtl.min.css', 'cb187c7b4022b62857700c7fbea6d98ae216f8a0c203bc246241ad592073bfe0', 49419, '2026-08-06 15:36:18', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:18', '2026-08-14 17:27:21'),
(362, 3, 447, 'may_09_messages/assets/bootstrap/css/bootstrap.css', '852059bf5eceae541fb77a8512e352b0c5f26e3b9f27eb91c47a73e88244767c', 203731, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(363, 3, 448, 'may_09_messages/assets/bootstrap/css/bootstrap.min.css', '6a669adda63d8e1d09a6455d7dc95bb126f27aa483d10f0b54ce14c303fa466f', 153117, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(364, 3, 449, 'may_09_messages/assets/bootstrap/css/bootstrap.min.css.map', '907fdc5d5ea3bf6de43add9285f2af433d214c8dd60296fc48422a11f9539d6c', 422371, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(365, 3, 450, 'may_09_messages/assets/bootstrap/css/bootstrap.rtl.css', 'a02d0cf104d191419322747bdd134ee4bccadd515dee048e528e27b416767fe2', 202974, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(366, 3, 451, 'may_09_messages/assets/bootstrap/css/bootstrap.rtl.min.css', '4af1e04118a5b53d828daa5b91f9647128ed28f85a2bed364b8db542a2873810', 153222, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(367, 3, 452, 'may_09_messages/assets/bootstrap/js/bootstrap.bundle.js', '46b44668a6a143301d12ab081019b9d48f3a7188a7f2cf6ec995c171a3bcd90d', 216787, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(368, 3, 453, 'may_09_messages/assets/bootstrap/js/bootstrap.bundle.min.js', '6fd9a7b3f4692ff54d61155781bee462c88e2989d00b40e2d7e3fe425cac736e', 80827, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(369, 3, 454, 'may_09_messages/assets/bootstrap/js/bootstrap.esm.js', '2cbc5a1149fb451f01d77f775053a12cbd9759d917fe153ca5f86a35a025fb3d', 147542, '2026-08-06 15:36:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:19', '2026-08-14 17:27:21'),
(370, 3, 455, 'may_09_messages/assets/bootstrap/js/bootstrap.esm.min.js', '6fc41fafcb8aa8833f776f3f89e6d609b60cc38b652e37c0032b804c62288d74', 76585, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(371, 3, 456, 'may_09_messages/assets/bootstrap/js/bootstrap.js', '6fe4aaeefa2567ef0698411e7e3b5d7657cbc5a42ad55d6c05cf48daa939d9b0', 156888, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(372, 3, 457, 'may_09_messages/assets/bootstrap/js/bootstrap.min.js', 'da5a8e7b12eb38994c867304d3325b93b8b7cb4002499e9e9547c010a1420f2a', 62417, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(373, 3, 458, 'may_09_messages/assets/css/app.all.css', 'f5ac84a855fdff6f406f8c406212a0c9f778eb566eaaebaeb6983e2f7207bdd6', 202593, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(374, 3, 459, 'may_09_messages/assets/css/color/00BCD4.css', '9954804c875a52910373071fce7014f1c1eb839cb72e7d7fa17652ab355cc142', 2877, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(375, 3, 460, 'may_09_messages/assets/css/color/17a589.css', '931768afab86a5e19a94a93f3be733e2a04895d7809a8ea84631a8fb62bb7a1b', 2969, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(376, 3, 461, 'may_09_messages/assets/css/color/1E202D.css', 'e9551cfa967a6fd7d157e5891e1790d006f95a9457b0489c5b12f761a459868e', 25590, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(377, 3, 462, 'may_09_messages/assets/css/color/1d2632.css', 'f5a53dd81a533e615f77f4121a354d5e02908c47d108e038c5fbb80f48bb6e63', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(378, 3, 463, 'may_09_messages/assets/css/color/2471a3.css', '48f1785483e76bbaa4095164463b3f340a1e360e6afc4279f0dcfa1d2f4fdf4f', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(379, 3, 464, 'may_09_messages/assets/css/color/2e4053.css', 'a177b3462fe65ca35fe3c41d83a8909d27d9cef78f733e3c368e1e7785c14052', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(380, 3, 465, 'may_09_messages/assets/css/color/2e86c1.css', '78de9821f785dcd27e13a14e240a613b96170597b7a5b48bbb29d986e4ab57a4', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(381, 3, 466, 'may_09_messages/assets/css/color/404040.css', '28f4dadbb6974ba45590246b6e606cec516f14e315fa6596045af290e658dd4d', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(382, 3, 467, 'may_09_messages/assets/css/color/555a61.css', '16ee4d27c546bb6aa374a89224a9972ae88473f5e58cb8ba0e6118a5800a1584', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(383, 3, 468, 'may_09_messages/assets/css/color/557bbb.css', 'c7f2313ce216cae6f344aac30a4782849810b8cdd1cf1e3f5fdced41a90d24a8', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(384, 3, 469, 'may_09_messages/assets/css/color/5d78ff.css', 'bfeaed9e2787eabb8b2e80ef091775ded2ee5edfd57ad39baf0764503f0127e5', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(385, 3, 470, 'may_09_messages/assets/css/color/839192.css', 'dee7d21d73228a776228e080c68b93f1ff311af8652f56f05f307a8609672515', 2972, '2026-08-06 15:36:20', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:20', '2026-08-14 17:27:21'),
(386, 3, 471, 'may_09_messages/assets/css/color/83c340.css', '9070c692b0cda115d75f66143692935e6f5e6423a53daef1c4453d76e9413090', 2969, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(387, 3, 472, 'may_09_messages/assets/css/color/884ea0.css', 'aef6f86b4b5bf764a6406666b0134b5d51130b9b5e3e7355affeaaee96c09ba1', 2972, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(388, 3, 473, 'may_09_messages/assets/css/color/a6acaf.css', '4e8d42058bce652778586753b8b5c0aa99c7fdad63ed87fc36ce613d5150d790', 2972, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(389, 3, 474, 'may_09_messages/assets/css/color/a93226.css', '99959186002d473c38408a3fee9689709909cfa45b3a6e18f35f8e613db63af1', 2972, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(390, 3, 475, 'may_09_messages/assets/css/color/d68910.css', '00669d1c3e4e5f836278cdb99f24f3053e466b54245ec623503ed38d4009fe6b', 2969, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(391, 3, 476, 'may_09_messages/assets/js/index.html', 'c703afa89e7517dd5020ac2960b7e14b44d4010ef02d5e0f6e34b9629935a3ce', 123, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(392, 3, 477, 'may_09_messages/assets/css/custom-style.css', 'f8b9abeb07ab0428da89592e9f50e56965689607f0c736c873c9cf70ab0a13a9', 41, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(393, 3, 478, 'may_09_messages/assets/css/fonts/opensans/opensans-bold.woff', 'e83c0f6faf72f8dc6d60141c4fd6785d871cede7589719f368136d42b6f7c421', 21816, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(394, 3, 479, 'may_09_messages/assets/css/fonts/opensans/opensans-light.woff', 'a6a5d7cbfec632fa7463ebca7babf4b1971b785cd1a26b596d58b8a831001136', 21744, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(395, 3, 480, 'may_09_messages/assets/css/fonts/opensans/opensans.woff', 'b9876c49ea883cf289637c99b4f29fa51e66c517b31a48fde22ad18e65f2ebc2', 21080, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(396, 3, 481, 'may_09_messages/assets/css/invoice.css', '129bab36478d91786c46fdea3fad63d2c34e490b3ec115093a32888fd7bf2c7a', 2211, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(397, 3, 482, 'may_09_messages/assets/css/message-style.css', '3ddc5518d0515ab79544e5d97a6147cb30f060da4177780e371bffdf5b6bf0aa', 23578, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(398, 3, 483, 'may_09_messages/assets/css/rtl.css', '941a5953cf8feb8e92c2561a98106dc26a6bc3aba23daaf6fa8b6f2beb37db1c', 7284, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(399, 3, 484, 'may_09_messages/assets/images/avatar-bot.jpg', '08ebf5e3648256b38fc4075f70d77c49aa3801c26b9b201dfb1e175cce824d04', 3137, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(400, 3, 485, 'may_09_messages/assets/images/avatar.jpg', '88aa41fb968a6c34acbef2ee2da4f8decfd392dd540cf05c3077261699ac24e4', 12708, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(401, 3, 486, 'may_09_messages/assets/images/bitbucket_logo.png', '5c91c0338df37e5dfc4dfe43c3e7f70a71458393bf3caefe9014e9fa1b401b08', 1050, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(402, 3, 487, 'may_09_messages/assets/images/check_mark.png', '32a3a2eef5ec581d1d2e8c70df92019b860972cddde917490fe236050249a868', 3069, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(403, 3, 488, 'may_09_messages/assets/images/checkbox-un-checked.png', '26fa1638ae618b0af46f67c254b5194bc01318e4fbfbd6762b115c32bee5c820', 231, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(404, 3, 489, 'may_09_messages/assets/images/checkbox.png', 'c47ac259971c18cc33ee9e42ab175dc451c6a5ad51a3434951d2230baad74e1c', 151, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(405, 3, 490, 'may_09_messages/assets/images/favicon.png', '03c18340660a0db289a84f456189be25193bc58cdd8ab4fa465ff2892ca2a74c', 4261, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(406, 3, 491, 'may_09_messages/assets/images/file_preview.jpg', '59370783478e088f061a1377ca512d894e160d317e98df15b979b32c2cec897f', 13986, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(407, 3, 492, 'may_09_messages/assets/images/filepreview.jpg', '3090a2af120ee1dbe7219c21d66f677975c36b7b28e509a1fe6944e02b18c8a4', 1739, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(408, 3, 493, 'may_09_messages/assets/images/github_logo.png', '8867ef04fee7b1edf840866a4312de43ac68ea43a0099235154c959afa403809', 17263, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(409, 3, 494, 'may_09_messages/assets/images/invoice_style_1.png', '504f0bc4bbc2e73f63461018673f51ebfd7630bdbb9f415243d11ba044ef4e86', 15339, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(410, 3, 495, 'may_09_messages/assets/images/invoice_style_2.png', 'cb84db297304ee141c448701e82c27516db5a403baf54c9cd40292d13a049453', 15253, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(411, 3, 496, 'may_09_messages/assets/images/loader2.gif', '917637a958ae8cffa27da24f8a22596057a3dc7403fb279c6713ca16855c8988', 1737, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(412, 3, 497, 'may_09_messages/assets/images/logo.png', '9644f66867b3c5acdb6ee74c9dd0bea2229252a8886e0d3b91f6f169249e9ae7', 1395, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(413, 3, 498, 'may_09_messages/assets/images/move.png', '89806284556d9a128224d557b1bdae0324296e1f26fc4d140938665558769eca', 3881, '2026-08-06 15:36:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:21', '2026-08-14 17:27:21'),
(414, 3, 499, 'may_09_messages/assets/images/search.png', '48978c790592f85e43842a799366182aa39660581fc387088d44a36dad4e9f57', 3025, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(415, 3, 500, 'may_09_messages/assets/images/sort_asc.png', '28fac76596775cff97aba478da505db74cad43f2194e146d7e696cf7480f93e3', 2907, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(416, 3, 501, 'may_09_messages/assets/images/sort_desc.png', '324d3432ece78d54d53ede8690311b72e70bda428102e0b6162345279431b16d', 2931, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(417, 3, 502, 'may_09_messages/assets/images/stripe-payment-logo.png', 'f87df18c1a3b2fa8b791ff81c1ceb55043d33af62dafa8c6303b7cd484087b22', 12022, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(418, 3, 503, 'may_09_messages/assets/images/video_preview.jpg', '3a967df63367d34a3260dff76b31cc4679bfaa8c9ebd49fadcb8b2e618c658de', 10078, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(419, 3, 504, 'may_09_messages/assets/js/app.all.js', 'a93c9293a4c9171743714defc476ed3d9e94d75b42c9397362989622bcc1f295', 1974732, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(420, 3, 505, 'may_09_messages/assets/js/app.js', '5c592b8d2775272f9b2e6206b831d5e5403dbb4b5d8f3c992752392736431f66', 136237, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(421, 3, 506, 'may_09_messages/assets/js/app.min.js', 'da6d5c7e80ae2ea58e5a3a88515de69da46e3313fba969ba4166eaf8f1112b40', 54418, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(422, 3, 507, 'may_09_messages/assets/js/atwho/caret/jquery.caret.js', 'fbb165850993748024040c2993c996020160af3e5e0c223804cbfac7e0acfb35', 12757, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(423, 3, 508, 'may_09_messages/assets/js/atwho/caret/jquery.caret.min.js', '51507de5e6bd66dd002c56ac7cb46c4b9cf460401d37270deba7d56eb516d8ef', 6262, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(424, 3, 509, 'may_09_messages/assets/js/atwho/css/jquery.atwho.css', 'dd330ce1c1d6f5d7a8416f34a63fd95bf4a0b86143cbd17bd0862111c50df2b9', 1384, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(425, 3, 510, 'may_09_messages/assets/js/atwho/css/jquery.atwho.min.css', '25e25d30705c212a17333d4d219aeca0d3ec1d0ae172d97a109b35b2ee6427df', 919, '2026-08-06 15:36:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:22', '2026-08-14 17:27:21'),
(426, 3, 511, 'may_09_messages/assets/js/atwho/js/jquery.atwho.js', '30f584fef85ff365d6615ed989ea7e9d442ccfbacf0406e22ceb948a08800e20', 36334, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(427, 3, 512, 'may_09_messages/assets/js/atwho/js/jquery.atwho.min.js', '24e822f1a04368fdf6507fc218120c584adc94beaae6a5ff3ed404b16e02758f', 20504, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(428, 3, 513, 'may_09_messages/assets/js/awesomplete/awesomplete.css', '2542c339df4c4c7930a767bf5d0c5695b6c6f66a5db7e33674ca7b8afb8ea7ca', 1982, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(429, 3, 514, 'may_09_messages/assets/js/awesomplete/awesomplete.js', '1e07e6c129eb20329835f0333a99e4905369890a85bd9b9bdd90eeb9180e6b6a', 13688, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(430, 3, 515, 'may_09_messages/assets/js/awesomplete/awesomplete.min.js', 'ec7b7af99469e787353d94a94e6075df7039a293a31e8078e1d68857f97854fe', 7555, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(431, 3, 516, 'may_09_messages/assets/js/bootstrap-datepicker/README.md', 'ea48b35e039f3c821db263d2f4dc0a3ae9f24c49ef8a615f0ec30ca49e29cdbf', 1987, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(432, 3, 517, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker.css', 'bb91d3f81747be9aa123c5ef0a8d0c91658f1c3ebe5a743bc0baab038a051038', 17612, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(433, 3, 518, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker.min.css', '9f1ed28c8aba11cd26abcfb4671e4f0201ae61d71832fa72cee3c90abeeae982', 15737, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(434, 3, 519, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker3.css', '6bca3dc478b0bc49c0783274629f2208e7b58fdc6e139f768999a71c7d4a5c33', 23462, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(435, 3, 520, 'may_09_messages/assets/js/bootstrap-datepicker/css/datepicker3.min.css', '2695bf33b2936ec801798a54e5974c6406b4b0c8c9d6e630a74064671fe52831', 21106, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(436, 3, 521, 'may_09_messages/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js', '447bb8dc7271c982934c09cc7886e9f003ac4485319b53980cda780b8d8339d5', 59935, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(437, 3, 522, 'may_09_messages/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js', 'c129f5956bc8e9c3a2838069e9a3ff50ad7a540a077b42934b2c5d7c902f4efc', 33700, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(438, 3, 523, 'may_09_messages/assets/js/bootstrap-timepicker/README-important.txt', '67147b2303f70d0a2d1881e7443787b9b751e45e7daaee19dd74a0aba43acf7b', 202, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(439, 3, 524, 'may_09_messages/assets/js/bootstrap-timepicker/README.md', 'ec4814c71caa3db7136df510e2196eae502172484d00b90771310b5ce803d50c', 393, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(440, 3, 525, 'may_09_messages/assets/js/bootstrap-timepicker/css/bootstrap-responsive.css', 'ebd197278890fa172ac9fbab87988c216c85dd2f03004ae7132b0b3bccabd10d', 22843, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(441, 3, 526, 'may_09_messages/assets/js/bootstrap-timepicker/css/bootstrap-timepicker.css', 'cca1ccbcc0feb6f13272eb831ab565ecdf7329216b02f891b85c3e74a5deed4e', 3621, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(442, 3, 527, 'may_09_messages/assets/js/bootstrap-timepicker/css/bootstrap-timepicker.min.css', '3ae19392cf624b7b6c7b888f7367970f12c39b3206389b8f98e3f834a5f82a51', 3059, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(443, 3, 528, 'may_09_messages/assets/js/bootstrap-timepicker/js/bootstrap-timepicker.js', 'fb6004cae9c4c4202461677362fd6d28d58560c256f33406de0e3e8e0c2099b5', 32564, '2026-08-06 15:36:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:23', '2026-08-14 17:27:21'),
(444, 3, 529, 'may_09_messages/assets/js/bootstrap-timepicker/js/bootstrap-timepicker.min.js', '4dda0b0bdf684510c971b55be32934fd5e2d5cb969db308ddfe9d6a4cd31c532', 17260, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(445, 3, 530, 'may_09_messages/assets/js/chartjs/chart.js', 'e6543a1abf0dfc8dbeca47d299a4647f6ea41e1391322fe40aa0df7ac75da4db', 444219, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(446, 3, 531, 'may_09_messages/assets/js/chartjs/chart.min.js', '7c1fda620c27b43beee9ac8f2581db2ae14d176450232fa98f1eeba67529c388', 172819, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(447, 3, 532, 'may_09_messages/assets/js/cropbox/cropbox-min.js', '30f8560042738ec2a7d7b3f82873c6ba5b5f3d833407a217d7a0a24db34e1d4f', 2181, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(448, 3, 533, 'may_09_messages/assets/js/cropbox/cropbox.js', '4c6fd5d952fe08557fd3a36d608807b36dd5ddfae4448a1a9a300cc19facf216', 5100, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(449, 3, 534, 'may_09_messages/assets/js/datatable/TableTools/js/buttons.html5.min.js', '022e83bf6415296af2b1d871284734b5341c408f819fbd1d48992930429af9a9', 25105, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(450, 3, 535, 'may_09_messages/assets/js/datatable/TableTools/js/buttons.print.min.js', '64d78575926de2dfc6940e130efb2292422fd2c49cf894bd10c2419179f2695c', 2311, '2026-08-06 15:36:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:24', '2026-08-14 17:27:21'),
(451, 3, 536, 'may_09_messages/assets/js/datatable/TableTools/js/dataTables.buttons.min.js', 'baba2b358274178ab65b5ef1097adc9d9d2a17647ea23f9ded987888bc84072c', 20511, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(452, 3, 537, 'may_09_messages/assets/js/datatable/TableTools/js/jszip.min.js', '7c80df229138794034d8c02a12c0586cc40ebd15e4da474ed8d2b6fb9c13819e', 99124, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(453, 3, 538, 'may_09_messages/assets/js/datatable/css/responsive.dataTables.min.css', 'bbd8cd1c4deaad335e9a3dcd0ca3e6145f7925cb6de3a6808b755e37a76cfd75', 4239, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(454, 3, 539, 'may_09_messages/assets/js/datatable/datatables.min.css', '29ceed8fb6e2f960be5260f77eb9e3cdf0966ed05116e4fd395b68866b2f5ce4', 37496, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(455, 3, 540, 'may_09_messages/assets/js/datatable/datatables.min.js', 'fe958a1e5754a8cbf57bc60a8cc06de4dac7f38640453e2e08acd14ac060b61e', 336125, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(456, 3, 541, 'may_09_messages/assets/js/datatable/js/dataTables.colReorder.min.js', '90567f443adf3742b446cb62d728f826d2b8d265357470ed2b890c9fe8434183', 13696, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(457, 3, 542, 'may_09_messages/assets/js/datatable/js/dataTables.responsive.min.js', 'b3c0c4c0d1842d69dd23eea7268979972d51d9c404e4df165cf452e5472075ac', 16897, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(458, 3, 543, 'may_09_messages/assets/js/dropzone/README.md', '8eae790a4443ccaaeee522f18e9f05d90bcbb64dccae97ffe6e823510593b94e', 1369, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(459, 3, 544, 'may_09_messages/assets/js/dropzone/basic.css', 'd4ec614654ca8fed07b40f64277c622509558e4bd6796f34060f8e76d9ce5503', 1121, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(460, 3, 545, 'may_09_messages/assets/js/dropzone/basic.min.css', '34c3c9a77a061dc645fa3084b964bce8e8880f002fc4131e23bc3be7bfec1061', 752, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(461, 3, 546, 'may_09_messages/assets/js/dropzone/dropzone-amd-module.js', '63c5be6cd98049e3d3d8ac5e2fcc21f512531b8ca72485dd5817dada0b97f628', 125734, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(462, 3, 547, 'may_09_messages/assets/js/dropzone/dropzone-amd-module.min.js', '84e0a17b67fa04a8de06a273620c7fc57401d43f80e471450f6ffbc5dcaa51f8', 45177, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(463, 3, 548, 'may_09_messages/assets/js/dropzone/dropzone.css', '290a6f6d168f305f930d30d9356e827434a82f4979256d2c4ff5de1ebebd093d', 13174, '2026-08-06 15:36:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:25', '2026-08-14 17:27:21'),
(464, 3, 549, 'may_09_messages/assets/js/dropzone/dropzone.js', '54b8e9b1b50c9b67c50abccd0e0c7b0028057eca12193389849688d229dd619d', 125332, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(465, 3, 550, 'may_09_messages/assets/js/dropzone/dropzone.min.css', 'ee3e1d0f1f0a54e3f08f02dfc8affc49562f2d27a8897d163a8480a220b2a0ab', 9709, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(466, 3, 551, 'may_09_messages/assets/js/dropzone/dropzone.min.js', '70eca65517e6af517ca61e5c27336e2320341f4f868a4263e537c0fc5b321d42', 47234, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(467, 3, 552, 'may_09_messages/assets/js/feather-icons/feather-sprite.svg', '92bf2d59bdc8d72053881e30b2e75408413774508bf8267c50af8a64ad9b7cd4', 60146, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(468, 3, 553, 'may_09_messages/assets/js/feather-icons/feather.js', '1517d5605ef51ea616b0bcb12d93180a03c024fa48f2fdb890183cce7a887131', 161077, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(469, 3, 554, 'may_09_messages/assets/js/feather-icons/feather.js.map', 'd4d9b23a4213a6853b2a372c8e64a714c942df39c68c60f7db7f61b9e1a7a4d7', 66446, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(470, 3, 555, 'may_09_messages/assets/js/feather-icons/feather.min.js', '0f5d5f7a0a9cfa25f321921ac926e23118a2af9fe884d51d98ff879f968cd290', 75750, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(471, 3, 556, 'may_09_messages/assets/js/feather-icons/feather.min.js.map', '568b7975416cbc42448d38d36948fbce3babc2b4da0d612f630a282165c34a08', 80886, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21');
INSERT INTO `sync_status` (`id`, `user_id`, `file_id`, `local_path`, `file_hash`, `file_size`, `modified_time`, `sync_status`, `sync_direction`, `last_sync`, `error_message`, `retry_count`, `created_at`, `updated_at`) VALUES
(472, 3, 557, 'may_09_messages/assets/js/feather-icons/icons.json', 'f2654ea6dcafa6dba1e6bed54ab723bed467767abb3c6b98d25381391ee4eb30', 53650, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(473, 3, 558, 'may_09_messages/assets/js/fullcalendar/fullcalendar.css', '63399a693c1493d659e66f1fcf533a7d2860e67fd605a23282431b46dccecedd', 41142, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(474, 3, 559, 'may_09_messages/assets/js/fullcalendar/fullcalendar.js', 'dbf500eaf140300b02cf87a623efb7cac5fde513a51c3238f2c6a10cf9e27a78', 690093, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(475, 3, 560, 'may_09_messages/assets/js/fullcalendar/fullcalendar.min.css', 'baaf4f36533307ed61d35223f5cc7bcde136391da92c07d1c37b9450e38f29d0', 24939, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(476, 3, 561, 'may_09_messages/assets/js/fullcalendar/fullcalendar.min.js', '9ad8ffdc4fc31ff3103659cd82f91c6aa86019bfe3047b813d47f1498216127b', 249535, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(477, 3, 562, 'may_09_messages/assets/js/fullcalendar/fullcalendar.print.css', '9b5a98e4f435980bcbae1ef35ce31b8d80a747ec47f62b374cfb523aa323a84c', 5753, '2026-08-06 15:36:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:26', '2026-08-14 17:27:21'),
(478, 3, 563, 'may_09_messages/assets/js/fullcalendar/gcal.js', 'a39ea5733626af9b8b6a61123ae39e4ad7e3a3d2dfd33a56d16b923edc28a933', 5931, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(479, 3, 564, 'may_09_messages/assets/js/fullcalendar/locales-all.min.js', 'fd9831bc38f742dc8164d2db7c96877706c717c47a396f36fb9313e7206c1fd8', 20006, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(480, 3, 565, 'may_09_messages/assets/js/fullcalendar/moment.min.js', '7b77d54e97fb3cf04c322e5c34e27e019c3dab6fdfe2558ebed571669f0e392f', 225904, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(481, 3, 566, 'may_09_messages/assets/js/gantt-chart/frappe-gantt.css', 'f32849a7fe8a42db7293c329a4308f61c794714c5a1a0b5b62eef07c79b399c6', 2524, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(482, 3, 567, 'may_09_messages/assets/js/gantt-chart/frappe-gantt.js', '7a92c5c7db9e9da3c91108ae11ee5c502d5456ab9fcd423cc7a36e2358b4d976', 71452, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(483, 3, 568, 'may_09_messages/assets/js/general_helper.js', '9d160e020afae5d2fe3bd7ad299d659a3a254f86606dedfdf9f6b9f5717f086a', 30416, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(484, 3, 569, 'may_09_messages/assets/js/jquery-3.5.1.min.js', '9a2723c21fb1b7dff0e2aa5dc6be24a9670220a17ae21f70fdbc602d1f8acd38', 89476, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(485, 3, 570, 'may_09_messages/assets/js/jquery-validation/README.md', 'a6b501398956e47fc0a6f6f06aa90ffabbd335813e4321716d31afba9f65e553', 4817, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(486, 3, 571, 'may_09_messages/assets/js/jquery-validation/jquery.form.js', '1773d47d8f84af6af858a40cd48e49a0e499103c936e0511023ac983759593a1', 43571, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(487, 3, 572, 'may_09_messages/assets/js/jquery-validation/jquery.validate.js', 'c5d85d054886c5b1438c896e06123d5d18a0f530f2da3c46271047b1b40cef00', 52599, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(488, 3, 573, 'may_09_messages/assets/js/jquery-validation/jquery.validate.min.js', 'a0c2a7a1d233e0904fc56dac825d2c939b934e2e8ea78bbed95b8998b8f42263', 24433, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(489, 3, 574, 'may_09_messages/assets/js/jspdfcdn/jspdf.umd.min.js', 'db83fc811c81a9c9c218f1787ba52fc9b4d0d0fbe51184ce3a4a4f8a7ba9a091', 420068, '2026-08-06 15:36:27', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:27', '2026-08-14 17:27:21'),
(490, 3, 575, 'may_09_messages/assets/js/jspdfcdn/svg2pdf.umd.min.js', '7c91b939405364524ccda0eafac0b42b456594504acfa7de0eac83d1b6347723', 87158, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(491, 3, 576, 'may_09_messages/assets/js/magnific-popup/README-important.txt', 'f12d39fd9e2f7ad8dbf9c2518cedabcba373f210cfb630b7fcd9885d734a0770', 65, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(492, 3, 577, 'may_09_messages/assets/js/magnific-popup/jquery.magnific-popup.js', '1c35905f3fbe3ca5a889c620994065009b61057e9211bb0d5703df090d01475b', 43898, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(493, 3, 578, 'may_09_messages/assets/js/magnific-popup/jquery.magnific-popup.min.js', '9ba487845343e31d0b846df1d92e75fb71845abf127c62c41893db5da08e6d11', 19788, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(494, 3, 579, 'may_09_messages/assets/js/magnific-popup/magnific-popup.css', '9aae795368baf56e6f23a792200fca81375a3203bbc3bf069839dbcb598d46f2', 7300, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(495, 3, 580, 'may_09_messages/assets/js/notification_handler.js', '359a235ccfb6ef0b7247319ae0f9f3fb4cf07d62ca19257e9dc7730ce8eb4f58', 5699, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(496, 3, 581, 'may_09_messages/assets/js/perfect-scrollbar/perfect-scrollbar.common.js', 'a86abc429dfdf51a9aaf26bfcbe1a4727d58d71420289a8a901dc7cdf80e2798', 37181, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(497, 3, 582, 'may_09_messages/assets/js/perfect-scrollbar/perfect-scrollbar.css', '213f234ea183924674fc5730ddcdc27096485f7c3a95f653362be5742a2ed992', 2867, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(498, 3, 583, 'may_09_messages/assets/js/perfect-scrollbar/perfect-scrollbar.esm.js', 'dd7db4e565a0dc5d9d1baa685d49f2af85d0990e06d8eaa9ab9d3c5607eff417', 37162, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(499, 3, 584, 'may_09_messages/assets/js/perfect-scrollbar/perfect-scrollbar.js', 'd5b769a1a4e1e48e030aa28b5a9bb4dcdfae865daa66e301ff0cc8f5e22f2db6', 39764, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(500, 3, 585, 'may_09_messages/assets/js/perfect-scrollbar/perfect-scrollbar.min.js', '2a748211a353236057191360c3614cd2173fe3baaab463b7dcd1cf7d4539e339', 19432, '2026-08-06 15:36:28', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:28', '2026-08-14 17:27:21'),
(501, 3, 586, 'may_09_messages/assets/js/push_notification/pusher/pusher.js', '540dfd0f00a9dcf66875f9689fe40c0117c41c74610116f12b48010ac2287768', 161574, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(502, 3, 587, 'may_09_messages/assets/js/push_notification/pusher/pusher.min.js', 'af98e195b7e055fc4fc54eaf38a2fb17d9ea1318625251f577531360e10f9d12', 65948, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(503, 3, 588, 'may_09_messages/assets/js/select2/select2-bootstrap.min.css', '573b4514df94b6c623009b994bfa1fe94b06645c4c0d05e62bd4233eef480874', 9794, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(504, 3, 589, 'may_09_messages/assets/js/select2/select2-spinner.gif', 'f6ecff617ec2ba7f559e6f535cad9b70a3f91120737535dab4d4548a6c83576c', 1849, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(505, 3, 590, 'may_09_messages/assets/js/select2/select2.css', '212bd31d1897a6ff2ece997b398479b4ade195719bb5c43c82083a4c83fdfcce', 19902, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(506, 3, 591, 'may_09_messages/assets/js/select2/select2.js', 'd6ddf72dfca8a8cc7a3ffec77df3da463aaa9b9c5a821cc615a5cd37d427482d', 152531, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(507, 3, 592, 'may_09_messages/assets/js/select2/select2.png', 'd6b5d8d83dbc18fb8d77c8761d331cd9e5123c9684950bab0406e98a24ac5ae8', 613, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(508, 3, 593, 'may_09_messages/assets/js/select2/select2x2.png', '6fe28d687dc0ed4d96016238c608ba1e7198c9c9accfa0b360b78018b9fb9bc2', 845, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(509, 3, 594, 'may_09_messages/assets/js/sortable/sortable.js', '202c8f64fee1e4a7f6fcddda9f075212f039e5a2215c5246bbfa88745dedd4fe', 124844, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(510, 3, 595, 'may_09_messages/assets/js/sortable/sortable.min.js', '100181ae8be2a7fc870be093c9ed2c9bb6b8c597b5cc5a15667fad9118c8628b', 43304, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(511, 3, 596, 'may_09_messages/assets/js/summernote/README-important.txt', '51888ed4a724b8318cfcff431c2dcea22fb62e46ca1a830dd85dbd8d4c5529eb', 220, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(512, 3, 597, 'may_09_messages/assets/js/summernote/README.md', 'f501b21cc86b58f74d25f0d48346913da19d7a496464807e3a466c404b331215', 3390, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(513, 3, 598, 'may_09_messages/assets/js/summernote/font/summernote.eot', 'e0e54acda6f6686e8bcae9d1c35be16aec516d38aac6c369b15d0ca81b9f067e', 12072, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(514, 3, 599, 'may_09_messages/assets/js/summernote/font/summernote.ttf', 'bc06998a0259b2a4be40fd043e21324ff9092011bae8ca0d963bb633edcd30e1', 11896, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(515, 3, 600, 'may_09_messages/assets/js/summernote/font/summernote.woff', 'ad20bd13f091371ecf28930028092ace4b62ec5a259b8c959b6d8f9d7d405d25', 7428, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(516, 3, 601, 'may_09_messages/assets/js/summernote/font/summernote.woff2', '6ae62bbeee0313336393d901d055b0749978abde25234397942609fef4521c1f', 6156, '2026-08-06 15:36:29', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:29', '2026-08-14 17:27:21'),
(517, 3, 602, 'may_09_messages/assets/js/summernote/lang/summernote-ar-AR.js', '332d97053fa18f52f81cad9dcecdf2cad3472cdf437a708296a7e0ad6bcb4a34', 10618, '2026-08-06 15:36:30', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:30', '2026-08-14 17:27:21'),
(518, 3, 603, 'may_09_messages/assets/js/summernote/lang/summernote-az-AZ.js', 'a985bd8b16357168bc57cadb4e92ff7bbe80bd6e5b9f2b4321103ab9cad9565d', 10357, '2026-08-06 15:36:30', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:30', '2026-08-14 17:27:21'),
(519, 3, 604, 'may_09_messages/assets/js/summernote/lang/summernote-bg-BG.js', '80d66e7528c637e4f1862cea9ee4b76fc04b00960cf2e124fe8ca1ccfd03ded0', 10386, '2026-08-06 15:36:30', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:36:30', '2026-08-14 17:27:21'),
(520, 3, 605, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:46:11', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:46:10', '2026-08-14 17:27:21'),
(521, 3, 606, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:46:11', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:46:11', '2026-08-14 17:27:21'),
(522, 3, 607, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:46:11', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:46:11', '2026-08-14 17:27:21'),
(523, 3, 608, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:46:11', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:46:11', '2026-08-14 17:27:21'),
(524, 3, 609, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:46:11', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:46:11', '2026-08-14 17:27:21'),
(525, 3, 610, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:47:56', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:47:56', '2026-08-14 17:27:21'),
(526, 3, 611, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:47:56', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:47:56', '2026-08-14 17:27:21'),
(527, 3, 612, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:47:56', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:47:56', '2026-08-14 17:27:21'),
(528, 3, 613, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:47:56', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:47:56', '2026-08-14 17:27:21'),
(529, 3, 614, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:47:56', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:47:56', '2026-08-14 17:27:21'),
(530, 3, 615, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:52:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:26', '2026-08-14 17:27:21'),
(531, 3, 616, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:52:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:26', '2026-08-14 17:27:21'),
(532, 3, 617, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:52:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:26', '2026-08-14 17:27:21'),
(533, 3, 618, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:52:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:26', '2026-08-14 17:27:21'),
(534, 3, 619, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:52:26', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:26', '2026-08-14 17:27:21'),
(535, 3, 620, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:52:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:45', '2026-08-14 17:27:21'),
(536, 3, 621, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:52:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:45', '2026-08-14 17:27:21'),
(537, 3, 622, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:52:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:46', '2026-08-14 17:27:21'),
(538, 3, 623, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:52:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:46', '2026-08-14 17:27:21'),
(539, 3, 624, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:52:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:52:46', '2026-08-14 17:27:21'),
(540, 3, 625, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:54:17', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:16', '2026-08-14 17:27:21'),
(541, 3, 626, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:54:17', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:16', '2026-08-14 17:27:21'),
(542, 3, 627, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:54:17', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:16', '2026-08-14 17:27:21'),
(543, 3, 628, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:54:17', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:16', '2026-08-14 17:27:21'),
(544, 3, 629, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:54:17', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:17', '2026-08-14 17:27:21'),
(545, 3, 630, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 15:54:55', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:54', '2026-08-14 17:27:21'),
(546, 3, 631, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 15:54:55', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:54', '2026-08-14 17:27:21'),
(547, 3, 632, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 15:54:55', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:54', '2026-08-14 17:27:21'),
(548, 3, 633, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 15:54:55', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:54', '2026-08-14 17:27:21'),
(549, 3, 634, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 15:54:55', 'idle', 'none', NULL, NULL, 0, '2026-08-06 15:54:54', '2026-08-14 17:27:21'),
(550, 3, 635, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:03:37', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:03:37', '2026-08-14 17:27:21'),
(551, 3, 636, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:03:37', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:03:37', '2026-08-14 17:27:21'),
(552, 3, 637, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:03:37', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:03:37', '2026-08-14 17:27:21'),
(553, 3, 638, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:03:37', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:03:37', '2026-08-14 17:27:21'),
(554, 3, 639, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:03:37', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:03:37', '2026-08-14 17:27:21'),
(555, 3, 640, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:04:00', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:04:00', '2026-08-14 17:27:21'),
(556, 3, 641, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:04:00', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:04:00', '2026-08-14 17:27:21'),
(557, 3, 642, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:04:00', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:04:00', '2026-08-14 17:27:21'),
(558, 3, 643, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:04:00', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:04:00', '2026-08-14 17:27:21'),
(559, 3, 644, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:04:00', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:04:00', '2026-08-14 17:27:21'),
(560, 3, 645, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:05:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:05:38', '2026-08-14 17:27:21'),
(561, 3, 646, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:05:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:05:38', '2026-08-14 17:27:21'),
(562, 3, 647, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:05:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:05:38', '2026-08-14 17:27:21'),
(563, 3, 648, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:05:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:05:38', '2026-08-14 17:27:21'),
(564, 3, 649, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:05:38', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:05:38', '2026-08-14 17:27:21'),
(565, 3, 650, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:14:35', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:35', '2026-08-14 17:27:21'),
(566, 3, 651, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:14:35', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:35', '2026-08-14 17:27:21'),
(567, 3, 652, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:14:35', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:35', '2026-08-14 17:27:21'),
(568, 3, 653, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:14:35', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:35', '2026-08-14 17:27:21'),
(569, 3, 654, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:14:35', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:35', '2026-08-14 17:27:21'),
(570, 3, 655, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:14:52', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:52', '2026-08-14 17:27:21'),
(571, 3, 656, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:14:52', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:52', '2026-08-14 17:27:21'),
(572, 3, 657, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:14:52', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:52', '2026-08-14 17:27:21'),
(573, 3, 658, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:14:52', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:52', '2026-08-14 17:27:21'),
(574, 3, 659, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:14:52', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:14:52', '2026-08-14 17:27:21'),
(575, 3, 660, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:23:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:22:58', '2026-08-14 17:27:21'),
(576, 3, 661, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:23:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:22:59', '2026-08-14 17:27:21'),
(577, 3, 662, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:23:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:22:59', '2026-08-14 17:27:21'),
(578, 3, 663, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:23:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:22:59', '2026-08-14 17:27:21'),
(579, 3, 664, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:23:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:22:59', '2026-08-14 17:27:21'),
(580, 3, 665, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:24:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:24:42', '2026-08-14 17:27:21'),
(581, 3, 666, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:24:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:24:42', '2026-08-14 17:27:21'),
(582, 3, 667, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:24:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:24:42', '2026-08-14 17:27:21'),
(583, 3, 668, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:24:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:24:43', '2026-08-14 17:27:21'),
(584, 3, 669, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:24:43', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:24:43', '2026-08-14 17:27:21'),
(585, 3, 670, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:25:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:25:33', '2026-08-14 17:27:21'),
(586, 3, 671, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:25:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:25:33', '2026-08-14 17:27:21'),
(587, 3, 672, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:25:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:25:33', '2026-08-14 17:27:21'),
(588, 3, 673, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:25:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:25:33', '2026-08-14 17:27:21'),
(589, 3, 674, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:25:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:25:33', '2026-08-14 17:27:21'),
(590, 3, 675, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:34:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:34:22', '2026-08-14 17:27:21'),
(591, 3, 676, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:34:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:34:22', '2026-08-14 17:27:21'),
(592, 3, 677, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 16:34:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:34:22', '2026-08-14 17:27:21'),
(593, 3, 678, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 16:34:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:34:22', '2026-08-14 17:27:21'),
(594, 3, 679, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 16:34:22', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:34:22', '2026-08-14 17:27:21'),
(595, 3, 680, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:46:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:46:22', '2026-08-14 17:27:21'),
(596, 3, 681, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:46:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:46:23', '2026-08-14 17:27:21'),
(597, 3, 682, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:02:41', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:46:23', '2026-08-14 17:27:21'),
(598, 3, 683, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:02:41', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:46:23', '2026-08-14 17:27:21'),
(599, 3, 684, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:02:41', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:46:23', '2026-08-14 17:27:21'),
(600, 3, 685, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:47:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:47:12', '2026-08-14 17:27:21'),
(601, 3, 686, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:47:12', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:47:12', '2026-08-14 17:27:21'),
(602, 3, 687, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:49:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:49:50', '2026-08-14 17:27:21'),
(603, 3, 688, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:49:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:49:50', '2026-08-14 17:27:21'),
(604, 3, 689, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 16:52:57', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:52:57', '2026-08-14 17:27:21'),
(605, 3, 690, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 16:52:57', 'idle', 'none', NULL, NULL, 0, '2026-08-06 16:52:57', '2026-08-14 17:27:21'),
(606, 3, 691, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:02:41', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:02:03', '2026-08-14 17:27:21'),
(607, 3, 692, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:02:41', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:02:03', '2026-08-14 17:27:21'),
(608, 3, 693, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:09:14', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:09:14', '2026-08-14 17:27:21'),
(609, 3, 694, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:09:14', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:09:14', '2026-08-14 17:27:21'),
(610, 3, 695, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:09:14', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:09:14', '2026-08-14 17:27:21'),
(611, 3, 696, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:09:14', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:09:14', '2026-08-14 17:27:21'),
(612, 3, 697, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:09:14', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:09:14', '2026-08-14 17:27:21'),
(613, 3, 698, 'user_3/documents/6a74737437348.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 17:13:48', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:13:48', '2026-08-14 17:27:21'),
(614, 3, 699, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:14:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:14:09', '2026-08-14 17:27:21'),
(615, 3, 700, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:14:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:14:09', '2026-08-14 17:27:21'),
(616, 3, 701, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:14:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:14:09', '2026-08-14 17:27:21'),
(617, 3, 702, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:14:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:14:09', '2026-08-14 17:27:21'),
(618, 3, 703, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:14:09', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:14:09', '2026-08-14 17:27:21'),
(619, 3, 704, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:17:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:17:45', '2026-08-14 17:27:21'),
(620, 3, 705, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:17:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:17:46', '2026-08-14 17:27:21'),
(621, 3, 706, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:17:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:17:46', '2026-08-14 17:27:21'),
(622, 3, 707, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:17:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:17:46', '2026-08-14 17:27:21'),
(623, 3, 708, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:17:46', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:17:46', '2026-08-14 17:27:21'),
(624, 3, 709, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:31:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:31:18', '2026-08-14 17:27:21'),
(625, 3, 710, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:31:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:31:18', '2026-08-14 17:27:21'),
(626, 3, 711, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:31:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:31:19', '2026-08-14 17:27:21'),
(627, 3, 712, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:31:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:31:19', '2026-08-14 17:27:21'),
(628, 3, 713, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:31:19', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:31:19', '2026-08-14 17:27:21'),
(629, 3, 714, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:37:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:37:21', '2026-08-14 17:27:21'),
(630, 3, 715, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:37:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:37:21', '2026-08-14 17:27:21'),
(631, 3, 716, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:37:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:37:21', '2026-08-14 17:27:21'),
(632, 3, 717, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:37:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:37:21', '2026-08-14 17:27:21'),
(633, 3, 718, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:37:21', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:37:21', '2026-08-14 17:27:21'),
(634, 3, 719, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:45:32', '2026-08-14 17:27:21'),
(635, 3, 720, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:45:32', '2026-08-14 17:27:21'),
(636, 3, 721, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:45:32', '2026-08-14 17:27:21'),
(637, 3, 722, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:45:33', '2026-08-14 17:27:21'),
(638, 3, 723, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:45:33', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:45:33', '2026-08-14 17:27:21'),
(639, 3, 724, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(640, 3, 725, 'New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(641, 3, 726, 'New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(642, 3, 727, 'New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(643, 3, 728, 'New folder/modalbrand/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(644, 3, 729, 'New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(645, 3, 730, 'New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(646, 3, 731, 'New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(647, 3, 732, 'New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(648, 3, 733, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(649, 3, 734, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(650, 3, 735, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(651, 3, 736, 'Projects/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-06 17:50:49', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:49', '2026-08-14 17:27:21'),
(652, 3, 737, 'Projects/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(653, 3, 738, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(654, 3, 739, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(655, 3, 740, 'Projects/bye/Alma Linux Installations (1) (1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(656, 3, 741, 'Projects/hi/Alma Linux Installations (2).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(657, 3, 742, 'Projects/test/sub4/commands.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(658, 3, 743, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 17:50:50', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:50:50', '2026-08-14 17:27:21'),
(659, 3, 744, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-06 17:58:23', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:23', '2026-08-14 17:27:21'),
(660, 3, 745, 'My Drive\\New folder\\help_and_knowledge_base\\articles\\view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-07 09:37:07', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(661, 3, 746, 'My Drive\\New folder\\help_and_knowledge_base\\index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(662, 3, 747, 'My Drive\\New folder\\help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(663, 3, 748, 'My Drive\\New folder\\modalbrand\\articles\\view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(664, 3, 749, 'My Drive\\New folder\\modalbrand\\brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(665, 3, 750, 'My Drive\\New folder\\modalbrand\\modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(666, 3, 751, 'My Drive\\New folder\\modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(667, 3, 752, 'My Drive\\New folder\\modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-07 09:37:08', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(668, 3, 753, 'Projects/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-06 17:58:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(669, 3, 754, 'Projects/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-06 17:58:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(670, 3, 755, 'Projects/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-06 17:58:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(671, 3, 756, 'Projects/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-06 17:58:24', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:24', '2026-08-14 17:27:21'),
(672, 3, 757, 'Projects/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21'),
(673, 3, 758, 'Projects/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21'),
(674, 3, 759, 'Projects/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21'),
(675, 3, 760, 'Projects/bye/Alma Linux Installations (1) (1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21'),
(676, 3, 761, 'Projects/hi/Alma Linux Installations (2).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21'),
(677, 3, 762, 'Projects/test/sub4/commands.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21');
INSERT INTO `sync_status` (`id`, `user_id`, `file_id`, `local_path`, `file_hash`, `file_size`, `modified_time`, `sync_status`, `sync_direction`, `last_sync`, `error_message`, `retry_count`, `created_at`, `updated_at`) VALUES
(678, 3, 763, 's1.PNG', '531836c3c3ae46e8460a5889dc8cada52b32bbba08e20bda602932c361fb6956', 46441, '2026-08-06 17:58:25', 'idle', 'none', NULL, NULL, 0, '2026-08-06 17:58:25', '2026-08-14 17:27:21'),
(679, 3, 764, 'Documents/Alma Linux Installations (1).pdf', '2b98179b61861e1fcdfcdc414b3febb9ece50e26042280287205131c7ed53aa5', 357914, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-06 18:07:14', '2026-08-18 11:30:56'),
(680, 3, 765, 'Videos/sample-5s.mp4', '6814682a7e6025952f178d241b7dd106c4cae2de6e8ccea96502884332ddaf96', 2848208, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-06 18:07:23', '2026-08-18 11:30:57'),
(681, 3, 766, 'Documents\\alma linux server  version details (6).docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 56252, '2026-08-12 14:37:27', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:08:03', '2026-08-14 17:27:21'),
(682, 3, 767, 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-07 09:29:52', '2026-08-18 11:30:56'),
(683, 3, 768, 'Documents/alma linux server  version details (6).docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 56252, '2026-08-07 09:37:15', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:15', '2026-08-14 17:27:21'),
(684, 3, 769, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-07 09:37:15', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:15', '2026-08-14 17:27:21'),
(685, 3, 770, 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-07 09:37:15', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:15', '2026-08-14 17:27:21'),
(686, 3, 771, 'My Drive/New folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-07 09:37:15', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:15', '2026-08-14 17:27:21'),
(687, 3, 772, 'My Drive/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(688, 3, 773, 'My Drive/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(689, 3, 774, 'My Drive/New folder/modalbrand/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(690, 3, 775, 'My Drive/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(691, 3, 776, 'My Drive/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(692, 3, 777, 'My Drive/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(693, 3, 778, 'My Drive/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(694, 3, 779, 'Videos/sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 2848208, '2026-08-07 09:37:16', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:37:16', '2026-08-14 17:27:21'),
(695, 3, 780, 'Documents/alma linux server  version details (6).docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 56252, '2026-08-07 09:54:07', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:07', '2026-08-14 17:27:21'),
(696, 3, 781, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-07 09:54:07', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:07', '2026-08-14 17:27:21'),
(697, 3, 782, 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-07 09:54:07', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:07', '2026-08-14 17:27:21'),
(698, 3, 783, 'My Drive/New folder/modalbrand/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-07 10:07:30', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:07', '2026-08-14 17:27:21'),
(699, 3, 784, 'My Drive/New folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-07 10:07:30', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:07', '2026-08-14 17:27:21'),
(700, 3, 785, 'My Drive/New folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-07 10:07:30', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:07', '2026-08-14 17:27:21'),
(701, 3, 786, 'My Drive/New folder/modalbrand/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-07 09:54:08', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:08', '2026-08-14 17:27:21'),
(702, 3, 787, 'My Drive/New folder/modalbrand/brand.php', '19bf7c03234acb14ff3698edbcc4c48c8c1642ce110403832b794bf08844e0fc', 63452, '2026-08-07 09:54:08', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:08', '2026-08-14 17:27:21'),
(703, 3, 788, 'My Drive/New folder/modalbrand/modal_formbrand.php', '553d09ac397eaf83758ebd6ff0324be440e4400120a30af6df624ebcbb216dc5', 31016, '2026-08-07 09:54:08', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:08', '2026-08-14 17:27:21'),
(704, 3, 789, 'My Drive/New folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-07 10:07:30', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:08', '2026-08-14 17:27:21'),
(705, 3, 790, 'My Drive/New folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-07 10:07:30', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:08', '2026-08-14 17:27:21'),
(706, 3, 791, 'Videos/sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 2848208, '2026-08-07 09:54:08', 'idle', 'none', NULL, NULL, 0, '2026-08-07 09:54:08', '2026-08-14 17:27:21'),
(707, 3, 13, 'Documents/alma linux server  version details (6).docx', '00a8ed4de6f7ce06ab998b46e5cd3a84d383fcbb02f72370bfb1291e3b1c673f', 56252, '2026-08-13 10:50:48', 'idle', 'none', NULL, NULL, 0, '2026-08-07 10:07:30', '2026-08-14 17:27:21'),
(708, 3, 15, 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-13 10:50:48', 'idle', 'none', NULL, NULL, 0, '2026-08-07 10:07:30', '2026-08-14 17:27:21'),
(709, 3, 792, 'My Drive/sample-5s.mp4', '05bd857af7f70bf51b6aac1144046973bf3325c9101a554bc27dc9607dbbd8f5', 2848208, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:07:31', '2026-08-18 11:30:57'),
(710, 3, 793, 'My Drive/folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:08:46', '2026-08-18 11:30:57'),
(711, 3, 794, 'My Drive/folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:08:46', '2026-08-18 11:30:57'),
(712, 3, 795, 'My Drive/folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:08:46', '2026-08-18 11:30:57'),
(713, 3, 796, 'My Drive/folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:08:46', '2026-08-18 11:30:57'),
(714, 3, 797, 'My Drive/folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:08:46', '2026-08-18 11:30:57'),
(715, 3, 798, 'Documents/IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 3032384, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:26:02', '2026-08-18 11:30:56'),
(716, 3, 799, 'Documents/Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 1381841, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-07 10:28:32', '2026-08-18 11:30:56'),
(717, 2, 800, 'user_2/projects/b1/6a757da330314.pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-07 12:09:31', 'completed', 'none', NULL, NULL, 0, '2026-08-07 12:09:31', '2026-08-07 13:21:46'),
(718, 2, 801, 'user_2/projects/b1/6a757da33e2dc.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-07 12:09:31', 'completed', 'none', NULL, NULL, 0, '2026-08-07 12:09:31', '2026-08-07 13:21:46'),
(719, 3, 76, 'Documents/IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 3032384, '2026-08-13 10:50:48', 'idle', 'none', NULL, NULL, 0, '2026-08-07 12:22:03', '2026-08-14 17:27:21'),
(720, 3, 802, 'Documents/Alma Linux Installations.docx', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 58134, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-07 13:14:16', '2026-08-18 11:30:56'),
(721, 3, 803, 'Documents/nastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-12 15:27:52', '2026-08-18 11:30:56'),
(722, 3, 804, 'Projects/NAS_TEST/innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-12 15:28:01', '2026-08-18 11:30:57'),
(723, 3, 805, 'My Drive/folder/help_and_knowledge_base/articles/1755490381_1755490381_296342e3efd59334904d.png', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 46499, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:15:33', '2026-08-18 11:30:57'),
(724, 3, 806, 'My Drive/app/AuthService.php', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 3405, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:50:50', '2026-08-18 11:30:56'),
(725, 3, 807, 'My Drive/app/BaseController.php', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 3250, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:50:50', '2026-08-18 11:30:56'),
(726, 3, 808, 'My Drive/app/AuthController.php', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 1419, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:50:50', '2026-08-18 11:30:56'),
(727, 3, 809, 'My Drive/app/DashboardController.php', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 942, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:50:50', '2026-08-18 11:30:56'),
(728, 3, 810, 'My Drive/app/HomeController.php', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 950, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:50:50', '2026-08-18 11:30:56'),
(729, 3, 811, 'My Drive/app/AuthController.php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 6099, '2026-08-18 09:12:19', 'synced', 'none', NULL, NULL, 0, '2026-08-12 16:50:50', '2026-08-18 09:12:19'),
(730, 5, 812, 'Documents/Api.php', '79e29f51490fe7dbd6079662b67c3b6d5a52c57e2005a290002ab2d44f34d3da', 4665, '2026-08-18 09:44:18', 'synced', 'none', NULL, NULL, 0, '2026-08-13 09:33:42', '2026-08-18 09:44:18'),
(731, 5, 813, 'Projects/b1/Alma Linux Installations (1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-18 09:44:18', 'synced', 'none', NULL, NULL, 0, '2026-08-13 09:34:11', '2026-08-18 09:44:18'),
(732, 5, 814, 'Projects/b1/bye.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-18 09:44:18', 'synced', 'none', NULL, NULL, 0, '2026-08-13 09:34:11', '2026-08-18 09:44:18'),
(733, 5, 4, 'Projects/b1/Alma Linux Installations (1).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-13 09:35:47', 'completed', 'none', NULL, NULL, 0, '2026-08-13 09:35:47', '2026-08-13 09:35:47'),
(734, 5, 6, 'Projects/b1/bye.zip', '450a6bc309ffae1147e2d73ff4ead3933efee29f3d81baaaf3db5c9e85b1241b', 53791, '2026-08-13 09:35:47', 'completed', 'none', NULL, NULL, 0, '2026-08-13 09:35:47', '2026-08-13 09:35:47'),
(735, 3, 815, 'Documents/Alma Linux Installations.docx', 'bce646cba2ea74f3b4be1dfc0694a59a9e2eafd1a568dbe78743bedf52ec7fc6', 58134, '2026-08-13 11:30:39', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:39', '2026-08-14 17:27:21'),
(736, 3, 816, 'Documents/IOT  SIM LIST new.xlsx', '1d1ab18cb921fbd4994190b84bebcd431a1d023fa1aa4f272f69259f0a2593dc', 3032384, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(737, 3, 817, 'My Drive/Play_store_and_User_reviews_set_1.xlsx', '17b15f036a9efbcec0ce5ec7bbfd01b4e654ed56abae5e0433d85380d348ce17', 1381841, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-18 11:30:57'),
(738, 3, 818, 'Documents/file_manager_v4 (1).sql', 'a59c9c68208c10f5cf6a4c543be91a8915c51781e9972cfff227074ef63c8864', 45227, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(739, 3, 819, 'Documents/final_ppt (1).pptx', 'd2c3da3aa25681e14c3bd9fd3555cbaa6816615684f0de7b0d892df7611e9c64', 7763161, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(740, 3, 820, 'Documents/nastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(741, 3, 821, 'My Drive/app/AuthController (1).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 6099, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(742, 3, 822, 'My Drive/app/AuthController (2).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 6099, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(743, 3, 823, 'My Drive/app/AuthController (3).php', '188725382d35d4f924583b0b578ee455600b827cfea40790a090ff7d362112b1', 6099, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(744, 3, 824, 'My Drive/app/AuthController.php', '9ade2e5c0d38ff82aedd246a731c607fe4a7e90e311c225e8791fcddf1a31a3a', 1419, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(745, 3, 825, 'My Drive/app/AuthService.php', 'b1cd833533ee9d9ecd8c6e5d40d1afedbe7bdb94f29d5cf56f1c93662c2b0a74', 3405, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(746, 3, 826, 'My Drive/app/BaseController.php', '915c8d182e847c75a565391f5a4c7d2bb73dd8692ffb7ca5225506c336abd911', 3250, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(747, 3, 827, 'My Drive/app/DashboardController.php', '9ab8706cefe430c8596a1cffef317abda18bbe92bd90083445f8b6b36f520f3f', 942, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(748, 3, 828, 'My Drive/app/HomeController.php', '1559deb36cb6b35f1f391992f7e72a6f45cdb286792acffc05c025c219f2def2', 950, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(749, 3, 829, 'My Drive/folder/help_and_knowledge_base/articles/1755490381_1755490381_296342e3efd59334904d.png', '133006e0eb350a960835f2c7abff90d13a7b8b8d7b9bd18d4da4eb242907f975', 46499, '2026-08-13 11:30:40', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:40', '2026-08-14 17:27:21'),
(750, 3, 830, 'My Drive/folder/help_and_knowledge_base/articles/view_page.php', 'e3b8b46bf1c812d815b629a62fb0f646af5954086c7ff9b4003c29257fd09d19', 19227, '2026-08-13 11:30:41', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:41', '2026-08-14 17:27:21'),
(751, 3, 831, 'My Drive/folder/help_and_knowledge_base/index.php', '805ef7b530ad4116f008896044340b0fdcc62f1a0ce8f53287c00f04889e26cd', 36351, '2026-08-13 11:30:41', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:41', '2026-08-14 17:27:21'),
(752, 3, 832, 'My Drive/folder/help_and_knowledge_base.zip', '62cb446b2f08da90a662ef3360f934f6f7e4fab06abd202637aa39eea8bde6a4', 12390, '2026-08-13 11:30:41', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:41', '2026-08-14 17:27:21'),
(753, 3, 833, 'My Drive/folder/modalbrand (2).zip', 'a5e3fbb3c21e85bbd8102aaa9130acc60cb3572886997fdf4fd3471203f11ed5', 20366, '2026-08-13 11:30:41', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:41', '2026-08-14 17:27:21'),
(754, 3, 834, 'My Drive/folder/modalbrand.zip', '8e22bddab3460788c9d661d34187f550e3faad5c32a059495395529dd776e060', 7078, '2026-08-13 11:30:41', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:41', '2026-08-14 17:27:21'),
(755, 3, 835, 'Projects/NAS_TEST/innernastest.txt', '0be67a678f57b00158e40eac337b82494cb4c329fd1fbd61a72d762e3217aa67', 105, '2026-08-13 11:30:41', 'idle', 'none', NULL, NULL, 0, '2026-08-13 11:30:41', '2026-08-14 17:27:21'),
(756, 3, 836, 'Archives/modalbrand.zip', '95d1cc13e23b14fa248bcdd165de2b12c50c219694f55df6a23d709f7a244c15', 19746, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 09:39:13', '2026-08-18 11:30:56'),
(757, 3, 837, 'Archives/modalbrand.zip', '95d1cc13e23b14fa248bcdd165de2b12c50c219694f55df6a23d709f7a244c15', 19746, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 09:40:20', '2026-08-18 11:30:56'),
(758, 3, 838, 'Documents/users.sql', 'a499449cca1bad9b2eb4440ed0716523b2cfc9a0ebd76748cb2ae9897e646106', 3031, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 11:16:38', '2026-08-18 11:30:56'),
(759, 3, 839, 'Documents/Alma Linux Installations (2).pdf', '00d7982c1921a9a5b26ae31f2026c6fba07778c04deac63e96d93aa45181c88d', 357914, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 11:40:00', '2026-08-18 11:30:56'),
(760, 3, 840, 'Documents/users.sql', 'a499449cca1bad9b2eb4440ed0716523b2cfc9a0ebd76748cb2ae9897e646106', 3031, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 11:45:06', '2026-08-18 11:30:56'),
(761, 3, 841, 'My Drive/Play_store_and_User_reviews_set_1.csv', 'dff6d9a15b109b8aea3a9be1ead5644a23a29ab60ef24bd2d04b972fdf663a44', 349163, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-14 11:45:30', '2026-08-18 11:30:57'),
(762, 3, 842, 'Documents/StorageService.php', '06db2cbcb4edbb246e4003b702347b1be8ba9232db310f6c140e040e9cfd7ce9', 14449, '2026-08-14 17:27:21', 'synced', 'none', NULL, NULL, 0, '2026-08-14 12:03:13', '2026-08-14 17:27:21'),
(763, 3, 843, 'Projects/NAS/FileService.php', '7ed4f20b51effd6f21a7b6bdb980ee6692a36ea8a164144b1270aa96bf636520', 49228, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-14 12:03:26', '2026-08-18 11:30:57'),
(764, 3, 844, 'Projects/NAS/StorageService.php', '06db2cbcb4edbb246e4003b702347b1be8ba9232db310f6c140e040e9cfd7ce9', 14449, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-14 12:03:26', '2026-08-18 11:30:57'),
(765, 3, 845, 'Documents/APIs_25-07-24.php', '4d324759c438250741121ebb6c0356fe319bce5c2d98750443aa09d6d3de9cd9', 6659, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 13:54:40', '2026-08-18 11:30:56'),
(766, 3, 846, 'Documents/FileService.php', '48ffe79bd6b767a9c617edc1b7a466a40ec097cead595b7e500a2b1b9bd79fc4', 51080, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 16:12:44', '2026-08-18 11:30:56'),
(767, 3, 847, 'Documents/StorageService.php', '8c774631363631d755cee44f62378711b2c3dd6f0ae4fec04dad8ffa85eb5e33', 14926, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 16:42:24', '2026-08-18 11:30:56'),
(768, 3, 848, 'Documents/Database.php', 'efdc5b99c90a8361e4581f35ab404ff06f2825d93614b3d5f8fb14f4dff50898', 2209, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-14 16:59:45', '2026-08-18 11:30:56'),
(769, 3, 849, 'Documents/ClusterService.php', '491a6958b67b1a778f90f2e625a5e303c27f9683a3d588930c6abf9c97dc8ea1', 1335, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:16:03', '2026-08-18 11:30:56'),
(770, 3, 850, 'Projects/nasassets/NAS/js/app.js', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 90794, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:17', '2026-08-18 11:30:57'),
(771, 3, 851, 'Projects/nasassets/NAS/js/app_12-8-26.js', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 84553, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:17', '2026-08-18 11:30:57'),
(772, 3, 852, 'Projects/nasassets/NAS/js/app_13-8-26(latest).js', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 89157, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:17', '2026-08-18 11:30:57'),
(773, 3, 853, 'Projects/nasassets/NAS/js/app_13-8-26.js', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 89683, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:17', '2026-08-18 11:30:57'),
(774, 3, 854, 'Projects/nasassets/NAS/js/config.js', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 4278, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:17', '2026-08-18 11:30:57'),
(775, 3, 855, 'Projects/nasassets/NAS/js/excelpreview.js', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 32387, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(776, 3, 856, 'Projects/nasassets/NAS/js/preview.js', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 60094, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(777, 3, 857, 'Projects/nasassets/NAS/js/share.js', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 21029, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(778, 3, 858, 'Projects/nasassets/NAS/js/sync.js', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 14959, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(779, 3, 859, 'Projects/nasassets/NAS/js/utils.js', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 4520, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(780, 3, 860, 'Projects/nasassets/NAS/js/websocket-client.js', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 10096, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(781, 3, 861, 'Projects/nasassets/NAS/images/efactor.png', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 41187, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(782, 3, 862, 'Projects/nasassets/NAS/css/home-style.css', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 0, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(783, 3, 863, 'Projects/nasassets/NAS/css/style.css', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 133124, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 12:21:18', '2026-08-18 11:30:57'),
(784, 3, 864, 'My Drive/arch.txt', '535264adba1fe527b28264cb2acd37dcfc3b557ae0c1ed5df1c3c2aed348d877', 1836, '2026-08-18 11:30:56', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:15:56', '2026-08-18 11:30:56'),
(785, 3, 865, 'My Drive/NAS/css/style.css', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 133124, '2026-08-17 17:20:56', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:56', '2026-08-17 17:20:56'),
(786, 3, 866, 'My Drive/NAS/css/home-style.css', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 0, '2026-08-17 17:20:56', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:56', '2026-08-17 17:20:56'),
(787, 3, 867, 'My Drive/NAS/images/efactor.png', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 41187, '2026-08-17 17:20:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:57', '2026-08-17 17:20:57'),
(788, 3, 868, 'My Drive/NAS/js/app.js', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 90794, '2026-08-17 17:20:57', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:57', '2026-08-17 17:20:57'),
(789, 3, 869, 'My Drive/NAS/js/app_12-8-26.js', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 84553, '2026-08-17 17:20:58', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:58', '2026-08-17 17:20:58'),
(790, 3, 870, 'My Drive/NAS/js/app_13-8-26(latest).js', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 89157, '2026-08-17 17:20:58', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:58', '2026-08-17 17:20:58'),
(791, 3, 871, 'My Drive/NAS/js/config.js', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 4278, '2026-08-17 17:20:58', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:58', '2026-08-17 17:20:58'),
(792, 3, 872, 'My Drive/NAS/js/app_13-8-26.js', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 89683, '2026-08-17 17:20:59', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:59', '2026-08-17 17:20:59'),
(793, 3, 873, 'My Drive/NAS/js/preview.js', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 60094, '2026-08-17 17:20:59', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:59', '2026-08-17 17:20:59'),
(794, 3, 874, 'My Drive/NAS/js/share.js', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 21029, '2026-08-17 17:20:59', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:59', '2026-08-17 17:20:59'),
(795, 3, 875, 'My Drive/NAS/js/excelpreview.js', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 32387, '2026-08-17 17:20:59', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:20:59', '2026-08-17 17:20:59'),
(796, 3, 876, 'My Drive/NAS/js/sync.js', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 14959, '2026-08-17 17:21:00', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:21:00', '2026-08-17 17:21:00'),
(797, 3, 877, 'My Drive/NAS/js/utils.js', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 4520, '2026-08-17 17:21:00', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:21:00', '2026-08-17 17:21:00'),
(798, 3, 878, 'My Drive/NAS/js/websocket-client.js', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 10096, '2026-08-17 17:21:00', 'synced', 'none', NULL, NULL, 0, '2026-08-17 17:21:00', '2026-08-17 17:21:00'),
(799, 3, 879, 'My Drive/NAS/css/style.css', '83e7ef7fb69dee54346119106ce86cd192bced5814ef497a656c6cd3b2bb26f8', 133124, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:19', '2026-08-18 11:30:57'),
(800, 3, 880, 'My Drive/NAS/css/home-style.css', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 0, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:19', '2026-08-18 11:30:57'),
(801, 3, 881, 'My Drive/NAS/images/efactor.png', '346622be963209edb1152fecfc9441156bc010c74747b4707df9c4902a57c8e2', 41187, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:19', '2026-08-18 11:30:57'),
(802, 3, 882, 'My Drive/NAS/js/app.js', '834fce5ca9a388884f50d183d2931d03ed104c7f95c3966394f26ba791868997', 90794, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:20', '2026-08-18 11:30:57'),
(803, 3, 883, 'My Drive/NAS/js/app_12-8-26.js', '9f17d495ba1c5393e362e84bc64381ab8330eb46cd6879b2695b628a5e359350', 84553, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:20', '2026-08-18 11:30:57'),
(804, 3, 884, 'My Drive/NAS/js/app_13-8-26(latest).js', 'ebd398cdde8ce4d05226533137b6acdf7ac0bdb74eee6dbd02d24202800d0b5a', 89157, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:20', '2026-08-18 11:30:57'),
(805, 3, 885, 'My Drive/NAS/js/app_13-8-26.js', 'faac70bdf8b5b372cfcfe534a22f48bc6c109333e0daff03de76b81228121f3f', 89683, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:21', '2026-08-18 11:30:57'),
(806, 3, 886, 'My Drive/NAS/js/config.js', '06bf2b16b556f07ae86551c9045198a7de2da06f18a7e27c89880ad8caa860bd', 4278, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:21', '2026-08-18 11:30:57'),
(807, 3, 887, 'My Drive/NAS/js/preview.js', '4a4234688ce5cec48777a8b992a924e533500c7cb1f03a507dbf9b9ea7ced2bd', 60094, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:21', '2026-08-18 11:30:57'),
(808, 3, 888, 'My Drive/NAS/js/sync.js', '5d39df6781c546f19549a73b891d95aeffd8d5d041b84a53a06776d48585f221', 14959, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:21', '2026-08-18 11:30:57'),
(809, 3, 889, 'My Drive/NAS/js/share.js', '9ff1d6316c3a3d3d5f2cdfdb215650251418f9e7867f529d603bb809e732af48', 21029, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:22', '2026-08-18 11:30:57'),
(810, 3, 890, 'My Drive/NAS/js/excelpreview.js', '05ce82eb8cff81468320901aa7be1f6f7168a9dfc60e6e37e7bbf95d842e6a69', 32387, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:22', '2026-08-18 11:30:57'),
(811, 3, 891, 'My Drive/NAS/js/utils.js', 'd58c5d6b6a80b2b498dc6ef69eee9be90d4e68ea62b49557fb5e53500cbd330d', 4520, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:22', '2026-08-18 11:30:57'),
(812, 3, 892, 'My Drive/NAS/js/websocket-client.js', '98a4ad26d5f2ff1bacd63fbe7c4fc6b969cc22ed32a2efea813ed4799fed752d', 10096, '2026-08-18 11:30:57', 'synced', 'none', NULL, NULL, 0, '2026-08-18 09:11:23', '2026-08-18 11:30:57');

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
(3, 177, 'prafulsshah', 'prafulsshah@yahoo.com', '$2y$10$OZwjn8mjRWcnAw80amvUOeIb0HHqzEQRm48S.M5OkuWzPbk9b9B3q', 10737418240, 69408189, '2026-08-04 05:33:47', '2026-08-19 03:58:50', 1),
(4, NULL, 'nastest1', 'nastest1@example.com', '$2y$10$1IP2jiTSg.M.qkSZThiwr..yuWILztB.34VLxNwbp2RjitNWJVGza', 10737418240, 0, '2026-08-12 04:05:38', NULL, 1),
(5, 2, 'kumar', 'balasai006@gmail.com', '$2y$10$OOZmBWQZYr3fcUPUWZoT2eUnlq7/tEcHGgyxmvYqoND.Jx7OJboWa', 10737418240, 832740, '2026-08-13 04:03:24', '2026-08-18 04:13:15', 1);

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
-- Table structure for table `users_backup_before_efactor_nas`
--

CREATE TABLE `users_backup_before_efactor_nas` (
  `id` int NOT NULL DEFAULT '0',
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
-- Dumping data for table `users_backup_before_efactor_nas`
--

INSERT INTO `users_backup_before_efactor_nas` (`id`, `username`, `email`, `password_hash`, `storage_limit`, `storage_used`, `created_at`, `last_login`, `is_active`) VALUES
(2, 'admin', 'admin@example.com', '$2y$10$atDnBaiAO5HhVIhG6l8rI.JyaPpU5bkQpln9iNf/fCBuXFV/dsVGW', 10737418240, 54260738, '2026-08-03 05:02:58', NULL, 1),
(3, 'prafulsshah', 'prafulsshah@yahoo.com', '$2y$10$OZwjn8mjRWcnAw80amvUOeIb0HHqzEQRm48S.M5OkuWzPbk9b9B3q', 10737418240, 61277319, '2026-08-04 05:33:47', NULL, 1),
(4, 'nastest1', 'nastest1@example.com', '$2y$10$1IP2jiTSg.M.qkSZThiwr..yuWILztB.34VLxNwbp2RjitNWJVGza', 10737418240, 0, '2026-08-12 04:05:38', NULL, 1);

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
(32, 3, 10, NULL, 1, '2026-08-04 05:33:47', '2026-08-04 05:33:47'),
(33, 4, 1, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(34, 4, 2, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(35, 4, 3, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(36, 4, 4, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(37, 4, 5, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(38, 4, 6, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(39, 4, 7, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(40, 4, 8, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(41, 4, 9, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(42, 4, 10, NULL, 1, '2026-08-12 04:05:38', '2026-08-12 04:05:38'),
(43, 5, 1, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(44, 5, 2, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(45, 5, 3, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(46, 5, 4, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(47, 5, 5, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(48, 5, 6, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(49, 5, 7, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(50, 5, 8, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(51, 5, 9, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24'),
(52, 5, 10, NULL, 1, '2026-08-13 04:03:24', '2026-08-13 04:03:24');

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
(10, 3, 'folder', 6, '2026-08-06 08:19:36'),
(12, 3, 'folder', 2, '2026-08-06 09:30:36');

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
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_users_efactor_user_id` (`efactor_user_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=893;

--
-- AUTO_INCREMENT for table `file_categories`
--
ALTER TABLE `file_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT for table `shared_items`
--
ALTER TABLE `shared_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `storage_clusters`
--
ALTER TABLE `storage_clusters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

--
-- AUTO_INCREMENT for table `sync_folders`
--
ALTER TABLE `sync_folders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sync_logs`
--
ALTER TABLE `sync_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `sync_queue`
--
ALTER TABLE `sync_queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `sync_sessions`
--
ALTER TABLE `sync_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sync_status`
--
ALTER TABLE `sync_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=813;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_clusters`
--
ALTER TABLE `user_clusters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user_starred_items`
--
ALTER TABLE `user_starred_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
