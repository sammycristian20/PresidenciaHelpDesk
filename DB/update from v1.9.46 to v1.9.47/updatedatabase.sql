-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 18, 2018 at 12:21 AM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `faveo_pro`
--

-- 
-- Truncate bar_notification table 
-- 
TRUNCATE TABLE bar_notifications;


--
-- Table structure for table `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `disposition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `driver` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `linked_attachments`
--

CREATE TABLE IF NOT EXISTS `linked_attachments` (
  `id` int(10) unsigned NOT NULL,
  `attachment_id` int(10) unsigned NOT NULL,
  `category_id` int(11) NOT NULL,
  `category_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `linked_attachments`
--
ALTER TABLE `linked_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `linked_attachments_attachment_id_foreign` (`attachment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `linked_attachments`
--
ALTER TABLE `linked_attachments`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;


--
-- Alter users table
--
ALTER TABLE `users` CHANGE `phone_number` `phone_number` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

--
-- Alter `plugins` table
--
ALTER TABLE `plugins` add `version` VARCHAR(30) NOT NULL DEFAULT '0.0.0';

--
-- Dump data into migrations table
--

INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2018_11_26_062439_create_attachments_table', 8),
('2018_11_26_062543_create_linked_attachments_table', 8),
('2018_11_27_110014_alter_user_table_phone_number', 8),
('2018_11_27_122807_add_version_to_plugins_table', 8),
('2018_09_03_054808_create_ticket_filters_table', 1),
('2018_09_03_113219_create_ticket_filter_metas_table', 1),
('2018_09_03_115529_create_ticket_filter_shareables_table', 1);

-- 
-- Update settings system table
-- 
UPDATE `settings_system` SET `version` = 'v1.9.47';

-- --------------------------------------------------------

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
