-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2024 at 11:16 AM
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
-- Database: `map_mark_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mark`
--

CREATE TABLE `tbl_mark` (
  `tbl_mark_id` int(11) NOT NULL,
  `mark_name` varchar(255) NOT NULL,
  `mark_long` double NOT NULL,
  `mark_lat` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_mark`
--

INSERT INTO `tbl_mark` (`tbl_mark_id`, `mark_name`, `mark_long`, `mark_lat`) VALUES
(1, 'Lorem House', 120.036621, 16.274435),
(2, 'Mom', 123.42041, 13.320347),
(3, 'Dad', 121.343994, 13.9759),
(6, 'Sister Jade', 121.92627, 13.727187),
(7, 'John Doe', 121.245117, 13.021586);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_mark`
--
ALTER TABLE `tbl_mark`
  ADD PRIMARY KEY (`tbl_mark_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_mark`
--
ALTER TABLE `tbl_mark`
  MODIFY `tbl_mark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
