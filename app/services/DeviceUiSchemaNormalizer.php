<?php

namespace App\Services;

/**
 * Last-mile normalizer for the device UI schema that is served to the Flutter app.
 *
 * The schema is authored in Design Studio, stored in `device_ui_schemas` and then
 * replayed for whatever device the app opens. Several fields are only correct for
 * the node that was open in Studio at publish time, and some product_api rows are
 * published with empty frame slots. This class repairs everything that can be
 * derived safely and records everything that cannot, instead of inventing values.
 *
 * Deliberately dependency free (no CodeIgniter services) so the plain-PHP v8
 * endpoints can require it directly.
 */
class DeviceUiSchemaNormalizer
{
    /**
     * Icon tokens that reach the schema from Material Symbols / ad-hoc naming and
     * have no entry in the Studio icon library. Targets are all real library names.
     */
    private static $iconAliases = [
        'category'            => 'grid',
        'categories'          => 'grid',
        'dashboard'           => 'grid',
        'apps'                => 'grid',
        'widgets'             => 'grid',
        'toggle_on'           => 'power',
        'toggle_off'          => 'power-off',
        'power_settings_new'  => 'power',
        'flash_on'            => 'zap',
        'flash_off'           => 'zap-off',
        'device_thermostat'   => 'thermometer',
        'thermostat'          => 'thermometer',
        'water_drop'          => 'droplet',
        'opacity'             => 'droplet',
        'air'                 => 'air-flow',
        'ac_unit'             => 'snow-flake',
        'speed'               => 'gauge',
        'tune'                => 'sliders',
        'settings_input_component' => 'settings',
        'lightbulb_outline'   => 'lightbulb',
        'light_mode'          => 'sun',
        'dark_mode'           => 'moon',
        'electric_bolt'       => 'bolt',
        'battery_full'        => 'battery',
        'signal_cellular_alt' => 'signal',
        'network_wifi'        => 'wifi',
        'qr_code_scanner'     => 'scan',
        'sensors'             => 'radio',
        'timer'               => 'clock',
        'schedule'            => 'clock',
    ];

    /**
     * @param array  $schema  Decoded schema document.
     * @param string $modelNo Model the app actually requested.
     * @param string $nodeId  Node the app actually opened.
     */
    public static function normalize(array $schema, $modelNo = '', $nodeId = '')
    {
        $modelNo = trim((string) $modelNo);
        $nodeId  = trim((string) $nodeId);

        $report = [
            'missingFrameValues' => [],
            'droppedFrameSlots'  => [],
            'repairedByteFields' => [],
            'iconFallbacks'      => [],
            'cardStylesApplied'  => 0,
        ];

        if ($nodeId !== '') {
            $schema = self::applyTestNodesNodeDetails($schema, $nodeId);
        }

        if ($nodeId !== '' || $modelNo !== '') {
            $schema = self::applyRequestedNode($schema, $modelNo, $nodeId);
        }

        $schema = self::normalizeDeviceFrames($schema, $report);
        // Fill CommandId / Function Code / ActionId from protocolMeta when the
        // slot exists but is empty, or insert the slot when meta has the value.
        // Inventing bytes is still forbidden — only copy known product_api values.
        // Terminator is protocol-fixed (0A) when the slot is present but empty.
        $schema = self::hydrateWriteFieldsFromMeta($schema, $report);
        // Send/writable controls: complete CommandId/Function/Data/Terminator
        // framing, enforce hex-width = 2*no_of_bytes, and publish write access.
        $schema = self::enforceSendProtocolContract($schema, $report);
        $schema = self::applyCardStyles($schema, $report);
        $schema = self::normalizeControlContracts($schema);
        // Rebuild missing multi-page hardware frames after controls are normalized.
        $schema = self::ensureDeviceConfigPagesFromControls($schema);
        $schema = self::applyCanonicalHeader($schema);
        $schema = self::applyCanonicalValueColors($schema);
        // Timer schemaSlider with min/max labels and a kicker fails Flutter validation
        // unless these style fields are present on props. Parameter bindings stay as-is.
        $schema = self::applyTimerSliderStyleContract($schema);
        // Register semantic tokens before icon remapping so filled control icons stick.
        $schema = self::ensureRequiredIcons($schema);
        $schema = self::normalizeIcons($schema, $report);
        $schema = self::recordValidation($schema, $report);

        return $schema;
    }

    /**
     * Copies protocolMeta.command_id / function_code / action_id into the matching
     * deviceFrame command slots. Does not invent values that product_api never stored.
     */
    private static function hydrateWriteFieldsFromMeta(array $schema, array &$report)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }

            $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta'])
                ? $control['protocolMeta']
                : [];
            $commandId = self::firstNonEmpty(
                isset($meta['command_id']) ? $meta['command_id'] : null,
                isset($meta['commandId']) ? $meta['commandId'] : null
            );
            $functionCode = self::firstNonEmpty(
                isset($meta['function_code']) ? $meta['function_code'] : null,
                isset($meta['functionCode']) ? $meta['functionCode'] : null
            );
            $actionId = self::firstNonEmpty(
                isset($meta['action_id']) ? $meta['action_id'] : null,
                isset($meta['actionId']) ? $meta['actionId'] : null
            );

            $frame = isset($control['deviceFrame']) && is_array($control['deviceFrame'])
                ? $control['deviceFrame']
                : (isset($meta['frame']) && is_array($meta['frame']) ? $meta['frame'] : null);
            if ($frame === null) {
                continue;
            }

            $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
            $filled = $commands;
            if ($commandId !== null || $functionCode !== null || $actionId !== null) {
                $filled = self::fillWriteFieldSlots($filled, $commandId, $functionCode, $actionId, $report, $control);
            }
            $filled = self::fillTerminatorSlot($filled, $report, $control);
            if ($filled === $commands) {
                continue;
            }

            $frame['commands'] = $filled;
            $control['deviceFrame'] = $frame;
            if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                $control['protocolMeta'] = $meta;
            }
            $control['protocolMeta']['frame'] = $frame;
            if ($commandId !== null) {
                $control['protocolMeta']['command_id'] = $commandId;
            }
            if ($functionCode !== null) {
                $control['protocolMeta']['function_code'] = $functionCode;
            }
            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    /**
     * Protocol frames always end with 0A. Fill an empty Terminator slot.
     * For writable/Send controls, insert a Terminator when the slot is missing.
     */
    private static function fillTerminatorSlot(array $commands, array &$report, array $control)
    {
        $controlId = trim((string) (isset($control['id']) ? $control['id'] : ''));
        $found = false;
        foreach ($commands as $i => $field) {
            if (!is_array($field)) {
                continue;
            }
            if (self::frameFieldName($field) !== 'terminator') {
                continue;
            }
            $found = true;
            $value = isset($field['value']) ? $field['value'] : null;
            $asperui = isset($field['asperui_value']) ? $field['asperui_value'] : null;
            if (($value === null || $value === '' || $value === []) && $asperui !== null && $asperui !== '' && $asperui !== []) {
                if (self::looksLikeProtocolHex($asperui)) {
                    $field['value'] = $asperui;
                    $value = $asperui;
                }
            }
            $normalized = self::normalizeProtocolHex($value, 1);
            if ($normalized === '') {
                $normalized = '0A';
            }
            $field['value'] = $normalized;
            $field['no_of_bytes'] = 1;
            if (!isset($field['asperui_value']) || $field['asperui_value'] === null || $field['asperui_value'] === ''
                || !self::looksLikeProtocolHex($field['asperui_value'])
            ) {
                $field['asperui_value'] = $normalized;
            }
            $commands[$i] = $field;
            $report['repairedByteFields'][] = ($controlId !== '' ? $controlId : 'control') . '.commands.Terminator';
        }

        if (!$found && self::controlNeedsSendProtocol($control)) {
            $nextStart = 1;
            foreach ($commands as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $start = isset($field['byte_start_position']) ? (int) $field['byte_start_position'] : 0;
                $bytes = isset($field['no_of_bytes']) ? (int) $field['no_of_bytes'] : 1;
                if ($start > 0 && $bytes > 0) {
                    $nextStart = max($nextStart, $start + $bytes);
                }
            }
            $commands[] = self::writeFieldSlot('Terminator', $nextStart, '0A');
            $report['repairedByteFields'][] = ($controlId !== '' ? $controlId : 'control') . '.commands.Terminator.insert';
        }

        return array_values($commands);
    }

    /**
     * Enforce Rule 1–3 for every Send / writable user-input control:
     * hex width = 2 * no_of_bytes, full CommandId/Function/Data/Terminator framing,
     * and explicit write permissions.
     */
    private static function enforceSendProtocolContract(array $schema, array &$report)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }

            $needsProtocol = self::controlNeedsSendProtocol($control);
            $frame = isset($control['deviceFrame']) && is_array($control['deviceFrame'])
                ? $control['deviceFrame']
                : (isset($control['protocolMeta']['frame']) && is_array($control['protocolMeta']['frame'])
                    ? $control['protocolMeta']['frame']
                    : null);

            if (is_array($frame)) {
                $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
                if ($needsProtocol) {
                    $commands = self::ensureCompleteSendCommandSlots($commands, $control, $report);
                }
                $commands = self::normalizeCommandWireHex($commands, $report, $control);
                $frame['commands'] = $commands;
                $control['deviceFrame'] = $frame;
                if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                    $control['protocolMeta'] = [];
                }
                $control['protocolMeta']['frame'] = $frame;
            }

            if ($needsProtocol && self::isWritableControl($control)) {
                $control = self::publishExplicitWriteAccess($control);
            }

            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    private static function controlNeedsSendProtocol(array $control)
    {
        if (!empty($control['component']['props']['sendButton'])) {
            return true;
        }
        if (isset($control['uiParts']) && is_array($control['uiParts']) && in_array('sendButton', $control['uiParts'], true)) {
            return true;
        }
        $type = strtolower(str_replace(['_', '-'], '', (string) (isset($control['componentType']) ? $control['componentType'] : '')));
        if (in_array($type, ['textfield', 'entertext', 'text'], true) && self::isWritableControl($control)) {
            return true;
        }

        return self::isWritableControl($control);
    }

    private static function publishExplicitWriteAccess(array $control)
    {
        $control['writable'] = true;
        $control['readOnly'] = false;
        $control['interactive'] = true;
        $control['access'] = 'write';
        if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
            $control['protocolMeta'] = [];
        }
        $access = strtolower(trim((string) (isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : '')));
        if ($access === '' || in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
            $control['protocolMeta']['access'] = 'write';
        } elseif (strpos($access, 'write') === false) {
            $control['protocolMeta']['access'] = 'Read/Write';
        }
        if (isset($control['component']) && is_array($control['component'])) {
            $control['component']['readOnly'] = false;
            $control['component']['interactive'] = true;
            if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                $control['component']['props']['readOnly'] = false;
                $control['component']['props']['enabled'] = true;
                $control['component']['props']['interactive'] = true;
            }
        }

        return $control;
    }

    /**
     * Guarantee CommandId + Function Code + Data + Terminator slots for Send controls.
     * Values are copied from protocolMeta when present; Terminator defaults to 0A.
     * Does not invent CommandId/Function Code bytes product_api never stored.
     */
    private static function ensureCompleteSendCommandSlots(array $commands, array $control, array &$report)
    {
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $commandId = self::firstNonEmpty(
            isset($meta['command_id']) ? $meta['command_id'] : null,
            isset($meta['commandId']) ? $meta['commandId'] : null
        );
        $functionCode = self::firstNonEmpty(
            isset($meta['function_code']) ? $meta['function_code'] : null,
            isset($meta['functionCode']) ? $meta['functionCode'] : null
        );
        $actionId = self::firstNonEmpty(
            isset($meta['action_id']) ? $meta['action_id'] : null,
            isset($meta['actionId']) ? $meta['actionId'] : null
        );

        // Copy known product_api CommandId / Function Code / ActionId into slots
        // (or insert named slots when meta already has those values).
        if ($commandId !== null) {
            $commandId = self::normalizeProtocolHex($commandId, 1) ?: $commandId;
        }
        if ($functionCode !== null) {
            $functionCode = self::normalizeProtocolHex($functionCode, 1) ?: $functionCode;
        }
        if ($actionId !== null) {
            $actionId = self::normalizeProtocolHex($actionId, 1) ?: $actionId;
        }
        $commands = self::fillWriteFieldSlots($commands, $commandId, $functionCode, $actionId, $report, $control);
        // Always close Send frames with protocol Terminator 0A.
        $commands = self::fillTerminatorSlot($commands, $report, $control);

        return array_values($commands);
    }

    /**
     * Resolve protocol width from no_of_bytes or mobile alias byteLength.
     * Does not rename either key — both stay in the schema when present.
     */
    private static function resolveCommandByteCount(array $field)
    {
        $bytes = isset($field['no_of_bytes']) ? (int) $field['no_of_bytes'] : 0;
        if ($bytes < 1 && isset($field['byteLength'])) {
            $bytes = (int) $field['byteLength'];
        }
        if ($bytes < 1 && isset($field['byte_length'])) {
            $bytes = (int) $field['byte_length'];
        }

        return $bytes > 0 ? $bytes : 1;
    }

    /**
     * Rule 1: every command segment value length must equal 2 * no_of_bytes.
     * Non-hex live/UI text is moved to asperui_value; wire value becomes zero hex.
     * Ambiguous short hex like "00" with width 2 is padded to "0000" (explicit 2-byte).
     */
    private static function normalizeCommandWireHex(array $commands, array &$report, array $control)
    {
        $controlId = trim((string) (isset($control['id']) ? $control['id'] : 'control'));
        foreach ($commands as $i => $field) {
            if (!is_array($field)) {
                continue;
            }
            $name = self::frameFieldName($field);
            $bytes = self::resolveCommandByteCount($field);
            $field['no_of_bytes'] = $bytes;
            if (isset($field['byteLength']) || array_key_exists('byteLength', $field)) {
                $field['byteLength'] = $bytes;
            }

            $value = isset($field['value']) ? $field['value'] : null;
            if (($value === null || $value === '' || $value === [])
                && isset($field['asperui_value'])
                && self::looksLikeProtocolHex($field['asperui_value'])
            ) {
                $value = $field['asperui_value'];
            }

            $normalized = self::normalizeProtocolHex($value, $bytes);
            if ($normalized === '') {
                if ($name === 'terminator') {
                    $normalized = '0A';
                } elseif (in_array($name, ['commandid', 'functioncode', 'actionid'], true)) {
                    // Never invent protocol IDs — leave empty so writeReady stays false
                    // until product_api supplies the real bytes.
                    $commands[$i] = $field;
                    continue;
                } else {
                    // Preserve human/live text off the wire so encoding cannot fail.
                    if ($value !== null && $value !== '' && $value !== [] && !self::looksLikeProtocolHex($value)) {
                        if (!isset($field['asperui_value']) || $field['asperui_value'] === null || $field['asperui_value'] === ''
                            || self::looksLikeProtocolHex($field['asperui_value'])
                        ) {
                            $field['asperui_value'] = is_scalar($value) ? (string) $value : '';
                        }
                    }
                    $normalized = str_repeat('00', $bytes);
                }
                $report['repairedByteFields'][] = $controlId . '.commands.' . (self::frameFieldLabel($field) ?: $name ?: 'field') . '.hexWidth';
            } elseif (strlen($normalized) !== ($bytes * 2)) {
                // Truncate oversized hex from the left (keep least-significant bytes).
                $normalized = substr($normalized, -($bytes * 2));
                if (strlen($normalized) < ($bytes * 2)) {
                    $normalized = str_pad($normalized, $bytes * 2, '0', STR_PAD_LEFT);
                }
                $report['repairedByteFields'][] = $controlId . '.commands.' . (self::frameFieldLabel($field) ?: $name ?: 'field') . '.hexWidth';
            }

            $field['value'] = $normalized;
            $field['no_of_bytes'] = $bytes;
            // Keep asperui_value width-safe when it is protocol hex (e.g. "00" → "0000").
            if (isset($field['asperui_value']) && self::looksLikeProtocolHex($field['asperui_value'])) {
                $asperuiHex = self::normalizeProtocolHex($field['asperui_value'], $bytes);
                if ($asperuiHex !== '') {
                    $field['asperui_value'] = $asperuiHex;
                }
            }
            $commands[$i] = $field;
        }

        return $commands;
    }

    private static function looksLikeProtocolHex($value)
    {
        $value = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', trim((string) $value))));

        return $value !== '' && (bool) preg_match('/^[0-9A-F]+$/', $value);
    }

    private static function normalizeProtocolHex($value, $byteCount = 0)
    {
        if ($value === null || $value === [] || is_array($value)) {
            return '';
        }
        $value = strtoupper(preg_replace('/^0x/i', '', trim((string) $value)));
        $value = preg_replace('/[\s:_-]+/', '', $value);
        if ($value === '' || !preg_match('/^[0-9A-F]+$/', $value)) {
            return '';
        }
        if ((strlen($value) % 2) !== 0) {
            $value = '0' . $value;
        }
        $bytes = max(0, (int) $byteCount);
        if ($bytes > 0) {
            $width = $bytes * 2;
            if (strlen($value) < $width) {
                $value = str_pad($value, $width, '0', STR_PAD_LEFT);
            } elseif (strlen($value) > $width) {
                $value = substr($value, -$width);
            }
        }

        return $value;
    }

    private static function fillWriteFieldSlots(array $commands, $commandId, $functionCode, $actionId, array &$report, array $control)
    {
        $present = [
            'commandid'    => false,
            'functioncode' => false,
            'actionid'     => false,
        ];
        $changed = false;
        $controlId = trim((string) (isset($control['id']) ? $control['id'] : ''));

        foreach ($commands as $i => $field) {
            if (!is_array($field)) {
                continue;
            }
            $name = self::frameFieldName($field);
            if (!isset($present[$name])) {
                continue;
            }
            $present[$name] = true;

            $value = isset($field['value']) ? $field['value'] : null;
            $asperui = isset($field['asperui_value']) ? $field['asperui_value'] : null;
            if (($value === null || $value === '' || $value === []) && $asperui !== null && $asperui !== '' && $asperui !== []) {
                $field['value'] = $asperui;
                $value = $asperui;
                $changed = true;
            }

            $fill = null;
            if ($name === 'commandid') {
                $fill = $commandId;
            } elseif ($name === 'functioncode') {
                $fill = $functionCode;
            } elseif ($name === 'actionid') {
                $fill = $actionId;
            }

            if (($value === null || $value === '' || $value === []) && $fill !== null) {
                $field['value'] = $fill;
                if (!isset($field['asperui_value']) || $field['asperui_value'] === null || $field['asperui_value'] === '') {
                    $field['asperui_value'] = $fill;
                }
                $changed = true;
                $report['repairedByteFields'][] = ($controlId !== '' ? $controlId : 'control')
                    . '.commands.' . self::frameFieldLabel($field);
            }

            $commands[$i] = $field;
        }

        // Insert missing named slots only when product_api already gave us the value.
        $inserts = [];
        if (!$present['commandid'] && $commandId !== null) {
            $inserts[] = self::writeFieldSlot('CommandId', 1, $commandId);
            $changed = true;
        }
        if (!$present['functioncode'] && $functionCode !== null) {
            $inserts[] = self::writeFieldSlot('Function Code', 2, $functionCode);
            $changed = true;
        }
        if (!$present['actionid'] && $actionId !== null) {
            $inserts[] = self::writeFieldSlot('ActionId', 3, $actionId);
            $changed = true;
        }
        if (!empty($inserts)) {
            $commands = array_merge($inserts, $commands);
        }

        return $changed ? array_values($commands) : $commands;
    }

    private static function writeFieldSlot($label, $byteStart, $value)
    {
        return [
            'default_name'         => $label,
            'Field_description'    => $label,
            'value'                => $value,
            'asperui_value'        => $value,
            'type'                 => 'Fixed Value',
            'data_type'            => 'UnsignedInt',
            'no_of_bytes'          => 1,
            'byte_start_position'  => (int) $byteStart,
            'ui_type'              => 'None',
            'local_control'        => 'enable',
        ];
    }

    private static function firstNonEmpty()
    {
        foreach (func_get_args() as $candidate) {
            if ($candidate === null || $candidate === '' || $candidate === []) {
                continue;
            }
            return $candidate;
        }
        return null;
    }

    // ---------------------------------------------------------------- node ids

    /**
     * Fetches `test_nodes` for the opened node_id and maps that payload's
     * `node_details` onto the schema. A different node's stored details are not used.
     */
    private static function applyTestNodesNodeDetails(array $schema, $nodeId)
    {
        $nodeId = trim((string) $nodeId);
        if ($nodeId === '') {
            return $schema;
        }

        $payload = self::fetchTestNodesPayload($nodeId);
        $rawDetails = isset($payload['node_details']) && is_array($payload['node_details'])
            ? $payload['node_details']
            : [];
        $ordered = self::orderNodeDetailsForNode($rawDetails, $nodeId);
        if (!$ordered) {
            $stored = isset($schema['node_details']) && is_array($schema['node_details'])
                ? $schema['node_details']
                : [];
            $ordered = self::orderNodeDetailsForNode($stored, $nodeId);
        }
        if (!$ordered) {
            return $schema;
        }

        $schema['node_details'] = $ordered;
        $schema['nodes'] = $nodeId;
        $config = isset($ordered[0]['config']) && is_array($ordered[0]['config'])
            ? $ordered[0]['config']
            : [];
        if (!empty($config)) {
            // Preserve any pages already published in the stored schema; test_nodes
            // often only ships Page 1 and would otherwise wipe secondary pages.
            $existingConfig = isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
                ? $schema['deviceConfig']
                : [];
            $schema['deviceConfig'] = self::mergeDeviceConfigPages($config, $existingConfig);
            if (isset($schema['state']['device']) && is_array($schema['state']['device'])) {
                $schema['state']['device']['config'] = $schema['deviceConfig'];
                $schema['state']['device']['node_id'] = $nodeId;
            }
            if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
                $schema['deviceState']['config'] = $schema['deviceConfig'];
                $schema['deviceState']['node_id'] = $nodeId;
            }
        }
        if (isset($schema['state']) && is_array($schema['state'])) {
            $schema['state']['node_id'] = $nodeId;
        }

        return $schema;
    }

    /**
     * Union live + existing deviceConfig pages without renaming keys.
     * Live frames win for shared page names; pages only in existing are kept.
     */
    private static function mergeDeviceConfigPages(array $liveConfig, array $existingConfig)
    {
        $livePages = isset($liveConfig['devices'][0]['pages']) && is_array($liveConfig['devices'][0]['pages'])
            ? $liveConfig['devices'][0]['pages']
            : [];
        $existingPages = isset($existingConfig['devices'][0]['pages']) && is_array($existingConfig['devices'][0]['pages'])
            ? $existingConfig['devices'][0]['pages']
            : [];

        if (empty($livePages) && !empty($existingPages)) {
            return !empty($existingConfig) ? $existingConfig : $liveConfig;
        }
        if (empty($existingPages)) {
            return $liveConfig;
        }

        foreach ($existingPages as $pageName => $frames) {
            if (!is_array($frames)) {
                continue;
            }
            $pageKey = (string) $pageName;
            if (!isset($livePages[$pageKey]) || !is_array($livePages[$pageKey]) || empty($livePages[$pageKey])) {
                $livePages[$pageKey] = $frames;
                continue;
            }
            // Prefer the richer frame set when live only has empty stubs.
            if (self::deviceConfigPageScore($frames) > self::deviceConfigPageScore($livePages[$pageKey])) {
                $livePages[$pageKey] = $frames;
            }
        }

        if (!isset($liveConfig['devices'][0]) || !is_array($liveConfig['devices'][0])) {
            $liveConfig['devices'][0] = isset($existingConfig['devices'][0]) && is_array($existingConfig['devices'][0])
                ? $existingConfig['devices'][0]
                : [];
        }
        $liveConfig['devices'][0]['pages'] = $livePages;
        $liveConfig['devices'][0]['total_pages'] = (string) max(1, count($livePages));

        return $liveConfig;
    }

    private static function deviceConfigPageScore(array $frames)
    {
        $score = 0;
        foreach ($frames as $item) {
            if (!is_array($item)) {
                continue;
            }
            $frame = isset($item['frame']) && is_array($item['frame']) ? $item['frame'] : $item;
            $name = trim((string) (isset($frame['name']) ? $frame['name'] : ''));
            $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
            if ($name !== '') {
                $score += 5;
            }
            $score += count($commands);
        }

        return $score;
    }

    /**
     * Publish every schema page into deviceConfig.devices[0].pages using control
     * deviceFrame payloads. Does not invent protocol bytes — only copies frames
     * already present on controls.
     */
    private static function ensureDeviceConfigPagesFromControls(array $schema)
    {
        $controls = isset($schema['controls']) && is_array($schema['controls']) ? $schema['controls'] : [];
        if ($controls === []) {
            return $schema;
        }

        $pagesFromControls = [];
        foreach ($controls as $control) {
            if (!is_array($control)) {
                continue;
            }
            $pageName = trim((string) (isset($control['pageName']) ? $control['pageName'] : ''));
            if ($pageName === '') {
                $pageNo = trim((string) (isset($control['pageNo']) ? $control['pageNo'] : '1'));
                $pageName = 'Page' . ($pageNo !== '' ? $pageNo : '1') . '.Operation';
            }
            $frame = null;
            if (isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
                $frame = $control['deviceFrame'];
            } elseif (isset($control['protocolMeta']['frame']) && is_array($control['protocolMeta']['frame'])) {
                $frame = $control['protocolMeta']['frame'];
            }
            if ($frame === null || !is_array($frame)) {
                continue;
            }
            if (!isset($pagesFromControls[$pageName])) {
                $pagesFromControls[$pageName] = [];
            }
            $frameName = strtolower(trim((string) (isset($frame['name']) ? $frame['name'] : '')));
            if ($frameName === '') {
                $frameName = strtolower(trim((string) (isset($control['parameterName']) ? $control['parameterName'] : (isset($control['parameter']) ? $control['parameter'] : ''))));
                if ($frameName !== '' && (!isset($frame['name']) || trim((string) $frame['name']) === '')) {
                    $frame['name'] = isset($control['parameterName']) ? $control['parameterName'] : (isset($control['parameter']) ? $control['parameter'] : $frameName);
                }
            }
            // One frame per parameter name per page (first / richest wins later).
            $existingIndex = null;
            foreach ($pagesFromControls[$pageName] as $idx => $item) {
                $existingName = strtolower(trim((string) (isset($item['frame']['name']) ? $item['frame']['name'] : '')));
                if ($existingName !== '' && $existingName === $frameName) {
                    $existingIndex = $idx;
                    break;
                }
            }
            $entry = ['frame' => $frame];
            if ($existingIndex === null) {
                $pagesFromControls[$pageName][] = $entry;
            } else {
                $existingCmds = isset($pagesFromControls[$pageName][$existingIndex]['frame']['commands'])
                    && is_array($pagesFromControls[$pageName][$existingIndex]['frame']['commands'])
                    ? count($pagesFromControls[$pageName][$existingIndex]['frame']['commands'])
                    : 0;
                $newCmds = isset($frame['commands']) && is_array($frame['commands']) ? count($frame['commands']) : 0;
                if ($newCmds >= $existingCmds) {
                    $pagesFromControls[$pageName][$existingIndex] = $entry;
                }
            }
        }

        // Also seed empty page shells from schema.pages titles so navigation keys match.
        if (isset($schema['pages']) && is_array($schema['pages'])) {
            foreach ($schema['pages'] as $page) {
                if (!is_array($page)) {
                    continue;
                }
                $title = trim((string) (isset($page['title']) ? $page['title'] : (isset($page['pageName']) ? $page['pageName'] : '')));
                if ($title === '') {
                    $pageNo = trim((string) (isset($page['pageNo']) ? $page['pageNo'] : ''));
                    if ($pageNo !== '') {
                        $title = 'Page' . $pageNo . '.Operation';
                    }
                }
                if ($title !== '' && !isset($pagesFromControls[$title])) {
                    $pagesFromControls[$title] = [];
                }
            }
        }

        if ($pagesFromControls === []) {
            return $schema;
        }

        if (!isset($schema['deviceConfig']) || !is_array($schema['deviceConfig'])) {
            $schema['deviceConfig'] = ['devices' => [['pages' => []]]];
        }
        if (!isset($schema['deviceConfig']['devices'][0]) || !is_array($schema['deviceConfig']['devices'][0])) {
            $schema['deviceConfig']['devices'][0] = ['pages' => []];
        }
        $existingPages = isset($schema['deviceConfig']['devices'][0]['pages'])
            && is_array($schema['deviceConfig']['devices'][0]['pages'])
            ? $schema['deviceConfig']['devices'][0]['pages']
            : [];

        foreach ($pagesFromControls as $pageName => $frames) {
            if (!isset($existingPages[$pageName]) || !is_array($existingPages[$pageName])
                || self::deviceConfigPageScore($frames) > self::deviceConfigPageScore($existingPages[$pageName])
            ) {
                $existingPages[$pageName] = $frames;
            }
        }

        $schema['deviceConfig']['devices'][0]['pages'] = $existingPages;
        $schema['deviceConfig']['devices'][0]['total_pages'] = (string) max(1, count($existingPages));

        if (isset($schema['state']['device']) && is_array($schema['state']['device'])) {
            $schema['state']['device']['config'] = $schema['deviceConfig'];
        }
        if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
            $schema['deviceState']['config'] = $schema['deviceConfig'];
        }
        if (isset($schema['node_details'][0]) && is_array($schema['node_details'][0])) {
            $schema['node_details'][0]['config'] = $schema['deviceConfig'];
        }

        return $schema;
    }

    private static function orderNodeDetailsForNode(array $nodeDetails, $nodeId)
    {
        $nodeId = trim((string) $nodeId);
        if ($nodeId === '') {
            return [];
        }
        $matched = null;
        foreach ($nodeDetails as $detail) {
            if (!is_array($detail)) {
                continue;
            }
            $id = trim((string) ($detail['id'] ?? $detail['node_id'] ?? ''));
            if ($id !== '' && strcasecmp($id, $nodeId) === 0) {
                $matched = $detail;
                break;
            }
        }
        if (!$matched) {
            return [];
        }
        $ordered = [$matched];
        $matchedId = strtolower(trim((string) ($matched['id'] ?? $matched['node_id'] ?? '')));
        foreach ($nodeDetails as $detail) {
            if (!is_array($detail)) {
                continue;
            }
            $id = strtolower(trim((string) ($detail['id'] ?? $detail['node_id'] ?? '')));
            if ($id === $matchedId) {
                continue;
            }
            $ordered[] = $detail;
        }
        return $ordered;
    }

    private static function fetchTestNodesPayload($nodeId)
    {
        static $cache = [];
        $nodeId = trim((string) $nodeId);
        $cacheKey = strtolower($nodeId);
        if ($nodeId === '') {
            return [];
        }
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $encoded = rawurlencode($nodeId);
        $urls = [
            'https://efactor.tech/v8/newapi.php?action=test_nodes&node_id=' . $encoded,
        ];
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        if ($host !== '' && stripos($host, 'efactor.tech') === false) {
            $urls[] = $scheme . '://' . $host . '/v8/newapi.php?action=test_nodes&node_id=' . $encoded;
        }
        $urls[] = 'http://localhost/v8/newapi.php?action=test_nodes&node_id=' . $encoded;

        $seen = [];
        foreach ($urls as $url) {
            if (isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            $payload = self::decodeTestNodesPayload(self::fetchUrl($url));
            $details = isset($payload['node_details']) && is_array($payload['node_details'])
                ? $payload['node_details']
                : [];
            if (self::orderNodeDetailsForNode($details, $nodeId)) {
                $cache[$cacheKey] = $payload;
                return $payload;
            }
        }

        $cache[$cacheKey] = [];
        return [];
    }

    private static function decodeTestNodesPayload($raw)
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && isset($decoded['node_details'])) {
            return $decoded;
        }
        $start = strpos($raw, '{"node_details"');
        if ($start === false) {
            return [];
        }
        $decoded = json_decode(substr($raw, $start), true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function fetchUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            if ($curl !== false) {
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_HTTPHEADER => ['Accept: application/json'],
                ]);
                $raw = curl_exec($curl);
                $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if (is_string($raw) && $raw !== '' && $httpCode >= 200 && $httpCode < 400) {
                    return $raw;
                }
            }
        }
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $raw = @file_get_contents($url, false, $context);
        return is_string($raw) ? $raw : '';
    }

    /**
     * Rewrites every node/model reference so the served document belongs to the
     * device the app opened, not to the device that happened to be open in Studio.
     * `testNodes` subtrees are left alone because they intentionally point elsewhere.
     */
    private static function applyRequestedNode(array $schema, $modelNo, $nodeId)
    {
        $schema = self::rewriteNodeReferences($schema, $modelNo, $nodeId);

        if ($nodeId !== '') {
            $schema['nodes'] = $nodeId;
            if (isset($schema['node_id']) && is_array($schema['node_id'])) {
                $schema['node_id']['id'] = $nodeId;
                $schema['node_id']['value'] = $nodeId;
            }
            $stored = isset($schema['node_details']) && is_array($schema['node_details'])
                ? $schema['node_details']
                : [];
            $ordered = self::orderNodeDetailsForNode($stored, $nodeId);
            if ($ordered) {
                $schema['node_details'] = $ordered;
            }
        }

        if ($modelNo !== '') {
            if (isset($schema['node_id']) && is_array($schema['node_id'])) {
                $schema['node_id']['modelNo'] = $modelNo;
                $schema['node_id']['model_id'] = $modelNo;
            }
            if (!isset($schema['meta']) || !is_array($schema['meta'])) {
                $schema['meta'] = [];
            }
            if (!isset($schema['meta']['app']) || !is_array($schema['meta']['app'])) {
                $schema['meta']['app'] = [];
            }
            $schema['meta']['app']['modelNo'] = $modelNo;
        }

        if ($nodeId !== '') {
            $schema['meta']['app']['nodeId'] = $nodeId;
        }

        return $schema;
    }

    private static function rewriteNodeReferences(array $node, $modelNo, $nodeId)
    {
        foreach ($node as $key => $child) {
            if (is_string($key) && strtolower($key) === 'testnodes') {
                continue;
            }

            if (is_array($child)) {
                $node[$key] = self::rewriteNodeReferences($child, $modelNo, $nodeId);
                continue;
            }

            if (!is_string($child) || $child === '') {
                continue;
            }

            $lowerKey = is_string($key) ? strtolower($key) : '';

            if ($nodeId !== '' && ($lowerKey === 'node_id' || $lowerKey === 'nodeid')) {
                $node[$key] = $nodeId;
                continue;
            }
            if ($modelNo !== '' && ($lowerKey === 'model_no' || $lowerKey === 'modelno' || $lowerKey === 'model_id')) {
                $node[$key] = $modelNo;
                continue;
            }

            $node[$key] = self::rewriteQueryString($child, $modelNo, $nodeId);
        }

        return $node;
    }

    private static function rewriteQueryString($value, $modelNo, $nodeId)
    {
        $value = self::forceHttpsUrl($value);

        if (strpos($value, '=') === false) {
            return $value;
        }

        if ($nodeId !== '' && stripos($value, 'node_id=') !== false) {
            $value = preg_replace(
                '/(^|[?&])node_id=[^&#]*/i',
                '${1}node_id=' . str_replace('$', '\\$', rawurlencode($nodeId)),
                $value
            );
        }
        if ($modelNo !== '' && stripos($value, 'model_no=') !== false) {
            $value = preg_replace(
                '/(^|[?&])model_no=[^&#]*/i',
                '${1}model_no=' . str_replace('$', '\\$', rawurlencode($modelNo)),
                $value
            );
        }

        return $value;
    }

    private static function forceHttpsUrl($value)
    {
        $value = (string) $value;
        if (stripos($value, 'http://') !== 0) {
            return $value;
        }
        $host = strtolower((string) parse_url($value, PHP_URL_HOST));
        if ($host === '' || in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return $value;
        }

        return 'https://' . substr($value, 7);
    }

    // --------------------------------------------------------------- frames

    /**
     * Repair command/response/error slots on device frames only.
     * Walking the full schema (preview HTML, icon paths, duplicated trees)
     * exhausts PHP memory on production (128MB).
     */
    private static function normalizeDeviceFrames(array $schema, array &$report)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }
        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            if (isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
                $schema['controls'][$index]['deviceFrame'] = self::normalizeFrameTree($control['deviceFrame'], $report);
            }
            if (isset($control['protocolMeta']['frame']) && is_array($control['protocolMeta']['frame'])) {
                $schema['controls'][$index]['protocolMeta']['frame'] = self::normalizeFrameTree(
                    $control['protocolMeta']['frame'],
                    $report
                );
            }
        }

        return $schema;
    }

    private static function schemaWalkSkipKey($key)
    {
        static $skip = [
            'exactHtmlPreview' => true,
            'icons' => true,
            'catalog' => true,
            'registerComponents' => true,
            'deviceFrame' => true,
            'protocolMeta' => true,
            'css' => true,
            'paths' => true,
            'assets' => true,
            'deviceConfig' => true,
            'deviceState' => true,
            'node_details' => true,
            'dataSources' => true,
            'permissions' => true,
            'state' => true,
            'actions' => true,
            'componentTemplates' => true,
            'extensions' => true,
            'html' => true,
        ];

        return is_string($key) && isset($skip[$key]);
    }

    private static function normalizeFrameTree(array $node, array &$report, $frameName = '', $depth = 0)
    {
        if ($depth > 20) {
            return $node;
        }
        $localName = $frameName;
        if (isset($node['name']) && is_string($node['name']) && $node['name'] !== '') {
            $localName = $node['name'];
        }

        if (isset($node['commands']) && self::isList($node['commands'])) {
            foreach (['commands', 'response', 'error'] as $bucket) {
                if (isset($node[$bucket]) && self::isList($node[$bucket])) {
                    $node[$bucket] = self::normalizeFrameFields($node[$bucket], $localName, $bucket, $report);
                }
            }
        }

        foreach ($node as $key => $child) {
            if (is_array($child) && !self::schemaWalkSkipKey($key)) {
                $node[$key] = self::normalizeFrameTree($child, $report, $localName, $depth + 1);
            }
        }

        return $node;
    }

    /**
     * Drops slots that carry no name, no value and no byte position, then gives every
     * surviving slot a positive byte_start_position and a no_of_bytes of at least 1.
     */
    private static function normalizeFrameFields(array $fields, $frameName, $bucket, array &$report)
    {
        $kept = [];
        foreach ($fields as $index => $field) {
            if (!is_array($field)) {
                continue;
            }
            if (self::isEmptyFrameSlot($field)) {
                $report['droppedFrameSlots'][] = ($frameName !== '' ? $frameName : 'frame')
                    . '.' . $bucket . '[' . $index . ']';
                continue;
            }
            $kept[] = $field;
        }

        $nextPosition = 1;
        $normalized = [];
        foreach ($kept as $field) {
            $name = self::frameFieldName($field);

            $bytes = self::resolveCommandByteCount($field);

            $start = isset($field['byte_start_position']) ? (int) $field['byte_start_position'] : 0;

            // A Trigger slot legitimately sits at position 0; everything else must be positive.
            if ($start === 0 && $name === 'trigger') {
                $field['no_of_bytes'] = $bytes;
                if (isset($field['byteLength']) || array_key_exists('byteLength', $field)) {
                    $field['byteLength'] = $bytes;
                }
                $normalized[] = $field;
                continue;
            }

            if ($start < 1) {
                $start = $nextPosition;
                $report['repairedByteFields'][] = ($frameName !== '' ? $frameName : 'frame')
                    . '.' . $bucket . '.' . (self::frameFieldLabel($field) ?: 'field');
            }

            $field['no_of_bytes'] = $bytes;
            if (isset($field['byteLength']) || array_key_exists('byteLength', $field)) {
                $field['byteLength'] = $bytes;
            }
            $field['byte_start_position'] = $start;

            // Rule 1 (hex-length = 2 * no_of_bytes) for any protocol-looking value.
            $wire = isset($field['value']) ? $field['value'] : null;
            if (($wire === null || $wire === '' || $wire === [])
                && isset($field['asperui_value'])
                && self::looksLikeProtocolHex($field['asperui_value'])
            ) {
                $wire = $field['asperui_value'];
            }
            if (self::looksLikeProtocolHex($wire)) {
                $padded = self::normalizeProtocolHex($wire, $bytes);
                if ($padded !== '' && $padded !== (string) $wire) {
                    $field['value'] = $padded;
                    $report['repairedByteFields'][] = ($frameName !== '' ? $frameName : 'frame')
                        . '.' . $bucket . '.' . (self::frameFieldLabel($field) ?: 'field') . '.hexWidth';
                } elseif ($padded !== '') {
                    $field['value'] = $padded;
                }
            }

            $nextPosition = max($nextPosition, $start + $bytes);
            $normalized[] = $field;
        }

        return $normalized;
    }

    private static function isEmptyFrameSlot(array $field)
    {
        $label = self::frameFieldLabel($field);
        $hasName = $label !== '' && !preg_match('/^field_\d+$/i', $label) && strtolower($label) !== 'none';

        $value = isset($field['value']) ? $field['value'] : null;
        $hasValue = $value !== null && $value !== '' && $value !== [];

        $start = isset($field['byte_start_position']) ? (int) $field['byte_start_position'] : 0;
        $uiType = strtolower(trim((string) (isset($field['ui_type']) ? $field['ui_type'] : '')));
        $hasUi = $uiType !== '' && $uiType !== 'none';

        return !$hasName && !$hasValue && !$hasUi && $start < 1;
    }

    private static function frameFieldLabel(array $field)
    {
        foreach (['Field_description', 'field_description', 'default_name'] as $key) {
            if (isset($field[$key]) && trim((string) $field[$key]) !== '') {
                return trim((string) $field[$key]);
            }
        }

        return '';
    }

    private static function frameFieldName(array $field)
    {
        return strtolower(preg_replace('/[\s_]+/', '', self::frameFieldLabel($field)));
    }

    // ------------------------------------------------------------ card styles

    private static function applyCardStyles(array $schema, array &$report)
    {
        $template = self::findCardStyleTemplate($schema);
        if (empty($template)) {
            $template = self::designSystemCardStyle($schema);
        }
        if (empty($template)) {
            return $schema;
        }

        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::fillCardStyles($schema['ui']['widgetTree'], $template, $report);
        }
        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $index => $control) {
                if (is_array($control)) {
                    self::fillCardStyles($schema['controls'][$index], $template, $report);
                }
            }
        }
        if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
            self::fillCardStyles($schema['extensions']['serverDrivenUi'], $template, $report);
        }

        return $schema;
    }

    /** Reuses a card style already published by a complete control (usually a toggle). */
    private static function findCardStyleTemplate(array $schema)
    {
        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $control) {
                if (!is_array($control)) {
                    continue;
                }
                $found = self::findCardStyleTemplateInNode($control);
                if (!empty($found)) {
                    return $found;
                }
            }
        }
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            $found = self::findCardStyleTemplateInNode($schema['ui']['widgetTree']);
            if (!empty($found)) {
                return $found;
            }
        }

        return [];
    }

    private static function findCardStyleTemplateInNode(array $node, $depth = 0)
    {
        if ($depth > 40) {
            return [];
        }
        if (isset($node['cardStyle']) && is_array($node['cardStyle']) && !empty($node['cardStyle'])) {
            return $node['cardStyle'];
        }

        foreach ($node as $key => $child) {
            if (!is_array($child) || self::schemaWalkSkipKey($key)) {
                continue;
            }
            $found = self::findCardStyleTemplateInNode($child, $depth + 1);
            if (!empty($found)) {
                return $found;
            }
        }

        return [];
    }

    private static function designSystemCardStyle(array $schema)
    {
        foreach ([
            ['designSystem', 'componentStyles', 'parameterCard'],
            ['extensions', 'serverDrivenUi', 'designSystem', 'componentStyles', 'parameterCard'],
        ] as $path) {
            $cursor = $schema;
            foreach ($path as $segment) {
                if (!is_array($cursor) || !isset($cursor[$segment])) {
                    $cursor = null;
                    break;
                }
                $cursor = $cursor[$segment];
            }
            if (is_array($cursor) && !empty($cursor)) {
                return $cursor;
            }
        }

        return [];
    }

    private static function fillCardStyles(&$node, array $template, array &$report, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        if (self::isParameterCardNode($node)) {
            if (!isset($node['properties']) || !is_array($node['properties'])) {
                $node['properties'] = [];
            }
            if (!isset($node['properties']['props']) || !is_array($node['properties']['props'])) {
                $node['properties']['props'] = [];
            }
            if (empty($node['properties']['props']['cardStyle']) || !is_array($node['properties']['props']['cardStyle'])) {
                $node['properties']['props']['cardStyle'] = $template;
                $report['cardStylesApplied']++;
            }
            if (empty($node['properties']['props']['styleRef'])) {
                $node['properties']['props']['styleRef'] = 'designSystem.componentStyles.parameterCard';
            }
        }

        foreach ($node as $key => $child) {
            if (!is_array($child) || self::schemaWalkSkipKey($key)) {
                continue;
            }
            self::fillCardStyles($node[$key], $template, $report, $depth + 1);
        }
    }

    private static function isParameterCardNode(array $node)
    {
        if (isset($node['type']) && $node['type'] === 'ParameterCard') {
            return true;
        }
        if (isset($node['renderAs']) && $node['renderAs'] === 'ParameterCard' && isset($node['properties'])) {
            return true;
        }
        if (isset($node['properties']['renderAs']) && $node['properties']['renderAs'] === 'ParameterCard') {
            return true;
        }

        return false;
    }

    // ----------------------------------------------------------------- icons

    private static function normalizeIcons(array $schema, array &$report)
    {
        $valid = self::availableIconNames($schema);
        if (empty($valid)) {
            return $schema;
        }

        $default = trim((string) (isset($schema['icons']['default']) ? $schema['icons']['default'] : ''));
        if ($default === '' || !isset($valid[$default])) {
            $default = isset($valid['gauge']) ? 'gauge' : key($valid);
        }

        self::replaceIconTokens($schema, $valid, $default, $report);

        return $schema;
    }

    private static function availableIconNames(array $schema)
    {
        $names = [];

        if (isset($schema['icons']['library']['paths']) && is_array($schema['icons']['library']['paths'])) {
            foreach (array_keys($schema['icons']['library']['paths']) as $name) {
                $names[(string) $name] = true;
            }
        }
        if (isset($schema['icons']['library']['icons']) && is_array($schema['icons']['library']['icons'])) {
            foreach ($schema['icons']['library']['icons'] as $name) {
                if (is_string($name) && $name !== '') {
                    $names[$name] = true;
                }
            }
        }

        return $names;
    }

    private static function replaceIconTokens(&$node, array $valid, $default, array &$report, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        foreach ($node as $key => $child) {
            if (is_array($child)) {
                if (self::schemaWalkSkipKey($key)) {
                    continue;
                }
                self::replaceIconTokens($node[$key], $valid, $default, $report, $depth + 1);
                continue;
            }

            if (!is_string($child) || $child === '' || !is_string($key)) {
                continue;
            }

            $lowerKey = strtolower($key);
            if ($lowerKey !== 'icon' && $lowerKey !== 'metaicon' && $lowerKey !== 'iconname') {
                continue;
            }

            // theme.colors.icon is a CSS color, not an icon token.
            if (preg_match('/^(#([0-9a-f]{3,8})|rgba?\(|transparent|hsla?\()/i', $child)) {
                continue;
            }

            $resolved = self::resolveIconToken($child, $valid, $default);
            if ($resolved !== $child) {
                $report['iconFallbacks'][$child] = $resolved;
                $node[$key] = $resolved;
            }
        }
    }

    private static function resolveIconToken($token, array $valid, $default)
    {
        if (isset($valid[$token])) {
            return $token;
        }

        $lookup = strtolower(trim($token));
        // Never substitute the send icon with the library default (gauge).
        if ($lookup === 'send') {
            return 'send';
        }
        if (isset(self::$iconAliases[$lookup]) && isset($valid[self::$iconAliases[$lookup]])) {
            return self::$iconAliases[$lookup];
        }

        $kebab = str_replace('_', '-', $lookup);
        if (isset($valid[$kebab])) {
            return $kebab;
        }

        return $default;
    }

    private static function ensureSendIcon(array $schema)
    {
        return self::ensureRequiredIcons($schema);
    }

    private static function ensureRequiredIcons(array $schema)
    {
        $required = [
            'send' => '<line x1="22" x2="11" y1="2" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>',
            'gauge' => '<path d="m12 14 4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
            'bolt' => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"></path>',
            'power' => '<path d="M12 2v10"></path><path d="M18.4 6.6a9 9 0 1 1-12.77.04"></path>',
            'timer' => '<circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2 2"></path><path d="M5 3 2 6"></path><path d="m22 6-3-3"></path><path d="M6.38 18.7 4 21"></path><path d="M17.64 18.67 20 21"></path>',
            'clock' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path>',
            'settings' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.52a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.52a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
            'key' => '<path d="M21 2 9 14m12 4-8.5-8.5a1.5 1.5 0 0 1 0-2.12L6.5 2.5a1.5 1.5 0 0 1 2.12 0l11.38 11.38a1.5 1.5 0 0 1 0 2.12Z"></path><circle cx="6" cy="16" r="2"></circle>',
            'trash-2' => '<path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m4 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>',
            'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path>',
            'help-circle' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'inbox' => '<rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="M2 6h3a6 6 0 0 1 6-6 6 6 0 0 1 6 6h3"></path>',
            'info' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'chevron-left' => '<path d="m15 18-6-6 6-6"></path>',
            'arrow-left' => '<path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>',
            'arrow-right' => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
        ];
        if (!isset($schema['icons']) || !is_array($schema['icons'])) {
            $schema['icons'] = [];
        }
        if (!isset($schema['icons']['library']) || !is_array($schema['icons']['library'])) {
            $schema['icons']['library'] = [
                'icons' => [],
                'paths' => [],
            ];
        }
        if (!isset($schema['icons']['library']['icons']) || !is_array($schema['icons']['library']['icons'])) {
            $schema['icons']['library']['icons'] = [];
        }
        if (!isset($schema['icons']['library']['paths']) || !is_array($schema['icons']['library']['paths'])) {
            $schema['icons']['library']['paths'] = [];
        }
        foreach ($required as $name => $path) {
            if (!in_array($name, $schema['icons']['library']['icons'], true)) {
                $schema['icons']['library']['icons'][] = $name;
            }
            if (empty($schema['icons']['library']['paths'][$name])) {
                $schema['icons']['library']['paths'][$name] = $path;
            }
        }
        $schema['icons']['library']['count'] = count($schema['icons']['library']['icons']);

        return $schema;
    }

    private static function isCssColor($value)
    {
        $raw = trim((string) $value);

        return $raw !== '' && (bool) preg_match('/^(#([0-9a-f]{3,8})|rgba?\(|transparent|hsla?\()/i', $raw);
    }

    private static function isForcedWhiteHeader($value)
    {
        $raw = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));
        if (strlen($raw) === 8) {
            $raw = substr($raw, -6);
        }
        if (strlen($raw) === 3) {
            $raw = $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
        }

        return $raw === 'FFFFFF';
    }

    private static function colorLuminance($value)
    {
        $raw = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));
        if (strlen($raw) === 8) {
            $raw = substr($raw, -6);
        }
        if (strlen($raw) === 3) {
            $raw = $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
        }
        if (strlen($raw) !== 6) {
            return 0;
        }

        $r = hexdec(substr($raw, 0, 2));
        $g = hexdec(substr($raw, 2, 2));
        $b = hexdec(substr($raw, 4, 2));

        return (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
    }

    private static function colorAt(array $schema, array $path)
    {
        $cursor = $schema;
        foreach ($path as $key) {
            if (!is_array($cursor) || !array_key_exists($key, $cursor)) {
                return null;
            }
            $cursor = $cursor[$key];
        }

        return self::isCssColor($cursor) ? (string) $cursor : null;
    }

    private static function resolveTopbarColor(array $schema)
    {
        $candidates = [];
        $paths = [
            ['designSystem', 'colors', 'topbar'],
            ['theme', 'tokens', 'topbarColor'],
            ['theme', 'tokens', 'colors', 'topbar'],
            ['theme', 'colors', 'topbar'],
            ['mobileView', 'header', 'backgroundColor'],
        ];
        foreach ($paths as $path) {
            $color = self::colorAt($schema, $path);
            if ($color !== null) {
                $candidates[] = $color;
            }
        }
        foreach ($candidates as $color) {
            if (!self::isForcedWhiteHeader($color)) {
                return $color;
            }
        }

        return $candidates[0] ?? '#0F172A';
    }

    private static function resolveValueTextColor(array $schema)
    {
        foreach ([
            ['theme', 'colors', 'text'],
            ['theme', 'colors', 'textColor'],
            ['designSystem', 'colors', 'text'],
            ['theme', 'tokens', 'colors', 'text'],
        ] as $path) {
            $color = self::colorAt($schema, $path);
            if ($color !== null) {
                return $color;
            }
        }

        return '#0F172A';
    }

    private static function applyCanonicalHeader(array $schema)
    {
        $bg = self::resolveTopbarColor($schema);
        $secondary = self::colorAt($schema, ['theme', 'colors', 'secondary']) ?: $bg;
        $dark = self::colorLuminance($bg) < 160;
        $fg = $dark ? '#FFFFFF' : '#0F172A';
        $muted = $dark ? 'rgba(255,255,255,0.72)' : '#64748B';
        if (!isset($schema['theme']) || !is_array($schema['theme'])) {
            $schema['theme'] = [];
        }
        if (!isset($schema['theme']['colors']) || !is_array($schema['theme']['colors'])) {
            $schema['theme']['colors'] = [];
        }
        $schema['theme']['colors']['topbar'] = $bg;
        if (isset($schema['theme']['tokens']) && is_array($schema['theme']['tokens'])) {
            $schema['theme']['tokens']['topbarColor'] = $bg;
            if (isset($schema['theme']['tokens']['colors']) && is_array($schema['theme']['tokens']['colors'])) {
                $schema['theme']['tokens']['colors']['topbar'] = $bg;
            }
        }
        if (isset($schema['designSystem']['colors']) && is_array($schema['designSystem']['colors'])) {
            $schema['designSystem']['colors']['topbar'] = $bg;
        }
        if (isset($schema['mobileView']['header']) && is_array($schema['mobileView']['header'])) {
            $schema['mobileView']['header']['backgroundColor'] = $bg;
            $schema['mobileView']['header']['foregroundColor'] = $fg;
            $schema['mobileView']['header']['titleColor'] = $fg;
            $schema['mobileView']['header']['className'] = 'app-topbar';
        }
        if (isset($schema['designSystem']['componentStyles']['appBar']) && is_array($schema['designSystem']['componentStyles']['appBar'])) {
            $schema['designSystem']['componentStyles']['appBar']['backgroundColor'] = $bg;
            $schema['designSystem']['componentStyles']['appBar']['foregroundColor'] = $fg;
            $schema['designSystem']['componentStyles']['appBar']['className'] = 'app-topbar';
            $schema['designSystem']['componentStyles']['appBar']['background'] = [
                'type' => 'linearGradient',
                'angle' => 145,
                'color' => $bg,
                'fallbackColor' => $bg,
                'colors' => [$bg, $secondary],
            ];
        }
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::rewriteWidgetTreeHeader($schema['ui']['widgetTree'], $bg, $fg, $muted, $secondary);
        }
        if (isset($schema['exactHtmlPreview']['css']) && is_string($schema['exactHtmlPreview']['css'])) {
            $css = $schema['exactHtmlPreview']['css'];
            $css = preg_replace(
                '/\.app-topbar\{[^}]*\}/',
                '.app-topbar{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:10px;align-items:center;min-height:84px;padding:14px 16px 12px;background:linear-gradient(145deg,' . $bg . ',' . $secondary . ');color:' . $fg . ';box-shadow:0 18px 38px rgba(18,50,70,.16);}',
                $css,
                1
            );
            $schema['exactHtmlPreview']['css'] = $css;
        }

        return $schema;
    }

    private static function rewriteWidgetTreeHeader(&$node, $bg, $fg, $muted, $secondary = null, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $secondary = $secondary ?: $bg;
        $id = strtolower(trim((string) (isset($node['id']) ? $node['id'] : '')));
        if (in_array($id, ['app_topbar', 'app-bar', 'appbar'], true)) {
            if (!isset($node['properties']) || !is_array($node['properties'])) {
                $node['properties'] = [];
            }
            $node['properties']['className'] = 'app-topbar';
            $node['className'] = 'app-topbar';
            if (!isset($node['style']) || !is_array($node['style'])) {
                $node['style'] = [];
            }
            $node['style']['backgroundColor'] = $bg;
            $node['style']['color'] = $fg;
            $node['style']['background'] = 'linear-gradient(145deg,' . $bg . ',' . $secondary . ')';
            $node['style']['gradient'] = [
                'type' => 'linear',
                'angle' => 145,
                'begin' => 'topLeft',
                'end' => 'bottomRight',
                'colors' => [$bg, $secondary],
            ];
            $node['backgroundColor'] = $bg;
        }
        if (in_array($id, ['active_page_title', 'top_title', 'phone_back_text'], true)) {
            if (!isset($node['style']) || !is_array($node['style'])) {
                $node['style'] = [];
            }
            $node['style']['color'] = $fg;
            $node['style']['textColor'] = $fg;
        }
        if (in_array($id, ['model_subtitle', 'active_page_subtitle'], true)) {
            if (!isset($node['style']) || !is_array($node['style'])) {
                $node['style'] = [];
            }
            $node['style']['color'] = $muted;
            $node['style']['textColor'] = $muted;
        }
        foreach ($node as $key => $child) {
            if (!is_array($child) || self::schemaWalkSkipKey($key)) {
                continue;
            }
            self::rewriteWidgetTreeHeader($node[$key], $bg, $fg, $muted, $secondary, $depth + 1);
        }
    }

    private static function applyCanonicalValueColors(array $schema)
    {
        $text = self::resolveValueTextColor($schema);
        if (isset($schema['theme']['colors']) && is_array($schema['theme']['colors'])) {
            $schema['theme']['colors']['text'] = $text;
        }
        if (isset($schema['designSystem']['componentStyles']['parameterCard']['parts']['value']) && is_array($schema['designSystem']['componentStyles']['parameterCard']['parts']['value'])) {
            $schema['designSystem']['componentStyles']['parameterCard']['parts']['value']['color'] = $text;
            $schema['designSystem']['componentStyles']['parameterCard']['parts']['value']['fontWeight'] = 700;
        }
        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as &$control) {
                if (!is_array($control)) {
                    continue;
                }
                self::restoreReadOnlyValueColors($control, $text);
            }
            unset($control);
        }
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::rewriteWidgetTreeValueColors($schema['ui']['widgetTree'], $text);
        }
        if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
            self::rewriteWidgetTreeValueColors($schema['extensions']['serverDrivenUi'], $text);
        }

        return $schema;
    }

    private static function restoreReadOnlyValueColors(array &$control, $text)
    {
        $readOnly = !empty($control['readOnly']) || (isset($control['interactive']) && $control['interactive'] === false);
        if (isset($control['styles']) && is_array($control['styles'])) {
            $control['styles']['textColor'] = $text;
            $control['styles']['valueColor'] = $text;
        }
        if (!isset($control['component']) || !is_array($control['component'])) {
            return;
        }
        if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
            $control['component']['props'] = [];
        }
        $control['component']['props']['textColor'] = $text;
        $control['component']['props']['valueColor'] = $text;
        if (!empty($control['component']['props']['cardStyle']['fieldValue']) && is_array($control['component']['props']['cardStyle']['fieldValue'])) {
            $control['component']['props']['cardStyle']['fieldValue']['color'] = $text;
            $control['component']['props']['cardStyle']['fieldValue']['fontWeight'] = 700;
        }
        if ($readOnly) {
            $control['component']['props']['readOnly'] = true;
            $control['component']['props']['enabled'] = true;
            $control['component']['props']['interactive'] = false;
            $control['component']['props']['disabledOpacity'] = 1;
            if (!isset($control['component']['style']) || !is_array($control['component']['style'])) {
                $control['component']['style'] = [];
            }
            $control['component']['style']['color'] = $text;
            $control['component']['style']['textColor'] = $text;
            $control['component']['style']['disabled'] = [
                'opacity' => 1,
                'color' => $text,
                'textColor' => $text,
            ];
        }
        if (isset($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
            self::rewriteWidgetTreeValueColors($control['component']['widgetTree'], $text);
        }
    }

    private static function rewriteWidgetTreeValueColors(&$node, $text, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $type = strtolower(trim((string) (isset($node['type']) ? $node['type'] : (isset($node['flutterWidget']) ? $node['flutterWidget'] : ''))));
        $isValueNode = in_array($type, ['text', 'textfield', 'parametercard', 'display'], true)
            || in_array(strtolower(trim((string) (isset($node['id']) ? $node['id'] : ''))), ['field_value', 'param_value', 'active_value'], true);

        $readOnly = false;
        foreach (['props', 'properties'] as $propKey) {
            if (!isset($node[$propKey]) || !is_array($node[$propKey])) {
                continue;
            }
            if (($node[$propKey]['readOnly'] ?? null) === true || ($node[$propKey]['interactive'] ?? null) === false) {
                $readOnly = true;
            }
        }
        if (($node['readOnly'] ?? null) === true || ($node['interactive'] ?? null) === false) {
            $readOnly = true;
        }

        if ($isValueNode || $readOnly) {
            if (!isset($node['style']) || !is_array($node['style'])) {
                $node['style'] = [];
            }
            if ($isValueNode) {
                $node['style']['color'] = $text;
                $node['style']['textColor'] = $text;
                $node['style']['valueColor'] = $text;
            }
            if ($readOnly) {
                $node['enabled'] = true;
                $node['disabled'] = false;
                if (!isset($node['props']) || !is_array($node['props'])) {
                    $node['props'] = [];
                }
                $node['props']['enabled'] = true;
                $node['props']['readOnly'] = true;
                $node['props']['interactive'] = false;
                $node['props']['disabledOpacity'] = 1;
                $node['props']['textColor'] = $text;
                $node['props']['valueColor'] = $text;
                $node['props']['disabledTextColor'] = $text;
                $node['props']['readOnlyTextColor'] = $text;
                $node['style']['disabled'] = [
                    'opacity' => 1,
                    'color' => $text,
                    'textColor' => $text,
                ];
                if (isset($node['properties']) && is_array($node['properties'])) {
                    $node['properties']['enabled'] = true;
                    $node['properties']['disabledOpacity'] = 1;
                    if (isset($node['properties']['props']) && is_array($node['properties']['props'])) {
                        $node['properties']['props']['enabled'] = true;
                        $node['properties']['props']['textColor'] = $text;
                        $node['properties']['props']['valueColor'] = $text;
                    }
                }
            }
        }

        foreach (['children', 'widgetTree'] as $key) {
            if (empty($node[$key]) || !is_array($node[$key])) {
                continue;
            }
            if (isset($node[$key][0]) || array_keys($node[$key]) === range(0, count($node[$key]) - 1)) {
                foreach ($node[$key] as $index => $child) {
                    if (is_array($child)) {
                        self::rewriteWidgetTreeValueColors($node[$key][$index], $text, $depth + 1);
                    }
                }
            } else {
                self::rewriteWidgetTreeValueColors($node[$key], $text, $depth + 1);
            }
        }
    }

    /**
     * Flutter rejects schemaSlider when showMinMaxLabels, variant "timer" and a
     * kicker are set but these style fields are absent. Only those fields are written.
     */
    private static function timerSliderStyleContract()
    {
        return [
            'labelColor' => '#64748B',
            'valueColor' => '#1E293B',
            'rangeFontSize' => 12,
            'currentValueFontSize' => 14,
            'kickerColor' => '#64748B',
            'kickerFontSize' => 12,
        ];
    }

    private static function applyTimerSliderStyleContract(array $schema)
    {
        $style = self::timerSliderStyleContract();

        if (isset($schema['components']['catalog']) && is_array($schema['components']['catalog'])) {
            foreach ($schema['components']['catalog'] as &$entry) {
                if (!is_array($entry)) {
                    continue;
                }
                $id = strtolower(str_replace(['_', '-'], '', (string) (isset($entry['id']) ? $entry['id'] : '')));
                if ($id !== 'autotriggertimer' || !isset($entry['widgetTree']) || !is_array($entry['widgetTree'])) {
                    continue;
                }
                self::patchTimerSliderStyleNodes($entry['widgetTree'], $style);
            }
            unset($entry);
        }

        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as &$control) {
                if (!is_array($control) || !self::controlIsTimerSlider($control)) {
                    continue;
                }
                if (isset($control['component']['props']) && is_array($control['component']['props'])
                    && self::timerSliderPropsNeedStyle($control['component']['props'])) {
                    $control['component']['props'] = array_merge($control['component']['props'], $style);
                }
                if (isset($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
                    self::patchTimerSliderStyleNodes($control['component']['widgetTree'], $style);
                }
            }
            unset($control);
        }

        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::patchTimerSliderStyleNodes($schema['ui']['widgetTree'], $style);
        }
        if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
            self::patchTimerSliderStyleNodes($schema['extensions']['serverDrivenUi'], $style);
        }
        if (isset($schema['extensions']['universalUi']) && is_array($schema['extensions']['universalUi'])) {
            self::patchTimerSliderStyleNodes($schema['extensions']['universalUi'], $style);
        }

        return $schema;
    }

    private static function controlIsTimerSlider(array $control)
    {
        $catalog = strtolower(str_replace(['_', '-'], '', (string) (isset($control['catalogComponentType']) ? $control['catalogComponentType'] : '')));
        if ($catalog === 'autotriggertimer') {
            return true;
        }
        $props = isset($control['component']['props']) && is_array($control['component']['props'])
            ? $control['component']['props']
            : [];
        if (self::timerSliderPropsNeedStyle($props)) {
            return true;
        }
        $haystack = strtolower(
            (string) (isset($control['binding']) ? $control['binding'] : '') . ' '
            . (string) (isset($control['parameterName']) ? $control['parameterName'] : '') . ' '
            . (string) (isset($control['id']) ? $control['id'] : '')
        );
        if (strpos($haystack, 'auto_trigger_with_timer') === false && strpos($haystack, 'autotriggertimer') === false) {
            return false;
        }
        $type = strtolower((string) (isset($control['componentType']) ? $control['componentType'] : ''));
        $widget = strtolower((string) (
            isset($control['flutterWidget'])
                ? $control['flutterWidget']
                : (isset($control['component']['flutterWidget']) ? $control['component']['flutterWidget'] : '')
        ));

        return $type === 'slider' || strpos($widget, 'slider') !== false;
    }

    private static function timerSliderPropsNeedStyle(array $props)
    {
        $variant = strtolower(trim((string) (isset($props['variant']) ? $props['variant'] : '')));
        $kicker = strtolower(trim((string) (isset($props['kicker']) ? $props['kicker'] : '')));

        return $variant === 'timer' || $kicker === 'timer';
    }

    private static function patchTimerSliderStyleNodes(&$node, array $style, $depth = 0)
    {
        if (!is_array($node) || $depth > 40) {
            return;
        }
        $type = strtolower(trim((string) (isset($node['type']) ? $node['type'] : '')));
        if ($type === 'schemaslider' || $type === 'slider') {
            if (isset($node['properties']['props']) && is_array($node['properties']['props'])
                && self::timerSliderPropsNeedStyle($node['properties']['props'])) {
                $node['properties']['props'] = array_merge($node['properties']['props'], $style);
            }
            if (isset($node['props']) && is_array($node['props']) && self::timerSliderPropsNeedStyle($node['props'])) {
                $node['props'] = array_merge($node['props'], $style);
            }
        }
        foreach ($node as &$child) {
            if (is_array($child)) {
                self::patchTimerSliderStyleNodes($child, $style, $depth + 1);
            }
        }
        unset($child);
    }

    private static function normalizeControlContracts(array $schema)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }
        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $id = trim((string) (isset($control['id']) ? $control['id'] : ''));
            if ($id !== '') {
                $control['action'] = 'update_' . trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $id), '_');
            }
            $type = strtolower(str_replace(['_', '-'], '', (string) (isset($control['componentType']) ? $control['componentType'] : '')));
            $catalog = strtolower(str_replace(['_', '-'], '', (string) (isset($control['catalogComponentType']) ? $control['catalogComponentType'] : '')));
            $labels = [
                'autotriggertimer' => 'Auto Trigger Timer',
                'slider' => 'Slider',
                'switch' => 'Switch',
                'button' => 'Power Button',
                'elevatedbutton' => 'Elevated Button',
                'textfield' => 'Text Field',
                'entertext' => 'Text Field',
                'value' => 'Value',
            ];
            $expected = $catalog === 'autotriggertimer'
                ? 'Auto Trigger Timer'
                : (isset($labels[$type]) ? $labels[$type] : null);
            $currentLabel = strtolower(trim((string) (isset($control['componentLabel']) ? $control['componentLabel'] : '')));
            if ($expected !== null && ($currentLabel === '' || $currentLabel === 'auto' || $currentLabel === 'toggle switch' || $currentLabel === 'toggle' || ($currentLabel === 'switch' && $type !== 'switch'))) {
                $control['componentLabel'] = $expected;
            } elseif ($expected !== null && $type === 'textfield' && $currentLabel !== 'text field') {
                $control['componentLabel'] = $expected;
            } elseif ($expected !== null && ($catalog === 'autotriggertimer' || $type === 'slider' || $type === 'button') && $currentLabel !== strtolower($expected)) {
                $control['componentLabel'] = $expected;
            }

            $accessoryType = strtolower((string) (isset($control['rightAccessory']['type']) ? $control['rightAccessory']['type'] : ''));
            $wanted = null;
            if ($catalog === 'autotriggertimer' || $type === 'slider') {
                $wanted = 'sliderRange';
            } elseif ($type === 'textfield' || $type === 'entertext') {
                $wanted = 'textField';
            } elseif ($type === 'button') {
                $wanted = 'powerButton';
            } elseif ($type === 'elevatedbutton') {
                $wanted = 'elevatedButton';
            }
            $source = strtolower(trim((string) (isset($control['protocolMeta']['ui_type_source']) ? $control['protocolMeta']['ui_type_source'] : '')));
            $access = strtolower(trim((string) (isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : '')));
            $writable = !in_array($source, ['response', 'error'], true);
            if ($access !== '' && in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
                $writable = false;
            } elseif ($access !== '' && strpos($access, 'write') === false) {
                $writable = false;
            }

            if ($wanted !== null && ($accessoryType === '' || $accessoryType === 'toggle' || $accessoryType !== strtolower($wanted))) {
                $value = isset($control['value']) ? $control['value'] : (isset($control['initialValue']) ? $control['initialValue'] : '');
                if ($wanted === 'sliderRange') {
                    $min = isset($control['constraints']['minimum']) ? $control['constraints']['minimum'] : 0;
                    $max = isset($control['constraints']['maximum']) ? $control['constraints']['maximum'] : 255;
                    $control['rightAccessory'] = [
                        'type' => 'sliderRange',
                        'min' => $min,
                        'max' => $max,
                        'current' => is_numeric($value) ? $value : $min,
                        'value' => is_numeric($value) ? $value : $min,
                    ];
                } elseif ($wanted === 'textField') {
                    $control['rightAccessory'] = [
                        'type' => 'textField',
                        'value' => is_scalar($value) ? (string) $value : '',
                    ];
                    if (!isset($control['component']) || !is_array($control['component'])) {
                        $control['component'] = [];
                    }
                    if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                        $control['component']['props'] = [];
                    }
                    // Send button is only for writable text fields (Rule 3).
                    if ($writable) {
                        $control['uiParts'] = ['textField', 'sendButton'];
                        $control['component']['props']['sendButton'] = [
                            'visible' => true,
                            'label' => 'Send',
                        ];
                    } else {
                        $control['uiParts'] = ['textField'];
                        unset($control['component']['props']['sendButton']);
                    }
                } elseif ($wanted === 'powerButton') {
                    $control['rightAccessory'] = [
                        'type' => 'powerButton',
                        'checked' => in_array(strtolower((string) $value), ['1', 'true', 'on', 'yes'], true),
                    ];
                }
            }

            $control['writable'] = $writable;
            $control['readOnly'] = !$writable;
            $control['interactive'] = $writable;
            $control['enabled'] = $writable;
            // Rule 3: writable Send/input controls publish access explicitly.
            if ($writable) {
                $control['access'] = 'write';
                if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                    $control['protocolMeta'] = [];
                }
                $metaAccess = strtolower(trim((string) (isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : '')));
                if ($metaAccess === '' || strpos($metaAccess, 'write') === false) {
                    $control['protocolMeta']['access'] = $metaAccess === '' || $metaAccess === 'read' ? 'write' : 'Read/Write';
                }
            }
            if (isset($control['props']) && is_array($control['props'])) {
                $control['props']['readOnly'] = !$writable;
                $control['props']['enabled'] = $writable;
                $control['props']['interactive'] = $writable;
            }
            if (isset($control['component']) && is_array($control['component'])) {
                $control['component']['readOnly'] = !$writable;
                $control['component']['interactive'] = $writable;
                if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                    $control['component']['props']['readOnly'] = !$writable;
                    $control['component']['props']['enabled'] = $writable;
                    $control['component']['props']['interactive'] = $writable;
                    if (!$writable) {
                        unset($control['component']['props']['sendButton']);
                    }
                }
            }

            // Populate legacy/mobile contract fields without renaming existing keys.
            $control = self::hydrateControlLegacyFields($control);

            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    /**
     * Fill top-level name / type / commands / icon from fields the schema already has.
     */
    private static function hydrateControlLegacyFields(array $control)
    {
        $name = trim((string) (isset($control['name']) ? $control['name'] : ''));
        if ($name === '') {
            $name = trim((string) self::firstNonEmpty(
                isset($control['parameterName']) ? $control['parameterName'] : null,
                isset($control['parameter']) ? $control['parameter'] : null,
                isset($control['label']) ? $control['label'] : null,
                isset($control['deviceFrame']['name']) ? $control['deviceFrame']['name'] : null
            ));
            if ($name !== '') {
                $control['name'] = $name;
            }
        }

        $type = trim((string) (isset($control['type']) ? $control['type'] : ''));
        if ($type === '') {
            $type = self::canonicalControlTypeToken($control);
            if ($type !== '') {
                $control['type'] = $type;
            }
        }

        if (!isset($control['commands']) || $control['commands'] === null) {
            if (isset($control['deviceFrame']['commands']) && is_array($control['deviceFrame']['commands'])) {
                $control['commands'] = $control['deviceFrame']['commands'];
            } elseif (isset($control['protocolMeta']['frame']['commands']) && is_array($control['protocolMeta']['frame']['commands'])) {
                $control['commands'] = $control['protocolMeta']['frame']['commands'];
            }
        }

        $icon = isset($control['icon']) ? $control['icon'] : null;
        if ($icon === null || $icon === '') {
            $control['icon'] = self::defaultIconForControl($control);
        }

        return $control;
    }

    private static function canonicalControlTypeToken(array $control)
    {
        $raw = self::firstNonEmpty(
            isset($control['protocolMeta']['ui_type']) ? $control['protocolMeta']['ui_type'] : null,
            isset($control['componentType']) ? $control['componentType'] : null,
            isset($control['controlType']) ? $control['controlType'] : null,
            isset($control['catalogComponentType']) ? $control['catalogComponentType'] : null
        );
        $raw = strtolower(trim((string) $raw));
        if ($raw === '') {
            // Fall back to the id suffix: "1:power:command-2-toggle-switch"
            $id = trim((string) (isset($control['id']) ? $control['id'] : ''));
            if (preg_match('/:([^:]+)$/', $id, $m)) {
                $raw = strtolower($m[1]);
            }
        }
        $raw = preg_replace('/[\s_]+/', '-', $raw);
        $raw = preg_replace('/-+/', '-', $raw);
        $aliases = [
            'switch' => 'toggle-switch',
            'toggleswitch' => 'toggle-switch',
            'toggle' => 'toggle-switch',
            'autotriggertimer' => 'auto-trigger-with-timer',
            'auto-trigger-timer' => 'auto-trigger-with-timer',
            'text' => 'text',
            'textfield' => 'text',
            'entertext' => 'text',
            'button' => 'button',
            'elevatedbutton' => 'button',
            'slider' => 'slider',
            'trigger' => 'trigger',
            'segmented' => 'trigger',
        ];
        $compact = str_replace('-', '', $raw);
        if (isset($aliases[$compact])) {
            return $aliases[$compact];
        }
        if (isset($aliases[$raw])) {
            return $aliases[$raw];
        }

        return $raw;
    }

    private static function defaultIconForControl(array $control)
    {
        $type = strtolower((string) self::canonicalControlTypeToken($control));
        $id = strtolower((string) (isset($control['id']) ? $control['id'] : ''));
        $name = strtolower((string) self::firstNonEmpty(
            isset($control['parameterName']) ? $control['parameterName'] : null,
            isset($control['name']) ? $control['name'] : null,
            isset($control['label']) ? $control['label'] : null
        ));

        if (strpos($type, 'timer') !== false || strpos($id, 'timer') !== false) {
            return 'timer';
        }
        if (strpos($type, 'slider') !== false) {
            return 'gauge';
        }
        if (strpos($type, 'toggle') !== false || strpos($type, 'switch') !== false || strpos($name, 'power') !== false) {
            return 'bolt';
        }
        if (strpos($type, 'button') !== false) {
            return 'power';
        }
        if (strpos($name, 'status') !== false || strpos($id, 'get_status') !== false) {
            return 'gauge';
        }

        return 'gauge';
    }

    // ------------------------------------------------------------ validation

    /**
     * Recomputes the validation block against the document that is actually being
     * served. `valid` covers structure we can guarantee; `dataComplete` covers the
     * product_api values only the web team can supply.
     */
    private static function recordValidation(array $schema, array $report)
    {
        $controls = isset($schema['controls']) && is_array($schema['controls']) ? $schema['controls'] : [];

        foreach ($controls as $index => $control) {
            if (!is_array($control)) {
                continue;
            }

            $missing = self::missingWriteFields($control);
            if (empty($missing)) {
                if (self::isWritableControl($control)) {
                    $control['writeReady'] = true;
                    $control['missingFrameValues'] = [];
                    $schema['controls'][$index] = $control;
                }
                continue;
            }

            $controlId = trim((string) (isset($control['id']) ? $control['id'] : ''));
            if ($controlId === '') {
                $controlId = 'controls[' . $index . ']';
            }

            $report['missingFrameValues'][] = [
                'controlId' => $controlId,
                'parameter' => (string) (isset($control['parameterName']) ? $control['parameterName'] : (isset($control['label']) ? $control['label'] : '')),
                'missing'   => $missing,
            ];

            // The app must not guess these bytes, so the control is published read-only.
            $control['writeReady'] = false;
            $control['missingFrameValues'] = $missing;
            $schema['controls'][$index] = $control;
        }

        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
            $schema['meta'] = [];
        }

        $existing = isset($schema['meta']['canonicalValidation']) && is_array($schema['meta']['canonicalValidation'])
            ? $schema['meta']['canonicalValidation']
            : [];

        $schema['meta']['canonicalValidation'] = array_merge($existing, [
            'valid'              => true,
            'dataComplete'       => empty($report['missingFrameValues']),
            'normalizedAt'       => gmdate('c'),
            'normalizer'         => 'DeviceUiSchemaNormalizer',
            'droppedFrameSlots'  => $report['droppedFrameSlots'],
            'repairedByteFields' => array_values(array_unique($report['repairedByteFields'])),
            'cardStylesApplied'  => $report['cardStylesApplied'],
            'iconFallbacks'      => $report['iconFallbacks'],
            'missingFrameValues' => $report['missingFrameValues'],
            'errors'             => [],
            'warnings'           => self::buildWarnings($report),
        ]);

        return $schema;
    }

    private static function missingWriteFields(array $control)
    {
        if (!self::isWritableControl($control)) {
            return [];
        }

        $commands = [];
        if (isset($control['deviceFrame']['commands']) && is_array($control['deviceFrame']['commands'])) {
            $commands = $control['deviceFrame']['commands'];
        } elseif (isset($control['protocolMeta']['frame']['commands']) && is_array($control['protocolMeta']['frame']['commands'])) {
            $commands = $control['protocolMeta']['frame']['commands'];
        }

        if (empty($commands)) {
            return ['CommandId'];
        }

        $present = [];
        foreach ($commands as $field) {
            if (!is_array($field)) {
                continue;
            }
            $value = isset($field['value']) ? $field['value'] : null;
            if ($value === null || $value === '' || $value === []) {
                $value = isset($field['asperui_value']) ? $field['asperui_value'] : null;
            }
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            $present[self::frameFieldName($field)] = true;
        }

        $missing = [];
        if (!isset($present['commandid'])) {
            $missing[] = 'CommandId';
        }
        if (!isset($present['functioncode'])) {
            $missing[] = 'Function Code';
        }

        return $missing;
    }

    private static function isWritableControl(array $control)
    {
        $source = strtolower(trim((string) (
            isset($control['protocolMeta']['ui_type_source']) ? $control['protocolMeta']['ui_type_source'] : ''
        )));
        if (in_array($source, ['response', 'error'], true)) {
            return false;
        }
        if (isset($control['readOnly']) && $control['readOnly'] === true) {
            return false;
        }
        if (isset($control['interactive']) && $control['interactive'] === false) {
            return false;
        }

        $access = '';
        foreach ([
            isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : null,
            isset($control['access']) ? $control['access'] : null,
        ] as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                $access = strtolower($candidate);
                break;
            }
        }

        // Empty access matches generation path: treat as writable unless source is response/error.
        if ($access === '') {
            return true;
        }
        if (in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
            return false;
        }

        return strpos($access, 'write') !== false;
    }

    private static function buildWarnings(array $report)
    {
        $warnings = [];

        foreach ($report['missingFrameValues'] as $entry) {
            $warnings[] = $entry['controlId'] . ': published read-only, schema is missing '
                . implode(', ', $entry['missing']) . '.';
        }
        foreach ($report['droppedFrameSlots'] as $slot) {
            $warnings[] = 'Dropped empty frame slot ' . $slot . '.';
        }
        foreach ($report['iconFallbacks'] as $from => $to) {
            $warnings[] = 'Icon "' . $from . '" is not in the icon library, replaced with "' . $to . '".';
        }

        return $warnings;
    }

    private static function isList($value)
    {
        if (!is_array($value)) {
            return false;
        }
        if ($value === []) {
            return true;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }
}
