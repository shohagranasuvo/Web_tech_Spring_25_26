<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "seeker") {
    Header("Location: Login.php");
    exit();
}
include "../Model/db.php";
$database = new db();
$connection = $database->connection();

$user_id = $_SESSION["user_id"];
$profile = $database->getSeekerProfile($connection, $user_id)->fetch_assoc();
$user = $database->getUserById($connection, $user_id)->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Job Seeker Profile</title>
    <style>
    body 
    { 
        font-family: Arial; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        min-height: 100vh; 
        margin: 0;
        background: #f0f4ff;
    }
    .box 
    { 
        padding: 30px; 
        width: 400px; 
        border: 1px solid #1a1aff;
        background: #f8f9ff;
    }
    h2 
    { 
        color: #1a1aff; 
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
        border: 1px solid #1a1aff; 
    }
    input[type=submit] 
    { 
        background: #1a1aff; 
        color: white; 
        border: none; 
        cursor: pointer; 
        margin-top: 14px; 
        font-size: 14px; 
    }
    input[type=submit]:hover 
    { 
        background: #0000cc; 
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
    a 
    { 
        color: #1a1aff; 
        font-size: 14px; 
    }
</style>
</head>
<body>
<div class="box">
    <h2>Edit Job Seeker Profile</h2>
    <p>Welcome, <b><?php echo $_SESSION["name"]; ?></b> | 
    Your ID: <b><?php echo $_SESSION["user_id"]; ?></b> | 
    Role: <b><?php echo $_SESSION["role"]; ?></b></p>

    <?php
    $formError = $_GET['error'] ?? '';
    $success = $_GET['success'] ?? '';
    if ($formError == "empty") echo "<p class='error'>Fields cannot be empty.</p>";
    if ($formError == "wrongpass") echo "<p class='error'>Current password is incorrect.</p>";
    if ($formError == "passlen") echo "<p class='error'>New password must be at least 8 characters.</p>";
    if ($formError == "filetype") echo "<p class='error'>Only PDF files allowed for resume.</p>";
    if ($formError == "filesize") echo "<p class='error'>Resume must be under 2MB.</p>";
    if ($success == "1") echo "<p class='success'>Profile updated successfully!</p>";
    if ($success == "2") echo "<p class='success'>Password changed successfully!</p>";
    ?>

    <form method="post" action="../Controller/EditProfileSeekerController.php" enctype="multipart/form-data">
        <p class="section-title">Your Details</p>

        <label>Headline: <span class="hint">(e.g. Junior Developer)</span></label>
        <input type="text" name="headline" value="<?php echo htmlspecialchars($profile['headline'] ?? ''); ?>">

        <label>Skills: <span class="hint">(comma-separated)</span></label>
        <input type="text" name="skills" value="<?php echo htmlspecialchars($profile['skills'] ?? ''); ?>">

        <label>Years of Experience:</label>
        <input type="number" name="years_experience" min="0" max="50" value="<?php echo htmlspecialchars($profile['years_experience'] ?? ''); ?>">

        <label>Re-upload Resume PDF (optional, max 2MB):</label>
        <?php if (!empty($user['file_path'])) echo "<p style='font-size:12px;color:gray;'>Current: " . basename($user['file_path']) . "</p>"; ?>
        <input type="file" name="file">

        <input type="submit" name="submit_profile" value="Update Profile">
    </form>

    <form method="post" action="../Controller/EditProfileSeekerController.php">
        <p class="section-title">Change Password</p>

        <label>Current Password:</label>
        <input type="password" name="current_password">

        <label>New Password (min 8 characters):</label>
        <input type="password" name="new_password">

        <input type="submit" name="submit_password" value="Change Password">
    </form>

    <br><a href="JobBoard.php">← Back to Job Board</a>
</div>
</body>
</html>



