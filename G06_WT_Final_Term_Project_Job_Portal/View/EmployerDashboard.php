<?php
include_once "../Model/db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "employer") {
    header("Location: login.php");
    exit();
}

$database = new db();
$connection = $database->connection();
$employer_id = $_SESSION["user_id"];

$my_jobs = $database->getEmployerJobsWithApplicationCount($connection, $employer_id);

$total_jobs = 0;
$active_jobs = 0;
$total_applications = 0;

$stat_query = "
SELECT
COUNT(DISTINCT j.id) AS total_jobs,
SUM(j.status='active') AS active_jobs,
COUNT(a.id) AS total_applications
FROM jobs j
LEFT JOIN applications a ON j.id = a.job_id
WHERE j.employer_id = ?
";

$stmt = $connection->prepare($stat_query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$stat_result = $stmt->get_result();

if ($row = $stat_result->fetch_assoc()) {
    $total_jobs = intval($row["total_jobs"]);
    $active_jobs = intval($row["active_jobs"]);
    $total_applications = intval($row["total_applications"]);
}

$msg = "";
$msg_class = "";
if (isset($_GET['success'])) {
    $msg_class = "success";
    if ($_GET['success'] == "job_created") $msg = "Job published successfully!";
    elseif ($_GET['success'] == "job_updated") $msg = "Job updated successfully!";
    elseif ($_GET['success'] == "job_deleted") $msg = "Job deleted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employer Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { max-width: 950px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: 0 auto; }
        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .btn-add { background: #2ecc71; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .btn-add:hover { background: #27ae60; }
        .message { padding: 12px; margin: 15px 0; border-radius: 4px; font-size: 14px; }
        .success { background: #e6ffe6; color: green; border: 1px solid green; }
        
        .stats-container { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { flex: 1; background: #fafafa; padding: 15px; border-radius: 6px; border: 1px solid #e1e1e1; text-align: center; }
        .stat-box h4 { margin: 0 0 5px 0; color: #666; font-size: 14px; }
        .stat-box p { margin: 0; font-size: 22px; font-weight: bold; color: #333; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; color: #333; }
        .count-badge { background: #eee; padding: 3px 8px; border-radius: 20px; font-weight: bold; color: #333; }
        
        .btn-action { color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block; vertical-align: middle; }
        
        .btn-edit { background: #f39c12; }
        .btn-edit:hover { background: #d68010; }
        
        .btn-delete { background: red; }
        .btn-delete:hover { background: darkred; }
        
        .btn-toggle { border: none; padding: 6px 12px; border-radius: 20px; font-weight: bold; cursor: pointer; font-size: 12px; transition: 0.2s; }
        .status-active { background-color: #e6ffe6; color: green; border: 1px solid green; }
        .status-closed { background-color: #ffe6e6; color: red; border: 1px solid red; }
        
        .action-cell { white-space: nowrap; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <div>
            <h2>Employer Dashboard</h2>
            <p>Manage your posted jobs and view application status.</p>
        </div>
        <a href="job.php" class="btn-add">+ Post a New Job</a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="message <?php echo $msg_class; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="stats-container">
        <div class="stat-box">
            <h4>Total Jobs</h4>
            <p><?php echo $total_jobs; ?></p>
        </div>
        <div class="stat-box">
            <h4>Active Jobs</h4>
            <p><?php echo $active_jobs; ?></p>
        </div>
        <div class="stat-box">
            <h4>Total Applications</h4>
            <p><?php echo $total_applications; ?></p>
        </div>
    </div>

    <h3>Your Job Listings</h3>
    <table>
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Category</th>
                <th>Job Type</th>
                <th>Applications</th>
                <th>Deadline</th>
                <th>Status (Click to Toggle)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($my_jobs && $my_jobs->num_rows > 0): ?>
                <?php while ($row = $my_jobs->fetch_assoc()): ?>
                    <tr id="job-row-<?php echo $row['id']; ?>">
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                        <td><?php echo htmlspecialchars($row['job_type']); ?></td>
                        <td><span class="count-badge"><?php echo $row['application_count']; ?> Applicants</span></td>
                        <td><?php echo htmlspecialchars($row['deadline']); ?></td>
                        <td>
                            <button class="btn-toggle <?php echo ($row['status'] === 'active') ? 'status-active' : 'status-closed'; ?>" 
                                    onclick="toggleStatus(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['status'], ENT_QUOTES); ?>')">
                                <?php echo ucfirst($row['status']); ?>
                            </button>
                        </td>
                        <td class="action-cell">
                            <a href="job.php?edit_id=<?php echo $row['id']; ?>" class="btn-action btn-edit">Edit</a>
                            <span style="color: #ddd; font-weight: bold; margin: 0 4px;">/</span>
                            <a href="../Controller/JobController.php?delete_job_id=<?php echo $row['id']; ?>" 
                               class="btn-action btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this job listing?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?> <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #777;">You haven't posted any jobs yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleStatus(jobId, currentStatus) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../Controller/ToggleStatusController.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var row = document.getElementById("job-row-" + jobId);
                    var button = row.querySelector(".btn-toggle");
                    
                    button.textContent = response.badge_text;
                    
                    if (response.new_status === 'active') {
                        button.className = "btn-toggle status-active";
                    } else {
                        button.className = "btn-toggle status-closed";
                    }
                    button.setAttribute("onclick", "toggleStatus(" + jobId + ", '" + response.new_status + "')");
                } else {
                    alert("Failed to update status.");
                }
            } catch (e) {
                console.error("Invalid JSON response");
            }
        }
    };
    xhr.send("job_id=" + jobId + "&current_status=" + currentStatus);
}
</script>

</body>
</html>