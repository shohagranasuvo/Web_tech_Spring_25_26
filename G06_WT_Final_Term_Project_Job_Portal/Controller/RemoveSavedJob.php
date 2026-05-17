<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: Login.php");
    exit();
}

include "../Model/db.php";

$db = new db();
$conn = $db->connection();

$user_id = $_SESSION["user_id"];
$job_id = $_GET['id'] ?? 0;

if ($job_id > 0) {
    $db->removeSavedJob($conn, $user_id, $job_id);
}

header("Location: ..\View\SavedJobs.php");
exit();
?>