<?php
// controllers/JobController.php

class JobController extends Controller {
    
    private $jobModel;
    private $categoryModel;
    private $savedJobModel;
    private $applicationModel;
    
    public function __construct() {
        $this->jobModel = $this->model('Job');
        $this->categoryModel = $this->model('Category');
        $this->savedJobModel = $this->model('SavedJob');
        $this->applicationModel = $this->model('Application');
    }
    
    // Display job board
    public function index() {
        $this->requireSeeker();
        
        $user_id = $_SESSION['user_id'];
        
        // Get all categories for filter
        $categories = $this->categoryModel->getAll();
        
        // Get all active jobs
        $jobs = $this->jobModel->getActiveJobs();
        
        // Check which jobs are saved
        foreach ($jobs as &$job) {
            $job['is_saved'] = $this->savedJobModel->isSaved($user_id, $job['id']);
        }
        
        $data = [
            'title' => 'Job Board',
            'categories' => $categories,
            'jobs' => $jobs,
            'total_jobs' => count($jobs)
        ];
        
        $this->view('jobs/index', $data);
    }
    
    // Display job detail
    public function detail() {
        $this->requireSeeker();
        
        $job_id = $this->get('id');
        
        if (empty($job_id)) {
            $this->redirect('index.php?page=jobs');
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Get job details
        $job = $this->jobModel->getJobById($job_id);
        
        if (!$job) {
            $this->setFlash('error', 'Job not found');
            $this->redirect('index.php?page=jobs');
        }
        
        // Check if already applied
        $has_applied = $this->applicationModel->hasApplied($user_id, $job_id);
        
        // Check if saved
        $is_saved = $this->savedJobModel->isSaved($user_id, $job_id);
        
        // Get seeker profile
        $userModel = $this->model('User');
        $seeker = $userModel->getSeekerProfile($user_id);
        
        $data = [
            'title' => $job['title'],
            'job' => $job,
            'has_applied' => $has_applied,
            'is_saved' => $is_saved,
            'seeker' => $seeker,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ];
        
        $this->view('jobs/detail', $data);
    }
    
    // Filter jobs (AJAX)
    public function filter() {
        $this->requireSeeker();
        
        $filters = [
            'category_id' => $this->get('category_id'),
            'location' => $this->get('location'),
            'job_type' => $this->get('job_type'),
            'salary_range' => $this->get('salary_range')
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });
        
        $jobs = $this->jobModel->getActiveJobs($filters);
        
        $user_id = $_SESSION['user_id'];
        
        // Check which jobs are saved
        foreach ($jobs as &$job) {
            $job['is_saved'] = $this->savedJobModel->isSaved($user_id, $job['id']);
        }
        
        $this->json([
            'success' => true,
            'jobs' => $jobs,
            'count' => count($jobs),
            'filters_applied' => $filters
        ]);
    }
    
    // Search jobs (AJAX)
    public function search() {
        $this->requireSeeker();
        
        $keyword = $this->get('q');
        
        if (empty($keyword)) {
            $this->json(['success' => false, 'message' => 'No search query provided']);
        }
        
        $jobs = $this->jobModel->searchJobs($keyword);
        
        $user_id = $_SESSION['user_id'];
        
        // Check which jobs are saved
        foreach ($jobs as &$job) {
            $job['is_saved'] = $this->savedJobModel->isSaved($user_id, $job['id']);
        }
        
        $this->json([
            'success' => true,
            'jobs' => $jobs,
            'count' => count($jobs)
        ]);
    }
}
?>
