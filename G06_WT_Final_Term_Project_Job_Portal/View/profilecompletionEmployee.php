<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "employer") {
    Header("Location: Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Employer Profile</title>
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
    .banner 
    { 
        background: yellow; 
        border: 1px solid orange; 
        padding: 8px; 
        text-align: center; 
        font-size: 14px; 
        margin-bottom: 10px;
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
    textarea 
    { 
        height: 80px; 
        resize: vertical; 
    }
    input[type=submit] 
    { 
        background: #003399; 
        color: white; 
        border: none; 
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
        color: red; 
        font-size: 13px; 
    }
    .success 
    { 
        color: green; 
        font-size: 13px; 
    }
</style>
</head>
<body>
<div class="box">
    <h2>Complete Your Employer Profile</h2>
    <div class="banner"> Profile Incomplete — Please fill in your company details.</div>

    <?php
    $formError = $_GET['error'] ?? '';
    $success = $_GET['success'] ?? '';
    if ($formError == "empty") echo "<p class='error'>All fields are required.</p>";
    if ($success == "1") echo "<p class='success'>Profile saved successfully!</p>";
    ?>

    <form method="post" action="../Controller/ProfileEmployerController.php" enctype="multipart/form-data">
        <label>Company Name:</label>
        <input type="text" name="company_name">

        <label>Industry:</label>
        <select name="industry">
            <option value="">-- Select Industry --</option>
            <option value="Technology">Technology</option>
            <option value="Finance">Finance</option>
            <option value="Healthcare">Healthcare</option>
            <option value="Education">Education</option>
            <option value="Retail">Retail</option>
            <option value="Manufacturing">Manufacturing</option>
            <option value="Other">Other</option>
        </select>

        <label>Company Description:</label>
        <textarea name="description" placeholder="Tell job seekers about your company..."></textarea>

        <label>Website URL:</label>
        <input type="url" name="website" placeholder="https://yourcompany.com">

        <input type="submit" name="submit" value="Save Profile">
    </form>
</div>
</body>
</html>

