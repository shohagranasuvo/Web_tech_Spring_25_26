<?php
include "../Controller/RegistrationController.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Job Portal</title>
    <script src="../Controller/JS/CheckEmail.js"></script>
    <style>
    body 
    { 
        font-family: Arial; 
        display: flex;
        justify-content: center;
        align-items: center; 
        min-height: 100vh; 
        margin: 0;
        background: #e8f4fd;
    }
    .box 
    { 
        padding: 30px; 
        width: 380px; 
        border: 1px solid #0077b6;
        background: #f0faff;
    }
    h2 
    { 
        color: #0077b6; 
        text-align: center; 
        font-weight: bold;
    }
    label 
    { 
        display: block; 
        margin-top: 12px; 
        font-weight: bold;
    } 
    input[type=text], input[type=email], input[type=password], input[type=file], select 
    { 
        width: 100%; 
        padding: 8px; 
        margin-top: 4px; 
        box-sizing: border-box; 
        border: 1px solid #0077b6; 
    }
    .role-row 
    { 
        display: flex; 
        gap: 20px; 
        margin-top: 8px; 
    }
    .role-row label 
    { 
        margin-top: 0;
        font-weight: normal;
    } 
    input[type=submit] 
    { 
        margin-top: 16px; 
        width: 100%; 
        padding: 10px; 
        background: #0077b6; 
        color: white; 
        border: none;
        cursor: pointer;
        font-size: 15px;
    }
    input[type=submit]:hover 
    { 
        background: #005f99; 
    }
    .error 
    { 
        color: red; 
        font-size: 13px; 
    }
    .hint 
    { 
        color: gray; 
        font-size: 12px; 
    }
    #emailresponse 
    { 
        font-size: 13px; 
        color: #a1f09f;
    }
    .link-row 
    {
        margin-top: 12px; 
        text-align: center;
    }
    
</style>
</head>
<body>
<div class="box">
    <h2>Job Portal - Register</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post" action="" enctype="multipart/form-data">

        <label>Select Role:</label>
        <div class="role-row">
            <label><input type="radio" name="role" value="employer" <?php echo (isset($_POST['role']) && $_POST['role']=='employer') ? 'checked' : ''; ?>> Employer</label>
            <label><input type="radio" name="role" value="seeker" <?php echo (isset($_POST['role']) && $_POST['role']=='seeker') ? 'checked' : ''; ?>> Job Seeker</label>
        </div>
        <?php if (!empty($roleError)) echo "<p class='error'>$roleError</p>"; ?>


        <label>Full Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
        <?php if (!empty($nameError)) echo "<p class='error'>$nameError</p>"; ?>

        <label>Email:</label>
        <input type="email" name="email" id="emailfield" value="<?php echo htmlspecialchars($email ?? ''); ?>" onkeyup="CheckEmail()">
        <p id="emailresponse"></p>
        <?php if (!empty($emailError)) echo "<p class='error'>$emailError</p>"; ?>

        <label>Password (min 8 characters):</label>
        <input type="password" name="password" id="passfield">
        <?php if (!empty($passError)) echo "<p class='error'>$passError</p>"; ?>

        <label id="filelabel">Upload File: <span class="hint">(Company Logo for Employer / Resume PDF max 2MB for Seeker)</span></label>
        <input type="file" name="file">
        <?php if (!empty($fileError)) echo "<p class='error'>$fileError</p>"; ?>

        <input type="submit" name="submit" value="Register">
    </form>
    <div class="link-row"><a href="login.php">Already have an account? Login</a></div>

</div>
</body>
</html>





