<?php

class db {
    
    function connection() {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "job_portal"; 

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if($connection->connect_error) {
            die("Could not Connect Database" . $connection->connect_error);
        }
        return $connection;
    }

    function signup($connection, $tablename, $username, $password) {
        $sql = "INSERT INTO " . $tablename . "(username, password) VALUES ('" . $username . "', '" . $password . "')";
        $result = $connection->query($sql);
        return $result;
    }
    
    function signin($connection, $tablename, $username, $password) {
        $sql = "SELECT * FROM " . $tablename . " WHERE username='" . $username . "' AND password='" . $password . "'";
        $result = $connection->query($sql);
        return $result;
    }

    // ==================== JOB SEARCH & FILTERING ====================
    
    function getActiveJobs($connection, $filters = []) {
        $sql = "SELECT j.*, e.company_name, c.name as category_name, u.id as employer_user_id
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN users u ON e.user_id = u.id
                WHERE j.status = 'active' AND j.deadline >= CURDATE()";
        
        // Apply filters
        if (!empty($filters['category_id'])) {
            $category_id = $connection->real_escape_string($filters['category_id']);
            $sql .= " AND j.category_id = '" . $category_id . "'";
        }
        
        if (!empty($filters['location'])) {
            $location = $connection->real_escape_string($filters['location']);
            $sql .= " AND j.location LIKE '%" . $location . "%'";
        }
        
        if (!empty($filters['job_type'])) {
            $job_type = $connection->real_escape_string($filters['job_type']);
            $sql .= " AND j.job_type = '" . $job_type . "'";
        }
        
        if (!empty($filters['salary_range'])) {
            $salary_range = $connection->real_escape_string($filters['salary_range']);
            $sql .= " AND j.salary_range LIKE '%" . $salary_range . "%'";
        }
        
        $sql .= " ORDER BY j.created_at DESC";
        
        $result = $connection->query($sql);
        return $result;
    }
    
    function searchJobs($connection, $keyword) {
        $keyword = $connection->real_escape_string($keyword);
        $sql = "SELECT j.*, e.company_name, c.name as category_name
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE j.status = 'active' AND j.deadline >= CURDATE()
                AND (j.title LIKE '%" . $keyword . "%' 
                    OR j.description LIKE '%" . $keyword . "%' 
                    OR e.company_name LIKE '%" . $keyword . "%'
                    OR j.requirements LIKE '%" . $keyword . "%')
                ORDER BY j.created_at DESC";
        
        $result = $connection->query($sql);
        return $result;
    }
    
    function getJobById($connection, $job_id) {
        $job_id = $connection->real_escape_string($job_id);
        $sql = "SELECT j.*, e.company_name, e.industry, e.description as company_description, 
                e.website, c.name as category_name, u.name as employer_name, u.file_path as company_logo
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN users u ON e.user_id = u.id
                WHERE j.id = '" . $job_id . "'";
        
        $result = $connection->query($sql);
        return $result;
    }
    
    function getAllCategories($connection) {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = $connection->query($sql);
        return $result;
    }
    
    // ==================== SAVED JOBS ====================
    
    function toggleSavedJob($connection, $user_id, $job_id) {
        $user_id = $connection->real_escape_string($user_id);
        $job_id = $connection->real_escape_string($job_id);
        
        // Check if already saved
        $check_sql = "SELECT id FROM saved_jobs WHERE user_id = '" . $user_id . "' AND job_id = '" . $job_id . "'";
        $check_result = $connection->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            // Remove from saved jobs
            $delete_sql = "DELETE FROM saved_jobs WHERE user_id = '" . $user_id . "' AND job_id = '" . $job_id . "'";
            $connection->query($delete_sql);
            return 'removed';
        } else {
            // Add to saved jobs
            $insert_sql = "INSERT INTO saved_jobs (user_id, job_id, created_at) 
                          VALUES ('" . $user_id . "', '" . $job_id . "', NOW())";
            $connection->query($insert_sql);
            return 'added';
        }
    }
    
    function isSavedJob($connection, $user_id, $job_id) {
        $user_id = $connection->real_escape_string($user_id);
        $job_id = $connection->real_escape_string($job_id);
        
        $sql = "SELECT id FROM saved_jobs WHERE user_id = '" . $user_id . "' AND job_id = '" . $job_id . "'";
        $result = $connection->query($sql);
        return $result->num_rows > 0;
    }
    
    function getSavedJobs($connection, $user_id) {
        $user_id = $connection->real_escape_string($user_id);
        $sql = "SELECT j.*, e.company_name, c.name as category_name, sj.created_at as saved_at
                FROM saved_jobs sj
                INNER JOIN jobs j ON sj.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE sj.user_id = '" . $user_id . "' AND j.status = 'active' AND j.deadline >= CURDATE()
                ORDER BY sj.created_at DESC";
        
        $result = $connection->query($sql);
        return $result;
    }
    
    // ==================== APPLICATIONS ====================
    
    function hasApplied($connection, $seeker_id, $job_id) {
        $seeker_id = $connection->real_escape_string($seeker_id);
        $job_id = $connection->real_escape_string($job_id);
        
        $sql = "SELECT id FROM applications WHERE seeker_id = '" . $seeker_id . "' AND job_id = '" . $job_id . "'";
        $result = $connection->query($sql);
        return $result->num_rows > 0;
    }
    
    function submitApplication($connection, $job_id, $seeker_id, $cover_letter, $resume_path) {
        $job_id = $connection->real_escape_string($job_id);
        $seeker_id = $connection->real_escape_string($seeker_id);
        $cover_letter = $connection->real_escape_string($cover_letter);
        $resume_path = $connection->real_escape_string($resume_path);
        
        $sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status, created_at) 
                VALUES ('" . $job_id . "', '" . $seeker_id . "', '" . $cover_letter . "', '" . $resume_path . "', 'Submitted', NOW())";
        
        $result = $connection->query($sql);
        return $result;
    }
    
    function getMyApplications($connection, $seeker_id) {
        $seeker_id = $connection->real_escape_string($seeker_id);
        $sql = "SELECT a.*, j.title as job_title, j.location, e.company_name, e.industry
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                WHERE a.seeker_id = '" . $seeker_id . "'
                ORDER BY a.created_at DESC";
        
        $result = $connection->query($sql);
        return $result;
    }
    
    function getSeekerProfile($connection, $user_id) {
        $user_id = $connection->real_escape_string($user_id);
        $sql = "SELECT u.*, sp.headline, sp.skills, sp.years_experience
                FROM users u
                LEFT JOIN seeker_profiles sp ON u.id = sp.user_id
                WHERE u.id = '" . $user_id . "'";
        
        $result = $connection->query($sql);
        return $result;
    }
}

?>
