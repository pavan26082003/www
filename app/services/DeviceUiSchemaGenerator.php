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
        $dir = self::absoluteDir(isset($decoded['storageDir']) ? (string) $decoded['storageDir'] : '');
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
        $document = [];
        $parts = [];
        $relativeDir = self::relativeDir($modelno, $nodeId);

        foreach ($schema as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json !== false && strlen($json) >= self::PART_LIMIT && self::writePart($relativeDir, $key, $json)) {
                $parts[$key] = self::partName($key);
                unset($json);
                continue;
            }
            unset($json);
            $document[$key] = $value;
        }

        if ($parts === []) {
            $inline = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $inline === false ? '{}' : $inline;
        }

        $guard = 0;
        while ($guard < 40) {
            $guard++;
            $manifest = self::manifest($schema, $relativeDir, $parts, $document);
            $packed = json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            unset($manifest);
            if ($packed === false) {
                break;
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
            if ($spillJson === false || !self::writePart($relativeDir, $spillKey, $spillJson)) {
                unset($spillJson);
                break;
            }
            $parts[$spillKey] = self::partName($spillKey);
            unset($document[$spillKey], $spillJson);
        }

        $fallback = json_encode(self::manifest($schema, $relativeDir, $parts, $document), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (is_string($fallback) && $fallback !== '') {
            return $fallback;
        }

        $inline = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $inline === false ? '{}' : $inline;
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
            return false;
        }
        $path = $dir . DIRECTORY_SEPARATOR . self::partName($key);
        $written = file_put_contents($path, $json, LOCK_EX);

        return $written !== false;
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
        $dir = self::absoluteDir($relativeDir);
        if ($dir === '') {
            return '';
        }
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return '';
        }

        return $dir;
    }

    private static function absoluteDir($relative)
    {
        $relative = str_replace('\\', '/', (string) $relative);
        $relative = str_replace("\0", '', $relative);
        $relative = trim($relative, '/');
        if ($relative === '' || strpos($relative, '..') !== false || strpos($relative, 'device_ui_schemas/') !== 0) {
            return '';
        }

        $suffix = str_replace('/', DIRECTORY_SEPARATOR, $relative);
        foreach (self::rootCandidates() as $root) {
            $path = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $suffix;
            if (is_dir($path)) {
                return $path;
            }
        }

        $roots = self::rootCandidates();
        if (empty($roots[0])) {
            return '';
        }

        return rtrim($roots[0], '/\\') . DIRECTORY_SEPARATOR . $suffix;
    }

    private static function rootCandidates()
    {
        $roots = [];
        if (defined('WRITEPATH')) {
            $roots[] = WRITEPATH;
        }

        $appDir = dirname(__DIR__);
        $roots[] = dirname($appDir) . DIRECTORY_SEPARATOR . 'writable';
        $roots[] = $appDir . DIRECTORY_SEPARATOR . 'writable';

        $unique = [];
        foreach ($roots as $root) {
            $root = rtrim((string) $root, '/\\');
            if ($root === '' || isset($unique[$root])) {
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
