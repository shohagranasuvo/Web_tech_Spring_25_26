<?php
// models/ApplicationModel.php

require_once __DIR__ . '/../config/database.php';

class ApplicationModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Get all jobs belonging to a specific employer (for the dropdown).
     */
    public function getJobsByEmployer(int $employerId): array {
        $stmt = $this->db->prepare("
            SELECT j.id, j.title, j.status, j.deadline,
                   COUNT(a.id) AS application_count
            FROM jobs j
            LEFT JOIN applications a ON a.job_id = j.id
            WHERE j.employer_id = :employer_id
            GROUP BY j.id
            ORDER BY j.created_at DESC
        ");
        $stmt->execute([':employer_id' => $employerId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all applications for a specific job with seeker details.
     */
    public function getApplicationsByJob(int $jobId, int $employerId): array {
        // Verify the job belongs to this employer before returning data
        $stmt = $this->db->prepare("
            SELECT a.id,
                   a.status,
                   a.cover_letter,
                   a.resume_path,
                   a.created_at,
                   u.name   AS seeker_name,
                   u.email  AS seeker_email,
                   sp.headline,
                   sp.skills,
                   sp.years_experience
            FROM applications a
            JOIN users u         ON u.id = a.seeker_id
            LEFT JOIN seeker_profiles sp ON sp.user_id = a.seeker_id
            JOIN jobs j          ON j.id = a.job_id
            WHERE a.job_id = :job_id
              AND j.employer_id = :employer_id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([
            ':job_id'      => $jobId,
            ':employer_id' => $employerId,
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Update an application's status.
     * Returns true on success, false if not found / not owned by employer.
     */
    public function updateStatus(int $applicationId, string $status, int $employerId): bool {
        $allowed = ['Reviewed', 'Shortlisted', 'Rejected'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE applications a
            JOIN jobs j ON j.id = a.job_id
            SET a.status = :status
            WHERE a.id = :app_id
              AND j.employer_id = :employer_id
        ");
        $stmt->execute([
            ':status'      => $status,
            ':app_id'      => $applicationId,
            ':employer_id' => $employerId,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get funnel counts (status breakdown) for a job.
     */
    public function getFunnelByJob(int $jobId, int $employerId): array {
        $stmt = $this->db->prepare("
            SELECT a.status, COUNT(*) AS cnt
            FROM applications a
            JOIN jobs j ON j.id = a.job_id
            WHERE a.job_id = :job_id
              AND j.employer_id = :employer_id
            GROUP BY a.status
        ");
        $stmt->execute([
            ':job_id'      => $jobId,
            ':employer_id' => $employerId,
        ]);
        return $stmt->fetchAll();
    }

    // ── Admin methods ──────────────────────────────────────────────

    /**
     * Admin: all jobs with optional filters.
     */
    public function adminGetAllJobs(?int $categoryId, ?string $status): array {
        $where  = [];
        $params = [];

        if ($categoryId) {
            $where[]                = 'j.category_id = :cat';
            $params[':cat']         = $categoryId;
        }
        if ($status && in_array($status, ['active', 'closed'], true)) {
            $where[]                = 'j.status = :status';
            $params[':status']      = $status;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT j.id, j.title, j.status, j.deadline, j.created_at,
                   c.name   AS category,
                   u.name   AS employer_name,
                   ep.company_name,
                   COUNT(a.id) AS application_count
            FROM jobs j
            LEFT JOIN categories c    ON c.id = j.category_id
            LEFT JOIN users u         ON u.id = j.employer_id
            LEFT JOIN employer_profiles ep ON ep.user_id = j.employer_id
            LEFT JOIN applications a  ON a.job_id = j.id
            $whereClause
            GROUP BY j.id
            ORDER BY j.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Admin: soft-delete (close) a job.
     */
    public function adminCloseJob(int $jobId): bool {
        $stmt = $this->db->prepare("UPDATE jobs SET status = 'closed' WHERE id = :id");
        $stmt->execute([':id' => $jobId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Admin: summary stats.
     */
    public function adminSummary(): array {
        $totalJobs = $this->db->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
        $totalApps = $this->db->query("SELECT COUNT(*) FROM applications")->fetchColumn();

        $stmt = $this->db->query("
            SELECT c.name AS category, COUNT(a.id) AS total_applications
            FROM categories c
            LEFT JOIN jobs j ON j.category_id = c.id
            LEFT JOIN applications a ON a.job_id = j.id
            GROUP BY c.id
            ORDER BY total_applications DESC
        ");
        $byCategory = $stmt->fetchAll();

        return [
            'total_jobs'         => (int) $totalJobs,
            'total_applications' => (int) $totalApps,
            'by_category'        => $byCategory,
        ];
    }

    /**
     * Get all categories (for admin filter dropdown).
     */
    public function getAllCategories(): array {
        return $this->db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
    }
}
