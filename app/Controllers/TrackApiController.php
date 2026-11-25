<?php

namespace App\Controllers;

use App\Libraries\DevicePushService;
use App\Models\TrackApiModel;
use CodeIgniter\Controller;

/**
 * GPS Tracking Phase 0 API — /index.php/api/track/*
 *
 * Auth: Authorization: Bearer <users.access_token> (same as mobile push APIs).
 * Scoping: every row/query filtered by app_id + tenant_id.
 * Idempotency: UNIQUE(app_id, tenant_id, client_uuid) on breadcrumbs + events.
 */
class TrackApiController extends Controller
{
    private TrackApiModel $track;

    public function __construct()
    {
        $this->track = new TrackApiModel();
        $this->track->ensureSchema();
    }

    /**
     * PUT /api/track/policies/{id}
     */
    public function updatePolicy($id = null)
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $policyId = (int) $id;
        if ($policyId <= 0) {
            return $this->jsonFail('Invalid policy id', 422);
        }

        $body = $this->jsonBody();
        $appId = $this->str($body['app_id'] ?? $this->request->getGet('app_id'));
        $tenantId = $this->str($body['tenant_id'] ?? $this->request->getGet('tenant_id'));
        if ($appId === '' || $tenantId === '') {
            return $this->jsonFail('app_id and tenant_id are required', 422);
        }

        $existing = $this->track->getPolicyById($policyId, $appId, $tenantId);
        if ($existing === null) {
            return $this->jsonFail('Policy not found', 404);
        }

        $fields = [];
        if (isset($body['name'])) {
            $fields['name'] = $this->str($body['name'], 191);
        }
        if (isset($body['mode'])) {
            $mode = strtolower($this->str($body['mode'], 32));
            if (!in_array($mode, ['interval', 'continuous', 'geofence', 'manual'], true)) {
                return $this->jsonFail('mode must be interval|continuous|geofence|manual', 422);
            }
            $fields['mode'] = $mode;
        }
        if (isset($body['interval_seconds'])) {
            $fields['interval_seconds'] = max(1, (int) $body['interval_seconds']);
        }
        if (array_key_exists('geofence', $body)) {
            $fields['geofence_json'] = $body['geofence'] === null ? null : json_encode($body['geofence']);
        }
        if (isset($body['upload']) && is_array($body['upload'])) {
            $fields['upload_json'] = json_encode($body['upload']);
        }
        if (isset($body['events']) && is_array($body['events'])) {
            $fields['events_json'] = json_encode($body['events']);
        }
        if (isset($body['background']) && is_array($body['background'])) {
            $extra = [];
            if (!empty($existing['policy_json'])) {
                $decoded = json_decode((string) $existing['policy_json'], true);
                if (is_array($decoded)) {
                    $extra = $decoded;
                }
            }
            $extra['background'] = array_merge(
                TrackApiModel::defaultBackgroundPolicy(),
                is_array($extra['background'] ?? null) ? $extra['background'] : [],
                $body['background']
            );
            $fields['policy_json'] = json_encode($extra);
        }
        if (isset($body['retention_days'])) {
            $fields['retention_days'] = max(1, (int) $body['retention_days']);
        }
        if (isset($body['is_active'])) {
            $fields['is_active'] = ((int) $body['is_active']) ? 1 : 0;
        }

        // Bump version on any edit unless caller supplies explicit v.
        if (isset($body['v']) && $this->str($body['v']) !== '') {
            $fields['version'] = $this->str($body['v'], 64);
        } else {
            $fields['version'] = (string) ((int) ($existing['version'] ?? 1) + 1);
        }

        if ($fields === []) {
            return $this->jsonFail('No policy fields to update', 422);
        }

        $this->track->updatePolicy($policyId, $fields);
        $updated = $this->track->getPolicyById($policyId, $appId, $tenantId);

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Policy updated',
            'v' => (string) ($updated['version'] ?? $fields['version']),
            'policy' => $this->track->formatPolicyResponse($updated ?? $existing),
        ]);
    }

    /**
     * GET /api/track/me
     * Returns authenticated user_id + resolved tenant_id (same as login).
     */
    public function me()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $tenantId = $this->track->resolveTenantIdForUser((int) $user['id']);

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Track session',
            'user_id' => (string) $user['id'],
            'tenant_id' => $tenantId,
            'app_id' => 'efactor_field_tracker',
        ]);
    }

    /**
     * POST /api/track/command
     * Admin or the user themselves. Sends a data-only FCM start/stop to the
     * user's registered device tokens. Does not embed the policy body.
     */
    public function command()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $body = $this->jsonBody();
        $action = strtolower($this->str($body['action'] ?? '', 16));
        $appId = $this->str($body['app_id'] ?? '');
        $tenantId = $this->str($body['tenant_id'] ?? '');
        $deviceId = $this->str($body['device_id'] ?? '', 191);
        $targetId = (int) ($body['user_id'] ?? 0);
        if ($targetId <= 0) {
            $targetId = (int) $user['id'];
        }

        if (!in_array($action, ['start', 'stop'], true)) {
            return $this->jsonFail('action must be start or stop', 422);
        }
        if ($appId === '' || $tenantId === '') {
            return $this->jsonFail('app_id and tenant_id are required', 422);
        }
        if (!$this->callerMayCommand((int) $user['id'], $targetId)) {
            return $this->jsonFail('You cannot command this user', 403);
        }
        if (!$this->track->userExists($targetId)) {
            return $this->jsonFail('User not found', 404);
        }

        $resolvedTenant = $this->track->resolveTenantIdForUser($targetId);
        if ($resolvedTenant !== $tenantId) {
            return $this->jsonFail('tenant_id does not match this user', 422);
        }

        $this->track->ensureTenantPolicy($appId, $tenantId);
        $policy = $this->track->resolveEffectivePolicy($appId, $tenantId, $deviceId, (string) $targetId);
        $version = (string) ($policy['version'] ?? '1');

        $data = [
            'type' => 'field_tracking',
            'action' => $action,
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'user_id' => (string) $targetId,
            'device_id' => $deviceId,
            'v' => $version,
            'command_id' => 'ft_' . bin2hex(random_bytes(8)),
            'issued_at' => (string) time(),
        ];

        $push = new DevicePushService();
        $result = $push->sendFieldTracking($targetId, $data);
        $sent = (int) ($result['sent'] ?? 0);

        return $this->json([
            'status' => $sent > 0 ? 'success' : 'failure',
            'success' => $sent > 0,
            'message' => $sent > 0
                ? 'Tracking command sent'
                : 'No FCM device token for this user. Register via POST /api/register_device_token',
            'delivery' => 'device_token',
            'fcm_sent' => $sent,
            'fcm_http_code' => (int) ($result['http_code'] ?? 0),
            'data' => $data,
        ], $sent > 0 ? 200 : 409);
    }

    /**
     * GET /api/track/effective-policy?app_id=&tenant_id=&device_id=&user_id=&v=
     */
    public function effectivePolicy()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $appId = $this->str($this->request->getGet('app_id'));
        $tenantId = $this->str($this->request->getGet('tenant_id'));
        $deviceId = $this->str($this->request->getGet('device_id'));
        $userId = $this->str($this->request->getGet('user_id'));
        $clientV = $this->str($this->request->getGet('v'));

        if ($appId === '' || $tenantId === '') {
            return $this->jsonFail('app_id and tenant_id are required', 422);
        }
        if ($userId === '') {
            $userId = (string) $user['id'];
        }

        // Any production app/tenant gets a tenant-wide policy (not demo-seed only).
        $this->track->ensureTenantPolicy($appId, $tenantId);
        $row = $this->track->resolveEffectivePolicy($appId, $tenantId, $deviceId, $userId);
        if ($row === null) {
            return $this->jsonFail('No effective policy for this device', 404);
        }

        $version = (string) ($row['version'] ?? '1');
        if ($clientV !== '' && $clientV === $version) {
            return $this->response->setStatusCode(304);
        }

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Effective policy',
            'v' => $version,
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'device_id' => $deviceId,
            'user_id' => $userId,
            'policy' => $this->track->formatPolicyResponse($row),
        ]);
    }

    /**
     * POST /api/track/breadcrumbs/batch
     */
    public function breadcrumbsBatch()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $body = $this->jsonBody();
        $appId = $this->str($body['app_id'] ?? '');
        $tenantId = $this->str($body['tenant_id'] ?? '');
        $deviceId = $this->str($body['device_id'] ?? '');
        $userId = $this->str($body['user_id'] ?? (string) $user['id']);
        $points = $body['points'] ?? null;

        if ($appId === '' || $tenantId === '' || $deviceId === '') {
            return $this->jsonFail('app_id, tenant_id, and device_id are required', 422);
        }
        if (!is_array($points) || $points === []) {
            return $this->jsonFail('points[] is required', 422);
        }
        if (count($points) > 500) {
            return $this->jsonFail('points[] max is 500 per batch', 422);
        }

        // Production: any real tenant can ingest; policy auto-created if missing.
        $this->track->ensureTenantPolicy($appId, $tenantId);

        $accepted = 0;
        $duplicates = 0;
        $rejected = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($points as $point) {
            if (!is_array($point)) {
                $rejected++;
                continue;
            }

            $clientUuid = $this->str($point['client_uuid'] ?? '', 64);
            $lat = $point['lat'] ?? null;
            $lng = $point['lng'] ?? null;
            $recordedAt = $this->normalizeDateTime($point['recorded_at'] ?? '');

            if ($clientUuid === '' || !is_numeric($lat) || !is_numeric($lng) || $recordedAt === '') {
                $rejected++;
                continue;
            }

            $result = $this->track->insertBreadcrumb([
                'app_id' => $appId,
                'tenant_id' => $tenantId,
                'device_id' => $deviceId,
                'user_id' => $userId,
                'client_uuid' => $clientUuid,
                'recorded_at' => $recordedAt,
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'accuracy_m' => $this->nullableFloat($point['accuracy_m'] ?? null),
                'speed_mps' => $this->nullableFloat($point['speed_mps'] ?? null),
                'heading' => $this->nullableFloat($point['heading'] ?? null),
                'altitude_m' => $this->nullableFloat($point['altitude_m'] ?? null),
                'battery_pct' => isset($point['battery_pct']) && is_numeric($point['battery_pct'])
                    ? (int) $point['battery_pct'] : null,
                'provider' => $this->str($point['provider'] ?? '', 32) ?: null,
                'created_at' => $now,
            ]);

            if ($result === 'accepted') {
                $accepted++;
            } elseif ($result === 'duplicate') {
                $duplicates++;
            } else {
                $rejected++;
            }
        }

        // Soft retention purge (best-effort, not every call if empty batch).
        if ($accepted > 0) {
            try {
                $this->track->purgeExpiredBreadcrumbs(90);
            } catch (\Throwable $e) {
                log_message('error', 'track purge: ' . $e->getMessage());
            }
        }

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Batch processed',
            'accepted' => $accepted,
            'duplicates' => $duplicates,
            'rejected' => $rejected,
        ]);
    }

    /**
     * POST /api/track/events
     */
    public function events()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $body = $this->jsonBody();
        $appId = $this->str($body['app_id'] ?? '');
        $tenantId = $this->str($body['tenant_id'] ?? '');
        $deviceId = $this->str($body['device_id'] ?? '');
        $userId = $this->str($body['user_id'] ?? (string) $user['id']);
        $clientUuid = $this->str($body['client_uuid'] ?? '', 64);
        $type = strtolower($this->str($body['type'] ?? '', 64));
        $recordedAt = $this->normalizeDateTime($body['recorded_at'] ?? '');

        if ($appId === '' || $tenantId === '' || $deviceId === '') {
            return $this->jsonFail('app_id, tenant_id, and device_id are required', 422);
        }
        if ($clientUuid === '') {
            return $this->jsonFail('client_uuid is required', 422);
        }
        if (!in_array($type, ['check_in', 'check_out', 'check-in', 'check-out'], true)) {
            return $this->jsonFail('type must be check_in or check_out', 422);
        }
        if ($recordedAt === '') {
            return $this->jsonFail('recorded_at is required (ISO-8601)', 422);
        }

        $type = str_replace('-', '_', $type);
        $this->track->ensureTenantPolicy($appId, $tenantId);

        $result = $this->track->insertEvent([
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'device_id' => $deviceId,
            'user_id' => $userId,
            'client_uuid' => $clientUuid,
            'type' => $type,
            'recorded_at' => $recordedAt,
            'lat' => $this->nullableFloat($body['lat'] ?? null),
            'lng' => $this->nullableFloat($body['lng'] ?? null),
            'accuracy_m' => $this->nullableFloat($body['accuracy_m'] ?? null),
            'site_id' => $this->str($body['site_id'] ?? '', 191) ?: null,
            'note' => $this->str($body['note'] ?? '', 512) ?: null,
            'payload_json' => isset($body['payload']) && is_array($body['payload'])
                ? json_encode($body['payload']) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result === 'duplicate') {
            return $this->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Duplicate ignored',
                'duplicate' => true,
            ]);
        }
        if ($result !== 'accepted') {
            return $this->jsonFail('Event rejected', 422);
        }

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Event accepted',
            'duplicate' => false,
            'type' => $type,
        ]);
    }

    /**
     * GET /api/track/live?app_id=&tenant_id=&user_id=
     */
    public function live()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $appId = $this->str($this->request->getGet('app_id'));
        $tenantId = $this->str($this->request->getGet('tenant_id'));
        $userId = $this->str($this->request->getGet('user_id'));

        if ($appId === '' || $tenantId === '') {
            return $this->jsonFail('app_id and tenant_id are required', 422);
        }

        $this->track->ensureTenantPolicy($appId, $tenantId);
        $rows = $this->track->livePositions($appId, $tenantId, $userId);
        $positions = [];
        foreach ($rows as $row) {
            $positions[] = [
                'device_id' => (string) $row['device_id'],
                'user_id' => (string) $row['user_id'],
                'lat' => (float) $row['lat'],
                'lng' => (float) $row['lng'],
                'accuracy_m' => $row['accuracy_m'] !== null ? (float) $row['accuracy_m'] : null,
                'speed_mps' => $row['speed_mps'] !== null ? (float) $row['speed_mps'] : null,
                'heading' => $row['heading'] !== null ? (float) $row['heading'] : null,
                'altitude_m' => $row['altitude_m'] !== null ? (float) $row['altitude_m'] : null,
                'battery_pct' => $row['battery_pct'] !== null ? (int) $row['battery_pct'] : null,
                'provider' => $row['provider'] !== null ? (string) $row['provider'] : null,
                'recorded_at' => $this->toIso8601((string) $row['recorded_at']),
                'updated_at' => $this->toIso8601((string) $row['updated_at']),
            ];
        }

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Live positions',
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'positions' => $positions,
        ]);
    }

    /**
     * GET /api/track/history?app_id=&tenant_id=&user_id=&from=&to=
     */
    public function history()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->jsonFail('Unauthorized', 401);
        }

        $appId = $this->str($this->request->getGet('app_id'));
        $tenantId = $this->str($this->request->getGet('tenant_id'));
        $userId = $this->str($this->request->getGet('user_id'));
        $from = $this->normalizeDateTime($this->request->getGet('from') ?? '');
        $to = $this->normalizeDateTime($this->request->getGet('to') ?? '');

        if ($appId === '' || $tenantId === '') {
            return $this->jsonFail('app_id and tenant_id are required', 422);
        }
        // user_id optional: empty = all users in tenant (production map/history).
        if ($from === '' || $to === '') {
            return $this->jsonFail('from and to are required (ISO-8601)', 422);
        }
        if (strtotime($from) > strtotime($to)) {
            return $this->jsonFail('from must be <= to', 422);
        }

        $this->track->ensureTenantPolicy($appId, $tenantId);
        $rows = $this->track->history($appId, $tenantId, $userId, $from, $to);
        $points = [];
        foreach ($rows as $row) {
            $points[] = [
                'client_uuid' => (string) $row['client_uuid'],
                'device_id' => (string) $row['device_id'],
                'user_id' => (string) $row['user_id'],
                'lat' => (float) $row['lat'],
                'lng' => (float) $row['lng'],
                'accuracy_m' => $row['accuracy_m'] !== null ? (float) $row['accuracy_m'] : null,
                'speed_mps' => $row['speed_mps'] !== null ? (float) $row['speed_mps'] : null,
                'heading' => $row['heading'] !== null ? (float) $row['heading'] : null,
                'altitude_m' => $row['altitude_m'] !== null ? (float) $row['altitude_m'] : null,
                'battery_pct' => $row['battery_pct'] !== null ? (int) $row['battery_pct'] : null,
                'provider' => $row['provider'] !== null ? (string) $row['provider'] : null,
                'recorded_at' => $this->toIso8601((string) $row['recorded_at']),
            ];
        }

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'History',
            'app_id' => $appId,
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'from' => $this->toIso8601($from),
            'to' => $this->toIso8601($to),
            'count' => count($points),
            'points' => $points,
        ]);
    }

    /**
     * @return array{id:int}|null
     */
    private function authenticatedUser(): ?array
    {
        $push = new DevicePushService();
        $token = $this->bearerToken();
        if ($token !== '') {
            $userId = $push->userIdForAccessToken($token);
            if ($userId > 0) {
                return ['id' => $userId];
            }
        }

        $session = \Config\Services::session();
        $userId = (int) ($session->get('user_id') ?? 0);
        if ($userId <= 0) {
            return null;
        }

        try {
            $row = \Config\Database::connect()->table('users')
                ->select('id, status, deleted')
                ->where('id', $userId)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Track API session auth: ' . $e->getMessage());
            return null;
        }

        if (!is_array($row) || (int) ($row['deleted'] ?? 0) === 1) {
            return null;
        }
        if (isset($row['status']) && $row['status'] !== '' && $row['status'] !== 'active') {
            return null;
        }

        return ['id' => (int) $row['id']];
    }

    private function callerMayCommand(int $callerId, int $targetId): bool
    {
        if ($callerId > 0 && $callerId === $targetId) {
            return true;
        }
        if ($callerId <= 0) {
            return false;
        }

        try {
            $row = \Config\Database::connect()->table('users')
                ->select('is_admin, user_type')
                ->where('id', $callerId)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Track command auth: ' . $e->getMessage());
            return false;
        }

        if (!is_array($row)) {
            return false;
        }
        if ((int) ($row['is_admin'] ?? 0) === 1) {
            return true;
        }

        return in_array((string) ($row['user_type'] ?? ''), ['Client', 'Developer'], true);
    }

    private function bearerToken(): string
    {
        $header = $this->authorizationHeader();
        if (preg_match('/Bearer\s+(\S+)/i', $header, $match)) {
            return trim($match[1]);
        }

        $header = trim($header);
        if ($header !== '' && stripos($header, 'Basic ') !== 0) {
            return $header;
        }

        $body = $this->jsonBody();
        foreach (['access_token', 'accesstoken', 'token'] as $key) {
            $value = trim((string) ($body[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return trim((string) ($this->request->getGet('access_token') ?: $this->request->getGet('accesstoken') ?: ''));
    }

    private function authorizationHeader(): string
    {
        $candidates = [
            $this->request->getHeaderLine('Authorization'),
            (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ''),
            (string) ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''),
            (string) ($_SERVER['Authorization'] ?? ''),
        ];

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $key => $value) {
                    if (strcasecmp((string) $key, 'Authorization') === 0) {
                        $candidates[] = (string) $value;
                    }
                }
            }
        }

        foreach ($candidates as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonBody(): array
    {
        $raw = $this->request->getBody();
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $post = $this->request->getPost();
        return is_array($post) ? $post : [];
    }

    private function str($value, int $max = 191): string
    {
        $out = trim((string) $value);
        if ($max > 0 && strlen($out) > $max) {
            $out = substr($out, 0, $max);
        }
        return $out;
    }

    private function nullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return is_numeric($value) ? (float) $value : null;
    }

    private function normalizeDateTime($value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $ts = strtotime($value);
        if ($ts === false) {
            return '';
        }

        return date('Y-m-d H:i:s', $ts);
    }

    private function toIso8601(string $mysqlDatetime): string
    {
        $ts = strtotime($mysqlDatetime);
        if ($ts === false) {
            return $mysqlDatetime;
        }
        return date('c', $ts);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function json(array $payload, int $status = 200)
    {
        return $this->response->setStatusCode($status)->setJSON($payload);
    }

    private function jsonFail(string $message, int $status = 400)
    {
        return $this->json([
            'status' => 'failure',
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
