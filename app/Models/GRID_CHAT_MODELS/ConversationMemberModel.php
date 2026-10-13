<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

/**
 * grid_chat_db.conversation_members — who is in a chat and their own state
 * (unread count, read position, favourite, mute, cleared/deleted).
 *
 * Composite key (conversation_id, user_id), so queries use the builder directly.
 */
class ConversationMemberModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'conversation_members';
    protected $primaryKey    = 'conversation_id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'conversation_id', 'user_id', 'role', 'joined_after_message_id', 'unread_count', 'last_read_message_id', 'last_read_at',
        'cleared_before_message_id', 'deleted_at', 'is_favourite', 'is_archived', 'muted_until',
        'joined_at', 'left_at',
    ];

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
        helper('grid_chat');
    }

    /** Active member row, or null if the user is not in the conversation. */
    public function findActive(int $conversationId, int $userId): ?array
    {
        return $this->builder()
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->where('left_at', null)
            ->get()->getRowArray();
    }

    /**
     * Other active members per conversation.
     *
     * @return array<int, array[]> conversation id → rows (conversation_id, user_id, last_read_message_id)
     */
    public function othersByConversation(array $conversationIds, int $userId): array
    {
        $conversationIds = array_values(array_unique(array_filter(array_map('intval', $conversationIds))));
        if (!$conversationIds) {
            return [];
        }

        $rows = $this->builder()
            ->select('conversation_id, user_id, last_read_message_id')
            ->whereIn('conversation_id', $conversationIds)
            ->where('user_id !=', $userId)
            ->where('left_at', null)
            ->get()->getResultArray();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['conversation_id']][] = $row;
        }
        return $grouped;
    }

    /**
     * Active members of one conversation, admins first, then by join time.
     *
     * @return array[] rows (user_id, role, joined_at)
     */
    public function activeMembers(int $conversationId): array
    {
        return $this->builder()
            ->select('user_id, role, joined_at')
            ->where('conversation_id', $conversationId)
            ->where('left_at', null)
            ->orderBy("role = 'admin'", 'DESC', false)
            ->orderBy('joined_at', 'ASC')
            ->get()->getResultArray();
    }

    public function countAdmins(int $conversationId): int
    {
        return $this->builder()
            ->where('conversation_id', $conversationId)
            ->where('role', 'admin')
            ->where('left_at', null)
            ->countAllResults();
    }

    /**
     * Add users to a group. They only see messages with id > $lastMessageId.
     * Users who left earlier are re-activated with a fresh join point.
     *
     * @param int[] $userIds
     * @return int[] ids actually added (already-active members are skipped)
     */
    public function addMembers(int $conversationId, array $userIds, int $lastMessageId): array
    {
        $now = grid_chat_now();
        $added = [];

        foreach (array_unique(array_map('intval', $userIds)) as $userId) {
            $existing = $this->builder()
                ->where('conversation_id', $conversationId)
                ->where('user_id', $userId)
                ->get()->getRowArray();

            $joinState = [
                'role' => 'member',
                'joined_after_message_id' => $lastMessageId,
                'unread_count' => 0,
                'last_read_message_id' => $lastMessageId ?: null,
                'deleted_at' => null,
                'joined_at' => $now,
                'left_at' => null,
            ];

            if ($existing === null) {
                $this->builder()->insert(['conversation_id' => $conversationId, 'user_id' => $userId] + $joinState);
                $added[] = $userId;
            } elseif ($existing['left_at'] !== null) {
                $this->builder()->where('conversation_id', $conversationId)->where('user_id', $userId)->update($joinState);
                $added[] = $userId;
            }
        }

        return $added;
    }

    /** Remove a member (or leave): they lose access to the group. */
    public function removeMember(int $conversationId, int $userId): bool
    {
        return $this->updateMember($conversationId, $userId, [
            'left_at' => grid_chat_now(),
            'role' => 'member',
            'unread_count' => 0,
        ]);
    }

    public function setRole(int $conversationId, int $userId, string $role): bool
    {
        return $this->updateMember($conversationId, $userId, ['role' => $role === 'admin' ? 'admin' : 'member']);
    }

    /** Longest-standing active member other than $exceptUserId (to promote when the last admin leaves). */
    public function oldestMemberExcept(int $conversationId, int $exceptUserId): ?int
    {
        $row = $this->builder()
            ->select('user_id')
            ->where('conversation_id', $conversationId)
            ->where('user_id !=', $exceptUserId)
            ->where('left_at', null)
            ->orderBy('joined_at', 'ASC')
            ->limit(1)
            ->get()->getRowArray();

        return $row ? (int) $row['user_id'] : null;
    }

    /** Show a chat again in the user's list (old messages stay cleared). */
    public function restoreForUser(int $conversationId, int $userId): bool
    {
        return (bool) $this->builder()
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['deleted_at' => null]);
    }

    /**
     * Move the user's read position forward to $messageId and recalculate unread.
     */
    public function markRead(int $conversationId, int $userId, int $messageId): array
    {
        $member = $this->findActive($conversationId, $userId);
        $current = (int) ($member['last_read_message_id'] ?? 0);
        $unread = (int) ($member['unread_count'] ?? 0);

        if ($messageId > $current) {
            $unread = (int) $this->db->table('messages')
                ->where('conversation_id', $conversationId)
                ->where('id >', $messageId)
                ->where('sender_id !=', $userId)
                ->countAllResults();

            $this->builder()
                ->where('conversation_id', $conversationId)
                ->where('user_id', $userId)
                ->update([
                    'last_read_message_id' => $messageId,
                    'last_read_at' => grid_chat_now(),
                    'unread_count' => $unread,
                ]);
            $current = $messageId;
        }

        return ['lastReadMessageId' => $current, 'unread' => $unread];
    }

    public function setFavourite(int $conversationId, int $userId, bool $isFavourite): bool
    {
        return $this->updateMember($conversationId, $userId, ['is_favourite' => $isFavourite ? 1 : 0]);
    }

    /** $mutedUntil: DATETIME(3) UTC, or null to unmute. */
    public function setMutedUntil(int $conversationId, int $userId, ?string $mutedUntil): bool
    {
        return $this->updateMember($conversationId, $userId, ['muted_until' => $mutedUntil]);
    }

    /**
     * Clear chat for this user only: hides messages up to $lastMessageId and marks them read.
     * Other members keep their messages.
     */
    public function clearForUser(int $conversationId, int $userId, int $lastMessageId): bool
    {
        return $this->updateMember($conversationId, $userId, $this->clearedState($conversationId, $userId, $lastMessageId));
    }

    /**
     * Delete chat for this user only: clears it and removes it from their chat list.
     * A new message in the conversation brings it back (MessageModel::createAndDeliver).
     */
    public function deleteForUser(int $conversationId, int $userId, int $lastMessageId): bool
    {
        return $this->updateMember($conversationId, $userId, $this->clearedState($conversationId, $userId, $lastMessageId) + [
            'deleted_at' => grid_chat_now(),
            'is_favourite' => 0,
            'is_archived' => 0,
        ]);
    }

    private function clearedState(int $conversationId, int $userId, int $lastMessageId): array
    {
        $member = $this->findActive($conversationId, $userId);
        $state = [
            'cleared_before_message_id' => max($lastMessageId, (int) ($member['cleared_before_message_id'] ?? 0)),
            'unread_count' => 0,
        ];
        if ($lastMessageId > (int) ($member['last_read_message_id'] ?? 0)) {
            $state['last_read_message_id'] = $lastMessageId;
            $state['last_read_at'] = grid_chat_now();
        }
        return $state;
    }

    private function updateMember(int $conversationId, int $userId, array $data): bool
    {
        return (bool) $this->builder()
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->where('left_at', null)
            ->update($data);
    }

    /**
     * Conversations of $userId where another member read messages after $since.
     *
     * @return int[]
     */
    public function conversationIdsReadSince(int $userId, string $since): array
    {
        $rows = $this->db->query(
            'SELECT DISTINCT other.conversation_id
             FROM conversation_members other
             JOIN conversation_members me
               ON me.conversation_id = other.conversation_id AND me.user_id = ? AND me.left_at IS NULL
             WHERE other.user_id <> ? AND other.last_read_at > ?',
            [$userId, $userId, $since]
        )->getResultArray();

        return array_map(static fn ($row) => (int) $row['conversation_id'], $rows);
    }
}
