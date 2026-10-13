<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_sections_model
 * Manages 1-to-Many relational profile sections for users in grid_db:
 * - Experiences
 * - Education
 * - Certifications
 * - Projects
 * - Honors & Awards
 * - Languages
 * - Featured Artifacts
 */
class Grid_sections_model extends Grid_base_model {

    function __construct() {
        parent::__construct('grid_experiences');
    }

    // ==========================================
    // 1. EXPERIENCES (1:N)
    // ==========================================
    public function get_user_experiences($user_id) {
        $rows = $this->db->table('grid_experiences')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['isCurrent'] = !empty($row['is_current']);
            $bullets = json_decode($row['bullets'] ?? '', true);
            $row['bullets'] = is_array($bullets) ? $bullets : array_filter(array_map('trim', explode("\n", (string)($row['bullets'] ?? ''))));
            $tags = json_decode($row['tags'] ?? '', true);
            $row['tags'] = is_array($tags) ? $tags : array_filter(array_map('trim', explode(',', (string)($row['tags'] ?? ''))));
            $row['iconClass'] = $row['icon_class'] ?: 'g-exp-icon-brand';
        }
        return $rows;
    }

    public function save_user_experience($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'title' => substr(trim($data['title'] ?? ''), 0, 128),
            'company' => substr(trim($data['company'] ?? ''), 0, 128),
            'dates' => substr(trim($data['dates'] ?? ''), 0, 64),
            'is_current' => !empty($data['isCurrent']) || !empty($data['is_current']) ? 1 : 0,
            'bullets' => isset($data['bullets']) ? (is_array($data['bullets']) ? json_encode(array_values($data['bullets'])) : (string)$data['bullets']) : null,
            'tags' => isset($data['tags']) ? (is_array($data['tags']) ? json_encode(array_values($data['tags'])) : (string)$data['tags']) : null,
            'icon' => substr(trim($data['icon'] ?? ''), 0, 16),
            'icon_class' => substr(trim($data['iconClass'] ?? $data['icon_class'] ?? 'g-exp-icon-brand'), 0, 64)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_experiences')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_experiences')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_experience($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_experiences')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }

    // ==========================================
    // 2. EDUCATION (1:N)
    // ==========================================
    public function get_user_educations($user_id) {
        $rows = $this->db->table('grid_education')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['name'] = $row['school_name'];
            $row['iconClass'] = $row['icon_class'] ?: 'g-edu-icon-brand';
        }
        return $rows;
    }

    public function save_user_education($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'school_name' => substr(trim($data['school_name'] ?? $data['name'] ?? ''), 0, 128),
            'degree' => substr(trim($data['degree'] ?? ''), 0, 128),
            'dates' => substr(trim($data['dates'] ?? ''), 0, 64),
            'activities' => trim((string)($data['activities'] ?? '')),
            'icon' => substr(trim($data['icon'] ?? ''), 0, 16),
            'icon_class' => substr(trim($data['iconClass'] ?? $data['icon_class'] ?? 'g-edu-icon-brand'), 0, 64)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_education')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_education')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_education($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_education')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }

    // ==========================================
    // 3. CERTIFICATIONS (1:N)
    // ==========================================
    public function get_user_certifications($user_id) {
        $rows = $this->db->table('grid_certifications')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['credId'] = $row['credential_id'];
            $row['url'] = $row['credential_url'];
            $row['iconClass'] = $row['icon_class'] ?: 'g-cert-icon-amber';
        }
        return $rows;
    }

    public function save_user_certification($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'name' => substr(trim($data['name'] ?? ''), 0, 128),
            'issuer' => substr(trim($data['issuer'] ?? ''), 0, 128),
            'credential_id' => substr(trim($data['credId'] ?? $data['credential_id'] ?? ''), 0, 128),
            'credential_url' => substr(trim($data['url'] ?? $data['credential_url'] ?? ''), 0, 255),
            'dates' => substr(trim($data['dates'] ?? ''), 0, 64),
            'icon' => substr(trim($data['icon'] ?? ''), 0, 16),
            'icon_class' => substr(trim($data['iconClass'] ?? $data['icon_class'] ?? 'g-cert-icon-amber'), 0, 64)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_certifications')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_certifications')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_certification($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_certifications')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }

    // ==========================================
    // 4. PROJECTS (1:N)
    // ==========================================
    public function get_user_projects($user_id) {
        $rows = $this->db->table('grid_projects')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['desc'] = $row['description'];
            $row['badgeClass'] = $row['badge_class'] ?: 'g-proj-badge-green';
            $tags = json_decode($row['tags'] ?? '', true);
            $row['tags'] = is_array($tags) ? $tags : array_filter(array_map('trim', explode(',', (string)($row['tags'] ?? ''))));
        }
        return $rows;
    }

    public function save_user_project($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'name' => substr(trim($data['name'] ?? ''), 0, 128),
            'dates' => substr(trim($data['dates'] ?? $data['date'] ?? ''), 0, 64),
            'badge' => substr(trim($data['badge'] ?? 'Live Demo'), 0, 64),
            'badge_class' => substr(trim($data['badgeClass'] ?? $data['badge_class'] ?? 'g-proj-badge-green'), 0, 64),
            'description' => trim((string)($data['desc'] ?? $data['description'] ?? '')),
            'tags' => isset($data['tags']) ? (is_array($data['tags']) ? json_encode(array_values($data['tags'])) : (string)$data['tags']) : null,
            'project_url' => substr(trim($data['project_url'] ?? $data['url'] ?? ''), 0, 255)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_projects')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_projects')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_project($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_projects')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }

    // ==========================================
    // 5. HONORS & AWARDS (1:N)
    // ==========================================
    public function get_user_honors($user_id) {
        $rows = $this->db->table('grid_honors')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
        }
        return $rows;
    }

    public function save_user_honor($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'title' => substr(trim($data['title'] ?? ''), 0, 128),
            'issuer' => substr(trim($data['issuer'] ?? ''), 0, 128),
            'year' => substr(trim($data['year'] ?? ''), 0, 16),
            'description' => trim((string)($data['description'] ?? '')),
            'icon' => substr(trim($data['icon'] ?? '🏆'), 0, 16)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_honors')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_honors')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_honor($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_honors')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }

    // ==========================================
    // 6. LANGUAGES (1:N)
    // ==========================================
    public function get_user_languages($user_id) {
        $rows = $this->db->table('grid_languages')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
        }
        return $rows;
    }

    public function save_user_language($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'name' => substr(trim($data['name'] ?? ''), 0, 64),
            'level' => substr(trim($data['level'] ?? $data['proficiency'] ?? 'Full Professional'), 0, 64)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_languages')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_languages')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_language($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_languages')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }

    // ==========================================
    // 7. FEATURED ARTIFACTS (1:N)
    // ==========================================
    public function get_user_artifacts($user_id) {
        $rows = $this->db->table('grid_artifacts')
            ->where('user_id', (int)$user_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['desc'] = $row['description'];
            $row['linkText'] = $row['link_text'];
            $row['linkUrl'] = $row['link_url'];
            $row['imgSrc'] = $row['img_src'];
            $row['coverClass'] = $row['cover_class'] ?: 'g-feat-cover-1';
            $row['tagClass'] = $row['tag_class'] ?: 'g-feat-tag-1';
        }
        return $rows;
    }

    public function save_user_artifact($user_id, array $data, $id = null) {
        $user_id = (int)$user_id;
        $record = [
            'user_id' => $user_id,
            'name' => substr(trim($data['name'] ?? ''), 0, 128),
            'type' => substr(trim($data['type'] ?? 'Open Source Repo'), 0, 64),
            'title' => substr(trim($data['title'] ?? ''), 0, 128),
            'description' => trim((string)($data['desc'] ?? $data['description'] ?? '')),
            'link_text' => substr(trim($data['linkText'] ?? $data['link_text'] ?? $data['name'] ?? ''), 0, 128),
            'link_url' => substr(trim($data['linkUrl'] ?? $data['link_url'] ?? '#'), 0, 255),
            'badge' => substr(trim($data['badge'] ?? '⭐ New'), 0, 32),
            'img_src' => substr(trim($data['imgSrc'] ?? $data['img_src'] ?? ''), 0, 255),
            'cover_class' => substr(trim($data['coverClass'] ?? $data['cover_class'] ?? 'g-feat-cover-1'), 0, 64),
            'tag_class' => substr(trim($data['tagClass'] ?? $data['tag_class'] ?? 'g-feat-tag-1'), 0, 64)
        ];

        $numericId = is_numeric($id) ? (int)$id : (is_numeric($data['id'] ?? null) ? (int)$data['id'] : null);

        if ($numericId && $numericId > 0) {
            $this->db->table('grid_artifacts')->where(['id' => $numericId, 'user_id' => $user_id])->update($record);
            return $numericId;
        } else {
            $this->db->table('grid_artifacts')->insert($record);
            return (int)$this->db->insertID();
        }
    }

    public function delete_user_artifact($user_id, $id) {
        if (!is_numeric($id)) return false;
        return $this->db->table('grid_artifacts')->where(['id' => (int)$id, 'user_id' => (int)$user_id])->delete();
    }
}
