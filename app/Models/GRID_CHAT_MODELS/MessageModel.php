<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

/**
 * grid_chat_db.messages — all message types of all conversations.
 *
 * "Visible to a user" = in a conversation they are an active member of,
 * newer than both their clear-chat point and the point they joined (groups),
 * and not hidden by them.
 */
class MessageModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'messages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'conversation_id', 'sender_id', 'client_id', 'message_type', 'body', 'metadata',
        'reply_to_message_id', 'edited_at', 'deleted_at', 'created_at',
    ];

    private const VISIBLE_FROM = 'FROM messages msg
        JOIN conversation_members me
          ON me.conversation_id = msg.conversation_id AND me.user_id = ? AND me.left_at IS NULL
        WHERE msg.id > GREATEST(COALESCE(me.cleared_before_message_id, 0), COALESCE(me.joined_after_message_id, 0))
          AND NOT EXISTS (SELECT 1 FROM message_hidden h WHERE h.message_id = msg.id AND h.user_id = ?)';

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
        helper('grid_chat');
    }

    public function findById(int $messageId): ?array
    {
        return $this->builder()->where('id', $messageId)->get()->getRowArray();
    }

    /**
     * Group event notice ("Admin added Ravi"). Stores user ids only; names are
     * resolved when displayed so renames are reflected.
     *
     * @param string $action created|added|removed|left|renamed|photo|admin
     * @return int new message id
     */
    public function createSystemMessage(int $conversationId, int $actorId, string $action, array $details = []): int
    {
        return $this->createAndDeliver([
            'conversation_id' => $conversationId,
            'sender_id' => $actorId,
            'message_type' => 'system',
            'metadata' => json_encode(['action' => $action] + $details, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function findByClientId(int $senderId, string $clientId): ?array
    {
        return $this->builder()
            ->where('sender_id', $senderId)
            ->where('client_id', $clientId)
            ->get()->getRowArray();
    }

    public function existsInConversation(int $messageId, int $conversationId): bool
    {
        return $this->builder()
            ->where('id', $messageId)
            ->where('conversation_id', $conversationId)
            ->countAllResults() > 0;
    }

    /**
     * The few fields a quoted "replying to..." card needs, for a batch of ids.
     * Called once per page of messages, so replies never cost an extra query
     * each.
     *
     * Visibility is deliberately NOT re-checked: the quote only ever shows a
     * message from the same conversation (send() enforces that), and a reply
     * whose original the reader has cleared should still show what was quoted.
     *
     * @param int[] $ids
     * @return array<int, array> message id → id, sender_id, body, message_type, deleted_at
     */
    public function previewByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return [];
        }

        $rows = $this->builder()
            ->select('id, sender_id, body, message_type, deleted_at')
            ->whereIn('id', $ids)
            ->get()->getResultArray();

        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row['id']] = $row;
        }
        return $byId;
    }

    /**
     * One page of visible messages, newest first; fetches $limit + 1 rows
     * so the caller can tell if older messages exist.
     */
    public function pageForUser(int $userId, int $conversationId, int $beforeId, int $limit): array
    {
        $sql = 'SELECT msg.* ' . self::VISIBLE_FROM . ' AND msg.conversation_id = ?';
        $binds = [$userId, $userId, $conversationId];
        if ($beforeId > 0) {
            $sql .= ' AND msg.id < ?';
            $binds[] = $beforeId;
        }
        $sql .= ' ORDER BY msg.id DESC LIMIT ' . ($limit + 1);

        return $this->db->query($sql, $binds)->getResultArray();
    }

    /** Visible messages with id > $afterId, oldest first ($limit + 1 rows). */
    public function newForUserSince(int $userId, int $afterId, int $limit): array
    {
        return $this->db->query(
            'SELECT msg.* ' . self::VISIBLE_FROM . ' AND msg.id > ? ORDER BY msg.id ASC LIMIT ' . ($limit + 1),
            [$userId, $userId, $afterId]
        )->getResultArray();
    }

    /**
     * "Delete for everyone": soft-deletes the user's OWN messages by stamping
     * deleted_at. The row stays so the other side learns about it — sync picks
     * it up through changedForUserSince, and formatMessages then hides the body
     * and the attachments.
     *
     * @param int[] $ids
     * @return int[] the ids actually deleted (someone else's are ignored)
     */
    public function deleteForEveryone(array $ids, int $userId): array
    {
        $ids = $this->cleanIds($ids);
        if (!$ids) {
            return [];
        }

        $owned = array_map(
            'intval',
            array_column(
                $this->builder()
                    ->select('id')
                    ->whereIn('id', $ids)
                    ->where('sender_id', $userId)
                    ->where('deleted_at', null)
                    ->where('message_type !=', 'system')
                    ->get()->getResultArray(),
                'id'
            )
        );
        if (!$owned) {
            return [];
        }

        $this->builder()->whereIn('id', $owned)->update(['deleted_at' => grid_chat_now()]);
        return $owned;
    }

    /**
     * "Delete for me": hides messages from one user only. Any message they can
     * see qualifies, including other people's. VISIBLE_FROM already filters
     * these out, and the rows cascade away with the message.
     *
     * @param int[] $ids
     * @return int[] the ids actually hidden
     */
    public function hideForUser(array $ids, int $userId): array
    {
        $ids = $this->cleanIds($ids);
        if (!$ids) {
            return [];
        }

        // Only messages in a conversation the user is actually a member of
        $visible = array_map('intval', array_column($this->db->query(
            'SELECT msg.id ' . self::VISIBLE_FROM . ' AND msg.id IN (' . implode(',', $ids) . ')',
            [$userId, $userId]
        )->getResultArray(), 'id'));

        if ($visible) {
            $now = grid_chat_now();
            $this->db->table('message_hidden')->ignore(true)->insertBatch(array_map(
                static fn (int $id) => ['message_id' => $id, 'user_id' => $userId, 'hidden_at' => $now],
                $visible
            ));
        }

        // VISIBLE_FROM already excludes anything hidden earlier, so a retry
        // would otherwise look like a failure. Report those as hidden too.
        $already = array_map('intval', array_column(
            $this->db->table('message_hidden')
                ->select('message_id')
                ->whereIn('message_id', $ids)
                ->where('user_id', $userId)
                ->get()->getResultArray(),
            'message_id'
        ));

        return array_values(array_unique(array_merge($visible, $already)));
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    private function cleanIds(array $ids): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    /** Already-delivered visible messages edited or deleted after $since. */
    public function changedForUserSince(int $userId, int $upToId, string $since, int $limit): array
    {
        return $this->db->query(
            'SELECT msg.* ' . self::VISIBLE_FROM . '
               AND msg.id <= ? AND (msg.edited_at > ? OR msg.deleted_at > ?)
             ORDER BY msg.id ASC LIMIT ' . $limit,
            [$userId, $userId, $upToId, $since, $since]
        )->getResultArray();
    }

    /**
     * Save a message and deliver it in one transaction:
     * message row → attachment row (optional) → conversation last message
     * → other members unread + restore → sender read state.
     *
     * @param array|null $attachment file_path, file_name, mime_type, size_bytes, duration_sec, width, height
     * @return int new message id
     */
    public function createAndDeliver(array $data, ?array $attachment = null): int
    {
        $conversationId = (int) $data['conversation_id'];
        $senderId = (int) $data['sender_id'];
        $now = grid_chat_now();

        $this->db->transBegin();
        try {
            $this->assertWrite($this->db->table($this->table)->insert([
                'conversation_id' => $conversationId,
                'sender_id' => $senderId,
                'client_id' => $data['client_id'] ?? null,
                'message_type' => $data['message_type'],
                'body' => $data['body'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'reply_to_message_id' => $data['reply_to_message_id'] ?? null,
                'created_at' => $now,
            ]));
            $messageId = (int) $this->db->insertID();

            if ($attachment !== null) {
                $this->assertWrite($this->db->table('message_attachments')->insert([
                    'message_id' => $messageId,
                    'file_path' => $attachment['file_path'],
                    'file_name' => $attachment['file_name'],
                    'mime_type' => $attachment['mime_type'],
                    'size_bytes' => $attachment['size_bytes'],
                    'duration_sec' => $attachment['duration_sec'] ?? null,
                    'width' => $attachment['width'] ?? null,
                    'height' => $attachment['height'] ?? null,
                    'created_at' => $now,
                ]));
            }

            $this->assertWrite($this->db->table('conversations')
                ->where('id', $conversationId)
                ->update(['last_message_id' => $messageId, 'last_message_at' => $now, 'updated_at' => $now]));

            // Other members: one more unread; the chat comes back if they had deleted it
            $this->assertWrite($this->db->query(
                'UPDATE conversation_members SET unread_count = unread_count + 1, deleted_at = NULL
                 WHERE conversation_id = ? AND user_id <> ? AND left_at IS NULL',
                [$conversationId, $senderId]
            ));

            // Sender has read everything up to their own message
            $this->assertWrite($this->db->table('conversation_members')
                ->where('conversation_id', $conversationId)
                ->where('user_id', $senderId)
                ->update([
                    'last_read_message_id' => $messageId,
                    'last_read_at' => $now,
                    'unread_count' => 0,
                    'deleted_at' => null,
                ]));

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        return $messageId;
    }

    /** Builder writes return false instead of throwing when DBDebug is off. */
    private function assertWrite($result): void
    {
        if ($result === false) {
            throw new \RuntimeException('GRID_CHAT messages write failed: ' . ($this->db->error()['message'] ?? 'unknown'));
        }
    }
}
