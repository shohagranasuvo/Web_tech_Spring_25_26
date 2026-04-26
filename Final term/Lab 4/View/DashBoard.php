<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get data safely
    $name     = $_POST["name"] ?? "";
    $email    = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $website  = $_POST["website"] ?? "";
    $comment  = $_POST["comment"] ?? "";
    $gender   = $_POST["gender"] ?? "";

    // File handling
    $filename = "";
    if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0){
        $filename = $_FILES["file"]["name"];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
</head>
<body>

<h1 style="color:blue;">📋 User Information</h1>

<table border="1" cellpadding="10">

<tr>
    <td><b>Name</b></td>
    <td><?php echo $name; ?></td>
</tr>

<tr>
    <td><b>Email</b></td>
    <td><?php echo $email; ?></td>
</tr>

<tr>
    <td><b>Password</b></td>
    <td><?php echo $password; ?></td>
</tr>

<tr>
    <td><b>Website</b></td>
    <td><?php echo $website; ?></td>
</tr>

<tr>
    <td><b>Comment</b></td>
    <td><?php echo $comment; ?></td>
</tr>

<tr>
    <td><b>Gender</b></td>
    <td><?php echo $gender; ?></td>
</tr>

<tr>
    <td><b>Uploaded File</b></td>
    <td><?php echo $filename ? $filename : "No file uploaded"; ?></td>
</tr>

</table>

</body>
</html>