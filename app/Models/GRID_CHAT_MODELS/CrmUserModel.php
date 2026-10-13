<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * CRM users (factor_db2.users) — READ-ONLY for GRID Chat.
 * Never add insert/update/delete methods here; chat data lives in grid_chat_db.
 */
class CrmUserModel extends Model
{
    protected $DBGroup       = 'default';
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [];

    /**
     * @param int[] $ids
     * @return array<int, array> keyed by user id
     */
    public function findByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return [];
        }

        $rows = $this->db->table($this->table)
            ->select('id, first_name, last_name, email, phone, job_title, user_type, image, deleted')
            ->whereIn('id', $ids)
            ->get()->getResultArray();

        $users = [];
        foreach ($rows as $row) {
            $users[(int) $row['id']] = $row;
        }
        return $users;
    }

    /**
     * Ids of active users matching a search, excluding one user.
     * Fetches one extra row so the caller can tell if there are more.
     *
     * @return int[]
     */
    public function searchActiveIds(int $excludeId, string $query, int $limit, int $offset): array
    {
        $builder = $this->db->table($this->table)
            ->select('id')
            ->where('deleted', 0)
            ->where('id !=', $excludeId);

        $query = trim($query);
        if ($query !== '') {
            $builder->groupStart()
                ->like('first_name', $query)
                ->orLike('last_name', $query)
                ->orLike('email', $query)
                ->orLike('phone', $query)
                ->groupEnd();
        }

        $rows = $builder->orderBy('first_name', 'ASC')
            ->limit($limit + 1, max(0, $offset))
            ->get()->getResultArray();

        return array_map(static fn ($row) => (int) $row['id'], $rows);
    }
}
