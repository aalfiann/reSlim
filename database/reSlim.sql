-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2016 at 02:19 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `core_status`
--

CREATE TABLE IF NOT EXISTS `core_status` (
`StatusID` int(11) NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `core_status`
--

INSERT INTO `core_status` (`StatusID`, `Status`) VALUES
(1, 'active'),
(2, 'allocated'),
(3, 'approved'),
(4, 'authorized'),
(5, 'banned'),
(6, 'blank'),
(7, 'canceled'),
(8, 'checked'),
(9, 'closed'),
(10, 'commented'),
(11, 'compared'),
(12, 'deleted'),
(13, 'disabled'),
(14, 'downloaded'),
(15, 'edited'),
(16, 'enabled'),
(17, 'error'),
(18, 'expired'),
(19, 'failed'),
(20, 'hidden'),
(21, 'installed'),
(22, 'listed'),
(23, 'locked'),
(24, 'maintenance'),
(25, 'merged'),
(26, 'moved'),
(27, 'ok'),
(28, 'on hold'),
(29, 'on process'),
(30, 'on request'),
(31, 'open'),
(32, 'outstanding'),
(33, 'overdue'),
(34, 'paid'),
(35, 'pending'),
(36, 'registered'),
(37, 'rejected'),
(38, 'removed'),
(39, 'signed'),
(40, 'stopped'),
(41, 'success'),
(42, 'suspended'),
(43, 'unauthorized'),
(44, 'unknown'),
(45, 'uploaded'),
(46, 'viewed'),
(47, 'void'),
(48, 'waiting');

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE IF NOT EXISTS `user_auth` (
  `Username` varchar(50) NOT NULL,
  `RS_Token` varchar(255) NOT NULL,
  `Created` datetime NOT NULL,
  `Expired` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE IF NOT EXISTS `user_data` (
`UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Fullname` varchar(50) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Aboutme` varchar(255) DEFAULT NULL,
  `Avatar` text,
  `RoleID` int(11) NOT NULL,
  `StatusID` int(11) NOT NULL,
  `Created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_data`
--

INSERT INTO `user_data` (`UserID`, `Username`, `Password`, `Fullname`, `Address`, `Phone`, `Email`, `Aboutme`, `Avatar`, `RoleID`, `StatusID`, `Created_at`, `Updated_at`) VALUES
(1, 'reslim', '$2y$11$D9ZWJOhKvLoor7RyUA70hOVzbwJ9RA.nk909QLENotxq26F6k/Qxu', 'Master', 'INDONESIA', '12345', 'your@yourdomain.com', 'Master of reSlim Project', '', 1, 1, '2016-12-28 20:17:12', '2016-12-28 20:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
`RoleID` int(11) NOT NULL,
  `Role` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`RoleID`, `Role`) VALUES
(1, 'superuser'),
(2, 'admin'),
(3, 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `core_status`
--
ALTER TABLE `core_status`
 ADD PRIMARY KEY (`StatusID`), ADD KEY `StatusID` (`StatusID`) USING BTREE;

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
 ADD PRIMARY KEY (`Username`,`RS_Token`), ADD KEY `token` (`Username`,`RS_Token`,`Expired`) USING BTREE;

--
-- Indexes for table `user_data`
--
ALTER TABLE `user_data`
 ADD PRIMARY KEY (`UserID`,`Username`), ADD KEY `user_data_ibfk_1` (`StatusID`), ADD KEY `user_data_ibfk_2` (`RoleID`), ADD KEY `Username` (`Username`), ADD KEY `Fullname` (`Fullname`) USING BTREE, ADD KEY `Password` (`Password`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
 ADD PRIMARY KEY (`RoleID`), ADD KEY `ID` (`RoleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `core_status`
--
ALTER TABLE `core_status`
MODIFY `StatusID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `user_data`
--
ALTER TABLE `user_data`
MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
ADD CONSTRAINT `user_token` FOREIGN KEY (`Username`) REFERENCES `user_data` (`Username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_data`
--
ALTER TABLE `user_data`
ADD CONSTRAINT `user_data_ibfk_1` FOREIGN KEY (`StatusID`) REFERENCES `core_status` (`StatusID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `user_data_ibfk_2` FOREIGN KEY (`RoleID`) REFERENCES `user_role` (`RoleID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
