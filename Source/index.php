<?php
session_start();
require_once 'includes/header.php';
require_once 'classes/Database.php';

$employeesArray = Database::getTopEmployees();
$statsData = Database::getStats();

$total = $statsData['total'];
$admins = $statsData['admins'];
$employees = $statsData['employees'];

$topEmployeesJson = json_encode($employeesArray);
$statsJson = json_encode($statsData);
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userName = $_SESSION['user_name'] ?? '';
?>

<div class="container">
    <h1>👥 Сотрудники компании</h1>
    
    <?php if($isLoggedIn): ?>
        <div class="welcome">
            <span>👋 Добро пожаловать, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
            <span class="role-badge"><?php echo $isAdmin ? '👑 Администратор' : '👨‍💼 Сотрудник'; ?></span>
        </div>
    <?php endif; ?>
    
    <div class="widgets-row">
        <div class="widget-card">
            <div class="widget-title">🏆 Топ сотрудников</div>
            <div class="widget-body" id="topWidget"></div>
        </div>
        
        <div class="widget-card">
            <div class="widget-title">📊 Статистика</div>
            <div class="widget-body" id="statsWidget"></div>
        </div>
        
        <div class="widget-card">
            <div class="widget-title">⚡ Быстрые действия</div>
            <div class="widget-body" id="actionsWidget"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/react@18.2.0/umd/react.development.js"></script>
<script src="https://cdn.jsdelivr.net/npm/react-dom@18.2.0/umd/react-dom.development.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@babel/standalone/babel.min.js"></script>
<script type="text/babel">

const topEmployees = <?php echo $topEmployeesJson; ?>;
const statsData = <?php echo $statsJson; ?>;
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
const userName = <?php echo json_encode($userName); ?>;

const TopWidget = () => {
    if (topEmployees.length === 0) {
        return <div style={{ textAlign: 'center', color: '#888', padding: '30px' }}>📭 Нет данных</div>;
    }
    
    return (
        <div>
            {topEmployees.map((emp, idx) => (
                <div key={emp.id} className="employee-item">
                    <div>
                        <div className="employee-name"><strong>{emp.name}</strong></div>
                        <div style={{ fontSize: '12px', color: '#888' }}>
                            {emp.role === 'admin' ? '👑 Администратор' : '👨‍💼 Сотрудник'}
                        </div>
                    </div>
                    <div className="employee-age">{emp.age || '?'} лет</div>
                </div>
            ))}
        </div>
    );
};

const StatsWidget = () => {
    return (
        <div className="stats-row">
            <div className="stat-box">
                <div className="stat-number">{statsData.total}</div>
                <div className="stat-label">Всего</div>
            </div>
            <div className="stat-box">
                <div className="stat-number">{statsData.admins}</div>
                <div className="stat-label">Админы</div>
            </div>
            <div className="stat-box">
                <div className="stat-number">{statsData.employees}</div>
                <div className="stat-label">Сотрудники</div>
            </div>
        </div>
    );
};

const ActionsWidget = () => {
    if (!isLoggedIn) {
        return (
            <div className="actions-list">
                <div style={{ textAlign: 'center', padding: '20px', color: '#888' }}>
                    🔒 Авторизуйтесь, чтобы увидеть быстрые действия
                </div>
            </div>
        );
    }
    
    return (
        <div className="actions-list">
            <div style={{ textAlign: 'center', marginBottom: '10px', padding: '8px', background: 'rgba(0,212,255,0.1)', borderRadius: '12px' }}>
                👋 Привет, <strong>{userName}</strong>
            </div>
            <a href="profile.php" className="action-btn action-btn-primary">👤 Мой профиль</a>
            {isAdmin && <a href="admin.php" className="action-btn action-btn-warning">👑 Админ-панель</a>}
            <a href="api/auth.php?logout=1" className="action-btn action-btn-danger">🚪 Выйти</a>
        </div>
    );
};

const topContainer = document.getElementById('topWidget');
const statsContainer = document.getElementById('statsWidget');
const actionsContainer = document.getElementById('actionsWidget');

if (topContainer) ReactDOM.createRoot(topContainer).render(<TopWidget />);
if (statsContainer) ReactDOM.createRoot(statsContainer).render(<StatsWidget />);
if (actionsContainer) ReactDOM.createRoot(actionsContainer).render(<ActionsWidget />);

</script>

<?php require_once 'includes/footer.php'; ?>