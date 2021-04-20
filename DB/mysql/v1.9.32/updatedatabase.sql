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

ALTER TABLE `department` ADD `en_auto_assign` BOOLEAN NOT NULL DEFAULT FALSE AFTER `nodes`;


--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'template-ticket-creation-acknowledgement-org-mngr-by-client', '0', 4, '', '<p>Hello {!! $receiver_name !!},<br/><br/>A member of your organization has created a new ticket in our helpdesk system.</p><p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/><strong>Member&#39;s Details</strong><br/><strong>Name:</strong>&nbsp; {!! $client_name !!}<br/><strong>E-mail:</strong> {!! $client_email !!}<br/><br/><strong>Message</strong><br/>{!! $message_content !!}<br/><br/>Kind Regards,<br />{!! $system_from !!}</p>', '', id, '2018-06-11 10:00:53', '2018-06-11 10:00:53', 'org-mngr-templates'
FROM `template_sets`;

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'template-ticket-creation-acknowledgement-org-mngr-by-agent', '0', 6, '', '<p>Hello {!! $receiver_name !!},<br/><br/>Our agent has created a new ticket for a request of your organization&#39;s member.</p><p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/><strong>Member&#39;s Details</strong><br/><strong>Name:</strong>&nbsp; {!! $client_name !!}<br/><strong>E-mail:</strong> {!! $client_email !!}<br/><br/><strong>Message</strong><br/>{!! $message_content !!}<br/><br/>Kind Regards,<br />{!! $system_from !!}</p>', '', id, '2018-06-11 10:00:53', '2018-06-11 10:00:53', 'org-mngr-templates'
FROM `template_sets`;


INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'template-ticket-reply-to-agents-by-client-to-org-mngr', '0', 9, '', '{!! $message_content !!}<br /><br /><strong>Ticket Details</strong><strong>Ticket ID:</strong> {!! $ticket_number !!}<br /><br />{!! $agent_signature !!}', '', id, '2018-06-11 10:00:53', '2018-06-11 10:00:53', 'org-mngr-templates'
FROM `template_sets`;

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'template-ticket-reply-to-client-by-agent-to-org-mngr', '0', 10, '', 'Hello {!! $receiver_name !!},<br /><br />Your organization member has made a new reply on their ticket in our helpdesk system.<br /><br /><strong>Ticket ID:</strong>  {!! $ticket_number !!}<br /><strong>Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />', '', id, '2018-06-11 10:00:53', '2018-06-11 10:00:53', 'org-mngr-templates'
FROM `template_sets`;

INSERT INTO `templates` (`name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT 'template-ticket-assignment-notice-to-org-mngr', '0', 1, '', 'Hello {!! $receiver_name !!},<br /><br />Your organization member\'s ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. They will contact them soon.<br /><br /><br />Kind Regards,<br />{!! $system_from !!}</p>', '', id, '2018-06-11 10:00:53', '2018-06-11 10:00:53', 'org-mngr-templates'
FROM `template_sets`;

UPDATE `settings_system` SET `version` = 'v1.9.32';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
