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

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `expired_at` datetime NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reports_hash_unique` (`hash`),
  KEY `reports_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);


--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(33, 'report-export', '2018-07-18 23:48:16', '2018-07-18 23:48:16');

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'rating', '1', 33, 'Helpdesk report available for download', '<p>Hello {!! $receiver_name !!},<br /><br />{!! $report_type !!} is successfully generated and ready for download.<br /><br /><strong>Download link</strong><br />{!! $report_link !!}<br /><br />***Please note this link will be expired in {!! $report_expiry !!}.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', id, '2018-04-17 22:56:39', '2018-04-17 22:56:39', 'agent-templates'
FROM `template_sets`;



-- 
-- Alter organisation_department column in user_assign_organisation
-- 
UPDATE `settings_system` SET `version` = 'v1.9.35';

-- 
-- Emptying bra_notifications table 
-- 
TRUNCATE `bar_notifications`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
