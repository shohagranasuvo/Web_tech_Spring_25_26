<?php
// models/Job.php

class Job extends Model {
    
    // Get all active jobs with optional filters
    public function getActiveJobs($filters = []) {
        $sql = "SELECT j.*, e.company_name, c.name as category_name, u.id as employer_user_id
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN users u ON e.user_id = u.id
                WHERE j.status = 'active' AND j.deadline >= CURDATE()";
        
        // Apply filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND j.category_id = '" . $this->escape($filters['category_id']) . "'";
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE '%" . $this->escape($filters['location']) . "%'";
        }
        
        if (!empty($filters['job_type'])) {
            $sql .= " AND j.job_type = '" . $this->escape($filters['job_type']) . "'";
        }
        
        if (!empty($filters['salary_range'])) {
            $sql .= " AND j.salary_range LIKE '%" . $this->escape($filters['salary_range']) . "%'";
        }
        
        $sql .= " ORDER BY j.created_at DESC";
        
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    // Search jobs by keyword
    public function searchJobs($keyword) {
        $keyword = $this->escape($keyword);
        $sql = "SELECT j.*, e.company_name, c.name as category_name
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE j.status = 'active' AND j.deadline >= CURDATE()
                AND (j.title LIKE '%{$keyword}%' 
                    OR j.description LIKE '%{$keyword}%' 
                    OR e.company_name LIKE '%{$keyword}%'
                    OR j.requirements LIKE '%{$keyword}%')
                ORDER BY j.created_at DESC";
        
        $result = $this->query($sql);
        return $this->fetchAll($result);
    }
    
    // Get job by ID with full details
    public function getJobById($job_id) {
        $job_id = $this->escape($job_id);
        $sql = "SELECT j.*, e.company_name, e.industry, e.description as company_description, 
                e.website, c.name as category_name, u.name as employer_name, u.file_path as company_logo
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN users u ON e.user_id = u.id
                WHERE j.id = '{$job_id}'";
        
        $result = $this->query($sql);
        return $this->fetchOne($result);
    }
    
    // Get total job count
    public function getJobCount($filters = []) {
        $jobs = $this->getActiveJobs($filters);
        return count($jobs);
    }
}
?>
