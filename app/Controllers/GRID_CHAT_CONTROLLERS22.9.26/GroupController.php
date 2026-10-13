<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

/**
 * GRID Chat groups (grid_chat_db): create, members, admins, name and photo.
 *
 * Rules:
 * - Only admins add/remove members, change admins, rename or change the photo.
 * - New members see only messages from the moment they were added.
 * - A group always keeps at least one admin (the longest-standing member is
 *   promoted when the last admin leaves).
 * - Removed members / members who left lose access to the group.
 */
class GroupController extends GridChatBaseController
{
    private const MAX_TITLE_LENGTH = 100;
    private const MAX_MEMBERS = 256;
    /** Group photos (public), inside the GRID Chat storage root files/grid_chat/ */
    private const PHOTO_DIR = 'files/grid_chat/groups/';
    private const PHOTO_MAX_BYTES = 5 * 1048576;
    private const PHOTO_MIMES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    /** POST title, user_ids (comma-separated or array) → new group */
    public function create()
    {
        return $this->guarded(function (int $me) {
            $input = ['title' => trim((string) $this->request->getPost('title'))];
            $errors = $this->validateInput(['title' => 'required|max_length[' . self::MAX_TITLE_LENGTH . ']'], $input);
            if ($errors) {
                return $this->respondError('Enter a group name (max ' . self::MAX_TITLE_LENGTH . ' characters)', 422, $errors);
            }

            $userIds = array_values(array_diff($this->postedUserIds(), [$me]));
            if (!$userIds) {
                return $this->respondError('Add at least one member', 422);
            }
            if (count($userIds) > self::MAX_MEMBERS) {
                return $this->respondError('A group can have at most ' . self::MAX_MEMBERS . ' other members', 422);
            }
            if ($unavailable = $this->unavailableUsers($userIds)) {
                return $this->respondError('Some selected users are not available', 422, ['user_ids' => $unavailable]);
            }

            $conversationId = $this->conversationModel->createGroup($me, $input['title'], $userIds);
            return $this->respondConversation($me, $conversationId, 201);
        });
    }

    /** GET conversation_id → members with roles */
    public function members()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getGet('conversation_id');
            if (!$this->findGroupMembership($conversationId, $me)) {
                return $this->respondError('Group not found', 404);
            }
            return $this->respondSuccess($this->membersPayload($conversationId, $me));
        });
    }

    /** POST conversation_id, user_ids → add members (admin only) */
    public function add_members()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getPost('conversation_id');
            if ($error = $this->requireAdmin($conversationId, $me)) {
                return $error;
            }

            $userIds = array_values(array_diff($this->postedUserIds(), [$me]));
            if (!$userIds) {
                return $this->respondError('Choose at least one person to add', 422);
            }
            if ($unavailable = $this->unavailableUsers($userIds)) {
                return $this->respondError('Some selected users are not available', 422, ['user_ids' => $unavailable]);
            }
            $currentCount = count($this->memberModel->activeMembers($conversationId));
            if ($currentCount + count($userIds) > self::MAX_MEMBERS + 1) {
                return $this->respondError('A group can have at most ' . (self::MAX_MEMBERS + 1) . ' members', 422);
            }

            // New members only see messages after this point
            $added = $this->memberModel->addMembers(
                $conversationId,
                $userIds,
                $this->conversationModel->getLastMessageId($conversationId)
            );
            if ($added) {
                $this->messageModel->createSystemMessage($conversationId, $me, 'added', ['targetIds' => $added]);
            }

            return $this->respondSuccess(['added' => $added] + $this->membersPayload($conversationId, $me));
        });
    }

    /** POST conversation_id, user_id → remove a member (admin only) */
    public function remove_member()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getPost('conversation_id');
            $userId = (int) $this->request->getPost('user_id');
            if ($error = $this->requireAdmin($conversationId, $me)) {
                return $error;
            }
            if ($userId === $me) {
                return $this->respondError('Use "Exit group" to leave the group', 422);
            }
            if (!$this->memberModel->findActive($conversationId, $userId)) {
                return $this->respondError('This person is not in the group', 404);
            }

            $this->memberModel->removeMember($conversationId, $userId);
            $this->messageModel->createSystemMessage($conversationId, $me, 'removed', ['targetIds' => [$userId]]);

            return $this->respondSuccess($this->membersPayload($conversationId, $me));
        });
    }

    /** POST conversation_id → leave the group */
    public function leave()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getPost('conversation_id');
            $member = $this->findGroupMembership($conversationId, $me);
            if (!$member) {
                return $this->respondError('Group not found', 404);
            }

            // Keep at least one admin
            if ($member['role'] === 'admin' && $this->memberModel->countAdmins($conversationId) === 1) {
                $successor = $this->memberModel->oldestMemberExcept($conversationId, $me);
                if ($successor !== null) {
                    $this->memberModel->setRole($conversationId, $successor, 'admin');
                }
            }

            // Notice first, while still a member, then leave
            $this->messageModel->createSystemMessage($conversationId, $me, 'left');
            $this->memberModel->removeMember($conversationId, $me);

            return $this->respondSuccess(['conversationId' => $conversationId, 'left' => true]);
        });
    }

    /** POST conversation_id, user_id, is_admin (0|1) → make or dismiss admin (admin only) */
    public function set_admin()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getPost('conversation_id');
            $userId = (int) $this->request->getPost('user_id');
            $makeAdmin = (string) $this->request->getPost('is_admin') === '1';
            if ($error = $this->requireAdmin($conversationId, $me)) {
                return $error;
            }

            $target = $this->memberModel->findActive($conversationId, $userId);
            if (!$target) {
                return $this->respondError('This person is not in the group', 404);
            }
            if (!$makeAdmin && $target['role'] === 'admin' && $this->memberModel->countAdmins($conversationId) <= 1) {
                return $this->respondError('A group needs at least one admin', 422);
            }

            $this->memberModel->setRole($conversationId, $userId, $makeAdmin ? 'admin' : 'member');
            return $this->respondSuccess($this->membersPayload($conversationId, $me));
        });
    }

    /** POST conversation_id, title → rename (admin only) */
    public function rename()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getPost('conversation_id');
            $input = ['title' => trim((string) $this->request->getPost('title'))];
            if ($error = $this->requireAdmin($conversationId, $me)) {
                return $error;
            }
            if ($errors = $this->validateInput(['title' => 'required|max_length[' . self::MAX_TITLE_LENGTH . ']'], $input)) {
                return $this->respondError('Enter a group name (max ' . self::MAX_TITLE_LENGTH . ' characters)', 422, $errors);
            }

            $group = $this->conversationModel->findGroup($conversationId);
            if ($group['title'] !== $input['title']) {
                $this->conversationModel->updateGroup($conversationId, ['title' => $input['title']]);
                $this->messageModel->createSystemMessage($conversationId, $me, 'renamed', ['title' => $input['title']]);
            }

            return $this->respondConversation($me, $conversationId);
        });
    }

    /** POST multipart conversation_id, photo → group photo (admin only) */
    public function photo()
    {
        return $this->guarded(function (int $me) {
            $conversationId = (int) $this->request->getPost('conversation_id');
            if ($error = $this->requireAdmin($conversationId, $me)) {
                return $error;
            }

            $file = $this->request->getFile('photo');
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return $this->respondError('No photo was uploaded', 422);
            }
            if ($file->getSize() > self::PHOTO_MAX_BYTES) {
                return $this->respondError('Photo is too large (max 5 MB)', 413);
            }
            $extension = self::PHOTO_MIMES[$file->getMimeType()] ?? null;
            if ($extension === null || @getimagesize($file->getTempName()) === false) {
                return $this->respondError('Please choose a JPG, PNG or WebP image', 415);
            }

            $absoluteDir = FCPATH . self::PHOTO_DIR;
            if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
                return $this->respondError('Upload folder is not writable', 500);
            }

            $storedName = 'g' . $conversationId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
            $file->move($absoluteDir, $storedName);

            $oldPath = $this->conversationModel->findGroup($conversationId)['avatar_path'] ?? null;
            try {
                $this->conversationModel->updateGroup($conversationId, ['avatar_path' => self::PHOTO_DIR . $storedName]);
            } catch (\Throwable $e) {
                @unlink($absoluteDir . $storedName);
                throw $e;
            }
            if ($oldPath && strpos($oldPath, self::PHOTO_DIR) === 0) {
                @unlink(FCPATH . self::PHOTO_DIR . basename($oldPath));
            }

            $this->messageModel->createSystemMessage($conversationId, $me, 'photo');
            return $this->respondConversation($me, $conversationId);
        });
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Active member row if the conversation is a group the user belongs to. */
    private function findGroupMembership(int $conversationId, int $userId): ?array
    {
        if ($conversationId <= 0 || !$this->conversationModel->findGroup($conversationId)) {
            return null;
        }
        return $this->memberModel->findActive($conversationId, $userId);
    }

    /** Error response unless the user is an admin of the group; null when allowed. */
    private function requireAdmin(int $conversationId, int $userId)
    {
        $member = $this->findGroupMembership($conversationId, $userId);
        if (!$member) {
            return $this->respondError('Group not found', 404);
        }
        if ($member['role'] !== 'admin') {
            return $this->respondError('Only group admins can do this', 403);
        }
        return null;
    }

    /** user_ids from POST: "1,2,3" or user_ids[]=1&user_ids[]=2 */
    private function postedUserIds(): array
    {
        $raw = $this->request->getPost('user_ids');
        $values = is_array($raw) ? $raw : explode(',', (string) $raw);
        $ids = array_filter(array_map('intval', $values), static fn ($id) => $id > 0);
        return array_values(array_unique($ids));
    }

    /** @return int[] ids that don't exist or are deleted in the CRM */
    private function unavailableUsers(array $userIds): array
    {
        $unavailable = [];
        foreach ($this->userProfileModel->getUsers($userIds) as $id => $user) {
            if ($user['isDeleted']) {
                $unavailable[] = $id;
            }
        }
        return $unavailable;
    }

    private function membersPayload(int $conversationId, int $me): array
    {
        $rows = $this->memberModel->activeMembers($conversationId);
        $users = $this->userProfileModel->getUsers(array_column($rows, 'user_id'));

        $members = array_map(static function (array $row) use ($users, $me) {
            $id = (int) $row['user_id'];
            $user = $users[$id] ?? [];
            return [
                'id' => $id,
                'name' => $user['name'] ?? 'Deleted user',
                'avatar' => $user['avatar'] ?? '',
                'headline' => $user['role'] ?? '',
                'isAdmin' => $row['role'] === 'admin',
                'isMe' => $id === $me,
                'isDeleted' => $user['isDeleted'] ?? true,
                'joinedAt' => grid_chat_iso($row['joined_at']),
            ];
        }, $rows);

        // Me first, then admins, then the rest (by join time)
        usort($members, static fn ($a, $b) => [$b['isMe'], $b['isAdmin']] <=> [$a['isMe'], $a['isAdmin']]);

        $mine = array_values(array_filter($members, static fn ($m) => $m['isMe']))[0] ?? null;

        return [
            'conversationId' => $conversationId,
            'members' => $members,
            'memberCount' => count($members),
            'isAdmin' => (bool) ($mine['isAdmin'] ?? false),
        ];
    }
}
