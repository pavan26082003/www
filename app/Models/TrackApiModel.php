<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * GPS Tracking Phase 0 data access.
 * All rows are scoped by app_id + tenant_id.
 */
class TrackApiModel extends Model
{
    protected $DBGroup = 'default';

    private bool $schemaReady = false;

    public function ensureSchema(): void
    {
        if ($this->schemaReady) {
            return;
        }

        $db = $this->db;
        $db->query("CREATE TABLE IF NOT EXISTS `track_policies` (
          `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `app_id` VARCHAR(191) NOT NULL,
          `tenant_id` VARCHAR(191) NOT NULL,
          `name` VARCHAR(191) NOT NULL DEFAULT 'default',
          `version` VARCHAR(64) NOT NULL DEFAULT '1',
          `mode` VARCHAR(32) NOT NULL DEFAULT 'interval',
          `interval_seconds` INT UNSIGNED NOT NULL DEFAULT 5,
          `geofence_json` LONGTEXT NULL,
          `upload_json` LONGTEXT NULL,
          `events_json` LONGTEXT NULL,
          `retention_days` INT UNSIGNED NOT NULL DEFAULT 90,
          `policy_json` LONGTEXT NULL,
          `is_active` TINYINT(1) NOT NULL DEFAULT 1,
          `created_at` DATETIME NOT NULL,
          `updated_at` DATETIME NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uq_track_policies_app_tenant_name` (`app_id`, `tenant_id`, `name`),
          KEY `idx_track_policies_scope` (`app_id`, `tenant_id`, `is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $db->query("CREATE TABLE IF NOT EXISTS `track_policy_bindings` (
          `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `app_id` VARCHAR(191) NOT NULL,
          `tenant_id` VARCHAR(191) NOT NULL,
          `policy_id` BIGINT UNSIGNED NOT NULL,
          `device_id` VARCHAR(191) NULL,
          `user_id` VARCHAR(191) NULL,
          `priority` INT NOT NULL DEFAULT 0,
          `created_at` DATETIME NOT NULL,
          `updated_at` DATETIME NOT NULL,
          PRIMARY KEY (`id`),
          KEY `idx_track_bindings_lookup` (`app_id`, `tenant_id`, `device_id`, `user_id`),
          KEY `idx_track_bindings_policy` (`policy_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $db->query("CREATE TABLE IF NOT EXISTS `track_breadcrumbs` (
          `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `app_id` VARCHAR(191) NOT NULL,
          `tenant_id` VARCHAR(191) NOT NULL,
          `device_id` VARCHAR(191) NOT NULL,
          `user_id` VARCHAR(191) NOT NULL,
          `client_uuid` VARCHAR(64) NOT NULL,
          `recorded_at` DATETIME NOT NULL,
          `lat` DOUBLE NOT NULL,
          `lng` DOUBLE NOT NULL,
          `accuracy_m` DOUBLE NULL,
          `speed_mps` DOUBLE NULL,
          `heading` DOUBLE NULL,
          `altitude_m` DOUBLE NULL,
          `battery_pct` INT NULL,
          `provider` VARCHAR(32) NULL,
          `created_at` DATETIME NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uq_track_breadcrumbs_idem` (`app_id`, `tenant_id`, `client_uuid`),
          KEY `idx_track_breadcrumbs_history` (`app_id`, `tenant_id`, `user_id`, `recorded_at`),
          KEY `idx_track_breadcrumbs_device` (`app_id`, `tenant_id`, `device_id`, `recorded_at`),
          KEY `idx_track_breadcrumbs_purge` (`recorded_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $db->query("CREATE TABLE IF NOT EXISTS `track_events` (
          `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `app_id` VARCHAR(191) NOT NULL,
          `tenant_id` VARCHAR(191) NOT NULL,
          `device_id` VARCHAR(191) NOT NULL,
          `user_id` VARCHAR(191) NOT NULL,
          `client_uuid` VARCHAR(64) NOT NULL,
          `type` VARCHAR(64) NOT NULL,
          `recorded_at` DATETIME NOT NULL,
          `lat` DOUBLE NULL,
          `lng` DOUBLE NULL,
          `accuracy_m` DOUBLE NULL,
          `site_id` VARCHAR(191) NULL,
          `note` VARCHAR(512) NULL,
          `payload_json` LONGTEXT NULL,
          `created_at` DATETIME NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uq_track_events_idem` (`app_id`, `tenant_id`, `client_uuid`),
          KEY `idx_track_events_lookup` (`app_id`, `tenant_id`, `user_id`, `recorded_at`),
          KEY `idx_track_events_type` (`app_id`, `tenant_id`, `type`, `recorded_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $db->query("CREATE TABLE IF NOT EXISTS `track_live_positions` (
          `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `app_id` VARCHAR(191) NOT NULL,
          `tenant_id` VARCHAR(191) NOT NULL,
          `device_id` VARCHAR(191) NOT NULL,
          `user_id` VARCHAR(191) NOT NULL,
          `lat` DOUBLE NOT NULL,
          `lng` DOUBLE NOT NULL,
          `accuracy_m` DOUBLE NULL,
          `speed_mps` DOUBLE NULL,
          `heading` DOUBLE NULL,
          `altitude_m` DOUBLE NULL,
          `battery_pct` INT NULL,
          `provider` VARCHAR(32) NULL,
          `recorded_at` DATETIME NOT NULL,
          `updated_at` DATETIME NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uq_track_live_device` (`app_id`, `tenant_id`, `device_id`),
          KEY `idx_track_live_user` (`app_id`, `tenant_id`, `user_id`),
          KEY `idx_track_live_scope` (`app_id`, `tenant_id`, `updated_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->ensureUsersTenantColumn();
        // Demo seed only — production tenants are auto-provisioned on first use.
        $this->ensureTenantPolicy('efactor_field_tracker', 'tenant_demo_001', 'default_field_tracker');
        // Walk-test policy: background stays on, and v bumps so a cached v1 is refetched.
        $this->publishDemoBackgroundPolicy();
        $this->schemaReady = true;
    }

    /**
     * Ensure a tenant-wide default policy exists for ANY app_id + tenant_id.
     * Not user-specific: binding has device_id/user_id = NULL so all users in the tenant share it.
     * Schema unchanged — inserts into existing columns only.
     *
     * @return array<string, mixed>|null
     */
    public function ensureTenantPolicy(string $appId, string $tenantId, string $name = 'default'): ?array
    {
        $appId = trim($appId);
        $tenantId = trim($tenantId);
        if ($appId === '' || $tenantId === '') {
            return null;
        }

        $existing = $this->db->table('track_policies')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        if (is_array($existing)) {
            $binding = $this->db->table('track_policy_bindings')
                ->where('app_id', $appId)
                ->where('tenant_id', $tenantId)
                ->where('policy_id', (int) $existing['id'])
                ->get()
                ->getRowArray();
            if (!is_array($binding)) {
                $now = date('Y-m-d H:i:s');
                $this->db->table('track_policy_bindings')->insert([
                    'app_id' => $appId,
                    'tenant_id' => $tenantId,
                    'policy_id' => (int) $existing['id'],
                    'device_id' => null,
                    'user_id' => null,
                    'priority' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            return $existing;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('track_policies')->insert([
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'name' => $name !== '' ? $name : 'default',
            'version' => '1',
            'mode' => 'interval',
            'interval_seconds' => 5,
            'geofence_json' => null,
            'upload_json' => json_encode([
                'batch_max_points' => 50,
                'batch_max_interval_seconds' => 60,
                'wifi_only' => false,
            ]),
            'events_json' => json_encode([
                'check_in_out_enabled' => true,
            ]),
            'retention_days' => 90,
            'policy_json' => json_encode([
                'background' => self::defaultBackgroundPolicy(),
            ]),
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $policyId = (int) $this->db->insertID();
        if ($policyId <= 0) {
            return null;
        }

        $this->db->table('track_policy_bindings')->insert([
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'policy_id' => $policyId,
            'device_id' => null,
            'user_id' => null,
            'priority' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->getPolicyById($policyId, $appId, $tenantId);
    }

    /**
     * Publish the field-tracker walk-test policy.
     * background.enabled is true with its own interval, and version is at least 2
     * so a device that cached v1 (background off) gets HTTP 200 instead of 304.
     */
    public function publishDemoBackgroundPolicy(): void
    {
        $appId = 'efactor_field_tracker';
        $tenantId = 'tenant_demo_001';
        $row = $this->ensureTenantPolicy($appId, $tenantId, 'default_field_tracker');
        if (!is_array($row)) {
            return;
        }

        $desired = [
            'enabled' => true,
            'interval_seconds' => 30,
            'distance_meters' => null,
            'mode' => 'interval',
        ];
        $extra = $this->decodeJson($row['policy_json'] ?? null);
        if (!is_array($extra)) {
            $extra = [];
        }
        $current = is_array($extra['background'] ?? null) ? $extra['background'] : [];
        $version = (int) ($row['version'] ?? 1);
        $matches = ($current['enabled'] ?? null) === true
            && ($current['mode'] ?? null) === 'interval'
            && array_key_exists('interval_seconds', $current)
            && (int) ($current['interval_seconds'] ?? 0) === 30
            && array_key_exists('distance_meters', $current)
            && $current['distance_meters'] === null
            && $version >= 2;
        if ($matches) {
            return;
        }

        $extra['background'] = $desired;
        $this->updatePolicy((int) $row['id'], [
            'policy_json' => json_encode($extra),
            'version' => (string) max($version + 1, 2),
        ]);
    }

    public function userExists(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        try {
            $row = $this->db->table('users')->select('id')->where('id', $userId)->get()->getRowArray();
        } catch (\Throwable $e) {
            return false;
        }

        return is_array($row);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPolicyById(int $id, string $appId = '', string $tenantId = ''): ?array
    {
        $builder = $this->db->table('track_policies')->where('id', $id);
        if ($appId !== '') {
            $builder->where('app_id', $appId);
        }
        if ($tenantId !== '') {
            $builder->where('tenant_id', $tenantId);
        }
        $row = $builder->get()->getRowArray();
        return is_array($row) ? $row : null;
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function updatePolicy(int $id, array $fields): bool
    {
        $fields['updated_at'] = date('Y-m-d H:i:s');
        return (bool) $this->db->table('track_policies')->where('id', $id)->update($fields);
    }

    /**
     * Resolve merged effective policy for device/user within app+tenant.
     *
     * @return array<string, mixed>|null
     */
    public function resolveEffectivePolicy(string $appId, string $tenantId, string $deviceId = '', string $userId = ''): ?array
    {
        // Production: auto-create tenant-wide policy for any real app/tenant (not seed-only).
        $this->ensureTenantPolicy($appId, $tenantId);

        $bindings = $this->db->table('track_policy_bindings')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->orderBy('priority', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $chosenPolicyId = 0;
        foreach ($bindings as $binding) {
            $bDevice = trim((string) ($binding['device_id'] ?? ''));
            $bUser = trim((string) ($binding['user_id'] ?? ''));

            if ($bDevice !== '' && $deviceId !== '' && $bDevice === $deviceId) {
                $chosenPolicyId = (int) $binding['policy_id'];
                break;
            }
            if ($bUser !== '' && $userId !== '' && $bUser === $userId && $bDevice === '') {
                if ($chosenPolicyId <= 0) {
                    $chosenPolicyId = (int) $binding['policy_id'];
                }
            }
            if ($bDevice === '' && $bUser === '' && $chosenPolicyId <= 0) {
                $chosenPolicyId = (int) $binding['policy_id'];
            }
        }

        if ($chosenPolicyId > 0) {
            $policy = $this->getPolicyById($chosenPolicyId, $appId, $tenantId);
            if (is_array($policy) && (int) ($policy['is_active'] ?? 0) === 1) {
                return $policy;
            }
        }

        $fallback = $this->db->table('track_policies')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        return is_array($fallback) ? $fallback : null;
    }

    /**
     * @param array<string, mixed> $point
     * @return string accepted|duplicate|rejected
     */
    public function insertBreadcrumb(array $point): string
    {
        $appId = (string) $point['app_id'];
        $tenantId = (string) $point['tenant_id'];
        $clientUuid = (string) $point['client_uuid'];

        $exists = $this->db->table('track_breadcrumbs')
            ->select('id')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->where('client_uuid', $clientUuid)
            ->get()
            ->getRowArray();
        if (is_array($exists)) {
            return 'duplicate';
        }

        try {
            $ok = $this->db->table('track_breadcrumbs')->insert($point);
            if (!$ok) {
                return 'rejected';
            }
        } catch (\Throwable $e) {
            $msg = strtolower($e->getMessage());
            if (strpos($msg, 'duplicate') !== false) {
                return 'duplicate';
            }
            log_message('error', 'track breadcrumb insert: ' . $e->getMessage());
            return 'rejected';
        }

        $this->upsertLivePosition([
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'device_id' => (string) $point['device_id'],
            'user_id' => (string) $point['user_id'],
            'lat' => (float) $point['lat'],
            'lng' => (float) $point['lng'],
            'accuracy_m' => $point['accuracy_m'] ?? null,
            'speed_mps' => $point['speed_mps'] ?? null,
            'heading' => $point['heading'] ?? null,
            'altitude_m' => $point['altitude_m'] ?? null,
            'battery_pct' => $point['battery_pct'] ?? null,
            'provider' => $point['provider'] ?? null,
            'recorded_at' => (string) $point['recorded_at'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return 'accepted';
    }

    /**
     * @param array<string, mixed> $event
     * @return string accepted|duplicate|rejected
     */
    public function insertEvent(array $event): string
    {
        $appId = (string) $event['app_id'];
        $tenantId = (string) $event['tenant_id'];
        $clientUuid = (string) $event['client_uuid'];

        $exists = $this->db->table('track_events')
            ->select('id')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->where('client_uuid', $clientUuid)
            ->get()
            ->getRowArray();
        if (is_array($exists)) {
            return 'duplicate';
        }

        try {
            $ok = $this->db->table('track_events')->insert($event);
            if (!$ok) {
                return 'rejected';
            }
        } catch (\Throwable $e) {
            $msg = strtolower($e->getMessage());
            if (strpos($msg, 'duplicate') !== false) {
                return 'duplicate';
            }
            log_message('error', 'track event insert: ' . $e->getMessage());
            return 'rejected';
        }

        if (isset($event['lat'], $event['lng']) && $event['lat'] !== null && $event['lng'] !== null) {
            $this->upsertLivePosition([
                'app_id' => $appId,
                'tenant_id' => $tenantId,
                'device_id' => (string) $event['device_id'],
                'user_id' => (string) $event['user_id'],
                'lat' => (float) $event['lat'],
                'lng' => (float) $event['lng'],
                'accuracy_m' => $event['accuracy_m'] ?? null,
                'speed_mps' => null,
                'heading' => null,
                'altitude_m' => null,
                'battery_pct' => null,
                'provider' => 'event',
                'recorded_at' => (string) $event['recorded_at'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return 'accepted';
    }

    /**
     * @param array<string, mixed> $row
     */
    public function upsertLivePosition(array $row): void
    {
        $existing = $this->db->table('track_live_positions')
            ->select('id, recorded_at')
            ->where('app_id', $row['app_id'])
            ->where('tenant_id', $row['tenant_id'])
            ->where('device_id', $row['device_id'])
            ->get()
            ->getRowArray();

        if (!is_array($existing)) {
            $this->db->table('track_live_positions')->insert($row);
            return;
        }

        // Keep newest recorded_at only.
        if (!empty($existing['recorded_at']) && !empty($row['recorded_at'])
            && strtotime((string) $row['recorded_at']) < strtotime((string) $existing['recorded_at'])) {
            return;
        }

        $this->db->table('track_live_positions')
            ->where('id', (int) $existing['id'])
            ->update($row);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function livePositions(string $appId, string $tenantId, string $userId = ''): array
    {
        $builder = $this->db->table('track_live_positions')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->orderBy('updated_at', 'DESC');

        if ($userId !== '') {
            $builder->where('user_id', $userId);
        }

        $rows = $builder->get()->getResultArray();
        return is_array($rows) ? $rows : [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    /**
     * Route history for a tenant. user_id is optional:
     * - with user_id → that user's track
     * - empty → all users in the tenant (not locked to one test user)
     *
     * @return list<array<string, mixed>>
     */
    public function history(string $appId, string $tenantId, string $userId, string $from, string $to): array
    {
        $builder = $this->db->table('track_breadcrumbs')
            ->where('app_id', $appId)
            ->where('tenant_id', $tenantId)
            ->where('recorded_at >=', $from)
            ->where('recorded_at <=', $to)
            ->orderBy('recorded_at', 'ASC')
            ->limit(20000);

        if ($userId !== '') {
            $builder->where('user_id', $userId);
        }

        $rows = $builder->get()->getResultArray();
        return is_array($rows) ? $rows : [];
    }

    public function purgeExpiredBreadcrumbs(int $retentionDays = 90): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - max(1, $retentionDays) * 86400);
        $this->db->table('track_breadcrumbs')->where('recorded_at <', $cutoff)->delete();
        return $this->db->affectedRows();
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    /**
     * Default background / power behaviour for field apps.
     * Stored in policy_json (no track_* schema change).
     *
     * @return array<string, mixed>
     */
    public static function defaultBackgroundPolicy(): array
    {
        return [
            'enabled' => true,
            // null = reuse top-level interval_seconds (battery-friendly default).
            'interval_seconds' => null,
            'distance_meters' => null,
            'mode' => 'interval', // interval | distance | same
        ];
    }

    /**
     * Resolve tenant_id for a logged-in user (login / profile / track/me).
     */
    public function resolveTenantIdForUser(int $userId): string
    {
        if ($userId <= 0) {
            return 'tenant_demo_001';
        }

        $this->ensureUsersTenantColumn();

        try {
            $hasTenantCol = $this->db->fieldExists('tenant_id', 'users');
            $select = $hasTenantCol ? 'tenant_id, client_id' : 'client_id';
            $row = $this->db->table('users')
                ->select($select)
                ->where('id', $userId)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            return 'tenant_demo_001';
        }

        if (!is_array($row)) {
            return 'tenant_demo_001';
        }

        $stored = $hasTenantCol ? trim((string) ($row['tenant_id'] ?? '')) : '';
        $resolved = self::resolveTenantId($stored, (int) ($row['client_id'] ?? 0));
        if ($hasTenantCol && $resolved !== $stored) {
            try {
                $this->db->table('users')->where('id', $userId)->update(['tenant_id' => $resolved]);
            } catch (\Throwable $e) {
                log_message('error', 'users.tenant_id persist: ' . $e->getMessage());
            }
        }

        return $resolved;
    }

    /**
     * Explicit tenant wins. A blank or backfilled tenant_demo_001 yields to
     * tenant_client_{client_id} when the account belongs to a client.
     * Accounts with no client are tenant_demo_001 — that value is assigned, not missing.
     */
    public static function resolveTenantId(string $stored, int $clientId): string
    {
        $stored = trim($stored);
        if ($stored !== '' && $stored !== 'tenant_demo_001') {
            return $stored;
        }
        if ($clientId > 0) {
            return 'tenant_client_' . $clientId;
        }

        return $stored !== '' ? $stored : 'tenant_demo_001';
    }

    private function ensureUsersTenantColumn(): void
    {
        try {
            if ($this->db->fieldExists('tenant_id', 'users')) {
                return;
            }
            $this->db->query('ALTER TABLE `users` ADD COLUMN `tenant_id` VARCHAR(191) NULL');
        } catch (\Throwable $e) {
            log_message('error', 'users.tenant_id column: ' . $e->getMessage());
        }
    }

    public function formatPolicyResponse(array $row): array
    {
        $geofence = $this->decodeJson($row['geofence_json'] ?? null);
        $upload = $this->decodeJson($row['upload_json'] ?? null);
        $events = $this->decodeJson($row['events_json'] ?? null);
        $extra = $this->decodeJson($row['policy_json'] ?? null);

        if (!is_array($upload)) {
            $upload = [
                'batch_max_points' => 50,
                'batch_max_interval_seconds' => 60,
                'wifi_only' => false,
            ];
        }
        if (!is_array($events)) {
            $events = ['check_in_out_enabled' => true];
        }

        $background = self::defaultBackgroundPolicy();
        if (is_array($extra) && isset($extra['background']) && is_array($extra['background'])) {
            $background = array_merge($background, $extra['background']);
        }

        $policy = [
            'id' => (int) $row['id'],
            'name' => (string) $row['name'],
            'mode' => (string) $row['mode'],
            'interval_seconds' => (int) $row['interval_seconds'],
            'geofence' => $geofence,
            'upload' => $upload,
            'events' => $events,
            'background' => $background,
            'retention_days' => (int) $row['retention_days'],
        ];

        if (is_array($extra) && $extra !== []) {
            $policy = array_merge($policy, $extra);
            // Keep canonical keys authoritative.
            $policy['id'] = (int) $row['id'];
            $policy['name'] = (string) $row['name'];
            $policy['mode'] = (string) $row['mode'];
            $policy['interval_seconds'] = (int) $row['interval_seconds'];
            $policy['geofence'] = $geofence;
            $policy['upload'] = $upload;
            $policy['events'] = $events;
            $policy['background'] = $background;
            $policy['retention_days'] = (int) $row['retention_days'];
        }

        return $policy;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJson($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : null;
    }
}
