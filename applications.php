<?php
// api/applications.php
// Handles: PUT /api/applications/{id}
// Body JSON: { "status": "Reviewed" | "Shortlisted" | "Rejected" }

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/ApplicationModel.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

// ── Auth guard ────────────────────────────────────────────────────
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthenticated.']);
    exit;
}

// ── Parse application id from URL: /api/applications/42 ──────────
$path  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$appId = (int) end($parts);   // last segment

if ($appId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid application ID.']);
    exit;
}

// ── Only PUT allowed ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Role must be employer ─────────────────────────────────────────
if ($_SESSION['role'] !== 'employer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden.']);
    exit;
}

// ── Read + validate body ──────────────────────────────────────────
$body   = file_get_contents('php://input');
$data   = json_decode($body, true);
$status = trim($data['status'] ?? '');

$allowed = ['Reviewed', 'Shortlisted', 'Rejected'];
if (!in_array($status, $allowed, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit;
}

// ── Update ────────────────────────────────────────────────────────
$model   = new ApplicationModel();
$updated = $model->updateStatus($appId, $status, (int) $_SESSION['user_id']);

if ($updated) {
    echo json_encode(['success' => true, 'status' => $status]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Application not found or access denied.']);
}
