/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.4.11-MariaDB : Database - triangle_pos
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `triangle-pos-new`;

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
  PRIMARY KEY (`id`)/*,
  KEY `adjusted_products_adjustment_id_foreign` (`adjustment_id`),
  CONSTRAINT `adjusted_products_adjustment_id_foreign` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE*/
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `adjusted_products` */

/*Table structure for table `adjustments` */

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

/*Table structure for table `categories` */

CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_category_code_unique` (`category_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`category_code`,`category_name`,`created_at`,`updated_at`) values (1,'CA_01','Food','2025-03-04 09:03:09','2025-03-04 14:20:17');
insert  into `categories`(`id`,`category_code`,`category_name`,`created_at`,`updated_at`) values (2,'CA_02','Beverage','2025-03-04 14:21:31','2025-03-04 14:21:31');
insert  into `categories`(`id`,`category_code`,`category_name`,`created_at`,`updated_at`) values (3,'CA_03','Snack','2025-03-19 09:27:24','2025-03-19 09:27:24');

/*Table structure for table `currencies` */

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

insert  into `currencies`(`id`,`currency_name`,`code`,`symbol`,`thousand_separator`,`decimal_separator`,`exchange_rate`,`created_at`,`updated_at`) values (1,'US Dollar','USD','$',',','.',NULL,'2025-03-04 09:03:09','2025-03-04 09:03:09');
insert  into `currencies`(`id`,`currency_name`,`code`,`symbol`,`thousand_separator`,`decimal_separator`,`exchange_rate`,`created_at`,`updated_at`) values (2,'IDR Rupiah','RP','Rp','.',',',NULL,'2025-03-04 15:15:37','2025-03-04 15:15:37');

/*Table structure for table `customers` */

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `customers` */

insert  into `customers`(`id`,`customer_name`,`customer_email`,`customer_phone`,`city`,`country`,`address`,`created_at`,`updated_at`) values (1,'BUDI','budi@gmail.com','0813','Sunter','DKI JAKARTA','Jl. Sunter','2025-04-30 09:29:26','2025-04-30 09:29:26');

/*Table structure for table `expense_categories` */

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

/*Table structure for table `media` */

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `media` */

insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (3,'Modules\\Product\\Entities\\Product',2,'4016e3a4-2f0e-4f0a-aac6-b2a85882c7be','images','1741076981','1741076981.png','image/png','public','public',93769,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 14:30:36','2025-03-04 14:30:36');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (4,'Modules\\Product\\Entities\\Product',3,'f55396ed-3807-4c62-85f1-ca127c49b00c','images','1741077454','1741077454.png','image/jpeg','public','public',59424,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 14:37:40','2025-03-04 14:37:40');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (5,'Modules\\Product\\Entities\\Product',5,'14cefebd-99e7-4c3d-bd07-d2521bd115c7','images','1741083953','1741083953.png','image/png','public','public',88082,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 16:26:05','2025-03-04 16:26:05');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (6,'Modules\\Product\\Entities\\Product',6,'1209fb7a-5911-4a2e-a6ab-bae40e7bd6e1','images','1741084318','1741084318.png','image/png','public','public',101248,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 16:32:03','2025-03-04 16:32:04');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (7,'Modules\\Product\\Entities\\Product',4,'539f575c-3faa-48d6-bec8-7339ac84119e','images','1741084530','1741084530.png','image/png','public','public',517494,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 16:35:40','2025-03-04 16:35:40');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (8,'Modules\\Product\\Entities\\Product',7,'01b399a7-160a-4c1d-a353-12e636e59229','images','1741085012','1741085012.png','image/jpeg','public','public',4775,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 16:43:37','2025-03-04 16:43:37');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (9,'Modules\\Product\\Entities\\Product',8,'ddd62972-0562-4069-b45e-31de06a560fb','images','1741085299','1741085299.png','image/png','public','public',712198,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-04 16:48:26','2025-03-04 16:48:27');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (14,'Modules\\Product\\Entities\\Product',1,'938d43ed-35ed-4dba-9f1a-334cecb82797','images','1741574180','1741574180.png','image/png','public','public',64235,'[]','[]','{\"thumb\":true}','[]',1,'2025-03-10 08:36:35','2025-03-10 08:36:37');
insert  into `media`(`id`,`model_type`,`model_id`,`uuid`,`collection_name`,`name`,`file_name`,`mime_type`,`disk`,`conversions_disk`,`size`,`manipulations`,`custom_properties`,`generated_conversions`,`responsive_images`,`order_column`,`created_at`,`updated_at`) values (19,'Modules\\Product\\Entities\\Product',9,'9ca17b1c-0054-41ab-93e4-6ca33af8542c','images','1745985690','1745985690.jpg','image/jpeg','public','public',11983,'[]','[]','{\"thumb\":true}','[]',1,'2025-04-30 10:01:35','2025-04-30 10:01:36');

/*Table structure for table `migrations` */

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values (1,'2014_10_12_000000_create_users_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (2,'2014_10_12_100000_create_password_resets_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (3,'2019_08_19_000000_create_failed_jobs_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (4,'2021_07_14_145038_create_categories_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (5,'2021_07_14_145047_create_products_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (6,'2021_07_15_211319_create_media_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (7,'2021_07_16_010005_create_uploads_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (8,'2021_07_16_220524_create_permission_tables',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (9,'2021_07_22_003941_create_adjustments_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (10,'2021_07_22_004043_create_adjusted_products_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (11,'2021_07_28_192608_create_expense_categories_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (12,'2021_07_28_192616_create_expenses_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (13,'2021_07_29_165419_create_customers_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (14,'2021_07_29_165440_create_suppliers_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (15,'2021_07_31_015923_create_currencies_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (16,'2021_07_31_140531_create_settings_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (17,'2021_07_31_201003_create_sales_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (18,'2021_07_31_212446_create_sale_details_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (19,'2021_08_07_192203_create_sale_payments_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (20,'2021_08_08_021108_create_purchases_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (21,'2021_08_08_021131_create_purchase_payments_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (22,'2021_08_08_021713_create_purchase_details_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (23,'2021_08_08_175345_create_sale_returns_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (24,'2021_08_08_175358_create_sale_return_details_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (25,'2021_08_08_175406_create_sale_return_payments_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (26,'2021_08_08_222603_create_purchase_returns_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (27,'2021_08_08_222612_create_purchase_return_details_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (28,'2021_08_08_222646_create_purchase_return_payments_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (29,'2021_08_16_015031_create_quotations_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (30,'2021_08_16_155013_create_quotation_details_table',1);
insert  into `migrations`(`id`,`migration`,`batch`) values (31,'2023_07_01_184221_create_units_table',1);

/*Table structure for table `model_has_permissions` */

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

/*Table structure for table `order_details` */

CREATE TABLE `order_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `price` int(11) DEFAULT 0,
  `unit_price` int(11) DEFAULT 0,
  `sub_total` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `order_details` */

insert  into `order_details`(`id`,`order_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`created_at`,`updated_at`) values (46,56,'DO/202506/0001',2,'Martabak Asin','10002',1,30000,30000,30000,'2025-06-25 14:02:52','2025-06-25 14:02:52');
insert  into `order_details`(`id`,`order_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`created_at`,`updated_at`) values (47,56,'DO/202506/0001',4,'Dimsum-Original','10004',4,3000,3000,12000,'2025-06-25 14:02:52','2025-06-25 14:02:52');
insert  into `order_details`(`id`,`order_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`created_at`,`updated_at`) values (48,56,'DO/202506/0001',7,'Juice Jeruk','10007',1,13000,13000,13000,'2025-06-25 14:02:52','2025-06-25 14:02:52');
insert  into `order_details`(`id`,`order_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`created_at`,`updated_at`) values (49,57,'DO/202506/0002',6,'Pempek Palembang','10006',3,9000,9000,27000,'2025-06-25 14:09:51','2025-06-25 14:09:51');
insert  into `order_details`(`id`,`order_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`created_at`,`updated_at`) values (50,57,'DO/202506/0002',9,'Tahu Sumedang','10009',8,500,500,4000,'2025-06-25 14:09:51','2025-06-25 14:09:51');
insert  into `order_details`(`id`,`order_id`,`reference`,`product_id`,`product_name`,`product_code`,`quantity`,`price`,`unit_price`,`sub_total`,`created_at`,`updated_at`) values (51,57,'DO/202506/0002',3,'Juice Alpukat','10003',1,17000,17000,17000,'2025-06-25 14:09:51','2025-06-25 14:09:51');

/*Table structure for table `orders` */

CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` int(11) DEFAULT 0,
  `paid_amount` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `orders` */

insert  into `orders`(`id`,`date`,`reference`,`customer_id`,`customer_name`,`total_amount`,`paid_amount`,`created_at`,`updated_at`) values (56,'2025-06-25','DO/202506/0001',NULL,'JANUARI',55000,0,'2025-06-25 14:02:52','2025-06-25 14:02:52');
insert  into `orders`(`id`,`date`,`reference`,`customer_id`,`customer_name`,`total_amount`,`paid_amount`,`created_at`,`updated_at`) values (57,'2025-06-25','DO/202506/0002',NULL,'FEBRUARI',48000,0,'2025-06-25 14:09:51','2025-06-25 14:09:51');

/*Table structure for table `password_resets` */

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `permissions` */

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (1,'edit_own_profile','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (2,'access_user_management','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (3,'show_total_stats','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (4,'show_month_overview','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (5,'show_weekly_sales_purchases','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (6,'show_monthly_cashflow','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (7,'show_notifications','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (8,'access_products','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (9,'create_products','web','2025-03-04 09:02:59','2025-03-04 09:02:59');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (10,'show_products','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (11,'edit_products','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (12,'delete_products','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (13,'access_product_categories','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (14,'print_barcodes','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (15,'access_adjustments','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (16,'create_adjustments','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (17,'show_adjustments','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (18,'edit_adjustments','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (19,'delete_adjustments','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (20,'access_quotations','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (21,'create_quotations','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (22,'show_quotations','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (23,'edit_quotations','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (24,'delete_quotations','web','2025-03-04 09:03:00','2025-03-04 09:03:00');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (25,'create_quotation_sales','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (26,'send_quotation_mails','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (27,'access_expenses','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (28,'create_expenses','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (29,'edit_expenses','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (30,'delete_expenses','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (31,'access_expense_categories','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (32,'access_customers','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (33,'create_customers','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (34,'show_customers','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (35,'edit_customers','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (36,'delete_customers','web','2025-03-04 09:03:01','2025-03-04 09:03:01');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (37,'access_suppliers','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (38,'create_suppliers','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (39,'show_suppliers','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (40,'edit_suppliers','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (41,'delete_suppliers','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (42,'access_sales','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (43,'create_sales','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (44,'show_sales','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (45,'edit_sales','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (46,'delete_sales','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (47,'create_pos_sales','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (48,'access_sale_payments','web','2025-03-04 09:03:02','2025-03-04 09:03:02');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (49,'access_sale_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (50,'create_sale_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (51,'show_sale_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (52,'edit_sale_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (53,'delete_sale_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (54,'access_sale_return_payments','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (55,'access_purchases','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (56,'create_purchases','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (57,'show_purchases','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (58,'edit_purchases','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (59,'delete_purchases','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (60,'access_purchase_payments','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (61,'access_purchase_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (62,'create_purchase_returns','web','2025-03-04 09:03:03','2025-03-04 09:03:03');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (63,'show_purchase_returns','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (64,'edit_purchase_returns','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (65,'delete_purchase_returns','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (66,'access_purchase_return_payments','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (67,'access_reports','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (68,'access_currencies','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (69,'create_currencies','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (70,'edit_currencies','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (71,'delete_currencies','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (72,'access_settings','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (73,'access_units','web','2025-03-04 09:03:04','2025-03-04 09:03:04');

/*Table structure for table `products` */

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (1,1,'Martabak Manis','10001','C128',100,2000000,2500000,'PC',100,NULL,NULL,NULL,'2025-03-04 14:27:42','2025-03-10 10:30:49');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (2,1,'Martabak Asin','10002','C128',95,2500000,3000000,'PC',100,NULL,NULL,NULL,'2025-03-04 14:30:36','2025-06-05 09:57:38');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (3,2,'Juice Alpukat','10003','C128',95,1200000,1700000,'PC',100,NULL,NULL,NULL,'2025-03-04 14:37:40','2025-06-05 09:57:38');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (4,3,'Dimsum-Original','10004','C128',97,150000,300000,'PC',100,NULL,NULL,NULL,'2025-03-04 16:22:35','2025-05-06 09:47:51');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (5,2,'Ice Cream Chocolate','10005','C128',100,2000000,2500000,'PC',100,NULL,NULL,NULL,'2025-03-04 16:26:05','2025-03-04 16:26:05');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (6,1,'Pempek Palembang','10006','C128',94,700000,900000,'PC',100,NULL,NULL,NULL,'2025-03-04 16:32:03','2025-06-09 08:57:14');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (7,2,'Juice Jeruk','10007','C128',96,1000000,1300000,'PC',100,NULL,NULL,NULL,'2025-03-04 16:43:37','2025-06-09 10:32:53');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (8,1,'Sate Lilit','10008','C128',97,3000000,3500000,'PC',100,NULL,NULL,NULL,'2025-03-04 16:48:26','2025-06-09 10:32:53');
insert  into `products`(`id`,`category_id`,`product_name`,`product_code`,`product_barcode_symbology`,`product_quantity`,`product_cost`,`product_price`,`product_unit`,`product_stock_alert`,`product_order_tax`,`product_tax_type`,`product_note`,`created_at`,`updated_at`) values (9,3,'Tahu Sumedang','10009','C128',82,20000,50000,'PC',100,NULL,NULL,NULL,'2025-03-19 09:29:54','2025-06-09 10:32:53');

/*Table structure for table `purchase_details` */

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

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

insert  into `role_has_permissions`(`permission_id`,`role_id`) values (1,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (3,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (4,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (5,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (6,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (7,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (8,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (9,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (10,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (11,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (12,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (13,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (14,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (15,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (16,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (17,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (18,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (19,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (20,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (21,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (22,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (23,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (24,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (25,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (26,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (27,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (28,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (29,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (30,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (31,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (32,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (33,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (34,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (35,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (36,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (37,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (38,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (39,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (40,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (41,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (42,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (43,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (44,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (45,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (46,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (47,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (48,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (49,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (50,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (51,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (52,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (53,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (54,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (55,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (56,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (57,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (58,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (59,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (60,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (61,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (62,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (63,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (64,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (65,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (66,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (67,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (68,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (69,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (70,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (71,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (72,1);
insert  into `role_has_permissions`(`permission_id`,`role_id`) values (73,1);

/*Table structure for table `roles` */

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

insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (1,'Admin','web','2025-03-04 09:03:04','2025-03-04 09:03:04');
insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (2,'Super Admin','web','2025-03-04 09:03:09','2025-03-04 09:03:09');

/*Table structure for table `sale_details` */

CREATE TABLE `sale_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned DEFAULT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `price` int(11) DEFAULT 0,
  `unit_price` int(11) DEFAULT 0,
  `sub_total` int(11) DEFAULT 0,
  `product_discount_amount` int(11) DEFAULT 0,
  `product_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_details_sale_id_foreign` (`sale_id`),
  KEY `sale_details_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_details` */

/*Table structure for table `sale_payments` */

CREATE TABLE `sale_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(10) DEFAULT 0,
  `cashpay` int(10) DEFAULT 0,
  `debitcard` int(10) DEFAULT 0,
  `creditcard` int(10) DEFAULT 0,
  `gopay` int(10) DEFAULT 0,
  `grabpay` int(10) DEFAULT 0,
  `ovopay` int(10) DEFAULT 0,
  `shopeepay` int(10) DEFAULT 0,
  `danapay` int(10) DEFAULT 0,
  `kredivopay` int(10) DEFAULT 0,
  `qrispay` int(10) DEFAULT 0,
  `change` int(10) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_payments_sale_id_foreign` (`sale_id`),
  CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sale_payments` */

/*Table structure for table `sale_return_details` */

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

CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int(11) NOT NULL DEFAULT 0,
  `tax_amount` int(11) NOT NULL DEFAULT 0,
  `discount_percentage` int(11) DEFAULT 0,
  `discount_amount` int(11) DEFAULT 0,
  `shipping_amount` int(11) DEFAULT 0,
  `total_amount` int(11) DEFAULT 0,
  `paid_amount` int(11) DEFAULT 0,
  `due_amount` int(11) DEFAULT 0,
  `change` int(10) DEFAULT 0,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sales` */

/*Table structure for table `settings` */

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
  `name_printer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `print_via_mobile` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `settings` */

insert  into `settings`(`id`,`company_name`,`company_email`,`company_phone`,`site_logo`,`default_currency_id`,`default_currency_position`,`notification_email`,`footer_text`,`company_address`,`name_printer`,`print_via_mobile`,`created_at`,`updated_at`) values (1,'PT. Dataprima POS','company@test.com','012345678901',NULL,2,'prefix','notification@test.com','Triangle Pos © 2021 || Developed by <strong><a target=\"_blank\" href=\"https://fahimanzam.me\">Fahim Anzam</a></strong>','Jl. Trembesi Kemayoran','POS-58',0,'2025-03-04 09:03:09','2025-06-24 08:53:02');

/*Table structure for table `sliders` */

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `sliders` */

/*Table structure for table `suppliers` */

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

insert  into `units`(`id`,`name`,`short_name`,`operator`,`operation_value`,`created_at`,`updated_at`) values (1,'Piece','PC','*',1,'2025-03-04 09:03:09','2025-03-04 09:03:09');

/*Table structure for table `uploads` */

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

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`is_active`,`remember_token`,`created_at`,`updated_at`) values (1,'Administrator','super.admin@test.com',NULL,'$2y$10$WIeIriVJo0KCKPtDzDyy.u1qGXVJ.Sm120s5uOVxqXg8K7PoKNpua',1,NULL,'2025-03-04 09:03:08','2025-03-04 09:03:08');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
