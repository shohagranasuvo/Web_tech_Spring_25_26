<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "employer") {
    header("Location: ../View/login.php");
    exit();
}

include_once "../Model/db.php";
$database = new db();
$connection = $database->connection();

// ==========================================
// CREATE JOB
// ==========================================
if (isset($_POST["create_job"])) {
    $employer_id  = $_SESSION["user_id"];
    $category_id  = intval($_POST["category_id"] ?? 0);
    $title        = trim($_POST["title"] ?? "");
    $description  = trim($_POST["description"] ?? "");
    $requirements = trim($_POST["requirements"] ?? ""); 
    $location     = trim($_POST["location"] ?? "");
    $salary_range = trim($_POST["salary_range"] ?? "");
    $job_type     = $_POST["job_type"] ?? "";
    $deadline     = $_POST["deadline"] ?? "";

    if (empty($title) || empty($description) || $category_id <= 0 || empty($location) || empty($job_type) || empty($deadline)) {
        
    $result = $database->createJob($connection, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline);
        header("Location: ../View/job.php?error=empty");
        exit();
    }

    $result = $database->createJob($connection, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline);

    if ($result) {
        header("Location: ../View/EmployerDashboard.php?success=job_created");
    } else {
        header("Location: ../View/job.php?error=failed");
    }
    exit();
}

// ==========================================
// UPDATE JOB
// ==========================================
if (isset($_POST["update_job"])) {
    $job_id       = intval($_POST["job_id"] ?? 0);
    $category_id  = intval($_POST["category_id"] ?? 0);
    $title        = trim($_POST["title"] ?? "");
    $description  = trim($_POST["description"] ?? "");
    $requirements = trim($_POST["requirements"] ?? ""); 
    $location     = trim($_POST["location"] ?? "");
    $salary_range = trim($_POST["salary_range"] ?? "");
    $job_type     = $_POST["job_type"] ?? "";
    $deadline     = $_POST["deadline"] ?? "";

    if ($job_id <= 0 || empty($title) || empty($description) || $category_id <= 0 || empty($location) || empty($job_type) || empty($deadline)) {
        header("Location: ../View/job.php?edit_id=" . $job_id . "&error=empty");
        exit();
    }

    $result = $database->updateJob($connection, $job_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline);

    if ($result) {
        header("Location: ../View/EmployerDashboard.php?success=job_updated");
    } else {
        header("Location: ../View/job.php?edit_id=" . $job_id . "&error=failed");
    }
    exit();
}


if (isset($_GET["delete_job_id"])) {
    $job_id = intval($_GET["delete_job_id"]);
    $employer_id = $_SESSION["user_id"];

    $result = $database->deleteJob($connection, $job_id, $employer_id);

    if ($result) {
        header("Location: ../View/EmployerDashboard.php?success=job_deleted");
    } else {
        header("Location: ../View/EmployerDashboard.php?error=delete_failed");
    }
    exit();
}

header("Location: ../View/EmployerDashboard.php");
exit();
?>