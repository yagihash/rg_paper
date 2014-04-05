DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT(10) NOT NULL AUTO_INCREMENT,
  `login_name` VARCHAR(12) NOT NULL UNIQUE,
  `name_ja` VARCHAR(50) NOT NULL,
  `name_en` VARCHAR(50) NOT NULL,
  `belongs` VARCHAR(50) NOT NULL,
  `timeadded` TIMESTAMP,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `papers`;
CREATE TABLE `papers` (
  `id` BIGINT(10) NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(10) NOT NULL,
  `title_ja` VARCHAR(256),
  `title_en` VARCHAR(256),
  `file_url` VARCHAR(256) NOT NULL,
  `description_ja` VARCHAR(256),
  `description_en` VARCHAR(256),
  `keywords` VARCHAR(256),
  -- `publicity` INT(1),
  `mail` VARCHAR(256),
  PRIMARY KEY (`id`)
);

