-- ****************** SqlDBM: MySQL ******************;
--  Mio Web Application Database Definition Statements
-- ***************************************************;

DROP TABLE IF EXISTS `file`;


DROP TABLE IF EXISTS `room_message`;


DROP TABLE IF EXISTS `room_member`;


DROP TABLE IF EXISTS `friends`;


DROP TABLE IF EXISTS `message`;


DROP TABLE IF EXISTS `room`;


DROP TABLE IF EXISTS `user`;



-- ************************************** `room`

CREATE TABLE `room`
(
 `id`      INT NOT NULL AUTO_INCREMENT,
 `user_id` INT NOT NULL ,
 `name`    VARCHAR(64) NOT NULL ,

PRIMARY KEY (`id`)
);






-- ************************************** `user`

CREATE TABLE `user`
(
 `id`             INT NOT NULL AUTO_INCREMENT,
 `name`           VARCHAR(64) NOT NULL ,
 `email`          VARCHAR(64) NOT NULL ,
 `password`       VARCHAR(64) NOT NULL ,
 `image`          LONGBLOB NOT NULL ,

PRIMARY KEY (`id`)
);






-- ************************************** `room_member`

CREATE TABLE `room_member`
(
 `id`   INT NOT NULL AUTO_INCREMENT,
 `room` INT NOT NULL ,
 `usr`  INT NOT NULL ,

PRIMARY KEY (`id`, `room`),
KEY `room_member_room_id` (`room`),
CONSTRAINT `fk_room_member_room_id` FOREIGN KEY (`room`) REFERENCES `room` (`id`)
ON DELETE CASCADE,
KEY `room_member_user_id` (`usr`),
CONSTRAINT `fk_room_member_user_id` FOREIGN KEY (`usr`) REFERENCES `user` (`id`)

);






-- ************************************** `friends`

CREATE TABLE `friends`
(
 `id`   INT NOT NULL AUTO_INCREMENT,
 `from_id` INT NOT NULL ,
 `to_id`   INT NOT NULL ,

PRIMARY KEY (`id`, `from_id`),
KEY `friends_user_id_to` (`to_id`),
CONSTRAINT `fk_friends_user_to` FOREIGN KEY (`to_id`) REFERENCES `user` (`id`)
ON DELETE CASCADE,
KEY `friends_user_id_from` (`from_id`),
CONSTRAINT `fk_friends_user_from` FOREIGN KEY (`from_id`) REFERENCES `user` (`id`)
ON DELETE CASCADE
);






-- ************************************** `message`

CREATE TABLE `message`
(
 `id`      INT NOT NULL AUTO_INCREMENT,
 `user_id` INT NOT NULL ,
 `content` TEXT NOT NULL ,
 `time`    TIMESTAMP NOT NULL ,

PRIMARY KEY (`id`, `user_id`),
KEY `message_user_id` (`user_id`),
CONSTRAINT `fk_message_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
ON DELETE CASCADE
);






-- ************************************** `file`

CREATE TABLE `file`
(
 `id`      INT NOT NULL AUTO_INCREMENT,
 `msg_id`  INT NOT NULL ,
 `user_id` INT NOT NULL ,
 `content` BLOB NOT NULL ,
 INDEX (user_id),

PRIMARY KEY (`id`, `msg_id`, `user_id`),
KEY `file_msg_usr` (`msg_id`, `user_id`),
CONSTRAINT `fk_file_msg_user_id` FOREIGN KEY (`msg_id`, `user_id`) REFERENCES `message` (`id`, `user_id`)
);






-- ************************************** `room_message`

CREATE TABLE `room_message`
(
 `id`         INT NOT NULL AUTO_INCREMENT,
 `room_id`    INT NOT NULL ,
 `message_id` INT NOT NULL ,
 `user_id`    INT NOT NULL ,

PRIMARY KEY (`id`, `room_id`),
KEY `rm_message_room_id` (`room_id`),
CONSTRAINT `fk_rm_message_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id`),
KEY `rm_message_message_id` (`message_id`, `user_id`),
CONSTRAINT `fk_rm_message_user_id` FOREIGN KEY (`message_id`, `user_id`) REFERENCES `message` (`id`, `user_id`)
);
