<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * grid_chat_db.poll_votes — user votes on poll messages.
 */
class PollVoteModel extends Model
{
    protected $DBGroup       = 'gridchat';
    protected $table         = 'poll_votes';
    protected $primaryKey    = 'message_id';
    protected $returnType    = 'array';
    protected $allowedFields = ['message_id', 'user_id', 'option_index', 'voted_at'];

    /**
     * Record or update a user's vote for a poll message.
     */
    public function recordVote(int $messageId, int $userId, int $optionIndex): bool
    {
        $sql = 'INSERT INTO poll_votes (message_id, user_id, option_index, voted_at)
                VALUES (?, ?, ?, NOW(3))
                ON DUPLICATE KEY UPDATE option_index = VALUES(option_index), voted_at = VALUES(voted_at)';
        return (bool) $this->db->query($sql, [$messageId, $userId, $optionIndex]);
    }

    /**
     * Fetch all votes for an array of message IDs.
     */
    public function getVotesForMessages(array $messageIds): array
    {
        if (empty($messageIds)) {
            return [];
        }

        return $this->db->table('poll_votes')
            ->whereIn('message_id', $messageIds)
            ->get()
            ->getResultArray();
    }

    /**
     * Get aggregated vote summary for a specific poll message.
     */
    public function getVoteSummary(int $messageId, int $userId): array
    {
        $rows = $this->db->table('poll_votes')
            ->where('message_id', $messageId)
            ->get()
            ->getResultArray();

        $optionCounts = [];
        $userVote = null;
        $totalVotes = count($rows);

        foreach ($rows as $row) {
            $opt = (int) $row['option_index'];
            $optionCounts[$opt] = ($optionCounts[$opt] ?? 0) + 1;
            if ((int) $row['user_id'] === $userId) {
                $userVote = $opt;
            }
        }

        return [
            'counts' => $optionCounts,
            'userVote' => $userVote,
            'totalVotes' => $totalVotes,
        ];
    }
}