<?php require_once 'includes/header.php'; ?>
<div class="container">
    <h1>📝 Регистрация</h1>
    <div class="form-container">
        <div class="form-group">
            <label>ФИО *</label>
            <input type="text" id="name" class="form-control" placeholder="Иванов Иван Иванович">
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" id="email" class="form-control" placeholder="ivan@example.com">
        </div>
        <div class="form-group">
            <label>Возраст</label>
            <input type="number" id="age" class="form-control" placeholder="25">
        </div>
        <div class="form-group">
            <label>Логин *</label>
            <input type="text" id="username" class="form-control" placeholder="ivanov">
        </div>
        <div class="form-group">
            <label>Пароль *</label>
            <input type="password" id="password" class="form-control" placeholder="минимум 6 символов">
        </div>
        <button onclick="register()" class="btn btn-primary" style="width:100%; background: linear-gradient(135deg, #00ff9d, #00cc7d);">📝 Зарегистрироваться</button>
        <p style="margin-top: 20px; text-align: center; color: #888;">
            Уже есть аккаунт? <a href="login.php" style="color: #00d4ff;">Войти</a>
        </p>
    </div>
</div>

<script>
async function register() {
    const data = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        age: document.getElementById('age').value || null,
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        role: 'employee'
    };
    if(!data.name || !data.email || !data.username || !data.password) {
        alert('Заполните все обязательные поля');
        return;
    }
    const res = await fetch('/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/users.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    const result = await res.json();
    if(result.success) {
        alert('Регистрация успешна! Теперь можно войти.');
        window.location.href = 'login.php';
    } else {
        alert('Ошибка: ' + result.message);
    }
}
</script>
<?php require_once 'includes/footer.php'; ?>