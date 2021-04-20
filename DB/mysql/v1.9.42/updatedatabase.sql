-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 01, 2018 at 04:08 PM
-- Server version: 5.7.22-0ubuntu0.17.10.1
-- PHP Version: 7.1.19-1+ubuntu17.10.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `development_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `ticket_filters`
--

CREATE TABLE `ticket_filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_filter_meta`
--

CREATE TABLE `ticket_filter_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_filter_id` int(10) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_filter_shareables`
--

CREATE TABLE `ticket_filter_shareables` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_filter_id` int(10) UNSIGNED NOT NULL,
  `ticket_filter_shareable_id` int(10) UNSIGNED NOT NULL,
  `ticket_filter_shareable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ticket_filters`
--
ALTER TABLE `ticket_filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_filters_user_id_foreign` (`user_id`);

--
-- Indexes for table `ticket_filter_meta`
--
ALTER TABLE `ticket_filter_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_filter_meta_ticket_filter_id_foreign` (`ticket_filter_id`);

--
-- Indexes for table `ticket_filter_shareables`
--
ALTER TABLE `ticket_filter_shareables`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ticket_filters`
--
ALTER TABLE `ticket_filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket_filter_meta`
--
ALTER TABLE `ticket_filter_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket_filter_shareables`
--
ALTER TABLE `ticket_filter_shareables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `ticket_filters`
--
ALTER TABLE `ticket_filters`
  ADD CONSTRAINT `ticket_filters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_filter_meta`
--
ALTER TABLE `ticket_filter_meta`
  ADD CONSTRAINT `ticket_filter_meta_ticket_filter_id_foreign` FOREIGN KEY (`ticket_filter_id`) REFERENCES `ticket_filters` (`id`);

-- 
-- Truncate bar_notification table 
-- 
TRUNCATE TABLE bar_notifications;

-- 
-- Update settings system table
-- 
UPDATE `settings_system` SET `version` = 'v1.9.42';

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;