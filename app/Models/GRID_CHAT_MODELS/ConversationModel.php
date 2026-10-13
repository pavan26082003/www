<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

/**
 * grid_chat_db.conversations — one row per direct or group chat.
 */
class ConversationModel extends Model
{
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
        helper('grid_chat');
    }

    protected $DBGroup       = 'gridchat';
    protected $table         = 'conversations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'type', 'direct_key', 'title', 'avatar_path', 'created_by',
        'last_message_id', 'last_message_at', 'created_at', 'updated_at',
    ];

    private const LIST_LIMIT = 200;

    public static function directKey(int $userA, int $userB): string
    {
        return min($userA, $userB) . ':' . max($userA, $userB);
    }

    public function findDirectByKey(string $directKey): ?array
    {
        return $this->builder()->where('direct_key', $directKey)->get()->getRowArray();
    }

    /**
     * Find or create the direct conversation between two users (with both member rows).
     */
    public function findOrCreateDirect(int $userId, int $otherUserId): array
    {
        $directKey = self::directKey($userId, $otherUserId);
        $conversation = $this->findDirectByKey($directKey);
        if ($conversation) {
            return $conversation;
        }

        $now = grid_chat_now();
        $this->db->transBegin();
        try {
            $this->assertWrite($this->db->table($this->table)->insert([
                'type' => 'direct',
                'direct_key' => $directKey,
                'created_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
            $conversationId = (int) $this->db->insertID();

            $this->assertWrite($this->db->table('conversation_members')->insertBatch([
                ['conversation_id' => $conversationId, 'user_id' => $userId, 'joined_at' => $now],
                ['conversation_id' => $conversationId, 'user_id' => $otherUserId, 'joined_at' => $now],
            ]));

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            // Another request may have created the same chat at the same moment
            $conversation = $this->findDirectByKey($directKey);
            if ($conversation) {
                return $conversation;
            }
            throw $e;
        }

        return $this->findDirectByKey($directKey);
    }

    /**
     * Create a group: conversation row, creator as admin, other members, and the
     * "created" notice — all in one transaction.
     *
     * @param int[] $memberIds other members (creator excluded)
     * @return int new conversation id
     */
    public function createGroup(int $creatorId, string $title, array $memberIds): int
    {
        $now = grid_chat_now();
        $this->db->transBegin();
        try {
            $this->assertWrite($this->db->table($this->table)->insert([
                'type' => 'group',
                'title' => $title,
                'created_by' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
            $conversationId = (int) $this->db->insertID();

            $rows = [['conversation_id' => $conversationId, 'user_id' => $creatorId, 'role' => 'admin', 'joined_at' => $now]];
            foreach ($memberIds as $memberId) {
                $rows[] = ['conversation_id' => $conversationId, 'user_id' => (int) $memberId, 'role' => 'member', 'joined_at' => $now];
            }
            $this->assertWrite($this->db->table('conversation_members')->insertBatch($rows));

            (new MessageModel($this->db))->createSystemMessage($conversationId, $creatorId, 'created', ['title' => $title]);

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        return $conversationId;
    }

    public function findGroup(int $conversationId): ?array
    {
        return $this->builder()->where('id', $conversationId)->where('type', 'group')->get()->getRowArray();
    }

    /** $data: title and/or avatar_path */
    public function updateGroup(int $conversationId, array $data): bool
    {
        $data = array_intersect_key($data, array_flip(['title', 'avatar_path']));
        if (!$data) {
            return true;
        }
        $data['updated_at'] = grid_chat_now();
        return (bool) $this->builder()->where('id', $conversationId)->where('type', 'group')->update($data);
    }

    /**
     * Chat list rows for a user (member state + last message), newest activity first.
     * Chats the user deleted are excluded.
     *
     * @param int[]|null $onlyIds limit to these conversation ids
     */
    public function listForUser(int $userId, ?array $onlyIds = null): array
    {
        $sql = 'SELECT c.id, c.type, c.title, c.avatar_path, c.created_at, c.last_message_id, c.last_message_at,
                       m.role, m.unread_count, m.cleared_before_message_id, m.joined_after_message_id,
                       m.is_favourite, m.is_archived, m.muted_until,
                       lm.sender_id AS lm_sender_id, lm.message_type AS lm_type, lm.body AS lm_body,
                       lm.metadata AS lm_metadata, lm.deleted_at AS lm_deleted_at
                FROM conversation_members m
                JOIN conversations c ON c.id = m.conversation_id
                LEFT JOIN messages lm ON lm.id = c.last_message_id
                WHERE m.user_id = ? AND m.left_at IS NULL AND m.deleted_at IS NULL';

        if ($onlyIds !== null) {
            $onlyIds = array_values(array_filter(array_map('intval', $onlyIds)));
            if (!$onlyIds) {
                return [];
            }
            $sql .= ' AND c.id IN (' . implode(',', $onlyIds) . ')';
        }
        $sql .= ' ORDER BY COALESCE(c.last_message_at, c.created_at) DESC LIMIT ' . self::LIST_LIMIT;

        return $this->db->query($sql, [$userId])->getResultArray();
    }

    public function getType(int $conversationId): ?string
    {
        return $this->builder()->select('type')->where('id', $conversationId)->get()->getRow('type');
    }

    public function getLastMessageId(int $conversationId): int
    {
        return (int) $this->builder()->select('last_message_id')->where('id', $conversationId)->get()->getRow('last_message_id');
    }

    /** Highest last_message_id across the user's conversations (sync starting point). */
    public function maxLastMessageIdForUser(int $userId): int
    {
        return (int) $this->db->query(
            'SELECT COALESCE(MAX(c.last_message_id), 0) AS max_id
             FROM conversation_members m
             JOIN conversations c ON c.id = m.conversation_id
             WHERE m.user_id = ? AND m.left_at IS NULL',
            [$userId]
        )->getRow('max_id');
    }

    /** Builder writes return false instead of throwing when DBDebug is off. */
    private function assertWrite($result): void
    {
        if ($result === false) {
            throw new \RuntimeException('GRID_CHAT conversations write failed: ' . ($this->db->error()['message'] ?? 'unknown'));
        }
    }
}
