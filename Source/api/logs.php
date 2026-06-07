<?php
session_start();
header('Content-Type: application/json');
require_once '../classes/Database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Доступ запрещён', 'logs' => []]);
    exit;
}

$logs = Database::getAllLogs();
echo json_encode(['success' => true, 'logs' => $logs]);
?>