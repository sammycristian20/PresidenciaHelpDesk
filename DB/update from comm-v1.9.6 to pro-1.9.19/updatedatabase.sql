-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 12, 2017 at 06:42 AM
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
-- Database: `master_pro`
--


-- ------------------------------------------------------------------------
--                        Create Tables                                   |   
-- ------------------------------------------------------------------------

-- 
-- Create all tables which are not present in community database
-- 


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
(1, 'approval', '0', '2017-12-11 05:45:20', '2017-12-11 05:45:20');

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
  `timezone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`id`, `name`, `description`, `status`, `timezone`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Default Business-Hours', 'default', 1, 'Asia/Kolkata', 1, '2017-12-11 05:45:20', '2017-12-11 05:50:42');

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
(1, 1, 'Sunday', 'Closed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(2, 1, 'Monday', 'Open_fixed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(3, 1, 'Tuesday', 'Open_fixed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(4, 1, 'Wednusday', 'Open_fixed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(5, 1, 'Thursday', 'Open_fixed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(6, 1, 'Friday', 'Open_fixed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(7, 1, 'Saturday', 'Closed', '2017-12-11 05:45:20', '2017-12-11 05:45:20');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `department_assign_agents`
--

INSERT INTO `department_assign_agents` (`id`, `department_id`, `agent_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2017-12-11 05:45:21', '2017-12-11 05:45:21');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, 'followup', '', '', '2017-12-11 05:45:20', '2017-12-11 05:45:20');

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `json` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `form`, `json`, `created_at`, `updated_at`) VALUES
(2, 'user', '[{ \'title\': \'First Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'First Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'First Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'first_name\' },{ \'title\': \'Last Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Last Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Last Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'last_name\' }, { \'title\': \'User Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'User Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'User Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':false, \'agentRequiredFormSubmit\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'user_name\' },{ \'title\': \'Work Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Work Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Work Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'phone_number\' },{ \'title\': \'Email\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Email\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Email\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'email\', \'agentRequiredFormSubmit\':true, \'customerDisplay\':true, \'customerRequiredFormSubmit\':true, \'value\':\'\', \'default\':\'yes\', \'unique\':\'email\' },{ \'title\': \'Mobile Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Mobile Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Mobile Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'mobile\' },{ \'title\': \'Address\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'no\', \'unique\':\'address\' },{ \'title\': \'Organisation\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organisation\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organisation\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'organisation\' },{ \'title\': \'Department Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'department\', \'options\':[ ], \'api\':\'organisationdept\' }, { \'title\': \'Captcha\', \'agentShow\':true, \'customerDisplay\':true, \'default\':\'yes\' }]', NULL, NULL),
(3, 'organisation', '[{ \'title\': \'Company Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Company Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Company Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'name\' },{ \'title\': \'Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'phone\' },{ \'title\': \'Company Domain Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Company Domain Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Company Domain Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'domain\' },{ \'title\': \'Description\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'internal_notes\' },{ \'title\': \'Address\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'address\' }, { \'title\': \'Department\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'department\' }]', NULL, NULL),
(5, 'ticket', '[{\"title\":\"Requester\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Requester\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Requester\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"email\",\"agentCCfield\":true,\"customerCCfield\":false,\"customerDisplay\":true,\"agentRequiredFormSubmit\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"requester\"},{\"title\":\"Subject\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Subject\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Subject\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"text\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"default\":\"yes\",\"value\":\"\",\"unique\":\"subject\"},{\"title\":\"Type\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Type\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Type\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"api\":\"type\",\"options\":[],\"default\":\"yes\",\"unique\":\"type\"},{\"title\":\"Status\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Status\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Status\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"status\",\"options\":[],\"default\":\"yes\",\"unique\":\"status\"},{\"title\":\"Priority\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Priority\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Priority\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"api\":\"priority\",\"options\":[],\"default\":\"yes\",\"unique\":\"priority\"},{\"title\":\"Location\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Location\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Location\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"location\",\"options\":[],\"default\":\"yes\",\"unique\":\"location\"},{\"title\":\"Department\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Department\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Department\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"multiselect\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"department\",\"options\":[{\"id\":1,\"optionvalue\":[{\"language\":\"en\",\"option\":\"Support\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"nodes\":[]},{\"id\":2,\"optionvalue\":[{\"language\":\"en\",\"option\":\"Sales\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"nodes\":[]},{\"id\":3,\"optionvalue\":[{\"language\":\"en\",\"option\":\"Operation\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"nodes\":[]}],\"default\":\"yes\",\"unique\":\"department\",\"linkHelpTopic\":true},{\"title\":\"Help Topic\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Help Topic\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Help Topic\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"multiselect\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":true,\"value\":\"\",\"api\":\"helptopic\",\"options\":[{\"id\":1,\"optionvalue\":[{\"language\":\"en\",\"option\":\"Support query\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"nodes\":[]}],\"default\":\"yes\",\"unique\":\"help_topic\"},{\"title\":\"Assigned\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Assigned\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Assigned\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"value\":\"\",\"api\":\"assigned_to\",\"options\":[],\"default\":\"yes\",\"unique\":\"assigned\"},{\"title\":\"Description\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Description\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Description\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"textarea\",\"agentRequiredFormSubmit\":true,\"customerDisplay\":true,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"unique\":\"description\",\"media_option\":true},{\"title\":\"Company\",\"agentlabel\":[{\"language\":\"en\",\"label\":\"Company\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"clientlabel\":[{\"language\":\"en\",\"label\":\"Company\",\"flag\":\"http:\\/\\/localhost\\/ladybird\\/master\\/pro\\/Faveo-Helpdesk-Pro\\/public\\/lb-faveo\\/flags\\/en.png\"}],\"type\":\"select\",\"agentRequiredFormSubmit\":false,\"customerDisplay\":false,\"customerRequiredFormSubmit\":false,\"default\":\"yes\",\"value\":\"\",\"api\":\"company\",\"options\":[],\"unique\":\"company\"},{\"title\":\"Captcha\",\"agentShow\":true,\"customerDisplay\":true,\"default\":\"yes\"}]', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `halts`
--

DROP TABLE IF EXISTS `halts`;
CREATE TABLE IF NOT EXISTS `halts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `halted_at` timestamp NOT NULL,
  `time_used` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `halted_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, 'Bangalore', '', '', '', 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20', 0),
(2, 'Delhi', '', '', '', 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20', 0),
(3, 'Kolkata', '', '', '', 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20', 0);

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permision`
--

-- 
-- Dumping data into permission tables
-- 
INSERT INTO `permision`(`user_id`, `permision`, `created_at`, `updated_at`)
SELECT `id`, '{"create_ticket":"1","edit_ticket":"1","transfer_ticket":"1","delete_ticket":"1","assign_ticket":"1","access_kb":"1","ban_email":"1"}', CURDATE(), CURDATE() FROM `users` where `role` != 'user';

-- --------------------------------------------------------

--
-- Table structure for table `pro_serial_key`
--

DROP TABLE IF EXISTS `pro_serial_key`;
CREATE TABLE IF NOT EXISTS `pro_serial_key` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` text COLLATE utf8_unicode_ci NOT NULL,
  `serial_key` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pro_serial_key`
--

-- INSERT INTO `pro_serial_key` (`id`, `order_id`, `serial_key`, `created_at`, `updated_at`) VALUES
-- (1, 'eyJpdiI6ImZDS2ppQjZOR2VpeFlrS2Rpa2FtaEE9PSIsInZhbHVlIjoidFk4WTI5cHJhd1VcL3RJOVFkbEVjcGc9PSIsIm1hYyI6IjUwMGViYTZhM2JjZTM5ZjUzOTIxMTkxNjdkNTZkNmEyMzA1NzYzNTBhZjdkMjhlNjQ3ODlhYzFhOWUyNmRjM2YifQ==', 'eyJpdiI6IlRIZ1JBMnVDeXdUMzhrbmd3XC9jdlNnPT0iLCJ2YWx1ZSI6ImZGYVhuSlNHYk4xTGN3RDlJeGxqdU5wcGNOMm1jWjd5eWh5K1p6ZmNMNjA9IiwibWFjIjoiNTBjZDA4OGEzYzFlY2YwOGMzZmFiYzgyOWIxZTY0YzY0ZjY0ZmM2N2E4MmRhZGU4YTMwNDljODA1MjUxYmM3ZiJ9', NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `requireds`
--

INSERT INTO `requireds` (`form`, `field`, `agent`, `client`, `parent`, `option`, `label`, `created_at`, `updated_at`) VALUES
('ticket', 'assigned', 'required', NULL, NULL, '', 'Assigned', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'description', 'required', NULL, NULL, '', 'Description', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'department', 'required', NULL, NULL, '', 'Department', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'help_topic', 'required', 'required', NULL, '', 'Help Topic', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'location', NULL, NULL, NULL, '', 'Location', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'priority', 'required', 'required', NULL, '', 'Priority', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'type', 'required', 'required', NULL, '', 'Type', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'status', 'required', NULL, NULL, '', 'Status', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('user', 'first_name', 'required', 'required', NULL, '', 'First Name', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'last_name', 'required', 'required', NULL, '', 'Last Name', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'user_name', NULL, NULL, NULL, '', 'User Name', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'phone_number', NULL, NULL, NULL, '', 'Work Phone', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'email', 'required', 'required', NULL, '', 'Email', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'mobile', NULL, NULL, NULL, '', 'Mobile Phone', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'address', NULL, NULL, NULL, '', 'Address', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'organisation', NULL, NULL, NULL, '', 'Organisation', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', 'department', NULL, NULL, NULL, '', 'Department Name', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('user', '', NULL, NULL, NULL, '', '', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('organisation', 'name', 'required', 'required', NULL, '', 'Company Name', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('organisation', 'phone', NULL, NULL, NULL, '', 'Phone', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('organisation', 'domain', NULL, NULL, NULL, '', 'Company Domain Name', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('organisation', 'internal_notes', NULL, NULL, NULL, '', 'Description', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('organisation', 'address', NULL, NULL, NULL, '', 'Address', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('organisation', 'department', NULL, NULL, NULL, '', 'Department', '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
('ticket', 'subject', 'required', 'required', NULL, '', 'Subject', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'requester', 'required', 'required', NULL, '', 'Requester', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', 'company', NULL, NULL, NULL, '', 'Company', '2017-12-11 23:32:02', '2017-12-11 23:32:02'),
('ticket', '', NULL, NULL, NULL, '', '', '2017-12-11 23:32:02', '2017-12-11 23:32:02');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, 'Database', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'File', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
  `location_id` int(11) DEFAULT NULL,
  `approval_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_changes_requester_foreign` (`requester`),
  KEY `sd_changes_status_id_foreign` (`status_id`),
  KEY `sd_changes_priority_id_foreign` (`priority_id`),
  KEY `sd_changes_change_type_id_foreign` (`change_type_id`),
  KEY `sd_changes_impact_id_foreign` (`impact_id`),
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
(1, 'Low', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'Medium', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'High', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(4, 'Urgent', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
(1, 'Open', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'Planning', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'Awaiting Approval', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(4, 'Pending Release', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(5, 'Pending Review', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(6, 'Closed', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
(1, 'Minor', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'Standard', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'Major', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(4, 'Emergency', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_impact_types`
--

INSERT INTO `sd_impact_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Low', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'Medium', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'High', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_location_categories`
--

INSERT INTO `sd_location_categories` (`id`, `parent_id`, `name`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Asia', NULL, NULL),
(2, NULL, 'Europe', NULL, NULL),
(3, NULL, 'America', NULL, NULL),
(4, NULL, 'Australia', NULL, NULL);

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
(1, 'Low', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'Medium', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'High', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(4, 'Urgent', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
(1, 'Open', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'On Hold', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'In Progress', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(4, 'Incomplete', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(5, 'Completed', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
(1, 'Minor', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(2, 'Standard', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(3, 'Major', '2017-11-12 05:42:20', '2017-11-12 05:42:20'),
(4, 'Emergency', '2017-11-12 05:42:20', '2017-11-12 05:42:20');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sla_targets`
--

INSERT INTO `sla_targets` (`id`, `name`, `sla_id`, `priority_id`, `respond_within`, `resolve_within`, `business_hour_id`, `send_email`, `send_sms`, `created_at`, `updated_at`) VALUES
(1, 'Low', 1, 1, '5-hrs', '10-hrs', 1, 1, 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(2, 'Normal', 2, 2, '4-hrs', '9-hrs', 1, 1, 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(3, 'High', 3, 3, '2-hrs', '4-hrs', 1, 1, 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(4, 'Emergency', 4, 4, '1-hrs', '2-hrs', 1, 1, 0, '2017-12-11 05:45:20', '2017-12-11 05:45:20');

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
(1, 'skin-yellow', 'skin-blue', 'null', 'null', 'null', 'null', '0', '0', '2017-12-11 05:45:21', '2017-12-11 05:45:21');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_status_type`
--

INSERT INTO `ticket_status_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'open', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(2, 'closed', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(3, 'archieved', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(4, 'deleted', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(5, 'approval', '2017-12-11 05:45:20', '2017-12-11 05:45:20'),
(6, 'spam', '2017-12-11 05:45:20', '2017-12-11 05:45:20');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ticket_type`
--

INSERT INTO `ticket_type` (`id`, `name`, `status`, `type_desc`, `ispublic`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Question', 1, 'Question', 1, 1, '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
(2, 'Incident', 0, 'Incident', 0, 0, '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
(3, 'Problem', 0, 'Problem', 0, 0, '2017-12-11 05:45:22', '2017-12-11 05:45:22'),
(4, 'Feature Request', 0, 'Feature Request', 0, 0, '2017-12-11 05:45:22', '2017-12-11 05:45:22');

-- ------------------------------------------------------------------------
--                        Create Tables                                   |
-- ------------------------------------------------------------------------

-- 
-- Drop all tables which are not present in pro and not required anymore 
-- 

DROP TABLE IF EXISTS `custom_forms`;
DROP TABLE IF EXISTS `custom_form_fields`;
DROP TABLE IF EXISTS `field_values`;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS `log_notification`;
DROP TABLE IF EXISTS `notification_types`;
DROP TABLE IF EXISTS `user_notification`;

-- ------------------------------------------------------------------------
--                        Alter Tables                                    |   
-- ------------------------------------------------------------------------

-- 
-- Alter structure of various tables according to Pro version
-- 

-- 
-- Alter `department` table
-- 
ALTER TABLE `department` ADD `business_hour` INT(11) NOT NULL AFTER `updated_at`, ADD `nodes` LONGTEXT NULL DEFAULT NULL AFTER `business_hour`;

-- ----------------------------------------------------

-- 
-- Alter `failed_jobs` table 
--
ALTER TABLE `failed_jobs` ADD `exception` LONGTEXT NOT NULL AFTER `failed_at`;

-- ----------------------------------------------------

-- 
-- Alter `help_topic` table
-- 
ALTER TABLE `help_topic`
  DROP `priority`,
  DROP `sla_plan`,
  DROP `auto_assign`;

ALTER TABLE `help_topic` ADD `nodes` LONGTEXT NULL DEFAULT NULL AFTER `updated_at`, ADD `linked_departments` VARCHAR(5000) NULL DEFAULT NULL AFTER `nodes`;

-- ----------------------------------------------------

-- 
-- Alter jobs table
-- 
ALTER TABLE `jobs` DROP `reserved`;

-- ----------------------------------------------------

-- 
-- Alter `kb_article` table
-- 
ALTER TABLE `kb_article` ADD `template` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `sco_title` VARCHAR(255) NOT NULL AFTER `template`, ADD `meta_description` VARCHAR(255) NOT NULL AFTER `sco_title`;

-- ----------------------------------------------------

-- 
-- Alter `kb_category` table
-- 
ALTER TABLE `kb_category` ADD `display_order` INT(11) NOT NULL AFTER `updated_at`, ADD `visible_to` VARCHAR(255) NOT NULL AFTER `display_order`;

-- ----------------------------------------------------

-- 
-- Alter `kb_pages` table
-- 
ALTER TABLE `kb_pages` ADD `sco_title` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `meta_description` VARCHAR(255) NOT NULL AFTER `sco_title`;

-- ----------------------------------------------------

-- 
-- Alter `kb_settings` table
-- 
ALTER TABLE `kb_settings` ADD `status` INT(11) NOT NULL AFTER `updated_at`;

-- ----------------------------------------------------

-- 
-- Alter `migrations` table
-- 
ALTER TABLE `migrations` ADD `id` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);

-- ----------------------------------------------------

-- 
-- Alter `notifications` table
-- 
ALTER TABLE `notifications`
  DROP `model_id`,
  DROP `userid_created`,
  DROP `type_id`;

ALTER TABLE `notifications` ADD `message` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `by` VARCHAR(255) NOT NULL AFTER `message`, ADD `to` VARCHAR(255) NOT NULL AFTER `by`, ADD `seen` VARCHAR(255) NOT NULL AFTER `to`, ADD `table` VARCHAR(255) NULL DEFAULT NULL AFTER `seen`, ADD `row_id` INT(11) NULL DEFAULT NULL AFTER `table`, ADD `url` VARCHAR(255) NULL DEFAULT NULL AFTER `row_id`;

-- ----------------------------------------------------

-- 
-- Alter `organization` table
-- 
ALTER TABLE `organization` ADD `client_Code` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `phone1` VARCHAR(255) NOT NULL AFTER `client_Code`, ADD `line_of_business` VARCHAR(255) NOT NULL AFTER `phone1`, ADD `relation_type` VARCHAR(255) NOT NULL AFTER `line_of_business`, ADD `branch` VARCHAR(255) NOT NULL AFTER `relation_type`, ADD `fax` VARCHAR(255) NOT NULL AFTER `branch`, ADD `domain` VARCHAR(255) NOT NULL AFTER `fax`;

-- ----------------------------------------------------

-- 
-- Alter `settings_alert_notice` table
-- 
ALTER TABLE `settings_alert_notice`
  DROP `ticket_status`,
  DROP `ticket_admin_email`,
  DROP `ticket_department_manager`,
  DROP `ticket_department_member`,
  DROP `ticket_organization_accmanager`,
  DROP `message_status`,
  DROP `message_last_responder`,
  DROP `message_assigned_agent`,
  DROP `message_department_manager`,
  DROP `message_organization_accmanager`,
  DROP `internal_status`,
  DROP `internal_last_responder`,
  DROP `internal_assigned_agent`,
  DROP `internal_department_manager`,
  DROP `assignment_status`,
  DROP `assignment_assigned_agent`,
  DROP `assignment_team_leader`,
  DROP `assignment_team_member`,
  DROP `transfer_status`,
  DROP `transfer_assigned_agent`,
  DROP `transfer_department_manager`,
  DROP `transfer_department_member`,
  DROP `overdue_status`,
  DROP `overdue_assigned_agent`,
  DROP `overdue_department_manager`,
  DROP `overdue_department_member`,
  DROP `system_error`,
  DROP `sql_error`,
  DROP `excessive_failure`;

ALTER TABLE `settings_alert_notice` ADD `key` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `value` VARCHAR(255) NOT NULL AFTER `key`;

-- ----------------------------------------------------

-- 
-- Alter `settings_system` table
-- 
ALTER TABLE `settings_system` DROP `date_time_format`;

ALTER TABLE `settings_system` ADD `date_time_format` VARCHAR(50) NOT NULL AFTER `updated_at`, ADD `serial_key` VARCHAR(100) NOT NULL AFTER `time_zone`, ADD `order_number` VARCHAR(100) NOT NULL AFTER `serial_key`;

ALTER TABLE `settings_system` CHANGE `time_zone` `time_zone` VARCHAR(50) NULL;
-- ----------------------------------------------------

-- 
-- Alter `settings_ticket` table
-- 

ALTER TABLE `settings_ticket`
  DROP `priority`,
  DROP `sla`;

-- ----------------------------------------------------

-- 
-- Alter `sla_plan` plan
-- 
ALTER TABLE `sla_plan` ADD `sla_target` INT(11) NOT NULL AFTER `updated_at`, ADD `apply_sla_depertment` VARCHAR(255) NOT NULL AFTER `sla_target`, ADD `apply_sla_company` VARCHAR(255) NOT NULL AFTER `apply_sla_depertment`, ADD `apply_sla_tickettype` VARCHAR(255) NOT NULL AFTER `apply_sla_company`, ADD `apply_sla_ticketsource` VARCHAR(255) NOT NULL AFTER `apply_sla_tickettype`, ADD `apply_sla_helptopic` VARCHAR(255) NOT NULL AFTER `apply_sla_ticketsource`, ADD `apply_sla_orgdepts` VARCHAR(255) NOT NULL AFTER `apply_sla_helptopic`, ADD `apply_sla_labels` VARCHAR(255) NOT NULL AFTER `apply_sla_orgdepts`, ADD `apply_sla_tags` VARCHAR(255) NOT NULL AFTER `apply_sla_labels`;

-- ----------------------------------------------------

-- 
-- Alter `social_media` table
-- 

ALTER TABLE `social_media` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- ----------------------------------------------------

-- 
-- Alter `template_sets` table
-- 

ALTER TABLE `template_sets` ADD `template_language` VARCHAR(10) NOT NULL DEFAULT 'en' AFTER `updated_at`;

-- ----------------------------------------------------

-- 
-- Alter `templates` table
-- 

ALTER TABLE `templates` ADD `template_category` VARCHAR(100) NULL;

-- ----------------------------------------------------

-- 
-- Alter table `ticket_form_data` table
-- 

ALTER TABLE `ticket_form_data` ADD `key` VARCHAR(255) NULL AFTER `updated_at`;

-- ----------------------------------------------------

-- 
-- Alter `ticket_source` table
-- 
ALTER TABLE `ticket_source` ADD `description` LONGTEXT NULL DEFAULT NULL AFTER `css_class`, ADD `location` VARCHAR(255) NOT NULL AFTER `description`;

-- ----------------------------------------------------

-- 
-- Alter `ticket_status` table
-- 
ALTER TABLE `ticket_status`
  DROP `state`,
  DROP `mode`,
  DROP `flags`,
  DROP `sort`,
  DROP `email_user`,
  DROP `icon_class`,
  DROP `properties`;

ALTER TABLE `ticket_status` ADD `visibility_for_client` TINYINT(1) NOT NULL AFTER `updated_at`, ADD `allow_client` TINYINT(1) NOT NULL AFTER `visibility_for_client`, ADD `visibility_for_agent` TINYINT(1) NOT NULL AFTER `allow_client`, ADD `purpose_of_status` INT(11) NOT NULL AFTER `visibility_for_agent`, ADD `secondary_status` INT(11) NULL DEFAULT NULL AFTER `purpose_of_status`, ADD `send_email` VARCHAR(255) NULL DEFAULT NULL AFTER `secondary_status`, ADD `halt_sla` INT(11) NOT NULL AFTER `send_email`, ADD `order` INT(11) NOT NULL AFTER `halt_sla`, ADD `icon` VARCHAR(255) NOT NULL AFTER `order`, ADD `icon_color` VARCHAR(255) NOT NULL AFTER `icon`, ADD `default` INT(11) NULL DEFAULT NULL AFTER `icon_color`, ADD `send_sms` TINYINT(1) NOT NULL DEFAULT '0' AFTER `default`;

-- ----------------------------------------------------

-- 
-- Alter `ticket_thread` table
-- 
ALTER TABLE `ticket_thread` ADD `thread_type` VARCHAR(255) NULL DEFAULT NULL AFTER `updated_at`, ADD `response_time` VARCHAR(255) NULL DEFAULT NULL AFTER `thread_type`;

-- ----------------------------------------------------

-- 
-- Alter `tickets` table
-- 
ALTER TABLE `tickets` CHANGE `last_response_at` `first_response_time` DATETIME NULL DEFAULT NULL;

ALTER TABLE `tickets` ADD `resolution_time` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `is_response_sla` INT(11) NOT NULL AFTER `resolution_time`, ADD `is_resolution_sla` INT(11) NOT NULL AFTER `is_response_sla`, ADD `type` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `is_resolution_sla`;


-- ----------------------------------------------------

--
-- Alter `user_assign_organization` table
--
ALTER TABLE `user_assign_organization` ADD `role` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `org_department` VARCHAR(255) NOT NULL AFTER `role`;

-- ----------------------------------------------------



-- ------------------------------------------------------------------------
--                        Dumping data in tables                          |   
-- ------------------------------------------------------------------------


INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('login_restrictions', 'email', '', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('allow_users_to_create_ticket', '', '0', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('dummy_data_installation', '', '0', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('micro_organization_status', '', '0', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('user_registration', '', '0', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('user_reply_org_ticket', '', '0', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('user_show_org_ticket', '', '0', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40');


--
--
--
DELETE FROM `settings_alert_notice` WHERE `settings_alert_notice`.`id` = 1;

INSERT INTO `settings_alert_notice` (`key`, `value`, `created_at`, `updated_at`) VALUES
('new_ticket_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_ticket_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_ticket_alert_persons', 'admin,department_manager,department_members', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('ticket_assign_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('ticket_assign_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('ticket_assign_alert_persons', 'assigned_agent_team', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('notification_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('notification_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('notification_alert_persons', 'admin,agent,department_manager,team_lead', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('internal_activity_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('internal_activity_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('internal_activity_alert_persons', 'assigned_agent_team', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('ticket_transfer_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('ticket_transfer_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('ticket_transfer_alert_persons', 'assigned_agent_team,department_members', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('registration_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('registration_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('registration_alert_persons', 'new_user', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_user_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_user_alert_mode', 'system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_user_alert_persons', 'admin', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('reply_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('reply_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('reply_alert_persons', 'client', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('reply_notification_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('reply_notification_alert_mode', 'email,system', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('reply_notification_alert_persons', 'assigned_agent_team', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_ticket_confirmation_alert', '1', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_ticket_confirmation_alert_mode', 'email', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('new_ticket_confirmation_alert_persons', 'client', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('browser_notification_status', '0', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('api_id', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('rest_api_key', '', '2017-12-14 04:51:40', '2017-12-14 04:51:40'),
('browser_notification_alert_persons', 'agent,admin, client', '2017-12-14 04:51:40', '2017-12-14 04:51:40');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
