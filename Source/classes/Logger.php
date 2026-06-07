<?php
class Logger {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }
    
    public function log($username, $action, $details = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return Database::addLog($username, $action, $ip, $details);
    }
    
    public function getAllLogs() {
        return Database::getAllLogs();
    }
}
?>