const API_URL = '/КР_2_БИВТ_24_4_Алфёров_Сергей_Сергеевич_служащий/Source/api';

async function checkAuth() {
    const res = await fetch(`${API_URL}/auth.php?check=1`);
    return res.json();
}

function saveToLocalStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

function getFromLocalStorage(key) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
}

async function addToFavorites(employeeId) {
    let favorites = getFromLocalStorage('favorite_employees') || [];
    if (!favorites.includes(employeeId)) {
        favorites.push(employeeId);
        saveToLocalStorage('favorite_employees', favorites);
        alert('✅ Добавлен в избранное');
    } else {
        alert('⚠️ Уже в избранном');
    }
}

function removeFromFavorites(employeeId) {
    let favorites = getFromLocalStorage('favorite_employees') || [];
    favorites = favorites.filter(id => id != employeeId);
    saveToLocalStorage('favorite_employees', favorites);
    location.reload();
}

async function getFavorites() {
    const favorites = getFromLocalStorage('favorite_employees') || [];
    const employees = [];
    for (let id of favorites) {
        const res = await fetch(`${API_URL}/users.php?id=${id}`);
        const user = await res.json();
        if (user && !user.error) {
            employees.push(user);
        }
    }
    return employees;
}