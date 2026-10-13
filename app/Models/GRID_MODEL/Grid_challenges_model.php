<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_challenges_model
 * Manages company-sponsored community challenges, hackathons, and bounties.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_challenges_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_challenges';
        parent::__construct($this->table);
    }

    /**
     * Get top active company-sponsored challenges for Home Suggested Topics and Events Hub
     */
    function get_top_active_challenges($limit = 6) {
        $builder = $this->db->table('grid_challenges gch')
            ->select('gch.*, gc.name as company_name, gc.slug as company_slug, gc.logo_url as company_logo, gc.website as company_website')
            ->join('grid_companies gc', 'gc.id = gch.company_id', 'left')
            ->where('gch.status', 'active')
            ->orderBy('gch.submissions_count', 'DESC')
            ->orderBy('gch.created_at', 'DESC')
            ->limit($limit);

        $challenges = $builder->get()->getResultArray();

        $now = time();
        foreach ($challenges as &$ch) {
            $end = strtotime($ch['end_date']);
            $diff = $end - $now;

            if ($diff <= 0) {
                $ch['time_left_str'] = 'Ended';
            } elseif ($diff < 86400) {
                $hours = floor($diff / 3600);
                $mins = floor(($diff % 3600) / 60);
                $ch['time_left_str'] = "{$hours}h {$mins}m";
            } else {
                $days = ceil($diff / 86400);
                $ch['time_left_str'] = "{$days}d";
            }

            // Normalization for frontend Topic Hub and Suggested Topics Carousel
            $ch['stat1Val'] = $ch['prize_amount'];
            $ch['stat1Label'] = 'Prize';
            $ch['stat2Val'] = $ch['time_left_str'];
            $ch['stat2Label'] = 'Left';
            $ch['isChallenge'] = true;
            $ch['company'] = [
                'name' => $ch['company_name'] ?? 'Partner Company',
                'slug' => $ch['company_slug'] ?? '',
                'logo' => $ch['company_logo'] ?? '',
                'website' => $ch['company_website'] ?? ''
            ];
        }

        return $challenges;
    }

    /**
     * Get challenge details by slug
     */
    function get_challenge_by_slug($slug) {
        return $this->db->table('grid_challenges gch')
            ->select('gch.*, gc.name as company_name, gc.slug as company_slug, gc.logo_url as company_logo, gc.website as company_website')
            ->join('grid_companies gc', 'gc.id = gch.company_id', 'left')
            ->where('gch.slug', $slug)
            ->get()
            ->getRowArray();
    }

    /**
     * Create a new company-sponsored challenge
     */
    function create_challenge($data) {
        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->table('grid_challenges')->insert($data);
        return $this->db->insertID();
    }
}
