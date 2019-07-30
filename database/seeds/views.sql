-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.30-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

DROP TABLE IF EXISTS `view_project_messages`;
DROP TABLE IF EXISTS `view_todos`;
DROP TABLE IF EXISTS `view_users`;

-- Dumping structure for view local.ijobdesk.com.view_project_messages
DROP VIEW IF EXISTS `view_project_messages`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_project_messages` (
	`unique_id` VARCHAR(30) NULL COLLATE 'utf8mb4_unicode_ci',
	`proposal_id` INT(10) UNSIGNED NOT NULL,
	`contract_id` INT(10) UNSIGNED NULL,
	`subject` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`freelancer_name` VARCHAR(97) NULL COLLATE 'utf8mb4_unicode_ci',
	`freelancer_id` INT(10) UNSIGNED NOT NULL,
	`buyer_name` VARCHAR(97) NULL COLLATE 'utf8mb4_unicode_ci',
	`buyer_id` INT(10) UNSIGNED NOT NULL,
	`project_id` INT(10) UNSIGNED NOT NULL,
	`job_posting` VARCHAR(200) NULL COLLATE 'utf8mb4_unicode_ci',
	`related_job` VARCHAR(255) NULL COLLATE 'utf8mb4_unicode_ci',
	`thread_created_at` TIMESTAMP NULL,
	`id` INT(10) UNSIGNED NULL,
	`thread_id` INT(10) UNSIGNED NULL,
	`sender_id` INT(10) UNSIGNED NULL,
	`message` VARCHAR(5001) NULL COLLATE 'utf8mb4_unicode_ci',
	`created_at` TIMESTAMP NULL,
	`received_at` DATETIME NULL,
	`deleted_at` TIMESTAMP NULL
) ENGINE=MyISAM;

-- Dumping structure for view local.ijobdesk.com.view_todos
DROP VIEW IF EXISTS `view_todos`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_todos` (
	`id` INT(11) NOT NULL,
	`subject` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`type` TINYINT(1) NOT NULL,
	`creator_id` INT(11) NOT NULL,
	`assigner_ids` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`priority` TINYINT(1) NOT NULL,
	`due_date` DATE NULL,
	`related_ticket_id` INT(11) NULL,
	`description` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`status` TINYINT(1) NULL COMMENT '1: opening 2: complete 3: cancel',
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`assigner_names` TEXT NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Dumping structure for view local.ijobdesk.com.view_users
DROP VIEW IF EXISTS `view_users`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_users` (
	`id` INT(10) UNSIGNED NOT NULL,
	`unique_id` VARCHAR(30) NULL COLLATE 'utf8mb4_unicode_ci',
	`is_auto_suspended` TINYINT(1) NULL,
	`username` VARCHAR(128) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`created_at_ymd` VARCHAR(10) NULL COLLATE 'utf8mb4_unicode_ci',
	`created_at_ym` VARCHAR(7) NULL COLLATE 'utf8mb4_unicode_ci',
	`created_at_y` VARCHAR(4) NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(128) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`password` VARCHAR(128) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`status` TINYINT(1) NOT NULL,
	`login_blocked` TINYINT(4) NOT NULL,
	`try_password` TINYINT(1) NOT NULL COMMENT 'How many times to change the password with invalid password',
	`try_question` TINYINT(1) NOT NULL COMMENT 'How many times to answer to the security question',
	`remember_token` VARCHAR(100) NULL COLLATE 'utf8mb4_unicode_ci',
	`role` TINYINT(1) NULL COMMENT '1: freelancer, 2: buyer, 3: both, 4: super admin, 5: admin, 6: ticket manager, 7: site manager, 8: security manager',
	`closed_reason` TINYINT(1) NOT NULL COMMENT '1: Poor Service, 2: Irresponsive, 3: Complicated, 4: Poor Freelancers, 5: Other',
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`deleted_at` TIMESTAMP NULL,
	`hourly_rate` DOUBLE NULL,
	`profile_title` VARCHAR(256) NULL COLLATE 'utf8mb4_unicode_ci',
	`profile_desc` MEDIUMTEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`location` VARCHAR(114) NULL COLLATE 'utf8mb4_unicode_ci',
	`country` VARCHAR(64) NULL COLLATE 'utf8mb4_unicode_ci',
	`invoice_location` VARCHAR(116) NULL COLLATE 'utf8mb4_unicode_ci',
	`fullname` VARCHAR(97) NULL COLLATE 'utf8mb4_unicode_ci',
	`phone` VARCHAR(24) NULL COLLATE 'utf8mb4_unicode_ci',
	`timezone` VARCHAR(30) NULL COLLATE 'utf8mb4_unicode_ci',
	`timezone_label` VARCHAR(80) NULL COLLATE 'utf8mb4_unicode_ci',
	`role_name` VARCHAR(10) NULL COLLATE 'utf8mb4_unicode_ci',
	`last_activity` DATETIME NULL COMMENT 'Last Activity Time',
	`jobs` BIGINT(11) NULL,
	`job_success` INT(4) NULL,
	`hours` BIGINT(11) NULL,
	`total_spent` DOUBLE(20,2) NULL,
	`earning` DOUBLE(20,2) NULL,
	`feedback` DOUBLE NULL,
	`hires` INT(4) NULL
) ENGINE=MyISAM;

-- Dumping structure for view local.ijobdesk.com.view_project_messages
DROP VIEW IF EXISTS `view_project_messages`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_project_messages`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ijobdesk`@`localhost` SQL SECURITY DEFINER VIEW `view_project_messages` AS select `pmt`.`unique_id` AS `unique_id`,`pmt`.`application_id` AS `proposal_id`,`contracts`.`id` AS `contract_id`,`pmt`.`subject` AS `subject`,concat(convert(`fuc`.`first_name` using utf8),convert(convert(if(((`fuc`.`first_name` <> '') and (`fuc`.`last_name` <> '')),' ','') using utf8) using utf8),convert(`fuc`.`last_name` using utf8)) AS `freelancer_name`,`fusers`.`id` AS `freelancer_id`,concat(`buc`.`first_name`,convert(if(((`buc`.`first_name` <> '') and (`buc`.`last_name` <> '')),' ','') using utf8),`buc`.`last_name`) AS `buyer_name`,`busers`.`id` AS `buyer_id`,`p`.`id` AS `project_id`,`p`.`subject` AS `job_posting`,`c`.`title` AS `related_job`,`pmt`.`created_at` AS `thread_created_at`,`pm`.`id` AS `id`,`pm`.`thread_id` AS `thread_id`,`pm`.`sender_id` AS `sender_id`,`pm`.`message` AS `message`,`pm`.`created_at` AS `created_at`,`pm`.`received_at` AS `received_at`,`pm`.`deleted_at` AS `deleted_at` from (((((((((`project_message_threads` `pmt` join `project_applications` `pa` on((`pa`.`id` = `pmt`.`application_id`))) join `projects` `p` on((`p`.`id` = `pa`.`project_id`))) left join `contracts` `c` on((`c`.`project_id` = `p`.`id`))) join `users` `fusers` on((`fusers`.`id` = `pa`.`user_id`))) left join `user_contacts` `fuc` on((`fuc`.`user_id` = `fusers`.`id`))) join `users` `busers` on((`busers`.`id` = `p`.`client_id`))) left join `user_contacts` `buc` on((`buc`.`user_id` = `busers`.`id`))) left join `project_messages` `pm` on((`pmt`.`id` = `pm`.`thread_id`))) left join `contracts` on((`contracts`.`application_id` = `pmt`.`application_id`)));

-- Dumping structure for view local.ijobdesk.com.view_todos
DROP VIEW IF EXISTS `view_todos`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_todos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ijobdesk`@`localhost` SQL SECURITY DEFINER VIEW `view_todos` AS select `t`.`id` AS `id`,`t`.`subject` AS `subject`,`t`.`type` AS `type`,`t`.`creator_id` AS `creator_id`,`t`.`assigner_ids` AS `assigner_ids`,`t`.`priority` AS `priority`,`t`.`due_date` AS `due_date`,`t`.`related_ticket_id` AS `related_ticket_id`,`t`.`description` AS `description`,`t`.`status` AS `status`,`t`.`created_at` AS `created_at`,`t`.`updated_at` AS `updated_at`,group_concat(`vu`.`fullname`,',' separator ',') AS `assigner_names` from (`todos` `t` left join `view_users` `vu` on((`t`.`assigner_ids` like convert(concat('%[',`vu`.`id`,']%') using utf8)))) group by `t`.`id`;

-- Dumping structure for view local.ijobdesk.com.view_users
DROP VIEW IF EXISTS `view_users`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_users`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ijobdesk`@`localhost` SQL SECURITY DEFINER VIEW `view_users` AS select `users`.`id` AS `id`,`users`.`unique_id` AS `unique_id`,`users`.`is_auto_suspended` AS `is_auto_suspended`,`users`.`username` AS `username`,date_format(`users`.`created_at`,'%Y-%m-%d') AS `created_at_ymd`,date_format(`users`.`created_at`,'%Y-%m') AS `created_at_ym`,date_format(`users`.`created_at`,'%Y') AS `created_at_y`,`users`.`email` AS `email`,`users`.`password` AS `password`,`users`.`status` AS `status`,`users`.`login_blocked` AS `login_blocked`,`users`.`try_password` AS `try_password`,`users`.`try_question` AS `try_question`,`users`.`remember_token` AS `remember_token`,`users`.`role` AS `role`,`users`.`closed_reason` AS `closed_reason`,`users`.`created_at` AS `created_at`,`users`.`updated_at` AS `updated_at`,`users`.`deleted_at` AS `deleted_at`,if((`user_profiles`.`rate` <> ''),`user_profiles`.`rate`,0) AS `hourly_rate`,if((`user_profiles`.`title` <> ''),`user_profiles`.`title`,'') AS `profile_title`,if((`user_profiles`.`desc` <> ''),`user_profiles`.`desc`,'') AS `profile_desc`,concat(convert(if((`user_contacts`.`city` <> ''),concat(`user_contacts`.`city`,', '),'') using utf8),convert(`countries`.`name` using utf8)) AS `location`,`countries`.`name` AS `country`,concat(convert(if((`user_contacts`.`invoice_city` <> ''),concat(`user_contacts`.`invoice_city`,', '),'') using utf8),convert(`countries_inv`.`name` using utf8)) AS `invoice_location`,concat(convert(`user_contacts`.`first_name` using utf8),convert(convert(if(((`user_contacts`.`first_name` <> '') and (`user_contacts`.`last_name` <> '')),' ','') using utf8) using utf8),convert(`user_contacts`.`last_name` using utf8)) AS `fullname`,`user_contacts`.`phone` AS `phone`,`timezones`.`name` AS `timezone`,`timezones`.`label` AS `timezone_label`,if((`users`.`role` = 1),'Freelancer',if((`users`.`role` = 2),'Buyer','')) AS `role_name`,`user_stats`.`last_activity` AS `last_activity`,if((`user_stats`.`jobs_posted` <> ''),`user_stats`.`jobs_posted`,0) AS `jobs`,if((`user_stats`.`job_success` <> ''),`user_stats`.`job_success`,0) AS `job_success`,if((`user_stats`.`hours` <> ''),`user_stats`.`hours`,0) AS `hours`,if((`user_stats`.`total_spent` <> ''),`user_stats`.`total_spent`,0) AS `total_spent`,if((`user_stats`.`earning` <> ''),`user_stats`.`earning`,0) AS `earning`,if((`user_stats`.`score` <> ''),`user_stats`.`score`,0) AS `feedback`,if((`user_stats`.`open_contracts` <> ''),`user_stats`.`open_contracts`,0) AS `hires` from (((((((`users` left join `contracts` on((`contracts`.`buyer_id` = `users`.`id`))) left join `user_contacts` on((`user_contacts`.`user_id` = `users`.`id`))) left join `countries` on((convert(`countries`.`charcode` using utf8) = convert(`user_contacts`.`country_code` using utf8)))) left join `countries` `countries_inv` on((convert(`countries_inv`.`charcode` using utf8) = convert(`user_contacts`.`invoice_country_code` using utf8)))) left join `user_profiles` on((`user_profiles`.`user_id` = `users`.`id`))) left join `timezones` on((`timezones`.`id` = `user_contacts`.`timezone_id`))) left join `user_stats` on((`user_stats`.`user_id` = `users`.`id`))) group by `users`.`id`;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
