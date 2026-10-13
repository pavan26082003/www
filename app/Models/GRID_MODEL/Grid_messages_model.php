<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_messages_model
 * Manages Circle Connect direct-message conversations and messages, anchored by grid_id.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_messages_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_conversations';
        parent::__construct($this->table);
    }

    /**
     * Find the canonical conversation between two users (user_one_id < user_two_id),
     * creating it if it doesn't exist yet. Returns the conversation id.
     */
    function get_or_create_conversation($user_id_1, $user_id_2) {
        $min = min((int) $user_id_1, (int) $user_id_2);
        $max = max((int) $user_id_1, (int) $user_id_2);

        $row = $this->db->table('grid_conversations')
            ->select('id')
            ->where('user_one_id', $min)
            ->where('user_two_id', $max)
            ->get()
            ->getRowArray();

        if ($row) {
            return (int) $row['id'];
        }

        $this->db->table('grid_conversations')->insert([
            'user_one_id' => $min,
            'user_two_id' => $max
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * List a user's conversations with opponent identity (name, grid_id, headline)
     * and unread counts, most recently active first.
     */
    function get_user_conversations($user_id) {
        $user_id = (int) $user_id;

        $rows = $this->db->table('grid_conversations gc')
            ->select("gc.id as conversation_id, gc.last_message, gc.last_message_at,
                     u.id as opponent_user_id, u.first_name, u.last_name,
                     gp.grid_id, gp.headline, gp.avatar as grid_avatar")
            ->join('users u', "u.id = IF(gc.user_one_id = {$user_id}, gc.user_two_id, gc.user_one_id)", 'inner', false)
            ->join('grid_profiles gp', 'gp.user_id = u.id', 'left')
            ->groupStart()
                ->where('gc.user_one_id', $user_id)
                ->orWhere('gc.user_two_id', $user_id)
            ->groupEnd()
            ->orderBy('gc.last_message_at', 'DESC')
            ->get()
            ->getResultArray();

        if (empty($rows)) {
            return [];
        }

        $conversationIds = array_column($rows, 'conversation_id');

        $unreadRows = $this->db->table('grid_messages')
            ->select('conversation_id, COUNT(*) as cnt')
            ->whereIn('conversation_id', $conversationIds)
            ->where('recipient_id', $user_id)
            ->where('is_read', 0)
            ->groupBy('conversation_id')
            ->get()
            ->getResultArray();

        $unreadByConv = [];
        foreach ($unreadRows as $u) {
            $unreadByConv[$u['conversation_id']] = (int) $u['cnt'];
        }

        $conversations = [];
        foreach ($rows as $row) {
            $conversations[] = [
                'conversation_id' => (int) $row['conversation_id'],
                'last_message' => $row['last_message'],
                'last_message_at' => $row['last_message_at'],
                'unread_count' => $unreadByConv[$row['conversation_id']] ?? 0,
                'opponent' => [
                    'user_id' => (int) $row['opponent_user_id'],
                    'grid_id' => $row['grid_id'],
                    'name' => trim($row['first_name'] . ' ' . ($row['last_name'] ?? '')),
                    'avatar' => $row['grid_avatar'] ?? '',
                    'headline' => $row['headline']
                ]
            ];
        }

        return $conversations;
    }

    /**
     * Fetch a conversation's messages, verifying $user_id is a participant, and
     * mark any unread incoming messages as read. Returns null if the conversation
     * doesn't exist or $user_id isn't one of its two participants.
     */
    function get_conversation_messages($conversation_id, $user_id, $limit = 50) {
        $conversation_id = (int) $conversation_id;
        $user_id = (int) $user_id;

        $conversation = $this->db->table('grid_conversations')
            ->select('id, user_one_id, user_two_id')
            ->where('id', $conversation_id)
            ->get()
            ->getRowArray();

        if (!$conversation) {
            return null;
        }

        if ((int) $conversation['user_one_id'] !== $user_id && (int) $conversation['user_two_id'] !== $user_id) {
            return null;
        }

        $opponentId = ((int) $conversation['user_one_id'] === $user_id)
            ? (int) $conversation['user_two_id']
            : (int) $conversation['user_one_id'];

        $opponentUser = $this->db->table('users')
            ->select('id, first_name, last_name, image')
            ->where('id', $opponentId)
            ->get()
            ->getRowArray();

        $opponentProfile = $this->db->table('grid_profiles')
            ->select('grid_id, headline, avatar')
            ->where('user_id', $opponentId)
            ->get()
            ->getRowArray();

        // Mark incoming messages as read now that the participant has opened the thread.
        $this->db->table('grid_messages')
            ->where('conversation_id', $conversation_id)
            ->where('recipient_id', $user_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $messageRows = $this->db->table('grid_messages')
            ->where('conversation_id', $conversation_id)
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $messages = [];
        foreach ($messageRows as $m) {
            $messages[] = [
                'id' => (int) $m['id'],
                'sender_id' => (int) $m['sender_id'],
                'recipient_id' => (int) $m['recipient_id'],
                'message' => $m['message_body'],
                'is_read' => (bool) $m['is_read'],
                'is_mine' => (int) $m['sender_id'] === $user_id,
                'created_at' => $m['created_at']
            ];
        }

        return [
            'conversation_id' => $conversation_id,
            'opponent' => [
                'user_id' => $opponentId,
                'grid_id' => $opponentProfile['grid_id'] ?? null,
                'name' => $opponentUser ? trim($opponentUser['first_name'] . ' ' . ($opponentUser['last_name'] ?? '')) : '',
                'avatar' => $opponentProfile['avatar'] ?? '',
                'headline' => $opponentProfile['headline'] ?? null
            ],
            'messages' => $messages
        ];
    }

    /**
     * Send a message, creating the conversation if needed, and bump the
     * conversation's last_message / last_message_at for the conversation list.
     */
    function send_message($sender_id, $recipient_id, $body) {
        $sender_id = (int) $sender_id;
        $recipient_id = (int) $recipient_id;
        $body = trim((string) $body);

        $conversationId = $this->get_or_create_conversation($sender_id, $recipient_id);
        $now = date('Y-m-d H:i:s');

        $this->db->table('grid_messages')->insert([
            'conversation_id' => $conversationId,
            'sender_id' => $sender_id,
            'recipient_id' => $recipient_id,
            'message_body' => $body,
            'is_read' => 0,
            'created_at' => $now
        ]);

        $messageId = (int) $this->db->insertID();

        $this->db->table('grid_conversations')
            ->where('id', $conversationId)
            ->update([
                'last_message' => $body,
                'last_message_at' => $now
            ]);

        return [
            'id' => $messageId,
            'conversation_id' => $conversationId,
            'sender_id' => $sender_id,
            'recipient_id' => $recipient_id,
            'message' => $body,
            'is_read' => false,
            'is_mine' => true,
            'created_at' => $now
        ];
    }
}
