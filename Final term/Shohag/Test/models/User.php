<?php
// models/User.php

class User extends Model {
    
    // Get user by ID
    public function getById($user_id) {
        $user_id = $this->escape($user_id);
        $sql = "SELECT * FROM users WHERE id = '{$user_id}'";
        $result = $this->query($sql);
        return $this->fetchOne($result);
    }
    
    // Get seeker profile
    public function getSeekerProfile($user_id) {
        $user_id = $this->escape($user_id);
        $sql = "SELECT u.*, sp.headline, sp.skills, sp.years_experience
                FROM users u
                LEFT JOIN seeker_profiles sp ON u.id = sp.user_id
                WHERE u.id = '{$user_id}' AND u.role = 'seeker'";
        
        $result = $this->query($sql);
        return $this->fetchOne($result);
    }
    
    // Update user
    public function update($user_id, $data) {
        $user_id = $this->escape($user_id);
        $updates = [];
        
        foreach ($data as $key => $value) {
            $value = $this->escape($value);
            $updates[] = "{$key} = '{$value}'";
        }
        
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = '{$user_id}'";
        return $this->query($sql);
    }
}
?>
