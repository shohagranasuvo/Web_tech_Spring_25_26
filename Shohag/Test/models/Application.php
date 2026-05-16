<?php
// models/Application.php

class Application extends Model {
    
    // Check if seeker has already applied for a job
    public function hasApplied($seeker_id, $job_id) {
        $seeker_id = $this->escape($seeker_id);
        $job_id = $this->escape($job_id);
        
        $sql = "SELECT id FROM applications 
                WHERE seeker_id = '{$seeker_id}' AND job_id = '{$job_id}'";
        
        $result = $this->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    // Submit a new application
    public function submitApplication($job_id, $seeker_id, $cover_letter, $resume_path) {
        $job_id = $this->escape($job_id);
        $seeker_id = $this->escape($seeker_id);
        $cover_letter = $this->escape($cover_letter);
        $resume_path = $this->escape($resume_path);
        
        $sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status, created_at) 
                VALUES ('{$job_id}', '{$seeker_id}', '{$cover_letter}', '{$resume_path}', 'Submitted', NOW())";
        
        $result = $this->query($sql);
        return $result ? $this->getLastInsertId() : false;
    }
    
    // Get all applications for a seeker
    public function getApplicationsBySeeker($seeker_id) {
        $seeker_id = $this->escape($seeker_id);
        
        $sql = "SELECT a.*, j.title as job_title, j.location, j.job_type, j.salary_range,
                e.company_name, e.industry
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                WHERE a.seeker_id = '{$seeker_id}'
                ORDER BY a.created_at DESC";
        
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    // Get application by ID
    public function getApplicationById($application_id) {
        $application_id = $this->escape($application_id);
        
        $sql = "SELECT a.*, j.title as job_title, j.location,
                e.company_name, e.industry
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                WHERE a.id = '{$application_id}'";
        
        $result = $this->query($sql);
        return $this->fetchOne($result);
    }
    
    // Get application statistics for a seeker
    public function getApplicationStats($seeker_id) {
        $seeker_id = $this->escape($seeker_id);
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Submitted' THEN 1 ELSE 0 END) as submitted,
                SUM(CASE WHEN status = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
                SUM(CASE WHEN status = 'Shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
                FROM applications
                WHERE seeker_id = '{$seeker_id}'";
        
        $result = $this->query($sql);
        return $this->fetchOne($result);
    }
}
?>
