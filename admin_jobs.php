<?php
// api/admin_jobs.php
// Handles: DELETE /api/admin/jobs/{id}   → soft-delete (sets status='closed')

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/ApplicationModel.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$path  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$jobId = (int) end($parts);

if ($jobId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid job ID.']);
    exit;
}

$model  = new ApplicationModel();
$closed = $model->adminCloseJob($jobId);

if ($closed) {
    echo json_encode(['success' => true, 'message' => 'Job closed successfully.']);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Job not found.']);
}
