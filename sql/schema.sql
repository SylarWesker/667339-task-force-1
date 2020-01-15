-- Создание БД.
CREATE DATABASE IF NOT EXISTS TaskForce
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

use TaskForce;

CREATE TABLE IF NOT EXISTS `Task`
(
    `id`            int PRIMARY KEY AUTO_INCREMENT,
    `client_id`     int          NOT NULL,
    `performer_id`  int,
    `finish_date`   datetime,
    `status_id`     int,
    `category_id`   int          NOT NULL,
    `description`   varchar(255) NOT NULL,
    `details`       text         NOT NULL,
    `cost`          int,
    `creation_date` datetime,
    `latitude`      varchar(255),
    `longitude`     varchar(255),
    `locality_id`   int
);

CREATE TABLE IF NOT EXISTS `TaskStatus`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255) UNIQUE NOT NULL,
    `text`       varchar(255),
    `image_path` varchar(255)
);

CREATE TABLE IF NOT EXISTS `TaskRelatedFile`
(
    `id`       int PRIMARY KEY AUTO_INCREMENT,
    `task_id`  int          NOT NULL,
    `filepath` varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `User`
(
    `id`                int PRIMARY KEY AUTO_INCREMENT,
    `email`             varchar(255) UNIQUE NOT NULL,
    `password`          varchar(255)        NOT NULL,
    `full_name`         varchar(255)        NOT NULL,
    `residence`         varchar(255),
    `avatar_filepath`   varchar(255),
    `birthday`          date,
    `about`             varchar(255),
    `phone`             varchar(255),
    `skype`             varchar(255),
    `another_messenger` varchar(255),
    `latitude`          varchar(255),
    `longitude`         varchar(255),
    `locality_id`       int
);

CREATE TABLE IF NOT EXISTS `Category`
(
    `id`   int PRIMARY KEY AUTO_INCREMENT,
    `name` varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `UserSpecialization`
(
    `id`          int PRIMARY KEY AUTO_INCREMENT,
    `user_id`     int NOT NULL,
    `category_id` int NOT NULL
);

CREATE TABLE IF NOT EXISTS `UserPortfolio`
(
    `id`       int PRIMARY KEY AUTO_INCREMENT,
    `user_id`  int NOT NULL,
    `filepath` varchar(255)
);

-- Населенный пункт
CREATE TABLE IF NOT EXISTS `Locality`
(
    `id`   int PRIMARY KEY AUTO_INCREMENT,
    `name` varchar(255) NOT NULL
);

-- Отклик
CREATE TABLE IF NOT EXISTS `Response`
(
    `id`            int PRIMARY KEY AUTO_INCREMENT,
    `candidate_id`  int,
    `task_id`       int,
    `offered_price` int
);

-- Отзыв
CREATE TABLE IF NOT EXISTS `Review`
(
    `id`          int PRIMARY KEY AUTO_INCREMENT,
    `task_id`     int NOT NULL,
    `is_finished` bool,
    `rate`        int,
    `comment`     varchar(255)
);

CREATE TABLE IF NOT EXISTS `Message`
(
    `id`          int PRIMARY KEY AUTO_INCREMENT,
    `sender_id`   int NOT NULL,
    `receiver_id` int NOT NULL,
    `task_id`     int NOT NULL,
    `send_date`   datetime
);

CREATE TABLE `FavoritePerformer`
(
    `id`           int PRIMARY KEY AUTO_INCREMENT,
    `client_id`    int NOT NULL,
    `performer_id` int NOT NULL
);

-- Добавление внешних ключей.
ALTER TABLE `Task`
    ADD FOREIGN KEY (`client_id`) REFERENCES `User` (`id`);
ALTER TABLE `Task`
    ADD FOREIGN KEY (`performer_id`) REFERENCES `User` (`id`);
ALTER TABLE `Task`
    ADD FOREIGN KEY (`category_id`) REFERENCES `Category` (`id`);
ALTER TABLE `Task`
    ADD FOREIGN KEY (`status_id`) REFERENCES `TaskStatus` (`id`);
ALTER TABLE `Task`
    ADD FOREIGN KEY (`locality_id`) REFERENCES `Locality` (`id`); -- !!!
ALTER TABLE `Task`
    ADD FOREIGN KEY (`id`) REFERENCES `Review` (`task_id`); -- !!!

ALTER TABLE `TaskRelatedFile`
    ADD FOREIGN KEY (`task_id`) REFERENCES `Task` (`id`);

ALTER TABLE `UserSpecialization`
    ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);
ALTER TABLE `UserSpecialization`
    ADD FOREIGN KEY (`category_id`) REFERENCES `Category` (`id`);

ALTER TABLE `User`
    ADD FOREIGN KEY (`id`) REFERENCES `UserPortfolio` (`user_id`);

ALTER TABLE `Response`
    ADD FOREIGN KEY (`candidate_id`) REFERENCES `User` (`id`);
ALTER TABLE `Response`
    ADD FOREIGN KEY (`task_id`) REFERENCES `Task` (`id`);

ALTER TABLE `Message`
    ADD FOREIGN KEY (`sender_id`) REFERENCES `User` (`id`);
ALTER TABLE `Message`
    ADD FOREIGN KEY (`receiver_id`) REFERENCES `User` (`id`);
ALTER TABLE `Message`
    ADD FOREIGN KEY (`task_id`) REFERENCES `Task` (`id`);

ALTER TABLE `FavoritePerformer`
    ADD FOREIGN KEY (`client_id`) REFERENCES `User` (`id`);
ALTER TABLE `FavoritePerformer`
    ADD FOREIGN KEY (`performer_id`) REFERENCES `User` (`id`);

-- ToDo
-- На будущее
-- Заполнение таблиц статусов заданий.
/*INSERT INTO `TaskStatus` (`id`, `name`, `text`)
VALUES
    (1, 'new', 'Новае'),
    (2, 'canceled', 'Отменено'),
    (3, 'worked', 'В работе'),
    (4, 'completed', 'Завершено'),
    (5, 'failed', 'Провалено')*/
