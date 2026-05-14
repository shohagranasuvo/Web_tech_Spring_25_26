<?php
session_start();
$isLoggedIn = $_SESSION["user_id"] ?? false;
if ($isLoggedIn) {
    $role = $_SESSION["role"];
    if ($role == "employer") {
        Header("Location:../View/EmployerDashboard.php");
    } elseif ($role == "seeker") {
        Header("Location:../View/JobBoard.php");
    } else {
        Header("Location:../View/AdminPanel.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Job Portal</title>
    
    <style>
    body 
    { 
        font-family: Arial; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        min-height: 100vh;
        margin: 0;
        background: #eaf0fb;
    }
    .box 
    { 
        padding: 30px; 
        width: 400px; 
        border: 1px solid #003399;
        background: #f0f5ff;
    }
    h2 
    { 
        color: #003399; 
        text-align: center; 
    }
    label 
    { 
        display: block; 
        margin-top: 10px; 
    }
    input, textarea, select 
    { 
        width: 100%; 
        padding: 7px; 
        margin-top: 4px; 
        box-sizing: border-box; 
        border: 1px solid #003399; 
    }
    input[type=submit] 
    { 
        background: #003399; 
        color: white; 
        cursor: pointer; 
        margin-top: 14px; 
        font-size: 14px;
    }
    input[type=submit]:hover 
    { 
        background: #0055cc;
    }
    .error 
    { 
        color: red; font-size: 13px; 
    }
    .success 
    {
        color: green; font-size: 13px; 
    }
</style>
</head>
<body>
<div class="box">
    <h2>Job Portal - Login</h2>

    <?php
    $loginError = $_GET['error'] ?? '';
    if ($loginError == "invalid") echo "<p class='error'>Invalid email or password.</p>";
    if ($loginError == "empty") echo "<p class='error'>Please fill all fields.</p>";
    ?>

    <form method="post" action="../Controller/LoginController.php" enctype="multipart/form-data">
        <label>Email:</label>
        <input type="email" name="email">

        <label>Password:</label>
        <input type="password" name="password">

        <input type="submit" name="submit" value="Login">
    </form>
</div>
</body>
</html>

