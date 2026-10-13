-- =====================================================================
-- GRID Platform - High-Performance Database Schema
-- Architecture: CodeIgniter 4 / MySQL 5.7+ / MariaDB
-- Time Complexity Target: O(1) primary/hash lookups, O(log N) composite indexes
-- Space Complexity Target: 3NF Normalized, lean integer types, zero redundant bloat
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. PROFILES TABLE: grid_profiles
-- Extends CRM users table without modifying legacy schema
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_profiles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `grid_id` VARCHAR(48) NULL UNIQUE,
    `avatar` VARCHAR(255) NULL,
    `headline` VARCHAR(255) NULL DEFAULT 'Software Engineer & Builder',
    `bio` TEXT NULL,
    `role` ENUM('candidate', 'recruiter') NOT NULL DEFAULT 'candidate',
    `is_open_to_work` TINYINT(1) NOT NULL DEFAULT 1,
    `is_hiring` TINYINT(1) NOT NULL DEFAULT 0,
    `location` VARCHAR(128) NULL DEFAULT 'Hyderabad, India',
    `website` VARCHAR(255) NULL,
    `github_url` VARCHAR(255) NULL,
    `linkedin_url` VARCHAR(255) NULL,
    `custom_card_slug` VARCHAR(64) NULL,
    `profile_views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `search_appearances_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_user_id` (`user_id`),
    UNIQUE KEY `uniq_card_slug` (`custom_card_slug`),
    INDEX `idx_role_status` (`role`, `is_open_to_work`, `is_hiring`),
    INDEX `idx_location` (`location`),
    INDEX `idx_grid_id` (`grid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. MASTER SKILLS DICTIONARY: grid_skills
-- Standard taxonomy for instant O(1) autocomplete & matching
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_skills` (
    `id` SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(64) NOT NULL,
    `category` ENUM('Engineering', 'AI & Data', 'Design', 'Cloud & DevOps', 'Product & Management', 'Other') NOT NULL DEFAULT 'Engineering',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_skill_name` (`name`),
    INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. USER SKILLS & ENDORSEMENTS: grid_user_skills
-- Pivot table with O(1) unique constraint and endorsement counts
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_user_skills` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `skill_id` SMALLINT UNSIGNED NOT NULL,
    `endorsements_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_user_skill` (`user_id`, `skill_id`),
    INDEX `idx_skill_user` (`skill_id`, `user_id`),
    INDEX `idx_user_endorsements` (`user_id`, `endorsements_count` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. EXPERIENCE TIMELINE: grid_experiences
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_experiences` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NULL, -- Linked if company page exists
    `company_name` VARCHAR(128) NOT NULL,
    `title` VARCHAR(128) NOT NULL,
    `employment_type` ENUM('Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance') NOT NULL DEFAULT 'Full-time',
    `location` VARCHAR(128) NULL,
    `is_current` TINYINT(1) NOT NULL DEFAULT 0,
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL,
    `description` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_user_timeline` (`user_id`, `start_date` DESC),
    INDEX `idx_company` (`company_id`, `is_current`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. EDUCATION TIMELINE: grid_education
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_education` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `school_name` VARCHAR(128) NOT NULL,
    `degree` VARCHAR(128) NOT NULL,
    `field_of_study` VARCHAR(128) NULL,
    `start_year` SMALLINT UNSIGNED NOT NULL,
    `end_year` SMALLINT UNSIGNED NULL,
    `grade` VARCHAR(32) NULL,
    `activities` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_user_edu` (`user_id`, `start_year` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. NETWORK CONNECTIONS GRAPH: grid_connections
-- Bidirectional relations with canonical LEAST/GREATEST ordering.
-- Guaranteed O(1) duplicate prevention and instant edge lookup.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_connections` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_one_id` INT UNSIGNED NOT NULL, -- Always LEAST(requester, receiver)
    `user_two_id` INT UNSIGNED NOT NULL, -- Always GREATEST(requester, receiver)
    `action_user_id` INT UNSIGNED NOT NULL, -- Who sent or last touched the request
    `status` ENUM('pending', 'accepted', 'declined', 'blocked') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_connection_pair` (`user_one_id`, `user_two_id`),
    INDEX `idx_user_one_status` (`user_one_id`, `status`),
    INDEX `idx_user_two_status` (`user_two_id`, `status`),
    INDEX `idx_action_user` (`action_user_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. ONE-WAY FOLLOWS (People, Companies, Newsletters): grid_follows
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_follows` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `follower_id` INT UNSIGNED NOT NULL,
    `target_type` ENUM('user', 'company', 'newsletter', 'space') NOT NULL DEFAULT 'user',
    `target_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_follower_target` (`follower_id`, `target_type`, `target_id`),
    INDEX `idx_target_lookup` (`target_type`, `target_id`, `follower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. COMPANY PAGES: grid_companies
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_companies` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `creator_user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `slug` VARCHAR(64) NOT NULL,
    `tagline` VARCHAR(255) NULL,
    `about` TEXT NULL,
    `industry` VARCHAR(64) NOT NULL DEFAULT 'Technology',
    `company_size` VARCHAR(32) NOT NULL DEFAULT '50-200 employees',
    `website` VARCHAR(255) NULL,
    `logo_url` VARCHAR(255) NULL,
    `cover_url` VARCHAR(255) NULL,
    `followers_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_company_slug` (`slug`),
    INDEX `idx_creator` (`creator_user_id`),
    INDEX `idx_industry` (`industry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. JOB POSTINGS: grid_jobs
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_jobs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `recruiter_user_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NULL,
    `company_name` VARCHAR(128) NOT NULL,
    `title` VARCHAR(128) NOT NULL,
    `discipline` VARCHAR(64) NOT NULL DEFAULT 'Engineering',
    `workplace_type` ENUM('Remote', 'Hybrid', 'Onsite') NOT NULL DEFAULT 'Remote',
    `location` VARCHAR(128) NOT NULL DEFAULT 'Remote / Global',
    `job_type` ENUM('Full-time', 'Part-time', 'Contract', 'Internship') NOT NULL DEFAULT 'Full-time',
    `experience_level` ENUM('Entry', 'Mid-Level', 'Senior', 'Lead', 'Executive') NOT NULL DEFAULT 'Senior',
    `salary_min` INT UNSIGNED NULL,
    `salary_max` INT UNSIGNED NULL,
    `salary_currency` VARCHAR(8) NOT NULL DEFAULT 'USD',
    `description` MEDIUMTEXT NOT NULL,
    `required_skills_csv` VARCHAR(512) NULL, -- Denormalized for fast O(1) token match
    `applicants_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('active', 'paused', 'closed') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_job_status_date` (`status`, `created_at` DESC),
    INDEX `idx_recruiter_jobs` (`recruiter_user_id`, `status`),
    INDEX `idx_company_jobs` (`company_id`, `status`),
    INDEX `idx_discipline` (`discipline`, `experience_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. JOB APPLICATIONS & TRACKER: grid_job_applications
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grid_job_applications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `job_id` INT UNSIGNED NOT NULL,
    `candidate_user_id` INT UNSIGNED NOT NULL,
    `stage` ENUM('applied', 'reviewing', 'screened', 'interviewing', 'offered', 'rejected') NOT NULL DEFAULT 'applied',
    `cover_note` TEXT NULL,
    `resume_url` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uniq_candidate_job` (`job_id`, `candidate_user_id`),
    INDEX `idx_candidate_tracker` (`candidate_user_id`, `stage`, `updated_at` DESC),
    INDEX `idx_job_pipeline` (`job_id`, `stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SEED DATA: Standard Tech Skills Master Taxonomy
-- =====================================================================
INSERT IGNORE INTO `grid_skills` (`name`, `category`) VALUES
-- AI & Data
('Python', 'AI & Data'),
('PyTorch', 'AI & Data'),
('TensorFlow', 'AI & Data'),
('LangChain', 'AI & Data'),
('LlamaIndex', 'AI & Data'),
('Machine Learning', 'AI & Data'),
('Natural Language Processing', 'AI & Data'),
('Computer Vision', 'AI & Data'),
('Data Engineering', 'AI & Data'),
('Pandas & NumPy', 'AI & Data'),

-- Engineering
('JavaScript', 'Engineering'),
('TypeScript', 'Engineering'),
('React.js', 'Engineering'),
('Next.js', 'Engineering'),
('Node.js', 'Engineering'),
('PHP', 'Engineering'),
('CodeIgniter', 'Engineering'),
('Laravel', 'Engineering'),
('Go (Golang)', 'Engineering'),
('Rust', 'Engineering'),
('C++', 'Engineering'),
('Java', 'Engineering'),
('PostgreSQL', 'Engineering'),
('MySQL', 'Engineering'),
('Redis', 'Engineering'),
('GraphQL', 'Engineering'),
('RESTful APIs', 'Engineering'),
('System Architecture', 'Engineering'),

-- Cloud & DevOps
('Docker', 'Cloud & DevOps'),
('Kubernetes', 'Cloud & DevOps'),
('Amazon Web Services (AWS)', 'Cloud & DevOps'),
('Google Cloud Platform (GCP)', 'Cloud & DevOps'),
('Microsoft Azure', 'Cloud & DevOps'),
('CI/CD Pipelines', 'Cloud & DevOps'),
('Linux System Administration', 'Cloud & DevOps'),
('Terraform', 'Cloud & DevOps'),

-- Design & Product
('UI/UX Design', 'Design'),
('Figma', 'Design'),
('Design Systems', 'Design'),
('Tailwind CSS', 'Design'),
('Product Management', 'Product & Management'),
('Agile Methodologies', 'Product & Management'),
('Scrum', 'Product & Management');

-- ---------------------------------------------------------------------
-- 12. CIRCLE CONNECT: grid_conversations & grid_messages
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `grid_conversations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_one_id` INT UNSIGNED NOT NULL,
    `user_two_id` INT UNSIGNED NOT NULL,
    `last_message` TEXT NULL,
    `last_message_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_canonical_conv` (`user_one_id`, `user_two_id`),
    INDEX `idx_user_one` (`user_one_id`),
    INDEX `idx_user_two` (`user_two_id`),
    INDEX `idx_last_message_at` (`last_message_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `grid_messages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT UNSIGNED NOT NULL,
    `sender_id` INT UNSIGNED NOT NULL,
    `recipient_id` INT UNSIGNED NOT NULL,
    `message_body` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_conversation` (`conversation_id`, `id`),
    INDEX `idx_unread` (`recipient_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
