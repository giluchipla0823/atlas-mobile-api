/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `atlas_auth` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci */;
USE `atlas_auth`;

CREATE TABLE IF NOT EXISTS `compounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `compounds` DISABLE KEYS */;
INSERT INTO `compounds` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(2, 'VALENCIA FORD PLANT TEST', '2021-03-24 11:01:43', '2021-03-24 11:01:43');
/*!40000 ALTER TABLE `compounds` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `uuid` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `version` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `imei` (`uuid`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `devices_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
INSERT INTO `devices` (`id`, `name`, `uuid`, `type_id`, `version`, `created_at`, `updated_at`) VALUES
	(1, '225', 0, 1, '1.0.0', '2021-03-15 11:45:10', '2021-03-15 11:45:10'),
	(2, '219', 93825462, 2, NULL, '2021-04-21 12:50:10', '2021-04-21 12:50:10'),
	(4, 'MOB0003', 666666666, 2, NULL, '2021-11-14 21:18:29', '2021-11-14 21:18:29'),
	(5, 'MOB0005', 666666667, 2, NULL, '2021-11-14 21:20:39', '2021-11-14 21:20:39'),
	(6, 'MOB0006', 666666668, 2, NULL, '2021-11-14 21:21:17', '2021-11-14 21:21:17'),
	(7, 'MOB0007', 666666669, 2, NULL, '2021-11-14 21:34:25', '2021-11-14 21:34:25'),
	(8, 'PDA0008', 666666679, 1, NULL, '2021-11-14 21:35:24', '2021-11-14 21:35:24'),
	(9, 'MOB0009', 666666689, 2, NULL, '2021-11-14 21:35:51', '2021-11-14 21:35:51');
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `devices_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `devices_type` DISABLE KEYS */;
INSERT INTO `devices_type` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'PDA', '2021-03-15 11:45:03', '2021-03-15 11:45:03'),
	(2, 'MOBILE', '2021-03-15 11:45:03', '2021-03-15 11:45:03');
/*!40000 ALTER TABLE `devices_type` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emitter` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `receiver` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `message` longtext COLLATE utf8_spanish_ci NOT NULL,
  `dt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` (`id`, `emitter`, `receiver`, `message`, `dt`) VALUES
	(1, 'david.victoria', 'david.victoria', 'd', '2021-04-16 10:28:23'),
	(2, 'david.victoria', 'david.victoria', 'd', '2021-04-16 10:28:26');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=333 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
	(329, 'App\\Models\\User', 10, 'access', '29d1f433cd56c2097a4607b39c6837a999b0a8bc1577ca3c50ba10be542dcf97', '["*"]', '2022-02-02 12:58:02', '2022-02-02 11:46:07', '2022-02-02 12:58:02'),
	(330, 'App\\Models\\User', 10, 'access', 'f3ddbed25cc99c47267338f9179bed4e85fc1428e7a08dba5c2932321f944749', '["*"]', NULL, '2022-02-02 14:03:54', '2022-02-02 14:03:54'),
	(331, 'App\\Models\\User', 10, 'access', '5787fda703a141eef5ddd64cb8e1f8b1750029e42a99f6eb0e1441cc4ec796f2', '["*"]', NULL, '2022-02-02 15:23:49', '2022-02-02 15:23:49'),
	(332, 'App\\Models\\User', 10, 'access', '9f283cf4d8d6b28aaf6a4fae9a82f733e7300d2c6ff07e6078a33cc6bf844eda', '["*"]', NULL, '2022-02-02 15:24:09', '2022-02-02 15:24:09');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `theme_color` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` (`id`, `name`, `theme_color`, `created_at`, `updated_at`) VALUES
	(12, 'default_david.victoria4', 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(13, 'default_david.victoria', 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(14, 'default_ignacio.tineo', 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `profiles_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `ordered` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_id` (`profile_id`),
  KEY `table_id` (`table_id`),
  KEY `profiles_config_ibfk_5` (`column_id`),
  CONSTRAINT `profiles_config_ibfk_3` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`),
  CONSTRAINT `profiles_config_ibfk_4` FOREIGN KEY (`table_id`) REFERENCES `atlas_config`.`tables` (`id`),
  CONSTRAINT `profiles_config_ibfk_5` FOREIGN KEY (`column_id`) REFERENCES `atlas_config`.`tables_columns` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `profiles_config` DISABLE KEYS */;
INSERT INTO `profiles_config` (`id`, `table_id`, `column_id`, `profile_id`, `ordered`, `active`, `created_at`, `updated_at`) VALUES
	(39, 1, 1, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(40, 2, 2, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(41, 1, 3, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(42, 1, 4, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(43, 1, 5, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(44, 1, 6, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(45, 1, 7, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(46, 1, 8, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(47, 1, 9, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(48, 1, 10, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(49, 1, 11, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(50, 2, 12, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(51, 2, 13, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(52, 2, 14, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(53, 2, 15, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(54, 2, 16, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(55, 2, 17, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(56, 2, 18, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(57, 2, 19, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(58, 2, 20, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(59, 2, 21, 12, 1, 1, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(60, 1, 1, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(61, 2, 2, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(62, 1, 3, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(63, 1, 4, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(64, 1, 5, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(65, 1, 6, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(66, 1, 7, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(67, 1, 8, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(68, 1, 9, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(69, 1, 10, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(70, 1, 11, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(71, 2, 12, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(72, 2, 13, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(73, 2, 14, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(74, 2, 15, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(75, 2, 16, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(76, 2, 17, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(77, 2, 18, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(78, 2, 19, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(79, 2, 20, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(80, 2, 21, 13, 1, 1, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(81, 1, 1, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(82, 2, 2, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(83, 1, 3, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(84, 1, 4, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(85, 1, 5, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(86, 1, 6, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(87, 1, 7, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(88, 1, 8, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(89, 1, 9, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(90, 1, 10, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(91, 1, 11, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(92, 2, 12, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(93, 2, 13, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(94, 2, 14, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(95, 2, 15, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(96, 2, 16, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(97, 2, 17, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(98, 2, 18, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(99, 2, 19, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(100, 2, 20, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23'),
	(101, 2, 21, 14, 1, 1, '2021-04-08 09:36:23', '2021-04-08 09:36:23');
/*!40000 ALTER TABLE `profiles_config` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `auth_level` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1 web, 2 movil',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `auth_level`, `type`) VALUES
	(1, 'user', 1, 1),
	(2, 'staff', 2, 1),
	(3, 'admin', 3, 1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) COLLATE utf8_spanish_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `two_factor_recovery_codes` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `auth_token` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `online` tinyint(1) NOT NULL,
  `change_pass` date DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `parent_compound` int(11) NOT NULL COMMENT 'COLUMNA PARA SABER EN Q CAMPA ESTAN ONLINE',
  `device_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `first_login` tinyint(1) NOT NULL DEFAULT 1,
  `web_user` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `admin_pin` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `device_id` (`device_id`),
  KEY `role_id` (`role_id`),
  KEY `profile_id` (`profile_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `surname`, `email`, `username`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `last_login`, `auth_token`, `online`, `change_pass`, `role_id`, `parent_compound`, `device_id`, `profile_id`, `first_login`, `web_user`, `created_at`, `updated_at`, `admin_pin`) VALUES
	(9, 'David', 'victoria martinez', 'david.victoria2@fractalys.es', 'david.victoria4', '$2y$04$wr6TCVfZ3CaXkOcSTV4aSu77FTtPHMMNHMgn7nGo75FkF0xDzyh.a', NULL, NULL, '2021-09-16 11:48:21', '9jJ6KkF1gOppvCsUwbxZfFaYfGL3AK89Bl3pq4ca', 1, '2028-11-15', 3, 2, NULL, 12, 0, 1, '2021-04-08 09:35:55', '2021-09-16 11:48:21', NULL),
	(10, 'David', 'victoria martinez', 'david.victoria@fractalys.es', 'david.victoria', '$2y$04$PUQDTPTWOuZv.nfhqo5Cv.wgJHgf4tQ8CE6q6aaV9NQ9fOpjkbVv2', NULL, NULL, '2021-07-05 09:45:24', '332|4s8A6q9D4sYNN8RVHqWyePqhLNS4mVS2QsrpQRJl', 1, '2022-01-28', 3, 2, 2, 13, 0, 0, '2021-04-08 09:35:58', '2022-02-02 15:24:09', 1234),
	(11, 'ignacio', 'tineo mercado', 'ignacio.tineo@fractalys.es', 'ignacio.tineo', '$2y$04$wr6TCVfZ3CaXkOcSTV4aSu77FTtPHMMNHMgn7nGo75FkF0xDzyh.a', NULL, NULL, '2021-07-23 09:29:49', 'iO7jstPb5dCAcUn6rXrfGuVWxzqh1PYZhtLG1zww', 1, '2021-10-19', 3, 2, NULL, 14, 0, 1, '2021-04-08 09:36:23', '2021-07-23 09:29:49', NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `users_compound_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `compound_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compound_id` (`compound_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_compound_permission_ibfk_1` FOREIGN KEY (`compound_id`) REFERENCES `compounds` (`id`),
  CONSTRAINT `users_compound_permission_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `users_compound_permission` DISABLE KEYS */;
INSERT INTO `users_compound_permission` (`id`, `user_id`, `compound_id`, `created_at`, `updated_at`) VALUES
	(8, 9, 2, '2021-04-08 09:35:55', '2021-04-08 09:35:55'),
	(9, 10, 2, '2021-04-08 09:35:58', '2021-04-08 09:35:58'),
	(10, 11, 2, '2021-04-08 09:36:23', '2021-04-08 09:36:23');
/*!40000 ALTER TABLE `users_compound_permission` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `users_recovery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `limit_dt` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `users_recovery` DISABLE KEYS */;
INSERT INTO `users_recovery` (`id`, `token`, `user_id`, `limit_dt`, `created_at`, `updated_at`) VALUES
	(29, '3hfK1DgcUMFvkgzvWGwnymUQe3qp6pbfP0wwMb3e', 9, '2021-04-17 15:44:25', '2021-04-16 15:44:25', '2021-04-16 15:44:25');
/*!40000 ALTER TABLE `users_recovery` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
