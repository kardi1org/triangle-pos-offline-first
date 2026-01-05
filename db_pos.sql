/*
SQLyog Professional v12.09 (64 bit)
MySQL - 10.4.21-MariaDB : Database - db_pos
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_pos` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `db_pos`;

/*Table structure for table `adjusted_products` */

DROP TABLE IF EXISTS `adjusted_products`;

CREATE TABLE `adjusted_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adjusted_products_adjustment_id_foreign` (`adjustment_id`),
  CONSTRAINT `adjusted_products_adjustment_id_foreign` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjusted_products` */

/*Table structure for table `adjustments` */

DROP TABLE IF EXISTS `adjustments`;

CREATE TABLE `adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjustments` */

/*Table structure for table `budgets` */

DROP TABLE IF EXISTS `budgets`;

CREATE TABLE `budgets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `budgets` */

/*Table structure for table `cash_transactions` */

DROP TABLE IF EXISTS `cash_transactions`;

CREATE TABLE `cash_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('pemasukan','pengeluaran') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `cash_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cash_transactions` */

insert  into `cash_transactions`(`id`,`user_id`,`type`,`amount`,`category`,`note`,`transaction_date`,`created_at`,`updated_at`) values (1,1,'pemasukan','10000.00','Tambahan',NULL,'2025-12-23 13:18:31','2025-12-23 13:18:31','2025-12-23 13:18:31'),(2,1,'pengeluaran','2000.00','Parkir',NULL,'2025-12-23 13:18:53','2025-12-23 13:18:53','2025-12-23 13:18:53'),(3,1,'pemasukan','5000.00','Tambahan',NULL,'2025-12-23 16:15:37','2025-12-23 16:15:37','2025-12-23 16:15:37'),(4,1,'pengeluaran','2000.00','Parkir',NULL,'2025-12-23 16:15:51','2025-12-23 16:15:51','2025-12-23 16:15:51'),(5,1,'pemasukan','25000.00','Tambahan Receh','Tambahan Receh','2025-12-29 09:38:59','2025-12-29 09:38:59','2025-12-29 09:38:59'),(6,1,'pengeluaran','2000.00','Parkir',NULL,'2025-12-29 09:39:36','2025-12-29 09:39:36','2025-12-29 09:39:36'),(7,1,'pengeluaran','2000.00','Parkir',NULL,'2025-12-29 10:29:12','2025-12-29 10:29:12','2025-12-29 10:29:12'),(8,1,'pemasukan','10000.00','Tambahan',NULL,'2025-12-29 13:51:02','2025-12-29 13:51:02','2025-12-29 13:51:02'),(9,1,'pengeluaran','2000.00','Parkir',NULL,'2025-12-29 13:51:18','2025-12-29 13:51:18','2025-12-29 13:51:18'),(10,1,'pemasukan','20000.00','Tambahan',NULL,'2025-12-29 14:21:48','2025-12-29 14:21:48','2025-12-29 14:21:48'),(11,1,'pemasukan','20000.00','Tambahan',NULL,'2025-12-29 15:22:06','2025-12-29 15:22:06','2025-12-29 15:22:06'),(12,1,'pemasukan','25000.00','Tambahan',NULL,'2025-12-30 09:48:36','2025-12-30 09:48:36','2025-12-30 09:48:36'),(13,1,'pemasukan','5000.00','Tambahan',NULL,'2025-12-30 09:49:35','2025-12-30 09:49:35','2025-12-30 09:49:35'),(14,1,'pengeluaran','2000.00','Parkir',NULL,'2025-12-30 09:49:55','2025-12-30 09:49:55','2025-12-30 09:49:55');

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_category_code_unique` (`category_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`category_code`,`category_name`,`created_at`,`updated_at`) values (1,'CA_01','Random','2025-12-23 10:56:38','2025-12-23 10:56:38');

/*Table structure for table `currencies` */

DROP TABLE IF EXISTS `currencies`;

CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thousand_separator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decimal_separator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `currencies` */

insert  into `currencies`(`id`,`currency_name`,`code`,`symbol`,`thousand_separator`,`decimal_separator`,`exchange_rate`,`created_at`,`updated_at`) values (1,'US Dollar','USD','$',',','.',NULL,'2025-12-23 10:56:38','2025-12-23 10:56:38'),(2,'Rupiah','IDR','Rp',',','.',NULL,'2025-12-29 09:34:17','2025-12-29 09:34:17');

/*Table structure for table `customers` */

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customers` */

/*Table structure for table `expense_categories` */

DROP TABLE IF EXISTS `expense_categories`;

CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `expense_categories` */

/*Table structure for table `expenses` */

DROP TABLE IF EXISTS `expenses`;

CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_category_id_foreign` (`category_id`),
  CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `expenses` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `inventories` */

DROP TABLE IF EXISTS `inventories`;

CREATE TABLE `inventories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) NOT NULL DEFAULT 0,
  `discount_amount` int(11) NOT NULL DEFAULT 0,
  `shipping_amount` int(11) NOT NULL DEFAULT 0,
  `total_amount` int(11) DEFAULT NULL,
  `paid_amount` int(11) DEFAULT NULL,
  `due_amount` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inventories` */

/*Table structure for table `inventory_details` */

DROP TABLE IF EXISTS `inventory_details`;

CREATE TABLE `inventory_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Inventory_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `sub_total` int(11) DEFAULT NULL,
  `product_discount_amount` int(11) DEFAULT NULL,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inventory_details` */

/*Table structure for table `media` */

DROP TABLE IF EXISTS `media`;

CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `media` */

insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (1,'Modules\\Product\\Entities\\Product',1,'338000c1-3e32-461a-80c6-542c0853f9c3','images','1766469025','1766469025.jpg','image/jpeg','public','public',24681,'[]','[]','{\"thumb\":true}','[]',1,'2025-12-23 11:50:27','2025-12-23 11:50:27');

/*Table structure for table `mejas` */

DROP TABLE IF EXISTS `mejas`;

CREATE TABLE `mejas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `no_meja` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_pax` int(11) DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `mejas` */

insert  into `mejas`(`id`,`no_meja`,`name`,`qty_pax`,`location`,`shape`,`status`,`created_at`,`updated_at`) values (1,1,'Tabel 1',4,'Indoor','Square',0,'2025-12-23 12:07:57','2025-12-23 12:07:57');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2021_07_14_145038_create_categories_table',1),(5,'2021_07_14_145047_create_products_table',1),(6,'2021_07_15_211319_create_media_table',1),(7,'2021_07_16_010005_create_uploads_table',1),(8,'2021_07_16_220524_create_permission_tables',1),(9,'2021_07_22_003941_create_adjustments_table',1),(10,'2021_07_22_004043_create_adjusted_products_table',1),(11,'2021_07_28_192608_create_expense_categories_table',1),(12,'2021_07_28_192616_create_expenses_table',1),(13,'2021_07_29_165419_create_customers_table',1),(14,'2021_07_29_165440_create_suppliers_table',1),(15,'2021_07_31_015923_create_currencies_table',1),(16,'2021_07_31_140531_create_settings_table',1),(17,'2021_07_31_201003_create_sales_table',1),(18,'2021_07_31_212446_create_sale_details_table',1),(19,'2021_08_07_192203_create_sale_payments_table',1),(20,'2021_08_08_021108_create_purchases_table',1),(21,'2021_08_08_021131_create_purchase_payments_table',1),(22,'2021_08_08_021713_create_purchase_details_table',1),(23,'2021_08_08_175345_create_sale_returns_table',1),(24,'2021_08_08_175358_create_sale_return_details_table',1),(25,'2021_08_08_175406_create_sale_return_payments_table',1),(26,'2021_08_08_222603_create_purchase_returns_table',1),(27,'2021_08_08_222612_create_purchase_return_details_table',1),(28,'2021_08_08_222646_create_purchase_return_payments_table',1),(29,'2021_08_16_015031_create_quotations_table',1),(30,'2021_08_16_155013_create_quotation_details_table',1),(31,'2023_07_01_184221_create_units_table',1),(32,'2025_05_14_143449_create_sliders_table',1),(33,'2025_11_05_095742_create_mejas_table',1),(34,'2025_11_05_123742_add_order_type_and_table_to_sales_table',1),(35,'2025_11_11_102815_create_variants_table',1),(36,'2025_11_26_091502_add_variant_detail_to_sale_details_table',1),(37,'2025_12_05_092208_add_selected_table_ids_to_sales_table',1),(38,'2025_12_10_090223_create_budgets_table',1),(39,'2025_12_10_091239_create_inventories_table',1),(40,'2025_12_10_091404_create_inventory_details_table',1),(41,'2025_12_10_093211_create_payments_table',1),(42,'2025_12_10_094025_update_payments_table_structure',1),(43,'2025_12_10_094339_create_payments_table_revised',1),(44,'2025_12_22_094522_change_password_columns_to_text',1),(45,'2025_12_22_105919_add_is_shift_to_settings_table',1),(46,'2025_12_23_082106_create_shifts_table',1),(47,'2025_12_23_092119_add_id_to_payments_table',1),(48,'2025_12_23_125827_create_cash_transactions_table',2),(49,'2025_12_23_155454_add_user_id_to_sales_table',3);

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_permissions` */

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_roles` */

insert  into `model_has_roles`(`role_id`,`model_type`,`model_id`) values (2,'App\\Models\\User',1);

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `code` int(11) NOT NULL,
  `Cash` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `DebitCard` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `Gopay` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `CreditCard` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `OVO` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `ShopeePay` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `Kredivo` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `Dana` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `GrabPay` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `QRIS` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payments` */

insert  into `payments`(`code`,`Cash`,`DebitCard`,`Gopay`,`CreditCard`,`OVO`,`ShopeePay`,`Kredivo`,`Dana`,`GrabPay`,`QRIS`,`id`) values (1,'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y',0);

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (1,'edit_own_profile','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(2,'access_user_management','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(3,'show_total_stats','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(4,'show_month_overview','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(5,'show_weekly_sales_purchases','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(6,'show_monthly_cashflow','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(7,'show_notifications','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(8,'access_products','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(9,'create_products','web','2025-12-23 10:56:26','2025-12-23 10:56:26'),(10,'show_products','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(11,'edit_products','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(12,'delete_products','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(13,'access_product_categories','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(14,'print_barcodes','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(15,'access_adjustments','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(16,'create_adjustments','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(17,'show_adjustments','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(18,'edit_adjustments','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(19,'delete_adjustments','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(20,'access_quotations','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(21,'create_quotations','web','2025-12-23 10:56:27','2025-12-23 10:56:27'),(22,'show_quotations','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(23,'edit_quotations','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(24,'delete_quotations','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(25,'create_quotation_sales','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(26,'send_quotation_mails','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(27,'access_budgets','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(28,'create_budgets','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(29,'edit_budgets','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(30,'delete_budgets','web','2025-12-23 10:56:28','2025-12-23 10:56:28'),(31,'access_expenses','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(32,'create_expenses','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(33,'edit_expenses','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(34,'delete_expenses','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(35,'access_expense_categories','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(36,'access_customers','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(37,'create_customers','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(38,'show_customers','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(39,'edit_customers','web','2025-12-23 10:56:29','2025-12-23 10:56:29'),(40,'delete_customers','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(41,'access_suppliers','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(42,'create_suppliers','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(43,'show_suppliers','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(44,'edit_suppliers','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(45,'delete_suppliers','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(46,'access_sales','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(47,'create_sales','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(48,'show_sales','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(49,'edit_sales','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(50,'delete_sales','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(51,'create_pos_sales','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(52,'access_sale_payments','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(53,'access_sale_returns','web','2025-12-23 10:56:30','2025-12-23 10:56:30'),(54,'create_sale_returns','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(55,'show_sale_returns','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(56,'edit_sale_returns','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(57,'delete_sale_returns','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(58,'access_sale_return_payments','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(59,'access_purchases','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(60,'create_purchases','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(61,'show_purchases','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(62,'edit_purchases','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(63,'delete_purchases','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(64,'access_purchase_payments','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(65,'access_purchase_returns','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(66,'create_purchase_returns','web','2025-12-23 10:56:31','2025-12-23 10:56:31'),(67,'show_purchase_returns','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(68,'edit_purchase_returns','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(69,'delete_purchase_returns','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(70,'access_purchase_return_payments','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(71,'access_reports','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(72,'access_currencies','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(73,'create_currencies','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(74,'edit_currencies','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(75,'delete_currencies','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(76,'access_settings','web','2025-12-23 10:56:32','2025-12-23 10:56:32'),(77,'access_units','web','2025-12-23 10:56:32','2025-12-23 10:56:32');

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_barcode_symbology` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_cost` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_stock_alert` int(11) NOT NULL,
  `product_order_tax` int(11) DEFAULT NULL,
  `product_tax_type` tinyint(4) DEFAULT NULL,
  `product_note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (1,1,'Siomay','1000','EAN13',100,1000000,1200000,'PC',0,NULL,NULL,NULL,'2025-12-23 11:50:27','2025-12-23 11:50:27');

/*Table structure for table `purchase_details` */

DROP TABLE IF EXISTS `purchase_details`;

CREATE TABLE `purchase_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL,
  `product_discount_amount` int(11) NOT NULL,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_details_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_details_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_details` */

/*Table structure for table `purchase_payments` */

DROP TABLE IF EXISTS `purchase_payments`;

CREATE TABLE `purchase_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_payments_purchase_id_foreign` (`purchase_id`),
  CONSTRAINT `purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_payments` */

/*Table structure for table `purchase_return_details` */

DROP TABLE IF EXISTS `purchase_return_details`;

CREATE TABLE `purchase_return_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL,
  `product_discount_amount` int(11) NOT NULL,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_details_purchase_return_id_foreign` (`purchase_return_id`),
  KEY `purchase_return_details_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_return_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_return_details_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_return_details` */

/*Table structure for table `purchase_return_payments` */

DROP TABLE IF EXISTS `purchase_return_payments`;

CREATE TABLE `purchase_return_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint(20) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_payments_purchase_return_id_foreign` (`purchase_return_id`),
  CONSTRAINT `purchase_return_payments_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_return_payments` */

/*Table structure for table `purchase_returns` */

DROP TABLE IF EXISTS `purchase_returns`;

CREATE TABLE `purchase_returns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) NOT NULL DEFAULT 0,
  `discount_amount` int(11) NOT NULL DEFAULT 0,
  `shipping_amount` int(11) NOT NULL DEFAULT 0,
  `total_amount` int(11) NOT NULL,
  `paid_amount` int(11) NOT NULL,
  `due_amount` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_returns_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchase_returns` */

/*Table structure for table `purchases` */

DROP TABLE IF EXISTS `purchases`;

CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) NOT NULL DEFAULT 0,
  `discount_amount` int(11) NOT NULL DEFAULT 0,
  `shipping_amount` int(11) NOT NULL DEFAULT 0,
  `total_amount` int(11) NOT NULL,
  `paid_amount` int(11) NOT NULL,
  `due_amount` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `purchases` */

/*Table structure for table `quotation_details` */

DROP TABLE IF EXISTS `quotation_details`;

CREATE TABLE `quotation_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL,
  `product_discount_amount` int(11) NOT NULL,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_details_quotation_id_foreign` (`quotation_id`),
  KEY `quotation_details_product_id_foreign` (`product_id`),
  CONSTRAINT `quotation_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotation_details_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `quotation_details` */

/*Table structure for table `quotations` */

DROP TABLE IF EXISTS `quotations`;

CREATE TABLE `quotations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) NOT NULL DEFAULT 0,
  `discount_amount` int(11) NOT NULL DEFAULT 0,
  `shipping_amount` int(11) NOT NULL DEFAULT 0,
  `total_amount` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotations_customer_id_foreign` (`customer_id`),
  CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `quotations` */

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

insert  into `role_has_permissions`(`permission_id`,`role_id`) values (1,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1);

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (1,'Admin','web','2025-12-23 10:56:33','2025-12-23 10:56:33'),(2,'Super Admin','web','2025-12-23 10:56:38','2025-12-23 10:56:38');

/*Table structure for table `sale_details` */

DROP TABLE IF EXISTS `sale_details`;

CREATE TABLE `sale_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL,
  `product_discount_amount` int(11) DEFAULT NULL,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) DEFAULT NULL,
  `variant_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_details_sale_id_foreign` (`sale_id`),
  KEY `sale_details_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_details` */

insert  into `sale_details`(`id`,`sale_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`product_discount_amount`,`product_discount_type`,`product_tax_amount`,`variant_detail`,`created_at`,`updated_at`) values (1,1,'SL-00001',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-23 11:50:49','2025-12-23 11:50:49'),(3,3,'SL-00003',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-23 12:36:01','2025-12-23 12:36:01'),(4,4,'SL-00004',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-23 13:03:19','2025-12-23 13:03:19'),(7,5,'SL-00005',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-23 16:08:14','2025-12-23 16:08:14'),(9,7,'SL-00007',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-23 16:09:57','2025-12-23 16:09:57'),(10,8,'SL-00008',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-29 09:31:06','2025-12-29 09:31:06'),(11,9,'SL-00009',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-29 09:35:11','2025-12-29 09:35:11'),(12,2,'SL-00002',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[{\"index\":1,\"variant\":\"Pedas, Manis\",\"typeOrder\":\"take_out\"}]','2025-12-29 14:05:52','2025-12-29 14:05:52'),(13,6,'SL-00006',1,'Siomay','1000',2,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-30 09:57:46','2025-12-30 09:57:46'),(14,10,'SL-00010',1,'Siomay','1000',1,1200000,1200000,1200000,NULL,'fixed',NULL,'[]','2025-12-30 10:14:18','2025-12-30 10:14:18');

/*Table structure for table `sale_payments` */

DROP TABLE IF EXISTS `sale_payments`;

CREATE TABLE `sale_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `cashpay` int(11) DEFAULT NULL,
  `debitcard` int(11) DEFAULT NULL,
  `creditcard` int(11) DEFAULT NULL,
  `gopay` int(11) DEFAULT NULL,
  `grabpay` int(11) DEFAULT NULL,
  `ovopay` int(11) DEFAULT NULL,
  `shopeepay` int(11) DEFAULT NULL,
  `danapay` int(11) DEFAULT NULL,
  `kredivopay` int(11) DEFAULT NULL,
  `qrispay` int(11) DEFAULT NULL,
  `change` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_payments_sale_id_foreign` (`sale_id`),
  CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_payments` */

insert  into `sale_payments`(`id`,`sale_id`,`date`,`reference`,`payment_method`,`note`,`amount`,`cashpay`,`debitcard`,`creditcard`,`gopay`,`grabpay`,`ovopay`,`shopeepay`,`danapay`,`kredivopay`,`qrispay`,`change`,`created_at`,`updated_at`) values (1,1,'2025-12-23','INV/SL-00001',NULL,NULL,1200000,12000,0,0,0,0,0,0,0,0,0,0,'2025-12-23 11:50:49','2025-12-23 11:50:49'),(2,3,'2025-12-23','INV/SL-00003',NULL,NULL,1200000,12000,0,0,0,0,0,0,0,0,0,0,'2025-12-23 12:36:01','2025-12-23 12:36:01'),(3,4,'2025-12-23','INV/SL-00004',NULL,NULL,1200000,12000,0,0,0,0,0,0,0,0,0,0,'2025-12-23 13:03:19','2025-12-23 13:03:19'),(4,7,'2025-12-23','INV/SL-00007',NULL,NULL,1200000,12000,0,0,0,0,0,0,0,0,0,0,'2025-12-23 16:09:57','2025-12-23 16:09:57'),(5,8,'2025-12-29','INV/SL-00008',NULL,NULL,1200000,12000,0,0,0,0,0,0,0,0,0,0,'2025-12-29 09:31:07','2025-12-29 09:31:07'),(6,9,'2025-12-29','INV/SL-00009',NULL,NULL,1500000,15000,0,0,0,0,0,0,0,0,0,3000,'2025-12-29 09:35:11','2025-12-29 09:35:11'),(7,2,'2025-12-29','INV/SL-00002',NULL,NULL,1200000,12000,0,0,0,0,0,0,0,0,0,0,'2025-12-29 14:05:52','2025-12-29 14:05:52'),(8,6,'2025-12-30','INV/SL-00006',NULL,NULL,3000000,30000,0,0,0,0,0,0,0,0,0,6000,'2025-12-30 09:57:46','2025-12-30 09:57:46'),(9,10,'2025-12-30','INV/SL-00010',NULL,NULL,2000000,20000,0,0,0,0,0,0,0,0,0,8000,'2025-12-30 10:14:18','2025-12-30 10:14:18');

/*Table structure for table `sale_return_details` */

DROP TABLE IF EXISTS `sale_return_details`;

CREATE TABLE `sale_return_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_return_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL,
  `product_discount_amount` int(11) NOT NULL,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_return_details_sale_return_id_foreign` (`sale_return_id`),
  KEY `sale_return_details_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_return_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_return_details_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_return_details` */

/*Table structure for table `sale_return_payments` */

DROP TABLE IF EXISTS `sale_return_payments`;

CREATE TABLE `sale_return_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_return_id` bigint(20) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_return_payments_sale_return_id_foreign` (`sale_return_id`),
  CONSTRAINT `sale_return_payments_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_return_payments` */

/*Table structure for table `sale_returns` */

DROP TABLE IF EXISTS `sale_returns`;

CREATE TABLE `sale_returns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) NOT NULL DEFAULT 0,
  `discount_amount` int(11) NOT NULL DEFAULT 0,
  `shipping_amount` int(11) NOT NULL DEFAULT 0,
  `total_amount` int(11) NOT NULL,
  `paid_amount` int(11) NOT NULL,
  `due_amount` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_returns_customer_id_foreign` (`customer_id`),
  CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_returns` */

/*Table structure for table `sales` */

DROP TABLE IF EXISTS `sales`;

CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `selected_table_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dine_in',
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) DEFAULT NULL,
  `discount_amount` int(11) DEFAULT NULL,
  `shipping_amount` int(11) DEFAULT NULL,
  `total_amount` int(11) DEFAULT NULL,
  `paid_amount` int(11) DEFAULT NULL,
  `due_amount` int(11) DEFAULT NULL,
  `change` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_table_id_foreign` (`table_id`),
  KEY `sales_user_id_foreign` (`user_id`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `mejas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales` */

insert  into `sales`(`id`,`user_id`,`selected_table_ids`,`date`,`reference`,`customer_id`,`customer_name`,`order_type`,`table_id`,`tax_percentage`,`tax_amount`,`discount_percentage`,`discount_amount`,`shipping_amount`,`total_amount`,`paid_amount`,`due_amount`,`change`,`status`,`payment_status`,`payment_method`,`note`,`created_at`,`updated_at`) values (1,1,'[]','2025-12-23','SL-00001',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,1200000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-23 11:50:49','2025-12-23 11:50:49'),(2,1,'[]','2025-12-29','SL-00002',NULL,'Guest','dine_in',NULL,0,0,0,0,0,1200000,1200000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-23 12:06:56','2025-12-29 14:05:52'),(3,1,'[]','2025-12-23','SL-00003',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,1200000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-23 12:36:01','2025-12-23 12:36:01'),(4,1,'[]','2025-12-23','SL-00004',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,1200000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-23 13:03:19','2025-12-23 13:03:19'),(5,1,'\"[]\"','2025-12-23','SL-00005',NULL,'Guest','dine_in',NULL,0,0,0,0,0,1200000,NULL,NULL,NULL,'Pending','Unpaid',NULL,NULL,'2025-12-23 16:02:31','2025-12-23 16:02:31'),(6,1,'[]','2025-12-30','SL-00006',NULL,'Guest','dine_in',NULL,0,0,0,0,0,2400000,3000000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-23 16:08:27','2025-12-30 09:57:46'),(7,1,'[]','2025-12-23','SL-00007',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,1200000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-23 16:09:57','2025-12-23 16:09:57'),(8,1,'[]','2025-12-29','SL-00008',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,1200000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-29 09:31:06','2025-12-29 09:31:06'),(9,1,'[]','2025-12-29','SL-00009',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,1500000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-29 09:35:11','2025-12-29 09:35:11'),(10,1,'[]','2025-12-30','SL-00010',NULL,NULL,'dine_in',NULL,0,0,0,0,0,1200000,2000000,NULL,NULL,'Completed','Paid',NULL,NULL,'2025-12-30 10:14:18','2025-12-30 10:14:18');

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_currency_id` int(11) NOT NULL,
  `default_currency_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tidak aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `settings` */

insert  into `settings`(`id`,`company_name`,`company_email`,`company_phone`,`site_logo`,`default_currency_id`,`default_currency_position`,`notification_email`,`footer_text`,`company_address`,`is_shift`,`created_at`,`updated_at`) values (1,'Data Prima POS','company@test.com','012345678901',NULL,2,'prefix','notification@test.com','Data Prima Pos © 2025 || Developed by <strong><a target=\"_blank\" href=\"https://google.com\">Data Prima</a></strong>','Jakarta, Indonesia','aktif','2025-12-23 10:56:38','2025-12-30 07:27:12');

/*Table structure for table `shifts` */

DROP TABLE IF EXISTS `shifts`;

CREATE TABLE `shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `open_time` datetime NOT NULL,
  `close_time` datetime DEFAULT NULL,
  `starting_cash` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ending_cash` decimal(15,2) DEFAULT NULL,
  `expected_ending_cash` decimal(15,2) DEFAULT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shifts_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `shifts` */

insert  into `shifts`(`id`,`user_id`,`open_time`,`close_time`,`starting_cash`,`ending_cash`,`expected_ending_cash`,`status`,`note`,`created_at`,`updated_at`) values (1,1,'2025-12-23 12:10:37','2025-12-23 12:33:52','100000.00','100000.00','100000.00','closed',' (Selisih: 0.00)','2025-12-23 12:10:37','2025-12-23 12:33:52'),(2,1,'2025-12-23 12:34:55','2025-12-23 12:36:37','100000.00','100000.00','112000.00','closed',' (Selisih: -12,000.00)','2025-12-23 12:34:55','2025-12-23 12:36:37'),(3,1,'2025-12-23 12:39:10','2025-12-23 15:24:58','150000.00','170000.00','170000.00','closed','Penjualan: 12000, Masuk: 10000.00, Keluar: 2000.00. ','2025-12-23 12:39:10','2025-12-23 15:24:58'),(4,1,'2025-12-23 15:26:10','2025-12-23 16:02:08','50000.00','50000.00','50000.00','closed','Penjualan: 0, Masuk: 0, Keluar: 0. ','2025-12-23 15:26:10','2025-12-23 16:02:08'),(5,1,'2025-12-23 16:02:16','2025-12-24 07:55:01','50000.00','65000.00','65000.00','closed','Penjualan: 12000, Masuk: 5000.00, Keluar: 2000.00. ','2025-12-23 16:02:16','2025-12-24 07:55:01'),(6,1,'2025-12-24 08:57:49','2025-12-29 07:52:19','200000.00','20000.00','200000.00','closed','Penjualan: 0, Masuk: 0, Keluar: 0. test','2025-12-24 08:57:49','2025-12-29 07:52:19'),(7,1,'2025-12-29 09:30:29','2025-12-29 10:10:08','0.00','40000.00','47000.00','closed','Penjualan: 24000, Masuk: 25000.00, Keluar: 2000.00. ','2025-12-29 09:30:29','2025-12-29 10:10:08'),(8,1,'2025-12-29 10:13:13','2025-12-29 10:15:26','40000.00','35000.00','40000.00','closed','Penjualan: 0, Masuk: 0, Keluar: 0. Salah kembalian','2025-12-29 10:13:13','2025-12-29 10:15:26'),(9,1,'2025-12-29 10:15:59','2025-12-29 10:37:45','40000.00','38000.00','38000.00','closed','Penjualan: 0, Masuk: 0, Keluar: 2000.00. ','2025-12-29 10:15:59','2025-12-29 10:37:45'),(10,1,'2025-12-29 10:52:14','2025-12-29 13:48:14','30000.00','30000.00','30000.00','closed','Penjualan: 0, Masuk: 0, Keluar: 0. ','2025-12-29 10:52:14','2025-12-29 13:48:14'),(11,1,'2025-12-29 13:48:31','2025-12-29 13:52:21','50000.00','58000.00','58000.00','closed','Penjualan: 0, Masuk: 10000.00, Keluar: 2000.00. ','2025-12-29 13:48:31','2025-12-29 13:52:21'),(12,1,'2025-12-29 13:59:08','2025-12-29 14:14:29','30000.00','42000.00','42000.00','closed','Penjualan: 12000, Masuk: 0, Keluar: 0. ','2025-12-29 13:59:08','2025-12-29 14:14:29'),(13,1,'2025-12-29 14:21:27','2025-12-29 14:22:07','45000.00','65000.00','65000.00','closed','Penjualan: 0, Masuk: 20000.00, Keluar: 0. ','2025-12-29 14:21:27','2025-12-29 14:22:07'),(14,1,'2025-12-29 15:21:50','2025-12-30 08:00:41','50000.00','70000.00','70000.00','closed','Penjualan: 0, Masuk: 20000.00, Keluar: 0. ','2025-12-29 15:21:50','2025-12-30 08:00:41'),(15,1,'2025-12-30 09:46:18','2025-12-30 10:56:41','35000.00','99000.00','99000.00','closed','Penjualan: 36000, Masuk: 30000.00, Keluar: 2000.00. ','2025-12-30 09:46:18','2025-12-30 10:56:41'),(16,1,'2025-12-30 10:57:33','2025-12-30 12:52:37','20000.00','20000.00','20000.00','closed','Penjualan: 0, Masuk: 0, Keluar: 0. ','2025-12-30 10:57:33','2025-12-30 12:52:37'),(17,1,'2025-12-30 12:54:42',NULL,'60000.00',NULL,NULL,'open',NULL,'2025-12-30 12:54:42','2025-12-30 12:54:42');

/*Table structure for table `sliders` */

DROP TABLE IF EXISTS `sliders`;

CREATE TABLE `sliders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sliders` */

/*Table structure for table `suppliers` */

DROP TABLE IF EXISTS `suppliers`;

CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `suppliers` */

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operator` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_value` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `units` */

insert  into `units`(`id`,`name`,`short_name`,`operator`,`operation_value`,`created_at`,`updated_at`) values (1,'Piece','PC','*',1,'2025-12-23 10:56:38','2025-12-23 10:56:38');

/*Table structure for table `uploads` */

DROP TABLE IF EXISTS `uploads`;

CREATE TABLE `uploads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `uploads` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_database` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_host` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_port` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenant_password` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valid_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`is_active`,`remember_token`,`tenant_database`,`tenant_host`,`tenant_port`,`tenant_username`,`tenant_password`,`valid_date`,`created_at`,`updated_at`) values (1,'Administrator','super.admin@test.com',NULL,'$2y$10$JXQrHdIXBC1swx5lAsTugOVAK7UsSd51gE0GxU4JH/jaVCLFSRgyK',1,NULL,'db_pos','127.0.0.1','3306','root',NULL,'2099-12-28','2025-12-23 10:56:38','2025-12-23 10:56:38');

/*Table structure for table `variants` */

DROP TABLE IF EXISTS `variants`;

CREATE TABLE `variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `variant_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variants_product_id_foreign` (`product_id`),
  CONSTRAINT `variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `variants` */

insert  into `variants`(`id`,`product_id`,`variant_name`,`created_at`,`updated_at`) values (1,1,'Pedas','2025-12-23 12:06:21','2025-12-23 12:06:21'),(2,1,'Manis','2025-12-23 12:06:21','2025-12-23 12:06:21'),(3,1,'Asin','2025-12-24 10:23:42','2025-12-24 10:23:42');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
