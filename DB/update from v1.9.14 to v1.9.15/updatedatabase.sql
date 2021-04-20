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
-- Table structure for table `agent_type_relations`
--

CREATE TABLE `agent_type_relations` (
  `id` int(10) UNSIGNED NOT NULL,
  `type_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_metas`
--

CREATE TABLE `approval_metas` (
  `id` int(10) UNSIGNED NOT NULL,
  `approval_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extra_orgs`
--

CREATE TABLE `extra_orgs` (
  `id` int(10) UNSIGNED NOT NULL,
  `org_id` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kb_article_template`
--

CREATE TABLE `kb_article_template` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `title`, `email`, `phone`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bangalore', '', '', '', 0, '2017-10-02 23:01:08', '2017-10-02 23:01:08'),
(2, 'Delhi', '', '', '', 0, '2017-10-02 23:01:08', '2017-10-02 23:01:08'),
(3, 'Kolkata', '', '', '', 0, '2017-10-02 23:01:08', '2017-10-02 23:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `organization_dept`
--

CREATE TABLE `organization_dept` (
  `id` int(10) UNSIGNED NOT NULL,
  `org_id` int(11) NOT NULL,
  `org_deptname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hours_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recure_contents`
--

CREATE TABLE `recure_contents` (
  `id` int(10) UNSIGNED NOT NULL,
  `recur_id` int(11) NOT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurs`
--

CREATE TABLE `recurs` (
  `id` int(10) UNSIGNED NOT NULL,
  `interval` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `delivery_on` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `last_execution` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent_type_relations`
--
ALTER TABLE `agent_type_relations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval_metas`
--
ALTER TABLE `approval_metas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extra_orgs`
--
ALTER TABLE `extra_orgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kb_article_template`
--
ALTER TABLE `kb_article_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organization_dept`
--
ALTER TABLE `organization_dept`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recure_contents`
--
ALTER TABLE `recure_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recurs`
--
ALTER TABLE `recurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agent_type_relations`
--
ALTER TABLE `agent_type_relations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `approval_metas`
--
ALTER TABLE `approval_metas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `extra_orgs`
--
ALTER TABLE `extra_orgs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kb_article_template`
--
ALTER TABLE `kb_article_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `organization_dept`
--
ALTER TABLE `organization_dept`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `recure_contents`
--
ALTER TABLE `recure_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `recurs`
--
ALTER TABLE `recurs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Alter users table
--
ALTER TABLE `users` ADD `location` VARCHAR(255) NULL DEFAULT NULL AFTER `updated_at`;

--
-- Alter department table add two more columns
--
ALTER TABLE `department` ADD `business_hour` INT(11) NOT NULL AFTER `updated_at`, ADD `nodes` LONGTEXT NULL DEFAULT NULL AFTER `business_hour`;

--
-- Alter SLA table, add 4 new columns
--
ALTER TABLE `sla_plan` ADD `apply_sla_helptopic` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `apply_sla_orgdepts` VARCHAR(255) NOT NULL AFTER `apply_sla_helptopic`, ADD `apply_sla_labels` VARCHAR(255) NOT NULL AFTER `apply_sla_orgdepts`, ADD `apply_sla_tags` VARCHAR(255) NOT NULL AFTER `apply_sla_labels`;

--
-- Aletr `user_assign_organization` table added org_department column
--
ALTER TABLE `user_assign_organization` ADD `org_department` VARCHAR(255) NOT NULL AFTER `role`;

--
-- Alter helptopic table added 2 new coulmns
--
ALTER TABLE `help_topic` ADD `nodes` LONGTEXT NULL DEFAULT NULL AFTER `updated_at`, ADD `linked_departments` VARCHAR(5000) NULL DEFAULT NULL AFTER `nodes`;

--
-- Alter ticket_source table added 2 new columns
--
ALTER TABLE `ticket_source` ADD `description` LONGTEXT NULL DEFAULT NULL AFTER `css_class`, ADD `location` VARCHAR(255) NOT NULL AFTER `description`;

--
-- Alter socail_media table structure made value nullable 
--
ALTER TABLE `social_media` CHANGE `value` `value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

--
-- Alter table kb_article, added new coulumn
--
ALTER TABLE `kb_article` ADD `template` VARCHAR(255) NOT NULL AFTER `updated_at`;

--
-- Alter kb_settings table
--
ALTER TABLE `kb_settings` ADD `status` INT(11) NOT NULL AFTER `updated_at`;

--
-- Alter settings_system table added 2 new columns 
--
ALTER TABLE `settings_system` ADD `serial_key` VARCHAR(100) NULL DEFAULT NULL AFTER `updated_at`, ADD `order_number` VARCHAR(100) NULL DEFAULT NULL AFTER `serial_key`;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `form`, `json`, `created_at`, `updated_at`) VALUES
(2, 'user', '[{ \'title\': \'First Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'First Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'First Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'first_name\' },{ \'title\': \'Last Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Last Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Last Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'last_name\' },{ \'title\': \'Work Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Work Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Work Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'phone_number\' },{ \'title\': \'Email\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Email\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Email\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'email\', \'agentRequiredFormSubmit\':true, \'customerDisplay\':true, \'customerRequiredFormSubmit\':true, \'value\':\'\', \'default\':\'yes\', \'unique\':\'email\' },{ \'title\': \'Mobile Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Mobile Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Mobile Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'mobile\' },{ \'title\': \'Address\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'no\', \'unique\':\'address\' },{ \'title\': \'Organisation\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organisation\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organisation\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'organisation\' },{ \'title\': \'Department Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'department\', \'options\':[ ], \'api\':\'organisationdept\' }, { \'title\': \'Captcha\', \'agentShow\':true, \'customerDisplay\':true, \'default\':\'yes\' }]', NULL, NULL),
(3, 'organisation', '[{ \'title\': \'Company Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Company Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Company Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'name\' },{ \'title\': \'Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Phone\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'customerDisplay\':true, \'agentRequiredFormSubmit\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'phone\' },{ \'title\': \'Company Domain Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Company Domain Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Company Domain Name\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'domain\' },{ \'title\': \'Description\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'internal_notes\' },{ \'title\': \'Address\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'address\' },{ \'title\': \'Manager\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Manager\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Manager\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'head\', \'api\':\'assigned_to\' },{ \'title\': \'Department\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://localhost/ladybird/master/pro/Faveo-Helpdesk-Pro/public/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'department\' }]', NULL, NULL);

--
-- Table structure for table `requireds`
--
DROP TABLE IF EXISTS `requireds`;

CREATE TABLE `requireds` (
  `id` int(10) UNSIGNED NOT NULL,
  `form` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `requireds`
--

INSERT INTO `requireds` (`id`, `form`, `field`, `agent`, `client`, `parent`, `option`, `label`, `created_at`, `updated_at`) VALUES
(1, 'ticket', 'requester', 'required', 'required', NULL, '', 'Requester', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(2, 'ticket', 'subject', 'required', 'required', NULL, '', 'Subject', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(3, 'ticket', 'type', 'required', 'required', NULL, '', 'Type', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(4, 'ticket', 'status', 'required', NULL, NULL, '', 'Status', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(5, 'ticket', 'priority', 'required', 'required', NULL, '', 'Priority', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(6, 'ticket', 'location', 'required', 'required', NULL, '', 'Location', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(7, 'ticket', 'help_topic', 'required', 'required', NULL, '', 'Help Topic', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(8, 'ticket', 'department', 'required', NULL, NULL, '', 'Department', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(9, 'ticket', 'assigned', 'required', NULL, NULL, '', 'Assigned', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(10, 'ticket', 'description', 'required', NULL, NULL, '', 'Description', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(11, 'ticket', 'company', NULL, NULL, NULL, '', 'Company', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(12, 'ticket', '', NULL, NULL, NULL, '', '', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(13, 'user', 'first_name', 'required', 'required', NULL, '', 'First Name', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(14, 'user', 'last_name', 'required', 'required', NULL, '', 'Last Name', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(15, 'user', 'phone_number', 'required', 'required', NULL, '', 'Work Phone', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(16, 'user', 'email', 'required', NULL, NULL, '', 'Email', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(17, 'user', 'mobile', 'required', 'required', NULL, '', 'Mobile Phone', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(18, 'user', 'address', 'required', 'required', NULL, '', 'Address', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(19, 'user', 'organisation', NULL, NULL, NULL, '', 'Organisation', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(20, 'user', 'department', NULL, NULL, NULL, '', 'Department Name', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(21, 'user', '', NULL, NULL, NULL, '', '', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(22, 'organisation', 'name', 'required', 'required', NULL, '', 'Company Name', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(23, 'organisation', 'phone', NULL, NULL, NULL, '', 'Phone', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(24, 'organisation', 'domain', NULL, NULL, NULL, '', 'Company Domain Name', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(25, 'organisation', 'internal_notes', NULL, NULL, NULL, '', 'Description', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(26, 'organisation', 'address', NULL, NULL, NULL, '', 'Address', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(27, 'organisation', 'head', NULL, NULL, NULL, '', 'Manager', '2017-10-04 01:53:47', '2017-10-04 01:53:47'),
(28, 'organisation', 'department', NULL, NULL, NULL, '', 'Department', '2017-10-04 01:53:47', '2017-10-04 01:53:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `requireds`
--
ALTER TABLE `requireds`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `requireds`
--
ALTER TABLE `requireds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

-- 
-- Dumping data for table `common_settings`
-- 

INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('micro_organization_status', '', '0', '', '2017-10-04 01:53:47', '2017-10-04 01:53:47');


--
-- Dumping data for table `template_types`
--

INSERT INTO `template_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(22, 'approve-ticket', '2017-10-04 01:53:46', '2017-10-04 01:53:46'),
(23, 'invoice', '2017-10-04 01:53:46', '2017-10-04 01:53:46');

--
-- Dumping data for table `templates`
--

INSERT INTO `templates`(`id`, `name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  33,
  'template-ticket-approve',
  '0',
  22,
  '',
  'Hello {!! $agent_name !!},<br /><br />An agent has applied for approval to ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br /><br /><strong>Approve link</strong><br />{!! $approve_url !!}<br /><strong>Deny link</strong><br />{!! $deny_url !!}<br /><br />Thanks.<br />{!! $system_from !!}',
  '',
  `id`,
  '2017-10-04 01:53:46',
  '2017-10-04 01:53:46',
  'agent-templates'
FROM
  `template_sets`;
  
INSERT INTO `templates`(`id`, `name`, `variable`, `type`, `subject`, `message`, `description`, `set_id`, `created_at`, `updated_at`, `template_category`)
SELECT
  34,
  'invoice',
  '0',
  23,
  '',
  'Hello {!! $client_name !!},<br /><br />An invoice has generated for ticket with ID: {!! $ticket_number !!}.<br /><br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br /><br /><strong>Total time spend</strong><br />{!! $total_time !!} Hours<br /><br /><strong>Cost</strong><br />{!! $currency !!}{!! $cost !!}<br /><br /><strong>Billing Date</strong><br />{!! $bill_date !!}<br /><br />Thanks.<br />{!! $system_from !!}',
  '',
  `id`,
  '2017-10-04 01:53:46',
  '2017-10-04 01:53:46',
  'client-templates'
FROM
  `template_sets`;

--
-- Delete extra widgets from widget table
--
DELETE FROM `widgets` WHERE `name` = 'side1' OR `name` = 'side2';

--
-- Delete extra records from source table
--
DELETE FROM `ticket_source` WHERE `css_class` = '';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
