<?php

namespace App\Services;

/**
 * Schema fetch, packaging, and reload for device UI documents.
 *
 * Large schemas are stored as several JSON part files plus a small manifest in
 * device_ui_schemas.schema_json. Callers still receive one schema array with the
 * same keys. The database value stays small, so MySQL string escaping does not
 * allocate another full copy of the document.
 */
class DeviceUiSchemaGenerator
{
    const STORAGE_VERSION = 'split-v1';
    const INLINE_LIMIT = 262144;
    const PART_LIMIT = 32768;

    private static $lastError = '';
    private static $resolvedDirs = [];
    private static $attemptedDirs = [];

    public static function lastError()
    {
        return self::$lastError;
    }

    public static function raiseMemoryLimit()
    {
        $current = self::memoryLimitBytes(ini_get('memory_limit'));
        if ($current !== -1 && $current < 2147483648) {
            @ini_set('memory_limit', '2048M');
        }
    }

    public static function isSplitPayload($raw)
    {
        if (!is_string($raw) || $raw === '') {
            return false;
        }

        return strpos($raw, '"schemaStorage"') !== false && strpos($raw, self::STORAGE_VERSION) !== false;
    }

    /**
     * Turn a stored schema_json value back into the full schema array.
     *
     * @param string $raw
     * @return array|null
     */
    public static function decode($raw)
    {
        self::raiseMemoryLimit();
        if (!is_string($raw)) {
            return null;
        }
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        unset($raw);
        if (!is_array($decoded)) {
            return null;
        }
        if (($decoded['schemaStorage'] ?? '') !== self::STORAGE_VERSION || empty($decoded['parts']) || !is_array($decoded['parts'])) {
            return $decoded;
        }

        $schema = (isset($decoded['document']) && is_array($decoded['document'])) ? $decoded['document'] : [];
        $partFiles = [];
        foreach ($decoded['parts'] as $file) {
            if (is_string($file) && $file !== '') {
                $partFiles[] = $file;
            }
        }
        $dir = self::absoluteDir(isset($decoded['storageDir']) ? (string) $decoded['storageDir'] : '', $partFiles);
        foreach ($decoded['parts'] as $key => $file) {
            if (!is_string($key) || !is_string($file)) {
                continue;
            }
            if ($dir === '') {
                break;
            }
            $path = $dir . DIRECTORY_SEPARATOR . basename($file);
            if (!is_file($path)) {
                continue;
            }
            $partRaw = file_get_contents($path);
            if (!is_string($partRaw) || $partRaw === '') {
                unset($partRaw);
                continue;
            }
            $part = json_decode($partRaw, true);
            unset($partRaw);
            if (json_last_error() === JSON_ERROR_NONE) {
                $schema[$key] = $part;
            }
            unset($part);
        }
        unset($decoded);

        return $schema;
    }

    /**
     * Package a schema for device_ui_schemas.schema_json.
     * Oversized top-level keys are written to part files and omitted from the SQL value.
     *
     * @param string $modelno
     * @param string $nodeId
     * @param array  $schema
     * @return string
     */
    public static function package($modelno, $nodeId, array $schema)
    {
        self::raiseMemoryLimit();
        self::$lastError = '';
        self::$attemptedDirs = [];
        @set_time_limit(300);
        $document = [];
        $parts = [];
        $relativeDir = self::relativeDir($modelno, $nodeId);

        foreach ($schema as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json !== false && strlen($json) >= self::PART_LIMIT) {
                if (!self::writePart($relativeDir, $key, $json)) {
                    if (self::$lastError === '') {
                        self::$lastError = 'Could not write schema part "' . $key . '". The schema storage directory is not writable.';
                    }
                    unset($json);
                    return '';
                }
                $parts[$key] = self::partName($key);
                unset($json);
                continue;
            }
            unset($json);
            $document[$key] = $value;
        }

        $guard = 0;
        while ($guard < 80) {
            $guard++;
            $payload = $parts === [] ? $document : self::manifest($schema, $relativeDir, $parts, $document);
            $packed = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            unset($payload);
            if ($packed === false) {
                self::$lastError = 'Schema JSON could not be encoded.';
                return '';
            }
            if (strlen($packed) <= self::INLINE_LIMIT) {
                return $packed;
            }
            unset($packed);

            $spillKey = self::largestDocumentKey($document);
            if ($spillKey === null) {
                break;
            }
            $spillJson = json_encode($document[$spillKey], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($spillJson === false) {
                self::$lastError = 'Schema JSON could not be encoded.';
                return '';
            }
            if (!self::writePart($relativeDir, $spillKey, $spillJson)) {
                if (self::$lastError === '') {
                    self::$lastError = 'Could not write schema part "' . $spillKey . '". The schema storage directory is not writable.';
                }
                unset($spillJson);
                return '';
            }
            $parts[$spillKey] = self::partName($spillKey);
            unset($document[$spillKey], $spillJson);
        }

        self::$lastError = 'Schema is still too large to store in MySQL after splitting.';
        return '';
    }

    private static function manifest(array $schema, $relativeDir, array $parts, array $document)
    {
        return [
            'schemaStorage' => self::STORAGE_VERSION,
            'schemaType' => isset($schema['schemaType']) ? $schema['schemaType'] : 'device-ui',
            'schemaVersion' => isset($schema['schemaVersion']) ? $schema['schemaVersion'] : '1.0.0',
            'storageDir' => $relativeDir,
            'parts' => $parts,
            'document' => $document,
        ];
    }

    private static function largestDocumentKey(array $document)
    {
        $largestKey = null;
        $largestLen = 0;
        foreach ($document as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                continue;
            }
            $len = strlen($json);
            unset($json);
            if ($len > $largestLen) {
                $largestLen = $len;
                $largestKey = $key;
            }
        }

        return $largestKey;
    }

    private static function writePart($relativeDir, $key, $json)
    {
        $dir = self::ensureDir($relativeDir);
        if ($dir === '') {
            $extra = self::$attemptedDirs !== [] ? ' Tried: ' . implode('; ', self::$attemptedDirs) . '.' : '';
            self::$lastError = 'Could not write schema part "' . $key . '". The schema storage directory is not writable.' . $extra;
            return false;
        }
        $path = $dir . DIRECTORY_SEPARATOR . self::partName($key);
        if (is_file($path) && !@is_writable($path)) {
            @chmod($path, 0664);
            if (!@is_writable($path)) {
                @chmod($path, 0666);
            }
            if (!@is_writable($path) && !@unlink($path)) {
                self::$lastError = 'Could not write schema part "' . $key . '". The existing schema file is not writable.';
                return false;
            }
        }
        if (!self::writeContents($path, $json)) {
            $detail = error_get_last();
            $why = is_array($detail) ? trim((string) ($detail['message'] ?? '')) : '';
            self::$lastError = 'Could not write schema part "' . $key . '".'
                . ($why !== '' ? ' ' . $why : ' The schema storage directory is not writable.');
            return false;
        }
        self::guardSchemaDir($dir);

        return true;
    }

    private static function writeContents($path, $contents)
    {
        $expected = strlen($contents);
        $tmp = $path . '.tmp';
        $handle = @fopen($tmp, 'wb');
        if ($handle === false) {
            $written = @file_put_contents($path, $contents);
            $size = @filesize($path);

            return $written !== false && $size === $expected;
        }

        $offset = 0;
        while ($offset < $expected) {
            $written = fwrite($handle, substr($contents, $offset, 1048576));
            if ($written === false || $written < 1) {
                fclose($handle);
                @unlink($tmp);
                return false;
            }
            $offset += $written;
        }
        fflush($handle);
        fclose($handle);

        if (is_file($path)) {
            @chmod($path, 0666);
            @unlink($path);
        }
        if (!@rename($tmp, $path)) {
            if (!@copy($tmp, $path)) {
                @unlink($tmp);
                return false;
            }
            @unlink($tmp);
        }
        clearstatcache(true, $path);
        $size = @filesize($path);

        return $size === $expected;
    }

    private static function partName($key)
    {
        $safe = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $key);
        if (!is_string($safe) || $safe === '') {
            $safe = 'part_' . substr(sha1((string) $key), 0, 12);
        }

        return $safe . '.json';
    }

    private static function relativeDir($modelno, $nodeId)
    {
        return 'device_ui_schemas/' . self::token($modelno) . '/' . self::token($nodeId);
    }

    private static function token($value)
    {
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '_', trim((string) $value));
        if (!is_string($safe) || $safe === '') {
            return '_default';
        }

        return substr($safe, 0, 120);
    }

    private static function ensureDir($relativeDir)
    {
        $relative = self::safeRelative($relativeDir);
        if ($relative === '') {
            return '';
        }
        if (isset(self::$resolvedDirs[$relative]) && is_dir(self::$resolvedDirs[$relative]) && self::probeWritable(self::$resolvedDirs[$relative])) {
            return self::$resolvedDirs[$relative];
        }

        $suffix = str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $tried = [];
        foreach (self::rootCandidates() as $root) {
            $dir = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $suffix;
            $tried[] = $dir;
            if (!self::makeWritableDir($dir) || !self::probeWritable($dir)) {
                continue;
            }
            self::guardSchemaDir($dir);
            self::$resolvedDirs[$relative] = $dir;
            self::$attemptedDirs = [];

            return $dir;
        }

        self::$attemptedDirs = $tried;

        return '';
    }

    private static function absoluteDir($relative, array $partFiles = [])
    {
        $relative = self::safeRelative($relative);
        if ($relative === '') {
            return '';
        }

        $suffix = str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $best = '';
        $bestMtime = -1;
        foreach (self::rootCandidates() as $root) {
            $path = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $suffix;
            if (!is_dir($path)) {
                continue;
            }
            $mtime = self::newestPartMtime($path, $partFiles);
            if ($mtime > $bestMtime) {
                $bestMtime = $mtime;
                $best = $path;
            }
        }
        if ($best !== '') {
            return $best;
        }

        $roots = self::rootCandidates();
        if (empty($roots[0])) {
            return '';
        }

        return rtrim($roots[0], '/\\') . DIRECTORY_SEPARATOR . $suffix;
    }

    private static function safeRelative($relative)
    {
        $relative = str_replace('\\', '/', (string) $relative);
        $relative = str_replace("\0", '', $relative);
        $relative = trim($relative, '/');
        if ($relative === '' || strpos($relative, '..') !== false || strpos($relative, 'device_ui_schemas/') !== 0) {
            return '';
        }

        return $relative;
    }

    private static function makeWritableDir($dir)
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
        }
        if (!is_dir($dir)) {
            return false;
        }
        if (!@is_writable($dir)) {
            @chmod($dir, 0775);
        }
        if (!@is_writable($dir)) {
            @chmod($dir, 0777);
        }

        return true;
    }

    private static function probeWritable($dir)
    {
        $probe = $dir . DIRECTORY_SEPARATOR . '.schema_write_probe';
        $ok = @file_put_contents($probe, 'ok') !== false;
        if ($ok) {
            @unlink($probe);
        }

        return $ok;
    }

    private static function newestPartMtime($dir, array $partFiles)
    {
        $newest = -1;
        foreach ($partFiles as $file) {
            if (!is_string($file) || $file === '') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . basename($file);
            if (!is_file($path)) {
                continue;
            }
            $mtime = @filemtime($path);
            if ($mtime !== false && $mtime > $newest) {
                $newest = $mtime;
            }
        }

        return $newest;
    }

    /**
     * Schema part files must not be downloaded from the web root.
     * writable/ and uploads/ both sit under the site on this project.
     */
    private static function guardSchemaDir($nodeDir)
    {
        $normalized = str_replace('\\', '/', (string) $nodeDir);
        $marker = '/device_ui_schemas/';
        $pos = strpos($normalized, $marker);
        if ($pos === false) {
            return;
        }
        $base = rtrim(str_replace('/', DIRECTORY_SEPARATOR, substr($normalized, 0, $pos + strlen('/device_ui_schemas'))), '/\\');
        if (!is_dir($base)) {
            return;
        }
        $htaccess = $base . DIRECTORY_SEPARATOR . '.htaccess';
        if (!is_file($htaccess)) {
            @file_put_contents(
                $htaccess,
                "Options -Indexes\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"
            );
        }
        $index = $base . DIRECTORY_SEPARATOR . 'index.html';
        if (!is_file($index)) {
            @file_put_contents($index, '');
        }
    }

    private static function rootCandidates()
    {
        $roots = [];
        if (defined('WRITEPATH')) {
            $roots[] = WRITEPATH;
        }
        if (defined('ROOTPATH')) {
            $roots[] = rtrim((string) ROOTPATH, '/\\') . DIRECTORY_SEPARATOR . 'writable';
        }

        $appDir = dirname(__DIR__);
        $roots[] = dirname($appDir) . DIRECTORY_SEPARATOR . 'writable';
        $roots[] = $appDir . DIRECTORY_SEPARATOR . 'writable';

        $public = [];
        if (defined('FCPATH')) {
            $public[] = rtrim((string) FCPATH, '/\\');
        }
        $docRoot = rtrim(str_replace('\\', '/', (string) (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '')), '/');
        if ($docRoot !== '') {
            $public[] = $docRoot;
        }
        if (defined('ROOTPATH')) {
            $public[] = rtrim((string) ROOTPATH, '/\\');
        }
        $cwd = getcwd();
        if (is_string($cwd) && $cwd !== '') {
            $public[] = rtrim($cwd, '/\\');
        }
        foreach ($public as $base) {
            if ($base === '' || $base === '/' || preg_match('/^[A-Za-z]:$/', $base)) {
                continue;
            }
            $roots[] = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $base), '/\\') . DIRECTORY_SEPARATOR . 'uploads';
        }

        $unique = [];
        foreach ($roots as $root) {
            $root = rtrim((string) $root, '/\\');
            if ($root === '' || $root === '/' || $root === '\\' || strlen($root) < 3 || isset($unique[$root])) {
                continue;
            }
            $unique[$root] = $root;
        }

        return array_values($unique);
    }

    private static function memoryLimitBytes($limit)
    {
        $limit = trim((string) $limit);
        if ($limit === '' || $limit === '-1') {
            return -1;
        }
        $unit = strtolower(substr($limit, -1));
        $number = (int) $limit;
        if ($unit === 'g') {
            return $number * 1073741824;
        }
        if ($unit === 'm') {
            return $number * 1048576;
        }
        if ($unit === 'k') {
            return $number * 1024;
        }

        return (int) $limit;
    }
}
