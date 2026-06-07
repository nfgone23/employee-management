CREATE DATABASE IF NOT EXISTS employee_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE employee_db;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    age INT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    department_id INT NULL,
    position_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    level INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;
ALTER TABLE users ADD FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL;

INSERT INTO departments (id, name, description) VALUES
(1, 'IT', 'Информационные технологии и разработка'),
(2, 'HR', 'Отдел кадров и управления персоналом'),
(3, 'Sales', 'Отдел продаж и работы с клиентами'),
(4, 'Finance', 'Финансовый отдел');

INSERT INTO positions (id, name, level) VALUES
(1, 'Junior', 1),
(2, 'Middle', 2),
(3, 'Senior', 3),
(4, 'Team Lead', 4),
(5, 'Department Head', 5);

INSERT INTO users (id, name, email, age, username, password_hash, role, department_id, position_id) VALUES
(1, 'Администратор', 'admin@test.com', 30, 'admin', '$2y$10$0yF5xFGt6S086jgPHV3kGOaGLUYwTLXKL8IifNBX8sSlONMzSKWs6', 'admin', 1, 5),
(2, 'Иванов Иван', 'ivan@test.com', 25, 'ivanov', '$2y$10$nWW5n2kWUqb5/CWvMKWJAOFk4DQ9XE4qkIA1kBgW1P1zVL4QyfwDe', 'employee', 2, 2),
(3, 'Петрова Мария', 'maria@test.com', 28, 'petrova', '$2y$10$XARl39v7KHcIRSgZFTytBOlc/srlMwU6vo9SXSrEYKi57D7xGxLxW', 'employee', 1, 3),
(4, 'Сидоров Алексей', 'alex@test.com', 35, 'sidorov', '$2y$10$/xtOpXeiDzeaZZn7w7TRcOy1/WfQLVFC5ZXTI2aaUxM/QZ75jDBiy', 'employee', 3, 3);

INSERT INTO logs (username, action, ip_address, details) VALUES
('admin', 'LOGIN_SUCCESS', '127.0.0.1', 'Успешный вход'),
('admin', 'LOGOUT', '127.0.0.1', 'Выход из системы'),
('ivanov', 'LOGIN_SUCCESS', '127.0.0.1', 'Успешный вход'),
('test', 'LOGIN_FAILED', '127.0.0.1', 'Неверный пароль');

CREATE OR REPLACE VIEW v_active_users AS
SELECT id, name, email, username, role, created_at
FROM users
WHERE role = 'employee';

CREATE OR REPLACE VIEW v_users_with_details AS
SELECT 
    u.id,
    u.name,
    u.email,
    u.age,
    u.username,
    u.role,
    d.name AS department,
    p.name AS position,
    p.level AS position_level,
    u.created_at
FROM users u
LEFT JOIN departments d ON u.department_id = d.id
LEFT JOIN positions p ON u.position_id = p.id;

CREATE OR REPLACE VIEW v_recent_logs AS
SELECT 
    id,
    username,
    action,
    ip_address,
    details,
    DATE(created_at) AS log_date,
    TIME(created_at) AS log_time
FROM logs
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC;

DELIMITER //
CREATE FUNCTION get_user_count_by_role(p_role VARCHAR(20))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE user_count INT;
    SELECT COUNT(*) INTO user_count FROM users WHERE role = p_role;
    RETURN user_count;
END //
DELIMITER ;

DELIMITER //
CREATE FUNCTION get_username_by_id(p_id INT)
RETURNS VARCHAR(100)
DETERMINISTIC
BEGIN
    DECLARE user_name VARCHAR(100);
    SELECT name INTO user_name FROM users WHERE id = p_id;
    RETURN user_name;
END //
DELIMITER ;

DELIMITER //
CREATE FUNCTION get_log_count_by_action(p_action VARCHAR(50))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE log_count INT;
    SELECT COUNT(*) INTO log_count FROM logs WHERE action = p_action;
    RETURN log_count;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_add_user(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_age INT,
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_role VARCHAR(20),
    IN p_department_id INT,
    IN p_position_id INT
)
BEGIN
    INSERT INTO users (name, email, age, username, password_hash, role, department_id, position_id)
    VALUES (p_name, p_email, p_age, p_username, p_password, p_role, p_department_id, p_position_id);
    
    INSERT INTO logs (username, action, ip_address, details)
    VALUES (p_username, 'CREATE_USER', '127.0.0.1', CONCAT('Добавлен пользователь: ', p_name));
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_delete_user(IN p_id INT)
BEGIN
    DECLARE user_username VARCHAR(50);
    DECLARE user_name VARCHAR(100);
    
    SELECT username, name INTO user_username, user_name FROM users WHERE id = p_id;
    
    INSERT INTO logs (username, action, ip_address, details)
    VALUES (user_username, 'DELETE_USER', '127.0.0.1', CONCAT('Удалён пользователь: ', user_name));
    
    DELETE FROM users WHERE id = p_id;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_clear_old_logs(IN p_days INT)
BEGIN
    DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL p_days DAY);
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO logs (user_id, username, action, ip_address, details)
    VALUES (NEW.id, NEW.username, 'USER_INSERT', '127.0.0.1', CONCAT('Добавлен пользователь: ', NEW.name));
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_user_update
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.name != NEW.name OR OLD.email != NEW.email OR OLD.role != NEW.role THEN
        INSERT INTO logs (user_id, username, action, ip_address, details)
        VALUES (NEW.id, NEW.username, 'USER_UPDATE', '127.0.0.1', CONCAT('Обновлён пользователь: ', OLD.name, ' → ', NEW.name));
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_user_delete
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO logs (user_id, username, action, ip_address, details)
    VALUES (OLD.id, OLD.username, 'USER_DELETE', '127.0.0.1', CONCAT('Удалён пользователь: ', OLD.name));
END //
DELIMITER ;