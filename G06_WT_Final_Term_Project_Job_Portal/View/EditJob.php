<?php
session_start();
include_once "../Model/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "employer") {
    header("Location: login.php");
    exit();
}

$db = new db();
$conn = $db->connection();

$job_id = $_GET['id'] ?? 0;

if ($job_id == 0) {
    header("Location: EmployerDashboard.php");
    exit();
}

// Fetch job details
$sql = "SELECT * FROM jobs WHERE id = ? AND employer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: EmployerDashboard.php");
    exit();
}

$job = $result->fetch_assoc();

// Fetch all categories for dropdown
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job</title>
    <style>
        /* Modern CSS Reset & Variable Tokens */
        :root {
            --primary-color: #0056b3;
            --primary-hover: #004085;
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
            --text-color: #333333;
            --border-color: #cccccc;
            --focus-ring: rgba(0, 86, 179, 0.25);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container Card */
        .form-container {
            background-color: var(--card-bg);
            width: 100%;
            max-width: 600px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #111111;
            font-size: 24px;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 10px;
        }

        /* Back Button Link */
        .back-link {
            display: inline-block;
            text-decoration: none;
            color: #666666;
            font-size: 14px;
            margin-bottom: 25px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        /* Form Layout Groups */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: #444444;
        }

        /* Input Controls */
        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 15px;
            box-sizing: border-box;
            background-color: #fafafa;
            transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px var(--focus-ring);
            outline: none;
        }

        select {
            cursor: pointer;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        /* Update Button */
        .submit-btn {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: var(--primary-hover);
        }

        .submit-btn:active {
            transform: scale(0.99);
        }

        .submit-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        /* Required field indicator */
        .required {
            color: #e74c3c;
        }
    </style>
</head>
<body>

<div class="form-container">

    <h2>Edit Job Listing</h2>

    <a href="EmployerDashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

    <form action="../Controller/JobController.php" method="POST">

        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['id']); ?>">

        <div class="form-group">
            <label for="category_id">Category <span class="required">*</span></label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo ($category['id'] == $job['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="title">Job Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" 
                   value="<?php echo htmlspecialchars($job['title']); ?>" 
                   required 
                   placeholder="e.g. Senior Software Engineer">
        </div>

        <div class="form-group">
            <label for="description">Job Description <span class="required">*</span></label>
            <textarea id="description" name="description" required 
                      placeholder="Describe the role, responsibilities, and what makes this position unique..."><?php echo htmlspecialchars($job['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="requirements">Requirements <span class="required">*</span></label>
            <textarea id="requirements" name="requirements" required 
                      placeholder="List required skills, qualifications, experience..."><?php echo htmlspecialchars($job['requirements']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="salary_range">Salary Range <span class="required">*</span></label>
            <input type="text" id="salary_range" name="salary_range" 
                   value="<?php echo htmlspecialchars($job['salary_range']); ?>" 
                   required 
                   placeholder="e.g. $80,000 - $120,000">
        </div>

        <div class="form-group">
            <label for="location">Location <span class="required">*</span></label>
            <input type="text" id="location" name="location" 
                   value="<?php echo htmlspecialchars($job['location']); ?>" 
                   required 
                   placeholder="e.g. New York, NY or Remote">
        </div>

        <div class="form-group">
            <label for="job_type">Job Type <span class="required">*</span></label>
            <input type="text" id="job_type" name="job_type" 
                   value="<?php echo htmlspecialchars($job['job_type']); ?>" 
                   required 
                   placeholder="e.g. Full-time, Part-time, Contract">
        </div>

        <div class="form-group">
            <label for="deadline">Application Deadline <span class="required">*</span></label>
            <input type="date" id="deadline" name="deadline" 
                   value="<?php echo htmlspecialchars($job['deadline']); ?>" 
                   required>
        </div>

        <button type="submit" name="update_job" class="submit-btn">Update Job Details</button>

    </form>

</div>

</body>
</html>