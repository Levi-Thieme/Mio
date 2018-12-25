-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2018 at 09:38 PM
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
  `pending` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`from_id`),
  KEY `friends_user_id_to` (`to_id`),
  KEY `friends_user_id_from` (`from_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `from_id`, `to_id`, `pending`) VALUES
(44, 17, 16, 0),
(48, 18, 16, 0),
(49, 17, 18, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=103 ;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `content`, `time`) VALUES
(86, 17, 'test message.', '2018-12-25 20:05:20'),
(87, 17, 'please stop!', '2018-12-25 20:05:37'),
(90, 17, 'wow!', '2018-12-25 20:43:06'),
(91, 17, 'This is great!', '2018-12-25 20:43:13'),
(92, 17, 'no more!!', '2018-12-25 20:51:23'),
(99, 17, 'Hey Joe By Jimi Hendrix', '2018-12-25 20:58:26'),
(100, 16, 'Hey Levi!! How are you?', '2018-12-25 20:59:51'),
(101, 17, 'Hi Joe!', '2018-12-25 21:16:41'),
(102, 16, 'Hi Levi!! How''s it going?', '2018-12-25 21:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `user_id`, `name`) VALUES
(4, 16, 'Joe''s Room'),
(6, 18, 'Brad''s Room'),
(7, 19, 'Isaac''s Room'),
(8, 20, 'Aaron''s Room'),
(10, 21, 'jordaj01s Personal Room'),
(11, 22, 'chenzs Personal Room'),
(12, 22, 'myroom'),
(22, 17, 'levi and joe'),
(25, 17, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `room_member`
--

CREATE TABLE IF NOT EXISTS `room_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` int(11) NOT NULL,
  `usr` int(11) NOT NULL,
  PRIMARY KEY (`id`,`room`),
  KEY `fk_room_member_room_id` (`room`),
  KEY `fk_room_member_user_id` (`usr`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `room_member`
--

INSERT INTO `room_member` (`id`, `room`, `usr`) VALUES
(35, 22, 16),
(40, 4, 17),
(19, 6, 18),
(24, 8, 18),
(18, 6, 19),
(22, 7, 19),
(25, 8, 19),
(37, 22, 19),
(23, 8, 20),
(39, 22, 20),
(30, 10, 21),
(32, 11, 21),
(31, 11, 22),
(33, 12, 22);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=96 ;

--
-- Dumping data for table `room_message`
--

INSERT INTO `room_message` (`id`, `room_id`, `message_id`, `user_id`) VALUES
(79, 25, 86, 17),
(80, 25, 87, 17),
(83, 25, 90, 17),
(84, 25, 91, 17),
(85, 25, 92, 17),
(92, 22, 99, 17),
(93, 22, 100, 16),
(94, 4, 101, 17),
(95, 4, 102, 16);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(41) NOT NULL,
  `image` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `image`) VALUES
(16, 'JosephShell', 'sheljl01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(17, 'LeviThieme', 'thielt01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(18, 'BradEberbach', 'eberbm01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(19, 'IsaacHarter', 'hartia01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(20, 'AaronODonnell', 'odonap01@students.ipfw.edu', '*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19', 0x416e20496d616765),
(21, 'jordaj01', 'jordaj01@pfw.edu', '*1A415D842F616B82C17AF887CB4B0135FA8CDA03', 0x616e20496d616765),
(22, 'chenz', 'chen@pfw.edu', '*720B26BCDF52FE9C5F61C4CC4C987B2C2B5601E3', 0x616e20496d616765),
(25, 'Sender User', '', '', ''),
(26, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', ''),
(29, 'Sender User', '', '', ''),
(30, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', ''),
(33, 'Sender User', '', '', ''),
(34, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', ''),
(37, 'Sender User', '', '', ''),
(38, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', ''),
(41, 'Sender User', '', '', ''),
(42, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', ''),
(45, 'Sender User', '', '', ''),
(46, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', ''),
(49, 'Sender User', '', '', ''),
(50, 'Receiver User', 'receiver@gmail.com', 'receiverpassword', '');

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
  ADD CONSTRAINT `fk_message_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_member`
--
ALTER TABLE `room_member`
  ADD CONSTRAINT `fk_room_member_room_id` FOREIGN KEY (`room`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `fk_room_member_user_id` FOREIGN KEY (`usr`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `room_member_ibfk_1` FOREIGN KEY (`usr`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_member_ibfk_2` FOREIGN KEY (`room`) REFERENCES `room` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_message`
--
ALTER TABLE `room_message`
  ADD CONSTRAINT `fk_rm_message_message_id` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rm_message_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rm_message_user_id` FOREIGN KEY (`message_id`, `user_id`) REFERENCES `message` (`id`, `user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
