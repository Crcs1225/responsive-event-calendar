-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 29, 2025 at 02:38 AM
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
-- Database: `calendar`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `user_id`, `title`, `description`, `start_datetime`, `end_datetime`, `type`, `status`, `created_at`, `updated_at`) VALUES
(5, NULL, 'new event 1', '', '2025-04-13 13:47:00', '2025-04-18 13:07:00', 0, NULL, '2025-04-24 08:31:56', '2025-04-28 05:13:10'),
(6, NULL, 'new event 1', 'this is filled', '2025-04-10 04:50:00', '2025-04-11 07:40:00', 0, 'Inactive', '2025-04-24 08:32:21', '2025-04-29 00:37:18'),
(11, NULL, 'events 2', 'there will be event here', '2025-04-25 07:00:00', '2025-04-25 11:00:00', 0, '', '2025-04-25 03:01:19', '2025-04-28 08:56:20'),
(12, NULL, '2 day event', 'this is a two day event', '2025-04-29 08:00:00', '2025-05-01 13:00:00', NULL, NULL, '2025-04-25 05:48:17', '2025-04-28 05:23:09'),
(13, NULL, 'Nurse', 'this is a yeah', '2025-04-28 08:00:00', '2025-04-28 16:00:00', NULL, NULL, '2025-04-25 08:38:34', '2025-04-25 08:38:34'),
(16, NULL, 'monday event', 'something', '2025-04-07 14:29:00', '2025-04-08 18:29:00', 3, 'Active', '2025-04-28 06:30:17', '2025-04-28 06:57:09'),
(18, NULL, 'hey', '', '2025-04-12 03:34:00', '2025-04-12 15:35:00', 3, 'Inactive', '2025-04-28 07:35:15', '2025-04-28 08:58:08'),
(22, NULL, 'any again', '', '2025-04-05 04:09:00', '2025-04-05 16:09:00', 1, 'Active', '2025-04-28 08:09:40', '2025-04-28 08:10:22'),
(23, NULL, 'helo', '', '2025-04-03 04:10:00', '2025-04-03 16:10:00', 3, 'Inactive', '2025-04-28 08:10:56', '2025-04-29 00:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `event_tracking`
--

CREATE TABLE `event_tracking` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(255) DEFAULT 'gen',
  `status` varchar(50) NOT NULL,
  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_tracking`
--

INSERT INTO `event_tracking` (`id`, `event_id`, `from`, `to`, `status`, `updated`, `created`) VALUES
(1, 22, 'current_user_id', '1', 'Inactive', '2025-04-28 16:09:40', '2025-04-28 16:09:40'),
(2, 23, 'current_user_id', '3', 'Active', '2025-04-28 16:10:56', '2025-04-28 16:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `event_type`
--

CREATE TABLE `event_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_type`
--

INSERT INTO `event_type` (`id`, `type_name`) VALUES
(1, 'RSP Interview'),
(2, 'LND Event'),
(3, 'General Event');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_tracking`
--
ALTER TABLE `event_tracking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_type`
--
ALTER TABLE `event_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `event_tracking`
--
ALTER TABLE `event_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event_type`
--
ALTER TABLE `event_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
