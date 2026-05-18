<?php
// controllers/SavedJobController.php

class SavedJobController extends Controller {
    
    private $savedJobModel;
    
    public function __construct() {
        $this->savedJobModel = $this->model('SavedJob');
    }
    
    // Display saved jobs
    public function index() {
        $this->requireSeeker();
        
        $user_id = $_SESSION['user_id'];
        
        // Get all saved jobs
        $savedJobs = $this->savedJobModel->getSavedJobs($user_id);
        
        $data = [
            'title' => 'Saved Jobs',
            'saved_jobs' => $savedJobs,
            'total' => count($savedJobs)
        ];
        
        $this->view('saved-jobs/index', $data);
    }
    
    // Toggle saved job (AJAX)
    public function toggle() {
        $this->requireSeeker();
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $job_id = isset($input['job_id']) ? $input['job_id'] : '';
        
        if (empty($job_id)) {
            $this->json(['success' => false, 'message' => 'Job ID is required']);
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Toggle saved job
        $action = $this->savedJobModel->toggle($user_id, $job_id);
        
        if ($action === 'added') {
            $this->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Job saved successfully'
            ]);
        } else if ($action === 'removed') {
            $this->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Job removed from saved list'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to toggle saved job'
            ]);
        }
    }
    
    // Remove saved job
    public function remove() {
        $this->requireSeeker();
        
        $job_id = $this->get('id');
        
        if (empty($job_id)) {
            $this->setFlash('error', 'Invalid job ID');
            $this->redirect('index.php?page=saved-jobs');
        }
        
        $user_id = $_SESSION['user_id'];
        
        $result = $this->savedJobModel->remove($user_id, $job_id);
        
        if ($result) {
            $this->setFlash('success', 'Job removed from saved list');
        } else {
            $this->setFlash('error', 'Failed to remove job');
        }
        
        $this->redirect('index.php?page=saved-jobs');
    }
}
?>
