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

    /** Call record shape — the details live in metadata, never only in the body. */
    private const CALL_TYPES = ['audio', 'video'];
    private const CALL_STATUSES = ['completed', 'missed', 'cancelled', 'declined'];
    private const CALL_MAX_SECONDS = 86400;

    /** Ceiling on one delete request, matching the selection UI's practical limit. */
    private const MAX_DELETE_IDS = 100;

    /** Mute options → seconds (-1 = always, 0 = unmute). */
    private const MUTE_DURATIONS = ['8h' => 28800, '1w' => 604800, 'always' => -1, 'off' => 0];
    private const MUTED_ALWAYS = '9999-12-31 23:59:59.000';

    /** GET → chat list */
    public function conversations()
    {
        return $this->guarded(fn (int $me) => $this->respondSuccess([
            'conversations' => $this->buildConversationList($me),
        ]));
    }

    /** POST user_id → find or create the direct chat */
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
            // Opening a chat I deleted brings it back (old messages stay cleared)
            $this->memberModel->restoreForUser($conversationId, $me);

            return $this->respondSuccess([
                'conversation' => $this->buildConversationList($me, [$conversationId])[0] ?? null,
            ]);
        });
    }

    /** GET conversation_id, before_id?, limit? → one page of messages, oldest first */
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

            $limit = (int) ($input['limit'] ?: self::PAGE_SIZE);
            $rows = $this->messageModel->pageForUser($me, $conversationId, (int) $input['before_id'], $limit);

            return $this->respondSuccess([
                'messages' => $this->formatMessages(array_reverse(array_slice($rows, 0, $limit)), $me),
                'hasMore' => count($rows) > $limit,
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
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Conversation not found', 404);
            }
            if ($input['message_type'] === 'text' && $input['body'] === '') {
                return $this->respondError('Message cannot be empty', 422);
            }
            if ($input['client_id'] !== '' && !preg_match(self::CLIENT_ID_PATTERN, $input['client_id'])) {
                return $this->respondError('Invalid client id', 422);
            }
            if ($this->isDirectChatWithDeletedUser($conversationId, $me)) {
                return $this->respondError('This user is no longer available', 403);
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
            } catch (\Throwable $e) {
                // Same client_id saved by a parallel retry
                if ($clientId !== null && ($existing = $this->messageModel->findByClientId($me, $clientId))) {
                    return $this->respondSuccess(['message' => $this->formatMessages([$existing], $me)[0]]);
                }
                throw $e;
            }

            $message = $this->messageModel->findById($messageId);
            return $this->respondSuccess(['message' => $this->formatMessages([$message], $me)[0]], 201);
        });
    }

    /** POST conversation_id, message_id? → mark read up to that message (default: latest) */
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
            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Conversation not found', 404);
            }

            $lastMessageId = $this->conversationModel->getLastMessageId($conversationId);
            $messageId = (int) $input['message_id'];
            $target = $messageId > 0 ? min($messageId, $lastMessageId) : $lastMessageId;

            return $this->respondSuccess(
                ['conversationId' => $conversationId] + $this->memberModel->markRead($conversationId, $me, $target)
            );
        });
    }

    /**
     * GET after_message_id, read_since → everything changed since the cursor.
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

    /** POST conversation_id → clear messages for me only */
    public function clear()
    {
        return $this->guarded(fn (int $me) => $this->clearOrDelete($me, false));
    }

    /** POST conversation_id → delete the chat for me only */
    public function delete_chat()
    {
        return $this->guarded(fn (int $me) => $this->clearOrDelete($me, true));
    }

    /**
     * POST message_ids (comma separated), mode (everyone|me)
     *
     * everyone → soft-deletes your own messages for both sides; the other
     *            person picks it up on their next sync
     * me       → hides them for you only, leaving them on the other side
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

            return $this->respondSuccess(['mode' => $mode, 'messageIds' => $deleted]);
        });
    }

    /** GET q?, offset? → active users to start a chat with */
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
     * Only a completed call carries a duration — a missed, cancelled or
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

        $duration = $status === 'completed'
            ? max(0, min(self::CALL_MAX_SECONDS, (int) ($data['duration'] ?? 0)))
            : 0;

        // The caller's own view; each side's bubble derives what it shows from
        // whether the message is theirs, so this is only the stored record.
        $direction = ($data['direction'] ?? '') === 'incoming' ? 'incoming' : 'outgoing';

        return [[
            'call_type' => $callType,
            'direction' => $direction,
            'status' => $status,
            'duration' => $duration,
            'started_at' => grid_chat_from_iso($data['started_at'] ?? null) !== null ? $data['started_at'] : null,
            'ended_at' => grid_chat_from_iso($data['ended_at'] ?? null) !== null ? $data['ended_at'] : null,
        ], null];
    }

    /** POST message_id, option_index — records vote in grid_chat_db.poll_votes */
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

            // Save vote to database table poll_votes
            $this->pollVoteModel->recordVote($messageId, $me, $optionIndex);

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
}
