<?php
include "../Model/db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "employer") {
    Header("Location: ../View/Login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_name= trim($_POST["company_name"] ?? "");
    $industry= trim($_POST["industry"] ?? "");
    $description= trim($_POST["description"] ?? "");
    $website= trim($_POST["website"] ?? "");
    $user_id= $_SESSION["user_id"];

    if (empty($company_name) || empty($industry) || empty($description)) {
        Header("Location: ../View/profilecompletionEmployee.php?error=empty");
        exit();
    }

    $database= new db();
    $connection= $database->connection();

    $result = $database->saveEmployerProfile($connection, $user_id, $company_name, $industry, $description, $website);

    if ($result) {
        Header("Location: ../View/EmployerDashboard.php");
    } else {
        Header("Location: ../View/profilecompletionEmployee.php?error=failed");
    }
    exit();
}
?>

