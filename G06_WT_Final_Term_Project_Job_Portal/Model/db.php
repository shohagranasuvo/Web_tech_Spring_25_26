<?php
class db {

    function connection() {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "job_portal";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($connection->connect_error) {
            die("Could not Connect Database: " . $connection->connect_error);
        }
        return $connection;
    }

    function checkEmail($connection, $email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function registerUser($connection, $name, $email, $password_hash, $role, $file_path) {
        $sql = "INSERT INTO users (name, email, password_hash, role, file_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $password_hash, $role, $file_path);
        return $stmt->execute();
    }

    function getUserByEmail($connection, $email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getLastId($connection) {
        return $connection->insert_id;
    }

   
    function getEmployerProfile($connection, $user_id) {
        $sql = "SELECT * FROM employer_profiles WHERE user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

  
    function getSeekerProfile($connection, $user_id) {
        $sql = "SELECT * FROM seeker_profiles WHERE user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

 
    function saveEmployerProfile($connection, $user_id, $company_name, $industry, $description, $website) {
        $sql = "INSERT INTO employer_profiles (user_id, company_name, industry, description, website) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("issss", $user_id, $company_name, $industry, $description, $website);
        return $stmt->execute();
    }

  
    function saveSeekerProfile($connection, $user_id, $headline, $skills, $years_experience) {
        $sql = "INSERT INTO seeker_profiles (user_id, headline, skills, years_experience) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("issi", $user_id, $headline, $skills, $years_experience);
        return $stmt->execute();
    }

    function updateEmployerProfile($connection, $user_id, $company_name, $industry, $description, $website) {
        $sql = "UPDATE employer_profiles SET company_name=?, industry=?, description=?, website=? WHERE user_id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssi", $company_name, $industry, $description, $website, $user_id);
        return $stmt->execute();
    }

  
    function updateSeekerProfile($connection, $user_id, $headline, $skills, $years_experience) {
        $sql = "UPDATE seeker_profiles SET headline=?, skills=?, years_experience=? WHERE user_id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssii", $headline, $skills, $years_experience, $user_id);
        return $stmt->execute();
    }

  
    function updateFilePath($connection, $user_id, $file_path) {
        $sql = "UPDATE users SET file_path=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $file_path, $user_id);
        return $stmt->execute();
    }

   
    function updatePassword($connection, $user_id, $new_hash) {
        $sql = "UPDATE users SET password_hash=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $new_hash, $user_id);
        return $stmt->execute();
    }


    function getUserById($connection, $user_id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }





    // ==================== TASK 3: JOB SEARCH & FILTERING ====================
    
    function getActiveJobs($connection, $filters = []) {
        $sql = "SELECT j.*, e.company_name, c.name as category_name
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE j.status = 'active' AND j.deadline >= CURDATE()";
        
        // Apply filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND j.category_id = ?";
        }
        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE ?";
        }
        if (!empty($filters['job_type'])) {
            $sql .= " AND j.job_type = ?";
        }
        if (!empty($filters['salary_range'])) {
            $sql .= " AND j.salary_range LIKE ?";
        }
        
        $sql .= " ORDER BY j.created_at DESC";
        
        $stmt = $connection->prepare($sql);
        
        // Bind parameters dynamically
        $types = "";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $types .= "i";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['location'])) {
            $types .= "s";
            $params[] = "%" . $filters['location'] . "%";
        }
        if (!empty($filters['job_type'])) {
            $types .= "s";
            $params[] = $filters['job_type'];
        }
        if (!empty($filters['salary_range'])) {
            $types .= "s";
            $params[] = "%" . $filters['salary_range'] . "%";
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function searchJobs($connection, $keyword) {
        $sql = "SELECT j.*, e.company_name, c.name as category_name
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE j.status = 'active' AND j.deadline >= CURDATE()
                AND (j.title LIKE ? OR j.description LIKE ? OR e.company_name LIKE ? OR j.requirements LIKE ?)
                ORDER BY j.created_at DESC";
        
        $stmt = $connection->prepare($sql);
        $searchTerm = "%" . $keyword . "%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function getJobById($connection, $job_id) {
        $sql = "SELECT j.*, e.company_name, e.industry, e.description as company_description, 
                e.website, c.name as category_name, u.name as employer_name, u.file_path as company_logo
                FROM jobs j
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN users u ON e.user_id = u.id
                WHERE j.id = ?";
        
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function getAllCategories($connection) {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $connection->query($sql);
    }
    
    // ==================== SAVED JOBS ====================
    
    function isSavedJob($connection, $user_id, $job_id) {
        $sql = "SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $user_id, $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    function toggleSavedJob($connection, $user_id, $job_id) {
        // Check if already saved
        $check_sql = "SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ?";
        $check_stmt = $connection->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $job_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Remove from saved jobs
            $delete_sql = "DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?";
            $delete_stmt = $connection->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $user_id, $job_id);
            $delete_stmt->execute();
            return 'removed';
        } else {
            // Add to saved jobs
            $insert_sql = "INSERT INTO saved_jobs (user_id, job_id, created_at) VALUES (?, ?, NOW())";
            $insert_stmt = $connection->prepare($insert_sql);
            $insert_stmt->bind_param("ii", $user_id, $job_id);
            $insert_stmt->execute();
            return 'added';
        }
    }
    
    function getSavedJobs($connection, $user_id) {
        $sql = "SELECT j.*, e.company_name, c.name as category_name, sj.created_at as saved_at
                FROM saved_jobs sj
                INNER JOIN jobs j ON sj.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                INNER JOIN categories c ON j.category_id = c.id
                WHERE sj.user_id = ? AND j.status = 'active' AND j.deadline >= CURDATE()
                ORDER BY sj.created_at DESC";
        
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function removeSavedJob($connection, $user_id, $job_id) {
        $sql = "DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $user_id, $job_id);
        return $stmt->execute();
    }
    
    // ==================== APPLICATIONS ====================
    
    function hasApplied($connection, $seeker_id, $job_id) {
        $sql = "SELECT id FROM applications WHERE seeker_id = ? AND job_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $seeker_id, $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    function submitApplication($connection, $job_id, $seeker_id, $cover_letter, $resume_path) {
        $sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status, created_at) 
                VALUES (?, ?, ?, ?, 'Submitted', NOW())";
        
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iiss", $job_id, $seeker_id, $cover_letter, $resume_path);
        return $stmt->execute();
    }
    
    function getMyApplications($connection, $seeker_id) {
        $sql = "SELECT a.*, j.title as job_title, j.location, e.company_name, e.industry
                FROM applications a
                INNER JOIN jobs j ON a.job_id = j.id
                INNER JOIN employer_profiles e ON j.employer_id = e.id
                WHERE a.seeker_id = ?
                ORDER BY a.created_at DESC";
        
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $seeker_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function getApplicationStats($connection, $seeker_id) {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Submitted' THEN 1 ELSE 0 END) as submitted,
                SUM(CASE WHEN status = 'Reviewed' THEN 1 ELSE 0 END) as reviewed,
                SUM(CASE WHEN status = 'Shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
                FROM applications
                WHERE seeker_id = ?";
        
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $seeker_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}

// ========================================
// END OF METHODS TO ADD
// ========================================
?>
