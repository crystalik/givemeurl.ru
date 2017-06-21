SET NAMES utf8;

CREATE TABLE `short_url` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` VARCHAR(40) NOT NULL UNIQUE,
  `url` VARCHAR(1000) NOT NULL,
  `short_url` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`hash`),
  UNIQUE KEY (`short_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;