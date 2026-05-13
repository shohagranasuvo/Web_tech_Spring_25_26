<?php
include "../Model/db.php";
session_start();

$name= "";
$email= "";
$password= "";
$error="";
$nameError= "";
$emailError= "";
$passError= "";
$roleError= "";
$fileError= "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    $name= trim($_POST["name"] ?? "");
    $email= trim($_POST["email"] ?? "");
    $password= $_POST["password"] ?? "";
    $role= $_POST["role"] ?? "";

    
    if (empty($role)) {
        $roleError = "Please select a role (Employer or Job Seeker).";
    }

   
    if (empty($name)) {
        $nameError = "Name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Valid email is required.";
    }

    
    if (strlen($password) < 8) {
        $passError = "Password must be at least 8 characters.";
    }

    
    $file_path = "";
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $file = $_FILES["file"];
        $allowed_employer_types = ["image/jpeg", "image/png", "image/gif"];
        $allowed_seeker_types   = ["application/pdf"];
        $max_size = 2 * 1024 * 1024; // 2MB

        // Validate MIME type (server-side, not just extension)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        if ($role == "employer" && !in_array($mime, $allowed_employer_types)) {
            $fileError = "Company logo must be JPG, PNG, or GIF.";
        } elseif ($role == "seeker" && !in_array($mime, $allowed_seeker_types)) {
            $fileError = "Resume must be a PDF file.";
        } elseif ($file["size"] > $max_size) {
            $fileError = "File size must be under 2MB.";
        } else {
            $upload_dir = "../File/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $upload_dir . time() . "_" . basename($file["name"]);
            move_uploaded_file($file["tmp_name"], $file_path);
        }
    }

   
    if (empty($roleError) && empty($nameError) && empty($emailError) && empty($passError) && empty($fileError)) {

        $database   = new db();
        $connection = $database->connection();

        
        $check = $database->checkEmail($connection, $email);
        if ($check->num_rows > 0) 
        {
            $emailError = "This email is already registered.";
        }
        else 
        {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $result = $database->registerUser($connection, $name, $email, $password_hash, $role, $file_path);

            if ($result)
            {
                Header("Location: ../View/Login.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

