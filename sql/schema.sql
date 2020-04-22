-- Создание БД.
CREATE DATABASE IF NOT EXISTS taskForce
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

use taskForce;

CREATE TABLE IF NOT EXISTS `task`
(
    `id`            int PRIMARY KEY AUTO_INCREMENT,
    `client_id`     int          NOT NULL,
    `performer_id`  int,
    `status_id`     int,
    `category_id`   int          NOT NULL,
    `description`   varchar(255) NOT NULL,
    `details`       text         NOT NULL,
    `budget`        int,
    `creation_date` datetime,
    `finish_date`   datetime,
    `address`       varchar(255), -- на сайте не вижу (есть в файле с данными. ну пускай будет. тут улица, дом и квартира походу).

    -- геоданные
    `latitude`      decimal(9, 7),
    `longitude`     decimal(9, 7),
    `locality_id`   int
);

CREATE TABLE IF NOT EXISTS `task_status`
(
    `id`         int PRIMARY KEY AUTO_INCREMENT,
    `name`       varchar(255) UNIQUE NOT NULL,
    `text`       varchar(255), -- название, которое выводится на фронт.
    `image_path` varchar(255)
);

CREATE TABLE IF NOT EXISTS `task_related_file`
(
    `id`       int PRIMARY KEY AUTO_INCREMENT,
    `task_id`  int          NOT NULL,
    `filepath` varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `user`
(
    `id`          int PRIMARY KEY AUTO_INCREMENT,
    `email`       varchar(255) UNIQUE NOT NULL,
    `password`    varchar(255)        NOT NULL,
    `full_name`   varchar(255)        NOT NULL,
    `add_data`    date, -- Дата добавления записи в таблицу.

    -- геоданные
    `latitude`    decimal(9, 7), -- это тут теперь не нужно? (потому что есть в геоданных населенного пункта)
    `longitude`   decimal(9, 7), -- это тут теперь не нужно? (потому что есть в геоданных населенного пункта)
    `locality_id` int
);

CREATE TABLE `profile`
(
    `id`                           int PRIMARY KEY AUTO_INCREMENT,
    `user_id`                      int,
    `avatar_filepath`              varchar(255),
    `address`                      varchar(255), -- на сайте не вижу (есть в файле с данными. ну пускай будет. тут улица, дом и квартира походу).
    `birthday`                     date,
    `about`                        varchar(255),
    `phone`                        varchar(255),
    `skype`                        varchar(255),
    `another_messenger`            varchar(255),
    `view_count`                   int DEFAULT 0,
    `last_activity_date`           datetime,
    `new_message_notification`     bool,
    `new_response_notification`    bool,
    `new_task_action_notification` bool,
    `show_contacts_only_to_client` bool,
    `hide_profile`                 bool
);

CREATE TABLE IF NOT EXISTS `category`
(
    `id`        int PRIMARY KEY AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `icon_name` varchar(255) NOT NULL -- название иконки (добавил потому что есть такое поле в файле categories.csv).
);

-- Специализации исполнителя
CREATE TABLE IF NOT EXISTS `user_specialization`
(
    `id`          int PRIMARY KEY AUTO_INCREMENT,
    `user_id`     int NOT NULL,
    `category_id` int NOT NULL
);

-- Фото работ исполнителя
CREATE TABLE IF NOT EXISTS `user_portfolio`
(
    `id`       int PRIMARY KEY AUTO_INCREMENT,
    `user_id`  int NOT NULL,
    `filepath` varchar(255)
);

-- Населенный пункт
CREATE TABLE IF NOT EXISTS `locality`
(
    `id`        int PRIMARY KEY AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `latitude`  decimal(9, 7),
    `longitude` decimal(9, 7)
);

-- Отклик
CREATE TABLE IF NOT EXISTS `response`
(
    `id`            int PRIMARY KEY AUTO_INCREMENT,
    `add_data`      date, -- Дата добавления записи.
    `candidate_id`  int, -- id претендента на выполнение задания
    `task_id`       int,
    `offered_price` int
);

-- Отзыв
CREATE TABLE IF NOT EXISTS `review`
(
    `id`      int PRIMARY KEY AUTO_INCREMENT,
    `add_data`      date, -- Дата добавления записи.
    `task_id` int NOT NULL,
    `rate`    int,
    `comment` varchar(255)
);

CREATE TABLE IF NOT EXISTS `message`
(
    `id`          int PRIMARY KEY AUTO_INCREMENT,
    `sender_id`   int NOT NULL,
    `receiver_id` int NOT NULL,
    `task_id`     int NOT NULL,
    `send_date`   datetime
);

-- Избранные исполнители
CREATE TABLE `favorite_performer`
(
    `id`           int PRIMARY KEY AUTO_INCREMENT,
    `client_id`    int NOT NULL,
    `performer_id` int NOT NULL
);

-- Добавление внешних ключей.
ALTER TABLE `task`
    ADD FOREIGN KEY (`client_id`) REFERENCES `user` (`id`);
ALTER TABLE `task`
    ADD FOREIGN KEY (`performer_id`) REFERENCES `user` (`id`);
ALTER TABLE `task`
    ADD FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
ALTER TABLE `task`
    ADD FOREIGN KEY (`status_id`) REFERENCES `task_status` (`id`);
ALTER TABLE `task`
    ADD FOREIGN KEY (`locality_id`) REFERENCES `Locality` (`id`);

ALTER TABLE `review`
    ADD FOREIGN KEY (`task_id`) REFERENCES `task` (`id`);

ALTER TABLE `task_related_file`
    ADD FOREIGN KEY (`task_id`) REFERENCES `task` (`id`);

ALTER TABLE `user_specialization`
    ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
ALTER TABLE `user_specialization`
    ADD FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

ALTER TABLE `user`
    ADD FOREIGN KEY (`locality_id`) REFERENCES `locality` (`id`);

ALTER TABLE `profile`
    ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

ALTER TABLE `user_portfolio`
    ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

ALTER TABLE `response`
    ADD FOREIGN KEY (`candidate_id`) REFERENCES `user` (`id`);
ALTER TABLE `response`
    ADD FOREIGN KEY (`task_id`) REFERENCES `task` (`id`);
ALTER TABLE `response`
    ADD CONSTRAINT unique_response_on_task UNIQUE KEY (`task_id`, `candidate_id`);

ALTER TABLE `message`
    ADD FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`);
ALTER TABLE `Message`
    ADD FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`);
ALTER TABLE `Message`
    ADD FOREIGN KEY (`task_id`) REFERENCES `task` (`id`);

ALTER TABLE `favorite_performer`
    ADD FOREIGN KEY (`client_id`) REFERENCES `user` (`id`);
ALTER TABLE `favorite_performer`
    ADD FOREIGN KEY (`performer_id`) REFERENCES `user` (`id`);

-- ToDo
-- На будущее
-- Заполнение таблиц статусов заданий.
/*INSERT INTO `taskStatus` (`id`, `name`, `text`)
VALUES
    (1, 'new', 'Новае'),
    (2, 'canceled', 'Отменено'),
    (3, 'worked', 'В работе'),
    (4, 'completed', 'Завершено'),
    (5, 'failed', 'Провалено')*/
