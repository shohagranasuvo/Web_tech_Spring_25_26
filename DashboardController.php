<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/ApplicationModel.php';

class DashboardController {

    private ApplicationModel $model;

    public function __construct() {
        $this->model = new ApplicationModel();
    }

    /**
     * Employer: main application tracking page.
     * GET /dashboard/employer-applications.php
     */
    public function employerDashboard(): void {
        requireEmployer();

        $employerId  = currentUserId();
        $jobs        = $this->model->getJobsByEmployer($employerId);

        // Which job is selected from the dropdown?
        $selectedJob = isset($_GET['job_id']) ? (int) $_GET['job_id'] : 0;
        $applications = [];
        $funnelData   = [];

        if ($selectedJob > 0) {
            $applications = $this->model->getApplicationsByJob($selectedJob, $employerId);
            $rawFunnel    = $this->model->getFunnelByJob($selectedJob, $employerId);

            // Build funnel keyed by status so Chart.js can consume it easily
            $allStatuses = ['Submitted', 'Reviewed', 'Shortlisted', 'Rejected'];
            $funnelMap   = array_column($rawFunnel, 'cnt', 'status');
            foreach ($allStatuses as $s) {
                $funnelData[] = ['status' => $s, 'count' => (int) ($funnelMap[$s] ?? 0)];
            }
        }

        // Pass to view
        require __DIR__ . '/../views/dashboard/employer_applications.php';
    }

    /**
     * Admin: all jobs panel.
     * GET /dashboard/admin-panel.php
     */
    public function adminPanel(): void {
        requireAdmin();

        $model      = $this->model;
        $categories = $model->getAllCategories();
        $catFilter  = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $statusFilter = $_GET['status'] ?? null;

        $jobs    = $model->adminGetAllJobs($catFilter, $statusFilter);
        $summary = $model->adminSummary();

        require __DIR__ . '/../views/dashboard/admin_panel.php';
    }
}
