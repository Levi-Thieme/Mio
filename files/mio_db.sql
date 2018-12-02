-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2018 at 07:01 PM
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
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  PRIMARY KEY (`id`,`from`),
  KEY `friends_user_id_to` (`to`),
  KEY `friends_user_id_from` (`from`)
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `content`, `time`) VALUES
(1, 13, 'Thanks for joining room 13!', '2018-12-02 00:50:05'),
(2, 13, 'Hey there\n', '2018-12-01 19:51:44'),
(3, 13, 'You  can stop that\n', '2018-12-01 19:51:52'),
(4, 13, 'hello\n', '2018-12-01 21:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `user_id`, `name`) VALUES
(1, 13, 'Boys Only'),
(2, 14, 'Room2'),
(3, 15, 'Room3');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `room_member`
--

INSERT INTO `room_member` (`id`, `room`, `usr`) VALUES
(1, 2, 13),
(5, 3, 13),
(7, 1, 13),
(3, 1, 14),
(6, 3, 14),
(8, 2, 14),
(2, 2, 15),
(4, 1, 15),
(9, 3, 15);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `room_message`
--

INSERT INTO `room_message` (`id`, `room_id`, `message_id`, `user_id`) VALUES
(1, 1, 1, 13),
(2, 1, 2, 13);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `image`) VALUES
(13, 'joe', 'joe@pfw.edu', '*60D178145669A4D1569FE820852BB3425CB2D4A7', 0x616e20496d616765),
(14, 'Joe2', 'crypt@pfw.edu', '*196BDEDE2AE4F84CA44C47D54D78478C7E2BD7B7', 0x616e20496d616765),
(15, 'joe3', 'pog@pfw.edu', '*56C8F0F483D3E0E5A5088938D5DE6CD90F825BD5', 0x616e20496d616765);

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
  ADD CONSTRAINT `fk_friends_user_from` FOREIGN KEY (`from`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_friends_user_to` FOREIGN KEY (`to`) REFERENCES `user` (`id`);

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
