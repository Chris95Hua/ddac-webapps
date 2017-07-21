-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2017 at 11:57 PM
-- Server version: 10.1.8-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uia_app`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`dev`@`localhost` PROCEDURE `get_page_by_id` (IN `_page_id` TINYINT, IN `_role_id` TINYINT)  NO SQL
BEGIN
	IF EXISTS(SELECT page_id FROM page WHERE page_id = _page_id) THEN
    	IF EXISTS(SELECT page.page_id FROM page LEFT JOIN page_permission ON page.page_id = page_permission.page_id WHERE page.page_id = _page_id AND page_permission.role_id = _role_id) THEN
        	BEGIN
        		SELECT * FROM page WHERE page_id = _page_id;
            END;
        ELSE
        	SELECT -1 AS page_id;
        END IF;
    ELSE
    	SELECT -2 AS page_id;
    END IF;
END$$

CREATE DEFINER=`dev`@`localhost` PROCEDURE `get_page_by_slug` (IN `_slug` VARCHAR(32), IN `_role_id` TINYINT)  BEGIN
	IF EXISTS(SELECT page_id FROM page WHERE page_slug = _slug) THEN
    	IF EXISTS(SELECT page.page_id FROM page LEFT JOIN page_permission ON page.page_id = page_permission.page_id WHERE page.page_slug = _slug AND page_permission.role_id = _role_id) THEN
        	BEGIN
        		SELECT * FROM page WHERE page_slug = _slug;
            END;
        ELSE
        	SELECT -1 AS page_id;
        END IF;
    ELSE
    	SELECT -2 AS page_id;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE `page` (
  `page_id` tinyint(3) UNSIGNED NOT NULL,
  `page_title` varchar(64) NOT NULL,
  `page_slug` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`page_id`, `page_title`, `page_slug`) VALUES
(1, 'Home', 'home'),
(2, 'Sign In', 'singin'),
(3, 'Sign Up', 'signup'),
(4, 'Booking', 'booking');

-- --------------------------------------------------------

--
-- Table structure for table `page_permission`
--

CREATE TABLE `page_permission` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `page_id` tinyint(3) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `page_permission`
--

INSERT INTO `page_permission` (`permission_id`, `page_id`, `role_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 3),
(5, 3, 3),
(6, 4, 1),
(7, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` char(64) NOT NULL,
  `salt` char(32) NOT NULL,
  `date_joined` date NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `role` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`role_id`, `role`) VALUES
(1, 'Admin'),
(2, 'Member'),
(3, 'Guest');

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--

CREATE TABLE `user_session` (
  `session_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `hash` char(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `page_permission`
--
ALTER TABLE `page_permission`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `user_session`
--
ALTER TABLE `user_session`
  ADD PRIMARY KEY (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
  MODIFY `page_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `page_permission`
--
ALTER TABLE `page_permission`
  MODIFY `permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `role_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_session`
--
ALTER TABLE `user_session`
  MODIFY `session_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
