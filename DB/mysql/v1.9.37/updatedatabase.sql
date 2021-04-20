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
-- Database: `faveo_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action_taker_id` int(11) DEFAULT NULL,
  `action_taker_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `initial_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `final_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_created` tinyint(1) NOT NULL,
  `is_updated` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


--
-- Alter settings_ticket table add new columns for inbox settings
--
ALTER TABLE
    `settings_ticket` ADD `count_internal` TINYINT NOT NULL DEFAULT '0' AFTER `updated_at`,
    ADD `show_status_date` TINYINT NOT NULL DEFAULT '0' AFTER `count_internal`,
    ADD `show_org_details` TINYINT NOT NULL DEFAULT '0' AFTER `show_status_date`,
    ADD `custom_field_name` TEXT NULL DEFAULT NULL AFTER `show_org_details`;

-- 
-- Dumping data for table `common_settings`
-- 

INSERT INTO `common_settings` (`option_name`, `option_value`, `status`, `optional_field`, `created_at`, `updated_at`) VALUES
('show_gravatar_image', '', 1, '', '2018-08-20 00:00:07', '2018-08-20 00:00:07'),
('time_track', '', 0, '', '2018-08-20 00:00:07', '2018-08-20 00:00:07');


--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2016_02_16_140450_create_banlist_table', 1),
(2, '2016_02_16_140450_create_canned_response_table', 1),
(3, '2016_02_16_140450_create_custom_form_fields_table', 1),
(4, '2016_02_16_140450_create_custom_forms_table', 1),
(5, '2016_02_16_140450_create_date_format_table', 1),
(6, '2016_02_16_140450_create_date_time_format_table', 1),
(7, '2016_02_16_140450_create_department_table', 1),
(8, '2016_02_16_140450_create_emails_table', 1),
(9, '2016_02_16_140450_create_group_assign_department_table', 1),
(10, '2016_02_16_140450_create_groups_table', 1),
(11, '2016_02_16_140450_create_help_topic_table', 1),
(12, '2016_02_16_140450_create_kb_article_relationship_table', 1),
(13, '2016_02_16_140450_create_kb_article_table', 1),
(14, '2016_02_16_140450_create_kb_category_table', 1),
(15, '2016_02_16_140450_create_kb_comment_table', 1),
(16, '2016_02_16_140450_create_kb_pages_table', 1),
(17, '2016_02_16_140450_create_kb_settings_table', 1),
(18, '2016_02_16_140450_create_languages_table', 1),
(19, '2016_02_16_140450_create_log_notification_table', 1),
(20, '2016_02_16_140450_create_mailbox_protocol_table', 1),
(21, '2016_02_16_140450_create_organization_table', 1),
(22, '2016_02_16_140450_create_password_resets_table', 1),
(23, '2016_02_16_140450_create_plugins_table', 1),
(24, '2016_02_16_140450_create_settings_alert_notice_table', 1),
(25, '2016_02_16_140450_create_settings_auto_response_table', 1),
(26, '2016_02_16_140450_create_settings_company_table', 1),
(27, '2016_02_16_140450_create_settings_email_table', 1),
(28, '2016_02_16_140450_create_settings_ratings_table', 1),
(29, '2016_02_16_140450_create_settings_system_table', 1),
(30, '2016_02_16_140450_create_settings_ticket_table', 1),
(31, '2016_02_16_140450_create_sla_plan_table', 1),
(32, '2016_02_16_140450_create_team_assign_agent_table', 1),
(33, '2016_02_16_140450_create_teams_table', 1),
(34, '2016_02_16_140450_create_template_table', 1),
(35, '2016_02_16_140450_create_ticket_attachment_table', 1),
(36, '2016_02_16_140450_create_ticket_collaborator_table', 1),
(37, '2016_02_16_140450_create_ticket_form_data_table', 1),
(38, '2016_02_16_140450_create_ticket_priority_table', 1),
(39, '2016_02_16_140450_create_ticket_source_table', 1),
(40, '2016_02_16_140450_create_ticket_status_table', 1),
(41, '2016_02_16_140450_create_ticket_thread_table', 1),
(42, '2016_02_16_140450_create_tickets_table', 1),
(43, '2016_02_16_140450_create_time_format_table', 1),
(44, '2016_02_16_140450_create_timezone_table', 1),
(45, '2016_02_16_140450_create_user_assign_organization_table', 1),
(46, '2016_02_16_140450_create_users_table', 1),
(47, '2016_02_16_140450_create_version_check_table', 1),
(48, '2016_02_16_140450_create_widgets_table', 1),
(49, '2016_02_16_140454_add_foreign_keys_to_canned_response_table', 1),
(50, '2016_02_16_140454_add_foreign_keys_to_department_table', 1),
(51, '2016_02_16_140454_add_foreign_keys_to_emails_table', 1),
(52, '2016_02_16_140454_add_foreign_keys_to_group_assign_department_table', 1),
(53, '2016_02_16_140454_add_foreign_keys_to_help_topic_table', 1),
(54, '2016_02_16_140454_add_foreign_keys_to_kb_article_relationship_table', 1),
(55, '2016_02_16_140454_add_foreign_keys_to_kb_comment_table', 1),
(56, '2016_02_16_140454_add_foreign_keys_to_organization_table', 1),
(57, '2016_02_16_140454_add_foreign_keys_to_settings_system_table', 1),
(58, '2016_02_16_140454_add_foreign_keys_to_team_assign_agent_table', 1),
(59, '2016_02_16_140454_add_foreign_keys_to_teams_table', 1),
(60, '2016_02_16_140454_add_foreign_keys_to_ticket_attachment_table', 1),
(61, '2016_02_16_140454_add_foreign_keys_to_ticket_collaborator_table', 1),
(62, '2016_02_16_140454_add_foreign_keys_to_ticket_form_data_table', 1),
(63, '2016_02_16_140454_add_foreign_keys_to_ticket_thread_table', 1),
(64, '2016_02_16_140454_add_foreign_keys_to_tickets_table', 1),
(65, '2016_02_16_140454_add_foreign_keys_to_user_assign_organization_table', 1),
(66, '2016_02_16_140454_add_foreign_keys_to_users_table', 1),
(67, '2016_03_31_061239_create_notifications_table', 1),
(68, '2016_03_31_061534_create_notification_types_table', 1),
(69, '2016_03_31_061740_create_user_notification_table', 1),
(70, '2016_04_18_115852_create_workflow_name_table', 1),
(71, '2016_04_18_115900_create_workflow_rule_table', 1),
(72, '2016_04_18_115908_create_workflow_action_table', 1),
(73, '2016_05_10_102423_create_country_code_table', 1),
(74, '2016_05_10_102604_create_bar_notifications_table', 1),
(75, '2016_05_11_105244_create_api_settings_table', 1),
(76, '2016_05_19_055008_create_workflow_close_table', 1),
(77, '2016_06_02_072210_create_common_settings_table', 1),
(78, '2016_06_02_074913_create_login_attempts_table', 1),
(79, '2016_06_02_080005_create_ratings_table', 1),
(80, '2016_06_02_081020_create_rating_ref_table', 1),
(81, '2016_06_02_090225_create_settings_security_table', 1),
(82, '2016_06_02_090628_create_templates_table', 1),
(83, '2016_06_02_094409_create_template_sets_table', 1),
(84, '2016_06_02_094420_create_template_types_table', 1),
(85, '2016_06_02_095357_create_ticket_token_table', 1),
(86, '2016_06_28_141613_version1079table', 1),
(87, '2016_07_02_051247_create_jobs_table', 1),
(88, '2016_07_02_051439_create_failed_jobs_table', 1),
(89, '2016_07_19_071910_create_field_values_table', 1),
(90, '2016_07_26_084458_create_faveo_mails_table', 1),
(91, '2016_07_26_090201_create_faveo_queues_table', 1),
(92, '2016_07_26_094753_create_mail_services_table', 1),
(93, '2016_07_26_095020_create_queue_services_table', 1),
(94, '2016_07_29_113012_create_conditions_table', 1),
(95, '2016_08_08_095744_create_social_media_table', 1),
(96, '2016_08_12_104410_create_user_additional_infos_table', 1),
(97, '2016_08_16_104539_alter_ticket_source_table', 1),
(98, '2016_08_31_223407_create_approval_table', 1),
(99, '2016_09_02_165516_create_follow_up_table', 1),
(100, '2016_09_05_140010_create_status_type_table', 1),
(101, '2016_09_05_140016_alter_status_table', 1),
(102, '2016_09_07_125906_business_hours_table', 1),
(103, '2016_09_07_130846_business_schedule_table', 1),
(104, '2016_09_07_130925_business_schedule_open_custom_time_table', 1),
(105, '2016_09_07_131000_business_holiday_table', 1),
(106, '2016_10_07_173423_create_ticket_type_table', 1),
(107, '2016_10_10_104534_create_labels_table', 1),
(108, '2016_10_10_113555_create_sla_targets_table', 1),
(109, '2016_10_10_124120_create_filters_table', 1),
(110, '2016_10_12_072502_create_tags_table', 1),
(111, '2016_11_07_123810_create_halts_table', 1),
(112, '2016_12_13_073613_create_department_assign_agents_table', 1),
(113, '2017_02_01_110129_update-notification', 1),
(114, '2017_02_02_073016_update_ticket_thread', 1),
(115, '2017_02_07_104802_update_notification_table', 1),
(116, '2017_02_09_093019_create_system_portal_table', 1),
(117, '2017_02_11_072240_create_sla_violated_escalatetable', 1),
(118, '2017_02_11_074259_create_sla_approach_escalatetable', 1),
(119, '2017_02_22_075706_update_users_table_22_1_2017', 1),
(120, '2017_02_23_044150_report_updates', 1),
(121, '2017_02_25_072334_DepartmentCannedResponse', 1),
(122, '2017_02_27_184502_create_org_attachment_table', 1),
(123, '2017_02_28_102237_create_licenses_table', 1),
(124, '2017_03_06_074629_notification_update', 1),
(125, '2017_03_07_073611_alter_tickets_table', 1),
(126, '2017_03_07_121632_updated_halts', 1),
(127, '2017_03_10_044621_update_settings_system', 1),
(128, '2017_03_24_095825_update_job', 1),
(129, '2017_04_03_063230_update_business_hours', 1),
(130, '2017_04_03_090919_create_no_assign_escalates_table', 1),
(131, '2017_04_30_151807_create_update_organization', 1),
(132, '2017_04_30_192424_create_update_groups', 1),
(133, '2017_05_05_082718_update_custom_form', 1),
(134, '2017_05_25_062940_update_ticket_status', 1),
(135, '2017_06_15_115324_create_email_threads_table', 1),
(136, '2017_06_16_113453_permision_update', 1),
(137, '2017_06_17_072635_add_template_lang_to_template_sets_table', 1),
(138, '2017_06_19_060545_add_template_catgory_to_template_table', 1),
(139, '2017_07_04_200732_AlterUserTableForAccountVerification', 1),
(140, '2017_07_20_115612_create_requireds_table', 1),
(141, '2017_07_24_102940_update_email_threads', 1),
(142, '2017_08_01_101844_create_extra_orgs_table', 1),
(143, '2017_08_07_130239_update_department', 1),
(144, '2017_08_08_072909_create_agent_type_relations_table', 1),
(145, '2017_08_12_063307_create_location_table', 1),
(146, '2017_08_14_054817_add_paid_to_kb_category', 1),
(147, '2017_08_14_073909_add_location_to_users', 1),
(148, '2017_08_17_115427_add_apply_sla_helptopic_to_sla_plan', 1),
(149, '2017_08_18_065645_add_domain_to_organization', 1),
(150, '2017_08_23_072141_add_apply_sla_orgdepts_to_sla_plan', 1),
(151, '2017_08_26_090230_create_organization_dept_table', 1),
(152, '2017_08_28_065405_add_department_to_user_assign_organization', 1),
(153, '2017_09_07_110839_add_nodes_to_departments_table', 1),
(154, '2017_09_07_110924_add_nodes_to_helptopic_table', 1),
(155, '2017_09_12_104144_add_descriptin_to_ticket_source', 1),
(156, '2017_09_13_114806_create_recurs_table', 1),
(157, '2017_09_13_114826_create_recure_contents_table', 1),
(158, '2017_09_14_064053_add_location_to_ticket_source', 1),
(159, '2017_09_14_064205_add_apply_sla_labels_to_sla_plan', 1),
(160, '2017_09_14_092331_create_kb_article_template_table', 1),
(161, '2017_09_14_131434_add_template_to_kb_article', 1),
(162, '2017_09_18_081123_create_approval_metas_table', 1),
(163, '2017_09_19_083527_add_sco_to_kb_pages', 1),
(164, '2017_09_21_063508_alter_social_media_table', 1),
(165, '2017_09_26_052319_add_linked_departments_in_help_topic', 1),
(166, '2017_09_29_075059_add_sco_to_kb_article', 1),
(167, '2017_09_30_100735_add_status_to_kb_settings', 1),
(168, '2017_10_03_063738_add_serial_key_and_order_number_in_system_setting', 1),
(169, '2017_10_31_073326_add_is_default_to_location', 1),
(170, '2017_11_11_081711_create_listeners_table', 1),
(171, '2017_11_11_081739_create_listener_events_table', 1),
(172, '2017_11_11_081751_create_listener_rules_table', 1),
(173, '2017_11_11_081806_create_listener_actions_table', 1),
(174, '2017_11_23_105805_add_logo_to_organization', 1),
(175, '2017_12_05_055135_create_team_assign_department_table', 1),
(176, '2017_12_06_055652_add_order_to_sla_plan', 1),
(177, '2017_12_06_074424_add_in_app_to_sla_target', 1),
(178, '2017_12_09_111807_alter_listener_actions', 1),
(179, '2017_12_09_111905_alter_listener_rules', 1),
(180, '2017_12_14_093039_add_custom_rule', 1),
(181, '2017_12_14_093106_add_custom_action', 1),
(182, '2017_12_29_092541_add_is_default_to_sla_plan', 1),
(183, '2018_01_02_093934_create_department_assign_manager_table', 1),
(184, '2018_01_02_172636_create_helptopic_assign_type_table', 1),
(185, '2018_01_11_072745_rename_kb_article_column', 1),
(186, '2018_01_11_074226_rename_kb_pages_column', 1),
(187, '2018_01_15_121625_create_subscribed_users_table', 1),
(188, '2018_01_15_121833_create_in_app_push_notifications_table', 1),
(189, '2018_01_26_055048_alter_labels_table', 1),
(190, '2018_02_16_060521_add_match_rule_column_in_workflow_table', 1),
(191, '2018_04_06_074050_create_ticket_status_override_table', 1),
(192, '2018_04_17_083239_add_visible_to_to_kb_article', 1),
(193, '2018_04_17_185700_alter_user_assign_organization_table', 1),
(194, '2018_04_24_110724_add_profile_pic_to_kb_comment', 1),
(195, '2018_04_27_041305_add_datetime_to_kb_settings', 1),
(196, '2018_04_28_130536_alter_custom_rule_column_make_nullable_in_workflow_rules', 1),
(197, '2018_04_28_130615_alter_custom_rule_column_make_nullable_in_listener_rules', 1),
(198, '2018_04_28_130734_alter_custom_rule_column_make_nullable_in_worflow_action', 1),
(199, '2018_04_28_130820_alter_custom_rule_column_make_nullable_in_listener_action', 1),
(200, '2018_05_01_051004_add_org_dept_manager_to_organization_dept', 1),
(201, '2018_05_01_170751_add_service_columns_to_emails', 1),
(202, '2018_05_28_114843_add_en_auto_assign_column_in_department_table', 1),
(203, '2018_06_04_071810_alter_emails_table', 1),
(204, '2018_06_08_070241_add_example_to_country_code', 1),
(205, '2018_07_01_131928_create_sla_custom_enforcements_fields', 1),
(206, '2018_07_09_102925_create_reports_table', 1),
(207, '2018_07_27_070308_create_activity_logs_table', 1),
(208, '2018_07_30_014930_add_new_inbox_settings_to_settings_ticket', 1),
(209, '2018_08_17_095204_alter_status_data_type_common_settings', 1),
(210, '2016_10_18_085435_create_bills_table', 2),
(211, '2017_09_02_033151_bill_type_table', 2),
(212, '2018_07_30_130542_create_time_tracks_table', 3),
(213, '2016_05_29_131105_create_sd_cab_table', 4),
(214, '2016_05_30_084708_create_sd_attachment_types_table', 4),
(215, '2016_05_30_085022_create_sd_attachments_table', 4),
(216, '2016_05_30_095158_create_sd_impact_types_table', 4),
(217, '2016_05_30_095427_create_sd_location_categories_table', 4),
(218, '2016_05_30_095428_create_sd_locations_table', 4),
(219, '2016_05_31_100449_create_sd_release_types_table', 4),
(220, '2016_05_31_100518_create_sd_release_status_table', 4),
(221, '2016_05_31_100605_create_sd_release_priority_table', 4),
(222, '2016_05_31_100704_create_sd_releases_table', 4),
(223, '2016_05_31_102512_create_sd_change_status_table', 4),
(224, '2016_05_31_102537_create_sd_change_priority_table', 4),
(225, '2016_05_31_102602_create_sd_change_types_table', 4),
(226, '2016_05_31_102657_create_sd_changes_table', 4),
(227, '2016_06_09_133929_create_sd_problem_table', 4),
(228, '2016_07_05_103737_create_sd_cab_votes_table', 4),
(229, '2016_07_09_073642_create_sd_general_table', 4),
(230, '2016_07_09_123113_create_sd_problem_change_table', 4),
(231, '2016_07_09_123149_create_sd_change_release_table', 4),
(232, '2016_07_12_120647_create_table_sd_ticket_relation', 4),
(233, '2016_11_30_122809_alter_attachment_table', 5),
(234, '2016_11_14_120138_alter_users_table', 6);


-- 
-- Truncate bar_notification table 
-- 
TRUNCATE TABLE bar_notifications;

-- 
-- Update settings system table
-- 
UPDATE `settings_system` SET `version` = 'v1.9.37';

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;