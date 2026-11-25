<?php

namespace App\Libraries;

/**
 * Stores device tokens and sends FCM payloads the mobile app handles.
 *
 * Delivery uses the Firebase HTTP v1 API when FCM_SERVICE_ACCOUNT (or
 * GOOGLE_APPLICATION_CREDENTIALS, or Config/firebase-service-account.json) points
 * at a service account for the same Firebase project as the mobile app.
 * Call and chat messages are data-only (no notification block) for Android so
 * it can run the app background handler. Tokens registered as platform "ios"
 * also get a top-level notification block plus the apns alert/category, since
 * APNs drops data-only pushes on a locked phone or killed app. Incoming calls are sent on every dial,
 * including when a WebSocket still looks connected.
 * Delivery is FCM HTTP v1 only. The legacy https://fcm.googleapis.com/fcm/send
 * endpoint was shut down and now returns HTTP 404.
 * Tokens are still saved when the service account file is missing.
 *
 * iOS calls: the app also registers its PushKit token as token_type
 * "apns_voip". INCOMING_CALL / CALL_CANCELLED / MISSED_CALL for the callee
 * are sent straight to APNs (apns-push-type: voip, topic <bundle-id>.voip,
 * .p8 token auth) because FCM cannot deliver VoIP pushes. See sendApnsVoip().
 */
class DevicePushService
{
    private const ROOM_PATH = 'grid-chat';

    /** token_type stored for an iOS PushKit (VoIP) token. */
    public const VOIP_TOKEN_TYPE = 'apns_voip';

    /** Call signals that go to iOS callees as APNs VoIP pushes. */
    private const VOIP_TYPES = ['INCOMING_CALL', 'CALL_CANCELLED', 'MISSED_CALL'];

    /** @var array<string, string>|null|false APNs settings; false = not loaded yet */
    private $apnsConfig = false;

    /** @var array{status: int, body: string} */
    private array $lastFcmResult = ['status' => 0, 'body' => ''];

    /** Grid id from the current call request, used to find the portal user. */
    private string $lookupGridId = '';

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

    /**
     * Key the app sends as X-Device-Key from a notification action while it
     * has no session cookie. Signed from the user id and this device's push
     * token, so it stops working when the token is unregistered or replaced.
     */
    public function deviceKeyFor(int $userId, string $deviceToken): string
    {
        $deviceToken = trim($deviceToken);
        $secret = $this->deviceKeySecret();
        if ($userId <= 0 || $deviceToken === '' || $secret === '') {
            return '';
        }

        return 'dk1.' . $userId . '.' . $this->base64Url(hash_hmac('sha256', $userId . '|' . $deviceToken, $secret, true));
    }

    /**
     * Resolves X-Device-Key to a user id. Accepts a key from deviceKeyFor(),
     * or the user's access_token (the same secret Bearer auth already accepts).
     */
    public function userIdForDeviceKey(string $key): int
    {
        $key = trim($key);
        if ($key === '' || strlen($key) > 512) {
            return 0;
        }
        if (!preg_match('/^dk1\.(\d{1,10})\.([A-Za-z0-9_-]{20,})$/', $key, $match)) {
            return $this->userIdForAccessToken($key);
        }

        $userId = (int) $match[1];
        if ($userId <= 0) {
            return 0;
        }

        try {
            $db = \Config\Database::connect();
            foreach ($this->tokenTables($db) as $table) {
                foreach ($db->table($table)->select('device_token')->where('user_id', $userId)->get()->getResultArray() as $row) {
                    $expected = $this->deviceKeyFor($userId, (string) ($row['device_token'] ?? ''));
                    if ($expected !== '' && hash_equals($expected, $key)) {
                        return $this->isActiveUser($userId) ? $userId : 0;
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Device key auth: ' . $e->getMessage());
        }

        return 0;
    }

    private function isActiveUser(int $userId): bool
    {
        try {
            $row = \Config\Database::connect()->table('users')
                ->select('id, status, deleted')
                ->where('id', $userId)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Device key user check: ' . $e->getMessage());
            return false;
        }

        if (!is_array($row) || (int) ($row['deleted'] ?? 0) === 1) {
            return false;
        }

        return !(isset($row['status']) && $row['status'] !== '' && $row['status'] !== 'active');
    }

    /**
     * GRID_DEVICE_KEY_SECRET from the environment, otherwise a random secret
     * created once under writable/ (outside the web root and source control).
     */
    private function deviceKeySecret(): string
    {
        $env = getenv('GRID_DEVICE_KEY_SECRET');
        if (is_string($env) && strlen(trim($env)) >= 32) {
            return trim($env);
        }

        $file = WRITEPATH . 'keys/device-key-secret.txt';
        if (is_file($file)) {
            $stored = trim((string) file_get_contents($file));
            if (strlen($stored) >= 32) {
                return $stored;
            }
        }

        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0700, true);
        }
        $secret = bin2hex(random_bytes(32));
        // x mode: if two requests race, the first write wins and both re-read it
        $handle = @fopen($file, 'x');
        if ($handle !== false) {
            fwrite($handle, $secret);
            fclose($handle);
            return $secret;
        }
        $stored = is_file($file) ? trim((string) file_get_contents($file)) : '';
        if (strlen($stored) >= 32) {
            return $stored;
        }

        log_message('error', 'Device key secret could not be saved in ' . $dir . '. Set GRID_DEVICE_KEY_SECRET.');
        return '';
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

    /**
     * @return array{sent: int, http_code: int, user_ids: int[]}
     */
    public function sendIncomingCall(int $callerId, int $calleeId, string $callId, string $callType, string $targetGridId = ''): array
    {
        $callType = $callType === 'audio' ? 'audio' : 'video';
        $this->lookupGridId = trim($targetGridId);
        if ($this->lookupGridId === '') {
            foreach (['target_grid_id', 'callee_grid_id', 'recipient_grid_id'] as $key) {
                $this->lookupGridId = $this->requestValue($key);
                if ($this->lookupGridId !== '') {
                    break;
                }
            }
        }
        $caller = $this->userProfile($callerId);
        $postedName = $this->requestValue('caller_name');
        if ($postedName !== '') {
            $caller['name'] = $postedName;
        }
        $conversationId = $this->requestValue('conversation_id');
        if ($conversationId === '') {
            $conversationId = $this->directConversationId($callerId, $calleeId);
        }
        // Saving the call row must not block the push. A locked or killed app
        // still has to be woken by FCM even if this insert fails.
        try {
            $this->rememberCall($callId, $callerId, $calleeId, $callType, $conversationId);
        } catch (\Throwable $e) {
            log_message('error', 'Call signal save: ' . $e->getMessage());
        }

        // Existing keys (caller_avatar, conversation_id, url, caller_grid_id)
        // stay so the current app handler keeps working.
        // Always send. A locked screen or a socket that has not dropped yet
        // still looks "connected", and that in-page socket cannot ring the phone.
        $sent = $this->pushToUser($calleeId, [
            'type' => 'INCOMING_CALL',
            'call_id' => $callId,
            'caller_id' => (string) $callerId,
            'caller_name' => $caller['name'],
            'caller_grid_id' => $caller['grid_id'] !== '' ? $caller['grid_id'] : $this->lookupGridId,
            'caller_user_id' => (string) $callerId,
            'caller_avatar' => $caller['avatar'],
            'call_type' => $callType,
            'conversation_id' => $conversationId,
            'room_url' => $this->roomUrl(),
            'url' => $this->chatUrl($conversationId, $callId),
            'timestamp' => (string) time(),
        ], true);
        if ($sent > 0) {
            log_message('info', 'Push INCOMING_CALL dispatched to ' . $sent . ' token(s) for user ' . $calleeId . ' call_id ' . $callId . ' HTTP ' . $this->lastFcmResult['status']);
        }

        return [
            'sent' => $sent,
            'http_code' => $this->lastFcmResult['status'],
            'user_ids' => $this->candidateUserIds($calleeId),
        ];
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

        // VoIP only goes to the saved callee of this call. The caller's phone
        // never rang through CallKit, and iOS penalises a VoIP push that is
        // not reported as a call, so the caller keeps the FCM path.
        $isCallee = $call['callee_id'] > 0 && $call['callee_id'] === $userId;

        if ($type === 'CALL_CANCELLED') {
            $this->pushToUser($userId, [
                'type' => 'CALL_CANCELLED',
                'call_id' => trim($callId),
            ], $isCallee);
            return;
        }

        $this->pushToUser($userId, [
            'type' => 'MISSED_CALL',
            'call_id' => trim($callId),
            'caller_name' => $caller['name'],
            'caller_user_id' => $callerId > 0 ? (string) $callerId : '',
            'call_type' => $callType,
        ], $isCallee);
    }

    /**
     * @param int[] $recipientIds
     */
    /**
     * @param array{message_type?: string, is_group?: string, expires_at?: string, locked?: array<int, string>} $meta
     */
    public function sendChatMessage(int $senderId, array $recipientIds, string $body, string $messageId = '', array $meta = []): void
    {
        $sender = $this->userProfile($senderId);
        $preview = trim($body);
        if ($preview === '') {
            $preview = 'New message';
        }
        if (strlen($preview) > 180) {
            $preview = substr($preview, 0, 177) . '...';
        }

        $conversationId = trim((string) ($meta['conversation_id'] ?? ''));
        if ($conversationId === '') {
            $conversationId = $this->requestValue('conversation_id');
        }
        if ($conversationId === '') {
            $conversationId = $this->latestConversationId($senderId, $recipientIds);
        }
        if ($messageId === '') {
            $messageId = $this->requestValue('message_id');
        }

        $messageType = trim((string) ($meta['message_type'] ?? 'text'));
        if ($messageType === '') {
            $messageType = 'text';
        }
        $isGroup = ((string) ($meta['is_group'] ?? '0')) === '1' ? '1' : '0';
        $groupTitle = $isGroup === '1' ? trim((string) ($meta['group_title'] ?? '')) : '';
        $expiresAt = trim((string) ($meta['expires_at'] ?? ''));
        $lockedMap = is_array($meta['locked'] ?? null) ? $meta['locked'] : [];

        foreach (array_unique(array_map('intval', $recipientIds)) as $recipientId) {
            if ($recipientId <= 0 || $recipientId === $senderId) {
                continue;
            }
            $locked = ((string) ($lockedMap[$recipientId] ?? '0')) === '1';
            $this->pushToUser($recipientId, [
                'type' => 'NEW_MESSAGE',
                'notification_type' => 'message',
                'conversation_id' => $conversationId,
                'message_id' => $messageId,
                // "message_type" is reserved by FCM: a data key with that name makes Firebase reject the push (400)
                'msg_type' => $messageType,
                // A group is titled with its name; sender_name still says who wrote it
                'title' => $locked ? 'GRID Chat' : ($groupTitle !== '' ? $groupTitle : $sender['name']),
                'group_title' => $locked ? '' : $groupTitle,
                'body' => $locked ? 'New message' : $preview,
                'sender_id' => (string) $senderId,
                'sender_name' => $locked ? '' : $sender['name'],
                'is_group' => $isGroup,
                'locked' => $locked ? '1' : '0',
                'expires_at' => $expiresAt,
                'tag' => 'conv-' . $conversationId,
                'url' => $this->chatUrl($conversationId),
            ]);
        }
    }

    /**
     * Data-only GPS start/stop. Sent to every FCM token registered for this
     * user (POST /api/register_device_token). Not a topic. Not apns_voip.
     *
     * @param array<string, scalar> $data
     * @return array{sent: int, http_code: int}
     */
    public function sendFieldTracking(int $userId, array $data): array
    {
        $data['type'] = 'field_tracking';
        $sent = $this->pushToUser($userId, $data, false);

        return [
            'sent' => $sent,
            'http_code' => (int) ($this->lastFcmResult['status'] ?? 0),
        ];
    }

    /**
     * Chat pushes wait until the user has no open socket. Incoming calls do
     * not use this: the in-page socket cannot ring a locked or killed app.
     *
     * @param array<string, string> $data
     */
    private function pushWhenOffline(int $userId, array $data): void
    {
        if ($userId <= 0) {
            return;
        }
        if ($this->isActiveOnWebSocket($userId)) {
            log_message('info', 'FCM skipped for user ' . $userId . ' type ' . ($data['type'] ?? '') . ': recipient is active on the WebSocket server.');
            return;
        }
        $this->pushToUser($userId, $data);
    }

    /**
     * Sends to every token of the user. With $allowVoip (iOS callee of a
     * call), INCOMING_CALL / CALL_CANCELLED / MISSED_CALL also go straight to
     * APNs for each apns_voip token, because FCM cannot deliver VoIP pushes.
     * When the VoIP INCOMING_CALL is accepted by APNs, that user's iOS FCM
     * banner is skipped (CallKit rings instead). If APNs is not configured or
     * fails, the FCM push is sent as before.
     *
     * @param array<string, string> $data
     */
    private function pushToUser(int $userId, array $data, bool $allowVoip = false): int
    {
        if ($userId <= 0) {
            return 0;
        }

        $sent = 0;
        $seen = [];
        $rows = $this->tokensForUser($userId);
        if ($rows === []) {
            log_message('error', 'FCM not sent. No device token for user ' . $userId . ' (also checked ' . implode(',', $this->candidateUserIds($userId)) . ') type ' . ($data['type'] ?? '') . '.');
            return 0;
        }

        $type = (string) ($data['type'] ?? '');
        $voipUsers = [];
        if ($allowVoip && in_array($type, self::VOIP_TYPES, true)) {
            foreach ($rows as $row) {
                $token = trim((string) ($row['device_token'] ?? ''));
                if ((string) ($row['token_type'] ?? '') !== self::VOIP_TOKEN_TYPE || $token === '' || isset($seen[$token])) {
                    continue;
                }
                $seen[$token] = true;
                try {
                    $delivered = $this->sendApnsVoip($token, $data);
                } catch (\Throwable $e) {
                    $delivered = false;
                    log_message('error', 'APNs VoIP push error: ' . $e->getMessage());
                }
                if ($delivered) {
                    $voipUsers[(int) ($row['user_id'] ?? 0)] = true;
                    $sent++;
                }
            }
        }

        foreach ($rows as $row) {
            $tokenType = (string) ($row['token_type'] ?? '');
            $token = (string) ($row['device_token'] ?? '');
            if ($token === '' || isset($seen[$token])) {
                continue;
            }
            if ($tokenType === self::VOIP_TOKEN_TYPE) {
                // VoIP tokens are only valid on APNs (handled above)
                continue;
            }
            if ($tokenType !== '' && $tokenType !== 'fcm') {
                log_message('notice', 'Push skipped for token_type ' . $tokenType . ' on user ' . $userId . '.');
                continue;
            }
            $platform = strtolower(trim((string) ($row['platform'] ?? '')));
            if ($type === 'INCOMING_CALL' && $platform === 'ios' && isset($voipUsers[(int) ($row['user_id'] ?? 0)])) {
                // CallKit already rings from the VoIP push; a banner would duplicate it
                log_message('info', 'FCM INCOMING_CALL banner skipped for iOS user ' . (int) ($row['user_id'] ?? 0) . ': VoIP push delivered.');
                continue;
            }
            $seen[$token] = true;
            $this->sendFcm($token, $data, $platform);
            $sent++;
        }

        if ($sent === 0) {
            log_message('error', 'Push not sent. user ' . $userId . ' has device rows but no usable token. type ' . $type . '.');
        }

        return $sent;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tokensForUser(int $userId): array
    {
        $this->ensureTable();
        $ids = $this->candidateUserIds($userId, $this->lookupGridId !== '' ? $this->lookupGridId : null);
        if ($ids === [0]) {
            return [];
        }

        try {
            $db = \Config\Database::connect();
            $rows = [];
            foreach ($this->tokenTables($db) as $table) {
                foreach ($db->table($table)->whereIn('user_id', $ids)->get()->getResultArray() as $row) {
                    $rows[] = $row;
                }
            }
            if ($rows === [] && $db->fieldExists('mob_device_token', 'users')) {
                $idColumn = $this->portalIdColumn($db);
                foreach ($db->table('users')->select($idColumn . ', mob_device_token')->whereIn($idColumn, $ids)->get()->getResultArray() as $userRow) {
                    $token = trim((string) ($userRow['mob_device_token'] ?? ''));
                    if ($token === '') {
                        continue;
                    }
                    // Platform is unknown for this legacy column. Leaving it
                    // empty attaches the apns block, which Android ignores,
                    // so an iPhone stored here still gets a lock-screen banner.
                    $rows[] = [
                        'user_id' => (int) ($userRow[$idColumn] ?? 0),
                        'device_token' => $token,
                        'token_type' => 'fcm',
                        'platform' => '',
                    ];
                }
            }

            return $rows;
        } catch (\Throwable $e) {
            log_message('error', 'FCM token lookup failed for user ' . $userId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @return string[]
     */
    private function tokenTables($db): array
    {
        $tables = [];
        foreach (['user_device_tokens', 'device_tokens'] as $table) {
            try {
                if ($db->tableExists($table) && $db->fieldExists('device_token', $table) && $db->fieldExists('user_id', $table)) {
                    $tables[] = $table;
                }
            } catch (\Throwable $e) {
                log_message('error', 'FCM token table ' . $table . ': ' . $e->getMessage());
            }
        }

        return $tables;
    }

    /**
     * Device tokens for a callee. The chat request sends a Circle Connect id.
     * The phone registers its FCM token under the portal users.id that shares
     * the same grid id or phone. No ids are hardcoded.
     *
     * @param int|string $calleeUserId
     * @return string[]
     */
    public function getCalleeDeviceTokens($calleeUserId, $targetGridId = null): array
    {
        $this->ensureTable();
        $ids = $this->candidateUserIds((int) $calleeUserId, $targetGridId);
        $tokens = [];

        try {
            $db = \Config\Database::connect();
            if ($ids !== [0]) {
                foreach ($this->tokenTables($db) as $table) {
                    foreach ($db->table($table)->select('device_token')->whereIn('user_id', $ids)->get()->getResultArray() as $row) {
                        $token = trim((string) ($row['device_token'] ?? ''));
                        if ($token !== '') {
                            $tokens[] = $token;
                        }
                    }
                }
            }

            if ($tokens === [] && $ids !== [0] && $db->fieldExists('mob_device_token', 'users')) {
                $idColumn = $this->portalIdColumn($db);
                foreach ($db->table('users')->select('mob_device_token')->whereIn($idColumn, $ids)->get()->getResultArray() as $row) {
                    $token = trim((string) ($row['mob_device_token'] ?? ''));
                    if ($token !== '') {
                        $tokens[] = $token;
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'FCM token lookup failed for user ' . $calleeUserId . ': ' . $e->getMessage());
        }

        $tokens = array_values(array_unique($tokens));
        log_message('info', 'FCM token lookup callee ' . $calleeUserId . ' grid ' . trim((string) $targetGridId) . ' ids ' . implode(',', $ids) . ' tokens ' . count($tokens));

        return $tokens;
    }

    /**
     * Portal users.id plus every id that shares this callee's grid id or phone.
     *
     * @return int[]
     */
    private function candidateUserIds(int $userId, $targetGridId = null): array
    {
        $ids = [];
        if ($userId > 0) {
            $ids[] = $userId;
        }

        foreach (['chat_user_id', 'circle_user_id', 'portal_user_id', 'callee_chat_user_id', 'callee_portal_user_id', 'callee_user_id'] as $key) {
            $posted = (int) $this->requestValue($key);
            if ($posted > 0) {
                $ids[] = $posted;
            }
        }

        $gridId = trim((string) ($targetGridId ?? ''));
        if ($gridId === '') {
            $gridId = $this->lookupGridId;
        }
        if ($gridId !== '') {
            $portalId = $this->userIdForGridId($gridId);
            if ($portalId > 0) {
                $ids[] = $portalId;
            }
        }

        try {
            $db = \Config\Database::connect();
            $idColumn = $this->portalIdColumn($db);
            $this->appendAppUserLinks($db, $idColumn, $userId, $ids);
            $this->appendGridUserLinks($userId, $ids);
            $this->appendChatProfilePhone($db, $idColumn, $userId, $ids);
            $postedPhone = $this->requestValue('target_phone');
            if ($postedPhone === '') {
                $postedPhone = $this->requestValue('phone');
            }
            $this->appendUsersByPhone($db, $idColumn, $postedPhone, $ids);
            if ($gridId !== '') {
                $this->appendUsersByGridId($db, $idColumn, $gridId, $ids);
            }
        } catch (\Throwable $e) {
            log_message('error', 'FCM user id link: ' . $e->getMessage());
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0)));

        return $ids === [] ? [0] : $ids;
    }

    /**
     * @param int[] $ids
     */
    private function appendAppUserLinks($db, string $idColumn, int $userId, array &$ids): void
    {
        if ($userId <= 0) {
            return;
        }
        $appField = $this->appUserField($db);
        if ($appField === '') {
            return;
        }

        $row = $db->table('users')->select($idColumn . ', ' . $appField)->where($idColumn, $userId)->get()->getRowArray();
        $linked = (int) ($row[$appField] ?? 0);
        if ($linked > 0) {
            $ids[] = $linked;
        }
        foreach ($db->table('users')->select($idColumn)->where($appField, $userId)->get()->getResultArray() as $linkedRow) {
            $linkedId = (int) ($linkedRow[$idColumn] ?? 0);
            if ($linkedId > 0) {
                $ids[] = $linkedId;
            }
        }
    }

    /**
     * grid_users is optional. When it exists, its grid_id and phone point at the portal user.
     *
     * @param int[] $ids
     */
    private function appendGridUserLinks(int $userId, array &$ids): void
    {
        if ($userId <= 0) {
            return;
        }

        foreach ($this->databaseConnections() as $db) {
            try {
                if (!$db->tableExists('grid_users')) {
                    continue;
                }
                $select = [];
                foreach (['phone', 'grid_id', 'id', 'user_id', 'portal_user_id'] as $column) {
                    if ($db->fieldExists($column, 'grid_users')) {
                        $select[] = $column;
                    }
                }
                if ($select === []) {
                    continue;
                }

                $builder = $db->table('grid_users')->select(implode(', ', $select));
                $builder->groupStart();
                $hasMatch = false;
                if (in_array('id', $select, true)) {
                    $builder->where('id', $userId);
                    $hasMatch = true;
                }
                if (in_array('user_id', $select, true)) {
                    if ($hasMatch) {
                        $builder->orWhere('user_id', $userId);
                    } else {
                        $builder->where('user_id', $userId);
                    }
                    $hasMatch = true;
                }
                $builder->groupEnd();
                if (!$hasMatch) {
                    continue;
                }

                $chatUser = $builder->get()->getRowArray();
                if (!is_array($chatUser)) {
                    continue;
                }

                $portal = \Config\Database::connect();
                $idColumn = $this->portalIdColumn($portal);
                $portalUserId = (int) ($chatUser['portal_user_id'] ?? 0);
                if ($portalUserId > 0) {
                    $ids[] = $portalUserId;
                }
                if ($this->lookupGridId !== '' && in_array('grid_id', $select, true) && in_array('portal_user_id', $select, true)) {
                    foreach ($db->table('grid_users')->select('portal_user_id')->where('grid_id', $this->lookupGridId)->limit(5)->get()->getResultArray() as $gridRow) {
                        $linkedPortal = (int) ($gridRow['portal_user_id'] ?? 0);
                        if ($linkedPortal > 0) {
                            $ids[] = $linkedPortal;
                        }
                    }
                }
                $gridId = trim((string) ($chatUser['grid_id'] ?? ''));
                if ($gridId !== '') {
                    $this->appendUsersByGridId($portal, $idColumn, $gridId, $ids);
                }
                $this->appendUsersByPhone($portal, $idColumn, (string) ($chatUser['phone'] ?? ''), $ids);
            } catch (\Throwable $e) {
                log_message('error', 'FCM grid_users link: ' . $e->getMessage());
            }
        }
    }

    /**
     * Circle Connect profile phone (grid_chat_db.user_profiles) matched to users.phone.
     *
     * @param int[] $ids
     */
    private function appendChatProfilePhone($db, string $idColumn, int $userId, array &$ids): void
    {
        if ($userId <= 0) {
            return;
        }

        try {
            $chat = \Config\Database::connect('gridchat', false);
            if (!$chat->tableExists('user_profiles') || !$chat->fieldExists('phone', 'user_profiles')) {
                return;
            }
            $row = $chat->table('user_profiles')->select('phone')->where('user_id', $userId)->get()->getRowArray();
            $this->appendUsersByPhone($db, $idColumn, (string) ($row['phone'] ?? ''), $ids);
        } catch (\Throwable $e) {
            log_message('error', 'FCM chat phone link: ' . $e->getMessage());
        }
    }

    /**
     * @param int[] $ids
     */
    private function appendUsersByGridId($db, string $idColumn, string $gridId, array &$ids): void
    {
        $gridId = ltrim(trim($gridId), '@');
        if ($gridId === '' || !$db->fieldExists('grid_id', 'users')) {
            return;
        }

        foreach ($db->table('users')->select($idColumn)->where('grid_id', $gridId)->limit(5)->get()->getResultArray() as $row) {
            $id = (int) ($row[$idColumn] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }
    }

    /**
     * @param int[] $ids
     */
    private function appendUsersByPhone($db, string $idColumn, string $phone, array &$ids): void
    {
        $phone = trim($phone);
        if (strlen($phone) < 8 || !$db->fieldExists('phone', 'users')) {
            return;
        }

        foreach ($db->table('users')->select($idColumn)->where('phone', $phone)->limit(5)->get()->getResultArray() as $row) {
            $id = (int) ($row[$idColumn] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }
    }

    private function portalIdColumn($db): string
    {
        try {
            if ($db->fieldExists('id', 'users')) {
                return 'id';
            }
            if ($db->fieldExists('user_id', 'users')) {
                return 'user_id';
            }
        } catch (\Throwable $e) {
            return 'id';
        }

        return 'id';
    }

    private function appUserField($db): string
    {
        try {
            foreach ($db->getFieldNames('users') as $field) {
                if (strcasecmp((string) $field, 'App_user') === 0) {
                    return (string) $field;
                }
            }
        } catch (\Throwable $e) {
            return '';
        }

        return '';
    }

    private function mobileSignalType(string $type): string
    {
        $type = strtoupper(trim($type));
        if (in_array($type, ['MISSED_CALL', 'CALL_MISSED', 'MISSED'], true)) {
            return 'MISSED_CALL';
        }
        if (in_array($type, ['CALL_HANDLED', 'CALL_CANCELLED', 'HANGUP', 'CALL_DECLINED', 'DECLINED', 'CANCELLED'], true)) {
            return 'CALL_CANCELLED';
        }

        return '';
    }

    /**
     * @param array<string, string> $data
     */
    private function sendFcm(string $token, array $data, string $platform = ''): void
    {
        $account = $this->serviceAccount();
        if ($account === null) {
            $this->lastFcmResult = ['status' => 0, 'body' => 'service account missing'];
            log_message('error', 'FCM HTTP v1 is not configured. Put the grid-factor service account JSON at app/Config/firebase-service-account.json or app/Config/firebase_credentials.json. The legacy fcm/send URL is not used.');
            return;
        }

        $this->sendFcmV1($token, $data, $account, $platform);
    }

    /**
     * Apple banner for a locked or closed iPhone. Android ignores this block.
     *
     * @param array<string, string> $data
     * @return array<string, mixed>|null
     */
    private function apnsBlock(array $data): ?array
    {
        $type = $data['type'] ?? '';
        if ($type === 'CALL_CANCELLED' || $type === 'field_tracking') {
            // Silent data push. field_tracking must reach the background
            // handler with no banner; a notification block would not start GPS.
            return [
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
        }

        if ($type === 'INCOMING_CALL') {
            // iOS contract: title is the caller, body names the call type
            $title = trim($data['caller_name'] ?? '') !== '' ? $data['caller_name'] : 'Efactor';
            $body = ($data['call_type'] ?? '') === 'video' ? 'Incoming Video Call...' : 'Incoming Audio Call...';
        } elseif ($type === 'NEW_MESSAGE' || $type === 'CHAT_MESSAGE') {
            if (($data['locked'] ?? '') === '1') {
                $title = 'GRID Chat';
                $body = 'New message';
            } else {
                $title = trim($data['sender_name'] ?? '') !== '' ? $data['sender_name'] : (trim($data['title'] ?? '') !== '' ? $data['title'] : 'Efactor');
                $body = trim($data['body'] ?? '') !== '' ? $data['body'] : 'New message';
            }
        } elseif ($type === 'MISSED_CALL') {
            $title = trim($data['caller_name'] ?? '') !== '' ? $data['caller_name'] : 'Efactor';
            $body = ($data['call_type'] ?? '') === 'video' ? 'Missed video call' : 'Missed audio call';
        } else {
            return null;
        }

        $aps = [
            'alert' => [
                'title' => $title,
                'body' => $body,
            ],
            'sound' => 'default',
            'content-available' => 1,
        ];
        if ($type === 'INCOMING_CALL') {
            // iOS app registers this category for the Accept / Decline buttons
            $aps['category'] = 'calls_category';
        } elseif ($type === 'NEW_MESSAGE' || $type === 'CHAT_MESSAGE') {
            $aps['badge'] = 1;
            // Lets an iOS Notification Service Extension run (delivery receipt)
            // even when the app was swiped away. Ignored if the app has none.
            $aps['mutable-content'] = 1;
            // Reply / Mark as read / Mute buttons. A locked chat gets no
            // lock-screen Reply, so its content stays behind the chat lock.
            if (($data['locked'] ?? '') !== '1') {
                $aps['category'] = 'messages_category';
            }
        }

        return [
            'headers' => [
                'apns-priority' => '10',
                'apns-push-type' => 'alert',
            ],
            'payload' => [
                'aps' => $aps,
            ],
        ];
    }

    /**
     * @param array<string, string> $data
     * @param array{project_id: string, client_email: string, private_key: string} $account
     */
    private function sendFcmV1(string $token, array $data, array $account, string $platform = ''): void
    {
        $accessToken = $this->googleAccessToken($account);
        if ($accessToken === '') {
            $this->lastFcmResult = ['status' => 0, 'body' => 'oauth failed'];
            return;
        }

        $data = $this->stringData($data);
        if ($platform === 'ios') {
            $data = $this->iosData($data);
        }
        $isCall = ($data['type'] ?? '') === 'INCOMING_CALL';
        // No top-level notification block. Android delivers the data map to
        // the call handler. The apns block is only for Apple.
        // HTTP v1 rejects a message.priority field. android.priority HIGH
        // is what wakes a locked or killed Android phone.
        $message = [
            'token' => $token,
            'data' => $data,
            'android' => [
                'priority' => 'HIGH',
            ],
        ];
        if ($isCall || ($data['type'] ?? '') === 'CALL_CANCELLED') {
            $message['android']['ttl'] = $isCall ? '60s' : '30s';
            $message['android']['direct_boot_ok'] = true;
        }
        if (($data['type'] ?? '') === 'field_tracking') {
            // Data-only, high priority. 1h TTL so a phone that was offline
            // still receives start/stop; the app drops it when issued_at is stale.
            $message['android']['ttl'] = '3600s';
            $message['android']['direct_boot_ok'] = true;
        }
        // Android stays data-only. The Apple banner is attached for an iOS
        // token, and also when the stored row has no platform.
        if ($platform !== 'android') {
            $apns = $this->apnsBlock($data);
            if ($apns !== null) {
                $message['apns'] = $apns;
                // A known iOS token also gets the top-level notification block
                // (same text as the Apple alert). Android and legacy rows with
                // no platform stay data-only so the Android handler still runs.
                $alert = $apns['payload']['aps']['alert'] ?? null;
                if ($platform === 'ios' && is_array($alert)) {
                    $message['notification'] = [
                        'title' => (string) ($alert['title'] ?? ''),
                        'body' => (string) ($alert['body'] ?? ''),
                    ];
                }
            }
        }

        $result = $this->postFcmV1($account, $accessToken, $message);
        if ($result['status'] === 400 && isset($message['notification']) && stripos($result['body'], 'notification') !== false) {
            unset($message['notification']);
            $result = $this->postFcmV1($account, $accessToken, $message);
        }
        if ($result['status'] === 400 && stripos($result['body'], 'ttl') !== false) {
            unset($message['android']['ttl']);
            $result = $this->postFcmV1($account, $accessToken, $message);
        }
        if ($result['status'] === 400 && stripos($result['body'], 'direct_boot') !== false) {
            unset($message['android']['direct_boot_ok']);
            $result = $this->postFcmV1($account, $accessToken, $message);
        }
        if ($result['status'] === 400 && stripos($result['body'], 'priority') !== false) {
            $message['android']['priority'] = 'high';
            $result = $this->postFcmV1($account, $accessToken, $message);
        }
        if ($result['status'] === 400 && isset($message['apns']) && preg_match('/apns|aps|alert|badge/i', $result['body'])) {
            unset($message['apns']);
            $result = $this->postFcmV1($account, $accessToken, $message);
        }

        $this->lastFcmResult = ['status' => $result['status'], 'body' => $result['body']];
        $this->logFcmResult($result);
        if ($this->isUnregistered($result['status'], $result['body'])) {
            $this->deleteToken($token);
        }
    }

    /**
     * @param array{project_id: string, client_email: string, private_key: string} $account
     * @param array<string, mixed> $message
     * @return array{status: int, errno: int, body: string}
     */
    private function postFcmV1(array $account, string $accessToken, array $message): array
    {
        $url = 'https://fcm.googleapis.com/v1/projects/' . rawurlencode($account['project_id']) . '/messages:send';

        return $this->curlJson($url, ['message' => $message], [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);
    }

    /**
     * Sends a PushKit (VoIP) push straight to APNs over HTTP/2 with a .p8
     * token key. Returns true when APNs accepted it (HTTP 200).
     *
     * Settings (server only, never sent to the app):
     *   APNS_KEY_ID, APNS_TEAM_ID, APNS_BUNDLE_ID (topic becomes <bundle>.voip),
     *   APNS_KEY_PATH (path to AuthKey_XXXX.p8) or APNS_PRIVATE_KEY (PEM text),
     *   APNS_ENVIRONMENT = production (default) or sandbox.
     * Or the same keys in app/Config/apns.json:
     *   {"key_id","team_id","bundle_id","key_path","environment"}.
     *
     * @param array<string, string> $data
     */
    private function sendApnsVoip(string $token, array $data): bool
    {
        $config = $this->apnsConfig();
        if ($config === null) {
            return false;
        }
        if (!defined('CURL_VERSION_HTTP2') || !(curl_version()['features'] & CURL_VERSION_HTTP2)) {
            log_message('error', 'APNs VoIP not sent: this PHP cURL build has no HTTP/2 support, which APNs requires.');
            return false;
        }

        $storedToken = $token;
        $token = strtolower(preg_replace('/[^0-9a-fA-F]/', '', $token) ?? '');
        if ($token === '') {
            return false;
        }

        $type = (string) ($data['type'] ?? '');
        $payload = $this->stringData($data);
        $ttl = $type === 'CALL_CANCELLED' ? 30 : 60;
        $headers = [
            'apns-push-type: voip',
            'apns-topic: ' . $config['topic'],
            'apns-priority: 10',
            'apns-expiration: ' . (time() + $ttl),
            'Content-Type: application/json',
        ];

        $hosts = $config['environment'] === 'sandbox'
            ? ['https://api.sandbox.push.apple.com', 'https://api.push.apple.com']
            : ['https://api.push.apple.com', 'https://api.sandbox.push.apple.com'];

        $result = ['status' => 0, 'errno' => 0, 'body' => ''];
        foreach ($hosts as $index => $host) {
            $jwt = $this->apnsJwt($config, false);
            if ($jwt === '') {
                return false;
            }
            $result = $this->postApns($host . '/3/device/' . $token, $payload, array_merge($headers, ['Authorization: bearer ' . $jwt]));
            $reason = $this->apnsReason($result['body']);

            if ($result['status'] === 403 && in_array($reason, ['ExpiredProviderToken', 'InvalidProviderToken'], true)) {
                $jwt = $this->apnsJwt($config, true);
                if ($jwt === '') {
                    return false;
                }
                $result = $this->postApns($host . '/3/device/' . $token, $payload, array_merge($headers, ['Authorization: bearer ' . $jwt]));
                $reason = $this->apnsReason($result['body']);
            }

            // A debug (Xcode) build token only works on sandbox and a
            // TestFlight / App Store token only on production: try the other host once.
            if ($index === 0 && $result['status'] === 400 && $reason === 'BadDeviceToken') {
                continue;
            }
            break;
        }

        $this->lastFcmResult = ['status' => $result['status'], 'body' => $result['body']];
        $label = 'APNs VoIP ' . $type . ' token ' . substr($token, 0, 8) . '... HTTP ' . $result['status'] . ' curl ' . $result['errno'];
        if ($result['status'] === 200) {
            log_message('info', $label);
            return true;
        }

        $reason = $this->apnsReason($result['body']);
        log_message('error', $label . ' reason ' . ($reason !== '' ? $reason : substr($result['body'], 0, 200)));
        if ($result['status'] === 410 || ($result['status'] === 400 && $reason === 'BadDeviceToken')) {
            // 410 = app uninstalled / token no longer valid; BadDeviceToken failed on both hosts
            $this->deleteToken($storedToken);
        }

        return false;
    }

    /**
     * @param array<string, string> $payload
     * @param string[] $headers
     * @return array{status: int, errno: int, body: string}
     */
    private function postApns(string $url, array $payload, array $headers): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload === [] ? new \stdClass() : $payload),
            CURLOPT_HTTP_VERSION => defined('CURL_HTTP_VERSION_2TLS') ? CURL_HTTP_VERSION_2TLS : CURL_HTTP_VERSION_2_0,
            CURLOPT_CONNECTTIMEOUT => 6,
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

    private function apnsReason(string $body): string
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) ? trim((string) ($decoded['reason'] ?? '')) : '';
    }

    /**
     * @return array{key_id: string, team_id: string, topic: string, environment: string, private_key: string}|null
     */
    private function apnsConfig(): ?array
    {
        if ($this->apnsConfig !== false) {
            return $this->apnsConfig;
        }
        $this->apnsConfig = null;

        $file = [];
        $jsonPath = APPPATH . 'Config/apns.json';
        if (is_file($jsonPath)) {
            $decoded = json_decode((string) file_get_contents($jsonPath), true);
            if (is_array($decoded)) {
                $file = $decoded;
            } else {
                log_message('error', 'APNs: app/Config/apns.json is not valid JSON.');
            }
        }

        $value = function (string $envName, string $fileKey) use ($file): string {
            $env = function_exists('env') ? env($envName) : getenv($envName);
            if (is_string($env) && trim($env) !== '') {
                return trim($env);
            }

            return trim((string) ($file[$fileKey] ?? ''));
        };

        $keyId = $value('APNS_KEY_ID', 'key_id');
        $teamId = $value('APNS_TEAM_ID', 'team_id');
        $bundleId = $value('APNS_BUNDLE_ID', 'bundle_id');
        $environment = strtolower($value('APNS_ENVIRONMENT', 'environment')) === 'sandbox' ? 'sandbox' : 'production';

        $privateKey = $value('APNS_PRIVATE_KEY', 'private_key');
        if ($privateKey !== '') {
            $privateKey = str_replace('\n', "\n", $privateKey);
        } else {
            $keyPath = $value('APNS_KEY_PATH', 'key_path');
            if ($keyPath !== '' && !preg_match('#^([A-Za-z]:[\\\\/]|/)#', $keyPath)) {
                $keyPath = APPPATH . 'Config/' . $keyPath;
            }
            if ($keyPath === '' && $keyId !== '' && is_file(APPPATH . 'Config/AuthKey_' . $keyId . '.p8')) {
                $keyPath = APPPATH . 'Config/AuthKey_' . $keyId . '.p8';
            }
            if ($keyPath !== '' && is_file($keyPath)) {
                $privateKey = (string) file_get_contents($keyPath);
            }
        }

        if ($keyId === '' || $teamId === '' || $bundleId === '' || trim($privateKey) === '') {
            log_message('notice', 'APNs VoIP is not configured (APNS_KEY_ID, APNS_TEAM_ID, APNS_BUNDLE_ID and the .p8 key are required). iOS calls fall back to FCM.');
            return null;
        }

        $topic = substr($bundleId, -5) === '.voip' ? $bundleId : $bundleId . '.voip';
        $this->apnsConfig = [
            'key_id' => $keyId,
            'team_id' => $teamId,
            'topic' => $topic,
            'environment' => $environment,
            'private_key' => $privateKey,
        ];

        return $this->apnsConfig;
    }

    /**
     * ES256 provider token. Apple accepts one for up to 60 minutes and rejects
     * refreshing more than once every 20, so it is cached for 40.
     *
     * @param array{key_id: string, team_id: string, private_key: string} $config
     */
    private function apnsJwt(array $config, bool $forceNew): string
    {
        $cacheFile = WRITEPATH . 'cache/apns-jwt.json';
        $now = time();
        if (!$forceNew && is_file($cacheFile)) {
            $cached = json_decode((string) @file_get_contents($cacheFile), true);
            if (is_array($cached)
                && ($cached['kid'] ?? '') === $config['key_id']
                && ($cached['iss'] ?? '') === $config['team_id']
                && (int) ($cached['iat'] ?? 0) > $now - 2400
                && trim((string) ($cached['token'] ?? '')) !== '') {
                return trim((string) $cached['token']);
            }
        }

        $key = openssl_pkey_get_private($config['private_key']);
        if ($key === false) {
            log_message('error', 'APNs: the .p8 key could not be read. Check the file is the original AuthKey_' . $config['key_id'] . '.p8 from Apple.');
            return '';
        }

        $input = $this->base64Url((string) json_encode(['alg' => 'ES256', 'kid' => $config['key_id']]))
            . '.' . $this->base64Url((string) json_encode(['iss' => $config['team_id'], 'iat' => $now]));
        $der = '';
        if (!openssl_sign($input, $der, $key, OPENSSL_ALGO_SHA256)) {
            log_message('error', 'APNs: signing the provider token with the .p8 key failed.');
            return '';
        }
        $raw = $this->ecdsaDerToRaw($der);
        if ($raw === '') {
            log_message('error', 'APNs: the .p8 key did not produce an ES256 signature (is it an Apple EC P-256 key?).');
            return '';
        }

        $jwt = $input . '.' . $this->base64Url($raw);
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($cacheFile, json_encode([
            'kid' => $config['key_id'],
            'iss' => $config['team_id'],
            'iat' => $now,
            'token' => $jwt,
        ]), LOCK_EX);

        return $jwt;
    }

    /**
     * openssl_sign returns an ASN.1 DER ECDSA signature; JWT ES256 needs R || S (64 bytes).
     */
    private function ecdsaDerToRaw(string $der, int $partLength = 32): string
    {
        $length = strlen($der);
        if ($length < 8 || ord($der[0]) !== 0x30) {
            return '';
        }
        $offset = 2;
        if (ord($der[1]) & 0x80) {
            $offset += ord($der[1]) & 0x7f;
        }

        $raw = '';
        for ($i = 0; $i < 2; $i++) {
            if ($offset + 2 > $length || ord($der[$offset]) !== 0x02) {
                return '';
            }
            $partLen = ord($der[$offset + 1]);
            $offset += 2;
            if ($offset + $partLen > $length) {
                return '';
            }
            $part = ltrim(substr($der, $offset, $partLen), "\x00");
            $offset += $partLen;
            if (strlen($part) > $partLength) {
                return '';
            }
            $raw .= str_pad($part, $partLength, "\x00", STR_PAD_LEFT);
        }

        return $raw;
    }

    /**
     * Data keys the iOS app expects (CHAT_MESSAGE, url = chat API base).
     * Only applied to tokens registered with platform "ios"; Android
     * payloads are unchanged. The open-chat link moves to chat_url.
     *
     * @param array<string, string> $data
     * @return array<string, string>
     */
    private function iosData(array $data): array
    {
        if (($data['type'] ?? '') !== 'NEW_MESSAGE') {
            return $data;
        }

        $data['type'] = 'CHAT_MESSAGE';
        if (isset($data['url'])) {
            $data['chat_url'] = $data['url'];
        }
        $data['url'] = $this->apiUrl();

        return $data;
    }

    private function apiUrl(): string
    {
        $url = GridWebSso::publicWebUrl(self::ROOM_PATH . '/api');
        if ($url !== '') {
            return $url;
        }
        helper('url');

        return site_url(self::ROOM_PATH . '/api');
    }

    /**
     * FCM data values must be strings.
     *
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function stringData(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }
            $out[$key] = is_scalar($value) ? (string) $value : '';
        }

        return $out;
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

        foreach ([
            APPPATH . 'Config/firebase_credentials.json',
            APPPATH . 'Config/firebase-service-account.json',
            APPPATH . 'config/firebase_credentials.json',
        ] as $bundled) {
            if (is_file($bundled)) {
                return $bundled;
            }
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

    private function roomUrl(): string
    {
        $url = GridWebSso::publicWebUrl(self::ROOM_PATH);
        if ($url !== '') {
            return $url;
        }
        helper('url');

        return site_url(self::ROOM_PATH);
    }

    private function isActiveOnWebSocket(int $userId): bool
    {
        if ($userId <= 0 || !function_exists('curl_init')) {
            return false;
        }

        $url = getenv('GRID_WS_STATUS_URL');
        if (!is_string($url) || trim($url) === '') {
            $url = 'http://127.0.0.1:8085/status';
        }

        try {
            $ch = curl_init(trim($url));
            if ($ch === false) {
                return false;
            }
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 1,
                CURLOPT_CONNECTTIMEOUT => 1,
            ]);
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (\Throwable $e) {
            log_message('error', 'WebSocket presence check: ' . $e->getMessage());
            return false;
        }

        if ($errno !== 0 || $status !== 200 || !is_string($body) || $body === '') {
            return false;
        }

        $decoded = json_decode($body, true);
        $users = is_array($decoded) ? ($decoded['connectedUsers'] ?? null) : null;
        if (!is_array($users)) {
            return false;
        }

        $needle = (string) $userId;
        foreach ($users as $connectedId) {
            if (trim((string) $connectedId) === $needle) {
                return true;
            }
        }

        return false;
    }

    private function chatUrl(string $conversationId, string $callId = ''): string
    {
        helper('url');
        $url = site_url(self::ROOM_PATH);
        $query = [];
        if (trim($conversationId) !== '') {
            $query['conversation_id'] = trim($conversationId);
        }
        if (trim($callId) !== '') {
            $query['call_id'] = trim($callId);
        }
        if ($query === []) {
            return $url;
        }

        return $url . '?' . http_build_query($query);
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
     * @return array{name: string, grid_id: string, avatar: string}
     */
    private function userProfile(int $userId): array
    {
        $name = 'Efactor';
        $gridId = '';
        $avatar = '';
        if ($userId <= 0) {
            return ['name' => $name, 'grid_id' => $gridId, 'avatar' => $avatar];
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
        $avatar = $this->avatarUrl($userId);

        return ['name' => $name, 'grid_id' => $gridId, 'avatar' => $avatar];
    }

    private function avatarUrl(int $userId): string
    {
        if ($userId <= 0) {
            return '';
        }

        try {
            $db = \Config\Database::connect('gridchat', false);
            if ($db->tableExists('user_profiles')) {
                $row = $db->table('user_profiles')
                    ->select('avatar_path')
                    ->where('user_id', $userId)
                    ->get()
                    ->getRowArray();
                $path = trim((string) ($row['avatar_path'] ?? ''));
                if ($path !== '') {
                    helper('url');
                    return base_url($path);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Push avatar profile: ' . $e->getMessage());
        }

        try {
            $db = \Config\Database::connect();
            if ($db->fieldExists('image', 'users')) {
                $row = $db->table('users')
                    ->select('image')
                    ->where('id', $userId)
                    ->get()
                    ->getRowArray();
                $image = trim((string) ($row['image'] ?? ''));
                if ($image !== '') {
                    helper('general');
                    return get_avatar($image);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Push avatar user: ' . $e->getMessage());
        }

        return '';
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
        try {
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
        } catch (\Throwable $e) {
            log_message('error', 'user_device_tokens table check: ' . $e->getMessage());
        }
    }

    private function ensureCallTable(): void
    {
        try {
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
        } catch (\Throwable $e) {
            log_message('error', 'user_call_signals table check: ' . $e->getMessage());
        }
    }
}
