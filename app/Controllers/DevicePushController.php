<?php

namespace App\Controllers;

use App\Libraries\DevicePushService;
use CodeIgniter\Controller;

/**
 * Mobile push contract:
 * POST /api/register_device_token
 * POST /grid-chat/api/push/native-token
 * POST /api/call_action
 * POST /grid-chat/api/call/decline
 * POST /grid-chat/api/call/notify
 *
 * Session cookie, Authorization: Bearer <users.access_token>,
 * or access_token / accesstoken in the JSON body.
 * A Bearer token is accepted even when the webview session cookie is not set yet.
 */
class DevicePushController extends Controller
{
    public function registerDeviceToken()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Unauthorized'], 401);
        }

        $body = $this->jsonBody();
        $action = strtolower(trim((string) ($body['action'] ?? 'register_device_token')));
        $postedUserId = (int) ($body['user_id'] ?? 0);
        if ($postedUserId > 0 && $postedUserId !== (int) $user['id']) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'User does not match the access token'], 403);
        }

        $deviceToken = trim((string) ($body['device_token'] ?? ''));
        $platform = strtolower(trim((string) ($body['platform'] ?? '')));
        $tokenType = strtolower(trim((string) ($body['token_type'] ?? 'fcm')));
        if (!in_array($platform, ['android', 'ios'], true)) {
            $platform = 'android';
        }
        if (!in_array($tokenType, ['fcm', 'apns_voip'], true)) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'token_type must be fcm or apns_voip'], 422);
        }

        $push = new DevicePushService();
        if ($action === 'unregister_device_token') {
            $push->unregisterToken((int) $user['id'], $deviceToken, $platform, $tokenType);
            return $this->json(['status' => 'success', 'success' => true, 'message' => 'Device token removed']);
        }

        if ($deviceToken === '' || strlen($deviceToken) > 512) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'device_token is required'], 422);
        }

        $push->registerToken((int) $user['id'], $deviceToken, $platform, $tokenType);

        return $this->json(['status' => 'success', 'success' => true, 'message' => 'Device token saved']);
    }

    public function callAction()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Unauthorized'], 401);
        }

        $body = $this->jsonBody();
        $action = strtolower(trim((string) ($body['action'] ?? '')));
        $callId = trim((string) ($body['call_id'] ?? ''));
        $postedUserId = (int) ($body['user_id'] ?? 0);
        if ($postedUserId > 0 && $postedUserId !== (int) $user['id']) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'User does not match the access token'], 403);
        }
        if ($callId === '') {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'call_id is required'], 422);
        }

        $push = new DevicePushService();
        if (in_array($action, ['call_cancelled', 'call_canceled', 'hangup', 'call_ended'], true)) {
            $call = $push->callRecord($callId);
            $callerId = (int) $call['caller_id'];
            $calleeId = (int) $call['callee_id'];
            $me = (int) $user['id'];
            if ($calleeId <= 0) {
                $calleeId = (int) ($body['callee_user_id'] ?? 0);
            }
            $target = 0;
            if ($me === $callerId && $calleeId > 0) {
                $target = $calleeId;
            } elseif ($me === $calleeId && $callerId > 0) {
                $target = $callerId;
            } elseif ($calleeId > 0 && $calleeId !== $me) {
                $target = $calleeId;
            }
            if ($target > 0) {
                $push->sendCallSignal($target, $callId, 'CALL_CANCELLED');
            }

            return $this->json(['status' => 'success', 'success' => true, 'message' => 'Call cancelled']);
        }

        if ($action !== 'call_rejected') {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Unsupported call action'], 422);
        }

        $callerId = $push->callerIdForCall($callId);
        if ($callerId <= 0) {
            $callerId = (int) ($body['caller_id'] ?? 0);
        }
        if ($callerId > 0 && $callerId !== (int) $user['id']) {
            $push->sendCallSignal($callerId, $callId, 'CALL_HANDLED');
        }

        return $this->json(['status' => 'success', 'success' => true, 'message' => 'Call rejected']);
    }

    /**
     * POST call_id, caller_grid_id. Dismisses the call on the other devices.
     */
    public function declineCall()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Unauthorized'], 401);
        }

        $body = $this->jsonBody();
        $postedUserId = (int) ($body['user_id'] ?? 0);
        if ($postedUserId > 0 && $postedUserId !== (int) $user['id']) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'User does not match the access token'], 403);
        }
        $callId = trim((string) ($body['call_id'] ?? ''));
        $callerGridId = trim((string) ($body['caller_grid_id'] ?? ''));
        if ($callId === '') {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'call_id is required'], 422);
        }

        $push = new DevicePushService();
        $call = $push->callRecord($callId);
        $callerId = (int) $call['caller_id'];
        $calleeId = (int) $call['callee_id'];
        $me = (int) $user['id'];

        if ($callerId > 0 && $calleeId > 0 && $me !== $callerId && $me !== $calleeId) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'You are not part of this call'], 403);
        }

        if ($callerId <= 0 && $callerGridId !== '') {
            $callerId = $push->userIdForGridId($callerGridId);
        }

        if ($callerId > 0) {
            $push->sendCallSignal($callerId, $callId, 'CALL_HANDLED');
        }
        if ($calleeId > 0 && $calleeId !== $callerId) {
            $push->sendCallSignal($calleeId, $callId, 'CALL_HANDLED');
        } elseif ($calleeId <= 0 && $me > 0 && $me !== $callerId) {
            $push->sendCallSignal($me, $callId, 'CALL_HANDLED');
        }

        return $this->json(['status' => 'success', 'success' => true, 'message' => 'Call declined']);
    }

    /**
     * POST /grid-chat/api/call/notify
     * call_id, call_type, and callee_user_id or callee_grid_id.
     * Always sends the data-only INCOMING_CALL FCM to the callee device token.
     */
    public function notifyCall()
    {
        $user = $this->authenticatedUser();
        if ($user === null) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Unauthorized'], 401);
        }

        $body = $this->jsonBody();
        $me = (int) $user['id'];
        $postedCaller = (int) ($body['caller_user_id'] ?? 0);
        if ($postedCaller > 0 && $postedCaller !== $me) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'User does not match the access token'], 403);
        }

        $callId = trim((string) ($body['call_id'] ?? ''));
        $callType = strtolower(trim((string) ($body['call_type'] ?? 'audio')));
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/', $callId)) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'call_id is required'], 422);
        }
        if (!in_array($callType, ['audio', 'video'], true)) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'call_type must be audio or video'], 422);
        }

        $calleeId = $this->calleeIdFromBody($body, $me);
        if ($calleeId <= 0 || $calleeId === $me) {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Callee not found'], 422);
        }

        $targetGridId = '';
        foreach (['target_grid_id', 'callee_grid_id', 'recipient_grid_id', 'grid_id'] as $key) {
            $targetGridId = trim((string) ($body[$key] ?? ''));
            if ($targetGridId !== '') {
                break;
            }
        }

        $fcmSent = 0;
        $fcmHttp = 0;
        try {
            $pushResult = (new DevicePushService())->sendIncomingCall($me, $calleeId, $callId, $callType, $targetGridId);
            $fcmSent = (int) ($pushResult['sent'] ?? 0);
            $fcmHttp = (int) ($pushResult['http_code'] ?? 0);
        } catch (\Throwable $e) {
            log_message('error', 'Call notify push: ' . $e->getMessage());
        }

        return $this->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Call notify accepted',
            'call_id' => $callId,
            'callee_user_id' => $calleeId,
            'fcm_sent' => $fcmSent,
            'fcm_http_code' => $fcmHttp,
        ]);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function calleeIdFromBody(array $body, int $me): int
    {
        foreach (['callee_user_id', 'recipient_user_id', 'recipient_id', 'callee_id', 'to_user_id'] as $key) {
            $id = (int) ($body[$key] ?? 0);
            if ($id > 0) {
                return $id;
            }
        }

        $postedUserId = (int) ($body['user_id'] ?? 0);
        if ($postedUserId > 0 && $postedUserId !== $me) {
            return $postedUserId;
        }

        $push = new DevicePushService();
        foreach (['callee_grid_id', 'recipient_grid_id', 'grid_id'] as $key) {
            $gridId = trim((string) ($body[$key] ?? ''));
            if ($gridId === '') {
                continue;
            }
            $id = $push->userIdForGridId($gridId);
            if ($id > 0) {
                return $id;
            }
        }

        return 0;
    }

    /**
     * @return array<string, mixed>|null
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
            log_message('error', 'Device token session auth: ' . $e->getMessage());
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
        foreach (array('access_token', 'accesstoken', 'token') as $key) {
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
            (string) (getenv('HTTP_AUTHORIZATION') ?: ''),
            (string) (getenv('REDIRECT_HTTP_AUTHORIZATION') ?: ''),
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
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
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

    /**
     * @param array<string, mixed> $payload
     */
    private function json(array $payload, int $status = 200)
    {
        return $this->response->setStatusCode($status)->setJSON($payload);
    }
}
