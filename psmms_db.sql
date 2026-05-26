-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2026 at 11:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `psmms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(4, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 04:28:07'),
(5, 1, 'manager_create', 'Created manager: John Cyril D. San Antonio', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 05:23:29'),
(6, 1, 'profile_update', 'User updated profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 05:38:30'),
(7, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 05:39:34'),
(8, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 05:43:13'),
(9, 1, 'admin_create', 'Created admin: John Cyril San Antonio', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 05:47:28'),
(10, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 05:55:20'),
(12, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:19:16'),
(13, 1, 'manager_approve', 'Approved manager user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:24:46'),
(14, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:26:48'),
(16, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:27:24'),
(17, 1, 'manager_approve', 'Approved manager user ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:28:15'),
(18, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:30:09'),
(20, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:30:42'),
(21, 1, 'manager_approve', 'Approved manager user ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:30:47'),
(22, 6, 'register', 'New user registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:33:16'),
(23, 1, 'manager_approve', 'Approved manager user ID: 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:33:24'),
(24, 1, 'manager_approve', 'Approved manager manager ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:35:43'),
(25, 1, 'manager_create', 'Created manager: John Cyril D. San Antonio', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:38:43'),
(26, 1, 'manager_approve', 'Approved manager manager ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:38:54'),
(27, 1, 'manager_create', 'Created manager: John Cyril D. San Antonio', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:42:50'),
(28, 1, 'manager_approve', 'Approved manager manager ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 06:42:55'),
(29, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 00:40:25'),
(30, 1, 'plan_create', 'Created plan: surf2sawa PLAN999', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 01:22:33'),
(31, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 02:42:30'),
(32, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 06:07:06'),
(33, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 08:07:50'),
(34, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 01:18:42'),
(35, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 01:18:43'),
(36, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 01:19:29'),
(37, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 01:22:47'),
(38, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 01:27:00'),
(39, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 03:29:09'),
(40, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 03:40:50'),
(41, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 03:41:51'),
(42, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:12:52'),
(43, 7, 'register', 'New user registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:16:24'),
(44, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:16:37'),
(45, 1, 'admin_approve', 'Approved admin: John Cyril San Antonio', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:18:23'),
(46, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:18:29'),
(47, 7, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:19:12'),
(48, 7, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:21:38'),
(49, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 05:24:57'),
(50, 1, 'login', 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 08:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `area` varchar(150) DEFAULT NULL,
  `contact_no` varchar(30) DEFAULT NULL,
  `employee_id` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `company_email` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `first_name`, `last_name`, `position`, `area`, `contact_no`, `employee_id`, `department`, `company_email`, `email`, `profile_picture`, `status`, `created_at`, `updated_at`) VALUES
(2, 7, 'John Cyril', 'San Antonio', 'sales_admin', NULL, '09505960921', 'PCC0122', 'operation', 'cyrilsantonio19@paragoncorp.com.ph', 'cyrilsanantonio19@gmail.com', 'admins/adm_29892953a357371cb31498c54c44bd6b.png', 'active', '2026-05-26 05:16:24', '2026-05-26 05:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `agent_codes`
--

CREATE TABLE `agent_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `sales_category_id` int(10) UNSIGNED NOT NULL,
  `sales_agent_id` int(10) UNSIGNED NOT NULL,
  `agent_code` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `validation` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asm_names`
--

CREATE TABLE `asm_names` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `validation_status` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `check_surf2sawa` tinyint(1) NOT NULL DEFAULT 0,
  `check_fiberx` tinyint(1) NOT NULL DEFAULT 0,
  `check_bida` tinyint(1) NOT NULL DEFAULT 0,
  `check_sme` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asm_per_areas`
--

CREATE TABLE `asm_per_areas` (
  `id` int(10) UNSIGNED NOT NULL,
  `sales_manager` varchar(150) NOT NULL,
  `region` varchar(80) NOT NULL,
  `province` varchar(120) NOT NULL,
  `municipality` varchar(120) NOT NULL,
  `validation_status` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `check_surf2sawa` tinyint(1) NOT NULL DEFAULT 0,
  `check_fiberx` tinyint(1) NOT NULL DEFAULT 0,
  `check_bida` tinyint(1) NOT NULL DEFAULT 0,
  `check_sme` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_remarks`
--

CREATE TABLE `dispatch_remarks` (
  `id` int(10) UNSIGNED NOT NULL,
  `dispatch_remarks` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_statuses`
--

CREATE TABLE `dispatch_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `dispatch_status` varchar(150) NOT NULL,
  `color` char(7) NOT NULL DEFAULT '#ffffff',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inhouse_sales`
--

CREATE TABLE `inhouse_sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_manager` varchar(150) NOT NULL,
  `sales_category` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `contact_no` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `installer_tech_data`
--

CREATE TABLE `installer_tech_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `installer_name` varchar(150) NOT NULL,
  `full_name` varchar(150) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `category` varchar(100) NOT NULL DEFAULT '',
  `contact_no` varchar(30) NOT NULL,
  `area` varchar(150) NOT NULL,
  `validation_status` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `check_surf2sawa` tinyint(1) NOT NULL DEFAULT 0,
  `check_fiberx` tinyint(1) NOT NULL DEFAULT 0,
  `check_bida` tinyint(1) NOT NULL DEFAULT 0,
  `check_sme` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `installer_tech_team_areas`
--

CREATE TABLE `installer_tech_team_areas` (
  `id` int(10) UNSIGNED NOT NULL,
  `area` varchar(150) NOT NULL,
  `team` varchar(100) NOT NULL,
  `validation_status` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `manager_name` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `contact_no` varchar(30) DEFAULT NULL,
  `company_email` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `msa_partners`
--

CREATE TABLE `msa_partners` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `company_name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `contact_no` varchar(30) NOT NULL,
  `address` varchar(255) NOT NULL,
  `installer` varchar(150) NOT NULL,
  `msa_type` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `municipalities`
--

CREATE TABLE `municipalities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `province_id` bigint(20) UNSIGNED NOT NULL,
  `municipality_name` varchar(120) NOT NULL,
  `municipality_code` varchar(40) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `product` varchar(100) NOT NULL,
  `plan` varchar(100) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `product`, `plan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'surf2sawa', 'PLAN999', 'unavailable', '2026-05-20 01:22:33', '2026-05-20 01:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `region_id` bigint(20) UNSIGNED NOT NULL,
  `province_name` varchar(120) NOT NULL,
  `province_code` varchar(40) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `region_name` varchar(120) NOT NULL,
  `region_code` varchar(40) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `region_name`, `region_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'NCR', NULL, 'active', '2026-05-26 05:32:29', '2026-05-26 05:32:29'),
(2, 'VIZMIN', NULL, 'active', '2026-05-26 05:32:41', '2026-05-26 05:32:41'),
(3, 'NCLZ', NULL, 'active', '2026-05-26 05:32:48', '2026-05-26 05:32:48'),
(4, 'SLB', NULL, 'active', '2026-05-26 05:32:53', '2026-05-26 05:32:53');

-- --------------------------------------------------------

--
-- Table structure for table `sales_agents`
--

CREATE TABLE `sales_agents` (
  `id` int(10) UNSIGNED NOT NULL,
  `sales_category_id` int(10) UNSIGNED NOT NULL,
  `agent_name` varchar(150) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `validation` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_categories`
--

CREATE TABLE `sales_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `sales_category` varchar(150) NOT NULL,
  `sales_manager` varchar(150) NOT NULL,
  `type` enum('partner','inhouse') NOT NULL,
  `tl_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `validation` enum('approved','pending','declined') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `company_email` varchar(150) DEFAULT NULL,
  `contact_no` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('accounting','asm_manager','admin','head_manager','super_admin','msa_partners','inhouse_sales','sme_sales') NOT NULL DEFAULT 'accounting',
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'active',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `middle_name`, `last_name`, `email`, `company_email`, `contact_no`, `password`, `role`, `status`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', 'System Admin', NULL, '', 'admin@psmms.local', NULL, NULL, '$2y$12$6/bYLVQDFFp2w4c2pTQ8KuRRW12foTIf6iiR7idiwLu0RZKgHNXpO', 'super_admin', 'active', 'avatars/usr_5064524f9a796daf714e060a11c34d91.jpg', '2026-05-15 05:35:32', '2026-05-15 05:35:32'),
(6, 'John Cyril Dunghayan San Antonio', 'John Cyril Dunghayan San Antonio', NULL, '', 'cyrilsanantonio34@gmail.com', 'cyrilsanantonio34@paragon.com', '09505960921', '$2y$12$pNckb45wQyE4PPWCBVRBj.N.C6tj4CE3Q6pMth/bDnOfnwwCGSr6C', 'asm_manager', 'active', NULL, '2026-05-15 06:33:16', '2026-05-15 06:33:24'),
(7, 'John Cyril San Antonio', 'John Cyril', NULL, 'San Antonio', 'cyrilsanantonio19@gmail.com', 'cyrilsantonio19@paragoncorp.com.ph', '09505960921', '$2y$12$KoF430CU3b3Dw.5lqUU5LucqpF7I7.RN3IhzLrsAx9P02vsVD12O.', 'super_admin', 'active', NULL, '2026-05-26 05:16:24', '2026-05-26 05:18:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_user_index` (`user_id`),
  ADD KEY `activity_action_index` (`action`),
  ADD KEY `activity_created_at_index` (`created_at`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `admins_company_email_unique` (`company_email`),
  ADD UNIQUE KEY `admins_email_unique` (`email`),
  ADD KEY `admins_user_id_index` (`user_id`),
  ADD KEY `admins_department_index` (`department`),
  ADD KEY `admins_status_index` (`status`),
  ADD KEY `admins_created_at_index` (`created_at`);

--
-- Indexes for table `agent_codes`
--
ALTER TABLE `agent_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agent_codes_code_unique` (`agent_code`),
  ADD KEY `agent_codes_category_index` (`sales_category_id`),
  ADD KEY `agent_codes_agent_index` (`sales_agent_id`),
  ADD KEY `agent_codes_status_index` (`status`),
  ADD KEY `agent_codes_validation_index` (`validation`),
  ADD KEY `agent_codes_created_at_index` (`created_at`);

--
-- Indexes for table `asm_names`
--
ALTER TABLE `asm_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name_index` (`name`),
  ADD KEY `validation_status_index` (`validation_status`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_at_index` (`created_at`);

--
-- Indexes for table `asm_per_areas`
--
ALTER TABLE `asm_per_areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_manager_index` (`sales_manager`),
  ADD KEY `region_index` (`region`),
  ADD KEY `province_index` (`province`),
  ADD KEY `municipality_index` (`municipality`),
  ADD KEY `validation_status_index` (`validation_status`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_at_index` (`created_at`);

--
-- Indexes for table `dispatch_remarks`
--
ALTER TABLE `dispatch_remarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dispatch_remarks_name_unique` (`dispatch_remarks`),
  ADD KEY `dispatch_remarks_created_at_index` (`created_at`);

--
-- Indexes for table `dispatch_statuses`
--
ALTER TABLE `dispatch_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dispatch_statuses_name_unique` (`dispatch_status`),
  ADD KEY `dispatch_statuses_created_at_index` (`created_at`);

--
-- Indexes for table `inhouse_sales`
--
ALTER TABLE `inhouse_sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inhouse_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `inhouse_email_unique` (`email`),
  ADD KEY `inhouse_user_id_index` (`user_id`),
  ADD KEY `inhouse_status_index` (`status`),
  ADD KEY `inhouse_created_at_index` (`created_at`);

--
-- Indexes for table `installer_tech_data`
--
ALTER TABLE `installer_tech_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `installer_name_index` (`installer_name`),
  ADD KEY `area_index` (`area`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_at_index` (`created_at`);

--
-- Indexes for table `installer_tech_team_areas`
--
ALTER TABLE `installer_tech_team_areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `area_index` (`area`),
  ADD KEY `team_index` (`team`),
  ADD KEY `validation_status_index` (`validation_status`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_at_index` (`created_at`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `managers_company_email_unique` (`company_email`),
  ADD UNIQUE KEY `managers_email_unique` (`email`),
  ADD KEY `managers_user_id_index` (`user_id`),
  ADD KEY `managers_position_index` (`position`),
  ADD KEY `managers_status_index` (`status`),
  ADD KEY `managers_created_at_index` (`created_at`);

--
-- Indexes for table `msa_partners`
--
ALTER TABLE `msa_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `msa_username_unique` (`username`),
  ADD UNIQUE KEY `msa_email_unique` (`email`),
  ADD KEY `msa_user_id_index` (`user_id`),
  ADD KEY `msa_type_index` (`msa_type`),
  ADD KEY `msa_status_index` (`status`),
  ADD KEY `msa_created_at_index` (`created_at`);

--
-- Indexes for table `municipalities`
--
ALTER TABLE `municipalities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_province_municipality` (`province_id`,`municipality_name`),
  ADD UNIQUE KEY `uniq_municipality_code` (`municipality_code`),
  ADD KEY `idx_municipality_status` (`status`),
  ADD KEY `idx_municipality_province` (`province_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_resets_token_index` (`token`),
  ADD KEY `password_resets_user_index` (`user_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plans_product_index` (`product`),
  ADD KEY `plans_status_index` (`status`),
  ADD KEY `plans_created_at_index` (`created_at`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_region_province` (`region_id`,`province_name`),
  ADD UNIQUE KEY `uniq_province_code` (`province_code`),
  ADD KEY `idx_province_status` (`status`),
  ADD KEY `idx_province_region` (`region_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_region_name` (`region_name`),
  ADD UNIQUE KEY `uniq_region_code` (`region_code`),
  ADD KEY `idx_region_status` (`status`);

--
-- Indexes for table `sales_agents`
--
ALTER TABLE `sales_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_agents_category_index` (`sales_category_id`),
  ADD KEY `sales_agents_status_index` (`status`),
  ADD KEY `sales_agents_validation_index` (`validation`),
  ADD KEY `sales_agents_created_at_index` (`created_at`);

--
-- Indexes for table `sales_categories`
--
ALTER TABLE `sales_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_categories_name_index` (`sales_category`),
  ADD KEY `sales_categories_manager_index` (`sales_manager`),
  ADD KEY `sales_categories_type_index` (`type`),
  ADD KEY `sales_categories_validation_index` (`validation`),
  ADD KEY `sales_categories_created_at_index` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_company_email_unique` (`company_email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_status_index` (`status`),
  ADD KEY `users_created_at_index` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `agent_codes`
--
ALTER TABLE `agent_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asm_names`
--
ALTER TABLE `asm_names`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asm_per_areas`
--
ALTER TABLE `asm_per_areas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispatch_remarks`
--
ALTER TABLE `dispatch_remarks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispatch_statuses`
--
ALTER TABLE `dispatch_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inhouse_sales`
--
ALTER TABLE `inhouse_sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `installer_tech_data`
--
ALTER TABLE `installer_tech_data`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `installer_tech_team_areas`
--
ALTER TABLE `installer_tech_team_areas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `managers`
--
ALTER TABLE `managers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `msa_partners`
--
ALTER TABLE `msa_partners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `municipalities`
--
ALTER TABLE `municipalities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_agents`
--
ALTER TABLE `sales_agents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_categories`
--
ALTER TABLE `sales_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admins_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `agent_codes`
--
ALTER TABLE `agent_codes`
  ADD CONSTRAINT `fk_agent_codes_agent` FOREIGN KEY (`sales_agent_id`) REFERENCES `sales_agents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_agent_codes_category` FOREIGN KEY (`sales_category_id`) REFERENCES `sales_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inhouse_sales`
--
ALTER TABLE `inhouse_sales`
  ADD CONSTRAINT `fk_inhouse_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `managers`
--
ALTER TABLE `managers`
  ADD CONSTRAINT `fk_managers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `msa_partners`
--
ALTER TABLE `msa_partners`
  ADD CONSTRAINT `fk_msa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `municipalities`
--
ALTER TABLE `municipalities`
  ADD CONSTRAINT `fk_municipalities_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provinces`
--
ALTER TABLE `provinces`
  ADD CONSTRAINT `fk_provinces_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sales_agents`
--
ALTER TABLE `sales_agents`
  ADD CONSTRAINT `fk_sales_agents_category` FOREIGN KEY (`sales_category_id`) REFERENCES `sales_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
