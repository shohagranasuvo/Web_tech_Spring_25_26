<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login as a job seeker.']);
    exit;
}

$database = new db();
$connection = $database->connection();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$job_id = isset($data['job_id']) ? $data['job_id'] : '';

if (empty($job_id)) {
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit;
}

// Toggle saved job
$result = $database->toggleSavedJob($connection, $_SESSION['user_id'], $job_id);

if ($result === 'added') {
    echo json_encode([
        'success' => true,
        'action' => 'added',
        'message' => 'Job saved successfully'
    ]);
} else if ($result === 'removed') {
    echo json_encode([
        'success' => true,
        'action' => 'removed',
        'message' => 'Job removed from saved list'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to toggle saved job'
    ]);
}

$connection->close();
?>
