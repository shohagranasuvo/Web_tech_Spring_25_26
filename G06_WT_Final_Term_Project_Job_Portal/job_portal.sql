-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 08:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `seeker_id` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume_path` varchar(500) DEFAULT NULL COMMENT 'Path to uploaded resume for this application',
  `status` enum('Submitted','Reviewed','Shortlisted','Rejected','Accepted') NOT NULL DEFAULT 'Submitted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Technology', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(2, 'Healthcare', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(3, 'Finance', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(4, 'Marketing', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(5, 'Sales', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(6, 'Education', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(7, 'Engineering', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(8, 'Design', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(9, 'Human Resources', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(10, 'Customer Service', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(11, 'Administration', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(12, 'Legal', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(13, 'Manufacturing', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(14, 'Retail', '2026-05-12 18:26:44', '2026-05-12 18:26:44'),
(15, 'Hospitality', '2026-05-12 18:26:44', '2026-05-12 18:26:44');

-- --------------------------------------------------------

--
-- Table structure for table `employer_profiles`
--

CREATE TABLE `employer_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `salary_range` varchar(100) DEFAULT NULL COMMENT 'e.g., $50,000 - $70,000',
  `location` varchar(255) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Remote','Contract','Internship') NOT NULL DEFAULT 'Full-time',
  `deadline` date DEFAULT NULL,
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seeker_profiles`
--

CREATE TABLE `seeker_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `headline` varchar(500) DEFAULT NULL,
  `skills` text DEFAULT NULL COMMENT 'Comma-separated skills or JSON array',
  `years_experience` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('employer','seeker') NOT NULL,
  `file_path` varchar(500) DEFAULT NULL COMMENT 'Logo for employers, resume for seekers',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_active_jobs`
-- (See below for the actual view)
--
CREATE TABLE `view_active_jobs` (
`id` int(11)
,`title` varchar(255)
,`description` text
,`salary_range` varchar(100)
,`location` varchar(255)
,`job_type` enum('Full-time','Part-time','Remote','Contract','Internship')
,`deadline` date
,`created_at` timestamp
,`employer_name` varchar(255)
,`company_name` varchar(255)
,`category_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_applications`
-- (See below for the actual view)
--
CREATE TABLE `view_applications` (
`id` int(11)
,`job_id` int(11)
,`seeker_id` int(11)
,`status` enum('Submitted','Reviewed','Shortlisted','Rejected','Accepted')
,`created_at` timestamp
,`job_title` varchar(255)
,`seeker_name` varchar(255)
,`seeker_email` varchar(255)
,`seeker_headline` varchar(500)
,`years_experience` int(11)
,`employer_name` varchar(255)
,`company_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `view_active_jobs`
--
DROP TABLE IF EXISTS `view_active_jobs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_active_jobs`  AS SELECT `j`.`id` AS `id`, `j`.`title` AS `title`, `j`.`description` AS `description`, `j`.`salary_range` AS `salary_range`, `j`.`location` AS `location`, `j`.`job_type` AS `job_type`, `j`.`deadline` AS `deadline`, `j`.`created_at` AS `created_at`, `u`.`name` AS `employer_name`, `ep`.`company_name` AS `company_name`, `c`.`name` AS `category_name` FROM (((`jobs` `j` join `users` `u` on(`j`.`employer_id` = `u`.`id`)) left join `employer_profiles` `ep` on(`u`.`id` = `ep`.`user_id`)) left join `categories` `c` on(`j`.`category_id` = `c`.`id`)) WHERE `j`.`status` = 'active' ORDER BY `j`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_applications`
--
DROP TABLE IF EXISTS `view_applications`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_applications`  AS SELECT `a`.`id` AS `id`, `a`.`job_id` AS `job_id`, `a`.`seeker_id` AS `seeker_id`, `a`.`status` AS `status`, `a`.`created_at` AS `created_at`, `j`.`title` AS `job_title`, `u`.`name` AS `seeker_name`, `u`.`email` AS `seeker_email`, `sp`.`headline` AS `seeker_headline`, `sp`.`years_experience` AS `years_experience`, `emp`.`name` AS `employer_name`, `ep`.`company_name` AS `company_name` FROM (((((`applications` `a` join `jobs` `j` on(`a`.`job_id` = `j`.`id`)) join `users` `u` on(`a`.`seeker_id` = `u`.`id`)) left join `seeker_profiles` `sp` on(`u`.`id` = `sp`.`user_id`)) join `users` `emp` on(`j`.`employer_id` = `emp`.`id`)) left join `employer_profiles` `ep` on(`emp`.`id` = `ep`.`user_id`)) ORDER BY `a`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`job_id`,`seeker_id`) COMMENT 'Prevent duplicate applications',
  ADD KEY `idx_job` (`job_id`),
  ADD KEY `idx_seeker` (`seeker_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_applications_seeker_status` (`seeker_id`,`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `employer_profiles`
--
ALTER TABLE `employer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`),
  ADD KEY `idx_company_name` (`company_name`),
  ADD KEY `idx_industry` (`industry`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employer` (`employer_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_job_type` (`job_type`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_jobs_employer_status` (`employer_id`,`status`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_saved_job` (`user_id`,`job_id`) COMMENT 'Prevent duplicate saves',
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_job` (`job_id`);

--
-- Indexes for table `seeker_profiles`
--
ALTER TABLE `seeker_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`),
  ADD KEY `idx_years_experience` (`years_experience`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employer_profiles`
--
ALTER TABLE `employer_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seeker_profiles`
--
ALTER TABLE `seeker_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`seeker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employer_profiles`
--
ALTER TABLE `employer_profiles`
  ADD CONSTRAINT `employer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seeker_profiles`
--
ALTER TABLE `seeker_profiles`
  ADD CONSTRAINT `seeker_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
