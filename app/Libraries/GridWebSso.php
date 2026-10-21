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
    /** Reusable until this time. Re-issued on every grid schema load. Not single-use: the WebView retries the URL. */
    public const TTL_SECONDS = 604800;
    public const ENTRY_PATH = 'app-web-entry/open';

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
            $id = (string) ($card['id'] ?? '');
            if ($id !== 'professional-space' && $id !== 'circle-connect') {
                continue;
            }
            // Keep the real module path in the URL. The phone decides which screen
            // to open from that path: /grid is Professional Space, /grid-chat is
            // Circle Connect. A shared entry path made both taps open Professional Space.
            $withToken = self::appendToken((string) ($cards[$i]['url'] ?? ''), $ssoToken);
            $cards[$i]['url'] = $withToken;
            $cards[$i]['route'] = $withToken;
        }
        $content['featureCards'] = $cards;
        return $content;
    }

    /**
     * WebView URL for a card tap. Identity is the grid_sso pass; destination is chosen server-side.
     */
    public static function entryUrl(string $cardId, string $token): string
    {
        $to = $cardId === 'circle-connect' ? 'circle-connect' : 'professional-space';
        $base = self::publicWebUrl(self::ENTRY_PATH) . '?to=' . rawurlencode($to);
        return self::appendToken($base, $token);
    }

    /**
     * Canonical Professional Space or Circle Connect URL for a known screen name.
     */
    public static function entryTarget(string $to): string
    {
        $module = self::moduleKeyFromToken($to);
        if ($module === '') {
            $module = self::moduleKeyFromUrl($to);
        }
        return self::urlForModule($module);
    }

    /**
     * Screen asked for on this request.
     * target=grid / redirect_url=/index.php/grid → Professional Space.
     * target=chat / redirect_url=/index.php/grid-chat → Circle Connect.
     * Empty when the request does not name a screen, so nothing is forced to Circle Connect.
     */
    public static function requestedModule(?array $query = null): string
    {
        $query = self::destinationQuery($query);
        $target = strtolower(trim((string) ($query['target'] ?? $query['to'] ?? '')));
        $fromTarget = self::moduleKeyFromToken($target);
        if ($fromTarget !== '') {
            return $fromTarget;
        }

        $redirect = trim((string) ($query['redirect_url'] ?? $query['redirect'] ?? ''));
        return self::moduleKeyFromUrl($redirect);
    }

    public static function requestedDestinationUrl(?array $query = null): string
    {
        return self::urlForModule(self::requestedModule($query));
    }

    /**
     * Relative path the existing sign-in return already understands.
     * grid → Professional Space. grid-chat → Circle Connect.
     */
    public static function requestedRelativePath(?array $query = null): string
    {
        $module = self::requestedModule($query);
        if ($module === 'professional') {
            return 'grid';
        }
        if ($module === 'circle') {
            return 'grid-chat';
        }
        return '';
    }

    private static function destinationQuery(?array $query): array
    {
        $merged = array();
        foreach (array($_GET, $_REQUEST) as $source) {
            if (!is_array($source)) {
                continue;
            }
            foreach (array('target', 'to', 'redirect_url', 'redirect') as $key) {
                if (isset($source[$key]) && !isset($merged[$key])) {
                    $merged[$key] = $source[$key];
                }
            }
        }
        if (is_array($query)) {
            foreach (array('target', 'to', 'redirect_url', 'redirect') as $key) {
                if (isset($query[$key]) && trim((string) $query[$key]) !== '') {
                    $merged[$key] = $query[$key];
                }
            }
        }
        return $merged;
    }

    /**
     * Which module the current URL path already is. Empty for sign-in and the entry page.
     */
    public static function moduleKeyFromPath(string $path): string
    {
        $path = strtolower(str_replace('\\', '/', $path));
        if (strpos($path, 'app-web-entry') !== false || strpos($path, 'grid-dashboard') !== false) {
            return '';
        }
        return self::moduleKeyFromUrl($path);
    }

    public static function moduleKeyFromToken(string $value): string
    {
        $value = strtolower(trim($value));
        if (in_array($value, array('chat', 'circle', 'circle-connect', 'grid-chat', 'grid_chat'), true)) {
            return 'circle';
        }
        if (in_array($value, array('grid', 'professional', 'professional-space'), true)) {
            return 'professional';
        }
        return '';
    }

    public static function moduleKeyFromUrl(string $url): string
    {
        $url = strtolower(trim($url));
        if ($url === '') {
            return '';
        }
        if (strpos($url, 'grid-chat') !== false || strpos($url, 'grid_chat') !== false) {
            return 'circle';
        }
        if (strpos($url, 'grid-dashboard') !== false || strpos($url, 'app-web-entry') !== false) {
            return '';
        }

        $path = $url;
        if (strpos($url, '://') !== false) {
            $parts = parse_url($url);
            $path = is_array($parts) ? (string) ($parts['path'] ?? '') : '';
        }
        $path = '/' . ltrim($path, '/');
        if (preg_match('~/(index\.php/)?grid([/?#]|$)~', $path)) {
            return 'professional';
        }
        return '';
    }

    public static function urlForModule(string $module): string
    {
        if ($module !== 'circle' && $module !== 'professional') {
            return '';
        }
        $defaults = self::defaultCardUrls();
        $key = $module === 'circle' ? 'circle-connect' : 'professional-space';
        return (string) ($defaults[$key] ?? '');
    }

    /**
     * Studio / saved layout should keep the real module URL, not the entry wrapper.
     */
    public static function studioCardUrl(string $cardId, string $url): string
    {
        $url = trim($url);
        if ($url !== '' && stripos($url, 'app-web-entry') !== false) {
            return self::entryTarget($cardId === 'circle-connect' ? 'circle-connect' : 'professional-space');
        }
        return self::stripToken(self::resolveCardUrl($cardId, $url));
    }

    public static function handoffPage(string $url): string
    {
        $href = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $jsUrl = json_encode($url, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        if (!is_string($jsUrl)) {
            $jsUrl = '""';
        }

        return '<!DOCTYPE html><html><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<meta http-equiv="refresh" content="0;url=' . $href . '">'
            . '<title>Loading</title>'
            . '<style>'
            . 'html,body{margin:0;height:100%;background:#f7f8fc}'
            . 'body{display:flex;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#6b7280}'
            . '.box{text-align:center}'
            . '.spin{width:36px;height:36px;margin:0 auto 14px;border-radius:50%;border:3px solid #e5e7eb;border-top-color:#5b6cff;animation:spin .7s linear infinite}'
            . '@keyframes spin{to{transform:rotate(360deg)}}'
            . 'p{margin:0;font-size:14px;font-weight:600}'
            . '</style></head><body>'
            . '<div class="box" role="status" aria-live="polite"><div class="spin"></div><p>Loading</p></div>'
            . '<script>location.replace(' . $jsUrl . ');</script>'
            . '</body></html>';
    }

    public static function retryPage(): string
    {
        return '<!DOCTYPE html><html><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>Open from the app</title></head><body>'
            . '<p>Go back to Grid and open this again.</p>'
            . '</body></html>';
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
            self::useLongWebSession();
            $session = session();
            $current = (int) ($session->get('user_id') ?? 0);
            if ($current === $userId) {
                self::persistWebCookie();
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
            self::persistWebCookie();
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Grid web SSO session failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Apply only before the session starts. Keeps this handoff signed in for a week
     * and stops the session id from rotating on the entry request.
     */
    public static function useLongWebSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        try {
            $app = config('App');
            if ($app) {
                $app->sessionExpiration = self::TTL_SECONDS;
                $app->sessionTimeToUpdate = self::TTL_SECONDS;
            }
        } catch (\Throwable $e) {
            // The default session lifetime still signs the user in for this request.
        }
    }

    /**
     * Repeat the session cookie with the handoff lifetime so the WebView keeps it
     * after the entry page continues into Professional Space or Circle Connect.
     */
    public static function persistWebCookie(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        $sid = session_id();
        if (!is_string($sid) || $sid === '') {
            return;
        }

        $name = 'ci_session';
        $path = '/';
        $domain = '';
        $secure = false;
        $sameSite = 'Lax';
        try {
            $app = config('App');
            if ($app) {
                if (!empty($app->sessionCookieName)) {
                    $name = (string) $app->sessionCookieName;
                }
                $path = (string) ($app->cookiePath ?? $path);
                $domain = (string) ($app->cookieDomain ?? '');
                $secure = (bool) ($app->cookieSecure ?? false);
                $sameSite = (string) ($app->cookieSameSite ?? $sameSite);
            }
        } catch (\Throwable $e) {
            // Fall back to the same cookie flags the rest of the app uses.
        }

        if ($path === '') {
            $path = '/';
        }
        if (!in_array(strtolower($sameSite), array('', 'lax', 'strict', 'none'), true)) {
            $sameSite = 'Lax';
        }

        $params = array(
            'expires' => time() + self::TTL_SECONDS,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => true,
        );
        if ($sameSite !== '') {
            $params['samesite'] = $sameSite;
        }
        setcookie($name, $sid, $params);
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
