<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

use App\Controllers\Security_Controller;
use App\Models\GRID_CHAT_MODELS\ConversationMemberModel;
use App\Models\GRID_CHAT_MODELS\ConversationModel;
use App\Models\GRID_CHAT_MODELS\MessageAttachmentModel;
use App\Models\GRID_CHAT_MODELS\MessageModel;
use App\Models\GRID_CHAT_MODELS\UserProfileModel;
use App\Models\GRID_CHAT_MODELS\PollVoteModel;

/**
 * Shared base for GRID Chat JSON API controllers:
 * login check, JSON responses (with fresh CSRF hash), validation, membership checks
 * and the message JSON shape used by every chat endpoint.
 */
abstract class GridChatBaseController extends Security_Controller
{
    /** Browser-generated UUID that makes sending safe to retry. */
    protected const CLIENT_ID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

    protected ConversationModel $conversationModel;
    protected ConversationMemberModel $memberModel;
    protected MessageModel $messageModel;
    protected MessageAttachmentModel $attachmentModel;
    protected UserProfileModel $userProfileModel;
    protected PollVoteModel $pollVoteModel;

    public function __construct()
    {
        // API: answer 401 JSON instead of redirecting to the sign-in page
        parent::__construct(false);
        helper(['url', 'grid_chat']);

        $this->conversationModel = new ConversationModel();
        $this->memberModel = new ConversationMemberModel();
        $this->messageModel = new MessageModel();
        $this->attachmentModel = new MessageAttachmentModel();
        $this->userProfileModel = new UserProfileModel();
        $this->pollVoteModel = new PollVoteModel();
    }

    protected function currentUserId(): int
    {
        return (int) ($this->login_user->id ?? 0);
    }

    /**
     * Runs an action for a logged-in user; unexpected errors become a 500 JSON response.
     *
     * @param callable $action fn (int $userId) => Response
     */
    protected function guarded(callable $action)
    {
        $userId = $this->currentUserId();
        if (!$userId) {
            return $this->respondError('Unauthorized', 401);
        }

        try {
            return $action($userId);
        } catch (\Throwable $e) {
            log_message('error', 'GRID_CHAT API error in ' . static::class . ': ' . $e->getMessage());
            return $this->respondError('Something went wrong', 500);
        }
    }

    protected function respondSuccess($data, int $status = 200)
    {
        return $this->response->setStatusCode($status)->setJSON([
            'success' => true,
            'data' => $data,
            'csrf_hash' => csrf_hash(),
        ]);
    }

    protected function respondError(string $message, int $status = 400, array $errors = [])
    {
        $body = [
            'success' => false,
            'message' => $message,
            'csrf_hash' => csrf_hash(),
        ];
        if ($errors) {
            $body['errors'] = $errors;
        }
        return $this->response->setStatusCode($status)->setJSON($body);
    }

    /**
     * CI4 validation; returns field errors, or an empty array when valid.
     */
    protected function validateInput(array $rules, array $data): array
    {
        $validation = \Config\Services::validation(null, false);
        $validation->setRules($rules);

        return $validation->run($data) ? [] : $validation->getErrors();
    }

    /** Active member row of the current user, or null. */
    protected function findMembership(int $conversationId, int $userId): ?array
    {
        return $conversationId > 0 ? $this->memberModel->findActive($conversationId, $userId) : null;
    }

    /** Direct chats can't receive new messages once the other user is deleted in the CRM. */
    protected function isDirectChatWithDeletedUser(int $conversationId, int $me): bool
    {
        if ($this->conversationModel->getType($conversationId) !== 'direct') {
            return false;
        }

        $others = $this->memberModel->othersByConversation([$conversationId], $me)[$conversationId] ?? [];
        foreach ($this->userProfileModel->getUsers(array_column($others, 'user_id')) as $user) {
            if ($user['isDeleted']) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------
    // Chat list JSON shape for the React view
    // ------------------------------------------------------------------

    protected function buildConversationList(int $me, ?array $onlyIds = null): array
    {
        $rows = $this->conversationModel->listForUser($me, $onlyIds);
        if (!$rows) {
            return [];
        }

        $otherMembers = $this->memberModel->othersByConversation(array_column($rows, 'id'), $me);

        $userIds = array_column($rows, 'lm_sender_id');
        foreach ($otherMembers as $members) {
            $userIds = array_merge($userIds, array_column($members, 'user_id'));
        }
        foreach ($rows as $row) {
            if ($row['lm_type'] === 'system' && $row['lm_metadata'] !== null) {
                $userIds = array_merge($userIds, json_decode($row['lm_metadata'], true)['targetIds'] ?? []);
            }
        }
        $users = $this->userProfileModel->getUsers($userIds);

        $pollMessageIds = array_column(
            array_filter($rows, fn(array $r) => ($r['message_type'] ?? '') === 'poll'),
            'id'
        );
        $pollVotesByMessage = [];
        if (!empty($pollMessageIds)) {
            $rawVotes = $this->pollVoteModel->getVotesForMessages($pollMessageIds);
            foreach ($rawVotes as $v) {
                $mId = (int) $v['message_id'];
                $uId = (int) $v['user_id'];
                $opt = (int) $v['option_index'];
                if (!isset($pollVotesByMessage[$mId])) {
                    $pollVotesByMessage[$mId] = ['counts' => [], 'userVote' => null, 'total' => 0];
                }
                $pollVotesByMessage[$mId]['counts'][$opt] = ($pollVotesByMessage[$mId]['counts'][$opt] ?? 0) + 1;
                $pollVotesByMessage[$mId]['total']++;
                if ($uId === $me) {
                    $pollVotesByMessage[$mId]['userVote'] = $opt;
                }
            }
        }

        return array_map(
            fn (array $row) => $this->formatConversation($row, $me, $otherMembers[(int) $row['id']] ?? [], $users),
            $rows
        );
    }

    protected function formatConversation(array $row, int $me, array $others, array $users): array
    {
        $isGroup = $row['type'] === 'group';
        $peer = !$isGroup && $others ? ($users[(int) $others[0]['user_id']] ?? null) : null;

        $lastMessageId = (int) ($row['last_message_id'] ?? 0);
        $visibleAfter = max((int) ($row['cleared_before_message_id'] ?? 0), (int) ($row['joined_after_message_id'] ?? 0));
        $lastMessage = null;
        if ($row['lm_sender_id'] !== null && $lastMessageId > $visibleAfter) {
            $senderId = (int) $row['lm_sender_id'];
            $isSystem = $row['lm_type'] === 'system';
            $isOutgoing = !$isSystem && $senderId === $me;
            $isDeleted = $row['lm_deleted_at'] !== null;

            if ($isSystem) {
                $text = $this->systemMessageText(json_decode((string) $row['lm_metadata'], true) ?: [], $senderId, $me, $users);
            } else {
                $text = $isDeleted ? null : mb_substr((string) $row['lm_body'], 0, 120);
            }

            $lastMessage = [
                'id' => $lastMessageId,
                'senderId' => $senderId,
                'senderName' => $users[$senderId]['name'] ?? '',
                'isOutgoing' => $isOutgoing,
                'messageType' => $row['lm_type'],
                'text' => $text,
                'isDeleted' => $isDeleted,
                'isRead' => $isOutgoing && $lastMessageId <= $this->minReadId($others),
            ];
        }

        return [
            'id' => (int) $row['id'],
            'type' => $row['type'],
            'isGroup' => $isGroup,
            'userId' => $peer['id'] ?? null,
            'name' => $isGroup ? ($row['title'] ?: 'Group') : ($peer['name'] ?? 'Deleted user'),
            'avatar' => $isGroup ? ($row['avatar_path'] ? base_url($row['avatar_path']) : '') : ($peer['avatar'] ?? ''),
            'isDeleted' => $peer['isDeleted'] ?? false,
            'memberCount' => count($others) + 1,
            'myRole' => $row['role'],
            'lastMessage' => $lastMessage,
            'lastMessageAt' => grid_chat_iso($row['last_message_at'] ?? $row['created_at']),
            'unread' => (int) $row['unread_count'],
            'isFavourite' => (bool) $row['is_favourite'],
            'isArchived' => (bool) $row['is_archived'],
            'isMuted' => grid_chat_is_future($row['muted_until']),
            'mutedUntil' => grid_chat_iso($row['muted_until']),
        ];
    }

    /** Current state of one conversation for the chat list. */
    protected function respondConversation(int $me, int $conversationId, int $status = 200)
    {
        return $this->respondSuccess([
            'conversation' => $this->buildConversationList($me, [$conversationId])[0] ?? null,
        ], $status);
    }

    // ------------------------------------------------------------------
    // Message JSON shape for the React view
    // ------------------------------------------------------------------

    protected function formatMessages(array $rows, int $me): array
    {
        if (!$rows) {
            return [];
        }

        $attachments = $this->attachmentModel->byMessageIds(array_column($rows, 'id'));
        $readUpTo = $this->readUpToByConversation(array_column($rows, 'conversation_id'), $me);

        // Quoted messages for the "replying to..." cards — one query for the page
        $quoted = $this->messageModel->previewByIds(array_column($rows, 'reply_to_message_id'));

        $userIds = array_column($rows, 'sender_id');
        foreach ($rows as $row) {
            if ($row['message_type'] === 'system' && $row['metadata'] !== null) {
                $userIds = array_merge($userIds, json_decode($row['metadata'], true)['targetIds'] ?? []);
            }
        }
        // ...so the quote can name whoever wrote the original
        $userIds = array_merge($userIds, array_column($quoted, 'sender_id'));
        $users = $this->userProfileModel->getUsers($userIds);

        $pollMessageIds = array_column(
            array_filter($rows, fn(array $r) => ($r['message_type'] ?? '') === 'poll'),
            'id'
        );
        $pollVotesByMessage = [];
        if (!empty($pollMessageIds)) {
            $rawVotes = $this->pollVoteModel->getVotesForMessages($pollMessageIds);
            foreach ($rawVotes as $v) {
                $mId = (int) $v['message_id'];
                $uId = (int) $v['user_id'];
                $opt = (int) $v['option_index'];
                if (!isset($pollVotesByMessage[$mId])) {
                    $pollVotesByMessage[$mId] = ['counts' => [], 'userVote' => null, 'total' => 0];
                }
                $pollVotesByMessage[$mId]['counts'][$opt] = ($pollVotesByMessage[$mId]['counts'][$opt] ?? 0) + 1;
                $pollVotesByMessage[$mId]['total']++;
                if ($uId === $me) {
                    $pollVotesByMessage[$mId]['userVote'] = $opt;
                }
            }
        }

        return array_map(function (array $row) use ($me, $attachments, $readUpTo, $users, $pollVotesByMessage, $quoted) {
            $id = (int) $row['id'];
            $senderId = (int) $row['sender_id'];
            $conversationId = (int) $row['conversation_id'];
            $isOutgoing = $senderId === $me;
            $isDeleted = $row['deleted_at'] !== null;

            if ($row['message_type'] === 'system') {
                return [
                    'id' => $id,
                    'conversationId' => $conversationId,
                    'clientId' => null,
                    'senderId' => $senderId,
                    'sender' => ['id' => $senderId, 'name' => $users[$senderId]['name'] ?? '', 'avatar' => ''],
                    'type' => 'system',
                    'messageType' => 'system',
                    'text' => $this->systemMessageText(json_decode((string) $row['metadata'], true) ?: [], $senderId, $me, $users),
                    'metadata' => null,
                    'replyToId' => null,
                    'replyTo' => null,
                    'attachments' => [],
                    'isDeleted' => false,
                    'isEdited' => false,
                    'isRead' => false,
                    'createdAt' => grid_chat_iso($row['created_at']),
                    'editedAt' => null,
                ];
            }

            return [
                'id' => $id,
                'conversationId' => $conversationId,
                'clientId' => $row['client_id'],
                'senderId' => $senderId,
                'sender' => [
                    'id' => $senderId,
                    'name' => $users[$senderId]['name'] ?? 'Deleted user',
                    'avatar' => $users[$senderId]['avatar'] ?? '',
                ],
                'type' => $isOutgoing ? 'outgoing' : 'incoming',
                'messageType' => $row['message_type'],
                'text' => $isDeleted ? null : $row['body'],
                'metadata' => (function() use ($row, $isDeleted, $id, $pollVotesByMessage) {
                    if ($isDeleted || $row['metadata'] === null) return null;
                    $meta = json_decode($row['metadata'], true);
                    if ($row['message_type'] === 'poll' && is_array($meta)) {
                        $pv = $pollVotesByMessage[$id] ?? ['counts' => [], 'userVote' => null, 'total' => 0];
                        if (isset($meta['options']) && is_array($meta['options'])) {
                            foreach ($meta['options'] as $idx => &$optItem) {
                                if (is_array($optItem)) {
                                    $optItem['votes'] = $pv['counts'][$idx] ?? 0;
                                }
                            }
                            unset($optItem);
                        }
                        $meta['userVotedIndex'] = $pv['userVote'];
                        $meta['totalVotes'] = $pv['total'];
                    }
                    return $meta;
                })(),
                'replyToId' => $row['reply_to_message_id'] !== null ? (int) $row['reply_to_message_id'] : null,
                // Enough of the quoted message to draw the card without a second request
                'replyTo' => $this->formatQuotedMessage(
                    $quoted[(int) $row['reply_to_message_id']] ?? null,
                    $me,
                    $users
                ),
                'attachments' => $isDeleted ? [] : array_map([$this, 'formatAttachment'], $attachments[$id] ?? []),
                'isDeleted' => $isDeleted,
                'isEdited' => $row['edited_at'] !== null,
                'isRead' => $isOutgoing && $id <= ($readUpTo[$conversationId] ?? 0),
                'createdAt' => grid_chat_iso($row['created_at']),
                'editedAt' => grid_chat_iso($row['edited_at']),
            ];
        }, $rows);
    }

    /**
     * Readable group notice, e.g. "You added Ravi, Kumar". Built at display time from
     * stored user ids, so it always shows current names and says "You" for the viewer.
     *
     * @param array $metadata action, targetIds, title
     * @param array $users user id → user (from UserProfileModel::getUsers)
     */
    protected function systemMessageText(array $metadata, int $actorId, int $me, array $users): string
    {
        $name = static fn (int $id): string => $id === $me ? 'You' : ($users[$id]['name'] ?? 'Someone');
        $actor = $name($actorId);
        $targets = implode(', ', array_map(static fn ($id) => $name((int) $id), $metadata['targetIds'] ?? []));
        $title = (string) ($metadata['title'] ?? '');

        switch ($metadata['action'] ?? '') {
            case 'created':
                return "{$actor} created the group \u{201C}{$title}\u{201D}";
            case 'added':
                return "{$actor} added {$targets}";
            case 'removed':
                return "{$actor} removed {$targets}";
            case 'left':
                return "{$actor} left";
            case 'renamed':
                return "{$actor} changed the group name to \u{201C}{$title}\u{201D}";
            case 'photo':
                return "{$actor} changed the group photo";
            default:
                return '';
        }
    }

    /**
     * The quoted message shown above a reply. `text` is null for media and for
     * deleted originals; the client turns `messageType` into a label such as
     * "📷 Photo" in those cases.
     *
     * @param array|null $quoted row from MessageModel::previewByIds
     */
    protected function formatQuotedMessage(?array $quoted, int $me, array $users): ?array
    {
        if ($quoted === null) {
            return null;
        }

        $senderId = (int) $quoted['sender_id'];
        $isDeleted = $quoted['deleted_at'] !== null;
        $body = (string) ($quoted['body'] ?? '');

        return [
            'id' => (int) $quoted['id'],
            // "You" reads better than your own name on your own quote
            'sender' => $senderId === $me ? 'You' : ($users[$senderId]['name'] ?? 'Deleted user'),
            'senderId' => $senderId,
            'text' => $isDeleted || $body === '' ? null : mb_substr($body, 0, 140),
            'messageType' => $quoted['message_type'],
            'isDeleted' => $isDeleted,
        ];
    }

    protected function formatAttachment(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'fileName' => $row['file_name'],
            'mimeType' => $row['mime_type'],
            'size' => (int) $row['size_bytes'],
            'duration' => $row['duration_sec'] !== null ? (int) $row['duration_sec'] : null,
            'width' => $row['width'] !== null ? (int) $row['width'] : null,
            'height' => $row['height'] !== null ? (int) $row['height'] : null,
            // Served by MediaController::show (members only)
            'url' => site_url('grid-chat/api/media/' . (int) $row['id']),
        ];
    }

    /**
     * Highest message id read by every other member, per conversation (read ticks).
     *
     * @return array<int, int>
     */
    protected function readUpToByConversation(array $conversationIds, int $me): array
    {
        $result = [];
        foreach ($this->memberModel->othersByConversation($conversationIds, $me) as $conversationId => $members) {
            $result[$conversationId] = $this->minReadId($members);
        }
        return $result;
    }

    protected function minReadId(array $members): int
    {
        if (!$members) {
            return 0;
        }
        return min(array_map(static fn ($member) => (int) ($member['last_read_message_id'] ?? 0), $members));
    }

    // ------------------------------------------------------------------
    // Private file delivery — shared by chat attachments and moments.
    // Both keep their files outside the web root (.htaccess "Require all
    // denied") and hand them out only after their own access check.
    // ------------------------------------------------------------------

    /** Shown inline in the browser; everything else downloads. */
    protected const INLINE_MIME_PREFIXES = ['image/', 'video/', 'audio/', 'application/pdf'];

    /**
     * Streams a file in chunks (never loads it fully into memory) with single-range support.
     * Ends the request itself because the body is written directly to the output.
     */
    protected function streamFile(string $path, string $mimeType, string $fileName, bool $forceDownload): void
    {
        $size = filesize($path);
        $start = 0;
        $end = $size - 1;
        $status = 200;

        $range = trim($this->request->getHeaderLine('Range'));
        if ($range !== '') {
            if (!preg_match('/^bytes=(\d*)-(\d*)$/', $range, $matches) || ($matches[1] === '' && $matches[2] === '')) {
                $this->sendRangeNotSatisfiable($size);
            }
            if ($matches[1] === '') {
                $start = max(0, $size - (int) $matches[2]); // last N bytes
            } else {
                $start = (int) $matches[1];
                if ($matches[2] !== '') {
                    $end = min((int) $matches[2], $size - 1);
                }
            }
            if ($start > $end || $start >= $size) {
                $this->sendRangeNotSatisfiable($size);
            }
            $status = 206;
        }

        $inline = !$forceDownload && $this->isInlineMime($mimeType);
        $asciiName = preg_replace('/[^\x20-\x7E]|["\\\\]/', '_', $fileName);

        // Don't hold the session lock while a large file streams (chat sync would wait)
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code($status);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . ($end - $start + 1));
        header('Accept-Ranges: bytes');
        if ($status === 206) {
            header("Content-Range: bytes {$start}-{$end}/{$size}");
        }
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment')
            . '; filename="' . $asciiName . '"; filename*=UTF-8\'\'' . rawurlencode($fileName));
        header('Cache-Control: private, max-age=86400');
        header('X-Content-Type-Options: nosniff');
        header('Content-Security-Policy: sandbox');

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            exit;
        }
        fseek($handle, $start);
        $remaining = $end - $start + 1;
        while ($remaining > 0 && !feof($handle) && !connection_aborted()) {
            $chunk = fread($handle, (int) min(262144, $remaining));
            if ($chunk === false || $chunk === '') {
                break;
            }
            echo $chunk;
            flush();
            $remaining -= strlen($chunk);
        }
        fclose($handle);
        exit;
    }

    protected function sendRangeNotSatisfiable(int $size): void
    {
        http_response_code(416);
        header("Content-Range: bytes */{$size}");
        exit;
    }

    protected function isInlineMime(string $mimeType): bool
    {
        foreach (self::INLINE_MIME_PREFIXES as $prefix) {
            if (strpos($mimeType, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Real path of a stored file, or null when it is missing or escapes its
     * folder. Both arguments are relative to FCPATH.
     */
    protected function resolveStoredFile(string $rootDir, ?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }

        $root = realpath(FCPATH . $rootDir);
        $path = realpath(FCPATH . $relativePath);

        if ($root === false || $path === false || !is_file($path)) {
            return null;
        }
        return strpos($path, $root . DIRECTORY_SEPARATOR) === 0 ? $path : null;
    }
}

