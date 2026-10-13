<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * grid_chat_db.moment_likes — hearts on a status.
 *
 * Everyone sees the total, but only the poster sees WHO liked — that list is
 * part of the viewers sheet, which is owner-only. Rows go when the moment does
 * (foreign key).
 */
class MomentLikeModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'moment_likes';
    protected $primaryKey    = 'moment_id';
    protected $returnType    = 'array';
    protected $allowedFields = ['moment_id', 'user_id', 'liked_at'];

    public function __construct()
    {
        parent::__construct();
        helper('grid_chat');
    }

    /**
     * Adds or removes this user's like.
     *
     * @return bool the state afterwards: true when liked
     */
    public function toggle(int $momentId, int $userId): bool
    {
        $exists = $this->builder()
            ->where('moment_id', $momentId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;

        if ($exists) {
            $this->builder()->where('moment_id', $momentId)->where('user_id', $userId)->delete();
            return false;
        }

        $this->builder()->insert([
            'moment_id' => $momentId,
            'user_id' => $userId,
            'liked_at' => grid_chat_now(),
        ]);
        return true;
    }

    /**
     * @param int[] $momentIds
     * @return array<int, int> moment id → number of likes
     */
    public function countsForMoments(array $momentIds): array
    {
        $momentIds = $this->cleanIds($momentIds);
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
     * Which of these moments the user has liked.
     *
     * @param int[] $momentIds
     * @return int[]
     */
    public function likedByUser(array $momentIds, int $userId): array
    {
        $momentIds = $this->cleanIds($momentIds);
        if (!$momentIds) {
            return [];
        }

        $rows = $this->builder()
            ->select('moment_id')
            ->whereIn('moment_id', $momentIds)
            ->where('user_id', $userId)
            ->get()->getResultArray();

        return array_map('intval', array_column($rows, 'moment_id'));
    }

    /**
     * Ids of the people who liked one moment.
     *
     * @return int[]
     */
    public function likerIds(int $momentId): array
    {
        $rows = $this->builder()
            ->select('user_id')
            ->where('moment_id', $momentId)
            ->get()->getResultArray();

        return array_map('intval', array_column($rows, 'user_id'));
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    private function cleanIds(array $ids): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }
}
