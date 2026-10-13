<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

/**
 * GRID Chat media: upload photos, videos, audio, voice notes and documents,
 * and serve them only to members of the conversation.
 *
 * Files live under www/files/grid_chat/media/<conversation_id>/<yyyy>/<mm>/ (direct URL
 * access is blocked by .htaccess); the database stores only the relative path.
 */
class MediaController extends GridChatBaseController
{
    private const MEDIA_DIR = 'files/grid_chat/media/';
    private const MB = 1048576;
    private const MAX_CAPTION_LENGTH = 4000;

    /**
     * Allowed types per message type: detected MIME type → stored extension, plus size limit.
     * The extension always comes from the detected type, never from the uploaded file name.
     */
    private const TYPE_RULES = [
        'image' => [
            'max' => 10 * self::MB,
            'mimes' => [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ],
        ],
        'video' => [
            'max' => 100 * self::MB,
            'mimes' => [
                'video/mp4' => 'mp4',
                'video/webm' => 'webm',
                'video/quicktime' => 'mov',
                'video/3gpp' => '3gp',
                'video/x-matroska' => 'mkv',
            ],
        ],
        'audio' => [
            'max' => 25 * self::MB,
            'mimes' => [
                'audio/mpeg' => 'mp3',
                'audio/mp4' => 'm4a',
                'audio/x-m4a' => 'm4a',
                'audio/aac' => 'aac',
                'audio/ogg' => 'ogg',
                'application/ogg' => 'ogg',
                'audio/wav' => 'wav',
                'audio/x-wav' => 'wav',
                'audio/flac' => 'flac',
                'audio/x-flac' => 'flac',
                'audio/webm' => 'weba',
                'video/webm' => 'webm',
            ],
        ],
        // Browser recordings: Chrome/Firefox record WebM/Ogg, Safari records MP4
        'voice' => [
            'max' => 25 * self::MB,
            'mimes' => [
                'audio/webm' => 'weba',
                'video/webm' => 'webm',
                'audio/ogg' => 'ogg',
                'application/ogg' => 'ogg',
                'audio/mp4' => 'm4a',
                'video/mp4' => 'm4a',
                'audio/mpeg' => 'mp3',
                'audio/wav' => 'wav',
                'audio/x-wav' => 'wav',
            ],
        ],
        'file' => [
            'max' => 100 * self::MB,
            'mimes' => [
                'application/pdf' => 'pdf',
                'text/plain' => 'txt',
                'text/csv' => 'csv',
                'application/csv' => 'csv',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/vnd.ms-excel' => 'xls',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'application/vnd.ms-powerpoint' => 'ppt',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                'application/vnd.oasis.opendocument.text' => 'odt',
                'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
                'application/vnd.oasis.opendocument.presentation' => 'odp',
                'application/zip' => 'zip',
                'application/x-zip-compressed' => 'zip',
                'application/x-rar' => 'rar',
                'application/vnd.rar' => 'rar',
                'application/x-7z-compressed' => '7z',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'video/mp4' => 'mp4',
                'audio/mpeg' => 'mp3',
            ],
        ],
    ];

    /** Plain-text and legacy Office files: keep a safe original extension (fileinfo can't tell them apart). */
    private const KEEP_ORIGINAL_EXTENSION = [
        'text/plain' => ['txt', 'csv', 'log', 'md'],
        'application/CDFV2' => ['doc', 'xls', 'ppt'],
    ];

    // Streaming itself (INLINE_MIME_PREFIXES, streamFile, isInlineMime) lives in
    // GridChatBaseController, shared with MomentController.

    /**
     * POST multipart: conversation_id, client_id, message_type (image|video|audio|voice|file),
     * body (caption), duration (seconds, audio/video/voice), file.
     */
    public function upload()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'conversation_id' => $this->request->getPost('conversation_id'),
                'client_id' => strtolower(trim((string) $this->request->getPost('client_id'))),
                'message_type' => trim((string) $this->request->getPost('message_type')),
                'body' => trim((string) $this->request->getPost('body')),
                'duration' => $this->request->getPost('duration'),
            ];
            $errors = $this->validateInput([
                'conversation_id' => 'required|is_natural_no_zero',
                'message_type' => 'required|in_list[' . implode(',', array_keys(self::TYPE_RULES)) . ']',
                'body' => 'permit_empty|max_length[' . self::MAX_CAPTION_LENGTH . ']',
                'duration' => 'permit_empty|is_natural|less_than_equal_to[86400]',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid upload', 422, $errors);
            }

            $conversationId = (int) $input['conversation_id'];
            $type = $input['message_type'];

            if (!$this->findMembership($conversationId, $me)) {
                return $this->respondError('Conversation not found', 404);
            }
            if ($input['client_id'] !== '' && !preg_match(self::CLIENT_ID_PATTERN, $input['client_id'])) {
                return $this->respondError('Invalid client id', 422);
            }
            if ($this->isDirectChatWithDeletedUser($conversationId, $me)) {
                return $this->respondError('This user is no longer available', 403);
            }

            // Direct conversation block enforcement
            $conversationRow = $this->conversationModel->find($conversationId);
            if ($conversationRow && ($conversationRow['type'] ?? '') === 'direct') {
                $otherMember = $this->memberModel->where('conversation_id', $conversationId)
                    ->where('user_id !=', $me)
                    ->first();
                $peerId = $otherMember ? (int) $otherMember['user_id'] : 0;
                if ($peerId > 0) {
                    if ($this->userBlockModel->hasBlocked($peerId, $me)) {
                        return $this->respondError('You cannot send messages to this contact.', 403);
                    }
                    if ($this->userBlockModel->hasBlocked($me, $peerId)) {
                        return $this->respondError('You have blocked this contact. Unblock to send a message.', 403);
                    }
                }
            }

            $clientId = $input['client_id'] !== '' ? $input['client_id'] : null;

            // Retry of an upload that was already saved
            if ($clientId !== null && ($existing = $this->messageModel->findByClientId($me, $clientId))) {
                return $this->respondSuccess(['message' => $this->formatMessages([$existing], $me)[0]]);
            }

            $file = $this->request->getFile('file');
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                $tooLarge = $file && in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
                return $tooLarge
                    ? $this->respondError('File is too large', 413)
                    : $this->respondError('No file was uploaded', 422);
            }

            $rules = self::TYPE_RULES[$type];
            if ($file->getSize() > $rules['max']) {
                return $this->respondError('File is too large (max ' . ($rules['max'] / self::MB) . ' MB)', 413);
            }

            $mimeType = $file->getMimeType();
            $extension = $this->resolveExtension($type, $mimeType, $file->getClientName());
            if ($extension === null) {
                return $this->respondError('This file type is not allowed', 415);
            }

            $width = null;
            $height = null;
            if ($type === 'image') {
                $size = @getimagesize($file->getTempName());
                if ($size === false) {
                    return $this->respondError('The image could not be read', 422);
                }
                [$width, $height] = $size;
            }

            $relativeDir = self::MEDIA_DIR . $conversationId . '/' . gmdate('Y/m') . '/';
            $absoluteDir = FCPATH . $relativeDir;
            if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
                log_message('error', 'GRID_CHAT media folder not writable: ' . $absoluteDir);
                return $this->respondError('Upload folder is not writable', 500);
            }

            $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
            $fileSize = (int) $file->getSize();
            $file->move($absoluteDir, $storedName);
            $storedPath = $absoluteDir . $storedName;

            try {
                $messageId = $this->messageModel->createAndDeliver([
                    'conversation_id' => $conversationId,
                    'sender_id' => $me,
                    'client_id' => $clientId,
                    'message_type' => $type,
                    'body' => $input['body'] !== '' ? $input['body'] : null,
                ], [
                    'file_path' => $relativeDir . $storedName,
                    'file_name' => $this->displayFileName($file->getClientName(), $extension),
                    'mime_type' => $mimeType,
                    'size_bytes' => $fileSize,
                    'duration_sec' => in_array($type, ['video', 'audio', 'voice'], true) && $input['duration'] !== ''
                        ? (int) $input['duration']
                        : null,
                    'width' => $width,
                    'height' => $height,
                ]);
            } catch (\Throwable $e) {
                // Don't leave orphan files when the message could not be saved
                if (is_file($storedPath)) {
                    @unlink($storedPath);
                }
                if ($clientId !== null && ($existing = $this->messageModel->findByClientId($me, $clientId))) {
                    return $this->respondSuccess(['message' => $this->formatMessages([$existing], $me)[0]]);
                }
                throw $e;
            }

            $message = $this->messageModel->findById($messageId);
            return $this->respondSuccess(['message' => $this->formatMessages([$message], $me)[0]], 201);
        });
    }

    /**
     * GET media/{attachmentId}: streams the file to a member of the conversation.
     * Supports HTTP Range so videos and audio can seek. Add ?download=1 to force a download.
     */
    public function show($attachmentId = 0)
    {
        $me = $this->currentUserId();
        if (!$me) {
            return $this->respondError('Unauthorized', 401);
        }

        $attachment = $this->attachmentModel->findVisibleForUser((int) $attachmentId, $me);
        if (!$attachment) {
            return $this->respondError('File not found', 404);
        }

        $path = $this->resolveStoredFile(self::MEDIA_DIR, $attachment['file_path']);
        if ($path === null) {
            return $this->respondError('File not found', 404);
        }

        $forceDownload = $this->request->getGet('download') === '1';
        $this->streamFile($path, $attachment['mime_type'], $attachment['file_name'], $forceDownload);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function resolveExtension(string $type, string $mimeType, string $clientName): ?string
    {
        $originalExtension = strtolower(pathinfo($clientName, PATHINFO_EXTENSION));

        if ($type === 'file' && isset(self::KEEP_ORIGINAL_EXTENSION[$mimeType])) {
            return in_array($originalExtension, self::KEEP_ORIGINAL_EXTENSION[$mimeType], true)
                ? $originalExtension
                : ($mimeType === 'text/plain' ? 'txt' : null);
        }

        return self::TYPE_RULES[$type]['mimes'][$mimeType] ?? null;
    }

    /** Original name for display only: no path, no control characters, safe extension. */
    private function displayFileName(string $clientName, string $extension): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F]+/u', '', basename(str_replace('\\', '/', $clientName)));
        $name = trim((string) $name);
        if ($name === '') {
            $name = 'file.' . $extension;
        }
        return mb_substr($name, 0, 255);
    }

}
