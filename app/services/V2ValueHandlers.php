<?php

namespace App\Services;

/**
 * V2 display-value handlers.
 *
 * Converts wire hex (+ data_type) into UI display values while leaving the wire
 * hex on `value` untouched. Used only to enrich V2 frames into the same
 * node_details shape the app already consumes.
 *
 * Dependency-free so v8 plain-PHP endpoints can require it directly.
 */
class V2ValueHandlers
{
    /**
     * Convert a wire/default value into a typed display value.
     *
     * @param mixed  $wireValue  Hex or raw default from DB / frame.value
     * @param string $dataType   Field data_type (UnsignedInt, Float, …)
     * @param mixed  $fallback   Kept when conversion is not applicable
     * @return mixed
     */
    public static function toDisplay($wireValue, $dataType = '', $fallback = null)
    {
        $dataType = self::normalizeDataType($dataType);
        $fallback = $fallback !== null ? $fallback : $wireValue;

        if (self::isEmptyValue($wireValue) && self::isEmptyValue($fallback)) {
            return $fallback;
        }

        // Already a usable human display (non-hex text / bool / number) — keep it.
        if (!self::isEmptyValue($fallback) && !self::looksLikeProtocolHex($fallback) && !is_bool($fallback)) {
            if ($dataType === 'boolean') {
                return self::toBoolean($fallback);
            }
            return self::castScalar($fallback, $dataType);
        }

        $source = !self::isEmptyValue($wireValue) ? $wireValue : $fallback;
        if (self::isEmptyValue($source)) {
            return $fallback;
        }

        if (!self::looksLikeProtocolHex($source)) {
            return self::castScalar($source, $dataType);
        }

        $hex = self::normalizeHex($source);
        if ($hex === '') {
            return $fallback;
        }

        switch ($dataType) {
            case 'boolean':
                return self::hexToBoolean($hex);

            case 'float':
                return self::hexToFloat($hex, $fallback);

            case 'double':
                return self::hexToDouble($hex, $fallback);

            case 'string':
            case 'ascii':
                return self::hexToAscii($hex, $fallback);

            case 'signedint':
            case 'int':
            case 'sint':
            case 'int8':
            case 'int16':
            case 'int32':
                return self::hexToSignedInt($hex);

            case 'unsignedint':
            case 'uint':
            case 'byte':
            case 'word':
            case 'dword':
            case 'uint8':
            case 'uint16':
            case 'uint32':
            default:
                // Numeric-ish types and unknown → unsigned int when hex.
                if ($dataType === '' || self::isNumericDataType($dataType)) {
                    return self::hexToUnsignedInt($hex);
                }
                return $fallback;
        }
    }

    /**
     * Enrich one frame field: keep value=wire, set asperui_value=display.
     */
    public static function enrichFrameField(array $field)
    {
        if (!isset($field['value']) && !isset($field['asperui_value'])) {
            return $field;
        }

        $dataType = (string) ($field['data_type'] ?? $field['dataType'] ?? $field['datatype'] ?? '');
        $wire = $field['value'] ?? null;
        $currentDisplay = $field['asperui_value'] ?? null;

        // Protocol-only slots without UI type usually drop asperui — leave alone
        // when there is no data_type and no asperui_value to fix.
        if ($dataType === '' && !array_key_exists('asperui_value', $field)) {
            return $field;
        }

        // Prefer existing human display; only convert when missing or still hex.
        if (!self::isEmptyValue($currentDisplay) && !self::looksLikeProtocolHex($currentDisplay)) {
            return $field;
        }

        $display = self::toDisplay($wire, $dataType, $currentDisplay);
        if ($display === null || $display === '') {
            return $field;
        }

        // Never put typed display onto wire value.
        $field['asperui_value'] = $display;

        return $field;
    }

    /**
     * Enrich commands/response/error lists on a frame.
     */
    public static function enrichFrame(array $frame)
    {
        foreach (['commands', 'response', 'error', 'Responses', 'Error'] as $key) {
            if (!isset($frame[$key]) || !is_array($frame[$key])) {
                continue;
            }
            foreach ($frame[$key] as $index => $field) {
                if (!is_array($field)) {
                    continue;
                }
                $frame[$key][$index] = self::enrichFrameField($field);
            }
        }

        return $frame;
    }

    /**
     * Enrich api metadata built from V2 tree (frame + ui_components defaults).
     */
    public static function enrichApiMetadata(array $api)
    {
        if (isset($api['frame']) && is_array($api['frame'])) {
            $api['frame'] = self::enrichFrame($api['frame']);
        }

        if (isset($api['ui_components']) && is_array($api['ui_components'])) {
            foreach ($api['ui_components'] as $index => $component) {
                if (!is_array($component)) {
                    continue;
                }
                $dataType = (string) ($component['data_type'] ?? '');
                if ($dataType === '' && isset($component['frame']) && is_array($component['frame'])) {
                    // Try data field type from frame when component has none.
                    $dataType = self::guessDataTypeFromFrame($component['frame']);
                }
                $current = $component['default'] ?? null;
                $wire = null;
                if (isset($component['frame']) && is_array($component['frame'])) {
                    $wire = self::findPrimaryWireValue($component['frame']);
                    $component['frame'] = self::enrichFrame($component['frame']);
                    $enrichedDisplay = self::findPrimaryDisplayValue($component['frame']);
                    if ($enrichedDisplay !== null && $enrichedDisplay !== '') {
                        $current = $enrichedDisplay;
                    }
                }
                if (self::isEmptyValue($current) || self::looksLikeProtocolHex($current)) {
                    $converted = self::toDisplay($wire !== null ? $wire : $current, $dataType, $current);
                    if ($converted !== null && $converted !== '') {
                        $component['default'] = $converted;
                    }
                } elseif ($current !== null) {
                    $component['default'] = $current;
                }
                $api['ui_components'][$index] = $component;
            }
        }

        // Top-level default used by Studio cards / schema controls.
        if (array_key_exists('default', $api)) {
            $dataType = (string) ($api['data_type'] ?? '');
            if ($dataType === '' && isset($api['frame']) && is_array($api['frame'])) {
                $dataType = self::guessDataTypeFromFrame($api['frame']);
            }
            $wire = isset($api['frame']) && is_array($api['frame'])
                ? self::findPrimaryWireValue($api['frame'])
                : null;
            $current = $api['default'];
            $fromFrame = isset($api['frame']) && is_array($api['frame'])
                ? self::findPrimaryDisplayValue($api['frame'])
                : null;
            if ($fromFrame !== null && $fromFrame !== '' && !self::looksLikeProtocolHex($fromFrame)) {
                $api['default'] = $fromFrame;
            } elseif (self::isEmptyValue($current) || self::looksLikeProtocolHex($current)) {
                $converted = self::toDisplay($wire !== null ? $wire : $current, $dataType, $current);
                if ($converted !== null && $converted !== '') {
                    $api['default'] = $converted;
                }
            }
        }

        return $api;
    }

    /**
     * Enrich deviceConfig.devices[*].pages[*].frame fields.
     */
    public static function enrichDeviceConfig(array $config)
    {
        if (!isset($config['devices']) || !is_array($config['devices'])) {
            return $config;
        }

        foreach ($config['devices'] as $deviceIndex => $device) {
            if (!is_array($device) || !isset($device['pages']) || !is_array($device['pages'])) {
                continue;
            }
            foreach ($device['pages'] as $pageName => $frames) {
                if (!is_array($frames)) {
                    continue;
                }
                foreach ($frames as $frameIndex => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    if (isset($item['frame']) && is_array($item['frame'])) {
                        $item['frame'] = self::enrichFrame($item['frame']);
                    } else {
                        $item = self::enrichFrame($item);
                    }
                    $config['devices'][$deviceIndex]['pages'][$pageName][$frameIndex] = $item;
                }
            }
        }

        return $config;
    }

    /**
     * Enrich node_details[*].config the same way.
     */
    public static function enrichNodeDetails(array $nodeDetails)
    {
        foreach ($nodeDetails as $index => $detail) {
            if (!is_array($detail)) {
                continue;
            }
            if (isset($detail['config']) && is_array($detail['config'])) {
                $nodeDetails[$index]['config'] = self::enrichDeviceConfig($detail['config']);
            }
        }

        return $nodeDetails;
    }

    // ------------------------------------------------------------------ helpers

    private static function normalizeDataType($dataType)
    {
        $dataType = strtolower(trim((string) $dataType));
        $dataType = preg_replace('/[\s_\-]+/', '', $dataType);

        $aliases = [
            'unsignedinteger' => 'unsignedint',
            'signedinteger' => 'signedint',
            'integer' => 'int',
            'bool' => 'boolean',
            'bit' => 'boolean',
            'utf8' => 'string',
            'char' => 'ascii',
            'characters' => 'ascii',
            'single' => 'float',
            'float32' => 'float',
            'float64' => 'double',
        ];

        return $aliases[$dataType] ?? $dataType;
    }

    private static function isNumericDataType($dataType)
    {
        $dataType = self::normalizeDataType($dataType);

        return in_array($dataType, [
            'unsignedint', 'uint', 'byte', 'word', 'dword',
            'uint8', 'uint16', 'uint32',
            'signedint', 'int', 'sint', 'int8', 'int16', 'int32',
            'float', 'double', 'boolean',
        ], true);
    }

    private static function isEmptyValue($value)
    {
        if ($value === null) {
            return true;
        }
        if (is_string($value)) {
            return trim($value) === '';
        }
        if (is_array($value)) {
            return $value === [];
        }

        return false;
    }

    public static function looksLikeProtocolHex($value)
    {
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return false;
        }
        $value = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', trim((string) $value))));
        if ($value === '') {
            return false;
        }

        return (bool) preg_match('/^[0-9A-F]+$/', $value);
    }

    private static function normalizeHex($value)
    {
        $value = strtoupper(preg_replace('/^0x/i', '', trim((string) $value)));
        $value = preg_replace('/[\s:_-]+/', '', $value);
        if ($value === '' || !preg_match('/^[0-9A-F]+$/', $value)) {
            return '';
        }
        if (strlen($value) % 2 === 1) {
            $value = '0' . $value;
        }

        return $value;
    }

    private static function hexToUnsignedInt($hex)
    {
        if (strlen($hex) > 16) {
            // Too wide for native int — keep hex rather than truncate silently.
            return $hex;
        }
        $number = hexdec($hex);

        return is_float($number) ? (string) $number : (int) $number;
    }

    private static function hexToSignedInt($hex)
    {
        $bits = strlen($hex) * 4;
        if ($bits <= 0 || $bits > 64) {
            return self::hexToUnsignedInt($hex);
        }
        $unsigned = hexdec($hex);
        if (is_float($unsigned)) {
            return (string) $unsigned;
        }
        $signBit = 1 << ($bits - 1);
        if (($unsigned & $signBit) !== 0) {
            $unsigned -= (1 << $bits);
        }

        return (int) $unsigned;
    }

    private static function hexToBoolean($hex)
    {
        $hex = ltrim($hex, '0');
        if ($hex === '') {
            return false;
        }

        return hexdec($hex) !== 0;
    }

    private static function toBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        $raw = strtolower(trim((string) $value));
        if (in_array($raw, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }
        if (in_array($raw, ['0', 'false', 'no', 'off', ''], true)) {
            return false;
        }
        if (self::looksLikeProtocolHex($value)) {
            return self::hexToBoolean(self::normalizeHex($value));
        }

        return (bool) $value;
    }

    private static function hexToFloat($hex, $fallback)
    {
        if (strlen($hex) < 8) {
            // Not IEEE-754 single — treat as scaled int-ish unsigned.
            return self::hexToUnsignedInt($hex);
        }
        $hex = substr($hex, -8);
        if (!function_exists('unpack') || !function_exists('pack') || !function_exists('hex2bin')) {
            return $fallback;
        }
        $bin = @hex2bin($hex);
        if ($bin === false || strlen($bin) !== 4) {
            return $fallback;
        }
        // Big-endian first (common on wire), then little-endian fallback.
        $be = @unpack('G', $bin);
        if (is_array($be) && isset($be[1]) && is_finite($be[1])) {
            return round((float) $be[1], 6);
        }
        $le = @unpack('g', $bin);
        if (is_array($le) && isset($le[1]) && is_finite($le[1])) {
            return round((float) $le[1], 6);
        }

        return $fallback;
    }

    private static function hexToDouble($hex, $fallback)
    {
        if (strlen($hex) < 16) {
            return self::hexToFloat($hex, $fallback);
        }
        $hex = substr($hex, -16);
        if (!function_exists('unpack') || !function_exists('hex2bin')) {
            return $fallback;
        }
        $bin = @hex2bin($hex);
        if ($bin === false || strlen($bin) !== 8) {
            return $fallback;
        }
        $be = @unpack('E', $bin);
        if (is_array($be) && isset($be[1]) && is_finite($be[1])) {
            return round((float) $be[1], 6);
        }
        $le = @unpack('e', $bin);
        if (is_array($le) && isset($le[1]) && is_finite($le[1])) {
            return round((float) $le[1], 6);
        }

        return $fallback;
    }

    private static function hexToAscii($hex, $fallback)
    {
        if (!function_exists('hex2bin')) {
            return $fallback;
        }
        $bin = @hex2bin($hex);
        if ($bin === false) {
            return $fallback;
        }
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/', '', $bin);
        $text = trim((string) $text);
        if ($text === '') {
            return $fallback;
        }

        return $text;
    }

    private static function castScalar($value, $dataType)
    {
        $dataType = self::normalizeDataType($dataType);
        if ($dataType === 'boolean') {
            return self::toBoolean($value);
        }
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }
        $raw = trim((string) $value);
        if ($raw === '') {
            return $value;
        }
        if (self::isNumericDataType($dataType) && is_numeric($raw)) {
            if (in_array($dataType, ['float', 'double'], true)) {
                return (float) $raw;
            }
            if (strpos($raw, '.') !== false) {
                return (float) $raw;
            }

            return (int) $raw;
        }

        return $value;
    }

    private static function guessDataTypeFromFrame(array $frame)
    {
        foreach (['commands', 'response', 'error'] as $key) {
            if (!isset($frame[$key]) || !is_array($frame[$key])) {
                continue;
            }
            foreach ($frame[$key] as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $name = strtolower(trim((string) ($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? '')));
                if (in_array($name, ['data', 'trigger'], true) || (!empty($field['ui_type']) && strtolower((string) $field['ui_type']) !== 'none')) {
                    $type = trim((string) ($field['data_type'] ?? ''));
                    if ($type !== '') {
                        return $type;
                    }
                }
            }
        }

        return '';
    }

    private static function findPrimaryWireValue(array $frame)
    {
        foreach (['commands', 'response', 'error'] as $key) {
            if (!isset($frame[$key]) || !is_array($frame[$key])) {
                continue;
            }
            foreach ($frame[$key] as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $name = strtolower(trim((string) ($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? '')));
                if (in_array($name, ['data', 'trigger'], true) && isset($field['value'])) {
                    return $field['value'];
                }
            }
        }

        return null;
    }

    private static function findPrimaryDisplayValue(array $frame)
    {
        foreach (['commands', 'response', 'error'] as $key) {
            if (!isset($frame[$key]) || !is_array($frame[$key])) {
                continue;
            }
            foreach ($frame[$key] as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $name = strtolower(trim((string) ($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? '')));
                if (in_array($name, ['data', 'trigger'], true) && array_key_exists('asperui_value', $field)) {
                    return $field['asperui_value'];
                }
            }
        }

        return null;
    }
}
