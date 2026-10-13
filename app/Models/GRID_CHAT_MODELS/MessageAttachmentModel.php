<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * grid_chat_db.message_attachments — files stored under www/files/grid_chat/media,
 * only the relative path is kept here.
 */
class MessageAttachmentModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'message_attachments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'message_id', 'file_path', 'file_name', 'mime_type', 'size_bytes',
        'duration_sec', 'width', 'height', 'created_at',
    ];

    /**
     * Attachment the user may open: they are an active member of the conversation,
     * the message is not deleted for everyone, not before their clear-chat point
     * and not hidden by them. Returns null otherwise.
     */
    public function findVisibleForUser(int $attachmentId, int $userId): ?array
    {
        return $this->db->query(
            'SELECT a.*, msg.conversation_id, msg.message_type
             FROM message_attachments a
             JOIN messages msg ON msg.id = a.message_id
             JOIN conversation_members me
               ON me.conversation_id = msg.conversation_id AND me.user_id = ? AND me.left_at IS NULL
             WHERE a.id = ?
               AND msg.deleted_at IS NULL
               AND msg.id > GREATEST(COALESCE(me.cleared_before_message_id, 0), COALESCE(me.joined_after_message_id, 0))
               AND NOT EXISTS (SELECT 1 FROM message_hidden h WHERE h.message_id = msg.id AND h.user_id = ?)',
            [$userId, $attachmentId, $userId]
        )->getRowArray();
    }

    /**
     * @param int[] $messageIds
     * @return array<int, array[]> message id → attachment rows
     */
    public function byMessageIds(array $messageIds): array
    {
        $messageIds = array_values(array_unique(array_filter(array_map('intval', $messageIds))));
        if (!$messageIds) {
            return [];
        }

        $rows = $this->builder()
            ->whereIn('message_id', $messageIds)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['message_id']][] = $row;
        }
        return $grouped;
    }
}
