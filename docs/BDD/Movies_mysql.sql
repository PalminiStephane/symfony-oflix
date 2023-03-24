CREATE DATABASE IF NOT EXISTS `MOVIES` DEFAULT CHARACTER SET UTF8MB4 COLLATE utf8_general_ci;
USE `MOVIES`;

CREATE TABLE `GENRE` (
  `code_genre` VARCHAR(42),
  `name` VARCHAR(42),
  PRIMARY KEY (`code_genre`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `MOVIE` (
  `code_movie` VARCHAR(42),
  `title` VARCHAR(42),
  `synopsis` VARCHAR(42),
  `poster` VARCHAR(42),
  `release_date` VARCHAR(42),
  `rating` VARCHAR(42),
  `duration` VARCHAR(42),
  `type` VARCHAR(42),
  `summary` VARCHAR(42),
  PRIMARY KEY (`code_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `PERSON` (
  `code_person` VARCHAR(42),
  `firstname` VARCHAR(42),
  `lastname` VARCHAR(42),
  PRIMARY KEY (`code_person`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `REVIEW` (
  `code_review` VARCHAR(42),
  `pseudo` VARCHAR(42),
  `description` VARCHAR(42),
  `view_date` VARCHAR(42),
  `rating` VARCHAR(42),
  `code_movie` VARCHAR(42),
  PRIMARY KEY (`code_review`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `SEASON` (
  `code_season` VARCHAR(42),
  `name` VARCHAR(42),
  `nb_episode` VARCHAR(42),
  `code_movie` VARCHAR(42),
  PRIMARY KEY (`code_season`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `USER` (
  `code_user` VARCHAR(42),
  `login` VARCHAR(42),
  `password` VARCHAR(42),
  `role` VARCHAR(42),
  PRIMARY KEY (`code_user`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `HAS` (
  `code_movie` VARCHAR(42),
  `code_genre` VARCHAR(42),
  PRIMARY KEY (`code_movie`, `code_genre`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `PLAY` (
  `code_movie` VARCHAR(42),
  `code_person` VARCHAR(42),
  `role` VARCHAR(42),
  PRIMARY KEY (`code_movie`, `code_person`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8MB4;

ALTER TABLE `REVIEW` ADD FOREIGN KEY (`code_movie`) REFERENCES `MOVIE` (`code_movie`);
ALTER TABLE `SEASON` ADD FOREIGN KEY (`code_movie`) REFERENCES `MOVIE` (`code_movie`);
ALTER TABLE `HAS` ADD FOREIGN KEY (`code_genre`) REFERENCES `GENRE` (`code_genre`);
ALTER TABLE `HAS` ADD FOREIGN KEY (`code_movie`) REFERENCES `MOVIE` (`code_movie`);
ALTER TABLE `PLAY` ADD FOREIGN KEY (`code_person`) REFERENCES `PERSON` (`code_person`);
ALTER TABLE `PLAY` ADD FOREIGN KEY (`code_movie`) REFERENCES `MOVIE` (`code_movie`);