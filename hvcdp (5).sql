-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 25, 2024 at 11:18 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hvcdp`
--

-- --------------------------------------------------------

--
-- Table structure for table `affiliations`
--

CREATE TABLE `affiliations` (
  `id` bigint UNSIGNED NOT NULL,
  `name_of_association` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_of_barangay` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `affiliations`
--

INSERT INTO `affiliations` (`id`, `name_of_association`, `name_of_barangay`, `created_at`, `updated_at`) VALUES
(1, 'Association 1', 'Divisoria', '2024-11-22 23:41:54', '2024-11-22 23:41:54'),
(2, 'Association 7', 'Esperanza', '2024-11-23 18:42:23', '2024-11-23 18:42:23'),
(3, 'Association 1', 'Cancamares', '2024-11-23 18:50:01', '2024-11-23 18:50:01'),
(6, NULL, 'Himakilo', '2024-11-23 20:22:54', '2024-11-23 20:22:54'),
(7, NULL, 'Paku', '2024-11-23 20:26:36', '2024-11-23 20:26:36'),
(8, 'Association 1', 'Paku', '2024-11-23 20:40:00', '2024-11-23 20:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` bigint UNSIGNED NOT NULL,
  `affiliation_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `control_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthdate` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `added_by` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `affiliation_id`, `first_name`, `last_name`, `middle_name`, `extension`, `control_number`, `birthdate`, `created_at`, `updated_at`, `added_by`, `user_id`) VALUES
(1, 1, 'GRACE', 'SIEGA', NULL, NULL, '08-64-02-037-000001', '2002-10-28', '2024-11-22 23:41:54', '2024-11-23 20:02:38', 1, 2),
(2, 2, 'SANDRA', 'WABINA', 'BAUL', NULL, '08-64-02-037-000002', '2002-10-28', '2024-11-23 18:42:23', '2024-11-23 18:58:37', 1, 3),
(12, 7, 'FELVERs', 'SY', NULL, NULL, '08-64-02-037-000003', '2009-10-28', '2024-11-23 20:39:21', '2024-11-23 21:33:24', 4, NULL),
(13, 8, 'JAYSSS', 'SY', NULL, NULL, '08-64-02-037-000004', '2009-10-10', '2024-11-23 20:41:24', '2024-11-23 20:41:24', 4, 7);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_valued_crops`
--

CREATE TABLE `inventory_valued_crops` (
  `id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `plant_id` bigint UNSIGNED NOT NULL,
  `count` int NOT NULL,
  `added_by` bigint UNSIGNED NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_valued_crops`
--

INSERT INTO `inventory_valued_crops` (`id`, `farmer_id`, `plant_id`, `count`, `added_by`, `latitude`, `longitude`, `image_path`, `created_at`, `updated_at`) VALUES
(10, 1, 1, 3, 1, '0.15668400', '51.52032100', 'images/68dHFTl8Z57faKp4Vq1EKL2UU6xQpsBmkVo15tt7.png', '2024-11-24 18:14:26', '2024-11-24 18:14:26'),
(14, 2, 1, 99, 1, '10.24233720', '124.98119480', 'images/XL5h8vX4hHrXmJpbs1v7epKhxCYn6thqQ6c3P3cz.jpg', '2024-11-25 02:35:08', '2024-11-25 02:35:08'),
(15, 12, 1, 777, 4, '10.24233720', '124.98119480', 'images/9yiPkasj3Jdjoqk0ugm3xzAJRK2BKHczE9bVrNm9.jpg', '2024-11-25 02:42:40', '2024-11-25 02:42:40'),
(16, 13, 1, 888, 4, '0.15668400', '51.52032100', 'images/waJoQ9f2v6aQEGCgfiscY79I6YxL4zFVT8Rs1Kt6.png', '2024-11-25 02:43:59', '2024-11-25 02:43:59'),
(17, 2, 1, 123, 3, '10.24233730', '124.98119480', 'images/IM07TW0jk8HtP26joXY9nxNZQRqQUa4bxNXoUZRH.jpg', '2024-11-25 02:49:10', '2024-11-25 02:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_07_24_100000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_08_24_024057_create_roles_table', 1),
(7, '2024_08_27_023313_add_role_id_to_users_table', 1),
(8, '2024_08_29_112531_create_farmers_table', 1),
(9, '2024_08_29_113519_create_affiliations_table', 1),
(10, '2024_08_29_113722_add_affiliation_id_foreign_key_to_farmers_table', 1),
(11, '2024_08_29_115356_create_plants_table', 1),
(12, '2024_09_10_024918_add_affiliation_to_users_table', 1),
(13, '2024_09_30_122021_create_inventory_valued_crops_table', 1),
(14, '2024_10_08_113048_create_plant_varieties_table', 1),
(15, '2024_10_12_135813_add_added_by_to_farmers_table', 1),
(16, '2024_10_13_014624_add_user_id_to_farmers_table', 1),
(17, '2024_10_20_133725_create_monthly_inventories_table', 1),
(18, '2024_10_25_110846_add_added_by_to_inventory_valued_crops_table', 1),
(19, '2024_11_13_223713_create_monthly_records_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_inventories`
--

CREATE TABLE `monthly_inventories` (
  `id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `plant_id` bigint UNSIGNED NOT NULL,
  `affiliation_id` bigint UNSIGNED NOT NULL,
  `planting_density` decimal(8,2) NOT NULL,
  `production_volume` decimal(8,2) DEFAULT NULL,
  `newly_planted` int DEFAULT NULL,
  `vegetative` int DEFAULT NULL,
  `reproductive` int DEFAULT NULL,
  `maturity_harvested` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  `newly_planted_divided` decimal(10,4) DEFAULT NULL,
  `vegetative_divided` decimal(10,4) DEFAULT NULL,
  `reproductive_divided` decimal(10,4) DEFAULT NULL,
  `maturity_harvested_divided` decimal(10,4) DEFAULT NULL,
  `total_planted_area` decimal(10,4) DEFAULT NULL,
  `area_harvested` decimal(10,4) DEFAULT NULL,
  `final_production_volume` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monthly_records`
--

CREATE TABLE `monthly_records` (
  `id` bigint UNSIGNED NOT NULL,
  `monthly_inventory_id` bigint UNSIGNED NOT NULL,
  `farmer_id` bigint UNSIGNED NOT NULL,
  `plant_id` bigint UNSIGNED NOT NULL,
  `affiliation_id` bigint UNSIGNED NOT NULL,
  `planting_density` decimal(8,2) DEFAULT NULL,
  `production_volume` decimal(8,2) DEFAULT NULL,
  `newly_planted` int DEFAULT NULL,
  `vegetative` int DEFAULT NULL,
  `reproductive` int DEFAULT NULL,
  `maturity_harvested` int DEFAULT NULL,
  `newly_planted_divided` decimal(8,2) DEFAULT NULL,
  `vegetative_divided` decimal(8,2) DEFAULT NULL,
  `reproductive_divided` decimal(8,2) DEFAULT NULL,
  `maturity_harvested_divided` decimal(8,2) DEFAULT NULL,
  `total_planted_area` decimal(8,2) DEFAULT NULL,
  `total` int DEFAULT NULL,
  `area_harvested` decimal(8,2) DEFAULT NULL,
  `final_production_volume` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

CREATE TABLE `plants` (
  `id` bigint UNSIGNED NOT NULL,
  `name_of_plants` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plants`
--

INSERT INTO `plants` (`id`, `name_of_plants`, `created_at`, `updated_at`) VALUES
(1, 'CHAYOTE', '2024-11-22 23:42:15', '2024-11-22 23:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `plant_varieties`
--

CREATE TABLE `plant_varieties` (
  `id` bigint UNSIGNED NOT NULL,
  `plant_id` bigint UNSIGNED NOT NULL,
  `variety_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plant_varieties`
--

INSERT INTO `plant_varieties` (`id`, `plant_id`, `variety_name`, `created_at`, `updated_at`) VALUES
(1, 1, '--', '2024-11-22 23:42:15', '2024-11-22 23:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2024-11-22 23:41:13', '2024-11-22 23:41:13'),
(2, 'User', '2024-11-22 23:41:13', '2024-11-22 23:41:13'),
(3, 'Aggregator', '2024-11-22 23:41:13', '2024-11-22 23:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `affiliation_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `extension`, `email`, `password`, `affiliation_id`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'ADMINISTRATOR', '', 'Admin', '', 'admin@example.com', '$2y$12$kBtoXfJHoUvM5FFYcwP3Ueupl/NzHGis9EAJ9wvb0aYzjJCqhC2JK', NULL, NULL, '2024-11-22 23:41:14', '2024-11-22 23:41:14', 1),
(2, 'GRACE', NULL, 'SIEGA', NULL, 'GRACE@GMAIL.COM', '$2y$12$Lte5diYzq9ny04QGz1JJvezvcBWdblkIco4NFmKISWJ56twHPfXOq', 1, NULL, '2024-11-22 23:41:54', '2024-11-23 21:36:00', 2),
(3, 'SANDRA', 'BAUL', 'WABINA', NULL, 'wabinasandra@gmail.com', '$2y$12$7QW4AZguuptI.yBTTrwYdeI257OuvfMnR6KFsgtWPkxtbrFj7s8rG', 2, NULL, '2024-11-23 18:58:37', '2024-11-23 18:58:37', 2),
(4, 'MARI JON', NULL, 'HIMAYA', NULL, 'himayamarijon@gmail.com', '$2y$12$TT9Mpefqof9iN17/scPwXO/n.DIh4gv/elTgOjJ5NASBJ6As.nvUa', 3, NULL, '2024-11-23 19:13:05', '2024-11-23 19:13:05', 3),
(7, 'JAYSSS', NULL, 'SY', NULL, 'sandra@gmail.com', '$2y$12$I0GhHXsUQLhkR9pNqEx4WuVRb6fgZBW6XEcZ6re1pLAPRVkGRHiOG', NULL, NULL, '2024-11-23 20:41:24', '2024-11-23 20:41:24', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliations`
--
ALTER TABLE `affiliations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `farmers`
--
ALTER TABLE `farmers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `farmers_control_number_unique` (`control_number`),
  ADD KEY `farmers_affiliation_id_foreign` (`affiliation_id`),
  ADD KEY `farmers_added_by_foreign` (`added_by`),
  ADD KEY `farmers_user_id_foreign` (`user_id`);

--
-- Indexes for table `inventory_valued_crops`
--
ALTER TABLE `inventory_valued_crops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_valued_crops_farmer_id_foreign` (`farmer_id`),
  ADD KEY `inventory_valued_crops_plant_id_foreign` (`plant_id`),
  ADD KEY `inventory_valued_crops_added_by_foreign` (`added_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_inventories`
--
ALTER TABLE `monthly_inventories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monthly_inventories_farmer_id_foreign` (`farmer_id`),
  ADD KEY `monthly_inventories_plant_id_foreign` (`plant_id`),
  ADD KEY `monthly_inventories_affiliation_id_foreign` (`affiliation_id`);

--
-- Indexes for table `monthly_records`
--
ALTER TABLE `monthly_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monthly_records_monthly_inventory_id_foreign` (`monthly_inventory_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plants`
--
ALTER TABLE `plants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plants_name_of_plants_unique` (`name_of_plants`);

--
-- Indexes for table `plant_varieties`
--
ALTER TABLE `plant_varieties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plant_varieties_plant_id_foreign` (`plant_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_role_name_unique` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `users_affiliation_id_foreign` (`affiliation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliations`
--
ALTER TABLE `affiliations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `inventory_valued_crops`
--
ALTER TABLE `inventory_valued_crops`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `monthly_inventories`
--
ALTER TABLE `monthly_inventories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monthly_records`
--
ALTER TABLE `monthly_records`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plants`
--
ALTER TABLE `plants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plant_varieties`
--
ALTER TABLE `plant_varieties`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `farmers`
--
ALTER TABLE `farmers`
  ADD CONSTRAINT `farmers_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `farmers_affiliation_id_foreign` FOREIGN KEY (`affiliation_id`) REFERENCES `affiliations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `farmers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_valued_crops`
--
ALTER TABLE `inventory_valued_crops`
  ADD CONSTRAINT `inventory_valued_crops_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_valued_crops_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_valued_crops_plant_id_foreign` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_inventories`
--
ALTER TABLE `monthly_inventories`
  ADD CONSTRAINT `monthly_inventories_affiliation_id_foreign` FOREIGN KEY (`affiliation_id`) REFERENCES `affiliations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monthly_inventories_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monthly_inventories_plant_id_foreign` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_records`
--
ALTER TABLE `monthly_records`
  ADD CONSTRAINT `monthly_records_monthly_inventory_id_foreign` FOREIGN KEY (`monthly_inventory_id`) REFERENCES `monthly_inventories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plant_varieties`
--
ALTER TABLE `plant_varieties`
  ADD CONSTRAINT `plant_varieties_plant_id_foreign` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_affiliation_id_foreign` FOREIGN KEY (`affiliation_id`) REFERENCES `affiliations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
