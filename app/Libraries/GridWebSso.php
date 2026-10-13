<?php

namespace App\Libraries;

/**
 * Same-app web SSO for Grid feature cards.
 * Stores clean URLs in layout; appends a short-lived token only when the schema is served.
 * Query parameter is added onto existing `url` / `route` strings — schema keys stay unchanged.
 */
class GridWebSso
{
    public const QUERY_PARAM = 'grid_sso';
    public const TTL_SECONDS = 1200;

    /**
     * Canonical Professional Space / Circle Connect URLs for the current host/profile.
     */
    public static function defaultCardUrls(): array
    {
        return array(
            'professional-space' => self::publicWebUrl('grid'),
            'circle-connect' => self::publicWebUrl('grid-chat'),
        );
    }

    public static function publicWebUrl(string $path): string
    {
        $path = trim($path, '/');
        if (function_exists('site_url')) {
            try {
                helper('url');
                $url = site_url($path);
                if (is_string($url) && trim($url) !== '') {
                    return self::canonicalizePublicUrl(rtrim($url, '/'));
                }
            } catch (\Throwable $e) {
                // fall through to host-based URL
            }
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '' && defined('SITE_URL')) {
            return self::canonicalizePublicUrl(rtrim((string) SITE_URL, '/') . '/index.php/' . $path);
        }
        if ($host === '') {
            $host = 'efactor.tech';
            $https = true;
        }
        $hostName = preg_replace('/:\d+$/', '', $host) ?: $host;
        if (!in_array($hostName, array('localhost', '127.0.0.1', '::1'), true)) {
            $https = true;
        }

        return self::canonicalizePublicUrl(($https ? 'https' : 'http') . '://' . $host . '/index.php/' . $path);
    }

    /**
     * Force https for non-local hosts and collapse /index.php/index.php/ duplicates.
     * Phone webviews lose ?grid_sso= when http://efactor.tech redirects to https.
     */
    public static function preferHttps(string $url): string
    {
        return self::canonicalizePublicUrl($url);
    }

    public static function canonicalizePublicUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return $url;
        }

        // Collapse accidental .../index.php/index.php/... from SITE_URL + site_url().
        $url = preg_replace('#(/index\.php)+/#i', '/index.php/', $url) ?? $url;

        if (stripos($url, 'http://') !== 0) {
            return $url;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '' || in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
            return $url;
        }

        return 'https://' . substr($url, 7);
    }

    public static function resolveCardUrl(string $cardId, string $url): string
    {
        $url = self::stripToken(trim($url));
        $defaults = self::defaultCardUrls();
        $fallback = (string) ($defaults[$cardId] ?? '');
        $expected = $cardId === 'circle-connect' ? 'grid-chat' : 'grid';
        if ($fallback !== '' && self::shouldUseDefaultCardUrl($url, $expected)) {
            return self::canonicalizePublicUrl($fallback);
        }
        // Always return a clean base URL here (no stale tokens). Fresh ?grid_sso=
        // is appended only in prepareContentForClient() at serve time for WebView login.
        return self::canonicalizePublicUrl(self::stripToken($url !== '' ? $url : $fallback));
    }

    public static function canonicalizeFeatureCardUrls(array $content): array
    {
        $cards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        foreach ($cards as $i => $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            if ($id !== 'professional-space' && $id !== 'circle-connect') {
                continue;
            }
            $resolved = self::resolveCardUrl($id, (string) ($card['url'] ?? $card['route'] ?? ''));
            $cards[$i]['url'] = $resolved;
            $cards[$i]['route'] = $resolved;
        }
        $content['featureCards'] = $cards;
        return $content;
    }

    public static function prepareContentForClient(array $content, string $ssoToken = ''): array
    {
        $content = self::canonicalizeFeatureCardUrls($content);
        $ssoToken = trim($ssoToken);
        if ($ssoToken === '') {
            return $content;
        }

        $cards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        foreach ($cards as $i => $card) {
            if (!is_array($card)) {
                continue;
            }
            $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
            if ($url === '' || !self::isTrustedGridWebUrl($url)) {
                continue;
            }
            // Fresh short-lived token only — never persist stale tokens into layout JSON.
            $withToken = self::appendToken(self::canonicalizePublicUrl($url), $ssoToken);
            $cards[$i]['url'] = $withToken;
            $cards[$i]['route'] = $withToken;
        }
        $content['featureCards'] = $cards;
        return $content;
    }

    public static function issue(int $userId): string
    {
        $userId = max(0, $userId);
        if ($userId <= 0) {
            return '';
        }

        $payload = $userId . '.' . (string) (time() + self::TTL_SECONDS) . '.' . bin2hex(random_bytes(8));
        $sig = hash_hmac('sha256', $payload, self::resolveSecret(), true);
        return self::b64url($payload) . '.' . self::b64url($sig);
    }

    public static function verify(string $token): ?int
    {
        $token = trim($token);
        if ($token === '' || strlen($token) > 512) {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        $payload = self::b64urlDecode($parts[0]);
        $sig = self::b64urlDecode($parts[1]);
        if ($payload === null || $sig === null || $sig === '') {
            return null;
        }

        $expected = hash_hmac('sha256', $payload, self::resolveSecret(), true);
        if (!hash_equals($expected, $sig)) {
            return null;
        }

        $bits = explode('.', $payload);
        if (count($bits) !== 3) {
            return null;
        }

        $userId = (int) $bits[0];
        $exp = (int) $bits[1];
        if ($userId <= 0 || $exp < time()) {
            return null;
        }

        return $userId;
    }

    public static function establishSession(int $userId): bool
    {
        $userId = max(0, $userId);
        if ($userId <= 0) {
            return false;
        }

        try {
            $session = session();
            $current = (int) ($session->get('user_id') ?? 0);
            if ($current === $userId) {
                return true;
            }

            $db = \Config\Database::connect();
            $usersTable = $db->prefixTable('users');
            $row = $db->table($usersTable)
                ->select('id, user_type')
                ->where('id', $userId)
                ->where('deleted', 0)
                ->where('status', 'active')
                ->where('disable_login', 0)
                ->get()
                ->getRow();
            if (!$row) {
                return false;
            }

            // App handoff is the source of truth. A leftover webview cookie
            // for someone else must not block this login.
            if ($current > 0 && $current !== $userId) {
                $session->regenerate(true);
            }

            $session->set('user_id', (int) $row->id);
            $session->set('type', strtolower((string) ($row->user_type ?? '')));
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Grid web SSO session failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Establish session from ?grid_sso= without redirect (safe inside constructors).
     */
    public static function consumeIncomingToken(): bool
    {
        $token = trim((string) ($_GET[self::QUERY_PARAM] ?? ''));
        if ($token === '') {
            return false;
        }
        $userId = self::verify($token);
        if ($userId === null) {
            return false;
        }
        return self::establishSession($userId);
    }

    public static function appendToken(string $url, string $token): string
    {
        $url = self::stripToken($url);
        $token = trim($token);
        if ($url === '' || $token === '') {
            return $url;
        }
        $parts = parse_url($url);
        if (!is_array($parts)) {
            return $url;
        }
        $query = array();
        if (!empty($parts['query'])) {
            parse_str((string) $parts['query'], $query);
        }
        $query[self::QUERY_PARAM] = $token;
        return self::buildUrl($parts, $query);
    }

    public static function stripToken(string $url): string
    {
        $url = trim($url);
        if ($url === '' || strpos($url, self::QUERY_PARAM) === false) {
            return $url;
        }
        $parts = parse_url($url);
        if (!is_array($parts)) {
            return $url;
        }
        $query = array();
        if (!empty($parts['query'])) {
            parse_str((string) $parts['query'], $query);
        }
        unset($query[self::QUERY_PARAM]);
        return self::buildUrl($parts, $query);
    }

    public static function requestUrlWithoutToken($request = null): string
    {
        try {
            if ($request && method_exists($request, 'getUri')) {
                $uri = $request->getUri();
                $query = $uri->getQuery();
                $params = array();
                if ($query !== '') {
                    parse_str($query, $params);
                }
                unset($params[self::QUERY_PARAM]);
                $uri = $uri->setQuery(http_build_query($params));
                return (string) $uri;
            }
        } catch (\Throwable $e) {
            // fall through
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
            $scheme = 'https';
        }
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $path = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $qPos = strpos($path, '?');
        $queryString = '';
        if ($qPos !== false) {
            $queryString = substr($path, $qPos + 1);
            $path = substr($path, 0, $qPos);
        }
        $params = array();
        if ($queryString !== '') {
            parse_str($queryString, $params);
        }
        unset($params[self::QUERY_PARAM]);
        $url = $scheme . '://' . $host . $path;
        if ($params !== array()) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    public static function isTrustedGridWebUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        $parts = parse_url($url);
        if (!is_array($parts)) {
            return false;
        }

        $path = (string) ($parts['path'] ?? '');
        if ($path === '' && strpos($url, '/') === 0) {
            $path = $url;
        }
        if (!self::pathIsGridWeb($path)) {
            return false;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host === '') {
            return true;
        }

        $current = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $currentHost = $current !== '' && strpos($current, ':') !== false
            ? substr($current, 0, (int) strpos($current, ':'))
            : $current;
        $allowed = array();
        if ($currentHost !== '') {
            $allowed[] = $currentHost;
        }
        if (in_array($currentHost, array('localhost', '127.0.0.1', '::1'), true) || $currentHost === '') {
            $allowed = array_merge($allowed, array('localhost', '127.0.0.1'));
        }
        $allowed = array_merge($allowed, array('efactor.tech', 'www.efactor.tech'));

        return in_array($host, $allowed, true);
    }

    private static function shouldUseDefaultCardUrl(string $url, string $expectedPath): bool
    {
        if ($url === '' || self::isPrivateOrBlankUrl($url) || self::isBareSiteRoot($url)) {
            return true;
        }
        if (self::isSameAppHost($url) && !self::urlPointsToPath($url, $expectedPath)) {
            return true;
        }
        return false;
    }

    private static function isBareSiteRoot(string $url): bool
    {
        $parts = parse_url($url);
        if (!is_array($parts)) {
            return false;
        }
        $path = strtolower(trim((string) ($parts['path'] ?? ''), '/'));
        return $path === '' || $path === 'index.php';
    }

    private static function isSameAppHost(string $url): bool
    {
        $parts = parse_url($url);
        if (!is_array($parts)) {
            return false;
        }
        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host === '') {
            return true;
        }
        $current = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if (strpos($current, ':') !== false) {
            $current = substr($current, 0, (int) strpos($current, ':'));
        }
        if (in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
            return $current === '' || in_array($current, array('localhost', '127.0.0.1', '::1'), true);
        }
        if ($current === '' || $current === $host) {
            return true;
        }
        $aliases = array(
            'efactor.tech' => array('www.efactor.tech'),
            'www.efactor.tech' => array('efactor.tech'),
        );
        return isset($aliases[$current]) && in_array($host, $aliases[$current], true);
    }

    private static function urlPointsToPath(string $url, string $route): bool
    {
        $parts = parse_url($url);
        $path = '/' . ltrim(strtolower((string) ($parts['path'] ?? '')), '/');
        $route = strtolower($route);
        return (bool) preg_match('#/(index\.php/)?' . preg_quote($route, '#') . '(/|$)#', $path);
    }

    public static function isPrivateOrBlankUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return true;
        }
        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['host'])) {
            return false;
        }
        $host = strtolower((string) $parts['host']);
        if ($host === 'localhost' || $host === '::1') {
            return true;
        }
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::isPrivateIp($host);
        }
        return false;
    }

    private static function isPrivateIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    private static function pathIsGridWeb(string $path): bool
    {
        $path = '/' . ltrim(strtolower($path), '/');
        if (strpos($path, 'grid-dashboard') !== false) {
            return false;
        }
        return (bool) preg_match('#/(index\.php/)?grid(-chat)?(/|$)#', $path);
    }

    private static function buildUrl(array $parts, array $query): string
    {
        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $user = $parts['user'] ?? '';
        $pass = isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $auth = ($user !== '' || $pass !== '') ? $user . $pass . '@' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '';
        $queryString = $query !== array() ? '?' . http_build_query($query) : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        return $scheme . $auth . $host . $port . $path . $queryString . $fragment;
    }

    private static function resolveSecret(): string
    {
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '' && defined('SITE_URL')) {
            $host = strtolower((string) parse_url((string) SITE_URL, PHP_URL_HOST));
        }
        if ($host === '') {
            $host = 'efactor.tech';
        }
        if (strpos($host, ':') !== false) {
            $host = substr($host, 0, (int) strpos($host, ':'));
        }
        if (in_array($host, array('127.0.0.1', '::1'), true)) {
            $host = 'localhost';
        }
        if ($host === 'www.efactor.tech') {
            $host = 'efactor.tech';
        }

        // Host-only secret so CI controllers and standalone grid_api_creation.php
        // issue/verify the same token without depending on CI config().
        return hash('sha256', 'efactor-grid-web-sso-v1|' . $host, true);
    }

    private static function b64url(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function b64urlDecode(string $value): ?string
    {
        $padded = strtr($value, '-_', '+/');
        $pad = strlen($padded) % 4;
        if ($pad > 0) {
            $padded .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode($padded, true);
        return $decoded === false ? null : $decoded;
    }
}
