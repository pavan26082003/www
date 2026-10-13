<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * grid_chat_db.moment_views — which moments a user has already watched, so the
 * ring can be drawn grey. The real key is (moment_id, viewer_id); rows are
 * removed by the foreign key when their moment is deleted or expires.
 */
class MomentViewModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'moment_views';
    protected $primaryKey    = 'moment_id';
    protected $returnType    = 'array';
    protected $allowedFields = ['moment_id', 'viewer_id', 'viewed_at'];

    public function __construct()
    {
        parent::__construct();
        helper('grid_chat');
    }

    /** Safe to call again for a moment the user has already seen. */
    public function markAsViewed(int $momentId, int $viewerId): bool
    {
        return (bool) $this->db->query(
            "INSERT INTO {$this->table} (moment_id, viewer_id, viewed_at) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE viewed_at = viewed_at",
            [$momentId, $viewerId, grid_chat_now()]
        );
    }

    /**
     * Ids of the unexpired moments this user has watched. Expired moments are
     * left out so the list stays as small as the feed itself.
     *
     * @return int[]
     */
    public function viewedMomentIds(int $viewerId): array
    {
        $rows = $this->db->query(
            "SELECT v.moment_id FROM {$this->table} v
             JOIN moments m ON m.id = v.moment_id
             WHERE v.viewer_id = ? AND m.expires_at > ?",
            [$viewerId, grid_chat_now()]
        )->getResultArray();

        return array_map('intval', array_column($rows, 'moment_id'));
    }

    /**
     * How many people have watched each moment. Only ever shown to the poster.
     *
     * @param int[] $momentIds
     * @return array<int, int> moment id → number of viewers
     */
    public function countsForMoments(array $momentIds): array
    {
        $momentIds = array_values(array_unique(array_filter(array_map('intval', $momentIds))));
        if (!$momentIds) {
            return [];
        }

        $rows = $this->builder()
            ->select('moment_id, COUNT(*) AS total')
            ->whereIn('moment_id', $momentIds)
            ->groupBy('moment_id')
            ->get()->getResultArray();

        return array_map('intval', array_column($rows, 'total', 'moment_id'));
    }

    /**
     * Who watched one moment, most recent first.
     *
     * @return array<int, array{viewer_id: int, viewed_at: string}>
     */
    public function viewersForMoment(int $momentId): array
    {
        return $this->builder()
            ->select('viewer_id, viewed_at')
            ->where('moment_id', $momentId)
            ->orderBy('viewed_at', 'DESC')
            ->get()->getResultArray();
    }
}
