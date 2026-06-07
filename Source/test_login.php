<?php
require_once 'classes/Database.php';

global $pdo;

echo "<h1>Диагностика входа</h1>";

// Получаем данные админа из БД
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<p>✅ Пользователь admin найден в БД</p>";
    echo "<p>Email: " . $user['email'] . "</p>";
    echo "<p>Хэш пароля: " . $user['password'] . "</p>";
    
    $password = 'admin123';
    if (password_verify($password, $user['password'])) {
        echo "<p style='color:green'>✅ Пароль '$password' ВЕРНЫЙ!</p>";
    } else {
        echo "<p style='color:red'>❌ Пароль '$password' НЕВЕРНЫЙ!</p>";
        // Создаём новый хэш для проверки
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        echo "<p>Новый хэш для '$password': <code>$new_hash</code></p>";
        echo "<p>Обновите БД этим хэшем!</p>";
    }
} else {
    echo "<p style='color:red'>❌ Пользователь admin не найден в БД</p>";
}

// Проверяем, работает ли соединение с БД
echo "<h2>Все пользователи в БД:</h2>";
$stmt = $pdo->query("SELECT id, username, role FROM users");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<p>ID: {$row['id']}, Логин: {$row['username']}, Роль: {$row['role']}</p>";
}
?>