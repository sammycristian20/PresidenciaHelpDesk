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
-- Database: `development`
--
-- -------------------------------------------------------

-- 
-- Alter organisation_department column in user_assign_organisation
-- 
ALTER TABLE `user_assign_organization` CHANGE `org_department` `org_department` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


-- --------------------------------------------------------

--
-- Table structure for table `ticket_status_override`
--

DROP TABLE IF EXISTS `ticket_status_override`;
CREATE TABLE IF NOT EXISTS `ticket_status_override` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `current_status` int(11) NOT NULL,
  `target_status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -------------------------------------------------------

--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(31, 'rating', '2018-04-17 22:56:39', '2018-04-17 22:56:39'),
(32, 'rating-confirmation', '2018-04-17 22:56:39', '2018-04-17 22:56:39');
COMMIT;

-- -------------------------------------------------------

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'rating', '1', 31, 'Rating', '<div><p><strong>Rating (overall rating)</strong></p><p><strong>How would you rate overall support for the ticket {!! $ticket_link !!}?</strong></p><p>&nbsp;</p><form action=\"{!! $ratingUrl !!}\" method=\"post\"><input name=\"_token\" type=\"hidden\" value=\"{!! $session_id !!}\" /> &nbsp;<input name=\"OverAll Satisfaction\" type=\"radio\" value=\"1\" /> Unsatisfactory<br /><br />&nbsp;<input name=\"OverAll Satisfaction\" type=\"radio\" value=\"2\" /> Fair<br /><br />&nbsp;<input name=\"OverAll Satisfaction\" type=\"radio\" value=\"3\" />&nbsp;Satisfactory<br /><br />&nbsp;<input name=\"OverAll Satisfaction\" type=\"radio\" value=\"4\" /> Good<br /><br />&nbsp;<input name=\"OverAll Satisfaction\" type=\"radio\" value=\"5\" /> Excellent<br /><br /><input type=\"submit\" value=\"SUBMIT\" />&nbsp;</form></div>Kind Regards,<br />{!! $system_from !!}', '', id, '2018-04-17 22:56:39', '2018-04-17 22:56:39', 'common-tmeplates'
FROM `template_sets`;

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'rating-confirmation', '1', 32, 'Rating Submitted', '<p>Hello {!! $receiver_name !!},<br /><br />Overall rating has been submitted for ticket {!! $ticket_link !!}Kind Regards,<br />{!! $system_from !!}', '', id, '2018-04-17 22:56:39', '2018-04-17 22:56:39', 'common-tmeplates'
FROM `template_sets`;


-- -------------------------------------------------------

-- 
-- Alter organisation_department column in user_assign_organisation
-- 
UPDATE `settings_system` SET `version` = 'v1.9.28';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
