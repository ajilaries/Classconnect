-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 07:17 AM
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
-- Database: `classconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `college_id` int(11) DEFAULT NULL,
  `batch_name` varchar(255) NOT NULL,
  `class_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `department_id`, `college_id`, `batch_name`, `class_code`) VALUES
(9, 1, NULL, 'BCA-26', 'A4B729');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `class_code` varchar(50) NOT NULL,
  `college_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `class_code`, `college_id`, `created_at`) VALUES
(7, 'Computer Science', 'CS2023@BVM', 4, '2025-09-02 09:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `classfeed`
--

CREATE TABLE `classfeed` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_type` varchar(50) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `college_id` int(11) NOT NULL DEFAULT 1,
  `batch_id` int(11) NOT NULL,
  `class_code` varchar(50) NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classfeed`
--

INSERT INTO `classfeed` (`id`, `user_id`, `post_type`, `subject`, `message`, `file_path`, `created_at`, `college_id`, `batch_id`, `class_code`) VALUES
(75, 92, 'announcement', 'Computer Networks', 'There will be online classes today @ 7:30 pm', NULL, '2025-11-01 03:56:36', 4, 9, 'UNKNOWN');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) NOT NULL,
  `college_code` varchar(50) NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_code`, `college_name`, `address`, `contact_email`, `contact_phone`, `created_at`) VALUES
(4, 'BVMHCC@20', 'BVM HCC', 'cherpunkal po', 'bvmhcc@gmail.com', '2242342242', '2025-09-02 09:08:20');

-- --------------------------------------------------------

--
-- Table structure for table `deadlines`
--

CREATE TABLE `deadlines` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `deadline` datetime NOT NULL,
  `type_number_id` int(11) DEFAULT NULL,
  `batch_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `college_id`, `department_name`, `created_at`) VALUES
(1, 4, 'Computer Science', '2025-09-03 04:34:55'),
(2, 4, 'commerce', '2025-09-03 04:45:33'),
(6, 4, 'Social Work', '2025-10-31 22:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General',
  `attachment` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `visibility` enum('global','department') DEFAULT 'global',
  `department_id` int(11) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `category`, `attachment`, `created_by`, `created_at`, `visibility`, `department_id`, `college_id`, `batch_id`) VALUES
(1, 'newevents', 'here is the events that are for the computer department students', '2025-09-16', '10:00:00', 'General', NULL, 53, '2025-09-14 04:25:52', '', 1, 4, NULL),
(2, 'newfiless', 'gagaa', '2025-09-16', '10:57:00', 'General', NULL, 53, '2025-09-14 04:26:10', '', 1, 4, NULL),
(3, 'bgssg', 'gwg', '2025-09-22', '00:00:00', 'General', NULL, 53, '2025-09-14 04:30:52', '', 1, NULL, NULL),
(4, 'hereus', 'hey gud mrng dear', '2025-09-03', '10:00:00', 'General', NULL, 53, '2025-09-14 06:11:22', '', NULL, NULL, NULL),
(5, 'heloo', 'here is the demmo file for this', '2025-08-07', '10:00:00', 'General', NULL, 53, '2025-09-14 06:18:16', 'department', NULL, NULL, NULL),
(6, 'gsf', 'gsgsg', '2025-09-17', '23:03:00', 'General', NULL, 53, '2025-09-14 06:25:05', '', NULL, 4, 2),
(7, 'ereere', 'qqrqrqtqt', '2025-09-16', '13:15:00', 'General', NULL, 53, '2025-09-14 06:45:08', '', 1, 4, 1),
(8, 'ggdsf', 'bebege', '2025-09-05', '03:03:00', 'General', NULL, 53, '2025-09-14 07:17:05', '', 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `anonymous` tinyint(1) DEFAULT 0,
  `rating` int(11) DEFAULT 0,
  `reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `feedback_text` text NOT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `batch_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `category`, `comment`, `anonymous`, `rating`, `reply`, `created_at`, `feedback_text`, `is_anonymous`, `batch_id`, `subject`, `teacher_id`) VALUES
(1, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:04:12', 'here is the feedback section made for classconnect', 0, 0, '', 0),
(2, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:04:52', 'here is the feedback section made for classconnect', 0, 0, '', 0),
(3, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:05:20', 'reaa', 0, 0, '', 0),
(4, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:07:47', 'eraraer', 0, 0, '', 0),
(5, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:13:33', 'eraraer', 0, 0, '', 0),
(6, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:17:58', 'wrwrwr', 0, 0, '', 0),
(7, NULL, 'other', '', 0, 5, NULL, '2025-10-01 07:38:42', 'hkgafgakgakgg', 1, 0, '', 0),
(8, NULL, 'other', '', 0, 5, NULL, '2025-10-01 07:47:58', 'hhhdgdgb', 1, 0, 'Computer Networks', 92),
(9, 95, 'other', '', 0, 5, NULL, '2025-10-01 07:51:22', 'yrttw', 0, 0, 'Computer Networks', 92),
(10, NULL, 'interaction', '', 0, 5, NULL, '2025-10-03 07:11:06', 'here is the feedback for', 1, 0, 'Computer Networks', 92);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_type` enum('material','assignment','other') DEFAULT 'material',
  `subject` varchar(100) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `register_no` varchar(50) DEFAULT NULL,
  `admission_no` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `status` enum('On Time','Late') DEFAULT 'On Time',
  `college_id` int(11) NOT NULL DEFAULT 1,
  `batch_id` int(11) NOT NULL,
  `class_code` varchar(50) NOT NULL DEFAULT 'UNKNOWN',
  `file_type_number` int(11) DEFAULT NULL,
  `file_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `file_name`, `title`, `file_path`, `uploaded_at`, `file_type`, `subject`, `subject_id`, `register_no`, `admission_no`, `comment`, `user_id`, `deadline`, `status`, `college_id`, `batch_id`, `class_code`, `file_type_number`, `file_type_id`) VALUES
(39, 'computernetworksassignment', 'Assignment 1', '0', '2025-09-30 07:48:59', '', 'Computer Networks', NULL, NULL, NULL, NULL, 95, NULL, 'On Time', 4, 9, 'UNKNOWN', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `file_categories`
--

CREATE TABLE `file_categories` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_types`
--

CREATE TABLE `file_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_types`
--

INSERT INTO `file_types` (`id`, `type_name`) VALUES
(18, 'Assignment'),
(19, 'Seminar');

-- --------------------------------------------------------

--
-- Table structure for table `file_type_numbers`
--

CREATE TABLE `file_type_numbers` (
  `id` int(11) NOT NULL,
  `file_type_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `type_label` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_type_numbers`
--

INSERT INTO `file_type_numbers` (`id`, `file_type_id`, `batch_id`, `type_label`, `created_at`) VALUES
(27, 18, 9, 'Assignment 1', '2025-09-30 07:41:17'),
(28, 18, 9, 'Assignment 2', '2025-10-03 06:32:04'),
(29, 19, 9, 'Seminar1', '2025-10-03 06:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `exam_type` varchar(100) NOT NULL,
  `marks_obtained` decimal(5,2) NOT NULL,
  `max_marks` decimal(5,2) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `teacher_id`, `batch_id`, `subject`, `exam_type`, `marks_obtained`, `max_marks`, `uploaded_at`) VALUES
(4, 95, 92, 9, 'Computer Networks', 'Model EXM', 45.00, 50.00, '2025-10-03 06:30:01'),
(5, 96, 92, 9, 'Computer Networks', 'Model EXM', 46.00, 50.00, '2025-10-03 06:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uploaded_by` varchar(50) DEFAULT NULL,
  `creator_id` int(11) NOT NULL,
  `target_role` enum('student','admin') DEFAULT 'student',
  `target_user_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `classfeed_id` int(11) DEFAULT NULL,
  `college_id` int(11) NOT NULL DEFAULT 1,
  `batch_id` int(11) NOT NULL,
  `class_code` varchar(50) NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `post_id`, `message`, `file_path`, `created_at`, `uploaded_by`, `creator_id`, `target_role`, `target_user_id`, `is_read`, `classfeed_id`, `college_id`, `batch_id`, `class_code`) VALUES
(89, 75, '📢 Jaise Jose uploaded a new announcement in Computer Networks', NULL, '2025-11-01 03:56:36', 'Jaise Jose', 0, 'student', NULL, 0, NULL, 4, 9, 'UNKNOWN');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `options` text NOT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `is_multiple_choice` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `college_id` int(11) NOT NULL DEFAULT 1,
  `batch_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`id`, `question`, `options`, `is_anonymous`, `is_multiple_choice`, `created_at`, `expires_at`, `college_id`, `batch_id`, `created_by`, `status`) VALUES
(15, 'Today class?', '[\"Yesh you can\",\"NO you cant\"]', 0, 0, '2025-10-01 14:22:24', '2025-10-01 14:32:24', 4, 9, 92, 1),
(16, 'Tour Participation?', '[\"Yes i will\",\"No i wont \"]', 0, 1, '2025-11-01 04:01:24', '2025-11-01 04:11:24', 4, 9, 92, 1),
(17, 'Tour Participation?', '[\"Yes i will\",\"No i wont \"]', 0, 1, '2025-11-01 04:46:16', '2025-11-01 04:56:16', 4, 9, 92, 1);

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_options`
--

INSERT INTO `poll_options` (`id`, `poll_id`, `option_text`, `position`) VALUES
(11, 15, 'Yesh you can', 0),
(12, 15, 'NO you cant', 0),
(13, 16, 'Yes i will', 0),
(14, 16, 'No i wont ', 0),
(15, 17, 'Yes i will', 0),
(16, 17, 'No i wont ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_votes`
--

INSERT INTO `poll_votes` (`id`, `poll_id`, `option_id`, `user_id`, `voted_at`) VALUES
(2, 15, 11, '95', '2025-10-01 08:52:49'),
(3, 17, 15, '95', '2025-10-31 23:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `question_papers`
--

CREATE TABLE `question_papers` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `allocation_id` int(11) DEFAULT NULL,
  `college_id` int(11) NOT NULL DEFAULT 1,
  `class_code` varchar(50) NOT NULL DEFAULT 'UNKNOWN',
  `uploaded_by` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_papers`
--

INSERT INTO `question_papers` (`id`, `title`, `subject`, `file_path`, `uploaded_at`, `allocation_id`, `college_id`, `class_code`, `uploaded_by`, `batch_id`) VALUES
(32, 'newquestion paper', '', 'uploads/question_papers/1759324633_ClassConnectDFD2Admin.drawio.pdf', '2025-10-01 13:17:13', 29, 4, 'UNKNOWN', 92, 9),
(33, 'newquestion paper', '', 'uploads/question_papers/1759324757_ClassConnectDFD2Admin.drawio.pdf', '2025-10-01 13:19:17', 29, 4, 'UNKNOWN', 92, 9),
(34, 'previousyear2023', '', 'uploads/question_papers/1761950053_ClassConnectDFD2Teacher.drawio.pdf', '2025-10-31 22:34:13', 29, 4, 'UNKNOWN', 92, 9);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_allocations`
--

CREATE TABLE `teacher_allocations` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `class_code` varchar(50) NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_allocations`
--

INSERT INTO `teacher_allocations` (`id`, `teacher_id`, `department_id`, `batch_id`, `subject`, `class_code`) VALUES
(29, 92, 1, 9, 'Computer Networks', 'UNKNOWN');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_by_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `college_id` int(11) NOT NULL DEFAULT 1,
  `batch_id` int(11) NOT NULL,
  `class_code` varchar(50) NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `filename`, `file_path`, `uploaded_by`, `uploaded_by_name`, `uploaded_at`, `college_id`, `batch_id`, `class_code`) VALUES
(27, 'InternalExamination', 'uploads/timetables/tt_6905382bba33e7.77416634.jpg', 92, 'Jaise Jose', '2025-10-31 22:28:59', 4, 9, 'UNKNOWN');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type_id` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `admission_no` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `register_no` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','student','teacher','admin') DEFAULT 'student',
  `subject` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `college_code` varchar(50) NOT NULL,
  `college_id` int(11) DEFAULT NULL,
  `class_code` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `admission_no`, `email`, `register_no`, `dob`, `course`, `password`, `role`, `subject`, `created_at`, `first_name`, `last_name`, `college_code`, `college_id`, `class_code`, `department_id`, `batch_id`) VALUES
(50, NULL, 'godofclassconnect@college.com', NULL, NULL, NULL, '$2y$10$C0RkBz131pConFT2M9K1V.xZiNbh/vePI0YkX2vsPD8QKKlwSIEfO', 'super_admin', NULL, '2025-09-02 16:11:19', 'Admin', 'God', '', NULL, NULL, NULL, NULL),
(61, 'B-8978', 'alfinmathewbabu@gmail.com', '5454', '2025-09-01', 'BCA', '$2y$10$mtFpyVp9juEGBLpPh/xNFOuMoVYYHRDhjJZzexiomF8bARE/Dt6ta', 'student', NULL, '2025-09-04 04:40:15', 'Alfin', 'Babu', '0', 4, NULL, 1, 2),
(70, 'A-9090', 'anandhuanil@gmail.com', '5757', '2025-09-01', 'BCA', '$2y$10$hKrMoZ6Y297klVxX9qUdauIdv2WsHhQFaWZBuHNk.eJgT.d9kBG2u', 'student', NULL, '2025-09-04 08:06:45', 'Anandhu', 'Anil', '0', 4, NULL, 1, 1),
(92, NULL, 'jaisejose@gmail.com', NULL, NULL, NULL, '$2y$10$em9dUZgocmyWpO0dn2CqD.3Tfj72AsDCtaL.vdjYNvZS6ex2EYfJa', 'teacher', NULL, '2025-09-29 11:50:24', 'Jaise', 'Jose', '', 4, NULL, 1, NULL),
(95, 'A-6467', 'ajilsaji@gmail.com', '5452252525', '2025-09-16', 'BCA', '$2y$10$8IZlxhRG4kpZSpklX4ZRyOvwrFm5fZnsaHCLLmcCc9V7x371k/gAy', 'student', NULL, '2025-09-29 12:03:23', 'Ajil', 'Saji', '0', 4, NULL, 1, 9),
(96, 'A-6564', 'arunjoshy@gmail.com', '2323454', '2025-10-05', 'bca', '$2y$10$Ii5ijDbmqqIL9luYOjFyHeiCMopUJlEDYFCAZMtNpyIAKo6PYBNW.', 'student', NULL, '2025-10-01 16:57:56', 'Arun', 'Joshy', '0', 4, NULL, 1, 9),
(97, NULL, 'bvmadmin@gmail.com', NULL, NULL, NULL, '$2y$10$Gkhv5uOY6uLCFrV6TjzxVuIH2P77uXS3CGw0dgKhDgP7cUrbIivSm', 'admin', NULL, '2025-10-31 22:56:01', 'BVM', 'Admin', '', 4, NULL, NULL, NULL),
(98, NULL, 'gokulbvm@gmail.com', NULL, NULL, NULL, '$2y$10$FqYJAn9ByLa7SxcKeZJCseIJ5piwCbxnQKyBnMDnsjlwwNLhqn2xW', 'teacher', NULL, '2025-10-31 23:00:09', 'Gokul', 'Das', '', 4, NULL, 1, NULL),
(100, 'A-6460', 'arjunnair@gmail.com', '55324522', '2025-11-12', 'bca', '$2y$10$w4vyuxZds1mN9bcWGNiojOw0NK775zIuy768ZIZrMTk1B5bmN/tYa', 'student', NULL, '2025-11-01 06:40:08', 'Arjun', 'Ks', '0', 4, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_code` (`class_code`),
  ADD KEY `fk_batches_college` (`college_id`),
  ADD KEY `fk_batches_department` (`department_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_code` (`class_code`),
  ADD KEY `fk_college` (`college_id`);

--
-- Indexes for table `classfeed`
--
ALTER TABLE `classfeed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_classfeed_college` (`college_id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`),
  ADD UNIQUE KEY `college_code` (`college_code`);

--
-- Indexes for table `deadlines`
--
ALTER TABLE `deadlines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_department` (`department_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_files_college` (`college_id`),
  ADD KEY `fk_files_batch` (`batch_id`),
  ADD KEY `fk_files_subject` (`subject_id`);

--
-- Indexes for table `file_categories`
--
ALTER TABLE `file_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `file_types`
--
ALTER TABLE `file_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `file_type_numbers`
--
ALTER TABLE `file_type_numbers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_type_id` (`file_type_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_college` (`college_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_polls_college` (`college_id`);

--
-- Indexes for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `poll_user_unique` (`poll_id`,`user_id`),
  ADD KEY `option_id` (`option_id`);

--
-- Indexes for table `question_papers`
--
ALTER TABLE `question_papers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_papers_college` (`college_id`),
  ADD KEY `fk_uploaded_by` (`uploaded_by`),
  ADD KEY `fk_questionpapers_allocation` (`allocation_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `teacher_allocations`
--
ALTER TABLE `teacher_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `fk_teacherallocations_batch` (`batch_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_timetable_college` (`college_id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `file_type_id` (`file_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admission_no` (`admission_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_admission` (`admission_no`),
  ADD UNIQUE KEY `unique_register` (`register_no`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `idx_users_college_code` (`college_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `classfeed`
--
ALTER TABLE `classfeed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `deadlines`
--
ALTER TABLE `deadlines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `file_categories`
--
ALTER TABLE `file_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `file_types`
--
ALTER TABLE `file_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `file_type_numbers`
--
ALTER TABLE `file_type_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `question_papers`
--
ALTER TABLE `question_papers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacher_allocations`
--
ALTER TABLE `teacher_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_batches_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_batches_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `classfeed`
--
ALTER TABLE `classfeed`
  ADD CONSTRAINT `classfeed_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classfeed_ibfk_2` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classfeed_ibfk_3` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classfeed_ibfk_4` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_classfeed_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE;

--
-- Constraints for table `deadlines`
--
ALTER TABLE `deadlines`
  ADD CONSTRAINT `deadlines_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `file_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `fk_files_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_files_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_files_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `file_type_numbers`
--
ALTER TABLE `file_type_numbers`
  ADD CONSTRAINT `file_type_numbers_ibfk_1` FOREIGN KEY (`file_type_id`) REFERENCES `file_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_batch_fk` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`),
  ADD CONSTRAINT `marks_student_fk` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `marks_teacher_fk` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE;

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `fk_polls_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_papers`
--
ALTER TABLE `question_papers`
  ADD CONSTRAINT `fk_allocation` FOREIGN KEY (`allocation_id`) REFERENCES `teacher_allocations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_question_papers_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_questionpapers_allocation` FOREIGN KEY (`allocation_id`) REFERENCES `teacher_allocations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_allocations`
--
ALTER TABLE `teacher_allocations`
  ADD CONSTRAINT `fk_teacherallocations_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_allocations_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `teacher_allocations_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `teacher_allocations_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`);

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `fk_timetable_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploads_ibfk_2` FOREIGN KEY (`file_type_id`) REFERENCES `file_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
