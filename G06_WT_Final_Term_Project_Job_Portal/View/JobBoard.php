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

// Get all categories for filter dropdown
$categories = $database->getAllCategories($connection);

// Get initial jobs (all active jobs)
$jobs_result = $database->getActiveJobs($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board</title>
    <style>
        body { 
            font-family: Arial; 
            background: #f0f4ff; 
            margin: 0; 
            padding: 20px; 
        }
        .header {
            background: white;
            padding: 15px 30px;
            border: 1px solid #1a1aff;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { color: #1a1aff; margin: 0; }
        .nav-links { display: flex; gap: 20px; }
        .nav-links a { 
            color: #1a1aff; 
            text-decoration: none; 
            font-weight: bold; 
        }
        .nav-links a:hover { text-decoration: underline; }
        
        .container { max-width: 1200px; margin: 0 auto; }
        
        .search-section {
            background: white;
            padding: 25px;
            border: 1px solid #1a1aff;
            margin-bottom: 25px;
        }
        
        .search-box {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #1a1aff;
            margin-bottom: 15px;
            box-sizing: border-box;
        }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .filters select {
            width: 100%;
            padding: 10px;
            border: 1px solid #1a1aff;
            font-size: 14px;
        }
        
        .results-count {
            margin: 15px 0;
            color: #333;
            font-weight: bold;
        }
        
        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .job-card {
            background: white;
            border: 1px solid #1a1aff;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .job-card:hover {
            box-shadow: 0 4px 12px rgba(26,26,255,0.2);
        }
        
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        
        .job-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1aff;
            margin-bottom: 5px;
        }
        
        .company-name {
            color: #666;
            font-size: 14px;
        }
        
        .bookmark-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 28px;
            color: #ccc;
            transition: color 0.3s;
            padding: 0;
        }
        
        .bookmark-btn.saved {
            color: #ff1a1a;
        }
        
        .bookmark-btn:hover {
            color: #ff1a1a;
        }
        
        .job-badges {
            margin: 12px 0;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: #e8f4f8;
            color: #1a1aff;
            font-size: 12px;
            margin-right: 8px;
            margin-bottom: 5px;
        }
        
        .job-details {
            margin: 12px 0;
            font-size: 13px;
            color: #555;
        }
        
        .job-details div {
            margin-bottom: 5px;
        }
        
        .view-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #1a1aff;
            color: white;
            text-align: center;
            text-decoration: none;
            margin-top: 12px;
            transition: background 0.3s;
        }
        
        .view-btn:hover {
            background: #0000cc;
        }
        
        .no-jobs {
            text-align: center;
            padding: 40px;
            background: white;
            border: 1px solid #1a1aff;
            color: #666;
        }
        
        .loading {
            text-align: center;
            padding: 30px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Job Portal - Find Jobs</h1>
    <div style="display: flex; gap: 30px; align-items: center;">
        <div class="nav-links">
            <a href="JobBoard.php">Browse Jobs</a>
            <a href="SavedJobs.php">Saved Jobs</a>
            <a href="../Controller/MyApplicationsController.php">My Applications</a>
            <a href="editProfileJobSeeker.php">Edit Profile</a>
        </div>
        <div style="color: #333;">
            Welcome, <b><?php echo $_SESSION["name"]; ?></b>
        </div>
    </div>
</div>

<div class="container">
    <div class="search-section">
        <input type="text" 
               id="search-box" 
               class="search-box" 
               placeholder="Search jobs, companies, keywords...">
        
        <div class="filters">
            <select id="category-filter">
                <option value="">All Categories</option>
                <?php 
                if ($categories && $categories->num_rows > 0) {
                    while ($cat = $categories->fetch_assoc()) {
                        echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</option>';
                    }
                }
                ?>
            </select>
            
            <select id="type-filter">
                <option value="">All Types</option>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Remote">Remote</option>
            </select>
            
            <select id="location-filter">
                <option value="">All Locations</option>
                <option value="Dhaka">Dhaka</option>
                <option value="Chittagong">Chittagong</option>
                <option value="Sylhet">Sylhet</option>
                <option value="Rajshahi">Rajshahi</option>
                <option value="Khulna">Khulna</option>
            </select>
            
            <select id="salary-filter">
                <option value="">All Salaries</option>
                <option value="20k-30k">20k-30k</option>
                <option value="30k-50k">30k-50k</option>
                <option value="50k-80k">50k-80k</option>
                <option value="80k+">80k+</option>
            </select>
        </div>
    </div>
    
    <div class="results-count" id="results-count">
        Loading jobs...
    </div>
    
    <div id="jobs-container" class="jobs-grid">
        <?php
        if ($jobs_result && $jobs_result->num_rows > 0) {
            while ($job = $jobs_result->fetch_assoc()) {
                $is_saved = $database->isSavedJob($connection, $user_id, $job['id']);
                ?>
                <div class="job-card">
                    <div class="job-header">
                        <div style="flex: 1;">
                            <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                            <div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>
                        </div>
                        <button class="bookmark-btn <?php echo $is_saved ? 'saved' : ''; ?>" 
                                data-job-id="<?php echo $job['id']; ?>">
                            <?php echo $is_saved ? '♥' : '♡'; ?>
                        </button>
                    </div>
                    
                    <div class="job-badges">
                        <span class="badge"><?php echo htmlspecialchars($job['category_name']); ?></span>
                        <span class="badge"><?php echo htmlspecialchars($job['job_type']); ?></span>
                    </div>
                    
                    <div class="job-details">
                        <div><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></div>
                        <div><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></div>
                        <div><strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['deadline'])); ?></div>
                    </div>
                    
                    <a href="JobDetail.php?id=<?php echo $job['id']; ?>" class="view-btn">
                        View Details & Apply
                    </a>
                </div>
                <?php
            }
        } else {
            echo '<div class="no-jobs">No active jobs found.</div>';
        }
        ?>
    </div>
</div>

<script src="../Controller/JS/job-board.js"></script>

</body>
</html>
<?php $connection->close(); ?>
