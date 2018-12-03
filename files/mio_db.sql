-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2018 at 12:35 AM
-- Server version: 5.5.57-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mio_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` blob NOT NULL,
  PRIMARY KEY (`id`,`msg_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `file_msg_usr` (`msg_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`from_id`),
  KEY `friends_user_id_to` (`to_id`),
  KEY `friends_user_id_from` (`from_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`user_id`),
  KEY `message_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `content`, `time`) VALUES
(7, 16, 'Welcome to my chatroom!', '2018-12-03 00:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `user_id`, `name`) VALUES
(4, 16, 'Joe''s Room'),
(5, 17, 'Levi''s Room'),
(6, 18, 'Brad''s Room'),
(7, 19, 'Isaac''s Room'),
(8, 20, 'Aaron''s Room');

-- --------------------------------------------------------

--
-- Table structure for table `room_member`
--

CREATE TABLE IF NOT EXISTS `room_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` int(11) NOT NULL,
  `usr` int(11) NOT NULL,
  PRIMARY KEY (`id`,`room`),
  KEY `room_member_room_id` (`room`),
  KEY `room_member_user_id` (`usr`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `room_member`
--

INSERT INTO `room_member` (`id`, `room`, `usr`) VALUES
(10, 4, 16),
(14, 5, 16),
(20, 7, 16),
(11, 4, 17),
(13, 5, 17),
(21, 7, 17),
(12, 4, 18),
(15, 5, 18),
(19, 6, 18),
(24, 8, 18),
(16, 5, 19),
(18, 6, 19),
(22, 7, 19),
(25, 8, 19),
(17, 5, 20),
(23, 8, 20);

-- --------------------------------------------------------

--
-- Table structure for table `room_message`
--

CREATE TABLE IF NOT EXISTS `room_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`room_id`),
  KEY `rm_message_room_id` (`room_id`),
  KEY `rm_message_message_id` (`message_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `room_message`
--

INSERT INTO `room_message` (`id`, `room_id`, `message_id`, `user_id`) VALUES
(5, 4, 7, 16);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(41) NOT NULL,
  `image` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `image`) VALUES
(16, 'JosephShell', 'sheljl01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(17, 'LeviThieme', 'thielt01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(18, 'BradEberbach', 'eberbm01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(19, 'IsaacHarter', 'hartia01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(20, 'AaronODonnell', 'odonap01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `fk_file_msg_user_id` FOREIGN KEY (`msg_id`, `user_id`) REFERENCES `message` (`id`, `user_id`);

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `fk_friends_user_from` FOREIGN KEY (`from_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_friends_user_to` FOREIGN KEY (`to_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `room_member`
--
ALTER TABLE `room_member`
  ADD CONSTRAINT `fk_room_member_room_id` FOREIGN KEY (`room`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `fk_room_member_user_id` FOREIGN KEY (`usr`) REFERENCES `user` (`id`);

--
-- Constraints for table `room_message`
--
ALTER TABLE `room_message`
  ADD CONSTRAINT `fk_rm_message_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `fk_rm_message_user_id` FOREIGN KEY (`message_id`, `user_id`) REFERENCES `message` (`id`, `user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
