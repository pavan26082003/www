<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * GRID Chat profile row in grid_chat_db.user_profiles.
 * user_id = factor_db2.users.id (linked by the app, no foreign key).
 */
class UserProfileModel extends Model
{
    protected $DBGroup          = 'gridchat';
    protected $table            = 'user_profiles';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'display_name',
        'headline',
        'phone',
        'bio',
        'avatar_path',
    ];

    /** @var array<int, array> merged users for this request */
    private array $userCache = [];

    public function getByUserId(int $userId): ?array
    {
        return $this->find($userId);
    }

    /**
     * Insert or update the profile row for a user.
     */
    public function upsert(int $userId, array $data): bool
    {
        $data = array_intersect_key($data, array_flip($this->allowedFields));
        unset($data['user_id']);

        if ($this->find($userId)) {
            return empty($data) ? true : (bool) $this->update($userId, $data);
        }

        $data['user_id'] = $userId;
        return $this->insert($data) !== false;
    }

    /**
     * Users for display: CRM details (factor_db2, read-only) with the user's
     * own chat profile overrides applied.
     *
     * @param int[] $ids
     * @return array<int, array> keyed by user id: id, name, email, phone, role, bio, avatar, isDeleted
     */
    public function getUsers(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0)));
        $missing = array_values(array_diff($ids, array_keys($this->userCache)));

        if ($missing) {
            $crmUsers = (new CrmUserModel())->findByIds($missing);

            $profiles = [];
            foreach ($this->builder()->whereIn('user_id', $missing)->get()->getResultArray() as $profile) {
                $profiles[(int) $profile['user_id']] = $profile;
            }

            foreach ($missing as $id) {
                $this->userCache[$id] = $this->mergeUser($id, $crmUsers[$id] ?? null, $profiles[$id] ?? null);
            }
        }

        $users = [];
        foreach ($ids as $id) {
            $users[$id] = $this->userCache[$id];
        }
        return $users;
    }

    public function getUser(int $id): array
    {
        return $this->getUsers([$id])[$id] ?? $this->mergeUser($id, null, null);
    }

    /**
     * Active CRM users to start a chat with.
     */
    public function searchContacts(int $excludeId, string $query, int $limit, int $offset): array
    {
        $limit = max(1, min(100, $limit));
        $ids = (new CrmUserModel())->searchActiveIds($excludeId, $query, $limit, $offset);

        return [
            'contacts' => array_values($this->getUsers(array_slice($ids, 0, $limit))),
            'hasMore' => count($ids) > $limit,
        ];
    }

    private function mergeUser(int $id, ?array $crm, ?array $profile): array
    {
        if ($crm === null || (int) $crm['deleted'] !== 0) {
            return [
                'id' => $id,
                'name' => 'Deleted user',
                'email' => '',
                'phone' => '',
                'role' => '',
                'bio' => '',
                'avatar' => '',
                'avatarSource' => 'none',
                'isDeleted' => true,
            ];
        }

        $crmName = trim(($crm['first_name'] ?? '') . ' ' . ($crm['last_name'] ?? ''));
        if ($crmName === '') {
            $crmName = explode('@', (string) $crm['email'])[0] ?: 'User';
        }

        // Uploaded chat photo → CRM photo → '' (the app then shows the name's initials)
        $avatar = '';
        $avatarSource = 'none';
        if (!empty($profile['avatar_path'])) {
            $avatar = base_url($profile['avatar_path']);
            $avatarSource = 'chat';
        } elseif (!empty($crm['image'])) {
            $avatar = get_avatar($crm['image']);
            $avatarSource = 'crm';
        }

        return [
            'id' => $id,
            'name' => ($profile['display_name'] ?? '') ?: $crmName,
            'email' => (string) ($crm['email'] ?? ''),
            'phone' => ($profile['phone'] ?? '') ?: (string) ($crm['phone'] ?? ''),
            'role' => ($profile['headline'] ?? '') ?: (string) ($crm['job_title'] ?? ''),
            'bio' => (string) ($profile['bio'] ?? ''),
            'avatar' => $avatar,
            'avatarSource' => $avatarSource,
            'isDeleted' => false,
        ];
    }
}
