<?php require_once 'includes/header.php'; ?>
<div class="container">
    <h1>🔐 Вход в систему</h1>
    <div class="form-container">
        <div class="form-group">
            <label>Логин</label>
            <input type="text" id="username" class="form-control" placeholder="Введите логин">
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" id="password" class="form-control" placeholder="Введите пароль">
        </div>
        <button onclick="login()" class="btn btn-primary" style="width:100%">🔐 Войти</button>
        <p style="margin-top: 20px; text-align: center; color: #888;">
            Нет аккаунта? <a href="register.php" style="color: #00d4ff;">Зарегистрироваться</a>
        </p>
    </div>
</div>

<script>
async function login() {
    const res = await fetch('/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api/auth.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            username: document.getElementById('username').value,
            password: document.getElementById('password').value
        })
    });
    const data = await res.json();
    if(data.success) {
        window.location.href = data.role === 'admin' ? 'admin.php' : 'index.php';
    } else {
        alert('Ошибка: ' + data.message);
    }
}
</script>
<?php require_once 'includes/footer.php'; ?>