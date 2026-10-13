<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_skills_model
 * Manages skills taxonomy, user skill associations, and endorsement counts.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_skills_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_skills';
        parent::__construct($this->table);
    }

    /**
     * Autocomplete suggestions using B-Tree index prefix match
     */
    function suggest_skills($query = '', $limit = 15) {
        $builder = $this->db->table('grid_skills')
            ->select('id, name, category');

        if (!empty($query)) {
            $builder->like('name', $query, 'after');
        }

        return $builder->orderBy('name', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get user's skills with endorsement counts
     */
    function get_user_skills($user_id) {
        $user_id = (int) $user_id;
        return $this->db->table('grid_user_skills gus')
            ->select('gs.id, gs.name, gs.category, gus.endorsements_count')
            ->join('grid_skills gs', 'gs.id = gus.skill_id', 'inner')
            ->where('gus.user_id', $user_id)
            ->orderBy('gus.endorsements_count', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Add or endorse a skill for a user
     */
    function endorse_user_skill($user_id, $skill_id) {
        $user_id = (int) $user_id;
        $skill_id = (int) $skill_id;

        $sql = "
            INSERT INTO grid_user_skills (user_id, skill_id, endorsements_count)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE endorsements_count = endorsements_count + 1
        ";
        return $this->db->query($sql, [$user_id, $skill_id]);
    }

    /**
     * Associate an array of skill names with a user (used by the onboarding wizard).
     * Looks up each name in grid_skills, inserts it if missing, then links it to the
     * user in grid_user_skills with endorsements_count = 1, ignoring existing links.
     *
     * @param int   $user_id
     * @param array $skill_names Plain skill name strings (not IDs)
     * @return int Number of skills linked (existing + newly created)
     */
    function associate_user_skills($user_id, array $skill_names) {
        $user_id = (int) $user_id;
        $linked = 0;

        foreach ($skill_names as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $existing = $this->db->table('grid_skills')
                ->select('id')
                ->where('name', $name)
                ->get()
                ->getRowArray();

            if ($existing) {
                $skill_id = (int) $existing['id'];
            } else {
                try {
                    $this->db->table('grid_skills')->insert(['name' => $name]);
                    $skill_id = (int) $this->db->insertID();
                } catch (\Throwable $e) {
                    // Another request inserted the same new skill name concurrently
                    // (grid_skills.name is UNIQUE) — re-fetch instead of failing.
                    $existing = $this->db->table('grid_skills')
                        ->select('id')
                        ->where('name', $name)
                        ->get()
                        ->getRowArray();
                    $skill_id = $existing ? (int) $existing['id'] : 0;
                }
            }

            if ($skill_id <= 0) {
                continue;
            }

            $this->db->query(
                'INSERT IGNORE INTO grid_user_skills (user_id, skill_id, endorsements_count) VALUES (?, ?, 1)',
                [$user_id, $skill_id]
            );

            $linked++;
        }

        return $linked;
    }

    /**
     * Remove a skill association for a user
     *
     * @param int $user_id
     * @param int|string $skill_id Skill ID (int) or name (string)
     * @return bool
     */
    public function remove_user_skill($user_id, $skill_id) {
        $user_id = (int) $user_id;
        if (is_numeric($skill_id)) {
            return (bool) $this->db->table('grid_user_skills')
                ->where('user_id', $user_id)
                ->where('skill_id', (int)$skill_id)
                ->delete();
        } else {
            $skill = $this->db->table('grid_skills')->select('id')->where('name', (string)$skill_id)->get()->getRowArray();
            if ($skill) {
                return (bool) $this->db->table('grid_user_skills')
                    ->where('user_id', $user_id)
                    ->where('skill_id', (int)$skill['id'])
                    ->delete();
            }
        }
        return false;
    }
}
