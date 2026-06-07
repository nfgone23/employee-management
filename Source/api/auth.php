<?php
session_start();
header('Content-Type: application/json');
require_once '../classes/Database.php';
require_once '../classes/Logger.php';
require_once '../classes/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check'])) {
    echo json_encode($auth->check());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['logout'])) {
    $auth->logout();
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $auth->login($data['username'] ?? '', $data['password'] ?? '');
    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Метод не поддерживается']);
?>