DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT(10) NOT NULL AUTO_INCREMENT,
  `login_name` VARCHAR(12) NOT NULL UNIQUE,
  `name_ja` VARCHAR(50) NOT NULL,
  `name_en` VARCHAR(50) NOT NULL,
  `belong` VARCHAR(50) NOT NULL,
  `mail` VARCHAR(256) NOT NULL,
  `timeadded` TIMESTAMP,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `papers`;
CREATE TABLE `papers` (
  `id` BIGINT(10) NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(10) NOT NULL,
  `class` VARCHAR(20) NOT NULL, -- 論文種別(卒論/修論/博論/論文(査読なし)/論文(査読あり))
  `title_ja` VARCHAR(256),
  `title_en` VARCHAR(256),
  `file_name` VARCHAR(36) NOT NULL,
  `description_ja` TEXT,
  `description_en` TEXT,
  `keywords` VARCHAR(256),
  `timeadded` TIMESTAMP,
  -- `publicity` INT(1),
  FOREIGN KEY (`user_id`) REFERENCES users(`id`),
  PRIMARY KEY (`id`)
);

