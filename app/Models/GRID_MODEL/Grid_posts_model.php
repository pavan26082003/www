<?php

namespace App\Models\GRID_MODEL;

/**
 * Grid_posts_model
 * Manages Home Feed posts, multi-media attachments (1:N), real-time likes, and threaded comments.
 * Connected to dedicated 'grid_db' database.
 */
class Grid_posts_model extends Grid_base_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grid_posts';
        parent::__construct($this->table);
    }

    /**
     * Build the feed-ready post shape from a raw grid_posts row plus its
     * joined author identity columns, media list, and user interaction states.
     */
    public function hydrate_post($row, $currentUserId = null, $prefetchedMedia = null, $prefetchedLikes = null, $prefetchedReposts = null) {
        $postId = (int) $row['id'];
        $tags = !empty($row['tags'])
            ? array_map('trim', explode(',', $row['tags']))
            : [];

        // 1. Resolve Media List
        $mediaList = [];
        if ($prefetchedMedia !== null && isset($prefetchedMedia[$postId])) {
            $mediaList = $prefetchedMedia[$postId];
        } else {
            $mediaRows = $this->db->table('grid_post_media')
                ->where('post_id', $postId)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();
            foreach ($mediaRows as $m) {
                $mediaList[] = [
                    'id' => (int) $m['id'],
                    'url' => $m['media_url'],
                    'type' => $m['media_type'] ?: 'image',
                    'order' => (int) $m['sort_order']
                ];
            }
        }

        // Fallback: If grid_post_media had no rows, but legacy media_url is present
        if (empty($mediaList) && !empty($row['media_url'])) {
            $mediaList[] = [
                'id' => 0,
                'url' => $row['media_url'],
                'type' => preg_match('/\.(mp4|webm|mov)$/i', $row['media_url']) ? 'video' : 'image',
                'order' => 0
            ];
        }

        // Primary media url for single-image backwards compatibility
        $primaryMediaUrl = !empty($mediaList) ? $mediaList[0]['url'] : ($row['media_url'] ?? null);

        // 2. Resolve Like status for current user
        $hasLiked = false;
        if (!empty($currentUserId)) {
            if ($prefetchedLikes !== null) {
                $hasLiked = isset($prefetchedLikes[$postId]);
            } else {
                $likeCount = $this->db->table('grid_post_likes')
                    ->where('post_id', $postId)
                    ->where('user_id', (int) $currentUserId)
                    ->countAllResults();
                $hasLiked = ($likeCount > 0);
            }
        }

        // 2b. Resolve whether the current viewer has reposted this post
        $hasReposted = false;
        if (!empty($currentUserId)) {
            if ($prefetchedReposts !== null) {
                $hasReposted = isset($prefetchedReposts[$postId]);
            } else {
                $repostCount = $this->db->table('grid_post_reposts')
                    ->where('post_id', $postId)
                    ->where('user_id', (int) $currentUserId)
                    ->countAllResults();
                $hasReposted = ($repostCount > 0);
            }
        }

        // 3. Repost attribution — present only when this row came from a repost
        // join (fetch_repost_rows()), never for a plain original-post row.
        $repostedBy = null;
        if (!empty($row['repost_id'])) {
            $repostedBy = [
                'id' => (int) ($row['reposter_id'] ?? 0),
                'name' => trim(($row['reposter_first_name'] ?? '') . ' ' . ($row['reposter_last_name'] ?? '')),
                'avatar' => $this->resolve_avatar_url($row['reposter_avatar'] ?? ''),
                'grid_id' => $row['reposter_grid_id'] ?? null,
                'headline' => $row['reposter_headline'] ?? null
            ];
        }

        return [
            // Repost entries get their own unique feed-item id (distinct from the
            // original post's own entry, which may appear in the same page/feed).
            'id' => $repostedBy !== null ? ('repost-' . $row['repost_id']) : ('db-' . $postId),
            'db_id' => $postId,
            'type' => $row['type'],
            'content' => $row['content'],
            'tags' => $tags,
            'media_url' => $primaryMediaUrl,
            'media' => $mediaList,
            'has_liked' => $hasLiked,
            'has_reposted' => $hasReposted,
            'created_at' => $row['created_at'],
            'is_repost' => $repostedBy !== null,
            'repost_id' => $repostedBy !== null ? (int) $row['repost_id'] : null,
            'reposted_at' => $repostedBy !== null ? ($row['repost_created_at'] ?? null) : null,
            'reposted_by' => $repostedBy,
            'author' => [
                'id' => (int) $row['user_id'],
                'name' => trim($row['first_name'] . ' ' . ($row['last_name'] ?? '')),
                'role' => $row['headline'] ?? 'GRID Member',
                'avatar' => $this->resolve_avatar_url($row['grid_avatar'] ?? ''),
                'grid_id' => $row['grid_id'] ?? null,
                'timeAgo' => $this->format_time_ago($row['created_at']),
                'discipline' => 'General'
            ],
            'metrics' => [
                'likes' => (int) $row['likes_count'],
                'comments' => (int) $row['comments_count'],
                'reposts' => (int) $row['reposts_count']
            ]
        ];
    }

    /**
     * Coarse relative-time label (e.g. "Just now", "5m ago", "3h ago", "2d ago").
     */
    public function format_time_ago($createdAt) {
        $seconds = time() - strtotime($createdAt);

        if ($seconds < 60) {
            return 'Just now';
        }
        if ($seconds < 3600) {
            return floor($seconds / 60) . 'm ago';
        }
        if ($seconds < 86400) {
            return floor($seconds / 3600) . 'h ago';
        }
        return floor($seconds / 86400) . 'd ago';
    }

    /**
     * Resolve raw users.image into a valid, public HTTP URL.
     */
    public function resolve_avatar_url($image) {
        if (empty($image)) {
            return '';
        }
        if (function_exists('get_avatar')) {
            return (string) get_avatar($image);
        }
        $file = @unserialize($image);
        if (is_array($file) && !empty($file['file_name'])) {
            return base_url('files/profile_images/' . $file['file_name']);
        }
        if (is_string($image) && preg_match('/^_file[a-z0-9]+.*\.png$/i', $image)) {
            return base_url('files/profile_images/' . $image);
        }
        return (string) $image;
    }

    /**
     * Create a post for $user_id with support for 1:N multi-media attachments.
     * $data accepts:
     * - 'content' (required)
     * - 'media_urls' (array of strings or array of {url, type})
     * - 'media_url' (single string fallback)
     * - 'tags' (array)
     * - 'type' ('general', 'tech_artifact', 'opportunity', 'marketplace')
     */
    function create_post($user_id, $data) {
        $user_id = (int) $user_id;
        $content = trim((string) ($data['content'] ?? ''));

        if ($content === '') {
            return null;
        }

        $allowedTypes = ['general', 'tech_artifact', 'opportunity', 'marketplace'];
        $type = in_array($data['type'] ?? '', $allowedTypes, true) ? $data['type'] : 'general';

        $tagsCsv = '';
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $tags = array_filter(array_map('trim', $data['tags']));
            $tagsCsv = implode(',', $tags);
        }

        // Normalize media URLs list
        $mediaList = [];
        if (!empty($data['media_urls']) && is_array($data['media_urls'])) {
            foreach ($data['media_urls'] as $idx => $item) {
                if (is_string($item) && trim($item) !== '') {
                    $u = trim($item);
                    $mediaList[] = [
                        'url' => $u,
                        'type' => preg_match('/\.(mp4|webm|mov)$/i', $u) ? 'video' : 'image',
                        'order' => $idx
                    ];
                } elseif (is_array($item) && !empty($item['url'])) {
                    $u = trim($item['url']);
                    $mediaList[] = [
                        'url' => $u,
                        'type' => $item['type'] ?? (preg_match('/\.(mp4|webm|mov)$/i', $u) ? 'video' : 'image'),
                        'order' => $item['order'] ?? $idx
                    ];
                }
            }
        } elseif (!empty($data['media_url']) && is_string($data['media_url'])) {
            $u = trim($data['media_url']);
            $mediaList[] = [
                'url' => $u,
                'type' => preg_match('/\.(mp4|webm|mov)$/i', $u) ? 'video' : 'image',
                'order' => 0
            ];
        }

        $primaryMediaUrl = !empty($mediaList) ? substr($mediaList[0]['url'], 0, 500) : null;
        $now = date('Y-m-d H:i:s');

        $insertData = [
            'user_id' => $user_id,
            'content' => $content,
            'tags' => substr($tagsCsv, 0, 255) ?: null,
            'media_url' => $primaryMediaUrl,
            'type' => $type,
            'created_at' => $now,
            'updated_at' => $now
        ];

        $this->db->table('grid_posts')->insert($insertData);
        $postId = (int) $this->db->insertID();

        // Insert into grid_post_media
        if (!empty($mediaList)) {
            foreach ($mediaList as $m) {
                $this->db->table('grid_post_media')->insert([
                    'post_id' => $postId,
                    'media_url' => substr($m['url'], 0, 500),
                    'media_type' => substr($m['type'], 0, 20),
                    'sort_order' => (int) $m['order'],
                    'created_at' => $now
                ]);
            }
        }

        $row = $this->db->table('grid_posts gp')
            ->select("gp.*, u.first_name, u.last_name, u.image, gpr.grid_id, gpr.headline")
            ->join('users u', 'u.id = gp.user_id', 'inner')
            ->join('grid_profiles gpr', 'gpr.user_id = gp.user_id', 'left')
            ->where('gp.id', $postId)
            ->get()
            ->getRowArray();

        return $this->hydrate_post($row, $user_id);
    }

    /**
     * Fetch the live feed, newest first: original posts merged with reposts
     * (each shown as its own entry, attributed to whoever reposted it),
     * hydrated with author identity, multi-media attachments, and like status.
     */
    function get_feed_posts($limit = 20, $offset = 0, $currentUserId = null) {
        return $this->build_merged_feed(null, $limit, $offset, $currentUserId);
    }

    /**
     * Fetch a single user's own posts merged with posts they've reposted —
     * powers the profile Activity Feed's Posts tab.
     */
    function get_user_posts($userId, $limit = 20, $offset = 0, $currentUserId = null) {
        return $this->build_merged_feed((int) $userId, $limit, $offset, $currentUserId);
    }

    /**
     * Shared feed builder: fetches a bounded window of original posts and a
     * bounded window of reposts (each independently sufficient to cover the
     * requested page per standard k-way-merge bounds), hydrates both sets
     * against the same prefetched media/likes, sorts by each entry's own
     * effective timestamp (post creation vs. repost creation), then slices
     * to the requested page. $scopeUserId narrows both sides to one user's
     * own activity (get_user_posts); null means the global feed.
     */
    private function build_merged_feed($scopeUserId, $limit, $offset, $currentUserId) {
        $window = max((int) $limit + (int) $offset, 1);

        $originalRows = $this->fetch_original_post_rows($window, 0, $scopeUserId);
        $repostRows = $this->fetch_repost_rows($window, 0, $scopeUserId);

        if (empty($originalRows) && empty($repostRows)) {
            return [];
        }

        $allPostIds = array_values(array_unique(array_merge(
            array_map(function($r) { return (int) $r['id']; }, $originalRows),
            array_map(function($r) { return (int) $r['id']; }, $repostRows)
        )));

        $prefetchedMedia = $this->prefetch_media($allPostIds);
        $prefetchedLikes = $this->prefetch_likes($allPostIds, $currentUserId);
        $prefetchedReposts = $this->prefetch_reposts($allPostIds, $currentUserId);

        $merged = [];
        foreach ($originalRows as $r) {
            $merged[] = [
                'sort_key' => strtotime($r['created_at']),
                'post' => $this->hydrate_post($r, $currentUserId, $prefetchedMedia, $prefetchedLikes, $prefetchedReposts)
            ];
        }
        foreach ($repostRows as $r) {
            $merged[] = [
                'sort_key' => strtotime($r['repost_created_at']),
                'post' => $this->hydrate_post($r, $currentUserId, $prefetchedMedia, $prefetchedLikes, $prefetchedReposts)
            ];
        }

        usort($merged, function($a, $b) { return $b['sort_key'] <=> $a['sort_key']; });

        return array_map(function($m) { return $m['post']; }, array_slice($merged, $offset, $limit));
    }

    /**
     * Original (non-repost) posts, newest first. $authorUserId narrows to one author.
     */
    private function fetch_original_post_rows($limit, $offset, $authorUserId = null) {
        $builder = $this->db->table('grid_posts gp')
            ->select("gp.*, u.first_name, u.last_name, gpr.avatar as grid_avatar, gpr.grid_id, gpr.headline")
            ->join('users u', 'u.id = gp.user_id', 'inner')
            ->join('grid_profiles gpr', 'gpr.user_id = gp.user_id', 'left')
            ->where('gp.deleted', 0);

        if ($authorUserId !== null) {
            $builder->where('gp.user_id', (int) $authorUserId);
        }

        return $builder->orderBy('gp.created_at', 'DESC')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Reposts joined to their original post + both the original author's and
     * the reposter's identity, newest-repost first. $reposterUserId narrows
     * to reposts made by one user.
     */
    private function fetch_repost_rows($limit, $offset, $reposterUserId = null) {
        $builder = $this->db->table('grid_post_reposts gr')
            ->select("gr.id as repost_id, gr.created_at as repost_created_at,
                     gp.*, u.first_name, u.last_name, gpr.avatar as grid_avatar, gpr.grid_id, gpr.headline,
                     ru.id as reposter_id, ru.first_name as reposter_first_name, ru.last_name as reposter_last_name,
                     rgp.avatar as reposter_avatar, rgp.grid_id as reposter_grid_id, rgp.headline as reposter_headline")
            ->join('grid_posts gp', 'gp.id = gr.post_id', 'inner')
            ->join('users u', 'u.id = gp.user_id', 'inner')
            ->join('grid_profiles gpr', 'gpr.user_id = gp.user_id', 'left')
            ->join('users ru', 'ru.id = gr.user_id', 'inner')
            ->join('grid_profiles rgp', 'rgp.user_id = gr.user_id', 'left')
            ->where('gp.deleted', 0);

        if ($reposterUserId !== null) {
            $builder->where('gr.user_id', (int) $reposterUserId);
        }

        return $builder->orderBy('gr.created_at', 'DESC')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Batch-fetch media for a set of post ids, keyed by post_id.
     */
    private function prefetch_media(array $postIds) {
        $prefetchedMedia = [];
        if (empty($postIds)) {
            return $prefetchedMedia;
        }

        $mediaRows = $this->db->table('grid_post_media')
            ->whereIn('post_id', $postIds)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($mediaRows as $m) {
            $pId = (int) $m['post_id'];
            if (!isset($prefetchedMedia[$pId])) {
                $prefetchedMedia[$pId] = [];
            }
            $prefetchedMedia[$pId][] = [
                'id' => (int) $m['id'],
                'url' => $m['media_url'],
                'type' => $m['media_type'] ?: 'image',
                'order' => (int) $m['sort_order']
            ];
        }

        return $prefetchedMedia;
    }

    /**
     * Batch-fetch which of a set of post ids $currentUserId has liked.
     */
    private function prefetch_likes(array $postIds, $currentUserId) {
        $prefetchedLikes = [];
        if (empty($postIds) || empty($currentUserId)) {
            return $prefetchedLikes;
        }

        $likeRows = $this->db->table('grid_post_likes')
            ->whereIn('post_id', $postIds)
            ->where('user_id', (int) $currentUserId)
            ->get()
            ->getResultArray();

        foreach ($likeRows as $l) {
            $prefetchedLikes[(int) $l['post_id']] = true;
        }

        return $prefetchedLikes;
    }

    /**
     * Batch-fetch which of a set of post ids $currentUserId has reposted.
     */
    private function prefetch_reposts(array $postIds, $currentUserId) {
        $prefetchedReposts = [];
        if (empty($postIds) || empty($currentUserId)) {
            return $prefetchedReposts;
        }

        $repostRows = $this->db->table('grid_post_reposts')
            ->whereIn('post_id', $postIds)
            ->where('user_id', (int) $currentUserId)
            ->get()
            ->getResultArray();

        foreach ($repostRows as $r) {
            $prefetchedReposts[(int) $r['post_id']] = true;
        }

        return $prefetchedReposts;
    }

    /**
     * Toggle like on a post for $userId.
     * Prevents double-likes and updates likes_count atomically.
     */
    function toggle_like($postId, $userId) {
        $postId = (int) $postId;
        $userId = (int) $userId;

        $existing = $this->db->table('grid_post_likes')
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($existing) {
            // Remove like
            $this->db->table('grid_post_likes')
                ->where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();

            // Decrement likes_count in grid_posts (never negative)
            $this->db->query("UPDATE grid_posts SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?", [$postId]);
            $liked = false;
        } else {
            // Add like
            $this->db->table('grid_post_likes')->insert([
                'post_id' => $postId,
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Increment likes_count
            $this->db->query("UPDATE grid_posts SET likes_count = likes_count + 1 WHERE id = ?", [$postId]);
            $liked = true;
        }

        $postRow = $this->db->table('grid_posts')
            ->select('likes_count')
            ->where('id', $postId)
            ->get()
            ->getRowArray();

        $likesCount = $postRow ? (int) $postRow['likes_count'] : 0;

        return [
            'liked' => $liked,
            'likes_count' => $likesCount
        ];
    }

    /**
     * Toggle a repost of $postId by $userId (idempotent, mirrors toggle_like).
     * Reposting shares the original post into the reposter's own feed/profile;
     * un-reposting removes it. Updates reposts_count atomically.
     */
    function toggle_repost($postId, $userId) {
        $postId = (int) $postId;
        $userId = (int) $userId;

        $existing = $this->db->table('grid_post_reposts')
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($existing) {
            // Always allow removing an existing repost (including a stray
            // self-repost from before this check existed) — only creating a
            // new self-repost is blocked below.
            $this->db->table('grid_post_reposts')
                ->where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();

            $this->db->query("UPDATE grid_posts SET reposts_count = GREATEST(0, reposts_count - 1) WHERE id = ?", [$postId]);
            $reposted = false;
        } else {
            // Reposting your own post isn't meaningful — it's already on
            // your profile/feed. Refuse rather than create a confusing
            // duplicate entry attributed to yourself.
            $postAuthor = $this->db->table('grid_posts')
                ->select('user_id')
                ->where('id', $postId)
                ->get()
                ->getRowArray();

            if ($postAuthor && (int) $postAuthor['user_id'] === $userId) {
                $currentCount = $this->db->table('grid_posts')
                    ->select('reposts_count')
                    ->where('id', $postId)
                    ->get()
                    ->getRowArray();

                return [
                    'error' => 'self_repost',
                    'reposted' => false,
                    'reposts_count' => $currentCount ? (int) $currentCount['reposts_count'] : 0
                ];
            }

            $this->db->table('grid_post_reposts')->insert([
                'post_id' => $postId,
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->query("UPDATE grid_posts SET reposts_count = reposts_count + 1 WHERE id = ?", [$postId]);
            $reposted = true;
        }

        $postRow = $this->db->table('grid_posts')
            ->select('reposts_count')
            ->where('id', $postId)
            ->get()
            ->getRowArray();

        $repostsCount = $postRow ? (int) $postRow['reposts_count'] : 0;

        return [
            'reposted' => $reposted,
            'reposts_count' => $repostsCount
        ];
    }

    /**
     * Add a comment to a post.
     */
    function add_comment($postId, $userId, $text) {
        $postId = (int) $postId;
        $userId = (int) $userId;
        $text = trim((string) $text);

        if ($text === '') {
            return null;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('grid_post_comments')->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'comment_text' => $text,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted' => 0
        ]);

        $commentId = (int) $this->db->insertID();

        // Increment comments_count
        $this->db->query("UPDATE grid_posts SET comments_count = comments_count + 1 WHERE id = ?", [$postId]);

        // Get author info
        $userRow = $this->db->table('users u')
            ->select('u.first_name, u.last_name, gpr.avatar as grid_avatar, gpr.headline')
            ->join('grid_profiles gpr', 'gpr.user_id = u.id', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();

        $authorName = trim(($userRow['first_name'] ?? 'User') . ' ' . ($userRow['last_name'] ?? ''));

        return [
            'id' => $commentId,
            'post_id' => $postId,
            'text' => $text,
            'author' => [
                'id' => $userId,
                'name' => $authorName ?: 'You',
                'verified' => true,
                'degree' => '1st',
                'avatar' => $this->resolve_avatar_url($userRow['grid_avatar'] ?? ''),
                'headline' => $userRow['headline'] ?? 'Member @ Grid',
                'timeAgo' => 'Just now'
            ],
            'likes' => 0,
            'userLiked' => false,
            'replies' => [],
            'created_at' => $now
        ];
    }

    /**
     * Get comments for a post.
     */
    function get_comments($postId, $limit = 50, $offset = 0) {
        $postId = (int) $postId;

        $rows = $this->db->table('grid_post_comments c')
            ->select('c.*, u.first_name, u.last_name, gpr.avatar as grid_avatar, gpr.headline')
            ->join('users u', 'u.id = c.user_id', 'inner')
            ->join('grid_profiles gpr', 'gpr.user_id = c.user_id', 'left')
            ->where('c.post_id', $postId)
            ->where('c.deleted', 0)
            ->orderBy('c.created_at', 'ASC')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->getResultArray();

        $comments = [];
        foreach ($rows as $r) {
            $authorName = trim(($r['first_name'] ?? 'User') . ' ' . ($r['last_name'] ?? ''));
            $comments[] = [
                'id' => (int) $r['id'],
                'post_id' => $postId,
                'text' => $r['comment_text'],
                'author' => [
                    'id' => (int) $r['user_id'],
                    'name' => $authorName ?: 'GRID Member',
                    'verified' => true,
                    'degree' => '1st',
                    'avatar' => $this->resolve_avatar_url($r['grid_avatar'] ?? ''),
                    'headline' => $r['headline'] ?? 'Member @ Grid',
                    'timeAgo' => $this->format_time_ago($r['created_at'])
                ],
                'likes' => 0,
                'userLiked' => false,
                'replies' => [],
                'created_at' => $r['created_at']
            ];
        }

        return $comments;
    }

    /**
     * Delete a comment (soft delete).
     */
    function delete_comment($commentId, $userId, $isAdmin = false) {
        $commentId = (int) $commentId;
        $userId = (int) $userId;

        $builder = $this->db->table('grid_post_comments')->where('id', $commentId);
        if (!$isAdmin) {
            $builder->where('user_id', $userId);
        }

        $row = $builder->get()->getRowArray();
        if (!$row) {
            return false;
        }

        $postId = (int) $row['post_id'];

        $this->db->table('grid_post_comments')
            ->where('id', $commentId)
            ->update([
                'deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // Decrement comments_count in grid_posts
        $this->db->query("UPDATE grid_posts SET comments_count = GREATEST(0, comments_count - 1) WHERE id = ?", [$postId]);

        return true;
    }

    /**
     * Delete a post (soft delete by setting deleted = 1).
     * Automatically unlinks orphaned physical files from disk (files/timeline_files/)!
     */
    function delete_post($postId, $userId = null, $isAdmin = false) {
        $postId = (int) $postId;
        if ($postId <= 0) {
            return false;
        }

        $builder = $this->db->table('grid_posts')->where('id', $postId);
        if (!$isAdmin && !empty($userId)) {
            $builder->where('user_id', (int) $userId);
        }

        $row = $builder->get()->getRowArray();
        if (!$row) {
            return false;
        }

        // 1. Gather all attached media files for physical disk cleanup
        $filesToUnlink = [];
        if (!empty($row['media_url'])) {
            $filesToUnlink[] = $row['media_url'];
        }

        $mediaRows = $this->db->table('grid_post_media')
            ->where('post_id', $postId)
            ->get()
            ->getResultArray();

        foreach ($mediaRows as $m) {
            if (!empty($m['media_url'])) {
                $filesToUnlink[] = $m['media_url'];
            }
        }

        // Unlink physical files from disk if stored locally under files/
        foreach (array_unique($filesToUnlink) as $mediaPath) {
            $parsedRel = ltrim((string) parse_url($mediaPath, PHP_URL_PATH), '/\\');
            if (strpos($parsedRel, 'files/') === 0) {
                $diskFile = FCPATH . $parsedRel;
                if (file_exists($diskFile) && is_file($diskFile)) {
                    @unlink($diskFile);
                }
            }
        }

        // 2. Mark post deleted
        $this->db->table('grid_posts')
            ->where('id', $postId)
            ->update([
                'deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return true;
    }
}
