<?php

namespace App\Libraries;

/**
 * Stores device tokens and sends FCM payloads the mobile app handles.
 *
 * Delivery uses the Firebase HTTP v1 API when FCM_SERVICE_ACCOUNT (or
 * GOOGLE_APPLICATION_CREDENTIALS, or Config/firebase-service-account.json) points
 * at a service account for the same Firebase project as the mobile app.
 * FCM_SERVER_KEY is only a fallback for the legacy HTTP API.
 * Tokens are still saved when neither credential is configured.
 */
class DevicePushService
{
    private const ROOM_PATH = 'grid-chat';

    public function registerToken(int $userId, string $deviceToken, string $platform, string $tokenType): bool
    {
        $this->ensureTable();
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();

        $db->table('user_device_tokens')
            ->where('user_id', $userId)
            ->where('platform', $platform)
            ->where('token_type', $tokenType)
            ->delete();

        return (bool) $db->table('user_device_tokens')->insert([
            'user_id' => $userId,
            'device_token' => $deviceToken,
            'platform' => $platform,
            'token_type' => $tokenType,
            'updated_at' => $now,
        ]);
    }

    public function unregisterToken(int $userId, string $deviceToken = '', string $platform = '', string $tokenType = ''): void
    {
        $this->ensureTable();
        $builder = \Config\Database::connect()->table('user_device_tokens')->where('user_id', $userId);
        if ($deviceToken !== '') {
            $builder->where('device_token', $deviceToken);
        }
        if ($platform !== '') {
            $builder->where('platform', $platform);
        }
        if ($tokenType !== '') {
            $builder->where('token_type', $tokenType);
        }
        $builder->delete();
    }

    public function userIdForAccessToken(string $token): int
    {
        $token = trim($token);
        if ($token === '') {
            return 0;
        }

        try {
            $row = \Config\Database::connect()->table('users')
                ->select('id, status, deleted')
                ->where('access_token', $token)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Device token auth: ' . $e->getMessage());
            return 0;
        }

        if (!is_array($row) || (int) ($row['deleted'] ?? 0) === 1) {
            return 0;
        }
        if (isset($row['status']) && $row['status'] !== '' && $row['status'] !== 'active') {
            return 0;
        }

        return (int) ($row['id'] ?? 0);
    }

    public function userIdForGridId(string $gridId): int
    {
        $gridId = ltrim(trim($gridId), '@');
        if ($gridId === '') {
            return 0;
        }

        foreach ($this->databaseConnections() as $db) {
            try {
                if (!$db->tableExists('grid_profiles')) {
                    continue;
                }
                $row = $db->table('grid_profiles')
                    ->select('user_id')
                    ->where('grid_id', $gridId)
                    ->get()
                    ->getRowArray();
                $userId = (int) ($row['user_id'] ?? 0);
                if ($userId > 0) {
                    return $userId;
                }
            } catch (\Throwable $e) {
                log_message('error', 'Push grid id lookup: ' . $e->getMessage());
            }
        }

        try {
            $db = \Config\Database::connect();
            if ($db->fieldExists('grid_id', 'users')) {
                $row = $db->table('users')
                    ->select('id')
                    ->where('grid_id', $gridId)
                    ->where('deleted', 0)
                    ->get()
                    ->getRowArray();
                return (int) ($row['id'] ?? 0);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Push user grid id lookup: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * @return array{caller_id: int, callee_id: int, call_type: string, conversation_id: string}
     */
    public function callRecord(string $callId): array
    {
        $empty = ['caller_id' => 0, 'callee_id' => 0, 'call_type' => '', 'conversation_id' => ''];
        $callId = trim($callId);
        if ($callId === '') {
            return $empty;
        }

        $this->ensureCallTable();
        try {
            $row = \Config\Database::connect()->table('user_call_signals')
                ->where('call_id', $callId)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Push call lookup: ' . $e->getMessage());
            return $empty;
        }

        if (!is_array($row)) {
            return $empty;
        }

        return [
            'caller_id' => (int) ($row['caller_id'] ?? 0),
            'callee_id' => (int) ($row['callee_id'] ?? 0),
            'call_type' => (string) ($row['call_type'] ?? ''),
            'conversation_id' => (string) ($row['conversation_id'] ?? ''),
        ];
    }

    public function callerIdForCall(string $callId): int
    {
        return $this->callRecord($callId)['caller_id'];
    }

    public function sendIncomingCall(int $callerId, int $calleeId, string $callId, string $callType): void
    {
        $callType = $callType === 'audio' ? 'audio' : 'video';
        $caller = $this->userProfile($callerId);
        $conversationId = $this->requestValue('conversation_id');
        if ($conversationId === '') {
            $conversationId = $this->directConversationId($callerId, $calleeId);
        }
        $this->rememberCall($callId, $callerId, $calleeId, $callType, $conversationId);

        $this->pushToUser($calleeId, $this->dataPayload('INCOMING_CALL', [
            'call_id' => $callId,
            'caller_name' => $caller['name'],
            'call_type' => $callType,
            'caller_grid_id' => $caller['grid_id'],
            'caller_user_id' => (string) $callerId,
            'conversation_id' => $conversationId,
            'body' => $callType === 'audio' ? 'Incoming audio call' : 'Incoming video call',
        ]));
    }

    public function sendCallSignal(int $userId, string $callId, string $type): void
    {
        $type = $this->mobileSignalType($type);
        if ($type === '' || $userId <= 0 || trim($callId) === '') {
            return;
        }

        $call = $this->callRecord($callId);
        $callerId = $call['caller_id'];
        $caller = $this->userProfile($callerId);
        $callType = $call['call_type'] === 'audio' ? 'audio' : ($call['call_type'] === 'video' ? 'video' : '');
        $conversationId = $call['conversation_id'];
        if ($conversationId === '') {
            $conversationId = $this->requestValue('conversation_id');
        }
        if ($conversationId === '' && $callerId > 0 && $call['callee_id'] > 0) {
            $conversationId = $this->directConversationId($callerId, $call['callee_id']);
        }

        $body = 'Call ended';
        if ($type === 'MISSED_CALL') {
            $body = $callType === 'video' ? 'Missed video call' : 'Missed audio call';
        }

        $this->pushToUser($userId, $this->dataPayload($type, [
            'call_id' => $callId,
            'caller_name' => $caller['name'],
            'call_type' => $callType,
            'caller_grid_id' => $caller['grid_id'],
            'caller_user_id' => $callerId > 0 ? (string) $callerId : '',
            'conversation_id' => $conversationId,
            'body' => $body,
        ]));
    }

    /**
     * @param int[] $recipientIds
     */
    public function sendChatMessage(int $senderId, array $recipientIds, string $body): void
    {
        $sender = $this->userProfile($senderId);
        $preview = trim($body);
        if ($preview === '') {
            $preview = 'New message';
        }
        if (strlen($preview) > 180) {
            $preview = substr($preview, 0, 177) . '...';
        }

        $conversationId = $this->requestValue('conversation_id');
        if ($conversationId === '') {
            $conversationId = $this->latestConversationId($senderId, $recipientIds);
        }

        $payload = $this->dataPayload('NEW_MESSAGE', [
            'call_id' => '',
            'caller_name' => $sender['name'],
            'call_type' => '',
            'caller_grid_id' => $sender['grid_id'],
            'caller_user_id' => (string) $senderId,
            'conversation_id' => $conversationId,
            'body' => $preview,
        ]);

        foreach (array_unique(array_map('intval', $recipientIds)) as $recipientId) {
            if ($recipientId > 0 && $recipientId !== $senderId) {
                $this->pushToUser($recipientId, $payload);
            }
        }
    }

    /**
     * @param array<string, string> $data
     */
    private function pushToUser(int $userId, array $data): void
    {
        foreach ($this->tokensForUser($userId) as $row) {
            $tokenType = (string) ($row['token_type'] ?? '');
            $token = (string) ($row['device_token'] ?? '');
            if ($token === '') {
                continue;
            }
            if ($tokenType === 'fcm') {
                $this->sendFcm($token, $data);
                continue;
            }
            log_message('notice', 'Push skipped for token_type ' . $tokenType . ' on user ' . $userId . '. Only FCM is configured.');
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tokensForUser(int $userId): array
    {
        $this->ensureTable();
        return \Config\Database::connect()->table('user_device_tokens')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();
    }

    /**
     * Every value is a string, which FCM requires. room_url and url are the same room.
     *
     * @param array<string, string> $fields
     * @return array<string, string>
     */
    private function dataPayload(string $type, array $fields): array
    {
        $callType = strtolower(trim((string) ($fields['call_type'] ?? '')));
        if (!in_array($callType, ['audio', 'video'], true)) {
            $callType = '';
        }

        $room = $this->roomUrl();

        return [
            'type' => $type,
            'call_id' => trim((string) ($fields['call_id'] ?? '')),
            'caller_name' => trim((string) ($fields['caller_name'] ?? '')),
            'call_type' => $callType,
            'url' => $room,
            'room_url' => $room,
            'caller_grid_id' => trim((string) ($fields['caller_grid_id'] ?? '')),
            'caller_user_id' => trim((string) ($fields['caller_user_id'] ?? '')),
            'conversation_id' => trim((string) ($fields['conversation_id'] ?? '')),
            'body' => (string) ($fields['body'] ?? ''),
        ];
    }

    private function mobileSignalType(string $type): string
    {
        $type = strtoupper(trim($type));
        if (in_array($type, ['MISSED_CALL', 'CALL_MISSED', 'MISSED'], true)) {
            return 'MISSED_CALL';
        }
        if (in_array($type, ['CALL_HANDLED', 'CALL_CANCELLED', 'HANGUP', 'CALL_DECLINED', 'DECLINED'], true)) {
            return 'CALL_HANDLED';
        }

        return '';
    }

    /**
     * @param array<string, string> $data
     */
    private function sendFcm(string $token, array $data): void
    {
        $account = $this->serviceAccount();
        if ($account !== null) {
            $this->sendFcmV1($token, $data, $account);
            return;
        }

        $key = $this->serverKey();
        if ($key === '') {
            log_message('error', 'FCM is not configured. Set FCM_SERVICE_ACCOUNT to the Firebase service account JSON. The device token was stored, but the push was not sent.');
            return;
        }

        $this->sendFcmLegacy($token, $data, $key);
    }

    /**
     * @param array<string, string> $data
     * @param array{project_id: string, client_email: string, private_key: string} $account
     */
    private function sendFcmV1(string $token, array $data, array $account): void
    {
        $accessToken = $this->googleAccessToken($account);
        if ($accessToken === '') {
            return;
        }

        $message = [
            'token' => $token,
            'data' => $data,
            'android' => [
                'priority' => 'HIGH',
            ],
        ];

        $alert = $this->alertFor($data);
        if ($alert === null) {
            $message['apns'] = [
                'headers' => [
                    'apns-priority' => '5',
                    'apns-push-type' => 'background',
                ],
                'payload' => [
                    'aps' => [
                        'content-available' => 1,
                    ],
                ],
            ];
        } else {
            $message['notification'] = [
                'title' => $alert['title'],
                'body' => $alert['body'],
            ];
            $androidNotification = [
                'sound' => 'default',
            ];
            if ($alert['channel'] !== '') {
                $androidNotification['channel_id'] = $alert['channel'];
            }
            $message['android']['notification'] = $androidNotification;
            $message['apns'] = [
                'headers' => [
                    'apns-priority' => '10',
                    'apns-push-type' => 'alert',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $alert['title'],
                            'body' => $alert['body'],
                        ],
                        'sound' => 'default',
                    ],
                ],
            ];
        }

        $url = 'https://fcm.googleapis.com/v1/projects/' . rawurlencode($account['project_id']) . '/messages:send';
        $result = $this->curlJson($url, ['message' => $message], [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);

        $this->logFcmResult($result);
        if ($this->isUnregistered($result['status'], $result['body'])) {
            $this->deleteToken($token);
        }
    }

    /**
     * Visible alert for calls and chat. Call-dismiss signals stay data-only.
     *
     * @param array<string, string> $data
     * @return array{title: string, body: string, channel: string}|null
     */
    private function alertFor(array $data): ?array
    {
        $type = strtoupper(trim((string) ($data['type'] ?? '')));
        $name = trim((string) ($data['caller_name'] ?? ''));
        $body = trim((string) ($data['body'] ?? ''));
        $callType = strtolower(trim((string) ($data['call_type'] ?? '')));

        if ($type === 'INCOMING_CALL') {
            $kind = $callType === 'video' ? 'video' : 'audio';
            $text = $name !== '' ? 'Incoming ' . $kind . ' call from ' . $name : ($body !== '' ? $body : 'Incoming ' . $kind . ' call');
            return [
                'title' => 'Incoming Call',
                'body' => $text,
                'channel' => 'grid_calls',
            ];
        }

        if ($type === 'MISSED_CALL') {
            return [
                'title' => 'Missed Call',
                'body' => $body !== '' ? $body : 'Missed call',
                'channel' => 'grid_calls',
            ];
        }

        if ($type === 'NEW_MESSAGE') {
            return [
                'title' => $name !== '' ? $name : 'New message',
                'body' => $body !== '' ? $body : 'New message',
                'channel' => '',
            ];
        }

        return null;
    }

    /**
     * @param array{status: int, errno: int, body: string} $result
     */
    private function logFcmResult(array $result): void
    {
        $line = 'FCM push HTTP ' . $result['status'] . ' curl ' . $result['errno'] . ' body ' . substr($result['body'], 0, 300);
        if ($result['errno'] !== 0 || $result['status'] >= 400 || $result['status'] === 0) {
            log_message('error', 'FCM push failed. ' . $line);
            return;
        }
        log_message('info', $line);
    }

    /**
     * @param array<string, string> $data
     */
    private function sendFcmLegacy(string $token, array $data, string $key): void
    {
        $legacy = [
            'to' => $token,
            'priority' => 'high',
            'content_available' => true,
            'data' => $data,
        ];
        $alert = $this->alertFor($data);
        if ($alert !== null) {
            $legacy['notification'] = [
                'title' => $alert['title'],
                'body' => $alert['body'],
                'sound' => 'default',
            ];
            if ($alert['channel'] !== '') {
                $legacy['notification']['android_channel_id'] = $alert['channel'];
            }
        }
        $result = $this->curlJson('https://fcm.googleapis.com/fcm/send', $legacy, [
            'Authorization: key=' . $key,
            'Content-Type: application/json',
        ]);

        $this->logFcmResult($result);

        $decoded = json_decode($result['body'], true);
        $error = is_array($decoded) ? (string) ($decoded['results'][0]['error'] ?? '') : '';
        if (in_array($error, ['NotRegistered', 'InvalidRegistration'], true)) {
            $this->deleteToken($token);
        }
    }

    /**
     * @param array<string, mixed> $payload
     * @param string[] $headers
     * @return array{status: int, errno: int, body: string}
     */
    private function curlJson(string $url, array $payload, array $headers): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 12,
        ]);
        $this->applyCaBundle($ch);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'errno' => $errno,
            'body' => is_string($response) ? $response : '',
        ];
    }

    private function applyCaBundle($ch): void
    {
        $configured = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        $candidates = [];
        if (is_string($configured) && $configured !== '') {
            $candidates[] = $configured;
        }
        $candidates[] = 'C:/Program Files/Ampps/php82/extras/ssl/cacert.pem';
        $candidates[] = '/etc/ssl/certs/ca-certificates.crt';
        $candidates[] = '/etc/pki/tls/certs/ca-bundle.crt';

        foreach ($candidates as $path) {
            if (is_file($path)) {
                curl_setopt($ch, CURLOPT_CAINFO, $path);
                return;
            }
        }
    }

    private function isUnregistered(int $status, string $body): bool
    {
        if ($status === 404) {
            return true;
        }
        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            return false;
        }
        $details = $decoded['error']['details'] ?? [];
        if (!is_array($details)) {
            return false;
        }
        foreach ($details as $detail) {
            if (is_array($detail) && ($detail['errorCode'] ?? '') === 'UNREGISTERED') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{project_id: string, client_email: string, private_key: string}|null
     */
    private function serviceAccount(): ?array
    {
        $path = $this->serviceAccountPath();
        if ($path === '' || !is_file($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded)) {
            log_message('error', 'FCM service account JSON could not be read.');
            return null;
        }

        $projectId = trim((string) ($decoded['project_id'] ?? ''));
        $email = trim((string) ($decoded['client_email'] ?? ''));
        $privateKey = (string) ($decoded['private_key'] ?? '');
        if ($projectId === '' || $email === '' || $privateKey === '') {
            log_message('error', 'FCM service account JSON is missing project_id, client_email, or private_key.');
            return null;
        }

        return [
            'project_id' => $projectId,
            'client_email' => $email,
            'private_key' => $privateKey,
        ];
    }

    private function serviceAccountPath(): string
    {
        foreach (['FCM_SERVICE_ACCOUNT', 'GOOGLE_APPLICATION_CREDENTIALS'] as $name) {
            $value = getenv($name);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        $bundled = APPPATH . 'Config/firebase-service-account.json';
        if (is_file($bundled)) {
            return $bundled;
        }

        return '';
    }

    /**
     * @param array{project_id: string, client_email: string, private_key: string} $account
     */
    private function googleAccessToken(array $account): string
    {
        $cached = $this->readTokenCache($account['client_email']);
        if ($cached !== '') {
            return $cached;
        }

        $now = time();
        $header = $this->base64Url((string) json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim = $this->base64Url((string) json_encode([
            'iss' => $account['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));
        $input = $header . '.' . $claim;
        $signature = '';
        $signed = openssl_sign($input, $signature, $account['private_key'], OPENSSL_ALGO_SHA256);
        if (!$signed) {
            log_message('error', 'FCM service account private key could not be used to sign the request.');
            return '';
        }

        $jwt = $input . '.' . $this->base64Url($signature);
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]),
            CURLOPT_TIMEOUT => 12,
        ]);
        $this->applyCaBundle($ch);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = is_string($response) ? json_decode($response, true) : null;
        $token = is_array($decoded) ? trim((string) ($decoded['access_token'] ?? '')) : '';
        if ($errno !== 0 || $status >= 400 || $token === '') {
            log_message('error', 'FCM OAuth failed. HTTP ' . $status . ' curl ' . $errno . ' body ' . substr((string) $response, 0, 300));
            return '';
        }

        $this->writeTokenCache($account['client_email'], $token, $now + 3300);
        return $token;
    }

    private function readTokenCache(string $email): string
    {
        $file = $this->tokenCacheFile();
        if (!is_file($file)) {
            return '';
        }
        $decoded = json_decode((string) file_get_contents($file), true);
        if (!is_array($decoded) || ($decoded['email'] ?? '') !== $email) {
            return '';
        }
        if ((int) ($decoded['exp'] ?? 0) < time() + 60) {
            return '';
        }

        return trim((string) ($decoded['token'] ?? ''));
    }

    private function writeTokenCache(string $email, string $token, int $exp): void
    {
        $dir = WRITEPATH . 'cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($this->tokenCacheFile(), json_encode([
            'email' => $email,
            'token' => $token,
            'exp' => $exp,
        ]));
    }

    private function tokenCacheFile(): string
    {
        return WRITEPATH . 'cache/fcm-oauth.json';
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function serverKey(): string
    {
        $key = getenv('FCM_SERVER_KEY');
        return is_string($key) ? trim($key) : '';
    }

    private function roomUrl(): string
    {
        helper('url');
        return site_url(self::ROOM_PATH);
    }

    private function requestValue(string $key): string
    {
        try {
            $request = service('request');
            $posted = $request->getPost($key);
            if ($posted !== null && trim((string) $posted) !== '') {
                return trim((string) $posted);
            }
            $raw = $request->getBody();
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && isset($decoded[$key])) {
                    return trim((string) $decoded[$key]);
                }
            }
        } catch (\Throwable $e) {
            return '';
        }

        return '';
    }

    /**
     * @return array{name: string, grid_id: string}
     */
    private function userProfile(int $userId): array
    {
        $name = 'Efactor';
        $gridId = '';
        if ($userId <= 0) {
            return ['name' => $name, 'grid_id' => $gridId];
        }

        try {
            $db = \Config\Database::connect();
            $select = 'first_name, last_name';
            if ($db->fieldExists('grid_id', 'users')) {
                $select .= ', grid_id';
            }
            $row = $db->table('users')
                ->select($select)
                ->where('id', $userId)
                ->get()
                ->getRowArray();
            if (is_array($row)) {
                $built = trim((string) ($row['first_name'] ?? '') . ' ' . (string) ($row['last_name'] ?? ''));
                if ($built !== '') {
                    $name = $built;
                }
                $gridId = trim((string) ($row['grid_id'] ?? ''));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Push user profile: ' . $e->getMessage());
        }

        if ($gridId === '') {
            $gridId = $this->gridIdFromProfiles($userId);
        }

        return ['name' => $name, 'grid_id' => $gridId];
    }

    private function gridIdFromProfiles(int $userId): string
    {
        foreach ($this->databaseConnections() as $db) {
            try {
                if (!$db->tableExists('grid_profiles')) {
                    continue;
                }
                $row = $db->table('grid_profiles')
                    ->select('grid_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getRowArray();
                $gridId = trim((string) ($row['grid_id'] ?? ''));
                if ($gridId !== '') {
                    return $gridId;
                }
            } catch (\Throwable $e) {
                log_message('error', 'Push profile grid id: ' . $e->getMessage());
            }
        }

        return '';
    }

    /**
     * @return array<int, \CodeIgniter\Database\BaseConnection>
     */
    private function databaseConnections(): array
    {
        $connections = [];
        try {
            $connections[] = \Config\Database::connect();
        } catch (\Throwable $e) {
            log_message('error', 'Push default database: ' . $e->getMessage());
        }
        try {
            $connections[] = \Config\Database::connect('grid', false);
        } catch (\Throwable $e) {
            log_message('error', 'Push grid database: ' . $e->getMessage());
        }

        return $connections;
    }

    private function directConversationId(int $userA, int $userB): string
    {
        if ($userA <= 0 || $userB <= 0 || $userA === $userB) {
            return '';
        }

        $key = min($userA, $userB) . ':' . max($userA, $userB);
        try {
            $row = \Config\Database::connect('gridchat', false)->table('conversations')
                ->select('id')
                ->where('direct_key', $key)
                ->get()
                ->getRowArray();
            return (string) ($row['id'] ?? '');
        } catch (\Throwable $e) {
            log_message('error', 'Push conversation lookup: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * @param int[] $recipientIds
     */
    private function latestConversationId(int $senderId, array $recipientIds): string
    {
        $direct = count($recipientIds) === 1 ? $this->directConversationId($senderId, (int) $recipientIds[0]) : '';
        if ($direct !== '') {
            return $direct;
        }

        try {
            $row = \Config\Database::connect('gridchat', false)->table('messages')
                ->select('conversation_id')
                ->where('sender_id', $senderId)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            return (string) ($row['conversation_id'] ?? '');
        } catch (\Throwable $e) {
            log_message('error', 'Push latest conversation: ' . $e->getMessage());
            return '';
        }
    }

    private function deleteToken(string $token): void
    {
        if ($token === '') {
            return;
        }
        try {
            $this->ensureTable();
            \Config\Database::connect()->table('user_device_tokens')
                ->where('device_token', $token)
                ->delete();
        } catch (\Throwable $e) {
            log_message('error', 'Push token delete: ' . $e->getMessage());
        }
    }

    private function rememberCall(string $callId, int $callerId, int $calleeId, string $callType, string $conversationId): void
    {
        $this->ensureCallTable();
        $db = \Config\Database::connect();
        $row = [
            'call_id' => $callId,
            'caller_id' => $callerId,
            'callee_id' => $calleeId,
            'call_type' => $callType,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if ($db->fieldExists('conversation_id', 'user_call_signals')) {
            $row['conversation_id'] = $conversationId;
        }
        $db->table('user_call_signals')->where('call_id', $callId)->delete();
        $db->table('user_call_signals')->insert($row);
    }

    private function ensureTable(): void
    {
        $db = \Config\Database::connect();
        $db->query(
            'CREATE TABLE IF NOT EXISTS `user_device_tokens` ('
            . '`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,'
            . '`user_id` INT UNSIGNED NOT NULL,'
            . '`device_token` VARCHAR(512) NOT NULL,'
            . '`platform` VARCHAR(20) NOT NULL,'
            . '`token_type` VARCHAR(20) NOT NULL,'
            . '`updated_at` DATETIME NULL,'
            . 'PRIMARY KEY (`id`),'
            . 'KEY `user_platform_type` (`user_id`,`platform`,`token_type`)'
            . ') ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );
    }

    private function ensureCallTable(): void
    {
        $db = \Config\Database::connect();
        $db->query(
            'CREATE TABLE IF NOT EXISTS `user_call_signals` ('
            . '`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,'
            . '`call_id` VARCHAR(64) NOT NULL,'
            . '`caller_id` INT UNSIGNED NOT NULL,'
            . '`callee_id` INT UNSIGNED NOT NULL,'
            . '`call_type` VARCHAR(20) NOT NULL,'
            . '`conversation_id` VARCHAR(32) NULL,'
            . '`created_at` DATETIME NULL,'
            . 'PRIMARY KEY (`id`),'
            . 'KEY `call_id` (`call_id`)'
            . ') ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );
        if (!$db->fieldExists('conversation_id', 'user_call_signals')) {
            $db->query('ALTER TABLE `user_call_signals` ADD COLUMN `conversation_id` VARCHAR(32) NULL AFTER `call_type`');
        }
    }
}
