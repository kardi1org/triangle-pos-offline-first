-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2025 at 05:56 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `triangle_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `adjusted_products`
--

CREATE TABLE `adjusted_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `adjusted_products`
--

INSERT INTO `adjusted_products` (`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'add', '2025-07-23 02:48:46', '2025-07-23 02:48:46'),
(2, 2, 1, 1, 'add', '2025-07-23 02:48:47', '2025-07-23 02:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `adjustments`
--

CREATE TABLE `adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `adjustments`
--

INSERT INTO `adjustments` (`id`, `date`, `reference`, `note`, `created_at`, `updated_at`) VALUES
(1, '2025-07-23', 'ADJ-00001', NULL, '2025-07-23 02:48:46', '2025-07-23 02:48:46'),
(2, '2025-07-23', 'ADJ-00002', NULL, '2025-07-23 02:48:47', '2025-07-23 02:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` bigint(20) NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `amount`, `date`, `details`, `created_at`, `updated_at`) VALUES
(1, 40000, '2025-07-15', 'detail 1', NULL, '2025-08-18 03:37:14'),
(2, 250000, '2025-07-15', 'detail 2', NULL, NULL),
(4, 50000, '2025-07-15', '123', '2025-07-15 03:47:26', '2025-07-15 03:47:26'),
(9, 450, '2025-07-16', 'test', '2025-07-16 00:55:37', '2025-07-16 00:55:37'),
(10, 1235, '2025-07-16', 'testing', '2025-07-16 00:57:00', '2025-07-16 00:57:00'),
(19, 25000, '2025-07-21', 'xxxx1', '2025-07-21 03:28:46', '2025-07-21 03:29:22'),
(20, 18000, '2025-07-24', 'test lagi', '2025-07-24 03:16:18', '2025-07-24 03:16:18'),
(21, 50000, '2025-08-06', NULL, '2025-08-06 05:55:04', '2025-08-06 05:55:04'),
(22, 40000, '2025-08-07', NULL, '2025-08-07 07:19:55', '2025-08-07 07:19:55'),
(24, 50000, '2025-08-18', NULL, '2025-08-18 03:35:44', '2025-08-18 03:35:44'),
(25, 50000, '2025-08-19', NULL, '2025-08-19 08:54:39', '2025-08-19 08:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_code`, `category_name`, `created_at`, `updated_at`) VALUES
(1, 'CA_01', 'Food', '2025-03-04 02:03:09', '2025-03-04 07:20:17'),
(2, 'CA_02', 'Beverage', '2025-03-04 07:21:31', '2025-03-04 07:21:31'),
(3, 'CA_03', 'Makanan', '2025-06-30 01:30:47', '2025-06-30 01:30:47'),
(4, 'CA_04', 'Makanan', '2025-07-16 02:09:36', '2025-07-16 02:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thousand_separator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decimal_separator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `currency_name`, `code`, `symbol`, `thousand_separator`, `decimal_separator`, `exchange_rate`, `created_at`, `updated_at`) VALUES
(1, 'US Dollar', 'USD', '$', ',', '.', NULL, '2025-03-04 02:03:09', '2025-03-04 02:03:09'),
(2, 'IDR Rupiah', 'RP', 'Rp', '.', ',', NULL, '2025-03-04 08:15:37', '2025-03-04 08:15:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_email`, `customer_phone`, `city`, `country`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Budi', 'budi@gmail.com', '085288889999', 'Kabupaten Bogor', 'Indonesia', 'Jakarta', '2025-07-10 06:08:52', '2025-07-10 06:08:52');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `category_id`, `date`, `reference`, `details`, `amount`, `created_at`, `updated_at`) VALUES
(4, 1, '2025-07-15', 'EXP-00001', NULL, 500000, '2025-07-15 07:07:22', '2025-07-15 07:07:22'),
(5, 1, '2025-08-14', 'EXP-00005', NULL, 300000, '2025-08-14 09:02:00', '2025-08-18 05:40:56'),
(6, 1, '2025-08-18', 'EXP-00006', NULL, 500000, '2025-08-18 05:39:15', '2025-08-18 05:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `category_name`, `category_description`, `created_at`, `updated_at`) VALUES
(1, 'Makanan', NULL, '2025-07-10 08:25:17', '2025-07-10 08:25:17');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percentage` int(11) DEFAULT 0,
  `tax_amount` int(11) DEFAULT 0,
  `discount_percentage` int(11) DEFAULT 0,
  `discount_amount` int(11) DEFAULT 0,
  `shipping_amount` int(11) DEFAULT 0,
  `total_amount` int(11) DEFAULT NULL,
  `paid_amount` int(11) DEFAULT NULL,
  `due_amount` int(11) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `date`, `reference`, `supplier_id`, `supplier_name`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(12, '2025-07-23', 'IR-00001', NULL, NULL, 0, 0, 0, 0, 0, 4200000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-23 09:18:21', '2025-07-23 09:18:21'),
(13, '2025-07-23', 'IR-00013', NULL, NULL, 0, 0, 0, 0, 0, 4500000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-23 09:18:49', '2025-07-23 09:18:49'),
(14, '2025-07-24', 'IR-00014', NULL, NULL, 0, 0, 0, 0, 0, 8000000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 02:06:48', '2025-07-24 02:07:21'),
(15, '2025-07-27', 'IR-00015', NULL, NULL, 0, 0, 0, 0, 0, 14000000, NULL, NULL, NULL, NULL, NULL, 'test2', '2025-07-28 06:52:47', '2025-07-28 09:11:01'),
(16, '2025-08-05', 'IR-00016', NULL, NULL, 0, 0, 0, 0, 0, 2000000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-05 06:31:37', '2025-08-05 06:31:37'),
(17, '2025-08-06', 'IR-00017', NULL, NULL, 0, 0, 0, 0, 0, 2000000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-06 05:55:43', '2025-08-06 05:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_details`
--

CREATE TABLE `inventory_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `Inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `sub_total` int(11) DEFAULT NULL,
  `product_discount_amount` int(11) DEFAULT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fixed',
  `product_tax_amount` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `inventory_details`
--

INSERT INTO `inventory_details` (`id`, `Inventory_id`, `product_id`, `product_name`, `product_code`, `quantity`, `price`, `unit_price`, `sub_total`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(21, 12, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-07-23 09:18:21', '2025-07-23 09:18:21'),
(22, 12, 3, 'Juice Alpukat', '10003', 1, 1700000, 1700000, 1700000, 0, 'fixed', 0, '2025-07-23 09:18:21', '2025-07-23 09:18:21'),
(23, 13, 4, 'Dimsum-Original', '10004', 1, 300000, 300000, 300000, 0, 'fixed', 0, '2025-07-23 09:18:49', '2025-07-23 09:18:49'),
(24, 13, 5, 'Ice Cream Chocolate', '10005', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-07-23 09:18:49', '2025-07-23 09:18:49'),
(25, 13, 3, 'Juice Alpukat', '10003', 1, 1700000, 1700000, 1700000, 0, 'fixed', 0, '2025-07-23 09:18:49', '2025-07-23 09:18:49'),
(28, 14, 2, 'Martabak Asin', '10002', 1, 3000000, 3000000, 3000000, 0, 'fixed', 0, '2025-07-24 02:07:21', '2025-07-24 02:07:21'),
(29, 14, 5, 'Ice Cream Chocolate', '10005', 2, 2500000, 2500000, 5000000, 0, 'fixed', 0, '2025-07-24 02:07:21', '2025-07-24 02:07:21'),
(42, 15, 2, 'Martabak Asin', '10002', 3, 3000000, 3000000, 9000000, 0, 'fixed', 0, '2025-07-28 09:11:01', '2025-07-28 09:11:01'),
(43, 15, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-07-28 09:11:01', '2025-07-28 09:11:01'),
(44, 15, 5, 'Ice Cream Chocolate', '10005', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-07-28 09:11:01', '2025-07-28 09:11:01'),
(45, 16, 1, 'Martabak Manis', '10001', 1, 2000000, 2000000, 2000000, 0, 'fixed', 0, '2025-08-05 06:31:37', '2025-08-05 06:31:37'),
(46, 17, 1, 'Martabak Manis', '10001', 1, 2000000, 2000000, 2000000, 0, 'fixed', 0, '2025-08-06 05:55:43', '2025-08-06 05:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `inv_opname`
--

CREATE TABLE `inv_opname` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percentage` int(11) DEFAULT 0,
  `tax_amount` int(11) DEFAULT 0,
  `discount_percentage` int(11) DEFAULT 0,
  `discount_amount` int(11) DEFAULT 0,
  `shipping_amount` int(11) DEFAULT 0,
  `total_amount` int(11) DEFAULT NULL,
  `paid_amount` int(11) DEFAULT NULL,
  `due_amount` int(11) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `inv_opname_details`
--

CREATE TABLE `inv_opname_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `Inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `sub_total` int(11) DEFAULT NULL,
  `product_discount_amount` int(11) DEFAULT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fixed',
  `product_tax_amount` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(3, 'Modules\\Product\\Entities\\Product', 2, '4016e3a4-2f0e-4f0a-aac6-b2a85882c7be', 'images', '1741076981', '1741076981.png', 'image/png', 'public', 'public', 93769, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 07:30:36', '2025-03-04 07:30:36'),
(4, 'Modules\\Product\\Entities\\Product', 3, 'f55396ed-3807-4c62-85f1-ca127c49b00c', 'images', '1741077454', '1741077454.png', 'image/jpeg', 'public', 'public', 59424, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 07:37:40', '2025-03-04 07:37:40'),
(5, 'Modules\\Product\\Entities\\Product', 5, '14cefebd-99e7-4c3d-bd07-d2521bd115c7', 'images', '1741083953', '1741083953.png', 'image/png', 'public', 'public', 88082, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 09:26:05', '2025-03-04 09:26:05'),
(6, 'Modules\\Product\\Entities\\Product', 6, '1209fb7a-5911-4a2e-a6ab-bae40e7bd6e1', 'images', '1741084318', '1741084318.png', 'image/png', 'public', 'public', 101248, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 09:32:03', '2025-03-04 09:32:04'),
(7, 'Modules\\Product\\Entities\\Product', 4, '539f575c-3faa-48d6-bec8-7339ac84119e', 'images', '1741084530', '1741084530.png', 'image/png', 'public', 'public', 517494, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 09:35:40', '2025-03-04 09:35:40'),
(8, 'Modules\\Product\\Entities\\Product', 7, '01b399a7-160a-4c1d-a353-12e636e59229', 'images', '1741085012', '1741085012.png', 'image/jpeg', 'public', 'public', 4775, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 09:43:37', '2025-03-04 09:43:37'),
(9, 'Modules\\Product\\Entities\\Product', 8, 'ddd62972-0562-4069-b45e-31de06a560fb', 'images', '1741085299', '1741085299.png', 'image/png', 'public', 'public', 712198, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-03-04 09:48:26', '2025-03-04 09:48:27'),
(13, 'Modules\\Product\\Entities\\Product', 1, '7a2ea779-744e-462d-b59a-6325425829d2', 'images', '1751250478', '1751250478.jpg', 'image/jpeg', 'public', 'public', 75797, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-06-30 01:28:01', '2025-06-30 01:28:01'),
(21, 'Modules\\Product\\Entities\\Product', 9, '94d0d82c-9e26-4861-a101-4ea926ed90e4', 'images', '1755161047', '1755161047.jpg', 'image/jpeg', 'public', 'public', 345782, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-08-14 07:44:12', '2025-08-14 07:44:14');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2021_07_14_145038_create_categories_table', 1),
(5, '2021_07_14_145047_create_products_table', 1),
(6, '2021_07_15_211319_create_media_table', 1),
(7, '2021_07_16_010005_create_uploads_table', 1),
(8, '2021_07_16_220524_create_permission_tables', 1),
(9, '2021_07_22_003941_create_adjustments_table', 1),
(10, '2021_07_22_004043_create_adjusted_products_table', 1),
(11, '2021_07_28_192608_create_expense_categories_table', 1),
(12, '2021_07_28_192616_create_expenses_table', 1),
(13, '2021_07_29_165419_create_customers_table', 1),
(14, '2021_07_29_165440_create_suppliers_table', 1),
(15, '2021_07_31_015923_create_currencies_table', 1),
(16, '2021_07_31_140531_create_settings_table', 1),
(17, '2021_07_31_201003_create_sales_table', 1),
(18, '2021_07_31_212446_create_sale_details_table', 1),
(19, '2021_08_07_192203_create_sale_payments_table', 1),
(20, '2021_08_08_021108_create_purchases_table', 1),
(21, '2021_08_08_021131_create_purchase_payments_table', 1),
(22, '2021_08_08_021713_create_purchase_details_table', 1),
(23, '2021_08_08_175345_create_sale_returns_table', 1),
(24, '2021_08_08_175358_create_sale_return_details_table', 1),
(25, '2021_08_08_175406_create_sale_return_payments_table', 1),
(26, '2021_08_08_222603_create_purchase_returns_table', 1),
(27, '2021_08_08_222612_create_purchase_return_details_table', 1),
(28, '2021_08_08_222646_create_purchase_return_payments_table', 1),
(29, '2021_08_16_015031_create_quotations_table', 1),
(30, '2021_08_16_155013_create_quotation_details_table', 1),
(31, '2023_07_01_184221_create_units_table', 1),
(32, '2025_05_14_143449_create_sliders_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` int(11) DEFAULT 0,
  `paid_amount` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `date`, `reference`, `customer_id`, `customer_name`, `total_amount`, `paid_amount`, `created_at`, `updated_at`) VALUES
(52, '2025-06-30', 'DO/202506/0001', NULL, NULL, 55000, 0, '2025-06-30 02:03:14', '2025-06-30 02:03:14'),
(53, '2025-06-30', 'DO/202506/0002', NULL, 'Santosa', 55000, 0, '2025-06-30 02:06:00', '2025-06-30 02:06:00'),
(54, '2025-06-30', 'DO/202506/0003', NULL, 'Santosa', 30000, 0, '2025-06-30 06:19:33', '2025-06-30 06:19:33'),
(55, '2025-07-08', 'DO/202507/0001', NULL, 'Santosa', 17000, 0, '2025-07-08 01:10:29', '2025-07-08 01:10:29'),
(56, '2025-07-08', 'DO/202507/0002', NULL, 'Santosa', 17000, 0, '2025-07-08 01:10:45', '2025-07-08 01:10:45'),
(57, '2025-07-09', 'DO/202507/0003', NULL, 'Santosa', 3000, 0, '2025-07-09 05:54:28', '2025-07-09 05:54:28'),
(58, '2025-07-25', 'DO/202507/0004', NULL, 'Santosa', NULL, 0, '2025-07-25 03:28:29', '2025-07-25 03:28:29'),
(59, '2025-08-08', 'DO/202508/0001', NULL, NULL, 20000, 0, '2025-08-08 08:05:13', '2025-08-08 08:05:13'),
(60, '2025-08-08', 'DO/202508/0002', NULL, NULL, 3000, 0, '2025-08-08 08:05:25', '2025-08-08 08:05:25'),
(61, '2025-08-13', 'DO/202508/0003', NULL, NULL, 3000, 0, '2025-08-13 01:11:42', '2025-08-13 01:11:43'),
(62, '2025-08-13', 'DO/202508/0004', NULL, NULL, 3000, 0, '2025-08-13 01:13:29', '2025-08-13 01:13:29'),
(63, '2025-08-13', 'DO/202508/0005', NULL, NULL, NULL, 0, '2025-08-13 06:11:17', '2025-08-13 06:11:17'),
(64, '2025-08-13', 'DO/202508/0006', NULL, NULL, NULL, 0, '2025-08-13 06:11:24', '2025-08-13 06:11:24'),
(65, '2025-08-13', 'DO/202508/0007', NULL, NULL, NULL, 0, '2025-08-13 06:12:29', '2025-08-13 06:12:29'),
(66, '2025-08-15', 'DO/202508/0008', NULL, NULL, 24000, 0, '2025-08-15 07:07:26', '2025-08-15 07:07:26'),
(67, '2025-08-22', 'DO/202508/0009', NULL, NULL, 3000, 0, '2025-08-22 02:53:24', '2025-08-22 02:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `price` int(11) DEFAULT 0,
  `unit_price` int(11) DEFAULT 0,
  `sub_total` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `reference`, `product_id`, `product_name`, `product_code`, `quantity`, `price`, `unit_price`, `sub_total`, `created_at`, `updated_at`) VALUES
(42, 52, 'DO/202506/0001', 1, 'Martabak Manis', '10001', 1, 25000, 25000, 25000, '2025-06-30 02:03:14', '2025-06-30 02:03:14'),
(43, 52, 'DO/202506/0001', 2, 'Martabak Asin', '10002', 1, 30000, 30000, 30000, '2025-06-30 02:03:14', '2025-06-30 02:03:14'),
(44, 53, 'DO/202506/0002', 1, 'Martabak Manis', '10001', 1, 25000, 25000, 25000, '2025-06-30 02:06:00', '2025-06-30 02:06:00'),
(45, 53, 'DO/202506/0002', 2, 'Martabak Asin', '10002', 1, 30000, 30000, 30000, '2025-06-30 02:06:00', '2025-06-30 02:06:00'),
(46, 54, 'DO/202506/0003', 2, 'Martabak Asin', '10002', 1, 30000, 30000, 30000, '2025-06-30 06:19:33', '2025-06-30 06:19:33'),
(47, 55, 'DO/202507/0001', 3, 'Juice Alpukat', '10003', 1, 17000, 17000, 17000, '2025-07-08 01:10:29', '2025-07-08 01:10:29'),
(48, 56, 'DO/202507/0002', 3, 'Juice Alpukat', '10003', 1, 17000, 17000, 17000, '2025-07-08 01:10:45', '2025-07-08 01:10:45'),
(49, 57, 'DO/202507/0003', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, '2025-07-09 05:54:28', '2025-07-09 05:54:28'),
(50, 59, 'DO/202508/0001', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, '2025-08-08 08:05:13', '2025-08-08 08:05:13'),
(51, 59, 'DO/202508/0001', 3, 'Juice Alpukat', '10003', 1, 17000, 17000, 17000, '2025-08-08 08:05:13', '2025-08-08 08:05:13'),
(52, 60, 'DO/202508/0002', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, '2025-08-08 08:05:25', '2025-08-08 08:05:25'),
(53, 61, 'DO/202508/0003', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, '2025-08-13 01:11:42', '2025-08-13 01:11:42'),
(54, 62, 'DO/202508/0004', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, '2025-08-13 01:13:29', '2025-08-13 01:13:29'),
(55, 66, 'DO/202508/0008', 4, 'Dimsum-Original', '10004', 8, 3000, 3000, 24000, '2025-08-15 07:07:26', '2025-08-15 07:07:26'),
(56, 67, 'DO/202508/0009', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, '2025-08-22 02:53:24', '2025-08-22 02:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `code` int(11) NOT NULL,
  `Cash` varchar(1) DEFAULT 'Y',
  `DebitCard` varchar(1) DEFAULT 'Y',
  `Gopay` varchar(1) DEFAULT 'Y',
  `CreditCard` varchar(1) DEFAULT 'Y',
  `OVO` varchar(1) DEFAULT 'Y',
  `ShopeePay` varchar(1) DEFAULT 'Y',
  `Kredivo` varchar(1) DEFAULT 'Y',
  `Dana` varchar(1) DEFAULT 'Y',
  `GrabPay` varchar(1) DEFAULT 'Y',
  `QRIS` varchar(1) DEFAULT 'Y',
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`code`, `Cash`, `DebitCard`, `Gopay`, `CreditCard`, `OVO`, `ShopeePay`, `Kredivo`, `Dana`, `GrabPay`, `QRIS`, `id`) VALUES
(1, 'Y', NULL, NULL, 'Y', 'Y', NULL, NULL, 'Y', NULL, 'Y', 0),
(2, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(3, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(4, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(5, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(6, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(7, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(8, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(9, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0),
(10, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'edit_own_profile', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(2, 'access_user_management', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(3, 'show_total_stats', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(4, 'show_month_overview', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(5, 'show_weekly_sales_purchases', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(6, 'show_monthly_cashflow', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(7, 'show_notifications', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(8, 'access_products', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(9, 'create_products', 'web', '2025-03-04 02:02:59', '2025-03-04 02:02:59'),
(10, 'show_products', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(11, 'edit_products', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(12, 'delete_products', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(13, 'access_product_categories', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(14, 'print_barcodes', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(15, 'access_adjustments', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(16, 'create_adjustments', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(17, 'show_adjustments', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(18, 'edit_adjustments', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(19, 'delete_adjustments', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(20, 'access_quotations', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(21, 'create_quotations', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(22, 'show_quotations', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(23, 'edit_quotations', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(24, 'delete_quotations', 'web', '2025-03-04 02:03:00', '2025-03-04 02:03:00'),
(25, 'create_quotation_sales', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(26, 'send_quotation_mails', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(27, 'access_expenses', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(28, 'create_expenses', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(29, 'edit_expenses', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(30, 'delete_expenses', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(31, 'access_expense_categories', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(32, 'access_customers', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(33, 'create_customers', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(34, 'show_customers', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(35, 'edit_customers', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(36, 'delete_customers', 'web', '2025-03-04 02:03:01', '2025-03-04 02:03:01'),
(37, 'access_suppliers', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(38, 'create_suppliers', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(39, 'show_suppliers', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(40, 'edit_suppliers', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(41, 'delete_suppliers', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(42, 'access_sales', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(43, 'create_sales', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(44, 'show_sales', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(45, 'edit_sales', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(46, 'delete_sales', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(47, 'create_pos_sales', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(48, 'access_sale_payments', 'web', '2025-03-04 02:03:02', '2025-03-04 02:03:02'),
(49, 'access_sale_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(50, 'create_sale_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(51, 'show_sale_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(52, 'edit_sale_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(53, 'delete_sale_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(54, 'access_sale_return_payments', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(55, 'access_purchases', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(56, 'create_purchases', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(57, 'show_purchases', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(58, 'edit_purchases', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(59, 'delete_purchases', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(60, 'access_purchase_payments', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(61, 'access_purchase_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(62, 'create_purchase_returns', 'web', '2025-03-04 02:03:03', '2025-03-04 02:03:03'),
(63, 'show_purchase_returns', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(64, 'edit_purchase_returns', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(65, 'delete_purchase_returns', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(66, 'access_purchase_return_payments', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(67, 'access_reports', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(68, 'access_currencies', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(69, 'create_currencies', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(70, 'edit_currencies', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(71, 'delete_currencies', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(72, 'access_settings', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(73, 'access_units', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `product_name`, `product_code`, `product_barcode_symbology`, `product_quantity`, `product_cost`, `product_price`, `product_unit`, `product_stock_alert`, `product_order_tax`, `product_tax_type`, `product_note`, `created_at`, `updated_at`) VALUES
(1, 1, 'Martabak Manis', '10001', 'C128', 103, 2000000, 2500000, 'PC', 100, 10, NULL, NULL, '2025-03-04 07:27:42', '2025-09-08 03:43:55'),
(2, 1, 'Martabak Asin', '10002', 'EAN13', 101, 2500000, 3000000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 07:30:36', '2025-09-08 03:43:55'),
(3, 2, 'Juice Alpukat', '10003', 'C128', 100, 1200000, 1700000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 07:37:40', '2025-03-04 09:08:20'),
(4, 1, 'Dimsum-Original', '10004', 'C128', 100, 150000, 300000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 09:22:35', '2025-03-04 09:22:35'),
(5, 2, 'Ice Cream Chocolate', '10005', 'C128', 100, 2000000, 2500000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 09:26:05', '2025-03-04 09:26:05'),
(6, 1, 'Pempek Palembang', '10006', 'C128', 100, 700000, 900000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 09:32:03', '2025-03-04 09:32:03'),
(7, 2, 'Juice Jeruk', '10007', 'C128', 100, 1000000, 1300000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 09:43:37', '2025-03-04 09:43:37'),
(8, 1, 'Sate Lilit', '10008', 'C128', 100, 3000000, 3500000, 'PC', 100, NULL, NULL, NULL, '2025-03-04 09:48:26', '2025-03-04 09:48:26'),
(9, 3, 'Nasi Goreng', '0001', 'EAN13', 12, 1200000, 1500000, 'PC', 1, NULL, NULL, NULL, '2025-06-30 01:31:55', '2025-06-30 01:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `date`, `reference`, `supplier_id`, `supplier_name`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(1, '2025-07-16', 'PR-00001', 1, 'PT.Sentosa Jaya', 0, 0, 0, 0, 0, 4500000, 4500000, 0, 'Pending', 'Paid', 'Cash', NULL, '2025-07-16 05:06:48', '2025-07-16 05:06:48'),
(2, '2025-07-28', 'PR-00002', 1, 'PT.Sentosa Jaya', 0, 0, 0, 0, 0, 4500000, 4500000, 0, 'Completed', 'Paid', 'Cash', NULL, '2025-07-28 08:56:44', '2025-09-08 03:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_details`
--

INSERT INTO `purchase_details` (`id`, `purchase_id`, `product_id`, `product_name`, `product_code`, `quantity`, `price`, `unit_price`, `sub_total`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Martabak Manis', '10001', 1, 2000000, 2000000, 2000000, 0, 'fixed', 0, '2025-07-16 05:06:49', '2025-07-16 05:06:49'),
(2, 1, 2, 'Martabak Asin', '10002', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-07-16 05:06:49', '2025-07-16 05:06:49'),
(5, 2, 1, 'Martabak Manis', '10001', 1, 2000000, 2000000, 2000000, 0, 'fixed', 0, '2025-09-08 03:43:55', '2025-09-08 03:43:55'),
(6, 2, 2, 'Martabak Asin', '10002', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-09-08 03:43:55', '2025-09-08 03:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_payments`
--

CREATE TABLE `purchase_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_payments`
--

INSERT INTO `purchase_payments` (`id`, `purchase_id`, `amount`, `date`, `reference`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 4500000, '2025-07-16', 'INV/PR-00001', 'Cash', NULL, '2025-07-16 05:06:49', '2025-07-16 05:06:49'),
(2, 2, 4500000, '2025-07-28', 'INV/PR-00002', 'Cash', NULL, '2025-07-28 08:56:45', '2025-07-28 08:56:45');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_details`
--

CREATE TABLE `purchase_return_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_payments`
--

CREATE TABLE `purchase_return_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `date`, `reference`, `customer_id`, `customer_name`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, '2025-07-16', 'QT-00001', 1, 'Budi', 0, 0, 0, 0, 0, 5500000, 'Pending', NULL, '2025-07-16 03:55:36', '2025-07-16 03:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_details`
--

CREATE TABLE `quotation_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_details`
--

INSERT INTO `quotation_details` (`id`, `quotation_id`, `product_id`, `product_name`, `product_code`, `quantity`, `price`, `unit_price`, `sub_total`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-07-16 03:55:36', '2025-07-16 03:55:36'),
(2, 1, 2, 'Martabak Asin', '10002', 1, 3000000, 3000000, 3000000, 0, 'fixed', 0, '2025-07-16 03:55:36', '2025-07-16 03:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2025-03-04 02:03:04', '2025-03-04 02:03:04'),
(2, 'Super Admin', 'web', '2025-03-04 02:03:09', '2025-03-04 02:03:09'),
(3, 'Kasir', 'web', '2025-07-15 06:10:53', '2025-08-06 09:08:31');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(3, 1),
(3, 3),
(4, 1),
(4, 3),
(5, 1),
(5, 3),
(6, 1),
(6, 3),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(39, 3),
(40, 1),
(41, 1),
(42, 1),
(42, 3),
(43, 1),
(43, 3),
(44, 1),
(45, 1),
(45, 3),
(46, 1),
(46, 3),
(47, 1),
(47, 3),
(48, 1),
(48, 3),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `date`, `reference`, `customer_id`, `customer_name`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `paid_amount`, `due_amount`, `change`, `status`, `payment_status`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(8, '2025-06-30', 'SL/202506/0001', NULL, 'Santosa', 0, 0, 0, 0, 0, 20000, 20000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-06-30 02:28:16', '2025-06-30 02:28:16'),
(9, '2025-08-12', 'SL/202508/0001', NULL, NULL, 0, 0, 0, 0, 0, 23000, NULL, 0, 0, 'Completed', 'Unpaid', NULL, NULL, '2025-08-12 06:39:29', '2025-08-12 06:39:29'),
(10, '2025-08-12', 'SL/202508/0002', NULL, NULL, 0, 0, 0, 0, 0, 3000, 3000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-08-12 08:15:06', '2025-08-12 08:15:06'),
(11, '2025-08-12', 'SL/202508/0003', NULL, NULL, 0, 0, 0, 0, 0, 12000, 18000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-08-12 08:23:44', '2025-08-12 08:23:44'),
(12, '2025-08-14', 'SL/202508/0004', NULL, NULL, 0, 0, 0, 0, 0, 3000, 6000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-08-14 07:32:02', '2025-08-14 07:32:02'),
(13, '2025-08-18', 'SL-00013', 1, 'Budi', 0, 0, 0, 0, 0, 55000, 55000, 0, 0, 'Pending', 'Paid', 'Cash', NULL, '2025-08-18 06:25:30', '2025-08-18 06:25:30'),
(14, '2025-08-18', 'SL-00014', 1, 'Budi', 0, 0, 0, 0, 0, 3000, 3000, 0, 0, 'Completed', 'Paid', 'Cash', NULL, '2025-08-18 06:43:26', '2025-08-18 06:43:26'),
(15, '2025-08-18', 'SL-00015', NULL, NULL, 0, 0, 0, 0, 0, 3000, 3000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-08-18 08:10:26', '2025-08-18 08:10:26'),
(16, '2025-08-18', 'SL-00016', NULL, 'Santosa', 0, 0, 0, 0, 0, 25000, 25000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-08-18 08:55:33', '2025-08-18 08:55:33'),
(17, '2025-08-18', 'SL-00017', NULL, 'Santosa', 0, 0, 0, 0, 0, 600000, 600000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-08-18 09:05:43', '2025-08-18 09:05:43'),
(18, '2025-08-18', 'SL-00018', NULL, NULL, 0, 0, 0, 0, 0, 2500000, 2500000, 0, 0, 'Pending', 'Paid', NULL, NULL, '2025-08-18 09:26:44', '2025-08-18 09:26:44'),
(19, '2025-09-03', 'SL-00019', NULL, NULL, 0, 0, 0, 0, 0, 2500000, 2500000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-09-03 06:35:00', '2025-09-03 06:35:00'),
(20, '2025-09-09', 'SL-00020', NULL, NULL, 0, 0, 0, 0, 0, 600000, 600000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-09-09 03:14:08', '2025-09-09 03:14:08'),
(21, '2025-09-09', 'SL-00021', NULL, NULL, 0, 0, 0, 0, 0, 8300000, 8300000, 0, 0, 'Completed', 'Paid', NULL, NULL, '2025-09-09 09:21:31', '2025-09-09 09:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `reference`, `product_id`, `product_name`, `product_code`, `quantity`, `price`, `unit_price`, `sub_total`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(19, 8, 'SL/202506/0001', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, 0, 'fixed', 0, '2025-06-30 02:28:16', '2025-06-30 02:28:16'),
(20, 8, 'SL/202506/0001', 3, 'Juice Alpukat', '10003', 1, 17000, 17000, 17000, 0, 'fixed', 0, '2025-06-30 02:28:16', '2025-06-30 02:28:16'),
(21, 9, 'SL/202508/0001', 4, 'Dimsum-Original', '10004', 2, 3000, 3000, 6000, 0, 'fixed', 0, '2025-08-12 06:39:29', '2025-08-12 06:39:29'),
(22, 9, 'SL/202508/0001', 3, 'Juice Alpukat', '10003', 1, 17000, 17000, 17000, 0, 'fixed', 0, '2025-08-12 06:39:29', '2025-08-12 06:39:29'),
(23, 10, 'SL/202508/0002', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, 0, 'fixed', 0, '2025-08-12 08:15:06', '2025-08-12 08:15:06'),
(24, 11, 'SL/202508/0003', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, 0, 'fixed', 0, '2025-08-12 08:23:44', '2025-08-12 08:23:44'),
(25, 11, 'SL/202508/0003', 6, 'Pempek Palembang', '10006', 1, 9000, 9000, 9000, 0, 'fixed', 0, '2025-08-12 08:23:44', '2025-08-12 08:23:44'),
(26, 12, 'SL/202508/0004', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, 0, 'fixed', 0, '2025-08-14 07:32:02', '2025-08-14 07:32:02'),
(27, 13, NULL, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-08-18 06:25:30', '2025-08-18 06:25:30'),
(28, 13, NULL, 2, 'Martabak Asin', '10002', 1, 3000000, 3000000, 3000000, 0, 'fixed', 0, '2025-08-18 06:25:31', '2025-08-18 06:25:31'),
(29, 14, 'SL-00014', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, 0, 'fixed', 0, '2025-08-18 06:43:26', '2025-08-18 06:43:26'),
(30, 15, 'SL-00015', 4, 'Dimsum-Original', '10004', 1, 3000, 3000, 3000, 0, 'fixed', 0, '2025-08-18 08:10:26', '2025-08-18 08:10:26'),
(31, 16, 'SL-00016', 1, 'Martabak Manis', '10001', 1, 25000, 25000, 25000, 0, 'fixed', 0, '2025-08-18 08:55:33', '2025-08-18 08:55:33'),
(32, 17, 'SL-00017', 4, 'Dimsum-Original', '10004', 2, 300000, 300000, 600000, 0, 'fixed', 0, '2025-08-18 09:05:43', '2025-08-18 09:05:43'),
(33, 18, NULL, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-08-18 09:26:44', '2025-08-18 09:26:44'),
(34, 19, 'SL-00019', 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-09-03 06:35:00', '2025-09-03 06:35:00'),
(35, 20, 'SL-00020', 4, 'Dimsum-Original', '10004', 2, 300000, 300000, 600000, 0, 'fixed', 0, '2025-09-09 03:14:08', '2025-09-09 03:14:08'),
(36, 21, 'SL-00021', 4, 'Dimsum-Original', '10004', 5, 300000, 300000, 300000, 0, 'fixed', 0, '2025-09-09 09:21:31', '2025-09-09 09:21:31'),
(37, 21, 'SL-00021', 3, 'Juice Alpukat', '10003', 3, 1700000, 1700000, 5100000, 0, 'fixed', 0, '2025-09-09 09:21:31', '2025-09-09 09:21:31'),
(38, 21, 'SL-00021', 3, 'Juice Alpukat', '10003', 1, 1700000, 1700000, 1700000, 0, 'fixed', 0, '2025-09-09 09:21:31', '2025-09-09 09:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `sale_payments`
--

CREATE TABLE `sale_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_payments`
--

INSERT INTO `sale_payments` (`id`, `sale_id`, `date`, `reference`, `payment_method`, `note`, `amount`, `cashpay`, `debitcard`, `creditcard`, `gopay`, `grabpay`, `ovopay`, `shopeepay`, `danapay`, `kredivopay`, `qrispay`, `change`, `created_at`, `updated_at`) VALUES
(5, 8, '2025-06-30', 'INV/SL/202506/0001', NULL, NULL, 20000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-30 02:28:16', '2025-06-30 02:28:16'),
(6, 10, '2025-08-12', 'INV/SL/202508/0002', NULL, NULL, 3000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-08-12 08:15:07', '2025-08-12 08:15:07'),
(7, 11, '2025-08-12', 'INV/SL/202508/0003', NULL, NULL, 18000, 6000, 5000, 3000, 4000, 0, 0, 0, 0, 0, 0, 6000, '2025-08-12 08:23:45', '2025-08-12 08:23:45'),
(8, 12, '2025-08-14', 'INV/SL/202508/0004', NULL, NULL, 6000, 6000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3000, '2025-08-14 07:32:02', '2025-08-14 07:32:02'),
(9, 13, '2025-08-18', 'INV/SL-00013', 'Cash', NULL, 55000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-08-18 06:25:31', '2025-08-18 06:25:31'),
(10, 14, '2025-08-18', 'INV/SL-00014', NULL, NULL, 3000, 3000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-08-18 06:43:26', '2025-08-18 06:43:26'),
(11, 15, '2025-08-18', 'INV/SL-00015', NULL, NULL, 3000, 3000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-08-18 08:10:26', '2025-08-18 08:10:26'),
(12, 16, '2025-08-18', 'INV/SL-00016', NULL, NULL, 25000, 25000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-08-18 08:55:33', '2025-08-18 08:55:33'),
(13, 17, '2025-08-18', 'INV/SL-00017', NULL, NULL, 600000, 6000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-08-18 09:05:43', '2025-08-18 09:05:43'),
(14, 18, '2025-08-18', 'INV/SL-00018', 'Cash', NULL, 2500000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-08-18 09:26:44', '2025-08-18 09:26:44'),
(15, 19, '2025-09-03', 'INV/SL-00019', NULL, NULL, 2500000, 25000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-09-03 06:35:00', '2025-09-03 06:35:00'),
(16, 20, '2025-09-09', 'INV/SL-00020', NULL, NULL, 600000, 6000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-09-09 03:14:08', '2025-09-09 03:14:08'),
(17, 21, '2025-09-09', 'INV/SL-00021', NULL, NULL, 8300000, 83000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-09-09 09:21:31', '2025-09-09 09:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_returns`
--

INSERT INTO `sale_returns` (`id`, `date`, `reference`, `customer_id`, `customer_name`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(1, '2025-08-18', 'SLRN-00001', 1, 'Budi', 0, 0, 0, 0, 0, 2500000, 2500000, 0, 'Pending', 'Paid', 'Cash', NULL, '2025-08-18 08:32:27', '2025-08-18 08:32:27'),
(2, '2025-08-18', 'SLRN-00002', 1, 'Budi', 0, 0, 0, 0, 0, 2500000, 2500000, 0, 'Pending', 'Paid', 'Cash', NULL, '2025-08-18 08:36:36', '2025-08-18 08:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_details`
--

CREATE TABLE `sale_return_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_return_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_return_details`
--

INSERT INTO `sale_return_details` (`id`, `sale_return_id`, `product_id`, `product_name`, `product_code`, `quantity`, `price`, `unit_price`, `sub_total`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-08-18 08:32:28', '2025-08-18 08:32:28'),
(2, 2, 1, 'Martabak Manis', '10001', 1, 2500000, 2500000, 2500000, 0, 'fixed', 0, '2025-08-18 08:36:36', '2025-08-18 08:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_payments`
--

CREATE TABLE `sale_return_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_return_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_return_payments`
--

INSERT INTO `sale_return_payments` (`id`, `sale_return_id`, `amount`, `date`, `reference`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 2500000, '2025-08-18', 'INV/SLRN-00001', 'Cash', NULL, '2025-08-18 08:32:28', '2025-08-18 08:32:28'),
(2, 2, 2500000, '2025-08-18', 'INV/SLRN-00002', 'Cash', NULL, '2025-08-18 08:36:36', '2025-08-18 08:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_name`, `company_email`, `company_phone`, `site_logo`, `default_currency_id`, `default_currency_position`, `notification_email`, `footer_text`, `company_address`, `name_printer`, `print_via_mobile`, `created_at`, `updated_at`) VALUES
(1, 'PT. Data Prima POS', 'company@test.com', '012345678901', NULL, 2, 'prefix', 'notification@test.com', 'Triangle Pos © 2021 || Developed by <strong><a target=\"_blank\" href=\"https://fahimanzam.me\">Fahim Anzam</a></strong>', 'Jl. Trembesi Kemayoran', '', 0, '2025-03-04 02:03:09', '2025-03-04 08:59:54');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `supplier_email`, `supplier_phone`, `city`, `country`, `address`, `created_at`, `updated_at`) VALUES
(1, 'PT.Sentosa Jaya', 'sentosa@gmail.com', '123456', 'jakarta', 'Indonesia', 'Jakarta', '2025-07-16 05:06:06', '2025-07-16 05:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operator` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_value` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `short_name`, `operator`, `operation_value`, `created_at`, `updated_at`) VALUES
(1, 'Piece', 'PC', '*', 1, '2025-03-04 02:03:09', '2025-03-04 02:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `valid_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_active`, `remember_token`, `created_at`, `updated_at`, `valid_date`) VALUES
(1, 'Administrator', 'super.admin@test.com', NULL, '$2y$10$WIeIriVJo0KCKPtDzDyy.u1qGXVJ.Sm120s5uOVxqXg8K7PoKNpua', 1, NULL, '2025-03-04 02:03:08', '2025-03-04 02:03:08', '2025-08-05'),
(2, 'Kardi', 'admin@gmail.com', NULL, '$2y$10$zE7Aq2fjo/RYlOUiZ.DEAeQBP1PSb.lftMiPKZrMEHUnND4uYaVBW', 1, NULL, '2025-08-06 06:59:52', '2025-08-08 07:19:31', '2025-08-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjusted_products_adjustment_id_foreign` (`adjustment_id`);

--
-- Indexes for table `adjustments`
--
ALTER TABLE `adjustments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD KEY `id` (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_category_code_unique` (`category_code`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_category_id_foreign` (`category_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_details`
--
ALTER TABLE `inventory_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inv_opname`
--
ALTER TABLE `inv_opname`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inv_opname_details`
--
ALTER TABLE `inv_opname_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_product_code_unique` (`product_code`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_details_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_payments_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_returns_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_details_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `purchase_return_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchase_return_payments`
--
ALTER TABLE `purchase_return_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_payments_purchase_return_id_foreign` (`purchase_return_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotations_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `quotation_details`
--
ALTER TABLE `quotation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_details_quotation_id_foreign` (`quotation_id`),
  ADD KEY `quotation_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_details_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_payments_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_details_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `sale_return_payments`
--
ALTER TABLE `sale_return_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_payments_sale_return_id_foreign` (`sale_return_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `adjustments`
--
ALTER TABLE `adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `inventory_details`
--
ALTER TABLE `inventory_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `inv_opname`
--
ALTER TABLE `inv_opname`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `inv_opname_details`
--
ALTER TABLE `inv_opname_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_payments`
--
ALTER TABLE `purchase_return_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotation_details`
--
ALTER TABLE `quotation_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sale_return_payments`
--
ALTER TABLE `sale_return_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD CONSTRAINT `adjusted_products_adjustment_id_foreign` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `purchase_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD CONSTRAINT `purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  ADD CONSTRAINT `purchase_return_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_return_details_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_return_payments`
--
ALTER TABLE `purchase_return_payments`
  ADD CONSTRAINT `purchase_return_payments_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quotation_details`
--
ALTER TABLE `quotation_details`
  ADD CONSTRAINT `quotation_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotation_details_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  ADD CONSTRAINT `sale_return_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_return_details_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_return_payments`
--
ALTER TABLE `sale_return_payments`
  ADD CONSTRAINT `sale_return_payments_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
