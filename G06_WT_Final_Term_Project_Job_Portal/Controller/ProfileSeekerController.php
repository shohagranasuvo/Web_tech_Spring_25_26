<?php
include "../Model/db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    Header("Location: ../View/Login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $headline= trim($_POST["headline"] ?? "");
    $skills= trim($_POST["skills"] ?? "");
    $years_experience= intval($_POST["years_experience"] ?? 0);
    $user_id= $_SESSION["user_id"];

    if (empty($headline) || empty($skills)) {
        Header("Location: ../View/profilecompletionJobS.php?error=empty");
        exit();
    }

    $database= new db();
    $connection= $database->connection();

    $result= $database->saveSeekerProfile($connection, $user_id, $headline, $skills, $years_experience);

    if ($result) {
        Header("Location: ../View/JobBoard.php");
    } else {
        Header("Location: ../View/profilecompletionJobS.php?error=failed");
    }
    exit();
}
?>

