-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 27, 2017 at 07:14 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `listeners`
--

DROP TABLE IF EXISTS `listeners`;
CREATE TABLE IF NOT EXISTS `listeners` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `performed_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `rule_match` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listener_actions`
--

DROP TABLE IF EXISTS `listener_actions`;
CREATE TABLE IF NOT EXISTS `listener_actions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listener_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta` longtext COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_action` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listener_events`
--

DROP TABLE IF EXISTS `listener_events`;
CREATE TABLE IF NOT EXISTS `listener_events` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listener_id` int(11) NOT NULL,
  `event` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `condition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `old` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listener_rules`
--

DROP TABLE IF EXISTS `listener_rules`;
CREATE TABLE IF NOT EXISTS `listener_rules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listener_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `condition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_rule` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ------------------------------------------------------

--
-- Alter organization tables
--
ALTER TABLE `organization` ADD `logo` VARCHAR(255) NOT NULL AFTER `fax`;


--
-- Alter sla_plan table
--
ALTER TABLE `sla_plan` ADD `order` INT(11) NOT NULL AFTER `apply_sla_tags`;

ALTER TABLE `sla_plan` ADD `is_default` TINYINT(1) NOT NULL DEFAULT '0' AFTER `order`;

UPDATE `sla_plan` SET `is_default` = 1 WHERE `id` = 1;

--
-- Alter sla_targets table
--
ALTER TABLE `sla_targets` ADD `in_app` INT(11) NOT NULL AFTER `updated_at`;

--
-- Alter workflow_rules table
--
ALTER TABLE `workflow_rules` ADD `custom_rule` LONGTEXT NULL AFTER `created_at`;

--
-- Alter workflow_actions tables
--
ALTER TABLE `workflow_action` ADD `custom_action` LONGTEXT NOT NULL AFTER `created_at`;

-- ----------------------------------------------------------


--
-- Dumping data for table `timezone`
--

INSERT INTO `timezone` (`name`, `location`) VALUES
('America/Santo_Domingo', '(GMT-04:00) Santo_Domingo');

--
-- Dumping data for table `date_time_format`
--

INSERT INTO `date_time_format` (`format`) VALUES
('F j, Y, g:i a'),
('jS F Y'),
('H:i');

--
-- Dumping data for table `common_settings`
--

INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('dashboard-statistics', 'departments,agents,teams', '', '', '2017-12-27 13:35:14', '2017-12-27 13:35:14');

--
-- Dumping data for table `settings_alert_notice`
--

INSERT INTO `settings_alert_notice` (`key`, `value`, `created_at`, `updated_at`) VALUES
('task_alert_mode', 'email,in-app-notify', '2017-12-06 12:08:41', '2017-12-06 12:08:41'),
('task_alert_persons', 'admin,agent', '2017-12-06 12:08:41', '2017-12-06 12:08:41'),
('task_alert_status', '1', '2017-12-06 12:08:41', '2017-12-06 12:08:41');


--
-- Dumping data for table `requireds`
--

INSERT INTO `requireds` (`form`, `field`, `agent`, `client`, `parent`, `option`, `label`, `created_at`, `updated_at`) VALUES
('ticket', 'org_dept', NULL, NULL, NULL, '', 'Micro Organisation', '2017-12-27 13:35:14', '2017-12-27 13:35:14');


-- ---------------------------------------------------------------

--
-- Emplty tables
--
TRUNCATE `workflow_action`;
TRUNCATE `workflow_name`;
TRUNCATE `workflow_rules`;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
