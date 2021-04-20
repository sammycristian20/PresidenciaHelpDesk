-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
-- 
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2017 at 07:35 AM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- 
-- Database: `working`
-- 

-- --------------------------------------------------------


-- 
-- Update ticket tables 
-- 
UPDATE `tickets`
SET `sla` = 1;

-- 
-- Table structure for table `business_holidays`
-- 

CREATE TABLE `business_holidays` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hours_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `business_hours`
-- 

CREATE TABLE `business_hours` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `timezone` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `business_hours`
-- 

INSERT INTO `business_hours` (`id`, `name`, `description`, `status`, `timezone`, `created_at`, `updated_at`) VALUES
(1, 'Default Business-Hours', 'default', 1, 0, '2017-01-13 01:32:34', '2017-01-13 01:32:34');

-- --------------------------------------------------------

-- 
-- Table structure for table `business_open_custom_time`
-- 

CREATE TABLE `business_open_custom_time` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_schedule_id` int(10) UNSIGNED NOT NULL,
  `open_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `close_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `business_schedule`
-- 

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
(1, 1, 'Sunday', 'Closed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(2, 1, 'Monday', 'Open_fixed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(3, 1, 'Tuesday', 'Open_fixed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(4, 1, 'Wednusday', 'Open_fixed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(5, 1, 'Thursday', 'Open_fixed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(6, 1, 'Friday', 'Open_fixed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(7, 1, 'Saturday', 'Closed', '2017-01-13 01:32:34', '2017-01-13 01:32:34');

-- --------------------------------------------------------

-- 
-- Table structure for table `sla_targets`
-- 

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
(1, 'default', 1, 1, '1-hrs', '5-hrs', 1, 1, 0, '2017-01-13 01:32:34', '2017-01-13 01:32:34');

-- --------------------------------------------------------

-- 
-- Table structure for table `sla_violated`
-- 

CREATE TABLE `sla_violated` (
  `id` int(10) UNSIGNED NOT NULL,
  `sla_plan` int(11) NOT NULL,
  `response_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `response_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Database: `working`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sla_approaches`
-- 

CREATE TABLE `sla_approaches` (
  `id` int(10) UNSIGNED NOT NULL,
  `sla_plan` int(11) NOT NULL,
  `response_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `response_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution_escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
-- 
-- Table structure for table `ticket_status_type`
-- 

CREATE TABLE `ticket_status_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `ticket_status_type`
-- 

INSERT INTO `ticket_status_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'open', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(2, 'closed', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(3, 'archieved', '2017-01-13 01:32:34', '2017-01-13 01:32:34'),
(4, 'deleted', '2017-01-13 01:32:34', '2017-01-13 01:32:34');

-- --------------------------------------------------------

-- 
-- Table structure for table `halts`
-- 

CREATE TABLE `halts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `halted_at` timestamp NOT NULL,
  `time_used` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `ticket_type`
-- 

CREATE TABLE `ticket_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `sla` int(11) NOT NULL,
  `type_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ispublic` int(11) NOT NULL,
  `is_default` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Update templates
-- 
INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(15, 'response-due', '2017-01-13 01:32:37', '2017-01-13 01:32:37'),
(16, 'resolve-due', '2017-01-13 01:32:37', '2017-01-13 01:32:37');

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`)
SELECT 'This template is for sending escalate notice to agents', '1', 15, 'Response Time SLA Violate.', '<p>Hello {!!$user!!},<br /><br /></p><p>There has been no response for a ticket. The first response was due by  {!!$duedate!!}.</p><br /><br /><p><strong>Ticket Details: </strong></p><br /><p><strong>Subject: </strong>{!!$title!!} </p><br /><p><strong>Number: </strong>{!!$ticket_number!!} </p><br /><p><strong>Requester: </strong>{!!$requester!!} </p><br /><br /><p>This is an automatic escalation email from {!!$system_from!!}  </p><br /><br /><p>Respond to ticket here {!!$ticket_link_with_number!!}  </p><br /><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', id, '2017-01-02 05:50:12', '2017-01-02 06:01:50'
FROM `template_sets`;

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`)
SELECT 'This template is for sending escalate notice to agents', '1', 16, 'Resolve Time SLA Violate.', '<p>Hello {!!$user!!},<br /><br /></p><p>Ticket has not been resolved within the SLA time period. The ticket was due by {!!$duedate!!}.</p><br /><br /><p><strong>Ticket Details: </strong></p><br /><p><strong>Subject: </strong>{!!$title!!} </p><br /><p><strong>Number: </strong>{!!$ticket_number!!} </p><br /><p><strong>Requester: </strong>{!!$requester!!} </p><br /><br /><p>This is an automatic escalation email from {!!$system_from!!}  </p><br /><br /><p>Respond to ticket here {!!$ticket_link_with_number!!}  </p><br /><p>Thank You.</p><p>Kind Regards,</p><p> {!!$system_from!!} </p>', '', id, '2017-01-02 05:50:12', '2017-01-02 06:01:50'
FROM `template_sets`;




-- 
-- Indexes for table `halts`
-- 
ALTER TABLE `halts`
  ADD PRIMARY KEY (`id`);

-- 
-- Indexes for table `ticket_type`
-- 
ALTER TABLE `ticket_type`
  ADD PRIMARY KEY (`id`);

-- 
-- AUTO_INCREMENT for dumped tables
-- 

-- 
-- AUTO_INCREMENT for table `halts`
-- 
ALTER TABLE `halts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
-- 
-- AUTO_INCREMENT for table `ticket_type`
-- 
ALTER TABLE `ticket_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- 
-- Indexes for table `sla_approaches`
-- 
ALTER TABLE `sla_approaches`
  ADD PRIMARY KEY (`id`);


-- 
-- Indexes for table `ticket_status_type`
-- 
ALTER TABLE `ticket_status_type`
  ADD PRIMARY KEY (`id`);

-- 
-- AUTO_INCREMENT for dumped tables
-- 

-- 
-- AUTO_INCREMENT for table `sla_approaches`
-- 
ALTER TABLE `sla_approaches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
-- 
-- AUTO_INCREMENT for table `ticket_status_type`
-- 
ALTER TABLE `ticket_status_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- 
-- Indexes for table `business_holidays`
-- 
ALTER TABLE `business_holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_holidays_business_hours_id_foreign` (`business_hours_id`);

-- 
-- Indexes for table `business_hours`
-- 
ALTER TABLE `business_hours`
  ADD PRIMARY KEY (`id`);

-- 
-- Indexes for table `business_open_custom_time`
-- 
ALTER TABLE `business_open_custom_time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_open_custom_time_business_schedule_id_foreign` (`business_schedule_id`);

-- 
-- Indexes for table `business_schedule`
-- 
ALTER TABLE `business_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_schedule_business_hours_id_foreign` (`business_hours_id`);

-- 
-- Indexes for table `sla_targets`
-- 
ALTER TABLE `sla_targets`
  ADD PRIMARY KEY (`id`);

-- 
-- Indexes for table `sla_violated`
-- 
ALTER TABLE `sla_violated`
  ADD PRIMARY KEY (`id`);

-- 
-- AUTO_INCREMENT for dumped tables
-- 

-- 
-- AUTO_INCREMENT for table `business_holidays`
-- 
ALTER TABLE `business_holidays`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
-- 
-- AUTO_INCREMENT for table `business_hours`
-- 
ALTER TABLE `business_hours`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
-- 
-- AUTO_INCREMENT for table `business_open_custom_time`
-- 
ALTER TABLE `business_open_custom_time`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
-- 
-- AUTO_INCREMENT for table `business_schedule`
-- 
ALTER TABLE `business_schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
-- 
-- AUTO_INCREMENT for table `sla_targets`
-- 
ALTER TABLE `sla_targets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
-- 
-- AUTO_INCREMENT for table `sla_violated`
-- 
ALTER TABLE `sla_violated`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- -----------------------------------------------------
-- 
-- Table structure for table `department_assign_agents`
-- 

CREATE TABLE `department_assign_agents` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Indexes for table `department_assign_agents`
-- 
ALTER TABLE `department_assign_agents`
  ADD PRIMARY KEY (`id`);
  
-- 
-- AUTO_INCREMENT for table `department_assign_agents`
-- 
ALTER TABLE `department_assign_agents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- 
-- Dumping data for table `department_assign_agents`
-- 

INSERT INTO `department_assign_agents`(`department_id`, `agent_id`, `created_at`, `updated_at`) SELECT `primary_dpt`, `id`, '2017-01-02 05:50:12', '2017-01-02 06:01:50' FROM `users` WHERE `role` <> 'user';

-- -------------------------------------------------------------

-- 
-- Alter sla_plan tables
-- 
ALTER TABLE `sla_plan` ADD `sla_target` INT NOT NULL DEFAULT '1' AFTER `updated_at`, ADD `apply_sla_depertment` VARCHAR(255) NOT NULL AFTER `sla_target`, ADD `apply_sla_company` VARCHAR(255) NOT NULL AFTER `apply_sla_depertment`, ADD `apply_sla_tickettype` VARCHAR(255) NOT NULL AFTER `apply_sla_company`, ADD `apply_sla_ticketsource` VARCHAR(255) NOT NULL AFTER `apply_sla_tickettype`;

-- ------------------------------------------------------------

--
-- Alter ticket_status table remove some columns
--
ALTER TABLE `ticket_status`
  DROP `state`,
  DROP `mode`,
  DROP `flags`,
  DROP `sort`,
  DROP `email_user`,
  DROP `icon_class`,
  DROP `properties`;

ALTER TABLE `ticket_status` ADD `visibility_for_client` TINYINT NOT NULL AFTER `updated_at`, ADD `allow_client` TINYINT NOT NULL AFTER `visibility_for_client`, ADD `visibility_for_agent` TINYINT NOT NULL AFTER `allow_client`, ADD `purpose_of_status` INT NOT NULL AFTER `visibility_for_agent`, ADD `secondary_status` INT NULL AFTER `purpose_of_status`, ADD `send_email` INT NOT NULL AFTER `secondary_status`, ADD `halt_sla` INT NOT NULL AFTER `send_email`, ADD `order` INT NOT NULL AFTER `halt_sla`, ADD `icon` VARCHAR(255) NOT NULL AFTER `order`, ADD `icon_color` VARCHAR(255) NOT NULL AFTER `icon`, ADD `default` INT NULL AFTER `icon_color`;

UPDATE `ticket_status`
SET visibility_for_client = 1,
allow_client = 1,
visibility_for_agent = 1
WHERE 1;

UPDATE `ticket_status` SET `purpose_of_status` = 1, `order` = 1, `icon` = 'fa fa-folder-open-o', `icon_color` = '#ff0000', `default` = 1 WHERE `id` = 1;
UPDATE `ticket_status` SET `purpose_of_status` = 2, `order` = 2, `icon` = 'fa fa-check-circle-o', `icon_color` = '#008000' WHERE `id` = 2;
UPDATE `ticket_status` SET `purpose_of_status` = 2, `order` = 3, `icon` = 'fa fa-check', `icon_color` = '#008000', `send_email` = 1,`default` = 1 WHERE `id` = 3;
UPDATE `ticket_status` SET `purpose_of_status` = 3, `order` = 4, `icon` = 'fa fa-trash', `icon_color` = '#ff0000' WHERE `id` = 4;
UPDATE `ticket_status` SET `purpose_of_status` = 4, `order` = 5, `icon` = 'fa fa-trash', `icon_color` = '#ff0000', `default` = 1 WHERE `id` = 5;
UPDATE `ticket_status` SET `purpose_of_status` = 1, `order` = 6, `icon` = 'fa fa-bell', `icon_color` = '#f1ac0b', `default` = 0 WHERE `id` = 6;
UPDATE `ticket_status` SET `purpose_of_status` = 1, `order` = 7, `icon` = 'fa fa-bell', `icon_color` = '#f1ac0b', `default` = 0 WHERE `id` = 7;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
