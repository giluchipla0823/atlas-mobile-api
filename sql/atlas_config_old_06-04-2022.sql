/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `atlas_config` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci */;
USE `atlas_config`;

CREATE TABLE IF NOT EXISTS `compound_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `compound_id` int(10) unsigned NOT NULL DEFAULT 0,
  `parameter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `compound_configs` DISABLE KEYS */;
INSERT INTO `compound_configs` (`id`, `compound_id`, `parameter`, `value`, `created_at`, `updated_at`) VALUES
	(1, 2, 'prefix_pda', 'PDA', '2021-10-13 08:44:34', NULL),
	(2, 2, 'prefix_mobile', 'MOB', '2021-10-13 08:44:34', NULL),
	(3, 2, 'prefix_tablet', 'TAB', '2021-10-13 08:44:34', NULL),
	(4, 2, 'device_type', '1', '2021-10-13 08:44:34', NULL),
	(5, 2, 'default_overflow', '2', '2021-10-13 08:44:34', NULL),
	(6, 2, 'device_type', '2', '2021-10-13 08:44:34', NULL),
	(7, 2, 'initial_position_truck', '2', '2021-10-13 08:44:34', NULL),
	(8, 2, 'initial_position_factory', '2', '2021-10-13 08:44:34', NULL),
	(9, 2, 'initial_position_ship', '2', '2021-10-13 08:44:34', NULL),
	(10, 2, 'initial_position_train', '2', '2021-10-13 08:44:34', NULL),
	(11, 2, 'initial_position_default', '2', '2021-10-13 08:44:34', NULL),
	(12, 2, 'initial_position_plant', '350', '2021-10-13 08:44:34', NULL),
	(13, 2, 'initial_position_buffer', '437', '2021-10-13 08:44:34', NULL);
/*!40000 ALTER TABLE `compound_configs` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `models_datatables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `table_name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `models_datatables` DISABLE KEYS */;
INSERT INTO `models_datatables` (`id`, `model`, `table_name`) VALUES
	(1, '\\App\\Models\\Brand', 'Brand'),
	(2, '\\App\\Models\\Color', 'Colors'),
	(3, '\\App\\Models\\Carrier', 'Carriers'),
	(4, '\\App\\Models\\Country', 'Countries'),
	(5, '\\App\\Models\\DestinationCode', 'Destination_codes'),
	(6, '\\App\\Models\\Route', 'Routes'),
	(7, '\\App\\Models\\Design', 'Designs'),
	(8, '\\App\\Models\\Dealer', 'Dealers');
/*!40000 ALTER TABLE `models_datatables` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `compound_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` (`id`, `compound_id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 2, 'home', NULL, NULL),
	(2, 2, 'operations', NULL, NULL),
	(3, 2, 'entrytype', NULL, NULL),
	(4, 2, 'backtoplant', NULL, NULL),
	(5, 2, 'rellocate', NULL, NULL),
	(6, 2, 'load', NULL, NULL);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
INSERT INTO `tables` (`id`, `name`) VALUES
	(1, 'stock'),
	(2, 'search');
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tables_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accessor` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `header` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `table_id` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `configurable` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40000 ALTER TABLE `tables_columns` DISABLE KEYS */;
INSERT INTO `tables_columns` (`id`, `accessor`, `header`, `table_id`, `visible`, `configurable`) VALUES
	(1, 'vin', 'VIN', 1, 1, 0),
	(2, 'vin', 'VIN', 2, 1, 0),
	(3, 'model', 'MODEL', 1, 1, 1),
	(4, 'vin_short', 'VIN SHORT', 1, 1, 1),
	(5, 'color', 'COLOR', 1, 1, 1),
	(6, 'eoc', 'EOC', 1, 1, 1),
	(7, 'country', 'COUNTRY', 1, 1, 1),
	(8, 'state', 'STATE', 1, 1, 1),
	(9, 'position', 'POSITION', 1, 1, 1),
	(10, 'shipping_rule', 'SHIPPING RULE', 1, 1, 1),
	(11, 'dt_onterminal', 'DT TERMINAL', 1, 1, 1),
	(12, 'model', 'MODEL', 2, 1, 1),
	(13, 'vin_short', 'VIN SHORT', 2, 1, 1),
	(14, 'color', 'COLOR', 2, 1, 1),
	(15, 'eoc', 'EOC', 2, 1, 1),
	(16, 'country', 'COUNTRY', 2, 1, 1),
	(17, 'state', 'STATE', 2, 1, 1),
	(18, 'position', 'POSITION', 2, 1, 1),
	(19, 'shipping_rule', 'SHIPPING RULE', 2, 1, 1),
	(20, 'dt_onterminal', 'DT TERMINAL', 2, 1, 1),
	(21, 'dt_left', 'DT LEFT', 2, 1, 1);
/*!40000 ALTER TABLE `tables_columns` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` int(11) NOT NULL,
  `order` int(11) unsigned NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `tiles` DISABLE KEYS */;
INSERT INTO `tiles` (`id`, `page_id`, `title`, `icon`, `action`, `order`, `active`, `created_at`, `updated_at`) VALUES
	(1, 1, 'MOVEMENTS', 'custom-steering-wheel', 1, 1, 1, NULL, NULL),
	(2, 1, 'YARD OPERATIONS', 'custom-cogwheel', 2, 2, 1, NULL, NULL),
	(3, 1, 'HANDOVER', 'custom-import', 3, 3, 1, NULL, NULL),
	(4, 1, 'SEARCH', 'custom-search', 4, 4, 1, NULL, NULL),
	(5, 1, 'VIN INFO', 'custom-info', 0, 5, 1, NULL, NULL),
	(6, 1, 'ROW INFO', 'custom-lane', 5, 6, 1, NULL, NULL),
	(7, 2, 'RELLOCATE', 'custom-route', 1, 1, 1, NULL, NULL),
	(8, 2, 'ROW RELLOCATE', 'custom-allocate', 2, 1, 1, NULL, NULL),
	(9, 2, 'BACK TO PLANT', 'custom-factory', 3, 1, 1, NULL, NULL),
	(10, 3, 'TRUCK', 'custom-cargo-truck', 1, 1, 1, NULL, NULL),
	(11, 3, 'FACTORY', 'custom-factory', 2, 1, 1, NULL, NULL),
	(12, 3, 'SHIP', 'custom-cargo-ship', 3, 1, 0, NULL, NULL),
	(13, 3, 'TRAIN', 'custom-cargo-train', 4, 1, 1, NULL, NULL),
	(14, 3, 'IMPORTS', 'custom-import', 7, 1, 0, NULL, NULL),
	(15, 4, 'COLORES', 'custom-location', 1, 1, 1, NULL, NULL),
	(16, 4, 'P12', 'custom-location', 2, 1, 1, NULL, NULL),
	(17, 4, 'MALVINAS', 'custom-location', 3, 1, 1, NULL, NULL),
	(18, 4, 'CARPA', 'custom-location', 4, 1, 1, NULL, NULL),
	(19, 5, 'READ BARCODE OR QR', 'custom-scan', 1, 1, 1, NULL, NULL),
	(20, 5, 'SELECT POSITION', 'custom-input', 2, 1, 1, NULL, NULL),
	(21, 6, 'MANUAL LOAD', 'custom-input', 1, 1, 1, NULL, NULL),
	(22, 6, 'TRAIN LOAD', 'custom-cargo-train', 2, 1, 1, NULL, NULL),
	(23, 6, 'SHIP LOAD', 'custom-cargo-ship', 3, 1, 1, NULL, NULL);
/*!40000 ALTER TABLE `tiles` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
