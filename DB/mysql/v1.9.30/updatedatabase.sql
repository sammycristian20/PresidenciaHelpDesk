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

-- ------------------------------------------------
--
-- Insert new records in common_settings table
--
INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('allow_organization_mngr_approve_tickets', '', '0', '', '2018-05-02 00:57:03', '2018-05-03 00:31:51'),
('allow_organization_dept_mngr_approve_tickets', '', '0', '', '2018-05-02 00:57:03', '2018-05-03 00:31:51');


-- -------------------------------------------------
--  Update key names in workflow and listenr tables
--
UPDATE `listener_rules` SET `key` = 'requester_email' WHERE `key` = 'requester' OR `key` = 'user_requester';
UPDATE `listener_rules` SET `key` = 'requester_name' WHERE `key` = 'name';
UPDATE `workflow_rules` SET `matching_scenario` = 'requester_email' WHERE `matching_scenario` = 'requester' OR `matching_scenario` = 'user_requester';
UPDATE `workflow_rules` SET `matching_scenario` = 'requester_name' WHERE `matching_scenario` = 'name';

-- ---------------------------------------------------
--
-- Alter listener_action table
--
ALTER TABLE `listener_actions` CHANGE `custom_action` `custom_action` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- ---------------------------------------------------
-- 
-- Alter  listener rules table
--
ALTER TABLE `listener_rules` CHANGE `custom_rule` `custom_rule` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


-- ---------------------------------------------------
--
-- Alter workflow_action table
--
ALTER TABLE `workflow_action` CHANGE `custom_action` `custom_action` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


-- ---------------------------------------------------
-- 
-- Alter  workflow_rules table
--
ALTER TABLE `workflow_rules` CHANGE `custom_rule` `custom_rule` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- ---------------------------------------------------
--
-- Alter org_dept table
--

ALTER TABLE `organization_dept` ADD `org_dept_manager` TEXT NULL DEFAULT NULL AFTER `updated_at`;


-- 
-- Alter organisation_department column in user_assign_organisation
-- 
UPDATE `settings_system` SET `version` = 'v1.9.30';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
