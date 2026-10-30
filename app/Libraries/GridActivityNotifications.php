<?php

namespace App\Libraries;

/**
 * Recent messages and calls for the Grid dashboard notification list.
 * Professional Space reads grid_db.grid_messages.
 * Circle Connect reads grid_chat_db.messages (message_type = call for calls).
 */
class GridActivityNotifications
{
    private const PER_BUCKET = 3;

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function forUser(int $userId, string $ssoToken = ''): array
    {
        $ssoToken = trim($ssoToken);
        $buckets = array(
            'professional-message' => array(),
            'professional-call' => array(),
            'circle-message' => array(),
            'circle-call' => array(),
        );

        if ($userId > 0) {
            try {
                $professional = self::professionalItems($userId, $ssoToken);
                $buckets['professional-message'] = $professional['message'];
                $buckets['professional-call'] = $professional['call'];
            } catch (\Throwable $e) {
                log_message('error', 'Grid professional notifications: ' . $e->getMessage());
            }
            try {
                $circle = self::circleItems($userId, $ssoToken);
                $buckets['circle-message'] = $circle['message'];
                $buckets['circle-call'] = $circle['call'];
            } catch (\Throwable $e) {
                log_message('error', 'Grid circle notifications: ' . $e->getMessage());
            }
        }

        $items = array();
        foreach ($buckets as $rows) {
            foreach ($rows as $row) {
                $items[] = $row;
            }
        }
        if (self::previewSamples()) {
            $items = array_merge($items, self::localSamples($items, $ssoToken));
        }
        usort($items, function ($a, $b) {
            return (int) ($b['at'] ?? 0) <=> (int) ($a['at'] ?? 0);
        });
        foreach ($items as $index => $item) {
            unset($items[$index]['at']);
        }
        if (!$items) {
            $items[] = self::emptyItem('professional-message', $ssoToken);
        }
        return array_values($items);
    }

    /**
     * Sample rows fill an empty inbox on this machine and on a phone
     * pointed at it. A production host keeps only real messages and calls.
     */
    private static function previewSamples(): bool
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
            return true;
        }
        $host = strtolower((string) preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? '')));
        if (in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
            return true;
        }
        return filter_var($host, FILTER_VALIDATE_IP) !== false
            && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * Local preview only. Adds enough samples to show two Professional
     * Space rows and two Circle Connect rows. Real rows are kept.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private static function localSamples(array $items, string $ssoToken): array
    {
        $professional = 0;
        $circle = 0;
        foreach ($items as $item) {
            if ((string) ($item['space'] ?? '') === 'circle') {
                $circle++;
            } else {
                $professional++;
            }
        }
        $samples = array(
            'professional' => array(
                array('Sarah Jenkins', 'Tech Lead', 'Mixed video sync · "Frontend Architect final pass"', 'SJ', '#7c6bf2', true, 2 * 3600),
                array('David Kim', 'VP Product', 'Reviewed Q3 design doc and left 3 notes', 'DK', '#8b5cf6', false, 3 * 3600),
            ),
            'circle' => array(
                array('Photography Club', 'Circle', 'Sophia shared 6 new photos in "Weekend"', 'PC', '#f59e0b', true, 3600),
                array('Weekend Hikes Circle', 'Circle', 'Mixed group voice huddle (6 participants)', 'WH', '#f97316', false, 3 * 3600),
            ),
        );
        $extra = array();
        foreach (array('professional' => $professional, 'circle' => $circle) as $space => $have) {
            $need = 2 - $have;
            if ($need <= 0) {
                continue;
            }
            $rows = array_slice($samples[$space], 0, $need);
            foreach ($rows as $index => $row) {
                $isCircle = $space === 'circle';
                $extra[] = self::item(array(
                    'id' => 'local-' . $space . '-' . ($index + 1),
                    'kind' => 'message',
                    'space' => $space,
                    'title' => $row[0],
                    'preview' => $row[2],
                    'badge' => $row[1],
                    'time' => self::shortAgo(date('Y-m-d H:i:s', time() - $row[6])),
                    'at' => time() - $row[6],
                    'initials' => $row[3],
                    'avatarColor' => $row[4],
                    'iconToken' => 'message',
                    'actionId' => $isCircle ? 'grid-open-circle-message' : 'grid-open-professional-message',
                    'path' => $isCircle ? 'grid-chat' : 'grid/messaging',
                    'query' => '',
                    'ssoToken' => $ssoToken,
                    'unread' => $row[5],
                ));
            }
        }
        return $extra;
    }

    /**
     * One indexed read for this recipient. Calls and texts are split in PHP.
     *
     * @return array{message: array<int, array<string, mixed>>, call: array<int, array<string, mixed>>}
     */
    private static function professionalItems(int $userId, string $ssoToken): array
    {
        $empty = array('message' => array(), 'call' => array());
        $db = self::db('grid');
        if ($db === null || !self::tableExists($db, 'grid_messages')) {
            return $empty;
        }

        $fields = array_flip($db->getFieldNames('grid_messages'));
        $hasType = isset($fields['message_type']);
        $hasMeta = isset($fields['metadata']);
        $select = 'id, conversation_id, sender_id, message_body, is_read, created_at';
        if ($hasType) {
            $select .= ', message_type';
        }
        if ($hasMeta) {
            $select .= ', metadata';
        }

        $rows = array();
        if (isset($fields['recipient_id'])) {
            if ($hasType) {
                foreach (array('text', 'call') as $type) {
                    $rows = array_merge($rows, $db->query(
                        'SELECT ' . $select . ' FROM grid_messages WHERE recipient_id = ? AND message_type = ? ORDER BY id DESC LIMIT ' . self::PER_BUCKET,
                        array($userId, $type)
                    )->getResultArray());
                }
            } else {
                $rows = $db->query(
                    'SELECT ' . $select . ' FROM grid_messages WHERE recipient_id = ? ORDER BY id DESC LIMIT ' . (self::PER_BUCKET * 2),
                    array($userId)
                )->getResultArray();
            }
        }
        if (!$rows && self::tableExists($db, 'grid_conversations')) {
            $joined = implode(', ', array_map(function ($column) {
                return 'm.' . trim($column);
            }, explode(',', $select)));
            $rows = $db->query(
                'SELECT ' . $joined . ' FROM grid_messages m
                 INNER JOIN grid_conversations c ON c.id = m.conversation_id
                 WHERE (c.user_one_id = ? OR c.user_two_id = ?) AND m.sender_id <> ?
                 ORDER BY m.id DESC LIMIT ' . (self::PER_BUCKET * 2),
                array($userId, $userId, $userId)
            )->getResultArray();
        }

        $people = self::people(array_map('intval', array_column($rows, 'sender_id')));
        $messages = array();
        $calls = array();
        foreach ($rows as $row) {
            $isCall = $hasType && (string) ($row['message_type'] ?? '') === 'call';
            if ($isCall && count($calls) >= self::PER_BUCKET) {
                continue;
            }
            if (!$isCall && count($messages) >= self::PER_BUCKET) {
                continue;
            }
            $item = self::professionalRow($row, $isCall, $ssoToken, $people);
            if ($isCall) {
                $calls[] = $item;
            } else {
                $messages[] = $item;
            }
        }

        return array('message' => $messages, 'call' => $calls);
    }

    /**
     * @param array<string, mixed> $row
     * @param array<int, string> $names
     * @return array<string, mixed>
     */
    private static function professionalRow(array $row, bool $isCall, string $ssoToken, array $names): array
    {
        $senderId = (int) ($row['sender_id'] ?? 0);
        $person = $names[$senderId] ?? array('name' => 'Someone', 'headline' => '', 'initials' => 'S');
        $meta = self::decodeMeta($row['metadata'] ?? null);
        $unread = (int) ($row['is_read'] ?? 0) === 0;
        $preview = $isCall
            ? self::callLabel($meta, (string) ($row['message_body'] ?? ''))
            : self::clip((string) ($row['message_body'] ?? ''));
        $conversationId = (int) ($row['conversation_id'] ?? 0);
        $badge = $isCall ? 'Call' : ($person['headline'] !== '' ? $person['headline'] : 'Professional');

        return self::item(array(
            'id' => ($isCall ? 'professional-call-' : 'professional-message-') . (int) $row['id'],
            'kind' => $isCall ? 'call' : 'message',
            'space' => 'professional',
            'title' => $person['name'],
            'preview' => $preview,
            'badge' => $badge,
            'time' => self::shortAgo((string) ($row['created_at'] ?? '')),
            'at' => strtotime((string) ($row['created_at'] ?? '')) ?: 0,
            'initials' => $person['initials'],
            'iconToken' => $isCall ? 'phone' : 'message',
            'actionId' => $isCall ? 'grid-open-professional-call' : 'grid-open-professional-message',
            'path' => 'grid/messaging',
            'query' => 'conversation=' . $conversationId . '&user=' . $senderId,
            'ssoToken' => $ssoToken,
            'unread' => $unread,
        ));
    }

    /**
     * @return array{message: array<int, array<string, mixed>>, call: array<int, array<string, mixed>>}
     */
    private static function circleItems(int $userId, string $ssoToken): array
    {
        $empty = array('message' => array(), 'call' => array());
        $db = self::db('gridchat');
        if ($db === null || !self::tableExists($db, 'messages') || !self::tableExists($db, 'conversation_members') || !self::tableExists($db, 'conversations')) {
            return $empty;
        }

        // Start from this user's chats. Scanning every message in grid_chat_db
        // made the dashboard wait on the whole history.
        $memberFields = array_flip($db->getFieldNames('conversation_members'));
        $memberSelect = 'conversation_id';
        foreach (array('last_read_message_id', 'cleared_before_message_id', 'joined_after_message_id') as $column) {
            if (isset($memberFields[$column])) {
                $memberSelect .= ', ' . $column;
            }
        }
        $memberWhere = 'user_id = ?';
        if (isset($memberFields['left_at'])) {
            $memberWhere .= ' AND left_at IS NULL';
        }
        if (isset($memberFields['deleted_at'])) {
            $memberWhere .= ' AND deleted_at IS NULL';
        }
        $members = $db->query(
            'SELECT ' . $memberSelect . ' FROM conversation_members WHERE ' . $memberWhere . ' LIMIT 40',
            array($userId)
        )->getResultArray();
        if (!$members) {
            return $empty;
        }

        $byConversation = array();
        $conversationIds = array();
        foreach ($members as $member) {
            $conversationId = (int) ($member['conversation_id'] ?? 0);
            if ($conversationId <= 0) {
                continue;
            }
            $byConversation[$conversationId] = $member;
            $conversationIds[] = $conversationId;
        }
        if (!$conversationIds) {
            return $empty;
        }

        $messageFields = array_flip($db->getFieldNames('messages'));
        $select = 'msg.id, msg.conversation_id, msg.sender_id, msg.body, msg.created_at, c.type AS conv_type, c.title AS conv_title';
        if (isset($messageFields['message_type'])) {
            $select .= ', msg.message_type';
        }
        if (isset($messageFields['metadata'])) {
            $select .= ', msg.metadata';
        }
        $where = 'msg.conversation_id IN (' . implode(',', $conversationIds) . ') AND msg.sender_id <> ?';
        if (isset($messageFields['deleted_at'])) {
            $where .= ' AND msg.deleted_at IS NULL';
        }
        if (isset($messageFields['message_type'])) {
            $where .= ' AND msg.message_type <> \'system\'';
        }
        $rows = $db->query(
            'SELECT ' . $select . '
             FROM messages msg
             INNER JOIN conversations c ON c.id = msg.conversation_id
             WHERE ' . $where . '
             ORDER BY msg.id DESC
             LIMIT 40',
            array($userId)
        )->getResultArray();
        if (!$rows) {
            return $empty;
        }

        $hidden = array();
        if (self::tableExists($db, 'message_hidden')) {
            $messageIds = array();
            foreach ($rows as $row) {
                $messageId = (int) ($row['id'] ?? 0);
                if ($messageId > 0) {
                    $messageIds[] = $messageId;
                }
            }
            if ($messageIds) {
                $hiddenRows = $db->query(
                    'SELECT message_id FROM message_hidden WHERE user_id = ? AND message_id IN (' . implode(',', $messageIds) . ')',
                    array($userId)
                )->getResultArray();
                foreach ($hiddenRows as $hiddenRow) {
                    $hidden[(int) $hiddenRow['message_id']] = true;
                }
            }
        }

        $visible = array();
        foreach ($rows as $row) {
            $messageId = (int) ($row['id'] ?? 0);
            $conversationId = (int) ($row['conversation_id'] ?? 0);
            if ($messageId <= 0 || isset($hidden[$messageId]) || !isset($byConversation[$conversationId])) {
                continue;
            }
            $member = $byConversation[$conversationId];
            $floor = max(
                (int) ($member['cleared_before_message_id'] ?? 0),
                (int) ($member['joined_after_message_id'] ?? 0)
            );
            if ($messageId <= $floor) {
                continue;
            }
            $row['last_read_message_id'] = (int) ($member['last_read_message_id'] ?? 0);
            $visible[] = $row;
        }
        $rows = $visible;
        if (!$rows) {
            return $empty;
        }

        $people = self::people(array_map('intval', array_column($rows, 'sender_id')));
        $messages = array();
        $calls = array();
        foreach ($rows as $row) {
            $isCall = (string) ($row['message_type'] ?? '') === 'call';
            $bucket = $isCall ? 'call' : 'message';
            if ($bucket === 'call' && count($calls) >= self::PER_BUCKET) {
                continue;
            }
            if ($bucket === 'message' && count($messages) >= self::PER_BUCKET) {
                continue;
            }

            $senderId = (int) ($row['sender_id'] ?? 0);
            $person = $people[$senderId] ?? array('name' => 'Someone', 'headline' => '', 'initials' => 'S');
            $circleTitle = trim((string) ($row['conv_title'] ?? ''));
            $isGroup = (string) ($row['conv_type'] ?? '') === 'group' && $circleTitle !== '';
            $displayName = $isGroup ? $circleTitle : $person['name'];
            $meta = self::decodeMeta($row['metadata'] ?? null);
            $lastRead = (int) ($row['last_read_message_id'] ?? 0);
            $unread = (int) ($row['id'] ?? 0) > $lastRead;
            $preview = $isCall
                ? self::callLabel($meta, (string) ($row['body'] ?? ''))
                : self::clip((string) ($row['body'] ?? ''));
            $conversationId = (int) ($row['conversation_id'] ?? 0);
            $query = 'conversation_id=' . $conversationId;
            if ($isCall) {
                $query .= '&view=calls';
            }

            $item = self::item(array(
                'id' => ($isCall ? 'circle-call-' : 'circle-message-') . (int) $row['id'],
                'kind' => $isCall ? 'call' : 'message',
                'space' => 'circle',
                'title' => $displayName,
                'preview' => $preview,
                'badge' => $isCall ? 'Call' : 'Circle',
                'time' => self::shortAgo((string) ($row['created_at'] ?? '')),
                'at' => strtotime((string) ($row['created_at'] ?? '')) ?: 0,
                'initials' => self::initials($displayName),
                'iconToken' => $isCall ? 'phone' : 'message',
                'actionId' => $isCall ? 'grid-open-circle-call' : 'grid-open-circle-message',
                'path' => 'grid-chat',
                'query' => $query,
                'ssoToken' => $ssoToken,
                'unread' => $unread,
            ));
            if ($isCall) {
                $calls[] = $item;
            } else {
                $messages[] = $item;
            }
            if (count($messages) >= self::PER_BUCKET && count($calls) >= self::PER_BUCKET) {
                break;
            }
        }

        return array('message' => $messages, 'call' => $calls);
    }

    /**
     * @param array<string, mixed> $spec
     * @return array<string, mixed>
     */
    private static function item(array $spec): array
    {
        $isCircle = (string) ($spec['space'] ?? '') === 'circle';
        $color = $isCircle ? '#f97316' : '#5b6cff';
        $url = self::url((string) ($spec['path'] ?? ''), (string) ($spec['query'] ?? ''), (string) ($spec['ssoToken'] ?? ''));
        $preview = (string) ($spec['preview'] ?? $spec['detail'] ?? '');
        return array(
            'id' => (string) ($spec['id'] ?? ''),
            'kind' => (string) ($spec['kind'] ?? 'message'),
            'space' => $isCircle ? 'circle' : 'professional',
            'title' => (string) ($spec['title'] ?? ''),
            'subtitle' => $preview,
            'preview' => $preview,
            'badge' => (string) ($spec['badge'] ?? ($isCircle ? 'Circle' : 'Professional')),
            'time' => (string) ($spec['time'] ?? ''),
            'at' => (int) ($spec['at'] ?? 0),
            'initials' => (string) ($spec['initials'] ?? self::initials((string) ($spec['title'] ?? ''))),
            'iconToken' => (string) ($spec['iconToken'] ?? 'message'),
            'color' => $color,
            'backgroundColor' => $isCircle ? '#fff4ec' : '#eef2ff',
            'avatarColor' => (string) ($spec['avatarColor'] ?? self::avatarColor((string) ($spec['title'] ?? ''))),
            'actionId' => (string) ($spec['actionId'] ?? ''),
            'url' => $url,
            'route' => $url,
            'unread' => !empty($spec['unread']),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function emptyItem(string $key, string $ssoToken): array
    {
        $map = array(
            'professional-message' => array('professional', 'message', 'Messages', 'No new messages', 'grid/messaging', '', 'grid-open-professional-message'),
            'professional-call' => array('professional', 'call', 'Calls', 'No new calls', 'grid/messaging', '', 'grid-open-professional-call'),
            'circle-message' => array('circle', 'message', 'Messages', 'No new messages', 'grid-chat', '', 'grid-open-circle-message'),
            'circle-call' => array('circle', 'call', 'Calls', 'No new calls', 'grid-chat', 'view=calls', 'grid-open-circle-call'),
        );
        $spec = $map[$key] ?? $map['professional-message'];
        return self::item(array(
            'id' => $key,
            'space' => $spec[0],
            'kind' => $spec[1],
            'title' => $spec[2],
            'preview' => $spec[3],
            'badge' => $spec[0] === 'circle' ? 'Circle' : 'Professional',
            'time' => '',
            'initials' => self::initials($spec[2]),
            'iconToken' => $spec[1] === 'call' ? 'phone' : 'message',
            'path' => $spec[4],
            'query' => $spec[5],
            'actionId' => $spec[6],
            'ssoToken' => $ssoToken,
            'unread' => false,
        ));
    }

    private static function url(string $path, string $query, string $ssoToken): string
    {
        $base = class_exists(GridWebSso::class)
            ? GridWebSso::publicWebUrl($path)
            : $path;
        if ($query !== '') {
            $base .= (strpos($base, '?') !== false ? '&' : '?') . $query;
        }
        if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
            $base = GridWebSso::appendToken($base, $ssoToken);
        }
        return $base;
    }

    /**
     * @param array<string, mixed> $meta
     */
    private static function callTitle(array $meta): string
    {
        $status = strtolower((string) ($meta['status'] ?? ''));
        if ($status === 'missed') {
            return 'Missed call';
        }
        if ($status === 'declined') {
            return 'Declined call';
        }
        if ($status === 'cancelled') {
            return 'Cancelled call';
        }
        return ((string) ($meta['call_type'] ?? '')) === 'video' ? 'Video call' : 'Voice call';
    }

    /**
     * @param array<string, mixed> $meta
     */
    private static function callLabel(array $meta, string $fallback): string
    {
        $type = ((string) ($meta['call_type'] ?? 'audio')) === 'video' ? 'Video' : 'Voice';
        $status = strtolower((string) ($meta['status'] ?? ''));
        $label = $status === 'missed' ? 'Missed ' . strtolower($type) . ' call' : $type . ' call';
        $seconds = (int) ($meta['duration'] ?? 0);
        if ($seconds > 0 && $status !== 'missed') {
            $label .= ' · ' . sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
        }
        if ($label !== '') {
            return $label;
        }
        return self::clip($fallback);
    }

    /**
     * @param mixed $raw
     * @return array<string, mixed>
     */
    private static function decodeMeta($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (!is_string($raw) || trim($raw) === '') {
            return array();
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : array();
    }

    private static function clip(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        if ($text === '') {
            return '';
        }
        if (function_exists('mb_strlen') && mb_strlen($text) > 72) {
            return rtrim(mb_substr($text, 0, 69)) . '...';
        }
        if (strlen($text) > 72) {
            return rtrim(substr($text, 0, 69)) . '...';
        }
        return $text;
    }

    private static function shortAgo(string $created): string
    {
        $ts = strtotime($created);
        if (!$ts) {
            return '';
        }
        $diff = time() - $ts;
        if ($diff < 60) {
            return 'now';
        }
        if ($diff < 3600) {
            return (int) floor($diff / 60) . 'm';
        }
        if ($diff < 86400) {
            return (int) floor($diff / 3600) . 'h';
        }
        if ($diff < 604800) {
            return (int) floor($diff / 86400) . 'd';
        }
        return date('M j', $ts);
    }

    private static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: array();
        $first = strtoupper(substr((string) ($parts[0] ?? 'S'), 0, 1));
        $second = isset($parts[1]) ? strtoupper(substr((string) $parts[1], 0, 1)) : '';
        $letters = $first . $second;
        return $letters !== '' ? $letters : 'S';
    }

    private static function avatarColor(string $name): string
    {
        $palette = array('#7c6bf2', '#3b82f6', '#14b8a6', '#f59e0b', '#8b5cf6', '#0ea5e9', '#f97316');
        $index = abs(crc32($name)) % count($palette);
        return $palette[$index];
    }

    private static function ago(string $created): string
    {
        $ts = strtotime($created);
        if (!$ts) {
            return '';
        }
        $diff = time() - $ts;
        if ($diff < 60) {
            return 'Just now';
        }
        if ($diff < 3600) {
            return (int) floor($diff / 60) . 'm ago';
        }
        if ($diff < 86400) {
            return (int) floor($diff / 3600) . 'h ago';
        }
        if ($diff < 604800) {
            return (int) floor($diff / 86400) . 'd ago';
        }
        return date('M j', $ts);
    }

    /**
     * @param int[] $ids
     * @return array<int, array{name:string,headline:string,initials:string}>
     */
    private static function people(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return array();
        }
        try {
            $rows = db_connect()->query(
                'SELECT id, first_name, last_name, email FROM users WHERE id IN (' . implode(',', $ids) . ')'
            )->getResultArray();
        } catch (\Throwable $e) {
            return array();
        }
        $headlines = array();
        $grid = self::db('grid');
        if ($grid && self::tableExists($grid, 'grid_profiles')) {
            try {
                $fields = array_flip($grid->getFieldNames('grid_profiles'));
                if (isset($fields['headline']) && isset($fields['user_id'])) {
                    $headlineRows = $grid->query(
                        'SELECT user_id, headline FROM grid_profiles WHERE user_id IN (' . implode(',', $ids) . ')'
                    )->getResultArray();
                    foreach ($headlineRows as $headlineRow) {
                        $headlines[(int) ($headlineRow['user_id'] ?? 0)] = trim((string) ($headlineRow['headline'] ?? ''));
                    }
                }
            } catch (\Throwable $e) {
                $headlines = array();
            }
        }
        $people = array();
        foreach ($rows as $row) {
            $name = trim((string) ($row['first_name'] ?? '') . ' ' . (string) ($row['last_name'] ?? ''));
            if ($name === '') {
                $email = (string) ($row['email'] ?? '');
                $name = $email !== '' ? (string) strstr($email, '@', true) : 'Someone';
            }
            if ($name === '') {
                $name = 'Someone';
            }
            $people[(int) $row['id']] = array(
                'name' => $name,
                'headline' => $headlines[(int) $row['id']] ?? '',
                'initials' => self::initials($name),
            );
        }
        return $people;
    }

    /**
     * @return \CodeIgniter\Database\BaseConnection|null
     */
    private static function db(string $group)
    {
        try {
            $db = db_connect($group);
        } catch (\Throwable $e) {
            log_message('error', 'Grid notification database ' . $group . ': ' . $e->getMessage());
            return null;
        }

        foreach (array('SET SESSION max_execution_time = 2000', 'SET SESSION max_statement_time = 2') as $limitSql) {
            try {
                if ($db->query($limitSql) !== false) {
                    break;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $db;
    }

    private static function tableExists($db, string $table): bool
    {
        try {
            $row = $db->query('SHOW TABLES LIKE ' . $db->escape($table))->getRowArray();
            return !empty($row);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
