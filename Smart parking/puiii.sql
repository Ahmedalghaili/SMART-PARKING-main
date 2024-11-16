-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2023 at 06:10 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `puiii`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_form_data`
--

CREATE TABLE `contact_form_data` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_form_data`
--

INSERT INTO `contact_form_data` (`id`, `name`, `email`, `subject`, `message`, `submission_date`) VALUES
(1, 'hide', '99mhnd7763@gmail.com', 'e ', 'kjhgf', '2023-12-03 09:54:31'),
(2, 'hcccdvfb', '99mhnd7763@gmail.com', '535', 'zvdv', '2023-12-03 13:46:23'),
(3, 'jbcx', '22523232@students.uii.ac.id', 'ncjkenv', 'jknchn', '2023-12-04 16:55:56'),
(4, 'hhhhhhhhhhhhhhhhhhhh', '99mhnd7763@gmail.com', 'jjjjjjjjjjjjjjjj', 'mkmkkmk', '2023-12-05 04:15:38'),
(5, '', '', '', '', '2023-12-05 06:45:07'),
(6, '', '', '', '', '2023-12-05 06:45:50'),
(7, '', '', '', '', '2023-12-05 07:21:08'),
(8, '', '', '', '', '2023-12-05 07:39:07');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `faculty_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_name`) VALUES
(1, 'Faculty of Business & Economics\r\n\r\n'),
(2, 'Faculty of Law'),
(3, 'Faculty of Islamic Religious Sciences'),
(4, 'Faculty of Medicine\r\n'),
(5, 'Faculty of Mathematics and Natural Sciences'),
(6, 'Faculty of Psychology and Social and Cultural Sciences'),
(7, 'Faculty of Civil Engineering and Planning'),
(8, 'Faculty of Industrial Technology');

-- --------------------------------------------------------

--
-- Table structure for table `parkingareas`
--

CREATE TABLE `parkingareas` (
  `area_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `area_type` enum('Car','Motorcycle') NOT NULL,
  `total_spaces` int(11) NOT NULL,
  `available_spaces` int(11) NOT NULL,
  `unavailable_spaces` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkingareas`
--

INSERT INTO `parkingareas` (`area_id`, `faculty_id`, `area_type`, `total_spaces`, `available_spaces`, `unavailable_spaces`) VALUES
(1, 1, 'Car', 50, 10, 40),
(2, 1, 'Motorcycle', 30, 29, 1),
(3, 2, 'Car', 60, 56, 4),
(4, 4, 'Motorcycle', 50, 50, 0),
(5, 2, 'Motorcycle', 100, 100, 0),
(6, 6, 'Car', 40, 40, 0),
(7, 3, 'Motorcycle', 400, 400, 0),
(8, 3, 'Car', 15, 15, 0),
(9, 4, 'Car', 20, 20, 0),
(10, 5, 'Motorcycle', 300, 300, 0),
(11, 5, 'Car', 40, 40, 0),
(12, 6, 'Motorcycle', 400, 400, 0),
(13, 7, 'Car', 45, 45, 0),
(14, 7, 'Motorcycle', 500, 500, 0),
(15, 8, 'Motorcycle', 250, 250, 0),
(16, 8, 'Car', 40, 39, 1);

-- --------------------------------------------------------

--
-- Table structure for table `parkingtransactions`
--

CREATE TABLE `parkingtransactions` (
  `transaction_id` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkingtransactions`
--

INSERT INTO `parkingtransactions` (`transaction_id`, `area_id`, `entry_time`, `exit_time`) VALUES
(747, 3, '2023-12-08 23:30:55', NULL),
(749, 3, '2023-12-08 23:33:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `vehicle_type` enum('Car','Motorcycle') NOT NULL,
  `owner_id` varchar(20) NOT NULL,
  `owner_type` enum('Student','Employee') NOT NULL,
  `faculty_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `plate_number`, `vehicle_type`, `owner_id`, `owner_type`, `faculty_id`) VALUES
(1, 'ABC123', 'Car', 'S123456', 'Student', 1),
(2, 'XYZ789', 'Motorcycle', 'E789012', 'Employee', 2),
(3, 'DEF456', 'Car', 'S654321', 'Student', 1),
(4, 'ABC153', 'Car', '2858', 'Student', 6),
(5, 'XYZ856', 'Motorcycle', '258', 'Employee', 5),
(6, 'DEF779', 'Car', '321', 'Employee', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_form_data`
--
ALTER TABLE `contact_form_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `parkingareas`
--
ALTER TABLE `parkingareas`
  ADD PRIMARY KEY (`area_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `parkingtransactions`
--
ALTER TABLE `parkingtransactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_form_data`
--
ALTER TABLE `contact_form_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parkingareas`
--
ALTER TABLE `parkingareas`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `parkingtransactions`
--
ALTER TABLE `parkingtransactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=750;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parkingareas`
--
ALTER TABLE `parkingareas`
  ADD CONSTRAINT `parkingareas_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`);

--
-- Constraints for table `parkingtransactions`
--
ALTER TABLE `parkingtransactions`
  ADD CONSTRAINT `parkingtransactions_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `parkingareas` (`area_id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
