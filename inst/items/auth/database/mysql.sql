SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` INT NOT NULL,
  `session_key` VARCHAR(255) NOT NULL,
  `active_time` INT NOT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `ip` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` INT NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_code` INT DEFAULT NULL,
  `email_status` TINYINT(1) NOT NULL DEFAULT 0,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  `login` VARCHAR(50) DEFAULT NULL,
  `active` TINYINT(1) NOT NULL,
  `user_role_id` INT NOT NULL,
  `timezone` INT NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `timezones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `region` VARCHAR(255) NOT NULL,
  `offset` TINYINT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_role` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `num` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `users` (`id`, `email`, `email_code`, `email_status`, `password`, `name`, `login`, `active`, `user_role_id`, `timezone`) VALUES
(1, '{admin_email}', 0000, 1, '{admin_pass}', '{admin_login}', '{admin_login}', 1, 1, 1);

INSERT INTO `timezones` (`region`, `offset`) VALUES
('UTC', '0');

INSERT INTO `user_role` (`id`, `name`, `slug`) VALUES
(1, 'Администратор', 'admin'),
(2, 'Пользователь', 'user'),
(3, 'Гость', 'goust');

ALTER TABLE `sessions` ADD PRIMARY KEY (`id`);
ALTER TABLE `sessions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD FOREIGN KEY (`user_role_id`) REFERENCES `user_role`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `users` ADD FOREIGN KEY (`timezone`) REFERENCES `timezones`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;