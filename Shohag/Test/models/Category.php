<?php
// models/Category.php

class Category extends Model {
    
    // Get all categories
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    // Get category by ID
    public function getById($category_id) {
        $category_id = $this->escape($category_id);
        $sql = "SELECT * FROM categories WHERE id = '{$category_id}'";
        $result = $this->query($sql);
        return $this->fetchOne($result);
    }
    
    // Get category with job count
    public function getAllWithJobCount() {
        $sql = "SELECT c.*, COUNT(j.id) as job_count
                FROM categories c
                LEFT JOIN jobs j ON c.id = j.category_id 
                    AND j.status = 'active' 
                    AND j.deadline >= CURDATE()
                GROUP BY c.id
                ORDER BY c.name ASC";
        
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
}
?>
