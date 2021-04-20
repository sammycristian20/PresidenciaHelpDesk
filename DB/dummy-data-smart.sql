-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 05, 2018 at 06:47 PM
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
-- Database: `demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent_type_relations`
--

DROP TABLE IF EXISTS `agent_type_relations`;
CREATE TABLE IF NOT EXISTS `agent_type_relations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_settings`
--

DROP TABLE IF EXISTS `api_settings`;
CREATE TABLE IF NOT EXISTS `api_settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval`
--

DROP TABLE IF EXISTS `approval`;
CREATE TABLE IF NOT EXISTS `approval` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `approval`
--

INSERT INTO `approval` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'approval', '0', '2017-03-29 05:23:24', '2017-03-29 05:23:24');

-- --------------------------------------------------------

--
-- Table structure for table `approval_metas`
--

DROP TABLE IF EXISTS `approval_metas`;
CREATE TABLE IF NOT EXISTS `approval_metas` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `approval_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banlist`
--

DROP TABLE IF EXISTS `banlist`;
CREATE TABLE IF NOT EXISTS `banlist` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ban_status` tinyint(1) NOT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bar_notifications`
--

DROP TABLE IF EXISTS `bar_notifications`;
CREATE TABLE IF NOT EXISTS `bar_notifications` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
CREATE TABLE IF NOT EXISTS `bills` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` int(11) NOT NULL,
  `agent` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `hours` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `billable` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount_hourly` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_type`
--

DROP TABLE IF EXISTS `bill_type`;
CREATE TABLE IF NOT EXISTS `bill_type` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `price` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_holidays`
--

DROP TABLE IF EXISTS `business_holidays`;
CREATE TABLE IF NOT EXISTS `business_holidays` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hours_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_holidays_business_hours_id_foreign` (`business_hours_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

DROP TABLE IF EXISTS `business_hours`;
CREATE TABLE IF NOT EXISTS `business_hours` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `timezone` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`id`, `name`, `description`, `status`, `timezone`, `created_at`, `updated_at`, `is_default`) VALUES
(1, 'Default Business-Hours', 'default', 1, 'US/Central', '2017-03-29 05:23:24', '2018-01-03 06:51:14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `business_open_custom_time`
--

DROP TABLE IF EXISTS `business_open_custom_time`;
CREATE TABLE IF NOT EXISTS `business_open_custom_time` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_schedule_id` int(10) UNSIGNED NOT NULL,
  `open_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `close_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_open_custom_time_business_schedule_id_foreign` (`business_schedule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_schedule`
--

DROP TABLE IF EXISTS `business_schedule`;
CREATE TABLE IF NOT EXISTS `business_schedule` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_hours_id` int(10) UNSIGNED NOT NULL,
  `days` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_schedule_business_hours_id_foreign` (`business_hours_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `business_schedule`
--

INSERT INTO `business_schedule` (`id`, `business_hours_id`, `days`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sunday', 'Closed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(2, 1, 'Monday', 'Open_fixed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(3, 1, 'Tuesday', 'Open_fixed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(4, 1, 'Wednusday', 'Open_fixed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(5, 1, 'Thursday', 'Open_fixed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(6, 1, 'Friday', 'Open_fixed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(7, 1, 'Saturday', 'Closed', '2017-03-29 05:23:24', '2017-03-29 05:23:24');

-- --------------------------------------------------------

--
-- Table structure for table `canned_response`
--

DROP TABLE IF EXISTS `canned_response`;
CREATE TABLE IF NOT EXISTS `canned_response` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `common_settings`
--

DROP TABLE IF EXISTS `common_settings`;
CREATE TABLE IF NOT EXISTS `common_settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `option_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `option_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `optional_field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `common_settings`
--

INSERT INTO `common_settings` (`id`, `option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
(1, 'itil', '', '0', '', NULL, NULL),
(2, 'ticket_token_time_duration', '1', '', '', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(3, 'enable_rtl', '0', '', '', '2017-03-29 05:23:27', '2017-04-02 17:20:48'),
(4, 'user_set_ticket_status', '', '1', '', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(5, 'send_otp', '', '0', '', '2017-03-29 05:23:27', '2017-04-02 17:20:48'),
(6, 'email_mandatory', '', '1', '', '2017-03-29 05:23:27', '2017-04-02 17:20:48'),
(7, 'user_priority', '', '0', '', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(8, 'storage', 'local', '', 'default', '2017-03-29 05:23:38', '2017-03-29 05:23:38'),
(9, 'storage', 'C:\\wamp64\\www\\faveo-helpdesk-pro-master-data\\storage\\app/private', '', 'private-root', '2017-03-29 05:23:38', '2017-03-29 05:23:38'),
(10, 'storage', 'C:\\wamp64\\www\\faveo-helpdesk-pro-master-data\\public', '', 'public-root', '2017-03-29 05:23:38', '2017-03-29 05:23:38'),
(11, 'dummy_data_installation', '', '1', '', '2017-03-29 05:23:38', '2017-03-29 05:23:38'),
(12, 'user_registration', '', '0', '', '2017-06-20 18:40:03', '2017-06-20 18:40:03'),
(13, 'user_show_org_ticket', '', '0', '', '2017-06-20 18:40:03', '2017-06-27 01:16:20'),
(14, 'user_reply_org_ticket', '', '0', '', '2017-06-20 18:40:03', '2017-06-20 18:40:03'),
(15, 'allow_users_to_create_ticket', '', '0', '', '2017-06-20 18:40:03', '2017-06-20 18:40:03'),
(16, 'login_restrictions', 'email', '', '', '2017-07-07 01:24:04', '2017-07-07 01:24:04'),
(17, 'micro_organization_status', '', '0', '', '2017-10-03 20:23:47', '2017-10-03 20:23:47'),
(18, 'micro_organization_status', '', '0', '', '2017-10-03 20:23:47', '2017-10-03 20:23:47'),
(19, 'dashboard-statistics', 'departments,agents,teams', '', '', '2017-12-27 13:35:14', '2017-12-27 13:35:14'),
(20, 'helptopic_link_with_type', '', '1', '', '2018-02-05 06:41:09', '2018-02-05 13:17:17'),
(21, 'allow_external_login', '', '0', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09'),
(22, 'allow_users_to_access_system_url', '', '0', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09'),
(23, 'redirect_unauthenticated_users_to', '', '', '', '2018-02-05 06:41:09', '2018-02-05 06:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

DROP TABLE IF EXISTS `conditions`;
CREATE TABLE IF NOT EXISTS `conditions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `job` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`id`, `job`, `value`, `created_at`, `updated_at`) VALUES
(1, 'fetching', 'everyTenMinutes', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(2, 'notification', 'everyTenMinutes', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(3, 'work', 'everyTenMinutes', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(4, 'followup', 'everyTenMinutes', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(5, 'escalate', 'everyTenMinutes', '2017-03-29 05:23:27', '2017-03-29 05:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `country_code`
--

DROP TABLE IF EXISTS `country_code`;
CREATE TABLE IF NOT EXISTS `country_code` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `iso` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nicename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `iso3` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `numcode` smallint(6) NOT NULL,
  `phonecode` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=240 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `country_code`
--

INSERT INTO `country_code` (`id`, `iso`, `name`, `nicename`, `iso3`, `numcode`, `phonecode`, `created_at`, `updated_at`) VALUES
(1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(8, 'AQ', 'ANTARCTICA', 'Antarctica', 'NUL', 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', 'NUL', 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', 'NUL', 0, 246, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(44, 'CN', 'CHINA', 'China', 'CHN', 156, 86, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', 'NUL', 0, 61, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', 'NUL', 0, 672, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(53, 'CI', 'COTE DIVOIRE', 'Cote DIvoire', 'CIV', 384, 225, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', 'NUL', 0, 0, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', 'NUL', 0, 0, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(99, 'IN', 'INDIA', 'India', 'IND', 356, 91, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(112, 'KP', 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF', 'Korea, Democratic Peoples Republic of', 'PRK', 408, 850, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(116, 'LA', 'LAO PEOPLES DEMOCRATIC REPUBLIC', 'Lao Peoples Democratic Republic', 'LAO', 418, 856, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(137, 'YT', 'MAYOTTE', 'Mayotte', 'NUL', 0, 269, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', 'NUL', 0, 970, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(168, 'PE', 'PERU', 'Peru', 'PER', 604, 51, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', 'NUL', 0, 381, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', 'NUL', 0, 0, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', 'NUL', 0, 670, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', 'NUL', 0, 1, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260, '2017-03-29 05:23:26', '2017-03-29 05:23:26'),
(239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263, '2017-03-29 05:23:26', '2017-03-29 05:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `date_format`
--

DROP TABLE IF EXISTS `date_format`;
CREATE TABLE IF NOT EXISTS `date_format` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `date_format`
--

INSERT INTO `date_format` (`id`, `format`) VALUES
(1, 'dd/mm/yyyy'),
(2, 'dd-mm-yyyy'),
(3, 'dd.mm.yyyy'),
(4, 'mm/dd/yyyy'),
(5, 'mm:dd:yyyy'),
(6, 'mm-dd-yyyy'),
(7, 'yyyy/mm/dd'),
(8, 'yyyy.mm.dd'),
(9, 'yyyy-mm-dd');

-- --------------------------------------------------------

--
-- Table structure for table `date_time_format`
--

DROP TABLE IF EXISTS `date_time_format`;
CREATE TABLE IF NOT EXISTS `date_time_format` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `date_time_format`
--

INSERT INTO `date_time_format` (`id`, `format`) VALUES
(1, 'd/m/Y H:i:s'),
(2, 'd.m.Y H:i:s'),
(3, 'd-m-Y H:i:s'),
(4, 'm/d/Y H:i:s'),
(5, 'm.d.Y H:i:s'),
(6, 'm-d-Y H:i:s'),
(7, 'Y/m/d H:i:s'),
(8, 'Y.m.d H:i:s'),
(9, 'Y-m-d H:i:s'),
(10, 'F j, Y, g:i a'),
(11, 'jS F Y'),
(12, 'H:i');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `manager` int(10) UNSIGNED DEFAULT NULL,
  `ticket_assignment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `outgoing_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_set` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_ticket_response` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_message_response` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_response_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `recipient` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_access` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department_sign` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `business_hour` int(11) NOT NULL,
  `nodes` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `manager_2` (`manager`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`, `type`, `manager`, `ticket_assignment`, `outgoing_email`, `template_set`, `auto_ticket_response`, `auto_message_response`, `auto_response_email`, `recipient`, `group_access`, `department_sign`, `created_at`, `updated_at`, `business_hour`, `nodes`) VALUES
(1, 'Support', '1', 6, '', '', '', '', '', '', '', '', '', '2017-03-29 05:23:25', '2017-03-30 03:06:18', 0, NULL),
(2, 'Sales', '1', 4, '', '', '', '', '', '', '', '', '', '2017-03-29 05:23:25', '2017-03-30 03:06:09', 0, NULL),
(3, 'Operation', '1', 7, '', '', '', '', '', '', '', '', '', '2017-03-29 05:23:25', '2017-03-30 03:05:50', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_assign_agents`
--

DROP TABLE IF EXISTS `department_assign_agents`;
CREATE TABLE IF NOT EXISTS `department_assign_agents` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `department_assign_agents`
--

INSERT INTO `department_assign_agents` (`id`, `department_id`, `agent_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2017-03-29 05:23:28', '2017-03-29 05:23:28'),
(2, 1, 2, '2017-03-29 05:43:44', '2017-03-29 05:43:44'),
(3, 1, 3, '2017-03-29 05:44:51', '2017-03-29 05:44:51'),
(4, 2, 4, '2017-03-29 05:46:01', '2017-03-29 05:46:01'),
(10, 3, 5, '2017-03-29 05:50:04', '2017-03-29 05:50:04'),
(9, 1, 5, '2017-03-29 05:50:04', '2017-03-29 05:50:04'),
(7, 1, 6, '2017-03-29 05:48:48', '2017-03-29 05:48:48'),
(8, 3, 7, '2017-03-29 05:49:54', '2017-03-29 05:49:54'),
(11, 1, 8, '2017-03-29 05:51:23', '2017-03-29 05:51:23'),
(12, 2, 8, '2017-03-29 05:51:23', '2017-03-29 05:51:23'),
(13, 1, 25, '2017-10-04 05:44:57', '2017-10-04 05:44:57'),
(14, 1, 26, '2017-10-04 02:35:06', '2017-10-04 02:35:06');

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
-- Table structure for table `department_canned_resposne`
--

DROP TABLE IF EXISTS `department_canned_resposne`;
CREATE TABLE IF NOT EXISTS `department_canned_resposne` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dept_id` int(10) UNSIGNED NOT NULL,
  `canned_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_canned_resposne_dept_id_foreign` (`dept_id`),
  KEY `department_canned_resposne_canned_id_foreign` (`canned_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` int(10) UNSIGNED DEFAULT NULL,
  `priority` int(10) UNSIGNED DEFAULT NULL,
  `help_topic` int(10) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_encryption` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mailbox_protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imap_config` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folder` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_encryption` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_validate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_authentication` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_response` tinyint(1) NOT NULL,
  `fetching_status` tinyint(1) NOT NULL,
  `move_to_folder` tinyint(1) NOT NULL,
  `delete_email` tinyint(1) NOT NULL,
  `do_nothing` tinyint(1) NOT NULL,
  `sending_status` tinyint(1) NOT NULL,
  `authentication` tinyint(1) NOT NULL,
  `header_spoofing` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department` (`department`,`priority`,`help_topic`),
  KEY `department_2` (`department`,`priority`,`help_topic`),
  KEY `priority` (`priority`),
  KEY `help_topic` (`help_topic`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `email_address`, `email_name`, `department`, `priority`, `help_topic`, `user_name`, `password`, `fetching_host`, `fetching_port`, `fetching_protocol`, `fetching_encryption`, `mailbox_protocol`, `imap_config`, `folder`, `sending_host`, `sending_port`, `sending_protocol`, `sending_encryption`, `smtp_validate`, `smtp_authentication`, `internal_notes`, `auto_response`, `fetching_status`, `move_to_folder`, `delete_email`, `do_nothing`, `sending_status`, `authentication`, `header_spoofing`, `created_at`, `updated_at`) VALUES
(2, 'demo@faveohelpdesk.com', 'Faveo Demo', NULL, NULL, NULL, 'demo@faveohelpdesk.com', 'brvgyhtfght10@(', 'mail.faveohelpdesk.com', '143', 'imap', '', 'novalidate-cert', '', '', 'mail.faveohelpdesk.com', '25', 'smtp', '', '', '', '', 0, 1, 0, 0, 0, 1, 0, 0, '2017-04-02 17:33:29', '2017-08-31 05:20:13');

-- --------------------------------------------------------

--
-- Table structure for table `email_threads`
--

DROP TABLE IF EXISTS `email_threads`;
CREATE TABLE IF NOT EXISTS `email_threads` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `message_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(11) NOT NULL,
  `reference_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fetching_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `email_threads`
--

INSERT INTO `email_threads` (`id`, `ticket_id`, `thread_id`, `message_id`, `uid`, `reference_id`, `created_at`, `updated_at`, `fetching_email`) VALUES
(134, 35, 154, '<fdb97c26-2ea1-bbcc-23b1-78d2555dd71d@agency9.com>', 26, '', '2017-08-31 05:20:13', '2017-08-31 05:20:13', 'demo@faveohelpdesk.com'),
(135, 35, 154, 'eb747a8efe0312648703e83764cfb4e9@www.faveohelpdesk.com', 0, '', '2017-08-31 05:20:13', '2017-08-31 05:20:13', '');

-- --------------------------------------------------------

--
-- Table structure for table `extra_orgs`
--

DROP TABLE IF EXISTS `extra_orgs`;
CREATE TABLE IF NOT EXISTS `extra_orgs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faveo_mails`
--

DROP TABLE IF EXISTS `faveo_mails`;
CREATE TABLE IF NOT EXISTS `faveo_mails` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_id` int(11) NOT NULL,
  `drive` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `faveo_mails`
--

INSERT INTO `faveo_mails` (`id`, `email_id`, `drive`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'smtp', 'fetching_status', 'on', '2017-03-29 05:25:33', '2017-03-29 05:25:33'),
(3, 2, 'smtp', 'user_name', '', '2017-08-31 05:20:13', '2017-08-31 05:20:13'),
(4, 2, 'smtp', 'fetching_status', 'on', '2017-08-31 05:20:13', '2017-08-31 05:20:13'),
(5, 2, 'smtp', 'count', '1', '2017-08-31 05:20:13', '2017-08-31 05:20:13'),
(6, 2, 'smtp', 'sys_email', 'on', '2017-08-31 05:20:13', '2017-08-31 05:20:13');

-- --------------------------------------------------------

--
-- Table structure for table `faveo_queues`
--

DROP TABLE IF EXISTS `faveo_queues`;
CREATE TABLE IF NOT EXISTS `faveo_queues` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

DROP TABLE IF EXISTS `filters`;
CREATE TABLE IF NOT EXISTS `filters` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `filters`
--

INSERT INTO `filters` (`id`, `ticket_id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'label', 'Request', '2017-03-29 23:22:19', '2017-03-29 23:22:19'),
(2, 2, 'label', 'Query', '2017-03-29 23:33:46', '2017-03-29 23:33:46'),
(3, 3, 'label', 'Query', '2017-03-29 23:37:36', '2017-03-29 23:37:36'),
(4, 5, 'label', 'Query', '2017-03-29 23:46:04', '2017-03-29 23:46:04'),
(11, 7, 'tag', 'Late delivery', '2017-03-29 23:53:54', '2017-03-29 23:53:54'),
(6, 7, 'label', 'Request', '2017-03-29 23:52:22', '2017-03-29 23:52:22'),
(7, 7, 'label', 'Query', '2017-03-29 23:52:22', '2017-03-29 23:52:22'),
(12, 8, 'label', 'Review', '2017-03-29 23:58:47', '2017-03-29 23:58:47'),
(13, 10, 'label', 'Query', '2017-03-30 00:07:21', '2017-03-30 00:07:21'),
(14, 16, 'label', 'Query', '2017-03-30 00:32:15', '2017-03-30 00:32:15'),
(15, 18, 'label', 'Query', '2017-03-30 00:42:22', '2017-03-30 00:42:22'),
(16, 25, 'label', 'Request', '2017-03-30 01:00:42', '2017-03-30 01:00:42'),
(17, 25, 'label', 'Query', '2017-03-30 01:00:42', '2017-03-30 01:00:42'),
(18, 28, 'label', 'Query', '2017-03-30 01:10:02', '2017-03-30 01:10:02'),
(19, 30, 'label', 'Query', '2017-03-30 01:14:36', '2017-03-30 01:14:36'),
(20, 30, 'label', 'Review', '2017-03-30 01:14:36', '2017-03-30 01:14:36'),
(21, 33, 'label', 'Query', '2017-03-30 03:04:35', '2017-03-30 03:04:35'),
(31, 33, 'tag', 'Rights', '2017-10-04 05:57:25', '2017-10-04 05:57:25'),
(32, 33, 'tag', 'Customer', '2017-10-04 05:57:25', '2017-10-04 05:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `followup`
--

DROP TABLE IF EXISTS `followup`;
CREATE TABLE IF NOT EXISTS `followup` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `condition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `followup`
--

INSERT INTO `followup` (`id`, `name`, `status`, `condition`, `created_at`, `updated_at`) VALUES
(1, 'followup', '', '', '2017-03-29 05:23:24', '2017-03-29 05:23:24');

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `json` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `form`, `json`, `created_at`, `updated_at`) VALUES
(1, 'ticket', '[{\"title\":\"Requester\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Requester\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Requester\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"email\",\"agentCCfield\":true,\"customerCCfield\":false,\"customerDisplay\":true,\"agentRequiredFormSubmit\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"requester\",\"agentDisplay\":true},{\"title\":\"Subject\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Subject\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Subject\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"text\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"subject\",\"agentDisplay\":true},{\"title\":\"Type\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Type\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Type\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"api\":\"type\",\"options\":[],\"default\":\"yes\",\"unique\":\"type\",\"agentDisplay\":true},{\"title\":\"Status\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Status\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Status\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"status\",\"options\":[],\"default\":\"yes\",\"unique\":\"status\",\"agentDisplay\":true},{\"title\":\"Priority\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Priority\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Priority\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"api\":\"priority\",\"options\":[],\"default\":\"yes\",\"unique\":\"priority\",\"agentDisplay\":true},{\"title\":\"Location\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Location\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Location\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"location\",\"options\":[],\"default\":\"yes\",\"unique\":\"location\",\"agentDisplay\":true},{\"title\":\"Help Topic\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Help Topic\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Help Topic\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"multiselect\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"api\":\"helptopic\",\"options\":[],\"default\":\"yes\",\"unique\":\"help_topic\",\"agentDisplay\":true},{\"title\":\"Department\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Department\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Department\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"multiselect\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"department\",\"options\":[],\"default\":\"yes\",\"unique\":\"department\",\"linkHelpTopic\":false,\"agentDisplay\":true},{\"title\":\"Assigned\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Assigned\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Assigned\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"assigned_to\",\"options\":[],\"default\":\"yes\",\"unique\":\"assigned\",\"agentDisplay\":true},{\"title\":\"Description\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Description\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Description\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"textarea\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"description\",\"media_option\":true,\"agentDisplay\":true},{\"title\":\"Company\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Company\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Company\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"api\":\"company\",\"options\":[],\"unique\":\"company\",\"agentDisplay\":true},{\"title\":\"Captcha\",\"customerDisplay\":true,\"default\":\"yes\",\"agentDisplay\":true},{\"title\":\"Micro Organisation\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Micro Organisation\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Micro Organisation\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":false,\"agentDisplay\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"org_dept\",\"options\":[],\"default\":\"yes\",\"unique\":\"org_dept\"}]', NULL, NULL),
(2, 'user', '[{\"title\":\"First Name\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"First Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"First Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"text\",\"customerDisplay\":true,\"agentRequiredFormSubmit\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"first_name\",\"agentDisplay\":true},{\"title\":\"Last Name\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Last Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Last Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"text\",\"customerDisplay\":true,\"agentRequiredFormSubmit\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"last_name\",\"agentDisplay\":true},{\"title\":\"User Name\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"User Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"User Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"text\",\"customerDisplay\":false,\"agentRequiredFormSubmit\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"user_name\",\"agentDisplay\":true},{\"title\":\"Work Phone\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Work Phone\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Work Phone\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"number\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"phone_number\",\"agentDisplay\":true},{\"title\":\"Email\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Email\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Email\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"email\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"default\":\"yes\",\"unique\":\"email\",\"agentDisplay\":true},{\"title\":\"Mobile Phone\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Mobile Phone\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Mobile Phone\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"number\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"default\":\"yes\",\"unique\":\"mobile\",\"agentDisplay\":true},{\"title\":\"Address\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Address\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Address\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"textarea\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"default\":\"no\",\"unique\":\"address\",\"agentDisplay\":true},{\"title\":\"Organisation\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Organisation\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Organisation\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select2\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"organisation\",\"agentDisplay\":true},{\"title\":\"Department Name\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Department Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Department Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"department\",\"options\":[],\"api\":\"organisationdept\",\"agentDisplay\":true},{\"title\":\"Captcha\",\"customerDisplay\":true,\"default\":\"yes\",\"agentDisplay\":true}]', NULL, NULL),
(3, 'organisation', '[{\"title\":\"Company Name\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Company Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Company Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"text\",\"customerDisplay\":true,\"agentRequiredFormSubmit\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"name\",\"agentDisplay\":true},{\"title\":\"Phone\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Phone\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Phone\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"number\",\"customerDisplay\":true,\"agentRequiredFormSubmit\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"phone\",\"agentDisplay\":true},{\"title\":\"Company Domain Name\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Company Domain Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Company Domain Name\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select2\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":true,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"domain\",\"agentDisplay\":true},{\"title\":\"Description\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Description\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Description\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"textarea\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":true,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"default\":\"yes\",\"unique\":\"internal_notes\",\"agentDisplay\":true},{\"title\":\"Address\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Address\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Address\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"textarea\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"default\":\"yes\",\"unique\":\"address\",\"agentDisplay\":true},{\"title\":\"Department\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Department\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Department\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select2\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":true,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"department\",\"agentDisplay\":true}]', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `group_assign_department`
--

DROP TABLE IF EXISTS `group_assign_department`;
CREATE TABLE IF NOT EXISTS `group_assign_department` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `halts`
--

DROP TABLE IF EXISTS `halts`;
CREATE TABLE IF NOT EXISTS `halts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `halted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_used` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `halted_time` int(11) DEFAULT NULL,
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
-- Table structure for table `help_topic`
--

DROP TABLE IF EXISTS `help_topic`;
CREATE TABLE IF NOT EXISTS `help_topic` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_form` int(10) UNSIGNED DEFAULT NULL,
  `department` int(10) UNSIGNED DEFAULT NULL,
  `ticket_status` int(10) UNSIGNED DEFAULT NULL,
  `thank_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ticket_num_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `auto_response` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nodes` longtext COLLATE utf8_unicode_ci,
  `linked_departments` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_form` (`custom_form`),
  KEY `department` (`department`),
  KEY `ticket_status` (`ticket_status`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `help_topic`
--

INSERT INTO `help_topic` (`id`, `topic`, `parent_topic`, `custom_form`, `department`, `ticket_status`, `thank_page`, `ticket_num_format`, `internal_notes`, `status`, `type`, `auto_response`, `created_at`, `updated_at`, `nodes`, `linked_departments`) VALUES
(1, 'Support query', '', NULL, 1, 1, '', '1', '', 1, 1, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25', NULL, NULL),
(2, 'Sales query', '', NULL, 2, 1, '', '1', '', 1, 1, 0, '2017-03-29 05:23:25', '2017-03-29 23:26:29', NULL, NULL),
(3, 'Operational query', '', NULL, 3, 1, '', '1', '', 1, 1, 0, '2017-03-29 05:23:25', '2017-03-29 23:26:37', NULL, NULL);

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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kb_article`
--

DROP TABLE IF EXISTS `kb_article`;
CREATE TABLE IF NOT EXISTS `kb_article` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `publish_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `kb_article`
--

INSERT INTO `kb_article` (`id`, `name`, `slug`, `description`, `status`, `type`, `publish_time`, `created_at`, `updated_at`, `template`, `seo_title`, `meta_description`) VALUES
(1, 'Shopping experience and review', 'shopping-experience-and-review', '<p>I lived in the USA for more than 10 years and am quite used to the shopping experience of that country. Most of the times, the quality of the items I can get from the American shops are much better than what we find in India. I was actually surprised to know that many of those high quality items that we buy in USA are \"Made in India\", but you can never buy it in India.<br /><br />\r\nI recently relocated to India after a pretty long time in the USA. Along with many other lifestyle issues, one of the major issue for me was the shopping experience and availability of many items we were using regularly. I started looking for options to bring stuff from USA and the most convenient option was to buy online, ship to a friend in USA and get them to bring it along, when they visit India. However, it wasn\'t very convenient. I had to wait too long for some friends to come to India and also it was inconvenient for them to bring a lot of items for me due to the baggage restrictions in the Airlines.<br />\r\n </p>\r\n\r\n<h2>Shop in USA and get it shipped to India</h2>\r\n\r\n<p><br />\r\nI came across a couple of online services that offer the <em>Buy in USA and Ship to India</em> service. I evaluated most of them. One of the disappointing factors about such services is that, most of them require a membership and an annual membership fee. This will be useful only if we buy a lot of items regularly. But I felt that over a period of time, my shopping frequency from USA will come down and eventually we will adapt ourselves to live with items available in India. Also, paying a membership fee to one shop will make me stick to one shop and I will lose the opportunity to grab the best deals from the best places.<br /><br />\r\nSo, I continued my search for an service with no membership fee and finally I landed onto a promotion of. I did some research and figured out some interesting facts about them. I tried an <a href=\"http://www.techulator.com/articles/Online-Shopping.aspx\">online purchase</a> through them and it worked out very smoothly, except that the local courier guy in my locality wanted me to come and pick up the item from their office rather than delivering it to my home address. But that is quite common in my place and I have found no courier service that can offer a good service in my area, so that is no reason to blame iShopinternational.com.</p>\r\n', 1, 1, '2017-01-23 16:30:00', '2017-03-29 05:30:25', '2017-03-29 05:30:25', '', '', ''),
(2, 'A site for deals, discounts coupons and online shopping', 'a-site-for-deals-discounts-coupons-and-online-shopping', '<p>The trend of <a href=\"http://www.techulator.com/articles/Online-Shopping-Websites.aspx\">online shopping in India</a> despite starting late is growing at a phenomenal rate. With big players fighting and competing hard against each other to gain and attract consumers is at a all-time high. And competition among business players usually benefits the consumers/end-users. I know that by now you would be wondering how? How are we benefiting from this competition? What`s in for us as shoppers?<br /><br />\r\nHere`s how:<br /><br />\r\nWe as a online shoppers now have lot more choices and range than ever before - Be it the seller, products, brands, services<br />\r\nIncreasing competition to lure the customers is resulting in lower prices with best services (more free shipping, <a href=\"http://www.techulator.com/articles/Cash-On-Delivery.aspx\">cash on delivery</a>, better return policies)<br />\r\nAnd most importantly, we benefit from the marketing strategies that includes daily deals, discount coupons, promotional and sale offers from these online retailers trying to gain the customers and increase their reach.<br /><br />\r\nBut the biggest and most important question that still needs an answer is - how much are we benefiting from all these deals and coupons? Not much in my view because it is practically impossible for us as shoppers to keep track of 100s and 1000s of site for all the good deals, discounts and coupons. And that is why daily deals sites are important. And one of the best daily deals <a href=\"http://www.techulator.com/articles/Discount-Coupons.aspx\">discount coupons</a> website in India is www.zordaar.com<br /><br />\r\nwww.zordaar.com aggregates and provides the best deals, discount coupons, sale and promotional offers available in the market from various online stores on a daily basis. They not only help us save money, but also help us save time and shop wisely. Finding latest deals and offers at www.zordaar.com is a breeze because of their extensive categories which covers deals, offers and discount coupons not only for Men`s and Women`s clothing, accessories, footwear but also has latest deals and offers on Home Decor, Furnishing, <a href=\"http://www.techulator.com/articles/Electronics.aspx\">electronics</a>, mobile, tablets and many more.<br /><br />\r\nQuality of the deals, heavy and big discounts and a good sense in picking the deals, discount coupons and offers that will appeal the shoppers is what makes www.zordaar.com stand-out from other daily deals websites. And the best thing is that it does`nt take more than 5 minutes to browse through all the deals, discount coupons and offers - thanks to their simple and elegant interface.<br />\r\n </p>\r\n', 1, 1, '2017-03-13 16:30:00', '2017-03-29 05:31:06', '2017-03-29 05:31:06', '', '', ''),
(3, 'Buying and Receiving the Product', 'buying-and-receiving-the-product', '<p><br />\r\nAfter finding and selecting your desired product, the webpage usually has a \"checkout\" option. When you check out, you are often given a list of shipping and payment options. Shipping options include standard, expedited and overnight shipping. Depending on the shipping company being used and your location, standard shipping usually takes seven to 21 <a href=\"http://www.investopedia.com/terms/b/business-day.asp\">business days</a> and expedited shipping can take anywhere from two to six business days.</p>\r\n\r\n<p>When it comes to paying for your purchase(s), there are also different options:</p>\r\n\r\n<ul><li><strong>E-Check</strong>: This payment option is just like paying directly from your bank account. If you choose to pay by <a href=\"http://www.investopedia.com/terms/e/electroniccheck.asp\">electronic check</a>, you are required to enter your <a href=\"http://www.investopedia.com/terms/r/routing_transit_number.asp\">routing</a> and account numbers. Once this is done, the amount is taken directly from your bank account.</li>\r\n  <li><strong>Credit Card</strong>: When you pay by <a href=\"http://www.investopedia.com/terms/c/creditcard.asp\">credit card</a>, instead of swiping your card like you would at a <a href=\"http://www.investopedia.com/terms/b/brickandmortar.asp\">brick-and-mortar store</a>, you type the required credit card information into provided fields. Required information includes your credit card number, <a href=\"http://www.investopedia.com/terms/e/expiration-date.asp\">expiration date</a>, type of card (Visa, MasterCard, etc) and verification/security number, which is usually the last three digits on the back of the card, right above the signature.</li>\r\n  <li><strong>Payment Vendors</strong>: Payment vendors or payment processing companies, such as <a href=\"http://www.investopedia.com/terms/p/paypal.asp\">PayPal</a>, are <a href=\"http://www.investopedia.com/terms/e/ecommerce.asp\">ecommerce</a> businesses that provide payment exchange services. They allow people to safely transfer money to one another without sharing financial information. Before you make purchase through a payment vendor, you\'ll need to set up an account first to verify your credit card and/or <a href=\"http://www.investopedia.com/terms/f/financialinstitution.asp\">financial institution</a> information.</li>\r\n</ul><p><strong>Advantages of Online Trading</strong><br />\r\nThere are a lot of benefits to be gained from buying and selling online. These include:</p>\r\n\r\n<ul><li><strong>Convenience:</strong> It is very convenient to be able to do all your shopping from one spot - your couch!</li>\r\n  <li><strong>Cost Savings:</strong> With ever-increasing gas prices, shopping online saves you the cost of driving to and between stores as well parking fees. You will also save time by avoiding standing in line, particularly around the holidays, when stores are very busy.</li>\r\n <li><strong>Variety:</strong> The internet provides sellers with unlimited shelf space, so they are more likely to offer a wider variety of products than they would in brick-and-mortar stores.</li>\r\n <li><strong>No Pressure:</strong> In a virtual or online store, there is no salesperson hovering around and pressuring you to purchase an item.</li>\r\n  <li><strong>Easy Comparison</strong>: Shopping online eliminates the need to wander from store to store trying to compare prices.</li>\r\n</ul>', 1, 1, '2017-03-29 16:31:00', '2017-03-29 05:31:34', '2017-03-29 05:31:34', '', '', ''),
(4, 'The history of online shopping', 'the-history-of-online-shopping', '<p><strong>1979</strong> – It all began when <strong><a href=\"http://en.wikipedia.org/wiki/Michael_Aldrich\">Michael Aldrich</a></strong> ‘invented’ online shopping.  Using <strong><a href=\"http://en.wikipedia.org/wiki/Videotext\">videotex</a></strong>, a two way message service, it revolutionised businesses. We now know this as e-commerce.</p>\r\n\r\n<p><strong>1981</strong> – Saw the first <strong><a href=\"http://speckycdn.sdm.netdna-cdn.com/wp-content/uploads/2011/11/shopping_infograhic_large2.jpg\">business to business</a></strong> transaction by UK based Thomson holidays.</p>\r\n\r\n<p><strong>1982</strong> – Mintel, a videotex online service accessible by telephone lines, could be used to make online purchases, train reservations, check stock prices, chat and search a telephone directory. It is considered the <strong><a href=\"http://en.wikipedia.org/wiki/Minitel\">most successful</a></strong> pre WWW online service.</p>\r\n\r\n<p><strong>1984</strong> – The first ever shopper buys online at a Tesco store.</p>\r\n\r\n<p><strong>1985</strong> – Nissan carries out the first online credit check.</p>\r\n\r\n<p><strong>1987</strong> – <strong><a href=\"https://www.mycommerce.com/swreg\">SWREG</a></strong> was founded. This offered businesses a chance to sell products online.  As it is today, SWREG offers many payment options, as well as customization and distribution into international markets. Users can purchase items with their currency of preference using all major debit and credit cards.</p>\r\n\r\n<p><strong>1989</strong> – In the USA the first online grocery store starts trading.  Peapod.com</p>\r\n\r\n<p><strong>1990</strong> – <strong><a href=\"http://www.mercurynews.com/business/ci_26844823/q-tim-berners-lee-professor-inventor-world-wide\">Tim Berners-Lee </a></strong>created the first WWW server and browser.  This started a whole new revolution. Nearly 25 years on, he is today still working hard at guiding the development and spread of the web, using the mantra ‘If it isn’t on the web then it isn’t happening’.</p>\r\n\r\n<p><strong>1991</strong> – The internet is commercialised and we saw the birth of e-commerce.</p>\r\n\r\n<p><strong>1994</strong> – Netscape launches the first commercial browser, which was once the dominant browser in terms of ‘visitors.’  It lost out in <strong><a href=\"http://www.nethistory.info/History%20of%20the%20Internet/browserwars.html\">the first browser war.</a></strong></p>\r\n\r\n<p><strong>1995</strong> – Amazon started selling books online; currently it sells almost anything. <strong><a href=\"http://www.instantshift.com/2010/03/26/the-history-of-online-shopping-in-nutshell/\">Companies like Dell and Cisco</a></strong> started using the internet for all their transactions. eBay is founded by Pierre Omidyar, though it was originally called Auctionweb.</p>\r\n\r\n<p><strong>1997</strong> – Began the era of comparison sites.</p>\r\n\r\n<p><strong>1998</strong> – <strong><a href=\"https://www.paypal-media.com/history\">Paypal</a></strong> is founded. This enabled transactions of money without sharing financial information and gave customers the flexibility to pay using their PayPal account balances, bank accounts, PayPal Credit and other credit cards.  Today, people can now pay across their favorite apps in a single touch (one touch) on any platform, eliminating the need for usernames and passwords each time you pay.</p>\r\n\r\n<p><strong>1999</strong> – the first online-only shop began, ‘Zappos’ although it was later bought by Amazon for $1.2 bn.</p>\r\n\r\n<p><strong>2001</strong> – Amazon launches mobile services.</p>\r\n\r\n<p><strong>2003</strong> – <strong><a href=\"http://news.bbc.co.uk/1/hi/business/3515287.stm\">US online shopping</a></strong> hits $50 billion, in the same year Amazon posted their first <strong><a href=\"http://news.bbc.co.uk/1/hi/business/3435965.stm\">yearly profit</a></strong> of $35.3m.</p>\r\n\r\n<p><strong>2005</strong> – Social commerce emerges.  Consumers begin to recommend items to friends via Facebook and Twitter</p>\r\n\r\n<p><strong>2007</strong> – A Pew Internet research study found that 81% of the Americans they surveyed had searched online for a product they intended to buy, with 15% doing so almost every single day. 66% of online users said they had actually bought something online.</p>\r\n\r\n<p><strong>2008</strong> – Increase in growth of online shopping by 17% from the last year in the US, with ecommerce sales figures around $204 billion.  In the same year Groupon is launched as is Magneto – so anyone could have a go at creating their own online store.</p>\r\n\r\n<p><strong>2013</strong> – UK shoppers spent a whopping £91bn online.</p>\r\n\r\n<p><strong>2014</strong> – In 2014, 198 million U.S. consumers bought something online in the first quarter alone,<strong>(</strong><a href=\"http://www.comscore.com/Insights/Events_and_Webinars/Webinar/2014/State_of_the_US_Online_Retail_Economy_in_Q1_2014\"><strong>comScore)</strong> </a>which is 78% of the U.S. <strong><a href=\"http://uk.businessinsider.com/the-surprising-demographics-of-who-shops-online-and-on-mobile-2014-6\">population</a></strong> age 15 and above.</p>\r\n\r\n<p><strong>2015</strong> – Today’s shoppers combine online shopping with real life shopping, using access to WiFi and the showrooming trend. They often make purchases in a retail stores at the same time as using mobile devices to buy something online.  In fact, this Forbes report tells us that <strong><a href=\"http://www.forbes.com/sites/cherylsnappconner/2013/11/12/fifty-essential-mobile-marketing-facts/\">74 per cent</a></strong> of people use their mobile phone to help them while shopping, with 79 per cent making a purchase as a result.</p>\r\n', 1, 1, '2017-01-26 16:31:00', '2017-03-29 05:32:07', '2017-03-29 05:32:07', '', '', ''),
(5, 'Optimizing in-store shopping', 'optimizing-in-store-shopping', '<p>We know that 36% of shoppers who go online in stores are actually visiting the retailer’s own app or mobile site (it’s the single most common in-store use of smartphones). But what do shoppers do when they’re in your store? Qualitative primary research has helped answer those questions, but smartphones can provide another level of insight, through in-store data. Smartphone WiFi signals can be tracked to determine how long and where in the store customers shop. Wal-Mart, for example, has an app that senses when customers enter a store and suggests switching their phone to “store mode.” In this mode, shoppers can interact with special QR signage located throughout stores to access useful product information. Wal-Mart, in turn, is able to track their in-store behavior. If sales of a certain item are slow, tracking can show whether customers are skipping the aisle entirely or looking at the product but not buying it so that store owners can make product placement or other adjustments.</p>\r\n\r\n<p>The effort is clearly paying off: on average, customers who use the Wal-Mart app make two additional visits to the store each month and spend 40% more than their app-free counterparts.</p>\r\n\r\n<p>Using coupons to link Internet behavior with in-store shopping lets retailers figure out which ad slogans or online product promotions work best, how long someone waits between searching and shopping, and even what offers a shopper will respond to or ignore. Mobile couponing, in fact, is a fairly simple way to get into mobile analytics.</p>\r\n', 1, 1, '2017-02-15 16:32:00', '2017-03-29 05:32:38', '2017-03-29 05:32:38', '', '', ''),
(6, 'Mobile shopping', 'mobile-shopping', '<p>Mobile shopping is here to stay whether retailers like it or not. Some 44% of shoppers use their smartphones while they’re shopping; more than a third of them are comparing prices. The impact of mobile research can be profound, affecting the buying behavior of nearly 90% of mobile shoppers, according to our research.</p>\r\n\r\n<p>But retailers shouldn’t despair when shoppers whip out their smartphones among the product displays. Smartphones could be a retailer’s best friend not just because they can open up new buying opportunities. We believe that the smartphones’ greatest benefit for retailers is that they provide a treasure trove of insights into customers’ in-store behavior.</p>\r\n\r\n<p>Stores need to create incentives for shoppers to go mobile — by offering value, convenience, or, ideally, both while protecting privacy — so that shoppers will share their info.</p>\r\n\r\n<p>Despite the well-publicized fears of companies abusing personal information, we’ve found that some 35% of online buyers are willing to share personal information in return for targeted offers, such as promotional coupons. And these people tend to be from the wealthiest group online.</p>\r\n\r\n<p>We see three areas where mobile shopping can provide the richest insights for brick and mortar retailers:</p>\r\n', 1, 1, '2017-02-15 16:32:00', '2017-03-29 05:33:09', '2017-03-29 05:33:09', '', '', ''),
(7, 'Rise in online shopping in India', 'rise-in-online-shopping-in-india', '<p>If the Internet is anything to go by, India\'s technological and economic growth has moved into the top gear.</p>\r\n\r\n<p>With more India\'s online shopping registering a phenomenal 100 per cent annual growth, many retail chains and consumer durable companies are joining the Web bandwagon to tap the eshopping market.</p>\r\n\r\n<p>\"The online shopping industry in India is fast catching on, not just in the larger metros but also in the smaller cities. At present the market is estimated at Rs 46,000 crore and is growing at 100 per cent per year,\" Ajit Chauhan, managing director, Synergy Promotions and Marketing Services, said.</p>\r\n\r\n<p>According to Google, India has more than 100 million Internet users, out of which around half opt for online purchases and the number is growing every year. With such a large market size, companies, right from retail shops to consumer goods, are entering the Web space to attract potential customers.</p>\r\n\r\n<p>Even traditional retailers like Shoppers Stop, Westside and Pantaloons are looking at the online shopping space for growth.</p>\r\n\r\n<p>According to the Associated Chambers of Commerce and Industry of India (Assocham), the size of the online retail industry is expected to touch Rs 7,000 crore by 2015, up from Rs 2,000 crore now, at an annual growth rate of 35 per cent.</p>\r\n\r\n<p>According to industry leaders, portals offering daily deals and discount offers with good delivery services attract the largest number of online shoppers.</p>\r\n\r\n<p>\"The companies that provide daily deals or discount offers are doing brisk business. People are looking for value shopping that saves their money as well as time,\" Chauhan said.</p>\r\n\r\n<p>He added that the company\'s Synergy discount cards offer 15-90 per cent discounts on deals from about 2,000 dining, movie and retail chains in various cities - for movie tickets, dining, hospital bills or for shopping for local brands.</p>\r\n\r\n<p>\"The customer behaviour is changing dramatically. People are not only using the Web to book air tickets and movie tickets but also do not hesitate in placing orders for mobiles, laptops and other consumer electronics and home appliances,\" a senior marketing executive from Flipkart. com , an online shopping portal, told Mail Today.</p>\r\n\r\n<p>\"Seeing this bold consumer behavior, more companies are collaborating with such daily deal and discount sites. All the top consumer electronics and home appliances companies are listed with us. In the growing competition space companies with good delivery services score points over others,\" the executive said.</p>\r\n\r\n<p>Keeping in mind this growing potential, not just large brands but even general retail chains are upgrading their sites for ecommerce, making it more convenient for customers to place online purchase orders.</p>\r\n\r\n<p>According to eBay, Indian online shoppers remain brandsavvy, even when they are shopping online. The eBay India Census has found that brands such as Sony, Nokia, Samsung, Apple and Reebok continue to top buyers\' charts.</p>\r\n\r\n<p>Category-wise lifestyle products, such as cosmetics, jewellery, watches, fashion products and fitness equipment contribute over 45 per cent to eBay\'s sales in India.</p>\r\n', 1, 1, '2017-01-12 16:33:00', '2017-03-29 05:33:39', '2017-03-29 05:33:39', '', '', ''),
(8, 'Right tools can make online shopping easier', 'right-tools-can-make-online-shopping-easier', '<p>Online shopping can be a smart consumer\'s best friend, with the ability to easily comparison shop, search for discounts and make purchases with a few mouse clicks.</p>\r\n\r\n<p>You could always visit shopping comparison sites, such as MySimon.com, or search for coupon codes at one of many code-aggregators. But now a slew of Web browser add-ons makes smart online shopping easier. Here\'s a sampling:</p>\r\n\r\n<p><img alt=\"\" src=\"http://articles.chicagotribune.com/images/pixel.gif\" style=\"height:1px;width:1px;\" /></p>\r\n\r\n<p><img alt=\"\" src=\"http://articles.chicagotribune.com/images/pixel.gif\" style=\"height:1px;width:1px;\" /></p>\r\n\r\n<p>Billeo toolbar: With perhaps the most functionality, billeo.com helps with shopping, discount codes and bill paying. It will autofill your logins and passwords to retailer sites, as well as forms for your shipping and billing addresses and credit card information. On the checkout page, it will alert you if a discount code is available for your purchase and autofill the code.</p>\r\n\r\n<p>Billeo will save transaction confirmation pages to provide a shopping history. And it aids with paying via vendor sites.</p>\r\n\r\n<p>Personal information is encrypted and stored on your computer rather than at Billeo, said company founder Murali Subbarao.</p>\r\n\r\n<p>Billeo is available for Internet Explorer and Firefox on Windows and Firefox for Mac. Offer Assistant, which applies discounts and coupon codes, is expected soon for the Mac, Subbarao said.</p>\r\n\r\n<p>Dealio: One important function Billeo doesn\'t have is comparing prices among retailers. Dealio.com/toolbar will do that and search eBay. Comparisons include tax and shipping for a true apples-to-apples approach. The toolbar works with IE and Firefox on Windows only.</p>\r\n\r\n<p>InvisibleHand plug-in: If you don\'t want a bunch of toolbars stealing your browser real estate, getinvisiblehand.com is for you. It shows itself only when you\'re on a product description page. It will alert you when another retailer has a lower price on the item. Firefox only.</p>\r\n\r\n<p>Retailmenot and CouponCabin: Retailmenot.ourtoolbar.com and couponcabin.com/toolbar are among the better sites to search for coupon codes, which can be entered at online checkout to give you goodies, including discounts or free shipping. Both are available for IE and Firefox. Retailmenot also has a version for Apple\'s Safari browser.</p>\r\n\r\n<p>PriceProtectr toolbar: What if the price for an item drops soon after the purchase? Many retailers offer price-protection policies that refund the difference within a certain time frame, such as 30 days after purchase. Priceprotectr.com/toolbar.jsp makes it easy to log your purchases at its Web site, which alerts you by e-mail if the price drops within the retailer\'s price-protection period. Available for IE and Firefox.</p>\r\n', 1, 1, '2017-02-23 16:33:00', '2017-03-29 05:34:14', '2017-03-29 05:34:14', '', '', ''),
(9, 'Shopping Online: Convenience, Bargains And A Few Scams ', 'shopping-online-convenience-bargains-and-a-few-scams', '<p>Online buying and selling has become an important part of many people\'s lives. Students and parents rely on the internet to acquire and sell textbooks at affordable prices, virtual stores allow people to shop from the comfort of their homes without the pressure of a salesperson, and online marketplaces provide a new and more convenient venue for the exchange of virtually all types of goods and services.</p>\r\n\r\n<p>Both businesses and customers have embraced online sales as a cheaper and more convenient way to shop, but just like anything associated with the internet, there are benefits and dangers associated with shopping online. Read on to learn how to protect yourself while you use this handy resource.</p>\r\n\r\n<p><strong>Mechanics: How Does Online Buying Work?</strong><br />\r\nShopping online is just like heading out to the store. You can buy all the same things from your home computer and can sometimes even get access to better sales.</p>\r\n\r\n<p><em>Finding a Product</em><br />\r\nWhen you shop online, you have to start by searching for a product. This can be done by visiting a store\'s website or, if you are not aware of any store that has the particular item you are looking for or you\'d like to compare prices between stores, you can always search for the items with a search engine and compare the results.</p>\r\n\r\n<p>On major retail websites, companies and merchants will have pictures, descriptions and prices of the goods that they have for sale. If a smaller company does not have the means to create a website, some sites like Amazon and Yahoo! make it possible for <a href=\"http://www.investopedia.com/university/small-business/\">small businesses</a> and individuals to display products or build their own online stores for a monthly and transaction fee.</p>\r\n\r\n<p>Other websites like eBay and Bidz provide an auction format, in which sellers can display items for a minimum price and buyers can bid on these items until the listing ends or the <a href=\"http://www.investopedia.com/terms/s/seller.asp\">seller</a> chooses to award it to a buyer. Most stores also have placed virtual <a href=\"http://www.investopedia.com/terms/c/customer-service.asp\">customer service</a> centers on their websites, so you can either call, email or chat with a live customer service representative if you have questions.</p>\r\n', 1, 1, '2017-01-16 16:34:00', '2017-03-29 05:34:43', '2017-03-29 05:34:43', '', '', ''),
(10, 'Buying Online vs In a store', 'buying-online-vs-in-a-store', '<h3>Who Can Buy - How Soon Do You Need It</h3>\r\n\r\n<p><strong>Online:</strong> Most likely, you\'ll need a credit card, PayPal account, checking account, or some sort of online financing option.</p>\r\n\r\n<p>You\'ll have to wait for it to arrive through the mail.</p>\r\n\r\n<p><strong>Retail:</strong> The easiest place to purchase products if you have the money. All you need is a way to get there, and means to take your item home.</p>\r\n\r\n<p><strong>ADVANTAGE:</strong> Retail</p>\r\n\r\n<h3>Product Selection and Availability</h3>\r\n\r\n<p><strong>Online:</strong> Every make and model is at your fingertips without having to drive miles to find it. Don\'t see your television at this store, surf to another. The only drawback is you can\'t test the product beforehand unless you find it at a retail store near you, but you can read product reviews, user opinions, and manufacturer specs with minimal exertion. Most reputable sites make aware the availability of each item.</p>\r\n\r\n<p><strong>Retail:</strong> Limited to only products it sells at the local level, but you can test your new television in-person before buying it. Depending on store size, selection may be limited, but availability is almost always certain.</p>\r\n\r\n<p><strong>ADVANTAGE:</strong> Online</p>\r\n\r\n<h3>Base Price</h3>\r\n\r\n<p><strong>Online:</strong> Generally, online sellers features lower prices because they don\'t have the overhead of renting a pad in a strip mall, high electric bills, and a staff of sales people.</p>\r\n\r\n<p>There\'s even Web sites that offer \'percent off\' discounts if you enter a code after meeting a minimum purchase requirement, which could save even more money. Though, pay attention whether or not your television is brand new or refurbished.</p>\r\n\r\n<p><strong>Retail:</strong> In order to compete with online purchasing, retail outlets are slashing prices all around.</p>\r\n\r\n<p>Paired with a coupon or special \'in store\' discount, prices might get as low as some online sellers. In addition, many retail outlets resell returned items for even lower prices.</p>\r\n\r\n<p><strong>ADVANTAGE:</strong> Online</p>\r\n\r\n<h3>Taxes, Shipping, and Delivery</h3>\r\n\r\n<p><strong>Online:</strong> Depending on where you live and what store you buy through, you might not have to pay a sales tax. Shipping is a different story. Some stores don\'t charge shipping or you can get coupons for free shipping while others do charge, which might drive the final cost of the television up several hundred dollars.</p>\r\n\r\n<p><strong>Retail:</strong> You will pay your local sales tax at a retail outlet, and there won\'t be any shipping charges. However, most stores will charge a fee to deliver your new television (if you choose) or offer free delivery. If they charge for delivery, try to get the fee waived.</p>\r\n\r\n<p><strong>ADVANTAGE:</strong> Tie</p>\r\n\r\n<h3>Customer Service and Warranty - Returns, Exchanges, Repairs</h3>\r\n\r\n<p><strong>Online:</strong> This is a sticky point with online purchasing. While most excel in servicing the customer, there\'s also a negative stigma associated with online sellers. Please read user opinions before buying and make a gut-call. At times, consumers are charged restocking fees, have to pay for shipping if sending the item to be fixed through warranty, or buy the item with a \'no return\' clause in the sale.</p>\r\n\r\n<p>Though, with some warranties, the consumer will get a replacement model temporarily or permanently depending on the issue. Customer service is sometimes hard to contact, and there\'s usually no storefront to voice a grievance in person.</p>\r\n\r\n<p><strong>Retail:</strong> With a receipt, modern retail outlets are easy to deal with when returning, exchanging, and <a href=\"https://www.lifewire.com/extended-tv-warranty-3276561\">using the warranty</a>. Customer service is usually driven to retain your business by any means necessary, even if it means taking one on the chin every now and then. To be on the safe side, read their return/exchange policy before buying.</p>\r\n\r\n<p><strong>ADVANTAGE:</strong> Retail</p>\r\n\r\n<h3>Security</h3>\r\n\r\n<p><strong>Online:</strong> While some people think buying stuff online means your credit information is there for the grabbing, that just isn\'t true anymore.</p>\r\n\r\n<p>Most online retailers use some sort of 128-bit encryption, and are as secure as banking sites. Sure, there\'s a risk, but no more than buying in a store. Read user opinions, check their security license, and you\'ll be fine.</p>\r\n\r\n<p><strong>Retail:</strong> What\'s written for online security goes for retail security. For the most part, your information will remain private, but there\'s always the rare case of identity theft at some level.</p>\r\n\r\n<p><strong>ADVANTAGE:</strong> Tie</p>\r\n\r\n<h3>Where to Buy</h3>\r\n\r\n<p>Buy online if you are looking for the best possible possible deal. Even with shipping charges, most online prices are lower. While retail can\'t compete across the board with prices, it has the advantage in customer service. If meeting the sales person, feeling a sense of community, and security knowing you can walk into the store at anytime are important - buy at a retail outlet.</p>\r\n\r\n<p>Where to purchase is as important as what to purchase. Before buying, be sure to read the fine print, do a little research on the company you plan on buying from, and everything should be all right.</p>\r\n', 1, 1, '2017-03-28 16:34:00', '2017-03-29 05:35:22', '2017-03-29 05:35:22', '', '', ''),
(11, 'How to buy electronic items online', 'how-to-buy-electronic-items-online', '<p>There are many online shopping stores that let you buy electronics online but who you should really buy electronics from? that\'s the question. Here you have a few choices. You can either go to famous online stores like Amazon to buy stuff or you can go to different affiliate websites that have detailed reviews on electronic products. Below are the steps involved that you need to follow while buy electronics online.</p>\r\n\r\n<p><strong>1</strong>. <strong>Identify different websites that offer your desired product.</strong> You can do this by entering the full name of desired product on your favorite search engine and check the lists of the websites that appear.</p>\r\n\r\n<p><strong>2. You can clearly differentiate between online retail stores and review sites by simply looking at the domain name of the websites or you could also open each link individually and check.</strong></p>\r\n\r\n<p><strong>3. Compare the prices for that product on all websites and identify the one that is offering the lowest price.</strong></p>\r\n\r\n<p><strong>4. Identify which website has more information on that particular product.</strong> Information could be in the form of overview, features, specifications and demos.</p>\r\n\r\n<p><strong>5. Read all the details about that product on the website that was identified in the previous step.</strong></p>\r\n\r\n<p><strong>6. If it is an affiliate website, check which company it is affiliated with.</strong> If it is affiliated with a famous online retail store and does not ask you for your personal or credit card information, that means you can trust that affiliate website.</p>\r\n\r\n<p><strong>7. Purchase the product from the website that you think is the most credible one.</strong></p>\r\n\r\n<p> </p>\r\n', 1, 1, '2017-01-24 16:35:00', '2017-03-29 05:36:08', '2017-03-29 05:36:08', '', '', ''),
(12, '10 benefits of shopping online', '10-benefits-of-shopping-online', '<p>Given below is my list of 10 reasons why it is better than conventional shopping.</p>\r\n\r\n<ol><li><strong>Convenience.</strong> The convenience is the biggest perk. Where else can you comfortably shop at midnight while in your pajamas? There are no lines to wait in or shop assistants to wait on to help you with your purchases, and you can do your shopping in minutes. Online shops give us the opportunity to shop 24/7, and also reward us with a ‘no pollution’ shopping experience. There is no better place to buy informational products like e-books, which are available to you instantly, as soon as the payment goes through. Downloadable items purchased online eliminate the need for any kind of material goods at all, as well, which helps the environment!</li>\r\n <li><strong>Better prices.</strong> Cheap deals and better prices are available online, because products come to you direct from the manufacturer or seller without middleman being involved. Many online shops offer discount coupons and rebates as well<strong>.</strong> Apart from this, online shops are only required to collect a sales tax if they have a physical location in our state, even if we buy from a store across the world.</li>\r\n <li><strong>More variety:</strong> The choices online are amazing. One can get several brands and products from different sellers all in one place. You can get in on the latest international trends without spending money on airfare. You can shop from retailers in other parts of the country, or even the world, all without being limited by geography. A far greater selection of colors and sizes than you will find locally are at your disposal. Apart from that, the stock is much more plentiful. Some online shops even provisions in place to accept orders for items out of stock and ship it when the stock becomes available. You also have the option of taking your business to another online store where the product is available.</li>\r\n <li><strong>You can send gifts more easily.</strong> Sending gifts to relatives and friends is easy, no matter where they are. Now, there is no need to make distance an excuse for not sending a gift on occasions like birthdays, weddings, anniversaries, Valentine\'s Day, Mother\'s Day, Father\'s Day, and so forth.</li>\r\n <li><strong>Fewer expenses.</strong> Many times, when we opt for conventional shopping, we tend to spend a lot more than planned. There are other outside expenses on things like eating out, transportation, and let\'s not forget impulse buys!</li>\r\n  <li><strong>Price comparisons.</strong> Comparing and researching products and their prices is so much easier online. Also, we have the ability to share information and reviews with other shoppers who have firsthand experience with a product or retailer.</li>\r\n <li><strong>No crowds.</strong> If you are like me, you hate crowds when you\'re shopping. Especially during festivals or special events, they can be such a huge headache. Also, it tends to be more chaotic when there are more crowds out and this sometimes makes us feel rush or hurried. Grumpy, annoying, and smelly people also annoy me when I\'m out shopping. Plus, parking becomes a huge issue. All of these problems can be avoided when you shop online.</li>\r\n  <li><strong>Less compulsive shopping.</strong> Often times when we\'re out shopping, we end up buying things compulsively that we don\'t really need. All because shop keepers pressure us or use their selling skills to compel us to make these purchases. Sometimes, we even compromise on our choices because of the lack of choices in those shops.</li>\r\n <li><strong>Buying old or unused items at lower prices.</strong> The marketplace on the Internet makes it much easier for us to buy old or unused things at rock bottom prices. Also, if we want to buy antiques, there\'s no better place to find great ones.</li>\r\n <li><strong>Discreet purchases are easier.</strong> Some things are better done in the privacy of your home. Online shops are best for discreet purchases for things like adult toys, sexy lingerie, and so on. This enables me to purchase undergarments and lingerie without embarrassment or any paranoia that there are several people watching me.</li>\r\n</ol>', 1, 1, '2017-01-19 16:36:00', '2017-03-29 05:36:42', '2017-03-29 05:36:42', '', '', ''),
(13, 'Disadvantages of Online Trading', 'disadvantages-of-online-trading', '<p><br />\r\nThere are also disadvantages to buying and selling online. These include:</p>\r\n\r\n<ul><li><strong>Increased Risk of Identity Theft</strong>: When paying for your goods online, it can be very easy for someone to intercept sensitive information such as a credit card number, address, phone number or <a href=\"http://www.investopedia.com/terms/a/account-number.asp\">account number</a>.</li>\r\n <li><strong>Vendor Fraud</strong>: If the vendor/seller is fraudulent, he or she might accept your payment and either refuse to send you your item, or send you the wrong or a defective product. Trying to rectify an incorrect order with a vendor through the internet can be a hassle.</li>\r\n</ul><p><strong>Protecting Yourself While Shopping Online</strong><br />\r\nOverall, the advantages to shopping online outweigh the disadvantages. That said, it is important to note that while they might be smaller in number, the disadvantages can be a major hardship.</p>\r\n\r\n<p>While shopping online, it is very important to protect yourself and your information. Here are some tips that can help you take care of yourself:</p>\r\n\r\n<ul><li><strong>Invest in Technology: </strong>It is a great idea to install antivirus and anti-phishing programs on your computer. An antivirus program will protect your computer from viruses and an anti-phishing program will attempt to protect you from websites that are designed look like legitimate sites, but actually collect your personal information for illegal activities.</li>\r\n  <li><strong>Be Careful</strong>: Vendors do not have the right to ask for certain information. If a website requests your <a href=\"http://www.investopedia.com/terms/s/ssn.asp\">Social Security number</a>, it is probably a scam. You will need to research the company requesting the information or exit that site as quickly as possible.</li>\r\n  <li><strong>Research: </strong>If you are searching for an item using search engines and you encounter a store or a website you have not heard about, make sure you check the bottom of the pages for an SSL logo. SSL is a standard security technology for establishing an encrypted link between a web server and a browser. To be able to create an SSL connection a web server requires an SSL certificate.</li>\r\n <li><strong>Shipping Check: </strong>Always read shipping policies posted on the seller\'s website or beneath the product listing. Some sellers allow you to return an item within a specific period of time, while other vendors never accept returns.</li>\r\n</ul>', 1, 1, '2017-02-26 16:36:00', '2017-03-29 05:37:15', '2017-03-29 05:37:15', '', '', ''),
(14, 'Acceptance of mobile shopping, benefits and impacts', 'acceptance-of-mobile-shopping-benefits-and-impacts', '<p>Despite their generally increasing use, the adoption of mobile shopping applications often differs across purchase contexts. In order to advance our understanding of smartphone-based mobile shopping acceptance, this study integrates and extends existing approaches from technology acceptance literature by examining two previously underexplored aspects. First, the study examines the impact of different mobile and personal benefits (instant connectivity, contextual value, and hedonic motivation), customer characteristics (habit), and risk facets (financial, performance, and security risk) as antecedents of mobile shopping acceptance. Second, it is assumed that several acceptance drivers differ in relevance subject to the perception of three mobile shopping characteristics (location sensitivity, time criticality, and extent of control), while other drivers are assumed to matter independent of the context. Based on a dataset of 410 smartphone shoppers, empirical results demonstrate that several acceptance predictors are associated with ease of use and usefulness, which in turn affect intentional and behavioral outcomes. Furthermore, the extent to which risks and benefits impact ease of use and usefulness is influenced by the three contextual characteristics. From a managerial perspective, results show which factors to consider in the development of mobile shopping applications and in which different application contexts they matter.</p>\r\n', 1, 1, '2017-02-26 16:37:00', '2017-03-29 05:37:46', '2017-03-29 05:37:46', '', '', ''),
(15, '5 tips to save money while shopping online', '5-tips-to-save-money-while-shopping-online', '<p>Online shopping or ecommerce has seen exponential growth in India. <a href=\"http://www.techulator.com/articles/Online-Shopping.aspx\">Online shopping</a> is extremely convenient, easy, hassle free and offers far more advantages than traditional methods of shopping. Many people directly visit the ecommerce sites and purchase the products, however a bit of research and smartness can help you to save money on your purchases at these sites. Below are 5 simple tips which you can use to save money on your purchases online.<br />\r\n </p>\r\n\r\n<h3>Use coupon codes</h3>\r\n\r\n<p><br />\r\nAlmost all online shopping sites publish coupon codes which enable you to get discount on your purchases at respective sites. You just need to search for the coupon codes online and find the best coupon code suiting your needs and offering maximum possible discount. Once you have the coupon code, use/apply it during your order or payment to get the specified discount. There are a lot of sites in India which list down coupon codes of <a href=\"http://www.techulator.com/articles/Online-Shopping-Sites.aspx\">online shopping sites</a> in India. You can just search for the term \'online shopping site name + coupons\' in the search engine to see the coupon codes.<br />\r\n </p>\r\n\r\n<h3>Shop during festive season</h3>\r\n\r\n<p><br />\r\nEcommerce sites launch various promotional offers and deals during festival season like Diwali, Christmas and Raksha Bandhan etc. If you are planning to purchase high ticket items like cameras, laptops etc then you can wait till the festive season and do your purchases then. This will help you to get good discounts and in turn save your money.<br />\r\n </p>\r\n\r\n<h3>Use cashback sites</h3>\r\n\r\n<p><br />\r\nCashback sites are sites which offer you a certain amount or percentage of your purchase amount at ecommerce sites as cashback. These sites work on the concept of <a href=\"http://www.techulator.com/articles/Affiliate-Marketing.aspx\">affiliate marketing</a> where <a href=\"http://www.techulator.com/articles/eCommerce-Websites.aspx\">ecommerce sites</a> pay the <a href=\"http://www.techulator.com/articles/Cashback-Websites.aspx\">cashback sites</a> a certain commission for every sale that is done through them. The cashback sites then pass some of this amount as cashback back to the user.<br />\r\n </p>\r\n\r\n<h3>Use price comparison sites</h3>\r\n\r\n<p><br />\r\nPrice comparison sites, as the name suggests are sites which enable you to search for the product of your choice and the search results will list down the prices for the same product at different ecommerce sites. Some price comparison sites even fetch and display details like shipping charge, delivery time etc for the product that you have searched. This will help you to see the ecommerce site which sells the product for the cheapest price and hence save your money.<br />\r\n </p>\r\n\r\n<h3>Browse daily deal sites and forums</h3>\r\n\r\n<p><br />\r\nDaily deal sites aggregating sites are sites which list out popular and value for money deals for several products across different online shopping sites. You can browse these sites and forums before your online purchases to find the best deal or offer offering highest discount.<br /><br />\r\nThese are some tried and tested ways which can help you to <a href=\"http://www.techulator.com/articles/Save-Money.aspx\">save money</a> on your purchases at online shopping sites.If you have any ideas or comments regarding this post, please share it through response box below at the end of the post.</p>\r\n', 1, 1, '2017-02-08 16:37:00', '2017-03-29 05:38:16', '2017-03-29 05:38:16', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `kb_article_relationship`
--

DROP TABLE IF EXISTS `kb_article_relationship`;
CREATE TABLE IF NOT EXISTS `kb_article_relationship` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_relationship_article_id_foreign` (`article_id`),
  KEY `article_relationship_category_id_foreign` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `kb_article_relationship`
--

INSERT INTO `kb_article_relationship` (`id`, `article_id`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, NULL),
(2, 2, 3, NULL, NULL),
(3, 3, 1, NULL, NULL),
(4, 4, 3, NULL, NULL),
(5, 5, 4, NULL, NULL),
(6, 6, 4, NULL, NULL),
(7, 7, 3, NULL, NULL),
(8, 8, 2, NULL, NULL),
(9, 9, 1, NULL, NULL),
(10, 10, 1, NULL, NULL),
(11, 11, 1, NULL, NULL),
(12, 12, 3, NULL, NULL),
(13, 13, 2, NULL, NULL),
(14, 14, 4, NULL, NULL),
(15, 15, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kb_article_template`
--

DROP TABLE IF EXISTS `kb_article_template`;
CREATE TABLE IF NOT EXISTS `kb_article_template` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kb_category`
--

DROP TABLE IF EXISTS `kb_category`;
CREATE TABLE IF NOT EXISTS `kb_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `parent` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `display_order` int(11) NOT NULL,
  `visible_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all_users',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `kb_category`
--

INSERT INTO `kb_category` (`id`, `name`, `slug`, `description`, `status`, `parent`, `created_at`, `updated_at`, `display_order`, `visible_to`) VALUES
(1, 'E store', 'e-store', '<p><b>E-store </b>is a transaction of buying or selling online. Electronic commerce draws on technologies such as <a href=\"https://en.wikipedia.org/wiki/Mobile_commerce\">mobile commerce</a>, <a href=\"https://en.wikipedia.org/wiki/Electronic_funds_transfer\">electronic funds transfer</a>, <a href=\"https://en.wikipedia.org/wiki/Supply_chain_management\">supply chain management</a>, <a href=\"https://en.wikipedia.org/wiki/Online_advertising\">Internet marketing</a>, <a href=\"https://en.wikipedia.org/wiki/Online_transaction_processing\">online transaction processing</a>, <a href=\"https://en.wikipedia.org/wiki/Electronic_data_interchange\">electronic data interchange</a> (EDI), <a href=\"https://en.wikipedia.org/wiki/Inventory_management_software\">inventory management systems</a>, and automated <a href=\"https://en.wikipedia.org/wiki/Data_collection\">data collection</a> systems. Modern electronic commerce typically uses the <a href=\"https://en.wikipedia.org/wiki/World_Wide_Web\">World Wide Web</a> for at least one part of the transaction\'s life cycle although it may also use other technologies such as e-mail.</p><p>E-commerce businesses may employ some or all of the following:</p><ul><li><a href=\"https://en.wikipedia.org/wiki/Online_shopping\">Online shopping</a> web sites for <a href=\"https://en.wikipedia.org/wiki/Direct_selling\">retail sales direct</a> to consumers</li><li>Providing or participating in <a href=\"https://en.wikipedia.org/wiki/Online_marketplace\">online marketplaces</a>, which process third-party business-to-consumer or consumer-to-consumer sales</li><li><a href=\"https://en.wikipedia.org/wiki/Business-to-business\">Business-to-business</a> buying and selling</li><li>Gathering and using demographic data through web contacts and social media</li><li>Business-to-business (B2B) <a href=\"https://en.wikipedia.org/wiki/Electronic_data_interchange\">electronic data interchange</a></li><li>Marketing to prospective and established customers by e-mail or fax (for example, with <a href=\"https://en.wikipedia.org/wiki/Newsletter\">newsletters</a>)</li><li>Engaging in <a href=\"https://en.wikipedia.org/wiki/Pretail\">pretail</a> for launching new products and services</li><li>Online financial exchanges for currency exchanges or trading purposes</li></ul><br /><br />', 1, 0, '2017-03-29 05:28:00', '2017-03-29 05:28:00', 1, 'all_users'),
(2, 'Footwear shopping ', 'footwear-shopping', '<p>A <b>shoe</b> is an item of <a href=\"https://en.wikipedia.org/wiki/Footwear\">footwear</a> intended to protect and comfort the human foot while the wearer is doing various activities. Shoes are also used as an item of decoration and <a href=\"https://en.wikipedia.org/wiki/Fashion\">fashion</a>. The design of shoes has varied enormously through time and from culture to culture, with appearance originally being tied to function. Additionally, fashion has often dictated many design elements, such as whether shoes have very high heels or flat ones. Contemporary footwear in the 2010s varies widely in style, complexity and cost. Basic sandals may consist of only a thin sole and simple strap and be sold for a low cost. High fashion shoes made by famous designers may be made of expensive materials, use complex construction and sell for hundreds or even thousands of dollars a pair. Some shoes are designed for specific purposes, such as <a href=\"https://en.wikipedia.org/wiki/Boot\">boots</a> designed specifically for <a href=\"https://en.wikipedia.org/wiki/Mountaineering\">mountaineering</a> or <a href=\"https://en.wikipedia.org/wiki/Skiing\">skiing</a>.</p><p>Traditionally, shoes have been made from leather, wood or <a href=\"https://en.wikipedia.org/wiki/Canvas\">canvas</a>, but in the 2010s, they are increasingly made from <a href=\"https://en.wikipedia.org/wiki/Synthetic_rubber\">rubber</a>, <a href=\"https://en.wikipedia.org/wiki/Plastic\">plastics</a>, and other <a href=\"https://en.wikipedia.org/wiki/Petrochemical\">petrochemical</a>-derived materials. Though the human foot is adapted to varied terrain and climate conditions, it is still vulnerable to environmental hazards such as sharp rocks and hot ground, which shoes protect against. Some shoes are worn as safety equipment, such as steel-soled boots which are required on construction sites.</p><br /><br />', 1, 0, '2017-03-29 05:28:26', '2017-03-29 05:28:26', 2, 'all_users'),
(3, 'Online Shopping', 'online-shopping', '<p><b>Online shopping</b> is a form of <a href=\"https://en.wikipedia.org/wiki/Electronic_commerce\">electronic commerce</a> which allows consumers to directly buy <a href=\"https://en.wikipedia.org/wiki/Good_%28economics%29\">goods</a> or <a href=\"https://en.wikipedia.org/wiki/Service_%28economics%29\">services</a> from a seller over the <a href=\"https://en.wikipedia.org/wiki/Internet\">Internet</a> using a <a href=\"https://en.wikipedia.org/wiki/Web_browser\">web browser</a>. Consumers find a product of interest by visiting the <a href=\"https://en.wikipedia.org/wiki/Website\">website</a> of the retailer directly or by searching among alternative vendors using a <a href=\"https://en.wikipedia.org/wiki/Shopping_search_engine\">shopping search engine</a>, which displays the same product\'s availability and pricing at different e-retailers. As of 2016, customers can shop online using a range of different computers and devices, including <a href=\"https://en.wikipedia.org/wiki/Desktop_computer\">desktop computers</a>, <a href=\"https://en.wikipedia.org/wiki/Laptop\">laptops</a>, <a href=\"https://en.wikipedia.org/wiki/Tablet_computer\">tablet computers</a> and <a href=\"https://en.wikipedia.org/wiki/Smartphone\">smartphones</a>.</p><p>An online shop evokes the physical analogy of buying <a href=\"https://en.wikipedia.org/wiki/Product_%28business%29\">products</a> or services at a regular <a href=\"https://en.wikipedia.org/wiki/Brick_and_mortar_business\">\"bricks-and-mortar\"</a> <a href=\"https://en.wikipedia.org/wiki/Retailing\">retailer</a> or <a href=\"https://en.wikipedia.org/wiki/Shopping_center\">shopping center</a>; the process is called business-to-consumer (B2C) online shopping. When an online store is set up to enable businesses to buy from another businesses, the process is called business-to-business (B2B) online shopping. A typical online store enables the customer to browse the firm\'s range of products and services, view photos or images of the products, along with information about the product specifications, features and prices.</p><br /><br />', 1, 0, '2017-03-29 05:28:48', '2017-03-29 05:28:48', 3, 'all_users'),
(4, 'Online mobile shopping', 'online-mobile-shopping', 'The phrase <b>mobile commerce</b> was originally coined in 1997 by Kevin Duffey at the launch of the Global Mobile Commerce Forum, to mean \"the delivery of <a href=\"https://en.wikipedia.org/wiki/Electronic_commerce\">electronic commerce</a> capabilities directly into the consumer’s hand, anywhere, via <a href=\"https://en.wikipedia.org/wiki/Wireless\">wireless</a> technology. Many choose to think of Mobile Commerce as meaning \"a retail outlet in your customer’s pocket.\"<p><a href=\"https://en.wikipedia.org/wiki/Mobile_device\">Mobile</a> commerce is worth <a href=\"https://en.wikipedia.org/wiki/Orders_of_magnitude_%28currency%29\">US$230 billion</a>, with Asia representing almost half of the market, and has been forecast to reach US$700 billion in 2017. According to BI Intelligence in January 2013, 29% of mobile users have now made a purchase with their phones. Walmart estimated that 40% of all visits to their internet shopping site in December 2012 was from a <a href=\"https://en.wikipedia.org/wiki/Mobile_device\">mobile device</a>. <a href=\"https://en.wikipedia.org/wiki/Bank_of_America\">Bank of America</a> predicts $67.1 billion in purchases will be made from mobile devices by European and U.S. shoppers in 2015. m-Commerce made up 11.6 per cent of total e-commerce spending in 2014, and is forecast to increase to 45 per cent by 2020, according to <a href=\"http://www.banklesstimes.com/2017/02/14/mobile-commerce-is-the-trend-for-2017/\">BI Intelligence</a>. ComScore <a href=\"http://www.retaildive.com/news/comscore-holiday-spending-topped-80b-driven-by-mobile-commerce-growth/436106/\">reported</a> in February 2017 that mobile commerce had grown 45% in year to December 2016.</p><br /><br />', 1, 0, '2017-03-29 05:29:09', '2017-03-29 05:29:09', 4, 'all_users');

-- --------------------------------------------------------

--
-- Table structure for table `kb_comment`
--

DROP TABLE IF EXISTS `kb_comment`;
CREATE TABLE IF NOT EXISTS `kb_comment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_article_id_foreign` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kb_pages`
--

DROP TABLE IF EXISTS `kb_pages`;
CREATE TABLE IF NOT EXISTS `kb_pages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `seo_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kb_settings`
--

DROP TABLE IF EXISTS `kb_settings`;
CREATE TABLE IF NOT EXISTS `kb_settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pagination` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `kb_settings`
--

INSERT INTO `kb_settings` (`id`, `pagination`, `created_at`, `updated_at`, `status`) VALUES
(1, 10, '2017-03-29 05:23:25', '2017-03-29 05:23:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

DROP TABLE IF EXISTS `labels`;
CREATE TABLE IF NOT EXISTS `labels` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `visible_to` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `title`, `color`, `status`, `order`, `created_at`, `updated_at`, `visible_to`) VALUES
(1, 'Bug', '#e91d1d', '1', 1, '2017-03-29 22:41:17', '2017-03-29 22:41:17', NULL),
(2, 'Request', '#000000', '1', 2, '2017-03-29 22:41:59', '2017-03-29 23:22:09', NULL),
(3, 'Query', '#50dc34', '1', 3, '2017-03-29 22:46:27', '2017-03-29 22:46:27', NULL),
(4, 'Review', '#273cc8', '1', 5, '2017-03-29 22:46:49', '2017-03-29 22:46:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `locale`) VALUES
(1, 'English', 'en'),
(2, 'Italian', 'it'),
(3, 'German', 'de'),
(4, 'French', 'fr'),
(5, 'Brazilian Portuguese', 'pt_BR'),
(6, 'Dutch', 'nl'),
(7, 'Spanish', 'es'),
(8, 'Norwegian', 'nb_NO'),
(9, 'Danish', 'da');

-- --------------------------------------------------------

--
-- Table structure for table `ldap`
--

DROP TABLE IF EXISTS `ldap`;
CREATE TABLE IF NOT EXISTS `ldap` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `search_base` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agent_auth` tinyint(1) NOT NULL,
  `client_auth` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ldap`
--

INSERT INTO `ldap` (`id`, `domain`, `username`, `password`, `search_base`, `agent_auth`, `client_auth`, `created_at`, `updated_at`) VALUES
(1, '', '', '', '', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

DROP TABLE IF EXISTS `licenses`;
CREATE TABLE IF NOT EXISTS `licenses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `licenses_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `org_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `licenses_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activated_on` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_on` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comments` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_default` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `title`, `email`, `phone`, `address`, `status`, `created_at`, `updated_at`, `is_default`) VALUES
(1, 'Bangalore', '', '', '', 0, '2017-10-02 23:01:08', '2017-10-02 23:01:08', 0),
(2, 'Delhi', '', '', '', 0, '2017-10-02 23:01:08', '2017-10-02 23:01:08', 0),
(3, 'Kolkata', '', '', '', 0, '2017-10-02 23:01:08', '2017-10-02 23:01:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `User` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `IP` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Attempts` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `LastLogin` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `User`, `IP`, `Attempts`, `LastLogin`, `created_at`, `updated_at`) VALUES
(1, 'demo_agent', '149.126.78.41', '1', '2017-04-02 17:24:05', '2017-03-29 05:23:27', '2017-03-29 05:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `mailbox_protocol`
--

DROP TABLE IF EXISTS `mailbox_protocol`;
CREATE TABLE IF NOT EXISTS `mailbox_protocol` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mailbox_protocol`
--

INSERT INTO `mailbox_protocol` (`id`, `name`, `value`) VALUES
(1, 'IMAP', '/imap'),
(2, 'IMAP+SSL', '/imap/ssl'),
(3, 'IMAP+TLS', '/imap/tls'),
(4, 'IMAP+SSL/No-validate', '/imap/ssl/novalidate-cert');

-- --------------------------------------------------------

--
-- Table structure for table `mail_services`
--

DROP TABLE IF EXISTS `mail_services`;
CREATE TABLE IF NOT EXISTS `mail_services` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mail_services`
--

INSERT INTO `mail_services` (`id`, `name`, `short_name`, `created_at`, `updated_at`) VALUES
(1, 'SMTP', 'smtp', '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(2, 'Php Mail', 'mail', '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(3, 'Send Mail', 'sendmail', '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(4, 'Mailgun', 'mailgun', '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(5, 'Mandrill', 'mandrill', '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(6, 'Log file', 'log', '2017-03-29 05:23:18', '2017-03-29 05:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2016_02_16_140450_create_banlist_table', 1),
('2016_02_16_140450_create_canned_response_table', 1),
('2016_02_16_140450_create_custom_form_fields_table', 1),
('2016_02_16_140450_create_custom_forms_table', 1),
('2016_02_16_140450_create_date_format_table', 1),
('2016_02_16_140450_create_date_time_format_table', 1),
('2016_02_16_140450_create_department_table', 1),
('2016_02_16_140450_create_emails_table', 1),
('2016_02_16_140450_create_group_assign_department_table', 1),
('2016_02_16_140450_create_groups_table', 1),
('2016_02_16_140450_create_help_topic_table', 1),
('2016_02_16_140450_create_kb_article_relationship_table', 1),
('2016_02_16_140450_create_kb_article_table', 1),
('2016_02_16_140450_create_kb_category_table', 1),
('2016_02_16_140450_create_kb_comment_table', 1),
('2016_02_16_140450_create_kb_pages_table', 1),
('2016_02_16_140450_create_kb_settings_table', 1),
('2016_02_16_140450_create_languages_table', 1),
('2016_02_16_140450_create_log_notification_table', 1),
('2016_02_16_140450_create_mailbox_protocol_table', 1),
('2016_02_16_140450_create_organization_table', 1),
('2016_02_16_140450_create_password_resets_table', 1),
('2016_02_16_140450_create_plugins_table', 1),
('2016_02_16_140450_create_settings_alert_notice_table', 1),
('2016_02_16_140450_create_settings_auto_response_table', 1),
('2016_02_16_140450_create_settings_company_table', 1),
('2016_02_16_140450_create_settings_email_table', 1),
('2016_02_16_140450_create_settings_ratings_table', 1),
('2016_02_16_140450_create_settings_system_table', 1),
('2016_02_16_140450_create_settings_ticket_table', 1),
('2016_02_16_140450_create_sla_plan_table', 1),
('2016_02_16_140450_create_team_assign_agent_table', 1),
('2016_02_16_140450_create_teams_table', 1),
('2016_02_16_140450_create_template_table', 1),
('2016_02_16_140450_create_ticket_attachment_table', 1),
('2016_02_16_140450_create_ticket_collaborator_table', 1),
('2016_02_16_140450_create_ticket_form_data_table', 1),
('2016_02_16_140450_create_ticket_priority_table', 1),
('2016_02_16_140450_create_ticket_source_table', 1),
('2016_02_16_140450_create_ticket_status_table', 1),
('2016_02_16_140450_create_ticket_thread_table', 1),
('2016_02_16_140450_create_tickets_table', 1),
('2016_02_16_140450_create_time_format_table', 1),
('2016_02_16_140450_create_timezone_table', 1),
('2016_02_16_140450_create_user_assign_organization_table', 1),
('2016_02_16_140450_create_users_table', 1),
('2016_02_16_140450_create_version_check_table', 1),
('2016_02_16_140450_create_widgets_table', 1),
('2016_02_16_140454_add_foreign_keys_to_canned_response_table', 1),
('2016_02_16_140454_add_foreign_keys_to_department_table', 1),
('2016_02_16_140454_add_foreign_keys_to_emails_table', 1),
('2016_02_16_140454_add_foreign_keys_to_group_assign_department_table', 1),
('2016_02_16_140454_add_foreign_keys_to_help_topic_table', 1),
('2016_02_16_140454_add_foreign_keys_to_kb_article_relationship_table', 1),
('2016_02_16_140454_add_foreign_keys_to_kb_comment_table', 1),
('2016_02_16_140454_add_foreign_keys_to_organization_table', 1),
('2016_02_16_140454_add_foreign_keys_to_settings_system_table', 1),
('2016_02_16_140454_add_foreign_keys_to_team_assign_agent_table', 1),
('2016_02_16_140454_add_foreign_keys_to_teams_table', 1),
('2016_02_16_140454_add_foreign_keys_to_ticket_attachment_table', 1),
('2016_02_16_140454_add_foreign_keys_to_ticket_collaborator_table', 1),
('2016_02_16_140454_add_foreign_keys_to_ticket_form_data_table', 1),
('2016_02_16_140454_add_foreign_keys_to_ticket_thread_table', 1),
('2016_02_16_140454_add_foreign_keys_to_tickets_table', 1),
('2016_02_16_140454_add_foreign_keys_to_user_assign_organization_table', 1),
('2016_02_16_140454_add_foreign_keys_to_users_table', 1),
('2016_03_31_061239_create_notifications_table', 1),
('2016_03_31_061534_create_notification_types_table', 1),
('2016_03_31_061740_create_user_notification_table', 1),
('2016_04_18_115852_create_workflow_name_table', 1),
('2016_04_18_115900_create_workflow_rule_table', 1),
('2016_04_18_115908_create_workflow_action_table', 1),
('2016_05_10_102423_create_country_code_table', 1),
('2016_05_10_102604_create_bar_notifications_table', 1),
('2016_05_11_105244_create_api_settings_table', 1),
('2016_05_19_055008_create_workflow_close_table', 1),
('2016_06_02_072210_create_common_settings_table', 1),
('2016_06_02_074913_create_login_attempts_table', 1),
('2016_06_02_080005_create_ratings_table', 1),
('2016_06_02_081020_create_rating_ref_table', 1),
('2016_06_02_090225_create_settings_security_table', 1),
('2016_06_02_090628_create_templates_table', 1),
('2016_06_02_094409_create_template_sets_table', 1),
('2016_06_02_094420_create_template_types_table', 1),
('2016_06_02_095357_create_ticket_token_table', 1),
('2016_06_28_141613_version1079table', 1),
('2016_07_02_051247_create_jobs_table', 1),
('2016_07_02_051439_create_failed_jobs_table', 1),
('2016_07_19_071910_create_field_values_table', 1),
('2016_07_26_084458_create_faveo_mails_table', 1),
('2016_07_26_090201_create_faveo_queues_table', 1),
('2016_07_26_094753_create_mail_services_table', 1),
('2016_07_26_095020_create_queue_services_table', 1),
('2016_07_29_113012_create_conditions_table', 1),
('2016_08_08_095744_create_social_media_table', 1),
('2016_08_12_104410_create_user_additional_infos_table', 1),
('2016_08_16_104539_alter_ticket_source_table', 1),
('2016_08_31_223407_create_approval_table', 1),
('2016_09_02_165516_create_follow_up_table', 1),
('2016_09_05_140010_create_status_type_table', 1),
('2016_09_05_140016_alter_status_table', 1),
('2016_09_07_125906_business_hours_table', 1),
('2016_09_07_130846_business_schedule_table', 1),
('2016_09_07_130925_business_schedule_open_custom_time_table', 1),
('2016_09_07_131000_business_holiday_table', 1),
('2016_10_07_173423_create_ticket_type_table', 1),
('2016_10_10_104534_create_labels_table', 1),
('2016_10_10_113555_create_sla_targets_table', 1),
('2016_10_10_124120_create_filters_table', 1),
('2016_10_12_072502_create_tags_table', 1),
('2016_10_21_081128_create_sla_approaches_table', 1),
('2016_10_21_081135_create_sla_violated_table', 1),
('2016_11_07_123810_create_halts_table', 1),
('2016_12_13_073613_create_department_assign_agents_table', 1),
('2017_02_01_110129_update-notification', 1),
('2017_02_02_073016_update_ticket_thread', 1),
('2017_02_07_104802_update_notification_table', 1),
('2017_02_09_093019_create_system_portal_table', 1),
('2017_02_11_072240_create_sla_violated_escalatetable', 1),
('2017_02_11_074259_create_sla_approach_escalatetable', 1),
('2017_02_22_075706_update_users_table_22_1_2017', 1),
('2017_02_23_044150_report_updates', 1),
('2017_02_25_072334_DepartmentCannedResponse', 1),
('2017_03_06_074629_notification_update', 1),
('2017_03_07_073611_alter_tickets_table', 1),
('2017_03_07_121632_updated_halts', 1),
('2017_03_10_044621_update_settings_system', 1),
('2016_10_18_085435_create_bills_table', 2),
('2016_05_29_131105_create_sd_cab_table', 3),
('2016_05_30_084708_create_sd_attachment_types_table', 3),
('2016_05_30_085022_create_sd_attachments_table', 3),
('2016_05_30_095158_create_sd_impact_types_table', 3),
('2016_05_30_095427_create_sd_location_categories_table', 3),
('2016_05_30_095428_create_sd_locations_table', 3),
('2016_05_31_100449_create_sd_release_types_table', 3),
('2016_05_31_100518_create_sd_release_status_table', 3),
('2016_05_31_100605_create_sd_release_priority_table', 3),
('2016_05_31_100704_create_sd_releases_table', 3),
('2016_05_31_102512_create_sd_change_status_table', 3),
('2016_05_31_102537_create_sd_change_priority_table', 3),
('2016_05_31_102602_create_sd_change_types_table', 3),
('2016_05_31_102657_create_sd_changes_table', 3),
('2016_06_09_133929_create_sd_problem_table', 3),
('2016_07_05_103737_create_sd_cab_votes_table', 3),
('2016_07_09_073642_create_sd_general_table', 3),
('2016_07_09_123113_create_sd_problem_change_table', 3),
('2016_07_09_123149_create_sd_change_release_table', 3),
('2016_07_12_120647_create_table_sd_ticket_relation', 3),
('2016_11_30_122809_alter_attachment_table', 4),
('2016_11_14_120138_alter_users_table', 5),
('2017_09_02_033151_bill_type_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seen` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `table` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `row_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `by`, `to`, `seen`, `table`, `row_id`, `url`, `created_at`, `updated_at`) VALUES
(1, 'A new ticket <b>How can I change my shipping address?</b> has created at 30/03/2017 09:35:41', '11', '1,3,2,5,6,8', '', 'tickets', 1, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/1', '2017-03-29 17:06:13', '2017-03-29 17:06:13'),
(2, 'Ticket Duedate on 30/03/2017 19:35:41,', '2', '', '', 'tickets', 1, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/1', '2017-03-29 17:28:39', '2017-03-29 17:28:39'),
(3, 'has assigned <b>AAAA-0000-0000</b> to himself,', '2', '2', '', 'tickets', 1, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/1', '2017-03-29 17:28:39', '2017-03-29 17:28:39'),
(4, 'has replied in <b>AAAA-0000-0000</b> at 30/03/2017 09:58:39', '2', '11', '', 'tickets', 1, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/1', '2017-03-29 17:28:45', '2017-03-29 17:28:45'),
(5, 'has replied in <b>AAAA-0000-0000</b> at 30/03/2017 10:24:15', '2', '11', '', 'tickets', 1, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/1', '2017-03-29 17:54:20', '2017-03-29 17:54:20'),
(6, 'Status set to <b>Resolved</b> from <b>Open</b> in <b>AAAA-0000-0000</b>,', '11', '2', '', 'tickets', 1, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/1', '2017-03-29 17:54:55', '2017-03-29 17:54:55'),
(7, 'A new ticket <b>What do you mean by points? How do I earn it?</b> has created at 30/03/2017 10:28:59', '18', '1,3,2,5,6,8', '', 'tickets', 2, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/2', '2017-03-29 17:59:27', '2017-03-29 17:59:27'),
(8, 'Ticket Duedate on 30/03/2017 20:28:59,', '1', '', '', 'tickets', 2, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/2', '2017-03-29 18:00:51', '2017-03-29 18:00:51'),
(9, 'has assigned <b>AAAA-0000-0001</b> to himself,', '1', '1', '', 'tickets', 2, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/2', '2017-03-29 18:00:51', '2017-03-29 18:00:51'),
(10, 'has replied in <b>AAAA-0000-0001</b> at 30/03/2017 10:30:51', '1', '18', '', 'tickets', 2, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/2', '2017-03-29 18:00:56', '2017-03-29 18:00:56'),
(11, 'has replied in <b>AAAA-0000-0001</b> at 30/03/2017 10:32:21', '1', '18', '', 'tickets', 2, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/2', '2017-03-29 18:02:26', '2017-03-29 18:02:26'),
(12, 'Status set to <b>Closed</b> from <b>Open</b> in <b>AAAA-0000-0001</b>,', '1', '1', '', 'tickets', 2, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/2', '2017-03-29 18:03:39', '2017-03-29 18:03:39'),
(13, 'A new ticket <b>Regarding the checkout limits</b> has created at 30/03/2017 10:35:17', '19', '1,3,4,8', '', 'tickets', 3, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/3', '2017-03-29 18:05:36', '2017-03-29 18:05:36'),
(14, 'Ticket Duedate on 30/03/2017 20:35:17,', '1', '', '', 'tickets', 3, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/3', '2017-03-29 18:07:19', '2017-03-29 18:07:19'),
(15, 'has assigned <b>AAAA-0000-0002</b> to himself,', '1', '1', '', 'tickets', 3, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/3', '2017-03-29 18:07:19', '2017-03-29 18:07:19'),
(16, 'has replied in <b>AAAA-0000-0002</b> at 30/03/2017 10:37:20', '1', '19', '', 'tickets', 3, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/3', '2017-03-29 18:07:25', '2017-03-29 18:07:25'),
(17, 'A new ticket <b>Delivery duration</b> has created at 30/03/2017 10:40:29', '15', '1,3,5,7', '', 'tickets', 4, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/4', '2017-03-29 18:10:48', '2017-03-29 18:10:48'),
(18, 'A new ticket <b>Redaring the delivery fee</b> has created at 30/03/2017 10:42:59', '16', '1,3,4,8', '', 'tickets', 5, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/5', '2017-03-29 18:13:18', '2017-03-29 18:13:18'),
(19, 'has assigned <b>AAAA-0000-0004</b> to <b>Ashok</b>,', '1', '4', '', 'tickets', 5, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/5', '2017-03-29 18:14:26', '2017-03-29 18:14:26'),
(20, 'Ticket Duedate on 30/03/2017 20:42:59,', '4', '4', '', 'tickets', 5, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/5', '2017-03-29 18:15:32', '2017-03-29 18:15:32'),
(21, 'has replied in <b>AAAA-0000-0004</b> at 30/03/2017 10:45:32', '4', '16', '', 'tickets', 5, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/5', '2017-03-29 18:15:37', '2017-03-29 18:15:37'),
(22, 'Status set to <b>Resolved</b> from <b>Open</b> in <b>AAAA-0000-0004</b>,', '4', '4', '', 'tickets', 5, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/5', '2017-03-29 18:15:58', '2017-03-29 18:15:58'),
(23, 'A new ticket <b>Remaining account credits</b> has created at 30/03/2017 10:49:12', '16', '1,3,2,5,6,8', '', 'tickets', 6, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/6', '2017-03-29 18:19:40', '2017-03-29 18:19:40'),
(24, 'A new ticket <b>Delivery mishap</b> has created at 30/03/2017 10:51:20', '10', '1,3,2,5,6,8', '', 'tickets', 7, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/7', '2017-03-29 18:21:48', '2017-03-29 18:21:48'),
(25, 'has assigned <b>AAAA-0000-0006</b> to <b>Ayesha</b>,', '1', '5', '', 'tickets', 7, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/7', '2017-03-29 18:22:36', '2017-03-29 18:22:36'),
(26, 'Ticket Duedate on 30/03/2017 20:51:20,', '5', '5', '', 'tickets', 7, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/7', '2017-03-29 18:23:46', '2017-03-29 18:23:46'),
(27, 'has replied in <b>AAAA-0000-0006</b> at 30/03/2017 10:53:46', '5', '10', '', 'tickets', 7, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/7', '2017-03-29 18:23:51', '2017-03-29 18:23:51'),
(28, 'Status set to <b>Request for close</b> from <b>Open</b> in <b>AAAA-0000-0006</b>,', '10', '5', '', 'tickets', 7, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/7', '2017-03-29 18:24:10', '2017-03-29 18:24:10'),
(29, 'A new ticket <b>Unsatisfied samples</b> has created at 30/03/2017 10:55:07', '20', '1,3,2,5,6,8', '', 'tickets', 8, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/8', '2017-03-29 18:25:36', '2017-03-29 18:25:36'),
(30, 'Ticket Duedate on 30/03/2017 20:55:07,', '1', '', '', 'tickets', 8, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/8', '2017-03-29 18:28:04', '2017-03-29 18:28:04'),
(31, 'has assigned <b>AAAA-0000-0007</b> to himself,', '1', '1', '', 'tickets', 8, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/8', '2017-03-29 18:28:04', '2017-03-29 18:28:04'),
(32, 'has replied in <b>AAAA-0000-0007</b> at 30/03/2017 10:58:05', '1', '20', '', 'tickets', 8, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/8', '2017-03-29 18:28:09', '2017-03-29 18:28:09'),
(33, 'A new ticket <b>Payment methods</b> has created at 30/03/2017 11:00:37', '23', '1,3,4,8', '', 'tickets', 9, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/9', '2017-03-29 18:30:56', '2017-03-29 18:30:56'),
(34, 'Ticket Duedate on 30/03/2017 21:00:37,', '1', '', '', 'tickets', 9, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/9', '2017-03-29 18:31:40', '2017-03-29 18:31:40'),
(35, 'has assigned <b>AAAA-0000-0008</b> to himself,', '1', '1', '', 'tickets', 9, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/9', '2017-03-29 18:31:40', '2017-03-29 18:31:40'),
(36, 'has replied in <b>AAAA-0000-0008</b> at 30/03/2017 11:01:41', '1', '23', '', 'tickets', 9, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/9', '2017-03-29 18:31:45', '2017-03-29 18:31:45'),
(37, 'A new ticket <b>Paymet methods</b> has created at 30/03/2017 11:06:31', '23', '1,3,4,8', '', 'tickets', 10, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/10', '2017-03-29 18:36:49', '2017-03-29 18:36:49'),
(38, 'Ticket Duedate on 30/03/2017 21:06:31,', '1', '', '', 'tickets', 10, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/10', '2017-03-29 18:39:18', '2017-03-29 18:39:18'),
(39, 'has assigned <b>AAAA-0000-0008</b> to himself,', '1', '1', '', 'tickets', 10, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/10', '2017-03-29 18:39:18', '2017-03-29 18:39:18'),
(40, 'has replied in <b>AAAA-0000-0008</b> at 30/03/2017 11:09:18', '1', '23', '', 'tickets', 10, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/10', '2017-03-29 18:39:23', '2017-03-29 18:39:23'),
(41, 'A new ticket <b>Pick up information</b> has created at 30/03/2017 11:13:39', '12', '1,3,2,5,6,8', '', 'tickets', 11, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/11', '2017-03-29 18:44:06', '2017-03-29 18:44:06'),
(42, 'Ticket Duedate on 30/03/2017 21:18:34,', '1', '', '', 'tickets', 13, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/13', '2017-03-29 18:50:15', '2017-03-29 18:50:15'),
(43, 'has assigned <b>AAAA-0000-0009</b> to himself,', '1', '1', '', 'tickets', 13, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/13', '2017-03-29 18:50:15', '2017-03-29 18:50:15'),
(44, 'has assigned <b>AAAA-0000-0010</b> to <b>Ashok</b>,', '1', '4', '', 'tickets', 14, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/14', '2017-03-29 18:56:41', '2017-03-29 18:56:41'),
(45, 'Ticket Duedate on 30/03/2017 21:23:35,', '4', '4', '', 'tickets', 14, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/14', '2017-03-29 18:58:51', '2017-03-29 18:58:51'),
(46, 'has replied in <b>AAAA-0000-0010</b> at 30/03/2017 11:28:52', '4', '16', '', 'tickets', 14, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/14', '2017-03-29 18:58:56', '2017-03-29 18:58:56'),
(47, 'A new ticket <b>Shopping Cart information</b> has created at 30/03/2017 11:30:20', '16', '1,3,2,5,6,8', '', 'tickets', 15, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/15', '2017-03-29 19:00:48', '2017-03-29 19:00:48'),
(48, 'A new ticket <b>Tax invoice</b> has created at 30/03/2017 11:31:45', '16', '1,3,2,5,6,8', '', 'tickets', 16, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/16', '2017-03-29 19:02:13', '2017-03-29 19:02:13'),
(49, 'Ticket Duedate on 30/03/2017 21:31:45,', '1', '', '', 'tickets', 16, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/16', '2017-03-29 19:03:34', '2017-03-29 19:03:34'),
(50, 'has assigned <b>AAAA-0000-0012</b> to himself,', '1', '1', '', 'tickets', 16, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/16', '2017-03-29 19:03:34', '2017-03-29 19:03:34'),
(51, 'has replied in <b>AAAA-0000-0012</b> at 30/03/2017 11:33:34', '1', '16', '', 'tickets', 16, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/16', '2017-03-29 19:03:39', '2017-03-29 19:03:39'),
(52, 'has assigned <b>AAAA-0000-0013</b> to <b>Divya </b>,', '1', '6', '', 'tickets', 17, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/17', '2017-03-29 19:05:08', '2017-03-29 19:05:08'),
(53, 'A new ticket <b>Order cancel information</b> has created at 30/03/2017 11:34:51', '17', '1,3,2,5,6,8', '', 'tickets', 17, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/17', '2017-03-29 19:05:20', '2017-03-29 19:05:20'),
(54, 'Ticket Duedate on 30/03/2017 21:34:51,', '6', '6', '', 'tickets', 17, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/17', '2017-03-29 19:06:43', '2017-03-29 19:06:43'),
(55, 'has replied in <b>AAAA-0000-0013</b> at 30/03/2017 11:36:44', '6', '17', '', 'tickets', 17, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/17', '2017-03-29 19:06:48', '2017-03-29 19:06:48'),
(56, 'Status set to <b>Resolved</b> from <b>Open</b> in <b>AAAA-0000-0013</b>,', '6', '6', '', 'tickets', 17, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/17', '2017-03-29 19:07:00', '2017-03-29 19:07:00'),
(57, 'has assigned <b>AAAA-0000-0014</b> to <b>Andrew</b>,', '1', '2', '', 'tickets', 18, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/18', '2017-03-29 19:10:35', '2017-03-29 19:10:35'),
(58, 'A new ticket <b>Payment methods</b> has created at 30/03/2017 11:40:16', '11', '1,3,4,8', '', 'tickets', 18, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/18', '2017-03-29 19:10:35', '2017-03-29 19:10:35'),
(59, 'Ticket Duedate on 30/03/2017 21:40:16,', '2', '2', '', 'tickets', 18, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/18', '2017-03-29 19:12:20', '2017-03-29 19:12:20'),
(60, 'has replied in <b>AAAA-0000-0014</b> at 30/03/2017 11:42:21', '2', '11', '', 'tickets', 18, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/18', '2017-03-29 19:12:26', '2017-03-29 19:12:26'),
(61, 'Status set to <b>Closed</b> from <b>Open</b> in <b>AAAA-0000-0014</b>,', '2', '2', '', 'tickets', 18, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/18', '2017-03-29 19:12:34', '2017-03-29 19:12:34'),
(62, 'has assigned <b>AAAA-0000-0015</b> to <b>Andrew</b>,', '1', '2', '', 'tickets', 19, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/19', '2017-03-29 19:14:00', '2017-03-29 19:14:00'),
(63, 'A new ticket <b>Payment information</b> has created at 30/03/2017 11:43:36', '11', '1,3,2,5,6,8', '', 'tickets', 19, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/19', '2017-03-29 19:14:04', '2017-03-29 19:14:04'),
(64, 'Ticket Duedate on 30/03/2017 21:43:36,', '2', '2', '', 'tickets', 19, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/19', '2017-03-29 19:15:25', '2017-03-29 19:15:25'),
(65, 'has replied in <b>AAAA-0000-0015</b> at 30/03/2017 11:45:25', '2', '11', '', 'tickets', 19, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/19', '2017-03-29 19:15:30', '2017-03-29 19:15:30'),
(66, 'A new ticket <b>Information regarding payment declined </b> has created at 30/03/2017 11:46:02', '11', '1,3,2,5,6,8', '', 'tickets', 20, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/20', '2017-03-29 19:16:31', '2017-03-29 19:16:31'),
(67, 'A new ticket <b>Refund information</b> has created at 30/03/2017 11:49:18', '18', '1,3,5,7', '', 'tickets', 21, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/21', '2017-03-29 19:19:37', '2017-03-29 19:19:37'),
(68, 'has assigned <b>AAAA-0000-0018</b> to <b>Sarayu</b>,', '1', '8', '', 'tickets', 22, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/22', '2017-03-29 19:22:30', '2017-03-29 19:22:30'),
(69, 'A new ticket <b>Regarding PDFs information </b> has created at 30/03/2017 11:52:12', '18', '1,3,5,7', '', 'tickets', 22, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/22', '2017-03-29 19:22:31', '2017-03-29 19:22:31'),
(70, 'Ticket Duedate on 30/03/2017 21:52:12,', '8', '8', '', 'tickets', 22, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/22', '2017-03-29 19:23:08', '2017-03-29 19:23:08'),
(71, 'has replied in <b>AAAA-0000-0018</b> at 30/03/2017 11:53:08', '8', '18', '', 'tickets', 22, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/22', '2017-03-29 19:23:13', '2017-03-29 19:23:13'),
(72, 'Status set to <b>Unverified Status</b> from <b>Open</b> in <b>AAAA-0000-0018</b>,', '18', '8', '', 'tickets', 22, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/22', '2017-03-29 19:23:23', '2017-03-29 19:23:23'),
(73, 'Status set to <b>Closed</b> from <b>Unverified Status</b> in <b>AAAA-0000-0018</b>,', '18', '8', '', 'tickets', 22, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/22', '2017-03-29 19:23:34', '2017-03-29 19:23:34'),
(74, 'has assigned <b>AAAA-0000-0019</b> to <b>Andrew</b>,', '1', '2', '', 'tickets', 23, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/23', '2017-03-29 19:24:44', '2017-03-29 19:24:44'),
(75, 'A new ticket <b>Infromation about Return policy</b> has created at 30/03/2017 11:54:30', '19', '1,3,2,5,6,8', '', 'tickets', 23, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/23', '2017-03-29 19:24:59', '2017-03-29 19:24:59'),
(76, 'Ticket Duedate on 30/03/2017 21:54:30,', '2', '2', '', 'tickets', 23, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/23', '2017-03-29 19:26:19', '2017-03-29 19:26:19'),
(77, 'has replied in <b>AAAA-0000-0019</b> at 30/03/2017 11:56:19', '2', '19', '', 'tickets', 23, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/23', '2017-03-29 19:26:24', '2017-03-29 19:26:24'),
(78, 'Status set to <b>Request for close</b> from <b>Open</b> in <b>AAAA-0000-0019</b>,', '19', '2', '', 'tickets', 23, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/23', '2017-03-29 19:26:35', '2017-03-29 19:26:35'),
(79, 'A new ticket <b>Informatiopn regarding \"return\"</b> has created at 30/03/2017 11:57:29', '19', '1,3,2,5,6,8', '', 'tickets', 24, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/24', '2017-03-29 19:27:57', '2017-03-29 19:27:57'),
(80, 'A new ticket <b>Publication information</b> has created at 30/03/2017 11:59:04', '10', '1,3,5,7', '', 'tickets', 25, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/25', '2017-03-29 19:29:23', '2017-03-29 19:29:23'),
(81, 'has assigned <b>AAAA-0000-0021</b> to <b>Ayesha</b>,', '1', '5', '', 'tickets', 25, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/25', '2017-03-29 19:29:58', '2017-03-29 19:29:58'),
(82, 'Ticket Duedate on 30/03/2017 21:59:04,', '5', '5', '', 'tickets', 25, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/25', '2017-03-29 19:30:36', '2017-03-29 19:30:36'),
(83, 'has replied in <b>AAAA-0000-0021</b> at 30/03/2017 12:00:37', '5', '10', '', 'tickets', 25, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/25', '2017-03-29 19:30:42', '2017-03-29 19:30:42'),
(84, 'Status set to <b>Deleted</b> from <b>Open</b> in <b>AAAA-0000-0021</b>,', '5', '5', '', 'tickets', 25, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/25', '2017-03-29 19:30:51', '2017-03-29 19:30:51'),
(85, 'A new ticket <b>Regarding sold-out information</b> has created at 30/03/2017 12:02:30', '10', '1,3,2,5,6,8', '', 'tickets', 26, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/26', '2017-03-29 19:32:58', '2017-03-29 19:32:58'),
(86, 'A new ticket <b>Infromation regarding checkout limits</b> has created at 30/03/2017 12:04:50', '23', '1,3,2,5,6,8', '', 'tickets', 27, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/27', '2017-03-29 19:35:18', '2017-03-29 19:35:18'),
(87, 'has assigned <b>AAAA-0000-0023</b> to <b>Ayesha</b>,', '1', '5', '', 'tickets', 27, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/27', '2017-03-29 19:35:21', '2017-03-29 19:35:21'),
(88, 'Ticket Duedate on 30/03/2017 22:04:50,', '5', '5', '', 'tickets', 27, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/27', '2017-03-29 19:36:01', '2017-03-29 19:36:01'),
(89, 'has replied in <b>AAAA-0000-0023</b> at 30/03/2017 12:06:02', '5', '23', '', 'tickets', 27, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/27', '2017-03-29 19:36:06', '2017-03-29 19:36:06'),
(90, 'A new ticket <b>Information regarding Duplicate invoices</b> has created at 30/03/2017 12:09:06', '20', '1,3,2,5,6,8', '', 'tickets', 28, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/28', '2017-03-29 19:39:35', '2017-03-29 19:39:35'),
(91, 'Ticket Duedate on 30/03/2017 22:09:06,', '1', '', '', 'tickets', 28, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/28', '2017-03-29 19:39:46', '2017-03-29 19:39:46'),
(92, 'has assigned <b>AAAA-0000-0024</b> to himself,', '1', '1', '', 'tickets', 28, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/28', '2017-03-29 19:39:46', '2017-03-29 19:39:46'),
(93, 'has replied in <b>AAAA-0000-0024</b> at 30/03/2017 12:09:46', '1', '20', '', 'tickets', 28, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/28', '2017-03-29 19:39:51', '2017-03-29 19:39:51'),
(94, 'has assigned <b>AAAA-0000-0025</b> to <b>Imran </b>,', '1', '7', '', 'tickets', 29, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/29', '2017-03-29 19:41:43', '2017-03-29 19:41:43'),
(95, 'A new ticket <b>Returns from booksellers</b> has created at 30/03/2017 12:11:16', '9', '1,3,2,5,6,8', '', 'tickets', 29, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/29', '2017-03-29 19:41:44', '2017-03-29 19:41:44'),
(96, 'A new ticket <b>Dangers of shopping online</b> has created at 30/03/2017 12:13:48', '9', '1,3,2,5,6,8', '', 'tickets', 30, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/30', '2017-03-29 19:44:16', '2017-03-29 19:44:16'),
(97, 'Ticket Duedate on 30/03/2017 22:13:48,', '1', '', '', 'tickets', 30, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/30', '2017-03-29 19:44:30', '2017-03-29 19:44:30'),
(98, 'has assigned <b>AAAA-0000-0026</b> to himself,', '1', '1', '', 'tickets', 30, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/30', '2017-03-29 19:44:30', '2017-03-29 19:44:30'),
(99, 'has replied in <b>AAAA-0000-0026</b> at 30/03/2017 12:14:31', '1', '9', '', 'tickets', 30, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/30', '2017-03-29 19:44:36', '2017-03-29 19:44:36'),
(100, 'A new ticket <b>Retailers to trust</b> has created at 30/03/2017 12:15:34', '14', '1,3,5,7', '', 'tickets', 31, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/31', '2017-03-29 19:45:52', '2017-03-29 19:45:52'),
(101, 'A new ticket <b>Safety on website</b> has created at 30/03/2017 12:16:20', '14', '1,3,2,5,6,8', '', 'tickets', 32, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/32', '2017-03-29 19:46:48', '2017-03-29 19:46:48'),
(102, 'Ticket Duedate on 30/03/2017 22:16:20,', '1', '', '', 'tickets', 32, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/32', '2017-03-29 19:46:54', '2017-03-29 19:46:54'),
(103, 'has assigned <b>AAAA-0000-0028</b> to himself,', '1', '1', '', 'tickets', 32, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/32', '2017-03-29 19:46:54', '2017-03-29 19:46:54'),
(104, 'has replied in <b>AAAA-0000-0028</b> at 30/03/2017 12:16:54', '1', '14', '', 'tickets', 32, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/32', '2017-03-29 19:46:59', '2017-03-29 19:46:59'),
(105, 'Status set to <b>Closed</b> from <b>Open</b> in <b>AAAA-0000-0028</b>,', '14', '1', '', 'tickets', 32, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/32', '2017-03-29 19:47:14', '2017-03-29 19:47:14'),
(106, 'Ticket Duedate on 30/03/2017 22:18:01,', '1', '', '', 'tickets', 33, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/33', '2017-03-29 19:48:20', '2017-03-29 19:48:20'),
(107, 'has assigned <b>AAAA-0000-0029</b> to himself,', '1', '1', '', 'tickets', 33, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/33', '2017-03-29 19:48:20', '2017-03-29 19:48:20'),
(108, 'has replied in <b>AAAA-0000-0029</b> at 30/03/2017 12:18:21', '1', '21', '', 'tickets', 33, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/33', '2017-03-29 19:48:26', '2017-03-29 19:48:26'),
(109, 'A new ticket <b>Rights of a customer</b> has created at 30/03/2017 12:18:01', '21', '1,3,2,5,6,8', '1', 'tickets', 33, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/33', '2017-03-29 19:48:30', '2017-03-30 23:53:54'),
(110, 'A new ticket <b>Purchases protection</b> has created at 30/03/2017 12:19:16', '21', '1,3,4,8', '1', 'tickets', 34, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/34', '2017-03-29 19:49:35', '2017-03-30 23:53:35'),
(111, 'Ticket Duedate on 30/03/2017 22:19:16,', '1', '', '', 'tickets', 34, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/34', '2017-03-29 19:49:53', '2017-03-29 19:49:53'),
(112, 'has assigned <b>AAAA-0000-0030</b> to himself,', '1', '1', '', 'tickets', 34, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/34', '2017-03-29 19:49:53', '2017-03-29 19:49:53'),
(113, 'has replied in <b>AAAA-0000-0030</b> at 30/03/2017 12:19:53', '1', '21', '', 'tickets', 34, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/34', '2017-03-29 19:49:58', '2017-03-29 19:49:58'),
(114, 'Status set to <b>Closed</b> from <b>Open</b> in <b>AAAA-0000-0030</b>,', '21', '1', '1,1', 'tickets', 34, 'http://www.faveohelpdesk.com/demo/helpdesk/thread/34', '2017-03-29 19:50:19', '2017-04-02 17:40:30'),
(115, 'A new user has been registered.', '24', '1,3', '', 'users', 24, 'https://www.faveohelpdesk.com/demo/helpdesk/user/24', '2017-08-31 05:20:13', '2017-08-31 05:20:13'),
(116, 'A new ticket <b>Test</b> has been created', '24', '1,3,6,2,5,8', '', 'tickets', 35, 'https://www.faveohelpdesk.com/demo/helpdesk/thread/35', '2017-08-31 05:20:13', '2017-08-31 05:20:13'),
(117, 'A new user has been registered.', '25', '1,3,25', '', 'users', 25, 'https://www.faveohelpdesk.com/demo/helpdesk/user/25', '2017-10-04 05:44:58', '2017-10-04 05:44:58'),
(118, 'has set Priority of <b>AAAA-0000-0025</b> as <b>Normal</b> from <b>Low</b>, has set SLA of <b>AAAA-0000-0025</b> as <b>Normal</b> from <b>Low</b>', '1', '7', '', 'tickets', 29, 'https://www.faveohelpdesk.com/demo/helpdesk/thread/29', '2017-10-04 05:47:32', '2017-10-04 05:47:32'),
(119, 'A new user has been registered.', '26', '1,3,25,26', '', 'users', 26, 'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/user/26', '2017-10-04 02:35:38', '2017-10-04 02:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `no_assign_escalate`
--

DROP TABLE IF EXISTS `no_assign_escalate`;
CREATE TABLE IF NOT EXISTS `no_assign_escalate` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

DROP TABLE IF EXISTS `organization`;
CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `head` int(10) UNSIGNED DEFAULT NULL,
  `internal_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_Code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `line_of_business` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `relation_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `head` (`head`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`id`, `name`, `phone`, `website`, `address`, `head`, `internal_notes`, `created_at`, `updated_at`, `domain`, `client_Code`, `phone1`, `line_of_business`, `relation_type`, `branch`, `fax`, `logo`) VALUES
(1, ' E shopping', '767896090', 'http://eshopping.com', '', NULL, '', '2017-03-29 05:39:08', '2017-03-29 05:39:08', NULL, '', '', '', '', '', '', ''),
(2, 'E-store ', '869868699', 'http://estore.co.in', '', NULL, '', '2017-03-29 05:39:36', '2017-03-29 05:39:36', NULL, '', '', '', '', '', '', ''),
(3, 'Foot Shoppe', '689698606', 'http://footshoppe.org', '', NULL, '', '2017-03-29 05:40:10', '2017-03-29 05:40:10', NULL, '', '', '', '', '', '', ''),
(4, 'Mob online ', '6896960979', 'http://mobonline.co.in', '', NULL, '', '2017-03-29 05:40:37', '2017-03-29 05:40:37', NULL, '', '', '', '', '', '', ''),
(5, 'Shop Indi', '9689786876', 'http://shopindi.com', '', NULL, '', '2017-03-29 05:41:05', '2017-03-29 05:41:05', NULL, '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `organization_dept`
--

DROP TABLE IF EXISTS `organization_dept`;
CREATE TABLE IF NOT EXISTS `organization_dept` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `org_deptname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hours_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `org_attachment`
--

DROP TABLE IF EXISTS `org_attachment`;
CREATE TABLE IF NOT EXISTS `org_attachment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permision`
--

DROP TABLE IF EXISTS `permision`;
CREATE TABLE IF NOT EXISTS `permision` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permision` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permision`
--

INSERT INTO `permision` (`id`, `user_id`, `permision`, `created_at`, `updated_at`) VALUES
(7, 1, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(8, 2, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(9, 3, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(10, 4, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(11, 5, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(12, 6, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(13, 7, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(14, 8, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\"}', '2017-08-31 00:00:00', '2017-08-31 00:00:00'),
(15, 1, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"close_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\",\"organisation_document_upload\":\"1\",\"email_verification\":\"1\",\"mobile_verification\":\"1\",\"account_activate\":\"1\"}', '2017-11-24 12:54:37', '2017-11-24 12:54:37'),
(16, 1, '{\"create_ticket\":\"1\",\"edit_ticket\":\"1\",\"close_ticket\":\"1\",\"transfer_ticket\":\"1\",\"delete_ticket\":\"1\",\"assign_ticket\":\"1\",\"access_kb\":\"1\",\"ban_email\":\"1\",\"organisation_document_upload\":\"1\",\"email_verification\":\"1\",\"mobile_verification\":\"1\",\"account_activate\":\"1\"}', '2018-01-03 06:51:15', '2018-01-03 06:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queue_services`
--

DROP TABLE IF EXISTS `queue_services`;
CREATE TABLE IF NOT EXISTS `queue_services` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `queue_services`
--

INSERT INTO `queue_services` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sync', 'sync', 1, '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(2, 'Database', 'database', 0, '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(3, 'Beanstalkd', 'beanstalkd', 0, '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(4, 'SQS', 'sqs', 0, '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(5, 'Iron', 'iron', 0, '2017-03-29 05:23:18', '2017-03-29 05:23:18'),
(6, 'Redis', 'redis', 0, '2017-03-29 05:23:18', '2017-03-29 05:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_order` int(11) NOT NULL,
  `allow_modification` int(11) NOT NULL,
  `rating_scale` int(11) NOT NULL,
  `rating_area` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `restrict` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `name`, `display_order`, `allow_modification`, `rating_scale`, `rating_area`, `restrict`, `created_at`, `updated_at`) VALUES
(1, 'OverAll Satisfaction', 1, 1, 5, 'Helpdesk Area', '', '2017-03-29 05:23:27', '2017-03-29 05:23:27'),
(2, 'Reply Rating', 1, 1, 5, 'Comment Area', '', '2017-03-29 05:23:27', '2017-03-29 05:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `rating_ref`
--

DROP TABLE IF EXISTS `rating_ref`;
CREATE TABLE IF NOT EXISTS `rating_ref` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rating_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `rating_value` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `rating_ref`
--

INSERT INTO `rating_ref` (`id`, `rating_id`, `ticket_id`, `thread_id`, `rating_value`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5, 3, '2017-03-29 23:24:35', '2017-03-29 23:24:35'),
(2, 2, 1, 6, 2, '2017-03-29 23:24:37', '2017-03-29 23:24:38'),
(3, 1, 1, 0, 4, '2017-03-29 23:24:42', '2017-03-29 23:24:42'),
(4, 2, 2, 12, 3, '2017-03-29 23:33:08', '2017-03-29 23:33:08'),
(5, 2, 2, 13, 4, '2017-03-29 23:33:09', '2017-03-29 23:33:09'),
(6, 1, 2, 0, 4, '2017-03-29 23:33:11', '2017-03-29 23:33:11'),
(7, 2, 7, 34, 4, '2017-03-29 23:53:59', '2017-03-29 23:53:59'),
(8, 1, 7, 0, 4, '2017-03-29 23:54:00', '2017-03-29 23:54:00'),
(9, 2, 18, 83, 3, '2017-03-30 00:42:43', '2017-03-30 00:42:43'),
(10, 1, 18, 0, 3, '2017-03-30 00:42:44', '2017-03-30 00:42:44'),
(11, 2, 22, 98, 2, '2017-03-30 00:53:15', '2017-03-30 00:53:15'),
(12, 1, 22, 0, 3, '2017-03-30 00:53:16', '2017-03-30 00:53:16'),
(13, 1, 23, 0, 4, '2017-03-30 00:56:22', '2017-03-30 00:56:22'),
(14, 2, 23, 105, 3, '2017-03-30 00:56:26', '2017-03-30 00:56:26'),
(15, 1, 27, 0, 3, '2017-03-30 01:06:26', '2017-03-30 01:06:26'),
(16, 1, 32, 0, 3, '2017-03-30 01:17:01', '2017-03-30 01:17:01'),
(17, 2, 32, 141, 3, '2017-03-30 01:17:03', '2017-03-30 01:17:03'),
(18, 1, 34, 0, 4, '2017-03-30 01:20:08', '2017-03-30 01:20:08'),
(19, 2, 34, 152, 2, '2017-03-30 01:20:10', '2017-03-30 01:20:10');

-- --------------------------------------------------------

--
-- Table structure for table `recure_contents`
--

DROP TABLE IF EXISTS `recure_contents`;
CREATE TABLE IF NOT EXISTS `recure_contents` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `recur_id` int(11) NOT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurs`
--

DROP TABLE IF EXISTS `recurs`;
CREATE TABLE IF NOT EXISTS `recurs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `interval` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `delivery_on` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `last_execution` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requireds`
--

DROP TABLE IF EXISTS `requireds`;
CREATE TABLE IF NOT EXISTS `requireds` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `requireds`
--

INSERT INTO `requireds` (`id`, `form`, `field`, `agent`, `client`, `parent`, `option`, `label`, `created_at`, `updated_at`) VALUES
(1, 'ticket', 'requester', 'required', 'required', NULL, '', 'Requester', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(2, 'ticket', 'subject', 'required', 'required', NULL, '', 'Subject', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(3, 'ticket', 'type', 'required', 'required', NULL, '', 'Type', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(4, 'ticket', 'status', 'required', NULL, NULL, '', 'Status', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(5, 'ticket', 'priority', 'required', 'required', NULL, '', 'Priority', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(6, 'ticket', 'location', NULL, NULL, NULL, '', 'Location', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(7, 'ticket', 'help_topic', 'required', 'required', NULL, '', 'Help Topic', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(8, 'ticket', 'department', 'required', NULL, NULL, '', 'Department', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(9, 'ticket', 'assigned', 'required', NULL, NULL, '', 'Assigned', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(10, 'ticket', 'description', 'required', NULL, NULL, '', 'Description', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(11, 'ticket', 'company', NULL, NULL, NULL, '', 'Company', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(12, 'ticket', '', NULL, NULL, NULL, '', '', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(13, 'user', 'first_name', 'required', 'required', NULL, '', 'First Name', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(14, 'user', 'last_name', 'required', 'required', NULL, '', 'Last Name', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(15, 'user', 'user_name', NULL, NULL, NULL, '', 'User Name', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(16, 'user', 'phone_number', NULL, NULL, NULL, '', 'Work Phone', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(17, 'user', 'email', 'required', 'required', NULL, '', 'Email', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(18, 'user', 'mobile', NULL, NULL, NULL, '', 'Mobile Phone', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(19, 'user', 'address', NULL, NULL, NULL, '', 'Address', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(20, 'user', 'organisation', NULL, NULL, NULL, '', 'Organisation', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(21, 'user', 'department', NULL, NULL, NULL, '', 'Department Name', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(22, 'user', '', NULL, NULL, NULL, '', '', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(23, 'organisation', 'name', 'required', 'required', NULL, '', 'Company Name', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(24, 'organisation', 'phone', NULL, NULL, NULL, '', 'Phone', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(25, 'organisation', 'domain', NULL, NULL, NULL, '', 'Company Domain Name', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(26, 'organisation', 'internal_notes', NULL, NULL, NULL, '', 'Description', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(27, 'organisation', 'address', NULL, NULL, NULL, '', 'Address', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(28, 'organisation', 'department', NULL, NULL, NULL, '', 'Department', '2017-10-25 05:09:45', '2017-10-25 05:09:45'),
(29, 'ticket', 'org_dept', NULL, NULL, NULL, '', 'Micro Organisation', '2017-12-27 13:35:14', '2017-12-27 13:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `required_fields`
--

DROP TABLE IF EXISTS `required_fields`;
CREATE TABLE IF NOT EXISTS `required_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_agent_required` int(11) NOT NULL,
  `is_client_required` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `required_fields`
--

INSERT INTO `required_fields` (`id`, `form`, `name`, `is_agent_required`, `is_client_required`, `created_at`, `updated_at`) VALUES
(1, 'ticket', 'Requester', 1, 1, NULL, NULL),
(2, 'ticket', 'Subject', 1, 1, NULL, NULL),
(3, 'ticket', 'Type', 1, 1, NULL, NULL),
(4, 'ticket', 'Status', 1, 1, NULL, NULL),
(5, 'ticket', 'Priority', 1, 1, NULL, NULL),
(6, 'ticket', 'Group', 1, 1, NULL, NULL),
(7, 'ticket', 'Agent', 1, 1, NULL, NULL),
(8, 'ticket', 'Description', 1, 1, NULL, NULL),
(9, 'ticket', 'Company', 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sd_attachments`
--

DROP TABLE IF EXISTS `sd_attachments`;
CREATE TABLE IF NOT EXISTS `sd_attachments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `saved` int(10) UNSIGNED NOT NULL,
  `owner` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_attachments_saved_foreign` (`saved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_attachment_types`
--

DROP TABLE IF EXISTS `sd_attachment_types`;
CREATE TABLE IF NOT EXISTS `sd_attachment_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_attachment_types`
--

INSERT INTO `sd_attachment_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Database', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'File', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_cab`
--

DROP TABLE IF EXISTS `sd_cab`;
CREATE TABLE IF NOT EXISTS `sd_cab` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `head` int(10) UNSIGNED DEFAULT NULL,
  `approvers` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aproval_mandatory` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_cab_head_foreign` (`head`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_cab_votes`
--

DROP TABLE IF EXISTS `sd_cab_votes`;
CREATE TABLE IF NOT EXISTS `sd_cab_votes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cab_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vote` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_cab_votes_cab_id_foreign` (`cab_id`),
  KEY `sd_cab_votes_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_changes`
--

DROP TABLE IF EXISTS `sd_changes`;
CREATE TABLE IF NOT EXISTS `sd_changes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `requester` int(10) UNSIGNED DEFAULT NULL,
  `status_id` int(10) UNSIGNED DEFAULT NULL,
  `priority_id` int(10) UNSIGNED DEFAULT NULL,
  `change_type_id` int(10) UNSIGNED DEFAULT NULL,
  `impact_id` int(10) UNSIGNED DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `approval_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_changes_requester_foreign` (`requester`),
  KEY `sd_changes_status_id_foreign` (`status_id`),
  KEY `sd_changes_priority_id_foreign` (`priority_id`),
  KEY `sd_changes_change_type_id_foreign` (`change_type_id`),
  KEY `sd_changes_impact_id_foreign` (`impact_id`),
  KEY `sd_changes_location_id_foreign` (`location_id`),
  KEY `sd_changes_approval_id_foreign` (`approval_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_change_priorities`
--

DROP TABLE IF EXISTS `sd_change_priorities`;
CREATE TABLE IF NOT EXISTS `sd_change_priorities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_change_priorities`
--

INSERT INTO `sd_change_priorities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Low', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Medium', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'High', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Urgent', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_change_release`
--

DROP TABLE IF EXISTS `sd_change_release`;
CREATE TABLE IF NOT EXISTS `sd_change_release` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `change_id` int(10) UNSIGNED DEFAULT NULL,
  `release_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_change_release_change_id_foreign` (`change_id`),
  KEY `sd_change_release_release_id_foreign` (`release_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_change_status`
--

DROP TABLE IF EXISTS `sd_change_status`;
CREATE TABLE IF NOT EXISTS `sd_change_status` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_change_status`
--

INSERT INTO `sd_change_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Open', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Planning', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Awaiting Approval', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Pending Release', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'Pending Review', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'Closed', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_change_types`
--

DROP TABLE IF EXISTS `sd_change_types`;
CREATE TABLE IF NOT EXISTS `sd_change_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_change_types`
--

INSERT INTO `sd_change_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Minor', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Standard', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Major', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Emergency', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_gerneral`
--

DROP TABLE IF EXISTS `sd_gerneral`;
CREATE TABLE IF NOT EXISTS `sd_gerneral` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_impact_types`
--

DROP TABLE IF EXISTS `sd_impact_types`;
CREATE TABLE IF NOT EXISTS `sd_impact_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_locations`
--

DROP TABLE IF EXISTS `sd_locations`;
CREATE TABLE IF NOT EXISTS `sd_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `location_category_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `organization` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `all_department_access` int(11) NOT NULL,
  `departments` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_locations_location_category_id_foreign` (`location_category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_location_categories`
--

DROP TABLE IF EXISTS `sd_location_categories`;
CREATE TABLE IF NOT EXISTS `sd_location_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_problem`
--

DROP TABLE IF EXISTS `sd_problem`;
CREATE TABLE IF NOT EXISTS `sd_problem` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status_type_id` int(10) UNSIGNED DEFAULT NULL,
  `priority_id` int(10) UNSIGNED DEFAULT NULL,
  `impact_id` int(10) UNSIGNED DEFAULT NULL,
  `location_type_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `agent_id` int(10) UNSIGNED DEFAULT NULL,
  `assigned_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_problem_change`
--

DROP TABLE IF EXISTS `sd_problem_change`;
CREATE TABLE IF NOT EXISTS `sd_problem_change` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `problem_id` int(10) UNSIGNED DEFAULT NULL,
  `change_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_problem_change_problem_id_foreign` (`problem_id`),
  KEY `sd_problem_change_change_id_foreign` (`change_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_releases`
--

DROP TABLE IF EXISTS `sd_releases`;
CREATE TABLE IF NOT EXISTS `sd_releases` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `planned_start_date` timestamp NULL DEFAULT NULL,
  `planned_end_date` timestamp NULL DEFAULT NULL,
  `status_id` int(10) UNSIGNED DEFAULT NULL,
  `priority_id` int(10) UNSIGNED DEFAULT NULL,
  `release_type_id` int(10) UNSIGNED DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_releases_status_id_foreign` (`status_id`),
  KEY `sd_releases_priority_id_foreign` (`priority_id`),
  KEY `sd_releases_release_type_id_foreign` (`release_type_id`),
  KEY `sd_releases_location_id_foreign` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_release_priorities`
--

DROP TABLE IF EXISTS `sd_release_priorities`;
CREATE TABLE IF NOT EXISTS `sd_release_priorities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_release_priorities`
--

INSERT INTO `sd_release_priorities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Low', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Medium', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'High', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Urgent', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_release_status`
--

DROP TABLE IF EXISTS `sd_release_status`;
CREATE TABLE IF NOT EXISTS `sd_release_status` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_release_status`
--

INSERT INTO `sd_release_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Open', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'On Hold', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'In Progress', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Incomplete', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'Completed', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_release_types`
--

DROP TABLE IF EXISTS `sd_release_types`;
CREATE TABLE IF NOT EXISTS `sd_release_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_release_types`
--

INSERT INTO `sd_release_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Minor', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Standard', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Major', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Emergency', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sd_ticket_relation`
--

DROP TABLE IF EXISTS `sd_ticket_relation`;
CREATE TABLE IF NOT EXISTS `sd_ticket_relation` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `owner` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings_alert_notice`
--

DROP TABLE IF EXISTS `settings_alert_notice`;
CREATE TABLE IF NOT EXISTS `settings_alert_notice` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_alert_notice`
--

INSERT INTO `settings_alert_notice` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'new_ticket_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(2, 'new_ticket_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(3, 'new_ticket_alert_persons', 'admin,department_manager,department_members', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(4, 'ticket_assign_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(5, 'ticket_assign_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(6, 'ticket_assign_alert_persons', 'assigned_agent_team', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(7, 'notification_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(8, 'notification_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(9, 'notification_alert_persons', 'admin,agent,department_manager,team_lead', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(10, 'internal_activity_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(11, 'internal_activity_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(12, 'internal_activity_alert_persons', 'assigned_agent_team', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(13, 'ticket_transfer_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(14, 'ticket_transfer_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(15, 'ticket_transfer_alert_persons', 'assigned_agent_team,department_members', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(16, 'registration_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(17, 'registration_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(18, 'registration_alert_persons', 'new_user', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(19, 'new_user_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(20, 'new_user_alert_mode', 'system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(21, 'new_user_alert_persons', 'admin', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(22, 'reply_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(23, 'reply_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(24, 'reply_alert_persons', 'client', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(25, 'reply_notification_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(26, 'reply_notification_alert_mode', 'email,system', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(27, 'reply_notification_alert_persons', 'assigned_agent_team', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(28, 'new_ticket_confirmation_alert', '1', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(29, 'new_ticket_confirmation_alert_mode', 'email', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(30, 'new_ticket_confirmation_alert_persons', 'client', '2017-07-06 11:40:08', '2017-07-06 11:40:08'),
(31, 'browser_notification_status', '0', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
(32, 'api_id', '', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
(33, 'rest_api_key', '', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
(34, 'browser_notification_alert_persons', 'agent,admin, client', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
(35, 'task_alert_mode', 'email,in-app-notify', '2017-12-06 12:08:41', '2017-12-06 12:08:41'),
(36, 'task_alert_persons', 'admin,agent', '2017-12-06 12:08:41', '2017-12-06 12:08:41'),
(37, 'task_alert_status', '1', '2017-12-06 12:08:41', '2017-12-06 12:08:41');

-- --------------------------------------------------------

--
-- Table structure for table `settings_auto_response`
--

DROP TABLE IF EXISTS `settings_auto_response`;
CREATE TABLE IF NOT EXISTS `settings_auto_response` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `new_ticket` tinyint(1) NOT NULL,
  `agent_new_ticket` tinyint(1) NOT NULL,
  `submitter` tinyint(1) NOT NULL,
  `participants` tinyint(1) NOT NULL,
  `overlimit` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_auto_response`
--

INSERT INTO `settings_auto_response` (`id`, `new_ticket`, `agent_new_ticket`, `submitter`, `participants`, `overlimit`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `settings_company`
--

DROP TABLE IF EXISTS `settings_company`;
CREATE TABLE IF NOT EXISTS `settings_company` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `landing_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `offline_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thank_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `use_logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_company`
--

INSERT INTO `settings_company` (`id`, `company_name`, `website`, `phone`, `address`, `landing_page`, `offline_page`, `thank_page`, `logo`, `use_logo`, `created_at`, `updated_at`) VALUES
(1, 'CHAMPPS', '', '', '', '', '', '', '3428.1801.logo.png', '0', '2017-03-29 05:23:25', '2017-04-30 11:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `settings_email`
--

DROP TABLE IF EXISTS `settings_email`;
CREATE TABLE IF NOT EXISTS `settings_email` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sys_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alert_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mta` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_fetching` tinyint(1) NOT NULL,
  `notification_cron` tinyint(1) NOT NULL,
  `strip` tinyint(1) NOT NULL,
  `separator` tinyint(1) NOT NULL,
  `all_emails` tinyint(1) NOT NULL,
  `email_collaborator` tinyint(1) NOT NULL,
  `attachment` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_email`
--

INSERT INTO `settings_email` (`id`, `template`, `sys_email`, `alert_email`, `admin_email`, `mta`, `email_fetching`, `notification_cron`, `strip`, `separator`, `all_emails`, `email_collaborator`, `attachment`, `created_at`, `updated_at`) VALUES
(1, 'default', '2', '', '', '', 1, 1, 0, 0, 1, 1, 1, '2017-03-29 05:23:25', '2017-04-02 17:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `settings_ratings`
--

DROP TABLE IF EXISTS `settings_ratings`;
CREATE TABLE IF NOT EXISTS `settings_ratings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rating_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `publish` int(11) NOT NULL,
  `modify` int(11) NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_ratings_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings_security`
--

DROP TABLE IF EXISTS `settings_security`;
CREATE TABLE IF NOT EXISTS `settings_security` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lockout_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `backlist_offender` int(11) NOT NULL,
  `backlist_threshold` int(11) NOT NULL,
  `lockout_period` int(11) NOT NULL,
  `days_to_keep_logs` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_security`
--

INSERT INTO `settings_security` (`id`, `lockout_message`, `backlist_offender`, `backlist_threshold`, `lockout_period`, `days_to_keep_logs`, `created_at`, `updated_at`) VALUES
(1, 'You have been locked out of application due to too many failed login attempts.', 0, 15, 15, 0, '2017-03-29 05:23:26', '2017-03-29 05:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `settings_system`
--

DROP TABLE IF EXISTS `settings_system`;
CREATE TABLE IF NOT EXISTS `settings_system` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `page_size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `log_level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `purge_log` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `api_enable` int(11) NOT NULL,
  `api_key_mandatory` int(11) NOT NULL,
  `api_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_farmat` int(10) UNSIGNED DEFAULT NULL,
  `date_format` int(10) UNSIGNED DEFAULT NULL,
  `time_zone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_time_format` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_date_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `serial_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_number` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_farmat` (`time_farmat`),
  KEY `date_format` (`date_format`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_system`
--

INSERT INTO `settings_system` (`id`, `status`, `url`, `name`, `department`, `page_size`, `log_level`, `purge_log`, `api_enable`, `api_key_mandatory`, `api_key`, `name_format`, `time_farmat`, `date_format`, `time_zone`, `date_time_format`, `day_date_time`, `content`, `version`, `created_at`, `updated_at`, `serial_key`, `order_number`) VALUES
(1, 1, '', 'CHAMPPS', '1', '', '', '', 0, 0, '', '', NULL, NULL, 'US/Central', 'd/m/Y H:i:s', '', 'en', '1.9.22', '2017-03-29 05:23:25', '2017-11-24 12:54:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings_ticket`
--

DROP TABLE IF EXISTS `settings_ticket`;
CREATE TABLE IF NOT EXISTS `settings_ticket` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `num_sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `help_topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `max_open_ticket` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `collision_avoid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lock_ticket_frequency` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `captcha` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `claim_response` tinyint(1) NOT NULL,
  `assigned_ticket` tinyint(1) NOT NULL,
  `answered_ticket` tinyint(1) NOT NULL,
  `agent_mask` tinyint(1) NOT NULL,
  `html` tinyint(1) NOT NULL,
  `client_update` tinyint(1) NOT NULL,
  `max_file_size` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_ticket`
--

INSERT INTO `settings_ticket` (`id`, `num_format`, `num_sequence`, `help_topic`, `max_open_ticket`, `collision_avoid`, `lock_ticket_frequency`, `captcha`, `status`, `claim_response`, `assigned_ticket`, `answered_ticket`, `agent_mask`, `html`, `client_update`, `max_file_size`, `created_at`, `updated_at`) VALUES
(1, '$$$$-####-####', 'sequence', '1', '', '2', '0', '', 1, 0, 0, 0, 0, 0, 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `sla_approaches`
--

DROP TABLE IF EXISTS `sla_approaches`;
CREATE TABLE IF NOT EXISTS `sla_approaches` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `response_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `response_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sla_approach_escalate`
--

DROP TABLE IF EXISTS `sla_approach_escalate`;
CREATE TABLE IF NOT EXISTS `sla_approach_escalate` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sla_plan`
--

DROP TABLE IF EXISTS `sla_plan`;
CREATE TABLE IF NOT EXISTS `sla_plan` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `grace_period` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `sla_target` int(11) NOT NULL,
  `apply_sla_depertment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_tickettype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_ticketsource` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `transient` tinyint(1) NOT NULL,
  `ticket_overdue` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `apply_sla_helptopic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_orgdepts` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_labels` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sla_plan`
--

INSERT INTO `sla_plan` (`id`, `name`, `grace_period`, `admin_note`, `status`, `sla_target`, `apply_sla_depertment`, `apply_sla_company`, `apply_sla_tickettype`, `apply_sla_ticketsource`, `transient`, `ticket_overdue`, `created_at`, `updated_at`, `apply_sla_helptopic`, `apply_sla_orgdepts`, `apply_sla_labels`, `apply_sla_tags`, `order`, `is_default`) VALUES
(1, 'Low', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 1, '', '', '', '', 0, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', '', '', '', '', 0, 1),
(2, 'Normal', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 2, '', '', '', '', 0, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', '', '', '', '', 0, 0),
(3, 'High', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 3, '', '', '', '', 0, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', '', '', '', '', 0, 0),
(4, 'Emergency', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 4, '', '', '', '', 0, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', '', '', '', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sla_targets`
--

DROP TABLE IF EXISTS `sla_targets`;
CREATE TABLE IF NOT EXISTS `sla_targets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sla_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `respond_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolve_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hour_id` int(11) NOT NULL,
  `send_email` int(11) NOT NULL,
  `send_sms` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `in_app` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sla_targets`
--

INSERT INTO `sla_targets` (`id`, `name`, `sla_id`, `priority_id`, `respond_within`, `resolve_within`, `business_hour_id`, `send_email`, `send_sms`, `created_at`, `updated_at`, `in_app`) VALUES
(1, 'Low', 1, 1, '5-hrs', '10-hrs', 1, 1, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', 0),
(2, 'Normal', 2, 2, '4-hrs', '9-hrs', 1, 1, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', 0),
(3, 'High', 3, 3, '2-hrs', '4-hrs', 1, 1, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', 0),
(4, 'Emergency', 4, 4, '1-hrs', '2-hrs', 1, 1, 0, '2017-03-29 05:23:24', '2017-03-29 05:23:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sla_violated`
--

DROP TABLE IF EXISTS `sla_violated`;
CREATE TABLE IF NOT EXISTS `sla_violated` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `response_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `response_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sla_violated_escalate`
--

DROP TABLE IF EXISTS `sla_violated_escalate`;
CREATE TABLE IF NOT EXISTS `sla_violated_escalate` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_media`
--

DROP TABLE IF EXISTS `social_media`;
CREATE TABLE IF NOT EXISTS `social_media` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `system_portal`
--

DROP TABLE IF EXISTS `system_portal`;
CREATE TABLE IF NOT EXISTS `system_portal` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agent_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_button_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_button_border_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_input_fild_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `system_portal`
--

INSERT INTO `system_portal` (`id`, `admin_header_color`, `agent_header_color`, `client_header_color`, `client_button_color`, `client_button_border_color`, `client_input_fild_color`, `logo`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'skin-yellow', 'skin-blue', 'null', 'null', 'null', 'null', '0', '0', '2017-03-29 05:23:28', '2017-04-02 17:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Late delivery', '', '2017-03-29 22:48:05', '2017-03-29 22:48:05'),
(2, 'Not satisfied', '', '2017-03-29 22:48:23', '2017-03-29 22:48:39'),
(3, 'Rights', '', '2017-03-30 03:04:57', '2017-03-30 03:04:57'),
(4, 'Customer', '', '2017-03-30 03:05:03', '2017-03-30 03:05:03');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `team_lead` int(10) UNSIGNED DEFAULT NULL,
  `assign_alert` tinyint(1) NOT NULL,
  `admin_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_lead` (`team_lead`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `status`, `team_lead`, `assign_alert`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'Level 1 Support', 1, NULL, 0, '', '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(2, 'Level 2 Support', 1, NULL, 0, '', '2017-03-29 05:23:25', '2017-03-29 05:41:54'),
(3, 'Developer', 1, NULL, 0, '', '2017-03-29 05:23:25', '2017-03-29 05:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `team_assign_agent`
--

DROP TABLE IF EXISTS `team_assign_agent`;
CREATE TABLE IF NOT EXISTS `team_assign_agent` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` int(10) UNSIGNED DEFAULT NULL,
  `agent_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`),
  KEY `agent_id` (`agent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `team_assign_agent`
--

INSERT INTO `team_assign_agent` (`id`, `team_id`, `agent_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, NULL, NULL),
(2, 1, 3, NULL, NULL),
(3, 2, 3, NULL, NULL),
(4, 3, 3, NULL, NULL),
(5, 2, 4, NULL, NULL),
(12, 3, 5, NULL, NULL),
(11, 2, 5, NULL, NULL),
(8, 1, 6, NULL, NULL),
(9, 3, 6, NULL, NULL),
(10, 3, 7, NULL, NULL),
(13, 1, 8, NULL, NULL),
(14, 2, 8, NULL, NULL),
(15, 3, 8, NULL, NULL),
(16, 1, 25, NULL, NULL),
(17, 2, 26, NULL, NULL),
(18, 3, 26, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

DROP TABLE IF EXISTS `template`;
CREATE TABLE IF NOT EXISTS `template` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `template_set_to_clone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `variable` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `set_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`) VALUES
(1, 'template-register-confirmation-with-account-details', '1', 7, 'Registration Confirmation', 'Hello {!! $receiver_name !!},<br /><br />This email is confirmation that you are now registered at our helpdesk.<br /><br /><strong>Registered Email:</strong> {!! $new_user_email !!}<br /><strong>Password:</strong> {!! $user_password !!}<br /><br />You can visit the helpdesk to browse articles and contact us at any time: {!! $system_link !!}<br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'common-tmeplates'),
(2, 'template-reset-password-link', '1', 8, 'Reset your Password', 'Hello {!! $receiver_name !!},<br /><br />You asked to reset your password. To do so, please click this link:<br />{!! $password_reset_link !!}<br /><br />This will let you change your password to something new. If you did not ask for this, do not worry, we will keep your password safe.<br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'common-tmeplates'),
(3, 'template-new-password', '1', 15, 'Password changed', 'Hello {!! $receiver_name !!},<br /><br />Your password is successfully changed.Your new password is : {!! $user_password !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'common-tmeplates'),
(4, 'template-register-confirmation-with-activation-link', '1', 11, 'Verify your email address', 'Hello {!! $receiver_name !!},<br /><br />This email is confirmation that you are now registered at our helpdesk.<br /><br /><strong>Registered Email:</strong> {!! $new_user_email !!}<br /><br />Please click on the below link to activate your account and Login to the system<br />{!! $account_activation_link !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'common-tmeplates'),
(5, 'template-ticket-checking-wihtout-login-link', '1', 2, 'Check your Ticket', 'Hello {!! $receiver_name !!},<br /><br />Click the link below to view your requested ticket<br />{!! $ticket_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'client-templates'),
(6, 'template-ticket-creation-acknowledgement-client-by-client', '0', 4, '', 'Hello {!! $receiver_name !!}<br /><br />Thank you for contacting us. This is an automated response confirming the receipt of your ticket. Our team will get back to you as soon as possible. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br /><strong>Ticket ID:</strong> {!! $ticket_number !!} <br /><br />{!! $department_signature !!}<br />You can check the status of or update this ticket online at: {!! $system_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'client-templates'),
(7, 'template-ticket-status-update-client', '1', 21, '', 'Hello {!! $receiver_name !!},<br /><br />This email is regarding your ticket with ID: {!! $ticket_number !!}.<br />{!! $message_content !!}<br />If you are not satisfied please respond to the ticket here {!! $ticket_link !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'client-templates'),
(8, 'template-ticket-assignment-notice-to-client', '0', 1, '', 'Hello {!! $receiver_name !!},<br /><br />Your ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. They will contact you soon.<br /><br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'client-templates'),
(9, 'template-ticket-reply-to-client-by-agent', '0', 9, '', '{!! $message_content !!}<br /><br /><strong>Ticket Details</strong><strong>Ticket ID:</strong> {!! $ticket_number !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />{!! $agent_signature !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'client-templates'),
(10, 'template-ticket-assigment-notice-to-team-client', '1', 14, '', '<p>Hello {!! $receiver_name !!},<br /><br />Your ticket with ID: {!! $ticket_number !!} has been assigned to our {!! $assigned_team_name !!} team. They will contact you soon.<br /><br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}</p>', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'client-templates'),
(11, 'template-ticket-assignment-notice-to-assigned-agent', '0', 1, '', '<p>Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br />Has been assigned to you by {!! $activity_by !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You<br />Kind Regards,<br />{!! $system_from !!}</p>', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(12, 'template-ticket-reply-to-assigned-agents-by-client', '0', 10, '', 'Hello {!! $receiver_name !!},<br /><br />Client has made a new reply on their ticket which is assigned to you.<br /><br /><strong>Ticket ID:</strong>  {!! $ticket_number !!}<br /><strong>Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(13, 'template-response-voilate-escalation-to-assigned-agent', '1', 12, 'Response Time SLA Violate', 'Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket assigned to you. The first response was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(14, 'template-resolve-voilate-escalation-to-assigned-agent', '1', 13, 'Resolve Time SLA Violate', 'Hello {!! $receiver_name !!},<br /><br />Ticket which is assigened to you has not been resolved within the SLA time period. The ticket was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(15, 'template-internal-change-to-assigned-agent', '1', 16, '', 'Hello {!! $receiver_name !!},<br /><br />This message is regarding your ticket with ticket ID {!! $ticket_number !!}.<br />{!! $message_content !!}.<br />By {!! $activity_by !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank you<br />Kind regards,<br />{!! $system_from !!}<br />', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(16, 'template-response-time-approach-to-assigned-agents', '1', 17, 'Response Time SLA Approaching', 'Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket assigned to you. The first response should happen on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(17, 'template-resolve-time-approach-to-assigned-agents', '1', 18, 'Resolution Time SLA Approaching', 'Hello {!! $receiver_name !!},<br /><br />Ticket has not been resolved within the SLA time period. The ticket has to resolve on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: ticket_title<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(18, 'template-ticket-status-update-assign-agent', '1', 21, '', 'Hello {!! $receiver_name !!},<br /><br />This email is regarding ticket {!! $ticket_number !!} which is assigned to you.<br />{!! $message_content !!}<br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(19, 'template-ticket-reply-to-assigned-agents-by-agent', '0', 9, '', 'Hello {!! $receiver_name !!},<br /><br />A reply has been made to ticket assigned to you with ID: {!! $ticket_number !!} by {!! $activity_by !!} in our helpdesk system.<br /><strong>Reply content</strong><br />{!! $message_content !!}<br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br />{!! $agent_signature !!}<br />Thanks<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'assigend-agent-templates'),
(20, 'template-new-ticket-creation-notice-agents', '0', 5, '', 'Hello {!! $receiver_name !!},<br /><br />New ticket with ID: {!! $ticket_number !!} has been created in our helpdesk.<br /><br /><strong>Client Details</strong><br /><strong>Name:</strong> {!! $client_name !!}<br /><strong>E-mail:</strong> {!! $client_email !!}<br /><br /><strong>Message</strong><br />{!! $message_content !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(21, 'template-ticket-reply-to-agents-by-client', '0', 10, '', 'Hello {!! $receiver_name !!},<br /><br />Client has made a new reply on their ticket in our helpdesk system.<br /><br /><strong>Ticket ID:</strong>  {!! $ticket_number !!}<br /><strong>Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(22, 'template-response-voilate-escalation-to-agent', '1', 12, 'Response Time SLA Violate', 'Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket. The first response was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(23, 'template-resolve-voilate-escalation-to-agent', '1', 13, 'Resolve Time SLA Violate', 'Hello {!! $receiver_name !!},<br /><br />Ticket has not been resolved within the SLA time period. The ticket was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(24, 'template-ticket-assigment-notice-to-team', '1', 14, '', '<p>Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br /><br />Has been assigned to your team <b>{!! $assigned_team_name !!}</b> by {!! $activity_by !!}<br /><br />Follow the link below to check and reply on the ticket.<br />{!! $ticket_link !!}<br /><br />Thank You<br />Kind Regards,<br />{!! $system_from !!}</p>', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(25, 'template-internal-change-to-agent', '1', 16, '', 'Hello {!! $receiver_name !!},<br /><br />This message is regarding ticket with ticket ID {!! $ticket_number !!}.<br />{!! $message_content !!}.<br />By {!! $activity_by !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank you<br />Kind regards,<br />{!! $system_from !!}<br />', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(26, 'template-response-time-approach-to-agents', '1', 17, 'Response Time SLA Approaching', 'Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket. The first response should happen on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(27, 'template-resolve-time-approach-to-agents', '1', 18, 'Resolution Time SLA Approaching', 'Hello {!! $receiver_name !!},<br /><br />Ticket has not been resolved within the SLA time period. The ticket has to resolve on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: ticket_title<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(28, 'template-new-user-entry-notice', '1', 19, 'New user has created', 'Hello {!! $receiver_name !!},<br /><br />A new user has been registered in our helpdesk system.<br /><br /><strong>User Details</strong><br /><strong>Name: </strong>{!! $new_user_name !!}<br /><strong>Email</strong><strong>:</strong> {!! $new_user_email !!}<br /><br />You can check or update the user\'s complete profile by clicking the link below<br />{!! $user_profile_link !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(29, 'template-non-assign-escalation-notice', '1', 20, 'Non Assign Ticket', '', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(30, 'template-ticket-status-update-agent', '1', 21, '', 'Hello {!! $receiver_name !!},<br /><br />This email is regarding ticket {!! $ticket_number !!}.<br />{!! $message_content !!}<br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(31, 'template-ticket-assignment-notice-to-agent', '0', 1, '', 'Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br />Has been assigned to {!! $agent_name !!} by Demo Admin<br /><br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You<br /><br />Kind Regards,<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(32, 'template-ticket-reply-to-agents-by-agent', '0', 9, '', 'Hello {!! $receiver_name !!},<br /><br />An agent has replied to ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br /><strong>Reply content</strong><br />{!! $message_content !!}<br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br />{!! $agent_signature !!}<br />Thanks.<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates'),
(33, 'template-ticket-approve', '0', 22, '', 'Hello {!! $agent_name !!},<br /><br />An agent has applied for approval to ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br /><br /><strong>Approve link</strong><br />{!! $approve_url !!}<br /><strong>Deny link</strong><br />{!! $deny_url !!}<br /><br />Thanks.<br />{!! $system_from !!}', '', 1, '2017-10-03 20:23:46', '2017-10-03 20:23:46', 'agent-templates'),
(34, 'invoice', '0', 23, '', 'Hello {!! $client_name !!},<br /><br />An invoice has generated for ticket with ID: {!! $ticket_number !!}.<br /><br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br /><br /><strong>Total time spend</strong><br />{!! $total_time !!} Hours<br /><br /><strong>Cost</strong><br />{!! $currency !!}{!! $cost !!}<br /><br /><strong>Billing Date</strong><br />{!! $bill_date !!}<br /><br />Thanks.<br />{!! $system_from !!}', '', 1, '2017-10-03 20:23:46', '2017-10-03 20:23:46', 'client-templates'),
(35, 'template-task-reminder', '1', 24, 'Task Alert', '<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} is due on {!! $task_end_date !!}, </p><p> </p><p>Regards, </p><p> </p>', '', 1, NULL, NULL, 'common-tmeplates'),
(36, 'template-task-created', '1', 25, 'Task created', '<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} has been created,  is due on {!! $task_end_date !!}, </p><p> </p><p>Regards, </p><p> </p>', '', 1, NULL, NULL, 'common-tmeplates'),
(37, 'template-task-update', '1', 26, 'Task Update', '<p>Hello!<br /><br />Your task {!! $task_name !!} has been updated by {!! $updated_by !!}.</p><p>Regards, </p><p> </p>', '', 1, NULL, NULL, 'common-tmeplates'),
(38, 'template-task-status', '1', 27, 'Task status update', '<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} status has been {!! $status !!}.</p><p> </p><p>Regards, </p><p> </p>', '', 1, NULL, NULL, 'common-tmeplates'),
(39, 'template-task-assigned', '1', 28, 'Task Assigned', '<p>Hello {!! $receiver_name !!},<br /><br />Your have been assigned to task {!! $task_name !!} by {!! $created_by !!}, is due on {!! $task_end_date !!}, </p><p> </p><p>Regards, </p><p> </p>', '', 1, NULL, NULL, 'common-tmeplates');

-- --------------------------------------------------------

--
-- Table structure for table `template_sets`
--

DROP TABLE IF EXISTS `template_sets`;
CREATE TABLE IF NOT EXISTS `template_sets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_language` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'en',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `template_sets`
--

INSERT INTO `template_sets` (`id`, `name`, `active`, `created_at`, `updated_at`, `template_language`) VALUES
(1, 'default', 1, '2017-07-06 11:40:03', '2017-07-06 11:40:03', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `template_types`
--

DROP TABLE IF EXISTS `template_types`;
CREATE TABLE IF NOT EXISTS `template_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'assign-ticket', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(2, 'check-ticket', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(3, 'close-ticket', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(4, 'create-ticket', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(5, 'create-ticket-agent', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(6, 'create-ticket-by-agent', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(7, 'registration-notification', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(8, 'reset-password', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(9, 'ticket-reply', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(10, 'ticket-reply-agent', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(11, 'registration', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(12, 'response_due_violate', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(13, 'resolve_due_violate', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(14, 'team_assign_ticket', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(15, 'reset_new_password', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(16, 'internal_change', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(17, 'response_due_approach', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(18, 'resolve_due_approach', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(19, 'new-user', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(20, 'no_assign_message', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(21, 'status-update', '2017-07-06 11:40:03', '2017-07-06 11:40:03'),
(22, 'approve-ticket', '2017-10-03 20:23:46', '2017-10-03 20:23:46'),
(23, 'invoice', '2017-10-03 20:23:46', '2017-10-03 20:23:46'),
(24, 'task-reminder', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(25, 'task-create', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(26, 'task-update', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(27, 'task-status-update', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(28, 'task-assigned', '2017-11-21 01:11:47', '2017-11-21 01:11:47');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `dept_id` int(10) UNSIGNED DEFAULT NULL,
  `team_id` int(10) UNSIGNED DEFAULT NULL,
  `priority_id` int(10) UNSIGNED DEFAULT NULL,
  `sla` int(10) UNSIGNED DEFAULT NULL,
  `help_topic_id` int(10) UNSIGNED DEFAULT NULL,
  `status` int(10) UNSIGNED DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `ratingreply` tinyint(1) NOT NULL,
  `flags` int(11) NOT NULL,
  `ip_address` int(11) NOT NULL,
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `lock_by` int(11) NOT NULL,
  `lock_at` datetime DEFAULT NULL,
  `source` int(10) UNSIGNED DEFAULT NULL,
  `isoverdue` int(11) NOT NULL,
  `reopened` int(11) NOT NULL,
  `isanswered` int(11) NOT NULL,
  `html` int(11) NOT NULL,
  `is_deleted` int(11) NOT NULL,
  `closed` int(11) NOT NULL,
  `is_transferred` tinyint(1) NOT NULL,
  `transferred_at` datetime NOT NULL,
  `reopened_at` datetime DEFAULT NULL,
  `duedate` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `first_response_time` datetime DEFAULT NULL,
  `approval` int(11) NOT NULL,
  `follow_up` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resolution_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_response_sla` int(11) NOT NULL,
  `is_resolution_sla` int(11) NOT NULL,
  `type` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `dept_id` (`dept_id`),
  KEY `team_id` (`team_id`),
  KEY `priority_id` (`priority_id`),
  KEY `sla` (`sla`),
  KEY `help_topic_id` (`help_topic_id`),
  KEY `status` (`status`),
  KEY `assigned_to` (`assigned_to`),
  KEY `source` (`source`),
  KEY `ticket_type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `user_id`, `dept_id`, `team_id`, `priority_id`, `sla`, `help_topic_id`, `status`, `rating`, `ratingreply`, `flags`, `ip_address`, `assigned_to`, `lock_by`, `lock_at`, `source`, `isoverdue`, `reopened`, `isanswered`, `html`, `is_deleted`, `closed`, `is_transferred`, `transferred_at`, `reopened_at`, `duedate`, `closed_at`, `last_message_at`, `first_response_time`, `approval`, `follow_up`, `created_at`, `updated_at`, `resolution_time`, `is_response_sla`, `is_resolution_sla`, `type`) VALUES
(1, 'AAAA-0000-0000', 11, 1, NULL, 1, 1, 1, 2, 0, 0, 0, 0, 2, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 14:05:41', '2017-03-30 04:54:50', NULL, '2017-03-30 04:28:39', 0, 0, '2017-03-29 22:35:41', '2017-03-29 23:24:50', '50', 1, 1, 1),
(2, 'AAAA-0000-0001', 18, 1, NULL, 1, 1, 1, 3, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 14:58:59', '2017-03-30 05:03:34', NULL, '2017-03-30 05:00:51', 0, 0, '2017-03-29 23:28:59', '2017-03-29 23:33:35', '5', 1, 1, 1),
(3, 'AAAA-0000-0002', 19, 2, NULL, 1, 1, 2, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:05:17', NULL, NULL, '2017-03-30 05:07:19', 0, 0, '2017-03-29 23:35:17', '2017-03-29 23:37:19', NULL, 1, 0, 1),
(4, 'AAAA-0000-0003', 15, 3, NULL, 1, 1, 3, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 10:10:29', NULL, NULL, NULL, 0, 0, '2017-03-29 23:40:29', '2017-03-29 23:40:29', NULL, 0, 0, 1),
(5, 'AAAA-0000-0004', 16, 2, NULL, 1, 1, 2, 2, 0, 0, 0, 0, 4, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:12:59', '2017-03-30 05:15:53', NULL, '2017-03-30 05:15:27', 0, 0, '2017-03-29 23:42:59', '2017-03-29 23:45:53', '3', 1, 1, 1),
(6, 'AAAA-0000-0005', 16, 2, NULL, 1, 1, 1, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 10:19:12', NULL, NULL, NULL, 0, 0, '2017-03-29 23:49:12', '2017-03-29 23:49:12', NULL, 0, 0, 1),
(7, 'AAAA-0000-0006', 10, 1, NULL, 1, 1, 1, 7, 0, 0, 0, 0, 5, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:21:20', NULL, NULL, '2017-03-30 05:23:41', 0, 0, '2017-03-29 23:51:20', '2017-03-29 23:54:06', NULL, 1, 0, 1),
(8, 'AAAA-0000-0007', 20, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:25:07', NULL, NULL, '2017-03-30 05:28:04', 0, 0, '2017-03-29 23:55:07', '2017-03-29 23:58:04', NULL, 1, 0, 1),
(14, 'AAAA-0000-0010', 16, 2, NULL, 1, 1, 2, 1, 0, 0, 0, 0, 4, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:53:35', NULL, NULL, '2017-03-30 05:58:46', 0, 0, '2017-03-30 00:23:35', '2017-03-30 00:28:46', NULL, 1, 0, 1),
(10, 'AAAA-0000-0008', 23, 2, NULL, 1, 1, 2, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:36:31', NULL, NULL, '2017-03-30 05:39:18', 0, 0, '2017-03-30 00:06:31', '2017-03-30 00:09:18', NULL, 1, 0, 1),
(13, 'AAAA-0000-0009', 12, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 15:48:34', NULL, NULL, '2017-03-30 05:50:15', 0, 0, '2017-03-30 00:18:34', '2017-03-30 00:20:15', NULL, 1, 0, 1),
(15, 'AAAA-0000-0011', 16, 2, NULL, 1, 1, 1, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:00:20', NULL, NULL, NULL, 0, 0, '2017-03-30 00:30:20', '2017-03-30 00:30:20', NULL, 0, 0, 1),
(16, 'AAAA-0000-0012', 16, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:01:45', NULL, NULL, '2017-03-30 06:03:34', 0, 0, '2017-03-30 00:31:45', '2017-03-30 00:33:34', NULL, 1, 0, 1),
(17, 'AAAA-0000-0013', 17, 1, NULL, 1, 1, 1, 2, 0, 0, 0, 0, 6, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:04:51', '2017-03-30 06:06:55', NULL, '2017-03-30 06:06:38', 0, 0, '2017-03-30 00:34:51', '2017-03-30 00:36:55', '3', 1, 1, 1),
(18, 'AAAA-0000-0014', 11, 2, NULL, 1, 1, 2, 3, 0, 0, 0, 0, 2, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:10:16', '2017-03-30 06:12:29', NULL, '2017-03-30 06:12:15', 0, 0, '2017-03-30 00:40:16', '2017-03-30 00:42:29', '3', 1, 1, 1),
(19, 'AAAA-0000-0015', 11, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 2, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:13:36', NULL, NULL, '2017-03-30 06:15:20', 0, 0, '2017-03-30 00:43:36', '2017-03-30 00:45:20', NULL, 1, 0, 1),
(20, 'AAAA-0000-0016', 11, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:16:02', NULL, NULL, NULL, 0, 0, '2017-03-30 00:46:02', '2017-03-30 00:46:03', NULL, 0, 0, 1),
(21, 'AAAA-0000-0017', 18, 3, NULL, 1, 1, 3, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:19:18', NULL, NULL, NULL, 0, 0, '2017-03-30 00:49:18', '2017-03-30 00:49:18', NULL, 0, 0, 1),
(22, 'AAAA-0000-0018', 18, 3, NULL, 1, 1, 3, 3, 0, 0, 0, 0, 8, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:22:12', '2017-03-30 06:23:29', NULL, '2017-03-30 06:23:03', 0, 0, '2017-03-30 00:52:12', '2017-03-30 00:53:29', '2', 1, 1, 1),
(23, 'AAAA-0000-0019', 19, 1, NULL, 1, 1, 1, 7, 0, 0, 0, 0, 2, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:24:30', NULL, NULL, '2017-03-30 06:26:14', 0, 0, '2017-03-30 00:54:30', '2017-03-30 00:56:30', NULL, 1, 0, 1),
(24, 'AAAA-0000-0020', 19, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:27:29', NULL, NULL, NULL, 0, 0, '2017-03-30 00:57:29', '2017-03-30 00:57:29', NULL, 0, 0, 1),
(25, 'AAAA-0000-0021', 10, 3, NULL, 1, 1, 3, 5, 0, 0, 0, 0, 5, 0, NULL, 1, 0, 0, 1, 0, 1, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:29:04', NULL, NULL, '2017-03-30 06:30:31', 0, 0, '2017-03-30 00:59:04', '2017-03-30 01:00:47', NULL, 1, 0, 1),
(26, 'AAAA-0000-0022', 10, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:32:30', NULL, NULL, NULL, 0, 0, '2017-03-30 01:02:30', '2017-03-30 01:02:30', NULL, 0, 0, 1),
(27, 'AAAA-0000-0023', 23, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 5, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:34:50', NULL, NULL, '2017-03-30 06:35:56', 0, 0, '2017-03-30 01:04:50', '2017-03-30 01:05:56', NULL, 1, 0, 1),
(28, 'AAAA-0000-0024', 20, 3, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:39:06', NULL, NULL, '2017-03-30 06:39:46', 0, 0, '2017-03-30 01:09:06', '2017-03-30 01:09:46', NULL, 1, 0, 1),
(29, 'AAAA-0000-0025', 9, 1, NULL, 2, 2, 1, 1, 0, 0, 0, 0, 7, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:41:16', NULL, NULL, NULL, 0, 0, '2017-03-30 01:11:16', '2017-10-04 05:47:32', NULL, 0, 0, 1),
(30, 'AAAA-0000-0026', 9, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:43:48', NULL, NULL, '2017-03-30 06:44:30', 0, 0, '2017-03-30 01:13:48', '2017-03-30 01:14:30', NULL, 1, 0, 1),
(31, 'AAAA-0000-0027', 14, 3, NULL, 1, 1, 3, 1, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 11:45:34', NULL, NULL, NULL, 0, 0, '2017-03-30 01:15:34', '2017-03-30 01:15:34', NULL, 0, 0, 1),
(32, 'AAAA-0000-0028', 14, 1, NULL, 1, 1, 1, 3, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:46:20', '2017-03-30 06:47:09', NULL, '2017-03-30 06:46:54', 0, 0, '2017-03-30 01:16:20', '2017-03-30 01:17:09', '1', 1, 1, 1),
(33, 'AAAA-0000-0029', 21, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:48:01', NULL, NULL, '2017-03-30 06:48:20', 0, 0, '2017-03-30 01:18:01', '2017-03-30 01:18:20', NULL, 1, 0, 1),
(34, 'AAAA-0000-0030', 21, 2, NULL, 1, 1, 2, 3, 0, 0, 0, 0, 1, 0, NULL, 1, 0, 0, 1, 0, 0, 1, 0, '0000-00-00 00:00:00', NULL, '2017-03-30 16:49:16', '2017-03-30 06:50:14', NULL, '2017-03-30 06:49:53', 0, 0, '2017-03-30 01:19:16', '2017-03-30 01:20:14', '1', 1, 1, 1),
(35, 'AAAA-0000-0031', 24, 1, NULL, 1, 1, 1, 1, 0, 0, 0, 0, NULL, 0, NULL, 2, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL, '2017-08-31 12:20:13', NULL, NULL, NULL, 0, 0, '2017-08-31 05:20:13', '2017-08-31 05:20:13', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachment`
--

DROP TABLE IF EXISTS `ticket_attachment`;
CREATE TABLE IF NOT EXISTS `ticket_attachment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thread_id` int(10) UNSIGNED DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `poster` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` mediumblob,
  `driver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_attachment`
--

INSERT INTO `ticket_attachment` (`id`, `name`, `thread_id`, `size`, `type`, `poster`, `created_at`, `updated_at`, `file`, `driver`, `path`) VALUES
(1, 'order.png', 55, '77355', 'image/png', 'ATTACHMENT', '2017-03-30 00:18:34', '2017-03-30 00:18:34', NULL, 'local', 'C:\\wamp64\\www\\faveo-helpdesk-pro-master-data\\storage\\app/private\\2017\\3\\30'),
(2, 'TiXH0_order.png', 61, '89383', 'image/png', 'ATTACHMENT', '2017-03-30 00:23:35', '2017-03-30 00:23:35', NULL, 'local', 'C:\\wamp64\\www\\faveo-helpdesk-pro-master-data\\storage\\app/private\\2017\\3\\30'),
(3, 'D37VR_order.png', 66, '89383', 'image/png', 'ATTACHMENT', '2017-03-30 00:30:20', '2017-03-30 00:30:20', NULL, 'local', 'C:\\wamp64\\www\\faveo-helpdesk-pro-master-data\\storage\\app/private\\2017\\3\\30');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_collaborator`
--

DROP TABLE IF EXISTS `ticket_collaborator`;
CREATE TABLE IF NOT EXISTS `ticket_collaborator` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `isactive` tinyint(1) NOT NULL,
  `ticket_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_form_data`
--

DROP TABLE IF EXISTS `ticket_form_data`;
CREATE TABLE IF NOT EXISTS `ticket_form_data` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) UNSIGNED DEFAULT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_priority`
--

DROP TABLE IF EXISTS `ticket_priority`;
CREATE TABLE IF NOT EXISTS `ticket_priority` (
  `priority_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `priority` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_urgency` tinyint(1) NOT NULL,
  `ispublic` tinyint(1) NOT NULL,
  `is_default` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`priority_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_priority`
--

INSERT INTO `ticket_priority` (`priority_id`, `priority`, `status`, `priority_desc`, `priority_color`, `priority_urgency`, `ispublic`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Low', '1', 'Low', '#00a65a', 4, 1, '', NULL, NULL),
(2, 'Normal', '1', 'Normal', '#00bfef', 3, 1, '1', NULL, NULL),
(3, 'High', '1', 'High', '#f39c11', 2, 1, '', NULL, NULL),
(4, 'Emergency', '1', 'Emergency', '#dd4b38', 1, 1, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_source`
--

DROP TABLE IF EXISTS `ticket_source`;
CREATE TABLE IF NOT EXISTS `ticket_source` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `css_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_source`
--

INSERT INTO `ticket_source` (`id`, `name`, `value`, `css_class`, `description`, `location`) VALUES
(1, 'web', 'Web', 'fa fa-internet-explorer', NULL, ''),
(2, 'email', 'E-mail', 'fa fa-envelope', NULL, ''),
(3, 'agent', 'Agent Panel', 'fa fa-envelope', NULL, ''),
(4, 'facebook', 'Facebook', 'fa fa-facebook', NULL, ''),
(5, 'twitter', 'Twitter', 'fa fa-twitter', NULL, ''),
(6, 'call', 'Call', 'fa fa-phone', NULL, ''),
(7, 'chat', 'Chat', 'fa fa-comment', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status`
--

DROP TABLE IF EXISTS `ticket_status`;
CREATE TABLE IF NOT EXISTS `ticket_status` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `visibility_for_client` tinyint(1) NOT NULL,
  `allow_client` tinyint(1) NOT NULL,
  `visibility_for_agent` tinyint(1) NOT NULL,
  `purpose_of_status` int(11) NOT NULL,
  `secondary_status` int(11) DEFAULT NULL,
  `send_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `halt_sla` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_status`
--

INSERT INTO `ticket_status` (`id`, `name`, `message`, `created_at`, `updated_at`, `visibility_for_client`, `allow_client`, `visibility_for_agent`, `purpose_of_status`, `secondary_status`, `send_email`, `halt_sla`, `order`, `icon`, `icon_color`, `default`) VALUES
(1, 'Open', 'Ticket have been Reopened by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 1, NULL, '{\"client\":\"1\",\"admin\":\"0\",\"assigned_agent_team\":\"1\"}', 0, 1, 'fa fa-folder-open-o', '#ff0000', 1),
(2, 'Resolved', 'Ticket have been Resolved by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 2, NULL, '{\"client\":\"1\",\"admin\":\"0\",\"assigned_agent_team\":\"1\"}', 0, 2, 'fa fa-check-circle-o', '#008000', NULL),
(3, 'Closed', 'Ticket have been Closed by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 2, NULL, '{\"client\":\"1\",\"admin\":\"0\",\"assigned_agent_team\":\"1\"}', 0, 3, 'fa fa-check', '#008000', 1),
(4, 'Archived', 'Ticket have been Archived by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 3, NULL, '{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}', 0, 4, 'fa fa-trash', '#ff0000', NULL),
(5, 'Deleted', 'Ticket have been Deleted by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 4, NULL, '{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}', 0, 5, 'fa fa-trash', '#ff0000', 1),
(6, 'Unverified Status', 'Approval requested by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 1, NULL, '{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}', 0, 6, 'fa fa-bell', '#f1ac0b', 0),
(7, 'Request for close', 'Approval requested by {!!$user!!}', '2017-03-29 05:23:24', '2017-03-29 05:23:24', 1, 1, 1, 5, NULL, '{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}', 0, 7, 'fa fa-bell', '#f1ac0b', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status_type`
--

DROP TABLE IF EXISTS `ticket_status_type`;
CREATE TABLE IF NOT EXISTS `ticket_status_type` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_status_type`
--

INSERT INTO `ticket_status_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'open', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(2, 'closed', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(3, 'archieved', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(4, 'deleted', '2017-03-29 05:23:24', '2017-03-29 05:23:24'),
(5, 'approval', '2017-03-29 05:23:24', '2017-03-29 05:23:24');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_thread`
--

DROP TABLE IF EXISTS `ticket_thread`;
CREATE TABLE IF NOT EXISTS `ticket_thread` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `thread_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poster` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `source` int(10) UNSIGNED DEFAULT NULL,
  `reply_rating` int(11) NOT NULL,
  `rating_count` int(11) NOT NULL,
  `is_internal` tinyint(1) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` longblob,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `response_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id_2` (`ticket_id`),
  KEY `user_id` (`user_id`),
  KEY `source` (`source`)
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_thread`
--

INSERT INTO `ticket_thread` (`id`, `ticket_id`, `user_id`, `thread_type`, `poster`, `source`, `reply_rating`, `rating_count`, `is_internal`, `title`, `body`, `format`, `ip_address`, `created_at`, `updated_at`, `response_time`) VALUES
(1, 1, 11, NULL, 'client', NULL, 0, 0, 0, 'How can I change my shipping address?', 0x48656c6c6f207465616d2c266e6273703b3c62723e3c62723e266e6273703b20266e6273703b20266e6273703b20266e6273703b2049276d206e6f742061626c6520746f20666967757265206f7574266e6273703b686f7720746f206368616e6765206d79207368697070696e672061646472657373206174266e6273703b3c6120687265663d22687474703a2f2f6573686f7070696e672e636f6d223e687474703a2f2f6573686f7070696e672e636f6d3c2f613e2e2043616e20796f7520706c656173652068656c70206d65206f75742077697468207468652073616d653f3c62723e3c62723e5468616e6b696e6720796f752c3c62723e416d72757468613c62723e3c62723e, '', '', '2017-03-29 22:35:41', '2017-03-29 22:35:41', NULL),
(2, 1, 11, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031343a33353a34312c, '', '', '2017-03-29 22:35:41', '2017-03-29 22:35:41', NULL),
(3, 1, 2, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031393a33353a34312c, '', '', '2017-03-29 22:58:39', '2017-03-29 22:58:39', NULL),
(4, 1, 2, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e416e647265773c2f623e2c, '', '', '2017-03-29 22:58:39', '2017-03-29 22:58:39', NULL),
(5, 1, 2, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416d72757468612c3c2f703e0d0a0d0a3c703e5468616e6b20796f7520666f7220636f6e74616374696e672075732e2057652077696c6c206265206261636b20736f6f6e2077697468206120736f6c7574696f6e20666f7220746865206973737565207468617420796f752061726520666163696e672e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f752c3c2f703e0d0a0d0a3c703e416e647265773c6272202f3e0d0a266e6273703b3c2f703e0d0a, '', '', '2017-03-29 22:58:39', '2017-03-29 22:58:39', '23'),
(6, 1, 2, NULL, 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416d72757468612c3c2f703e0d0a0d0a3c703e42792064656661756c742c20746865206c6173742075736564207368697070696e6720616464726573732077696c6c20626520736176656420696e746f20796f75722053616d706c652053746f7265206163636f756e742e205768656e20796f752061726520636865636b696e67206f757420796f7572206f726465722c207468652064656661756c74207368697070696e6720616464726573732077696c6c20626520646973706c6179656420616e6420796f75206861766520746865206f7074696f6e20746f20616d656e6420697420696620796f75206e65656420746f2e3c2f703e0d0a0d0a3c703e5468616e6b20796f753c2f703e0d0a, '', '', '2017-03-29 23:24:15', '2017-03-29 23:24:15', '49'),
(7, 1, 11, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e5265736f6c7665643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-29 23:24:50', '2017-03-29 23:24:50', NULL),
(8, 2, 18, NULL, 'client', NULL, 0, 0, 0, 'What do you mean by points? How do I earn it?', 0x4861692c3c62723e3c62723e4d61792049206b6e6f772074686520667572746865722064657461696c732061626f75742077686174206973206d65616e7420627920706f696e747320616e6420686f7720776f756c642049206561726e2069743f3c62723e3c62723e526567617264732c3c62723e50657465723c62723e3c62723e, '', '', '2017-03-29 23:28:59', '2017-03-29 23:28:59', NULL),
(9, 2, 18, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a32383a35392c, '', '', '2017-03-29 23:28:59', '2017-03-29 23:28:59', NULL),
(10, 2, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032303a32383a35392c, '', '', '2017-03-29 23:30:51', '2017-03-29 23:30:51', NULL),
(11, 2, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-29 23:30:51', '2017-03-29 23:30:51', NULL),
(12, 2, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2050657465722c3c2f703e0d0a0d0a3c703e5468616e6b20796f7520666f7220636f6e74616374696e672075732e2057652077696c6c206265206261636b20736f6f6e2077697468206120736f6c7574696f6e20666f7220746865206973737565207468617420796f752061726520666163696e672e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-29 23:30:51', '2017-03-29 23:30:51', '2'),
(13, 2, 1, NULL, 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2050657465722c3c6272202f3e0d0a3c6272202f3e0d0a4265636175736520796f752061726520696d706f7274616e7420746f2075732c2077652077616e7420746f206b6e6f77207768617420796f75207468696e6b2061626f7574207468652070726f64756374732e20417320616e2061646465642076616c75652c2065766572792074696d6520796f752072617465207468652070726f647563747320796f75206561726e20706f696e747320776869636820676f20737472616967687420746f20796f7572206163636f756e742e203120706f696e7420697320616464656420746f20796f7572206163636f756e7420666f7220657665727920726576696577207468617420796f7520676976652e20596f752077696c6c206e6565642074686f736520706f696e747320696e206f7264657220746f2072656465656d207468652073616d706c652070726f64756374732e20536f206b65657020726174696e67207468652070726f647563747320746f206b656570206561726e696e6720706f696e7473213c6272202f3e0d0a3c6272202f3e0d0a5468616e6b20796f753c2f703e0d0a, '', '', '2017-03-29 23:32:21', '2017-03-29 23:32:21', '4'),
(14, 2, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e436c6f7365643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-29 23:33:35', '2017-03-29 23:33:35', NULL),
(15, 3, 19, NULL, 'client', NULL, 0, 0, 0, 'Regarding the checkout limits', 0x3c68353e576879206973207468657265206120636865636b6f7574206c696d69743f202f20576861742061726520616c6c2074686520636865636b6f7574206c696d6974733f3c2f68353e, '', '', '2017-03-29 23:35:17', '2017-03-29 23:35:17', NULL),
(16, 3, 19, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a33353a31372c, '', '', '2017-03-29 23:35:17', '2017-03-29 23:35:17', NULL),
(17, 3, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032303a33353a31372c, '', '', '2017-03-29 23:37:19', '2017-03-29 23:37:19', NULL),
(18, 3, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-29 23:37:19', '2017-03-29 23:37:19', NULL),
(19, 3, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2053616d75616c2c3c2f703e0d0a0d0a3c703e53616d706c652053746f7265206973206120706f70756c61722073706f7420616e642067657473206c6f7473206f662073686f707065727320617420612074696d652e205468657365206c696d6974732061726520696e20706c61636520746f206d616b6520737572652065766572796f6e6520686173206120676f6f642074696d6520747279696e6720616e642070757263686173696e672074686569722070726f64756374732e20536f2e2e2e3c2f703e0d0a0d0a3c703e2d2045616368206163636f756e7420697320656e7469746c656420746f206f6e6c79206f6e6520283129206f72646572206f6620757020746f203420646966666572656e742073616d706c657320706572206461793c2f703e0d0a0d0a3c703e2d2045616368206163636f756e7420697320656e7469746c656420746f206f6e6c79206f6e652028312920726564656d7074696f6e207065722073616d706c652e3c2f703e0d0a0d0a3c703e2d20596f7572206163636f756e74206d75737420686176652073756666696369656e7420706f696e7473206265666f726520796f752063616e20636865636b6f7574207468652073616d706c652070726f64756374732e3c2f703e0d0a0d0a3c703e2d204b696e646c7920636c65617220616c6c2070656e64696e67207061796d656e7473206265666f726520616e6f7468657220636865636b6f75742e3c2f703e0d0a0d0a3c703e266e6273703b0d0a3c703e3c6272202f3e0d0a5468616e6b696e6720796f753c2f703e0d0a3c2f703e0d0a, '', '', '2017-03-29 23:37:20', '2017-03-29 23:37:20', '3'),
(20, 4, 15, NULL, 'client', NULL, 0, 0, 0, 'Delivery duration', 0x3c68353e486f77206c6f6e672077696c6c2069742074616b6520666f72206d79206f7264657220746f206172726976652061667465722049206d616b65207061796d656e743f3c2f68353e3c62723e, '', '', '2017-03-29 23:40:29', '2017-03-29 23:40:29', NULL),
(21, 4, 15, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a34303a32392c, '', '', '2017-03-29 23:40:29', '2017-03-29 23:40:29', NULL),
(22, 5, 16, NULL, 'client', NULL, 0, 0, 0, 'Redaring the delivery fee', 0x3c68353e576861742069732074686520616363756d756c617465642064656c69766572792066656520666f723f20486f77206d756368206973207468652068616e646c696e67206665653f3c2f68353e, '', '', '2017-03-29 23:42:59', '2017-03-29 23:42:59', NULL),
(23, 5, 16, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a34323a35392c, '', '', '2017-03-29 23:42:59', '2017-03-29 23:42:59', NULL),
(24, 5, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e4173686f6b3c2f623e2c, '', '', '2017-03-29 23:44:26', '2017-03-29 23:44:26', NULL),
(25, 5, 4, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032303a34323a35392c, '', '', '2017-03-29 23:45:27', '2017-03-29 23:45:27', NULL),
(26, 5, 4, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f204d6164616e2c3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e54686520666c61742d726174652068616e646c696e6720666565206973205324352e393920616e64206974206973206f6e6c79206170706c696361626c6520746f206e6f726d616c2073616d706c65732e20466f7220667265652073616d706c65732c2074686579206172652066756c6c79207061696420666f7220616e6420746865726520617265206e6f206164646974696f6e616c206368617267657320746f2064656c697665722074686520667265652073616d706c65732e3c2f703e0d0a0d0a3c703e48616e646c696e672066656520636f76657273207468652064656c69766572792c206d6174657269616c2c206c61626f757220616e64206c6f6769737469637320636f737420746f20737570706f7274207468652073616d706c696e6720736572766963652e20596f752063616e2072656465656d20757020746f203420646966666572656e742073616d706c657320696e206561636820636865636b6f757420616e64206974206973206c696b656c79207468617420796f752077696c6c2066696e642073616d706c65732062756e646c652077697468206c617267657220737570706c792028757020746f20312d7765656b20737570706c7929206f6e2053616d706c6573746f72652e636f6d2e3c6272202f3e0d0a3c6272202f3e0d0a266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-29 23:45:32', '2017-03-29 23:45:32', '3'),
(27, 5, 4, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e5265736f6c7665643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-29 23:45:53', '2017-03-29 23:45:53', NULL),
(28, 6, 16, NULL, 'client', NULL, 0, 0, 0, 'Remaining account credits', 0x3c68353e486f772063616e204920757365206d792072656d61696e696e67204163636f756e7420437265646974733f3c2f68353e, '', '', '2017-03-29 23:49:12', '2017-03-29 23:49:12', NULL),
(29, 6, 16, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a34393a31322c, '', '', '2017-03-29 23:49:12', '2017-03-29 23:49:12', NULL),
(30, 7, 10, NULL, 'client', NULL, 0, 0, 0, 'Delivery mishap', 0x3c68353e576861742068617070656e732069662074686572652773206265656e20612064656c6976657279206d697368617020746f206d79206f726465723f202844616d61676564206f72206c6f73742064656c6976657279293c2f68353e, '', '', '2017-03-29 23:51:20', '2017-03-29 23:51:20', NULL),
(31, 7, 10, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a35313a32302c, '', '', '2017-03-29 23:51:20', '2017-03-29 23:51:20', NULL),
(32, 7, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e4179657368613c2f623e2c, '', '', '2017-03-29 23:52:36', '2017-03-29 23:52:36', NULL),
(33, 7, 5, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032303a35313a32302c, '', '', '2017-03-29 23:53:41', '2017-03-29 23:53:41', NULL),
(34, 7, 5, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416c697368612c3c2f703e0d0a0d0a3c703e57652074616b652073756368206d617474657273207665727920736572696f75736c7920616e642077696c6c206c6f6f6b20696e746f20696e646976696475616c2063617365732074686f726f7567686c792e20416e792073616d706c6520746861742066616c6c7320756e646572207468652062656c6f772063617465676f726965732073686f756c64206e6f74206265207468726f776e2061776179206265666f72652074616b696e672070686f746f2070726f6f6620616e6420656d61696c696e67207468652070686f746f206f66207468652061666665637465642073616d706c6520616e6420796f757220442e4f202844656c6976657279204f726465722920746f2075732061742068656c704073616d706c6573746f72652e636f6d20286966206170706c696361626c65292e3c2f703e0d0a0d0a3c703e57652072656772657420746f20696e666f726d20796f752074686174206e6f20726566756e64732077696c6c20626520676976656e20666f72206f726465727320746861742066616c6c20756e646572207468652062656c6f772063617465676f726965732e3c2f703e0d0a0d0a3c703e312e20496e20746865206576656e74206f662064616d616765642073616d706c65732072656365697665642c2077652077696c6c20726571756972652070686f746f2070726f6f66206f66207468652061666665637465642073616d706c657320616e6420796f757220442e4f202844656c6976657279204f726465722920696e206f7264657220666f7220757320746f20696e76657374696761746520616e6420726576696577206265666f72652061206465636973696f6e206973206d61646520746f2072652d73656e64207468652073616d706c6520746f20796f75206174206e6f20636f73742c207375626a65637420746f20617661696c6162696c6974792e20496e206c69676874206f6620746869732c20616e792073616d706c6520746861742066616c6c7320696e746f20746869732063617465676f72792073686f756c64206e6f74206265207468726f776e2061776179206265666f72652074616b696e672070686f746f2070726f6f6620616e6420656d61696c696e67207468652070686f746f20746f2075732061742068656c704073616d706c6573746f72652e636f6d3c2f703e0d0a0d0a3c703e322e20496e20746865206576656e74206f66206c6f7374206d61696c2c2077652077696c6c2074727920746f206c6f63617465207468652064656c6976657279207465616d20696e2053696e67706f737420616e64206966207468657265262333393b73206120636c65617220696e6469636174696f6e207468617420796f7572206f7264657220697320696e64656564206c6f73742c207765262333393b6c6c2072652d73656e6420746865206f7264657220746f20796f75206174206e6f20636f73742c207375626a65637420746f20617661696c6162696c6974792e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b20796f753c2f703e0d0a, '', '', '2017-03-29 23:53:46', '2017-03-29 23:53:46', '3'),
(35, 7, 10, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e5265717565737420666f7220636c6f73653c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-29 23:54:06', '2017-03-29 23:54:06', NULL),
(36, 8, 20, NULL, 'client', NULL, 0, 0, 0, 'Unsatisfied samples', 0x3c68353e576861742068617070656e7320696e20746865206576656e74206f6620756e7361746973666163746f72792f657870697265642f77726f6e672073616d706c652f6d697373696e672073616d706c65733f3c2f68353e, '', '', '2017-03-29 23:55:07', '2017-03-29 23:55:07', NULL),
(37, 8, 20, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031353a35353a30372c, '', '', '2017-03-29 23:55:07', '2017-03-29 23:55:07', NULL),
(38, 8, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032303a35353a30372c, '', '', '2017-03-29 23:58:04', '2017-03-29 23:58:04', NULL),
(39, 8, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-29 23:58:04', '2017-03-29 23:58:04', NULL),
(40, 8, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2053656261737469616e2c3c2f703e0d0a0d0a3c703e57652074616b652073756368206d617474657273207665727920736572696f75736c7920616e642077696c6c206c6f6f6b20696e746f20696e646976696475616c2063617365732074686f726f7567686c792e20416e792073616d706c6520746861742066616c6c7320756e646572207468652062656c6f772063617465676f726965732073686f756c64206e6f74206265207468726f776e2061776179206265666f72652074616b696e672070686f746f2070726f6f6620616e6420656d61696c696e67207468652070686f746f206f66207468652061666665637465642073616d706c6520616e6420796f757220442e4f202844656c6976657279204f726465722920746f2075732061742068656c704073616d706c6573746f72652e636f6d20286966206170706c696361626c65292e3c2f703e0d0a0d0a3c703e57652072656772657420746f20696e666f726d20796f752074686174206e6f20726566756e64732077696c6c20626520676976656e20666f72206f726465727320746861742066616c6c20756e646572207468652062656c6f772063617465676f726965732e3c2f703e0d0a0d0a3c703e312e20496e20746865206576656e742074686174207468652073616d706c6520796f75262333393b766520726563656976656420697320756e7361746973666163746f727920696e20616e792077617920796f752070657263656976652c2077652077696c6c20726571756972652070686f746f2070726f6f66206f66207468652073616d706c6520616e6420796f757220442e4f202844656c6976657279204f72646572292061732077656c6c20616e6420796f75206d617920626520726571756972656420746f2073656e64207573206261636b207468652073616d706c6520666f7220636c6f736520696e7370656374696f6e20616e6420726576696577206265666f72652061206465636973696f6e206973206d61646520746f2072652d73656e6420612073616d706c6520746f20796f75206174206e6f20636f73742c207375626a65637420746f20617661696c6162696c6974792e2054686520706f737461676520636f73742077696c6c206265206372656469746564206261636b20746f20796f7572206163636f756e742061667465722077652072656365697665207468652072657475726e6564206974656d2e3c2f703e0d0a0d0a3c703e322e20496e20746865206576656e74207468617420796f75207265636569766520616e20657870697265642070726f647563742c2077652077696c6c207265717569726520636c6561722070686f746f2070726f6f66206f66207468652073616d706c6520616e642069747320657870697279206461746520666f7220636c6f736520696e7370656374696f6e20616e6420726576696577206265666f72652061206465636973696f6e206973206d61646520746f2072652d73656e6420612073616d706c6520746f20796f75206174206e6f20636f73742c207375626a65637420746f20617661696c6162696c6974792e3c2f703e0d0a0d0a3c703e332e20496e20746865206576656e74207468617420796f75262333393b7665207265636569766564207468652077726f6e672073616d706c652c2077652077696c6c20726571756972652070686f746f2070726f6f66206f66207468652077726f6e676c792073656e742073616d706c6520616e6420442e4f202844656c6976657279204f726465722920616e6420616674657220726576696577696e672c207765262333393b6c6c2072652d73656e642074686520636f72726563742073616d706c6520746f20796f75206174206e6f20636f73742c207375626a65637420746f20617661696c6162696c6974792e3c2f703e0d0a0d0a3c703e342e20496e20746865206576656e7420796f75262333393b766520726563656976656420796f7572206f7264657220776974682061206d697373696e672073616d706c652c2077652077696c6c207265717569726520796f7520746f20656d61696c207573206120636c6561722070686f746f2070726f6f66206f6620796f757220442e4f202844656c6976657279204f726465722920746f2068656c704073616d706c6573746f72652e636f6d20616e642061667465722077686963682c206b696e646c79206769766520757320612063616c6c20617420282b36352920363433363732323820616e64206f757220637573746f6d65722073657276696365206f6666696365722077696c6c20617474656e6420746f20796f7520746f2066696e64206f7574206d6f7265206265666f72652061206465636973696f6e206973206d61646520746f2072652d73656e6420746865206d697373696e672073616d706c6520746f20796f75206174206e6f20636f73742c207375626a65637420746f20617661696c6162696c6974792e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-29 23:58:05', '2017-03-29 23:58:05', '3'),
(41, 9, 23, NULL, 'client', NULL, 0, 0, 0, 'Payment methods', 0x57686174206172652074686520646966666572656e74207073796d656e74206d6574686f64733f, '', '', '2017-03-30 00:00:37', '2017-03-30 00:00:37', NULL),
(42, 9, 23, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a30303a33372c, '', '', '2017-03-30 00:00:38', '2017-03-30 00:00:38', NULL),
(43, 9, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a30303a33372c, '', '', '2017-03-30 00:01:40', '2017-03-30 00:01:40', NULL),
(44, 9, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 00:01:40', '2017-03-30 00:01:40', NULL),
(45, 9, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c68333e5041594d454e54204d4554484f44533c2f68333e0d0a0d0a3c756c3e0d0a093c6c693e3c7374726f6e673e50617950616c3c2f7374726f6e673e3a2073656375726520616e642066617374207061796d656e74207669612050617950616c3c2f6c693e0d0a093c6c693e3c7374726f6e673e4372656469742043617264733c2f7374726f6e673e3a2057652061636365707420564953412c204d6173746572436172642c20414d45524943414e20455850524553532a2c204a434220616e642044696e6572262333393b7320436c756220496e7465726e6174696f6e616c3c6272202f3e0d0a09282671756f743b566572696669656420627920564953412671756f743b20616e64202671756f743b4d61737465724361726420536563757265436f64652671756f743b2061726520737570706f72746564206279204e49293c2f6c693e0d0a093c6c693e3c7374726f6e673e44656269742043617264733c2f7374726f6e673e3a20564953412064656269742063617264732c20436172746520426c657565202846292c2043617274652042616e6361697265202846292c204d69737465722043617368202842292c20706f73746570617920284954293c2f6c693e0d0a093c6c693e5061796d656e7420696e20616476616e63652062792062616e6b207472616e736665722028417573747269612c2042656c6769756d2c204379707275732c204573746f6e69652c204672616e63652c2046696e6c616e642c204765726d616e792c204772656563652c204972656c616e642c204974616c792c204c61747669612c204c7578656d626f7572672c204d616c74612c204e65746865726c616e64732c20506f72747567616c2c20536c6f76616b2052657075626c69632c20536c6f76656e69612c20537061696e2c203c7374726f6e673e6e6f74206170706c696361626c6520746f20646f776e6c6f6164733c2f7374726f6e673e293c2f6c693e0d0a093c6c693e3c7374726f6e673e4469726563742042616e6b205472616e736665722053657276696365733c2f7374726f6e673e3a20534f464f52542042616e6b696e6720284765726d616e792c20417573747269612c204672616e63652c2042656c6769756d292c20694445414c20284e4c292c20504f4c6920284175737472616c6961293c2f6c693e0d0a3c2f756c3e0d0a, '', '', '2017-03-30 00:01:41', '2017-03-30 00:01:41', '2'),
(46, 10, 23, NULL, 'client', NULL, 0, 0, 0, 'Paymet methods', 0x57686174206172652074686520646966666572656e74207061796d656e74206d6574686f64733f3c62723e3c62723e, '', '', '2017-03-30 00:06:31', '2017-03-30 00:06:31', NULL),
(47, 10, 23, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a30363a33312c, '', '', '2017-03-30 00:06:31', '2017-03-30 00:06:31', NULL),
(48, 10, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a30363a33312c, '', '', '2017-03-30 00:09:18', '2017-03-30 00:09:18', NULL),
(49, 10, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 00:09:18', '2017-03-30 00:09:18', NULL),
(50, 10, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c68333e5041594d454e54204d4554484f44533c2f68333e0d0a0d0a3c756c3e0d0a093c6c693e3c7374726f6e673e50617950616c3c2f7374726f6e673e3a2073656375726520616e642066617374207061796d656e74207669612050617950616c3c2f6c693e0d0a093c6c693e3c7374726f6e673e4372656469742043617264733c2f7374726f6e673e3a2057652061636365707420564953412c204d6173746572436172642c20414d45524943414e20455850524553532a2c204a434220616e642044696e6572262333393b7320436c756220496e7465726e6174696f6e616c3c6272202f3e0d0a09282671756f743b566572696669656420627920564953412671756f743b20616e64202671756f743b4d61737465724361726420536563757265436f64652671756f743b2061726520737570706f72746564206279204e49293c2f6c693e0d0a093c6c693e3c7374726f6e673e44656269742043617264733c2f7374726f6e673e3a20564953412064656269742063617264732c20436172746520426c657565202846292c2043617274652042616e6361697265202846292c204d69737465722043617368202842292c20706f73746570617920284954293c2f6c693e0d0a093c6c693e5061796d656e7420696e20616476616e63652062792062616e6b207472616e736665722028417573747269612c2042656c6769756d2c204379707275732c204573746f6e69652c204672616e63652c2046696e6c616e642c204765726d616e792c204772656563652c204972656c616e642c204974616c792c204c61747669612c204c7578656d626f7572672c204d616c74612c204e65746865726c616e64732c20506f72747567616c2c20536c6f76616b2052657075626c69632c20536c6f76656e69612c20537061696e2c203c7374726f6e673e6e6f74206170706c696361626c6520746f20646f776e6c6f6164733c2f7374726f6e673e293c2f6c693e0d0a093c6c693e3c7374726f6e673e4469726563742042616e6b205472616e736665722053657276696365733c2f7374726f6e673e3a20534f464f52542042616e6b696e6720284765726d616e792c20417573747269612c204672616e63652c2042656c6769756d292c20694445414c20284e4c292c20504f4c6920284175737472616c6961293c2f6c693e0d0a3c2f756c3e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c68333e5041594d454e54204d4554484f44533c2f68333e0d0a0d0a3c756c3e0d0a093c6c693e3c7374726f6e673e50617950616c3c2f7374726f6e673e3a2073656375726520616e642066617374207061796d656e74207669612050617950616c3c2f6c693e0d0a093c6c693e3c7374726f6e673e4372656469742043617264733c2f7374726f6e673e3a2057652061636365707420564953412c204d6173746572436172642c20414d45524943414e20455850524553532a2c204a434220616e642044696e6572262333393b7320436c756220496e7465726e6174696f6e616c3c6272202f3e0d0a09282671756f743b566572696669656420627920564953412671756f743b20616e64202671756f743b4d61737465724361726420536563757265436f64652671756f743b2061726520737570706f72746564206279204e49293c2f6c693e0d0a093c6c693e3c7374726f6e673e44656269742043617264733c2f7374726f6e673e3a20564953412064656269742063617264732c20436172746520426c657565202846292c2043617274652042616e6361697265202846292c204d69737465722043617368202842292c20706f73746570617920284954293c2f6c693e0d0a093c6c693e5061796d656e7420696e20616476616e63652062792062616e6b207472616e736665722028417573747269612c2042656c6769756d2c204379707275732c204573746f6e69652c204672616e63652c2046696e6c616e642c204765726d616e792c204772656563652c204972656c616e642c204974616c792c204c61747669612c204c7578656d626f7572672c204d616c74612c204e65746865726c616e64732c20506f72747567616c2c20536c6f76616b2052657075626c69632c20536c6f76656e69612c20537061696e2c203c7374726f6e673e6e6f74206170706c696361626c6520746f20646f776e6c6f6164733c2f7374726f6e673e293c2f6c693e0d0a093c6c693e3c7374726f6e673e4469726563742042616e6b205472616e736665722053657276696365733c2f7374726f6e673e3a20534f464f52542042616e6b696e6720284765726d616e792c20417573747269612c204672616e63652c2042656c6769756d292c20694445414c20284e4c292c20504f4c6920284175737472616c6961293c2f6c693e0d0a3c2f756c3e0d0a0d0a3c703e3c696d6720616c743d2222207372633d22687474703a2f2f7777772e64656d61636d656469612e636f6d2f77702d636f6e74656e742f75706c6f6164732f323031342f30342f5061796d656e742d476174657761792e6a706722207374796c653d226865696768743a37313070783b2077696474683a32303030707822202f3e3c2f703e0d0a, '', '', '2017-03-30 00:09:18', '2017-03-30 00:09:18', '3'),
(51, 11, 12, NULL, 'client', NULL, 0, 0, 0, 'Pick up information', 0x3c6469763e3c62723e3c2f6469763e3c6469763e5768617420646f6573205069636b757020696e20706572736f6e206d65616e20696e20746865204f7264657220436f6e6669726d6174696f6e20706167653f266e6273703b3c2f6469763e, '', '', '2017-03-30 00:13:39', '2017-03-30 00:13:39', NULL),
(52, 11, 12, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a31333a33392c, '', '', '2017-03-30 00:13:39', '2017-03-30 00:13:39', NULL),
(53, 12, 12, NULL, 'client', NULL, 0, 0, 0, ' Pick up in person information', 0x5768617420646f657320e28098205069636b20266e6273703b757020696e20266e6273703b706572736f6e20e28099206d65616e20266e6273703b696e20746865204f7264657220436f6e6669726d6174696f6e2070616765203f, '', '', '2017-03-30 00:17:12', '2017-03-30 00:17:12', NULL),
(54, 12, 12, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a31373a31322c, '', '', '2017-03-30 00:17:12', '2017-03-30 00:17:12', NULL),
(55, 13, 12, NULL, 'client', NULL, 0, 0, 0, ' Pick up in person information', 0x5768617420646f657320e28098205069636b20266e6273703b757020696e20266e6273703b706572736f6e20e28099206d65616e20266e6273703b696e20746865204f7264657220436f6e6669726d6174696f6e2070616765203f, '', '', '2017-03-30 00:18:34', '2017-03-30 00:18:34', NULL),
(56, 13, 12, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a31383a33342c, '', '', '2017-03-30 00:18:34', '2017-03-30 00:18:34', NULL),
(57, 13, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a31383a33342c, '', '', '2017-03-30 00:20:15', '2017-03-30 00:20:15', NULL),
(58, 13, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 00:20:15', '2017-03-30 00:20:15', NULL),
(59, 13, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e4966207468652064656c6976657279206d6574686f642073656c656374656420696e2064726f7020646f776e206c697374206973205069636b20757020696e20706572736f6e26727371756f3b2c20636f6c6c656374696f6e206d757374206265206d61646520696e20706572736f6e2061742074686520737065636966696564207069636b207570206c6f636174696f6e2c2077697468696e20627573696e65737320686f7572732c2077697468206120636f7079206f66207468652054617820496e766f69636520616e642070686f746f206964656e74696669636174696f6e2e3c2f703e0d0a, '', '', '2017-03-30 00:20:15', '2017-03-30 00:20:15', '2'),
(60, 13, 1, NULL, 'support', NULL, 0, 0, 0, '', 0x3c703e4966207468652064656c6976657279206d6574686f642073656c656374656420696e2064726f7020646f776e206c697374206973205069636b20757020696e20706572736f6e26727371756f3b2c20636f6c6c656374696f6e206d757374206265206d61646520696e20706572736f6e2061742074686520737065636966696564207069636b207570206c6f636174696f6e2c2077697468696e20627573696e65737320686f7572732c2077697468206120636f7079206f66207468652054617820496e766f69636520616e642070686f746f206964656e74696669636174696f6e2e3c2f703e0d0a, '', '', '2017-03-30 00:20:48', '2017-03-30 00:20:48', '3'),
(61, 14, 16, NULL, 'client', NULL, 0, 0, 0, 'infomation about order confirmation', 0x5768617420646f6573202244656c697665727920636f737422206d65616e20696e206f7264657220636f6e6669726d6174696f6e20706167653f3c62723e3c62723e, '', '', '2017-03-30 00:23:35', '2017-03-30 00:23:35', NULL),
(62, 14, 16, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a32333a33352c, '', '', '2017-03-30 00:23:35', '2017-03-30 00:23:35', NULL),
(63, 14, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e4173686f6b3c2f623e2c, '', '', '2017-03-30 00:26:41', '2017-03-30 00:26:41', NULL),
(64, 14, 4, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a32333a33352c, '', '', '2017-03-30 00:28:46', '2017-03-30 00:28:46', NULL),
(65, 14, 4, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e4966207468652064656c6976657279206d6574686f642073656c656374656420696e2064726f7020646f776e206c69737420697320266c7371756f3b44656c697665727920436f737420796f7572206f726465722077696c6c206265207368697070656420746f20746865206e6f6d696e617465642044656c697665727920416464726573732e3c2f703e0d0a0d0a3c703e506c6561736520656e737572652074686520616464726573732064657461696c732061726520636f72726563743c2f703e0d0a, '', '', '2017-03-30 00:28:52', '2017-03-30 00:28:52', '6'),
(66, 15, 16, NULL, 'client', NULL, 0, 0, 0, 'Shopping Cart information', 0x3c6469763e5768792063616ee2809974204920616464206974656d7320746f206d793c2f6469763e3c6469763e2053686f7070696e6720436172743f3c2f6469763e3c62723e, '', '', '2017-03-30 00:30:20', '2017-03-30 00:30:20', NULL),
(67, 15, 16, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a33303a32302c, '', '', '2017-03-30 00:30:20', '2017-03-30 00:30:20', NULL),
(68, 16, 16, NULL, 'client', NULL, 0, 0, 0, 'Tax invoice', 0x3c6469763e446f2049207265636569766520612054617820496e766f6963653f3c2f6469763e, '', '', '2017-03-30 00:31:45', '2017-03-30 00:31:45', NULL),
(69, 16, 16, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a33313a34352c, '', '', '2017-03-30 00:31:45', '2017-03-30 00:31:45', NULL),
(70, 16, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a33313a34352c, '', '', '2017-03-30 00:33:34', '2017-03-30 00:33:34', NULL),
(71, 16, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 00:33:34', '2017-03-30 00:33:34', NULL),
(72, 16, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f204d6164616e2c3c2f703e0d0a0d0a3c703e5965732c206120636f7079206f66207468652054617820496e766f6963652077696c6c20626520617474616368656420746f20746865204f7264657220436f6e6669726d6174696f6e20456d61696c2e204966207468652064656c6976657279206d6574686f6420666f7220746865206974656d206973205069636b20757020696e20706572736f6e26727371756f3b207468656e20796f752077696c6c206e65656420746f206272696e6720746869732054617820496e766f69636520616e642070686f746f206964656e74696669636174696f6e20746f207468652064657369676e61746564207069636b207570206c6f636174696f6e2e3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 00:33:34', '2017-03-30 00:33:34', '2'),
(73, 17, 17, NULL, 'client', NULL, 0, 0, 0, 'Order cancel information', 0x3c6469763e43616e2049206368616e6765206f722063616e63656c206d79206f726465723f266e6273703b3c2f6469763e, '', '', '2017-03-30 00:34:51', '2017-03-30 00:34:51', NULL),
(74, 17, 17, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a33343a35312c, '', '', '2017-03-30 00:34:51', '2017-03-30 00:34:51', NULL),
(75, 17, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e4469767961203c2f623e2c, '', '', '2017-03-30 00:35:08', '2017-03-30 00:35:08', NULL),
(76, 17, 6, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a33343a35312c, '', '', '2017-03-30 00:36:38', '2017-03-30 00:36:38', NULL),
(77, 17, 6, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f204d6172792c3c2f703e0d0a0d0a3c703e4368616e676573206f722063616e63656c6c6174696f6e7320746f20796f7572206f726465722063616e206265206d61646520627920636f6e74616374696e67207468652053746f7265204f776e6572206f66207468652050726f6475637428732920696e207175657374696f6e20266e6273703b466f722074686573652064657461696c732c20726566657220746f2074686520436f6e7461637420616e6420456d61696c20696e666f726d6174696f6e207468617420697320696e636c7564656420616761696e737420656163682050726f64756374206f6e20796f75722054617820496e766f69636520286120636f7079206f6620776869636820697320617474616368656420746f20796f7572204f72646572205061796d656e7420436f6e6669726d6174696f6e456d61696c292e496620746865206f726465722068617320616c7265616479206265656e2070726570617265642c2077652077696c6c206e6f742062652061626c6520746f206d616b6520616e79206368616e6765732e3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 00:36:44', '2017-03-30 00:36:44', '2'),
(78, 17, 6, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e5265736f6c7665643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 00:36:55', '2017-03-30 00:36:55', NULL),
(79, 18, 11, NULL, 'client', NULL, 0, 0, 0, 'Payment methods', 0x486f7720646f20492070617920666f72206d79206f726465723f, '', '', '2017-03-30 00:40:16', '2017-03-30 00:40:16', NULL),
(80, 18, 11, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a34303a31362c, '', '', '2017-03-30 00:40:16', '2017-03-30 00:40:16', NULL),
(81, 18, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e416e647265773c2f623e2c, '', '', '2017-03-30 00:40:35', '2017-03-30 00:40:35', NULL),
(82, 18, 2, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a34303a31362c, '', '', '2017-03-30 00:42:15', '2017-03-30 00:42:15', NULL),
(83, 18, 2, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416d72757468612c3c2f703e0d0a0d0a3c703e416c6c207072696365732061726520696e204175737472616c69616e20446f6c6c6172732028415544292e204120636861726765206f6620313025204175737472616c69616e20476f6f647320616e6420536572766963652054617820284753542977696c6c20626520696e636c7564656420696620475354206170706c69657320746f206120706172746963756c61722070726f647563742e57652063757272656e746c792061636365707420637265646974206f722064656269742063617264202d205649534120616e64204d617374657243617264202e204e6f2043617368206f6e2044656c6976657279206f7220616c7465726e617465207061796d656e74206d6574686f647320737563682061732050617950616c2061726520617661696c61626c652e20506c6561736520656e73757265207468617420796f75722063617264206973207265676973746572656420746f20616c6c6f77206f6e6c696e652070757263686173696e672e20496620746865207472616e73616374696f6e206973206265696e67206465636c696e65642c20636f6e7461637420796f7572206361726420697373756572202f2066696e616e6369616c20696e737469747574696f6e20746f2064657465726d696e65207768657468657220796f7572206163636f756e7420686173206265656e20636f6e66696775726564206163636f7264696e676c792e3c2f703e0d0a, '', '', '2017-03-30 00:42:21', '2017-03-30 00:42:21', '2'),
(84, 18, 2, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e436c6f7365643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 00:42:29', '2017-03-30 00:42:29', NULL),
(85, 19, 11, NULL, 'client', NULL, 0, 0, 0, 'Payment information', 0x43616e204920676f206261636b20746f2070726576696f757320706167652c20696620492068617665206163636964656e746c792063686f73656e20616e20696e636f727265637420266e6273703b436172643f, '', '', '2017-03-30 00:43:36', '2017-03-30 00:43:36', NULL),
(86, 19, 11, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a34333a33362c, '', '', '2017-03-30 00:43:36', '2017-03-30 00:43:36', NULL),
(87, 19, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e416e647265773c2f623e2c, '', '', '2017-03-30 00:44:00', '2017-03-30 00:44:00', NULL),
(88, 19, 2, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a34333a33362c, '', '', '2017-03-30 00:45:20', '2017-03-30 00:45:20', NULL),
(89, 19, 2, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416d72757468612c3c2f703e0d0a0d0a3c703e5768656e20796f7520636c69636b206f6e20266c7371756f3b434f4e4649524d204f5244455226727371756f3b2c20796f752077696c6c20626520646972656374656420746f20436f6d6d6f6e7765616c74682042616e6b205061796d656e74207765627369746520746f2073656c65637420612063617264207479706520282063757272656e746c79206163636570746564206361726473206172652056495341206f72204d61737465724361726420637265646974206f722064656269742063617264292e20496620796f752068617665206163636964656e746c792063686f73656e2074686520696e636f7272656374206361726420747970652c20444f204e4f5420555345205448452042524f57534552204241434b20425554544f4e2e20434c4f5345205448452042524f575345522057494e444f57207468656e207220652d6c61756e6368204f6e6c696e652053686f7020616761696e202e3c2f703e0d0a, '', '', '2017-03-30 00:45:25', '2017-03-30 00:45:25', '2'),
(90, 20, 11, NULL, 'client', NULL, 0, 0, 0, 'Information regarding payment declined ', 0x5768656e207061796d656e74206973206465636c696e65642c20776861742073686f756c64204920646f3f, '', '', '2017-03-30 00:46:02', '2017-03-30 00:46:02', NULL),
(91, 20, 11, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a34363a30322c, '', '', '2017-03-30 00:46:03', '2017-03-30 00:46:03', NULL),
(92, 21, 18, NULL, 'client', NULL, 0, 0, 0, 'Refund information', 0x43616e2049207265717565737420666f72206120726566756e643f, '', '', '2017-03-30 00:49:18', '2017-03-30 00:49:18', NULL),
(93, 21, 18, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a34393a31382c, '', '', '2017-03-30 00:49:18', '2017-03-30 00:49:18', NULL),
(94, 22, 18, NULL, 'client', NULL, 0, 0, 0, 'Regarding PDFs information ', 0x486f7720646f2049206761696e2061636365737320746f20796f7572206672656520504446733f, '', '', '2017-03-30 00:52:12', '2017-03-30 00:52:12', NULL),
(95, 22, 18, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a35323a31322c, '', '', '2017-03-30 00:52:12', '2017-03-30 00:52:12', NULL),
(96, 22, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e5361726179753c2f623e2c, '', '', '2017-03-30 00:52:30', '2017-03-30 00:52:30', NULL),
(97, 22, 8, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a35323a31322c, '', '', '2017-03-30 00:53:03', '2017-03-30 00:53:03', NULL),
(98, 22, 8, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2050617465722c3c2f703e0d0a0d0a3c703e4f6e20746865203c6120687265663d22687474703a2f2f7777772e776f726c6477617463682e6f72672f726573656172636822207461726765743d225f626c616e6b223e52657365617263682073656374696f6e3c2f613e266e6273703b6f66206f757220776562736974652c20796f752077696c6c207365652061206c696e6b20746f206f757220667265656c7920646f776e6c6f616461626c6520504446732c2061732077656c6c20617320746f206d616e79206f7468657220667265652061727469636c657320616e642070726573656e746174696f6e732e20466f722074686520504446732c20636c69636b206f6e20746865207075626c69636174696f6e20796f752061726520696e746572657374656420696e20646f776e6c6f6164696e672c207468656e207363726f6c6c20746f2074686520626f74746f6d206f662074686174206974656d262333393b7320706167652e20486572652c20796f752077696c6c206e65656420746f20656e74657220796f757220652d6d61696c206164647265737320746f2076616c6964617465207468617420796f7520617265207265676973746572656420776974682074686520576f726c64776174636820496e7374697475746520776562736974652e20496620796f752061726520616c726561647920726567697374657265642c2074686520646f776e6c6f61642077696c6c20626567696e2e20496620796f7520617265206e6f74207265676973746572656420796f752077696c6c20626520676976656e2061206c696e6b20746f2074686520576f726c64776174636820726567697374726174696f6e20706167652e204f6e636520796f75206861766520636f6d706c657465642074686520726567697374726174696f6e20666f726d2c20796f752077696c6c20686176652061636365737320746f2074686520636f6d706c657465206c696272617279206f66206672656520576f726c64776174636820496e73746974757465207075626c69636174696f6e7320696e2050444620666f726d61742e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 00:53:08', '2017-03-30 00:53:08', '1'),
(99, 22, 18, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e556e7665726966696564205374617475733c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 00:53:18', '2017-03-30 00:53:18', NULL),
(100, 22, 18, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e436c6f7365643c2f623e2066726f6d203c623e556e7665726966696564205374617475733c2f623e2c, '', '', '2017-03-30 00:53:29', '2017-03-30 00:53:29', NULL),
(101, 23, 19, NULL, 'client', NULL, 0, 0, 0, 'Infromation about Return policy', 0x3c623e5768617420697320796f75722072657475726e7320706f6c6963793f3c2f623e, '', '', '2017-03-30 00:54:30', '2017-03-30 00:54:30', NULL),
(102, 23, 19, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a35343a33302c, '', '', '2017-03-30 00:54:30', '2017-03-30 00:54:30', NULL),
(103, 23, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e416e647265773c2f623e2c, '', '', '2017-03-30 00:54:44', '2017-03-30 00:54:44', NULL),
(104, 23, 2, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a35343a33302c, '', '', '2017-03-30 00:56:14', '2017-03-30 00:56:14', NULL),
(105, 23, 2, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2053616d75616c2c3c2f703e0d0a0d0a3c703e57652077616e7420796f7520746f20626520636f6d706c6574656c7920736174697366696564207769746820796f7572206f726465722e20496620666f7220736f6d6520726561736f6e20796f7520617265206e6f7420616e6420796f75207769736820746f2072657475726e20796f75722070757263686173652c20796f7526727371756f3b6c6c206e65656420746f2063616c6c206f757220437573746f6d65722053657276696365206465706172746d656e74206174202832303229203734352d3830393220746f206f627461696e20616e20617070726f76616c206e756d6265722e20266e6273703b4f6e636520796f752068617665206f627461696e656420796f757220617070726f76616c206e756d6265722c20706c6561736520666f6c6c6f77207468657365207468726565206561737920696e737472756374696f6e7320746f2072657475726e20796f75722070757263686173653a3c2f703e0d0a0d0a3c6f6c3e0d0a093c6c693e4d616b65207375726520746865206974656d20697320696e20726573616c61626c6520636f6e646974696f6e2e2057652063616e6e6f7420726566756e64206f72206372656469742072657475726e656420626f6f6b73207468617420617265206d61726b6564206f72206f74686572776973652064616d616765642e3c2f6c693e0d0a093c6c693e456e636c6f7365206120636f7079206f6620796f757220696e766f6963652e20506c6561736520696e636c756465206120636f7079206f6620746865206f726967696e616c20696e766f69636520286f722074686520696e766f696365206e756d62657220616e6420696e766f6963652064617465292077697468207468652072657475726e656420626f6f6b20736f20746861742077652063616e206973737565207468652070726f70657220637265646974206f7220726566756e642e3c2f6c693e0d0a093c6c693e5368697020746865207061636b61676520746f206f75722072657475726e73206465706172746d656e742e20546f2067756172616e7465652064656c69766572792c20706c6561736520696e7375726520796f7572207061636b616765206f722073686970206974206279205550533c2f6c693e0d0a3c2f6f6c3e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 00:56:19', '2017-03-30 00:56:19', '2'),
(106, 23, 19, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e5265717565737420666f7220636c6f73653c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 00:56:30', '2017-03-30 00:56:30', NULL),
(107, 24, 19, NULL, 'client', NULL, 0, 0, 0, 'Informatiopn regarding \"return\"', 0x3c7374726f6e673e266e6273703b43616e2049202272657475726e222061207075726368617365206f6620616e206974656d20696e20656c656374726f6e696320666f726d617420285044462c204b696e646c652c206574632e293f3c2f7374726f6e673e, '', '', '2017-03-30 00:57:29', '2017-03-30 00:57:29', NULL),
(108, 24, 19, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a35373a32392c, '', '', '2017-03-30 00:57:29', '2017-03-30 00:57:29', NULL),
(109, 25, 10, NULL, 'client', NULL, 0, 0, 0, 'Publication information', 0x3c623e486f7720646f2049206f7264657220636f70696573206f6620796f7572207075626c69636174696f6e7320696e206f74686572206c616e6775616765733f3c2f623e, '', '', '2017-03-30 00:59:04', '2017-03-30 00:59:04', NULL),
(110, 25, 10, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031363a35393a30342c, '', '', '2017-03-30 00:59:04', '2017-03-30 00:59:04', NULL),
(111, 25, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e4179657368613c2f623e2c, '', '', '2017-03-30 00:59:58', '2017-03-30 00:59:58', NULL),
(112, 25, 5, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032313a35393a30342c, '', '', '2017-03-30 01:00:31', '2017-03-30 01:00:31', NULL),
(113, 25, 5, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416c697368612c3c2f703e0d0a0d0a3c703e576f726c64776174636820776f726b7320776974682061206e6574776f726b206f66206d6f7265207468616e20323020706172746e6572732077686f207472616e736c61746520616e64207075626c69736820666f726569676e2d6c616e67756167652065646974696f6e73206f66206f757220616e6e75616c203c656d3e5374617465206f662074686520576f726c64203c2f656d3e7265706f72742e3c656d3e20266e6273703b3c2f656d3e576f726c64776174636820646f6573206e6f74206d61696e7461696e20696e76656e746f7279206f6620746865736520696e7465726e6174696f6e616c2065646974696f6e732c20736f20776520617265206e6f742061626c6520746f2074616b65206f726465727320666f72207468657365207075626c69636174696f6e732e20266e6273703b486f77657665722c2065616368206f66206f757220706172746e6572732063616e20626520636f6e74616374656420746f20617373697374207769746820796f7572206f72646572206f72207175657374696f6e732e20546f2066696e64206f7574207768696368206f7267616e697a6174696f6e73207075626c69736820696e2061207370656369666963206c616e67756167652c20706c6561736520766973697420746865203c6120687265663d22687474703a2f2f7777772e776f726c6477617463682e6f72672f6d697373696f6e223e41626f75742055733c2f613e2073656374696f6e206f66206f757220776562736974652c2066696e6420746865202671756f743b53656520506172746e6572732671756f743b20627574746f6e2c20616e6420636c69636b206f6e20746865206c616e6775616765207468617420796f752061726520696e746572657374656420696e2e204865726520796f752077696c6c2066696e6420746865206f7267616e697a6174696f6e732074686174207075626c69736820696e2074686174206c616e67756167652c20616c6f6e67207769746820746865697220636f6e7461637420646174612e20436f6e746163742074686520617070726f707269617465206f7267616e697a6174696f6e20616e6420706c61636520796f7572206f726465722e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 01:00:37', '2017-03-30 01:00:37', '2'),
(114, 25, 5, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e44656c657465643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 01:00:47', '2017-03-30 01:00:47', NULL),
(115, 26, 10, NULL, 'client', NULL, 0, 0, 0, 'Regarding sold-out information', 0x3c68343e416e206974656d20616464656420746f206d792073686f7070696e6720626167207761732073756464656e6c7920736f6c64206f757420617420636865636b6f75742e20486f77206973207468697320706f737369626c653f3c2f68343e, '', '', '2017-03-30 01:02:30', '2017-03-30 01:02:30', NULL),
(116, 26, 10, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a30323a33302c, '', '', '2017-03-30 01:02:30', '2017-03-30 01:02:30', NULL),
(117, 27, 23, NULL, 'client', NULL, 0, 0, 0, 'Infromation regarding checkout limits', 0x3c68353e576879206973207468657265206120636865636b6f7574206c696d69743f202f20576861742061726520616c6c2074686520636865636b6f7574206c696d6974733f3c2f68353e, '', '', '2017-03-30 01:04:50', '2017-03-30 01:04:50', NULL),
(118, 27, 23, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a30343a35302c, '', '', '2017-03-30 01:04:50', '2017-03-30 01:04:50', NULL);
INSERT INTO `ticket_thread` (`id`, `ticket_id`, `user_id`, `thread_type`, `poster`, `source`, `reply_rating`, `rating_count`, `is_internal`, `title`, `body`, `format`, `ip_address`, `created_at`, `updated_at`, `response_time`) VALUES
(119, 27, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e4179657368613c2f623e2c, '', '', '2017-03-30 01:05:21', '2017-03-30 01:05:21', NULL),
(120, 27, 5, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032323a30343a35302c, '', '', '2017-03-30 01:05:56', '2017-03-30 01:05:56', NULL),
(121, 27, 5, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f205961736877616e74682c3c2f703e0d0a0d0a3c703e53616d706c652053746f7265206973206120706f70756c61722073706f7420616e642067657473206c6f7473206f662073686f707065727320617420612074696d652e205468657365206c696d6974732061726520696e20706c61636520746f206d616b6520737572652065766572796f6e6520686173206120676f6f642074696d6520747279696e6720616e642070757263686173696e672074686569722070726f64756374732e20536f2e2e2e3c2f703e0d0a0d0a3c703e2d2045616368206163636f756e7420697320656e7469746c656420746f206f6e6c79206f6e6520283129206f72646572206f6620757020746f203420646966666572656e742073616d706c657320706572206461793c2f703e0d0a0d0a3c703e2d2045616368206163636f756e7420697320656e7469746c656420746f206f6e6c79206f6e652028312920726564656d7074696f6e207065722073616d706c652e3c2f703e0d0a0d0a3c703e2d20596f7572206163636f756e74206d75737420686176652073756666696369656e7420706f696e7473206265666f726520796f752063616e20636865636b6f7574207468652073616d706c652070726f64756374732e3c2f703e0d0a0d0a3c703e2d204b696e646c7920636c65617220616c6c2070656e64696e67207061796d656e7473206265666f726520616e6f7468657220636865636b6f75742e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a, '', '', '2017-03-30 01:06:02', '2017-03-30 01:06:02', '2'),
(122, 28, 20, NULL, 'client', NULL, 0, 0, 0, 'Information regarding Duplicate invoices', 0x3c623e576861742069662049e280996d20726563656976696e67206475706c696361746520636f70696573206f66207075626c69636174696f6e73206f7220696e766f696365733f3c2f623e, '', '', '2017-03-30 01:09:06', '2017-03-30 01:09:06', NULL),
(123, 28, 20, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a30393a30362c, '', '', '2017-03-30 01:09:06', '2017-03-30 01:09:06', NULL),
(124, 28, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032323a30393a30362c, '', '', '2017-03-30 01:09:46', '2017-03-30 01:09:46', NULL),
(125, 28, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 01:09:46', '2017-03-30 01:09:46', NULL),
(126, 28, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f2053656261737469616e2c3c2f703e0d0a0d0a3c703e46726f6d2074696d6520746f2074696d652c2077652066696e64206475706c696361746520637573746f6d6572206163636f756e7473206f6e206f75722073797374656d2c20616e642074686973206f63636173696f6e616c6c79206c6561647320746f20646f75626c6520636f70696573206f66207075626c69636174696f6e7320616e642f6f7220696e766f69636573206265696e67206d61696c6564206f75742e20496620796f752061726520726563656976696e67206475706c6963617465732066726f6d207573207468617420796f752068617665206e6f74206f7264657265642c20706c6561736520636f6e74616374206f757220437573746f6d65722053657276696365206465706172746d656e7420736f20746865792063616e206d616b6520746865206e6563657373617279206368616e6765732e20496620706f737369626c652c20706c65617365206861766520617661696c61626c6520636f70696573206f6620746865206d61696c696e67206c6162656c7320666f722065616368206974656d207468617420796f7520686176652072656365697665642066726f6d2075732c20617320746869732077696c6c206173736973742074686520437573746f6d65722053657276696365206465706172746d656e7420696e207265736f6c76696e672074686520736974756174696f6e20717569636b6c792e20546f20636f6e746163742075732c20706c6561736520736565206f757220637573746f6d6572207365727669636520696e666f726d6174696f6e2061742074686520746f70206f66207468697320706167652e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b20796f753c2f703e0d0a, '', '', '2017-03-30 01:09:46', '2017-03-30 01:09:46', '1'),
(127, 29, 9, 'first_reply', 'client', NULL, 0, 0, 0, 'Returns from booksellers', 0x3c7374726f6e673e5768617420697320796f757220706f6c69637920666f722072657475726e732066726f6d20626f6f6b73656c6c6572733f3c2f7374726f6e673e, '', '', '2017-03-30 01:11:16', '2017-10-04 05:47:32', NULL),
(128, 29, 9, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a31313a31362c, '', '', '2017-03-30 01:11:16', '2017-03-30 01:11:16', NULL),
(129, 29, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e496d72616e203c2f623e2c, '', '', '2017-03-30 01:11:43', '2017-03-30 01:11:43', NULL),
(130, 30, 9, NULL, 'client', NULL, 0, 0, 0, 'Dangers of shopping online', 0x3c623e5768617420617265207468652064616e67657273206f662073686f7070696e67206f6e6c696e653f3c2f623e, '', '', '2017-03-30 01:13:48', '2017-03-30 01:13:48', NULL),
(131, 30, 9, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a31333a34382c, '', '', '2017-03-30 01:13:48', '2017-03-30 01:13:48', NULL),
(132, 30, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032323a31333a34382c, '', '', '2017-03-30 01:14:30', '2017-03-30 01:14:30', NULL),
(133, 30, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 01:14:30', '2017-03-30 01:14:30', NULL),
(134, 30, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f20416c626572742c3c2f703e0d0a0d0a3c703e466f72206d6f73742070656f706c652c20696e7465726e65742073686f7070696e672069732070726f626c656d2d6672656520616e6420616c6c6f777320796f7520746f20747261636b20646f776e207468652062657374206465616c7320776974686f757420747261697073696e6720757020616e6420646f776e207468652068696768207374726565742e2042757420746865726520617265207269736b732e3c2f703e0d0a0d0a3c703e5065726861707320746865206d6f7374206f6276696f75732069732074686520746872656174206f66206964656e74697479207468656674206173206120726573756c74206f6620736f6d656f6e6520737465616c696e6720796f757220637265646974206f72206465626974206361726420696e666f726d6174696f6e2e20486f77657665722c20796f7520616c736f206e65656420746f206265206177617265206f6620796f75722072696768747320696620796f752062757920676f6f6473207468617420646f6e262333393b742073686f77207570206f7220617265206e6f742061732074686579207765726520646573637269626564206f6e2074686520736974652e3c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 01:14:31', '2017-03-30 01:14:31', '1'),
(135, 31, 14, NULL, 'client', NULL, 0, 0, 0, 'Retailers to trust', 0x3c623e486f7720646f2049206b6e6f7720776869636820696e7465726e65742072657461696c65727320746f2074727573743f3c2f623e, '', '', '2017-03-30 01:15:34', '2017-03-30 01:15:34', NULL),
(136, 31, 14, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a31353a33342c, '', '', '2017-03-30 01:15:34', '2017-03-30 01:15:34', NULL),
(137, 32, 14, NULL, 'client', NULL, 0, 0, 0, 'Safety on website', 0x3c623e486f7720646f2049206b6e6f77204920616d206f6e2061207361666520776562736974653f3c2f623e, '', '', '2017-03-30 01:16:20', '2017-03-30 01:16:20', NULL),
(138, 32, 14, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a31363a32302c, '', '', '2017-03-30 01:16:20', '2017-03-30 01:16:20', NULL),
(139, 32, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032323a31363a32302c, '', '', '2017-03-30 01:16:54', '2017-03-30 01:16:54', NULL),
(140, 32, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 01:16:54', '2017-03-30 01:16:54', NULL),
(141, 32, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f204a6f736c696e612c3c2f703e0d0a0d0a3c703e4c6f6f6b20666f722061207061646c6f636b2073796d626f6c20696e207468652077696e646f77262333393b73206672616d6520616e6420636865636b207468617420746865206164647265737320626567696e7320262333393b6874747073262333393b20726174686572207468616e20262333393b68747470262333393b2c20617320746869732064656e6f7465732061207365637572652077656220706167652e204d616b652073757265207468617420796f757220636f6d7075746572206861732075702d746f2d6461746520616e74692d766972757320616e6420616e74692d7370797761726520736f6674776172652c20617320746869732070726f746563747320796f752066726f6d20746872656174732077697468696e20796f7572206f776e2050432e3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a, '', '', '2017-03-30 01:16:54', '2017-03-30 01:16:54', '1'),
(142, 32, 14, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e436c6f7365643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 01:17:09', '2017-03-30 01:17:09', NULL),
(143, 33, 21, NULL, 'client', NULL, 0, 0, 0, 'Rights of a customer', 0x3c623e5768617420617265206d79207269676874733f3c2f623e, '', '', '2017-03-30 01:18:01', '2017-03-30 01:18:01', NULL),
(144, 33, 21, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a31383a30312c, '', '', '2017-03-30 01:18:02', '2017-03-30 01:18:02', NULL),
(145, 33, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032323a31383a30312c, '', '', '2017-03-30 01:18:20', '2017-03-30 01:18:20', NULL),
(146, 33, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 01:18:20', '2017-03-30 01:18:20', NULL),
(147, 33, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e596f752068617665207468652073616d6520726967687473206f6e6c696e65206173206f6e207468652068696768207374726565742c20736f206665656c206672656520746f20636f6d706c61696e20616e642072657475726e20706f6f72206f72206661756c747920676f6f64732e20596f752061726520616c736f20656e7469746c656420746f206120736576656e2d64617920262333393b636f6f6c696e67206f6666262333393b20706572696f642e2054686174206d65616e7320746861742c20756e6c65737320746865206974656d20686173206265656e206d61646520746f20796f757220706572736f6e616c2073706563696669636174696f6e206f722069732070657269736861626c652c20796f752063616e206368616e676520796f7572206d696e64206173206c6f6e6720617320796f7520646f206e6f742072656d6f766520746865207772617070696e672e3c2f703e0d0a, '', '', '2017-03-30 01:18:21', '2017-03-30 01:18:21', '1'),
(148, 34, 21, NULL, 'client', NULL, 0, 0, 0, 'Purchases protection', 0x3c623e43616e20492070726f74656374206d79207075726368617365733f3c2f623e, '', '', '2017-03-30 01:19:16', '2017-03-30 01:19:16', NULL),
(149, 34, 21, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372031373a31393a31362c, '', '', '2017-03-30 01:19:16', '2017-03-30 01:19:16', NULL),
(150, 34, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b65742044756564617465206f6e2033302f30332f323031372032323a31393a31362c, '', '', '2017-03-30 01:19:53', '2017-03-30 01:19:53', NULL),
(151, 34, 1, NULL, 'support', NULL, 0, 0, 1, '', 0x5469636b6574206861732061737369676e656420746f203c623e44656570616b2052616a3c2f623e2c, '', '', '2017-03-30 01:19:53', '2017-03-30 01:19:53', NULL),
(152, 34, 1, 'first_reply', 'support', NULL, 0, 0, 0, '', 0x3c703e48656c6c6f205368726176616e2c3c2f703e0d0a0d0a3c703e5768656e20796f7520757365206120637265646974206361726420746f206d616b65206120707572636861736520776f727468206265747765656e2026706f756e643b31303020616e642026706f756e643b33302c3030302c207468652070726f7669646572206265636f6d6573206a6f696e746c79206c6961626c652077697468207468652072657461696c65722e2054686174206d65616e73746861742c20696620736f6d657468696e672069732077726f6e67206f72207468652072657461696c65722076616e6973686573206f7220676f6573206f7574206f6620627573696e6573732c20796f752073686f756c64207374696c6c2062652061626c6520746f2067657420796f7572206d6f6e6579206261636b2e3c2f703e0d0a0d0a3c703e5468616e6b696e6720796f753c2f703e0d0a0d0a3c703e266e6273703b3c2f703e0d0a, '', '', '2017-03-30 01:19:53', '2017-03-30 01:19:53', '1'),
(153, 34, 21, NULL, 'support', NULL, 0, 0, 1, '', 0x5374617475732073657420746f203c623e436c6f7365643c2f623e2066726f6d203c623e4f70656e3c2f623e2c, '', '', '2017-03-30 01:20:14', '2017-03-30 01:20:14', NULL),
(154, 35, 24, NULL, 'client', NULL, 0, 0, 0, 'test', 0x746573743c6272202f3e0d0a3c6272202f3e0d0a2d2d203c6272202f3e0d0a4d65642076c3a46e6c6967612068c3a46c736e696e6761722f4265737420726567617264733c6272202f3e0d0a2f2f48656e72696b204f6c73736f6e3c6272202f3e0d0a4167656e637939, '', '', '2017-08-31 05:20:13', '2017-08-31 05:20:13', NULL),
(155, 35, NULL, 'response_due', 'support', NULL, 0, 0, 1, '', 0x55706461746564204475656461746520746f20323031372d30382d33312031373a35303a3133, '', '', '2017-08-31 05:20:13', '2017-08-31 05:20:13', NULL),
(156, 29, 1, 'internal_activity_alert', 'support', NULL, 0, 0, 1, '', 0x5072696f726974792073657420746f203c623e4e6f726d616c3c2f623e2066726f6d203c623e4c6f773c2f623e2c20534c412073657420746f203c623e4e6f726d616c3c2f623e2066726f6d203c623e4c6f773c2f623e, '', '', '2017-10-04 05:47:32', '2017-10-04 05:47:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_token`
--

DROP TABLE IF EXISTS `ticket_token`;
CREATE TABLE IF NOT EXISTS `ticket_token` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_type`
--

DROP TABLE IF EXISTS `ticket_type`;
CREATE TABLE IF NOT EXISTS `ticket_type` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `type_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ispublic` int(11) NOT NULL,
  `is_default` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_type`
--

INSERT INTO `ticket_type` (`id`, `name`, `status`, `type_desc`, `ispublic`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Default', 1, 'Default', 0, 1, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(2, 'Question', 0, 'Question', 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(3, 'Incident', 0, 'Incident', 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(4, 'Problem', 0, 'Problem', 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(5, 'Feature Request', 0, 'Feature Request', 0, 0, '2017-03-29 05:23:25', '2017-03-29 05:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `timezone`
--

DROP TABLE IF EXISTS `timezone`;
CREATE TABLE IF NOT EXISTS `timezone` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `timezone`
--

INSERT INTO `timezone` (`id`, `name`, `location`) VALUES
(1, 'Pacific/Midway', '(GMT-11:00) Midway Island'),
(2, 'US/Samoa', '(GMT-11:00) Samoa'),
(3, 'US/Hawaii', '(GMT-10:00) Hawaii'),
(4, 'US/Alaska', '(GMT-09:00) Alaska'),
(5, 'US/Pacific', '(GMT-08:00) Pacific Time (US &amp; Canada)'),
(6, 'America/Tijuana', '(GMT-08:00) Tijuana'),
(7, 'US/Arizona', '(GMT-07:00) Arizona'),
(8, 'US/Mountain', '(GMT-07:00) Mountain Time (US &amp; Canada)'),
(9, 'America/Chihuahua', '(GMT-07:00) Chihuahua'),
(10, 'America/Mazatlan', '(GMT-07:00) Mazatlan'),
(11, 'America/Mexico_City', '(GMT-06:00) Mexico City'),
(12, 'America/Monterrey', '(GMT-06:00) Monterrey'),
(13, 'Canada/Saskatchewan', '(GMT-06:00) Saskatchewan'),
(14, 'US/Central', '(GMT-06:00) Central Time (US &amp; Canada)'),
(15, 'US/Eastern', '(GMT-05:00) Eastern Time (US &amp; Canada)'),
(16, 'US/East-Indiana', '(GMT-05:00) Indiana (East)'),
(17, 'America/Bogota', '(GMT-05:00) Bogota'),
(18, 'America/Lima', '(GMT-05:00) Lima'),
(19, 'America/Caracas', '(GMT-04:30) Caracas'),
(20, 'Canada/Atlantic', '(GMT-04:00) Atlantic Time (Canada)'),
(21, 'America/La_Paz', '(GMT-04:00) La Paz'),
(22, 'America/Santiago', '(GMT-04:00) Santiago'),
(23, 'Canada/Newfoundland', '(GMT-03:30) Newfoundland'),
(24, 'America/Buenos_Aires', '(GMT-03:00) Buenos Aires'),
(25, 'Greenland', '(GMT-03:00) Greenland'),
(26, 'Atlantic/Stanley', '(GMT-02:00) Stanley'),
(27, 'Atlantic/Azores', '(GMT-01:00) Azores'),
(28, 'Atlantic/Cape_Verde', '(GMT-01:00) Cape Verde Is.'),
(29, 'Africa/Casablanca', '(GMT) Casablanca'),
(30, 'Europe/Dublin', '(GMT) Dublin'),
(31, 'Europe/Lisbon', '(GMT) Lisbon'),
(32, 'Europe/London', '(GMT) London'),
(33, 'Africa/Monrovia', '(GMT) Monrovia'),
(34, 'Europe/Amsterdam', '(GMT+01:00) Amsterdam'),
(35, 'Europe/Belgrade', '(GMT+01:00) Belgrade'),
(36, 'Europe/Berlin', '(GMT+01:00) Berlin'),
(37, 'Europe/Bratislava', '(GMT+01:00) Bratislava'),
(38, 'Europe/Brussels', '(GMT+01:00) Brussels'),
(39, 'Europe/Budapest', '(GMT+01:00) Budapest'),
(40, 'Europe/Copenhagen', '(GMT+01:00) Copenhagen'),
(41, 'Europe/Ljubljana', '(GMT+01:00) Ljubljana'),
(42, 'Europe/Madrid', '(GMT+01:00) Madrid'),
(43, 'Europe/Paris', '(GMT+01:00) Paris'),
(44, 'Europe/Prague', '(GMT+01:00) Prague'),
(45, 'Europe/Rome', '(GMT+01:00) Rome'),
(46, 'Europe/Sarajevo', '(GMT+01:00) Sarajevo'),
(47, 'Europe/Skopje', '(GMT+01:00) Skopje'),
(48, 'Europe/Stockholm', '(GMT+01:00) Stockholm'),
(49, 'Europe/Vienna', '(GMT+01:00) Vienna'),
(50, 'Europe/Warsaw', '(GMT+01:00) Warsaw'),
(51, 'Europe/Zagreb', '(GMT+01:00) Zagreb'),
(52, 'Europe/Athens', '(GMT+02:00) Athens'),
(53, 'Europe/Bucharest', '(GMT+02:00) Bucharest'),
(54, 'Africa/Cairo', '(GMT+02:00) Cairo'),
(55, 'Africa/Harare', '(GMT+02:00) Harare'),
(56, 'Europe/Helsinki', '(GMT+02:00) Helsinki'),
(57, 'Europe/Istanbul', '(GMT+02:00) Istanbul'),
(58, 'Asia/Jerusalem', '(GMT+02:00) Jerusalem'),
(59, 'Europe/Kiev', '(GMT+02:00) Kyiv'),
(60, 'Europe/Minsk', '(GMT+02:00) Minsk'),
(61, 'Europe/Riga', '(GMT+02:00) Riga'),
(62, 'Europe/Sofia', '(GMT+02:00) Sofia'),
(63, 'Europe/Tallinn', '(GMT+02:00) Tallinn'),
(64, 'Europe/Vilnius', '(GMT+02:00) Vilnius'),
(65, 'Asia/Baghdad', '(GMT+03:00) Baghdad'),
(66, 'Asia/Kuwait', '(GMT+03:00) Kuwait'),
(67, 'Africa/Nairobi', '(GMT+03:00) Nairobi'),
(68, 'Asia/Riyadh', '(GMT+03:00) Riyadh'),
(69, 'Asia/Tehran', '(GMT+03:30) Tehran'),
(70, 'Europe/Moscow', '(GMT+04:00) Moscow'),
(71, 'Asia/Baku', '(GMT+04:00) Baku'),
(72, 'Europe/Volgograd', '(GMT+04:00) Volgograd'),
(73, 'Asia/Muscat', '(GMT+04:00) Muscat'),
(74, 'Asia/Tbilisi', '(GMT+04:00) Tbilisi'),
(75, 'Asia/Yerevan', '(GMT+04:00) Yerevan'),
(76, 'Asia/Kabul', '(GMT+04:30) Kabul'),
(77, 'Asia/Karachi', '(GMT+05:00) Karachi'),
(78, 'Asia/Tashkent', '(GMT+05:00) Tashkent'),
(79, 'Asia/Kolkata', '(GMT+05:30) Kolkata'),
(80, 'Asia/Kathmandu', '(GMT+05:45) Kathmandu'),
(81, 'Asia/Yekaterinburg', '(GMT+06:00) Ekaterinburg'),
(82, 'Asia/Almaty', '(GMT+06:00) Almaty'),
(83, 'Asia/Dhaka', '(GMT+06:00) Dhaka'),
(84, 'Asia/Novosibirsk', '(GMT+07:00) Novosibirsk'),
(85, 'Asia/Bangkok', '(GMT+07:00) Bangkok'),
(86, 'Asia/Ho_Chi_Minh', '(GMT+07.00) Ho Chi Minh'),
(87, 'Asia/Jakarta', '(GMT+07:00) Jakarta'),
(88, 'Asia/Krasnoyarsk', '(GMT+08:00) Krasnoyarsk'),
(89, 'Asia/Chongqing', '(GMT+08:00) Chongqing'),
(90, 'Asia/Hong_Kong', '(GMT+08:00) Hong Kong'),
(91, 'Asia/Kuala_Lumpur', '(GMT+08:00) Kuala Lumpur'),
(92, 'Australia/Perth', '(GMT+08:00) Perth'),
(93, 'Asia/Singapore', '(GMT+08:00) Singapore'),
(94, 'Asia/Taipei', '(GMT+08:00) Taipei'),
(95, 'Asia/Ulaanbaatar', '(GMT+08:00) Ulaan Bataar'),
(96, 'Asia/Urumqi', '(GMT+08:00) Urumqi'),
(97, 'Asia/Irkutsk', '(GMT+09:00) Irkutsk'),
(98, 'Asia/Seoul', '(GMT+09:00) Seoul'),
(99, 'Asia/Tokyo', '(GMT+09:00) Tokyo'),
(100, 'Australia/Adelaide', '(GMT+09:30) Adelaide'),
(101, 'Australia/Darwin', '(GMT+09:30) Darwin'),
(102, 'Asia/Yakutsk', '(GMT+10:00) Yakutsk'),
(103, 'Australia/Brisbane', '(GMT+10:00) Brisbane'),
(104, 'Australia/Canberra', '(GMT+10:00) Canberra'),
(105, 'Pacific/Guam', '(GMT+10:00) Guam'),
(106, 'Australia/Hobart', '(GMT+10:00) Hobart'),
(107, 'Australia/Melbourne', '(GMT+10:00) Melbourne'),
(108, 'Pacific/Port_Moresby', '(GMT+10:00) Port Moresby'),
(109, 'Australia/Sydney', '(GMT+10:00) Sydney'),
(110, 'Asia/Vladivostok', '(GMT+11:00) Vladivostok'),
(111, 'Asia/Magadan', '(GMT+12:00) Magadan'),
(112, 'Pacific/Auckland', '(GMT+12:00) Auckland'),
(113, 'Pacific/Fiji', '(GMT+12:00) Fiji'),
(114, 'America/Santo_Domingo', '(GMT-04:00) Santo_Domingo');

-- --------------------------------------------------------

--
-- Table structure for table `time_format`
--

DROP TABLE IF EXISTS `time_format`;
CREATE TABLE IF NOT EXISTS `time_format` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `time_format`
--

INSERT INTO `time_format` (`id`, `format`) VALUES
(1, 'H:i:s'),
(2, 'H.i.s');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL,
  `is_delete` int(11) NOT NULL,
  `ext` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country_code` int(11) NOT NULL,
  `phone_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent_sign` text COLLATE utf8_unicode_ci NOT NULL,
  `account_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `assign_group` int(10) UNSIGNED DEFAULT NULL,
  `primary_dpt` int(10) UNSIGNED DEFAULT NULL,
  `agent_tzone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `daylight_save` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `limit_access` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `directory_listing` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vacation_mode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profile_pic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_language` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_verify` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `email_verify` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `is_login` tinyint(1) NOT NULL,
  `not_accept_ticket` tinyint(1) NOT NULL,
  `ldap_username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isldapauth` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_unique` (`mobile`),
  KEY `assign_group_3` (`assign_group`),
  KEY `primary_dpt_2` (`primary_dpt`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--


INSERT INTO `users` (`id`, `user_name`, `first_name`, `last_name`, `gender`, `email`, `password`, `active`, `is_delete`, `ext`, `country_code`, `phone_number`, `mobile`, `agent_sign`, `account_type`, `account_status`, `assign_group`, `primary_dpt`, `agent_tzone`, `daylight_save`, `limit_access`, `directory_listing`, `vacation_mode`, `company`, `role`, `internal_note`, `profile_pic`, `remember_token`, `created_at`, `updated_at`, `location`, `user_language`, `mobile_verify`, `email_verify`, `is_login`, `not_accept_ticket`, `ldap_username`, `isldapauth`) VALUES
(1, 'demo_admin', 'Demo', 'Admin', 1, 'mr.dmartin1984@gmail.com', '$2y$10$KQ6KXyamhJhtH1UvwxWMle2LJUfQ/nU.IpNf9KqYEMyodYw.7oPoi', 1, 0, '', 0, '', NULL, '', '', '', 1, 1, '79', '', '', '', '', '', 'admin', '', '4388.user8-128x128.jpg', 'tbaVDQTgGLjckn5SAcymhhnB6mOlAQIhD48sY4zBROgrzjI8NLXycw5qRJoi', '2017-03-29 05:23:28', '2018-03-29 02:02:43', NULL, 'en', '1', '1', 1, 0, '', 0),
(2, 'andrew', 'Andrew', 'Thomas', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '6786758969', '996578578', '', '', '', 1, 1, '9', '', '', '', '', '', 'agent', '', '', 'OM3EfLS8u1KtGOhNkvigWfsZgWISAzdPnQhm8PafMCJ98snxdLHItRuJjF3i', '2017-03-29 05:43:44', '2017-03-30 00:59:45', NULL, NULL, '1', '1', 0, 0, '', 0),
(3, 'ashley', 'Ashley', 'Knite', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '44675875987', '85765857587', '', '', '', 1, 1, '3', '', '', '', '', '', 'admin', '', '', NULL, '2017-03-29 05:44:51', '2017-03-29 05:44:51', NULL, NULL, '1', '1', 0, 0, '', 0),
(4, 'jruth', 'Tim', 'West', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '7657965879', '8769869686', '', '', '', 2, 2, '6', '', '', '', '', '', 'agent', '', '', 'W1HjnNsR6Ftt5iShQYIziwxqg3ANPpr0Fb855XkNtYhpwmQI2GtcmXEAs6P4', '2017-03-29 05:46:01', '2017-03-30 00:27:02', NULL, NULL, '1', '1', 1, 0, '', 0),
(5, 'ayesha', 'Ayesha', 'Lance', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 98, '8787696969', '765875967', '', '', '', 1, 1, '8', '', '', '', '', '', 'agent', '', '', 'JLvCNy7vrN6PkEHUQ8PJFZqWNaKHdze16kOcQsF4FRagDgvGDAOrba4tLiEk', '2017-03-29 05:47:46', '2017-03-30 01:00:07', NULL, NULL, '1', '1', 1, 0, '', 0),
(6, 'Anna', 'Anna', 'Christopher ', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '7980789908', '9879070707', '', '', '', 4, 1, '5', '', '', '', '', '', 'agent', '', '', '83G5Q9zhCqknwhhXqxKTY6T1S9Vlf83TRr26T4LibVYvKKp3sPyyAh5KtoeK', '2017-03-29 05:48:48', '2017-03-30 00:40:38', NULL, NULL, '1', '1', 0, 0, '', 0),
(7, 'demo_agent', 'Emila', 'Verban', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '578575987', '789879687', '', '', '', 3, 3, '7', '', '', '', '', '', 'agent', '', '5606.user7-128x128.jpg', 'Az8Or7PEdHd70Bz4vU1S2RhnhDmIiqM0bAQEHeRtbYeQ6s27vLxwHFTStICD', '2017-03-29 05:49:54', '2017-04-02 17:25:27', NULL, NULL, '1', '1', 0, 0, '', 0),
(8, 'sara', 'Sara', 'Lance', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '69867987980', '89709870907', '', '', '', 1, 1, '5', '', '', '', '', '', 'agent', '', '', 'HmuvCCNJqvxd9Trna2gxZ48VG46LMMbxrjlqpAPLUw9oTmtCasPzmBsPXhRC', '2017-03-29 05:51:23', '2017-03-30 00:54:47', NULL, NULL, '1', '1', 0, 0, '', 0),
(9, 'albert', 'Albert', '', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '789876896897', '658758975898', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'vmJVaUReG5bBNn0QeF2Mqx8XQ0dNVy8ZAMa7s95mM6VSGB9D6MxPYLNon5jp', '2017-03-29 05:53:25', '2017-03-30 01:14:59', NULL, NULL, '1', '1', 0, 0, '', 0),
(10, 'alisha', 'Alisha', '', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '598769876986', '5785876986986', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'cBcN1V6PuXSpSYtrFalvrirIiDdcgyaRslkKi1r0vn31pBDOqWg6OUeKRg7t', '2017-03-29 05:54:04', '2017-03-30 01:04:07', NULL, NULL, '1', '1', 0, 0, '', 0),
(11, 'david', 'David', '', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '57858758676', '68578589757', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'h8BXcgskcvTHLetmnkbadHMllQrbOWer8ZBvAiX8fAZpRHUIOSIVuDkQQe3x', '2017-03-29 05:54:47', '2017-03-30 00:46:24', NULL, NULL, '1', '1', 0, 0, '', 0),
(12, 'aston', 'Aston', '', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '795876986879', '8758798769', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'usCbizppuRcS89bBYpaYlBUC6cjSp7XD0GfQa0qLrdeq5egFJby6tYYhMQYH', '2017-03-29 05:55:33', '2017-03-30 00:21:42', NULL, NULL, '1', '1', 0, 0, '', 0),
(13, 'isha', 'Isha', 'Nagraj', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '758795987', '87587598659876', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', NULL, '2017-03-29 05:56:12', '2017-03-29 05:56:12', NULL, NULL, '1', '1', 0, 0, '', 0),
(14, 'joslina', 'Joslina', 'Carter ', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '786987698767', '587587969698', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', '5agBPUQxvqAaCTCpBxfxkAfqcUof0xof1yPgJD91m2Qg55CpeX1pR4v0Kfif', '2017-03-29 05:57:02', '2017-03-30 01:17:23', NULL, NULL, '1', '1', 0, 0, '', 0),
(15, 'louis', 'Louis', 'Philippe ', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '879686980690', '65875987689', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'iimMyC9578TJFham4VQv8QlrdQXR81gTvc5ykPeVEJzk4IGUlQb5UMmSfA0y', '2017-03-29 05:57:39', '2017-03-29 23:42:05', NULL, NULL, '1', '1', 0, 0, '', 0),
(16, 'Mathew ', 'Mathew', 'Murdoch ', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '7957896869', '66857986986', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'OqnTWi6FLLRjjjHGW2iz8TxbGfjna95oQkZq6z6YUNZPzXMFYHq6m1RqU0TV', '2017-03-29 05:58:33', '2017-03-30 00:34:13', NULL, NULL, '1', '1', 0, 0, '', 0),
(17, 'mary', 'Mary', 'Rubin ', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '8796869868', '8758796986', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'P0YqjrWmi1tn3SbGJ5FPBN0MsyTyLsLawdrw2qkWxOasremOf573PQXJbknC', '2017-03-29 05:59:11', '2017-03-30 00:39:27', NULL, NULL, '1', '1', 0, 0, '', 0),
(18, 'peter', 'Peter', 'Parker', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '8969860987', '59876098698', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'gJsgZlmPeargzTvj3Zt1iW5MLWKOBHYOeQruY89DeQxB0RQiP4q2dhGlsySm', '2017-03-29 06:00:45', '2017-03-30 00:54:02', NULL, NULL, '1', '1', 0, 0, '', 0),
(19, 'samual', 'Samual', 'John ', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '5879687698', '7658756986', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'HCj8REFW5ys8CIbrBBwsUnzFw6GIzFyakhU07NPIBdNkNYHiZRxowID3iytw', '2017-03-29 06:01:40', '2017-03-30 00:58:32', NULL, NULL, '1', '1', 0, 0, '', 0),
(20, 'sebastian', 'Sebastian', 'Diesel', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '5795875899', '875875798756', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'NajCkg6mh5wZQqeQGnBO66wHp8lsyu5VXtDXOT8GTKedQZg0b1d1gQTKSBtx', '2017-03-29 06:02:26', '2017-03-30 01:10:44', NULL, NULL, '1', '1', 0, 0, '', 0),
(21, 'demo_client', 'Jim', 'Travis', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '78968689689698', '68758975454', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '6441.user1-128x128.jpg', 'lB2Lw98YugrGxZ4C7lrCjGM5TPSGUY6iVwfZYlq1tYOQLKilPO4JWI3P1Dzj', '2017-03-29 06:03:13', '2017-04-02 17:43:31', NULL, NULL, '1', '1', 0, 0, '', 0),
(22, 'sean', 'Sean', 'Paul', 1, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '78957698679', '658758969', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', NULL, '2017-03-29 06:03:59', '2017-03-29 06:03:59', NULL, NULL, '1', '1', 0, 0, '', 0),
(23, 'xiayu', 'Xiayu', 'Won Pu', 0, NULL, '$2y$10$GvyOKyIIwZcRacB0GU7wI.MKIzHnhAkrAan8/WO0FynEtd0Nze1ia', 1, 0, '', 91, '87587959879', '4765758975', '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', 'egSfRkvdehSANyCtKXmpESnKqcbNw1pDu255Vh7cOXKmf46CaDkryhUDY1G3', '2017-03-29 06:04:50', '2017-03-30 01:04:20', NULL, NULL, '1', '1', 1, 0, '', 0),
(24, 'henrik.olsson', 'Henrik', 'Olsson', 0, NULL, '$2y$10$eJ0.4WmnryOhCxFRnYjFMuZU5ZBmD7v/skO3/9j6FC7Nq9HEc.ZDa', 1, 0, '', 0, '', NULL, '', '', '', NULL, NULL, '', '', '', '', '', '', 'user', '', '', NULL, '2017-08-31 05:20:13', '2017-08-31 05:20:13', NULL, NULL, 'oRe9dqP5bfT4cR8RxJrOQkmDg25Ms9uPzOAtnmAWPJH1Pef5g6H1TJykFEPU', 'oRe9dqP5bfT4cR8RxJrOQkmDg25Ms9uPzOAtnmAWPJH1Pef5g6H1TJykFEPU', 0, 0, '', 0),
(25, 'manish', 'Manish', 'Verma', 0, NULL, '$2y$10$o9DNnaE5sj38FFaR5J0mKOq6q3PhtiNTOu0cEFIezyZV7TFEspjxa', 1, 0, '', 0, '', NULL, '', '', '', NULL, 1, '14', '', '', '', '', '', 'admin', '', '', NULL, '2017-10-04 05:44:57', '2017-10-04 05:44:57', NULL, NULL, 'verifymobileifenable', '1', 0, 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_additional_infos`
--

DROP TABLE IF EXISTS `user_additional_infos`;
CREATE TABLE IF NOT EXISTS `user_additional_infos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `service` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_assign_organization`
--

DROP TABLE IF EXISTS `user_assign_organization`;
CREATE TABLE IF NOT EXISTS `user_assign_organization` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `org_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `org_department` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `org_id` (`org_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_assign_organization`
--

INSERT INTO `user_assign_organization` (`id`, `org_id`, `user_id`, `role`, `org_department`, `created_at`, `updated_at`) VALUES
(1, 4, 9, 'members', '', '2017-03-29 05:53:25', '2017-03-29 05:53:25'),
(2, 3, 10, 'members', '', '2017-03-29 05:54:04', '2017-03-29 05:54:04'),
(3, 1, 11, 'members', '', '2017-03-29 05:54:47', '2017-03-29 05:54:47'),
(4, 5, 12, 'members', '', '2017-03-29 05:55:33', '2017-03-29 05:55:33'),
(5, 5, 13, 'members', '', '2017-03-29 05:56:12', '2017-03-29 05:56:12'),
(6, 4, 14, 'members', '', '2017-03-29 05:57:02', '2017-03-29 05:57:02'),
(7, 2, 15, 'members', '', '2017-03-29 05:57:39', '2017-03-29 05:57:39'),
(8, 2, 16, 'members', '', '2017-03-29 05:58:33', '2017-03-29 05:58:33'),
(9, 2, 17, 'members', '', '2017-03-29 05:59:11', '2017-03-29 05:59:11'),
(10, 1, 18, 'members', '', '2017-03-29 06:00:45', '2017-03-29 06:00:45'),
(11, 1, 19, 'members', '', '2017-03-29 06:01:40', '2017-03-29 06:01:40'),
(12, 3, 20, 'members', '', '2017-03-29 06:02:26', '2017-03-29 06:02:26'),
(13, 4, 21, 'members', '', '2017-03-29 06:03:13', '2017-03-29 06:03:13'),
(14, 5, 22, 'members', '', '2017-03-29 06:03:59', '2017-03-29 06:03:59'),
(15, 3, 23, 'members', '', '2017-03-29 06:04:50', '2017-03-29 06:04:50'),
(16, 1, 2, 'members', '', '2017-03-29 22:52:01', '2017-03-29 22:52:01'),
(17, 2, 4, 'members', '', '2017-03-29 22:52:17', '2017-03-29 22:52:17'),
(18, 3, 5, 'members', '', '2017-03-29 22:52:29', '2017-03-29 22:52:29'),
(19, 4, 7, 'members', '', '2017-03-29 22:52:44', '2017-03-29 22:52:44'),
(20, 5, 8, 'members', '', '2017-03-29 22:53:11', '2017-03-29 22:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `version_check`
--

DROP TABLE IF EXISTS `version_check`;
CREATE TABLE IF NOT EXISTS `version_check` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `current_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `new_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `version_check`
--

INSERT INTO `version_check` (`id`, `current_version`, `new_version`, `created_at`, `updated_at`) VALUES
(1, '', '', '2017-03-29 05:23:25', '2017-03-29 05:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

DROP TABLE IF EXISTS `widgets`;
CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `widgets`
--

INSERT INTO `widgets` (`id`, `name`, `title`, `value`, `created_at`, `updated_at`) VALUES
(1, 'footer1', 'Our Services', '<ul><li>Fashion</li><li>Footwear</li><li>Accessories</li><li>Electronic Gadgets</li><li>Home appliances</li></ul><br /><br />', '2017-03-29 05:23:25', '2017-04-02 11:43:15'),
(2, 'footer2', 'Company', '<ul><li>About us</li><li>Privacy</li><li>Sell goods with us</li><li>Cancellation &amp; Refund Policy</li><li>Term &amp; Condition</li></ul>', '2017-03-29 05:23:25', '2017-04-02 11:40:50'),
(3, 'footer3', 'Find out more', '<ul><li>Our Forum</li><li>News</li><li>Blog</li><li>Become a partner </li></ul>', '2017-03-29 05:23:25', '2017-04-02 11:38:16'),
(4, 'footer4', 'Contact Us', '<p><i>600 N Clark St, Chicago, IL 60610, USA</i></p><p><i>Telephone: </i><i>+91 9999999999</i></p><p><i>Email: </i><a><i>   support@champps.com</i></a></p>', '2017-03-29 05:23:25', '2017-03-31 06:51:07'),
(7, 'linkedin', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(8, 'stumble', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(9, 'google', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(10, 'deviantart', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(11, 'flickr', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(12, 'skype', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(13, 'rss', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(14, 'twitter', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(15, 'facebook', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(16, 'youtube', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(17, 'vimeo', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(18, 'pinterest', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(19, 'dribbble', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25'),
(20, 'instagram', NULL, NULL, '2017-03-29 05:23:25', '2017-03-29 05:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_action`
--

DROP TABLE IF EXISTS `workflow_action`;
CREATE TABLE IF NOT EXISTS `workflow_action` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `condition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `custom_action` longtext COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_action_1` (`workflow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workflow_close`
--

DROP TABLE IF EXISTS `workflow_close`;
CREATE TABLE IF NOT EXISTS `workflow_close` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `days` int(11) NOT NULL,
  `condition` int(11) NOT NULL,
  `send_email` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `workflow_close`
--

INSERT INTO `workflow_close` (`id`, `days`, `condition`, `send_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 3, '2017-03-29 05:23:23', '2017-03-29 05:23:23');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_name`
--

DROP TABLE IF EXISTS `workflow_name`;
CREATE TABLE IF NOT EXISTS `workflow_name` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_note` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workflow_rules`
--

DROP TABLE IF EXISTS `workflow_rules`;
CREATE TABLE IF NOT EXISTS `workflow_rules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `matching_criteria` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `matching_scenario` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `matching_relation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `matching_value` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `custom_rule` longtext COLLATE utf8_unicode_ci,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_rules_1` (`workflow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
