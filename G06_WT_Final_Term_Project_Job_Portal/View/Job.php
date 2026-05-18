<?php
include_once "../Model/db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "employer") {
    header("Location: Login.php");
    exit();
}

$database = new db();
$connection = $database->connection();
$categories = $database->getAllCategories($connection);

$is_edit = false;
$job = [];

if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $job_id = intval($_GET['edit_id']);
    $job_result = $database->getJobById($connection, $job_id);

    if ($job_result && $job_result->num_rows > 0) {
        $job = $job_result->fetch_assoc();
        
        // এখানে ফিক্সড: EmployerDashboard.php
        if ($job['employer_id'] != $_SESSION["user_id"]) {
            header("Location: EmployerDashboard.php");
            exit();
        }
    } else {
        header("Location: EmployerDashboard.php");
        exit();
    }
}

$error_message = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == "empty") $error_message = "Please fill all required fields (*)";
    if ($_GET['error'] == "failed") $error_message = "Database error. Failed to save job.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $is_edit ? "Edit Job" : "Create Job"; ?></title>
    <style>
        body{ font-family: Arial; background-color: #f4f4f4; margin: 40px; }
        .box{ width: 600px; background: white; margin: auto; padding: 20px; border-radius: 5px; }
        h2{ text-align: center; }
        label{ display: block; margin-top: 10px; font-weight: bold; }
        input[type=text], input[type=date], textarea, select{ width: 100%; padding: 10px; margin-top: 5px; box-sizing: border-box; }
        textarea{ height: 100px; }
        input[type=submit]{ width: 100%; padding: 12px; margin-top: 20px; background: green; color: white; border: none; cursor: pointer; }
        .error{ background: #ffd6d6; color: red; padding: 10px; margin-bottom: 15px; }
        a{ text-decoration: none; color: blue; }
    </style>
</head>
<body>

<div class="box">
    <h2><?php echo $is_edit ? "Edit Job" : "Create Job"; ?></h2>

    <?php if($error_message != ""){ ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php } ?>

    <form method="POST" action="../Controller/JobController.php">
        <?php if($is_edit){ ?>
            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
        <?php } ?>

        <label>Job Title *</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($job['title'] ?? ''); ?>" required>

        <label>Category *</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while($cat = $categories->fetch_assoc()){ ?>
                <option value="<?php echo $cat['id']; ?>" <?php if(isset($job['category_id']) && $job['category_id'] == $cat['id']){ echo "selected"; } ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Description *</label>
        <textarea name="description" required><?php echo htmlspecialchars($job['description'] ?? ''); ?></textarea>

        <label>Requirements</label>
        <textarea name="requirements"><?php echo htmlspecialchars($job['requirements'] ?? ''); ?></textarea>

        <label>Salary</label>
        <input type="text" name="salary_range" value="<?php echo htmlspecialchars($job['salary_range'] ?? ''); ?>">

        <label>Location *</label>
        <select name="location" required>
            <option value="">Select Location</option>
            <?php
            $locations = ["Dhaka", "Chattogram", "Sylhet", "Rajshahi", "Khulna", "Barishal", "Rangpur", "Mymensingh", "Remote"];
            $current_location = $job['location'] ?? '';
            foreach($locations as $loc){
            ?>
                <option value="<?php echo $loc; ?>" <?php if($current_location == $loc){ echo "selected"; } ?>><?php echo $loc; ?></option>
            <?php } ?>
        </select>

        <br><br>
        <label>Job Type *</label>
        <?php $type = $job['job_type'] ?? ''; ?>
        <input type="radio" name="job_type" value="Full-time" <?php if($type == "Full-time" || empty($type)){ echo "checked"; } ?>> Full-time
        <input type="radio" name="job_type" value="Part-time" <?php if($type == "Part-time"){ echo "checked"; } ?>> Part-time
        <input type="radio" name="job_type" value="Remote" <?php if($type == "Remote"){ echo "checked"; } ?>> Remote

        <br><br>
        <label>Deadline *</label>
        <input type="date" name="deadline" value="<?php echo $job['deadline'] ?? ''; ?>" required>

        <?php if($is_edit){ ?>
            <input type="submit" name="update_job" value="Update Job">
        <?php } else { ?>
            <input type="submit" name="create_job" value="Create Job">
        <?php } ?>
    </form>
    <br>
    <a href="EmployerDashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>