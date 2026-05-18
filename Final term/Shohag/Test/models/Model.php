<?php
// core/Model.php

class Model {
    
    protected $db;
    protected $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    // Escape string for SQL injection prevention
    protected function escape($value) {
        return $this->conn->real_escape_string($value);
    }
    
    // Execute query
    protected function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            error_log("Query Error: " . $this->conn->error);
        }
        return $result;
    }
    
    // Fetch all results
    protected function fetchAll($result) {
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    // Fetch single result
    protected function fetchOne($result) {
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // Get last insert ID
    protected function getLastInsertId() {
        return $this->conn->insert_id;
    }
    
    // Get affected rows
    protected function getAffectedRows() {
        return $this->conn->affected_rows;
    }
}
?>
