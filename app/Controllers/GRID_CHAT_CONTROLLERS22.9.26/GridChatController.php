<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

use App\Controllers\Security_Controller;
use App\Models\GRID_CHAT_MODELS\UserProfileModel;

class GridChatController extends Security_Controller
{
    /** Public folder (under FCPATH) for GRID Chat profile photos. */
    private const AVATAR_DIR = 'files/grid_chat/profile/';

    public function __construct()
    {
        $uri = service('uri');
        $segments = $uri ? $uri->getSegments() : [];
        $isAsset = in_array('assets', $segments, true);
        parent::__construct(!$isAsset);
        helper(['url', 'form', 'date_time']);
    }

    /**
     * Render the Grid Chat UI View
     */
    public function index(...$params)
    {
        $login_user_id = $this->login_user->id ?? 0;
        if (!$login_user_id) {
            return redirect()->to(get_uri('signin'));
        }

        // Detect if media route or view was requested directly via URL
        $uri = service('uri');
        $segments = $uri ? $uri->getSegments() : [];
        $is_media_route = in_array('media', $segments, true) || (isset($_GET['view']) && $_GET['view'] === 'media');
        $initial_route = $is_media_route ? 'media' : '';

        $view_data = [
            'current_user' => $this->buildCurrentUser(),
            'api_base' => site_url('grid-chat/api'),
            'asset_base' => site_url('grid-chat/assets'),
            'csrf_token_name' => csrf_token(),
            'csrf_hash' => csrf_hash(),
            'initial_route' => $initial_route,
        ];

        return view('GRID_CHAT/index', $view_data);
    }

    /**
     * Deliver static Vite bundle assets with proper Content-Type headers
     */
    public function asset(string $filename = '')
    {
        // Sanitize filename to prevent directory traversal and strip any query params
        $filename = explode('?', basename($filename))[0];
        $filePath = APPPATH . 'Views/GRID_CHAT/dist/assets/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404, 'Asset Not Found');
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'js' => 'application/javascript; charset=utf-8',
            'mjs' => 'application/javascript; charset=utf-8',
            'css' => 'text/css; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
        ];

        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setBody(file_get_contents($filePath));
    }

    /**
     * Handle API requests from GRID_CHAT frontend
     */
    public function api(string $action = '')
    {
        $login_user_id = $this->login_user->id ?? 0;
        if (!$login_user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(401);
        }

        switch ($action) {
            case 'current_user':
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $this->buildCurrentUser(),
                ]);

            case 'update_profile':
                return $this->updateProfile((int) $login_user_id);

            case 'upload_avatar':
                return $this->uploadAvatar((int) $login_user_id);

            case 'remove_avatar':
                return $this->removeAvatar((int) $login_user_id);

            case 'contacts':
                $users = [];
                try {
                    $db = \Config\Database::connect();
                    $builder = $db->table('users');
                    $builder->select('id, first_name, last_name, email, image, user_type, status, last_online');
                    $builder->where('deleted', 0);
                    $builder->where('id !=', $login_user_id);
                    $builder->limit(50);
                    $query = $builder->get();

                    foreach ($query->getResult() as $row) {
                        $name = trim($row->first_name . ' ' . $row->last_name);
                        if (empty($name)) {
                            $name = explode('@', $row->email)[0];
                        }

                        $isOnline = false;
                        if (!empty($row->last_online)) {
                            $diff = time() - strtotime($row->last_online);
                            $isOnline = ($diff < 300); // within 5 minutes
                        }

                        // Query the latest message between these users if any
                        $lastMsgText = '';
                        $lastMsgTime = '';
                        $hasMessage = false;

                        try {
                            $msgBuilder = $db->table('messages');
                            $msgBuilder->select('message, created_at');
                            $msgBuilder->groupStart()
                                ->where('from_user_id', $login_user_id)->where('to_user_id', $row->id)
                                ->groupEnd()
                                ->orGroupStart()
                                ->where('from_user_id', $row->id)->where('to_user_id', $login_user_id)
                                ->groupEnd();
                            $msgBuilder->orderBy('id', 'DESC');
                            $msgBuilder->limit(1);
                            $latestMsg = $msgBuilder->get()->getRow();
                            if ($latestMsg && !empty($latestMsg->message)) {
                                $lastMsgText = $latestMsg->message;
                                $lastMsgTime = $this->format_compact_time($latestMsg->created_at);
                                $hasMessage = true;
                            }
                        } catch (\Throwable $mEx) {
                            // ignore fallback
                        }

                        // Only include users who actually have message history in recent conversations
                        if ($hasMessage) {
                            $users[] = [
                                'id' => (int) $row->id,
                                'name' => $name,
                                'email' => $row->email,
                                'time' => $lastMsgTime,
                                'avatar' => !empty($row->image) ? get_avatar($row->image) : null,
                                'text' => $lastMsgText,
                                'type' => 'text',
                                'status' => $isOnline ? 'online' : 'offline',
                                'hasMessage' => true,
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'GRID_CHAT contacts error: ' . $e->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'data' => $users,
                ]);

            case 'messages':
                $to_user_id = (int) $this->request->getGet('user_id');
                if (!$to_user_id) {
                    return $this->response->setJSON(['success' => true, 'data' => []]);
                }

                $formatted_messages = [];
                try {
                    $db = \Config\Database::connect();
                    $builder = $db->table('messages');
                    $builder->groupStart()
                        ->where('from_user_id', $login_user_id)->where('to_user_id', $to_user_id)
                        ->groupEnd()
                        ->orGroupStart()
                        ->where('from_user_id', $to_user_id)->where('to_user_id', $login_user_id)
                        ->groupEnd();
                    $builder->orderBy('created_at', 'ASC');
                    $builder->limit(100);
                    $query = $builder->get();

                    foreach ($query->getResult() as $m) {
                        $isOutgoing = ((int) $m->from_user_id === (int) $login_user_id);
                        $formatted_messages[] = [
                            'id' => (int) $m->id,
                            'type' => $isOutgoing ? 'outgoing' : 'incoming',
                            'text' => $m->message,
                            'time' => !empty($m->created_at) ? date('H:i', strtotime($m->created_at)) : '',
                            'check' => true,
                        ];
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'GRID_CHAT messages fetch error: ' . $e->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'data' => $formatted_messages,
                ]);

            case 'send_message':
                $to_user_id = (int) $this->request->getPost('to_user_id');
                $message_text = trim((string) $this->request->getPost('message'));

                if (!$to_user_id || empty($message_text)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Recipient and message cannot be empty',
                        'csrf_hash' => csrf_hash(),
                    ]);
                }

                try {
                    $db = \Config\Database::connect();
                    $data = [
                        'from_user_id' => $login_user_id,
                        'to_user_id' => $to_user_id,
                        'subject' => 'Chat Message',
                        'message' => $message_text,
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => 'unread',
                        'deleted_by_users' => '',
                        'files' => serialize([]),
                    ];

                    $builder = $db->table('messages');
                    $builder->insert($data);
                    $insert_id = $db->insertID();

                    return $this->response->setJSON([
                        'success' => true,
                        'message_id' => $insert_id,
                        'csrf_hash' => csrf_hash(),
                    ]);
                } catch (\Throwable $e) {
                    log_message('error', 'GRID_CHAT send_message error: ' . $e->getMessage());
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to save message',
                        'error' => $e->getMessage(),
                        'csrf_hash' => csrf_hash(),
                    ]);
                }

            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unknown action: ' . htmlspecialchars($action),
                ]);
        }
    }

    /**
     * Current user for the React app: CRM user (factor_db2, read-only)
     * merged with the GRID Chat profile row (grid_chat_db). Empty profile
     * fields fall back to the CRM values.
     */
    private function buildCurrentUser(): array
    {
        $user_id = (int) ($this->login_user->id ?? 0);

        try {
            $user = (new UserProfileModel())->getUser($user_id);
        } catch (\Throwable $e) {
            log_message('error', 'GRID_CHAT current user read error: ' . $e->getMessage());
            $user = [
                'name' => trim(($this->login_user->first_name ?? '') . ' ' . ($this->login_user->last_name ?? '')),
                'email' => $this->login_user->email ?? '',
                'phone' => '',
                'role' => '',
                'bio' => '',
                'avatar' => !empty($this->login_user->image) ? get_avatar($this->login_user->image) : '',
                'avatarSource' => !empty($this->login_user->image) ? 'crm' : 'none',
            ];
        }

        return [
            'id' => $user_id,
            'name' => $user['name'] ?: ($this->login_user->email ?? 'User'),
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'] ?: ($this->login_user->role_title ?? ($this->login_user->user_type ?? 'Team Member')),
            'bio' => $user['bio'],
            'avatar' => $user['avatar'],
            // chat = uploaded here (removable), crm = from the CRM, none = initials
            'avatarSource' => $user['avatarSource'] ?? 'none',
            'isCurrentUser' => true,
        ];
    }

    private function profileJson(bool $success, string $message = '', int $status = 200)
    {
        $body = [
            'success' => $success,
            'csrf_hash' => csrf_hash(),
        ];
        if ($message !== '') {
            $body['message'] = $message;
        }
        if ($success) {
            $body['data'] = $this->buildCurrentUser();
        }
        return $this->response->setStatusCode($status)->setJSON($body);
    }

    /**
     * POST name, role, phone, bio → grid_chat_db.user_profiles
     */
    private function updateProfile(int $user_id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->profileJson(false, 'POST required', 405);
        }

        $name  = trim((string) $this->request->getPost('name'));
        $role  = trim((string) $this->request->getPost('role'));
        $phone = trim((string) $this->request->getPost('phone'));
        $bio   = trim((string) $this->request->getPost('bio'));

        if ($name === '' || mb_strlen($name) > 50) {
            return $this->profileJson(false, 'Display name must be 1-50 characters', 422);
        }
        if (mb_strlen($role) > 60) {
            return $this->profileJson(false, 'Role / headline must be at most 60 characters', 422);
        }
        // Lenient: existing CRM phones may use other formats
        if (mb_strlen($phone) > 20 || preg_match('/[\x00-\x1F<>]/', $phone)) {
            return $this->profileJson(false, 'Phone number is not valid (max 20 characters)', 422);
        }
        if (mb_strlen($bio) > 180) {
            return $this->profileJson(false, 'Bio must be at most 180 characters', 422);
        }

        try {
            (new UserProfileModel())->upsert($user_id, [
                'display_name' => $name,
                'headline' => $role !== '' ? $role : null,
                'phone' => $phone !== '' ? $phone : null,
                'bio' => $bio !== '' ? $bio : null,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'GRID_CHAT update_profile error: ' . $e->getMessage());
            return $this->profileJson(false, 'Failed to save profile', 500);
        }

        return $this->profileJson(true, 'Profile saved');
    }

    /**
     * POST multipart "avatar" → files/grid_chat/profile/, path in user_profiles
     */
    private function uploadAvatar(int $user_id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->profileJson(false, 'POST required', 405);
        }

        $file = $this->request->getFile('avatar');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->profileJson(false, 'No valid image uploaded', 422);
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $mime = $file->getMimeType();
        if (!isset($allowed[$mime]) || @getimagesize($file->getTempName()) === false) {
            return $this->profileJson(false, 'Please upload a JPG, PNG, WebP or GIF image', 422);
        }
        if ($file->getSize() > 8 * 1024 * 1024) {
            return $this->profileJson(false, 'Photo too large (max 8MB)', 422);
        }

        $target_dir = FCPATH . self::AVATAR_DIR;
        if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
            return $this->profileJson(false, 'Upload folder is not writable', 500);
        }

        $model = new UserProfileModel();
        try {
            $old = $model->getByUserId($user_id);

            $new_name = 'u' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
            $file->move($target_dir, $new_name);

            $model->upsert($user_id, ['avatar_path' => self::AVATAR_DIR . $new_name]);

            $this->deleteOldAvatar($old['avatar_path'] ?? null);
        } catch (\Throwable $e) {
            log_message('error', 'GRID_CHAT upload_avatar error: ' . $e->getMessage());
            return $this->profileJson(false, 'Failed to save photo', 500);
        }

        return $this->profileJson(true, 'Profile photo updated');
    }

    /**
     * POST → remove the uploaded chat photo; the app falls back to the CRM photo or initials
     */
    private function removeAvatar(int $user_id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->profileJson(false, 'POST required', 405);
        }

        $model = new UserProfileModel();
        try {
            $old = $model->getByUserId($user_id);
            if (!empty($old['avatar_path'])) {
                $model->upsert($user_id, ['avatar_path' => null]);
                $this->deleteOldAvatar($old['avatar_path']);
            }
        } catch (\Throwable $e) {
            log_message('error', 'GRID_CHAT remove_avatar error: ' . $e->getMessage());
            return $this->profileJson(false, 'Failed to remove photo', 500);
        }

        return $this->profileJson(true, 'Profile photo removed');
    }

    /**
     * Remove a previously uploaded GRID Chat avatar (only inside AVATAR_DIR).
     */
    private function deleteOldAvatar(?string $path): void
    {
        if (empty($path) || strpos($path, self::AVATAR_DIR) !== 0) {
            return;
        }
        $full = FCPATH . self::AVATAR_DIR . basename($path);
        if (is_file($full)) {
            @unlink($full);
        }
    }

    /**
     * Format timestamp to ultra-compact relative time (e.g. '2m ago', '1h ago', '11:55 am', 'Yesterday')
     */
    private function format_compact_time($timestamp): string
    {
        if (empty($timestamp)) {
            return '';
        }
        $time = is_numeric($timestamp) ? (int) $timestamp : strtotime((string) $timestamp);
        if (!$time) {
            return '';
        }

        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'now';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . 'm ago';
        }
        if ($diff < 86400 && date('Y-m-d', $time) === date('Y-m-d', $now)) {
            return date('g:i a', $time);
        }
        if ($diff < 172800 && date('Y-m-d', $time) === date('Y-m-d', strtotime('-1 day', $now))) {
            return 'Yesterday';
        }
        if ($diff < 604800) {
            return floor($diff / 86400) . 'd ago';
        }
        return date('M j', $time);
    }
}
