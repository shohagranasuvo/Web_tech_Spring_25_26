<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: Login.php");
    exit();
}

include "../Model/db.php";

$db = new db();
$conn = $db->connection();

$user_id = $_SESSION["user_id"];

// GET saved jobs
$saved_jobs = $db->getSavedJobs($conn, $user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saved Jobs</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6ff;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h2 {
            color: #1a1aff;
            margin-bottom: 20px;
        }

        .job-card {
            background: white;
            border: 1px solid #1a1aff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .job-card:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1aff;
        }

        .company {
            color: #555;
            margin-top: 5px;
        }

        .meta {
            font-size: 13px;
            color: #777;
            margin-top: 8px;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin-top: 10px;
            background: #1a1aff;
            color: white;
            text-decoration: none;
            font-size: 13px;
            border-radius: 4px;
        }

        .btn:hover {
            background: #0000cc;
        }

        .remove-btn {
            background: #ff3333;
        }

        .empty {
            background: white;
            padding: 30px;
            text-align: center;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>⭐ Saved Jobs</h2>

    <?php if ($saved_jobs && $saved_jobs->num_rows > 0): ?>

        <?php while ($job = $saved_jobs->fetch_assoc()): ?>
            
            <div class="job-card">

                <div class="title">
                    <?php echo htmlspecialchars($job['title']); ?>
                </div>

                <div class="company">
                    <?php echo htmlspecialchars($job['company_name']); ?>
                </div>

                <div class="meta">
                    Category: <?php echo htmlspecialchars($job['category_name']); ?><br>
                    Saved At: <?php echo $job['created_at']; ?>
                </div>

                <a class="btn"
                   href="JobDetail.php?id=<?php echo $job['id']; ?>">
                   View Job
                </a>
                
                <a class="btn remove-btn"
                   href="RemoveSavedJob.php?id=<?php echo $job['id']; ?>">
                   Remove
                </a>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="empty">
            No saved jobs yet 😢
        </div>

    <?php endif; ?>

</div>

</body>
</html>