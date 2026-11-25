<?php

namespace App\Services;

/**
 * Production display sanitizer for device UI schemas.
 *
 * Strips junk users should never see as control values:
 * - bracket option dumps like [00][01], [false][true], [3][0][0][1], []
 * - wire padding (00 / 0000)
 * - reserved row names (Command / Response / Error)
 * - parameter/field names accidentally used as values
 *
 * Keeps wire hex on frame.value untouched. Safe for legacy and V2.
 */
class DeviceUiDisplaySanitizer
{
    /**
     * True for dumps like [00][01], [false][true], [3][0][0][1], or empty [].
     */
    public static function isBracketOptionList($value): bool
    {
        if (is_array($value)) {
            return false;
        }
        $text = trim((string) $value);
        if ($text === '' || $text[0] !== '[') {
            return false;
        }
        if ($text === '[]') {
            return true;
        }

        return (bool) preg_match('/^(\[[^\]]*\]){1,}$/', $text);
    }

    public static function isReservedSourceName($value): bool
    {
        $text = strtolower(trim((string) $value));

        return in_array($text, ['command', 'response', 'error', 'null', 'undefined', '--'], true);
    }

    public static function looksLikeWirePadding($value): bool
    {
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return false;
        }
        $raw = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', trim((string) $value))));
        if ($raw === '' || strlen($raw) < 2) {
            return false;
        }

        return (bool) preg_match('/^[0-9A-F]+$/', $raw) && (bool) preg_match('/^0+$/', $raw);
    }

    public static function looksLikeProtocolHex($value): bool
    {
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return false;
        }
        $raw = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', trim((string) $value))));

        return $raw !== '' && (bool) preg_match('/^[0-9A-F]+$/', $raw);
    }

    /**
     * Normalize frame/parameter names for fuzzy matching:
     * "Voltage (V)" / "Voltage" / "voltage_v" → "voltage"
     */
    public static function frameNameKey($name): string
    {
        $name = strtolower(trim((string) $name));
        $name = preg_replace('/\([^)]*\)/', '', $name);
        $name = preg_replace('/[^a-z0-9]+/', '', $name);

        return (string) $name;
    }

    /**
     * Parse [00][01] → ['00','01'].
     *
     * @return string[]
     */
    public static function parseBracketTokens($value): array
    {
        $text = trim((string) $value);
        if ($text === '' || !preg_match_all('/\[([^\]]*)\]/', $text, $matches)) {
            return [];
        }

        return array_values(array_map('trim', $matches[1]));
    }

    /**
     * True when a display candidate is unsafe for end users.
     */
    public static function isJunkDisplayValue($value): bool
    {
        if ($value === null) {
            return true;
        }
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return false;
        }
        if (is_array($value)) {
            return true;
        }
        $text = trim((string) $value);
        if ($text === '' || self::isReservedSourceName($text) || self::isBracketOptionList($text) || self::looksLikeWirePadding($text)) {
            return true;
        }
        if (in_array(strtolower($text), ['data', 'trigger', 'frame'], true)) {
            return true;
        }

        return false;
    }

    /**
     * Return a user-safe display value, or '' when unresolved.
     *
     * @param mixed $value
     * @param array $context wire, labels (asperui), item_values, label, componentType
     * @return mixed
     */
    public static function sanitizeDisplayValue($value, array $context = [])
    {
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_array($value)) {
            $value = reset($value);
            if (is_array($value)) {
                return '';
            }
        }

        $text = trim((string) ($value ?? ''));
        if ($text === '' || self::isReservedSourceName($text) || self::looksLikeWirePadding($text)) {
            return self::fallbackDisplay($context);
        }

        $paramLabel = trim((string) ($context['label'] ?? ''));
        if ($paramLabel !== '' && strcasecmp($text, $paramLabel) === 0) {
            return self::fallbackDisplay($context);
        }

        $fieldLabel = trim((string) ($context['fieldLabel'] ?? ''));
        if ($fieldLabel !== '' && strcasecmp($text, $fieldLabel) === 0) {
            return self::fallbackDisplay($context);
        }
        if (in_array(strtolower($text), ['data', 'trigger', 'frame'], true)) {
            return self::fallbackDisplay($context);
        }

        if (self::isBracketOptionList($text)) {
            return self::resolveBracketListDisplay($text, $context);
        }

        return $value;
    }

    /**
     * @param array $context
     * @return mixed
     */
    private static function resolveBracketListDisplay($bracketText, array $context)
    {
        $tokens = self::parseBracketTokens($bracketText);
        if ($tokens === []) {
            return '';
        }

        // Expand labels/values that were wrongly stored as a single "[a][b]" entry.
        $labels = self::expandChoiceList($context['labels'] ?? ($context['asperui'] ?? []));
        $itemValues = self::expandChoiceList($context['item_values'] ?? ($context['asperui_values'] ?? []));
        $wire = trim((string) ($context['wire'] ?? ($context['value'] ?? '')));
        $type = strtolower(preg_replace('/[\s_\-]+/', '', (string) ($context['componentType'] ?? '')));

        $index = self::matchTokenIndex($tokens, $wire);
        if ($index === null && !empty($itemValues)) {
            $index = self::matchTokenIndex($itemValues, $wire);
        }

        if ($index !== null && isset($labels[$index]) && !self::isJunkDisplayValue($labels[$index])) {
            return self::normalizeBooleanishLabel($labels[$index]);
        }

        $isToggle = in_array($type, ['switch', 'toggleswitch', 'power', 'boolean'], true)
            || self::tokensLookBoolean($tokens)
            || self::tokensLookBoolean($itemValues);

        if ($isToggle) {
            $on = self::wireLooksOn($wire, $tokens, $itemValues);
            if (isset($labels[$on ? 1 : 0]) && !self::isJunkDisplayValue($labels[$on ? 1 : 0])) {
                return self::normalizeBooleanishLabel($labels[$on ? 1 : 0]);
            }
            if (isset($itemValues[$on ? 1 : 0]) && !self::isJunkDisplayValue($itemValues[$on ? 1 : 0]) && !self::looksLikeProtocolHex($itemValues[$on ? 1 : 0])) {
                return self::normalizeBooleanishLabel($itemValues[$on ? 1 : 0]);
            }

            // Boolean token words like false/true.
            if (isset($tokens[$on ? 1 : 0]) && self::isBooleanWord($tokens[$on ? 1 : 0])) {
                return self::normalizeBooleanishLabel($tokens[$on ? 1 : 0]);
            }

            return $on ? 'ON' : 'OFF';
        }

        foreach ($labels as $label) {
            if (!self::isJunkDisplayValue($label)) {
                return $label;
            }
        }

        return '';
    }

    private static function matchTokenIndex(array $tokens, $wire)
    {
        $wire = trim((string) $wire);
        if ($wire === '') {
            return null;
        }
        foreach ($tokens as $i => $token) {
            if (strcasecmp((string) $token, $wire) === 0) {
                return $i;
            }
        }
        if (self::looksLikeProtocolHex($wire)) {
            $wireNorm = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', $wire)));
            foreach ($tokens as $i => $token) {
                $tokenNorm = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', (string) $token)));
                if ($tokenNorm !== '' && $tokenNorm === $wireNorm) {
                    return $i;
                }
            }
        }

        return null;
    }

    private static function wireLooksOn($wire, array $tokens, array $itemValues): bool
    {
        $wire = trim((string) $wire);
        if ($wire === '' || self::looksLikeWirePadding($wire)) {
            return false;
        }
        if (self::isBooleanWord($wire)) {
            return in_array(strtolower($wire), ['1', 'true', 'on', 'yes'], true);
        }
        $values = !empty($itemValues) ? $itemValues : $tokens;
        if (count($values) >= 2) {
            $idx = self::matchTokenIndex($values, $wire);
            if ($idx !== null) {
                return $idx > 0;
            }
        }
        if (self::looksLikeProtocolHex($wire)) {
            $raw = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', $wire)));

            return $raw !== '' && !preg_match('/^0+$/', $raw);
        }

        return (bool) $wire;
    }

    private static function tokensLookBoolean(array $tokens): bool
    {
        if (count($tokens) !== 2) {
            return false;
        }
        $a = strtolower(trim((string) $tokens[0]));
        $b = strtolower(trim((string) $tokens[1]));
        $bools = ['0', '1', '00', '01', 'false', 'true', 'off', 'on', 'no', 'yes'];

        return in_array($a, $bools, true) && in_array($b, $bools, true);
    }

    private static function isBooleanWord($value): bool
    {
        $text = strtolower(trim((string) $value));

        return in_array($text, ['0', '1', 'false', 'true', 'off', 'on', 'no', 'yes'], true);
    }

    private static function normalizeBooleanishLabel($value)
    {
        $text = trim((string) $value);
        $lower = strtolower($text);
        if ($lower === 'true' || $lower === 'on' || $lower === 'yes') {
            return 'ON';
        }
        if ($lower === 'false' || $lower === 'off' || $lower === 'no') {
            return 'OFF';
        }

        return $value;
    }

    /**
     * @param array $context
     * @return mixed
     */
    private static function fallbackDisplay(array $context)
    {
        $labels = self::expandChoiceList($context['labels'] ?? ($context['asperui'] ?? []));
        foreach ($labels as $label) {
            if (self::isJunkDisplayValue($label)) {
                continue;
            }
            $paramLabel = trim((string) ($context['label'] ?? ''));
            if ($paramLabel !== '' && strcasecmp((string) $label, $paramLabel) === 0) {
                continue;
            }

            return self::normalizeBooleanishLabel($label);
        }

        $type = strtolower(preg_replace('/[\s_\-]+/', '', (string) ($context['componentType'] ?? '')));
        if (in_array($type, ['switch', 'toggleswitch', 'power', 'boolean'], true)) {
            $wire = trim((string) ($context['wire'] ?? ''));

            return self::wireLooksOn($wire, [], []) ? 'ON' : 'OFF';
        }

        return '';
    }

    /**
     * Expand choice lists, including wrongly stored "[00][01]" / "[false][true]" singles.
     *
     * @return string[]
     */
    private static function expandChoiceList($value): array
    {
        if (!is_array($value)) {
            $text = trim((string) $value);
            if ($text === '') {
                return [];
            }
            if (self::isBracketOptionList($text)) {
                return self::parseBracketTokens($text);
            }
            $decoded = json_decode($text, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = preg_split('/\s*,\s*/', $text) ?: [];
            }
        }

        $out = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                continue;
            }
            $text = trim((string) $item);
            if ($text === '') {
                continue;
            }
            if (self::isBracketOptionList($text)) {
                foreach (self::parseBracketTokens($text) as $token) {
                    if ($token !== '') {
                        $out[] = $token;
                    }
                }
                continue;
            }
            $out[] = $text;
        }

        return $out;
    }

    public static function sanitizeFrameField(array $field, array $context = []): array
    {
        $desc = trim((string) ($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? ''));
        $uiType = strtolower(trim((string) ($field['ui_type'] ?? 'none')));
        // Protocol-only slots without asperui: leave as-is.
        if (($uiType === '' || $uiType === 'none')
            && !in_array(strtolower($desc), ['data', 'trigger'], true)
            && !array_key_exists('asperui_value', $field)
        ) {
            return $field;
        }

        // Normalize corrupted option lists first.
        if (isset($field['asperui'])) {
            $expanded = self::expandChoiceList($field['asperui']);
            $field['asperui'] = $expanded === [] ? null : array_values($expanded);
        }
        if (isset($field['asperui_values'])) {
            $expanded = self::expandChoiceList($field['asperui_values']);
            $field['asperui_values'] = $expanded === [] ? null : array_values($expanded);
        }

        $ctx = array_merge([
            'wire' => $field['value'] ?? null,
            'labels' => $field['asperui'] ?? [],
            'item_values' => $field['asperui_values'] ?? [],
            'fieldLabel' => $desc,
            'label' => $context['label'] ?? $desc,
            'componentType' => $context['componentType'] ?? ($field['ui_type'] ?? ''),
        ], $context);

        if (array_key_exists('asperui_value', $field)) {
            $clean = self::sanitizeDisplayValue($field['asperui_value'], $ctx);
            // Hard guarantee: never leave bracket dumps behind.
            if (self::isJunkDisplayValue($clean) && !is_bool($clean) && !is_int($clean) && !is_float($clean)) {
                $clean = self::fallbackDisplay($ctx);
            }
            if (self::isBracketOptionList($clean)) {
                $clean = '';
            }
            $field['asperui_value'] = $clean;
        }

        return $field;
    }

    public static function sanitizeFrame(array $frame, array $context = []): array
    {
        $context['label'] = $context['label'] ?? ($frame['name'] ?? '');
        foreach (['commands', 'response', 'error', 'Responses', 'Error'] as $key) {
            if (!isset($frame[$key]) || !is_array($frame[$key])) {
                continue;
            }
            foreach ($frame[$key] as $index => $field) {
                if (is_array($field)) {
                    $frame[$key][$index] = self::sanitizeFrameField($field, $context);
                }
            }
        }

        return $frame;
    }

    public static function sanitizeDeviceConfig(array $config): array
    {
        if (!isset($config['info']) || !is_array($config['info'])) {
            $config['info'] = [];
        }
        if (!array_key_exists('chat_status', $config['info']) || $config['info']['chat_status'] === null || $config['info']['chat_status'] === '') {
            $config['info']['chat_status'] = 0;
        }

        if (!isset($config['devices']) || !is_array($config['devices'])) {
            return $config;
        }
        foreach ($config['devices'] as $dIdx => $device) {
            if (!is_array($device) || !isset($device['pages']) || !is_array($device['pages'])) {
                continue;
            }
            foreach ($device['pages'] as $pageName => $frames) {
                if (!is_array($frames)) {
                    continue;
                }
                foreach ($frames as $fIdx => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    if (isset($item['frame']) && is_array($item['frame'])) {
                        $item['frame'] = self::sanitizeFrame($item['frame'], [
                            'label' => $item['frame']['name'] ?? '',
                        ]);
                    } else {
                        $item = self::sanitizeFrame($item, ['label' => $item['name'] ?? '']);
                    }
                    $config['devices'][$dIdx]['pages'][$pageName][$fIdx] = $item;
                }
            }
        }

        return $config;
    }

    public static function sanitizeNodeDetails(array $nodeDetails): array
    {
        foreach ($nodeDetails as $index => $detail) {
            if (!is_array($detail)) {
                continue;
            }
            if (isset($detail['config']) && is_array($detail['config'])) {
                $nodeDetails[$index]['config'] = self::sanitizeDeviceConfig($detail['config']);
            }
        }

        return $nodeDetails;
    }

    public static function sanitizeControl(array $control): array
    {
        $type = (string) ($control['componentType'] ?? $control['catalogComponentType'] ?? '');
        $label = (string) ($control['label'] ?? $control['parameterName'] ?? $control['parameter'] ?? '');
        $meta = is_array($control['protocolMeta'] ?? null) ? $control['protocolMeta'] : [];
        $ctx = [
            'label' => $label,
            'componentType' => $type,
            'labels' => $meta['items'] ?? ($control['items'] ?? []),
            'item_values' => $meta['item_values'] ?? ($control['item_values'] ?? []),
            'wire' => null,
        ];

        foreach (['initialValue', 'value', 'displayValue'] as $key) {
            if (!array_key_exists($key, $control)) {
                continue;
            }
            $clean = self::sanitizeDisplayValue($control[$key], $ctx);
            if (self::isBracketOptionList($clean)) {
                $clean = '';
            }
            $control[$key] = $clean;
        }
        if (isset($control['component']) && is_array($control['component'])) {
            foreach (['initialValue', 'displayValue', 'value'] as $key) {
                if (!array_key_exists($key, $control['component'])) {
                    continue;
                }
                $clean = self::sanitizeDisplayValue($control['component'][$key], $ctx);
                if (self::isBracketOptionList($clean)) {
                    $clean = '';
                }
                $control['component'][$key] = $clean;
            }
            if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                foreach (['value', 'displayValue', 'text'] as $key) {
                    if (!array_key_exists($key, $control['component']['props'])) {
                        continue;
                    }
                    $clean = self::sanitizeDisplayValue($control['component']['props'][$key], $ctx);
                    if (self::isBracketOptionList($clean)) {
                        $clean = '';
                    }
                    $control['component']['props'][$key] = $clean;
                }
            }
        }

        if (isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
            $control['deviceFrame'] = self::sanitizeFrame($control['deviceFrame'], [
                'label' => $control['parameterName'] ?? $label,
                'componentType' => $type,
            ]);
        }

        return $control;
    }

    public static function ensureLiveStateContract(array $schema): array
    {
        if (!isset($schema['deviceState']) || !is_array($schema['deviceState'])) {
            $schema['deviceState'] = [];
        }
        $schema['deviceState']['liveStatePath'] = 'node_details[0].config.devices[0]';
        $schema['deviceState']['preferLiveState'] = true;
        if (empty($schema['deviceState']['bindingRoot'])) {
            $schema['deviceState']['bindingRoot'] = 'state.parameters';
        }
        if (empty($schema['deviceState']['liveStateSource'])) {
            $schema['deviceState']['liveStateSource'] = 'dataSources.deviceState';
        }
        if (empty($schema['deviceState']['mergeStrategy'])) {
            $schema['deviceState']['mergeStrategy'] = 'deepMerge';
        }

        if (isset($schema['mqtt']) && is_array($schema['mqtt'])) {
            if (!isset($schema['mqtt']['liveState']) || !is_array($schema['mqtt']['liveState'])) {
                $schema['mqtt']['liveState'] = [];
            }
            $schema['mqtt']['liveState']['preferLiveState'] = true;
            if (empty($schema['mqtt']['liveState']['mergeInto'])) {
                $schema['mqtt']['liveState']['mergeInto'] = 'state.parameters';
            }
            if (empty($schema['mqtt']['liveState']['valueLoadingFlag'])) {
                $schema['mqtt']['liveState']['valueLoadingFlag'] = 'valueLoading';
            }
        }

        return $schema;
    }

    public static function sanitizeSchema(array $schema): array
    {
        if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
            $schema['deviceConfig'] = self::sanitizeDeviceConfig($schema['deviceConfig']);
        }
        if (isset($schema['node_details']) && is_array($schema['node_details'])) {
            $schema['node_details'] = self::sanitizeNodeDetails($schema['node_details']);
            if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
                && isset($schema['node_details'][0]) && is_array($schema['node_details'][0])
            ) {
                $schema['node_details'][0]['config'] = $schema['deviceConfig'];
            }
        }
        if (isset($schema['v2_node_details']) && is_array($schema['v2_node_details'])) {
            $schema['v2_node_details'] = self::sanitizeNodeDetails($schema['v2_node_details']);
        }
        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $index => $control) {
                if (is_array($control)) {
                    $schema['controls'][$index] = self::sanitizeControl($control);
                }
            }
        }

        if (isset($schema['state']['parameters']) && is_array($schema['state']['parameters'])) {
            foreach ($schema['state']['parameters'] as $key => $param) {
                if (is_array($param) && array_key_exists('value', $param)) {
                    $label = (string) ($param['meta']['parameterName'] ?? $param['meta']['label'] ?? $key);
                    $clean = self::sanitizeDisplayValue($param['value'], ['label' => $label]);
                    if (self::isBracketOptionList($clean)) {
                        $clean = '';
                    }
                    $param['value'] = $clean;
                    $schema['state']['parameters'][$key] = $param;
                } elseif (!is_array($param)) {
                    $clean = self::sanitizeDisplayValue($param, ['label' => (string) $key]);
                    $schema['state']['parameters'][$key] = self::isBracketOptionList($clean) ? '' : $clean;
                }
            }
        }

        if (isset($schema['deviceState']['parameters']) && is_array($schema['deviceState']['parameters'])) {
            foreach ($schema['deviceState']['parameters'] as $key => $value) {
                if (is_array($value) && array_key_exists('value', $value)) {
                    $clean = self::sanitizeDisplayValue($value['value'], ['label' => (string) $key]);
                    $value['value'] = self::isBracketOptionList($clean) ? '' : $clean;
                    $schema['deviceState']['parameters'][$key] = $value;
                } elseif (!is_array($value)) {
                    $clean = self::sanitizeDisplayValue($value, ['label' => (string) $key]);
                    $schema['deviceState']['parameters'][$key] = self::isBracketOptionList($clean) ? '' : $clean;
                }
            }
        }

        $schema = self::ensureLiveStateContract($schema);
        $schema = self::enrichMissingInfoFromSchema($schema);
        $schema = self::stripLeakedProtocolBracketText($schema);

        return $schema;
    }

    /**
     * sno_asperui / item_values are protocol tables. A concatenated bracket
     * string such as [00][01] or [000000003C6E..][000001..] must not be copied
     * into a caption or the control's initial value. The tables themselves stay.
     */
    public static function stripLeakedProtocolBracketText(array $schema): array
    {
        self::stripLeakedProtocolBracketNode($schema);

        return $schema;
    }

    private static function stripLeakedProtocolBracketNode(array &$node, $depth = 0): void
    {
        if ($depth > 80) {
            return;
        }
        foreach ($node as $key => &$child) {
            if (self::isProtocolChoiceListKey($key)) {
                continue;
            }
            if (is_array($child)) {
                if (self::isScalarChoiceList($key, $child)) {
                    continue;
                }
                self::stripLeakedProtocolBracketNode($child, $depth + 1);
                continue;
            }
            if (!is_string($child) || !self::isLeakedDisplayKey($key) || !self::isBracketOptionList($child)) {
                continue;
            }
            if (self::isFrameDescriptionKey($key)) {
                $child = self::fallbackFrameDescription($node, (string) $key);
                continue;
            }
            $child = '';
        }
        unset($child);
    }

    private static function isProtocolChoiceListKey($key): bool
    {
        return in_array((string) $key, [
            'sno_asperui', 'item_values', 'asperui', 'asperui_values',
            'snoAsperui', 'asperuiValues',
        ], true);
    }

    /**
     * Choice lists are scalar tokens. Widget `items` are nodes and still get walked.
     */
    private static function isScalarChoiceList($key, array $list): bool
    {
        if ((string) $key !== 'items') {
            return false;
        }
        foreach ($list as $item) {
            if (is_array($item)) {
                return false;
            }
        }

        return true;
    }

    private static function isLeakedDisplayKey($key): bool
    {
        return in_array((string) $key, [
            'label', 'fieldLabel', 'componentLabel', 'title', 'text', 'subtitle',
            'default_name', 'value', 'initialValue', 'displayValue', 'fieldValue',
            'default', 'hintText', 'name',
            'Field_description', 'field_description', 'field_name',
        ], true);
    }

    private static function isFrameDescriptionKey($key): bool
    {
        return in_array((string) $key, ['Field_description', 'field_description', 'field_name'], true);
    }

    private static function fallbackFrameDescription(array $node, $key): string
    {
        foreach (['default_name', 'Field_description', 'field_description', 'field_name'] as $alt) {
            if ($alt === $key || !isset($node[$alt]) || !is_string($node[$alt])) {
                continue;
            }
            $text = trim($node[$alt]);
            if ($text !== '' && !self::isBracketOptionList($text)) {
                return $text;
            }
        }

        return 'Data';
    }

    /**
     * Fill empty info.brand / product from already-present schema assets/meta
     * without requiring Studio republish or DB access in the normalizer.
     */
    private static function enrichMissingInfoFromSchema(array $schema): array
    {
        $brand = '';
        if (!empty($schema['assets']['brandLogo']['alt'])) {
            $brand = trim((string) $schema['assets']['brandLogo']['alt']);
        }
        if ($brand === '' && !empty($schema['meta']['app']['brand'])) {
            $brand = trim((string) $schema['meta']['app']['brand']);
        }
        $product = '';
        if (!empty($schema['meta']['app']['name'])) {
            $product = trim((string) $schema['meta']['app']['name']);
            if (preg_match('/\s+Control$/i', $product)) {
                $product = trim((string) preg_replace('/\s+Control$/i', '', $product));
            }
        }

        $fillInfo = static function (array $config) use ($brand, $product) {
            if (!isset($config['info']) || !is_array($config['info'])) {
                $config['info'] = [];
            }
            if (($config['info']['brand'] ?? null) === null || $config['info']['brand'] === '') {
                if ($brand !== '') {
                    $config['info']['brand'] = $brand;
                }
            }
            if (($config['info']['product'] ?? null) === null || $config['info']['product'] === '') {
                if ($product !== '') {
                    $config['info']['product'] = $product;
                }
            }
            if (!array_key_exists('chat_status', $config['info']) || $config['info']['chat_status'] === null || $config['info']['chat_status'] === '') {
                $config['info']['chat_status'] = 0;
            }

            return $config;
        };

        if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
            $schema['deviceConfig'] = $fillInfo($schema['deviceConfig']);
        }
        if (isset($schema['node_details'][0]['config']) && is_array($schema['node_details'][0]['config'])) {
            $schema['node_details'][0]['config'] = $fillInfo($schema['node_details'][0]['config']);
        }

        return $schema;
    }
}
