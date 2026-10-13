<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_jobs_model
 * Manages job listings, candidate applications, and application milestone tracking.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_jobs_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_jobs';
        parent::__construct($this->table);
    }

    /**
     * Get active jobs with company details and candidate application status
     */
    function get_active_jobs($candidate_user_id = null, $discipline = null, $workplace = null, $limit = 50) {
        $builder = $this->db->table('grid_jobs gj')
            ->select('gj.*, gc.logo_url as company_logo')
            ->join('grid_companies gc', 'gc.id = gj.company_id', 'left')
            ->where('gj.status', 'active');

        if (!empty($discipline)) {
            $builder->where('gj.discipline', $discipline);
        }
        if (!empty($workplace)) {
            $builder->where('gj.workplace_type', $workplace);
        }

        $jobs = $builder->orderBy('gj.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        // If candidate provided, fetch their applied map
        if ($candidate_user_id) {
            $appliedRows = $this->db->table('grid_job_applications')
                ->select('job_id, stage')
                ->where('candidate_user_id', (int) $candidate_user_id)
                ->get()
                ->getResultArray();

            $appliedMap = [];
            foreach ($appliedRows as $app) {
                $appliedMap[$app['job_id']] = $app['stage'];
            }

            foreach ($jobs as &$job) {
                $job['has_applied'] = isset($appliedMap[$job['id']]);
                $job['application_stage'] = $appliedMap[$job['id']] ?? null;
                $job['required_skills'] = !empty($job['required_skills_csv']) 
                    ? array_map('trim', explode(',', $job['required_skills_csv'])) 
                    : [];
            }
        }

        return $jobs;
    }

    /**
     * Submit an application for a candidate
     */
    function apply_for_job($job_id, $candidate_user_id, $cover_note = '') {
        $job_id = (int) $job_id;
        $candidate_user_id = (int) $candidate_user_id;

        $exists = $this->db->table('grid_job_applications')
            ->where('job_id', $job_id)
            ->where('candidate_user_id', $candidate_user_id)
            ->countAllResults();

        if ($exists > 0) {
            return false; // Already applied
        }

        $this->db->table('grid_job_applications')->insert([
            'job_id' => $job_id,
            'candidate_user_id' => $candidate_user_id,
            'stage' => 'applied',
            'cover_note' => $cover_note,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->table('grid_jobs')
            ->where('id', $job_id)
            ->increment('applicants_count', 1);

        return true;
    }

    /**
     * Get candidate's tracker applications
     */
    function get_candidate_tracker($candidate_user_id) {
        return $this->db->table('grid_job_applications gja')
            ->select('gja.id as application_id, gja.stage, gja.created_at as applied_at, gja.updated_at,
                     gj.id as job_id, gj.title, gj.company_name, gj.location, gj.workplace_type, gj.salary_min, gj.salary_max, gj.salary_currency,
                     gc.logo_url as company_logo')
            ->join('grid_jobs gj', 'gj.id = gja.job_id', 'inner')
            ->join('grid_companies gc', 'gc.id = gj.company_id', 'left')
            ->where('gja.candidate_user_id', (int) $candidate_user_id)
            ->orderBy('gja.updated_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // =========================================================================
    // RECRUITER MODE & JOB POSTING PIPELINE (ATS)
    // =========================================================================

    /**
     * Normalize a workplace type string (e.g. "remote") to match the grid_jobs
     * ENUM casing ("Remote"), defaulting to Remote for anything unrecognized.
     */
    function normalize_workplace_type($value) {
        $map = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'onsite' => 'Onsite'];
        $key = strtolower(trim((string) $value));
        return $map[$key] ?? 'Remote';
    }

    /**
     * Create a new job posting for a recruiter.
     * Returns the created job_id, or null if a required field is missing.
     */
    function create_job($poster_user_id, array $job_data) {
        $poster_user_id = (int) $poster_user_id;

        $title = trim((string) ($job_data['title'] ?? ''));
        $companyName = trim((string) ($job_data['company_name'] ?? ''));
        $discipline = trim((string) ($job_data['discipline'] ?? ''));
        $workplaceType = trim((string) ($job_data['workplace_type'] ?? ''));
        $location = trim((string) ($job_data['location'] ?? ''));

        if ($title === '' || $companyName === '' || $discipline === '' || $workplaceType === '' || $location === '') {
            return null;
        }

        $skillsCsv = '';
        if (!empty($job_data['skills_required']) && is_array($job_data['skills_required'])) {
            $skills = array_filter(array_map('trim', $job_data['skills_required']));
            $skillsCsv = implode(',', $skills);
        }

        $insertData = [
            'recruiter_user_id' => $poster_user_id,
            'company_name' => substr($companyName, 0, 128),
            'title' => substr($title, 0, 128),
            'discipline' => substr($discipline, 0, 64),
            'workplace_type' => $this->normalize_workplace_type($workplaceType),
            'location' => substr($location, 0, 128),
            'salary_range' => substr(trim((string) ($job_data['salary_range'] ?? '')), 0, 255),
            'description' => (string) ($job_data['description'] ?? ''),
            'required_skills_csv' => substr($skillsCsv, 0, 512),
            'status' => 'active'
        ];

        $this->db->table('grid_jobs')->insert($insertData);

        return (int) $this->db->insertID();
    }

    /**
     * List jobs posted by a recruiter, each annotated with its applicant count.
     */
    function get_recruiter_jobs($poster_user_id) {
        $poster_user_id = (int) $poster_user_id;

        $jobs = $this->db->table('grid_jobs')
            ->select('id, title, company_name, location, status, applicants_count, workplace_type, discipline, salary_range, required_skills_csv, created_at')
            ->where('recruiter_user_id', $poster_user_id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($jobs as &$job) {
            $job['id'] = (int) $job['id'];
            $job['applicant_count'] = (int) $job['applicants_count'];
            $job['workplace_type'] = strtolower($job['workplace_type']);
            $job['required_skills'] = !empty($job['required_skills_csv'])
                ? array_map('trim', explode(',', $job['required_skills_csv']))
                : [];
            unset($job['applicants_count'], $job['required_skills_csv']);
        }

        return $jobs;
    }

    /**
     * List applicants for a job, verifying it belongs to the requesting recruiter.
     * Returns null if the job doesn't exist or isn't owned by $poster_user_id.
     */
    function get_job_applicants($job_id, $poster_user_id) {
        $job_id = (int) $job_id;
        $poster_user_id = (int) $poster_user_id;

        $job = $this->db->table('grid_jobs')
            ->select('id, recruiter_user_id')
            ->where('id', $job_id)
            ->get()
            ->getRowArray();

        if (!$job || (int) $job['recruiter_user_id'] !== $poster_user_id) {
            return null;
        }

        $rows = $this->db->table('grid_job_applications gja')
            ->select('gja.id, gja.stage, gja.cover_note, gja.created_at,
                     u.id as user_id, u.first_name, u.last_name,
                     gp.grid_id, gp.headline, gp.location')
            ->join('users u', 'u.id = gja.candidate_user_id', 'inner')
            ->join('grid_profiles gp', 'gp.user_id = gja.candidate_user_id', 'left')
            ->where('gja.job_id', $job_id)
            ->orderBy('gja.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $applicants = [];
        foreach ($rows as $row) {
            $fullName = trim($row['first_name'] . ' ' . ($row['last_name'] ?? ''));
            $initials = strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'] ?? '', 0, 1));

            $applicants[] = [
                'id' => (int) $row['id'],
                'status' => $row['stage'],
                'cover_note' => $row['cover_note'],
                'created_at' => $row['created_at'],
                'user_id' => (int) $row['user_id'],
                'user' => [
                    'initials' => $initials,
                    'name' => $fullName
                ],
                'profile' => [
                    'grid_id' => $row['grid_id'],
                    'headline' => $row['headline'],
                    'location' => $row['location']
                ]
            ];
        }

        return $applicants;
    }

    /**
     * Move a candidate's application to a new pipeline stage.
     * Returns 'ok', 'invalid_status', 'not_found', or 'forbidden'.
     */
    function update_application_status($application_id, $poster_user_id, $new_status) {
        $application_id = (int) $application_id;
        $poster_user_id = (int) $poster_user_id;
        $allowed = ['applied', 'reviewing', 'interview', 'offered', 'rejected'];

        if (!in_array($new_status, $allowed, true)) {
            return 'invalid_status';
        }

        $row = $this->db->table('grid_job_applications gja')
            ->select('gja.id, gj.recruiter_user_id')
            ->join('grid_jobs gj', 'gj.id = gja.job_id', 'inner')
            ->where('gja.id', $application_id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return 'not_found';
        }

        if ((int) $row['recruiter_user_id'] !== $poster_user_id) {
            return 'forbidden';
        }

        $this->db->table('grid_job_applications')
            ->where('id', $application_id)
            ->update([
                'stage' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return 'ok';
    }
}
