<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/header.php';
?>

<div class="container">
    <h1>👤 Мой профиль</h1>
    
    <div class="profile-card" id="profileInfo">Загрузка...</div>
    
    <h2>⭐ Избранные сотрудники</h2>
    <div id="favoritesList" class="favorites-grid"></div>
    
    <div style="margin-top: 30px; text-align: center;">
        <a href="index.php" class="btn btn-primary">🏠 На главную</a>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="admin.php" class="btn btn-primary" style="background: linear-gradient(135deg, #ffa500, #ff8c00);">👑 Админ-панель</a>
        <?php endif; ?>
    </div>
</div>

<script>
const userId = <?= json_encode($_SESSION['user_id']) ?>;

async function loadProfile() {
    const res = await fetch(`/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/users.php?id=${userId}`);
    let user = await res.json();
    
    if (user.error) {
        document.getElementById('profileInfo').innerHTML = '<p>Ошибка загрузки профиля</p>';
        return;
    }
    
    document.getElementById('profileInfo').innerHTML = `
        <p><strong>👤 ФИО:</strong> ${user.name}</p>
        <p><strong>📧 Email:</strong> ${user.email}</p>
        <p><strong>📅 Возраст:</strong> ${user.age || '—'}</p>
        <p><strong>👑 Роль:</strong> ${user.role === 'admin' ? 'Администратор' : 'Сотрудник'}</p>
    `;
}

async function loadFavorites() {
    let favs = JSON.parse(localStorage.getItem('favorite_employees') || '[]');
    if(favs.length === 0) {
        document.getElementById('favoritesList').innerHTML = '<div class="favorite-card" style="text-align:center">Нет избранных сотрудников</div>';
        return;
    }
    
    let html = '';
    for(let id of favs) {
        const res = await fetch(`/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/users.php?id=${id}`);
        let user = await res.json();
        
        if (user.error) continue;
        
        html += `
            <div class="favorite-card">
                <strong>👤 ${user.name}</strong>
                <p>📧 ${user.email}</p>
                <p>📅 Возраст: ${user.age || '—'}</p>
                <button onclick="removeFav(${id})" style="margin-top:10px; padding:8px 16px; background: transparent; border: 1px solid #ff4444; color: #ff4444; border-radius: 30px; cursor: pointer;">🗑️ Удалить</button>
            </div>
        `;
    }
    document.getElementById('favoritesList').innerHTML = html;
}

function removeFav(id) {
    let favs = JSON.parse(localStorage.getItem('favorite_employees') || '[]');
    favs = favs.filter(f => f != id);
    localStorage.setItem('favorite_employees', JSON.stringify(favs));
    loadFavorites();
}

loadProfile();
loadFavorites();
</script>

<?php require_once 'includes/footer.php'; ?>