<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

/**
 * WorkHiveModel
 * Single-file model for WorkHive attendance + employee + work tracking.
 * PHP 7.4.33 compatible.
 *
 * Supports:
 *  - Project and Schedule containers in work_projects.work_type
 *  - Task assignment and task progress
 *  - Daily task-by-task updates linked to attendance login
 *  - Employee / TeamLead / Manager / HR / Admin hierarchy
 *  - Daily, weekly, monthly and comparison reports
 */
class WorkHiveModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'attend_attendance';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = array(
        'user_id', 'attendance_date', 'check_in_time', 'check_out_time',
        'status', 'latitude', 'longitude', 'location_name',
        'ip_address', 'device_info', 'remarks',
        'is_manual_entry', 'edited_by', 'edited_at',
        'created_at', 'updated_at',
    );

    private $istSet = false;
    private $schemaReady = false;

    private function setIst(): void
    {
        if (!$this->istSet) {
            date_default_timezone_set('Asia/Kolkata');
            try {
                $this->db->query("SET time_zone = '+05:30'");
            } catch (Throwable $e) {
                // Keep PHP timezone even if MySQL timezone cannot be set.
            }
            $this->istSet = true;
        }
    }

    private function now(): string
    {
        $this->setIst();
        return date('Y-m-d H:i:s');
    }

    private function today(): string
    {
        $this->setIst();
        return date('Y-m-d');
    }

    private function cleanText($value, int $max = 255): string
    {
        $text = trim(strip_tags((string) $value));
        return strlen($text) > $max ? substr($text, 0, $max) : $text;
    }

    private function cleanLong($value): ?string
    {
        $text = trim(strip_tags((string) $value));
        return $text !== '' ? $text : null;
    }

    private function safeDate($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        $ts = strtotime($value);
        return $ts !== false ? date('Y-m-d', $ts) : null;
    }

    private function safeTime($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value . ':00';
        }
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return $value;
        }
        return null;
    }

    private function positiveOrNull($value): ?int
    {
        $id = (int) $value;
        return $id > 0 ? $id : null;
    }

    private function clampInt($value, int $min, int $max): int
    {
        $n = (int) $value;
        if ($n < $min) {
            return $min;
        }
        if ($n > $max) {
            return $max;
        }
        return $n;
    }

    private function clampFloat($value, float $min, float $max): float
    {
        $n = (float) $value;
        if ($n < $min) {
            return $min;
        }
        if ($n > $max) {
            return $max;
        }
        return $n;
    }

    private function tableExists(string $table): bool
    {
        try {
            $row = $this->db->query(
                "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1",
                array($table)
            )->getRowArray();
            return !empty($row);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            $row = $this->db->query(
                "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1",
                array($table, $column)
            )->getRowArray();
            return !empty($row);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function ensureColumn(string $table, string $column, string $definition): void
    {
        try {
            if (!$this->columnExists($table, $column)) {
                $this->db->query("ALTER TABLE `" . $table . "` ADD COLUMN " . $definition);
            }
        } catch (Throwable $e) {
            // Idempotent schema helper. Ignore duplicate/unsupported alterations.
        }
    }

    private function ensureIndex(string $table, string $indexName, string $definition): void
    {
        try {
            $row = $this->db->query(
                "SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1",
                array($table, $indexName)
            )->getRowArray();
            if (empty($row)) {
                $this->db->query("ALTER TABLE `" . $table . "` ADD " . $definition);
            }
        } catch (Throwable $e) {
            // Ignore duplicate index failures.
        }
    }

    public function ensureSchema(): void
    {
        if ($this->schemaReady) {
            return;
        }

        $this->setIst();

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_projects` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `work_type` VARCHAR(20) NOT NULL DEFAULT 'project',
            `project_code` VARCHAR(80) NOT NULL,
            `project_name` VARCHAR(220) NOT NULL,
            `client_name` VARCHAR(220) NULL,
            `project_type` VARCHAR(120) NULL,
            `category` VARCHAR(120) NULL,
            `department_id` INT UNSIGNED NULL,
            `visibility` VARCHAR(30) NOT NULL DEFAULT 'assigned',
            `priority` VARCHAR(30) NOT NULL DEFAULT 'medium',
            `status` VARCHAR(40) NOT NULL DEFAULT 'planned',
            `progress` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `start_date` DATE NULL,
            `due_date` DATE NULL,
            `schedule_frequency` VARCHAR(30) NOT NULL DEFAULT 'one_time',
            `schedule_start_date` DATE NULL,
            `schedule_end_date` DATE NULL,
            `repeat_days` VARCHAR(120) NULL,
            `owner_name` VARCHAR(220) NULL,
            `current_focus` TEXT NULL,
            `next_step` TEXT NULL,
            `blockers` TEXT NULL,
            `description` TEXT NULL,
            `repo_url` VARCHAR(500) NULL,
            `demo_url` VARCHAR(500) NULL,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_by_user_id` INT UNSIGNED NULL,
            `updated_by_user_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wp_type` (`work_type`),
            KEY `idx_wp_status` (`status`),
            KEY `idx_wp_priority` (`priority`),
            KEY `idx_wp_dept` (`department_id`),
            KEY `idx_wp_active` (`is_active`),
            KEY `idx_wp_created` (`created_by_user_id`),
            KEY `idx_wp_due` (`due_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->ensureColumn('work_projects', 'work_type', "`work_type` VARCHAR(20) NOT NULL DEFAULT 'project' AFTER `id`");
        $this->ensureColumn('work_projects', 'category', "`category` VARCHAR(120) NULL AFTER `project_type`");
        $this->ensureColumn('work_projects', 'schedule_frequency', "`schedule_frequency` VARCHAR(30) NOT NULL DEFAULT 'one_time' AFTER `due_date`");
        $this->ensureColumn('work_projects', 'schedule_start_date', "`schedule_start_date` DATE NULL AFTER `schedule_frequency`");
        $this->ensureColumn('work_projects', 'schedule_end_date', "`schedule_end_date` DATE NULL AFTER `schedule_start_date`");
        $this->ensureColumn('work_projects', 'repeat_days', "`repeat_days` VARCHAR(120) NULL AFTER `schedule_end_date`");
        $this->ensureColumn('work_projects', 'created_by_user_id', "`created_by_user_id` INT UNSIGNED NULL AFTER `is_active`");
        $this->ensureColumn('work_projects', 'updated_by_user_id', "`updated_by_user_id` INT UNSIGNED NULL AFTER `created_by_user_id`");
        $this->ensureIndex('work_projects', 'idx_wp_type', 'KEY `idx_wp_type` (`work_type`)');
        $this->ensureIndex('work_projects', 'idx_wp_created', 'KEY `idx_wp_created` (`created_by_user_id`)');

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_members` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `attend_user_id` INT UNSIGNED NULL,
            `employee_id` VARCHAR(80) NULL,
            `member_name` VARCHAR(220) NOT NULL,
            `email` VARCHAR(220) NULL,
            `phone` VARCHAR(50) NULL,
            `role_title` VARCHAR(120) NULL,
            `department` VARCHAR(120) NULL,
            `availability_status` VARCHAR(40) NOT NULL DEFAULT 'available',
            `daily_capacity_hours` DECIMAL(6,2) NOT NULL DEFAULT 8.00,
            `notes` TEXT NULL,
            `source_type` VARCHAR(60) NOT NULL DEFAULT 'manual',
            `last_synced_at` DATETIME NULL,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wm_attend_user` (`attend_user_id`),
            KEY `idx_wm_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->ensureColumn('work_members', 'last_synced_at', '`last_synced_at` DATETIME NULL AFTER `source_type`');
        $this->ensureIndex('work_members', 'idx_wm_attend_user', 'KEY `idx_wm_attend_user` (`attend_user_id`)');

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_project_assignments` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `project_id` INT UNSIGNED NOT NULL,
            `member_id` INT UNSIGNED NOT NULL,
            `attend_user_id` INT UNSIGNED NULL,
            `role_on_project` VARCHAR(120) NULL,
            `responsibility` VARCHAR(255) NULL,
            `allocation_percent` TINYINT UNSIGNED NOT NULL DEFAULT 100,
            `assigned_from` DATE NULL,
            `assigned_until` DATE NULL,
            `assignment_status` VARCHAR(40) NOT NULL DEFAULT 'active',
            `assigned_by_user_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ux_wpa_project_member` (`project_id`, `member_id`),
            KEY `idx_wpa_project` (`project_id`),
            KEY `idx_wpa_member` (`member_id`),
            KEY `idx_wpa_attend_user` (`attend_user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->ensureColumn('work_project_assignments', 'attend_user_id', '`attend_user_id` INT UNSIGNED NULL AFTER `member_id`');
        $this->ensureIndex('work_project_assignments', 'ux_wpa_project_member', 'UNIQUE KEY `ux_wpa_project_member` (`project_id`, `member_id`)');
        $this->ensureIndex('work_project_assignments', 'idx_wpa_attend_user', 'KEY `idx_wpa_attend_user` (`attend_user_id`)');

        if ($this->tableExists('work_project_members')) {
            try {
                $this->db->query("INSERT IGNORE INTO `work_project_assignments`
                    (`project_id`, `member_id`, `attend_user_id`, `role_on_project`, `allocation_percent`, `assigned_from`, `assignment_status`, `assigned_by_user_id`, `created_at`, `updated_at`)
                    SELECT wpm.project_id, wpm.member_id, wm.attend_user_id, 'Contributor', 100, CURDATE(), 'active', NULL,
                           COALESCE(wpm.added_at, NOW()), COALESCE(wpm.added_at, NOW())
                    FROM `work_project_members` wpm
                    LEFT JOIN `work_members` wm ON wm.id = wpm.member_id");
            } catch (Throwable $e) {
                // Old compatibility table migration is best-effort.
            }
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_tasks` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `project_id` INT UNSIGNED NOT NULL,
            `parent_task_id` INT UNSIGNED NULL,
            `task_code` VARCHAR(80) NULL,
            `task_title` VARCHAR(260) NOT NULL,
            `task_description` TEXT NULL,
            `task_type` VARCHAR(40) NOT NULL DEFAULT 'project_task',
            `priority` VARCHAR(30) NOT NULL DEFAULT 'medium',
            `status` VARCHAR(40) NOT NULL DEFAULT 'todo',
            `progress` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `assigned_to_member_id` INT UNSIGNED NULL,
            `assigned_to_user_id` INT UNSIGNED NULL,
            `start_date` DATE NULL,
            `due_date` DATE NULL,
            `planned_start_time` TIME NULL,
            `planned_end_time` TIME NULL,
            `planned_hours` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `actual_hours` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `checklist_json` TEXT NULL,
            `checklist_total` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `checklist_done` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_by_user_id` INT UNSIGNED NULL,
            `updated_by_user_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wt_project` (`project_id`),
            KEY `idx_wt_assigned_user` (`assigned_to_user_id`),
            KEY `idx_wt_status` (`status`),
            KEY `idx_wt_due` (`due_date`),
            KEY `idx_wt_created` (`created_by_user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_daily_updates` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `project_id` INT UNSIGNED NOT NULL,
            `task_id` INT UNSIGNED NULL,
            `member_id` INT UNSIGNED NULL,
            `attend_user_id` INT UNSIGNED NULL,
            `attendance_id` BIGINT UNSIGNED NULL,
            `update_date` DATE NOT NULL,
            `update_title` VARCHAR(300) NOT NULL,
            `work_status` VARCHAR(50) NOT NULL DEFAULT 'working',
            `is_today_focus` TINYINT(1) NOT NULL DEFAULT 1,
            `completion_percent` TINYINT UNSIGNED NULL,
            `hours_spent` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `planned_hours` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `today_task` TEXT NULL,
            `progress_note` TEXT NULL,
            `achievements` TEXT NULL,
            `blockers` TEXT NULL,
            `tomorrow_plan` TEXT NULL,
            `steps` TEXT NULL,
            `steps_total` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `steps_done` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `sync_project_progress` TINYINT(1) NOT NULL DEFAULT 1,
            `review_status` VARCHAR(30) NOT NULL DEFAULT 'submitted',
            `review_note` TEXT NULL,
            `reviewed_by_user_id` INT UNSIGNED NULL,
            `reviewed_at` DATETIME NULL,
            `created_by_user_id` INT UNSIGNED NULL,
            `updated_by_user_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wdu_project` (`project_id`),
            KEY `idx_wdu_task` (`task_id`),
            KEY `idx_wdu_user_date` (`attend_user_id`, `update_date`),
            KEY `idx_wdu_date` (`update_date`),
            KEY `idx_wdu_status` (`work_status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->ensureColumn('work_daily_updates', 'task_id', '`task_id` INT UNSIGNED NULL AFTER `project_id`');
        $this->ensureColumn('work_daily_updates', 'steps', '`steps` TEXT NULL AFTER `tomorrow_plan`');
        $this->ensureColumn('work_daily_updates', 'steps_total', '`steps_total` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `steps`');
        $this->ensureColumn('work_daily_updates', 'steps_done', '`steps_done` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `steps_total`');
        $this->ensureColumn('work_daily_updates', 'created_by_user_id', '`created_by_user_id` INT UNSIGNED NULL AFTER `reviewed_at`');
        $this->ensureColumn('work_daily_updates', 'updated_by_user_id', '`updated_by_user_id` INT UNSIGNED NULL AFTER `created_by_user_id`');
        $this->ensureIndex('work_daily_updates', 'idx_wdu_task', 'KEY `idx_wdu_task` (`task_id`)');
        $this->ensureIndex('work_daily_updates', 'idx_wdu_user_date', 'KEY `idx_wdu_user_date` (`attend_user_id`, `update_date`)');

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_task_updates` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `task_id` INT UNSIGNED NOT NULL,
            `project_id` INT UNSIGNED NOT NULL,
            `daily_update_id` INT UNSIGNED NULL,
            `member_id` INT UNSIGNED NULL,
            `attend_user_id` INT UNSIGNED NULL,
            `update_date` DATE NOT NULL,
            `old_status` VARCHAR(40) NULL,
            `new_status` VARCHAR(40) NULL,
            `old_progress` TINYINT UNSIGNED NULL,
            `new_progress` TINYINT UNSIGNED NULL,
            `hours_spent` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `note` TEXT NULL,
            `created_by_user_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wtu_task` (`task_id`),
            KEY `idx_wtu_date` (`update_date`),
            KEY `idx_wtu_user` (`attend_user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_update_steps` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `daily_update_id` INT UNSIGNED NOT NULL,
            `project_id` INT UNSIGNED NOT NULL,
            `task_id` INT UNSIGNED NULL,
            `member_id` INT UNSIGNED NULL,
            `attend_user_id` INT UNSIGNED NULL,
            `step_date` DATE NOT NULL,
            `step_no` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            `step_title` VARCHAR(220) NOT NULL,
            `step_description` TEXT NULL,
            `step_status` VARCHAR(40) NOT NULL DEFAULT 'pending',
            `planned_start_time` TIME NULL,
            `planned_end_time` TIME NULL,
            `actual_start_time` TIME NULL,
            `actual_end_time` TIME NULL,
            `planned_hours` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `actual_hours` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
            `completion_percent` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `blocker_note` TEXT NULL,
            `created_by_user_id` INT UNSIGNED NULL,
            `updated_by_user_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wus_update` (`daily_update_id`),
            KEY `idx_wus_task` (`task_id`),
            KEY `idx_wus_user_date` (`attend_user_id`, `step_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->ensureColumn('work_update_steps', 'task_id', '`task_id` INT UNSIGNED NULL AFTER `project_id`');
        $this->ensureIndex('work_update_steps', 'idx_wus_task', 'KEY `idx_wus_task` (`task_id`)');

        $this->db->query("CREATE TABLE IF NOT EXISTS `work_review_logs` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `daily_update_id` INT UNSIGNED NOT NULL,
            `old_status` VARCHAR(30) NULL,
            `new_status` VARCHAR(30) NOT NULL,
            `review_note` TEXT NULL,
            `reviewed_by_user_id` INT UNSIGNED NULL,
            `reviewed_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_wrl_update` (`daily_update_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Add Admin role safely for super-power access.
        try {
            $this->db->query("INSERT INTO `attend_roles` (`name`, `label`, `can_edit`, `can_view_analytics`)
                SELECT 'admin', 'Admin', 1, 1
                WHERE NOT EXISTS (SELECT 1 FROM `attend_roles` WHERE `name` = 'admin' LIMIT 1)");
        } catch (Throwable $e) {
            // Existing role table may not be available in some installations.
        }

        $this->createWorkViews();
        $this->schemaReady = true;
    }

    private function createWorkViews(): void
    {
        try {
            $this->db->query("CREATE OR REPLACE VIEW `attend_work_daily_v` AS
                SELECT
                    wdu.attend_user_id AS user_id,
                    wdu.update_date AS work_date,
                    COUNT(wdu.id) AS work_update_count,
                    COUNT(DISTINCT wdu.task_id) AS task_update_count,
                    COALESCE(SUM(wdu.hours_spent), 0) AS work_logged_hours,
                    COALESCE(SUM(wdu.planned_hours), 0) AS work_planned_hours,
                    COALESCE(SUM(wdu.steps_total), 0) AS total_steps,
                    COALESCE(SUM(wdu.steps_done), 0) AS completed_steps,
                    COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS avg_progress,
                    GROUP_CONCAT(DISTINCT CONCAT(COALESCE(wp.project_code, ''), CASE WHEN COALESCE(wp.project_code, '') = '' THEN '' ELSE ' - ' END, wp.project_name) ORDER BY wp.project_name SEPARATOR ', ') AS project_names,
                    GROUP_CONCAT(DISTINCT wt.task_title ORDER BY wt.task_title SEPARATOR ', ') AS task_titles,
                    u.department_id,
                    u.full_name,
                    u.employee_id
                FROM `work_daily_updates` wdu
                LEFT JOIN `work_projects` wp ON wp.id = wdu.project_id
                LEFT JOIN `work_tasks` wt ON wt.id = wdu.task_id
                LEFT JOIN `attend_users` u ON u.id = wdu.attend_user_id
                WHERE wdu.attend_user_id IS NOT NULL
                GROUP BY wdu.attend_user_id, wdu.update_date, u.department_id, u.full_name, u.employee_id");

            $this->db->query("CREATE OR REPLACE VIEW `attend_work_weekly_v` AS
                SELECT
                    wdu.attend_user_id AS user_id,
                    DATE_SUB(wdu.update_date, INTERVAL (DAYOFWEEK(wdu.update_date) - 2 + 7) % 7 DAY) AS week_start,
                    DATE_ADD(DATE_SUB(wdu.update_date, INTERVAL (DAYOFWEEK(wdu.update_date) - 2 + 7) % 7 DAY), INTERVAL 6 DAY) AS week_end,
                    COUNT(wdu.id) AS work_update_count,
                    COUNT(DISTINCT wdu.task_id) AS task_update_count,
                    COALESCE(SUM(wdu.hours_spent), 0) AS work_logged_hours,
                    COALESCE(SUM(wdu.planned_hours), 0) AS work_planned_hours,
                    COALESCE(SUM(wdu.steps_total), 0) AS total_steps,
                    COALESCE(SUM(wdu.steps_done), 0) AS completed_steps,
                    COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS avg_completion_percent,
                    u.department_id,
                    u.full_name,
                    u.employee_id
                FROM `work_daily_updates` wdu
                LEFT JOIN `attend_users` u ON u.id = wdu.attend_user_id
                WHERE wdu.attend_user_id IS NOT NULL
                GROUP BY wdu.attend_user_id,
                         DATE_SUB(wdu.update_date, INTERVAL (DAYOFWEEK(wdu.update_date) - 2 + 7) % 7 DAY),
                         u.department_id, u.full_name, u.employee_id");

            $this->db->query("CREATE OR REPLACE VIEW `attend_work_monthly_v` AS
                SELECT
                    wdu.attend_user_id AS user_id,
                    YEAR(wdu.update_date) AS work_year,
                    MONTH(wdu.update_date) AS work_month,
                    COUNT(wdu.id) AS total_updates,
                    COUNT(DISTINCT wdu.task_id) AS task_update_count,
                    COALESCE(SUM(wdu.hours_spent), 0) AS total_work_logged_hours,
                    COALESCE(SUM(wdu.planned_hours), 0) AS total_planned_hours,
                    COALESCE(SUM(wdu.steps_total), 0) AS total_steps,
                    COALESCE(SUM(wdu.steps_done), 0) AS completed_steps,
                    COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS avg_completion_percent,
                    COALESCE(att_hours.total_attendance_hours, 0) AS total_attendance_hours,
                    u.department_id,
                    u.full_name,
                    u.employee_id
                FROM `work_daily_updates` wdu
                LEFT JOIN `attend_users` u ON u.id = wdu.attend_user_id
                LEFT JOIN (
                    SELECT
                        a.user_id,
                        YEAR(a.attendance_date) AS yr,
                        MONTH(a.attendance_date) AS mo,
                        ROUND(SUM(CASE WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60 ELSE 0 END), 2) AS total_attendance_hours
                    FROM `attend_attendance` a
                    GROUP BY a.user_id, YEAR(a.attendance_date), MONTH(a.attendance_date)
                ) att_hours ON att_hours.user_id = wdu.attend_user_id AND att_hours.yr = YEAR(wdu.update_date) AND att_hours.mo = MONTH(wdu.update_date)
                WHERE wdu.attend_user_id IS NOT NULL
                GROUP BY wdu.attend_user_id, YEAR(wdu.update_date), MONTH(wdu.update_date), att_hours.total_attendance_hours,
                         u.department_id, u.full_name, u.employee_id");
        } catch (Throwable $e) {
            // Views are report helpers. API CRUD should continue even if view creation fails.
        }
    }

    public function makeContext(array $data): array
    {
        $role = strtolower(trim(str_replace(array(' ', '-'), '_', (string) ($data['role'] ?? 'employee'))));
        if ($role === 'team_lead') {
            $role = 'teamlead';
        }
        return array(
            'user_id' => (int) ($data['user_id'] ?? 0),
            'role' => $role !== '' ? $role : 'employee',
            'department_id' => (int) ($data['department_id'] ?? 0),
            'can_edit' => !empty($data['can_edit']),
            'can_analytics' => !empty($data['can_analytics']),
        );
    }

    public function canManageWork(array $ctx): bool
    {
        $role = (string) ($ctx['role'] ?? 'employee');
        if (in_array($role, array('admin', 'manager', 'hr', 'teamlead'), true)) {
            return true;
        }
        return !empty($ctx['can_edit']);
    }

    public function isSuperAdmin(array $ctx): bool
    {
        return (string) ($ctx['role'] ?? '') === 'admin';
    }

    private function normalizeWorkType($type): string
    {
        $type = strtolower(trim((string) $type));
        return $type === 'schedule' ? 'schedule' : 'project';
    }

    private function normalizeStatus($status, string $fallback, array $allowed): string
    {
        $status = strtolower(trim((string) $status));
        return in_array($status, $allowed, true) ? $status : $fallback;
    }

    public function ensureWorkMemberForAttendUser(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }
        $this->ensureSchema();

        try {
            $user = $this->db->query(
                "SELECT u.id, u.full_name, u.employee_id, u.email, r.name AS role_name, d.name AS department_name
                 FROM attend_users u
                 INNER JOIN attend_roles r ON r.id = u.role_id
                 LEFT JOIN attend_departments d ON d.id = u.department_id
                 WHERE u.id = ? AND u.is_active = 1 LIMIT 1",
                array($userId)
            )->getRowArray();

            if (!$user) {
                return;
            }

            $now = $this->now();
            $existing = $this->db->query('SELECT id FROM work_members WHERE attend_user_id = ? LIMIT 1', array($userId))->getRowArray();
            $payload = array(
                'employee_id' => (string) ($user['employee_id'] ?? ''),
                'member_name' => (string) ($user['full_name'] ?? 'Unknown'),
                'email' => (string) ($user['email'] ?? ''),
                'role_title' => (string) ($user['role_name'] ?? ''),
                'department' => (string) ($user['department_name'] ?? ''),
                'availability_status' => 'available',
                'daily_capacity_hours' => 8.00,
                'source_type' => 'attendance_sync',
                'last_synced_at' => $now,
                'is_active' => 1,
                'updated_at' => $now,
            );

            if ($existing) {
                $this->db->table('work_members')->where('id', (int) $existing['id'])->update($payload);
            } else {
                $payload['attend_user_id'] = $userId;
                $payload['notes'] = 'Auto-created from attendance user for integrated project/work tracking.';
                $payload['created_at'] = $now;
                $this->db->table('work_members')->insert($payload);
            }
        } catch (Throwable $e) {
            // Member sync must not block login.
        }
    }

    private function getCurrentMemberId(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $this->ensureWorkMemberForAttendUser($userId);
        $row = $this->db->query('SELECT id FROM work_members WHERE attend_user_id = ? AND is_active = 1 LIMIT 1', array($userId))->getRowArray();
        return $row ? (int) $row['id'] : 0;
    }

    private function getMemberById(int $memberId): ?array
    {
        if ($memberId <= 0) {
            return null;
        }
        $row = $this->db->query('SELECT * FROM work_members WHERE id = ? LIMIT 1', array($memberId))->getRowArray();
        return $row ?: null;
    }

    public function getProjectTypes(): array
    {
        return array(
            array('value' => 'development', 'label' => 'Development'),
            array('value' => 'design', 'label' => 'Design'),
            array('value' => 'marketing', 'label' => 'Marketing'),
            array('value' => 'hr', 'label' => 'HR'),
            array('value' => 'operations', 'label' => 'Operations'),
            array('value' => 'sales', 'label' => 'Sales'),
            array('value' => 'support', 'label' => 'Support'),
            array('value' => 'finance', 'label' => 'Finance'),
            array('value' => 'research', 'label' => 'Research'),
            array('value' => 'other', 'label' => 'Other'),
        );
    }

    public function getMembers(bool $activeOnly = true, array $ctx = array()): array
    {
        $this->ensureSchema();
        $sql = "SELECT wm.*, au.full_name AS attend_full_name, au.role_id, r.name AS attend_role
                FROM work_members wm
                LEFT JOIN attend_users au ON au.id = wm.attend_user_id
                LEFT JOIN attend_roles r ON r.id = au.role_id";
        if ($activeOnly) {
            $sql .= " WHERE wm.is_active = 1";
        }
        $sql .= " ORDER BY wm.member_name ASC";
        return $this->db->query($sql)->getResultArray();
    }

    public function saveMember(array $data): int
    {
        $this->ensureSchema();
        $id = (int) ($data['id'] ?? 0);
        $name = $this->cleanText($data['member_name'] ?? '', 220);
        if ($name === '') {
            return 0;
        }
        $now = $this->now();
        $payload = array(
            'attend_user_id' => $this->positiveOrNull($data['attend_user_id'] ?? 0),
            'employee_id' => $this->cleanText($data['employee_id'] ?? '', 80) ?: null,
            'member_name' => $name,
            'email' => filter_var(trim((string) ($data['email'] ?? '')), FILTER_VALIDATE_EMAIL) ?: null,
            'phone' => $this->cleanText($data['phone'] ?? '', 50) ?: null,
            'role_title' => $this->cleanText($data['role_title'] ?? '', 120) ?: null,
            'department' => $this->cleanText($data['department'] ?? '', 120) ?: null,
            'availability_status' => $this->normalizeStatus($data['availability_status'] ?? '', 'available', array('available', 'busy', 'on_leave', 'offline')),
            'daily_capacity_hours' => $this->clampFloat($data['daily_capacity_hours'] ?? 8, 0.5, 24.0),
            'notes' => $this->cleanLong($data['notes'] ?? ''),
            'source_type' => $this->cleanText($data['source_type'] ?? 'manual', 60),
            'is_active' => 1,
            'updated_at' => $now,
        );
        if ($id > 0) {
            $this->db->table('work_members')->where('id', $id)->update($payload);
            return $id;
        }
        $payload['created_at'] = $now;
        $this->db->table('work_members')->insert($payload);
        return (int) $this->db->insertID();
    }

    public function deleteMember(int $id): void
    {
        if ($id <= 0) {
            return;
        }
        $this->ensureSchema();
        $this->db->table('work_members')->where('id', $id)->update(array('is_active' => 0, 'updated_at' => $this->now()));
    }

    private function projectScopeSql(array $ctx, string $alias = 'p'): array
    {
        if ($this->canManageWork($ctx)) {
            return array('1=1', array());
        }
        $userId = (int) ($ctx['user_id'] ?? 0);
        $deptId = (int) ($ctx['department_id'] ?? 0);
        $sql = "(
            {$alias}.created_by_user_id = ?
            OR {$alias}.visibility = 'all'
            OR ({$alias}.visibility = 'department' AND {$alias}.department_id = ?)
            OR EXISTS (
                SELECT 1 FROM work_project_assignments sx
                LEFT JOIN work_members sm ON sm.id = sx.member_id
                WHERE sx.project_id = {$alias}.id
                  AND sx.assignment_status = 'active'
                  AND (sx.attend_user_id = ? OR sm.attend_user_id = ?)
            )
            OR EXISTS (
                SELECT 1 FROM work_tasks st
                WHERE st.project_id = {$alias}.id
                  AND st.is_active = 1
                  AND st.assigned_to_user_id = ?
            )
        )";
        return array($sql, array($userId, $deptId, $userId, $userId, $userId));
    }

    private function canEditProject(int $projectId, array $ctx): bool
    {
        if ($projectId <= 0) {
            return false;
        }
        if ($this->canManageWork($ctx)) {
            return true;
        }
        $row = $this->db->query('SELECT created_by_user_id FROM work_projects WHERE id = ? AND is_active = 1 LIMIT 1', array($projectId))->getRowArray();
        return $row && (int) ($row['created_by_user_id'] ?? 0) === (int) ($ctx['user_id'] ?? 0);
    }

    private function canViewProject(int $projectId, array $ctx): bool
    {
        if ($projectId <= 0) {
            return false;
        }
        if ($this->canManageWork($ctx)) {
            return true;
        }
        list($scopeSql, $scopeParams) = $this->projectScopeSql($ctx, 'p');
        $sql = "SELECT p.id FROM work_projects p WHERE p.id = ? AND p.is_active = 1 AND " . $scopeSql . " LIMIT 1";
        $params = array_merge(array($projectId), $scopeParams);
        $row = $this->db->query($sql, $params)->getRowArray();
        return !empty($row);
    }

    public function getProjects(array $filters, array $ctx): array
    {
        $this->ensureSchema();
        list($scopeSql, $scopeParams) = $this->projectScopeSql($ctx, 'p');

        $sql = "SELECT
                    p.*,
                    d.name AS department_name,
                    cu.full_name AS created_by_name,
                    (SELECT COUNT(*) FROM work_project_assignments wpa WHERE wpa.project_id = p.id AND wpa.assignment_status = 'active') AS member_count,
                    (SELECT COUNT(*) FROM work_tasks wt WHERE wt.project_id = p.id AND wt.is_active = 1) AS task_count,
                    (SELECT COUNT(*) FROM work_tasks wt WHERE wt.project_id = p.id AND wt.is_active = 1 AND wt.status NOT IN ('completed','cancelled')) AS open_task_count,
                    (SELECT COUNT(*) FROM work_daily_updates wdu WHERE wdu.project_id = p.id AND wdu.update_date = CURDATE()) AS updates_today,
                    (SELECT COALESCE(SUM(wdu.hours_spent), 0) FROM work_daily_updates wdu WHERE wdu.project_id = p.id) AS total_hours
                FROM work_projects p
                LEFT JOIN attend_departments d ON d.id = p.department_id
                LEFT JOIN attend_users cu ON cu.id = p.created_by_user_id
                WHERE p.is_active = 1 AND " . $scopeSql;
        $params = $scopeParams;

        if (!empty($filters['work_type'])) {
            $sql .= ' AND p.work_type = ?';
            $params[] = $this->normalizeWorkType($filters['work_type']);
        }
        if (!empty($filters['project_type'])) {
            $sql .= ' AND p.project_type = ?';
            $params[] = $this->cleanText($filters['project_type'], 120);
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND p.status = ?';
            $params[] = $this->cleanText($filters['status'], 40);
        }
        if (!empty($filters['priority'])) {
            $sql .= ' AND p.priority = ?';
            $params[] = $this->cleanText($filters['priority'], 30);
        }
        if (!empty($filters['member_id'])) {
            $sql .= ' AND EXISTS (SELECT 1 FROM work_project_assignments fpa WHERE fpa.project_id = p.id AND fpa.member_id = ? AND fpa.assignment_status = \'active\')';
            $params[] = (int) $filters['member_id'];
        }
        if (!empty($filters['today'])) {
            $sql .= ' AND EXISTS (SELECT 1 FROM work_daily_updates wdu WHERE wdu.project_id = p.id AND wdu.update_date = CURDATE())';
        }
        if (!empty($filters['search'])) {
            $term = '%' . trim((string) $filters['search']) . '%';
            $sql .= ' AND (p.project_name LIKE ? OR p.project_code LIKE ? OR p.client_name LIKE ? OR p.description LIKE ?)';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY FIELD(p.priority, 'critical', 'high', 'medium', 'low'), p.due_date IS NULL, p.due_date ASC, p.created_at DESC";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function getProject(int $id, array $ctx): ?array
    {
        $this->ensureSchema();
        if (!$this->canViewProject($id, $ctx)) {
            return null;
        }
        $project = $this->db->query(
            "SELECT p.*, d.name AS department_name, cu.full_name AS created_by_name
             FROM work_projects p
             LEFT JOIN attend_departments d ON d.id = p.department_id
             LEFT JOIN attend_users cu ON cu.id = p.created_by_user_id
             WHERE p.id = ? AND p.is_active = 1 LIMIT 1",
            array($id)
        )->getRowArray();
        if (!$project) {
            return null;
        }
        $project['members'] = $this->db->query(
            "SELECT wpa.*, wm.member_name, wm.employee_id, wm.email, wm.role_title, wm.department, wm.attend_user_id
             FROM work_project_assignments wpa
             INNER JOIN work_members wm ON wm.id = wpa.member_id
             WHERE wpa.project_id = ? AND wpa.assignment_status = 'active'
             ORDER BY wm.member_name ASC",
            array($id)
        )->getResultArray();
        $project['tasks'] = $this->getTasks(array('project_id' => $id), $ctx);
        $project['recent_updates'] = $this->getUpdates(array('project_id' => $id, 'limit' => 10), $ctx);
        return $project;
    }

    public function saveProject(array $data, array $memberIds, array $ctx): int
    {
        $this->ensureSchema();
        $id = (int) ($data['id'] ?? 0);
        $name = $this->cleanText($data['project_name'] ?? '', 220);
        if ($name === '') {
            return 0;
        }
        if ($id > 0 && !$this->canEditProject($id, $ctx)) {
            return 0;
        }

        $workType = $this->normalizeWorkType($data['work_type'] ?? ($data['type'] ?? 'project'));
        $now = $this->now();
        $userId = (int) ($ctx['user_id'] ?? 0);
        $deptId = (int) ($ctx['department_id'] ?? 0);
        $canManage = $this->canManageWork($ctx);
        $code = $this->cleanText($data['project_code'] ?? '', 80);
        if ($code === '') {
            $code = strtoupper($workType === 'schedule' ? 'SCH' : 'PRJ') . '-' . date('ymd-His');
        }

        $payload = array(
            'work_type' => $workType,
            'project_code' => $code,
            'project_name' => $name,
            'client_name' => $this->cleanText($data['client_name'] ?? '', 220) ?: null,
            'project_type' => $this->cleanText($data['project_type'] ?? '', 120) ?: null,
            'category' => $this->cleanText($data['category'] ?? '', 120) ?: null,
            'department_id' => $this->positiveOrNull($data['department_id'] ?? 0) ?: ($deptId > 0 ? $deptId : null),
            'visibility' => $canManage ? $this->normalizeStatus($data['visibility'] ?? '', 'assigned', array('assigned', 'department', 'all')) : 'assigned',
            'priority' => $this->normalizeStatus($data['priority'] ?? '', 'medium', array('low', 'medium', 'high', 'critical')),
            'status' => $this->normalizeStatus($data['status'] ?? '', 'planned', array('planned', 'discovery', 'in_progress', 'review', 'blocked', 'on_hold', 'completed', 'closed', 'cancelled')),
            'progress' => $this->clampInt($data['progress'] ?? 0, 0, 100),
            'start_date' => $this->safeDate($data['start_date'] ?? ''),
            'due_date' => $this->safeDate($data['due_date'] ?? ''),
            'schedule_frequency' => $this->normalizeStatus($data['schedule_frequency'] ?? '', 'one_time', array('one_time', 'daily', 'weekly', 'monthly', 'custom')),
            'schedule_start_date' => $this->safeDate($data['schedule_start_date'] ?? ($data['start_date'] ?? '')),
            'schedule_end_date' => $this->safeDate($data['schedule_end_date'] ?? ($data['due_date'] ?? '')),
            'repeat_days' => $this->cleanText($data['repeat_days'] ?? '', 120) ?: null,
            'owner_name' => $this->cleanText($data['owner_name'] ?? '', 220) ?: null,
            'current_focus' => $this->cleanLong($data['current_focus'] ?? ''),
            'next_step' => $this->cleanLong($data['next_step'] ?? ''),
            'blockers' => $this->cleanLong($data['blockers'] ?? ''),
            'description' => $this->cleanLong($data['description'] ?? ''),
            'repo_url' => $this->cleanText($data['repo_url'] ?? '', 500) ?: null,
            'demo_url' => $this->cleanText($data['demo_url'] ?? '', 500) ?: null,
            'is_active' => 1,
            'updated_by_user_id' => $userId > 0 ? $userId : null,
            'updated_at' => $now,
        );

        if ($id > 0) {
            $this->db->table('work_projects')->where('id', $id)->update($payload);
        } else {
            $payload['created_by_user_id'] = $userId > 0 ? $userId : null;
            $payload['created_at'] = $now;
            $this->db->table('work_projects')->insert($payload);
            $id = (int) $this->db->insertID();
        }

        if ($id <= 0) {
            return 0;
        }

        $currentMemberId = $this->getCurrentMemberId($userId);
        if (!$canManage) {
            $memberIds = $currentMemberId > 0 ? array($currentMemberId) : array();
        } else {
            if ($currentMemberId > 0) {
                $memberIds[] = $currentMemberId;
            }
        }
        $this->saveProjectAssignments($id, $memberIds, $ctx);
        return $id;
    }

    private function saveProjectAssignments(int $projectId, array $memberIds, array $ctx): void
    {
        if ($projectId <= 0) {
            return;
        }
        $now = $this->now();
        $userId = (int) ($ctx['user_id'] ?? 0);
        $memberIds = array_values(array_unique(array_filter(array_map('intval', $memberIds))));
        $this->db->table('work_project_assignments')->where('project_id', $projectId)->delete();
        foreach ($memberIds as $memberId) {
            if ($memberId <= 0) {
                continue;
            }
            $member = $this->getMemberById($memberId);
            if (!$member) {
                continue;
            }
            try {
                $this->db->table('work_project_assignments')->insert(array(
                    'project_id' => $projectId,
                    'member_id' => $memberId,
                    'attend_user_id' => !empty($member['attend_user_id']) ? (int) $member['attend_user_id'] : null,
                    'role_on_project' => $this->canManageWork($ctx) ? 'Contributor' : 'Owner / Creator',
                    'responsibility' => null,
                    'allocation_percent' => 100,
                    'assigned_from' => $this->today(),
                    'assignment_status' => 'active',
                    'assigned_by_user_id' => $userId > 0 ? $userId : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
            } catch (Throwable $e) {
                // Duplicate-safe.
            }
        }
    }

    public function archiveProject(int $id, array $ctx = array()): bool
    {
        $this->ensureSchema();
        if ($id <= 0 || !$this->canEditProject($id, $ctx)) {
            return false;
        }
        $this->db->table('work_projects')->where('id', $id)->update(array(
            'is_active' => 0,
            'updated_by_user_id' => (int) ($ctx['user_id'] ?? 0) ?: null,
            'updated_at' => $this->now(),
        ));
        return true;
    }

    private function taskScopeSql(array $ctx, string $alias = 't'): array
    {
        if ($this->canManageWork($ctx)) {
            return array('1=1', array());
        }
        $userId = (int) ($ctx['user_id'] ?? 0);
        $memberId = $this->getCurrentMemberId($userId);
        $sql = "(
            {$alias}.created_by_user_id = ?
            OR {$alias}.assigned_to_user_id = ?
            OR {$alias}.assigned_to_member_id = ?
            OR EXISTS (
                SELECT 1 FROM work_projects sp
                WHERE sp.id = {$alias}.project_id AND sp.created_by_user_id = ?
            )
            OR EXISTS (
                SELECT 1 FROM work_project_assignments spa
                LEFT JOIN work_members sm ON sm.id = spa.member_id
                WHERE spa.project_id = {$alias}.project_id
                  AND spa.assignment_status = 'active'
                  AND (spa.attend_user_id = ? OR sm.attend_user_id = ?)
            )
        )";
        return array($sql, array($userId, $userId, $memberId, $userId, $userId, $userId));
    }

    public function getTasks(array $filters, array $ctx): array
    {
        $this->ensureSchema();
        list($scopeSql, $scopeParams) = $this->taskScopeSql($ctx, 't');
        $sql = "SELECT
                    t.*,
                    p.project_name,
                    p.project_code,
                    p.work_type,
                    p.priority AS container_priority,
                    wm.member_name AS assigned_to_name,
                    wm.employee_id AS assigned_to_employee_id,
                    cu.full_name AS created_by_name,
                    (SELECT COUNT(*) FROM work_daily_updates wdu WHERE wdu.task_id = t.id) AS update_count,
                    (SELECT COALESCE(SUM(wdu.hours_spent), 0) FROM work_daily_updates wdu WHERE wdu.task_id = t.id) AS logged_hours
                FROM work_tasks t
                INNER JOIN work_projects p ON p.id = t.project_id
                LEFT JOIN work_members wm ON wm.id = t.assigned_to_member_id
                LEFT JOIN attend_users cu ON cu.id = t.created_by_user_id
                WHERE t.is_active = 1 AND p.is_active = 1 AND " . $scopeSql;
        $params = $scopeParams;

        if (!empty($filters['project_id'])) {
            $sql .= ' AND t.project_id = ?';
            $params[] = (int) $filters['project_id'];
        }
        if (!empty($filters['task_id'])) {
            $sql .= ' AND t.id = ?';
            $params[] = (int) $filters['task_id'];
        }
        if (!empty($filters['assigned_to_user_id'])) {
            $sql .= ' AND t.assigned_to_user_id = ?';
            $params[] = (int) $filters['assigned_to_user_id'];
        }
        if (!empty($filters['member_id'])) {
            $sql .= ' AND t.assigned_to_member_id = ?';
            $params[] = (int) $filters['member_id'];
        }
        if (!empty($filters['work_type'])) {
            $sql .= ' AND p.work_type = ?';
            $params[] = $this->normalizeWorkType($filters['work_type']);
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND t.status = ?';
            $params[] = $this->cleanText($filters['status'], 40);
        }
        if (!empty($filters['today'])) {
            $sql .= " AND (t.due_date = CURDATE() OR EXISTS (SELECT 1 FROM work_daily_updates du WHERE du.task_id = t.id AND du.update_date = CURDATE()))";
        }
        if (!empty($filters['search'])) {
            $term = '%' . trim((string) $filters['search']) . '%';
            $sql .= ' AND (t.task_title LIKE ? OR t.task_code LIKE ? OR t.task_description LIKE ? OR p.project_name LIKE ?)';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY FIELD(t.status, 'blocked', 'review', 'in_progress', 'todo', 'paused', 'completed', 'cancelled'), FIELD(t.priority, 'critical', 'high', 'medium', 'low'), t.due_date IS NULL, t.due_date ASC, t.created_at DESC";
        $rows = $this->db->query($sql, $params)->getResultArray();
        foreach ($rows as &$row) {
            $row['checklist'] = $this->decodeJsonList($row['checklist_json'] ?? null);
        }
        unset($row);
        return $rows;
    }

    public function getTask(int $id, array $ctx): ?array
    {
        $rows = $this->getTasks(array('task_id' => $id), $ctx);
        return isset($rows[0]) ? $rows[0] : null;
    }

    public function saveTask(array $data, array $ctx): int
    {
        $this->ensureSchema();
        $id = (int) ($data['id'] ?? 0);
        $projectId = (int) ($data['project_id'] ?? 0);
        $title = $this->cleanText($data['task_title'] ?? '', 260);
        if ($title === '') {
            return 0;
        }
        if ($id > 0) {
            $existing = $this->getTask($id, $ctx);
            if (!$existing) {
                return 0;
            }
            $projectId = (int) $existing['project_id'];
            if (!$this->canManageWork($ctx) && (int) ($existing['created_by_user_id'] ?? 0) !== (int) ($ctx['user_id'] ?? 0) && (int) ($existing['assigned_to_user_id'] ?? 0) !== (int) ($ctx['user_id'] ?? 0)) {
                return 0;
            }
        }
        if ($projectId <= 0 || !$this->canViewProject($projectId, $ctx)) {
            return 0;
        }

        $now = $this->now();
        $userId = (int) ($ctx['user_id'] ?? 0);
        $canManage = $this->canManageWork($ctx);
        $assignedMemberId = (int) ($data['assigned_to_member_id'] ?? ($data['member_id'] ?? 0));
        if (!$canManage) {
            $assignedMemberId = $this->getCurrentMemberId($userId);
        }
        if ($assignedMemberId <= 0) {
            $assignedMemberId = $this->getCurrentMemberId($userId);
        }
        $assignedUserId = null;
        $member = $this->getMemberById($assignedMemberId);
        if ($member && !empty($member['attend_user_id'])) {
            $assignedUserId = (int) $member['attend_user_id'];
        }

        $checklist = $this->encodeChecklist($data['checklist'] ?? ($data['checklist_json'] ?? null));
        $status = $this->normalizeStatus($data['status'] ?? '', 'todo', array('todo', 'in_progress', 'review', 'blocked', 'paused', 'completed', 'cancelled'));
        $progress = $this->clampInt($data['progress'] ?? ($status === 'completed' ? 100 : 0), 0, 100);
        if ($status === 'completed') {
            $progress = 100;
        }

        $taskCode = $this->cleanText($data['task_code'] ?? '', 80);
        if ($taskCode === '') {
            $taskCode = 'TASK-' . date('ymd-His');
        }

        $payload = array(
            'project_id' => $projectId,
            'parent_task_id' => $this->positiveOrNull($data['parent_task_id'] ?? 0),
            'task_code' => $taskCode,
            'task_title' => $title,
            'task_description' => $this->cleanLong($data['task_description'] ?? ''),
            'task_type' => $this->normalizeStatus($data['task_type'] ?? '', 'project_task', array('project_task', 'schedule_task')),
            'priority' => $this->normalizeStatus($data['priority'] ?? '', 'medium', array('low', 'medium', 'high', 'critical')),
            'status' => $status,
            'progress' => $progress,
            'assigned_to_member_id' => $assignedMemberId > 0 ? $assignedMemberId : null,
            'assigned_to_user_id' => $assignedUserId,
            'start_date' => $this->safeDate($data['start_date'] ?? ''),
            'due_date' => $this->safeDate($data['due_date'] ?? ''),
            'planned_start_time' => $this->safeTime($data['planned_start_time'] ?? ''),
            'planned_end_time' => $this->safeTime($data['planned_end_time'] ?? ''),
            'planned_hours' => $this->clampFloat($data['planned_hours'] ?? 0, 0, 999),
            'actual_hours' => $this->clampFloat($data['actual_hours'] ?? 0, 0, 999),
            'checklist_json' => $checklist['json'],
            'checklist_total' => $checklist['total'],
            'checklist_done' => $checklist['done'],
            'is_active' => 1,
            'updated_by_user_id' => $userId > 0 ? $userId : null,
            'updated_at' => $now,
        );

        if ($id > 0) {
            $this->db->table('work_tasks')->where('id', $id)->update($payload);
        } else {
            $payload['created_by_user_id'] = $userId > 0 ? $userId : null;
            $payload['created_at'] = $now;
            $this->db->table('work_tasks')->insert($payload);
            $id = (int) $this->db->insertID();
        }

        if ($id > 0) {
            $this->syncProjectProgressFromTasks($projectId);
        }
        return $id;
    }

    public function archiveTask(int $id, array $ctx): bool
    {
        $this->ensureSchema();
        $task = $this->getTask($id, $ctx);
        if (!$task) {
            return false;
        }
        if (!$this->canManageWork($ctx) && (int) ($task['created_by_user_id'] ?? 0) !== (int) ($ctx['user_id'] ?? 0)) {
            return false;
        }
        $this->db->table('work_tasks')->where('id', $id)->update(array(
            'is_active' => 0,
            'updated_by_user_id' => (int) ($ctx['user_id'] ?? 0) ?: null,
            'updated_at' => $this->now(),
        ));
        $this->syncProjectProgressFromTasks((int) $task['project_id']);
        return true;
    }

    private function updateScopeSql(array $ctx, string $alias = 'wdu'): array
    {
        if ($this->canManageWork($ctx)) {
            return array('1=1', array());
        }
        $userId = (int) ($ctx['user_id'] ?? 0);
        $sql = "(
            {$alias}.attend_user_id = ?
            OR {$alias}.created_by_user_id = ?
            OR EXISTS (SELECT 1 FROM work_tasks ut WHERE ut.id = {$alias}.task_id AND ut.assigned_to_user_id = ?)
            OR EXISTS (SELECT 1 FROM work_projects up WHERE up.id = {$alias}.project_id AND up.created_by_user_id = ?)
        )";
        return array($sql, array($userId, $userId, $userId, $userId));
    }

    public function getUpdates(array $filters, array $ctx): array
    {
        $this->ensureSchema();
        list($scopeSql, $scopeParams) = $this->updateScopeSql($ctx, 'wdu');
        $sql = "SELECT
                    wdu.*,
                    wp.project_name,
                    wp.project_code,
                    wp.work_type,
                    wt.task_title,
                    wt.task_code,
                    wm.member_name,
                    wm.employee_id AS member_emp_id,
                    wm.role_title,
                    au.full_name AS attend_user_name
                FROM work_daily_updates wdu
                LEFT JOIN work_projects wp ON wp.id = wdu.project_id
                LEFT JOIN work_tasks wt ON wt.id = wdu.task_id
                LEFT JOIN work_members wm ON wm.id = wdu.member_id
                LEFT JOIN attend_users au ON au.id = wdu.attend_user_id
                WHERE " . $scopeSql;
        $params = $scopeParams;
        if (!empty($filters['project_id'])) {
            $sql .= ' AND wdu.project_id = ?';
            $params[] = (int) $filters['project_id'];
        }
        if (!empty($filters['task_id'])) {
            $sql .= ' AND wdu.task_id = ?';
            $params[] = (int) $filters['task_id'];
        }
        if (!empty($filters['member_id'])) {
            $sql .= ' AND wdu.member_id = ?';
            $params[] = (int) $filters['member_id'];
        }
        if (!empty($filters['date'])) {
            $sql .= ' AND wdu.update_date = ?';
            $params[] = $this->safeDate($filters['date']) ?: $this->today();
        } elseif (!empty($filters['today'])) {
            $sql .= ' AND wdu.update_date = CURDATE()';
        } else {
            if (!empty($filters['from'])) {
                $sql .= ' AND wdu.update_date >= ?';
                $params[] = $this->safeDate($filters['from']) ?: $this->today();
            }
            if (!empty($filters['to'])) {
                $sql .= ' AND wdu.update_date <= ?';
                $params[] = $this->safeDate($filters['to']) ?: $this->today();
            }
        }
        if (!empty($filters['search'])) {
            $term = '%' . trim((string) $filters['search']) . '%';
            $sql .= ' AND (wdu.update_title LIKE ? OR wdu.today_task LIKE ? OR wdu.progress_note LIKE ? OR wt.task_title LIKE ? OR wp.project_name LIKE ?)';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        $limit = !empty($filters['limit']) ? max(1, min(200, (int) $filters['limit'])) : 0;
        $sql .= ' ORDER BY wdu.update_date DESC, wdu.id DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        $rows = $this->db->query($sql, $params)->getResultArray();
        foreach ($rows as &$row) {
            $row['steps'] = $this->decodeJsonList($row['steps'] ?? null);
        }
        unset($row);
        return $rows;
    }

    public function saveUpdate(array $data, array $ctx): int
    {
        $this->ensureSchema();
        $id = (int) ($data['id'] ?? 0);
        $taskId = (int) ($data['task_id'] ?? 0);
        $projectId = (int) ($data['project_id'] ?? 0);
        $title = $this->cleanText($data['update_title'] ?? ($data['summary'] ?? ''), 300);
        if ($title === '') {
            return 0;
        }

        $oldTask = null;
        if ($taskId > 0) {
            $oldTask = $this->getTask($taskId, $ctx);
            if (!$oldTask) {
                return 0;
            }
            $projectId = (int) $oldTask['project_id'];
        }
        if ($projectId <= 0 || !$this->canViewProject($projectId, $ctx)) {
            return 0;
        }

        $userId = (int) ($ctx['user_id'] ?? 0);
        $canManage = $this->canManageWork($ctx);
        $memberId = (int) ($data['member_id'] ?? 0);
        if (!$canManage || $memberId <= 0) {
            if ($taskId > 0 && $oldTask && !empty($oldTask['assigned_to_member_id']) && $canManage) {
                $memberId = (int) $oldTask['assigned_to_member_id'];
            } else {
                $memberId = $this->getCurrentMemberId($userId);
            }
        }
        $attendUserId = $userId > 0 ? $userId : null;
        $member = $this->getMemberById($memberId);
        if ($canManage && $member && !empty($member['attend_user_id'])) {
            $attendUserId = (int) $member['attend_user_id'];
        }
        if (!$canManage) {
            $attendUserId = $userId;
        }

        $steps = $this->encodeSteps($data['steps'] ?? ($data['steps_json'] ?? null));
        $updateDate = $this->safeDate($data['update_date'] ?? ($data['work_date'] ?? '')) ?: $this->today();
        $status = $this->normalizeStatus($data['work_status'] ?? ($data['status'] ?? ''), 'working', array('working', 'testing', 'review', 'blocked', 'paused', 'completed'));
        $completion = null;
        if (isset($data['completion_percent']) && $data['completion_percent'] !== '' && $data['completion_percent'] !== null) {
            $completion = $this->clampInt($data['completion_percent'], 0, 100);
        } elseif ($status === 'completed') {
            $completion = 100;
        }

        $now = $this->now();
        $payload = array(
            'project_id' => $projectId,
            'task_id' => $taskId > 0 ? $taskId : null,
            'member_id' => $memberId > 0 ? $memberId : null,
            'attend_user_id' => $attendUserId,
            'update_date' => $updateDate,
            'update_title' => $title,
            'work_status' => $status,
            'is_today_focus' => (int) ($data['is_today_focus'] ?? 1) === 1 ? 1 : 0,
            'completion_percent' => $completion,
            'hours_spent' => $this->clampFloat($data['hours_spent'] ?? 0, 0, 99),
            'planned_hours' => $this->clampFloat($data['planned_hours'] ?? 0, 0, 99),
            'today_task' => $this->cleanLong($data['today_task'] ?? ($data['summary'] ?? '')),
            'progress_note' => $this->cleanLong($data['progress_note'] ?? ($data['summary'] ?? '')),
            'achievements' => $this->cleanLong($data['achievements'] ?? ($data['completed_tasks'] ?? '')),
            'blockers' => $this->cleanLong($data['blockers'] ?? ''),
            'tomorrow_plan' => $this->cleanLong($data['tomorrow_plan'] ?? ($data['next_plan'] ?? '')),
            'steps' => $steps['json'],
            'steps_total' => $steps['total'],
            'steps_done' => $steps['done'],
            'sync_project_progress' => (int) ($data['sync_project_progress'] ?? 1) === 1 ? 1 : 0,
            'review_status' => $this->normalizeStatus($data['review_status'] ?? '', 'submitted', array('submitted', 'approved', 'rejected')),
            'updated_by_user_id' => $userId > 0 ? $userId : null,
            'updated_at' => $now,
        );

        if ($id > 0) {
            $existing = $this->db->query('SELECT * FROM work_daily_updates WHERE id = ? LIMIT 1', array($id))->getRowArray();
            if (!$existing) {
                return 0;
            }
            if (!$canManage && (int) ($existing['attend_user_id'] ?? 0) !== $userId && (int) ($existing['created_by_user_id'] ?? 0) !== $userId) {
                return 0;
            }
            $this->db->table('work_daily_updates')->where('id', $id)->update($payload);
        } else {
            $payload['created_by_user_id'] = $userId > 0 ? $userId : null;
            $payload['created_at'] = $now;
            $this->db->table('work_daily_updates')->insert($payload);
            $id = (int) $this->db->insertID();
        }

        if ($id <= 0) {
            return 0;
        }

        $this->replaceStepRows($id, $projectId, $taskId, $memberId, $attendUserId, $updateDate, $steps['items'], $ctx);

        if ($taskId > 0) {
            $oldStatus = $oldTask ? (string) ($oldTask['status'] ?? '') : null;
            $oldProgress = $oldTask ? (int) ($oldTask['progress'] ?? 0) : null;
            $taskStatus = $this->mapUpdateStatusToTaskStatus($status);
            $taskPayload = array(
                'actual_hours' => $this->calculateTaskHours($taskId),
                'updated_by_user_id' => $userId > 0 ? $userId : null,
                'updated_at' => $now,
            );
            if ($completion !== null) {
                $taskPayload['progress'] = $completion;
            }
            if ($taskStatus !== '') {
                $taskPayload['status'] = $taskStatus;
            }
            if ($completion === 100) {
                $taskPayload['status'] = 'completed';
            }
            $this->db->table('work_tasks')->where('id', $taskId)->update($taskPayload);
            $newTask = $this->db->query('SELECT status, progress FROM work_tasks WHERE id = ? LIMIT 1', array($taskId))->getRowArray();
            $this->db->table('work_task_updates')->insert(array(
                'task_id' => $taskId,
                'project_id' => $projectId,
                'daily_update_id' => $id,
                'member_id' => $memberId > 0 ? $memberId : null,
                'attend_user_id' => $attendUserId,
                'update_date' => $updateDate,
                'old_status' => $oldStatus,
                'new_status' => $newTask ? (string) $newTask['status'] : $taskStatus,
                'old_progress' => $oldProgress,
                'new_progress' => $newTask ? (int) $newTask['progress'] : $completion,
                'hours_spent' => $payload['hours_spent'],
                'note' => $payload['progress_note'],
                'created_by_user_id' => $userId > 0 ? $userId : null,
                'created_at' => $now,
            ));
        }

        if (!empty($payload['sync_project_progress'])) {
            $this->syncProjectProgressFromTasks($projectId, $completion);
        }

        return $id;
    }

    private function mapUpdateStatusToTaskStatus(string $status): string
    {
        if ($status === 'completed') {
            return 'completed';
        }
        if ($status === 'blocked') {
            return 'blocked';
        }
        if ($status === 'review' || $status === 'testing') {
            return 'review';
        }
        if ($status === 'paused') {
            return 'paused';
        }
        if ($status === 'working') {
            return 'in_progress';
        }
        return '';
    }

    private function calculateTaskHours(int $taskId): float
    {
        if ($taskId <= 0) {
            return 0.0;
        }
        $row = $this->db->query('SELECT COALESCE(SUM(hours_spent), 0) AS hrs FROM work_daily_updates WHERE task_id = ?', array($taskId))->getRowArray();
        return round((float) ($row['hrs'] ?? 0), 2);
    }

    private function syncProjectProgressFromTasks(int $projectId, ?int $fallbackProgress = null): void
    {
        if ($projectId <= 0) {
            return;
        }
        $row = $this->db->query(
            "SELECT COUNT(*) AS task_count, COALESCE(ROUND(AVG(progress), 0), 0) AS avg_progress,
                    SUM(status NOT IN ('completed','cancelled')) AS open_tasks
             FROM work_tasks WHERE project_id = ? AND is_active = 1",
            array($projectId)
        )->getRowArray();
        $taskCount = (int) ($row['task_count'] ?? 0);
        $progress = $taskCount > 0 ? (int) ($row['avg_progress'] ?? 0) : ($fallbackProgress !== null ? $fallbackProgress : null);
        if ($progress === null) {
            return;
        }
        $payload = array('progress' => $this->clampInt($progress, 0, 100), 'updated_at' => $this->now());
        if ($payload['progress'] >= 100) {
            $payload['status'] = 'completed';
        } elseif ($payload['progress'] > 0) {
            $payload['status'] = 'in_progress';
        }
        $this->db->table('work_projects')->where('id', $projectId)->update($payload);
    }

    public function deleteUpdate(int $id, array $ctx = array()): bool
    {
        $this->ensureSchema();
        if ($id <= 0) {
            return false;
        }
        $row = $this->db->query('SELECT * FROM work_daily_updates WHERE id = ? LIMIT 1', array($id))->getRowArray();
        if (!$row) {
            return false;
        }
        if (!$this->canManageWork($ctx) && (int) ($row['attend_user_id'] ?? 0) !== (int) ($ctx['user_id'] ?? 0) && (int) ($row['created_by_user_id'] ?? 0) !== (int) ($ctx['user_id'] ?? 0)) {
            return false;
        }
        $this->db->table('work_update_steps')->where('daily_update_id', $id)->delete();
        $this->db->table('work_daily_updates')->where('id', $id)->delete();
        if (!empty($row['task_id'])) {
            $this->db->table('work_tasks')->where('id', (int) $row['task_id'])->update(array('actual_hours' => $this->calculateTaskHours((int) $row['task_id']), 'updated_at' => $this->now()));
        }
        $this->syncProjectProgressFromTasks((int) $row['project_id']);
        return true;
    }

    public function reviewUpdate(int $id, string $reviewStatus, string $note, array $ctx): bool
    {
        $this->ensureSchema();
        if (!$this->canManageWork($ctx) || $id <= 0) {
            return false;
        }
        $reviewStatus = $this->normalizeStatus($reviewStatus, 'approved', array('submitted', 'approved', 'rejected'));
        $row = $this->db->query('SELECT review_status FROM work_daily_updates WHERE id = ? LIMIT 1', array($id))->getRowArray();
        if (!$row) {
            return false;
        }
        $now = $this->now();
        $this->db->table('work_daily_updates')->where('id', $id)->update(array(
            'review_status' => $reviewStatus,
            'review_note' => $this->cleanLong($note),
            'reviewed_by_user_id' => (int) ($ctx['user_id'] ?? 0) ?: null,
            'reviewed_at' => $now,
            'updated_at' => $now,
        ));
        $this->db->table('work_review_logs')->insert(array(
            'daily_update_id' => $id,
            'old_status' => (string) ($row['review_status'] ?? ''),
            'new_status' => $reviewStatus,
            'review_note' => $this->cleanLong($note),
            'reviewed_by_user_id' => (int) ($ctx['user_id'] ?? 0) ?: null,
            'reviewed_at' => $now,
        ));
        return true;
    }

    public function getDashboard(array $ctx): array
    {
        $this->ensureSchema();
        $projects = $this->getProjects(array(), $ctx);
        $tasks = $this->getTasks(array(), $ctx);
        $today = $this->today();
        $weekStart = date('Y-m-d', strtotime('monday this week'));

        $projectStats = array('total' => 0, 'projects' => 0, 'schedules' => 0, 'in_progress' => 0, 'completed' => 0, 'blocked' => 0, 'overdue' => 0);
        foreach ($projects as $project) {
            $projectStats['total']++;
            if (($project['work_type'] ?? 'project') === 'schedule') {
                $projectStats['schedules']++;
            } else {
                $projectStats['projects']++;
            }
            if (($project['status'] ?? '') === 'in_progress') {
                $projectStats['in_progress']++;
            }
            if (($project['status'] ?? '') === 'completed') {
                $projectStats['completed']++;
            }
            if (($project['status'] ?? '') === 'blocked') {
                $projectStats['blocked']++;
            }
            if (!empty($project['due_date']) && $project['due_date'] < $today && !in_array((string) $project['status'], array('completed', 'closed', 'cancelled'), true)) {
                $projectStats['overdue']++;
            }
        }

        $taskStats = array('total' => 0, 'todo' => 0, 'in_progress' => 0, 'review' => 0, 'blocked' => 0, 'completed' => 0, 'overdue' => 0);
        foreach ($tasks as $task) {
            $taskStats['total']++;
            $status = (string) ($task['status'] ?? 'todo');
            if (isset($taskStats[$status])) {
                $taskStats[$status]++;
            }
            if (!empty($task['due_date']) && $task['due_date'] < $today && !in_array($status, array('completed', 'cancelled'), true)) {
                $taskStats['overdue']++;
            }
        }

        $todayAgg = $this->aggregateUpdates($ctx, $today, $today);
        $weekAgg = $this->aggregateUpdates($ctx, $weekStart, $today);
        $memberCount = (int) ($this->db->query('SELECT COUNT(*) AS cnt FROM work_members WHERE is_active = 1')->getRowArray()['cnt'] ?? 0);

        return array(
            'projects' => $projectStats,
            'tasks' => $taskStats,
            'updates_today' => (int) ($todayAgg['updates'] ?? 0),
            'hours_today' => round((float) ($todayAgg['hours'] ?? 0), 2),
            'hours_this_week' => round((float) ($weekAgg['hours'] ?? 0), 2),
            'steps_today' => array('done' => (int) ($todayAgg['steps_done'] ?? 0), 'total' => (int) ($todayAgg['steps_total'] ?? 0)),
            'active_members' => $memberCount,
            'comparison' => $this->getComparison($ctx),
            'recent_updates' => $this->getUpdates(array('limit' => 8), $ctx),
        );
    }

    // ======================================================================
    // WORK PROGRESS COMMAND CENTER
    // ======================================================================

    private function resolveWorkPeriod(array $filters): array
    {
        $period = strtolower(trim((string) ($filters['period'] ?? 'today')));
        $today = $this->today();
        $weekMonday = date('Y-m-d', strtotime('monday this week'));

        switch ($period) {
            case 'yesterday':
                $from = $to = date('Y-m-d', strtotime($today . ' -1 day'));
                break;

            case 'specific_day':
                $from = $to = $this->safeDate($filters['day'] ?? ($filters['from'] ?? '')) ?: $today;
                break;

            case 'this_week':
                $from = $weekMonday;
                $to = date('Y-m-d', strtotime($weekMonday . ' +6 days'));
                break;

            case 'last_week':
                $lws = date('Y-m-d', strtotime($weekMonday . ' -7 days'));
                $from = $lws;
                $to = date('Y-m-d', strtotime($lws . ' +6 days'));
                break;

            case 'specific_week':
                $weekOf = $this->safeDate($filters['week_of'] ?? '') ?: $today;
                $from = date('Y-m-d', strtotime('monday this week', strtotime($weekOf)));
                $to = date('Y-m-d', strtotime($from . ' +6 days'));
                break;

            case 'this_month':
                $from = date('Y-m-01');
                $to = $today;
                break;

            case 'last_month':
                $from = date('Y-m-01', strtotime(date('Y-m-01') . ' -1 month'));
                $to = date('Y-m-t', strtotime($from));
                break;

            case 'specific_month':
                $monthRaw = trim((string) ($filters['month'] ?? ''));
                $year = (int) ($filters['year'] ?? date('Y'));
                $month = (int) date('m');
                if (preg_match('/^(\d{4})-(\d{2})$/', $monthRaw, $m)) {
                    $year = (int) $m[1];
                    $month = (int) $m[2];
                } elseif ($monthRaw !== '') {
                    $month = (int) $monthRaw;
                }
                if ($year < 2000 || $year > 2100) {
                    $year = (int) date('Y');
                }
                if ($month < 1 || $month > 12) {
                    $month = (int) date('m');
                }
                $from = sprintf('%04d-%02d-01', $year, $month);
                $to = date('Y-m-t', strtotime($from));
                break;

            case 'this_year':
                $from = date('Y-01-01');
                $to = $today;
                break;

            case 'last_year':
                $year = (int) date('Y') - 1;
                $from = sprintf('%04d-01-01', $year);
                $to = sprintf('%04d-12-31', $year);
                break;

            case 'specific_year':
                $year = (int) ($filters['year'] ?? date('Y'));
                if ($year < 2000 || $year > 2100) {
                    $year = (int) date('Y');
                }
                $from = sprintf('%04d-01-01', $year);
                $to = sprintf('%04d-12-31', $year);
                break;

            case 'custom':
                $from = $this->safeDate($filters['from'] ?? '') ?: $today;
                $to = $this->safeDate($filters['to'] ?? '') ?: $from;
                if ($to < $from) {
                    $to = $from;
                }
                break;

            default:
                $period = 'today';
                $from = $to = $today;
                break;
        }

        $labels = array(
            'today' => 'Today — ' . date('d M Y', strtotime($from)),
            'yesterday' => 'Yesterday — ' . date('d M Y', strtotime($from)),
            'specific_day' => 'Specific Day — ' . date('d M Y', strtotime($from)),
            'this_week' => 'This Week: ' . date('d M', strtotime($from)) . ' – ' . date('d M Y', strtotime($to)),
            'last_week' => 'Last Week: ' . date('d M', strtotime($from)) . ' – ' . date('d M Y', strtotime($to)),
            'specific_week' => 'Week of ' . date('d M', strtotime($from)) . ' – ' . date('d M Y', strtotime($to)),
            'this_month' => 'This Month: ' . date('d M', strtotime($from)) . ' – ' . date('d M Y', strtotime($to)),
            'last_month' => 'Last Month: ' . date('M Y', strtotime($from)),
            'specific_month' => 'Month: ' . date('M Y', strtotime($from)),
            'this_year' => 'This Year: ' . date('Y', strtotime($from)) . ' till ' . date('d M Y', strtotime($to)),
            'last_year' => 'Last Year: ' . date('Y', strtotime($from)),
            'specific_year' => 'Year: ' . date('Y', strtotime($from)),
            'custom' => date('d M Y', strtotime($from)) . ' – ' . date('d M Y', strtotime($to)),
        );

        return array(
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'label' => $labels[$period] ?? ($from . ' to ' . $to),
        );
    }

    private function countWorkingDays(string $from, string $to): int
    {
        $count = 0;
        $cursor = strtotime($from);
        $end = strtotime($to);
        while ($cursor !== false && $end !== false && $cursor <= $end) {
            if ((int) date('N', $cursor) <= 6) {
                $count++;
            }
            $cursor += 86400;
        }
        return max($count, 1);
    }

    public function getCommandCenter(array $filters, array $ctx): array
    {
        $this->ensureSchema();

        $range = $this->resolveWorkPeriod($filters);
        $period = $range['period'];
        $from = $range['from'];
        $to = $range['to'];
        $today = $this->today();

        $isManager     = $this->canManageWork($ctx);
        $viewUserId    = (int) ($ctx['user_id'] ?? 0);
        $empFilter     = (int) ($filters['emp_id']     ?? 0);
        $projectFilter = (int) ($filters['project_id'] ?? 0);
        $workTypeFilter = $this->cleanText($filters['work_type'] ?? '', 20);
        $statusFilter   = $this->cleanText($filters['status']    ?? '', 40);
        $searchFilter   = trim((string) ($filters['search']  ?? ''));

        // ── Scope SQL — who can see what ─────────────────────────────────
        $scopeParams = array();
        $scopeSql    = '1=1';
        if (!$isManager) {
            $scopeSql    = 'wdu.attend_user_id = ?';
            $scopeParams = array($viewUserId);
        } elseif ($empFilter > 0) {
            $scopeSql    = 'wdu.attend_user_id = ?';
            $scopeParams = array($empFilter);
        }

        // ── Optional search term helper ───────────────────────────────────
        $searchTerm = $searchFilter !== '' ? '%' . $searchFilter . '%' : null;

        // ── Employee performance ──────────────────────────────────────────
        $empSql = "SELECT
                    wdu.attend_user_id AS user_id,
                    u.full_name,
                    u.employee_id,
                    d.name AS department_name,
                    r.name AS role_name,
                    COUNT(wdu.id) AS update_count,
                    COALESCE(SUM(wdu.hours_spent), 0) AS work_hours,
                    COALESCE(SUM(wdu.planned_hours), 0) AS planned_hours,
                    COALESCE(SUM(wdu.steps_total), 0) AS steps_total,
                    COALESCE(SUM(wdu.steps_done), 0) AS steps_done,
                    COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS avg_progress,
                    SUM(CASE WHEN wdu.work_status = 'blocked' THEN 1 ELSE 0 END) AS blocked_updates,
                    SUM(CASE WHEN wdu.work_status = 'completed' THEN 1 ELSE 0 END) AS completed_updates,
                    SUM(CASE WHEN wdu.review_status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                    SUM(CASE WHEN wdu.review_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
                   FROM work_daily_updates wdu
                   LEFT JOIN attend_users u ON u.id = wdu.attend_user_id
                   LEFT JOIN attend_departments d ON d.id = u.department_id
                   LEFT JOIN attend_roles r ON r.id = u.role_id
                   WHERE wdu.update_date BETWEEN ? AND ?
                     AND wdu.attend_user_id IS NOT NULL
                     AND {$scopeSql}";
        $empParams = array_merge(array($from, $to), $scopeParams);

        if ($projectFilter > 0) {
            $empSql .= ' AND wdu.project_id = ?';
            $empParams[] = $projectFilter;
        }
        if ($workTypeFilter !== '') {
            $empSql .= " AND EXISTS (SELECT 1 FROM work_projects wpc WHERE wpc.id = wdu.project_id AND wpc.work_type = ?)";
            $empParams[] = $workTypeFilter;
        }
        if ($statusFilter !== '') {
            $empSql .= ' AND wdu.work_status = ?';
            $empParams[] = $statusFilter;
        }
        if ($searchTerm !== null) {
            $empSql .= ' AND (u.full_name LIKE ? OR u.employee_id LIKE ?)';
            $empParams[] = $searchTerm;
            $empParams[] = $searchTerm;
        }
        $empSql .= ' GROUP BY wdu.attend_user_id, u.full_name, u.employee_id, d.name, r.name
                    ORDER BY work_hours DESC, update_count DESC';
        $empRows = $this->db->query($empSql, $empParams)->getResultArray();

        // Attendance hours per employee in range
        $attByUser = array();
        if (!empty($empRows)) {
            $userIds      = array_column($empRows, 'user_id');
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $attRows      = $this->db->query(
                "SELECT a.user_id,
                        COALESCE(ROUND(SUM(CASE
                            WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                            THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60
                            ELSE 0 END), 2), 0) AS attendance_hours,
                        COUNT(DISTINCT a.attendance_date) AS days_attended
                 FROM attend_attendance a
                 WHERE a.attendance_date BETWEEN ? AND ? AND a.user_id IN ({$placeholders})
                 GROUP BY a.user_id",
                array_merge(array($from, $to), $userIds)
            )->getResultArray();
            foreach ($attRows as $ar) {
                $attByUser[(int) $ar['user_id']] = $ar;
            }
        }

        // Working-day count in range (Mon–Sat)
        $dayCount = $this->countWorkingDays($from, $to);

        // Build employee performance table with scores and risk
        $empPerf = array();
        foreach ($empRows as $emp) {
            $uid            = (int) $emp['user_id'];
            $att            = $attByUser[$uid] ?? array('attendance_hours' => 0, 'days_attended' => 0);
            $workHours      = (float) ($emp['work_hours']      ?? 0);
            $stepsTotal     = (int)   ($emp['steps_total']     ?? 0);
            $stepsDone      = (int)   ($emp['steps_done']      ?? 0);
            $avgProgress    = (float) ($emp['avg_progress']    ?? 0);
            $updateCount    = (int)   ($emp['update_count']    ?? 0);
            $blockedUpdates = (int)   ($emp['blocked_updates'] ?? 0);
            $attHours       = (float) ($att['attendance_hours'] ?? 0);

            $stepCompletion = $stepsTotal > 0
                ? (int) round(($stepsDone / $stepsTotal) * 100)
                : ($updateCount > 0 ? 100 : 0);

            $expectedHours = $dayCount * 8.0;
            $hoursRatio    = $expectedHours > 0 ? min(100, (int) round(($workHours / $expectedHours) * 100)) : 0;
            $updateRatio   = min(100, (int) round(($updateCount / max($dayCount, 1)) * 100));

            $perfScore = (int) round(
                ($avgProgress    * 0.35) +
                ($stepCompletion * 0.25) +
                ($hoursRatio     * 0.25) +
                ($updateRatio    * 0.15)
            );

            $blockedRatio = $updateCount > 0 ? ($blockedUpdates / $updateCount) : 0;
            if ($blockedRatio >= 0.5 || ($avgProgress < 20 && $updateCount > 0)) {
                $riskLevel = 'high';
            } elseif ($blockedRatio >= 0.25 || ($avgProgress < 50 && $updateCount > 0)) {
                $riskLevel = 'medium';
            } else {
                $riskLevel = 'low';
            }

            $empPerf[] = array(
                'user_id'           => $uid,
                'full_name'         => (string) ($emp['full_name']       ?? ''),
                'employee_id'       => (string) ($emp['employee_id']     ?? ''),
                'department_name'   => (string) ($emp['department_name'] ?? ''),
                'role_name'         => (string) ($emp['role_name']       ?? ''),
                'update_count'      => $updateCount,
                'work_hours'        => round($workHours, 2),
                'attendance_hours'  => round($attHours, 2),
                'steps_total'       => $stepsTotal,
                'steps_done'        => $stepsDone,
                'step_completion'   => $stepCompletion,
                'avg_progress'      => $avgProgress,
                'blocked_updates'   => $blockedUpdates,
                'completed_updates' => (int) ($emp['completed_updates'] ?? 0),
                'approved_count'    => (int) ($emp['approved_count']    ?? 0),
                'rejected_count'    => (int) ($emp['rejected_count']    ?? 0),
                'performance_score' => $perfScore,
                'risk_level'        => $riskLevel,
            );
        }

        // ── Project / Schedule risk ───────────────────────────────────────
        list($projScopeSql, $projScopeParams) = $this->projectScopeSql($ctx, 'p');
        $projSql = "SELECT
                        p.id, p.work_type, p.project_code, p.project_name,
                        p.status AS project_status, p.priority, p.progress, p.due_date,
                        COUNT(wdu.id) AS update_count,
                        COALESCE(SUM(wdu.hours_spent), 0) AS total_hours,
                        SUM(CASE WHEN wdu.work_status = 'blocked' THEN 1 ELSE 0 END) AS blocked_updates,
                        (SELECT COUNT(*) FROM work_tasks wt WHERE wt.project_id = p.id AND wt.is_active = 1 AND wt.status = 'blocked') AS blocked_tasks,
                        (SELECT COUNT(*) FROM work_tasks wt WHERE wt.project_id = p.id AND wt.is_active = 1 AND wt.status NOT IN ('completed','cancelled')) AS open_tasks,
                        (SELECT COUNT(*) FROM work_tasks wt WHERE wt.project_id = p.id AND wt.is_active = 1 AND wt.status = 'completed') AS completed_tasks
                    FROM work_projects p
                    LEFT JOIN work_daily_updates wdu
                        ON wdu.project_id = p.id AND wdu.update_date BETWEEN ? AND ?
                    WHERE p.is_active = 1
                      AND p.status NOT IN ('completed','closed','cancelled')
                      AND {$projScopeSql}";
        $projParams = array_merge(array($from, $to), $projScopeParams);

        if ($projectFilter > 0) {
            $projSql .= ' AND p.id = ?';
            $projParams[] = $projectFilter;
        }
        if ($workTypeFilter !== '') {
            $projSql .= ' AND p.work_type = ?';
            $projParams[] = $workTypeFilter;
        }
        if ($searchTerm !== null) {
            $projSql .= ' AND (p.project_name LIKE ? OR p.project_code LIKE ?)';
            $projParams[] = $searchTerm;
            $projParams[] = $searchTerm;
        }
        $projSql .= ' GROUP BY p.id ORDER BY blocked_updates DESC, p.priority DESC, p.due_date ASC';
        $projRows = $this->db->query($projSql, $projParams)->getResultArray();

        $projectRisk = array();
        foreach ($projRows as $pr) {
            $isOverdue    = !empty($pr['due_date']) && $pr['due_date'] < $today;
            $blockedUpd   = (int) ($pr['blocked_updates'] ?? 0);
            $blockedTasks = (int) ($pr['blocked_tasks']   ?? 0);
            $progress     = (int) ($pr['progress']        ?? 0);
            $updCount     = (int) ($pr['update_count']    ?? 0);
            $openTasks    = (int) ($pr['open_tasks']      ?? 0);

            if (($isOverdue && ($blockedUpd > 0 || $blockedTasks > 0)) || ($pr['priority'] === 'critical' && $progress < 30)) {
                $riskLevel = 'critical';
            } elseif ($isOverdue || $blockedUpd > 1 || $blockedTasks > 1) {
                $riskLevel = 'high';
            } elseif ($blockedUpd > 0 || $blockedTasks > 0 || ($progress < 40 && $openTasks > 0)) {
                $riskLevel = 'medium';
            } else {
                $riskLevel = 'low';
            }

            $projectRisk[] = array(
                'id'             => (int)    $pr['id'],
                'work_type'      => (string) $pr['work_type'],
                'project_code'   => (string) ($pr['project_code']    ?? ''),
                'project_name'   => (string) ($pr['project_name']    ?? ''),
                'project_status' => (string) ($pr['project_status']  ?? ''),
                'priority'       => (string) ($pr['priority']        ?? 'medium'),
                'progress'       => $progress,
                'update_count'   => $updCount,
                'total_hours'    => round((float) ($pr['total_hours']    ?? 0), 2),
                'blocked_updates'=> $blockedUpd,
                'blocked_tasks'  => $blockedTasks,
                'open_tasks'     => $openTasks,
                'completed_tasks'=> (int) ($pr['completed_tasks'] ?? 0),
                'is_overdue'     => $isOverdue,
                'due_date'       => $pr['due_date'] ?? null,
                'risk_level'     => $riskLevel,
            );
        }

        // ── Day-by-day step timeline ──────────────────────────────────────
        $dlWhere  = '';
        $dlParams = array($from, $to);
        if (!$isManager) {
            $dlWhere   = 'AND wdu.attend_user_id = ?';
            $dlParams[] = $viewUserId;
        } elseif ($empFilter > 0) {
            $dlWhere   = 'AND wdu.attend_user_id = ?';
            $dlParams[] = $empFilter;
        }
        if ($projectFilter > 0) {
            $dlWhere   .= ' AND wdu.project_id = ?';
            $dlParams[] = $projectFilter;
        }
        if ($workTypeFilter !== '') {
            $dlWhere   .= " AND EXISTS (SELECT 1 FROM work_projects dpc WHERE dpc.id = wdu.project_id AND dpc.work_type = ?)";
            $dlParams[] = $workTypeFilter;
        }
        if ($statusFilter !== '') {
            $dlWhere   .= ' AND wdu.work_status = ?';
            $dlParams[] = $statusFilter;
        }
        if ($searchTerm !== null) {
            $dlWhere   .= ' AND (u.full_name LIKE ? OR wp.project_name LIKE ? OR wt.task_title LIKE ?)';
            $dlParams[] = $searchTerm;
            $dlParams[] = $searchTerm;
            $dlParams[] = $searchTerm;
        }

        $daylineSql = "SELECT
                            wdu.id AS update_id, wdu.update_date, wdu.attend_user_id AS user_id,
                            u.full_name, u.employee_id,
                            wp.project_name, wp.work_type, wp.project_code,
                            wt.task_title, wt.task_code,
                            wdu.update_title, wdu.work_status, wdu.completion_percent,
                            wdu.hours_spent, wdu.steps_total, wdu.steps_done,
                            wdu.blockers, wdu.tomorrow_plan, wdu.achievements,
                            wdu.progress_note, wdu.today_task, wdu.steps,
                            wdu.review_status
                       FROM work_daily_updates wdu
                       LEFT JOIN attend_users u ON u.id = wdu.attend_user_id
                       LEFT JOIN work_projects wp ON wp.id = wdu.project_id
                       LEFT JOIN work_tasks wt ON wt.id = wdu.task_id
                       WHERE wdu.update_date BETWEEN ? AND ? {$dlWhere}
                       ORDER BY wdu.update_date DESC, wdu.attend_user_id ASC, wdu.id ASC";
        $daylineRows = $this->db->query($daylineSql, $dlParams)->getResultArray();
        foreach ($daylineRows as &$dlr) {
            $dlr['steps'] = $this->decodeJsonList($dlr['steps'] ?? null);
        }
        unset($dlr);

        // Group by date
        $dayTimeline = array();
        foreach ($daylineRows as $row) {
            $date = $row['update_date'];
            if (!isset($dayTimeline[$date])) {
                $dayTimeline[$date] = array('date' => $date, 'updates' => array());
            }
            $dayTimeline[$date]['updates'][] = $row;
        }
        $dayTimeline = array_values($dayTimeline);

        // ── Global KPIs ───────────────────────────────────────────────────
        $kpiWhere  = '';
        $kpiParams = array_merge(array($from, $to), $scopeParams);
        if ($projectFilter > 0) {
            $kpiWhere   .= ' AND wdu.project_id = ?';
            $kpiParams[] = $projectFilter;
        }
        if ($workTypeFilter !== '') {
            $kpiWhere   .= " AND EXISTS (SELECT 1 FROM work_projects kpc WHERE kpc.id = wdu.project_id AND kpc.work_type = ?)";
            $kpiParams[] = $workTypeFilter;
        }
        if ($statusFilter !== '') {
            $kpiWhere   .= ' AND wdu.work_status = ?';
            $kpiParams[] = $statusFilter;
        }

        $kpi = $this->db->query(
            "SELECT
                COUNT(*) AS total_updates,
                COUNT(DISTINCT wdu.attend_user_id) AS active_employees,
                COALESCE(SUM(wdu.hours_spent), 0) AS total_hours,
                COALESCE(SUM(wdu.steps_total), 0) AS steps_total,
                COALESCE(SUM(wdu.steps_done), 0) AS steps_done,
                COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS avg_progress,
                SUM(CASE WHEN wdu.work_status = 'blocked' THEN 1 ELSE 0 END) AS blocked_count,
                COUNT(DISTINCT wdu.project_id) AS active_projects
             FROM work_daily_updates wdu
             WHERE wdu.update_date BETWEEN ? AND ? AND {$scopeSql} {$kpiWhere}",
            $kpiParams
        )->getRowArray() ?: array();

        $overdueCount = 0;
        foreach ($projectRisk as $pr) {
            if ($pr['is_overdue']) {
                $overdueCount++;
            }
        }

        // ── Admin Insights (managers only, today coverage) ────────────────
        $adminInsights = array();
        if ($isManager) {
            $insightDate = ($period === 'today') ? $today : (($period === 'yesterday') ? $from : $today);
            $noUpdateSql = "SELECT u.id AS user_id, u.full_name, u.employee_id, d.name AS department_name, r.name AS role_name
                            FROM attend_users u
                            LEFT JOIN attend_departments d ON d.id = u.department_id
                            LEFT JOIN attend_roles r ON r.id = u.role_id
                            WHERE u.is_active = 1
                              AND u.id NOT IN (
                                  SELECT DISTINCT wdu2.attend_user_id FROM work_daily_updates wdu2
                                  WHERE wdu2.update_date = ? AND wdu2.attend_user_id IS NOT NULL
                              )
                            ORDER BY u.full_name ASC";
            $noUpdateRows = $this->db->query($noUpdateSql, array($insightDate))->getResultArray();
            $totalActive  = (int) ($this->db->query('SELECT COUNT(*) AS cnt FROM attend_users WHERE is_active = 1')->getRowArray()['cnt'] ?? 0);
            $submitted    = $totalActive - count($noUpdateRows);
            $adminInsights = array(
                'insight_date'          => $insightDate,
                'no_update_today'       => $noUpdateRows,
                'total_active_employees'=> $totalActive,
                'submitted_today'       => max(0, $submitted),
                'coverage_pct'          => $totalActive > 0 ? (int) round(($submitted / $totalActive) * 100) : 0,
            );
        }

        // ── Filter dropdown data ─────────────────────────────────────────
        $membersList = array();
        if ($isManager) {
            $membersList = $this->db->query(
                "SELECT wm.attend_user_id AS user_id, wm.member_name, wm.employee_id
                 FROM work_members wm WHERE wm.is_active = 1 AND wm.attend_user_id IS NOT NULL
                 ORDER BY wm.member_name ASC"
            )->getResultArray();
        }
        list($vpSql, $vpParams) = $this->projectScopeSql($ctx, 'vp');
        $visibleProjects = $this->db->query(
            "SELECT vp.id, vp.project_name, vp.project_code, vp.work_type
             FROM work_projects vp WHERE vp.is_active = 1 AND {$vpSql} ORDER BY vp.project_name ASC",
            $vpParams
        )->getResultArray();

        return array(
            'success'              => true,
            'period'               => $period,
            'period_label'         => $range['label'],
            'date_range'           => array('from' => $from, 'to' => $to),
            'is_manager'           => $isManager,
            'kpis'                 => array(
                'total_updates'    => (int)   ($kpi['total_updates']    ?? 0),
                'active_employees' => (int)   ($kpi['active_employees'] ?? 0),
                'total_hours'      => round((float) ($kpi['total_hours']    ?? 0), 2),
                'steps_total'      => (int)   ($kpi['steps_total']      ?? 0),
                'steps_done'       => (int)   ($kpi['steps_done']       ?? 0),
                'avg_progress'     => (float) ($kpi['avg_progress']     ?? 0),
                'blocked_count'    => (int)   ($kpi['blocked_count']    ?? 0),
                'active_projects'  => (int)   ($kpi['active_projects']  ?? 0),
                'overdue_projects' => $overdueCount,
            ),
            'admin_insights'        => $adminInsights,
            'employee_performance'  => $empPerf,
            'project_risk'          => $projectRisk,
            'day_timeline'          => $dayTimeline,
            'members_list'          => $membersList,
            'visible_projects'      => $visibleProjects,
        );
    }

    public function getEmployeeWorkDetail(int $employeeUserId, array $filters, array $ctx): array
    {
        $this->ensureSchema();

        $range = $this->resolveWorkPeriod($filters);
        $from = $range['from'];
        $to = $range['to'];
        $isManager = $this->canManageWork($ctx);
        $currentUserId = (int) ($ctx['user_id'] ?? 0);

        if ($employeeUserId <= 0) {
            $employeeUserId = $currentUserId;
        }
        if ($employeeUserId <= 0) {
            return array('success' => false, 'message' => 'Invalid employee.');
        }
        if (!$isManager && $employeeUserId !== $currentUserId) {
            return array('success' => false, 'message' => 'You can only view your own work details.');
        }

        $employee = $this->db->query(
            "SELECT
                u.id AS user_id, u.employee_id, u.full_name, u.email, u.designation, u.is_active,
                d.id AS department_id, d.name AS department_name, d.code AS department_code,
                r.name AS role_name, r.label AS role_label, r.can_edit, r.can_view_analytics
             FROM attend_users u
             LEFT JOIN attend_departments d ON d.id = u.department_id
             LEFT JOIN attend_roles r ON r.id = u.role_id
             WHERE u.id = ?
             LIMIT 1",
            array($employeeUserId)
        )->getRowArray();

        if (!$employee) {
            return array('success' => false, 'message' => 'Employee not found.');
        }

        $member = $this->db->query(
            "SELECT * FROM work_members WHERE attend_user_id = ? AND is_active = 1 ORDER BY id DESC LIMIT 1",
            array($employeeUserId)
        )->getRowArray() ?: array();
        $memberId = (int) ($member['id'] ?? 0);

        $work = $this->db->query(
            "SELECT
                COUNT(*) AS update_count,
                COUNT(DISTINCT project_id) AS active_projects,
                COUNT(DISTINCT task_id) AS touched_tasks,
                COALESCE(SUM(hours_spent), 0) AS work_hours,
                COALESCE(SUM(planned_hours), 0) AS planned_hours,
                COALESCE(SUM(steps_total), 0) AS steps_total,
                COALESCE(SUM(steps_done), 0) AS steps_done,
                COALESCE(ROUND(AVG(completion_percent), 1), 0) AS avg_progress,
                SUM(CASE WHEN work_status = 'blocked' THEN 1 ELSE 0 END) AS blocked_updates,
                SUM(CASE WHEN work_status = 'completed' THEN 1 ELSE 0 END) AS completed_updates,
                SUM(CASE WHEN review_status = 'approved' THEN 1 ELSE 0 END) AS approved_updates,
                SUM(CASE WHEN review_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_updates
             FROM work_daily_updates
             WHERE attend_user_id = ? AND update_date BETWEEN ? AND ?",
            array($employeeUserId, $from, $to)
        )->getRowArray() ?: array();

        $attendance = $this->db->query(
            "SELECT
                COUNT(*) AS attendance_records,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_days,
                SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) AS half_days,
                SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) AS leave_days,
                SUM(CASE WHEN status = 'work_from_home' THEN 1 ELSE 0 END) AS wfh_days,
                COALESCE(ROUND(SUM(CASE WHEN check_in_time IS NOT NULL AND check_out_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time) / 60 ELSE 0 END), 2), 0) AS attendance_hours,
                MAX(attendance_date) AS last_attendance_date
             FROM attend_attendance
             WHERE user_id = ? AND attendance_date BETWEEN ? AND ?",
            array($employeeUserId, $from, $to)
        )->getRowArray() ?: array();

        $projectSql = "SELECT
                p.id, p.work_type, p.project_code, p.project_name, p.client_name, p.project_type, p.category,
                p.priority, p.status, p.progress, p.start_date, p.due_date, p.current_focus, p.next_step, p.blockers,
                COUNT(DISTINCT wt.id) AS assigned_task_count,
                SUM(CASE WHEN wt.status = 'completed' THEN 1 ELSE 0 END) AS completed_task_count,
                SUM(CASE WHEN wt.status = 'blocked' THEN 1 ELSE 0 END) AS blocked_task_count,
                COUNT(DISTINCT wdu.id) AS update_count,
                COALESCE(SUM(wdu.hours_spent), 0) AS hours_spent,
                COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS period_avg_progress
             FROM work_projects p
             LEFT JOIN work_project_assignments wpa ON wpa.project_id = p.id AND wpa.assignment_status = 'active'
                 AND (wpa.attend_user_id = ? OR wpa.member_id = ?)
             LEFT JOIN work_tasks wt ON wt.project_id = p.id AND wt.is_active = 1
                 AND (wt.assigned_to_user_id = ? OR wt.assigned_to_member_id = ?)
             LEFT JOIN work_daily_updates wdu ON wdu.project_id = p.id AND wdu.attend_user_id = ? AND wdu.update_date BETWEEN ? AND ?
             WHERE p.is_active = 1
               AND (p.created_by_user_id = ? OR wpa.id IS NOT NULL OR wt.id IS NOT NULL OR wdu.id IS NOT NULL)
             GROUP BY p.id
             ORDER BY p.status = 'blocked' DESC, p.due_date IS NULL ASC, p.due_date ASC, p.updated_at DESC
             LIMIT 30";
        $projects = $this->db->query($projectSql, array(
            $employeeUserId, $memberId, $employeeUserId, $memberId, $employeeUserId, $from, $to, $employeeUserId
        ))->getResultArray();

        $taskSql = "SELECT
                wt.id, wt.project_id, wt.task_code, wt.task_title, wt.task_type, wt.priority, wt.status, wt.progress,
                wt.start_date, wt.due_date, wt.planned_hours, wt.actual_hours, wt.checklist_total, wt.checklist_done,
                wp.project_name, wp.project_code, wp.work_type,
                COUNT(DISTINCT wdu.id) AS update_count,
                COALESCE(SUM(wdu.hours_spent), 0) AS period_hours,
                COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS period_avg_progress,
                MAX(wdu.update_date) AS last_update_date
             FROM work_tasks wt
             LEFT JOIN work_projects wp ON wp.id = wt.project_id
             LEFT JOIN work_daily_updates wdu ON wdu.task_id = wt.id AND wdu.attend_user_id = ? AND wdu.update_date BETWEEN ? AND ?
             WHERE wt.is_active = 1
               AND (wt.assigned_to_user_id = ? OR wt.assigned_to_member_id = ? OR wdu.id IS NOT NULL)
             GROUP BY wt.id
             ORDER BY wt.status = 'blocked' DESC, wt.due_date IS NULL ASC, wt.due_date ASC, wt.updated_at DESC
             LIMIT 40";
        $tasks = $this->db->query($taskSql, array($employeeUserId, $from, $to, $employeeUserId, $memberId))->getResultArray();

        $updates = $this->db->query(
            "SELECT
                wdu.id, wdu.project_id, wdu.task_id, wdu.update_date, wdu.update_title, wdu.work_status,
                wdu.completion_percent, wdu.hours_spent, wdu.planned_hours, wdu.today_task, wdu.progress_note,
                wdu.achievements, wdu.blockers, wdu.tomorrow_plan, wdu.steps, wdu.steps_total, wdu.steps_done,
                wdu.review_status, wdu.review_note, wdu.created_at,
                wp.project_name, wp.project_code, wp.work_type,
                wt.task_title, wt.task_code
             FROM work_daily_updates wdu
             LEFT JOIN work_projects wp ON wp.id = wdu.project_id
             LEFT JOIN work_tasks wt ON wt.id = wdu.task_id
             WHERE wdu.attend_user_id = ? AND wdu.update_date BETWEEN ? AND ?
             ORDER BY wdu.update_date DESC, wdu.id DESC
             LIMIT 60",
            array($employeeUserId, $from, $to)
        )->getResultArray();

        foreach ($updates as &$u) {
            $u['steps'] = $this->decodeJsonList($u['steps'] ?? null);
        }
        unset($u);

        $workingDays = $this->countWorkingDays($from, $to);
        $workHours = (float) ($work['work_hours'] ?? 0);
        $stepsTotal = (int) ($work['steps_total'] ?? 0);
        $stepsDone = (int) ($work['steps_done'] ?? 0);
        $avgProgress = (float) ($work['avg_progress'] ?? 0);
        $updateCount = (int) ($work['update_count'] ?? 0);
        $blockedUpdates = (int) ($work['blocked_updates'] ?? 0);
        $stepCompletion = $stepsTotal > 0 ? (int) round(($stepsDone / $stepsTotal) * 100) : ($updateCount > 0 ? 100 : 0);
        $expectedHours = $workingDays * 8.0;
        $hoursRatio = $expectedHours > 0 ? min(100, (int) round(($workHours / $expectedHours) * 100)) : 0;
        $updateRatio = min(100, (int) round(($updateCount / max($workingDays, 1)) * 100));
        $performanceScore = (int) round(($avgProgress * 0.35) + ($stepCompletion * 0.25) + ($hoursRatio * 0.25) + ($updateRatio * 0.15));
        $blockedRatio = $updateCount > 0 ? ($blockedUpdates / $updateCount) : 0;
        $riskLevel = 'low';
        if ($blockedRatio >= 0.5 || ($avgProgress < 20 && $updateCount > 0)) {
            $riskLevel = 'high';
        } elseif ($blockedRatio >= 0.25 || ($avgProgress < 50 && $updateCount > 0)) {
            $riskLevel = 'medium';
        }

        return array(
            'success' => true,
            'period' => $range['period'],
            'period_label' => $range['label'],
            'date_range' => array('from' => $from, 'to' => $to),
            'employee' => $employee,
            'member' => $member,
            'summary' => array(
                'update_count' => $updateCount,
                'active_projects' => (int) ($work['active_projects'] ?? 0),
                'touched_tasks' => (int) ($work['touched_tasks'] ?? 0),
                'work_hours' => round($workHours, 2),
                'planned_hours' => round((float) ($work['planned_hours'] ?? 0), 2),
                'attendance_hours' => round((float) ($attendance['attendance_hours'] ?? 0), 2),
                'present_days' => (int) ($attendance['present_days'] ?? 0),
                'absent_days' => (int) ($attendance['absent_days'] ?? 0),
                'half_days' => (int) ($attendance['half_days'] ?? 0),
                'leave_days' => (int) ($attendance['leave_days'] ?? 0),
                'wfh_days' => (int) ($attendance['wfh_days'] ?? 0),
                'steps_total' => $stepsTotal,
                'steps_done' => $stepsDone,
                'step_completion' => $stepCompletion,
                'avg_progress' => $avgProgress,
                'blocked_updates' => $blockedUpdates,
                'completed_updates' => (int) ($work['completed_updates'] ?? 0),
                'approved_updates' => (int) ($work['approved_updates'] ?? 0),
                'rejected_updates' => (int) ($work['rejected_updates'] ?? 0),
                'performance_score' => $performanceScore,
                'risk_level' => $riskLevel,
                'working_days' => $workingDays,
                'expected_hours' => $expectedHours,
            ),
            'projects' => $projects,
            'tasks' => $tasks,
            'updates' => $updates,
        );
    }

    private function aggregateUpdates(array $ctx, string $from, string $to): array
    {
        list($scopeSql, $scopeParams) = $this->updateScopeSql($ctx, 'wdu');
        $sql = "SELECT COUNT(*) AS updates, COALESCE(SUM(hours_spent), 0) AS hours,
                       COALESCE(SUM(steps_done), 0) AS steps_done, COALESCE(SUM(steps_total), 0) AS steps_total,
                       COALESCE(ROUND(AVG(completion_percent), 1), 0) AS avg_progress
                FROM work_daily_updates wdu
                WHERE " . $scopeSql . " AND wdu.update_date BETWEEN ? AND ?";
        $params = array_merge($scopeParams, array($from, $to));
        return $this->db->query($sql, $params)->getRowArray() ?: array();
    }

    public function getComparison(array $ctx): array
    {
        $this->ensureSchema();
        $today = $this->today();
        $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        $lastWeekStart = date('Y-m-d', strtotime($weekStart . ' -7 days'));
        $lastWeekEnd = date('Y-m-d', strtotime($weekStart . ' -1 day'));
        $monthStart = date('Y-m-01');
        $lastMonthStart = date('Y-m-01', strtotime($monthStart . ' -1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime($lastMonthStart));

        list($scopeSql, $scopeParams) = $this->updateScopeSql($ctx, 'wdu');
        $dailySql = "SELECT wdu.update_date, COUNT(*) AS updates, COALESCE(SUM(wdu.hours_spent), 0) AS hours,
                            COALESCE(SUM(wdu.steps_done), 0) AS steps_done, COALESCE(SUM(wdu.steps_total), 0) AS steps_total,
                            COALESCE(ROUND(AVG(wdu.completion_percent), 1), 0) AS avg_progress
                     FROM work_daily_updates wdu
                     WHERE " . $scopeSql . " AND wdu.update_date BETWEEN ? AND ?
                     GROUP BY wdu.update_date ORDER BY wdu.update_date ASC";
        $dailyBreakdown = $this->db->query($dailySql, array_merge($scopeParams, array($weekStart, $weekEnd)))->getResultArray();

        $todayAgg = $this->aggregateUpdates($ctx, $today, $today);
        $yesterdayAgg = $this->aggregateUpdates($ctx, $yesterday, $yesterday);
        $thisWeek = $this->aggregateUpdates($ctx, $weekStart, $weekEnd);
        $lastWeek = $this->aggregateUpdates($ctx, $lastWeekStart, $lastWeekEnd);
        $thisMonth = $this->aggregateUpdates($ctx, $monthStart, $today);
        $lastMonth = $this->aggregateUpdates($ctx, $lastMonthStart, $lastMonthEnd);

        return array(
            'today' => $todayAgg,
            'yesterday' => $yesterdayAgg,
            'day_delta' => array(
                'updates' => (int) ($todayAgg['updates'] ?? 0) - (int) ($yesterdayAgg['updates'] ?? 0),
                'hours' => round((float) ($todayAgg['hours'] ?? 0) - (float) ($yesterdayAgg['hours'] ?? 0), 2),
            ),
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'week_delta' => array(
                'updates' => (int) ($thisWeek['updates'] ?? 0) - (int) ($lastWeek['updates'] ?? 0),
                'hours' => round((float) ($thisWeek['hours'] ?? 0) - (float) ($lastWeek['hours'] ?? 0), 2),
            ),
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'daily_breakdown' => $dailyBreakdown,
        );
    }

    public function getAttendanceWorkDaily(array $filters, array $ctx): array
    {
        $this->ensureSchema();
        $from = $this->safeDate($filters['from'] ?? '') ?: $this->today();
        $to = $this->safeDate($filters['to'] ?? '') ?: $from;
        $deptId = (int) ($filters['department_id'] ?? 0);
        $uid = (int) ($filters['user_id'] ?? 0);

        $sql = "SELECT
                    u.id AS user_id,
                    u.full_name,
                    u.employee_id,
                    d.name AS department_name,
                    r.name AS role_name,
                    wv.work_date,
                    COALESCE(wv.work_update_count, 0) AS work_update_count,
                    COALESCE(wv.task_update_count, 0) AS task_update_count,
                    COALESCE(wv.work_logged_hours, 0) AS work_logged_hours,
                    COALESCE(wv.work_planned_hours, 0) AS work_planned_hours,
                    COALESCE(wv.total_steps, 0) AS total_steps,
                    COALESCE(wv.completed_steps, 0) AS completed_steps,
                    COALESCE(wv.avg_progress, 0) AS avg_progress,
                    wv.project_names,
                    wv.task_titles,
                    a.check_in_time,
                    a.check_out_time,
                    a.status AS attendance_status,
                    CASE WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL THEN ROUND(TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60, 2) ELSE NULL END AS attendance_hours
                FROM attend_work_daily_v wv
                INNER JOIN attend_users u ON u.id = wv.user_id
                LEFT JOIN attend_departments d ON d.id = u.department_id
                LEFT JOIN attend_roles r ON r.id = u.role_id
                LEFT JOIN attend_attendance a ON a.user_id = wv.user_id AND a.attendance_date = wv.work_date
                WHERE wv.work_date BETWEEN ? AND ? AND u.is_active = 1";
        $params = array($from, $to);
        if (!$this->canManageWork($ctx)) {
            $sql .= ' AND wv.user_id = ?';
            $params[] = (int) ($ctx['user_id'] ?? 0);
        } elseif ($uid > 0) {
            $sql .= ' AND wv.user_id = ?';
            $params[] = $uid;
        }
        if ($deptId > 0) {
            $sql .= ' AND u.department_id = ?';
            $params[] = $deptId;
        }
        $sql .= ' ORDER BY wv.work_date DESC, u.full_name ASC';
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function getAttendanceWorkWeekly(array $filters, array $ctx): array
    {
        $this->ensureSchema();
        $from = $this->safeDate($filters['from'] ?? '') ?: date('Y-m-d', strtotime('monday this week'));
        $to = $this->safeDate($filters['to'] ?? '') ?: date('Y-m-d', strtotime('sunday this week'));
        $deptId = (int) ($filters['department_id'] ?? 0);
        $uid = (int) ($filters['user_id'] ?? 0);
        $sql = "SELECT
                    u.id AS user_id,
                    u.full_name,
                    u.employee_id,
                    d.name AS department_name,
                    wv.week_start,
                    wv.week_end,
                    COALESCE(wv.work_update_count, 0) AS work_update_count,
                    COALESCE(wv.task_update_count, 0) AS task_update_count,
                    COALESCE(wv.work_logged_hours, 0) AS work_logged_hours,
                    COALESCE(wv.work_planned_hours, 0) AS work_planned_hours,
                    COALESCE(wv.total_steps, 0) AS total_steps,
                    COALESCE(wv.completed_steps, 0) AS completed_steps,
                    COALESCE(wv.avg_completion_percent, 0) AS avg_completion_percent,
                    att.present_days,
                    att.total_attendance_hours
                FROM attend_work_weekly_v wv
                INNER JOIN attend_users u ON u.id = wv.user_id
                LEFT JOIN attend_departments d ON d.id = u.department_id
                LEFT JOIN (
                    SELECT a.user_id,
                           DATE_SUB(a.attendance_date, INTERVAL (DAYOFWEEK(a.attendance_date) - 2 + 7) % 7 DAY) AS w_start,
                           COUNT(CASE WHEN a.status = 'present' THEN 1 END) AS present_days,
                           ROUND(SUM(CASE WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time)/60 ELSE 0 END), 2) AS total_attendance_hours
                    FROM attend_attendance a
                    GROUP BY a.user_id, w_start
                ) att ON att.user_id = wv.user_id AND att.w_start = wv.week_start
                WHERE wv.week_start BETWEEN ? AND ? AND u.is_active = 1";
        $params = array($from, $to);
        if (!$this->canManageWork($ctx)) {
            $sql .= ' AND wv.user_id = ?';
            $params[] = (int) ($ctx['user_id'] ?? 0);
        } elseif ($uid > 0) {
            $sql .= ' AND wv.user_id = ?';
            $params[] = $uid;
        }
        if ($deptId > 0) {
            $sql .= ' AND u.department_id = ?';
            $params[] = $deptId;
        }
        $sql .= ' ORDER BY wv.week_start DESC, u.full_name ASC';
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function getAttendanceWorkMonthly(array $filters, array $ctx): array
    {
        $this->ensureSchema();
        $year = (int) ($filters['year'] ?? date('Y'));
        $month = (int) ($filters['month'] ?? date('n'));
        $deptId = (int) ($filters['department_id'] ?? 0);
        $uid = (int) ($filters['user_id'] ?? 0);
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }

        $sql = "SELECT
                    u.id AS user_id,
                    u.full_name,
                    u.employee_id,
                    d.name AS department_name,
                    wv.work_year,
                    wv.work_month,
                    COALESCE(wv.total_updates, 0) AS work_update_count,
                    COALESCE(wv.task_update_count, 0) AS task_update_count,
                    COALESCE(wv.total_work_logged_hours, 0) AS work_logged_hours,
                    COALESCE(wv.total_planned_hours, 0) AS work_planned_hours,
                    COALESCE(wv.total_steps, 0) AS total_steps,
                    COALESCE(wv.completed_steps, 0) AS completed_steps,
                    COALESCE(wv.avg_completion_percent, 0) AS avg_completion_percent,
                    COALESCE(wv.total_attendance_hours, 0) AS total_attendance_hours,
                    att.present_days,
                    att.absent_days,
                    att.wfh_days,
                    att.leave_days
                FROM attend_work_monthly_v wv
                INNER JOIN attend_users u ON u.id = wv.user_id
                LEFT JOIN attend_departments d ON d.id = u.department_id
                LEFT JOIN (
                    SELECT a.user_id, YEAR(a.attendance_date) AS yr, MONTH(a.attendance_date) AS mo,
                           SUM(a.status = 'present') AS present_days,
                           SUM(a.status = 'absent') AS absent_days,
                           SUM(a.status = 'work_from_home') AS wfh_days,
                           SUM(a.status = 'leave') AS leave_days
                    FROM attend_attendance a
                    GROUP BY a.user_id, YEAR(a.attendance_date), MONTH(a.attendance_date)
                ) att ON att.user_id = wv.user_id AND att.yr = wv.work_year AND att.mo = wv.work_month
                WHERE wv.work_year = ? AND wv.work_month = ? AND u.is_active = 1";
        $params = array($year, $month);
        if (!$this->canManageWork($ctx)) {
            $sql .= ' AND wv.user_id = ?';
            $params[] = (int) ($ctx['user_id'] ?? 0);
        } elseif ($uid > 0) {
            $sql .= ' AND wv.user_id = ?';
            $params[] = $uid;
        }
        if ($deptId > 0) {
            $sql .= ' AND u.department_id = ?';
            $params[] = $deptId;
        }
        $sql .= ' ORDER BY u.full_name ASC';
        $rows = $this->db->query($sql, $params)->getResultArray();
        foreach ($rows as &$row) {
            $row['total_updates'] = $row['work_update_count'];
            $row['total_work_logged_hours'] = $row['work_logged_hours'];
            $row['attendance_hours'] = $row['total_attendance_hours'];
            $row['avg_progress'] = $row['avg_completion_percent'];
        }
        unset($row);
        return $rows;
    }

    private function encodeChecklist($items): array
    {
        return $this->encodeList($items, false);
    }

    private function encodeSteps($items): array
    {
        return $this->encodeList($items, true);
    }

    private function encodeList($items, bool $isSteps): array
    {
        if ($items === null || $items === '') {
            return array('json' => null, 'total' => 0, 'done' => 0, 'items' => array());
        }

        $arr = null;
        if (is_array($items)) {
            $arr = $items;
        } elseif (is_string($items)) {
            $text = trim($items);
            $decoded = json_decode($text, true);
            if (is_array($decoded)) {
                $arr = $decoded;
            } else {
                $lines = preg_split('/\r\n|\r|\n/', $text);
                $arr = array();
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }
                    $arr[] = array(
                        $isSteps ? 'step_title' : 'title' => preg_replace('/^\d+[\.\)\-\s]+/', '', $line),
                        'status' => $isSteps ? 'pending' : 'todo',
                    );
                }
            }
        }

        if (!is_array($arr)) {
            return array('json' => null, 'total' => 0, 'done' => 0, 'items' => array());
        }

        $clean = array();
        $done = 0;
        $i = 1;
        foreach ($arr as $item) {
            if (!is_array($item)) {
                $item = array($isSteps ? 'step_title' : 'title' => (string) $item);
            }
            $titleKey = $isSteps ? 'step_title' : 'title';
            $title = $this->cleanText($item[$titleKey] ?? ($item['task'] ?? ($item['text'] ?? ($item['name'] ?? ''))), 220);
            if ($title === '') {
                continue;
            }
            $status = strtolower((string) ($item['step_status'] ?? ($item['status'] ?? 'pending')));
            $isDone = !empty($item['done']) || !empty($item['completed']) || in_array($status, array('done', 'completed'), true);
            if ($isDone) {
                $done++;
            }
            $clean[] = array(
                'no' => (int) ($item['no'] ?? $i),
                $titleKey => $title,
                'description' => $this->cleanLong($item['description'] ?? ($item['step_description'] ?? '')),
                'status' => $isDone ? 'completed' : ($status !== '' ? $status : 'pending'),
                'done' => $isDone ? 1 : 0,
                'planned_hours' => $this->clampFloat($item['planned_hours'] ?? 0, 0, 99),
                'actual_hours' => $this->clampFloat($item['actual_hours'] ?? 0, 0, 99),
                'completion_percent' => $this->clampInt($item['completion_percent'] ?? ($isDone ? 100 : 0), 0, 100),
            );
            $i++;
        }
        return array(
            'json' => !empty($clean) ? json_encode($clean, JSON_UNESCAPED_UNICODE) : null,
            'total' => count($clean),
            'done' => $done,
            'items' => $clean,
        );
    }

    private function decodeJsonList($json)
    {
        if ($json === null || $json === '') {
            return array();
        }
        $decoded = json_decode((string) $json, true);
        return is_array($decoded) ? $decoded : array();
    }

    private function replaceStepRows(int $dailyUpdateId, int $projectId, int $taskId, int $memberId, ?int $attendUserId, string $date, array $steps, array $ctx): void
    {
        $this->db->table('work_update_steps')->where('daily_update_id', $dailyUpdateId)->delete();
        if (empty($steps)) {
            return;
        }
        $now = $this->now();
        $userId = (int) ($ctx['user_id'] ?? 0);
        $no = 1;
        foreach ($steps as $step) {
            $title = $this->cleanText($step['step_title'] ?? ($step['title'] ?? ''), 220);
            if ($title === '') {
                continue;
            }
            $status = $this->normalizeStatus($step['status'] ?? ($step['step_status'] ?? ''), 'pending', array('pending', 'working', 'completed', 'blocked', 'skipped'));
            $done = !empty($step['done']) || $status === 'completed';
            $this->db->table('work_update_steps')->insert(array(
                'daily_update_id' => $dailyUpdateId,
                'project_id' => $projectId,
                'task_id' => $taskId > 0 ? $taskId : null,
                'member_id' => $memberId > 0 ? $memberId : null,
                'attend_user_id' => $attendUserId,
                'step_date' => $date,
                'step_no' => (int) ($step['no'] ?? $no),
                'step_title' => $title,
                'step_description' => $this->cleanLong($step['description'] ?? ''),
                'step_status' => $done ? 'completed' : $status,
                'planned_hours' => $this->clampFloat($step['planned_hours'] ?? 0, 0, 99),
                'actual_hours' => $this->clampFloat($step['actual_hours'] ?? 0, 0, 99),
                'completion_percent' => $this->clampInt($step['completion_percent'] ?? ($done ? 100 : 0), 0, 100),
                'created_by_user_id' => $userId > 0 ? $userId : null,
                'updated_by_user_id' => $userId > 0 ? $userId : null,
                'created_at' => $now,
                'updated_at' => $now,
            ));
            $no++;
        }
    }

    // ------------------------------------------------------------------
    // ATTENDANCE EMPLOYEE / LOGIN METHODS
    // ------------------------------------------------------------------

    // ------------------------------------------------------------------
    // FIND USER BY EMPLOYEE ID (used for login)
    // ------------------------------------------------------------------
    public function findByEmployeeId(string $employeeId): ?array
    {
        $this->setIst();

        $row = $this->db->query(
            '
            SELECT
                u.*,
                r.name AS role_name,
                r.can_edit,
                r.can_view_analytics,
                d.name AS department_name
            FROM attend_users u
            INNER JOIN attend_roles r ON r.id = u.role_id
            LEFT  JOIN attend_departments d ON d.id = u.department_id
            WHERE UPPER(u.employee_id) = ?
              AND u.is_active = 1
            LIMIT 1
            ',
            [strtoupper(trim($employeeId))]
        )->getRowArray();

        return $row ?: null;
    }

    // ------------------------------------------------------------------
    // PASSWORD VERIFICATION
    // Plain-text comparison only. This project currently stores normal passwords
    // inside attend_users.password_hash, so do NOT use password_verify() here.
    // ------------------------------------------------------------------
    public function verifyPassword(string $plain, string $storedPassword): bool
    {
        $plain          = trim((string) $plain);
        $storedPassword = trim((string) $storedPassword);

        if ($plain === '' || $storedPassword === '') {
            return false;
        }

        return hash_equals($storedPassword, $plain);
    }

    // ------------------------------------------------------------------
    // GET ALL ACTIVE EMPLOYEES (optionally filtered by department code)
    // ------------------------------------------------------------------
    public function getAllActive(string $deptCode = ''): array
    {
        $sql = '
            SELECT
                u.id,
                u.employee_id,
                u.full_name,
                u.email,
                u.designation,
                u.is_active,
                r.name  AS role_name,
                r.label AS role_label,
                d.name  AS department_name,
                d.code  AS department_code,
                d.id    AS department_id,
                u.role_id
            FROM attend_users u
            INNER JOIN attend_roles r ON r.id = u.role_id
            LEFT  JOIN attend_departments d ON d.id = u.department_id
            WHERE u.is_active = 1
        ';

        $params = [];

        if (trim($deptCode) !== '') {
            $sql .= ' AND UPPER(d.code) = ?';
            $params[] = strtoupper(trim($deptCode));
        }

        $sql .= ' ORDER BY u.full_name ASC';

        return $this->db->query($sql, $params)->getResultArray();
    }

    // ------------------------------------------------------------------
    // ADD EMPLOYEE
    // Returns ['success'=>bool, 'message'=>string, 'employee_id'=>string]
    // ------------------------------------------------------------------
    public function addEmployee(array $data): array
    {
        $this->setIst();

        // Validate required fields
        $employeeId  = strtoupper(trim((string) ($data['employee_id']  ?? '')));
        $fullName    = trim((string) ($data['full_name']    ?? ''));
        $email       = strtolower(trim((string) ($data['email']       ?? '')));
        $password    = trim((string) ($data['password']    ?? ''));
        $roleId      = (int) ($data['role_id']     ?? 0);
        $deptId      = (int) ($data['department_id'] ?? 0);
        $designation = trim((string) ($data['designation'] ?? ''));

        if ($employeeId === '') {
            return ['success' => false, 'message' => 'Employee ID is required.'];
        }
        if ($fullName === '') {
            return ['success' => false, 'message' => 'Full name is required.'];
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'A valid email address is required.'];
        }
        if ($password === '' || strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }
        if ($roleId <= 0) {
            return ['success' => false, 'message' => 'Please select a valid role.'];
        }

        // Check for duplicate Employee ID
        $dup = $this->db->query(
            'SELECT id FROM attend_users WHERE UPPER(employee_id) = ? LIMIT 1',
            [$employeeId]
        )->getRowArray();

        if ($dup) {
            return ['success' => false, 'message' => "Employee ID '{$employeeId}' already exists."];
        }

        // Check for duplicate email
        $dupEmail = $this->db->query(
            'SELECT id FROM attend_users WHERE LOWER(email) = ? LIMIT 1',
            [$email]
        )->getRowArray();

        if ($dupEmail) {
            return ['success' => false, 'message' => 'This email address is already registered.'];
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('attend_users')->insert([
            'employee_id'   => $employeeId,
            'full_name'     => $fullName,
            'email'         => $email,
            'password_hash' => $password,   // plain-text as per existing schema
            'role_id'       => $roleId,
            'department_id' => $deptId > 0 ? $deptId : null,
            'designation'   => $designation !== '' ? $designation : null,
            'is_active'     => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        if ($this->db->affectedRows() === 0) {
            return ['success' => false, 'message' => 'Failed to add employee. Please try again.'];
        }

        return [
            'success'     => true,
            'message'     => "Employee {$fullName} ({$employeeId}) added successfully.",
            'employee_id' => $employeeId,
        ];
    }


    // ------------------------------------------------------------------
    // FIND ACTIVE USER BY INTERNAL ID (used by Work integration sync)
    // ------------------------------------------------------------------
    public function findActiveById(int $id): ?array
    {
        $this->setIst();

        if ($id <= 0) {
            return null;
        }

        $row = $this->db->query(
            '
            SELECT
                u.*,
                r.name AS role_name,
                r.label AS role_label,
                r.can_edit,
                r.can_view_analytics,
                d.name AS department_name,
                d.code AS department_code
            FROM attend_users u
            INNER JOIN attend_roles r ON r.id = u.role_id
            LEFT  JOIN attend_departments d ON d.id = u.department_id
            WHERE u.id = ?
              AND u.is_active = 1
            LIMIT 1
            ',
            [$id]
        )->getRowArray();

        return $row ?: null;
    }

    // ------------------------------------------------------------------
    // FIND ACTIVE USER BY EMAIL (used for safe attendance ↔ work matching)
    // ------------------------------------------------------------------
    public function findActiveByEmail(string $email): ?array
    {
        $this->setIst();

        $email = strtolower(trim($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $row = $this->db->query(
            '
            SELECT
                u.*,
                r.name AS role_name,
                r.label AS role_label,
                r.can_edit,
                r.can_view_analytics,
                d.name AS department_name,
                d.code AS department_code
            FROM attend_users u
            INNER JOIN attend_roles r ON r.id = u.role_id
            LEFT  JOIN attend_departments d ON d.id = u.department_id
            WHERE LOWER(u.email) = ?
              AND u.is_active = 1
            LIMIT 1
            ',
            [$email]
        )->getRowArray();

        return $row ?: null;
    }

    // ------------------------------------------------------------------
    // GET ROLES
    // ------------------------------------------------------------------
    public function getRoles(): array
    {
        return $this->db->query(
            'SELECT id, name, label FROM attend_roles ORDER BY id ASC'
        )->getResultArray();
    }

    // ------------------------------------------------------------------
    // GET DEPARTMENTS
    // ------------------------------------------------------------------
    public function getDepartments(): array
    {
        return $this->db->query(
            'SELECT id, name, code FROM attend_departments ORDER BY name ASC'
        )->getResultArray();
    }

    // ------------------------------------------------------------------
    // ATTENDANCE PUNCH / REPORTING METHODS
    // ------------------------------------------------------------------

    private function nowIst(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function todayIst(): string
    {
        return date('Y-m-d');
    }

    public function checkIn(int $userId, array $location): array
    {
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Invalid session. Please login again.');
        }

        $this->setIst();

        $today = $this->todayIst();
        $now = $this->nowIst();
        $existing = $this->getTodayRecord($userId);

        if ($existing && !empty($existing['check_in_time'])) {
            return array('success' => false, 'message' => 'You have already checked in today.');
        }

        $payload = array(
            'check_in_time' => $now,
            'status' => 'present',
            'latitude' => $location['latitude'] ?? null,
            'longitude' => $location['longitude'] ?? null,
            'location_name' => $location['name'] ?? null,
            'ip_address' => $location['ip'] ?? null,
            'device_info' => substr((string) ($location['device'] ?? ''), 0, 255),
            'updated_at' => $now,
        );

        if ($existing) {
            $this->db->table($this->table)
                ->where('user_id', $userId)
                ->where('attendance_date', $today)
                ->update($payload);
        } else {
            $payload['user_id'] = $userId;
            $payload['attendance_date'] = $today;
            $payload['created_at'] = $now;
            $this->db->table($this->table)->insert($payload);
        }

        $this->linkTodayWorkUpdates($userId, $today);

        return array(
            'success' => true,
            'message' => 'Check-in recorded at ' . date('h:i A') . ' IST.',
            'record' => $this->getTodayRecord($userId),
        );
    }

    public function checkOut(int $userId): array
    {
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Invalid session. Please login again.');
        }

        $this->setIst();

        $today = $this->todayIst();
        $now = $this->nowIst();
        $existing = $this->getTodayRecord($userId);

        if (!$existing || empty($existing['check_in_time'])) {
            return array('success' => false, 'message' => 'You have not checked in yet today.');
        }

        if (!empty($existing['check_out_time'])) {
            return array('success' => false, 'message' => 'You have already checked out today.');
        }

        $this->db->table($this->table)
            ->where('user_id', $userId)
            ->where('attendance_date', $today)
            ->update(array(
                'check_out_time' => $now,
                'updated_at' => $now,
            ));

        $this->linkTodayWorkUpdates($userId, $today);

        return array(
            'success' => true,
            'message' => 'Check-out recorded at ' . date('h:i A') . ' IST.',
            'record' => $this->getTodayRecord($userId),
        );
    }

    public function editAttendance(int $targetUserId, string $date, array $data, int $editorId): array
    {
        if ($targetUserId <= 0 || $editorId <= 0) {
            return array('success' => false, 'message' => 'Invalid user/editor.');
        }

        $this->setIst();
        $now = $this->nowIst();

        $this->db->transBegin();

        try {
            $existing = $this->db->table($this->table)
                ->where('user_id', $targetUserId)
                ->where('attendance_date', $date)
                ->get()
                ->getRowArray();

            $payload = array(
                'status' => $data['status'] ?? 'present',
                'check_in_time' => $data['check_in'] ?? null,
                'check_out_time' => $data['check_out'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'is_manual_entry' => 1,
                'edited_by' => $editorId,
                'edited_at' => $now,
                'updated_at' => $now,
            );

            if ($existing) {
                $this->logEdit($existing, $data, $editorId);
                $this->db->table($this->table)
                    ->where('user_id', $targetUserId)
                    ->where('attendance_date', $date)
                    ->update($payload);
            } else {
                $payload['user_id'] = $targetUserId;
                $payload['attendance_date'] = $date;
                $payload['created_at'] = $now;
                $this->db->table($this->table)->insert($payload);
            }

            $this->linkTodayWorkUpdates($targetUserId, $date);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return array('success' => false, 'message' => 'Unable to update attendance.');
            }

            $this->db->transCommit();
            return array('success' => true, 'message' => 'Attendance updated successfully.');
        } catch (Throwable $e) {
            $this->db->transRollback();
            return array('success' => false, 'message' => 'Unable to update attendance.');
        }
    }

    public function getTodayRecord(int $userId): ?array
    {
        $this->setIst();

        $row = $this->db->query(
            '
            SELECT
                a.*,
                CASE
                    WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                    THEN ROUND(TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60, 2)
                    ELSE NULL
                END AS work_hours,
                COALESCE(wv.work_update_count, 0) AS work_update_count,
                COALESCE(wv.work_logged_hours, 0) AS work_logged_hours,
                COALESCE(wv.work_planned_hours, 0) AS work_planned_hours,
                COALESCE(wv.total_steps, 0) AS work_total_steps,
                COALESCE(wv.completed_steps, 0) AS work_completed_steps,
                wv.project_names AS work_project_names
            FROM attend_attendance a
            LEFT JOIN attend_work_daily_v wv
                   ON wv.user_id = a.user_id
                  AND wv.work_date = a.attendance_date
            WHERE a.user_id = ?
              AND a.attendance_date = CURDATE()
            LIMIT 1
            ',
            array($userId)
        )->getRowArray();

        return $row ?: null;
    }

    public function getUserAttendance(int $userId, string $from, string $to): array
    {
        $this->setIst();

        return $this->db->query(
            '
            SELECT
                a.*,
                u.full_name,
                u.employee_id,
                CASE
                    WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                    THEN ROUND(TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60, 2)
                    ELSE NULL
                END AS work_hours,
                COALESCE(wv.work_update_count, 0) AS work_update_count,
                COALESCE(wv.work_logged_hours, 0) AS work_logged_hours,
                COALESCE(wv.total_steps, 0) AS work_total_steps,
                COALESCE(wv.completed_steps, 0) AS work_completed_steps,
                wv.project_names AS work_project_names
            FROM attend_attendance a
            INNER JOIN attend_users u ON u.id = a.user_id
            LEFT JOIN attend_work_daily_v wv
                   ON wv.user_id = a.user_id
                  AND wv.work_date = a.attendance_date
            WHERE a.user_id = ?
              AND a.attendance_date BETWEEN ? AND ?
            ORDER BY a.attendance_date DESC
            ',
            array($userId, $from, $to)
        )->getResultArray();
    }

    public function getAllAttendance(string $from, string $to, int $deptId = 0): array
    {
        $this->setIst();

        if ($from === $to) {
            return $this->getDailyAttendanceForManage($from, $deptId);
        }

        $sql = '
            SELECT
                a.id AS id,
                a.user_id AS attendance_user_id,
                a.attendance_date,
                a.check_in_time,
                a.check_out_time,
                a.status,
                a.latitude, a.longitude, a.location_name,
                a.ip_address, a.device_info,
                a.remarks, a.is_manual_entry, a.edited_by, a.edited_at,
                a.created_at, a.updated_at,
                u.id AS user_id,
                u.full_name, u.employee_id,
                r.name AS role_name,
                d.name AS department_name,
                CASE
                    WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                    THEN ROUND(TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60, 2)
                    ELSE NULL
                END AS work_hours,
                COALESCE(wv.work_update_count, 0) AS work_update_count,
                COALESCE(wv.work_logged_hours, 0) AS work_logged_hours,
                COALESCE(wv.total_steps, 0) AS work_total_steps,
                COALESCE(wv.completed_steps, 0) AS work_completed_steps,
                wv.project_names AS work_project_names
            FROM attend_attendance a
            INNER JOIN attend_users u ON u.id = a.user_id
            INNER JOIN attend_roles r ON r.id = u.role_id
            LEFT JOIN attend_departments d ON d.id = u.department_id
            LEFT JOIN attend_work_daily_v wv
                   ON wv.user_id = a.user_id
                  AND wv.work_date = a.attendance_date
            WHERE a.attendance_date BETWEEN ? AND ?
              AND u.is_active = 1
        ';

        $params = array($from, $to);

        if ($deptId > 0) {
            $sql .= ' AND u.department_id = ?';
            $params[] = $deptId;
        }

        $sql .= ' ORDER BY a.attendance_date DESC, u.full_name ASC';

        return $this->db->query($sql, $params)->getResultArray();
    }

    private function getDailyAttendanceForManage(string $date, int $deptId = 0): array
    {
        $sql = '
            SELECT
                a.id AS id,
                a.user_id AS attendance_user_id,
                COALESCE(a.attendance_date, ?) AS attendance_date,
                a.check_in_time,
                a.check_out_time,
                a.status,
                a.latitude, a.longitude, a.location_name,
                a.ip_address, a.device_info,
                a.remarks, a.is_manual_entry, a.edited_by, a.edited_at,
                a.created_at, a.updated_at,
                u.id AS user_id,
                u.full_name, u.employee_id,
                r.name AS role_name,
                d.name AS department_name,
                CASE
                    WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                    THEN ROUND(TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60, 2)
                    ELSE NULL
                END AS work_hours,
                COALESCE(wv.work_update_count, 0) AS work_update_count,
                COALESCE(wv.work_logged_hours, 0) AS work_logged_hours,
                COALESCE(wv.total_steps, 0) AS work_total_steps,
                COALESCE(wv.completed_steps, 0) AS work_completed_steps,
                wv.project_names AS work_project_names
            FROM attend_users u
            INNER JOIN attend_roles r ON r.id = u.role_id
            LEFT JOIN attend_departments d ON d.id = u.department_id
            LEFT JOIN attend_attendance a
                    ON a.user_id = u.id
                   AND a.attendance_date = ?
            LEFT JOIN attend_work_daily_v wv
                   ON wv.user_id = u.id
                  AND wv.work_date = ?
            WHERE u.is_active = 1
        ';

        $params = array($date, $date, $date);

        if ($deptId > 0) {
            $sql .= ' AND u.department_id = ?';
            $params[] = $deptId;
        }

        $sql .= ' ORDER BY u.full_name ASC';

        return $this->db->query($sql, $params)->getResultArray();
    }

    public function getMonthlySummary(int $year, int $month, int $userId = 0): array
    {
        $this->setIst();

        $from = sprintf('%04d-%02d-01', $year, $month);
        $to = date('Y-m-t', strtotime($from));

        $sql = '
            SELECT
                u.id AS user_id,
                u.employee_id,
                u.full_name,
                COALESCE(d.name, "N/A") AS department,
                COALESCE(SUM(a.status = "present"), 0) AS present,
                COALESCE(SUM(a.status = "absent"), 0) AS absent,
                COALESCE(SUM(a.status = "half_day"), 0) AS half_day,
                COALESCE(SUM(a.status = "work_from_home"), 0) AS wfh,
                COALESCE(SUM(a.status = "leave"), 0) AS on_leave,
                ROUND(AVG(CASE
                    WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                    THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60
                    ELSE NULL
                END), 2) AS avg_work_hours,
                ROUND(SUM(CASE
                    WHEN a.check_in_time IS NOT NULL AND a.check_out_time IS NOT NULL
                    THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time) / 60
                    ELSE 0
                END), 2) AS total_work_hours,
                COALESCE(wm.total_updates, 0) AS work_updates,
                COALESCE(wm.total_work_logged_hours, 0) AS work_logged_hours,
                COALESCE(wm.total_steps, 0) AS work_total_steps,
                COALESCE(wm.completed_steps, 0) AS work_completed_steps
            FROM attend_users u
            LEFT JOIN attend_departments d ON d.id = u.department_id
            LEFT JOIN attend_attendance a
                   ON a.user_id = u.id
                  AND a.attendance_date BETWEEN ? AND ?
            LEFT JOIN attend_work_monthly_v wm
                   ON wm.user_id = u.id
                  AND wm.work_year = ?
                  AND wm.work_month = ?
            WHERE u.is_active = 1
        ';

        $params = array($from, $to, $year, $month);

        if ($userId > 0) {
            $sql .= ' AND u.id = ?';
            $params[] = $userId;
        }

        $sql .= ' GROUP BY u.id, u.employee_id, u.full_name, d.name, wm.total_updates, wm.total_work_logged_hours, wm.total_steps, wm.completed_steps ORDER BY u.full_name ASC';

        return $this->db->query($sql, $params)->getResultArray();
    }

    public function getTodaySnapshot(): array
    {
        $this->setIst();
        return $this->getDailyAttendanceForManage($this->todayIst(), 0);
    }

    public function getDeptSummary(string $date): array
    {
        $this->setIst();

        return $this->db->query(
            '
            SELECT
                COALESCE(d.name, "N/A") AS department,
                COUNT(DISTINCT u.id) AS total_employees,
                COALESCE(SUM(a.status = "present"), 0) AS present,
                COALESCE(SUM(a.status = "absent"), 0) AS absent,
                COALESCE(SUM(a.status = "half_day"), 0) AS half_day,
                COALESCE(SUM(a.status = "work_from_home"), 0) AS wfh,
                COALESCE(SUM(a.status = "leave"), 0) AS on_leave,
                COALESCE(SUM(a.status = "holiday"), 0) AS holiday,
                COALESCE(SUM(a.id IS NULL), 0) AS not_marked,
                COALESCE(SUM(wv.work_update_count), 0) AS work_updates,
                COALESCE(SUM(wv.work_logged_hours), 0) AS work_logged_hours
            FROM attend_users u
            LEFT JOIN attend_departments d ON d.id = u.department_id
            LEFT JOIN attend_attendance a
                   ON a.user_id = u.id
                  AND a.attendance_date = ?
            LEFT JOIN attend_work_daily_v wv
                   ON wv.user_id = u.id
                  AND wv.work_date = ?
            WHERE u.is_active = 1
            GROUP BY d.id, d.name
            ORDER BY d.name ASC
            ',
            array($date, $date)
        )->getResultArray();
    }

    public function getAttendanceTrend(int $days = 30): array
    {
        $this->setIst();
        $days = max(1, min(365, $days));

        return $this->db->query(
            '
            SELECT
                attendance_date,
                COALESCE(SUM(status = "present"), 0) AS present,
                COALESCE(SUM(status = "absent"), 0) AS absent,
                COALESCE(SUM(status = "half_day"), 0) AS half_day,
                COALESCE(SUM(status = "work_from_home"), 0) AS wfh,
                COALESCE(SUM(status = "leave"), 0) AS on_leave,
                COALESCE(SUM(status = "holiday"), 0) AS holiday,
                COUNT(*) AS marked
            FROM attend_attendance
            WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)
            GROUP BY attendance_date
            ORDER BY attendance_date ASC
            '
        )->getResultArray();
    }

    public function getIntegratedDaily(string $from, string $to, int $deptId = 0, int $userId = 0): array
    {
        $this->setIst();

        $builder = $this->db->table('attend_work_daily_v')
            ->where('work_date >=', $from)
            ->where('work_date <=', $to);

        if ($deptId > 0) {
            $builder->where('department_id', $deptId);
        }
        if ($userId > 0) {
            $builder->where('user_id', $userId);
        }

        return $builder->orderBy('work_date', 'DESC')->orderBy('full_name', 'ASC')->get()->getResultArray();
    }

    public function getIntegratedWeekly(string $from, string $to, int $deptId = 0, int $userId = 0): array
    {
        $this->setIst();

        $builder = $this->db->table('attend_work_weekly_v')
            ->where('week_start >=', $from)
            ->where('week_start <=', $to);

        if ($deptId > 0) {
            $builder->where('department_id', $deptId);
        }
        if ($userId > 0) {
            $builder->where('user_id', $userId);
        }

        return $builder->orderBy('week_start', 'DESC')->orderBy('full_name', 'ASC')->get()->getResultArray();
    }

    public function getIntegratedMonthly(int $year, int $month, int $deptId = 0, int $userId = 0): array
    {
        $this->setIst();

        $builder = $this->db->table('attend_work_monthly_v')
            ->where('work_year', $year)
            ->where('work_month', $month);

        if ($deptId > 0) {
            $builder->where('department_id', $deptId);
        }
        if ($userId > 0) {
            $builder->where('user_id', $userId);
        }

        $rows = $builder->orderBy('full_name', 'ASC')->get()->getResultArray();
        foreach ($rows as &$row) {
            $row['attendance_hours'] = $row['total_attendance_hours'] ?? 0;
            $row['work_update_count'] = $row['total_updates'] ?? 0;
            $row['work_logged_hours'] = $row['total_work_logged_hours'] ?? 0;
            $row['avg_progress'] = $row['avg_completion_percent'] ?? 0;
        }
        unset($row);
        return $rows;
    }

    private function linkTodayWorkUpdates(int $userId, string $date): void
    {
        try {
            $attendance = $this->db->table('attend_attendance')
                ->select('id')
                ->where('user_id', $userId)
                ->where('attendance_date', $date)
                ->get(1)
                ->getRowArray();

            if (!$attendance) {
                return;
            }

            $this->db->table('work_daily_updates')
                ->where('attend_user_id', $userId)
                ->where('update_date', $date)
                ->update(array(
                    'attendance_id' => (int) $attendance['id'],
                    'updated_at' => $this->nowIst(),
                ));
        } catch (Throwable $e) {
            // Work linkage must never block attendance punch/edit flow.
        }
    }

    private function logEdit(array $old, array $new, int $editorId): void
    {
        try {
            $this->db->table('attend_edit_log')->insert(array(
                'attendance_id' => $old['id'] ?? null,
                'changed_by' => $editorId,
                'old_status' => $old['status'] ?? null,
                'new_status' => $new['status'] ?? ($old['status'] ?? null),
                'old_check_in' => $old['check_in_time'] ?? null,
                'new_check_in' => $new['check_in'] ?? ($old['check_in_time'] ?? null),
                'old_check_out' => $old['check_out_time'] ?? null,
                'new_check_out' => $new['check_out'] ?? ($old['check_out_time'] ?? null),
                'old_remarks' => $old['remarks'] ?? null,
                'new_remarks' => $new['remarks'] ?? ($old['remarks'] ?? null),
                'changed_at' => $this->nowIst(),
            ));
        } catch (Throwable $e) {
            // Audit logging must never block a valid correction.
        }
    }
}