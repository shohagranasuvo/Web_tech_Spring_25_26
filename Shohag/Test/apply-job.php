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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: job-board.php');
    exit;
}

// Get form data
$job_id = isset($_POST['job_id']) ? trim($_POST['job_id']) : '';
$cover_letter = isset($_POST['cover_letter']) ? trim($_POST['cover_letter']) : '';
$resume_option = isset($_POST['resume_option']) ? $_POST['resume_option'] : 'upload';

// Validation
$errors = [];

if (empty($job_id)) {
    $errors[] = 'Job ID is required';
}

if (empty($cover_letter)) {
    $errors[] = 'Cover letter is required';
}

// Check if already applied
if ($database->hasApplied($connection, $_SESSION['user_id'], $job_id)) {
    $errors[] = 'You have already applied for this job';
}

// Handle resume
$resume_path = '';

if ($resume_option === 'profile') {
    // Use profile resume
    $seeker_result = $database->getSeekerProfile($connection, $_SESSION['user_id']);
    $seeker = $seeker_result->fetch_assoc();
    
    if (empty($seeker['file_path'])) {
        $errors[] = 'No resume found in your profile';
    } else {
        $resume_path = $seeker['file_path'];
    }
} else {
    // Upload new resume
    if (!isset($_FILES['resume']) || $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Please upload your resume';
    } else if ($_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Error uploading resume';
    } else {
        $file = $_FILES['resume'];
        
        // Validate file type
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        
        if (!in_array($file['type'], $allowed_types) && !in_array($file_extension, $allowed_extensions)) {
            $errors[] = 'Invalid file type. Only PDF, DOC, and DOCX files are allowed';
        }
        
        // Validate file size (5MB max)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            $errors[] = 'File size exceeds 5MB limit';
        }
        
        if (empty($errors)) {
            // Create uploads directory if it doesn't exist
            $upload_dir = '../uploads/resumes/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $filename = 'resume_' . $_SESSION['user_id'] . '_' . time() . '_' . uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $resume_path = 'uploads/resumes/' . $filename;
            } else {
                $errors[] = 'Failed to upload resume';
            }
        }
    }
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['application_error'] = implode('<br>', $errors);
    header('Location: job-detail.php?id=' . $job_id);
    exit;
}

// Submit application
$result = $database->submitApplication($connection, $job_id, $_SESSION['user_id'], $cover_letter, $resume_path);

if ($result) {
    $_SESSION['application_success'] = 'Application submitted successfully!';
    header('Location: my-applications.php');
} else {
    $_SESSION['application_error'] = 'Failed to submit application. Please try again.';
    header('Location: job-detail.php?id=' . $job_id);
}

$connection->close();
exit;
?>
