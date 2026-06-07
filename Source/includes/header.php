<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сотрудники</title>
    <link rel="stylesheet" href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/assets/js/script.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/index.php">👥 Сотрудники</a>
            </div>
            <div class="nav-menu">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <span class="user-role"><?php echo $_SESSION['role'] === 'admin' ? 'Админ' : 'Сотрудник'; ?></span>
                        <a href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/profile.php" class="nav-btn nav-btn-profile">👤 Профиль</a>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                            <a href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/admin.php" class="nav-btn nav-btn-admin">👑 Админка</a>
                        <?php endif; ?>
                        <a href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/auth.php?logout=1" class="nav-btn nav-btn-logout">🚪 Выйти</a>
                    </div>
                <?php else: ?>
                    <a href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/login.php" class="nav-btn nav-btn-login">🔐 Вход</a>
                    <a href="/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/register.php" class="nav-btn nav-btn-register">📝 Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">