<?php

namespace App\Controllers;

use App\Libraries\DevicePushService;
use CodeIgniter\Controller;

/**
 * Mobile push contract:
 * POST /api/register_device_token
 * POST /api/call_action
 * POST /grid-chat/api/call/decline
 *
 * Session cookie, Authorization: Bearer <users.access_token>,
 * or access_token / accesstoken in the JSON body.
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

        if ($action !== 'call_rejected') {
            return $this->json(['status' => 'failure', 'success' => false, 'message' => 'Unsupported call action'], 422);
        }

        $push = new DevicePushService();
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
     * @return array<string, mixed>|null
     */
    private function authenticatedUser(): ?array
    {
        $push = new DevicePushService();
        $token = $this->bearerToken();
        if ($token !== '') {
            $userId = $push->userIdForAccessToken($token);
            return $userId > 0 ? ['id' => $userId] : null;
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
        $header = $this->request->getHeaderLine('Authorization');
        if ($header === '' && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = (string) $_SERVER['HTTP_AUTHORIZATION'];
        }
        if ($header === '' && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if ($header === '' && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (is_array($headers)) {
                foreach ($headers as $key => $value) {
                    if (strcasecmp((string) $key, 'Authorization') === 0) {
                        $header = (string) $value;
                        break;
                    }
                }
            }
        }

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
