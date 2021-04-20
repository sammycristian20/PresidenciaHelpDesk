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
-- Update settings system table
--
UPDATE `settings_system` SET `version` = 'v1.9.45';

-- --------------------------------------------------------

--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(35, 'registration-and-verify', '2018-07-18 23:48:16', '2018-07-18 23:48:16');

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'template-register-confirmation-with-account-details-and-activation', '1', 35, 'Registration confirmed, account verifcation required', 'Hello {!! $receiver_name !!},<br /><br />This email is confirmation that you are now registered at our helpdesk.<br /><br /><strong>Registered Email:</strong> {!! $new_user_email !!}<br /><strong>Password:</strong> {!! $user_password !!}<br /><br />Please click on the below link to activate your account and Login to the system<br/>{!! $account_activation_link !!}<br/><br/>You can visit the helpdesk to browse articles and contact us at any time: {!! $system_link !!}<br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', id, '2018-11-07 01:02:01', '2018-11-07 01:02:01', 'common-tmeplates'
FROM `template_sets`;

-- --------------------------------------------------------

--
-- Alter reports table
--

ALTER TABLE `reports` ADD `ext` CHAR(5) NOT NULL AFTER `updated_at`, ADD `is_completed` TINYINT NOT NULL DEFAULT '0' AFTER `ext`;

UPDATE `reports` SET `file` = REPLACE(`file`, '.xls', '') where `id` > 0;
UPDATE `reports` SET `ext` = 'xls', `is_completed` = 1 where `id` > 0;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2018_10_31_123651_alter_table_reports', 1);

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
