<?php
header("Content-Type: application/json");
include "../Model/db.php";

$email= trim($_POST["email"] ?? "");

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
{
    echo json_encode(["available" => false, "message" => "Enter a valid email."]);
    exit();
}

$database= new db();
$connection= $database->connection();

$result= $database->checkEmail($connection, $email);

if ($result->num_rows > 0) 
{
    echo json_encode(["available" => false, "message" => "Email already registered."]);
} 
else 
{
    echo json_encode(["available" => true, "message" => "Email is available."]);
}
?>