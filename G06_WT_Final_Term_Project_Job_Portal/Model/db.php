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

}
?>

