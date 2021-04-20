-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 05, 2018 at 02:08 PM
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

-- 
-- Alter table workflow_name
-- 
ALTER TABLE `workflow_name` ADD `rule_match` VARCHAR(10) NOT NULL DEFAULT 'all' AFTER `updated_at`;

-- 
-- Select and delete records from tickets which do not exist in ticket_thread
-- 
DELETE FROM `tickets` WHERE `id` NOT IN (SELECT `ticket_id` FROM `ticket_thread` GROUP BY `ticket_id`);

-- 
-- Select and delete records from ticket_thread which do not exist in tickets
-- 
DELETE FROM `ticket_thread` WHERE `ticket_id` NOT IN (SELECT `id` FROM `tickets`);

-- 
-- Update tickets table, update updated_at column with the value of last ticket_thread's updated_at value
-- 
UPDATE `tickets` SET `updated_at` = (SELECT `updated_at` FROM `ticket_thread` WHERE `ticket_thread`.`ticket_id` = `tickets`.`id` ORDER BY `updated_at` DESC LIMIT 1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;