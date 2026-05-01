-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 01, 2026 at 11:27 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meal_namagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` varchar(128) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `date`, `amount`, `comment`) VALUES
(37, '2026-03-08', '500', 'Rice and Oil');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int(11) NOT NULL,
  `username` varchar(128) NOT NULL,
  `secret_key` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id`, `username`, `secret_key`) VALUES
(1, 'Manager', '$2y$10$Bmn.qNxXzt5f5EQXQ0Lhs.jBhgTcnB7UTNGzPb0Xf6LR2asj1sSju');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(128) NOT NULL,
  `payment` varchar(128) NOT NULL,
  `day_1` varchar(128) NOT NULL,
  `day_2` varchar(128) NOT NULL,
  `day_3` varchar(128) NOT NULL,
  `day_4` varchar(128) NOT NULL,
  `day_5` varchar(128) NOT NULL,
  `day_6` varchar(128) NOT NULL,
  `day_7` varchar(128) NOT NULL,
  `day_8` varchar(128) NOT NULL,
  `day_9` varchar(128) NOT NULL,
  `day_10` varchar(128) NOT NULL,
  `day_11` varchar(128) NOT NULL,
  `day_12` varchar(128) NOT NULL,
  `day_13` varchar(128) NOT NULL,
  `day_14` varchar(128) NOT NULL,
  `day_15` varchar(128) NOT NULL,
  `day_16` varchar(128) NOT NULL,
  `day_17` varchar(128) NOT NULL,
  `day_18` varchar(128) NOT NULL,
  `day_19` varchar(128) NOT NULL,
  `day_20` varchar(128) NOT NULL,
  `day_21` varchar(128) NOT NULL,
  `day_22` varchar(128) NOT NULL,
  `day_23` varchar(128) NOT NULL,
  `day_24` varchar(128) NOT NULL,
  `day_25` varchar(128) NOT NULL,
  `day_26` varchar(128) NOT NULL,
  `day_27` varchar(128) NOT NULL,
  `day_28` varchar(128) NOT NULL,
  `day_29` varchar(128) NOT NULL,
  `day_30` varchar(128) NOT NULL,
  `day_31` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `number`, `payment`, `day_1`, `day_2`, `day_3`, `day_4`, `day_5`, `day_6`, `day_7`, `day_8`, `day_9`, `day_10`, `day_11`, `day_12`, `day_13`, `day_14`, `day_15`, `day_16`, `day_17`, `day_18`, `day_19`, `day_20`, `day_21`, `day_22`, `day_23`, `day_24`, `day_25`, `day_26`, `day_27`, `day_28`, `day_29`, `day_30`, `day_31`) VALUES
(1, 'Habib', '0112233445', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(2, 'Sadat', '0123456789', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(3, 'Ratul', '0111111119', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users_logs`
--

CREATE TABLE `users_logs` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `device` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT 'Unknown',
  `ip_address` varchar(45) NOT NULL,
  `checked_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users_logs`
--

INSERT INTO `users_logs` (`id`, `member_name`, `device`, `location`, `ip_address`, `checked_at`) VALUES
(210, 'Habib', 'Desktop > Windows > Chrome', 'Dhaka, Bangladesh', '59.153.103.51', '2026-05-01 05:04:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_logs`
--
ALTER TABLE `users_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users_logs`
--
ALTER TABLE `users_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
