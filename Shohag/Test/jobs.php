<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

$database = new db();
$connection = $database->connection();

// Get filter parameters
$filters = [
    'category_id' => isset($_GET['category_id']) ? $_GET['category_id'] : '',
    'location' => isset($_GET['location']) ? $_GET['location'] : '',
    'job_type' => isset($_GET['job_type']) ? $_GET['job_type'] : '',
    'salary_range' => isset($_GET['salary_range']) ? $_GET['salary_range'] : ''
];

// Remove empty filters
$filters = array_filter($filters, function($value) {
    return !empty($value);
});

// Get jobs with filters
$result = $database->getActiveJobs($connection, $filters);

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
    'count' => count($jobs),
    'filters_applied' => $filters
]);

$connection->close();
?>
