<?php
include "../Model/db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "employer") {
    Header("Location: ../View/Login.php");
    exit();
}

$user_id= $_SESSION["user_id"];
$database= new db();
$connection= $database->connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    if (isset($_POST["submit_profile"])) {

        $company_name= trim($_POST["company_name"] ?? "");
        $industry= trim($_POST["industry"] ?? "");
        $description= trim($_POST["description"] ?? "");
        $website= trim($_POST["website"] ?? "");

        if (empty($company_name) || empty($industry) || empty($description)) {
            Header("Location: ../View/editProfileEmployee.php?error=empty");
            exit();
        }

        $database->updateEmployerProfile($connection, $user_id, $company_name, $industry, $description, $website);

    
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
            $file= $_FILES["file"];
            $allowed= ["image/jpeg", "image/png", "image/gif"];
            $finfo= finfo_open(FILEINFO_MIME_TYPE);
            $mime= finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);

            if (in_array($mime, $allowed) && $file["size"] <= 2 * 1024 * 1024) {
                $upload_dir = "../public/uploads/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $file_path = $upload_dir . time() . "_" . basename($file["name"]);
                move_uploaded_file($file["tmp_name"], $file_path);
                $database->updateFilePath($connection, $user_id, $file_path);
            }
        }

        Header("Location: ../View/editProfileEmployee.php?success=1");
        exit();
    }

    
    if (isset($_POST["submit_password"])) {

        $current_password = $_POST["current_password"] ?? "";
        $new_password     = $_POST["new_password"] ?? "";

        if (strlen($new_password) < 8) {
            Header("Location: ../View/editProfileEmployee.php?error=passlen");
            exit();
        }

        $user = $database->getUserById($connection, $user_id)->fetch_assoc();

        if (!password_verify($current_password, $user["password_hash"])) {
            Header("Location: ../View/editProfileEmployee.php?error=wrongpass");
            exit();
        }

        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $database->updatePassword($connection, $user_id, $new_hash);

        Header("Location: ../View/editProfileEmployee.php?success=2");
        exit();
    }
}
?>

