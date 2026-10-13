<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_connections_model
 * Manages bidirectional connection graphs with canonical ordering (LEAST/GREATEST).
 * Guarantees O(1) duplicate prevention and instant status lookups.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_connections_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_connections';
        parent::__construct($this->table);
    }

    /**
     * Instant O(1) canonical connection status lookup
     */
    function get_connection_status($u1, $u2) {
        $min = min((int) $u1, (int) $u2);
        $max = max((int) $u1, (int) $u2);

        $row = $this->db->table('grid_connections')
            ->select('status, action_user_id')
            ->where('user_one_id', $min)
            ->where('user_two_id', $max)
            ->get()
            ->getRowArray();

        if (!$row) return 'none';
        if ($row['status'] === 'accepted') return 'connected';
        if ($row['status'] === 'pending') {
            return ($row['action_user_id'] == $u1) ? 'pending_sent' : 'pending_received';
        }
        return $row['status'];
    }

    /**
     * Get all accepted connections for a user
     */
    function get_accepted_connections($user_id) {
        $user_id = (int) $user_id;
        $sql = "
            SELECT 
                u.id, 
                u.first_name, 
                u.last_name, 
                u.image, 
                gp.headline, 
                gp.is_open_to_work,
                gc.updated_at AS connected_at
            FROM grid_connections gc
            JOIN users u ON (u.id = CASE WHEN gc.user_one_id = ? THEN gc.user_two_id ELSE gc.user_one_id END)
            LEFT JOIN grid_profiles gp ON gp.user_id = u.id
            WHERE (gc.user_one_id = ? OR gc.user_two_id = ?)
              AND gc.status = 'accepted'
            ORDER BY gc.updated_at DESC
        ";

        return $this->db->query($sql, [$user_id, $user_id, $user_id])->getResultArray();
    }

    /**
     * Send or re-send connection invitation
     */
    function send_request($from_user_id, $to_user_id) {
        $min = min((int) $from_user_id, (int) $to_user_id);
        $max = max((int) $from_user_id, (int) $to_user_id);

        $sql = "
            INSERT INTO grid_connections (user_one_id, user_two_id, action_user_id, status)
            VALUES (?, ?, ?, 'pending')
            ON DUPLICATE KEY UPDATE 
                action_user_id = VALUES(action_user_id),
                status = IF(status = 'declined', 'pending', status)
        ";
        return $this->db->query($sql, [$min, $max, (int) $from_user_id]);
    }

    /**
     * Accept or decline a connection invitation
     */
    function respond_request($user_id, $target_user_id, $action) {
        $min = min((int) $user_id, (int) $target_user_id);
        $max = max((int) $user_id, (int) $target_user_id);
        $status = ($action === 'accept') ? 'accepted' : 'declined';

        return $this->db->table('grid_connections')
            ->where('user_one_id', $min)
            ->where('user_two_id', $max)
            ->update([
                'status' => $status,
                'action_user_id' => (int) $user_id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
}
