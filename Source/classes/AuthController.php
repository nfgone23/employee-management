<?php
class AuthController {
    private $logger;
    
    public function __construct() {
        $this->logger = Logger::getInstance();
    }
    
    public function login($username, $password) {
        $user = Database::findUserByUsername($username);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            Database::updateUserSession($user);
            $this->logger->log($user['username'], 'LOGIN_SUCCESS', 'Успешный вход');
            return ['success' => true, 'role' => $user['role']];
        }
        
        $this->logger->log($username, 'LOGIN_FAILED', 'Неверный логин или пароль');
        return ['success' => false, 'message' => 'Неверный логин или пароль'];
    }
    
    public function check() {
        if (isset($_SESSION['user_id'])) {
            return [
                'authenticated' => true,
                'role' => $_SESSION['role'],
                'user_id' => $_SESSION['user_id'],
                'user_name' => $_SESSION['user_name']
            ];
        }
        return ['authenticated' => false];
    }
    
    public function logout() {
        if (isset($_SESSION['username'])) {
            $this->logger->log($_SESSION['username'], 'LOGOUT', 'Выход из системы');
        }
        session_destroy();
        return true;
    }
}
?>