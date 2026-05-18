<?php
// controllers/ApplicationController.php

class ApplicationController extends Controller {
    
    private $applicationModel;
    private $jobModel;
    
    public function __construct() {
        $this->applicationModel = $this->model('Application');
        $this->jobModel = $this->model('Job');
    }
    
    // Display all applications
    public function index() {
        $this->requireSeeker();
        
        $user_id = $_SESSION['user_id'];
        
        // Get all applications
        $applications = $this->applicationModel->getApplicationsBySeeker($user_id);
        
        // Get statistics
        $stats = $this->applicationModel->getApplicationStats($user_id);
        
        $data = [
            'title' => 'My Applications',
            'applications' => $applications,
            'stats' => $stats,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ];
        
        $this->view('applications/index', $data);
    }
    
    // Submit application
    public function submit() {
        $this->requireSeeker();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=jobs');
        }
        
        $user_id = $_SESSION['user_id'];
        $job_id = $this->post('job_id');
        $cover_letter = $this->post('cover_letter');
        $resume_option = $this->post('resume_option', 'upload');
        
        // Validation
        $errors = [];
        
        if (empty($job_id)) {
            $errors[] = 'Job ID is required';
        }
        
        if (empty($cover_letter)) {
            $errors[] = 'Cover letter is required';
        }
        
        // Check if already applied
        if ($this->applicationModel->hasApplied($user_id, $job_id)) {
            $errors[] = 'You have already applied for this job';
        }
        
        // Handle resume
        $resume_path = '';
        
        if ($resume_option === 'profile') {
            // Use profile resume
            $userModel = $this->model('User');
            $seeker = $userModel->getSeekerProfile($user_id);
            
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
                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($file_extension, ALLOWED_FILE_TYPES)) {
                    $errors[] = 'Invalid file type. Only PDF, DOC, and DOCX files are allowed';
                }
                
                // Validate file size
                if ($file['size'] > MAX_FILE_SIZE) {
                    $errors[] = 'File size exceeds 5MB limit';
                }
                
                if (empty($errors)) {
                    // Create uploads directory if it doesn't exist
                    if (!file_exists(RESUME_PATH)) {
                        mkdir(RESUME_PATH, 0755, true);
                    }
                    
                    // Generate unique filename
                    $filename = 'resume_' . $user_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $target_path = RESUME_PATH . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $resume_path = 'uploads/resumes/' . $filename;
                    } else {
                        $errors[] = 'Failed to upload resume';
                    }
                }
            }
        }
        
        // If there are errors, redirect back
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('index.php?page=job-detail&id=' . $job_id);
        }
        
        // Submit application
        $result = $this->applicationModel->submitApplication($job_id, $user_id, $cover_letter, $resume_path);
        
        if ($result) {
            $this->setFlash('success', 'Application submitted successfully!');
            $this->redirect('index.php?page=applications');
        } else {
            $this->setFlash('error', 'Failed to submit application. Please try again.');
            $this->redirect('index.php?page=job-detail&id=' . $job_id);
        }
    }
}
?>
