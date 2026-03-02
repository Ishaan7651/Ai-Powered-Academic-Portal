-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 12, 2026 at 09:26 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u348045755_test5`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_assignments`
--

CREATE TABLE `ai_assignments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `assignment_type` enum('research','essay','project','case_study','presentation') DEFAULT 'research',
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `word_count` int(11) DEFAULT 500,
  `assignment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`assignment_data`)),
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `due_weeks` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_messages`
--

CREATE TABLE `ai_chat_messages` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `role` enum('user','assistant','system') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_sessions`
--

CREATE TABLE `ai_chat_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `session_name` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_question_papers`
--

CREATE TABLE `ai_question_papers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `total_marks` int(11) DEFAULT 100,
  `duration_minutes` int(11) DEFAULT 180,
  `format_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`format_config`)),
  `paper_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`paper_data`)),
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_quizzes`
--

CREATE TABLE `ai_quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `num_questions` int(11) DEFAULT 10,
  `quiz_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quiz_data`)),
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_usage_logs`
--

CREATE TABLE `ai_usage_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feature_type` enum('chat','quiz','question_paper','summary') NOT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `tokens_used` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_marks` int(11) DEFAULT 100,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'CS', 'Department of Computer Science and Engineering', '2026-02-06 03:50:12', '2026-02-06 03:50:12'),
(2, 'Information Technology', 'IT', 'Department of Information Technology', '2026-02-06 03:50:12', '2026-02-06 03:50:12'),
(3, 'Electronics', 'EC', 'Department of Electronics and Communication', '2026-02-06 03:50:12', '2026-02-06 03:50:12'),
(4, 'Mechanical', 'ME', 'Department of Mechanical Engineering', '2026-02-06 03:50:12', '2026-02-06 03:50:12'),
(5, 'Civil', 'CE', 'Department of Civil Engineering', '2026-02-06 03:50:12', '2026-02-06 03:50:12'),
(6, 'Psychology', 'PSY', '', '2026-02-06 03:56:31', '2026-02-06 03:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `user_id`, `department_id`, `employee_id`, `department`) VALUES
(1, 2, 1, 'FAC001', 'Computer Science'),
(2, 4, NULL, NULL, NULL),
(3, 6, 6, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_subjects`
--

CREATE TABLE `faculty_subjects` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `faculty_subjects`
--

INSERT INTO `faculty_subjects` (`id`, `faculty_id`, `subject_id`, `assigned_at`) VALUES
(1, 1, 1, '2026-02-06 03:50:12'),
(2, 1, 4, '2026-02-06 03:50:12'),
(3, 1, 6, '2026-02-06 03:50:12'),
(4, 3, 7, '2026-02-06 09:56:45'),
(5, 2, 4, '2026-02-06 10:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(5);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `correct_answers` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `time_taken` int(11) NOT NULL,
  `attempted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT 0,
  `original_filename` varchar(255) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `semester` int(11) DEFAULT 1,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `faculty_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `title`, `description`, `file_path`, `file_type`, `file_size`, `original_filename`, `subject_id`, `semester`, `uploaded_by`, `created_at`, `is_active`, `faculty_id`) VALUES
(1, 'Clinical Pychology Reference Book', 'Reference Book', 'uploads/resources/resource_1770719392_698b08a0862e1.pdf', 'pdf', 3263, 'James E. Maddux, Barbara A. Winstead (eds.)-Psychopathology_ Foundations for a Contemporary Understanding-Routledge (2016).pdf', 7, 4, 6, '2026-02-10 10:29:52', 1, NULL),
(2, 'DSM 5 Explainer Book', '', 'uploads/resources/resource_1770719510_698b0916dfc73.pdf', 'pdf', 3579, 'DSM 5 Made Eaasy.pdf', 7, 4, 6, '2026-02-10 10:31:50', 1, NULL),
(3, 'Personality Disorders Reference Book', '', 'uploads/resources/resource_1770719708_698b09dc96b9f.pdf', 'pdf', 2553, 'Personality Disorders - Mario Maj.pdf', 7, 4, 6, '2026-02-10 10:35:08', 1, NULL),
(4, 'DSM 5 TR Original text', '', 'uploads/resources/resource_1770719761_698b0a119b06f.pdf', 'pdf', 8666, 'DSM 5 TR.pdf', 7, 4, 6, '2026-02-10 10:36:01', 1, NULL),
(5, 'Substance Abuse Prevention, Treatment and Recovery', '', 'uploads/resources/resource_1770720421_698b0ca59226c.pdf', 'pdf', 18425, 'Substance Abuse Prevention,Treatment And Recovery.pdf', 7, 4, 6, '2026-02-10 10:47:01', 1, NULL),
(6, 'Module 1 Notes: Introduction to Personality and Personality Disorders', '', 'uploads/resources/resource_1770770053_698bce85ac337.pdf', 'pdf', 167, '1.1_SLABSPSY6.02005_Clinical_Psychology_–_II__Module_1_-_Introduction_to_Personality_and_personality_Disorders.pdf', 7, 4, 6, '2026-02-11 00:34:13', 1, NULL),
(7, 'Module 1 Notes: Cluster A Personality Disorders', '', 'uploads/resources/resource_1770770092_698bceac8e645.pdf', 'pdf', 199, 'SLABSPSY6.02005 Clinical Psychology – II  Module 1 - Cluster A Disorders.pdf', 7, 4, 6, '2026-02-11 00:34:52', 1, NULL),
(8, 'Module 1 Notes: Cluster B Personality Disorders', '', 'uploads/resources/resource_1770770121_698bcec9c37a2.pdf', 'pdf', 173, 'SLABSPSY6.02005 Clinical Psychology – II  Module 1 - Cluster B Disorders.pdf', 7, 4, 6, '2026-02-11 00:35:21', 1, NULL),
(9, 'Module 1 Notes: Cluster C Personality Disorders', '', 'uploads/resources/resource_1770770147_698bcee3467a6.pdf', 'pdf', 208, 'SLABSPSY6.02005 Clinical Psychology – II  Module 1 -  Cluster C Disorders.pdf', 7, 4, 6, '2026-02-11 00:35:47', 1, NULL),
(10, 'Module 2: Trauma & Stress related Disorders and Somatoform Disorders', '', 'uploads/resources/resource_1770770185_698bcf094dd10.pdf', 'pdf', 229, 'Module 2 Trauma & Stress related Disorders and Somatoform Disorders.pdf', 7, 4, 6, '2026-02-11 00:36:25', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `current_semester` int(11) NOT NULL,
  `enrollment_year` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_id`, `current_semester`, `enrollment_year`) VALUES
(1, 3, 'STU001', 2, '2025'),
(2, 5, NULL, 8, '2026'),
(4, 8, NULL, 4, '2024'),
(5, 9, NULL, 4, '2024'),
(6, 10, NULL, 4, '2024'),
(7, 11, NULL, 4, '2024');

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `enrolled_at` timestamp NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `student_id`, `subject_id`, `enrolled_at`, `is_active`) VALUES
(1, 3, 1, '2026-02-06 03:50:12', 1),
(2, 3, 4, '2026-02-06 03:50:12', 1),
(3, 3, 6, '2026-02-06 03:50:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `semester` int(11) NOT NULL,
  `credits` int(11) DEFAULT 3,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `semester`, `credits`, `created_at`, `is_active`) VALUES
(1, 'MATH101', 'Mathematics', 1, 3, '2026-02-06 03:50:12', 1),
(2, 'PHYS101', 'Physics', 1, 3, '2026-02-06 03:50:12', 1),
(3, 'CHEM101', 'Chemistry', 3, 3, '2026-02-06 03:50:12', 1),
(4, 'CS101', 'Computer Science', 1, 4, '2026-02-06 03:50:12', 1),
(5, 'ENG101', 'English', 1, 3, '2026-02-06 03:50:12', 1),
(6, 'DL002', 'Deep Learning', 2, 3, '2026-02-06 03:50:12', 1),
(7, 'SLABSPSY6.02005', 'Clinical Psychology', 4, 2, '2026-02-06 09:53:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','faculty','student') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `updated_at`, `last_login`, `is_active`) VALUES
(1, 'admin', 'admin@college.edu', '$2y$12$wkc.02EbqNi7o5oPGibvxOCpYwfdoDHhJN/yZzw2h7pNdXuziUIAu', 'admin', '2026-02-06 03:50:12', '2026-02-06 03:50:12', NULL, 1),
(2, 'faculty_demo', 'faculty@college.edu', '$2y$12$wSL2BPre8mMcAI5qycnHMeiSO6ty93ULVhu1k3gQaStZcXl07SFou', 'faculty', '2026-02-06 03:50:12', '2026-02-06 03:50:12', NULL, 1),
(3, 'student_demo', 'student@college.edu', '$2y$12$HBZBwEkqSdgc9Gi.tGR/cuiYTN8qfZk.mN8GMBOHLBqYJWsVnOtTe', 'student', '2026-02-06 03:50:12', '2026-02-06 03:50:12', NULL, 1),
(4, 'ftest1', 'ftest1@test.com', '$2y$10$2g8SFnpFQyTP06TWpXZ0BOQQEouB1griR1.JCaCiixNCeVKOcXtxS', 'faculty', '2026-02-06 03:52:09', '2026-02-06 03:52:09', NULL, 1),
(5, 'stest1', 'stest1@test.com', '$2y$10$LAa7W2o8KbhoFUbMNGvJkermBbGxgmjLnQQcSmr7KmNaku7jAq8BO', 'student', '2026-02-06 03:52:37', '2026-02-06 03:52:37', NULL, 1),
(6, 'psychology', 'psychology@uai.com', '$2y$10$HlWeg7CiRitgbi/53v8LBOwkYzTOzLqcruvehDf/JpKQQ2GVOt70u', 'faculty', '2026-02-06 03:57:35', '2026-02-06 03:57:35', NULL, 1),
(7, 'Arshia.Agicha', 'arshia.agicha@universalai.in', '$2y$10$LYqrLeWvM.X9gmgPm7yiluedXb5AQmJtjw5xGtBtNriGLvwYyCv3e', 'student', '2026-02-06 10:56:58', '2026-02-06 10:56:58', NULL, 1),
(8, 'Diya.Kakde', 'diya.kakde@universalai.in', '$2y$10$Dj.mHZeKy7ygyp7e/y8NJ.gmKpkH3jgoYOPv24P98nQWFE7SUWQOa', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1),
(9, 'Pari.Hamirwasia', 'pari.hamirwasia@universalai.in', '$2y$10$CP5o6NT/Ht2IQZdbERnaQOZTrdAXFUVQLnVhG2f5ag02rYASdQ4Qu', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1),
(10, 'Sayali.Dhumal', 'taqee.shaikh@universalai.in', '$2y$10$/gX3ljI60a3fIXCA6tGCBurPoTdkQPn22nSBxqI.H7skz0bENh3m.', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1),
(11, 'Taqee.Shaikh', 'sayali.dhumal@universalai.in', '$2y$10$D2Wn0ObtVsPBIm4DzS4HO.qC0Yf2kSG1dQHCQP37hH.YZYxD9P.hq', 'student', '2026-02-06 10:57:37', '2026-02-06 10:57:37', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_assignments`
--
ALTER TABLE `ai_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_published` (`is_published`);

--
-- Indexes for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `ai_question_papers`
--
ALTER TABLE `ai_question_papers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_id` (`resource_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_published` (`is_published`);

--
-- Indexes for table `ai_quizzes`
--
ALTER TABLE `ai_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_resource` (`resource_id`),
  ADD KEY `idx_subject_id` (`subject_id`),
  ADD KEY `idx_published` (`is_published`);

--
-- Indexes for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_id` (`resource_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_feature` (`feature_type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `fk_assignments_faculty` (`faculty_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `fk_faculty_user` (`user_id`),
  ADD KEY `fk_faculty_department` (`department_id`);

--
-- Indexes for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`faculty_id`,`subject_id`),
  ADD KEY `idx_faculty` (`faculty_id`),
  ADD KEY `idx_subject` (`subject_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_quiz` (`quiz_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_uploader` (`uploaded_by`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_semester` (`current_semester`),
  ADD KEY `fk_student_user` (`user_id`);

--
-- Indexes for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`subject_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_subject` (`subject_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `idx_subject_code` (`subject_code`),
  ADD KEY `idx_semester` (`semester`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_active` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_assignments`
--
ALTER TABLE `ai_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_question_papers`
--
ALTER TABLE `ai_question_papers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_quizzes`
--
ALTER TABLE `ai_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_assignments`
--
ALTER TABLE `ai_assignments`
  ADD CONSTRAINT `ai_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_assignments_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_assignments_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_chat_messages`
--
ALTER TABLE `ai_chat_messages`
  ADD CONSTRAINT `ai_chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `ai_chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD CONSTRAINT `ai_chat_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_chat_sessions_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_question_papers`
--
ALTER TABLE `ai_question_papers`
  ADD CONSTRAINT `ai_question_papers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_question_papers_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ai_question_papers_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_quizzes`
--
ALTER TABLE `ai_quizzes`
  ADD CONSTRAINT `ai_quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_quizzes_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_usage_logs`
--
ALTER TABLE `ai_usage_logs`
  ADD CONSTRAINT `ai_usage_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_usage_logs_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assignments_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`),
  ADD CONSTRAINT `fk_assignments_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_faculty_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_faculty_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD CONSTRAINT `faculty_subjects_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faculty_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `ai_quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resources_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD CONSTRAINT `student_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
