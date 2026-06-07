-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2026 at 08:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_user` (IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_age` INT, IN `p_username` VARCHAR(50), IN `p_password` VARCHAR(255), IN `p_role` VARCHAR(20), IN `p_department_id` INT, IN `p_position_id` INT)   BEGIN
    INSERT INTO users (name, email, age, username, password_hash, role, department_id, position_id)
    VALUES (p_name, p_email, p_age, p_username, p_password, p_role, p_department_id, p_position_id);
    
    INSERT INTO logs (username, action, ip_address, details)
    VALUES (p_username, 'CREATE_USER', '127.0.0.1', CONCAT('Добавлен пользователь: ', p_name));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_clear_old_logs` (IN `p_days` INT)   BEGIN
    DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL p_days DAY);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_user` (IN `p_id` INT)   BEGIN
    DECLARE user_username VARCHAR(50);
    DECLARE user_name VARCHAR(100);
    
    SELECT username, name INTO user_username, user_name FROM users WHERE id = p_id;
    
    INSERT INTO logs (username, action, ip_address, details)
    VALUES (user_username, 'DELETE_USER', '127.0.0.1', CONCAT('Удалён пользователь: ', user_name));
    
    DELETE FROM users WHERE id = p_id;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_log_count_by_action` (`p_action` VARCHAR(50)) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE log_count INT;
    SELECT COUNT(*) INTO log_count FROM logs WHERE action = p_action;
    RETURN log_count;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `get_username_by_id` (`p_id` INT) RETURNS VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DETERMINISTIC BEGIN
    DECLARE user_name VARCHAR(100);
    SELECT name INTO user_name FROM users WHERE id = p_id;
    RETURN user_name;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `get_user_count_by_role` (`p_role` VARCHAR(20)) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE user_count INT;
    SELECT COUNT(*) INTO user_count FROM users WHERE role = p_role;
    RETURN user_count;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'IT', 'Информационные технологии и разработка', '2026-06-07 18:15:22'),
(2, 'HR', 'Отдел кадров и управления персоналом', '2026-06-07 18:15:22'),
(3, 'Sales', 'Отдел продаж и работы с клиентами', '2026-06-07 18:15:22'),
(4, 'Finance', 'Финансовый отдел', '2026-06-07 18:15:22');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `username`, `action`, `ip_address`, `details`, `created_at`) VALUES
(1, NULL, 'admin', 'LOGIN_SUCCESS', '127.0.0.1', 'Успешный вход', '2026-06-07 18:15:22'),
(2, NULL, 'admin', 'LOGOUT', '127.0.0.1', 'Выход из системы', '2026-06-07 18:15:22'),
(3, NULL, 'ivanov', 'LOGIN_SUCCESS', '127.0.0.1', 'Успешный вход', '2026-06-07 18:15:22'),
(4, NULL, 'test', 'LOGIN_FAILED', '127.0.0.1', 'Неверный пароль', '2026-06-07 18:15:22'),
(5, NULL, 'admin', 'LOGIN_SUCCESS', '::1', 'Успешный вход', '2026-06-07 18:18:30');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `level` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`, `level`, `created_at`) VALUES
(1, 'Junior', 1, '2026-06-07 18:15:22'),
(2, 'Middle', 2, '2026-06-07 18:15:22'),
(3, 'Senior', 3, '2026-06-07 18:15:22'),
(4, 'Team Lead', 4, '2026-06-07 18:15:22'),
(5, 'Department Head', 5, '2026-06-07 18:15:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','employee') DEFAULT 'employee',
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `age`, `username`, `password_hash`, `role`, `department_id`, `position_id`, `created_at`, `updated_at`) VALUES
(1, 'Администратор', 'admin@test.com', 30, 'admin', '$2y$10$0yF5xFGt6S086jgPHV3kGOaGLUYwTLXKL8IifNBX8sSlONMzSKWs6', 'admin', 1, 5, '2026-06-07 18:15:22', '2026-06-07 18:15:22'),
(2, 'Иванов Иван', 'ivan@test.com', 25, 'ivanov', '$2y$10$nWW5n2kWUqb5/CWvMKWJAOFk4DQ9XE4qkIA1kBgW1P1zVL4QyfwDe', 'employee', 2, 2, '2026-06-07 18:15:22', '2026-06-07 18:15:22'),
(3, 'Петрова Мария', 'maria@test.com', 28, 'petrova', '$2y$10$XARl39v7KHcIRSgZFTytBOlc/srlMwU6vo9SXSrEYKi57D7xGxLxW', 'employee', 1, 3, '2026-06-07 18:15:22', '2026-06-07 18:15:22'),
(4, 'Сидоров Алексей', 'alex@test.com', 35, 'sidorov', '$2y$10$/xtOpXeiDzeaZZn7w7TRcOy1/WfQLVFC5ZXTI2aaUxM/QZ75jDBiy', 'employee', 3, 3, '2026-06-07 18:15:22', '2026-06-07 18:15:22');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `trg_user_delete` BEFORE DELETE ON `users` FOR EACH ROW BEGIN
    INSERT INTO logs (user_id, username, action, ip_address, details)
    VALUES (OLD.id, OLD.username, 'USER_DELETE', '127.0.0.1', CONCAT('Удалён пользователь: ', OLD.name));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO logs (user_id, username, action, ip_address, details)
    VALUES (NEW.id, NEW.username, 'USER_INSERT', '127.0.0.1', CONCAT('Добавлен пользователь: ', NEW.name));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF OLD.name != NEW.name OR OLD.email != NEW.email OR OLD.role != NEW.role THEN
        INSERT INTO logs (user_id, username, action, ip_address, details)
        VALUES (NEW.id, NEW.username, 'USER_UPDATE', '127.0.0.1', CONCAT('Обновлён пользователь: ', OLD.name, ' → ', NEW.name));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_users`
-- (See below for the actual view)
--
CREATE TABLE `v_active_users` (
`id` int(11)
,`name` varchar(100)
,`email` varchar(100)
,`username` varchar(50)
,`role` enum('admin','employee')
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_recent_logs`
-- (See below for the actual view)
--
CREATE TABLE `v_recent_logs` (
`id` int(11)
,`username` varchar(50)
,`action` varchar(50)
,`ip_address` varchar(45)
,`details` text
,`log_date` date
,`log_time` time
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_users_with_details`
-- (See below for the actual view)
--
CREATE TABLE `v_users_with_details` (
`id` int(11)
,`name` varchar(100)
,`email` varchar(100)
,`age` int(11)
,`username` varchar(50)
,`role` enum('admin','employee')
,`department` varchar(100)
,`position` varchar(100)
,`position_level` int(11)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `v_active_users`
--
DROP TABLE IF EXISTS `v_active_users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_users`  AS SELECT `users`.`id` AS `id`, `users`.`name` AS `name`, `users`.`email` AS `email`, `users`.`username` AS `username`, `users`.`role` AS `role`, `users`.`created_at` AS `created_at` FROM `users` WHERE `users`.`role` = 'employee' ;

-- --------------------------------------------------------

--
-- Structure for view `v_recent_logs`
--
DROP TABLE IF EXISTS `v_recent_logs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_recent_logs`  AS SELECT `logs`.`id` AS `id`, `logs`.`username` AS `username`, `logs`.`action` AS `action`, `logs`.`ip_address` AS `ip_address`, `logs`.`details` AS `details`, cast(`logs`.`created_at` as date) AS `log_date`, cast(`logs`.`created_at` as time) AS `log_time` FROM `logs` WHERE `logs`.`created_at` >= current_timestamp() - interval 7 day ORDER BY `logs`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_users_with_details`
--
DROP TABLE IF EXISTS `v_users_with_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_users_with_details`  AS SELECT `u`.`id` AS `id`, `u`.`name` AS `name`, `u`.`email` AS `email`, `u`.`age` AS `age`, `u`.`username` AS `username`, `u`.`role` AS `role`, `d`.`name` AS `department`, `p`.`name` AS `position`, `p`.`level` AS `position_level`, `u`.`created_at` AS `created_at` FROM ((`users` `u` left join `departments` `d` on(`u`.`department_id` = `d`.`id`)) left join `positions` `p` on(`u`.`position_id` = `p`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
