<?php
include "../Model/db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    Header("Location: ../View/Login.php");
    exit();
}

$user_id= $_SESSION["user_id"];
$database= new db();
$connection= $database->connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Update Profile ---
    if (isset($_POST["submit_profile"])) {

        $headline = trim($_POST["headline"] ?? "");
        $skills= trim($_POST["skills"] ?? "");
        $years_experience= intval($_POST["years_experience"] ?? 0);

        if (empty($headline) || empty($skills)) {
            Header("Location: ../View/editProfileJobSeeker.php?error=empty");
            exit();
        }

        $database->updateSeekerProfile($connection, $user_id, $headline, $skills, $years_experience);

        // Handle resume re-upload
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
            $file = $_FILES["file"];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);

            if ($mime != "application/pdf") {
                Header("Location: ../View/editProfileJobSeeker.php?error=filetype");
                exit();
            }
            if ($file["size"] > 2 * 1024 * 1024) {
                Header("Location: ../View/editProfileJobSeeker.php?error=filesize");
                exit();
            }

            $upload_dir = "../public/uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_path = $upload_dir . time() . "_" . basename($file["name"]);
            move_uploaded_file($file["tmp_name"], $file_path);
            $database->updateFilePath($connection, $user_id, $file_path);
        }

        Header("Location: ../View/editProfileJobSeeker.php?success=1");
        exit();
    }

    // --- Change Password ---
    if (isset($_POST["submit_password"])) {

        $current_password = $_POST["current_password"] ?? "";
        $new_password     = $_POST["new_password"] ?? "";

        if (strlen($new_password) < 8) {
            Header("Location: ../View/editProfileJobSeeker.php?error=passlen");
            exit();
        }

        $user = $database->getUserById($connection, $user_id)->fetch_assoc();

        if (!password_verify($current_password, $user["password_hash"])) {
            Header("Location: ../View/editProfileJobSeeker.php?error=wrongpass");
            exit();
        }

        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $database->updatePassword($connection, $user_id, $new_hash);

        Header("Location: ../View/editProfileJobSeeker.php?success=2");
        exit();
    }
}
?>

