<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * grid_chat_db.moments — status updates that expire after 24 hours.
 *
 * Who may see a moment is decided once, when it is posted: `audience` plus the
 * rows in `moment_audience` are a snapshot, so changing your status privacy
 * later never changes who can see something you already published.
 *
 * Media files live under www/files/grid_chat/moments; only the relative path is
 * kept here, and the file itself is served by MomentController::media.
 */
class MomentModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'moments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id', 'audience', 'type', 'media_path', 'mime_type', 'size_bytes',
        'text_content', 'text_bg', 'caption', 'created_at', 'expires_at',
    ];

    /**
     * True when the viewer is in a moment's audience. Your own moments are
     * always visible to you.
     *
     * Bind order wherever this is used: expires_at, owner, except-check, only-check.
     */
    private const VISIBLE_TO = "
        m.expires_at > ?
        AND (
            m.user_id = ?
            OR m.audience = 'contacts'
            OR (m.audience = 'contacts_except'
                AND NOT EXISTS (SELECT 1 FROM moment_audience a
                                WHERE a.moment_id = m.id AND a.user_id = ?))
            OR (m.audience = 'only'
                AND EXISTS (SELECT 1 FROM moment_audience a
                            WHERE a.moment_id = m.id AND a.user_id = ?))
        )";

    public function __construct()
    {
        parent::__construct();
        helper('grid_chat');
    }

    /**
     * Every unexpired moment the viewer is allowed to see, oldest first per
     * user so the story viewer plays them in order.
     */
    public function activeVisibleTo(int $viewerId): array
    {
        return $this->db->query(
            'SELECT m.* FROM moments m WHERE ' . self::VISIBLE_TO
            . ' ORDER BY m.user_id ASC, m.created_at ASC, m.id ASC',
            [grid_chat_now(), $viewerId, $viewerId, $viewerId]
        )->getResultArray();
    }

    /**
     * One moment, only if the viewer is in its audience. Used before streaming a
     * file or recording a view, so neither trusts an id from the browser.
     */
    public function findVisibleForUser(int $momentId, int $viewerId): ?array
    {
        return $this->db->query(
            'SELECT m.* FROM moments m WHERE m.id = ? AND ' . self::VISIBLE_TO,
            [$momentId, grid_chat_now(), $viewerId, $viewerId, $viewerId]
        )->getRowArray();
    }

    /**
     * Saves a moment together with the audience it was posted to.
     *
     * @param array $data      moment columns, including 'audience'
     * @param int[] $targetIds excluded users for 'contacts_except',
     *                         allowed users for 'only', ignored for 'contacts'
     * @return int new moment id
     */
    public function createWithAudience(array $data, array $targetIds): int
    {
        $this->db->transBegin();

        try {
            $momentId = (int) $this->insert($data, true);
            if (!$momentId) {
                throw new \RuntimeException('Could not save the moment');
            }

            $targetIds = $this->cleanIds($targetIds, (int) $data['user_id']);
            if ($targetIds && $data['audience'] !== 'contacts') {
                $this->db->table('moment_audience')->insertBatch(array_map(
                    static fn (int $id) => ['moment_id' => $momentId, 'user_id' => $id],
                    $targetIds
                ));
            }

            $this->db->transCommit();
            return $momentId;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Deletes a moment the user owns and returns the row it deleted, so the
     * caller can remove the file from disk. moment_audience cascades.
     *
     * @return array|null null when the moment does not exist or belongs to someone else
     */
    public function deleteOwned(int $momentId, int $userId): ?array
    {
        $row = $this->where('id', $momentId)->where('user_id', $userId)->first();
        if (!$row) {
            return null;
        }

        $this->where('id', $momentId)->where('user_id', $userId)->delete();
        return $row;
    }

    /**
     * Removes moments past their 24 hours and returns them so their files can be
     * unlinked. Called opportunistically when the list is read.
     */
    public function deleteExpired(): array
    {
        $expired = $this->where('expires_at <=', grid_chat_now())->findAll(500);
        if ($expired) {
            $this->whereIn('id', array_column($expired, 'id'))->delete();
        }
        return $expired;
    }

    /**
     * @param int[] $ids
     * @return int[] positive and unique, never the poster themselves
     */
    private function cleanIds(array $ids, int $excludeId = 0): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $ids),
            static fn (int $id) => $id > 0 && $id !== $excludeId
        )));
    }
}
