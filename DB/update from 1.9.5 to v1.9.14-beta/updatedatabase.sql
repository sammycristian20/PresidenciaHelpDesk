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
-- Alter table organizations
-- 
ALTER TABLE `organization` 
ADD `client_Code` VARCHAR(255) NOT NULL AFTER `updated_at`, 
ADD `phone1` VARCHAR(255) NOT NULL AFTER `client_Code`, 
ADD `line_of_business` VARCHAR(255) NOT NULL AFTER `phone1`, 
ADD `relation_type` VARCHAR(255) NOT NULL AFTER `line_of_business`, 
ADD `branch` VARCHAR(255) NOT NULL AFTER `relation_type`, 
ADD `fax` VARCHAR(255) NOT NULL AFTER `branch`;

-- --------------------------------------------------------

--
-- Table structure for table `no_assign_escalate`
--
DROP TABLE  IF EXISTS `no_assign_escalate`; 
CREATE TABLE `no_assign_escalate` (
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
-- Indexes for table `no_assign_escalate`
--
ALTER TABLE `no_assign_escalate`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `no_assign_escalate`
--
ALTER TABLE `no_assign_escalate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- 
-- Drop various tables
--
DROP TABLE IF EXISTS `field_values`;
DROP TABLE IF EXISTS `custom_form_fields`;
DROP TABLE IF EXISTS `form_details`;
DROP TABLE IF EXISTS `form_name`;
DROP TABLE IF EXISTS `form_value`;
DROP TABLE IF EXISTS `custom_forms`;
DROP TABLE IF EXISTS `groups`;

-- -----------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` int(10) UNSIGNED NOT NULL,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `json` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `form`, `json`, `created_at`, `updated_at`) VALUES
(1, 'ticket', '[{"title":"Requester","agentlabel":[{"language":"en","label":"Requester","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Requester","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"email","agentCCfield":true,"customerCCfield":false,"customerDisplay":true,"agentRequiredFormSubmit":true,"customerRequiredFormSubmit":true,"default":"yes","value":"","unique":"requester"},{"title":"Subject","agentlabel":[{"language":"en","label":"Subject","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Subject","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"text","agentRequiredFormSubmit":true,"customerDisplay":true,"customerRequiredFormSubmit":true,"default":"yes","value":"","unique":"subject"},{"title":"Type","agentlabel":[{"language":"en","label":"Type","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Type","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"select","agentRequiredFormSubmit":false,"customerDisplay":false,"customerRequiredFormSubmit":false,"value":"","api":"type","options":[],"default":"yes","unique":"type"},{"title":"Status","agentlabel":[{"language":"en","label":"Status","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Status","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"select","agentRequiredFormSubmit":true,"customerDisplay":false,"customerRequiredFormSubmit":false,"value":"","api":"status","options":[],"default":"yes","unique":"status"},{"title":"Priority","agentlabel":[{"language":"en","label":"Priority","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Priority","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"select","agentRequiredFormSubmit":true,"customerDisplay":true,"customerRequiredFormSubmit":true,"value":"","api":"priority","options":[],"default":"yes","unique":"priority"},{"title":"Help Topic","agentlabel":[{"language":"en","label":"Help Topic","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Help Topic","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"select","agentRequiredFormSubmit":true,"customerDisplay":true,"customerRequiredFormSubmit":true,"value":"","api":"helptopic","options":[],"default":"yes","unique":"help_topic"},{"title":"Assigned","agentlabel":[{"language":"en","label":"Assigned","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Assigned","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"select","agentRequiredFormSubmit":true,"customerDisplay":false,"customerRequiredFormSubmit":false,"value":"","api":"assigned_to","options":[],"default":"yes","unique":"assigned"},{"title":"Description","agentlabel":[{"language":"en","label":"Description","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Description","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"textarea","agentRequiredFormSubmit":true,"customerDisplay":true,"customerRequiredFormSubmit":false,"default":"yes","value":"","unique":"description"},{"title":"Company","agentlabel":[{"language":"en","label":"Company","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"clientlabel":[{"language":"en","label":"Company","flag":"https:\/\/www.ladybirdweb.com\/support\/lb-faveo\/flags\/en.png"}],"type":"select","agentRequiredFormSubmit":false,"customerDisplay":false,"customerRequiredFormSubmit":false,"default":"yes","value":"","api":"company","options":[],"unique":"company"}]', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `required_fields`
--

CREATE TABLE `required_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_agent_required` int(11) NOT NULL,
  `is_client_required` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `required_fields`
--
ALTER TABLE `required_fields`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `required_fields`
--
ALTER TABLE `required_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Dumping data for table `ticket_status_type`
--

INSERT INTO `ticket_status_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(6, 'spam', '2017-08-26 06:45:21', '2017-08-26 06:45:21');

-- 
-- Alter ticket_status table
-- 
ALTER TABLE `ticket_status` CHANGE `send_email` `send_email` VARCHAR(255) NULL DEFAULT NULL;

-- -------------------------------------------------

-- 
-- Alter ticket status table
-- 
UPDATE `ticket_status` SET `send_email` = '{"client":"1","admin":"0","assigned_agent_team":"1"}' where `id` = 1;
UPDATE `ticket_status` SET `send_email` = '{"client":"1","admin":"0","assigned_agent_team":"1"}' where `id` = 2;
UPDATE `ticket_status` SET `send_email` = '{"client":"1","admin":"0","assigned_agent_team":"1"}' where `id` = 3;
UPDATE `ticket_status` SET `send_email` = '{"client":"0","admin":"0","assigned_agent_team":"0"}' where `id` = 4;
UPDATE `ticket_status` SET `send_email` = '{"client":"0","admin":"0","assigned_agent_team":"0"}' where `id` = 5;
UPDATE `ticket_status` SET `name` = 'Spam', `message` = 'Ticket have been marked as Spam by {!!$user!!}', `visibility_for_client` = '0', `allow_client` = '0', `purpose_of_status` = '6', `secondary_status` = NULL, `send_email` = '{"client":"0","admin":"0","assigned_agent_team":"0"}' where `id` = 6;
UPDATE `ticket_status` SET `send_email` = '{"client":"0","admin":"0","assigned_agent_team":"0"}' where `id` = 7;

--
-- Table structure for table `email_threads`
--

CREATE TABLE `email_threads` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `message_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(11) NOT NULL,
  `reference_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_threads`
--
ALTER TABLE `email_threads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_threads`
--
ALTER TABLE `email_threads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

-- --------------------------------------------------------

--
-- Table structure for table `permision`
--

CREATE TABLE `permision` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `permision` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ---------------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permision`
--
ALTER TABLE `permision`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permision`
--
ALTER TABLE `permision`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- ----------------------------------------------------------------

-- 
-- Dumping data into permission tables
-- 
INSERT INTO `permision`(`user_id`, `permision`, `created_at`, `updated_at`)
SELECT `id`, '{"create_ticket":"1","edit_ticket":"1","transfer_ticket":"1","delete_ticket":"1","assign_ticket":"1","access_kb":"1","ban_email":"1"}', CURDATE(), CURDATE() FROM `users` where `role` != 'user';


-- ------------------------------------------------------------
--
-- Dumping data for table `common_settings`
--

INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('user_registration', '', '0', '', '2017-06-20 18:40:03', '2017-06-20 18:40:03'),
('user_show_org_ticket', '', '0', '', '2017-06-20 18:40:03', '2017-06-27 01:16:20'),
('user_reply_org_ticket', '', '0', '', '2017-06-20 18:40:03', '2017-06-20 18:40:03'),
('allow_users_to_create_ticket', '', '0', '', '2017-06-20 18:40:03', '2017-06-20 18:40:03'),
('login_restrictions', 'email', '', '', '2017-07-07 01:24:04', '2017-07-07 01:24:04');

-- ------------------------------------------------------------

--
-- Alter ticket_form_data table
--  
ALTER TABLE `ticket_form_data` ADD `key` VARCHAR(255) NOT NULL AFTER `updated_at`;


-- --------------------------------------------------------

--
-- Table structure for table `templates`
--
DROP TABLE IF EXISTS `templates`;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(32, 'template-ticket-reply-to-agents-by-agent', '0', 9, '', 'Hello {!! $receiver_name !!},<br /><br />An agent has replied to ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br /><strong>Reply content</strong><br />{!! $message_content !!}<br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br />{!! $agent_signature !!}<br />Thanks.<br />{!! $system_from !!}', '', 1, '2017-08-14 07:58:33', '2017-08-14 07:58:33', 'agent-templates');

-- --------------------------------------------------------

--
-- Table structure for table `template_sets`
--
DROP TABLE IF EXISTS `template_sets`;
CREATE TABLE `template_sets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_language` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'en'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(21, 'status-update', '2017-07-06 11:40:03', '2017-07-06 11:40:03');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `template_sets`
--
ALTER TABLE `template_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `template_types`
--
ALTER TABLE `template_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

-- -------------------------------------------------------

-- 
-- Alter users table
-- 
ALTER TABLE `users` ADD `mobile_verify` VARCHAR(255) NOT NULL DEFAULT '1' AFTER `user_language`, ADD `email_verify` VARCHAR(255) NOT NULL DEFAULT '1' AFTER `mobile_verify`;

-- --------------------------------------------------------

--
-- Table structure for table `settings_alert_notice`
--
DROP TABLE IF EXISTS `settings_alert_notice`;
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
(30, 'new_ticket_confirmation_alert_persons', 'client', '2017-07-06 11:40:08', '2017-07-06 11:40:08');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
