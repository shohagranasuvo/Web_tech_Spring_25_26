<?php
session_start();

require_once "../Model/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    header("Location: Login.php");
    exit();
}

$db = new db();
$conn = $db->connection();

$user_id = $_SESSION["user_id"];


$applications = $db->getMyApplications($conn, $user_id);

include "../View/MyApplications.php";
?>