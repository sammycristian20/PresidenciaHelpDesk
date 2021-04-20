-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 05, 2018 at 02:08 PM
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
-- Database: `development`
--

--
-- Dumping data for table `common_settings`
--

INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('helptopic_link_with_type', '', '0', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09'),
('allow_external_login', '', '0', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09'),
('allow_users_to_access_system_url', '', '0', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09'),
('redirect_unauthenticated_users_to', '', '', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `department_assign_manager`
--

DROP TABLE IF EXISTS `department_assign_manager`;
CREATE TABLE IF NOT EXISTS `department_assign_manager` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `helptopic_assign_type`
--

DROP TABLE IF EXISTS `helptopic_assign_type`;
CREATE TABLE IF NOT EXISTS `helptopic_assign_type` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `helptopic_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `in_app_push_notifications`
--

DROP TABLE IF EXISTS `in_app_push_notifications`;
CREATE TABLE IF NOT EXISTS `in_app_push_notifications` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscribed_user_id` int(11) NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('pending','failed','delivered') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribed_users`
--

DROP TABLE IF EXISTS `subscribed_users`;
CREATE TABLE IF NOT EXISTS `subscribed_users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `browser_name` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `version` int(11) NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `platform` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------------------------------------

-- 
-- Alter table column name in `kb_article`
-- 
ALTER TABLE `kb_article` CHANGE `sco_title` `seo_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

-- ----------------------------------------------------------

-- 
-- Alter table column name in `kb_pages`
-- 
ALTER TABLE `kb_pages` CHANGE `sco_title` `seo_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

-- ----------------------------------------------------------

-- 
-- Alter tables `labels` add `visible_to`
-- 
ALTER TABLE `labels` ADD `visible_to` TEXT NULL DEFAULT NULL AFTER `updated_at`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
