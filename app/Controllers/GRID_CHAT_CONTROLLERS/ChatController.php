<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

/**
 * GRID Chat conversation API (grid_chat_db).
 * Validates requests, checks membership, calls models and shapes JSON for the React view.
 */
class ChatController extends GridChatBaseController
{
    private const PAGE_SIZE = 50;
    private const SYNC_LIMIT = 200;
    private const CONTACTS_PAGE_SIZE = 50;
    private const MAX_METADATA_BYTES = 20000;

    /** Message types sent without a file (media types go through MediaController::upload). */
    private const NON_FILE_TYPES = ['text', 'location', 'contact', 'poll', 'event', 'call'];

    /** Call record shape â€” the details live in metadata, never only in the body. */
    private const CALL_TYPES = ['audio', 'video'];
    // "ongoing" = group call announcement; the app shows it live or ended from WebSocket heartbeats
    private const CALL_STATUSES = ['completed', 'missed', 'cancelled', 'declined', 'ongoing'];
    private const CALL_MAX_SECONDS = 86400;

    /** Ceiling on one delete request, matching the selection UI's practical limit. */
    private const MAX_DELETE_IDS = 100;

    /** WhatsApp limits forwarding to 5 chats at a time. */
    private const MAX_FORWARD_CHATS = 5;
    private const MAX_FORWARD_MESSAGES = 30;

    private const CALL_HISTORY_LIMIT = 100;

    /** Mute options â†’ seconds (-1 = always, 0 = unmute). */
    private const MUTE_DURATIONS = ['8h' => 28800, '1w' => 604800, 'always' => -1, 'off' => 0];
    private const MUTED_ALWAYS = '9999-12-31 23:59:59.000';

    private function chatLockPayload(int $userId): array
    {
        $ids = $this->chatLockModel->lockedConversationIds($userId);
        return [
            'configured' => $this->chatLockModel->hasSecret($userId),
            'lockedCount' => count($ids),
            'lockedConversationIds' => $ids,
        ];
    }

    /** GET â†’ chat list */
    public function conversations()
    {
        return $this->guarded(fn (int $me) => $this->respondSuccess([
            'conversations' => $this->buildConversationList($me),
            'blocked_users' => $this->userBlockModel->getBlockedUserIds($me),
            'blocked_by' => $this->userBlockModel->getBlockedByUserIds($me),
            'chat_lock' => $this->chatLockPayload($me),
        ]));
    }

    /** POST user_id â†’ find or create the direct chat */
    public function open_direct()
    {
        return $this->guarded(function (int $me) {
            $input = ['user_id' => $this->request->getPost('user_id')];
            if ($errors = $this->validateInput(['user_id' => 'required|is_natural_no_zero'], $input)) {
                return $this->respondError('Choose a user to chat with', 422, $errors);
            }

            $otherUserId = (int) $input['user_id'];
            if ($otherUserId === $me) {
                return $this->respondError('You cannot start a chat with yourself', 422);
            }
            if ($this->userProfileModel->getUser($otherUserId)['isDeleted']) {
                return $this->respondError('This user is not available', 404);
            }

            $conversation = $this->conversationModel->findOrCreateDirect($me, $otherUserId);
            $conversationId = (int) $conversation['id'];
            if (!$this->hasChatLockAccess($conversationId, $me)) {
                return $this->respondError('Unlock this chat from Locked chats first', 423);
            }
            // Opening a chat I deleted brings it back (old messages stay cleared)
            $this->memberModel->restoreForUser($conversationId, $me);

            return $this->respondSuccess([
                'conversation' => $this->buildConversationList($me, [$conversationId])[0] ?? null,
            ]);
        });
    }

    /** GET conversation_id, before_id?, limit? â†’ one page of messages, oldest first */
    public function messages()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'conversation_id' => $this->request->getGet('conversation_id'),
                'before_id' => $this->request->getGet('before_id'),
                'limit' => $this->request->getGet('limit'),
            ];
            $errors = $this->validateInput([
                'conversation_id' => 'required|is_natural_no_zero',
                'before_id' => 'permit_empty|is_natural',
                'limit' => 'permit_empty|is_natural_no_zero|less_than_equal_to[100]',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            $conversationId = (int) $input['conversation_id'];
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Conversation not found', 404);
            }
            if (!$this->hasChatLockAccess($conversationId, $me)) {
                return $this->respondError('Unlock this chat first', 423);
            }

            $limit = (int) ($input['limit'] ?: self::PAGE_SIZE);
            $rows = $this->messageModel->pageForUser($me, $conversationId, (int) $input['before_id'], $limit);
            $hasMore = count($rows) > $limit;
            // The extra row only says whether older messages exist; it is not shown
            $rows = array_slice($rows, 0, $limit);

            // Opening the chat delivers anything not fetched before (grey âœ”âœ” for the senders)
            $this->notifyReceipts($this->receiptModel->markDelivered($me, $rows), $me);

            return $this->respondSuccess([
                'messages' => $this->formatMessages(array_reverse($rows), $me),
                'hasMore' => $hasMore,
            ]);
        });
    }

    /** POST conversation_id, client_id, message_type, body, metadata, reply_to_message_id */
    public function send()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'conversation_id' => $this->request->getPost('conversation_id'),
                'client_id' => strtolower(trim((string) $this->request->getPost('client_id'))),
                'message_type' => trim((string) $this->request->getPost('message_type')) ?: 'text',
                'body' => trim((string) $this->request->getPost('body')),
                'metadata' => $this->request->getPost('metadata'),
                'reply_to_message_id' => $this->request->getPost('reply_to_message_id'),
            ];
            $errors = $this->validateInput([
                'conversation_id' => 'required|is_natural_no_zero',
                'message_type' => 'required|in_list[' . implode(',', self::NON_FILE_TYPES) . ']',
                'body' => 'permit_empty|max_length[10000]',
                'reply_to_message_id' => 'permit_empty|is_natural_no_zero',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid message', 422, $errors);
            }

            $conversationId = (int) $input['conversation_id'];

            if ($input['message_type'] === 'text' && $input['body'] === '') {
                return $this->respondError('Message cannot be empty', 422);
            }
            if ($input['client_id'] !== '' && !preg_match(self::CLIENT_ID_PATTERN, $input['client_id'])) {
                return $this->respondError('Invalid client id', 422);
            }

            [$sendError, $hiddenForUserId] = $this->sendPermission($conversationId, $me);
            if ($sendError !== null) {
                return $this->respondError($sendError[0], $sendError[1]);
            }

            [$metadata, $metadataError] = $this->parseMetadata($input['message_type'], $input['metadata']);
            if ($metadataError !== null) {
                return $this->respondError($metadataError, 422);
            }

            $replyTo = (int) $input['reply_to_message_id'];
            if ($replyTo > 0 && !$this->messageModel->existsInConversation($replyTo, $conversationId)) {
                return $this->respondError('The message you replied to was not found', 422);
            }

            $clientId = $input['client_id'] !== '' ? $input['client_id'] : null;

            // Retry of a message that was already saved
            if ($clientId !== null && ($existing = $this->messageModel->findByClientId($me, $clientId))) {
                return $this->respondSuccess(['message' => $this->formatMessages([$existing], $me)[0]]);
            }

            try {
                $messageId = $this->messageModel->createAndDeliver([
                    'conversation_id' => $conversationId,
                    'sender_id' => $me,
                    'client_id' => $clientId,
                    'message_type' => $input['message_type'],
                    'body' => $input['body'] !== '' ? $input['body'] : null,
                    'metadata' => $metadata,
                    'reply_to_message_id' => $replyTo > 0 ? $replyTo : null,
                ]);

                $this->hideFromBlocker($conversationId, $messageId, $hiddenForUserId);
            } catch (\Throwable $e) {
                // Same client_id saved by a parallel retry
                if ($clientId !== null && ($existing = $this->messageModel->findByClientId($me, $clientId))) {
                    return $this->respondSuccess(['message' => $this->formatMessages([$existing], $me)[0]]);
                }
                throw $e;
            }

            $this->pushMobileNotification($me, $conversationId, (string) $input['message_type'], (string) $input['body'], $metadata);
            $this->notifyNewMessage($conversationId, $messageId, $me, $hiddenForUserId);

            $message = $this->messageModel->findById($messageId);
            return $this->respondSuccess(['message' => $this->formatMessages([$message], $me)[0]], 201);
        });
    }

    /**
     * POST call_id, call_type, callee_user_id or conversation_id.
     * Sends the high-priority INCOMING_CALL payload to the other person's device tokens.
     */
    public function call_invite()
    {
        return $this->guarded(function (int $me) {
            $callId = trim((string) $this->request->getPost('call_id'));
            $callType = strtolower(trim((string) $this->request->getPost('call_type')));
            $calleeId = (int) $this->request->getPost('callee_user_id');
            $conversationId = (int) $this->request->getPost('conversation_id');

            if (!$this->isCallId($callId)) {
                return $this->respondError('Invalid call id', 422);
            }
            if (!in_array($callType, self::CALL_TYPES, true)) {
                return $this->respondError('A call needs a type of audio or video', 422);
            }
            if ($calleeId <= 0 && $conversationId > 0) {
                if (!$this->findMembership($conversationId, $me)) {
                    return $this->respondError('Conversation not found', 404);
                }
                $others = $this->memberModel->othersByConversation([$conversationId], $me)[$conversationId] ?? [];
                $calleeId = (int) ($others[0]['user_id'] ?? 0);
            }
            if ($calleeId <= 0 || $calleeId === $me) {
                return $this->respondError('Callee not found', 422);
            }

            try {
                (new \App\Libraries\DevicePushService())->sendIncomingCall($me, $calleeId, $callId, $callType);
            } catch (\Throwable $e) {
                log_message('error', 'Call invite push: ' . $e->getMessage());
            }

            return $this->respondSuccess(['call_id' => $callId, 'callee_user_id' => $calleeId]);
        });
    }

    /** POST conversation_id, message_id? â†’ mark read up to that message (default: latest) */
    public function read()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'conversation_id' => $this->request->getPost('conversation_id'),
                'message_id' => $this->request->getPost('message_id'),
            ];
            $errors = $this->validateInput([
                'conversation_id' => 'required|is_natural_no_zero',
                'message_id' => 'permit_empty|is_natural',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            $conversationId = (int) $input['conversation_id'];
            $membership = $this->findMembership($conversationId, $me);
            if (!$membership) {
                return $this->respondError('Conversation not found', 404);
            }

            // Never mark read past the latest message actually visible to $me
            $lastVisibleId = $this->messageModel->lastVisibleIdForUser($conversationId, $me);

            $messageId = (int) $input['message_id'];
            $target = $messageId > 0 ? min($messageId, $lastVisibleId) : $lastVisibleId;

            // Per-message read times for Message info, from where I had read up to before
            $readFrom = max((int) ($membership['last_read_message_id'] ?? 0), (int) ($membership['joined_after_message_id'] ?? 0));
            $state = $this->memberModel->markRead($conversationId, $me, $target);
            $senders = $this->receiptModel->markRead($me, $conversationId, $readFrom, $target);
            if ($senders) {
                $this->notifyReceipts([$conversationId => $senders], $me);
            }

            return $this->respondSuccess(['conversationId' => $conversationId] + $state);
        });
    }

    /**
     * GET after_message_id, read_since â†’ everything changed since the cursor.
     * First call (after_message_id omitted) returns the chat list and a starting cursor;
     * after that the browser always sends the cursor it got back (it may be 0).
     */
    public function sync()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'after_message_id' => $this->request->getGet('after_message_id'),
                'read_since' => (string) $this->request->getGet('read_since'),
            ];
            if ($errors = $this->validateInput(['after_message_id' => 'permit_empty|is_natural'], $input)) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            $serverTime = grid_chat_now();
            $isInitial = $input['after_message_id'] === null || $input['after_message_id'] === '';
            $afterMessageId = (int) $input['after_message_id'];

            if ($isInitial) {
                return $this->respondSuccess([
                    'messages' => [],
                    'updatedMessages' => [],
                    'reads' => [],
                    'conversations' => $this->buildConversationList($me),
                    'chat_lock' => $this->chatLockPayload($me),
                    'hasMore' => false,
                    'cursor' => [
                        'afterMessageId' => $this->conversationModel->maxLastMessageIdForUser($me),
                        'readSince' => grid_chat_iso($serverTime),
                    ],
                ]);
            }

            $newRows = $this->messageModel->newForUserSince($me, $afterMessageId, self::SYNC_LIMIT);
            $hasMore = count($newRows) > self::SYNC_LIMIT;
            $newRows = array_slice($newRows, 0, self::SYNC_LIMIT);

            // These reached my device: grey âœ”âœ” for their senders
            $this->notifyReceipts($this->receiptModel->markDelivered($me, $newRows), $me);

            $updatedRows = [];
            $reads = [];
            $readSince = grid_chat_from_iso($input['read_since']);
            if ($readSince !== null) {
                $updatedRows = $this->messageModel->changedForUserSince($me, $afterMessageId, $readSince, self::SYNC_LIMIT);

                $readConversationIds = $this->memberModel->conversationIdsReadSince($me, $readSince);
                foreach ($this->readUpToByConversation($readConversationIds, $me) as $conversationId => $readUpTo) {
                    $reads[] = ['conversationId' => $conversationId, 'readUpToMessageId' => $readUpTo];
                }
            }

            $changed = $newRows || $updatedRows || $reads;

            return $this->respondSuccess([
                'messages' => $this->formatMessages($newRows, $me),
                'updatedMessages' => $this->formatMessages($updatedRows, $me),
                'reads' => $reads,
                'conversations' => $changed ? $this->buildConversationList($me) : [],
                'chat_lock' => $this->chatLockPayload($me),
                'hasMore' => $hasMore,
                'cursor' => [
                    'afterMessageId' => $newRows ? (int) end($newRows)['id'] : $afterMessageId,
                    // Keep the old read cursor until all new messages are fetched
                    'readSince' => $hasMore && $readSince !== null ? $input['read_since'] : grid_chat_iso($serverTime),
                ],
            ]);
        });
    }

    /** POST conversation_id, is_favourite (0|1) */
    public function favourite()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'conversation_id' => $this->request->getPost('conversation_id'),
                'is_favourite' => $this->request->getPost('is_favourite'),
            ];
            $errors = $this->validateInput([
                'conversation_id' => 'required|is_natural_no_zero',
                'is_favourite' => 'required|in_list[0,1]',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            $conversationId = (int) $input['conversation_id'];
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Conversation not found', 404);
            }

            $this->memberModel->setFavourite($conversationId, $me, $input['is_favourite'] === '1');
            return $this->respondConversation($me, $conversationId);
        });
    }

    /** POST conversation_id, duration (8h|1w|always|off) */
    public function mute()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'conversation_id' => $this->request->getPost('conversation_id'),
                'duration' => $this->request->getPost('duration'),
            ];
            $errors = $this->validateInput([
                'conversation_id' => 'required|is_natural_no_zero',
                'duration' => 'required|in_list[' . implode(',', array_keys(self::MUTE_DURATIONS)) . ']',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            $conversationId = (int) $input['conversation_id'];
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Conversation not found', 404);
            }

            $seconds = self::MUTE_DURATIONS[$input['duration']];
            $mutedUntil = null;
            if ($seconds === -1) {
                $mutedUntil = self::MUTED_ALWAYS;
            } elseif ($seconds > 0) {
                $mutedUntil = gmdate('Y-m-d H:i:s', time() + $seconds) . '.000';
            }

            $this->memberModel->setMutedUntil($conversationId, $me, $mutedUntil);
            return $this->respondConversation($me, $conversationId);
        });
    }

    /** POST conversation_id â†’ clear messages for me only */
    public function clear()
    {
        return $this->guarded(fn (int $me) => $this->clearOrDelete($me, false));
    }

    /** POST conversation_id â†’ delete the chat for me only */
    public function delete_chat()
    {
        return $this->guarded(fn (int $me) => $this->clearOrDelete($me, true));
    }

    /**
     * POST message_ids (comma separated), mode (everyone|me)
     *
     * everyone â†’ soft-deletes your own messages for both sides; the other
     *            person picks it up on their next sync
     * me       â†’ hides them for you only, leaving them on the other side
     */
    public function delete_message()
    {
        return $this->guarded(function (int $me) {
            $mode = $this->request->getPost('mode') === 'everyone' ? 'everyone' : 'me';

            $ids = array_slice(array_values(array_unique(array_filter(
                array_map('intval', explode(',', (string) $this->request->getPost('message_ids'))),
                static fn (int $id) => $id > 0
            ))), 0, self::MAX_DELETE_IDS);

            if (!$ids) {
                return $this->respondError('No messages to delete', 422);
            }

            $deleted = $mode === 'everyone'
                ? $this->messageModel->deleteForEveryone($ids, $me)
                : $this->messageModel->hideForUser($ids, $me);

            if (!$deleted) {
                return $this->respondError(
                    $mode === 'everyone'
                        ? 'You can only delete your own messages for everyone'
                        : 'Those messages are no longer available',
                    403
                );
            }

            // Everyone else's copy changes too: one real-time hint per conversation
            if ($mode === 'everyone') {
                $lastByConversation = array_column($this->messageModel->visibleForUserByIds($me, $deleted), 'id', 'conversation_id');
                foreach ($lastByConversation as $conversationId => $messageId) {
                    $this->notifyMessageChanged((int) $conversationId, (int) $messageId, $me);
                }
            }

            return $this->respondSuccess(['mode' => $mode, 'messageIds' => $deleted]);
        });
    }

    /** GET q?, offset? â†’ active users to start a chat with */
    public function contacts()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'q' => trim((string) $this->request->getGet('q')),
                'offset' => $this->request->getGet('offset'),
            ];
            $errors = $this->validateInput([
                'q' => 'permit_empty|max_length[100]',
                'offset' => 'permit_empty|is_natural',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            return $this->respondSuccess(
                $this->userProfileModel->searchContacts($me, $input['q'], self::CONTACTS_PAGE_SIZE, (int) $input['offset'])
            );
        });
    }

    // ------------------------------------------------------------------
    // Request helpers
    // ------------------------------------------------------------------

    private function clearOrDelete(int $me, bool $delete)
    {
        $input = ['conversation_id' => $this->request->getPost('conversation_id')];
        if ($errors = $this->validateInput(['conversation_id' => 'required|is_natural_no_zero'], $input)) {
            return $this->respondError('Invalid request', 422, $errors);
        }

        $conversationId = (int) $input['conversation_id'];
        if (!$this->findMembership($conversationId, $me)) {
            return $this->respondError('Conversation not found', 404);
        }

        $lastMessageId = $this->conversationModel->getLastMessageId($conversationId);
        if ($delete) {
            $this->memberModel->deleteForUser($conversationId, $me, $lastMessageId);
            return $this->respondSuccess(['conversationId' => $conversationId, 'deleted' => true]);
        }

        $this->memberModel->clearForUser($conversationId, $me, $lastMessageId);
        return $this->respondConversation($me, $conversationId);
    }

    /**
     * @return array{0: ?string, 1: ?string} [metadata JSON, error message]
     */
    private function parseMetadata(string $type, $raw): array
    {
        if ($raw === null || $raw === '') {
            return $type === 'text' ? [null, null] : [null, 'Missing details for this message'];
        }

        $raw = (string) $raw;
        if (strlen($raw) > self::MAX_METADATA_BYTES) {
            return [null, 'Message details are too large'];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return [null, 'Message details are not valid'];
        }

        if ($type === 'poll') {
            $options = $data['options'] ?? null;
            if (trim((string) ($data['question'] ?? '')) === '' || !is_array($options) || count($options) < 2 || count($options) > 12) {
                return [null, 'A poll needs a question and 2-12 options'];
            }
        }

        if ($type === 'call') {
            [$data, $error] = $this->normaliseCallMetadata($data);
            if ($error !== null) {
                return [null, $error];
            }
        }

        return [json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), null];
    }

    /**
     * Keeps a call record to the agreed shape, so the bubble can trust it:
     * call_type, direction, status, duration, started_at, ended_at.
     *
     * Only a completed call carries a duration â€” a missed, cancelled or
     * declined call is forced to zero rather than trusting the client.
     *
     * @return array{0: array|null, 1: string|null} [normalised, error]
     */
    private function normaliseCallMetadata(array $data): array
    {
        $callType = strtolower(trim((string) ($data['call_type'] ?? '')));
        if (!in_array($callType, self::CALL_TYPES, true)) {
            return [null, 'A call record needs a call type of audio or video'];
        }

        $status = strtolower(trim((string) ($data['status'] ?? '')));
        if (!in_array($status, self::CALL_STATUSES, true)) {
            return [null, 'A call record needs a status of completed, missed, cancelled or declined'];
        }

        $isGroup = !empty($data['is_group']);
        $callId = trim((string) ($data['call_id'] ?? ''));
        if ($callId !== '' && !$this->isCallId($callId)) {
            return [null, 'Invalid call id'];
        }
        if ($status === 'ongoing' && (!$isGroup || $callId === '')) {
            return [null, 'Only a group call can be announced as ongoing'];
        }

        $duration = $status === 'completed'
            ? max(0, min(self::CALL_MAX_SECONDS, (int) ($data['duration'] ?? 0)))
            : 0;

        // The caller's own view; each side's bubble derives what it shows from
        // whether the message is theirs, so this is only the stored record.
        $direction = ($data['direction'] ?? '') === 'incoming' ? 'incoming' : 'outgoing';

        $record = [
            'call_type' => $callType,
            'direction' => $direction,
            'status' => $status,
            'duration' => $duration,
            'started_at' => grid_chat_from_iso($data['started_at'] ?? null) !== null ? $data['started_at'] : null,
            'ended_at' => grid_chat_from_iso($data['ended_at'] ?? null) !== null ? $data['ended_at'] : null,
        ];
        if ($isGroup) {
            $record['is_group'] = true;
        }
        if ($callId !== '' || $isGroup) {
            $record['call_id'] = $callId !== '' ? $callId : null;
        }

        return [$record, null];
    }

    /** UUID from the web app, or the mobile id shape such as CALL_1042_9921. */
    private function isCallId(string $callId): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/', $callId);
    }

    /**
     * Data-only FCM: NEW_MESSAGE for chat, CALL_HANDLED or MISSED_CALL for a finished call.
     */
    private function pushMobileNotification(int $senderId, int $conversationId, string $messageType, string $body, $metadata): void
    {
        try {
            $recipients = [];
            foreach ($this->memberModel->activeMembers($conversationId) as $member) {
                $memberId = (int) ($member['user_id'] ?? 0);
                if ($memberId > 0 && $memberId !== $senderId) {
                    $recipients[] = $memberId;
                }
            }
            if (!$recipients) {
                return;
            }

            $push = new \App\Libraries\DevicePushService();
            if ($messageType === 'call') {
                $data = is_string($metadata) ? json_decode($metadata, true) : null;
                $status = is_array($data) ? strtolower((string) ($data['status'] ?? '')) : '';
                $callId = is_array($data) ? trim((string) ($data['call_id'] ?? '')) : '';
                $type = $status === 'missed' ? 'CALL_MISSED' : 'CALL_CANCELLED';
                if ($status === 'completed' || $status === 'ongoing' || $callId === '') {
                    return;
                }
                foreach ($recipients as $recipientId) {
                    $push->sendCallSignal($recipientId, $callId, $type);
                }
                return;
            }

            $push->sendChatMessage($senderId, $recipients, $body);
        } catch (\Throwable $e) {
            log_message('error', 'Chat push: ' . $e->getMessage());
        }
    }

    /** POST message_id, option_index â€” records vote in grid_chat_db.poll_votes */
    public function vote_poll()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'message_id' => $this->request->getPost('message_id'),
                'option_index' => $this->request->getPost('option_index'),
            ];

            $errors = $this->validateInput([
                'message_id' => 'required|is_natural_no_zero',
                'option_index' => 'required|is_natural',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid request', 422, $errors);
            }

            $messageId = (int) $input['message_id'];
            $optionIndex = (int) $input['option_index'];

            $message = $this->messageModel->findById($messageId);
            if (!$message || $message['deleted_at'] !== null) {
                return $this->respondError('Poll message not found', 404);
            }

            if ($message['message_type'] !== 'poll') {
                return $this->respondError('Message is not a poll', 422);
            }

            $conversationId = (int) $message['conversation_id'];
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('You are not a member of this conversation', 403);
            }

            $metadata = json_decode((string) $message['metadata'], true);
            $options = $metadata['options'] ?? [];
            if (!is_array($options) || $optionIndex < 0 || $optionIndex >= count($options)) {
                return $this->respondError('Invalid poll option', 422);
            }

            // Save vote to database table poll_votes; the other members' sync picks up the new counts
            $this->pollVoteModel->recordVote($messageId, $me, $optionIndex);
            $this->notifyMessageChanged($conversationId, $messageId, $me);

            // Fetch updated summary
            $summary = $this->pollVoteModel->getVoteSummary($messageId, $me);

            foreach ($options as $idx => &$opt) {
                if (is_array($opt)) {
                    $opt['votes'] = $summary['counts'][$idx] ?? 0;
                }
            }
            unset($opt);

            $updatedPollData = [
                'question' => $metadata['question'] ?? '',
                'options' => $options,
                'userVotedIndex' => $summary['userVote'],
                'totalVotes' => $summary['totalVotes'],
            ];

            return $this->respondSuccess([
                'messageId' => $messageId,
                'conversationId' => $conversationId,
                'pollData' => $updatedPollData,
            ]);
        });
    }

    /**
     * POST message_ids, conversation_ids â†’ copy messages (with their files) into up to
     * 5 chats, like WhatsApp. Each copy is marked forwarded; forwardCount grows each hop
     * so the bubble can say "Forwarded many times".
     */
    public function forward()
    {
        return $this->guarded(function (int $me) {
            $messageIds = $this->postedIds('message_ids');
            $targetIds = $this->postedIds('conversation_ids');
            if (!$messageIds || count($messageIds) > self::MAX_FORWARD_MESSAGES) {
                return $this->respondError('Choose 1-' . self::MAX_FORWARD_MESSAGES . ' messages to forward', 422);
            }
            if (!$targetIds || count($targetIds) > self::MAX_FORWARD_CHATS) {
                return $this->respondError('Choose 1-' . self::MAX_FORWARD_CHATS . ' chats to forward to', 422);
            }

            // Only messages I can see, never deleted ones, group notices or call records
            $sources = array_values(array_filter(
                $this->messageModel->visibleForUserByIds($me, $messageIds),
                static fn (array $m) => $m['deleted_at'] === null && !in_array($m['message_type'], ['system', 'call'], true)
            ));
            if (!$sources) {
                return $this->respondError('These messages cannot be forwarded', 422);
            }
            $attachments = $this->attachmentModel->byMessageIds(array_column($sources, 'id'));

            // Check every target first so nothing is half-sent
            $targets = [];
            foreach ($targetIds as $conversationId) {
                [$error, $hiddenFor] = $this->sendPermission($conversationId, $me);
                if ($error !== null) {
                    return $this->respondError($error[0], $error[1], ['conversation_id' => $conversationId]);
                }
                $targets[$conversationId] = $hiddenFor;
            }

            $sent = [];
            foreach ($targets as $conversationId => $hiddenForUserId) {
                foreach ($sources as $source) {
                    $metadata = $source['metadata'] !== null ? (json_decode($source['metadata'], true) ?: []) : [];
                    // Votes and status-reply context belong to the original, not the copy
                    unset($metadata['userVotedIndex'], $metadata['totalVotes'], $metadata['statusReply']);
                    $metadata['forwarded'] = true;
                    $metadata['forwardCount'] = (int) ($metadata['forwardCount'] ?? 0) + 1;

                    $file = $attachments[(int) $source['id']][0] ?? null;
                    $messageId = $this->messageModel->createAndDeliver([
                        'conversation_id' => $conversationId,
                        'sender_id' => $me,
                        'client_id' => null,
                        'message_type' => $source['message_type'],
                        'body' => $source['body'],
                        'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'reply_to_message_id' => null,
                    ], $file ? [
                        // Same stored file: messages are only ever soft-deleted, so it stays available
                        'file_path' => $file['file_path'],
                        'file_name' => $file['file_name'],
                        'mime_type' => $file['mime_type'],
                        'size_bytes' => $file['size_bytes'],
                        'duration_sec' => $file['duration_sec'],
                        'width' => $file['width'],
                        'height' => $file['height'],
                    ] : null);

                    $this->hideFromBlocker($conversationId, $messageId, $hiddenForUserId);
                    $this->notifyNewMessage($conversationId, $messageId, $me, $hiddenForUserId);
                    $sent[] = $messageId;
                }
            }

            $rows = array_filter(array_map([$this->messageModel, 'findById'], $sent));
            return $this->respondSuccess([
                'messages' => $this->formatMessages(array_values($rows), $me),
                'conversationIds' => array_keys($targets),
            ], 201);
        });
    }

    /**
     * GET message_id â†’ Message info for one of MY messages: sent time and, per recipient,
     * when it was delivered and read. Only the sender may see this.
     */
    public function message_info()
    {
        return $this->guarded(function (int $me) {
            $messageId = (int) $this->request->getGet('message_id');
            $message = $messageId > 0 ? $this->messageModel->findById($messageId) : null;
            if (!$message || (int) $message['sender_id'] !== $me || $message['message_type'] === 'system') {
                return $this->respondError('Message not found', 404);
            }
            $conversationId = (int) $message['conversation_id'];
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Message not found', 404);
            }

            $isBlockedDrop = in_array($messageId, $this->messageModel->hiddenIdsIn([$messageId]), true);
            $receipts = [];
            foreach ($this->receiptModel->byMessageIds([$messageId])[$messageId] ?? [] as $row) {
                $receipts[(int) $row['user_id']] = $row;
            }

            // Everyone who could see it: current members who joined before it, plus anyone with a receipt
            $members = [];
            foreach ($this->memberModel->othersByConversation([$conversationId], $me)[$conversationId] ?? [] as $member) {
                if ((int) ($member['joined_after_message_id'] ?? 0) < $messageId) {
                    $members[(int) $member['user_id']] = $member;
                }
            }
            $userIds = array_values(array_unique(array_merge(array_keys($members), array_keys($receipts))));
            $users = $this->userProfileModel->getUsers($userIds);

            $recipients = [];
            foreach ($userIds as $userId) {
                $receipt = $receipts[$userId] ?? null;
                $member = $members[$userId] ?? null;
                $readAt = $receipt['read_at'] ?? null;
                // Older messages (before receipts existed) only have the member's read pointer
                $readByPointer = $readAt === null && $member && (int) ($member['last_read_message_id'] ?? 0) >= $messageId;
                if ($readByPointer) {
                    $readAt = $member['last_read_at'] ?? null;
                }
                $isRead = $readAt !== null || $readByPointer;

                $recipients[] = [
                    'id' => $userId,
                    'name' => $users[$userId]['name'] ?? 'Deleted user',
                    'avatar' => $users[$userId]['avatar'] ?? '',
                    'deliveredAt' => $isBlockedDrop ? null : grid_chat_iso($receipt['delivered_at'] ?? ($isRead ? $readAt : null)),
                    'readAt' => $isBlockedDrop ? null : grid_chat_iso($readAt),
                    'isRead' => !$isBlockedDrop && $isRead,
                    'isDelivered' => !$isBlockedDrop && ($receipt !== null || $isRead),
                ];
            }

            return $this->respondSuccess([
                'message' => $this->formatMessages([$message], $me)[0],
                'sentAt' => grid_chat_iso($message['created_at']),
                'isGroup' => (bool) $this->conversationModel->findGroup($conversationId),
                'recipients' => $recipients,
            ]);
        });
    }

    /**
     * GET â†’ Calls history, built from the call records stored in chats: type, direction
     * (from my side), missed, start time and duration, plus who/which group.
     */
    public function calls()
    {
        return $this->guarded(function (int $me) {
            $rows = $this->messageModel->callsForUser($me, self::CALL_HISTORY_LIMIT);
            $conversations = [];
            foreach ($this->buildConversationList($me, array_values(array_unique(array_map('intval', array_column($rows, 'conversation_id'))))) as $conversation) {
                $conversations[(int) $conversation['id']] = $conversation;
            }

            $calls = [];
            foreach ($rows as $row) {
                $conversation = $conversations[(int) $row['conversation_id']] ?? null;
                if (!$conversation) {
                    continue;
                }
                $meta = json_decode((string) $row['metadata'], true) ?: [];
                $isOutgoing = (int) $row['sender_id'] === $me;
                $status = (string) ($meta['status'] ?? 'completed');
                $isGroup = (bool) ($conversation['isGroup'] ?? false);

                $calls[] = [
                    'id' => (int) $row['id'],
                    'conversationId' => (int) $row['conversation_id'],
                    'isGroup' => $isGroup,
                    'userId' => $conversation['userId'] ?? null,
                    'grid_id' => $conversation['grid_id'] ?? null,
                    'name' => $conversation['name'] ?? '',
                    'avatar' => $conversation['avatar'] ?? '',
                    'callType' => ($meta['call_type'] ?? 'audio') === 'video' ? 'video' : 'audio',
                    'direction' => $isOutgoing ? 'outgoing' : 'incoming',
                    'status' => $status,
                    // A call to me that never connected (unanswered, or the caller gave up)
                    'isMissed' => !$isOutgoing && in_array($status, ['missed', 'cancelled'], true),
                    'duration' => (int) ($meta['duration'] ?? 0),
                    'startedAt' => $meta['started_at'] ?? grid_chat_iso($row['created_at']),
                    'createdAt' => grid_chat_iso($row['created_at']),
                ];
            }

            return $this->respondSuccess(['calls' => $calls]);
        });
    }

    /**
     * POST message_ids (comma separated, call records) or all=1 → remove calls from
     * MY call history only; the other side keeps theirs. Records nobody can see any
     * more are deleted from the database (MessageModel::deleteCallsForUser).
     */
    public function delete_calls()
    {
        return $this->guarded(function (int $me) {
            $ids = null;
            if ($this->request->getPost('all') !== '1') {
                $ids = array_slice(array_values(array_unique(array_filter(
                    array_map('intval', explode(',', (string) $this->request->getPost('message_ids'))),
                    static fn (int $id) => $id > 0
                ))), 0, self::MAX_DELETE_IDS);

                if (!$ids) {
                    return $this->respondError('No calls to delete', 422);
                }
            }

            $result = $this->messageModel->deleteCallsForUser($me, $ids);
            if ($ids !== null && !$result['removed']) {
                return $this->respondError('Those calls are no longer in your history', 403);
            }

            return $this->respondSuccess(['messageIds' => $result['removed'], 'purged' => $result['purged']]);
        });
    }

    /** "1,2,3" or ids[]=1&ids[]=2 from POST, positive and unique */
    private function postedIds(string $field): array
    {
        $raw = $this->request->getPost($field);
        $values = is_array($raw) ? $raw : explode(',', (string) $raw);
        return array_values(array_unique(array_filter(array_map('intval', $values), static fn ($id) => $id > 0)));
    }

    /**
     * POST message_id, emoji â†’ react (one per user per message, like WhatsApp):
     * the same emoji again removes it, another emoji replaces it. Returns the message's chips.
     */
    public function react()
    {
        return $this->guarded(function (int $me) {
            $messageId = (int) $this->request->getPost('message_id');
            $emoji = trim((string) $this->request->getPost('emoji'));

            // An emoji is a few code points (ZWJ sequences, skin tones, flags); never text or markup
            if ($messageId <= 0 || $emoji === '' || mb_strlen($emoji) > 16 || strlen($emoji) > 32
                || preg_match('/[\p{L}\s<>&"\']/u', $emoji) || preg_match('/^[\x00-\x7F]+$/', $emoji)) {
                return $this->respondError('Choose an emoji to react with', 422);
            }

            $message = $this->messageModel->findById($messageId);
            if (!$message || $message['deleted_at'] !== null || $message['message_type'] === 'system') {
                return $this->respondError('Message not found', 404);
            }
            $conversationId = (int) $message['conversation_id'];
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Message not found', 404);
            }

            $current = $this->reactionModel->reactionOf($messageId, $me);
            $this->reactionModel->setReaction($messageId, $me, $current === $emoji ? null : $emoji);
            $this->notifyMessageChanged($conversationId, $messageId, $me);

            $formatted = $this->formatMessages([$message], $me)[0];
            return $this->respondSuccess([
                'messageId' => $messageId,
                'conversationId' => $conversationId,
                'reactions' => $formatted['reactions'] ?? [],
            ]);
        });
    }

    /** POST user_id â€” block a user */
    public function block()
    {
        return $this->guarded(function (int $me) {
            $input = ['user_id' => $this->request->getPost('user_id')];
            if ($errors = $this->validateInput(['user_id' => 'required|is_natural_no_zero'], $input)) {
                return $this->respondError('Choose a user to block', 422, $errors);
            }

            $otherUserId = (int) $input['user_id'];
            if ($otherUserId === $me) {
                return $this->respondError('You cannot block yourself', 422);
            }

            // Blocker is User A ($me), blocked is User B ($otherUserId)
            $this->userBlockModel->blockUser($me, $otherUserId);

            return $this->respondSuccess([
                'blocker_id' => $me,
                'blocked_user_id' => $otherUserId,
                'is_blocked' => true,
                'blocked_users' => $this->userBlockModel->getBlockedUserIds($me),
            ]);
        });
    }

    /** POST user_id â€” unblock a user */
    public function unblock()
    {
        return $this->guarded(function (int $me) {
            $input = ['user_id' => $this->request->getPost('user_id')];
            if ($errors = $this->validateInput(['user_id' => 'required|is_natural_no_zero'], $input)) {
                return $this->respondError('Choose a user to unblock', 422, $errors);
            }

            $otherUserId = (int) $input['user_id'];
            $this->userBlockModel->unblockUser($me, $otherUserId);

            return $this->respondSuccess([
                'blocker_id' => $me,
                'blocked_user_id' => $otherUserId,
                'is_blocked' => false,
                'blocked_users' => $this->userBlockModel->getBlockedUserIds($me),
            ]);
        });
    }

    /** GET â€” get blocked users for current user */
    public function blocked_users()
    {
        return $this->guarded(function (int $me) {
            return $this->respondSuccess([
                'blocked_users' => $this->userBlockModel->getBlockedUserIds($me),
                'blocked_by' => $this->userBlockModel->getBlockedByUserIds($me),
            ]);
        });
    }
    /** POST message_id, body â†’ edit one of my text messages; members get it through sync */
    public function edit_message()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'message_id' => $this->request->getPost('message_id'),
                'body' => trim((string) $this->request->getPost('body')),
            ];
            $errors = $this->validateInput([
                'message_id' => 'required|is_natural_no_zero',
                'body' => 'required|max_length[10000]',
            ], $input);
            if ($errors) {
                return $this->respondError('Message cannot be empty', 422, $errors);
            }

            $messageId = (int) $input['message_id'];
            $message = $this->messageModel->findById($messageId);
            // Only while I'm still in the chat, like sending
            if (!$message || !$this->findMembership((int) $message['conversation_id'], $me)) {
                return $this->respondError('Message not found', 404);
            }

            if (!$this->messageModel->editMessage($messageId, $me, $input['body'])) {
                return $this->respondError('You can only edit your own text messages', 403);
            }

            $conversationId = (int) $message['conversation_id'];
            $this->notifyMessageChanged($conversationId, $messageId, $me);

            return $this->respondSuccess([
                'message' => $this->formatMessages([$this->messageModel->findById($messageId)], $me)[0],
            ]);
        });
    }

    /** POST custom_name, contact_grid_id?, contact_user_id?, phone? -> save/update custom contact nickname */
    public function save_contact()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'custom_name' => trim((string) $this->request->getPost('custom_name')),
                'contact_grid_id' => trim((string) $this->request->getPost('contact_grid_id')),
                'contact_user_id' => $this->request->getPost('contact_user_id'),
                'phone' => trim((string) $this->request->getPost('phone')),
            ];

            if ($input['custom_name'] === '') {
                return $this->respondError('Contact name cannot be empty', 422);
            }

            if ($input['contact_grid_id'] === '' && empty($input['contact_user_id']) && $input['phone'] === '') {
                return $this->respondError('Contact identifier (Grid ID, user ID or phone) is required', 422);
            }

            $success = $this->userContactModel->saveContact($me, $input);
            if (!$success) {
                return $this->respondError('Failed to save contact name', 500);
            }

            return $this->respondSuccess([
                'success' => true,
                'contact' => [
                    'custom_name' => $input['custom_name'],
                    'contact_grid_id' => $input['contact_grid_id'],
                    'contact_user_id' => $input['contact_user_id'] ? (int) $input['contact_user_id'] : null,
                    'phone' => $input['phone'],
                ],
                'custom_contacts' => $this->userContactModel->getCustomContactsList($me),
            ]);
        });
    }

    /** GET -> get all custom contacts saved by current user */
    public function custom_contacts()
    {
        return $this->guarded(function (int $me) {
            return $this->respondSuccess([
                'custom_contacts' => $this->userContactModel->getCustomContactsList($me),
                'custom_contacts_map' => $this->userContactModel->getCustomContactsMap($me),
            ]);
        });
    }
}