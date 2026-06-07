<?php
session_start();
header('Content-Type: application/json');
require_once '../classes/Database.php';
require_once '../classes/UserService.php';

$userService = new UserService();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET' && !$id) {
    $users = $userService->getAll();
    echo json_encode(array_values($users));
}

else if ($method === 'GET' && $id) {
    $user = $userService->getById($id);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Пользователь не найден']);
        exit;
    }
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id)) {
        http_response_code(403);
        echo json_encode(['error' => 'Доступ запрещён']);
        exit;
    }
    echo json_encode($user);
}

else if ($method === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['name']) || empty($data['email']) || empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
        exit;
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Неверный email']);
        exit;
    }
    
    $result = $userService->create($data);
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Сотрудник добавлен']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Ошибка добавления']);
    }
}

else if ($method === 'PUT' && $id) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $userService->update($id, $data);
    echo json_encode(['success' => true, 'message' => 'Сотрудник обновлён']);
}

else if ($method === 'DELETE' && $id) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);
        exit;
    }
    
    $result = $userService->delete($id);
    echo json_encode(['success' => true, 'message' => 'Сотрудник удалён']);
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается']);
}
?>