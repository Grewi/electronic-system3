SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_key` varchar(255) NOT NULL,
  `active_time` int(11) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_code` int(5) DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `users` (`id`, `email`, `email_code`, `email_status`, `password`, `name`, `login`, `active`, `user_role_id`, `date_create`) VALUES
(1, '{admin_email}', 0000, 1, '{admin_pass}', '{admin_login}', '{admin_login}', 1, 1, '2022-10-21 05:54:22');

INSERT INTO `user_role` (`id`, `name`, `slug`) VALUES
(1, 'Администратор', 'admin'),
(2, 'Пользователь', 'user'),
(3, 'Гость', 'goust');

ALTER TABLE `sessions` ADD PRIMARY KEY (`id`);
ALTER TABLE `sessions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD FOREIGN KEY (`user_role_id`) REFERENCES `user_role`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;