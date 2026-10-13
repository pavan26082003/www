<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

/**
 * grid_chat_db.user_blocks — Tracks user block relationships:
 * blocker_id = User A (who blocked)
 * blocked_user_id = User B (who is blocked)
 */
class UserBlockModel extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
        helper('grid_chat');
    }

    protected $DBGroup       = 'gridchat';
    protected $table         = 'user_blocks';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'blocker_id', 'blocked_user_id', 'created_at',
    ];

    /**
     * User A blocks User B.
     * blocker_id = User A
     * blocked_user_id = User B
     */
    public function blockUser(int $blockerId, int $blockedUserId): bool
    {
        if ($blockerId <= 0 || $blockedUserId <= 0 || $blockerId === $blockedUserId) {
            return false;
        }

        if ($this->hasBlocked($blockerId, $blockedUserId)) {
            return true;
        }

        $now = grid_chat_now();
        return (bool) $this->builder()->insert([
            'blocker_id' => $blockerId,
            'blocked_user_id' => $blockedUserId,
            'created_at' => $now,
        ]);
    }

    /**
     * User A unblocks User B.
     */
    public function unblockUser(int $blockerId, int $blockedUserId): bool
    {
        if ($blockerId <= 0 || $blockedUserId <= 0) {
            return false;
        }

        return (bool) $this->builder()
            ->where('blocker_id', $blockerId)
            ->where('blocked_user_id', $blockedUserId)
            ->delete();
    }

    /**
     * Checks if $blockerId has blocked $blockedUserId.
     */
    public function hasBlocked(int $blockerId, int $blockedUserId): bool
    {
        if ($blockerId <= 0 || $blockedUserId <= 0) {
            return false;
        }

        $count = $this->builder()
            ->where('blocker_id', $blockerId)
            ->where('blocked_user_id', $blockedUserId)
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Checks if either user has blocked the other.
     */
    public function isBlockedEither(int $userA, int $userB): bool
    {
        if ($userA <= 0 || $userB <= 0) {
            return false;
        }

        $count = $this->builder()
            ->groupStart()
                ->where('blocker_id', $userA)
                ->where('blocked_user_id', $userB)
            ->groupEnd()
            ->orGroupStart()
                ->where('blocker_id', $userB)
                ->where('blocked_user_id', $userA)
            ->groupEnd()
            ->countAllResults();

        return $count > 0;
    }

    /**
     * User IDs that $blockerId has blocked.
     *
     * @return int[]
     */
    public function getBlockedUserIds(int $blockerId): array
    {
        if ($blockerId <= 0) {
            return [];
        }

        $rows = $this->builder()
            ->select('blocked_user_id')
            ->where('blocker_id', $blockerId)
            ->get()->getResultArray();

        return array_values(array_unique(array_map('intval', array_column($rows, 'blocked_user_id'))));
    }

    /**
     * User IDs that have blocked $userId.
     *
     * @return int[]
     */
    public function getBlockedByUserIds(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $rows = $this->builder()
            ->select('blocker_id')
            ->where('blocked_user_id', $userId)
            ->get()->getResultArray();

        return array_values(array_unique(array_map('intval', array_column($rows, 'blocker_id'))));
    }
}