<?php
class Database {
    private static $pdo = null;
    
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $host = 'localhost';
                $dbname = 'employee_db';
                $username = 'root';
                $password = '';
                
                self::$pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Ошибка подключения к БД: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
    
    public static function getAllUsers() {
        $pdo = self::getConnection();
        $stmt = $pdo->query("SELECT * FROM v_users_with_details ORDER BY id");
        return $stmt->fetchAll();
    }
    
    public static function getUserById($id) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public static function createUser($data) {
        $pdo = self::getConnection();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$data['email'], $data['username']]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Email или логин уже существует'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, age, username, password_hash, role, department_id, position_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['age'] ?? null,
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'employee',
            $data['department_id'] ?? null,
            $data['position_id'] ?? null
        ]);
        
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    }
    
    public static function updateUser($id, $data) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, email = ?, age = ?, role = ?, department_id = ?, position_id = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['age'] ?? null,
            $data['role'] ?? 'employee',
            $data['department_id'] ?? null,
            $data['position_id'] ?? null,
            $id
        ]);
        
        return ['success' => true];
    }
    
    public static function deleteUser($id) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }
    
    public static function getAllLogs() {
        $pdo = self::getConnection();
        $stmt = $pdo->query("SELECT * FROM logs ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public static function addLog($username, $action, $ip, $details = null) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO logs (username, action, ip_address, details)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$username, $action, $ip, $details]);
        return true;
    }
    
    public static function deleteLog($id) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM logs WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }
    
    public static function clearOldLogs($days = 30) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$days]);
        return ['success' => true];
    }
    
    public static function getStats() {
        $pdo = self::getConnection();
        $total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $admins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        $employees = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn();
        
        return [
            'total' => (int)$total,
            'admins' => (int)$admins,
            'employees' => (int)$employees
        ];
    }
    
    public static function getTopEmployees() {
        $pdo = self::getConnection();
        $stmt = $pdo->query("SELECT id, name, age, role FROM users ORDER BY age DESC LIMIT 5");
        return $stmt->fetchAll();
    }
    
    public static function findUserByUsername($username) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public static function updateUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
    }
}
?>