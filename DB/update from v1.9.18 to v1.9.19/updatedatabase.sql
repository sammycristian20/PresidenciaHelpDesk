-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2017 at 04:50 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `master_pro`
--

-- --------------------------------------------------------

--
-- Dumping data for table `template_types`
--
INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(24, 'task-reminder', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(25, 'task-create', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(26, 'task-update', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(27, 'task-status-update', '2017-11-21 01:11:47', '2017-11-21 01:11:47'),
(28, 'task-assigned', '2017-11-21 01:11:47', '2017-11-21 01:11:47');

-- ---------------------------------------------------------

--
-- Dumping data for table `templates`
--
INSERT INTO `templates`(`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  'template-task-reminder', 
  '1', 
  24, 
  'Task Alert', 
  '<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} is due on {!! $task_end_date !!}, </p><p> </p><p>Regards, </p><p> </p>',
  '', 
  `id`,
  NULL, 
  NULL, 
  'common-tmeplates'
FROM
  `template_sets`;

INSERT INTO `templates`(`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  'template-task-created', 
  '1', 
  25, 
  'Task created', 
  '<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} has been created,  is due on {!! $task_end_date !!}, </p><p> </p><p>Regards, </p><p> </p>', 
  '', 
  `id`, 
  NULL, 
  NULL, 
  'common-tmeplates'
FROM
  `template_sets`;

INSERT INTO `templates`(`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  'template-task-update', 
  '1', 
  26, 
  'Task Update', 
  '<p>Hello!<br /><br />Your task {!! $task_name !!} has been updated by {!! $updated_by !!}.</p><p>Regards, </p><p> </p>', 
  '', 
  `id`, 
  NULL, 
  NULL,
  'common-tmeplates'
FROM
  `template_sets`;

INSERT INTO `templates`(`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  'template-task-status', 
  '1', 
  27, 
  'Task status update', 
  '<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} status has been {!! $status !!}.</p><p> </p><p>Regards, </p><p> </p>', 
  '', 
  `id`, 
  NULL, 
  NULL,
  'common-tmeplates'
FROM
  `template_sets`;

INSERT INTO `templates`(`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  'template-task-assigned', 
  '1', 
  28, 
  'Task Assigned', 
  '<p>Hello {!! $receiver_name !!},<br /><br />Your have been assigned to task {!! $task_name !!} by {!! $created_by !!}, is due on {!! $task_end_date !!}, </p><p> </p><p>Regards, </p><p> </p>', 
  '', 
  `id`, 
  NULL, 
  NULL,
  'common-tmeplates'
FROM
  `template_sets`;

  --
-- Dumping data for table `settings_alert_notice`
--

INSERT INTO `settings_alert_notice` (`key`, `value`, `created_at`, `updated_at`) VALUES
('browser_notification_status', '0', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
('api_id', '', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
('rest_api_key', '', '2017-11-23 08:34:34', '2017-11-23 08:34:34'),
('browser_notification_alert_persons', 'agent,admin, client', '2017-11-23 08:34:34', '2017-11-23 08:34:34');

-- 
-- UPDATE system version
-- 
UPDATE `settings_system` SET `version` = 'v1.9.19';