<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/header.php';
?>

<div class="container">
    <h1>👑 Админ-панель</h1>
    
    <div class="admin-bar">
        <button onclick="showForm()" class="admin-btn">➕ Добавить сотрудника</button>
        <button onclick="loadData()" class="admin-btn">🔄 Обновить</button>
        <button onclick="showLogs()" class="admin-btn">📋 Логи</button>
    </div>
    
    <div id="addForm" style="display:none; background:#0f1219; border:1px solid #1a1f2e; border-radius:24px; padding:24px; margin-bottom:32px;">
        <h3 style="margin-bottom:20px; color:#00d4ff;">➕ Добавление сотрудника</h3>
        <input type="text" id="newName" class="form-control" placeholder="ФИО *" style="margin-bottom:12px;">
        <input type="email" id="newEmail" class="form-control" placeholder="Email *" style="margin-bottom:12px;">
        <input type="number" id="newAge" class="form-control" placeholder="Возраст" style="margin-bottom:12px;">
        <input type="text" id="newUsername" class="form-control" placeholder="Логин *" style="margin-bottom:12px;">
        <input type="password" id="newPassword" class="form-control" placeholder="Пароль *" style="margin-bottom:12px;">
        <select id="newRole" class="form-control" style="margin-bottom:20px;">
            <option value="employee">Сотрудник</option>
            <option value="admin">Администратор</option>
        </select>
        <button onclick="addUser()" class="btn btn-primary" style="background: #00ff9d; color:#0a0e17;">💾 Сохранить</button>
        <button onclick="hideForm()" class="btn" style="background: transparent; border-color:#ff4444; color:#ff4444;">❌ Отмена</button>
    </div>
    
    <h2>📋 Список сотрудников</h2>
    <div class="table-container" id="usersTable">Загрузка...</div>
    
    <div id="logsBlock" style="display:none; margin-top:32px;">
        <h2>📋 Журнал действий</h2>
        <div class="table-container" id="logsList"></div>
    </div>
</div>

<script>
async function loadData() {
    const res = await fetch('/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/users.php');
    let users = await res.json();
    
    if (!Array.isArray(users)) {
        users = Object.values(users);
    }
    
    if(users.length > 0) {
        let html = '<table class="data-table"><thead><tr><th>ID</th><th>ФИО</th><th>Email</th><th>Возраст</th><th>Роль</th><th>⭐</th></tr></thead><tbody>';
        users.forEach(u => {
            let favBtn = `<button onclick="addToFavorites(${u.id})" style="background: transparent; border: 1px solid #ffa500; color: #ffa500; padding: 5px 10px; border-radius: 20px; cursor: pointer;">⭐</button>`;
            html += `<tr>
                <td>${u.id}</td>
                <td><strong>${u.name}</strong></td>
                <td>${u.email}</td>
                <td>${u.age || '—'}</td>
                <td>${u.role === 'admin' ? '<span style="color:#00d4ff">👑 Админ</span>' : '👨‍💼 Сотрудник'}</td>
                <td>${favBtn}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('usersTable').innerHTML = html;
    } else {
        document.getElementById('usersTable').innerHTML = '<p>Нет сотрудников</p>';
    }
}

function addToFavorites(id) {
    let favorites = JSON.parse(localStorage.getItem('favorite_employees') || '[]');
    if (!favorites.includes(id)) {
        favorites.push(id);
        localStorage.setItem('favorite_employees', JSON.stringify(favorites));
        alert('✅ Добавлен в избранное');
    } else {
        alert('⚠️ Уже в избранном');
    }
}

function showForm() { document.getElementById('addForm').style.display = 'block'; }
function hideForm() { document.getElementById('addForm').style.display = 'none'; }

async function addUser() {
    const data = {
        name: document.getElementById('newName').value,
        email: document.getElementById('newEmail').value,
        age: document.getElementById('newAge').value || null,
        username: document.getElementById('newUsername').value,
        password: document.getElementById('newPassword').value,
        role: document.getElementById('newRole').value
    };
    
    const res = await fetch('/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/users.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    const result = await res.json();
    if(result.success) {
        alert(result.message);
        hideForm();
        loadData();
        document.getElementById('newName').value = '';
        document.getElementById('newEmail').value = '';
        document.getElementById('newAge').value = '';
        document.getElementById('newUsername').value = '';
        document.getElementById('newPassword').value = '';
    } else {
        alert('Ошибка: ' + result.message);
    }
}

async function showLogs() {
    const res = await fetch('/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/logs.php');
    const data = await res.json();
    if(data.logs && data.logs.length > 0) {
        let html = '<table class="data-table"><thead><tr><th>Время</th><th>Пользователь</th><th>Действие</th><th>IP</th></tr></thead><tbody>';
        data.logs.forEach(log => {
            html += `<tr>
                <td>${log.created_at}</td>
                <td>${log.username || '-'}</td>
                <td>${log.action}</td>
                <td>${log.ip_address || '-'}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('logsList').innerHTML = html;
    } else {
        document.getElementById('logsList').innerHTML = '<p>Нет записей в логах</p>';
    }
    document.getElementById('logsBlock').style.display = 'block';
}

loadData();
</script>

<?php require_once 'includes/footer.php'; ?>