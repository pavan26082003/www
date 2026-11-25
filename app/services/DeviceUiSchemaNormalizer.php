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
        'refresh'             => 'refresh-cw',
        'sync'                => 'refresh-cw',
        'autorenew'           => 'refresh-cw',
        'loop'                => 'refresh-cw',
        'description'         => 'file',
        'article'             => 'file',
        'list_alt'            => 'file',
        'fan'                 => 'fan',
        'toys'                => 'fan',
        'mode_fan'            => 'fan',
    ];

    /**
     * @param array  $schema  Decoded schema document.
     * @param string $modelNo Model the app actually requested.
     * @param string $nodeId  Node the app actually opened.
     * @param int    $userId  Account id for Profile -> Appearance (0 = resolve from request).
     */
    public static function normalize(array $schema, $modelNo = '', $nodeId = '', $userId = 0)
    {
        $modelNo = trim((string) $modelNo);
        $nodeId  = trim((string) $nodeId);
        $userId  = (int) $userId;
        if ($userId <= 0) {
            $userId = self::resolveRequestUserId();
        }

        $report = [
            'missingFrameValues' => [],
            'droppedFrameSlots'  => [],
            'repairedByteFields' => [],
            'iconFallbacks'      => [],
            'cardStylesApplied'  => 0,
        ];

        if ($nodeId !== '') {
            $schema = self::applyTestNodesNodeDetails($schema, $nodeId, $modelNo);
        }

        // V2: convert remaining wire-hex display fields into typed asperui values.
        // No-op when values are already human-readable (legacy-safe).
        $schema = self::applyV2DisplayHandlers($schema);

        if ($nodeId !== '' || $modelNo !== '') {
            $schema = self::applyRequestedNode($schema, $modelNo, $nodeId);
        }

        // Unlock MQTT/deviceCommands for already-online devices without changing schema shape.
        $schema = self::unlockOnlineDeviceControl($schema);
        // Match web preview: single-UI groups keep the group title only.
        $schema = self::stripGroupedSingleInnerLabels($schema);
        // Multi-control groups keep each field name (Power, Timer, Speed).
        $schema = self::restoreMultiControlLabels($schema);
        $schema = self::pruneStaleControlIconMaps($schema);
        // Hide "1 Control" badge on single-control groups.
        $schema = self::applyGroupCountVisibility($schema);
        // Drop duplicate "Page N.Operation" header subtitle (keep title + model only).
        $schema = self::stripDuplicatePageHeaderSubtitle($schema);

        $schema = self::normalizeDeviceFrames($schema, $report);
        // Fill CommandId / Function Code / ActionId from protocolMeta when the
        // slot exists but is empty, or insert the slot when meta has the value.
        // Inventing bytes is still forbidden - only copy known product_api values.
        // Terminator is protocol-fixed (0A) when the slot is present but empty.
        $schema = self::hydrateWriteFieldsFromMeta($schema, $report);
        $schema = self::repairPublishedFrames($schema);
        // Send/writable controls: complete CommandId/Function/Data/Terminator
        // framing, enforce hex-width = 2*no_of_bytes, and publish write access.
        $schema = self::enforceSendProtocolContract($schema, $report);
        $schema = self::repairPublishedFrames($schema);
        $schema = self::applyCardStyles($schema, $report);
        $schema = self::normalizeControlContracts($schema);
        // Get Status: response starts collapsed; command toggles state.getStatusResponse.*.open.
        $schema = self::applyGetStatusRevealContract($schema);
        // Brand/info title = product/model identity — never live node nicknames.
        $schema = self::applyDisplayModelIdentity($schema, $modelNo);
        // Controls commit on change; hide footer Save Changes for all models.
        $schema = self::applyImmediateCommitContract($schema);
        // Slider shells follow theme (no forced light #F8FAFC box on dark UIs).
        $schema = self::applySliderShellThemeContract($schema);
        // Rebuild missing multi-page hardware frames after controls are normalized.
        $schema = self::ensureDeviceConfigPagesFromControls($schema);
        // V2/legacy-safe: push richer deviceConfig/v2_node_details frames onto
        // controls[].deviceFrame so app controlFramePath matches page wires.
        $schema = self::syncControlDeviceFramesFromConfigPages($schema);
        $schema = self::applyCanonicalHeader($schema);
        $schema = self::applyCanonicalValueColors($schema);
        // Timer schemaSlider with min/max labels and a kicker fails Flutter validation
        // unless these style fields are present on props. Parameter bindings stay as-is.
        $schema = self::applyTimerSliderStyleContract($schema);
        // Register semantic tokens before icon remapping so filled control icons stick.
        $schema = self::ensureRequiredIcons($schema);
        $schema = self::normalizeIcons($schema, $report);
        $schema = self::recordValidation($schema, $report);
        $schema = self::attachModeThemes($schema, $userId);
        // Pixel parity last so later contract rewrites cannot wipe preview sizes
        // (e.g. sendButton rebuilt without width/height in normalizeControlContracts).
        $schema = self::applyPreviewPixelParity($schema);
        // Ensure actions.update_*.input.dataField carries bitwise + access for Flutter.
        $schema = self::enrichActionDataFields($schema);

        // LAST: strip junk user-facing values after all frame rebuilds
        // ([00][01], [false][true], padding, name-as-value) + ensure liveStatePath.
        // Must run after ensureDeviceConfigPagesFromControls / repairPublishedFrames
        // or those steps reintroduce option-list dumps into asperui_value.
        $schema = self::applyDisplaySanitizer($schema);
        // Frame rebuilds / sanitizer may leave wire hex or empty asperui again -
        // convert once more so sensors show typed 0 instead of Loading forever.
        $schema = self::applyV2DisplayHandlers($schema);
        $schema = self::syncControlDisplayFromFrames($schema);
        // Re-sync after display/frame rebuilds so controlFramePath stays operable.
        $schema = self::syncControlDeviceFramesFromConfigPages($schema);
        // Wire padding / empty / junk cleared above -> valueLoading flags.
        $schema = self::applyLiveValueLoadingFlags($schema);
        // Keep MQTT enableWhen + Flutter state.online in sync with connectivity.
        $schema = self::syncOnlineStateFromConnectivity($schema);
        // Last: bytewise command slots that share a byte cannot be packed.
        // Move only the later colliding slot. Same keys, no invented protocol bytes.
        $overlapReport = ['repairedByteFields' => []];
        $schema = self::repairKnownCommandFrames($schema, $overlapReport);
        // Recompute writeReady / missingFrameValues AFTER overlap repair so the
        // app does not see stale overlap errors or null writeReady flags.
        $schema = self::recordValidation($schema, $report);
        if (!empty($overlapReport['repairedByteFields'])
            && isset($schema['meta']['canonicalValidation'])
            && is_array($schema['meta']['canonicalValidation'])
        ) {
            $existingRepairs = isset($schema['meta']['canonicalValidation']['repairedByteFields'])
                && is_array($schema['meta']['canonicalValidation']['repairedByteFields'])
                ? $schema['meta']['canonicalValidation']['repairedByteFields']
                : [];
            $schema['meta']['canonicalValidation']['repairedByteFields'] = array_values(array_unique(array_merge(
                $existingRepairs,
                $overlapReport['repairedByteFields']
            )));
        }

        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
            $schema['meta'] = [];
        }
        // Authoritative build stamp for app gating. schemaBuildVersion mirrors it.
        $schema['meta']['normalizerBuild'] = '2026-11-25.2';
        $schema['meta']['schemaBuildVersion'] = '2026-11-25.2';
        $schema['meta']['writeContract'] = self::writeTransportContractDoc();
        // Linked Studio pages are the only screens. Drop device-only pages and
        // a second copy of the same page title before the app reads the schema.
        $schema = self::alignPublishedUiPages($schema);
        // One node payload. V2 is meta.v2DisplayHandlers, not a second copy of node_details.
        $schema = self::publishSingleNodeDetails($schema);
        // After frame display sync: direction buttons follow the byte, and
        // raw [08][08] / 00 / 01 text is not a card title.
        $schema = self::repairDirectionControls($schema);
        // Last: sno_asperui / item_values tables must not remain in captions or values.
        if (self::loadDisplaySanitizer()) {
            $schema = DeviceUiDisplaySanitizer::stripLeakedProtocolBracketText($schema);
        }

        return $schema;
    }

    /**
     * Documented write contract for Flutter. Cloud writes use the existing
     * binding `update` action — not meta.links.parameters (schema GET) and not
     * apiContracts.deviceCommands (device frame packer).
     */
    private static function writeTransportContractDoc()
    {
        return [
            'version' => '2026-11-19.3',
            'authoritativeBuildField' => 'meta.normalizerBuild',
            'device' => [
                'when' => 'control.writeReady === true && control.deviceWriteSupported === true',
                'preferredTransport' => 'device',
                'actionType' => 'send',
                'dataSource' => 'deviceCommands',
                'apiContract' => 'apiContracts.deviceCommands',
                'framePath' => 'controls[].deviceFrame',
                'notes' => 'Pack deviceFrame only. Do not invent CommandId/Function Code.',
            ],
            'cloud' => [
                'when' => 'control.preferredTransport === "cloud" || control.deviceWriteSupported === false',
                'preferredTransport' => 'cloud',
                'actionType' => 'update',
                'bindingPath' => 'control.binding',
                'bindingExample' => 'device.parameters.{parameterKey}',
                'valuePath' => 'state.parameters.{parameterKey}.value',
                'doNotUse' => [
                    'meta.links.parameters',
                    'apiContracts.parameters',
                    'apiContracts.deviceCommands',
                    'dataSources.deviceCommands',
                ],
                'notes' => 'Use the existing Cloud/TAP DISPATCHED parameter update on the control binding. No protocol frame. No CommandId/Function Code body.',
            ],
            'fieldTypes' => [
                'writeReady' => 'boolean',
                'deviceWriteSupported' => 'boolean',
                'preferredTransport' => 'device|cloud',
                'missingFrameValues' => 'string[]',
            ],
        ];
    }

    /**
     * Resolve the account id for Appearance from the current HTTP request.
     * Prefer explicit user_id, then Bearer access_token, then session.
     */
    public static function resolveRequestUserId(): int
    {
        foreach (['user_id', 'userId'] as $key) {
            if (isset($_GET[$key]) && is_scalar($_GET[$key]) && (int) $_GET[$key] > 0) {
                return (int) $_GET[$key];
            }
            if (isset($_REQUEST[$key]) && is_scalar($_REQUEST[$key]) && (int) $_REQUEST[$key] > 0) {
                return (int) $_REQUEST[$key];
            }
        }

        $token = self::requestBearerToken();
        if ($token !== '') {
            $fromToken = self::userIdForAccessToken($token);
            if ($fromToken > 0) {
                return $fromToken;
            }
        }

        try {
            if (class_exists('\\Config\\Services', false) || class_exists('\\Config\\Services')) {
                $session = \Config\Services::session();
                $sessionUserId = (int) ($session->get('user_id') ?? 0);
                if ($sessionUserId > 0) {
                    return $sessionUserId;
                }
            }
        } catch (\Throwable $e) {
            // Ignore - fall through.
        }

        if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0) {
            return (int) $_SESSION['user_id'];
        }

        return 0;
    }

    private static function requestBearerToken(): string
    {
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = (string) $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (is_array($headers)) {
                foreach ($headers as $name => $value) {
                    if (strcasecmp((string) $name, 'Authorization') === 0) {
                        $header = (string) $value;
                        break;
                    }
                }
            }
        }

        if (preg_match('/^\s*Bearer\s+(\S+)/i', $header, $m)) {
            return trim($m[1]);
        }

        if (isset($_GET['access_token']) && is_scalar($_GET['access_token'])) {
            return trim((string) $_GET['access_token']);
        }

        return '';
    }

    private static function userIdForAccessToken(string $token): int
    {
        $token = trim($token);
        if ($token === '') {
            return 0;
        }

        try {
            if (!class_exists('\\Config\\Database', false) && !class_exists('\\Config\\Database')) {
                return 0;
            }
            $row = \Config\Database::connect('default', false)->table('users')
                ->select('id, status, deleted')
                ->where('access_token', $token)
                ->get()
                ->getRowArray();
            if (!is_array($row) || (int) ($row['deleted'] ?? 0) === 1) {
                return 0;
            }
            if (isset($row['status']) && $row['status'] !== '' && $row['status'] !== 'active') {
                return 0;
            }

            return (int) ($row['id'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Account Profile -> Appearance for device UI (same source as App Home / IoT / GRID).
     * Default system until the user saves light/dark in web settings.
     */
    private static function resolveAccountAppearance(int $userId): string
    {
        if ($userId <= 0) {
            return 'system';
        }

        try {
            $appearanceFile = dirname(__DIR__) . '/Libraries/AppAppearance.php';
            if (defined('APPPATH')) {
                $appearanceFile = rtrim(str_replace('\\', '/', (string) APPPATH), '/') . '/Libraries/AppAppearance.php';
            }
            if (!class_exists('\\App\\Libraries\\AppAppearance', false) && is_file($appearanceFile)) {
                require_once $appearanceFile;
            }
            if (class_exists('\\App\\Libraries\\AppAppearance')) {
                return \App\Libraries\AppAppearance::resolve($userId);
            }
        } catch (\Throwable $e) {
            // Keep system default.
        }

        return 'system';
    }

    /**
     * Additive: copy field_level / start_bit / end_bit / access onto action dataField
     * so Flutter can pack frames from actions.update_* without reading protocolMeta only.
     */
    private static function enrichActionDataFields(array $schema): array
    {
        if (!isset($schema['actions']) || !is_array($schema['actions'])) {
            return $schema;
        }

        $controlsByAction = [];
        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $control) {
                if (!is_array($control)) {
                    continue;
                }
                $action = trim((string) ($control['action'] ?? ''));
                if ($action !== '' && stripos($action, 'update_') === 0) {
                    $controlsByAction[$action] = $control;
                }
            }
        }

        foreach ($schema['actions'] as $actionId => $action) {
            if (!is_array($action)) {
                continue;
            }
            if (!isset($action['input']) || !is_array($action['input'])) {
                $action['input'] = [];
            }
            $dataField = (isset($action['input']['dataField']) && is_array($action['input']['dataField']))
                ? $action['input']['dataField']
                : [];
            $control = $controlsByAction[$actionId] ?? null;
            $meta = (is_array($control) && isset($control['protocolMeta']) && is_array($control['protocolMeta']))
                ? $control['protocolMeta']
                : [];

            $fieldLevel = strtolower(trim((string) (
                $dataField['field_level']
                ?? $dataField['fieldLevel']
                ?? $meta['field_level']
                ?? $meta['fieldLevel']
                ?? 'bytewise'
            )));
            $startBit = $dataField['start_bit'] ?? $dataField['startBit'] ?? $meta['start_bit'] ?? $meta['startBit'] ?? null;
            $endBit = $dataField['end_bit'] ?? $dataField['endBit'] ?? $meta['end_bit'] ?? $meta['endBit'] ?? null;
            $access = $dataField['access'] ?? $meta['access'] ?? null;

            $dataField['field_level'] = $fieldLevel;
            $dataField['fieldLevel'] = $fieldLevel;
            $dataField['start_bit'] = $startBit;
            $dataField['end_bit'] = $endBit;
            $dataField['startBit'] = $startBit;
            $dataField['endBit'] = $endBit;
            if ($access !== null && $access !== '') {
                $dataField['access'] = $access;
            }
            if (!isset($dataField['fieldIndex']) && isset($meta['field_index'])) {
                $dataField['fieldIndex'] = $meta['field_index'];
            }

            $action['input']['dataField'] = $dataField;
            $schema['actions'][$actionId] = $action;
        }

        if (isset($schema['mqtt']) && is_array($schema['mqtt'])) {
            if (!isset($schema['mqtt']['connection']) || !is_array($schema['mqtt']['connection'])) {
                $schema['mqtt']['connection'] = [
                    'brokerSource' => 'runtime.mqttBroker || appConfig.mqtt',
                    'authSource' => 'authenticatedSession.accessToken',
                    'usernameSource' => 'authenticatedSession.emailOrUsername',
                    'nodeIdSource' => '_clientConnection.nodeId',
                    'schemaMustNotContainSecrets' => true,
                ];
            }
            if (!isset($schema['mqtt']['liveState']) || !is_array($schema['mqtt']['liveState'])) {
                $schema['mqtt']['liveState'] = [
                    'preferLiveState' => true,
                    'valueLoadingFlag' => 'valueLoading',
                    'clearValueLoadingWhen' => 'liveStateApplied',
                    'mergeInto' => 'state.parameters',
                ];
            }
        }

        return $schema;
    }

    /**
     * Single-control groups already render the parameter name on the group header.
     * Clear the inner ParameterCard title so the app does not show Current / Current(A) twice.
     */
    private static function stripGroupedSingleInnerLabels(array $schema): array
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        $groupCounts = [];
        foreach ($schema['controls'] as $control) {
            if (!is_array($control)) {
                continue;
            }
            $groupName = trim((string) ($control['groupParameter'] ?? ''));
            if ($groupName === '') {
                continue;
            }
            $groupKey = trim((string) ($control['pageNo'] ?? '')) . '::' . strtolower($groupName);
            $groupCounts[$groupKey] = ($groupCounts[$groupKey] ?? 0) + 1;
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $uiCount = isset($control['uiCount']) ? (int) $control['uiCount'] : 0;
            $groupName = trim((string) ($control['groupParameter'] ?? ''));
            $groupKey = trim((string) ($control['pageNo'] ?? '')) . '::' . strtolower($groupName);
            // uiCount is 1 on every row of a multi-control group (Get Status).
            // Only a group that really has one control hides its inner title.
            $groupSize = $groupName !== '' ? (int) ($groupCounts[$groupKey] ?? 0) : 0;
            $isSingle = $groupSize === 1 || ($groupName === '' && $uiCount === 1);
            if (!$isSingle) {
                continue;
            }

            $control['label'] = '';
            $control['fieldLabel'] = '';
            if (isset($control['component']) && is_array($control['component'])) {
                if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                    $control['component']['props'] = [];
                }
                $control['component']['props']['label'] = '';
                $control['component']['props']['title'] = '';
                $control['component']['props']['showTitle'] = false;
                $control['component']['props']['hideLabel'] = true;

                if (isset($control['component']['widgetTree']['properties']['props'])
                    && is_array($control['component']['widgetTree']['properties']['props'])) {
                    $props = &$control['component']['widgetTree']['properties']['props'];
                    $props['label'] = '';
                    $props['title'] = '';
                    $props['showTitle'] = false;
                    $props['hideLabel'] = true;
                    if (isset($props['metaChip']) && is_array($props['metaChip'])) {
                        $props['metaChip']['label'] = '';
                        $props['metaChip']['text'] = '';
                        $props['metaChip']['title'] = '';
                    }
                    unset($props);
                }
            }
            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    /**
     * A Get Status group has several rows that each store uiCount 1.
     * Those rows need their own field name. Protocol roles (Command, commandId,
     * Terminator) are not names a person can use.
     */
    private static function restoreMultiControlLabels(array $schema): array
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        $groupCounts = [];
        foreach ($schema['controls'] as $control) {
            if (!is_array($control)) {
                continue;
            }
            $groupName = trim((string) ($control['groupParameter'] ?? ''));
            if ($groupName === '') {
                continue;
            }
            $groupKey = trim((string) ($control['pageNo'] ?? '')) . '::' . strtolower($groupName);
            $groupCounts[$groupKey] = ($groupCounts[$groupKey] ?? 0) + 1;
        }

        $labelsById = [];
        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $type = strtolower(str_replace(['_', '-'], '', (string) ($control['componentType'] ?? '')));
            if (strpos($type, 'slider') !== false) {
                $step = $control['constraints']['step'] ?? null;
                if ($step === null || $step === '' || (is_numeric($step) && (float) $step <= 0)) {
                    if (!isset($control['constraints']) || !is_array($control['constraints'])) {
                        $control['constraints'] = [];
                    }
                    $control['constraints']['step'] = 1;
                    if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                        $control['component']['props']['step'] = 1;
                    }
                }
            }

            $groupName = trim((string) ($control['groupParameter'] ?? ''));
            $groupKey = trim((string) ($control['pageNo'] ?? '')) . '::' . strtolower($groupName);
            if ($groupName === '' || (int) ($groupCounts[$groupKey] ?? 0) < 2) {
                $schema['controls'][$index] = $control;
                $labelsById[(string) ($control['id'] ?? '')] = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? ''));
                continue;
            }

            $label = self::humanControlLabel($control);
            $control['label'] = $label;
            $control['fieldLabel'] = $label;
            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }
            $control['component']['props']['label'] = $label;
            $control['component']['props']['title'] = $label;
            $control['component']['props']['showTitle'] = true;
            $control['component']['props']['hideLabel'] = false;
            if (isset($control['component']['widgetTree']['properties']['props'])
                && is_array($control['component']['widgetTree']['properties']['props'])) {
                $props = &$control['component']['widgetTree']['properties']['props'];
                $props['label'] = $label;
                $props['title'] = $label;
                $props['showTitle'] = true;
                $props['hideLabel'] = false;
                if (isset($props['metaChip']) && is_array($props['metaChip'])) {
                    $props['metaChip']['label'] = $label;
                    $props['metaChip']['text'] = $label;
                    $props['metaChip']['title'] = $label;
                }
                unset($props);
            }
            $schema['controls'][$index] = $control;
            $labelsById[(string) ($control['id'] ?? '')] = $label;
        }

        return self::syncPreviewSnapshotLabels($schema, $labelsById);
    }

    private static function humanControlLabel(array $control): string
    {
        $parameter = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? ''));
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $candidates = [
            $meta['field_description'] ?? '',
            $meta['field_name'] ?? '',
            $control['fieldLabel'] ?? '',
            $control['label'] ?? '',
        ];
        $frame = isset($control['deviceFrame']) && is_array($control['deviceFrame'])
            ? $control['deviceFrame']
            : (isset($meta['frame']) && is_array($meta['frame']) ? $meta['frame'] : null);
        $source = strtolower(trim((string) ($meta['ui_type_source'] ?? 'command')));
        $bucket = $source === 'response' ? 'response' : ($source === 'error' ? 'error' : 'commands');
        $index = $meta['field_index'] ?? null;
        if (is_array($frame) && isset($frame[$bucket][$index]) && is_array($frame[$bucket][$index])) {
            $field = $frame[$bucket][$index];
            $candidates[] = $field['Field_description'] ?? '';
            $candidates[] = $field['field_description'] ?? '';
            $candidates[] = $field['default_name'] ?? '';
        }
        foreach ($candidates as $candidate) {
            $text = trim((string) $candidate);
            if ($text !== '' && !self::labelIsProtocolRole($text) && !self::textIsProtocolBracketDump($text)) {
                return $text;
            }
        }

        return self::textIsProtocolBracketDump($parameter) ? '' : $parameter;
    }

    private static function textIsProtocolBracketDump($value): bool
    {
        if (is_array($value) || is_bool($value) || is_int($value) || is_float($value)) {
            return false;
        }
        $text = trim((string) $value);
        if ($text === '' || $text[0] !== '[') {
            return false;
        }

        return (bool) preg_match('/^(\[[^\]]*\])+$/', $text);
    }

    private static function labelIsProtocolRole($name): bool
    {
        if (self::textIsProtocolBracketDump($name)) {
            return true;
        }
        $name = strtolower(trim((string) $name));
        $name = str_replace(['_', '-'], ' ', $name);

        return in_array($name, [
            '', 'data', 'trigger', 'command', 'response', 'error', 'none', 'field',
            'commandid', 'command id', 'functioncode', 'function code', 'terminator',
            'actionid', 'action id',
        ], true);
    }

    private static function syncPreviewSnapshotLabels(array $schema, array $labelsById): array
    {
        $pages = $schema['extensions']['universalUi']['extensions']['exactHtmlPreview']['pages'] ?? null;
        if (!is_array($pages)) {
            return $schema;
        }
        foreach ($pages as $pageIndex => $page) {
            if (!is_array($page) || !isset($page['controls']) || !is_array($page['controls'])) {
                continue;
            }
            foreach ($page['controls'] as $controlIndex => $previewControl) {
                if (!is_array($previewControl)) {
                    continue;
                }
                $id = (string) ($previewControl['id'] ?? '');
                $label = ($id !== '' && isset($labelsById[$id])) ? $labelsById[$id] : '';
                if ($label === '' && self::labelIsProtocolRole($previewControl['label'] ?? '')) {
                    $parts = explode(':', $id);
                    $label = isset($parts[1]) ? trim($parts[1]) : '';
                }
                if ($label === '') {
                    continue;
                }
                $previewControl['label'] = $label;
                $previewControl['name'] = $label;
                $previewControl['fieldLabel'] = $label;
                $pages[$pageIndex]['controls'][$controlIndex] = $previewControl;
            }
        }
        $schema['extensions']['universalUi']['extensions']['exactHtmlPreview']['pages'] = $pages;

        return $schema;
    }

    /**
     * Drop icon entries for controls that are no longer on a page
     * (old Home.operation Power / Buzzer / Speed / Timer rows).
     */
    private static function pruneStaleControlIconMaps(array $schema): array
    {
        $keys = [];
        foreach ($schema['controls'] ?? [] as $control) {
            if (!is_array($control)) {
                continue;
            }
            foreach (['id', 'parameterKey', 'parameter', 'parameterName'] as $field) {
                $value = trim((string) ($control[$field] ?? ''));
                if ($value !== '') {
                    $keys[$value] = true;
                }
            }
            $pageNo = trim((string) ($control['pageNo'] ?? ''));
            $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? ''));
            if ($pageNo !== '' && $name !== '') {
                $keys[$pageNo . ':' . $name] = true;
            }
        }
        if ($keys === []) {
            return $schema;
        }

        $mapPaths = [
            ['settings', 'parameterIcons'],
            ['settings', 'componentOverrides'],
            ['theme', 'parameterIcons'],
            ['extensions', 'universalUi', 'designSystem', 'icons', 'overrides'],
            ['extensions', 'universalUi', 'extensions', 'exactHtmlPreview', 'theme', 'parameterIcons'],
            ['extensions', 'universalUi', 'extensions', 'exactHtmlPreview', 'theme', 'componentOverrides'],
        ];
        foreach ($mapPaths as $path) {
            $schema = self::pruneMapAtPath($schema, $path, $keys);
        }

        $listPaths = [
            ['extensions', 'universalUi', 'extensions', 'exactHtmlPreview', 'xmlHints', 'parameterIconReferences'],
            ['extensions', 'universalUi', 'extensions', 'exactHtmlPreview', 'xmlHints', 'parameterMetaIconReferences'],
        ];
        foreach ($listPaths as $path) {
            $schema = self::pruneIconListAtPath($schema, $path, $keys);
        }

        return $schema;
    }

    private static function pruneMapAtPath(array $schema, array $path, array $keys): array
    {
        $cursor = &$schema;
        $last = count($path) - 1;
        foreach ($path as $depth => $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                unset($cursor);
                return $schema;
            }
            if ($depth === $last) {
                $kept = [];
                foreach ($cursor[$segment] as $key => $value) {
                    if (isset($keys[(string) $key])) {
                        $kept[$key] = $value;
                    }
                }
                $cursor[$segment] = $kept;
                unset($cursor);
                return $schema;
            }
            $cursor = &$cursor[$segment];
        }
        unset($cursor);

        return $schema;
    }

    private static function pruneIconListAtPath(array $schema, array $path, array $keys): array
    {
        $cursor = &$schema;
        $last = count($path) - 1;
        foreach ($path as $depth => $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                unset($cursor);
                return $schema;
            }
            if ($depth === $last) {
                $kept = [];
                foreach ($cursor[$segment] as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $key = trim((string) ($item['parameterKey'] ?? ''));
                    if ($key !== '' && isset($keys[$key])) {
                        $kept[] = $item;
                    }
                }
                $cursor[$segment] = $kept;
                unset($cursor);
                return $schema;
            }
            $cursor = &$cursor[$segment];
        }
        unset($cursor);

        return $schema;
    }

    /**
     * Direction buttons follow the fan byte: 00 anti-clockwise, 01 clockwise.
     * A stored label "Clockwise" on byte 00 is rewritten. Bracket dumps and
     * the raw byte are not shown as the card title. The active mode is not
     * sent again.
     */
    private static function repairDirectionControls(array $schema): array
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['groupParameter'] ?? ''));
            if (!self::isDirectionControlName($name)) {
                continue;
            }

            $source = strtolower(trim((string) ($control['protocolMeta']['ui_type_source'] ?? '')));
            if (in_array($source, ['response', 'error'], true)) {
                continue;
            }

            $options = self::directionOptionsForControl($control);
            $raw = trim((string) ($control['value'] ?? ($control['initialValue'] ?? '')));
            $selected = self::matchDirectionOptionValue($options, $raw);
            $control['options'] = $options;
            $control['value'] = $selected;
            $control['initialValue'] = $selected;
            $control['displayValue'] = '';
            foreach (['label', 'fieldLabel', 'componentLabel', 'subtitle', 'title'] as $textKey) {
                if (self::isDirectionJunkText($control[$textKey] ?? '')) {
                    $control[$textKey] = '';
                }
            }

            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            $control['component']['options'] = $options;
            $control['component']['initialValue'] = $selected;
            $control['component']['displayValue'] = '';
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }
            $props = &$control['component']['props'];
            $props['options'] = $options;
            $props['value'] = $selected;
            $props['hideValue'] = true;
            $props['showValue'] = false;
            $props['hideRawValue'] = true;
            $props['allowReselect'] = false;
            $props['sendOnReselect'] = false;
            if (self::isDirectionJunkText($props['label'] ?? '')) {
                $props['label'] = '';
            }
            if (self::isDirectionJunkText($props['title'] ?? '')) {
                $props['title'] = '';
            }
            unset($props);

            if (isset($control['component']['widgetTree']['properties']['props'])
                && is_array($control['component']['widgetTree']['properties']['props'])
            ) {
                $treeProps = &$control['component']['widgetTree']['properties']['props'];
                $treeProps['options'] = $options;
                $treeProps['value'] = $selected;
                $treeProps['hideValue'] = true;
                $treeProps['showValue'] = false;
                $treeProps['hideRawValue'] = true;
                $treeProps['allowReselect'] = false;
                $treeProps['sendOnReselect'] = false;
                unset($treeProps);
            }

            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    private static function isDirectionControlName($name)
    {
        $name = strtolower(trim((string) $name));
        if ($name === '' || strpos($name, 'data flow') !== false || strpos($name, 'dataflow') !== false) {
            return false;
        }

        return $name === 'direction' || (bool) preg_match('/(^|[^a-z0-9])direction([^a-z0-9]|$)/', $name);
    }

    private static function isDirectionJunkText($value)
    {
        $text = trim((string) $value);
        if ($text === '') {
            return false;
        }
        if (preg_match('/^(\[[^\]]*\])+$/', $text)) {
            return true;
        }

        return (bool) preg_match('/^(0|1|00|01|08)$/i', $text);
    }

    private static function directionOptionsForControl(array $control)
    {
        $pairs = [];
        $existing = isset($control['options']) && is_array($control['options']) ? $control['options'] : [];
        foreach ($existing as $option) {
            if (!is_array($option)) {
                continue;
            }
            $pairs[] = [
                'label' => (string) ($option['label'] ?? ''),
                'value' => (string) ($option['value'] ?? ''),
            ];
        }
        if (count($pairs) < 2) {
            $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
            $labels = isset($meta['items']) && is_array($meta['items']) ? $meta['items'] : [];
            $values = isset($meta['item_values']) && is_array($meta['item_values']) ? $meta['item_values'] : [];
            $pairs = [];
            if ($labels === []) {
                $pairs = [
                    ['label' => '0', 'value' => '00'],
                    ['label' => '1', 'value' => '01'],
                ];
            } else {
                foreach ($labels as $index => $label) {
                    $pairs[] = [
                        'label' => (string) $label,
                        'value' => array_key_exists($index, $values) ? (string) $values[$index] : (string) $label,
                    ];
                }
            }
        }

        $mapped = [];
        foreach ($pairs as $index => $pair) {
            $token = strtolower((string) preg_replace('/[^a-z0-9]+/', '', (string) $pair['label']));
            $valueToken = strtolower((string) preg_replace('/[^a-z0-9]+/', '', (string) $pair['value']));
            if (in_array($valueToken, ['0', '00'], true) || in_array($token, ['0', '00'], true)) {
                $isAnti = true;
                $wire = '00';
            } elseif (in_array($valueToken, ['1', '01'], true) || in_array($token, ['1', '01'], true)) {
                $isAnti = false;
                $wire = '01';
            } elseif (in_array($token, ['ccw', 'anticlockwise', 'anticlock', 'counterclockwise', 'counterclock', 'reverse', 'rev'], true)) {
                $isAnti = true;
                $wire = $pair['value'] !== '' ? (string) $pair['value'] : '00';
            } elseif (in_array($token, ['cw', 'clockwise', 'clock', 'forward', 'fwd'], true)) {
                $isAnti = false;
                $wire = $pair['value'] !== '' ? (string) $pair['value'] : '01';
            } else {
                $isAnti = $index === 1;
                $wire = $pair['value'] !== '' ? (string) $pair['value'] : ($isAnti ? '00' : '01');
            }
            $mapped[] = [
                'label' => $isAnti ? 'Anti-clockwise' : 'Clockwise',
                'value' => $wire,
            ];
        }

        if (count($mapped) < 2) {
            return [
                ['label' => 'Anti-clockwise', 'value' => '00'],
                ['label' => 'Clockwise', 'value' => '01'],
            ];
        }

        return array_slice($mapped, 0, 2);
    }

    private static function matchDirectionOptionValue(array $options, $raw)
    {
        $raw = trim((string) $raw);
        $selected = (string) ($options[0]['value'] ?? '00');
        if ($raw === '' || preg_match('/^(\[[^\]]*\])+$/', $raw)) {
            return $selected;
        }
        foreach ($options as $option) {
            $optionValue = (string) ($option['value'] ?? '');
            if ($optionValue !== '' && strcasecmp($optionValue, $raw) === 0) {
                return $optionValue;
            }
            if (strcasecmp((string) ($option['label'] ?? ''), $raw) === 0) {
                return $optionValue;
            }
            if (preg_match('/^(0|1|00|01)$/i', $raw)
                && preg_match('/^(0|1|00|01)$/i', $optionValue)
                && (int) $raw === (int) $optionValue
            ) {
                return $optionValue;
            }
        }

        return $selected;
    }

    /**
     * Single-control groups should not show a "1 Control" / "1 UI" badge.
     * Additive flags only - Flutter that ignores them keeps prior behavior.
     */
    private static function applyGroupCountVisibility(array $schema): array
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        $groupCounts = [];
        foreach ($schema['controls'] as $control) {
            if (!is_array($control)) {
                continue;
            }
            $groupName = trim((string) ($control['groupParameter'] ?? ''));
            if ($groupName === '') {
                continue;
            }
            $groupKey = trim((string) ($control['pageNo'] ?? '')) . '::' . strtolower($groupName);
            $groupCounts[$groupKey] = ($groupCounts[$groupKey] ?? 0) + 1;
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $uiCount = isset($control['uiCount']) ? (int) $control['uiCount'] : 0;
            $groupName = trim((string) ($control['groupParameter'] ?? ''));
            $groupKey = trim((string) ($control['pageNo'] ?? '')) . '::' . strtolower($groupName);
            $groupSize = $groupName !== '' ? (int) ($groupCounts[$groupKey] ?? 0) : 0;
            $isSingle = $groupSize === 1 || ($groupName === '' && $uiCount === 1);
            $showCount = !$isSingle && ($groupSize > 1 || $uiCount > 1);
            if ($uiCount <= 0 && !$isSingle) {
                $showCount = ($groupCounts[$groupKey] ?? 0) > 1;
            }
            $control['showGroupCount'] = $showCount;
            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }
            $control['component']['props']['showGroupCount'] = $showCount;
            $control['component']['props']['hideGroupCount'] = !$showCount;
            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    /**
     * App header showed page title plus another "Page N.Operation" line.
     * Keep title + model subtitle only; rewrite legacy subtitle patterns on GET.
     */
    private static function stripDuplicatePageHeaderSubtitle(array $schema): array
    {
        $modelNo = '';
        if (isset($schema['meta']['app']['modelNo'])) {
            $modelNo = trim((string) $schema['meta']['app']['modelNo']);
        } elseif (isset($schema['meta']['modelNo'])) {
            $modelNo = trim((string) $schema['meta']['modelNo']);
        } elseif (isset($schema['app']['modelNo'])) {
            $modelNo = trim((string) $schema['app']['modelNo']);
        }
        $subtitle = $modelNo !== '' ? $modelNo : 'Model UI';

        if (isset($schema['state']) && is_array($schema['state'])) {
            $schema['state']['activePageSubtitle'] = $subtitle;
        }
        if (isset($schema['pages']) && is_array($schema['pages'])) {
            foreach ($schema['pages'] as $i => $page) {
                if (!is_array($page)) {
                    continue;
                }
                $existing = trim((string) ($page['subtitle'] ?? ''));
                if ($existing === '' || preg_match('/^Page\s*\d+\s*\.?\s*Operation$/i', $existing)) {
                    $schema['pages'][$i]['subtitle'] = $subtitle;
                }
            }
        }
        if (isset($schema['mobileView']['header']) && is_array($schema['mobileView']['header'])) {
            $pattern = trim((string) ($schema['mobileView']['header']['subtitlePattern'] ?? ''));
            if ($pattern === '' || stripos($pattern, 'Operation') !== false || stripos($pattern, 'pageNo') !== false) {
                $schema['mobileView']['header']['subtitlePattern'] = '{modelNo}';
            }
        }
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::rewriteHeaderSubtitleNodes($schema['ui']['widgetTree'], $subtitle);
        }
        if (isset($schema['extensions']['universalUi']) && is_array($schema['extensions']['universalUi'])) {
            self::rewriteHeaderSubtitleNodes($schema['extensions']['universalUi'], $subtitle);
        }

        return $schema;
    }

    private static function rewriteHeaderSubtitleNodes(&$node, $subtitle, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $id = strtolower(trim((string) ($node['id'] ?? '')));
        if ($id === 'active_page_subtitle') {
            // Drop the duplicate third header line from legacy schemas.
            $node['visible'] = false;
            $node['visibility'] = false;
            if (isset($node['properties']) && is_array($node['properties'])) {
                $node['properties']['visible'] = false;
                $node['properties']['text'] = $subtitle;
            }
            if (isset($node['text'])) {
                $node['text'] = $subtitle;
            }
        }
        if ($id === 'model_subtitle') {
            if (isset($node['properties']) && is_array($node['properties'])) {
                $node['properties']['text'] = $subtitle;
                $node['properties']['textBinding'] = 'state.activePageSubtitle';
            }
            if (isset($node['text'])) {
                $node['text'] = $subtitle;
            }
        }
        foreach (['children', 'body', 'items', 'nodes'] as $listKey) {
            if (!isset($node[$listKey]) || !is_array($node[$listKey])) {
                continue;
            }
            $kept = [];
            foreach ($node[$listKey] as $child) {
                if (!is_array($child)) {
                    $kept[] = $child;
                    continue;
                }
                $childId = strtolower(trim((string) ($child['id'] ?? '')));
                if ($childId === 'active_page_subtitle') {
                    continue;
                }
                self::rewriteHeaderSubtitleNodes($child, $subtitle, $depth + 1);
                $kept[] = $child;
            }
            $node[$listKey] = $kept;
        }
        foreach ($node as $key => $child) {
            if (!is_array($child) || self::schemaWalkSkipKey($key) || in_array($key, ['children', 'body', 'items', 'nodes'], true)) {
                continue;
            }
            self::rewriteHeaderSubtitleNodes($node[$key], $subtitle, $depth + 1);
        }
    }

    /**
     * Prefer Loading... over fake wire padding (00 / 0000 / 00000000) until MQTT live state arrives.
     */
    private static function applyLiveValueLoadingFlags(array $schema): array
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }
        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $type = strtolower((string) ($control['componentType'] ?? $control['catalogComponentType'] ?? ''));
            $typeKey = str_replace(['_', '-'], '', $type);
            $skipLoadingTypes = in_array($typeKey, ['toggleswitch', 'switch', 'button', 'pushbutton', 'elevatedbutton'], true);
            $writable = self::isWritableControl($control);
            $skipWritableText = $writable && in_array($typeKey, ['entertext', 'textfield', 'cmd'], true);

            $value = '';
            foreach (['value', 'initialValue', 'displayValue'] as $key) {
                if (isset($control[$key]) && is_scalar($control[$key]) && trim((string) $control[$key]) !== '') {
                    $value = trim((string) $control[$key]);
                    break;
                }
            }
            if ($value === '' && isset($control['component']['initialValue']) && is_scalar($control['component']['initialValue'])) {
                $value = trim((string) $control['component']['initialValue']);
            }
            if ($value === '' && isset($control['component']['displayValue']) && is_scalar($control['component']['displayValue'])) {
                $value = trim((string) $control['component']['displayValue']);
            }

            $needsLoading = !$skipLoadingTypes && !$skipWritableText && self::valueNeedsLiveLoading($value);

            // Clear stale Loading flags when sanitizer already resolved a real value.
            if (!$needsLoading) {
                if (!empty($control['valueLoading'])) {
                    $control['valueLoading'] = false;
                    $control['loadingText'] = null;
                    if (isset($control['component']) && is_array($control['component'])) {
                        $control['component']['valueLoading'] = false;
                        if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                            $control['component']['props']['valueLoading'] = false;
                            $control['component']['props']['loading'] = false;
                            unset($control['component']['props']['loadingText'], $control['component']['props']['placeholder']);
                        }
                    }
                    $schema['controls'][$index] = $control;
                }
                continue;
            }

            $control['valueLoading'] = true;
            $control['loadingText'] = 'Loading...';
            $control['preferLiveState'] = true;
            $control['value'] = '';
            $control['initialValue'] = null;
            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            $control['component']['initialValue'] = null;
            $control['component']['displayValue'] = '';
            $control['component']['valueLoading'] = true;
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }
            $control['component']['props']['valueLoading'] = true;
            $control['component']['props']['loading'] = true;
            $control['component']['props']['loadingText'] = 'Loading...';
            $control['component']['props']['placeholder'] = 'Loading...';
            if (isset($schema['state']['device']['parameters']) && is_array($schema['state']['device']['parameters'])) {
                $paramKey = trim((string) ($control['parameterKey'] ?? ''));
                if ($paramKey === '' && !empty($control['binding'])) {
                    $paramKey = preg_replace('/^device\.parameters\./', '', (string) $control['binding']);
                }
                if ($paramKey !== '' && array_key_exists($paramKey, $schema['state']['device']['parameters'])) {
                    $schema['state']['device']['parameters'][$paramKey] = null;
                }
            }
            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    private static function valueNeedsLiveLoading($value): bool
    {
        // Typed numeric/bool zeros are real readings (power off / 0V), not "still loading".
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return false;
        }
        $text = trim((string) $value);
        if ($text === '' || $text === '--' || strtolower($text) === 'null' || strtolower($text) === 'undefined') {
            return true;
        }
        // Option-list dumps are not real readings.
        if (class_exists(DeviceUiDisplaySanitizer::class, false)
            && DeviceUiDisplaySanitizer::isBracketOptionList($text)
        ) {
            return true;
        }
        if (preg_match('/^(\[[^\]]*\]){2,}$/', $text)) {
            return true;
        }
        $raw = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', $text)));
        return $raw !== '' && strlen($raw) >= 2 && (bool) preg_match('/^[0-9A-F]+$/', $raw) && (bool) preg_match('/^0+$/', $raw);
    }

    /**
     * Push typed frame/deviceFrame asperui into controls + state.parameters so
     * sensors show 0/OFF instead of staying on valueLoading after sanitizer.
     */
    private static function syncControlDisplayFromFrames(array $schema): array
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        $frameDisplayByKey = [];
        $collect = static function (array $pages) use (&$frameDisplayByKey) {
            foreach ($pages as $frames) {
                if (!is_array($frames)) {
                    continue;
                }
                foreach ($frames as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $frame = isset($item['frame']) && is_array($item['frame']) ? $item['frame'] : $item;
                    if (!is_array($frame)) {
                        continue;
                    }
                    $name = trim((string) ($frame['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }
                    $display = null;
                    foreach (['commands', 'response', 'error'] as $bucket) {
                        foreach ($frame[$bucket] ?? [] as $field) {
                            if (!is_array($field)) {
                                continue;
                            }
                            $desc = strtolower(trim((string) (
                                $field['Field_description']
                                ?? $field['field_description']
                                ?? $field['default_name']
                                ?? ''
                            )));
                            if (!in_array($desc, ['data', 'trigger'], true)) {
                                continue;
                            }
                            if (array_key_exists('asperui_value', $field)
                                && $field['asperui_value'] !== null
                                && $field['asperui_value'] !== ''
                            ) {
                                $display = $field['asperui_value'];
                                break 2;
                            }
                        }
                    }
                    if ($display === null || $display === '') {
                        continue;
                    }
                    $key = class_exists(DeviceUiDisplaySanitizer::class, false)
                        ? DeviceUiDisplaySanitizer::frameNameKey($name)
                        : strtolower(preg_replace('/[^a-z0-9]+/', '', $name));
                    if ($key !== '') {
                        $frameDisplayByKey[$key] = $display;
                    }
                }
            }
        };

        if (isset($schema['deviceConfig']['devices'][0]['pages']) && is_array($schema['deviceConfig']['devices'][0]['pages'])) {
            $collect($schema['deviceConfig']['devices'][0]['pages']);
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }

            $display = null;
            if (isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
                foreach (['commands', 'response', 'error'] as $bucket) {
                    foreach ($control['deviceFrame'][$bucket] ?? [] as $field) {
                        if (!is_array($field)) {
                            continue;
                        }
                        $desc = strtolower(trim((string) (
                            $field['Field_description']
                            ?? $field['field_description']
                            ?? $field['default_name']
                            ?? ''
                        )));
                        if (!in_array($desc, ['data', 'trigger'], true)) {
                            continue;
                        }
                        if (array_key_exists('asperui_value', $field)
                            && $field['asperui_value'] !== null
                            && $field['asperui_value'] !== ''
                        ) {
                            $display = $field['asperui_value'];
                            break 2;
                        }
                    }
                }
            }

            if ($display === null || $display === '') {
                $name = trim((string) ($control['parameterName'] ?? $control['parameter'] ?? $control['label'] ?? ''));
                $key = class_exists(DeviceUiDisplaySanitizer::class, false)
                    ? DeviceUiDisplaySanitizer::frameNameKey($name)
                    : strtolower(preg_replace('/[^a-z0-9]+/', '', $name));
                if ($key !== '' && array_key_exists($key, $frameDisplayByKey)) {
                    $display = $frameDisplayByKey[$key];
                }
            }

            if ($display === null || $display === '') {
                continue;
            }
            // Don't overwrite a good non-hex control value with frame display.
            $current = $control['value'] ?? ($control['initialValue'] ?? null);
            $currentOk = $current !== null && $current !== ''
                && !(is_string($current) && self::valueNeedsLiveLoading($current));
            if ($currentOk && !self::valueNeedsLiveLoading($display) && !is_bool($display) && !is_int($display) && !is_float($display)) {
                // keep existing human text
            } else {
                $control['value'] = $display;
                $control['initialValue'] = $display;
                if (isset($control['component']) && is_array($control['component'])) {
                    $control['component']['initialValue'] = $display;
                    $control['component']['displayValue'] = is_scalar($display) ? (string) $display : $display;
                }
            }

            $paramKey = trim((string) ($control['parameterKey'] ?? ''));
            if ($paramKey !== '') {
                if (!isset($schema['state']) || !is_array($schema['state'])) {
                    $schema['state'] = [];
                }
                if (!isset($schema['state']['parameters']) || !is_array($schema['state']['parameters'])) {
                    $schema['state']['parameters'] = [];
                }
                $existing = $schema['state']['parameters'][$paramKey] ?? null;
                if (is_array($existing)) {
                    $existing['value'] = $display;
                    $schema['state']['parameters'][$paramKey] = $existing;
                } else {
                    $schema['state']['parameters'][$paramKey] = ['value' => $display];
                }
            }

            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    /**
     * Apply DeviceUiDisplaySanitizer across schema (legacy + V2).
     */
    private static function applyDisplaySanitizer(array $schema): array
    {
        if (!self::loadDisplaySanitizer()) {
            // Minimal fallback: still ensure liveStatePath.
            if (!isset($schema['deviceState']) || !is_array($schema['deviceState'])) {
                $schema['deviceState'] = [];
            }
            if (empty($schema['deviceState']['liveStatePath'])) {
                $schema['deviceState']['liveStatePath'] = 'node_details[0].config.devices[0]';
            }
            if (!array_key_exists('preferLiveState', $schema['deviceState'])) {
                $schema['deviceState']['preferLiveState'] = true;
            }

            return $schema;
        }

        try {
            return DeviceUiDisplaySanitizer::sanitizeSchema($schema);
        } catch (\Throwable $exception) {
            return $schema;
        }
    }

    private static function loadDisplaySanitizer(): bool
    {
        if (class_exists(DeviceUiDisplaySanitizer::class, false)) {
            return true;
        }
        $candidates = [
            __DIR__ . '/DeviceUiDisplaySanitizer.php',
            dirname(__DIR__) . '/services/DeviceUiDisplaySanitizer.php',
            dirname(__DIR__) . '/Services/DeviceUiDisplaySanitizer.php',
        ];
        foreach ($candidates as $file) {
            if (is_file($file)) {
                require_once $file;
                return class_exists(DeviceUiDisplaySanitizer::class, false);
            }
        }

        return false;
    }

    /**
     * Pixel-to-pixel with Views/items/view_page_ui.php .phone-shell ParameterCard CSS.
     * Rewrites known size/alignment style values only - no schema structure change.
     */
    private static function applyPreviewPixelParity(array $schema): array
    {
        $parity = [
            'layout' => [
                'type' => 'grid',
                'columns' => ['auto', '1fr', 'auto'],
                'gap' => 10,
                'alignItems' => 'center',
            ],
            'minHeight' => 60,
            'gap' => 10,
            'iconBubble' => [
                'size' => 44,
                'width' => 44,
                'height' => 44,
                'shape' => 'circle',
                'iconSize' => 22,
            ],
            'label' => [
                'fontSize' => 10,
                'fontWeight' => 600,
                'marginBottom' => 4,
                'letterSpacing' => 0.08,
                'lineHeight' => 1.15,
                'uppercase' => true,
                'textAlign' => 'left',
            ],
            'value' => [
                'fontSize' => 15,
                'fontWeight' => 600,
                'lineHeight' => 1.35,
                'letterSpacing' => -0.015,
                'textAlign' => 'left',
            ],
            'switch' => [
                'trackWidth' => 52,
                'trackHeight' => 28,
                'width' => 52,
                'height' => 28,
                'thumbSize' => 22,
                'thumbInset' => 3,
                'thumbTravel' => 24,
                'shape' => 'pill',
            ],
            'powerButton' => [
                'size' => 48,
                'width' => 48,
                'height' => 48,
                'borderWidth' => 3,
                'shape' => 'circle',
            ],
            'actionDot' => [
                'size' => 48,
                'width' => 48,
                'height' => 48,
                'borderWidth' => 3,
                'shape' => 'circle',
            ],
            'sendButton' => [
                'size' => 32,
                'width' => 32,
                'height' => 32,
                'borderRadius' => 9,
                'iconSize' => 15,
            ],
            'metaChip' => [
                'minWidth' => 40,
                'height' => 32,
                'fontSize' => 11,
                'fontWeight' => 600,
                'iconSize' => 14,
            ],
        ];

        foreach ([
            ['designSystem', 'componentStyles', 'parameterCard'],
            ['extensions', 'universalUi', 'designSystem', 'componentStyles', 'parameterCard'],
            ['extensions', 'serverDrivenUi', 'designSystem', 'componentStyles', 'parameterCard'],
        ] as $path) {
            $schema = self::mergeParameterCardParityPath($schema, $path, $parity);
        }

        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $index => $control) {
                if (!is_array($control)) {
                    continue;
                }
                $schema['controls'][$index] = self::applyControlPreviewPixelParity($control, $parity);
            }
        }

        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::rewriteParameterCardWidgetParity($schema['ui']['widgetTree'], $parity);
        }

        if (isset($schema['theme']) && is_array($schema['theme'])) {
            $btn = (int) ($schema['theme']['buttonSize'] ?? 0);
            if ($btn > 0 && $btn < 40) {
                $schema['theme']['buttonSize'] = 48;
            }
        }

        return $schema;
    }

    private static function mergeParameterCardParityPath(array $schema, array $path, array $parity): array
    {
        $cursor = &$schema;
        foreach ($path as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                unset($cursor);
                return $schema;
            }
            $cursor = &$cursor[$segment];
        }
        $cursor['layout'] = $parity['layout'];
        $cursor['minHeight'] = $parity['minHeight'];
        $cursor['gap'] = $parity['gap'];
        $cursor['alignItems'] = 'center';
        if (!isset($cursor['parts']) || !is_array($cursor['parts'])) {
            $cursor['parts'] = [];
        }
        foreach (['iconBubble', 'label', 'value', 'switch', 'powerButton', 'actionDot', 'sendButton', 'metaChip'] as $part) {
            $existing = (isset($cursor['parts'][$part]) && is_array($cursor['parts'][$part])) ? $cursor['parts'][$part] : [];
            $cursor['parts'][$part] = array_merge($existing, $parity[$part]);
        }
        unset($cursor);

        return $schema;
    }

    private static function applyControlPreviewPixelParity(array $control, array $parity): array
    {
        $uiCount = isset($control['uiCount']) ? (int) $control['uiCount'] : 0;
        $grouped = $uiCount === 1 || trim((string) ($control['groupParameter'] ?? '')) !== '';
        $iconSize = $grouped ? 42 : 44;
        if (!isset($control['styles']) || !is_array($control['styles'])) {
            $control['styles'] = [];
        }
        $bubble = (isset($control['styles']['iconBubble']) && is_array($control['styles']['iconBubble']))
            ? $control['styles']['iconBubble']
            : [];
        $control['styles']['iconBubble'] = array_merge($bubble, [
            'size' => $iconSize,
            'width' => $iconSize,
            'height' => $iconSize,
            'iconSize' => min((int) ($bubble['iconSize'] ?? 22), 22),
            'shape' => 'circle',
        ]);

        $type = strtolower(str_replace(['_', '-'], '', (string) ($control['componentType'] ?? '')));
        if (in_array($type, ['switch', 'toggleswitch'], true)) {
            $control = self::mergeSizedProps($control, $parity['switch']);
        }
        if (in_array($type, ['button', 'pushbutton'], true)) {
            $control = self::mergeSizedProps($control, [
                'size' => $parity['powerButton']['size'],
                'width' => $parity['powerButton']['width'],
                'height' => $parity['powerButton']['height'],
                'borderWidth' => $parity['powerButton']['borderWidth'],
                'shape' => $parity['powerButton']['shape'],
            ]);
        }
        if (in_array($type, ['textfield', 'entertext'], true)
            && isset($control['component']['props']['sendButton'])
            && is_array($control['component']['props']['sendButton'])) {
            $control['component']['props']['sendButton'] = array_merge(
                $control['component']['props']['sendButton'],
                $parity['sendButton']
            );
        }

        return $control;
    }

    private static function mergeSizedProps(array $control, array $metrics): array
    {
        if (isset($control['component']['props']) && is_array($control['component']['props'])) {
            $control['component']['props'] = array_merge($control['component']['props'], $metrics);
        }
        if (isset($control['component']['widgetTree']['properties']['props'])
            && is_array($control['component']['widgetTree']['properties']['props'])) {
            $control['component']['widgetTree']['properties']['props'] = array_merge(
                $control['component']['widgetTree']['properties']['props'],
                $metrics
            );
        }
        if (isset($control['rightAccessory']) && is_array($control['rightAccessory'])) {
            $control['rightAccessory'] = array_merge($control['rightAccessory'], $metrics);
        }

        return $control;
    }

    private static function rewriteParameterCardWidgetParity(&$node, array $parity, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $type = strtolower(trim((string) ($node['type'] ?? '')));
        if ($type === 'parametercard') {
            if (!isset($node['style']) || !is_array($node['style'])) {
                $node['style'] = [];
            }
            $node['style']['minHeight'] = $parity['minHeight'];
            $node['style']['gap'] = $parity['gap'];
            $node['style']['alignItems'] = 'center';
            $node['style']['crossAxisAlignment'] = 'center';
            if (isset($node['properties']) && is_array($node['properties'])) {
                $node['properties']['minHeight'] = $parity['minHeight'];
                $node['properties']['gap'] = $parity['gap'];
                $node['properties']['alignItems'] = 'center';
            }
        }
        foreach (['children', 'body', 'items', 'nodes'] as $listKey) {
            if (!isset($node[$listKey]) || !is_array($node[$listKey])) {
                continue;
            }
            foreach ($node[$listKey] as $i => $child) {
                if (is_array($child)) {
                    self::rewriteParameterCardWidgetParity($node[$listKey][$i], $parity, $depth + 1);
                }
            }
        }
        foreach ($node as $key => $child) {
            if (!is_array($child) || self::schemaWalkSkipKey($key) || in_array($key, ['children', 'body', 'items', 'nodes'], true)) {
                continue;
            }
            self::rewriteParameterCardWidgetParity($node[$key], $parity, $depth + 1);
        }
    }

    /**
     * Already-provisioned devices must control/status without re-running Wi-Fi setup.
     * Keeps the same keys; only refreshes enableWhen / controlReadyWhen / onlineConfirmed values.
     */
    private static function unlockOnlineDeviceControl(array $schema): array
    {
        $readyWhen = 'state.online == true || state.wifiProvisioning.onlineConfirmed == true';
        $legacyWifiOnly = [
            'state.wifiProvisioning.onlineConfirmed == true',
            'state.wifiProvisioning.onlineConfirmed===true',
            "state.wifiProvisioning.onlineConfirmed == true",
        ];

        if (isset($schema['mqtt']) && is_array($schema['mqtt'])) {
            $enableWhen = trim((string) ($schema['mqtt']['enableWhen'] ?? ''));
            if ($enableWhen === '' || in_array($enableWhen, $legacyWifiOnly, true)) {
                $schema['mqtt']['enableWhen'] = $readyWhen;
            }
        }

        if (isset($schema['wifiProvisioning']['postProvision']) && is_array($schema['wifiProvisioning']['postProvision'])) {
            $controlReady = trim((string) ($schema['wifiProvisioning']['postProvision']['controlReadyWhen'] ?? ''));
            if ($controlReady === '' || in_array($controlReady, $legacyWifiOnly, true)) {
                $schema['wifiProvisioning']['postProvision']['controlReadyWhen'] = $readyWhen;
            }
        }

        $schema = self::syncOnlineStateFromConnectivity($schema);

        $online = !empty($schema['state']['online']);
        if (isset($schema['state']['wifiProvisioning']) && is_array($schema['state']['wifiProvisioning'])) {
            if ($online) {
                $schema['state']['wifiProvisioning']['onlineConfirmed'] = true;
            } elseif (!array_key_exists('onlineConfirmed', $schema['state']['wifiProvisioning'])) {
                $schema['state']['wifiProvisioning']['onlineConfirmed'] = false;
            }
        }

        return $schema;
    }

    /**
     * Prefer live connectivity.connected over a stale state.online=false baked at publish time.
     */
    private static function syncOnlineStateFromConnectivity(array $schema): array
    {
        $connected = null;
        if (isset($schema['node_details'][0]['status']['connectivity']['connected'])) {
            $connected = (bool) $schema['node_details'][0]['status']['connectivity']['connected'];
        } elseif (isset($schema['state']['deviceConnectivity']['online'])) {
            $connected = (bool) $schema['state']['deviceConnectivity']['online'];
        }

        if ($connected === null) {
            return $schema;
        }

        if (!isset($schema['state']) || !is_array($schema['state'])) {
            $schema['state'] = [];
        }
        $label = $connected ? 'Online' : 'Offline';
        $schema['state']['online'] = $connected;
        $schema['state']['onlineLabel'] = $label;

        if (!isset($schema['state']['deviceConnectivity']) || !is_array($schema['state']['deviceConnectivity'])) {
            $schema['state']['deviceConnectivity'] = [];
        }
        $schema['state']['deviceConnectivity']['online'] = $connected;
        $schema['state']['deviceConnectivity']['label'] = $label;

        if (isset($schema['extensions']['serverDrivenUi']['state']) && is_array($schema['extensions']['serverDrivenUi']['state'])) {
            $schema['extensions']['serverDrivenUi']['state']['online'] = $connected;
            $schema['extensions']['serverDrivenUi']['state']['onlineLabel'] = $label;
        }
        if (isset($schema['extensions']['universal']['data']['state']) && is_array($schema['extensions']['universal']['data']['state'])) {
            $schema['extensions']['universal']['data']['state']['online'] = $connected;
            $schema['extensions']['universal']['data']['state']['onlineLabel'] = $label;
        }

        $theme = (isset($schema['theme']) && is_array($schema['theme'])) ? $schema['theme'] : [];
        $colors = (isset($theme['colors']) && is_array($theme['colors'])) ? $theme['colors'] : [];
        $color = $connected
            ? (string) ($theme['successColor'] ?? ($colors['success'] ?? ($colors['accent'] ?? '#22C55E')))
            : (string) ($theme['errorColor'] ?? ($colors['error'] ?? '#EF4444'));
        self::paintOnlineLabels($schema, $label, $color);

        return $schema;
    }

    /**
     * Keep baked brand-strip text on the same connectivity flag as state.online.
     */
    private static function paintOnlineLabels(&$node, string $label, string $color): void
    {
        if (!is_array($node)) {
            return;
        }

        $id = isset($node['id']) ? (string) $node['id'] : '';
        $props = (isset($node['properties']) && is_array($node['properties'])) ? $node['properties'] : null;
        $textBinding = ($props && isset($props['textBinding'])) ? (string) $props['textBinding'] : '';
        if ($textBinding === 'state.onlineLabel' || $id === 'online_text') {
            if ($props !== null) {
                $node['properties']['text'] = $label;
            }
            if (array_key_exists('text', $node)) {
                $node['text'] = $label;
            }
            if ($color !== '' && isset($node['style']) && is_array($node['style'])) {
                foreach (['color', 'textColor'] as $key) {
                    if (array_key_exists($key, $node['style'])) {
                        $node['style'][$key] = $color;
                    }
                }
                if (isset($node['style']['textStyle']) && is_array($node['style']['textStyle']) && array_key_exists('color', $node['style']['textStyle'])) {
                    $node['style']['textStyle']['color'] = $color;
                }
            }
        }
        if ($id === 'online_dot' && $color !== '' && isset($node['style']) && is_array($node['style']) && array_key_exists('backgroundColor', $node['style'])) {
            $node['style']['backgroundColor'] = $color;
        }
        if (isset($node['labelBinding']) && (string) $node['labelBinding'] === 'state.onlineLabel') {
            if (array_key_exists('text', $node)) {
                $node['text'] = $label;
            }
            if ($color !== '' && array_key_exists('color', $node)) {
                $node['color'] = $color;
            }
        }

        foreach ($node as &$child) {
            if (is_array($child)) {
                self::paintOnlineLabels($child, $label, $color);
            }
        }
        unset($child);
    }

    /**
     * Add dark/light presets and the same runtime appearance contract App Home uses.
     *
     * Default: system (follow phone). If the account saved light/dark in web Profile
     * settings, that value is applied via AppAppearance - never collapse system here.
     */
    private static function attachModeThemes(array $schema, int $userId = 0): array
    {
        $theme = (isset($schema['theme']) && is_array($schema['theme'])) ? $schema['theme'] : array();
        $activeMode = self::resolveAccountAppearance($userId);
        $schema['themes'] = self::buildModeThemes(self::devicePalettes($theme), $activeMode);

        if (!isset($schema['theme']) || !is_array($schema['theme'])) {
            $schema['theme'] = array();
        }
        // Keep authored colors for System (the app follows the phone).
        // Light/Dark from Profile rewrites only color values that already exist.
        $schema['theme']['defaultMode'] = $activeMode;
        $schema['theme']['mode'] = $activeMode;
        if (array_key_exists('themeMode', $schema['theme'])) {
            $schema['theme']['themeMode'] = $activeMode;
        }
        $schema = self::applyAccountPaletteToExistingColors($schema, $activeMode);
        // Header and value colors were painted from the shared model palette.
        // Repaint them for this request only so one phone's Light does not keep
        // another phone's Dark surfaces. Keys stay the same.
        if ($activeMode === 'light' || $activeMode === 'dark') {
            $schema = self::applyCanonicalHeader($schema);
            $schema = self::applyCanonicalValueColors($schema);
            if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
                self::paintWidgetChrome($schema['ui']['widgetTree'], $activeMode);
            }
            if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
                self::paintWidgetChrome($schema['extensions']['serverDrivenUi'], $activeMode);
            }
            if (isset($schema['extensions']['universalUi']) && is_array($schema['extensions']['universalUi'])) {
                self::paintWidgetChrome($schema['extensions']['universalUi'], $activeMode);
            }
            foreach (array('designSystem', 'mobileView', 'controls', 'pages') as $chromeKey) {
                if (isset($schema[$chromeKey]) && is_array($schema[$chromeKey])) {
                    self::paintWidgetChrome($schema[$chromeKey], $activeMode);
                }
            }
        }

        if (!isset($schema['state']) || !is_array($schema['state'])) {
            $schema['state'] = array();
        }
        if (!isset($schema['state']['global']) || !is_array($schema['state']['global'])) {
            $schema['state']['global'] = array();
        }
        $schema['state']['global']['themeMode'] = $activeMode;
        $schema['state']['global']['themeServerControlled'] = ($activeMode !== 'system');
        $schema['state']['global']['followSystem'] = ($activeMode === 'system');
        $schema['state']['global']['watchOsAppearance'] = ($activeMode === 'system');

        if (isset($schema['wifiProvisioning']) && is_array($schema['wifiProvisioning'])) {
            $wifiTheme = (isset($schema['wifiProvisioning']['theme']) && is_array($schema['wifiProvisioning']['theme']))
                ? $schema['wifiProvisioning']['theme']
                : array();
            $wifiColors = (isset($wifiTheme['colors']) && is_array($wifiTheme['colors'])) ? $wifiTheme['colors'] : array();
            $schema['wifiProvisioning']['themes'] = self::buildModeThemes(self::wifiPalettes($wifiColors), $activeMode);
        }

        return $schema;
    }

    /**
     * Profile Light/Dark: replace existing theme color values so the app paints
     * a readable palette. Does not add or remove keys.
     *
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    private static function applyAccountPaletteToExistingColors(array $schema, string $activeMode): array
    {
        if ($activeMode !== 'light' && $activeMode !== 'dark') {
            return $schema;
        }
        if (!isset($schema['theme']) || !is_array($schema['theme'])) {
            return $schema;
        }

        $dark = $activeMode === 'dark';
        $colors = $dark
            ? array(
                'background' => '#0B1220',
                'gradientEnd' => '#111827',
                'shell' => '#0F172A',
                'surface' => '#1E293B',
                'card' => '#1E293B',
                'text' => '#F8FAFC',
                'mutedText' => '#94A3B8',
                'muted' => '#94A3B8',
                'secondary' => '#64748B',
                'border' => '#334155',
                'topbar' => '#020617',
                'toggleOff' => '#475569',
            )
            : array(
                'background' => '#F8FAFC',
                'gradientEnd' => '#EEF2F7',
                'shell' => '#F8FAFC',
                'surface' => '#FFFFFF',
                'card' => '#FFFFFF',
                'text' => '#0F172A',
                'mutedText' => '#64748B',
                'muted' => '#64748B',
                'secondary' => '#94A3B8',
                'border' => '#E2E8F0',
                'topbar' => '#FFFFFF',
                'toggleOff' => '#CBD5E1',
            );
        $flat = $dark
            ? array(
                'backgroundColor' => '#0B1220',
                'gradientColor' => '#111827',
                'shellColor' => '#0F172A',
                'cardColor' => '#1E293B',
                'textColor' => '#F8FAFC',
                'mutedTextColor' => '#94A3B8',
                'topbarColor' => '#020617',
                'secondaryColor' => '#64748B',
                'borderColor' => '#334155',
                'toggleOffColor' => '#475569',
            )
            : array(
                'backgroundColor' => '#F8FAFC',
                'gradientColor' => '#EEF2F7',
                'shellColor' => '#F8FAFC',
                'cardColor' => '#FFFFFF',
                'textColor' => '#0F172A',
                'mutedTextColor' => '#64748B',
                'topbarColor' => '#FFFFFF',
                'secondaryColor' => '#94A3B8',
                'borderColor' => '#E2E8F0',
                'toggleOffColor' => '#CBD5E1',
            );

        $theme = &$schema['theme'];
        if (isset($theme['colors']) && is_array($theme['colors'])) {
            foreach ($colors as $key => $value) {
                if (array_key_exists($key, $theme['colors'])) {
                    $theme['colors'][$key] = $value;
                }
            }
        }
        foreach ($flat as $key => $value) {
            if (array_key_exists($key, $theme)) {
                $theme[$key] = $value;
            }
        }
        if (isset($theme['tokens']) && is_array($theme['tokens'])) {
            foreach ($flat as $key => $value) {
                if (array_key_exists($key, $theme['tokens'])) {
                    $theme['tokens'][$key] = $value;
                }
            }
            if (isset($theme['tokens']['colors']) && is_array($theme['tokens']['colors'])) {
                foreach ($colors as $key => $value) {
                    if (array_key_exists($key, $theme['tokens']['colors'])) {
                        $theme['tokens']['colors'][$key] = $value;
                    }
                }
            }
        }
        unset($theme);

        return $schema;
    }

    private static function activeThemeMode(array $theme): string
    {
        // studioMode keeps the authored Design Studio palette after runtime mode is "system".
        $studio = strtolower(trim((string) ($theme['studioMode'] ?? '')));
        if ($studio === 'light' || $studio === 'dark') {
            return $studio;
        }

        $mode = strtolower(trim((string) ($theme['mode'] ?? ($theme['themeMode'] ?? 'dark'))));
        if ($mode === 'light') {
            return 'light';
        }
        if ($mode === 'dark') {
            return 'dark';
        }

        // Runtime "system" (or unknown): infer from authored background when possible.
        $background = '';
        if (isset($theme['colors']['background']) && is_string($theme['colors']['background'])) {
            $background = $theme['colors']['background'];
        } elseif (isset($theme['backgroundColor']) && is_string($theme['backgroundColor'])) {
            $background = $theme['backgroundColor'];
        }
        $hex = strtoupper(ltrim(trim($background), '#'));
        if (preg_match('/^[0-9A-F]{6}$/', $hex)) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $luma = (0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b);

            return $luma >= 140 ? 'light' : 'dark';
        }

        return 'dark';
    }

    private static function buildModeThemes(array $palettes, string $activeMode): array
    {
        $dark = isset($palettes['dark']) && is_array($palettes['dark']) ? $palettes['dark'] : array();
        $light = isset($palettes['light']) && is_array($palettes['light']) ? $palettes['light'] : array();
        $keys = array_unique(array_merge(array_keys($dark), array_keys($light)));
        sort($keys);
        $alignedDark = array();
        $alignedLight = array();
        foreach ($keys as $key) {
            $darkValue = array_key_exists($key, $dark) ? $dark[$key] : null;
            $lightValue = array_key_exists($key, $light) ? $light[$key] : null;
            if ($darkValue === null || $darkValue === '') {
                $darkValue = $lightValue;
            }
            if ($lightValue === null || $lightValue === '') {
                $lightValue = $darkValue;
            }
            $alignedDark[$key] = $darkValue;
            $alignedLight[$key] = $lightValue;
        }
        if ($activeMode !== 'light' && $activeMode !== 'dark' && $activeMode !== 'system') {
            $activeMode = 'system';
        }

        return array(
            'serverControlled' => ($activeMode !== 'system'),
            'followSystem' => ($activeMode === 'system'),
            'watchOsAppearance' => ($activeMode === 'system'),
            'overrideSystemBrightness' => ($activeMode !== 'system'),
            'activeMode' => $activeMode,
            'activeModeBinding' => array('$state' => 'global.themeMode'),
            'presets' => array(
                'dark' => array('color' => $alignedDark),
                'light' => array('color' => $alignedLight),
            ),
        );
    }

    private static function devicePalettes(array $theme): array
    {
        $colors = array();
        if (isset($theme['colors']) && is_array($theme['colors'])) {
            foreach ($theme['colors'] as $key => $value) {
                if (is_string($key) && is_string($value) && $value !== '') {
                    $colors[$key] = $value;
                }
            }
        }
        $accent = $colors['accent'] ?? '#60A5FA';
        $dark = array(
            'bg.primary' => $colors['background'] ?? '#0B1220',
            'bg.surface' => $colors['surface'] ?? ($colors['card'] ?? '#1E293B'),
            'bg.elevated' => $colors['shell'] ?? '#0F172A',
            'text.primary' => $colors['text'] ?? '#F8FAFC',
            'text.secondary' => $colors['mutedText'] ?? ($colors['muted'] ?? '#94A3B8'),
            'text.muted' => $colors['mutedText'] ?? ($colors['muted'] ?? '#94A3B8'),
            'accent' => $accent,
            'accent.on' => $colors['buttonText'] ?? '#FFFFFF',
            'border' => $colors['border'] ?? '#334155',
            'online' => $colors['success'] ?? '#22C55E',
            'offline' => $colors['secondary'] ?? '#64748B',
            'danger' => $colors['error'] ?? '#EF4444',
            'nav.background' => $colors['topbar'] ?? '#020617',
            'nav.active' => $accent,
        );
        $light = array(
            'bg.primary' => '#F8FAFC',
            'bg.surface' => '#FFFFFF',
            'bg.elevated' => '#FFFFFF',
            'text.primary' => '#0F172A',
            'text.secondary' => '#475569',
            'text.muted' => '#64748B',
            'accent' => $accent,
            'accent.on' => '#FFFFFF',
            'border' => '#E2E8F0',
            'online' => '#16A34A',
            'offline' => '#94A3B8',
            'danger' => '#DC2626',
            'nav.background' => '#FFFFFF',
            'nav.active' => $accent,
        );
        $lightLegacy = array(
            'background' => '#F8FAFC',
            'gradientEnd' => '#E2E8F0',
            'shell' => '#FFFFFF',
            'surface' => '#FFFFFF',
            'card' => '#FFFFFF',
            'text' => '#0F172A',
            'mutedText' => '#64748B',
            'muted' => '#64748B',
            'accent' => $accent,
            'primary' => $accent,
            'secondary' => '#64748B',
            'success' => '#16A34A',
            'warning' => '#D97706',
            'error' => '#DC2626',
            'border' => '#E2E8F0',
            'topbar' => '#FFFFFF',
            'toggleOn' => $accent,
            'toggleOff' => '#CBD5E1',
            'buttonBg' => $accent,
            'buttonText' => '#FFFFFF',
            'icon' => $accent,
        );
        if (self::activeThemeMode($theme) === 'light') {
            $light['bg.primary'] = $colors['background'] ?? $light['bg.primary'];
            $light['bg.surface'] = $colors['surface'] ?? ($colors['card'] ?? $light['bg.surface']);
            $light['bg.elevated'] = $colors['shell'] ?? $light['bg.elevated'];
            $light['text.primary'] = $colors['text'] ?? $light['text.primary'];
            $light['text.secondary'] = $colors['mutedText'] ?? ($colors['muted'] ?? $light['text.secondary']);
            $light['text.muted'] = $colors['mutedText'] ?? ($colors['muted'] ?? $light['text.muted']);
            $light['border'] = $colors['border'] ?? $light['border'];
            $light['nav.background'] = $colors['topbar'] ?? $light['nav.background'];
            $light['danger'] = $colors['error'] ?? $light['danger'];
            $light['online'] = $colors['success'] ?? $light['online'];
            $dark['bg.primary'] = '#0B1220';
            $dark['bg.surface'] = '#1E293B';
            $dark['bg.elevated'] = '#0F172A';
            $dark['text.primary'] = '#F8FAFC';
            $dark['text.secondary'] = '#94A3B8';
            $dark['text.muted'] = '#94A3B8';
            $dark['border'] = '#334155';
            $dark['nav.background'] = '#020617';
        }
        foreach ($colors as $key => $value) {
            if (self::activeThemeMode($theme) === 'light') {
                $light[$key] = $value;
                if (!array_key_exists($key, $dark)) {
                    $dark[$key] = $lightLegacy[$key] ?? $value;
                }
            } else {
                $dark[$key] = $value;
                if (!array_key_exists($key, $light)) {
                    $light[$key] = $lightLegacy[$key] ?? $value;
                }
            }
        }

        return array('dark' => $dark, 'light' => $light);
    }

    private static function wifiPalettes(array $colors): array
    {
        $clean = array();
        foreach ($colors as $key => $value) {
            if (is_string($key) && is_string($value) && $value !== '') {
                $clean[$key] = $value;
            }
        }
        $accent = $clean['accent'] ?? '#2F6BFF';
        $light = array(
            'bg.primary' => '#F7F9FC',
            'bg.surface' => '#FFFFFF',
            'bg.elevated' => '#FFFFFF',
            'text.primary' => '#0B1B3D',
            'text.secondary' => '#8B95A7',
            'text.muted' => '#8B95A7',
            'accent' => $accent,
            'accent.on' => '#FFFFFF',
            'border' => '#E6EAF2',
            'online' => '#22C55E',
            'offline' => '#8B95A7',
            'danger' => '#EF4444',
            'nav.background' => '#FFFFFF',
            'nav.active' => $accent,
            'background' => '#F7F9FC',
            'surface' => '#FFFFFF',
            'text' => '#0B1B3D',
            'mutedText' => '#8B95A7',
            'accentSoft' => '#E8F0FE',
            'primary' => $accent,
            'secondary' => '#1D4ED8',
            'success' => '#22C55E',
            'successSoft' => '#DCFCE7',
            'warning' => '#F59E0B',
            'warningSoft' => '#FEF3C7',
            'error' => '#EF4444',
            'errorSoft' => '#FEE2E2',
            'heroStart' => '#0B1F4D',
            'heroEnd' => '#1D4ED8',
            'heroSuccessStart' => '#064E3B',
            'heroSuccessEnd' => '#059669',
            'heroErrorStart' => '#7F1D1D',
            'heroErrorEnd' => '#DC2626',
            'onAccent' => '#FFFFFF',
            'onHero' => '#FFFFFF',
            'trackerPendingFill' => '#E8EDF5',
            'trackerPendingText' => '#9AA4B2',
            'trackerLinePending' => '#E8EDF5',
        );
        $dark = array(
            'bg.primary' => '#0B1220',
            'bg.surface' => '#1E293B',
            'bg.elevated' => '#0F172A',
            'text.primary' => '#F8FAFC',
            'text.secondary' => '#94A3B8',
            'text.muted' => '#94A3B8',
            'accent' => $accent,
            'accent.on' => '#FFFFFF',
            'border' => '#334155',
            'online' => '#22C55E',
            'offline' => '#94A3B8',
            'danger' => '#FCA5A5',
            'nav.background' => '#0B1220',
            'nav.active' => $accent,
            'background' => '#0B1220',
            'surface' => '#1E293B',
            'text' => '#F8FAFC',
            'mutedText' => '#94A3B8',
            'accentSoft' => '#1E3A8A',
            'primary' => $accent,
            'secondary' => '#93C5FD',
            'success' => '#22C55E',
            'successSoft' => '#064E3B',
            'warning' => '#F59E0B',
            'warningSoft' => '#78350F',
            'error' => '#FCA5A5',
            'errorSoft' => '#7F1D1D',
            'heroStart' => '#0B1F4D',
            'heroEnd' => '#1D4ED8',
            'heroSuccessStart' => '#064E3B',
            'heroSuccessEnd' => '#059669',
            'heroErrorStart' => '#7F1D1D',
            'heroErrorEnd' => '#DC2626',
            'onAccent' => '#FFFFFF',
            'onHero' => '#FFFFFF',
            'trackerPendingFill' => '#1E293B',
            'trackerPendingText' => '#94A3B8',
            'trackerLinePending' => '#334155',
        );
        foreach ($clean as $key => $value) {
            $light[$key] = $value;
            if (!array_key_exists($key, $dark)) {
                $dark[$key] = $value;
            }
        }

        return array('dark' => $dark, 'light' => $light);
    }

    /**
     * Copies protocolMeta.command_id / function_code / action_id into the matching
     * deviceFrame command slots. Also reverse-hydrates meta from non-empty frame
     * slots when meta is blank. Does not invent values product_api never stored.
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

            // Frame → meta: recover real ids already present on slots but missing in meta.
            foreach ($commands as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $slot = self::frameFieldName($field);
                if (!in_array($slot, ['commandid', 'functioncode', 'actionid'], true)) {
                    continue;
                }
                $slotValue = isset($field['value']) ? $field['value'] : null;
                if ($slotValue === null || $slotValue === '' || $slotValue === []) {
                    $slotValue = isset($field['asperui_value']) ? $field['asperui_value'] : null;
                }
                if ($slotValue === null || $slotValue === '' || $slotValue === [] || self::isZeroProtocolId($slotValue)) {
                    continue;
                }
                $normalized = self::normalizeProtocolHex($slotValue, 1);
                if ($normalized === '' || self::isZeroProtocolId($normalized)) {
                    continue;
                }
                if ($slot === 'commandid' && ($commandId === null || self::isZeroProtocolId($commandId))) {
                    $commandId = $normalized;
                } elseif ($slot === 'functioncode' && ($functionCode === null || self::isZeroProtocolId($functionCode))) {
                    $functionCode = $normalized;
                } elseif ($slot === 'actionid' && ($actionId === null || self::isZeroProtocolId($actionId))) {
                    $actionId = $normalized;
                }
            }

            $filled = $commands;
            if ($commandId !== null || $functionCode !== null || $actionId !== null) {
                $filled = self::fillWriteFieldSlots($filled, $commandId, $functionCode, $actionId, $report, $control);
            }
            $filled = self::fillTerminatorSlot($filled, $report, $control);
            $metaChanged = false;
            if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                $control['protocolMeta'] = $meta;
                $metaChanged = true;
            }
            if ($commandId !== null && !self::isZeroProtocolId($commandId)
                && self::firstNonEmpty(isset($control['protocolMeta']['command_id']) ? $control['protocolMeta']['command_id'] : null) === null
            ) {
                $control['protocolMeta']['command_id'] = $commandId;
                $metaChanged = true;
            }
            if ($functionCode !== null && !self::isZeroProtocolId($functionCode)
                && self::firstNonEmpty(isset($control['protocolMeta']['function_code']) ? $control['protocolMeta']['function_code'] : null) === null
            ) {
                $control['protocolMeta']['function_code'] = $functionCode;
                $metaChanged = true;
            }
            if ($actionId !== null && !self::isZeroProtocolId($actionId)
                && self::firstNonEmpty(isset($control['protocolMeta']['action_id']) ? $control['protocolMeta']['action_id'] : null) === null
            ) {
                $control['protocolMeta']['action_id'] = $actionId;
                $metaChanged = true;
            }

            if ($filled === $commands && !$metaChanged) {
                continue;
            }

            $frame['commands'] = $filled;
            $control['deviceFrame'] = $frame;
            $control['protocolMeta']['frame'] = $frame;
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
        $terminatorIndexes = [];
        foreach ($commands as $i => $field) {
            if (!is_array($field)) {
                continue;
            }
            if (self::frameFieldName($field) !== 'terminator') {
                continue;
            }
            $terminatorIndexes[] = $i;
        }

        if (!empty($terminatorIndexes)) {
            $keep = null;
            foreach ($terminatorIndexes as $i) {
                $val = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) (isset($commands[$i]['value']) ? $commands[$i]['value'] : '')));
                if ($val === '0A') {
                    $keep = $i;
                    break;
                }
            }
            if ($keep === null) {
                $keep = (int) end($terminatorIndexes);
            }

            $out = [];
            foreach ($commands as $i => $field) {
                if (!is_array($field)) {
                    $out[] = $field;
                    continue;
                }
                if (self::frameFieldName($field) !== 'terminator') {
                    $out[] = $field;
                    continue;
                }
                if ($i !== $keep) {
                    continue;
                }
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
                $field['default_name'] = 'Terminator';
                $field['Field_description'] = 'Terminator';
                $field['value'] = $normalized;
                $field['no_of_bytes'] = 1;
                if (!isset($field['asperui_value']) || $field['asperui_value'] === null || $field['asperui_value'] === ''
                    || !self::looksLikeProtocolHex($field['asperui_value'])
                ) {
                    $field['asperui_value'] = $normalized;
                }
                $out[] = $field;
                $report['repairedByteFields'][] = ($controlId !== '' ? $controlId : 'control') . '.commands.Terminator';
            }

            return array_values($out);
        }

        if (self::controlNeedsSendProtocol($control)) {
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
     * Enforce Rule 1-3 for every Send / writable user-input control:
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
        if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
            $control['protocolMeta'] = [];
        }
        $access = strtolower(trim((string) (isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : '')));
        // Preserve Ready when the API declared it; only fill blank/read with write.
        if ($access === '' || in_array($access, ['read', 'readonly', 'read-only', 'read_only', 'none'], true)) {
            $control['access'] = 'write';
            $control['protocolMeta']['access'] = 'write';
        } elseif (strpos($access, 'ready') !== false) {
            $control['access'] = $control['protocolMeta']['access'];
        } elseif (strpos($access, 'write') === false) {
            $control['access'] = 'Read/Write';
            $control['protocolMeta']['access'] = 'Read/Write';
        } else {
            $control['access'] = $control['protocolMeta']['access'];
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
     * Does not rename either key - both stay in the schema when present.
     */
    private static function resolveCommandByteCount(array $field)
    {
        // Explicit 0 is a zero-width payload (Command Get Status Data). Do not
        // invent a byte — that shifts the terminator and can absorb a header byte.
        if (array_key_exists('no_of_bytes', $field) && $field['no_of_bytes'] !== '' && $field['no_of_bytes'] !== null) {
            $declared = (int) $field['no_of_bytes'];
            if ($declared === 0) {
                return 0;
            }
            if ($declared > 0) {
                return $declared;
            }
        }
        $bytes = 0;
        if (isset($field['byteLength'])) {
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
            if ($bytes < 1 && $name !== 'terminator') {
                $commands[$i] = $field;
                continue;
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
                    // Never invent protocol IDs - leave empty so writeReady stays false
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
            // Widen asperui only when it is the wire value (e.g. "00" -> "0000").
            // A shorter UI token such as "1" next to wire "A2EA" must stay "1".
            if (isset($field['asperui_value']) && self::looksLikeProtocolHex($field['asperui_value'])) {
                $asperuiRaw = strtoupper(preg_replace('/[\s:_-]+/', '', preg_replace('/^0x/i', '', trim((string) $field['asperui_value']))));
                if ($normalized === '' || strcasecmp($asperuiRaw, $normalized) === 0 || strlen($asperuiRaw) >= strlen($normalized)) {
                    $asperuiHex = self::normalizeProtocolHex($field['asperui_value'], $bytes);
                    if ($asperuiHex !== '') {
                        $field['asperui_value'] = $asperuiHex;
                    }
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

            if ($fill !== null && self::protocolSlotNeedsFill($value, $fill)) {
                $field['value'] = $fill;
                if (!isset($field['asperui_value']) || $field['asperui_value'] === null || $field['asperui_value'] === ''
                    || self::protocolSlotNeedsFill($field['asperui_value'], $fill)
                ) {
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

    /**
     * Empty slots and zero-padding written when product_api had no hex
     * must take the real protocolMeta id. A stored non-zero id is left alone.
     */
    private static function protocolSlotNeedsFill($current, $incoming)
    {
        if ($incoming === null || $incoming === '' || $incoming === []) {
            return false;
        }
        if ($current === null || $current === '' || $current === []) {
            return true;
        }
        $currentHex = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $current));
        $incomingHex = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $incoming));
        $currentZero = $currentHex !== '' && preg_match('/^0+$/', $currentHex) === 1;
        $incomingZero = $incomingHex !== '' && preg_match('/^0+$/', $incomingHex) === 1;

        return $currentZero && !$incomingZero;
    }

    /**
     * Copy known command ids into every frame the app reads, drop "[]" option
     * lists, and name response telemetry after the parameter.
     */
    private static function repairPublishedFrames(array $schema)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }
        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
            $parameterName = trim((string) self::firstNonEmpty(
                isset($control['parameterName']) ? $control['parameterName'] : null,
                isset($control['parameter']) ? $control['parameter'] : null,
                isset($control['label']) ? $control['label'] : null
            ));
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
            if (isset($control['deviceFrame']) && is_array($control['deviceFrame'])) {
                $control['deviceFrame'] = self::repairFrameDocument($control['deviceFrame'], $commandId, $functionCode, $actionId, $parameterName);
            }
            if (isset($meta['frame']) && is_array($meta['frame'])) {
                $meta['frame'] = self::repairFrameDocument($meta['frame'], $commandId, $functionCode, $actionId, $parameterName);
            }
            if (isset($meta['ui_components']) && is_array($meta['ui_components'])) {
                foreach ($meta['ui_components'] as $componentIndex => $component) {
                    if (!is_array($component)) {
                        continue;
                    }
                    $componentCommand = self::firstNonEmpty(
                        isset($component['command_id']) ? $component['command_id'] : null,
                        $commandId
                    );
                    $componentFunction = self::firstNonEmpty(
                        isset($component['function_code']) ? $component['function_code'] : null,
                        $functionCode
                    );
                    $componentAction = self::firstNonEmpty(
                        isset($component['action_id']) ? $component['action_id'] : null,
                        $actionId
                    );
                    $componentName = trim((string) self::firstNonEmpty(
                        isset($component['parameter_name']) ? $component['parameter_name'] : null,
                        $parameterName
                    ));
                    if (isset($component['frame']) && is_array($component['frame'])) {
                        $component['frame'] = self::repairFrameDocument($component['frame'], $componentCommand, $componentFunction, $componentAction, $componentName);
                    }
                    foreach (['items', 'item_values', 'sno_asperui'] as $listKey) {
                        if (array_key_exists($listKey, $component)) {
                            $component[$listKey] = self::choiceListOrNull($component[$listKey]);
                        }
                    }
                    $meta['ui_components'][$componentIndex] = $component;
                }
            }
            $control['protocolMeta'] = $meta;
            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    private static function repairFrameDocument(array $frame, $commandId, $functionCode, $actionId, $parameterName)
    {
        foreach (['commands', 'response', 'error'] as $group) {
            if (!isset($frame[$group]) || !is_array($frame[$group])) {
                continue;
            }
            foreach ($frame[$group] as $index => $field) {
                if (!is_array($field)) {
                    continue;
                }
                $name = self::frameFieldName($field);
                $fill = null;
                if ($name === 'commandid') {
                    $fill = $commandId;
                } elseif ($name === 'functioncode') {
                    $fill = $functionCode;
                } elseif ($name === 'actionid') {
                    $fill = $actionId;
                }
                if ($fill !== null && self::protocolSlotNeedsFill(isset($field['value']) ? $field['value'] : null, $fill)) {
                    $field['value'] = $fill;
                    if (!isset($field['asperui_value']) || self::protocolSlotNeedsFill($field['asperui_value'], $fill)) {
                        $field['asperui_value'] = $fill;
                    }
                }
                if (in_array($group, ['response', 'error'], true)) {
                    $label = self::telemetryLabel($field, $parameterName);
                    if ($label !== '') {
                        $field['default_name'] = $label;
                        $description = trim((string) (isset($field['Field_description']) ? $field['Field_description'] : (isset($field['field_description']) ? $field['field_description'] : '')));
                        if ($description === '' || self::telemetryNameIsGeneric($description)) {
                            $field['Field_description'] = $label;
                            $field['field_description'] = $label;
                        }
                    }
                }
                foreach (['asperui', 'asperui_values', 'sno_asperui'] as $listKey) {
                    if (array_key_exists($listKey, $field)) {
                        $field[$listKey] = self::choiceListOrNull($field[$listKey]);
                    }
                }
                $frame[$group][$index] = $field;
            }
        }

        return $frame;
    }

    private static function telemetryNameIsGeneric($name)
    {
        $name = strtolower(trim((string) $name));

        return $name === '' || in_array($name, ['data', 'none', 'field'], true) || preg_match('/^field_\d+$/', $name) === 1;
    }

    private static function telemetryLabel(array $field, $parameterName)
    {
        $current = trim((string) (isset($field['default_name']) ? $field['default_name'] : ''));
        $description = trim((string) (isset($field['Field_description']) ? $field['Field_description'] : (isset($field['field_description']) ? $field['field_description'] : '')));
        if (!self::telemetryNameIsGeneric($current) && $current !== '' && !self::textIsProtocolBracketDump($current)) {
            return $current;
        }
        if (!self::telemetryNameIsGeneric($description) && $description !== '' && !self::textIsProtocolBracketDump($description)) {
            return $description;
        }
        $raw = isset($field['asperui_value']) ? trim((string) $field['asperui_value']) : '';
        if ($raw !== '' && !self::telemetryNameIsGeneric($raw) && !self::textIsProtocolBracketDump($raw) && !preg_match('/^[0-9A-Fa-f]+$/', $raw)) {
            return $raw;
        }
        $parameterName = trim((string) $parameterName);
        if ($parameterName !== '' && strcasecmp($parameterName, 'frame') !== 0 && !self::textIsProtocolBracketDump($parameterName)) {
            return $parameterName;
        }

        return $current !== '' && !self::telemetryNameIsGeneric($current) && !self::textIsProtocolBracketDump($current) ? $current : 'Value';
    }

    private static function choiceListOrNull($value)
    {
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            $text = trim((string) $value);
            if ($text === '' || $text === '[]' || $text === '{}') {
                return null;
            }
            $decoded = json_decode($text, true);
            $value = is_array($decoded) ? $decoded : [$text];
        }
        $clean = [];
        foreach ($value as $item) {
            if (is_array($item) || is_object($item)) {
                continue;
            }
            $text = trim((string) $item);
            if ($text === '' || $text === '[]' || $text === '{}' || strcasecmp($text, 'null') === 0) {
                continue;
            }
            $clean[] = $text;
        }

        return $clean === [] ? null : array_values($clean);
    }

    private static function stripWriteEvents(&$node)
    {
        if (!is_array($node)) {
            return;
        }
        if (isset($node['events']) && is_array($node['events'])) {
            $keys = array_keys($node['events']);
            $isList = $keys === [] || $keys === range(0, count($keys) - 1);
            if ($isList) {
                $node['events'] = array_values(array_filter($node['events'], static function ($event) {
                    return !self::eventIsWrite($event);
                }));
            } else {
                foreach ($node['events'] as $eventName => $event) {
                    $probe = is_array($event) ? $event : [];
                    if (!isset($probe['event']) && !isset($probe['name'])) {
                        $probe['event'] = (string) $eventName;
                    }
                    if (self::eventIsWrite($probe)) {
                        unset($node['events'][$eventName]);
                    }
                }
            }
        }
        foreach ($node as $key => &$child) {
            if ($key === 'events' || !is_array($child)) {
                continue;
            }
            self::stripWriteEvents($child);
        }
        unset($child);
    }

    private static function eventIsWrite($event)
    {
        if (!is_array($event)) {
            return false;
        }
        $name = strtolower(trim((string) (isset($event['event']) ? $event['event'] : (isset($event['name']) ? $event['name'] : ''))));
        if (in_array($name, ['onchange', 'onchanged', 'onchangeend', 'ontap', 'onvaluecommitted', 'onsubmitted', 'onselected', 'onsendtap', 'oncommand'], true)) {
            return true;
        }
        $action = isset($event['action']) ? $event['action'] : null;
        if (is_string($action) && stripos($action, 'update_') === 0) {
            return true;
        }
        if (!is_array($action)) {
            return false;
        }
        $type = strtolower(trim((string) (isset($action['type']) ? $action['type'] : '')));
        if (in_array($type, ['send', 'update', 'write'], true)) {
            return true;
        }
        $id = trim((string) (isset($action['id']) ? $action['id'] : (isset($action['actionId']) ? $action['actionId'] : '')));

        return stripos($id, 'update_') === 0;
    }

    private static function rememberControlAction(array &$schema, array $control)
    {
        $actionId = trim((string) (isset($control['action']) ? $control['action'] : ''));
        if ($actionId === '') {
            return;
        }
        if (!isset($schema['actions'])) {
            $schema['actions'] = [];
        } elseif (is_object($schema['actions'])) {
            $schema['actions'] = (array) $schema['actions'];
        }
        if (!is_array($schema['actions'])) {
            $schema['actions'] = [];
        }
        if (isset($schema['actions'][$actionId]) && is_array($schema['actions'][$actionId])) {
            return;
        }
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta']) ? $control['protocolMeta'] : [];
        $binding = trim((string) (isset($control['binding']) ? $control['binding'] : ''));
        $schema['actions'][$actionId] = [
            'type' => 'dataOperation',
            'dataSource' => 'deviceCommands',
            'operation' => 'write',
            'input' => [
                'modelNo' => '$app.modelNo',
                'parameter' => trim((string) self::firstNonEmpty(
                    isset($control['parameterName']) ? $control['parameterName'] : null,
                    isset($control['parameter']) ? $control['parameter'] : null,
                    isset($control['label']) ? $control['label'] : null
                )),
                'value' => $binding !== '' ? ('$' . $binding) : '',
                'commandId' => isset($meta['command_id']) ? $meta['command_id'] : null,
                'functionCode' => isset($meta['function_code']) ? $meta['function_code'] : null,
            ],
        ];
    }

    // ---------------------------------------------------------------- node ids

    /**
     * Fetches node_details for the opened node_id and maps onto the schema.
     *
     * V2: prefer /v8/v2_test_nodes.php (same envelope as legacy test_nodes).
     * Legacy: keep /v8/newapi.php?action=test_nodes unchanged.
     */
    private static function applyTestNodesNodeDetails(array $schema, $nodeId, $modelNo = '')
    {
        $nodeId = trim((string) $nodeId);
        $modelNo = trim((string) $modelNo);
        if ($nodeId === '') {
            return $schema;
        }

        $existingConfig = isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
            ? $schema['deviceConfig']
            : [];
        $preferStoredV2 = self::schemaPrefersV2NodeDetails($schema);
        if ($modelNo === '') {
            $modelNo = trim((string) (
                $schema['meta']['app']['modelNo']
                ?? $schema['node_id']['modelNo']
                ?? $schema['node_id']['model_id']
                ?? ''
            ));
        }

        $usedV2Api = false;
        $payload = [];
        if ($preferStoredV2 || $modelNo !== '') {
            $payload = self::fetchV2TestNodesPayload($nodeId, $modelNo);
            if (self::isValidTestNodesPayload($payload, $nodeId)) {
                $usedV2Api = true;
            } else {
                $payload = [];
            }
        }

        // Legacy path only when V2 API did not supply node_details.
        if (!$usedV2Api) {
            $payload = self::fetchTestNodesPayload($nodeId);
        }

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

        $remoteStatus = isset($ordered[0]['status']) && is_array($ordered[0]['status'])
            ? $ordered[0]['status']
            : null;
        $config = isset($ordered[0]['config']) && is_array($ordered[0]['config'])
            ? $ordered[0]['config']
            : [];

        if ($usedV2Api) {
            // Live V2 test node is the page/frame/value authority. Do not let a
            // richer stale stored snapshot overwrite API value changes on shared
            // page keys. Pages removed by the API are dropped (not merged back).
            $schema['node_details'] = $ordered;
            $schema['v2_node_details'] = $ordered;
            if (!empty($config)) {
                $schema['deviceConfig'] = $config;
                if (isset($schema['node_details'][0]) && is_array($schema['node_details'][0])) {
                    $schema['node_details'][0]['config'] = $schema['deviceConfig'];
                }
                if (isset($schema['v2_node_details'][0]) && is_array($schema['v2_node_details'][0])) {
                    $schema['v2_node_details'][0]['config'] = $schema['deviceConfig'];
                }
            }
            if (!isset($schema['meta']) || !is_array($schema['meta'])) {
                $schema['meta'] = [];
            }
            $schema['meta']['v2DisplayHandlers'] = true;
            if (!isset($schema['meta']['app']) || !is_array($schema['meta']['app'])) {
                $schema['meta']['app'] = [];
            }
            $schema['meta']['app']['v2DisplayHandlers'] = true;
        } elseif ($preferStoredV2 && !empty($existingConfig)) {
            // Stored V2 pages/frames win; legacy test_nodes only fills missing pages + status.
            $mergedConfig = self::mergeDeviceConfigPages($existingConfig, $config);
            $schema['deviceConfig'] = $mergedConfig;
            $storedDetails = isset($schema['v2_node_details']) && is_array($schema['v2_node_details'])
                ? $schema['v2_node_details']
                : (isset($schema['node_details']) && is_array($schema['node_details']) ? $schema['node_details'] : []);
            $storedOrdered = self::orderNodeDetailsForNode($storedDetails, $nodeId);
            if (!$storedOrdered) {
                $storedOrdered = [[
                    'id' => $nodeId,
                    'config' => $mergedConfig,
                    'role' => 'primary',
                    'status' => [
                        'connectivity' => [
                            'connected' => false,
                            'timestamp' => (int) round(microtime(true) * 1000),
                        ],
                    ],
                ]];
            }
            $storedOrdered[0]['config'] = $mergedConfig;
            if ($remoteStatus !== null) {
                $storedOrdered[0]['status'] = $remoteStatus;
            }
            if ($nodeId !== '') {
                $storedOrdered[0]['id'] = $nodeId;
            }
            $schema['node_details'] = $storedOrdered;
            $schema['v2_node_details'] = $storedOrdered;
        } else {
            $schema['node_details'] = $ordered;
            if (!empty($config)) {
                // Preserve any pages already published in the stored schema; test_nodes
                // often only ships Page 1 and would otherwise wipe secondary pages.
                $schema['deviceConfig'] = self::mergeDeviceConfigPages($config, $existingConfig);
                if (isset($schema['node_details'][0]) && is_array($schema['node_details'][0])) {
                    $schema['node_details'][0]['config'] = $schema['deviceConfig'];
                }
            }
        }

        $schema['nodes'] = $nodeId;
        if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
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

    private static function isValidTestNodesPayload($payload, $nodeId): bool
    {
        if (!is_array($payload) || empty($payload['node_details']) || !is_array($payload['node_details'])) {
            return false;
        }
        return self::orderNodeDetailsForNode($payload['node_details'], $nodeId) !== [];
    }

    /**
     * Load V2 node_details from /v8/v2_test_nodes.php (or in-process builder).
     * Does not use api.php.
     */
    private static function fetchV2TestNodesPayload($nodeId, $modelNo = '')
    {
        static $cache = [];
        $nodeId = trim((string) $nodeId);
        $modelNo = trim((string) $modelNo);
        $cacheKey = strtolower($nodeId) . '|' . strtolower($modelNo);
        if ($nodeId === '' && $modelNo === '') {
            return [];
        }
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        // Prefer in-process builder when CI model is already available (api_schema path).
        // Autoload: api_schema boots CI before normalize. Skipping autoload forced a
        // loopback HTTP call that often timed out and left the stored node in place.
        if (!class_exists(\App\Models\View_page_model::class, true)) {
            $boot = dirname(__DIR__) . '/Libraries/AppHomeBootstrap.php';
            if (is_file($boot)) {
                try {
                    require_once $boot;
                    if (class_exists(\App\Libraries\AppHomeBootstrap::class, false)) {
                        \App\Libraries\AppHomeBootstrap::bootIfNeeded();
                    }
                } catch (\Throwable $exception) {
                    // HTTP fallback below.
                }
            }
        }
        if (class_exists(\App\Models\View_page_model::class, false)) {
            try {
                $model = new \App\Models\View_page_model();
                $payload = $model->buildV2TestNodesPayload($nodeId, $modelNo);
                if (self::isValidTestNodesPayload($payload, $nodeId !== '' ? $nodeId : (string) ($payload['nodes'] ?? ''))) {
                    $cache[$cacheKey] = $payload;
                    return $payload;
                }
                // If node id was empty but payload has nodes, accept it.
                if (is_array($payload) && !empty($payload['node_details']) && is_array($payload['node_details'])) {
                    $cache[$cacheKey] = $payload;
                    return $payload;
                }
            } catch (\Throwable $exception) {
                // fall through to HTTP
            }
        }

        $encodedNode = rawurlencode($nodeId);
        $encodedModel = rawurlencode($modelNo);
        $query = 'node_id=' . $encodedNode;
        if ($modelNo !== '') {
            $query .= '&model_no=' . $encodedModel;
        }

        $urls = [
            'https://efactor.tech/v8/v2_test_nodes.php?' . $query,
        ];
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        if ($host !== '') {
            $urls[] = $scheme . '://' . $host . '/v8/v2_test_nodes.php?' . $query;
        }
        $urls[] = 'http://localhost/v8/v2_test_nodes.php?' . $query;

        $seen = [];
        foreach ($urls as $url) {
            if (isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            $payload = self::decodeTestNodesPayload(self::fetchUrl($url));
            if (self::isValidTestNodesPayload($payload, $nodeId) || (!empty($payload['node_details']) && is_array($payload['node_details']))) {
                $cache[$cacheKey] = $payload;
                return $payload;
            }
        }

        $cache[$cacheKey] = [];
        return [];
    }

    /**
     * Published schema carries one node list: node_details.
     * V2 stays on meta.v2DisplayHandlers. v2_node_details was the same payload.
     */
    private static function publishSingleNodeDetails(array $schema)
    {
        $hasNode = isset($schema['node_details']) && is_array($schema['node_details']) && $schema['node_details'] !== [];
        $hasV2 = isset($schema['v2_node_details']) && is_array($schema['v2_node_details']) && $schema['v2_node_details'] !== [];
        if ($hasV2 && !$hasNode) {
            $schema['node_details'] = $schema['v2_node_details'];
            $hasNode = true;
        }
        if ($hasV2 || !empty($schema['meta']['v2DisplayHandlers']) || !empty($schema['meta']['app']['v2DisplayHandlers'])) {
            if (!isset($schema['meta']) || !is_array($schema['meta'])) {
                $schema['meta'] = [];
            }
            $schema['meta']['v2DisplayHandlers'] = true;
            if (!isset($schema['meta']['app']) || !is_array($schema['meta']['app'])) {
                $schema['meta']['app'] = [];
            }
            $schema['meta']['app']['v2DisplayHandlers'] = true;
        }
        unset($schema['v2_node_details']);

        return $schema;
    }

    /**
     * Detect schemas that carry V2 handler-built node details.
     */
    private static function schemaPrefersV2NodeDetails(array $schema)
    {
        if (isset($schema['v2_node_details']) && is_array($schema['v2_node_details']) && $schema['v2_node_details'] !== []) {
            return true;
        }
        if (!empty($schema['meta']['v2DisplayHandlers'])) {
            return true;
        }
        if (!empty($schema['meta']['app']['v2DisplayHandlers'])) {
            return true;
        }

        return false;
    }

    /**
     * Load V2ValueHandlers and enrich deviceConfig / node_details display values.
     */
    private static function applyV2DisplayHandlers(array $schema)
    {
        if (!self::loadV2ValueHandlers()) {
            return $schema;
        }

        try {
            if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
                $schema['deviceConfig'] = V2ValueHandlers::enrichDeviceConfig($schema['deviceConfig']);
            }
            if (isset($schema['node_details']) && is_array($schema['node_details'])) {
                $schema['node_details'] = V2ValueHandlers::enrichNodeDetails($schema['node_details']);
            }
            if (isset($schema['v2_node_details']) && is_array($schema['v2_node_details'])) {
                $schema['v2_node_details'] = V2ValueHandlers::enrichNodeDetails($schema['v2_node_details']);
            }
            if (isset($schema['controls']) && is_array($schema['controls'])) {
                foreach ($schema['controls'] as $index => $control) {
                    if (!is_array($control) || !isset($control['deviceFrame']) || !is_array($control['deviceFrame'])) {
                        continue;
                    }
                    $schema['controls'][$index]['deviceFrame'] = V2ValueHandlers::enrichFrame($control['deviceFrame']);
                }
            }
            if (isset($schema['node_details'][0]['config']) && is_array($schema['node_details'][0]['config'])
                && isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
            ) {
                $schema['node_details'][0]['config'] = $schema['deviceConfig'];
            }
            if (isset($schema['v2_node_details'][0]['config']) && is_array($schema['v2_node_details'][0])
                && isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
            ) {
                $schema['v2_node_details'][0]['config'] = $schema['deviceConfig'];
            }
            if (isset($schema['state']['device']) && is_array($schema['state']['device'])
                && isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
            ) {
                $schema['state']['device']['config'] = $schema['deviceConfig'];
            }
            if (isset($schema['deviceState']) && is_array($schema['deviceState'])
                && isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])
            ) {
                $schema['deviceState']['config'] = $schema['deviceConfig'];
            }
        } catch (\Throwable $exception) {
            // Keep schema intact if handlers fail.
        }

        return $schema;
    }

    private static function loadV2ValueHandlers()
    {
        if (class_exists(V2ValueHandlers::class, false)) {
            return true;
        }

        $candidates = [
            __DIR__ . '/V2ValueHandlers.php',
            dirname(__DIR__) . '/services/V2ValueHandlers.php',
            dirname(__DIR__) . '/Services/V2ValueHandlers.php',
        ];
        foreach ($candidates as $file) {
            if (is_file($file)) {
                require_once $file;
                return class_exists(V2ValueHandlers::class, false);
            }
        }

        return false;
    }

    /**
     * Union live + existing deviceConfig pages without renaming keys.
     * Live frames win for shared page names; pages only in existing are kept.
     */
    /**
     * Keep stored frame detail only for pages the live V2 node still publishes.
     * Pages removed by a new API are not merged back into deviceConfig.
     */
    private static function configLimitedToLivePages(array $existingConfig, array $liveConfig): array
    {
        $livePages = isset($liveConfig['devices'][0]['pages']) && is_array($liveConfig['devices'][0]['pages'])
            ? $liveConfig['devices'][0]['pages']
            : [];
        if ($existingConfig === [] || $livePages === []) {
            return [];
        }
        if (!isset($existingConfig['devices'][0]) || !is_array($existingConfig['devices'][0])) {
            return [];
        }
        $existingPages = isset($existingConfig['devices'][0]['pages']) && is_array($existingConfig['devices'][0]['pages'])
            ? $existingConfig['devices'][0]['pages']
            : [];
        $kept = [];
        foreach ($livePages as $pageName => $frames) {
            $pageKey = (string) $pageName;
            if (isset($existingPages[$pageKey]) && is_array($existingPages[$pageKey])) {
                $kept[$pageKey] = $existingPages[$pageKey];
            }
        }
        $existingConfig['devices'][0]['pages'] = $kept;

        return $existingConfig;
    }

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
            // Prefer frames with real protocol hex over zero/empty placeholders.
            $score += self::frameWireScore($frame);
        }

        return $score;
    }

    /**
     * Score how operable a frame's command wire values look (non-zero hex wins).
     */
    private static function frameWireScore(array $frame)
    {
        $score = 0;
        $commands = isset($frame['commands']) && is_array($frame['commands']) ? $frame['commands'] : [];
        foreach ($commands as $field) {
            if (!is_array($field)) {
                continue;
            }
            $level = strtolower(trim((string) ($field['field_level'] ?? ($field['fieldLevel'] ?? 'bytewise'))));
            $parentId = (int) ($field['parent_field_id'] ?? ($field['parentFieldId'] ?? 0));
            if ($level === 'bitwise' && $parentId > 0) {
                continue;
            }
            $hex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', (string) ($field['value'] ?? '')));
            if ($hex === '') {
                continue;
            }
            if (preg_match('/^0+$/', $hex)) {
                $score += 1;
                continue;
            }
            $score += 10 + (int) (strlen($hex) / 2);
        }

        return $score;
    }

    /**
     * Copy richer deviceConfig / v2_node_details page frames onto controls[].deviceFrame.
     * Preserves schema keys and only replaces when the config frame has better wire values.
     * Safe for app: same deviceFrame shape, no new response keys.
     */
    private static function syncControlDeviceFramesFromConfigPages(array $schema)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls']) || $schema['controls'] === []) {
            return $schema;
        }

        $pageSets = [];
        if (isset($schema['deviceConfig']['devices'][0]['pages']) && is_array($schema['deviceConfig']['devices'][0]['pages'])) {
            $pageSets[] = $schema['deviceConfig']['devices'][0]['pages'];
        }
        foreach (['v2_node_details', 'node_details'] as $detailKey) {
            if (!isset($schema[$detailKey][0]['config']['devices'][0]['pages'])
                || !is_array($schema[$detailKey][0]['config']['devices'][0]['pages'])
            ) {
                continue;
            }
            $pageSets[] = $schema[$detailKey][0]['config']['devices'][0]['pages'];
        }

        if ($pageSets === []) {
            return $schema;
        }

        $bestByName = [];
        $bestBySlug = [];
        foreach ($pageSets as $pages) {
            foreach ($pages as $entries) {
                if (!is_array($entries)) {
                    continue;
                }
                foreach ($entries as $entry) {
                    if (!is_array($entry)) {
                        continue;
                    }
                    $frame = isset($entry['frame']) && is_array($entry['frame']) ? $entry['frame'] : null;
                    if ($frame === null) {
                        continue;
                    }
                    $name = strtolower(trim((string) ($frame['name'] ?? '')));
                    if ($name === '') {
                        continue;
                    }
                    $slug = preg_replace('/[^a-z0-9]+/', '', $name);
                    $score = self::frameWireScore($frame);
                    if ($score <= 0) {
                        continue;
                    }
                    if (!isset($bestByName[$name]) || $score > self::frameWireScore($bestByName[$name])) {
                        $bestByName[$name] = $frame;
                    }
                    if ($slug !== '' && (!isset($bestBySlug[$slug]) || $score > self::frameWireScore($bestBySlug[$slug]))) {
                        $bestBySlug[$slug] = $frame;
                    }
                }
            }
        }

        if ($bestByName === [] && $bestBySlug === []) {
            return $schema;
        }

        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $candidates = [
                strtolower(trim((string) ($control['deviceFrame']['name'] ?? ''))),
                strtolower(trim((string) ($control['parameterName'] ?? ''))),
                strtolower(trim((string) ($control['parameter'] ?? ''))),
                strtolower(trim((string) ($control['label'] ?? ''))),
                strtolower(trim((string) ($control['name'] ?? ''))),
            ];
            $best = null;
            $bestScore = -1;
            foreach ($candidates as $cand) {
                if ($cand === '') {
                    continue;
                }
                if (isset($bestByName[$cand])) {
                    $score = self::frameWireScore($bestByName[$cand]);
                    if ($score > $bestScore) {
                        $best = $bestByName[$cand];
                        $bestScore = $score;
                    }
                }
                $slug = preg_replace('/[^a-z0-9]+/', '', $cand);
                if ($slug !== '' && isset($bestBySlug[$slug])) {
                    $score = self::frameWireScore($bestBySlug[$slug]);
                    if ($score > $bestScore) {
                        $best = $bestBySlug[$slug];
                        $bestScore = $score;
                    }
                }
            }
            if ($best === null || $bestScore <= 0) {
                continue;
            }

            $current = isset($control['deviceFrame']) && is_array($control['deviceFrame'])
                ? $control['deviceFrame']
                : [];
            $currentScore = self::frameWireScore($current);
            $v2Authority = self::schemaPrefersV2NodeDetails($schema);
            // A longer stored control wire is not richer when it repeats a header
            // byte (Command Get Status Data = E7). The test-node page frame wins.
            if (!$v2Authority && $bestScore <= $currentScore) {
                continue;
            }

            // Keep control-facing name if present; copy operable commands/response/error.
            $merged = $best;
            if (!empty($current['name'])) {
                $merged['name'] = $current['name'];
            } elseif (!empty($control['parameterName'])) {
                $merged['name'] = $control['parameterName'];
            }
            $control['deviceFrame'] = $merged;
            if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                $control['protocolMeta'] = [];
            }
            $control['protocolMeta']['frame'] = $merged;
            if (isset($merged['commands']) && is_array($merged['commands'])) {
                $control['commands'] = $merged['commands'];
            }
            // Refresh protocol ids from upgraded frame when meta still zero/empty.
            foreach ($merged['commands'] ?? [] as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $slot = self::frameFieldName($field);
                $hex = self::normalizeProtocolHex($field['value'] ?? null, 0);
                if ($hex === '' || self::isZeroProtocolId($hex)) {
                    continue;
                }
                if ($slot === 'commandid'
                    && (self::firstNonEmpty($control['protocolMeta']['command_id'] ?? null) === null
                        || self::isZeroProtocolId($control['protocolMeta']['command_id'] ?? null))
                ) {
                    $control['protocolMeta']['command_id'] = $hex;
                }
                if ($slot === 'functioncode'
                    && (self::firstNonEmpty($control['protocolMeta']['function_code'] ?? null) === null
                        || self::isZeroProtocolId($control['protocolMeta']['function_code'] ?? null))
                ) {
                    $control['protocolMeta']['function_code'] = $hex;
                }
            }
            $schema['controls'][$index] = $control;
        }

        return $schema;
    }

    /**
     * Publish every schema page into deviceConfig.devices[0].pages using control
     * deviceFrame payloads. Does not invent protocol bytes - only copies frames
     * already present on controls.
     */
    /**
     * Publish one screen per linked page title.
     * A parameter kept on an earlier page is not repeated on a later page.
     * Device pages that were never linked are removed from the node the app reads.
     */
    public static function alignPublishedUiPages(array $schema)
    {
        $controls = isset($schema['controls']) && is_array($schema['controls']) ? $schema['controls'] : [];
        if ($controls === []) {
            return $schema;
        }

        $orderedIndexes = array_keys($controls);
        usort($orderedIndexes, static function ($a, $b) use ($controls) {
            $left = is_array($controls[$a]) ? $controls[$a] : [];
            $right = is_array($controls[$b]) ? $controls[$b] : [];
            $pageCompare = ((int) ($left['pageNo'] ?? 0)) <=> ((int) ($right['pageNo'] ?? 0));
            if ($pageCompare !== 0) {
                return $pageCompare;
            }

            return ((int) ($left['sequence'] ?? 0)) <=> ((int) ($right['sequence'] ?? 0));
        });

        $titleCounts = [];
        $titleDisplay = [];
        $normalized = [];
        foreach ($orderedIndexes as $index) {
            $control = $controls[$index];
            if (!is_array($control)) {
                continue;
            }
            $pageNo = trim((string) ($control['pageNo'] ?? '1'));
            if ($pageNo === '') {
                $pageNo = '1';
            }
            $title = trim((string) ($control['pageName'] ?? ''));
            if ($title === '') {
                $title = 'Page ' . $pageNo;
            }
            $titleKey = strtolower($title);
            if (!isset($titleCounts[$titleKey])) {
                $titleCounts[$titleKey] = [];
            }
            if (!isset($titleCounts[$titleKey][$pageNo])) {
                $titleCounts[$titleKey][$pageNo] = 0;
            }
            $titleCounts[$titleKey][$pageNo]++;
            if (!isset($titleDisplay[$titleKey]) || (int) $pageNo < (int) array_key_first($titleCounts[$titleKey])) {
                $titleDisplay[$titleKey] = $title;
            }
            $control['pageNo'] = $pageNo;
            $control['pageName'] = $title;
            $normalized[] = $control;
        }

        $titleOwner = [];
        foreach ($titleCounts as $titleKey => $counts) {
            $bestNo = '';
            $bestCount = -1;
            foreach ($counts as $pageNo => $count) {
                if ($count > $bestCount || ($count === $bestCount && ($bestNo === '' || (int) $pageNo < (int) $bestNo))) {
                    $bestNo = (string) $pageNo;
                    $bestCount = $count;
                }
            }
            $titleOwner[$titleKey] = $bestNo;
        }

        $parametersOnWinner = [];
        foreach ($normalized as $control) {
            $titleKey = strtolower(trim((string) ($control['pageName'] ?? '')));
            $pageNo = (string) ($control['pageNo'] ?? '');
            if (($titleOwner[$titleKey] ?? '') !== $pageNo) {
                continue;
            }
            $parameter = strtolower(trim((string) ($control['parameterName'] ?? ($control['parameter'] ?? ''))));
            if ($parameter !== '') {
                $parametersOnWinner[$titleKey][$parameter] = true;
            }
        }

        $keptControls = [];
        foreach ($normalized as $control) {
            $titleKey = strtolower(trim((string) ($control['pageName'] ?? '')));
            $pageNo = (string) ($control['pageNo'] ?? '');
            $winner = (string) ($titleOwner[$titleKey] ?? $pageNo);
            $parameter = strtolower(trim((string) ($control['parameterName'] ?? ($control['parameter'] ?? ''))));
            if ($pageNo !== $winner) {
                if ($parameter !== '' && !empty($parametersOnWinner[$titleKey][$parameter])) {
                    continue;
                }
                $control['pageNo'] = $winner;
            }
            $keptControls[] = $control;
        }

        $idsByPage = [];
        $pageTitle = [];
        foreach ($keptControls as $index => $control) {
            $pageNo = trim((string) ($control['pageNo'] ?? '1'));
            if ($pageNo === '') {
                $pageNo = '1';
            }
            $title = trim((string) ($control['pageName'] ?? ''));
            if ($title === '') {
                $title = 'Page ' . $pageNo;
            }
            $titleKey = strtolower($title);
            $keptNo = (string) ($titleOwner[$titleKey] ?? $pageNo);
            $keptTitle = (string) ($titleDisplay[$titleKey] ?? $title);
            $control['pageNo'] = $keptNo;
            $control['pageName'] = $keptTitle;
            $control['pageId'] = 'page_' . $keptNo;
            $keptControls[$index] = $control;
            $id = trim((string) ($control['id'] ?? ''));
            if ($id === '') {
                continue;
            }
            if (!isset($idsByPage[$keptNo])) {
                $idsByPage[$keptNo] = [];
                $pageTitle[$keptNo] = $keptTitle;
            }
            $idsByPage[$keptNo][] = $id;
        }
        $schema['controls'] = array_values($keptControls);

        uksort($idsByPage, static function ($a, $b) {
            return ((int) $a) <=> ((int) $b);
        });

        $previousPages = [];
        foreach ($schema['pages'] ?? [] as $page) {
            if (!is_array($page)) {
                continue;
            }
            $pageNo = trim((string) ($page['pageNo'] ?? ''));
            if ($pageNo !== '') {
                $previousPages[$pageNo] = $page;
            }
        }

        $newPages = [];
        $pageIds = [];
        foreach ($idsByPage as $pageNo => $ids) {
            $page = isset($previousPages[$pageNo]) && is_array($previousPages[$pageNo])
                ? $previousPages[$pageNo]
                : [];
            $page['id'] = trim((string) ($page['id'] ?? '')) !== '' ? $page['id'] : ('page_' . $pageNo);
            $page['pageNo'] = (string) $pageNo;
            $page['title'] = $pageTitle[$pageNo] ?? ('Page ' . $pageNo);
            $page['controlIds'] = array_values($ids);
            $page['controlCount'] = count($page['controlIds']);
            $page['index'] = count($newPages);
            $newPages[] = $page;
            $pageIds[] = $page['id'];
        }
        $schema['pages'] = $newPages;

        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
            $schema['meta'] = [];
        }
        $schema['meta']['pageCount'] = count($newPages);
        $schema['meta']['totalControlCount'] = count($schema['controls']);
        if (isset($schema['meta']['canonicalValidation']) && is_array($schema['meta']['canonicalValidation'])) {
            $schema['meta']['canonicalValidation']['pageCount'] = count($newPages);
            $schema['meta']['canonicalValidation']['totalControlCount'] = count($schema['controls']);
        }

        if (!isset($schema['navigation']) || !is_array($schema['navigation'])) {
            $schema['navigation'] = [];
        }
        $existingNavPages = isset($schema['navigation']['pages']) && is_array($schema['navigation']['pages'])
            ? $schema['navigation']['pages']
            : [];
        $navFirst = $existingNavPages !== [] ? reset($existingNavPages) : null;
        $schema['navigation']['pages'] = is_string($navFirst) ? $pageIds : $newPages;
        $schema['navigation']['pageIds'] = $pageIds;
        $initial = trim((string) ($schema['navigation']['initialPageId'] ?? ($schema['navigation']['initial'] ?? '')));
        if ($initial === '' || !in_array($initial, $pageIds, true)) {
            $schema['navigation']['initial'] = $pageIds[0] ?? '';
            $schema['navigation']['initialPageId'] = $pageIds[0] ?? '';
            $schema['navigation']['activePageId'] = $pageIds[0] ?? '';
        }
        if (isset($schema['navigation']['graph']['routes']) && is_array($schema['navigation']['graph']['routes'])) {
            $allowedIds = array_fill_keys($pageIds, true);
            $schema['navigation']['graph']['routes'] = array_values(array_filter(
                $schema['navigation']['graph']['routes'],
                static function ($route) use ($allowedIds) {
                    return is_array($route) && isset($allowedIds[(string) ($route['id'] ?? '')]);
                }
            ));
            if (isset($schema['navigation']['deepLinks']) && is_array($schema['navigation']['deepLinks'])) {
                $schema['navigation']['deepLinks'] = array_values(array_filter(
                    $schema['navigation']['deepLinks'],
                    static function ($link) use ($allowedIds) {
                        return is_array($link) && isset($allowedIds[(string) ($link['routeId'] ?? '')]);
                    }
                ));
            }
        }
        if (isset($schema['dashboard']) && is_array($schema['dashboard'])) {
            $schema['dashboard']['pages'] = array_map(static function ($page) {
                return [
                    'id' => $page['id'],
                    'pageNo' => $page['pageNo'],
                    'title' => $page['title'],
                    'controlIds' => $page['controlIds'],
                    'controlCount' => $page['controlCount'],
                    'widgetTreeRef' => 'page_slide_' . $page['index'],
                ];
            }, $newPages);
        }
        if (isset($schema['state']) && is_array($schema['state'])) {
            if (!empty($newPages) && !in_array($schema['state']['activePageId'] ?? '', $pageIds, true)) {
                $schema['state']['activePageId'] = $newPages[0]['id'];
                $schema['state']['activePageNo'] = $newPages[0]['pageNo'];
                $schema['state']['activePageName'] = $newPages[0]['title'];
                $schema['state']['activePageIndex'] = 0;
            }
            if (isset($schema['state']['pages']) && is_array($schema['state']['pages'])) {
                $schema['state']['pages'] = $newPages;
            }
        }

        $allowedTitles = [];
        foreach ($pageTitle as $title) {
            $allowedTitles[strtolower(trim((string) $title))] = $title;
        }
        $schema = self::limitSchemaDevicePages($schema, $allowedTitles, $idsByPage);
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            $seen = ['slide' => [], 'list' => []];
            $schema['ui']['widgetTree'] = self::prunePublishedPageNodes(
                $schema['ui']['widgetTree'],
                $pageTitle,
                $idsByPage,
                $seen,
                false
            );
            // A mapping sync can leave schema_controls_N as a sibling of
            // phone_shell_column. That sibling overflows the matchParent column
            // and pushes the pager off screen. Move the same node under the
            // existing page slider. Trees with no page_slider are left as-is.
            $schema['ui']['widgetTree'] = self::nestLooseSchemaControlLists(
                $schema['ui']['widgetTree'],
                $pageTitle
            );
        }

        return $schema;
    }

    /**
     * Place schemaControlList nodes that sit outside a page slide into
     * page_slider → page_slide_X. No-op when this tree has no page_slider.
     * The list node is moved as-is: no new control fields, bindings, or actions.
     *
     * @param array $pageTitle pageNo => title
     */
    public static function nestLooseSchemaControlLists(array $widgetTree, array $pageTitle = [])
    {
        if (!self::widgetTreeHasId($widgetTree, 'page_slider')) {
            return $widgetTree;
        }

        $loose = [];
        $widgetTree = self::detachLooseSchemaControlLists($widgetTree, false, $loose);
        if ($loose === []) {
            return $widgetTree;
        }

        return self::attachListsToPageSlider($widgetTree, $loose, $pageTitle);
    }

    private static function widgetTreeHasId(array $node, $id)
    {
        if ((string) ($node['id'] ?? '') === (string) $id) {
            return true;
        }
        if (!isset($node['children']) || !is_array($node['children'])) {
            return false;
        }
        foreach ($node['children'] as $child) {
            if (is_array($child) && self::widgetTreeHasId($child, $id)) {
                return true;
            }
        }

        return false;
    }

    private static function widgetPageNo(array $node)
    {
        if (isset($node['properties']['pageNo'])) {
            return trim((string) $node['properties']['pageNo']);
        }
        if (isset($node['pageNo'])) {
            return trim((string) $node['pageNo']);
        }

        return '';
    }

    private static function nodeIsPageSlide(array $node)
    {
        $type = strtolower(trim((string) ($node['type'] ?? '')));
        $pageNo = self::widgetPageNo($node);
        $childId = (string) ($node['id'] ?? '');

        return $type === 'column'
            && $pageNo !== ''
            && (strpos($childId, 'page_slide_') === 0 || isset($node['properties']['pageName']));
    }

    private static function detachLooseSchemaControlLists(array $node, $insideSlide, array &$loose)
    {
        if (!isset($node['children']) || !is_array($node['children'])) {
            return $node;
        }

        $kept = [];
        foreach ($node['children'] as $child) {
            if (!is_array($child)) {
                $kept[] = $child;
                continue;
            }
            $type = strtolower(trim((string) ($child['type'] ?? '')));
            $pageNo = self::widgetPageNo($child);
            $isList = $type === 'schemacontrollist';
            if ($isList && !$insideSlide && $pageNo !== '') {
                $loose[] = $child;
                continue;
            }
            $kept[] = self::detachLooseSchemaControlLists(
                $child,
                $insideSlide || self::nodeIsPageSlide($child),
                $loose
            );
        }
        $node['children'] = array_values($kept);

        return $node;
    }

    private static function attachListsToPageSlider(array $node, array $loose, array $pageTitle)
    {
        if ((string) ($node['id'] ?? '') === 'page_slider') {
            return self::insertListsIntoSlider($node, $loose, $pageTitle);
        }
        if (!isset($node['children']) || !is_array($node['children'])) {
            return $node;
        }
        foreach ($node['children'] as $index => $child) {
            if (!is_array($child)) {
                continue;
            }
            $node['children'][$index] = self::attachListsToPageSlider($child, $loose, $pageTitle);
        }

        return $node;
    }

    private static function insertListsIntoSlider(array $slider, array $loose, array $pageTitle)
    {
        $children = isset($slider['children']) && is_array($slider['children']) ? $slider['children'] : [];
        $slidesByPage = [];
        $template = null;
        $maxIndex = -1;
        foreach ($children as $index => $child) {
            if (!is_array($child) || !self::nodeIsPageSlide($child)) {
                continue;
            }
            $template = $child;
            $pageNo = self::widgetPageNo($child);
            if ($pageNo !== '') {
                $slidesByPage[$pageNo] = $index;
            }
            if (preg_match('/page_slide_(\d+)/', (string) ($child['id'] ?? ''), $match)) {
                $maxIndex = max($maxIndex, (int) $match[1]);
            }
        }

        foreach ($loose as $list) {
            if (!is_array($list)) {
                continue;
            }
            $pageNo = self::widgetPageNo($list);
            if ($pageNo === '') {
                continue;
            }
            if (isset($slidesByPage[$pageNo])) {
                $index = $slidesByPage[$pageNo];
                if (!self::slideHasControlList($children[$index])) {
                    if (!isset($children[$index]['children']) || !is_array($children[$index]['children'])) {
                        $children[$index]['children'] = [];
                    }
                    $children[$index]['children'][] = $list;
                }
                continue;
            }
            $maxIndex++;
            $children[] = self::makePageSlideFromTemplate($template, $maxIndex, $pageNo, $pageTitle, $list);
            $slidesByPage[$pageNo] = count($children) - 1;
        }

        $slider['children'] = array_values($children);

        return $slider;
    }

    private static function slideHasControlList(array $slide)
    {
        if (!isset($slide['children']) || !is_array($slide['children'])) {
            return false;
        }
        foreach ($slide['children'] as $child) {
            if (!is_array($child)) {
                continue;
            }
            $type = strtolower(trim((string) ($child['type'] ?? '')));
            if ($type === 'schemacontrollist') {
                return true;
            }
        }

        return false;
    }

    private static function makePageSlideFromTemplate($template, $index, $pageNo, array $pageTitle, array $list)
    {
        $title = isset($pageTitle[$pageNo]) ? (string) $pageTitle[$pageNo] : ('Page ' . $pageNo);
        if (is_array($template)) {
            $slide = $template;
        } else {
            $slide = [
                'type' => 'column',
                'properties' => [
                    'width' => 'matchParent',
                    'crossAxisAlignment' => 'stretch',
                    'visibility' => true,
                    'scrollable' => true,
                ],
            ];
        }
        $slide['type'] = 'column';
        $slide['id'] = 'page_slide_' . $index;
        if (!isset($slide['properties']) || !is_array($slide['properties'])) {
            $slide['properties'] = [];
        }
        $slide['properties']['pageNo'] = (string) $pageNo;
        $slide['properties']['pageId'] = 'page_' . $pageNo;
        $slide['properties']['pageName'] = $title;
        $slide['properties']['isActive'] = false;
        $slide['children'] = [$list];

        return $slide;
    }

    private static function limitSchemaDevicePages(array $schema, array $allowedTitles, array $idsByPage)
    {
        if ($allowedTitles === []) {
            return $schema;
        }
        $apply = static function ($config) use ($allowedTitles, $schema) {
            if (!is_array($config)) {
                return $config;
            }
            if (!isset($config['devices'][0]) || !is_array($config['devices'][0])) {
                $config['devices'][0] = ['pages' => []];
            }
            $pages = isset($config['devices'][0]['pages']) && is_array($config['devices'][0]['pages'])
                ? $config['devices'][0]['pages']
                : [];
            $kept = [];
            foreach ($pages as $pageName => $frames) {
                $key = strtolower(trim((string) $pageName));
                if (!isset($allowedTitles[$key]) || !is_array($frames)) {
                    continue;
                }
                $kept[$allowedTitles[$key]] = $frames;
            }
            foreach ($allowedTitles as $title) {
                if (isset($kept[$title])) {
                    continue;
                }
                $frames = [];
                foreach ($schema['controls'] as $control) {
                    if (!is_array($control) || trim((string) ($control['pageName'] ?? '')) !== $title) {
                        continue;
                    }
                    $frame = isset($control['deviceFrame']) && is_array($control['deviceFrame'])
                        ? $control['deviceFrame']
                        : null;
                    if ($frame === null) {
                        continue;
                    }
                    $frames[] = ['frame' => $frame];
                }
                $kept[$title] = $frames;
            }
            $config['devices'][0]['pages'] = $kept;
            $config['devices'][0]['total_pages'] = (string) max(1, count($kept));

            return $config;
        };

        if (isset($schema['deviceConfig'])) {
            $schema['deviceConfig'] = $apply($schema['deviceConfig']);
        }
        foreach (['node_details', 'v2_node_details'] as $detailKey) {
            if (!isset($schema[$detailKey][0]) || !is_array($schema[$detailKey][0])) {
                continue;
            }
            if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
                $schema[$detailKey][0]['config'] = $schema['deviceConfig'];
            } elseif (isset($schema[$detailKey][0]['config']) && is_array($schema[$detailKey][0]['config'])) {
                $schema[$detailKey][0]['config'] = $apply($schema[$detailKey][0]['config']);
            }
        }
        if (isset($schema['state']['device']) && is_array($schema['state']['device']) && isset($schema['deviceConfig'])) {
            $schema['state']['device']['config'] = $schema['deviceConfig'];
        }
        if (isset($schema['deviceState']) && is_array($schema['deviceState']) && isset($schema['deviceConfig'])) {
            $schema['deviceState']['config'] = $schema['deviceConfig'];
        }
        if (isset($schema['info']) && is_array($schema['info'])) {
            $schema['info']['pages'] = array_values($allowedTitles);
            $schema['info']['total_pages'] = count($allowedTitles);
        }

        return $schema;
    }

    private static function prunePublishedPageNodes($node, array $pageTitle, array $idsByPage, array &$seen, $insideSlide)
    {
        if (!is_array($node)) {
            return $node;
        }
        $type = strtolower(trim((string) ($node['type'] ?? '')));
        $pageNo = trim((string) ($node['properties']['pageNo'] ?? ''));
        if ($type === 'schemacontrollist' && $pageNo !== '' && isset($idsByPage[$pageNo])) {
            $node['properties']['controlIds'] = array_values($idsByPage[$pageNo]);
            $node['properties']['controlCount'] = count($idsByPage[$pageNo]);
            $node['properties']['pageId'] = 'page_' . $pageNo;
        }
        if ($type === 'column' && $pageNo !== '' && isset($pageTitle[$pageNo])) {
            $node['properties']['pageName'] = $pageTitle[$pageNo];
            $node['properties']['pageId'] = 'page_' . $pageNo;
        }
        if (!isset($node['children']) || !is_array($node['children'])) {
            return $node;
        }

        $children = [];
        $isButtonList = ((string) ($node['id'] ?? '')) === 'page_buttons';
        foreach ($node['children'] as $child) {
            if (!is_array($child)) {
                continue;
            }
            $childType = strtolower(trim((string) ($child['type'] ?? '')));
            $childId = (string) ($child['id'] ?? '');
            $childPage = trim((string) ($child['properties']['pageNo'] ?? ''));
            $childIsSlide = $childType === 'column'
                && $childPage !== ''
                && (strpos($childId, 'page_slide_') === 0 || isset($child['properties']['pageName']));
            $childIsList = $childType === 'schemacontrollist';
            if ($childIsSlide) {
                if (!isset($pageTitle[$childPage]) || isset($seen['slide'][$childPage])) {
                    continue;
                }
                $seen['slide'][$childPage] = true;
            }
            if ($childIsList && !$insideSlide && !$childIsSlide) {
                if (!isset($pageTitle[$childPage]) || isset($seen['slide'][$childPage]) || isset($seen['list'][$childPage])) {
                    continue;
                }
                $seen['list'][$childPage] = true;
            }
            $children[] = self::prunePublishedPageNodes($child, $pageTitle, $idsByPage, $seen, $insideSlide || $childIsSlide);
        }
        if ($isButtonList && count($children) > count($pageTitle)) {
            $children = array_slice($children, 0, count($pageTitle));
        }
        $node['children'] = array_values($children);

        return $node;
    }

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
        // New V2 models keep the v2 test-node page list. Do not add pages that
        // exist only on the previous Studio schema.
        $v2PageAuthority = self::schemaPrefersV2NodeDetails($schema);

        foreach ($pagesFromControls as $pageName => $frames) {
            $pageExists = isset($existingPages[$pageName]) && is_array($existingPages[$pageName]) && $existingPages[$pageName] !== [];
            // V2 test-node pages are the wire source. Do not replace them with a
            // stored control frame that merely has a longer hex string.
            if ($v2PageAuthority && $pageExists) {
                continue;
            }
            if ($v2PageAuthority && !$pageExists) {
                continue;
            }
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
        if (isset($schema['v2_node_details'][0]) && is_array($schema['v2_node_details'][0])) {
            $schema['v2_node_details'][0]['config'] = $schema['deviceConfig'];
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
            // Contract topics must match the opened node (Studio may have baked another id).
            $schema = self::rewriteTransportNodeTopics($schema, $nodeId);
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

    /**
     * Force mqtt / websocket / clientIdPattern onto device/{openedNodeId}/...
     * App may key off opened node_id already; this keeps schema contract correct.
     */
    private static function rewriteTransportNodeTopics(array $schema, $nodeId): array
    {
        $nodeId = trim((string) $nodeId);
        if ($nodeId === '') {
            return $schema;
        }

        $rewriteTopic = static function ($topic) use ($nodeId) {
            $topic = (string) $topic;
            if ($topic === '') {
                return $topic;
            }
            $replaced = preg_replace('#^device/[^/]+/#', 'device/' . $nodeId . '/', $topic);
            return is_string($replaced) ? $replaced : $topic;
        };

        if (isset($schema['mqtt']) && is_array($schema['mqtt'])) {
            if (!isset($schema['mqtt']['topics']) || !is_array($schema['mqtt']['topics'])) {
                $schema['mqtt']['topics'] = [];
            }
            foreach (['command', 'state', 'telemetry', 'presence'] as $kind) {
                $current = trim((string) ($schema['mqtt']['topics'][$kind] ?? ''));
                $schema['mqtt']['topics'][$kind] = $current !== ''
                    ? $rewriteTopic($current)
                    : ('device/' . $nodeId . '/' . $kind);
            }
            $schema['mqtt']['clientIdPattern'] = 'efactor_' . $nodeId . '_{clientId}';
        }

        if (isset($schema['websocket']['subscriptions']) && is_array($schema['websocket']['subscriptions'])) {
            foreach ($schema['websocket']['subscriptions'] as $index => $subscription) {
                if (!is_array($subscription) || empty($subscription['topic'])) {
                    continue;
                }
                $schema['websocket']['subscriptions'][$index]['topic'] = $rewriteTopic($subscription['topic']);
            }
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
            'v2_node_details' => true,
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

        if ($bucket === 'commands') {
            $normalized = self::separateOverlappingBytewiseSlots(
                $normalized,
                $report,
                ($frameName !== '' ? $frameName : 'frame') . '.commands'
            );
        }

        return $normalized;
    }

    /**
     * Two bytewise command slots on the same byte cannot be packed into one frame.
     * Keep field order, names, values and lengths. When a later bytewise slot
     * overlaps an earlier one, move that later slot to the next free byte and
     * shift only the slots that then collide. Bitwise slots stay put so a shared
     * byte with different bit ranges remains valid. Gaps that do not overlap
     * are left alone. Nothing is added, removed, renamed, or invented.
     */
    private static function separateOverlappingBytewiseSlots(array $fields, array &$report, $label)
    {
        $ranges = [];
        foreach ($fields as $index => $field) {
            if (!is_array($field)) {
                continue;
            }
            $name = self::frameFieldName($field);
            $bytes = self::resolveCommandByteCount($field);
            $start = isset($field['byte_start_position']) ? (int) $field['byte_start_position'] : 0;
            if ($start === 0 && $name === 'trigger') {
                continue;
            }
            $level = strtolower(trim((string) (
                isset($field['field_level']) ? $field['field_level'] : (isset($field['fieldLevel']) ? $field['fieldLevel'] : '')
            )));
            if ($level === 'bitwise' || $start < 1 || $bytes < 1) {
                continue;
            }

            $end = $start + $bytes;
            $overlaps = false;
            foreach ($ranges as $range) {
                if ($start < $range[1] && $end > $range[0]) {
                    $overlaps = true;
                    break;
                }
            }
            if ($overlaps) {
                $nextFree = 1;
                foreach ($ranges as $range) {
                    if ($range[1] > $nextFree) {
                        $nextFree = $range[1];
                    }
                }
                $start = $nextFree;
                $end = $start + $bytes;
                $field['byte_start_position'] = $start;
                $fields[$index] = $field;
                $report['repairedByteFields'][] = $label . '.' . (self::frameFieldLabel($field) ?: 'field') . '.byteOverlap';
            }
            $ranges[] = [$start, $end];
        }

        return $fields;
    }

    /**
     * Apply the overlap repair to every command list the app can pack from.
     * Response and error lists are not command frames and are left unchanged.
     */
    private static function repairKnownCommandFrames(array $schema, array &$report)
    {
        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $index => $control) {
                if (!is_array($control)) {
                    continue;
                }
                $id = trim((string) (isset($control['id']) ? $control['id'] : ('controls[' . $index . ']')));
                $schema['controls'][$index] = self::repairControlCommandFrames($control, $report, $id);
            }
        }

        if (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
            $schema['deviceConfig'] = self::repairConfigCommandFrames($schema['deviceConfig'], $report, 'deviceConfig');
        }
        if (isset($schema['state']['device']['config']) && is_array($schema['state']['device']['config'])) {
            $schema['state']['device']['config'] = self::repairConfigCommandFrames(
                $schema['state']['device']['config'],
                $report,
                'state.device.config'
            );
        }
        if (isset($schema['deviceState']['config']) && is_array($schema['deviceState']['config'])) {
            $schema['deviceState']['config'] = self::repairConfigCommandFrames(
                $schema['deviceState']['config'],
                $report,
                'deviceState.config'
            );
        }
        foreach (['node_details', 'v2_node_details'] as $listKey) {
            if (!isset($schema[$listKey]) || !is_array($schema[$listKey])) {
                continue;
            }
            foreach ($schema[$listKey] as $index => $detail) {
                if (!is_array($detail) || !isset($detail['config']) || !is_array($detail['config'])) {
                    continue;
                }
                $schema[$listKey][$index]['config'] = self::repairConfigCommandFrames(
                    $detail['config'],
                    $report,
                    $listKey . '[' . $index . '].config'
                );
            }
        }
        if (isset($schema['extensions']['serverDrivenUi']) && is_array($schema['extensions']['serverDrivenUi'])) {
            $sdui = $schema['extensions']['serverDrivenUi'];
            if (isset($sdui['controls']) && is_array($sdui['controls'])) {
                foreach ($sdui['controls'] as $index => $control) {
                    if (!is_array($control)) {
                        continue;
                    }
                    $id = trim((string) (isset($control['id']) ? $control['id'] : ('sdui.controls[' . $index . ']')));
                    $sdui['controls'][$index] = self::repairControlCommandFrames($control, $report, $id);
                }
            }
            if (isset($sdui['deviceConfig']) && is_array($sdui['deviceConfig'])) {
                $sdui['deviceConfig'] = self::repairConfigCommandFrames($sdui['deviceConfig'], $report, 'sdui.deviceConfig');
            }
            $schema['extensions']['serverDrivenUi'] = $sdui;
        }

        return $schema;
    }

    private static function repairControlCommandFrames(array $control, array &$report, $label)
    {
        if (isset($control['deviceFrame']['commands']) && is_array($control['deviceFrame']['commands'])) {
            $control['deviceFrame']['commands'] = self::splitLegacyCommandList(self::separateOverlappingBytewiseSlots(
                $control['deviceFrame']['commands'],
                $report,
                $label . '.deviceFrame.commands'
            ));
        }
        if (isset($control['protocolMeta']['frame']['commands']) && is_array($control['protocolMeta']['frame']['commands'])) {
            $control['protocolMeta']['frame']['commands'] = self::splitLegacyCommandList(self::separateOverlappingBytewiseSlots(
                $control['protocolMeta']['frame']['commands'],
                $report,
                $label . '.protocolMeta.frame.commands'
            ));
        }
        if (isset($control['commands']) && self::looksLikeCommandFieldList($control['commands'])) {
            $control['commands'] = self::splitLegacyCommandList(self::separateOverlappingBytewiseSlots(
                $control['commands'],
                $report,
                $label . '.commands'
            ));
        }
        if (isset($control['protocolMeta']['ui_components']) && is_array($control['protocolMeta']['ui_components'])) {
            foreach ($control['protocolMeta']['ui_components'] as $index => $component) {
                if (!is_array($component) || !isset($component['frame']['commands']) || !is_array($component['frame']['commands'])) {
                    continue;
                }
                $component['frame']['commands'] = self::separateOverlappingBytewiseSlots(
                    $component['frame']['commands'],
                    $report,
                    $label . '.ui_components[' . $index . '].frame.commands'
                );
                $control['protocolMeta']['ui_components'][$index] = $component;
            }
        }

        return $control;
    }

    private static function repairConfigCommandFrames(array $config, array &$report, $label)
    {
        if (!isset($config['devices']) || !is_array($config['devices'])) {
            return $config;
        }
        foreach ($config['devices'] as $deviceIndex => $device) {
            if (!is_array($device) || !isset($device['pages']) || !is_array($device['pages'])) {
                continue;
            }
            foreach ($device['pages'] as $pageKey => $pageFrames) {
                if (!is_array($pageFrames)) {
                    continue;
                }
                foreach ($pageFrames as $frameIndex => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $path = $label . '.devices[' . $deviceIndex . '].pages.' . $pageKey . '[' . $frameIndex . ']';
                    if (isset($item['frame']['commands']) && is_array($item['frame']['commands'])) {
                        $item['frame']['commands'] = self::splitLegacyCommandList(self::separateOverlappingBytewiseSlots(
                            $item['frame']['commands'],
                            $report,
                            $path . '.frame.commands'
                        ));
                    } elseif (self::looksLikeCommandFieldList(isset($item['commands']) ? $item['commands'] : null)) {
                        $item['commands'] = self::splitLegacyCommandList(self::separateOverlappingBytewiseSlots(
                            $item['commands'],
                            $report,
                            $path . '.commands'
                        ));
                    }
                    $config['devices'][$deviceIndex]['pages'][$pageKey][$frameIndex] = $item;
                }
            }
        }

        return $config;
    }

    /**
     * Split a combined header (A2EA, A4EA, A1E7) into CommandId / Function Code / Terminator.
     */
    private static function splitLegacyCommandList(array $commands)
    {
        if ($commands === [] || !class_exists(\App\Models\View_page_model::class, false)) {
            return $commands;
        }
        try {
            $model = new \App\Models\View_page_model();
            $split = $model->splitLegacyProtocolCommandFields($commands);

            return is_array($split) ? $split : $commands;
        } catch (\Throwable $exception) {
            return $commands;
        }
    }

    private static function looksLikeCommandFieldList($fields)
    {
        if (!self::isList($fields) || $fields === []) {
            return false;
        }
        $sample = $fields[0];
        if (!is_array($sample)) {
            return false;
        }

        return array_key_exists('byte_start_position', $sample)
            || array_key_exists('no_of_bytes', $sample)
            || array_key_exists('default_name', $sample)
            || array_key_exists('Field_description', $sample);
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
        return self::canonicalizeProtocolFieldName(self::frameFieldLabel($field));
    }

    /**
     * Match view_page_model: terminatorCrc / "Terminator / CRC" => terminator.
     */
    private static function canonicalizeProtocolFieldName($raw)
    {
        // Lowercase FIRST — [^a-z0-9] would otherwise strip uppercase A-Z.
        $name = preg_replace('/[^a-z0-9]+/', '', strtolower((string) $raw));
        if ($name === '') {
            return '';
        }
        if ($name === 'terminator' || $name === 'terminatorcrc' || $name === 'crc'
            || strpos($name, 'terminator') === 0
        ) {
            return 'terminator';
        }
        if (in_array($name, ['commandid', 'command', 'cmdid', 'cmd'], true)) {
            return 'commandid';
        }
        if (in_array($name, ['functioncode', 'function', 'funcode', 'funccode'], true)) {
            return 'functioncode';
        }
        if (in_array($name, ['actionid', 'action'], true)) {
            return 'actionid';
        }

        return $name;
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
            'send' => '<path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path>',
            'gauge' => '<path d="m12 14 4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
            'bolt' => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"></path>',
            'power' => '<path d="M12 2v10"></path><path d="M18.4 6.6a9 9 0 1 1-12.77.04"></path>',
            'timer' => '<circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2 2"></path><path d="M5 3 2 6"></path><path d="m22 6-3-3"></path><path d="M6.38 18.7 4 21"></path><path d="M17.64 18.67 20 21"></path>',
            'clock' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
            'settings' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.52a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.52a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
            'key' => '<path d="M21 2 9 14m12 4-8.5-8.5a1.5 1.5 0 0 1 0-2.12L6.5 2.5a1.5 1.5 0 0 1 2.12 0l11.38 11.38a1.5 1.5 0 0 1 0 2.12Z"></path><circle cx="6" cy="16" r="2"></circle>',
            'trash-2' => '<path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m4 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>',
            'file' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path>',
            'help-circle' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'inbox' => '<rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="M2 6h3a6 6 0 0 1 6-6 6 6 0 0 1 6 6h3"></path>',
            'info' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>',
            'chevron-left' => '<path d="m15 18-6-6 6-6"></path>',
            'arrow-left' => '<path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>',
            'arrow-right' => '<path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>',
            'fan' => '<path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0-4 0"></path><path d="M12 10c-1.6-3.8-.8-6.7 2.4-8 2.1 3.1 1.3 5.8-2.4 8Z"></path><path d="M14 12c3.8-1.6 6.7-.8 8 2.4-3.1 2.1-5.8 1.3-8-2.4Z"></path><path d="M12 14c1.6 3.8.8 6.7-2.4 8-2.1-3.1-1.3-5.8 2.4-8Z"></path><path d="M10 12c-3.8 1.6-6.7.8-8-2.4 3.1-2.1 5.8-1.3 8 2.4Z"></path>',
            'refresh-cw' => '<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path><path d="M8 16H3v5"></path>',
            'lightbulb' => '<path d="M15 14c.2-1 .7-1.7 1.4-2.5A6 6 0 1 0 7.6 11.5C8.3 12.3 8.8 13 9 14"></path><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M10 14h4v4h-4z"></path>',
            'thermometer' => '<path d="M14 14.76V5a2 2 0 1 0-4 0v9.76a4 4 0 1 0 4 0Z"></path>',
            'droplet' => '<path d="M12 2.69 5.66 9.03a9 9 0 1 0 12.68 0L12 2.69z"></path>',
            'wifi' => '<path d="M12 20h.01"></path><path d="M2 8.82a15 15 0 0 1 20 0"></path><path d="M5 12.86a10 10 0 0 1 14 0"></path><path d="M8.5 16.43a5 5 0 0 1 7 0"></path>',
            'signal' => '<path d="M2 20h.01"></path><path d="M7 20a5 5 0 0 1 10 0"></path><path d="M4 14a11 11 0 0 1 16 0"></path><path d="M2 8a16 16 0 0 1 20 0"></path>',
            'target' => '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
            'lock' => '<rect width="18" height="11" x="3" y="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>',
            'radio' => '<circle cx="12" cy="12" r="2"></circle><path d="M4.93 19.07a10 10 0 0 1 0-14.14"></path><path d="M7.76 16.24a6 6 0 0 1 0-8.49"></path><path d="M16.24 7.76a6 6 0 0 1 0 8.49"></path><path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>',
            'battery' => '<rect width="16" height="10" x="2" y="7" rx="2" ry="2"></rect><line x1="22" x2="20" y1="11" y2="11"></line>',
            'volume-2' => '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>',
            'zap' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>',
            'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path>',
            'cpu' => '<rect width="16" height="16" x="4" y="4" rx="2"></rect><path d="M9 9h6v6H9z"></path><path d="M9 1v3"></path><path d="M15 1v3"></path><path d="M9 20v3"></path><path d="M15 20v3"></path><path d="M20 9h3"></path><path d="M20 14h3"></path><path d="M1 9h3"></path><path d="M1 14h3"></path>',
            'air-flow' => '<path d="M3 8h10a3 3 0 1 0-3-3"></path><path d="M3 12h16"></path><path d="M3 16h10a3 3 0 1 1-3 3"></path>',
            'motor' => '<rect width="16" height="12" x="4" y="7" rx="2"></rect><path d="M8 7V5h8v2"></path><path d="M10 13h4"></path><path d="M2 11h2"></path><path d="M20 11h2"></path>',
            'sliders' => '<line x1="4" x2="4" y1="21" y2="14"></line><line x1="4" x2="4" y1="10" y2="3"></line><line x1="12" x2="12" y1="21" y2="12"></line><line x1="12" x2="12" y1="8" y2="3"></line><line x1="20" x2="20" y1="21" y2="16"></line><line x1="20" x2="20" y1="12" y2="3"></line><line x1="2" x2="6" y1="14" y2="14"></line><line x1="10" x2="14" y1="8" y2="8"></line><line x1="18" x2="22" y1="16" y2="16"></line>',
            'sun' => '<circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>',
            'palette' => '<circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2a10 10 0 0 0 0 20 2 2 0 0 0 2-2 1.5 1.5 0 0 1 1.5-1.5H17a5 5 0 0 0 5-5.3A10 10 0 0 0 12 2z"></path>',
            'aperture' => '<circle cx="12" cy="12" r="10"></circle><path d="M14.31 8l5.74 9.94"></path><path d="M9.69 8h11.48"></path><path d="M7.38 12.5l5.74-9.94"></path><path d="M13.69 16l-5.74 9.94"></path><path d="M14.31 16h-11.48"></path><path d="M16.62 11.5l-5.74 9.94"></path>',
            'humidity' => '<path d="M12 2s6 7 6 12a6 6 0 0 1-12 0c0-5 6-12 6-12Z"></path><path d="M8 14a4 4 0 0 0 8 0"></path>',
            'snow-flake' => '<path d="M12 2v20"></path><path d="m4.93 4.93 14.14 14.14"></path><path d="M2 12h20"></path><path d="m4.93 19.07 14.14-14.14"></path>',
            'bluetooth' => '<path d="m7 7 10 10-5 5V2l5 5L7 17"></path>',
            'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>',
            'menu' => '<line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line>',
            'code' => '<path d="m16 18 6-6-6-6"></path><path d="m8 6-6 6 6 6"></path>',
            'edit' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>',
            'play' => '<polygon points="5 3 19 12 5 21 5 3"></polygon>',
            'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>',
            'plug' => '<path d="M12 22v-5"></path><path d="M9 8V2"></path><path d="M15 8V2"></path><path d="M7 8h10v4a5 5 0 0 1-10 0V8Z"></path>',
        ];
        // Always refresh these redesigned outline paths so Flutter picks up the new look.
        $refreshPaths = [
            'send', 'gauge', 'bolt', 'clock', 'file', 'fan', 'refresh-cw', 'wifi',
            'target', 'zap', 'radio', 'info', 'power', 'check-circle', 'cpu', 'air-flow',
            'motor', 'sliders', 'sun', 'palette', 'aperture', 'humidity', 'snow-flake',
            'bluetooth', 'bell', 'menu', 'code', 'edit', 'play', 'eye', 'plug', 'volume-2',
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
            if (empty($schema['icons']['library']['paths'][$name]) || in_array($name, $refreshPaths, true)) {
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
        $mode = strtolower(trim((string) ($schema['theme']['mode'] ?? '')));
        $themedTopbar = self::colorAt($schema, ['theme', 'colors', 'topbar']);
        if ($mode === 'light') {
            return $themedTopbar ?: '#FFFFFF';
        }
        if ($mode === 'dark') {
            if ($themedTopbar !== null && self::colorLuminance($themedTopbar) < 160) {
                return $themedTopbar;
            }

            return '#020617';
        }

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

    /**
     * Swap only existing surface and text colors on the widget tree.
     * Button, icon, status, and accent fills are left alone.
     *
     * @param array<string, mixed> $node
     */
    private static function paintWidgetChrome(&$node, string $mode, int $depth = 0): void
    {
        if (!is_array($node) || $depth > 60 || ($mode !== 'light' && $mode !== 'dark')) {
            return;
        }

        $id = strtolower(trim((string) ($node['id'] ?? '')));
        $skip = $id !== '' && (
            strpos($id, 'button') !== false
            || strpos($id, 'icon') !== false
            || strpos($id, 'toggle') !== false
            || strpos($id, 'online') !== false
            || strpos($id, 'badge') !== false
            || strpos($id, 'power') !== false
            || strpos($id, 'send') !== false
            || strpos($id, 'wifi') !== false
        );
        if (!$skip) {
            $surfaceMap = $mode === 'light'
                ? array(
                    '0B1220' => '#F8FAFC',
                    '111827' => '#EEF2F7',
                    '0F172A' => '#F8FAFC',
                    '1E293B' => '#FFFFFF',
                    '020617' => '#FFFFFF',
                    '0B0F17' => '#F8FAFC',
                )
                : array(
                    'F8FAFC' => '#0B1220',
                    'EEF2F7' => '#111827',
                    'F1F5F9' => '#111827',
                    'FFFFFF' => '#0F172A',
                    'E2E8F0' => '#334155',
                );
            $textMap = $mode === 'light'
                ? array(
                    'F8FAFC' => '#0F172A',
                    'E2E8F0' => '#0F172A',
                    '94A3B8' => '#64748B',
                )
                : array(
                    '0F172A' => '#F8FAFC',
                    '1E293B' => '#F8FAFC',
                    '64748B' => '#94A3B8',
                );
            $surfaceKeys = array('backgroundColor', 'background', 'fillColor', 'cardColor', 'shellColor', 'surface', 'topbar');
            $textKeys = array('color', 'textColor', 'foregroundColor', 'titleColor', 'mutedText', 'subtitleColor');
            foreach (array('style', 'properties', 'props') as $bag) {
                if (!isset($node[$bag]) || !is_array($node[$bag])) {
                    continue;
                }
                foreach ($surfaceKeys as $key) {
                    if (!array_key_exists($key, $node[$bag]) || !is_string($node[$bag][$key])) {
                        continue;
                    }
                    $hex = self::normalizeHex($node[$bag][$key]);
                    if ($hex !== '' && isset($surfaceMap[$hex])) {
                        $node[$bag][$key] = $surfaceMap[$hex];
                    }
                }
                foreach ($textKeys as $key) {
                    if (!array_key_exists($key, $node[$bag]) || !is_string($node[$bag][$key])) {
                        continue;
                    }
                    $hex = self::normalizeHex($node[$bag][$key]);
                    if ($hex !== '' && isset($textMap[$hex])) {
                        $node[$bag][$key] = $textMap[$hex];
                    }
                }
            }
            foreach ($surfaceKeys as $key) {
                if (!array_key_exists($key, $node) || !is_string($node[$key])) {
                    continue;
                }
                $hex = self::normalizeHex($node[$key]);
                if ($hex !== '' && isset($surfaceMap[$hex])) {
                    $node[$key] = $surfaceMap[$hex];
                }
            }
        }

        foreach ($node as $key => &$child) {
            if (!is_array($child) || self::schemaWalkSkipKey($key)) {
                continue;
            }
            self::paintWidgetChrome($child, $mode, $depth + 1);
        }
        unset($child);
    }

    private static function normalizeHex($value): string
    {
        $raw = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));
        if (strlen($raw) === 8) {
            $raw = substr($raw, -6);
        }
        if (strlen($raw) === 3) {
            $raw = $raw[0] . $raw[0] . $raw[1] . $raw[1] . $raw[2] . $raw[2];
        }

        return strlen($raw) === 6 ? $raw : '';
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
            $schema['mobileView']['header'] = self::stripHeaderScanAction($schema['mobileView']['header']);
        }
        // Drop the retired header scan control from every known schema surface.
        $schema = self::stripHeaderScanEverywhere($schema);
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

    /**
     * Remove the header scan/QR button from published device UI schemas.
     * WiFi-flow QR scanner actions are left alone.
     */
    private static function stripHeaderScanEverywhere(array $schema)
    {
        if (isset($schema['mobileView']['header']) && is_array($schema['mobileView']['header'])) {
            $schema['mobileView']['header'] = self::stripHeaderScanAction($schema['mobileView']['header']);
        }
        if (isset($schema['document']['mobileView']['header']) && is_array($schema['document']['mobileView']['header'])) {
            $schema['document']['mobileView']['header'] = self::stripHeaderScanAction($schema['document']['mobileView']['header']);
        }
        if (isset($schema['actions']['ui.scan'])) {
            unset($schema['actions']['ui.scan']);
        }
        if (isset($schema['document']['actions']['ui.scan'])) {
            unset($schema['document']['actions']['ui.scan']);
        }
        if (isset($schema['components']) && is_array($schema['components'])) {
            unset($schema['components']['scanButton']);
            if (isset($schema['components']['appBar']) && is_array($schema['components']['appBar'])
                && isset($schema['components']['appBar']['children']) && is_array($schema['components']['appBar']['children'])) {
                $schema['components']['appBar']['children'] = array_values(array_filter(
                    $schema['components']['appBar']['children'],
                    static function ($child) {
                        return strcasecmp(trim((string) $child), 'scanButton') !== 0;
                    }
                ));
            }
        }
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::stripHeaderScanFromWidgetTree($schema['ui']['widgetTree']);
        }
        if (isset($schema['extensions']['serverDrivenUi']['page']['body']) && is_array($schema['extensions']['serverDrivenUi']['page']['body'])) {
            foreach ($schema['extensions']['serverDrivenUi']['page']['body'] as $index => $node) {
                if (!is_array($node)) {
                    continue;
                }
                if (self::isHeaderScanNode($node)) {
                    unset($schema['extensions']['serverDrivenUi']['page']['body'][$index]);
                    continue;
                }
                if (isset($node['children']) && is_array($node['children'])) {
                    self::stripHeaderScanFromWidgetTree($schema['extensions']['serverDrivenUi']['page']['body'][$index]);
                }
            }
            $schema['extensions']['serverDrivenUi']['page']['body'] = array_values($schema['extensions']['serverDrivenUi']['page']['body']);
        }

        return $schema;
    }

    private static function stripHeaderScanAction(array $header)
    {
        if (!isset($header['actions']) || !is_array($header['actions'])) {
            return $header;
        }
        $header['actions'] = array_values(array_filter($header['actions'], static function ($action) {
            if (!is_array($action)) {
                return true;
            }
            $id = strtolower(trim((string) (isset($action['id']) ? $action['id'] : '')));
            $icon = strtolower(trim((string) (isset($action['icon']) ? $action['icon'] : '')));
            $label = strtolower(trim((string) (isset($action['label']) ? $action['label'] : '')));
            $act = strtolower(trim((string) (isset($action['action']) ? $action['action'] : '')));

            return !($id === 'scan'
                || $icon === 'scan'
                || $label === 'scan'
                || $act === 'openscanner'
                || $act === 'ui.scan');
        }));

        return $header;
    }

    private static function isHeaderScanNode(array $node)
    {
        $id = strtolower(trim((string) (isset($node['id']) ? $node['id'] : '')));
        // Only the app-topbar scan control - never wifiProvisioning QR scan UI.
        return in_array($id, [
            'scan-button',
            'scanbutton',
            'device_scan_button',
            'device_scan_icon',
            'scan',
        ], true);
    }

    private static function stripHeaderScanFromWidgetTree(&$node, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $id = strtolower(trim((string) (isset($node['id']) ? $node['id'] : '')));
        $inHeaderActions = in_array($id, [
            'top_actions',
            'headeractions',
            'header-actions',
            'app_topbar',
            'app-bar',
            'appbar',
            'app_topbar_row',
        ], true);

        foreach (['children', 'body', 'items', 'nodes'] as $listKey) {
            if (!isset($node[$listKey]) || !is_array($node[$listKey])) {
                continue;
            }
            $kept = [];
            foreach ($node[$listKey] as $child) {
                if (!is_array($child)) {
                    $kept[] = $child;
                    continue;
                }
                if (self::isHeaderScanNode($child)) {
                    continue;
                }
                self::stripHeaderScanFromWidgetTree($child, $depth + 1);
                // Strip scan entries only from header action groups.
                if ($inHeaderActions && isset($child['actions']) && is_array($child['actions'])) {
                    $child = self::stripHeaderScanAction($child);
                }
                $kept[] = $child;
            }
            $node[$listKey] = array_values($kept);
        }
        if ($inHeaderActions && isset($node['actions']) && is_array($node['actions'])) {
            $node = self::stripHeaderScanAction($node);
        }
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

    /**
     * Full send-button props. Apps require action.type === "send".
     */
    private static function buildSendButtonProps($binding, $visible = true, $accentColor = '#2563EB')
    {
        $accent = trim((string) $accentColor);
        if ($accent === '') {
            $accent = '#2563EB';
        }

        return [
            'visible' => (bool) $visible,
            'label' => 'Send',
            'icon' => 'send',
            'size' => 32,
            'borderRadius' => 9,
            'backgroundColor' => $accent,
            'color' => $accent,
            'iconColor' => '#FFFFFF',
            'iconSize' => 15,
            'action' => [
                'type' => 'send',
                'binding' => (string) $binding,
            ],
        ];
    }

    /**
     * Merge missing send-button fields without dropping existing keys.
     */
    private static function mergeSendButtonProps($existing, $binding, $visible = true, $accentColor = '#2563EB')
    {
        $full = self::buildSendButtonProps($binding, $visible, $accentColor);
        if (!is_array($existing)) {
            return $full;
        }
        foreach ($full as $key => $value) {
            if (!array_key_exists($key, $existing) || $existing[$key] === null || $existing[$key] === '') {
                $existing[$key] = $value;
            }
        }
        if (!isset($existing['action']) || !is_array($existing['action'])) {
            $existing['action'] = $full['action'];
        } else {
            $type = strtolower(trim((string) ($existing['action']['type'] ?? '')));
            if ($type === '' || $type === 'update') {
                // textField send affordance must be "send", never a bare update stub.
                $existing['action']['type'] = 'send';
            }
            if (empty($existing['action']['binding'])) {
                $existing['action']['binding'] = (string) $binding;
            }
        }
        $existing['visible'] = array_key_exists('visible', $existing)
            ? (bool) $existing['visible']
            : (bool) $visible;

        return $existing;
    }

    private static function isGetStatusParameterName($name)
    {
        $normalized = strtolower(trim(preg_replace('/[_-]+/', ' ', (string) $name)));

        return $normalized !== '' && strpos($normalized, 'get status') !== false;
    }

    private static function getStatusRevealBinding(array $control)
    {
        $pageNo = trim((string) (isset($control['pageNo']) ? $control['pageNo'] : ''));
        $name = trim((string) self::firstNonEmpty(
            isset($control['parameterName']) ? $control['parameterName'] : null,
            isset($control['parameter']) ? $control['parameter'] : null,
            isset($control['label']) ? $control['label'] : null,
            isset($control['name']) ? $control['name'] : null
        ));
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $pageNo . ':' . $name), '_'));
        if ($slug === '') {
            $slug = 'get_status';
        }

        return 'state.getStatusResponse.' . $slug . '.open';
    }

    private static function controlUiTypeSource(array $control)
    {
        $candidates = [
            isset($control['protocolMeta']['ui_type_source']) ? $control['protocolMeta']['ui_type_source'] : null,
            isset($control['protocolMeta']['source']) ? $control['protocolMeta']['source'] : null,
            isset($control['meta']['ui_type_source']) ? $control['meta']['ui_type_source'] : null,
            isset($control['uiTypeSource']) ? $control['uiTypeSource'] : null,
            isset($control['apiUiSource']) ? $control['apiUiSource'] : null,
        ];
        foreach ($candidates as $value) {
            $token = strtolower(trim((string) $value));
            if ($token !== '') {
                return $token;
            }
        }

        return '';
    }

    /**
     * Foundation Get Status contract for Flutter:
     * - response controls start collapsed (visibleWhen + initiallyExpanded false)
     * - command tap toggles state.getStatusResponse.*.open
     * - state seeds open=false so the dropdown is closed on first paint
     */
    private static function applyGetStatusRevealContract(array $schema)
    {
        if (!isset($schema['controls']) || !is_array($schema['controls'])) {
            return $schema;
        }

        $revealState = [];
        foreach ($schema['controls'] as $index => $control) {
            if (!is_array($control)) {
                continue;
            }
            $paramName = self::firstNonEmpty(
                isset($control['parameterName']) ? $control['parameterName'] : null,
                isset($control['parameter']) ? $control['parameter'] : null,
                isset($control['label']) ? $control['label'] : null,
                isset($control['name']) ? $control['name'] : null
            );
            if (!self::isGetStatusParameterName($paramName)) {
                continue;
            }

            $revealBinding = self::getStatusRevealBinding($control);
            $pathParts = explode('.', $revealBinding);
            // state.getStatusResponse.{slug}.open
            if (count($pathParts) >= 4) {
                $slug = $pathParts[2];
                $revealState[$slug] = ['open' => false];
            }

            $source = self::controlUiTypeSource($control);
            $writable = self::isWritableControl($control);

            if (!isset($control['component']) || !is_array($control['component'])) {
                $control['component'] = [];
            }
            if (!isset($control['component']['props']) || !is_array($control['component']['props'])) {
                $control['component']['props'] = [];
            }

            if ($source === 'response' || (!$writable && $source !== 'command' && $source !== 'error')) {
                // Response payload: collapsed until command reveals it.
                if ($source === '' || $source === 'response') {
                    $control['component']['props'] = array_merge($control['component']['props'], [
                        'initiallyExpanded' => false,
                        'collapsed' => true,
                        'revealBinding' => $revealBinding,
                        'visibleWhen' => $revealBinding . ' == true',
                    ]);
                }
            }

            if ($writable || $source === 'command') {
                $revealEvent = [
                    'event' => 'onTap',
                    'action' => [
                        'type' => 'update',
                        'binding' => $revealBinding,
                        'toggle' => true,
                    ],
                ];
                $control['events'] = isset($control['events']) && is_array($control['events'])
                    ? $control['events']
                    : [];
                $hasReveal = false;
                foreach ($control['events'] as $ev) {
                    if (!is_array($ev) || !isset($ev['action']) || !is_array($ev['action'])) {
                        continue;
                    }
                    if (trim((string) ($ev['action']['binding'] ?? '')) === $revealBinding) {
                        $hasReveal = true;
                        break;
                    }
                }
                if (!$hasReveal) {
                    $control['events'][] = $revealEvent;
                }
                $control['component']['events'] = isset($control['component']['events'])
                    && is_array($control['component']['events'])
                    ? $control['component']['events']
                    : [];
                $hasComponentReveal = false;
                foreach ($control['component']['events'] as $ev) {
                    if (!is_array($ev) || !isset($ev['action']) || !is_array($ev['action'])) {
                        continue;
                    }
                    if (trim((string) ($ev['action']['binding'] ?? '')) === $revealBinding) {
                        $hasComponentReveal = true;
                        break;
                    }
                }
                if (!$hasComponentReveal) {
                    $control['component']['events'][] = $revealEvent;
                }
                $control['component']['props'] = array_merge($control['component']['props'], [
                    'revealsResponse' => true,
                    'revealBinding' => $revealBinding,
                    'initiallyExpanded' => false,
                    'collapsed' => true,
                ]);
            }

            $schema['controls'][$index] = $control;
        }

        if ($revealState !== []) {
            if (!isset($schema['state']) || !is_array($schema['state'])) {
                $schema['state'] = [];
            }
            $existing = isset($schema['state']['getStatusResponse']) && is_array($schema['state']['getStatusResponse'])
                ? $schema['state']['getStatusResponse']
                : [];
            foreach ($revealState as $slug => $payload) {
                if (!isset($existing[$slug]) || !is_array($existing[$slug])) {
                    $existing[$slug] = $payload;
                } elseif (!array_key_exists('open', $existing[$slug])) {
                    $existing[$slug]['open'] = false;
                }
            }
            $schema['state']['getStatusResponse'] = $existing;
        }

        // Mirror contract onto ParameterCard nodes in the widget tree.
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])
            && isset($schema['controls']) && is_array($schema['controls'])) {
            self::rewriteGetStatusWidgetTree($schema['ui']['widgetTree'], $schema['controls']);
        }

        return $schema;
    }

    private static function rewriteGetStatusWidgetTree(&$node, array $controls, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $type = strtolower(trim((string) ($node['type'] ?? '')));
        $binding = trim((string) (
            $node['properties']['binding']
            ?? $node['binding']
            ?? $node['properties']['props']['binding']
            ?? ''
        ));
        if (($type === 'parametercard' || $type === 'parametertile') && $binding !== '') {
            foreach ($controls as $control) {
                if (!is_array($control)) {
                    continue;
                }
                if (trim((string) ($control['binding'] ?? '')) !== $binding) {
                    continue;
                }
                $props = isset($control['component']['props']) && is_array($control['component']['props'])
                    ? $control['component']['props']
                    : [];
                if (isset($props['visibleWhen'])) {
                    $node['visibleWhen'] = $props['visibleWhen'];
                    $node['expanded'] = false;
                    if (!isset($node['properties']) || !is_array($node['properties'])) {
                        $node['properties'] = [];
                    }
                    $node['properties']['visibleWhen'] = $props['visibleWhen'];
                    $node['properties']['expanded'] = false;
                    $node['properties']['initiallyExpanded'] = false;
                    $node['properties']['collapsed'] = true;
                    if (!isset($node['properties']['props']) || !is_array($node['properties']['props'])) {
                        $node['properties']['props'] = [];
                    }
                    $node['properties']['props'] = array_merge($node['properties']['props'], [
                        'visibleWhen' => $props['visibleWhen'],
                        'initiallyExpanded' => false,
                        'collapsed' => true,
                        'revealBinding' => $props['revealBinding'] ?? null,
                    ]);
                }
                if (!empty($props['revealsResponse'])) {
                    if (!isset($node['properties']) || !is_array($node['properties'])) {
                        $node['properties'] = [];
                    }
                    if (!isset($node['properties']['props']) || !is_array($node['properties']['props'])) {
                        $node['properties']['props'] = [];
                    }
                    $node['properties']['props']['revealsResponse'] = true;
                    $node['properties']['props']['revealBinding'] = $props['revealBinding'] ?? null;
                    $node['properties']['initiallyExpanded'] = false;
                    $node['properties']['collapsed'] = true;
                    $node['expanded'] = false;
                }
                break;
            }
        }
        foreach (['children', 'body', 'items', 'nodes'] as $listKey) {
            if (!isset($node[$listKey]) || !is_array($node[$listKey])) {
                continue;
            }
            foreach ($node[$listKey] as &$child) {
                if (is_array($child)) {
                    self::rewriteGetStatusWidgetTree($child, $controls, $depth + 1);
                }
            }
            unset($child);
        }
    }

    /**
     * Prefer product/model identity for brand + info titles.
     * Never publish live node nicknames (current_devicetype / hostnames) as the model name.
     */
    private static function applyDisplayModelIdentity(array $schema, $modelNo = '')
    {
        $modelNo = trim((string) $modelNo);
        if ($modelNo === '') {
            $modelNo = trim((string) (
                $schema['meta']['app']['modelNo']
                ?? $schema['meta']['modelNo']
                ?? $schema['app']['modelNo']
                ?? ''
            ));
        }

        $product = '';
        $brand = '';
        $paths = [
            ['deviceConfig', 'info'],
            ['node_details', 0, 'config', 'info'],
            ['v2_node_details', 0, 'config', 'info'],
            ['state', 'device', 'config', 'info'],
        ];
        foreach ($paths as $path) {
            $info = self::valueAtPath($schema, $path);
            if (!is_array($info)) {
                continue;
            }
            if ($product === '') {
                $product = trim((string) self::firstNonEmpty(
                    $info['product'] ?? null,
                    $info['product_name'] ?? null,
                    $info['model'] ?? null
                ));
            }
            if ($brand === '') {
                $brand = trim((string) ($info['brand'] ?? ''));
            }
        }
        if ($product === '' && !empty($schema['meta']['app']['name'])) {
            $product = trim((string) preg_replace('/\s+Control$/i', '', (string) $schema['meta']['app']['name']));
        }
        // Reject nicknames / device-type tokens that are not the catalog model.
        if ($product !== '' && self::looksLikeNodeNickname($product, $modelNo)) {
            $product = '';
        }
        $displayName = $product !== '' ? $product : ($modelNo !== '' ? $modelNo : 'Model UI');

        $rewriteInfo = static function (array $info) use ($displayName, $modelNo, $brand) {
            $currentName = trim((string) ($info['name'] ?? ''));
            if ($currentName === '' || self::looksLikeNodeNickname($currentName, $modelNo)) {
                $info['name'] = $displayName;
            }
            if (empty($info['product']) || self::looksLikeNodeNickname((string) $info['product'], $modelNo)) {
                $info['product'] = $displayName;
            }
            if ($modelNo !== '') {
                $info['model'] = $modelNo;
            }
            if ($brand !== '' && (empty($info['brand']) || self::looksLikeNodeNickname((string) $info['brand'], $modelNo))) {
                $info['brand'] = $brand;
            }

            return $info;
        };

        if (isset($schema['deviceConfig']['info']) && is_array($schema['deviceConfig']['info'])) {
            $schema['deviceConfig']['info'] = $rewriteInfo($schema['deviceConfig']['info']);
        }
        if (isset($schema['deviceConfig']['devices'][0]) && is_array($schema['deviceConfig']['devices'][0])) {
            $deviceName = trim((string) ($schema['deviceConfig']['devices'][0]['name'] ?? ''));
            if ($deviceName === '' || self::looksLikeNodeNickname($deviceName, $modelNo)) {
                $schema['deviceConfig']['devices'][0]['name'] = $modelNo !== '' ? $modelNo : $displayName;
            }
        }
        foreach (['node_details', 'v2_node_details'] as $detailsKey) {
            if (!isset($schema[$detailsKey][0]['config']) || !is_array($schema[$detailsKey][0]['config'])) {
                continue;
            }
            if (isset($schema[$detailsKey][0]['config']['info']) && is_array($schema[$detailsKey][0]['config']['info'])) {
                $schema[$detailsKey][0]['config']['info'] = $rewriteInfo($schema[$detailsKey][0]['config']['info']);
            }
            if (isset($schema[$detailsKey][0]['config']['devices'][0]) && is_array($schema[$detailsKey][0]['config']['devices'][0])) {
                $deviceName = trim((string) ($schema[$detailsKey][0]['config']['devices'][0]['name'] ?? ''));
                if ($deviceName === '' || self::looksLikeNodeNickname($deviceName, $modelNo)) {
                    $schema[$detailsKey][0]['config']['devices'][0]['name'] = $modelNo !== '' ? $modelNo : $displayName;
                }
            }
        }
        if (isset($schema['state']['device']['config']) && is_array($schema['state']['device']['config'])) {
            if (isset($schema['state']['device']['config']['info']) && is_array($schema['state']['device']['config']['info'])) {
                $schema['state']['device']['config']['info'] = $rewriteInfo($schema['state']['device']['config']['info']);
            }
        }

        // Brand strip text node + state subtitle stay on model identity.
        if (isset($schema['state']) && is_array($schema['state'])) {
            $schema['state']['displayModelName'] = $displayName;
            if ($modelNo !== '') {
                $schema['state']['activePageSubtitle'] = $modelNo;
            }
        }
        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::rewriteBrandModelNodes($schema['ui']['widgetTree'], $displayName);
        }

        return $schema;
    }

    private static function looksLikeNodeNickname($value, $modelNo = '')
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }
        $modelNo = trim((string) $modelNo);
        if ($modelNo !== '' && strcasecmp($value, $modelNo) === 0) {
            return false;
        }
        // Host-style nicknames: mirror_A38DE454, node_xxx, etc.
        if (preg_match('/^(mirror|node|device|gw|gateway)[_-]/i', $value)) {
            return true;
        }
        if (preg_match('/^[a-z]+_[A-F0-9]{4,}$/i', $value)) {
            return true;
        }
        // Generic one-word device-type tokens that replace the model title.
        if (preg_match('/^(fan|light|bulb|switch|plug|sensor|mirror|gateway|hub|camera|lock)$/i', $value)) {
            return true;
        }

        return false;
    }

    private static function rewriteBrandModelNodes(&$node, $displayName, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $id = strtolower(trim((string) ($node['id'] ?? '')));
        if ($id === 'brand_model') {
            if (isset($node['properties']) && is_array($node['properties'])) {
                $node['properties']['text'] = $displayName;
                $node['properties']['textBinding'] = 'state.displayModelName';
            }
            if (isset($node['text'])) {
                $node['text'] = $displayName;
            }
        }
        foreach (['children', 'body', 'items', 'nodes'] as $listKey) {
            if (!isset($node[$listKey]) || !is_array($node[$listKey])) {
                continue;
            }
            foreach ($node[$listKey] as &$child) {
                if (is_array($child)) {
                    self::rewriteBrandModelNodes($child, $displayName, $depth + 1);
                }
            }
            unset($child);
        }
    }

    private static function valueAtPath(array $schema, array $path)
    {
        $cursor = $schema;
        foreach ($path as $segment) {
            if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                return null;
            }
            $cursor = $cursor[$segment];
        }

        return $cursor;
    }

    /**
     * Hide footer Save Changes; controls commit immediately via onChanged/onValueCommitted/send.
     */
    private static function applyImmediateCommitContract(array $schema)
    {
        if (!isset($schema['settings']) || !is_array($schema['settings'])) {
            $schema['settings'] = [];
        }
        if (!isset($schema['settings']['visibility']) || !is_array($schema['settings']['visibility'])) {
            $schema['settings']['visibility'] = [];
        }
        $schema['settings']['visibility']['footerVisible'] = false;
        if (!isset($schema['settings']['interaction']) || !is_array($schema['settings']['interaction'])) {
            $schema['settings']['interaction'] = [];
        }
        $schema['settings']['interaction']['commitMode'] = 'immediate';
        $schema['settings']['interaction']['autoSave'] = true;
        if (isset($schema['settings']['layout']) && is_array($schema['settings']['layout'])) {
            // Keep label for legacy readers but hide the footer affordance.
            if (!isset($schema['settings']['layout']['buttonText']) || $schema['settings']['layout']['buttonText'] === '') {
                $schema['settings']['layout']['buttonText'] = 'Save Changes';
            }
        }
        if (isset($schema['mobileView']['footer']) && is_array($schema['mobileView']['footer'])) {
            $schema['mobileView']['footer']['visible'] = false;
        }

        return $schema;
    }

    /**
     * Replace forced light slider shells with theme-aware transparent/card shells.
     */
    private static function applySliderShellThemeContract(array $schema)
    {
        $dark = false;
        $card = '#FFFFFF';
        $shell = '#F8FAFC';
        $mode = strtolower(trim((string) (
            $schema['theme']['themeMode']
            ?? $schema['theme']['mode']
            ?? $schema['state']['global']['themeMode']
            ?? ''
        )));
        if ($mode === 'dark') {
            $dark = true;
        }
        if (isset($schema['theme']['colors']) && is_array($schema['theme']['colors'])) {
            if (!empty($schema['theme']['colors']['card'])) {
                $card = (string) $schema['theme']['colors']['card'];
            } elseif (!empty($schema['theme']['colors']['surface'])) {
                $card = (string) $schema['theme']['colors']['surface'];
            }
            if (!empty($schema['theme']['colors']['background'])) {
                $shell = (string) $schema['theme']['colors']['background'];
            }
        }
        if (!$dark && isset($schema['theme']['colors']['background'])) {
            $dark = self::colorLuminance((string) $schema['theme']['colors']['background']) < 160;
        }
        $containerBg = $dark ? 'transparent' : '#F8FAFC';
        $inactiveTrack = $dark ? '#475569' : '#E2E8F0';

        $patchProps = static function (array $props) use ($containerBg, $inactiveTrack, $dark, $card, $shell) {
            if (isset($props['container']) && is_array($props['container'])) {
                $bg = trim((string) ($props['container']['backgroundColor'] ?? ''));
                if ($bg === '' || strcasecmp($bg, '#F8FAFC') === 0 || strcasecmp($bg, '#FFFFFF') === 0) {
                    $props['container']['backgroundColor'] = $containerBg;
                }
                if ($dark && (strcasecmp((string) ($props['container']['backgroundColor'] ?? ''), '#FFFFFF') === 0
                    || strcasecmp((string) ($props['container']['backgroundColor'] ?? ''), '#F8FAFC') === 0)) {
                    $props['container']['backgroundColor'] = 'transparent';
                }
            } elseif (isset($props['min']) || isset($props['max']) || isset($props['trackGradient'])) {
                $props['container'] = [
                    'visible' => true,
                    'backgroundColor' => $containerBg,
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                    'borderRadius' => 16,
                    'padding' => 14,
                ];
            }
            if ($dark) {
                if (empty($props['inactiveTrackColor']) || strcasecmp((string) $props['inactiveTrackColor'], '#D9DEEB') === 0
                    || strcasecmp((string) $props['inactiveTrackColor'], '#E2E8F0') === 0) {
                    $props['inactiveTrackColor'] = $inactiveTrack;
                }
                if (empty($props['inactiveColor']) || strcasecmp((string) $props['inactiveColor'], '#D9DEEB') === 0) {
                    $props['inactiveColor'] = $inactiveTrack;
                }
                if (isset($props['visual']) && is_array($props['visual'])) {
                    $vbg = trim((string) ($props['visual']['background'] ?? ''));
                    if ($vbg === '' || stripos($vbg, '248,250,252') !== false || stripos($vbg, '#F8FAFC') !== false) {
                        $props['visual']['background'] = 'linear-gradient(180deg,' . $card . ' 0%,' . $shell . ' 100%)';
                    }
                }
            }

            return $props;
        };

        if (isset($schema['controls']) && is_array($schema['controls'])) {
            foreach ($schema['controls'] as $i => $control) {
                if (!is_array($control)) {
                    continue;
                }
                $type = strtolower(str_replace(['_', '-'], '', (string) ($control['componentType'] ?? '')));
                if ($type !== 'slider' && $type !== 'autotriggertimer') {
                    $catalog = strtolower(str_replace(['_', '-'], '', (string) ($control['catalogComponentType'] ?? '')));
                    if ($catalog !== 'autotriggertimer') {
                        continue;
                    }
                }
                if (isset($control['component']['props']) && is_array($control['component']['props'])) {
                    $schema['controls'][$i]['component']['props'] = $patchProps($control['component']['props']);
                }
            }
        }

        if (isset($schema['ui']['widgetTree']) && is_array($schema['ui']['widgetTree'])) {
            self::patchSliderShellNodes($schema['ui']['widgetTree'], $patchProps);
        }

        return $schema;
    }

    private static function patchSliderShellNodes(&$node, $patchProps, $depth = 0)
    {
        if (!is_array($node) || $depth > 60) {
            return;
        }
        $type = strtolower(trim((string) ($node['type'] ?? '')));
        if ($type === 'schemaslider' || $type === 'slider') {
            if (isset($node['properties']['props']) && is_array($node['properties']['props'])) {
                $node['properties']['props'] = $patchProps($node['properties']['props']);
            }
            if (isset($node['props']) && is_array($node['props'])) {
                $node['props'] = $patchProps($node['props']);
            }
        }
        foreach ($node as &$child) {
            if (is_array($child)) {
                self::patchSliderShellNodes($child, $patchProps, $depth + 1);
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
            // Use the shared Ready/Write/blank rules (empty = writable for legacy
            // command UIs; Ready counts as writable; Read = display-only).
            $writable = self::isWritableControl($control);
            $access = strtolower(trim((string) (isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : '')));

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
                    // Apps reject the stub {visible,label} shape — always publish
                    // action.type=send + binding (same contract as Studio/PHP builders).
                    if ($writable) {
                        $control['uiParts'] = ['textField', 'sendButton'];
                        $binding = trim((string) (isset($control['binding']) ? $control['binding'] : ''));
                        if ($binding === '') {
                            $binding = 'device.parameters.' . trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $id), '_');
                        }
                        $existingSend = (isset($control['component']['props']['sendButton'])
                            && is_array($control['component']['props']['sendButton']))
                            ? $control['component']['props']['sendButton']
                            : [];
                        $control['component']['props']['sendButton'] = self::mergeSendButtonProps(
                            $existingSend,
                            $binding,
                            true
                        );
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
            if ($writable && $id !== '') {
                $control['action'] = 'update_' . trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $id), '_');
                self::rememberControlAction($schema, $control);
            } else {
                unset($control['action']);
                $control['events'] = [];
                if (isset($control['component']) && is_array($control['component'])) {
                    $control['component']['events'] = [];
                    if (!empty($control['component']['widgetTree']) && is_array($control['component']['widgetTree'])) {
                        self::stripWriteEvents($control['component']['widgetTree']);
                    }
                }
                if (!empty($control['widgetTree']) && is_array($control['widgetTree'])) {
                    self::stripWriteEvents($control['widgetTree']);
                }
            }
            // Rule 3: writable Send/input controls publish access explicitly.
            // Preserve Ready when the API declared it; do not rewrite Ready -> Write.
            if ($writable) {
                if (!isset($control['protocolMeta']) || !is_array($control['protocolMeta'])) {
                    $control['protocolMeta'] = [];
                }
                $metaAccess = strtolower(trim((string) (isset($control['protocolMeta']['access']) ? $control['protocolMeta']['access'] : '')));
                if ($metaAccess === '' || $metaAccess === 'read') {
                    $control['access'] = 'write';
                    $control['protocolMeta']['access'] = 'write';
                } elseif (strpos($metaAccess, 'ready') !== false) {
                    $control['access'] = $control['protocolMeta']['access'];
                } elseif (strpos($metaAccess, 'write') === false) {
                    $control['access'] = 'Read/Write';
                    $control['protocolMeta']['access'] = 'Read/Write';
                } else {
                    $control['access'] = $control['protocolMeta']['access'];
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
                    } elseif (($type === 'textfield' || $type === 'entertext' || $wanted === 'textField')
                        && (isset($control['component']['props']['sendButton']) || $wanted === 'textField')) {
                        // Repair legacy stubs even when rightAccessory was already textField.
                        $binding = trim((string) (isset($control['binding']) ? $control['binding'] : ''));
                        if ($binding === '') {
                            $binding = 'device.parameters.' . trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $id), '_');
                        }
                        $existingSend = (isset($control['component']['props']['sendButton'])
                            && is_array($control['component']['props']['sendButton']))
                            ? $control['component']['props']['sendButton']
                            : [];
                        $control['component']['props']['sendButton'] = self::mergeSendButtonProps(
                            $existingSend,
                            $binding,
                            true
                        );
                        $control['uiParts'] = ['textField', 'sendButton'];
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

        $icon = isset($control['icon']) ? trim((string) $control['icon']) : '';
        $suggestedIcon = self::defaultIconForControl($control);
        // Fill missing icons, soft-upgrade leftover "gauge", and replace leading
        // power glyphs that duplicate the trailing action button.
        $iconLower = strtolower($icon);
        if ($icon === '') {
            $control['icon'] = $suggestedIcon;
        } elseif (in_array($iconLower, ['gauge', 'power', 'power-off', 'bolt', 'zap', 'file', 'eye', 'cpu', 'play', 'info'], true)
            && strcasecmp($suggestedIcon, $icon) !== 0) {
            // Soft-upgrade leftover generic glyphs when keyword mapping found a better match.
            $control['icon'] = $suggestedIcon;
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

    private static function matchIconKeywordRules($name, $id = '')
    {
        $hay = trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/', ' ', strtolower(trim((string) $name)))));
        $compact = str_replace(' ', '', $hay);
        $id = strtolower(trim((string) $id));
        if ($hay === '' && $id === '') {
            return '';
        }

        // Field/value keywords first. Parent group POWER must not stamp every child.
        $rules = [
            ['icon' => 'check-circle', 'needles' => ['get status', 'getstatus', 'command get']],
            ['icon' => 'info', 'needles' => ['health', 'diagnostics'], 'word' => ['status', 'info']],
            // Fan / airflow before generic "speed" so "Fan Speed" does not become gauge.
            ['icon' => 'fan', 'needles' => ['fan', 'blower', 'ventilat']],
            ['icon' => 'air-flow', 'needles' => ['airflow', 'air flow', 'air speed']],
            ['icon' => 'gauge', 'needles' => ['speed', 'pwm', 'rpm', 'throttle']],
            ['icon' => 'move', 'needles' => ['direction', 'forward', 'backward', 'clockwise', 'anticlock']],
            ['icon' => 'palette', 'needles' => ['led color', 'led colour', 'color', 'colour', 'hue', 'color wheel']],
            ['icon' => 'lightbulb', 'needles' => ['light', 'lamp', 'bulb'], 'word' => ['led']],
            ['icon' => 'motor', 'needles' => ['motor', 'drive', 'actuator']],
            ['icon' => 'sliders', 'needles' => ['preset', 'level', 'range']],
            ['icon' => 'refresh-cw', 'needles' => ['cycle', 'rotate', 'oscillat', 'reverse', 'auto rev', 'autorev', 'mode reg']],
            ['icon' => 'clock', 'needles' => ['timeout', 'timer', 'delay', 'schedule', 'duration', 'countdown']],
            ['icon' => 'sun', 'needles' => ['brightness', 'dimmer', 'lumen']],
            ['icon' => 'thermometer', 'needles' => ['cct', 'color temp', 'colortemp', 'temp', 'thermal', 'heat']],
            ['icon' => 'aperture', 'needles' => ['saturation', 'chroma']],
            ['icon' => 'snow-flake', 'needles' => ['cool', 'cold', 'freeze']],
            ['icon' => 'humidity', 'needles' => ['humid', 'moisture']],
            ['icon' => 'droplet', 'needles' => ['water', 'liquid', 'fluid']],
            ['icon' => 'wifi', 'needles' => ['wifi', 'wlan', 'ssid', 'rssi']],
            ['icon' => 'signal', 'needles' => ['network', 'signal', 'connectivity']],
            ['icon' => 'bluetooth', 'needles' => ['bluetooth', 'ble']],
            ['icon' => 'lock', 'needles' => ['lock', 'password', 'secure', 'pin']],
            ['icon' => 'settings', 'needles' => ['setting', 'config', 'setup', 'calibrat']],
            ['icon' => 'cpu', 'needles' => ['device name', 'node name', 'device id', 'smart fan', 'device'], 'word' => ['name'], 'exact' => ['name', 'device']],
            ['icon' => 'file', 'needles' => ['parameter', 'register', 'hex addr', 'hex', 'addr', 'frame'], 'word' => ['param', 'reg']],
            ['icon' => 'code', 'needles' => ['cmd', 'console', 'terminal']],
            ['icon' => 'edit', 'needles' => ['enter text', 'textbox', 'input']],
            ['icon' => 'play', 'needles' => ['execute', 'run', 'start']],
            ['icon' => 'zap', 'needles' => ['command', 'trigger']],
            ['icon' => 'bell', 'needles' => ['notification', 'alarm', 'alert', 'toast']],
            ['icon' => 'menu', 'needles' => ['dropdown', 'drop down', 'select', 'option']],
            ['icon' => 'eye', 'needles' => ['display', 'monitor', 'view', 'readout']],
            ['icon' => 'plug', 'needles' => ['plug', 'socket', 'outlet']],
            ['icon' => 'volume-2', 'needles' => ['volume', 'sound', 'audio', 'mute']],
            ['icon' => 'battery', 'needles' => ['battery', 'charge']],
            ['icon' => 'radio', 'needles' => ['sensor', 'probe', 'telemetry']],
            ['icon' => 'target', 'needles' => ['target', 'focus', 'aim']],
            ['icon' => 'bolt', 'needles' => ['system power', 'power supply', 'mains power', 'electric', 'voltage', 'watt', 'energy', 'relay'], 'word' => ['power']],
        ];
        foreach ($rules as $rule) {
            foreach ($rule['needles'] as $needle) {
                if ($needle !== '' && (strpos($hay, $needle) !== false || ($id !== '' && strpos($id, str_replace(' ', '', $needle)) !== false))) {
                    return $rule['icon'];
                }
            }
            if (!empty($rule['exact']) && is_array($rule['exact'])) {
                foreach ($rule['exact'] as $exact) {
                    $exactCompact = str_replace(' ', '', (string) $exact);
                    if ($hay === $exact || $compact === $exactCompact) {
                        return $rule['icon'];
                    }
                }
            }
            if (!empty($rule['word']) && is_array($rule['word'])) {
                foreach ($rule['word'] as $word) {
                    if ($word !== '' && preg_match('/\b' . preg_quote($word, '/') . '\b/', $hay)) {
                        return $rule['icon'];
                    }
                }
            }
        }

        return '';
    }

    private static function isIconHintNoise($name)
    {
        $raw = strtolower(trim((string) $name));
        $hay = trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/', ' ', $raw)));
        if ($hay === '') {
            return true;
        }
        $noise = [
            'text', 'button', 'push', 'toggle', 'switch', 'slider', 'dropdown', 'select',
            'display', 'trigger', 'command', 'response', 'error', 'data', 'cmd', 'console',
            'entertext', 'textfield', 'notification', 'auto', 'ui', 'api', 'value', 'none',
            'read', 'write', 'readonly', 'hex', 'byte', 'frame',
        ];
        $tokens = preg_split('/\s+/', $hay) ?: [];
        foreach ($tokens as $token) {
            if ($token === '') {
                continue;
            }
            if (preg_match('/^[0-9]+$/', $token) || preg_match('/^[0-9a-f]{1,4}$/', $token)) {
                continue;
            }
            if (in_array($token, $noise, true)) {
                continue;
            }

            return false;
        }

        return true;
    }

    private static function defaultIconForControl(array $control)
    {
        $type = strtolower((string) self::canonicalControlTypeToken($control));
        $id = strtolower((string) (isset($control['id']) ? $control['id'] : ''));

        // Field/parameter names first so parent groups like POWER do not stamp every child.
        $genericMeta = ['data' => true, 'trigger' => true, 'command' => true, 'response' => true, 'error' => true, 'none' => true, '' => true];
        $fieldDesc = trim((string) (isset($control['protocolMeta']['field_description']) ? $control['protocolMeta']['field_description'] : ''));
        $fieldName = trim((string) (isset($control['protocolMeta']['field_name']) ? $control['protocolMeta']['field_name'] : ''));
        $parts = [];
        if ($fieldDesc !== '' && empty($genericMeta[strtolower($fieldDesc)])) {
            $parts[] = $fieldDesc;
        }
        if ($fieldName !== '' && empty($genericMeta[strtolower($fieldName)])) {
            $parts[] = $fieldName;
        }
        foreach ([
            isset($control['label']) ? (string) $control['label'] : '',
            isset($control['fieldLabel']) ? (string) $control['fieldLabel'] : '',
            isset($control['displayValue']) ? (string) $control['displayValue'] : '',
            isset($control['value']) ? (string) $control['value'] : '',
        ] as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate === '' || self::isIconHintNoise($candidate)) {
                continue;
            }
            if ((isset($control['displayValue']) && $candidate === trim((string) $control['displayValue']))
                || (isset($control['value']) && $candidate === trim((string) $control['value']))) {
                if (!preg_match('/[a-z]/i', $candidate)) {
                    continue;
                }
            }
            $parts[] = $candidate;
        }

        $groupName = (string) self::firstNonEmpty(
            isset($control['parameterName']) ? $control['parameterName'] : null,
            isset($control['name']) ? $control['name'] : null
        );
        $hasSemantic = false;
        foreach ($parts as $part) {
            if (!self::isIconHintNoise($part)) {
                $hasSemantic = true;
                break;
            }
        }

        foreach ($parts as $part) {
            $hit = self::matchIconKeywordRules($part, $id);
            if ($hit !== '') {
                return $hit;
            }
        }
        $combined = trim(implode(' ', array_filter($parts, static function ($part) {
            return trim((string) $part) !== '' && !self::isIconHintNoise($part);
        })));
        if ($combined !== '') {
            $combinedHit = self::matchIconKeywordRules($combined, $id);
            if ($combinedHit !== '') {
                return $combinedHit;
            }
        }
        if (!$hasSemantic) {
            $groupHit = self::matchIconKeywordRules($groupName, '');
            // Parent group POWER must not stamp bolt onto text/button/slider children.
            $powerishType = strpos($type, 'toggle') !== false
                || strpos($type, 'switch') !== false
                || strpos($type, 'power') !== false;
            if ($groupHit === 'bolt' && !$powerishType) {
                $groupHit = '';
            }
            if ($groupHit !== '') {
                return $groupHit;
            }
        }

        if (strpos($type, 'brightness') !== false) {
            return 'sun';
        }
        if (strpos($type, 'airflow') !== false) {
            return 'air-flow';
        }
        if (strpos($type, 'cct') !== false) {
            return 'thermometer';
        }
        if (strpos($type, 'saturation') !== false) {
            return 'aperture';
        }
        if (strpos($type, 'hue') !== false) {
            return 'palette';
        }
        if (strpos($type, 'notification') !== false) {
            return 'bell';
        }
        if (strpos($type, 'dropdown') !== false || strpos($type, 'select') !== false) {
            return 'menu';
        }
        if (strpos($type, 'timer') !== false || strpos($id, 'timer') !== false) {
            return 'clock';
        }
        if (strpos($type, 'slider') !== false || strpos($type, 'gauge') !== false) {
            return 'sliders';
        }
        if (strpos($type, 'toggle') !== false || strpos($type, 'switch') !== false) {
            return 'bolt';
        }
        if (strpos($type, 'push') !== false) {
            return 'zap';
        }
        if (strpos($type, 'button') !== false) {
            return 'play';
        }
        if (strpos($type, 'trigger') !== false) {
            return 'zap';
        }
        if (strpos($type, 'cmd') !== false || strpos($type, 'console') !== false) {
            return 'code';
        }
        if (strpos($type, 'enter') !== false || strpos($type, 'textfield') !== false) {
            return 'edit';
        }
        if (strpos($type, 'display') !== false || $type === 'text') {
            return 'eye';
        }
        if (strpos($type, 'text') !== false) {
            return 'file';
        }

        return 'cpu';
    }

    // ------------------------------------------------------------ validation

    /**
     * Recomputes the validation block against the document that is actually being
     * served. `valid` covers structure we can guarantee; `dataComplete` covers the
     * product_api values only the web team can supply.
     *
     * Also publishes an explicit transport contract:
     * - writeReady / deviceWriteSupported
     * - preferredTransport: device|cloud
     * Incomplete device frames keep Cloud UI writes, but must not be packed.
     */
    private static function recordValidation(array $schema, array $report)
    {
        $controls = isset($schema['controls']) && is_array($schema['controls']) ? $schema['controls'] : [];

        foreach ($controls as $index => $control) {
            if (!is_array($control)) {
                continue;
            }

            // Always publish an explicit writeReady boolean for writable controls.
            // Null/absent flags made the app attempt device-frame packing and then
            // fail with "incomplete protocol frame".
            if (!self::isWritableControl($control)) {
                $control = self::applyWriteTransportFlags($control, false, []);
                $schema['controls'][$index] = $control;
                continue;
            }

            $missing = self::missingWriteFields($control);
            if (empty($missing)) {
                $control = self::applyWriteTransportFlags($control, true, []);
                $schema['controls'][$index] = $control;
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

            // The app must not guess these bytes - mark not write-ready.
            // Keep UI access flags intact so Cloud parameter binding still works.
            // Drop empty/zero protocol-id slots so the app does not attempt packing
            // an incomplete CommandId/Function Code frame. Do not invent replacements.
            $control = self::stripUnusableProtocolIdSlots($control, $report);
            $control = self::applyWriteTransportFlags($control, false, $missing);
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

    /**
     * Publish strict transport flags + cloud/device write hints on one control.
     * When device write is unsupported, remove every action.type=send (top-level
     * events, nested widgetTree/props, sendButton.action) so the app cannot
     * attempt frame packing. Keep action.type=update and all bindings/keys.
     *
     * @param string[] $missing
     */
    private static function applyWriteTransportFlags(array $control, $deviceReady, array $missing)
    {
        $deviceReady = $deviceReady === true;
        $missing = array_values(array_map('strval', $missing));
        $binding = trim((string) (isset($control['binding']) ? $control['binding'] : ''));
        if ($binding === '' && isset($control['parameterKey']) && trim((string) $control['parameterKey']) !== '') {
            $binding = 'device.parameters.' . trim((string) $control['parameterKey']);
        }

        $control['writeReady'] = $deviceReady;
        $control['deviceWriteSupported'] = $deviceReady;
        $control['preferredTransport'] = $deviceReady ? 'device' : 'cloud';
        $control['missingFrameValues'] = $missing;

        $control['cloudWrite'] = [
            'enabled' => true,
            'actionType' => 'update',
            'binding' => $binding,
            'transport' => 'cloud',
        ];
        $control['deviceWrite'] = [
            'enabled' => $deviceReady,
            'actionType' => 'send',
            'dataSource' => 'deviceCommands',
            'framePath' => 'deviceFrame',
            'transport' => 'device',
        ];

        if (!$deviceReady) {
            // Invariant: never publish send while the frame is incomplete.
            // Walk the whole control tree (events / component / props / sendButton).
            $control = self::stripDeviceSendTriggers($control, $binding);
        }

        return $control;
    }

    /**
     * Recursively neutralize device-frame send triggers on a cloud/incomplete
     * control. Does not rename keys, remove parameters, or invent protocol bytes.
     * - events with action.type=send → action.type=update (same binding)
     * - sendButton.action.type=send → update
     * - bare action.type=send nodes → update
     */
    private static function stripDeviceSendTriggers(array $node, $fallbackBinding = '')
    {
        $fallbackBinding = trim((string) $fallbackBinding);

        if (isset($node['events']) && is_array($node['events'])) {
            $node['events'] = self::rewriteSendEventsList($node['events'], $fallbackBinding);
        }

        if (isset($node['sendButton']) && is_array($node['sendButton'])) {
            $node['sendButton'] = self::downgradeSendActionNode($node['sendButton'], $fallbackBinding);
        }

        if (isset($node['action']) && is_array($node['action'])) {
            $node['action'] = self::downgradeSendActionNode($node['action'], $fallbackBinding);
        } elseif (isset($node['type']) && strtolower(trim((string) $node['type'])) === 'send'
            && (isset($node['binding']) || $fallbackBinding !== '')
        ) {
            // Bare action object: {type:send, binding:...}
            $node['type'] = 'update';
            if (!isset($node['binding']) || trim((string) $node['binding']) === '') {
                $node['binding'] = $fallbackBinding;
            }
        }

        foreach ($node as $key => $child) {
            if (!is_array($child)) {
                continue;
            }
            if ($key === 'events' || $key === 'sendButton' || $key === 'action') {
                continue;
            }
            // Do not rewrite protocol frame documents or meta transport contracts.
            if ($key === 'deviceFrame' || $key === 'protocolMeta' || $key === 'commands'
                || $key === 'cloudWrite' || $key === 'deviceWrite' || $key === 'missingFrameValues'
            ) {
                continue;
            }
            $node[$key] = self::stripDeviceSendTriggers($child, $fallbackBinding);
        }

        return $node;
    }

    /**
     * @param mixed $events
     * @return mixed
     */
    private static function rewriteSendEventsList($events, $fallbackBinding = '')
    {
        if (!is_array($events)) {
            return $events;
        }

        $keys = array_keys($events);
        $isList = $keys === [] || $keys === range(0, count($keys) - 1);
        if ($isList) {
            $out = [];
            foreach ($events as $event) {
                if (!is_array($event)) {
                    $out[] = $event;
                    continue;
                }
                $out[] = self::downgradeSendEvent($event, $fallbackBinding);
            }

            return array_values($out);
        }

        foreach ($events as $eventName => $event) {
            if (!is_array($event)) {
                continue;
            }
            $events[$eventName] = self::downgradeSendEvent($event, $fallbackBinding);
        }

        return $events;
    }

    private static function downgradeSendEvent(array $event, $fallbackBinding = '')
    {
        if (isset($event['action']) && is_array($event['action'])) {
            $event['action'] = self::downgradeSendActionNode($event['action'], $fallbackBinding);
        }

        return $event;
    }

    private static function downgradeSendActionNode(array $action, $fallbackBinding = '')
    {
        $type = strtolower(trim((string) (isset($action['type']) ? $action['type'] : '')));
        if ($type === 'send') {
            $action['type'] = 'update';
            $binding = trim((string) (isset($action['binding']) ? $action['binding'] : ''));
            if ($binding === '' && trim((string) $fallbackBinding) !== '') {
                $action['binding'] = trim((string) $fallbackBinding);
            }
        }
        if (isset($action['action']) && is_array($action['action'])) {
            $action['action'] = self::downgradeSendActionNode($action['action'], $fallbackBinding);
        }
        // Nested sendButton inside props blobs.
        if (isset($action['sendButton']) && is_array($action['sendButton'])) {
            $action['sendButton'] = self::downgradeSendActionNode($action['sendButton'], $fallbackBinding);
        }

        return $action;
    }

    /**
     * Remove CommandId / Function Code / ActionId slots that are empty or zero
     * padding when the control is not device-write-ready. Leaves Data/Terminator
     * intact for Cloud binding. Never invents replacement protocol bytes.
     */
    private static function stripUnusableProtocolIdSlots(array $control, array &$report)
    {
        $controlId = trim((string) (isset($control['id']) ? $control['id'] : 'control'));
        $frameKeys = [];
        if (isset($control['deviceFrame']['commands']) && is_array($control['deviceFrame']['commands'])) {
            $frameKeys[] = 'deviceFrame';
        }
        if (isset($control['protocolMeta']['frame']['commands']) && is_array($control['protocolMeta']['frame']['commands'])) {
            $frameKeys[] = 'protocolMeta.frame';
        }

        foreach ($frameKeys as $key) {
            if ($key === 'deviceFrame') {
                $commands = $control['deviceFrame']['commands'];
            } else {
                $commands = $control['protocolMeta']['frame']['commands'];
            }
            $kept = [];
            $removed = false;
            foreach ($commands as $field) {
                if (!is_array($field)) {
                    $kept[] = $field;
                    continue;
                }
                $name = self::frameFieldName($field);
                if (!in_array($name, ['commandid', 'functioncode', 'actionid'], true)) {
                    $kept[] = $field;
                    continue;
                }
                $value = isset($field['value']) ? $field['value'] : null;
                if ($value === null || $value === '' || $value === []) {
                    $value = isset($field['asperui_value']) ? $field['asperui_value'] : null;
                }
                if ($value !== null && $value !== '' && $value !== [] && !self::isZeroProtocolId($value)) {
                    $kept[] = $field;
                    continue;
                }
                $removed = true;
                $report['droppedFrameSlots'][] = $controlId . '.commands.' . (self::frameFieldLabel($field) ?: $name);
            }
            if (!$removed) {
                continue;
            }
            $kept = array_values($kept);
            if ($key === 'deviceFrame') {
                $control['deviceFrame']['commands'] = $kept;
            } else {
                $control['protocolMeta']['frame']['commands'] = $kept;
            }
        }

        return $control;
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
        $definedSlots = [];
        $hasPayload = false;
        foreach ($commands as $field) {
            if (!is_array($field)) {
                continue;
            }
            $name = self::frameFieldName($field);
            $value = isset($field['value']) ? $field['value'] : null;
            if ($value === null || $value === '' || $value === []) {
                $value = isset($field['asperui_value']) ? $field['asperui_value'] : null;
            }
            $isProtocolSlot = in_array($name, ['commandid', 'functioncode', 'actionid', 'terminator'], true);
            if ($isProtocolSlot) {
                $definedSlots[$name] = true;
            }
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            // Placeholder 0x00 / 00 protocol ids cannot be packed by the app.
            // Treat them as missing so writeReady stays false (do not invent real ids).
            if (in_array($name, ['commandid', 'functioncode', 'actionid'], true)
                && self::isZeroProtocolId($value)
            ) {
                continue;
            }
            $present[$name] = true;
            if (!$isProtocolSlot && !self::isZeroProtocolId($value)) {
                $hasPayload = true;
            }
        }

        // protocolMeta may hold a non-zero id even when the frame slot is still 00.
        $meta = isset($control['protocolMeta']) && is_array($control['protocolMeta'])
            ? $control['protocolMeta']
            : [];
        foreach ([
            'commandid' => ['command_id', 'commandId'],
            'functioncode' => ['function_code', 'functionCode'],
            'actionid' => ['action_id', 'actionId'],
        ] as $slot => $keys) {
            if (isset($present[$slot])) {
                continue;
            }
            foreach ($keys as $key) {
                if (!array_key_exists($key, $meta)) {
                    continue;
                }
                $metaVal = $meta[$key];
                if ($metaVal === null || $metaVal === '' || $metaVal === []) {
                    continue;
                }
                if (!self::isZeroProtocolId($metaVal)) {
                    $present[$slot] = true;
                    break;
                }
            }
        }

        $missing = [];
        // Require a protocol id only when that slot is part of the authored frame.
        // A frame whose command bytes live entirely in Data (no CommandId slot)
        // is complete — do not invent CommandId or Function Code.
        if (isset($definedSlots['commandid'])) {
            if (!isset($present['commandid'])) {
                $missing[] = 'CommandId';
            }
        }
        if (isset($definedSlots['functioncode'])) {
            if (!isset($present['functioncode'])) {
                $missing[] = 'Function Code';
            }
        }
        if (!isset($definedSlots['commandid']) && !isset($definedSlots['functioncode']) && !$hasPayload) {
            $missing[] = 'CommandId';
        }

        return $missing;
    }

    /**
     * True when a protocol id is only zero padding (00 / 0x00 / 0000).
     * Empty values are not zero-padding.
     */
    private static function isZeroProtocolId($value)
    {
        if ($value === null || $value === '' || $value === []) {
            return false;
        }
        $hex = strtoupper(preg_replace('/[^0-9A-F]/', '', (string) $value));

        return $hex !== '' && preg_match('/^0+$/', $hex) === 1;
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
        // Ready and Write allow send; Read is display-only.
        if ($access === 'ready' || strpos($access, 'ready') !== false) {
            return true;
        }

        return strpos($access, 'write') !== false || $access === 'w' || $access === 'rw';
    }

    private static function buildWarnings(array $report)
    {
        $warnings = [];

        foreach ($report['missingFrameValues'] as $entry) {
            $warnings[] = $entry['controlId'] . ': device write unavailable (use Cloud), schema is missing '
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
