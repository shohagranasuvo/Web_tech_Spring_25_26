<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    Header("Location: Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Seeker Profile</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: white; padding: 30px; width: 400px; border: 1px solid #ccc; }
        h2 { color: #003399; text-align: center; }
        .banner { background: #fff3cd; border: 1px solid #ffc107; padding: 10px; color: #856404; margin-bottom: 14px; text-align: center; font-size: 14px; }
        label { display: block; margin-top: 12px; color: #333; }
        input[type=text], input[type=number] { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #aaa; }
        input[type=submit] { margin-top: 16px; width: 100%; padding: 10px; background: #003399; color: white; border: none; cursor: pointer; font-size: 15px; }
        input[type=submit]:hover { background: #0044cc; }
        .error { color: red; font-size: 13px; }
        .success { color: green; font-size: 13px; }
        .hint { color: gray; font-size: 12px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Complete Your Job Seeker Profile</h2>
    <div class="banner">Profile Incomplete — Please fill in your details.</div>

    <?php
    $formError = $_GET['error'] ?? '';
    $success = $_GET['success'] ?? '';
    if ($formError == "empty") echo "<p class='error'>All fields are required.</p>";
    if ($success == "1") echo "<p class='success'>Profile saved successfully!</p>";
    ?>

    <form method="post" action="../Controller/ProfileSeekerController.php" enctype="multipart/form-data">
        <label>Headline: <span class="hint">(e.g. Junior Developer)</span></label>
        <input type="text" name="headline" placeholder="e.g. Junior Web Developer">

        <label>Skills: <span class="hint">(comma-separated, e.g. PHP, JS, MySQL)</span></label>
        <input type="text" name="skills" placeholder="PHP, JavaScript, MySQL">

        <label>Years of Experience:</label>
        <input type="number" name="years_experience" min="0" max="50" placeholder="e.g. 2">

        <input type="submit" name="submit" value="Save Profile">
    </form>
</div>
</body>
</html>

