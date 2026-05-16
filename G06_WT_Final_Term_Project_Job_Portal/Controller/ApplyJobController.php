<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    header("Location: ../View/Login.php");
    exit();
}

include "../Model/db.php";

$db = new db();
$conn = $db->connection();

$user_id = $_SESSION["user_id"];

$job_id = $_POST['job_id'] ?? 0;
$cover_letter = $_POST['cover_letter'] ?? '';
$resume_option = $_POST['resume_option'] ?? 'upload';

// validation
if (empty($job_id) || empty($cover_letter)) {
    echo "Missing required fields!";
    exit();
}

if ($db->hasApplied($conn, $user_id, $job_id)) {
    echo "<script>
        alert('You have already applied for this job!');
        window.location.href = '../View/JobDetail.php?id=$job_id';
    </script>";
    exit();
}


$resume_path = "";


$user = $db->getUserById($conn, $user_id)->fetch_assoc();
$profile_resume = $user['file_path'] ?? '';


if ($resume_option == "profile" && !empty($profile_resume)) {
    $resume_path = $profile_resume;
}

else {

    if (!isset($_FILES['resume']) || $_FILES['resume']['error'] != 0) {
        echo "Resume upload failed!";
        exit();
    }

    $file_name = $_FILES['resume']['name'];
    $tmp_name = $_FILES['resume']['tmp_name'];
    $file_size = $_FILES['resume']['size'];


    if ($file_size > 2 * 1024 * 1024) {
        echo "File too large (Max 2MB)";
        exit();
    }

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
        echo "Invalid file type!";
        exit();
    }

    $upload_dir = "../uploads/resumes/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $new_file = time() . "_" . $file_name;
    $destination = $upload_dir . $new_file;

    move_uploaded_file($tmp_name, $destination);

    $resume_path = $destination;
}

$sql = "INSERT INTO applications 
        (job_id, seeker_id, cover_letter, resume_path, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'Submitted', NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $job_id, $user_id, $cover_letter, $resume_path);

if ($stmt->execute()) {
    echo "<script>
        alert('Application submitted successfully!');
        window.location.href = '../View/JobDetail.php?id=$job_id';
    </script>";
} else {
    echo "Error submitting application!";
}

?>