<?php
/**
 * Shared helpers for schema HTTP responses (Phase A):
 * - gzip when the client advertises Accept-Encoding
 * - file-backed response body cache (same JSON shape)
 *
 * Safe for older app versions: body bytes are unchanged; only encoding/headers differ.
 */

if (!function_exists('efactor_schema_revision')) {
    /**
     * Revision the app compares with <=. A floor above the cached Get Status
     * schemaRevision forces one refresh when updated_at itself did not move.
     *
     * @param string $updatedAt
     * @return int
     */
    function efactor_schema_revision($updatedAt)
    {
        $updatedAt = trim((string) $updatedAt);
        if ($updatedAt === '') {
            return 0;
        }
        $ts = strtotime($updatedAt);
        if (!$ts) {
            return 0;
        }
        $revision = (int) $ts;
        // 2026-11-23 20:06:40 UTC. Above the bad cached revision 1795457179.
        $floor = 1795464400;
        if ($revision < $floor) {
            return $floor;
        }

        return $revision;
    }
}

if (!function_exists('efactor_schema_client_accepts_gzip')) {
    /**
     * @return bool
     */
    function efactor_schema_client_accepts_gzip()
    {
        $header = isset($_SERVER['HTTP_ACCEPT_ENCODING'])
            ? (string) $_SERVER['HTTP_ACCEPT_ENCODING']
            : '';
        if ($header === '') {
            return false;
        }

        // Respect explicit identity-only clients.
        if (preg_match('/\bgzip\b/i', $header) !== 1) {
            return false;
        }
        if (preg_match('/\bgzip\s*;\s*q=0(?:\.0+)?\b/i', $header) === 1) {
            return false;
        }

        return function_exists('gzencode');
    }
}

if (!function_exists('efactor_schema_cache_dir')) {
    /**
     * @return string
     */
    function efactor_schema_cache_dir()
    {
        $candidates = array(
            dirname(__DIR__) . '/app/writable/cache/schema_http',
            __DIR__ . '/cache/schema_http',
            rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'efactor_schema_http',
        );

        foreach ($candidates as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            if (is_dir($dir) && is_writable($dir)) {
                return $dir;
            }
        }

        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('efactor_schema_cache_get')) {
    /**
     * @param string $key
     * @param int $ttlSeconds
     * @return string|null Raw JSON body
     */
    function efactor_schema_cache_get($key, $ttlSeconds = 300)
    {
        $key = preg_replace('/[^a-f0-9]/i', '', (string) $key);
        if ($key === '' || strlen($key) < 16) {
            return null;
        }

        $path = efactor_schema_cache_dir() . DIRECTORY_SEPARATOR . 'body_' . $key . '.json';
        if (!is_file($path)) {
            return null;
        }

        $mtime = @filemtime($path);
        if ($mtime === false || (time() - $mtime) > max(1, (int) $ttlSeconds)) {
            return null;
        }

        $raw = @file_get_contents($path);
        if (!is_string($raw) || $raw === '') {
            return null;
        }

        return $raw;
    }
}

if (!function_exists('efactor_schema_cache_set')) {
    /**
     * @param string $key
     * @param string $body
     * @return void
     */
    function efactor_schema_cache_set($key, $body)
    {
        $key = preg_replace('/[^a-f0-9]/i', '', (string) $key);
        if ($key === '' || !is_string($body) || $body === '') {
            return;
        }

        // Avoid caching error envelopes.
        if (strpos($body, '"status":false') !== false || strpos($body, '"success":false') !== false) {
            return;
        }

        $dir = efactor_schema_cache_dir();
        $path = $dir . DIRECTORY_SEPARATOR . 'body_' . $key . '.json';
        $tmp = $dir . DIRECTORY_SEPARATOR . 'body_' . $key . '.' . getmypid() . '.tmp';

        if (@file_put_contents($tmp, $body, LOCK_EX) === false) {
            return;
        }
        if (!@rename($tmp, $path)) {
            @unlink($tmp);
        }
    }
}

if (!function_exists('efactor_schema_cache_key')) {
    /**
     * @param array $parts
     * @return string
     */
    function efactor_schema_cache_key(array $parts)
    {
        return hash('sha256', implode('|', array_map('strval', $parts)));
    }
}

if (!function_exists('efactor_schema_emit_json_body')) {
    /**
     * Echo a JSON body, gzip-compressed when the client accepts it.
     * Sets Content-Encoding / Content-Length / Vary. Does not change JSON bytes.
     *
     * @param string $body
     * @return void
     */
    function efactor_schema_emit_json_body($body)
    {
        if (!is_string($body)) {
            $body = '';
        }

        // Drop any prior Content-Length so compressed length can replace it.
        if (!headers_sent() && function_exists('header_remove')) {
            header_remove('Content-Length');
        }

        $useGzip = efactor_schema_client_accepts_gzip();
        $out = $body;
        if ($useGzip) {
            $compressed = @gzencode($body, 6);
            if (is_string($compressed) && $compressed !== '' && strlen($compressed) < strlen($body)) {
                $out = $compressed;
                if (!headers_sent()) {
                    header('Content-Encoding: gzip');
                    header('Vary: Accept-Encoding', false);
                }
            }
        }

        if (!headers_sent()) {
            header('Content-Length: ' . strlen($out));
        }

        echo $out;
    }
}
