<?php
// models/SavedJob.php

class SavedJob extends Model {
    
    // Check if job is saved by user
    public function isSaved($user_id, $job_id) {
        $user_id = $this->escape($user_id);
        $job_id = $this->escape($job_id);
        
        $sql = "SELECT id FROM saved_jobs 
                WHERE user_id = '{$user_id}' AND job_id = '{$job_id}'";
        
        $result = $this->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    // Toggle saved job (add or remove)
    public function toggle($user_id, $job_id) {
        $user_id = $this->escape($user_id);
        $job_id = $this->escape($job_id);
        
        if ($this->isSaved($user_id, $job_id)) {
            // Remove from saved jobs
            $sql = "DELETE FROM saved_jobs 
                    WHERE user_id = '{$user_id}' AND job_id = '{$job_id}'";
            $this->query($sql);
            return 'removed';
        } else {
            // Add to saved jobs
            $sql = "INSERT INTO saved_jobs (user_id, job_id, created_at) 
                    VALUES ('{$user_id}', '{$job_id}', NOW())";
            $this->query($sql);
            return 'added';
        }
    }
    
    // Get all saved jobs for a user
    public function getSavedJobs($user_id) {
        $user_id = $this->escape($user_id);
        
        $sql = "SELECT j.*, e.company_name, c.name as category_name, sj.created_at as saved_at
                FROM saved_jobs sj
                INNER JOIN jobs j ON sj.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE sj.user_id = '{$user_id}' 
                AND j.status = 'active' 
                AND j.deadline >= CURDATE()
                ORDER BY sj.created_at DESC";
        
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    // Remove saved job
    public function remove($user_id, $job_id) {
        $user_id = $this->escape($user_id);
        $job_id = $this->escape($job_id);
        
        $sql = "DELETE FROM saved_jobs 
                WHERE user_id = '{$user_id}' AND job_id = '{$job_id}'";
        
        return $this->query($sql);
    }
    
    // Get count of saved jobs
    public function getCount($user_id) {
        $saved = $this->getSavedJobs($user_id);
        return count($saved);
    }
}
?>
