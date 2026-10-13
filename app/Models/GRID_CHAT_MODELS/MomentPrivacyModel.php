<?php

namespace App\Models\GRID_CHAT_MODELS;

use CodeIgniter\Model;

/**
 * grid_chat_db.moment_privacy_settings — a user's current answers to
 * "Who can see my statuses?" and "Who can download my statuses?", plus the
 * people they picked for the "except..." and "only..." options
 * (moment_privacy_targets).
 *
 * These are the CURRENT settings. When a moment is posted the share setting is
 * copied onto the moment itself (MomentModel::createWithAudience), so editing
 * these values never changes who can see an already-published moment.
 */
class MomentPrivacyModel extends Model
{
    protected $DBGroup          = 'gridchat';
    protected $table            = 'moment_privacy_settings';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'share_audience', 'download_audience', 'updated_at'];

    public const SETTINGS  = ['share', 'download'];
    public const AUDIENCES = ['contacts', 'contacts_except', 'only'];

    private const TARGETS_TABLE = 'moment_privacy_targets';

    public function __construct()
    {
        parent::__construct();
        helper('grid_chat');
    }

    /**
     * Both settings for a user, falling back to "My contacts" when they have
     * never opened the panels.
     *
     * @return array{share: array{audience: string, targetIds: int[]}, download: array{audience: string, targetIds: int[]}}
     */
    public function getSettings(int $userId): array
    {
        $row = $this->find($userId);
        $targets = $this->targetsBySetting($userId);

        return [
            'share' => [
                'audience'  => $row['share_audience'] ?? 'contacts',
                'targetIds' => $targets['share'] ?? [],
            ],
            'download' => [
                'audience'  => $row['download_audience'] ?? 'contacts',
                'targetIds' => $targets['download'] ?? [],
            ],
        ];
    }

    /**
     * Replaces one setting and its picked people in a single transaction.
     *
     * @param string $setting  'share' or 'download'
     * @param int[]  $targetIds
     */
    public function saveSetting(int $userId, string $setting, string $audience, array $targetIds): void
    {
        if (!in_array($setting, self::SETTINGS, true) || !in_array($audience, self::AUDIENCES, true)) {
            throw new \InvalidArgumentException('Unknown status privacy setting');
        }

        $this->db->transBegin();

        try {
            $this->upsertAudience($userId, $setting, $audience);

            $this->db->table(self::TARGETS_TABLE)
                ->where('user_id', $userId)
                ->where('setting', $setting)
                ->delete();

            // "My contacts" means everyone, so it never keeps a list
            if ($audience !== 'contacts' && $targetIds) {
                $this->db->table(self::TARGETS_TABLE)->insertBatch(array_map(
                    static fn (int $id) => [
                        'user_id'        => $userId,
                        'setting'        => $setting,
                        'target_user_id' => $id,
                    ],
                    $targetIds
                ));
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Whether each of these users lets the viewer download their statuses.
     * Users with no saved setting allow everyone.
     *
     * @param int[] $ownerIds
     * @return array<int, bool> owner id → may download
     */
    public function downloadAllowedFor(array $ownerIds, int $viewerId): array
    {
        $ownerIds = array_values(array_unique(array_filter(array_map('intval', $ownerIds))));
        if (!$ownerIds) {
            return [];
        }

        $allowed = array_fill_keys($ownerIds, true);

        $rows = $this->builder()
            ->select('user_id, download_audience')
            ->whereIn('user_id', $ownerIds)
            ->where('download_audience !=', 'contacts')
            ->get()->getResultArray();
        if (!$rows) {
            return $allowed;
        }

        $restricted = array_column($rows, 'download_audience', 'user_id');

        $listed = array_column(
            $this->db->table(self::TARGETS_TABLE)
                ->select('user_id')
                ->whereIn('user_id', array_keys($restricted))
                ->where('setting', 'download')
                ->where('target_user_id', $viewerId)
                ->get()->getResultArray(),
            'user_id'
        );
        $listed = array_flip(array_map('intval', $listed));

        foreach ($restricted as $ownerId => $audience) {
            $ownerId = (int) $ownerId;
            if ($ownerId === $viewerId) {
                continue; // you can always download your own
            }
            $isListed = isset($listed[$ownerId]);
            $allowed[$ownerId] = $audience === 'contacts_except' ? !$isListed : $isListed;
        }

        return $allowed;
    }

    /** @return array<string, int[]> setting → picked user ids */
    private function targetsBySetting(int $userId): array
    {
        $rows = $this->db->table(self::TARGETS_TABLE)
            ->select('setting, target_user_id')
            ->where('user_id', $userId)
            ->get()->getResultArray();

        $targets = [];
        foreach ($rows as $row) {
            $targets[$row['setting']][] = (int) $row['target_user_id'];
        }
        return $targets;
    }

    private function upsertAudience(int $userId, string $setting, string $audience): void
    {
        $column = $setting === 'share' ? 'share_audience' : 'download_audience';

        if ($this->find($userId)) {
            $this->update($userId, [$column => $audience, 'updated_at' => grid_chat_now()]);
            return;
        }

        $this->insert([
            'user_id'    => $userId,
            $column      => $audience,
            'updated_at' => grid_chat_now(),
        ]);
    }
}
