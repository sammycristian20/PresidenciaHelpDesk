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
-- Alter kb_pages table
-- 
ALTER TABLE `kb_pages` ADD `sco_title` VARCHAR(255) NOT NULL AFTER `updated_at`, ADD `meta_description` VARCHAR(255) NOT NULL AFTER `sco_title`;

-- --------------------------------------------------------
-- 
-- Alter `kb_article` table
--
ALTER TABLE `kb_article` ADD `sco_title` VARCHAR(255) NOT NULL AFTER `template`, ADD `meta_description` VARCHAR(255) NOT NULL AFTER `sco_title`;