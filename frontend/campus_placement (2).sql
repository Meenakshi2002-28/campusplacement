-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2024 at 10:58 AM
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
-- Database: `campus_placement`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_details`
--

CREATE TABLE `academic_details` (
  `user_id` varchar(25) NOT NULL,
  `school_tenth` varchar(50) NOT NULL,
  `board_tenth` varchar(25) NOT NULL,
  `percentage_tenth` decimal(5,2) NOT NULL,
  `year_tenth` year(4) NOT NULL,
  `school_twelfth` varchar(50) NOT NULL,
  `board_twelfth` varchar(25) NOT NULL,
  `percentage_twelfth` decimal(5,2) NOT NULL,
  `year_twelfth` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_details`
--

INSERT INTO `academic_details` (`user_id`, `school_tenth`, `board_tenth`, `percentage_tenth`, `year_tenth`, `school_twelfth`, `board_twelfth`, `percentage_twelfth`, `year_twelfth`) VALUES
('KHENU3CDS22045', 'abcd', 'cbse', 93.00, '2019', 'abcd', 'cbse', 95.00, '2021'),
('KHENU3CDS22055', 'abcd', 'cbse', 93.00, '2019', 'abcd', 'cbse', 95.00, '2021');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `phone_number` int(11) DEFAULT NULL,
  `name` varchar(25) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `user_id` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `course_branch` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_name`, `course_branch`) VALUES
(1, 'B.com taxation and finance', 'COMMERCE'),
(2, 'BBA', 'COMMERCE'),
(3, 'B.com fintech', 'COMMERCE'),
(4, 'Int MCA', 'CS'),
(5, 'BCA', 'CS'),
(6, 'BCA DataScience', 'CS'),
(7, 'BA English and Literature', 'ENGLISH'),
(8, 'Int MA English and Literature', 'ENGLISH'),
(9, 'Int MSC mathematics', 'PHYSICAL SCIENCES'),
(10, 'Int Physics', 'PHYSICS'),
(11, 'Int Msc Physics', 'PHYSICS'),
(12, 'Int Msc Mathematics', 'MATHEMATICS');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `user_id` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) DEFAULT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`user_id`, `password`, `role`, `email`) VALUES
('JESINE', '$2y$10$Tyv3NeOPcP1TX2liv81.DOBaaZgEIw0sMan0mQSy4ZTmcgXU0/STy', 'admin', 'jesine@gmail.com'),
('KHENU3CDS22035', '$2y$10$LU5mW4eVXK9vB0LfVoLXYeGgf1edMod/e02H4lAun0KcROcjHFPgG', 'student', 'gowriparvathy2004@gmail.com'),
('KHENU3CDS22045', '$2y$10$4IOVCyxHFhpXjPRmgv8BIOm46CinPbyv7pJCpLAe8v4fbfZXpePPO', 'student', 'meenuptr2002@gmail.com'),
('KHENU3CDS22055', '$2y$10$epAf3EAIgjtcJ4a.pRKkOepXL7Lfugiw.UtniMtlBD00P86rQIidS', 'student', 'niranjana@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `user_id` varchar(25) NOT NULL,
  `name` varchar(50) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `cgpa` decimal(3,2) DEFAULT 0.00,
  `graduation_year` year(4) DEFAULT NULL,
  `current_year` year(4) DEFAULT NULL,
  `current_arrears` int(11) DEFAULT 0,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resume` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`user_id`, `name`, `gender`, `course_id`, `email`, `phone_number`, `cgpa`, `graduation_year`, `current_year`, `current_arrears`, `dob`, `created_at`, `resume`, `photo`) VALUES
('KHENU3CDS22035', 'Gowri', 'female', 5, 'gowriparvathy2004@gmail.com', '7301032641', 0.00, '2025', '2003', 0, '2024-09-06', '2024-09-17 15:00:14', NULL, NULL),
('KHENU3CDS22045', 'Meenakshi B', 'female', 5, 'meenu@gmail.com', '8301032641', 9.00, '2025', '2003', 0, '2024-09-10', '2024-09-17 15:22:09', NULL, NULL),
('KHENU3CDS22055', 'Niranjana A S', 'female', 6, 'niranjana@gmail.com', '8001932641', 9.00, '2025', '2003', 0, '2024-09-14', '2024-09-19 16:53:28', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_details`
--
ALTER TABLE `academic_details`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_details`
--
ALTER TABLE `academic_details`
  ADD CONSTRAINT `academic_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student` (`user_id`);

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
