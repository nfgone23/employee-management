<?php
class UserService {
    
    public function getAll() {
        return Database::getAllUsers();
    }
    
    public function getById($id) {
        return Database::getUserById($id);
    }
    
    public function create($data) {
        return Database::createUser($data);
    }
    
    public function update($id, $data) {
        return Database::updateUser($id, $data);
    }
    
    public function delete($id) {
        return Database::deleteUser($id);
    }
}
?>