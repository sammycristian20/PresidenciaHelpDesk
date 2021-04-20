-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 29, 2018 at 05:37 AM
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
-- Dumping data for table `common_settings`
--

INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('redirect_unauthenticated_users_to', '', '', '', '2018-03-29 02:37:42', '2018-03-29 02:37:42'),
('validate_token_api', '', '', '', '2018-03-29 02:37:42', '2018-03-29 02:37:42'),
('validate_api_parameter', 'token', '', '', '2018-03-29 02:37:42', '2018-03-29 02:37:42');

-- --------------------------------------------------------
--
-- Dumping data for table `ticket_status_type`
--

INSERT INTO `ticket_status_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
(7, 'unapproved', '2018-03-28 08:56:08', '2018-03-28 08:56:08');

--
-- Dumping data for table `ticket_status`
--

INSERT INTO `ticket_status` (`name`, `message`, `created_at`, `updated_at`, `visibility_for_client`, `allow_client`, `visibility_for_agent`, `purpose_of_status`, `secondary_status`, `send_email`, `halt_sla`, `order`, `icon`, `icon_color`, `default`) VALUES
('Unapproved', 'Ticket have been marked as Unapproved by {!!$user!!}', '2018-03-28 08:56:08', '2018-03-28 08:56:08', 0, 0, 1, 7, NULL, '{\"client\":\"0\",\"admin\":\"1\",\"assigned_agent_team\":\"0\"}', 0, 7, 'fa fa-clock-o', '#f20630', 1);


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
