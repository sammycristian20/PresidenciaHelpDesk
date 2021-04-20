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
-- Alter business hour table structure
--
ALTER TABLE  `business_hours` CHANGE  `timezone`  `timezone` VARCHAR( 50 ) NOT NULL ;

ALTER TABLE `business_hours` ADD `is_default` TINYINT NOT NULL DEFAULT '0' AFTER `updated_at`;
UPDATE `business_hours` SET `is_default` =  1 WHERE `id` = 1;

UPDATE `business_hours` JOIN `timezone` ON `business_hours`.`timezone` = `timezone`.`id` SET `business_hours`.`timezone` = `timezone`.`name` WHERE 1;