<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "employer") {
    Header("Location: Login.php");
    exit();
}
include "../Model/db.php";
$database = new db();
$connection = $database->connection();

$user_id = $_SESSION["user_id"];
$profile = $database->getEmployerProfile($connection, $user_id)->fetch_assoc();
$user = $database->getUserById($connection, $user_id)->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Employer Profile</title>
    <style>
        body 
        { 
            font-family: Arial; 
            background: #f0f0f0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }
        .box 
        { 
            background: white; 
            padding: 30px; 
            width: 420px; 
            border: 1px solid #ccc; 
        }
        h2 
        { 
            color: #003399; 
            text-align: center; 
        }
        label 
        { 
            display: block; 
            margin-top: 12px; 
            color: #333; }
        input[type=text], input[type=url], input[type=password], input[type=file], textarea, select 
        { 
            width: 100%; 
            padding: 8px; 
            margin-top: 4px;
            box-sizing: border-box; 
            border: 1px solid #aaa; 
        }
        textarea 
        { 
            height: 80px; 
            resize: vertical; 
        }
        input[type=submit] 
        { 
            margin-top: 16px; 
            width: 100%; 
            padding: 10px; 
            background: blue;
            color: white; 
            border: none; 
            cursor: pointer; 
            font-size: 15px; 
        }
        input[type=submit]:hover 
        { 
            background: #0044cc; 
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
        .section-title { color: #003399; margin-top: 20px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        a { color: #003399; font-size: 14px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Edit Employer Profile</h2>
    <p>Welcome, <b><?php echo $_SESSION["name"]; ?></b> | 
    Your ID: <b><?php echo $_SESSION["user_id"]; ?></b> | 
    Role: <b><?php echo $_SESSION["role"]; ?></b></p>

    <?php
    $formError = $_GET['error'] ?? '';
    $success = $_GET['success'] ?? '';
    if ($formError== "empty") echo "<p class='error'>Fields cannot be empty.</p>";
    if ($formError== "wrongpass") echo "<p class='error'>Current password is incorrect.</p>";
    if ($formError== "passlen") echo "<p class='error'>New password must be at least 8 characters.</p>";
    if ($success== "1") echo "<p class='success'>Profile updated successfully!</p>";
    if ($success== "2") echo "<p class='success'>Password changed successfully!</p>";
    ?>

    <form method="post" action="" enctype="multipart/form-data" enctype="multipart/form-data">
        <p class="section-title">Company Info</p>

        <label>Company Name:</label>
        <input type="text" name="company_name" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>">

        <label>Industry:</label>
        <select name="industry">
            <?php
            $industries = ["Technology","Finance","Healthcare","Education","Retail","Manufacturing","Other"];
            foreach ($industries as $ind) {
                $sel = (($profile['industry'] ?? '') == $ind) ? 'selected' : '';
                echo "<option value='$ind' $sel>$ind</option>";
            }
            ?>
        </select>

        <label>Description:</label>
        <textarea name="description"><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>

        <label>Website URL:</label>
        <input type="url" name="website" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">

        <label>Re-upload Company Logo (optional):</label>
        <?php if (!empty($user['file_path'])) echo "<p style='font-size:12px;color:gray;'>Current: " . basename($user['file_path']) . "</p>"; ?>
        <input type="file" name="file">

        <input type="submit" name="submit_profile" value="Update Profile">
    </form>

    <form method="post" action="../Controller/EditProfileEmployerController.php">
        <p class="section-title">Change Password</p>

        <label>Current Password:</label>
        <input type="password" name="current_password">

        <label>New Password (min 8 characters):</label>
        <input type="password" name="new_password">

        <input type="submit" name="submit_password" value="Change Password">
    </form>

    <br><a href="EmployerDashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>


