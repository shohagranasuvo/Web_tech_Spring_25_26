<?php
session_start();
require_once '../config/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    header('Location: login.php');
    exit;
}

$database = new db();
$connection = $database->connection();


$categories = $database->getAllCategories($connection);


$jobs_result = $database->getActiveJobs($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board - Find Your Dream Job</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .job-board-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .search-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .search-box {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filters-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .job-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .job-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .job-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
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
            font-size: 24px;
            color: #ccc;
            transition: color 0.3s;
        }
        
        .bookmark-btn.saved {
            color: #e74c3c;
        }
        
        .bookmark-btn:hover {
            color: #e74c3c;
        }
        
        .job-details {
            margin: 15px 0;
        }
        
        .job-detail-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 8px;
            font-size: 13px;
            color: #666;
        }
        
        .job-detail-item strong {
            color: #333;
        }
        
        .job-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e8f4f8;
            color: #0066cc;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .view-details-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s;
        }
        
        .view-details-btn:hover {
            background: #0056b3;
        }
        
        .no-jobs {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .results-count {
            margin: 20px 0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="job-board-container">
        <h1>Find Your Dream Job</h1>
        
        <div class="search-section">
            <input type="text" 
                   id="search-box" 
                   class="search-box" 
                   placeholder="Search for jobs, companies, or keywords...">
            
            <div class="filters-container">
                <div class="filter-group">
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
                </div>
                
                <div class="filter-group">
                    <select id="type-filter">
                        <option value="">All Job Types</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Remote">Remote</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="location-filter">
                        <option value="">All Locations</option>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Chittagong">Chittagong</option>
                        <option value="Sylhet">Sylhet</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Khulna">Khulna</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="salary-filter">
                        <option value="">All Salary Ranges</option>
                        <option value="20k-30k">20k-30k</option>
                        <option value="30k-50k">30k-50k</option>
                        <option value="50k-80k">50k-80k</option>
                        <option value="80k+">80k+</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="results-count" id="results-count">
            Loading jobs...
        </div>
        
        <div id="jobs-container" class="jobs-grid">
       
            <?php
            if ($jobs_result && $jobs_result->num_rows > 0) {
                while ($job = $jobs_result->fetch_assoc()) {
                    $is_saved = $database->isSavedJob($connection, $_SESSION['user_id'], $job['id']);
                    ?>
                    <div class="job-card" data-job-id="<?php echo $job['id']; ?>">
                        <div class="job-header">
                            <div>
                                <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                                <div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>
                            </div>
                            <button class="bookmark-btn <?php echo $is_saved ? 'saved' : ''; ?>" 
                                    data-job-id="<?php echo $job['id']; ?>"
                                    title="<?php echo $is_saved ? 'Remove from saved' : 'Save job'; ?>">
                                <?php echo $is_saved ? '♥' : '♡'; ?>
                            </button>
                        </div>
                        
                        <div class="job-details">
                            <span class="job-badge"><?php echo htmlspecialchars($job['category_name']); ?></span>
                            <span class="job-badge"><?php echo htmlspecialchars($job['job_type']); ?></span>
                        </div>
                        
                        <div class="job-details">
                            <div class="job-detail-item">
                                <strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?>
                            </div>
                            <div class="job-detail-item">
                                <strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?>
                            </div>
                            <div class="job-detail-item">
                                <strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                            </div>
                        </div>
                        
                        <a href="job-detail.php?id=<?php echo $job['id']; ?>" class="view-details-btn">
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

    <script src="../js/job-board.js"></script>
</body>
</html>

<?php $connection->close(); ?>
