<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

$database = new db();
$connection = $database->connection();

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'No search query provided']);
    exit;
}

// Search jobs
$result = $database->searchJobs($connection, $query);

$jobs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if saved (if user is logged in)
        $is_saved = false;
        if (isset($_SESSION['user_id'])) {
            $is_saved = $database->isSavedJob($connection, $_SESSION['user_id'], $row['id']);
        }
        
        $jobs[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'company_name' => $row['company_name'],
            'category_name' => $row['category_name'],
            'location' => $row['location'],
            'job_type' => $row['job_type'],
            'salary_range' => $row['salary_range'],
            'deadline' => $row['deadline'],
            'created_at' => $row['created_at'],
            'is_saved' => $is_saved
        ];
    }
}

echo json_encode([
    'success' => true,
    'jobs' => $jobs,
    'count' => count($jobs)
]);

$connection->close();
?>
