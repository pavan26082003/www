<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_profile_model
 * Manages user professional profiles, roles, and open-to-work status.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_profile_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_profiles';
        parent::__construct($this->table);
    }

    /**
     * Get profile by user_id with fallback defaults.
     * Self-heals a missing grid_id on any existing profile row (e.g. rows created
     * before the grid_id column existed) by generating and persisting one.
     */
    function get_profile_by_user_id($user_id) {
        $user_id = (int) $user_id;
        $profile = $this->get_one_where(['user_id' => $user_id]);

        if ($profile && !empty($profile->id)) {
            $profileArray = (array) $profile;

            if (empty($profileArray['grid_id'])) {
                $userRow = $this->db->table('users')
                    ->select('first_name, last_name')
                    ->where('id', $user_id)
                    ->get()
                    ->getRowArray();

                $fullName = trim(($userRow['first_name'] ?? '') . ' ' . ($userRow['last_name'] ?? ''));
                $gridId = $this->generate_unique_grid_id($fullName);

                $this->db_builder->where('user_id', $user_id)->update(['grid_id' => $gridId]);
                $profileArray['grid_id'] = $gridId;
            }

            return $profileArray;
        }

        return [
            'user_id' => $user_id,
            'grid_id' => null,
            'avatar' => '',
            'headline' => '',
            'bio' => '',
            'role' => 'candidate',
            'is_open_to_work' => 1,
            'is_hiring' => 0,
            'location' => '',
            'website' => '',
            'github_url' => '',
            'linkedin_url' => '',
            'is_wizard_completed' => 0,
            'pronoun' => 'He/Him',
            'about_skills' => '',
            'about_json' => null,
            'otw_roles' => 'Open to opportunities',
            'otw_note' => 'Available',
            'banner_type' => 'gradient',
            'banner_video_src' => '',
            'banner_image_src' => '',
            'banner_gradient' => 'radial-gradient(circle at 20% 30%, #4338ca 0%, #1e1b4b 65%, #0f172a 100%)',
            'banner_tag' => '',
            'banner_title' => '',
            'banner_url' => ''
        ];
    }

    /**
     * Lookup a profile by its public grid_id vanity handle.
     * Returns null if no profile owns that handle.
     */
    function get_profile_by_grid_id($grid_id) {
        $grid_id = trim((string) $grid_id);
        if ($grid_id === '') {
            return null;
        }

        $profile = $this->get_one_where(['grid_id' => $grid_id]);

        if ($profile && !empty($profile->id)) {
            return (array) $profile;
        }

        return null;
    }

    /**
     * Build a URL-safe grid_id from a display name (e.g. "K Pavan Kumar" -> "pavan-kumar"),
     * append a random 4-digit discriminator, and retry until it's unique.
     */
    function generate_unique_grid_id($full_name) {
        $slug = strtolower(trim((string) $full_name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        if ($slug === '') {
            $slug = 'member';
        }

        do {
            $candidate = $slug . '-' . random_int(1000, 9999);
            $exists = $this->db->table('grid_profiles')
                ->select('id')
                ->where('grid_id', $candidate)
                ->get()
                ->getRowArray();
        } while ($exists);

        return $candidate;
    }

    /**
     * Upsert profile data for a user
     */
    function save_profile($user_id, $data) {
        $user_id = (int) $user_id;
        $existing = $this->get_one_where(['user_id' => $user_id]);

        if ($existing && !empty($existing->id)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->db_builder->where('user_id', $user_id)->update($data);
        } else {
            $data['user_id'] = $user_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db_builder->insert($data);
        }
    }

    /**
     * Provision a unique grid_id for a user upon account creation or migration.
     * Idempotent: returns existing grid_id if one is already assigned.
     *
     * @param int $user_id
     * @param string|null $preferred_name_or_handle Optional full name, display name, or handle
     * @return string The provisioned unique grid_id
     */
    function provision_grid_id($user_id, $preferred_name_or_handle = null) {
        $user_id = (int) $user_id;
        if ($user_id <= 0) {
            return '';
        }

        // 1. Check if user already has a profile with a valid grid_id
        $existing = $this->get_one_where(['user_id' => $user_id]);
        if ($existing && !empty($existing->id) && !empty($existing->grid_id)) {
            return (string) $existing->grid_id;
        }

        // 2. Determine base name for slug generation
        $baseName = trim((string) $preferred_name_or_handle);
        if ($baseName === '') {
            $userRow = $this->db->table('users')
                ->select('first_name, last_name')
                ->where('id', $user_id)
                ->get()
                ->getRowArray();

            $baseName = trim(($userRow['first_name'] ?? '') . ' ' . ($userRow['last_name'] ?? ''));
        }

        if ($baseName === '') {
            $baseName = 'member';
        }

        // 3. Generate unique handle
        $gridId = $this->generate_unique_grid_id($baseName);

        // 4. Upsert into grid_profiles
        if ($existing && !empty($existing->id)) {
            $this->db_builder->where('user_id', $user_id)->update([
                'grid_id' => $gridId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db_builder->insert([
                'user_id' => $user_id,
                'grid_id' => $gridId,
                'headline' => 'Professional Member',
                'bio' => '',
                'role' => 'candidate',
                'is_open_to_work' => 1,
                'is_hiring' => 0,
                'location' => '',
                'website' => '',
                'github_url' => '',
                'linkedin_url' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $gridId;
    }

    /**
     * Search active users by name for the header search bar. Returns each
     * match's identity (id, grid_id, name, raw avatar, headline) — the
     * controller resolves the raw avatar into a public URL, matching
     * getProfile()'s convention.
     */
    function search_users($query, $limit = 8, $excludeUserId = null) {
        $query = trim((string) $query);
        if ($query === '' || mb_strlen($query) < 2) {
            return [];
        }

        $builder = $this->db->table('users u')
            ->select("u.id, u.first_name, u.last_name, u.image, gp.grid_id, gp.headline")
            ->join('grid_profiles gp', 'gp.user_id = u.id', 'left')
            ->groupStart()
                ->like('u.first_name', $query)
                ->orLike('u.last_name', $query)
                ->orLike("CONCAT(u.first_name, ' ', u.last_name)", $query)
            ->groupEnd()
            ->where('u.deleted', 0);

        if (!empty($excludeUserId)) {
            $builder->where('u.id !=', (int) $excludeUserId);
        }

        $rows = $builder->orderBy('u.first_name', 'ASC')
            ->limit((int) $limit)
            ->get()
            ->getResultArray();

        $results = [];
        foreach ($rows as $r) {
            $results[] = [
                'id' => (int) $r['id'],
                'grid_id' => $r['grid_id'] ?? null,
                'name' => trim($r['first_name'] . ' ' . ($r['last_name'] ?? '')),
                'avatar' => $r['image'] ?? '',
                'headline' => $r['headline'] ?? 'GRID Member'
            ];
        }

        return $results;
    }
}
