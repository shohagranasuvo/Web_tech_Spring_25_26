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
            /* background: white;  */
            /* display: flex;  */
            /* justify-content: center;  */
            align-items: center; 
            min-height: 400px; 
            margin: 0; 
            text-align: center;
        }
        .box 
        { 
            /* background: white; */
            padding: 30px; 
            width: 350px; 
            border: 1px solid white; 
        }
        h2 
        { 
            color: blue; 
            text-align: center;
            font-weight: bold; 
        }
        label 
        { 
            display: block; 
            margin-top: 12px; 
            /* color: #333; */
        }
        input[type=email], input[type=password] 
        { 
            width: 100%; 
            padding: 8px; 
            margin-top: 4px; 
            /* box-sizing: border-box; 
            border: 1px solid #aaa; */
            } 
        input[type=submit] 
        { 
            /* margin-top: 16px; */
            width: 100%; 
            padding: 10px; 
            background: green; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-size: 15px; }
        input[type=submit]:hover 
        { 
            background: darkgreen; 
        }
        .error 
        { 
            color: red; 
            font-size: 13px; 
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
    <div class="link-row"><a href="Registration.php">Don't have an account? Register</a></div>
</div>
</body>
</html>

