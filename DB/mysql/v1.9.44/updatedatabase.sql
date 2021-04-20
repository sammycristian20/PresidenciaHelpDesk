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
-- Truncate bar_notification table 
-- 
TRUNCATE TABLE bar_notifications;

-- 
-- Update settings system table
-- 
UPDATE `settings_system` SET `version` = 'v1.9.44';

-- --------------------------------------------------------

-- 
-- Alter tickets table add new columns
-- 
ALTER TABLE `tickets` ADD `last_estd_duedate` TIMESTAMP NULL DEFAULT NULL AFTER `type`;


-- --------------------------------------------------------

-- 
-- Alter conditions table add new columns
-- 
ALTER TABLE `conditions` ADD `icon` VARCHAR(30) NOT NULL AFTER `updated_at`, ADD `command` VARCHAR(255) NOT NULL AFTER `icon`, ADD `job_info` VARCHAR(100) NOT NULL AFTER `command`, ADD `active` TINYINT NOT NULL DEFAULT '1' AFTER `job_info`, ADD `plugin_job` TINYINT NOT NULL DEFAULT '0' AFTER `active`, ADD `plugin_name` VARCHAR(100) NULL DEFAULT NULL AFTER `plugin_job`;

-- --------------------------------------------------------

--
-- Table structure for table `approval_levels`
--

CREATE TABLE `approval_levels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `approval_workflow_id` int(10) UNSIGNED NOT NULL,
  `match` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_level_approvers`
--

CREATE TABLE `approval_level_approvers` (
  `id` int(10) UNSIGNED NOT NULL,
  `approval_level_id` int(10) UNSIGNED NOT NULL,
  `approval_level_approver_id` int(10) UNSIGNED NOT NULL,
  `approval_level_approver_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_level_statuses`
--

CREATE TABLE `approval_level_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `approval_level_id` int(10) UNSIGNED NOT NULL,
  `approval_workflow_ticket_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `match` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflows`
--

CREATE TABLE `approval_workflows` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action_on_approve` int(11) NOT NULL,
  `action_on_deny` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflow_tickets`
--

CREATE TABLE `approval_workflow_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `approval_workflow_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action_on_approve` int(11) NOT NULL,
  `action_on_deny` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approver_statuses`
--

CREATE TABLE `approver_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `approver_id` int(10) UNSIGNED NOT NULL,
  `approver_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `approval_level_status_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `name`) VALUES
(1, 'user'),
(2, 'agent'),
(3, 'admin'),
(4, 'department_manager'),
(5, 'team_lead');


--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2018_09_05_104247_add_session_timeout_duration_to_settings_security', 1),
('2018_09_20_071459_create_approval_workflows_table', 1),
('2018_09_20_072143_create_approval_levels_table', 1),
('2018_09_20_073404_create_approval_level_approvers_table', 1),
('2018_10_09_021450_create_approval_workflow_tickets_table', 1),
('2018_10_09_021533_create_approval_level_statuses_table', 1),
('2018_10_09_021548_create_approver_statuses_table', 1),
('2018_10_09_110747_create_user_types_table', 1),
('2018_10_16_084837_add_new_columns_to_conditions_table', 1),
('2018_10_18_005529_add_comment_to_approver_statuses_table', 1),
('2018_10_19_132628_add_status_columns_to_approval_workflows_table', 1),
('2018_10_24_025346_add_status_columns_to_approval_workflow_tickets_table', 1),
('2018_10_25_113734_alter_tickets_table_add_column_last_estd_duedate', 1);

--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(34, 'ticket-approval', '2018-07-18 23:48:16', '2018-07-18 23:48:16');

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'ticket-approval', '1', 34, 'Ticket Approval Link', '<p>Hello {!! $receiver_name !!},<br /><br />Ticket <a href="{!! $ticket_link !!}">{!! $ticket_number !!}</a> has been waiting for your approval.<br /><br />Please click here to review the ticket : {!! $approval_link !!} .<br /><br />Have a nice day.<br /><br />Kind Regards,<br />{!! $system_from !!}', '', id, '2018-04-17 22:56:39', '2018-04-17 22:56:39', 'agent-templates'
FROM `template_sets`;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_levels`
--
ALTER TABLE `approval_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_levels_approval_workflow_id_foreign` (`approval_workflow_id`);

--
-- Indexes for table `approval_level_approvers`
--
ALTER TABLE `approval_level_approvers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_level_approvers_approval_level_id_foreign` (`approval_level_id`);

--
-- Indexes for table `approval_level_statuses`
--
ALTER TABLE `approval_level_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_level_statuses_approval_level_id_foreign` (`approval_level_id`),
  ADD KEY `approval_level_statuses_approval_workflow_ticket_id_foreign` (`approval_workflow_ticket_id`);

--
-- Indexes for table `approval_workflows`
--
ALTER TABLE `approval_workflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_workflows_user_id_foreign` (`user_id`);

--
-- Indexes for table `approval_workflow_tickets`
--
ALTER TABLE `approval_workflow_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_workflow_tickets_ticket_id_foreign` (`ticket_id`),
  ADD KEY `approval_workflow_tickets_approval_workflow_id_foreign` (`approval_workflow_id`);

--
-- Indexes for table `approver_statuses`
--
ALTER TABLE `approver_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approver_statuses_approval_level_status_id_foreign` (`approval_level_status_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_levels`
--
ALTER TABLE `approval_levels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `approval_level_approvers`
--
ALTER TABLE `approval_level_approvers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `approval_level_statuses`
--
ALTER TABLE `approval_level_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `approval_workflows`
--
ALTER TABLE `approval_workflows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `approval_workflow_tickets`
--
ALTER TABLE `approval_workflow_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `approver_statuses`
--
ALTER TABLE `approver_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;