<?php
include "../Model/db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email= trim($_POST["email"] ?? "");
    $password= $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        Header("Location: ../View/Login.php?error=empty");
        exit();
    }

    $database= new db();
    $connection= $database->connection();

    $result= $database->getUserByEmail($connection, $email);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password_hash"])) {

            $_SESSION["user_id"]= $row["id"];
            $_SESSION["name"]= $row["name"];
            $_SESSION["role"]= $row["role"];

            $role = $row["role"];

            // Check if profile is complete and redirect
            if ($role == "employer") {
                $profile = $database->getEmployerProfile($connection, $row["id"]);
                if ($profile->num_rows == 0) {
                    Header("Location: ../View/profilecompletionEmployee.php");
                } else {
                    Header("Location: ../View/EmployerDashboard.php");
                }
            } elseif ($role == "seeker") {
                $profile = $database->getSeekerProfile($connection, $row["id"]);
                if ($profile->num_rows == 0) {
                    Header("Location: ../View/profilecompletionJobS.php");
                } else {
                    Header("Location: ../View/JobBoard.php");
                }
            } else {
                Header("Location: ../View/AdminPanel.php");
            }
            exit();

        } else {
            Header("Location: ../View/Login.php?error=invalid");
            exit();
        }
    } else {
        Header("Location: ../View/Login.php?error=invalid");
        exit();
    }
}
?>

