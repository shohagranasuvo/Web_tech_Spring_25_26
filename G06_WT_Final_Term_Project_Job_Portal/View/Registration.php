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
            background: white; 
            display: flex;
            justify-content: center;
            align-items: center; 
            min-height: 400px; 
            margin: 0; 
            
        }
        .box 
        { 
            background: white; 
            padding: 30px; 
            width: 380px; 
            border: 1px solid black; 
        }
        h2 
        { 
            color:purple; 
            text-align: center; 
            font-weight: bold;
        }
         label 
        { 
            display: block; 
            margin-top: 12px; 
            color: #333; 
            font-weight: bold;
            text-align: left;
        } 
        input[type=text], input[type=email], input[type=password], input[type=file], select 
        { 
            width: 100%; 
            padding: 8px; 
            margin-top: 4px; 
            box-sizing: border-box; 
            border: 1px solid black; 
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
        } 
        input[type=submit] 
        { 
            margin-top: 16px; 
            width: 100%; 
            padding: 10px; 
            background: green; 
            color: white; 
            /*border: none; */
            /*cursor: pointer; */
            /*font-size: 15px; */
        }
        input[type=submit]:hover 
        { 
            background: darkgreen; 
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
        { font-size: 13px; }

        a{
            color:red;
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
            <label><input type="radio" name="role" value="employer" <?php echo (isset($_POST['role']) && $_POST['role']=='employer') ? 'checked' : ''; ?> onclick="showRoleHint()"> Employer</label>
            <label><input type="radio" name="role" value="seeker" <?php echo (isset($_POST['role']) && $_POST['role']=='seeker') ? 'checked' : ''; ?> onclick="showRoleHint()"> Job Seeker</label>
        </div>
        <?php if (!empty($roleError)) echo "<p class='error'>$roleError</p>"; ?>

        <p id="rolehint" class="hint"></p>

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

</div>

<script>
function showRoleHint() {
    let role = document.querySelector('input[name="role"]:checked');
    let hint = document.getElementById("rolehint");
    if (role) {
        if (role.value === "employer") {
            hint.innerHTML = "Your ID(Employer): " + <?php echo $_SESSION["user_id"] ?? 0; ?>;
        } else {
            hint.innerHTML = "Your ID (Job Seeker): " + <?php echo $_SESSION["user_id"] ?? 0; ?>;
        }
    }
}
</script>
</body>
</html>





