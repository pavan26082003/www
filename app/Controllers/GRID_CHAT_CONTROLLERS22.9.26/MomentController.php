<?php

namespace App\Controllers\GRID_CHAT_CONTROLLERS;

use App\Models\GRID_CHAT_MODELS\MomentLikeModel;
use App\Models\GRID_CHAT_MODELS\MomentModel;
use App\Models\GRID_CHAT_MODELS\MomentPrivacyModel;
use App\Models\GRID_CHAT_MODELS\MomentViewModel;

/**
 * Moments (Status/Stories): 24-hour status updates in grid_chat_db.
 *
 * Status privacy is applied when a moment is POSTED, not when it is read: the
 * poster's current "Who can see my statuses?" answer is copied onto the moment,
 * so changing the setting later leaves already-published moments alone.
 *
 * Media never has a public URL. Files sit under files/grid_chat/moments behind
 * an .htaccess deny and are streamed by media() only to people in the audience.
 */
class MomentController extends GridChatBaseController
{
    /** Private folder (under FCPATH) for moment photos and videos. */
    private const MOMENTS_DIR = 'files/grid_chat/moments/';

    private const MB = 1048576;
    private const LIFETIME_HOURS = 24;
    private const MAX_TEXT_LENGTH = 700;
    private const MAX_CAPTION_LENGTH = 200;

    /** Same caps and detection-based extensions as chat attachments. */
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
    ];

    /**
     * Backgrounds a text status may use. The browser sends raw CSS, and it ends
     * up in a style attribute, so only these exact values are stored.
     * Keep in step with GRADIENTS in src/components/Moments/CreateStoryModal.jsx.
     */
    private const TEXT_BACKGROUNDS = [
        'linear-gradient(135deg, #00a884, #059669)',
        'linear-gradient(135deg, #ec4899, #f43f5e)',
        'linear-gradient(135deg, #f97316, #ea580c)',
        'linear-gradient(135deg, #10b981, #059669)',
        'linear-gradient(135deg, #f59e0b, #ea580c)',
        'linear-gradient(135deg, #1e293b, #0f172a)',
    ];

    /** Guards against someone pasting a whole directory into the picker. */
    private const MAX_PRIVACY_TARGETS = 256;

    protected MomentModel $momentModel;
    protected MomentViewModel $momentViewModel;
    protected MomentLikeModel $momentLikeModel;
    protected MomentPrivacyModel $privacyModel;

    public function __construct()
    {
        parent::__construct();
        $this->momentModel = new MomentModel();
        $this->momentViewModel = new MomentViewModel();
        $this->momentLikeModel = new MomentLikeModel();
        $this->privacyModel = new MomentPrivacyModel();
    }

    // ------------------------------------------------------------------
    // Reading
    // ------------------------------------------------------------------

    /**
     * GET moments: every unexpired status the current user may see, grouped by
     * poster with "My Status" first.
     */
    public function index()
    {
        return $this->guarded(function (int $me) {
            $this->purgeExpired();

            $rows = $this->momentModel->activeVisibleTo($me);
            $viewedIds = array_flip($this->momentViewModel->viewedMomentIds($me));

            $ownerIds = array_map('intval', array_column($rows, 'user_id'));
            $users = $this->userProfileModel->getUsers(array_merge($ownerIds, [$me]));
            $canDownload = $this->privacyModel->downloadAllowedFor($ownerIds, $me);

            $momentIds = array_map('intval', array_column($rows, 'id'));
            $likeCounts = $this->momentLikeModel->countsForMoments($momentIds);
            $likedByMe = array_flip($this->momentLikeModel->likedByUser($momentIds, $me));
            // View totals are the poster's business only
            $viewCounts = $this->momentViewModel->countsForMoments(array_column(
                array_filter($rows, static fn (array $r) => (int) $r['user_id'] === $me),
                'id'
            ));

            $grouped = [];
            foreach ($rows as $row) {
                $ownerId = (int) $row['user_id'];
                $isMine = $ownerId === $me;

                // A status from someone removed in the CRM is not shown to others
                if (!$isMine && ($users[$ownerId]['isDeleted'] ?? true)) {
                    continue;
                }

                if (!isset($grouped[$ownerId])) {
                    $grouped[$ownerId] = [
                        'id' => $isMine ? 'user' : 'story-user-' . $ownerId,
                        'contactId' => $ownerId,
                        'userName' => $isMine ? 'My Status' : $users[$ownerId]['name'],
                        // The real name behind "My Status", so the UI can fall back to initials
                        'displayName' => $users[$ownerId]['name'],
                        'userAvatar' => $users[$ownerId]['avatar'] ?? '',
                        'isUser' => $isMine,
                        'viewed' => true,
                        'canDownload' => $isMine || ($canDownload[$ownerId] ?? true),
                        'updatedAt' => null,
                        'stories' => [],
                    ];
                }

                $momentId = (int) $row['id'];
                $isViewed = $isMine || isset($viewedIds[$momentId]);
                if (!$isViewed) {
                    $grouped[$ownerId]['viewed'] = false;
                }

                $createdAt = grid_chat_iso($row['created_at']);
                $grouped[$ownerId]['updatedAt'] = $createdAt; // rows are oldest first, so this ends on the newest
                $grouped[$ownerId]['stories'][] = [
                    'id' => 'story-' . $momentId,
                    'realId' => $momentId,
                    'type' => $row['type'],
                    'media' => $row['media_path'] ? site_url('grid-chat/api/moments/media/' . $momentId) : null,
                    'text' => $row['text_content'],
                    'bg' => $row['text_bg'],
                    'caption' => $row['caption'],
                    'viewed' => $isViewed,
                    'isMine' => $isMine,
                    'likeCount' => $likeCounts[$momentId] ?? 0,
                    'likedByMe' => isset($likedByMe[$momentId]),
                    // Only your own statuses carry a view count
                    'viewCount' => $isMine ? ($viewCounts[$momentId] ?? 0) : null,
                    'createdAt' => $createdAt,
                    'expiresAt' => grid_chat_iso($row['expires_at']),
                ];
            }

            return $this->respondSuccess(['moments' => $this->myStatusFirst($grouped, $me, $users)]);
        });
    }

    /**
     * GET moments/media/{id}: streams a moment's photo or video, but only to
     * someone in that moment's audience. Add ?download=1 to force a download,
     * which is refused when the poster restricts downloads.
     */
    public function media($momentId = 0)
    {
        $me = $this->currentUserId();
        if (!$me) {
            return $this->respondError('Unauthorized', 401);
        }

        $moment = $this->momentModel->findVisibleForUser((int) $momentId, $me);
        if (!$moment || empty($moment['media_path'])) {
            return $this->respondError('File not found', 404);
        }

        $path = $this->resolveStoredFile(self::MOMENTS_DIR, $moment['media_path']);
        if ($path === null) {
            return $this->respondError('File not found', 404);
        }

        $ownerId = (int) $moment['user_id'];
        $forceDownload = $this->request->getGet('download') === '1'
            && ($ownerId === $me || ($this->privacyModel->downloadAllowedFor([$ownerId], $me)[$ownerId] ?? true));

        $extension = strtolower(pathinfo($moment['media_path'], PATHINFO_EXTENSION));
        $mimeType = $moment['mime_type'] ?: 'application/octet-stream';

        $this->streamFile($path, $mimeType, 'moment-' . (int) $moment['id'] . '.' . $extension, $forceDownload);
    }

    // ------------------------------------------------------------------
    // Posting
    // ------------------------------------------------------------------

    /** POST moments/text: text, bg, caption. */
    public function createText()
    {
        return $this->guarded(function (int $me) {
            $input = [
                'text' => trim((string) $this->request->getPost('text')),
                'caption' => trim((string) $this->request->getPost('caption')),
                'bg' => (string) $this->request->getPost('bg'),
            ];

            $errors = $this->validateInput([
                'text' => 'required|max_length[' . self::MAX_TEXT_LENGTH . ']',
                'caption' => 'permit_empty|max_length[' . self::MAX_CAPTION_LENGTH . ']',
            ], $input);
            if ($errors) {
                return $this->respondError('Invalid status', 422, $errors);
            }

            $background = in_array($input['bg'], self::TEXT_BACKGROUNDS, true)
                ? $input['bg']
                : self::TEXT_BACKGROUNDS[0];

            $momentId = $this->saveMoment($me, [
                'type' => 'text',
                'text_content' => $input['text'],
                'text_bg' => $background,
                'caption' => $input['caption'] !== '' ? $input['caption'] : null,
            ]);

            return $this->respondSuccess(['momentId' => $momentId], 201);
        });
    }

    /** POST moments/media: multipart file (image or video) plus an optional caption. */
    public function createMedia()
    {
        return $this->guarded(function (int $me) {
            $caption = trim((string) $this->request->getPost('caption'));
            $errors = $this->validateInput(
                ['caption' => 'permit_empty|max_length[' . self::MAX_CAPTION_LENGTH . ']'],
                ['caption' => $caption]
            );
            if ($errors) {
                return $this->respondError('Invalid status', 422, $errors);
            }

            $file = $this->request->getFile('file');
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                $tooLarge = $file && in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
                return $tooLarge
                    ? $this->respondError('File is too large', 413)
                    : $this->respondError('No file was uploaded', 422);
            }

            $mimeType = $file->getMimeType();
            $type = isset(self::TYPE_RULES['image']['mimes'][$mimeType]) ? 'image'
                : (isset(self::TYPE_RULES['video']['mimes'][$mimeType]) ? 'video' : null);
            if ($type === null) {
                return $this->respondError('Only photos and videos can be posted as a status', 415);
            }

            $rules = self::TYPE_RULES[$type];
            if ($file->getSize() > $rules['max']) {
                return $this->respondError('File is too large (max ' . ($rules['max'] / self::MB) . ' MB)', 413);
            }
            if ($type === 'image' && @getimagesize($file->getTempName()) === false) {
                return $this->respondError('The image could not be read', 422);
            }

            $relativeDir = self::MOMENTS_DIR . $me . '/' . gmdate('Y/m') . '/';
            $absoluteDir = FCPATH . $relativeDir;
            if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
                log_message('error', 'GRID_CHAT moments folder not writable: ' . $absoluteDir);
                return $this->respondError('Upload folder is not writable', 500);
            }

            $storedName = bin2hex(random_bytes(16)) . '.' . $rules['mimes'][$mimeType];
            $fileSize = (int) $file->getSize();
            $file->move($absoluteDir, $storedName);
            $storedPath = $absoluteDir . $storedName;

            try {
                $momentId = $this->saveMoment($me, [
                    'type' => $type,
                    'media_path' => $relativeDir . $storedName,
                    'mime_type' => $mimeType,
                    'size_bytes' => $fileSize,
                    'caption' => $caption !== '' ? $caption : null,
                ]);
            } catch (\Throwable $e) {
                // Don't leave an orphan file when the row could not be saved
                if (is_file($storedPath)) {
                    @unlink($storedPath);
                }
                throw $e;
            }

            return $this->respondSuccess(['momentId' => $momentId], 201);
        });
    }

    // ------------------------------------------------------------------
    // Viewing and deleting
    // ------------------------------------------------------------------

    /** POST moments/view: moment_id. Recorded only for a status you may see. */
    public function view()
    {
        return $this->guarded(function (int $me) {
            $momentId = (int) $this->request->getPost('moment_id');
            if ($momentId <= 0) {
                return $this->respondError('Moment id is required', 422);
            }

            $moment = $this->momentModel->findVisibleForUser($momentId, $me);
            if (!$moment) {
                return $this->respondError('Status not found', 404);
            }

            // Your own status is never "seen by you"
            if ((int) $moment['user_id'] !== $me) {
                $this->momentViewModel->markAsViewed($momentId, $me);
            }

            return $this->respondSuccess(['momentId' => $momentId, 'viewed' => true]);
        });
    }

    /**
     * POST moments/like: moment_id. Adds or removes your heart on someone
     * else's status and answers with the state afterwards.
     */
    public function like()
    {
        return $this->guarded(function (int $me) {
            $momentId = (int) $this->request->getPost('moment_id');
            if ($momentId <= 0) {
                return $this->respondError('Moment id is required', 422);
            }

            $moment = $this->momentModel->findVisibleForUser($momentId, $me);
            if (!$moment) {
                return $this->respondError('Status not found', 404);
            }
            if ((int) $moment['user_id'] === $me) {
                return $this->respondError('You cannot like your own status', 422);
            }

            $liked = $this->momentLikeModel->toggle($momentId, $me);

            return $this->respondSuccess([
                'momentId' => $momentId,
                'liked' => $liked,
                'likeCount' => $this->momentLikeModel->countsForMoments([$momentId])[$momentId] ?? 0,
            ]);
        });
    }

    /**
     * GET moments/viewers?moment_id=: who watched one of YOUR statuses, newest
     * first, each flagged with whether they liked it.
     */
    public function viewers()
    {
        return $this->guarded(function (int $me) {
            $momentId = (int) $this->request->getGet('moment_id');
            if ($momentId <= 0) {
                return $this->respondError('Moment id is required', 422);
            }

            // Deliberately owner-only: nobody may see who watched someone else's status
            $moment = $this->momentModel->where('id', $momentId)->where('user_id', $me)->first();
            if (!$moment) {
                return $this->respondError('Status not found', 404);
            }

            $rows = $this->momentViewModel->viewersForMoment($momentId);
            $likerIds = array_flip($this->momentLikeModel->likerIds($momentId));
            $users = $this->userProfileModel->getUsers(array_column($rows, 'viewer_id'));

            $viewers = [];
            foreach ($rows as $row) {
                $viewerId = (int) $row['viewer_id'];
                $user = $users[$viewerId] ?? null;
                if (!$user || $user['isDeleted']) {
                    continue;
                }
                $viewers[] = [
                    'id' => $viewerId,
                    'name' => $user['name'],
                    'avatar' => $user['avatar'],
                    'liked' => isset($likerIds[$viewerId]),
                    'viewedAt' => grid_chat_iso($row['viewed_at']),
                ];
            }

            return $this->respondSuccess([
                'momentId' => $momentId,
                'viewers' => $viewers,
                'likeCount' => count($likerIds),
            ]);
        });
    }

    /** POST moments/delete: moment_id. Only the poster can delete. */
    public function deleteMoment()
    {
        return $this->guarded(function (int $me) {
            $momentId = (int) $this->request->getPost('moment_id');
            if ($momentId <= 0) {
                return $this->respondError('Moment id is required', 422);
            }

            $deleted = $this->momentModel->deleteOwned($momentId, $me);
            if (!$deleted) {
                return $this->respondError('Status not found', 404);
            }

            $this->unlinkMedia($deleted['media_path'] ?? null);

            return $this->respondSuccess(['momentId' => $momentId, 'deleted' => true]);
        });
    }

    // ------------------------------------------------------------------
    // Status privacy
    // ------------------------------------------------------------------

    /** GET moments/privacy: both settings with the people currently picked. */
    public function privacy()
    {
        return $this->guarded(function (int $me) {
            return $this->respondSuccess(['privacy' => $this->privacyPayload($me)]);
        });
    }

    /**
     * POST moments/privacy: setting (share|download), audience
     * (contacts|contacts_except|only) and user_ids as a comma-separated list.
     */
    public function savePrivacy()
    {
        return $this->guarded(function (int $me) {
            $setting = (string) $this->request->getPost('setting');
            $audience = (string) $this->request->getPost('audience');

            if (!in_array($setting, MomentPrivacyModel::SETTINGS, true)) {
                return $this->respondError('Unknown setting', 422);
            }
            if (!in_array($audience, MomentPrivacyModel::AUDIENCES, true)) {
                return $this->respondError('Unknown audience', 422);
            }

            $targetIds = $this->parseUserIds((string) $this->request->getPost('user_ids'), $me);
            if (count($targetIds) > self::MAX_PRIVACY_TARGETS) {
                return $this->respondError('Please select at most ' . self::MAX_PRIVACY_TARGETS . ' people', 422);
            }
            if ($audience === 'only' && !$targetIds) {
                return $this->respondError('Select at least one person to share with', 422);
            }
            if ($audience === 'contacts') {
                $targetIds = [];
            }

            // Drop ids that are not real, active users
            if ($targetIds) {
                $targetIds = array_values(array_filter(
                    $targetIds,
                    fn (int $id) => !$this->userProfileModel->getUser($id)['isDeleted']
                ));
            }

            $this->privacyModel->saveSetting($me, $setting, $audience, $targetIds);

            return $this->respondSuccess(['privacy' => $this->privacyPayload($me)]);
        });
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Inserts a moment with the poster's current share setting snapshotted onto it.
     *
     * @param array $fields type-specific columns
     */
    private function saveMoment(int $me, array $fields): int
    {
        $share = $this->privacyModel->getSettings($me)['share'];
        $now = grid_chat_now();
        $expiresAt = (new \DateTimeImmutable($now, new \DateTimeZone('UTC')))
            ->modify('+' . self::LIFETIME_HOURS . ' hours')
            ->format('Y-m-d H:i:s.v');

        return $this->momentModel->createWithAudience(array_merge([
            'user_id' => $me,
            'audience' => $share['audience'],
            'created_at' => $now,
            'expires_at' => $expiresAt,
        ], $fields), $share['targetIds']);
    }

    /** Both settings with their picked people expanded into display users. */
    private function privacyPayload(int $me): array
    {
        $settings = $this->privacyModel->getSettings($me);

        foreach ($settings as $key => $setting) {
            $users = $this->userProfileModel->getUsers($setting['targetIds']);
            $settings[$key]['targets'] = array_values(array_filter(
                $users,
                static fn (array $user) => !$user['isDeleted']
            ));
            unset($settings[$key]['targetIds']);
        }

        return $settings;
    }

    /**
     * "My Status" always leads the list, with an empty placeholder when the user
     * has not posted anything.
     *
     * @param array<int, array> $grouped
     */
    private function myStatusFirst(array $grouped, int $me, array $users): array
    {
        $mine = $grouped[$me] ?? [
            'id' => 'user',
            'contactId' => $me,
            'userName' => 'My Status',
            'displayName' => $users[$me]['name'] ?? '',
            'userAvatar' => $users[$me]['avatar'] ?? '',
            'isUser' => true,
            'viewed' => true,
            'canDownload' => true,
            'updatedAt' => null,
            'stories' => [],
        ];
        unset($grouped[$me]);

        return array_merge([$mine], array_values($grouped));
    }

    /** Deletes moments past 24 hours and their files. */
    private function purgeExpired(): void
    {
        foreach ($this->momentModel->deleteExpired() as $moment) {
            $this->unlinkMedia($moment['media_path'] ?? null);
        }
    }

    private function unlinkMedia(?string $relativePath): void
    {
        $path = $this->resolveStoredFile(self::MOMENTS_DIR, $relativePath);
        if ($path !== null) {
            @unlink($path);
        }
    }

    /**
     * "12,15,15" → [12, 15], never including the current user.
     *
     * @return int[]
     */
    private function parseUserIds(string $csv, int $excludeId): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', array_filter(explode(',', $csv), static fn ($part) => trim($part) !== '')),
            static fn (int $id) => $id > 0 && $id !== $excludeId
        )));
    }
}
