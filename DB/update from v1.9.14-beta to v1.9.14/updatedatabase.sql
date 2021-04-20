-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2017 at 01:48 PM
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
-- Table structure for table `requireds`
--

CREATE TABLE `requireds` (
  `id` int(10) UNSIGNED NOT NULL,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL,
  `option` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- -----------------------------------------

-- 
-- Alter email_thread table structure
--
ALTER TABLE `email_threads` ADD `fetching_email` VARCHAR(255) NOT NULL AFTER `updated_at`;

-- ------------------------------------------------

-- 
-- Alter organization table to add domain
-- 
ALTER TABLE `organization` ADD `domain` VARCHAR(255) NULL DEFAULT NULL AFTER `updated_at`;

-- --------------------------------------------

-- 
-- Alter kb_category table
-- 
ALTER TABLE `kb_category` 
ADD `display_order` INT NOT NULL AFTER `updated_at`, 
ADD `visible_to` VARCHAR(255) NOT NULL DEFAULT 'all_users' AFTER `display_order`;

-- 
-- Update display order for alreadt existing Kb categories in db 
--
UPDATE `kb_category` SET `display_order` = `id` WHERE 1;

-- ------------------------------------------------
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
