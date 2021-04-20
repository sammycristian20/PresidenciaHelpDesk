-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2017 at 05:23 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings_alert_notice`
--
DROP TABLE IF EXISTS `settings_alert_notice` CASCADE;

CREATE TABLE `settings_alert_notice` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_alert_notice`
--

INSERT INTO `settings_alert_notice` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'new_ticket_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(2, 'new_ticket_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(3, 'new_ticket_alert_persons', 'admin,department_manager', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(4, 'ticket_assign_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(5, 'ticket_assign_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(6, 'ticket_assign_alert_persons', 'agent,assigned_agent_team', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(7, 'notification_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(8, 'notification_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(9, 'notification_alert_persons', 'admin,agent,department_manager,organization_manager,team_lead', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(10, 'internal_activity_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(11, 'internal_activity_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(12, 'internal_activity_alert_persons', 'agent,team_members,team_lead', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(13, 'ticket_transfer_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(14, 'ticket_transfer_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(15, 'transfer_alert_persons', 'assigned_agent_team,department_manager,organization_manager', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(16, 'registration_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(17, 'registration_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(18, 'registration_alert_persons', 'client', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(19, 'registration_notification_alert', '0', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(20, 'registration_notification_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(21, 'registration_notification_alert_persons', 'admin', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(22, 'reply_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(23, 'reply_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(24, 'reply_alert_persons', 'client', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(25, 'reply_notification_alert', '1', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(26, 'reply_notification_alert_mode', 'email,system', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(27, 'reply_notification_alert_persons', 'admin', '2017-03-19 23:12:43', '2017-03-19 23:12:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `settings_alert_notice`
--
ALTER TABLE `settings_alert_notice`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `settings_alert_notice`
--
ALTER TABLE `settings_alert_notice`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

-- --------------------------------------------------------
-- 
-- Alter `ticket_thread` table 
-- 
ALTER TABLE `ticket_thread` ADD `thread_type` VARCHAR(255) NULL DEFAULT NULL AFTER `updated_at`, ADD `response_time` VARCHAR(255) NULL DEFAULT NULL AFTER `thread_type`;

-- --------------------------------------------------------
-- 
-- Drop unnecssary tables
-- 
DROP TABLE `log_notification`, `notification_types`, `user_notification`;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--
DROP TABLE IF EXISTS `notifications` CASCADE;

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seen` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `table` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `row_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Table structure for table `system_portal`
--

CREATE TABLE `system_portal` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agent_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_button_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_button_border_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_input_fild_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `system_portal`
--

INSERT INTO `system_portal` (`id`, `admin_header_color`, `agent_header_color`, `client_header_color`, `client_button_color`, `client_button_border_color`, `client_input_fild_color`, `logo`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'skin-yellow', 'skin-blue', '0', '0', '0', '0', '0', '0', '2017-03-19 23:12:43', '2017-03-19 23:12:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `system_portal`
--
ALTER TABLE `system_portal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `system_portal`
--
ALTER TABLE `system_portal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --------------------------------------------------------

--
-- Table structure for table `sla_approach_escalate`
--

CREATE TABLE `sla_approach_escalate` (
  `id` int(10) UNSIGNED NOT NULL,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sla_violated_escalate`
--

CREATE TABLE `sla_violated_escalate` (
  `id` int(10) UNSIGNED NOT NULL,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sla_approach_escalate`
--
ALTER TABLE `sla_approach_escalate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sla_violated_escalate`
--
ALTER TABLE `sla_violated_escalate`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sla_approach_escalate`
--
ALTER TABLE `sla_approach_escalate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sla_violated_escalate`
--
ALTER TABLE `sla_violated_escalate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- -------------------------------------------------

-- 
-- Alter `users` table for language preference 
-- 
ALTER TABLE `users` ADD `user_language` VARCHAR(10) NULL DEFAULT NULL AFTER `not_accept_ticket`;

-- --------------------------------------------------------

--
-- Table structure for table `department_canned_resposne`
--

CREATE TABLE `department_canned_resposne` (
  `id` int(10) UNSIGNED NOT NULL,
  `dept_id` int(10) UNSIGNED NOT NULL,
  `canned_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department_canned_resposne`
--
ALTER TABLE `department_canned_resposne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_canned_resposne_dept_id_foreign` (`dept_id`),
  ADD KEY `department_canned_resposne_canned_id_foreign` (`canned_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department_canned_resposne`
--
ALTER TABLE `department_canned_resposne`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- -------------------------------------------------------

-- 
-- Alter `tickets` table
-- 
ALTER TABLE `tickets` ADD `first_response_time` DATETIME NULL DEFAULT NULL AFTER `updated_at`, ADD `resolution_time` VARCHAR(255) NULL DEFAULT NULL AFTER `first_response_time`, ADD `is_response_sla` INT NOT NULL AFTER `resolution_time`, ADD `is_resolution_sla` INT NOT NULL AFTER `is_response_sla`, ADD `type` INT UNSIGNED NULL DEFAULT NULL AFTER `is_resolution_sla`;

-- -------------------------------------------------

-- 
-- Alter `halts` table
-- 
ALTER TABLE `halts` ADD `halted_time` INT NULL DEFAULT NULL AFTER `updated_at`;

-- ----------------------------------------------------------

--
-- Table structure for table `settings_system`
--
DROP TABLE IF EXISTS `settings_system`;
CREATE TABLE `settings_system` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_system`
--

INSERT INTO `settings_system` (`id`, `status`, `url`, `name`, `department`, `page_size`, `log_level`, `purge_log`, `api_enable`, `api_key_mandatory`, `api_key`, `name_format`, `time_farmat`, `date_format`, `time_zone`, `date_time_format`, `day_date_time`, `content`, `version`, `created_at`, `updated_at`) VALUES
(1, 1, '', '', '1', '', '', '', 0, 0, '', '', NULL, NULL, 'Asia/Kolkata', 'd/m/Y H:i:s', '', 'en', '1.0.8.0', '2017-03-19 23:12:39', '2017-03-19 23:12:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `settings_system`
--
ALTER TABLE `settings_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time_farmat` (`time_farmat`),
  ADD KEY `date_format` (`date_format`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `settings_system`
--
ALTER TABLE `settings_system`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--
DROP TABLE IF EXISTS `groups` CASCADE;
CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_status` tinyint(1) NOT NULL,
  `can_create_ticket` tinyint(1) NOT NULL,
  `can_edit_ticket` tinyint(1) NOT NULL,
  `can_post_ticket` tinyint(1) NOT NULL,
  `can_close_ticket` tinyint(1) NOT NULL,
  `can_assign_ticket` tinyint(1) NOT NULL,
  `can_transfer_ticket` tinyint(1) NOT NULL,
  `can_delete_ticket` tinyint(1) NOT NULL,
  `can_ban_email` tinyint(1) NOT NULL,
  `can_manage_canned` tinyint(1) NOT NULL,
  `can_manage_faq` tinyint(1) NOT NULL,
  `can_view_agent_stats` tinyint(1) NOT NULL,
  `department_access` tinyint(1) NOT NULL,
  `admin_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `group_status`, `can_create_ticket`, `can_edit_ticket`, `can_post_ticket`, `can_close_ticket`, `can_assign_ticket`, `can_transfer_ticket`, `can_delete_ticket`, `can_ban_email`, `can_manage_canned`, `can_manage_faq`, `can_view_agent_stats`, `department_access`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'Full Access Agents', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, '', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(2, 'Edit Ticket Agents', 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 1, 1, '', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(3, 'Delete Ticket Agents', 1, 1, 0, 1, 1, 1, 1, 1, 0, 1, 0, 1, 1, '', '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(4, 'Ban User Agents', 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 0, 1, 1, '', '2017-03-19 23:12:43', '2017-03-19 23:12:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- -----------------------------------
-- 
-- Alter ticket_status_type
-- 
INSERT INTO `ticket_status_type` (`id`, `name`, `created_at`, `updated_at`) VALUES (NULL, 'approval', NULL, NULL);

-- 
-- Aletr ticket_status
-- 
UPDATE `ticket_status` SET `name` = 'Request for close', `purpose_of_status` = '5' WHERE `ticket_status`.`id` = 7;

-- ------------------------------------------
--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--
DROP TABLE IF EXISTS `templates` CASCADE;
CREATE TABLE `templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `variable` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `set_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`) VALUES
(1, 'This template is for sending notice to agent when ticket is assigned to them', '0', 1, '', '<div>Hello {!!$ticket_agent_name!!},<br /><br /><b>Ticket No:</b> {!!$ticket_number!!}<br />Has been assigned to you by {!!$ticket_assigner!!} <br /><br />Thank You<br />Kind Regards,<br /> {!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(2, 'This template is for sending notice to client with ticket link to check ticket without logging in to system', '1', 2, 'Check your Ticket', '<div>Hello {!!$user!!},<br /><br />Click the link below to view your requested ticket<br /> {!!$ticket_link_with_number!!}<br /><br />Kind Regards,<br /> {!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(3, 'This template is for sending notice to client when ticket status is changed to close', '0', 3, '', '<div>Hello,<br /><br />This message is regarding your ticket ID {!!$ticket_number!!}. We are changing the status of this ticket to "Closed" as the issue appears to be resolved.<br /><br />Thank you<br />Kind regards,<br /> {!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(4, 'This template is for sending notice to client on successful ticket creation', '0', 4, '', '<div><span>Hello {!!$user!!}<br /><br /></span><span>Thank you for contacting us. This is an automated response confirming the receipt of your ticket. Our team will get back to you as soon as possible. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br /></span><span><b>Ticket ID:</b> {!!$ticket_number!!} <br /><br /></span><span> {!!$department_sign!!}<br /></span>You can check the status of or update this ticket online at: {!!$system_link!!}</div>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(5, 'This template is for sending notice to agent on new ticket creation', '0', 5, '', '<div>Hello {!!$ticket_agent_name!!},<br /><br />New ticket {!!$ticket_number!!}created <br /><br /><b>From</b><br /><b>Name:</b> {!!$ticket_client_name!!}   <br /><b>E-mail:</b> {!!$ticket_client_email!!}<br /><br /> {!!$content!!}<br /><br />Kind Regards,<br /> {!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(6, 'This template is for sending notice to client on new ticket created by agent in name of client', '0', 6, '', '<div> {!!$content!!}<br /><br /> {!!$agent_sign!!}<br /><br />You can check the status of or update this ticket online at: {!!$system_link!!}</div>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(7, 'This template is for sending notice to client on new registration during new ticket creation for un registered clients', '1', 7, 'Registration Confirmation', '<p>Hello {!!$user!!}, </p><p>This email is confirmation that you are now registered at our helpdesk.</p><p><b>Registered Email:</b> {!!$email_address!!}</p><p><b>Password:</b> {!!$user_password!!}</p><p>You can visit the helpdesk to browse articles and contact us at any time: {!!$system_link!!}</p><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:42', '2017-03-19 23:12:42'),
(8, 'This template is for sending notice to any user about reset password option', '1', 8, 'Reset your Password', 'Hello {!!$user!!},<br /><br />You asked to reset your password. To do so, please click this link:<br /><br /> {!!$password_reset_link!!}<br /><br />This will let you change your password to something new. If you didn\'t ask for this, don\'t worry, we\'ll keep your password safe.<br /><br />Thank You.<br /><br />Kind Regards,<br /> {!!$system_from!!}', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(9, 'This template is for sending notice to client when a reply made to his/her ticket', '0', 9, '', '<span></span><div><span></span><p> {!!$content!!}<br /></p><p> {!!$agent_sign!!} </p><p><b>Ticket Details</b></p><p><b>Ticket ID:</b> {!!$ticket_number!!}</p></div>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(10, 'This template is for sending notice to agent when ticket reply is made by client on a ticket', '0', 10, '', '<div>Hello {!!$ticket_agent_name!!},<br /><b><br /></b>A reply been made to ticket {!!$ticket_number!!}<br /><b><br /></b><b>From<br /></b><b>Name: </b>{!!$ticket_client_name!!}<br /><b>E-mail: </b>{!!$ticket_client_email!!}<br /><b><br /></b> {!!$content!!}<br /><b><br /></b>Kind Regards,<br /> {!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(11, 'This template is for sending notice to client about registration confirmation link', '1', 11, 'Verify your email address', '<p>Hello {!!$user!!}, </p><p>This email is confirmation that you are now registered at our helpdesk.</p><p><b>Registered Email:</b> {!!$email_address!!}</p><p>Please click on the below link to activate your account and Login to the system {!!$password_reset_link!!}</p><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(12, 'This template is for sending escalate notice to agents', '1', 12, 'Response Time SLA Violate', 'Hello {!!$user!!},<br /><p>There has been no response for a ticket. The first response was due by  {!!$duedate!!}.</p><br /><p><strong>Ticket Details: </strong></p><p><strong>Subject: </strong>{!!$title!!} </p><p><strong>Number: </strong>{!!$ticket_number!!} </p><p><strong>Requester: </strong>{!!$requester!!} </p><br /><p>This is an automatic escalation email from {!!$system_from!!}  </p><br /><p>Respond to ticket here {!!$ticket_link_with_number!!}  </p><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(13, 'This template is for sending escalate notice to agents', '1', 13, 'Resolve Time SLA Violate', 'Hello {!!$user!!},<br /><p>Ticket has not been resolved within the SLA time period. The ticket was due by {!!$duedate!!}.</p><br /><p><strong>Ticket Details: </strong></p><p><strong>Subject: </strong>{!!$title!!} </p><p><strong>Number: </strong>{!!$ticket_number!!} </p><p><strong>Requester: </strong>{!!$requester!!} </p><br /><p>This is an automatic escalation email from {!!$system_from!!}  </p><br /><p>Respond to ticket here {!!$ticket_link_with_number!!}  </p><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(14, 'This template is for sending notice to team when ticket is assigned to team', '1', 14, '', '<div>\r\n             Hello {!!$ticket_agent_name!!}<br /><br /><b>Ticket No:</b> {!!$ticket_number!!}<br />Has been assigned to your team  by {!!$ticket_assigner!!} <br />Please check and resppond on the ticket.<br />Link: {!!$ticket_link!!}<br /><br />Thank You<br />Kind Regards,<br />{!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(15, 'This template is for sending notice to client when password is changed', '1', 15, 'Password changed', 'Hello {!!$user!!},<br /><br />Your password is successfully changed.Your new password is : {!!$user_password!!}<br /><br />Thank You.<br /><br />Kind Regards,<br /> {!!$system_from!!}', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(16, 'This template is for sending notice when ticket elements is changed', '1', 16, '', '<div>Hello {!!$ticket_agent_name!!},<br /><br />This message is regarding your ticket ID {!!$ticket_number!!}.<br />{!!$internal_content!!}.<br />By {!!$by!!}<br /><br />Thank you<br />Kind regards,<br /> {!!$system_from!!}</div>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(17, 'This template is for sending response escalate notice for approach conditions', '1', 17, 'Response Time SLA Approaching', 'Hello {!!$user!!},<br /><p>There has been no response for a ticket. The first response should happen on or before {!!$duedate!!}.</p><br /><p><strong>Ticket Details: </strong></p><p><strong>Subject: </strong>{!!$title!!} </p><p><strong>Number: </strong>{!!$ticket_number!!} </p><p><strong>Requester: </strong>{!!$requester!!} </p><br /><p>This is an automatic escalation email from {!!$system_from!!}  </p><br /><p>Respond to ticket here {!!$ticket_link_with_number!!}  </p><br /><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(18, 'This template is for sending resolution escalate notice for approach conditions', '1', 18, 'Resolution Time SLA Approaching', 'Hello {!!$user!!},<br /><br /><p>Ticket has not been resolved within the SLA time period. The ticket has to resolve on or before {!!$duedate!!}.</p><br /><p><strong>Ticket Details: </strong></p><p><strong>Subject: </strong>{!!$title!!} </p><p><strong>Number: </strong>{!!$ticket_number!!} </p><p><strong>Requester: </strong>{!!$requester!!} </p><br /><p>This is an automatic escalation email from {!!$system_from!!}  </p><br /><p>Respond to ticket here {!!$ticket_link_with_number!!}  </p><br /><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43'),
(19, 'This template is for sending notification for a new user entry', '1', 19, 'New user has created', '<p>Hello {!!$user!!}, </p><p>This email is notifying you that a new user has entred in our helpdesk.</p><p><b>Registered User Name:</b> {!!$email_address!!}</p><p>Please click on the below link to see the profile {!!$user_profile_link!!}</p><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', 1, '2017-03-19 23:12:43', '2017-03-19 23:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `template_sets`
--
DROP TABLE IF EXISTS `template_sets` CASCADE;
CREATE TABLE `template_sets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `template_sets`
--

INSERT INTO `template_sets` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'default', 1, '2017-03-19 23:12:41', '2017-03-19 23:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `template_types`
--
DROP TABLE IF EXISTS `template_types` CASCADE;
CREATE TABLE `template_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'assign-ticket', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(2, 'check-ticket', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(3, 'close-ticket', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(4, 'create-ticket', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(5, 'create-ticket-agent', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(6, 'create-ticket-by-agent', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(7, 'registration-notification', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(8, 'reset-password', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(9, 'ticket-reply', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(10, 'ticket-reply-agent', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(11, 'registration', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(12, 'response_due_violate', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(13, 'resolve_due_violate', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(14, 'team_assign_ticket', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(15, 'reset_new_password', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(16, 'internal_change', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(17, 'response_due_approach', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(18, 'resolve_due_approach', '2017-03-19 23:12:41', '2017-03-19 23:12:41'),
(19, 'new-user', '2017-03-19 23:12:41', '2017-03-19 23:12:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_sets`
--
ALTER TABLE `template_sets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_types`
--
ALTER TABLE `template_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `template_sets`
--
ALTER TABLE `template_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `template_types`
--
ALTER TABLE `template_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

-- --------------------------------------------------------

--
-- Table structure for table `business_schedule`
--
DROP TABLE IF EXISTS `business_schedule` CASCADE;

CREATE TABLE `business_schedule` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_hours_id` int(10) UNSIGNED NOT NULL,
  `days` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `business_schedule`
--

INSERT INTO `business_schedule` (`id`, `business_hours_id`, `days`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sunday', 'Closed', '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(2, 1, 'Monday', 'Open_fixed', '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(3, 1, 'Tuesday', 'Open_fixed', '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(4, 1, 'Wednusday', 'Open_fixed', '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(5, 1, 'Thursday', 'Open_fixed', '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(6, 1, 'Friday', 'Open_fixed', '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(7, 1, 'Saturday', 'Closed', '2017-03-19 23:12:39', '2017-03-19 23:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `sla_plan`
--
DROP TABLE IF EXISTS `sla_plan` CASCADE;

CREATE TABLE `sla_plan` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sla_plan`
--

INSERT INTO `sla_plan` (`id`, `name`, `grace_period`, `admin_note`, `status`, `sla_target`, `apply_sla_depertment`, `apply_sla_company`, `apply_sla_tickettype`, `apply_sla_ticketsource`, `transient`, `ticket_overdue`, `created_at`, `updated_at`) VALUES
(1, 'Low', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 1, '', '', '', '', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(2, 'Normal', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 2, '', '', '', '', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(3, 'High', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 3, '', '', '', '', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(4, 'Emergency', '', 'This is the default sla. Please do not delete this untill create a new one', 1, 4, '', '', '', '', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `sla_targets`
--
DROP TABLE IF EXISTS `sla_targets` CASCADE;

CREATE TABLE `sla_targets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sla_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `respond_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolve_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hour_id` int(11) NOT NULL,
  `send_email` int(11) NOT NULL,
  `send_sms` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sla_targets`
--

INSERT INTO `sla_targets` (`id`, `name`, `sla_id`, `priority_id`, `respond_within`, `resolve_within`, `business_hour_id`, `send_email`, `send_sms`, `created_at`, `updated_at`) VALUES
(1, 'Low', 1, 1, '5-hrs', '10-hrs', 1, 1, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(2, 'Normal', 2, 2, '4-hrs', '9-hrs', 1, 1, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(3, 'High', 3, 3, '2-hrs', '4-hrs', 1, 1, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(4, 'Emergency', 4, 4, '1-hrs', '2-hrs', 1, 1, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_schedule`
--
ALTER TABLE `business_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_schedule_business_hours_id_foreign` (`business_hours_id`);

--
-- Indexes for table `sla_plan`
--
ALTER TABLE `sla_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sla_targets`
--
ALTER TABLE `sla_targets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_schedule`
--
ALTER TABLE `business_schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `sla_plan`
--
ALTER TABLE `sla_plan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `sla_targets`
--
ALTER TABLE `sla_targets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


-- -----------------------------------
--
-- Dumping data for table `ticket_type`
--

INSERT INTO `ticket_type` (`id`, `name`, `status`, `type_desc`, `ispublic`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Default', 1, 'Default', 0, 1, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(2, 'Question', 0, 'Question', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(3, 'Incident', 0, 'Incident', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(4, 'Problem', 0, 'Problem', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39'),
(5, 'Feature Request', 0, 'Feature Request', 0, 0, '2017-03-19 23:12:39', '2017-03-19 23:12:39');

-- -----------------------------------------------

-- 
-- Alter user_assign_organization 
-- 
ALTER TABLE `user_assign_organization` ADD `role` VARCHAR(255) NOT NULL DEFAULT 'members' AFTER `updated_at`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
