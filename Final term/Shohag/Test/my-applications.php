<?php
session_start();
require_once '../config/db.php';

// Redirect if not logged in as seeker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    header('Location: login.php');
    exit;
}

$database = new db();
$connection = $database->connection();

// Get all applications for this seeker
$applications_result = $database->getMyApplications($connection, $_SESSION['user_id']);

// Check for success message
$success_message = isset($_SESSION['application_success']) ? $_SESSION['application_success'] : '';
unset($_SESSION['application_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .applications-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .back-link {
            color: #007bff;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .applications-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .application-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            transition: all 0.3s ease;
        }
        
        .application-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .job-info h3 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #333;
        }
        
        .company-name {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .job-location {
            color: #999;
            font-size: 13px;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-submitted {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-reviewed {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-shortlisted {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .application-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .meta-item {
            font-size: 13px;
        }
        
        .meta-label {
            color: #666;
            display: block;
            margin-bottom: 3px;
        }
        
        .meta-value {
            color: #333;
            font-weight: 500;
        }
        
        .application-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        .view-job-btn {
            padding: 8px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .view-job-btn:hover {
            background: #0056b3;
        }
        
        .no-applications {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .no-applications-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .no-applications h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .no-applications p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .browse-jobs-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .browse-jobs-btn:hover {
            background: #0056b3;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="applications-container">
        <div class="page-header">
            <h1>My Applications</h1>
            <a href="job-board.php" class="back-link">← Back to Job Board</a>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($applications_result && $applications_result->num_rows > 0): ?>
            <?php
            // Calculate statistics
            $total_applications = $applications_result->num_rows;
            $submitted_count = 0;
            $reviewed_count = 0;
            $shortlisted_count = 0;
            $rejected_count = 0;
            
            $applications = [];
            while ($app = $applications_result->fetch_assoc()) {
                $applications[] = $app;
                
                switch ($app['status']) {
                    case 'Submitted':
                        $submitted_count++;
                        break;
                    case 'Reviewed':
                        $reviewed_count++;
                        break;
                    case 'Shortlisted':
                        $shortlisted_count++;
                        break;
                    case 'Rejected':
                        $rejected_count++;
                        break;
                }
            }
            ?>
            
            <div class="stats-summary">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_applications; ?></div>
                    <div class="stat-label">Total Applications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $submitted_count; ?></div>
                    <div class="stat-label">Submitted</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $reviewed_count; ?></div>
                    <div class="stat-label">Under Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $shortlisted_count; ?></div>
                    <div class="stat-label">Shortlisted</div>
                </div>
            </div>
            
            <div class="applications-list">
                <?php foreach ($applications as $application): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <div class="job-info">
                                <h3><?php echo htmlspecialchars($application['job_title']); ?></h3>
                                <div class="company-name"><?php echo htmlspecialchars($application['company_name']); ?></div>
                                <div class="job-location">📍 <?php echo htmlspecialchars($application['location']); ?></div>
                            </div>
                            <div class="status-badge status-<?php echo strtolower($application['status']); ?>">
                                <?php echo htmlspecialchars($application['status']); ?>
                            </div>
                        </div>
                        
                        <div class="application-meta">
                            <div class="meta-item">
                                <span class="meta-label">Applied On</span>
                                <span class="meta-value"><?php echo date('M d, Y', strtotime($application['created_at'])); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Industry</span>
                                <span class="meta-value"><?php echo htmlspecialchars($application['industry']); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Resume</span>
                                <span class="meta-value">
                                    <a href="../<?php echo htmlspecialchars($application['resume_path']); ?>" 
                                       target="_blank" 
                                       style="color: #007bff;">View Resume</a>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($application['cover_letter'])): ?>
                            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                                <strong style="display: block; margin-bottom: 8px; color: #333;">Cover Letter:</strong>
                                <div style="color: #666; font-size: 14px; line-height: 1.6;">
                                    <?php 
                                    // Show first 200 characters
                                    $cover = htmlspecialchars($application['cover_letter']);
                                    echo strlen($cover) > 200 ? substr($cover, 0, 200) . '...' : $cover;
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="application-actions">
                            <a href="job-detail.php?id=<?php echo $application['job_id']; ?>" class="view-job-btn">
                                View Job Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <div class="no-applications">
                <div class="no-applications-icon">📋</div>
                <h3>No Applications Yet</h3>
                <p>You haven't applied to any jobs yet. Start exploring opportunities!</p>
                <a href="job-board.php" class="browse-jobs-btn">Browse Jobs</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $connection->close(); ?>
