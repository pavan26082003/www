<?php

namespace App\Libraries;

/**
 * Production IoT SDUI schema builder shared by CodeIgniter and standalone iot_api_creation.php.
 */
class IotSduiSchemaBuilder
{
    public function build(array $devices, int $userId = 0, string $accessToken = '', string $apiBaseUrl = '', string $ciBase = ''): array
    {
        $online = 0;
        $offline = 0;
        foreach ($devices as $d) {
            if (!empty($d['online'])) {
                $online++;
            } else {
                $offline++;
            }
        }

        $devices = $this->sortDevicesOnlineFirst($devices);
        if ($apiBaseUrl === '') {
            $apiBaseUrl = $this->detectPublicApiBaseUrl();
        }
        if ($ciBase === '') {
            $ciBase = $apiBaseUrl;
        }

        // schemaUrl is used by the web dashboard UI as a display/copy hint only.
        // The token is included so users can open it directly from the dashboard.
        $tokenQs = $accessToken !== '' ? '&access_token=' . rawurlencode($accessToken) : '';
        $schemaUrl = $apiBaseUrl . '?action=get_iot_schema' . $tokenQs;

        return $this->composeSduiSchema(
            $devices,
            $userId,
            $online,
            $offline,
            $apiBaseUrl,
            $ciBase,
            $schemaUrl
        );
    }

    public static function isSduiCapable(?array $schema): bool
    {
        if (!is_array($schema) || $schema === []) {
            return false;
        }
        if (empty($schema['theme']) || empty($schema['typography'])) {
            return false;
        }
        if (empty($schema['page']['header'])) {
            return false;
        }
        if (empty($schema['dataSources']['getDevices'])) {
            return false;
        }
        if (empty($schema['dataSources']['getAllNodesStatus'])) {
            return false;
        }
        if (empty($schema['icons']['catalog']) || empty($schema['icons']['deviceTypeMap'])) {
            return false;
        }
        if (empty($schema['deviceFields'])) {
            return false;
        }
        if (!isset($schema['devices']) || !is_array($schema['devices'])) {
            return false;
        }

        $components = $schema['components'] ?? array();
        if (!is_array($components) || count($components) < 2) {
            return false;
        }

        $types = array();
        foreach ($components as $component) {
            if (is_array($component) && !empty($component['type'])) {
                $types[] = (string) $component['type'];
            }
        }

        if (empty($schema['nodeFields'])) {
            return false;
        }
        if (empty($schema['metrics'])) {
            return false;
        }
        if (empty($schema['auth']['tokenBinding'])) {
            return false;
        }
        if (version_compare((string) ($schema['schemaVersion'] ?? '0'), '2.4.3', '<')) {
            return false;
        }
        $powerToggle = $schema['dataSources']['powerToggle'] ?? null;
        $powerBody = is_array($powerToggle) ? ($powerToggle['nativeApi']['body'] ?? null) : null;
        if (!is_array($powerBody) || trim((string) ($powerBody['node_id'] ?? '')) === '') {
            return false;
        }
        $powerEnabled = is_array($powerToggle) ? (string) ($powerToggle['enabledWhen'] ?? '') : '';
        if ($powerEnabled === '' || $powerEnabled === '{{device.online}}' || $powerEnabled === 'device.online') {
            return false;
        }

        $hasLiveStatusGrid = false;
        $hasPowerToggleAction = false;
        foreach ($components as $component) {
            if (!is_array($component) || ($component['type'] ?? '') !== 'deviceGrid') {
                continue;
            }
            if (!empty($component['liveStatusSource']) && !empty($component['refreshWith'])) {
                $fields = $component['card']['fields'] ?? array();
                if (isset($fields['model_no'])) {
                    $hasLiveStatusGrid = true;
                }
                $toggle = $fields['powerToggle'] ?? array();
                if (($toggle['onTap']['action'] ?? '') === 'callApi' && ($toggle['onTap']['source'] ?? '') === 'powerToggle') {
                    $hasPowerToggleAction = true;
                }
            }
        }

        return in_array('summaryCard', $types, true)
            && in_array('deviceGrid', $types, true)
            && $hasLiveStatusGrid
            && $hasPowerToggleAction;
    }

    public function sortDevicesOnlineFirst(array $devices): array
    {
        usort($devices, function (array $a, array $b): int {
            $aOnline = !empty($a['online']) ? 1 : 0;
            $bOnline = !empty($b['online']) ? 1 : 0;
            if ($aOnline !== $bOnline) {
                return $bOnline <=> $aOnline;
            }

            $aName = strtolower((string) ($a['product_name'] ?? $a['productName'] ?? $a['name'] ?? ''));
            $bName = strtolower((string) ($b['product_name'] ?? $b['productName'] ?? $b['name'] ?? ''));
            return $aName <=> $bName;
        });

        return $devices;
    }

    public function detectPublicApiBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        // Issue #1: use the same canonical v8/ path as detectV8DeviceApiBaseUrl so all
        // dataSources resolve to one base URL — no mixing of /iot_api_creation.php and /v8/iot_api_creation.php.
        if ($docRoot !== '' && is_file($docRoot . '/v8/iot_api_creation.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/v8/iot_api_creation.php';
        }
        if ($docRoot !== '' && is_file($docRoot . '/iot_api_creation.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/iot_api_creation.php';
        }

        return 'https://efactor.tech/v8/iot_api_creation.php';
    }

    public function detectV8DeviceApiBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        // check_device, add_wifi_node, remove_node are handled by iot_api_creation.php.
        // Prefer it over the legacy api.php so the app hits the right endpoint.
        if ($docRoot !== '' && is_file($docRoot . '/v8/iot_api_creation.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/v8/iot_api_creation.php';
        }
        if ($docRoot !== '' && is_file($docRoot . '/v8/api.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/v8/api.php';
        }
        if ($docRoot !== '' && is_file($docRoot . '/api.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/api.php';
        }

        return 'https://efactor.tech/v8/iot_api_creation.php';
    }

    /**
     * Production SDUI schema — layout tokens, typography, icons, datasources, components.
     */
    private function composeSduiSchema(
        array $devices,
        int $userId,
        int $online,
        int $offline,
        string $v8Base,
        string $ciBase,
        string $schemaUrl
    ): array {
        $total = count($devices);
        $deviceApiBase = $this->detectV8DeviceApiBaseUrl();
        $appHomeBase = $this->detectAppHomeApiBaseUrl($ciBase);
        $onboarding = $this->buildDeviceOnboardingSpec($deviceApiBase, $ciBase);
        $tokenBinding = array('$secureState' => 'session.iotAccessToken');
        $authQuery = array(
            'type'       => 'header',
            'headerName' => 'Authorization',
            'binding'    => $tokenBinding,
            // Token goes in Authorization header only — no query param, no body field.
        );

        $schema = array(
            'schemaVersion'     => '2.4.3',
            'kind'              => 'efactor.iot.sdui.dashboard',
            'generatedAt'       => date('c'),
            'generatedDate'     => date('Y-m-d'),
            'generatedTime'     => date('H:i:s'),
            'generatedTimezone' => date_default_timezone_get(),
            'userId'            => $userId ?: null,
            'deviceCount'       => $total,

            // ── Responsive contract ──────────────────────────────────────────
            // Flutter reads these breakpoints + layout rules locally.
            // One schema serves all devices/orientations — no per-model variants.
            // All values are logical pixels (dp). safeArea is always on.
            'responsive' => array(
                'version'          => '1.0',
                'unit'             => 'logicalPx',
                'dimensionPolicy'  => 'logical',
                'scaleLegacyDimensions' => false,
                'safeArea'         => true,
                'breakpoints' => array(
                    // Keep keys; shift boundary so padded 360dp phones stay in compact.
                    'narrow'   => array('minimumWidth' =>   0, 'maximumWidth' => 319),
                    'compact'  => array('minimumWidth' => 320, 'maximumWidth' => 599),
                    'medium'   => array('minimumWidth' => 600, 'maximumWidth' => 839),
                    'expanded' => array('minimumWidth' => 840),
                ),
                'layouts' => array(
                    'narrow'   => array(
                        'pagePadding'     => array('horizontal' => 10, 'top' => 12, 'bottom' => 108),
                        'maxContentWidth' => null,
                    ),
                    'compact'  => array(
                        'pagePadding'     => array('horizontal' => 12, 'top' => 14, 'bottom' => 108),
                        'maxContentWidth' => null,
                    ),
                    'medium'   => array(
                        'pagePadding'     => array('horizontal' => 20, 'top' => 16, 'bottom' => 32),
                        'maxContentWidth' => 720,
                    ),
                    'expanded' => array(
                        'pagePadding'     => array('horizontal' => 32, 'top' => 20, 'bottom' => 40),
                        'maxContentWidth' => 1024,
                    ),
                ),
            ),

            'auth' => array(
                'type'         => 'iot-access-token',
                // Token is sent in the Authorization header only — never in query params or body.
                'headerName'   => 'Authorization',
                'tokenBinding' => $tokenBinding,
                'note'         => 'Token is resolved at runtime from session.iotAccessToken — never hardcode JWT in published schema.',
            ),

            'hostChrome' => array(
                'reuseHostAppShell' => true,
                'keepHostBottomNavigation' => true,
                'replaceAppShell' => false,
                'bottomNavigationFixed' => true,
                'bottomNavigationOutsideScroll' => true,
                'reservedBottomInset' => 108,
            ),
            'appShell' => array(
                'reuseHostShell' => true,
                'replaceOnNestedSchema' => false,
                'keepHostBottomNavigation' => true,
                'bottomNavigationFixed' => true,
                'bottomNavigationOutsidePageScroll' => true,
                'bottomNavigationPosition' => 'fixed',
                'contentAvoidsBottomNavigation' => true,
                'reservedBottomInset' => 108,
            ),
            'layout' => array(
                'engine'      => 'sdui',
                'pagePadding' => array('horizontal' => 12, 'top' => 14, 'bottom' => 108),
                'grid'        => array('columns' => 2, 'gap' => 12),
                'card'        => array(
                    'minHeight'    => 168,
                    'borderRadius' => 24,
                    'padding'      => array('top' => 14, 'right' => 12, 'bottom' => 12, 'left' => 12),
                    'zoneSpacing'  => array(
                        'topRow'       => array('marginBottom' => 10),
                        'titleBlock'   => array('marginBottom' => 6),
                        'productImage' => array('paddingTop' => 4, 'paddingBottom' => 6, 'minHeight' => 48),
                        'statusRow'    => array('marginBottom' => 8),
                        'footer'       => array('paddingTop' => 2, 'gap' => 8),
                    ),
                ),
                // Breakpoint-specific page-level overrides — Flutter deep-merges the
                // selected entry into the base layout above.
                'responsiveLayout' => array(
                    'narrow'   => array('pagePadding' => array('horizontal' => 10, 'top' => 12, 'bottom' => 24)),
                    'compact'  => array('pagePadding' => array('horizontal' => 12, 'top' => 14, 'bottom' => 28)),
                    'medium'   => array(
                        'pagePadding'     => array('horizontal' => 20, 'top' => 16, 'bottom' => 32),
                        'maxContentWidth' => 720,
                    ),
                    'expanded' => array(
                        'pagePadding'     => array('horizontal' => 32, 'top' => 20, 'bottom' => 40),
                        'maxContentWidth' => 1024,
                    ),
                ),
            ),

            'theme' => $this->getSduiThemeTokens(),

            'typography' => $this->getSduiTypographyTokens(),

            'icons' => array(
                'smartHomeHeader' => $this->getSduiSmartHomeIconSpec(),
                'wifiSignal'      => $this->getSduiWifiIconSpec(),
                'connectionArt'   => $this->getSduiWifiIconSpec(array(
                    'id' => 'connection-art',
                    'containerSize' => 58, 'iconSize' => 28, 'borderRadius' => 18,
                    'paths' => array(
                        'M5 12.55a11 11 0 0 1 14.08 0',
                        'M1.42 9a16 16 0 0 1 21.16 0',
                        'M8.53 16.11a6 6 0 0 1 6.95 0',
                    ),
                )),
                'addDevice'       => $this->getAddDeviceIconSpec(),
                'search'          => $this->getSearchIconSpec(),
                'qrcode'          => $this->getQrCodeIconSpec(),
                'catalog'         => $this->getSduiDeviceIconCatalog(),
                'deviceTypeMap'   => $this->getSduiDeviceIconMap(),
            ),

            'ui' => $onboarding['ui'],
            'overlays' => $onboarding['overlays'],

            'deviceFields' => \App\Libraries\IotDeviceNormalizer::deviceFieldCatalog(),
            'nodeFields'   => \App\Libraries\IotNodesStatusBuilder::nodeFieldCatalog(),

            'schemaContract' => array(
                'version'    => '2.4.3',
                'required'   => array(
                    'schemaVersion', 'kind', 'auth', 'responsive', 'layout', 'theme', 'typography', 'icons', 'ui', 'overlays',
                    'deviceFields', 'nodeFields', 'page', 'dataSources', 'components', 'devices', 'metrics', 'infoPage',
                ),
                'dataSources'=> array(
                    // Issue #6: refreshDevices now calls the same endpoint as getDevices (v8 get_iot_devices).
                    // addWifiNode.refreshAfter no longer calls refreshDevices — it calls getDevices directly.
                    'getDevices', 'getAllNodesStatus', 'getSchema', 'cardClick', 'powerToggle',
                    'checkDevice', 'addWifiNode', 'refreshDevices', 'removeDevice',
                ),
                'components' => array('summaryCard', 'deviceGrid'),
                'overlays'   => array('qrScannerModal'),
                'interactions' => array(
                    'addDevice' => $onboarding['interaction'],
                    'manualNodeId' => $onboarding['manualInteraction'],
                    'powerToggle' => array(
                        'type'            => 'callApi',
                        'dataSource'      => 'powerToggle',
                        'trigger'         => 'deviceGrid.card.fields.powerToggle.onTap',
                        'control'         => 'powerApiToggle',
                        'statePath'       => 'device.power',
                        'alias'           => 'device.powerOn',
                        'enabledWhen'     => 'device.node_id != ""',
                        'stopPropagation' => true,
                        'refreshAfter'    => array('getDevices', 'getAllNodesStatus'),
                        'mutation'        => array(
                            'action' => 'device_power_toggle',
                            'method' => 'POST',
                            'body'   => array(
                                'node_id' => '{{device.node_id}}',
                                'command' => '{{toggle_next|off:on}}',
                                'power'   => '{{toggle_next}}',
                            ),
                        ),
                        'note'            => 'Functional control — POST device_power_toggle via dataSources.powerToggle. Binds to iot-toggle / powerApiToggle UI.',
                    ),
                    // Issue #3: removeDevice must be declared consistently in every location.
                    // body.node_id = runtime {{device.node_id}}, requestSchema binding, native Authorization.
                    'removeDevice' => array(
                        'type'               => 'callApi',
                        'dataSource'         => 'removeDevice',
                        'trigger'            => 'infoPage.screens.gatewayDetails.footerActions.deleteDevice',
                        'control'            => 'device_info_delete_button',
                        'confirmBeforeExecute'=> true,
                        'confirmDialog' => array(
                            'title'        => 'Delete Device',
                            'message'      => 'Remove this device from your account? This cannot be undone.',
                            'confirmLabel' => 'Delete',
                            'cancelLabel'  => 'Cancel',
                            'confirmStyle' => 'destructive',
                        ),
                        'method'             => 'POST',
                        'headers'            => array(
                            'Content-Type'  => 'application/json',
                            'Authorization' => $tokenBinding,
                        ),
                        'body'               => array(
                            // Runtime binding — resolved from the tapped device, never a schema-time constant.
                            'node_id' => '{{device.node_id}}',
                        ),
                        'requestSchema' => array(
                            'type'       => 'object',
                            'required'   => array('node_id'),
                            'properties' => array(
                                'node_id' => array(
                                    'type'    => 'string',
                                    'binding' => 'device.node_id',
                                ),
                            ),
                        ),
                        'response' => array(
                            'successField' => 'success',
                            'statusField'  => 'status',
                            'messageField' => 'message',
                            'nodeIdField'  => 'node_id',
                            'devicesField' => 'devices',
                        ),
                        'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
                        // After successful delete: show alert and navigate back to IoT Devices page.
                        'onSuccess' => array(
                            'showAlert' => array(
                                'title'   => 'Device Deleted',
                                'message' => 'Device has been removed from your account.',
                                'style'   => 'success',
                            ),
                            'navigate' => array(
                                'action' => 'popToRoute',
                                'target' => 'iotDevices',
                                'route'  => '/iot/devices',
                            ),
                        ),
                    ),
                ),
            ),

            'page' => array(
                'id'      => 'iot-devices',
                'title'   => 'IoT Devices',
                'route'   => '/iot/devices',
                'layout'  => 'mobile-device-grid',
                'padding' => array('horizontal' => 12, 'top' => 14, 'bottom' => 28),
                'scrollable'       => true,
                'safeArea'         => true,
                'keyboardAvoidance'=> false,
                // Breakpoint overrides for the page container.
                'responsiveLayout' => array(
                    'narrow'   => array(
                        'padding'         => array('horizontal' => 10, 'top' => 12, 'bottom' => 24),
                        'maxContentWidth' => null,
                    ),
                    'compact'  => array(
                        'padding'         => array('horizontal' => 12, 'top' => 14, 'bottom' => 28),
                        'maxContentWidth' => null,
                    ),
                    'medium'   => array(
                        'padding'         => array('horizontal' => 20, 'top' => 16, 'bottom' => 32),
                        'maxContentWidth' => 720,
                    ),
                    'expanded' => array(
                        'padding'         => array('horizontal' => 32, 'top' => 20, 'bottom' => 40),
                        'maxContentWidth' => 1024,
                    ),
                ),

                'header' => array(
                    'eyebrow'  => 'Smart Home',
                    'title'    => 'My Devices',
                    'subtitle' => $total . ' products · ' . $online . ' online',
                    'subtitleBind' => array(
                        'template'    => '{{page.summary.total}} products · {{page.summary.online}} online',
                        'computeFrom' => 'devices',
                    ),
                    'style'    => array(
                        'padding'       => array('top' => 4, 'right' => 2, 'bottom' => 12, 'left' => 2),
                        'gap'           => 10,
                        'eyebrow'       => array('fontSize' => 12, 'fontWeight' => 600, 'lineHeight' => 1.2, 'color' => '#9ca3af'),
                        'title'         => array('fontSize' => 22, 'fontWeight' => 900, 'lineHeight' => 1.08, 'color' => '#111827'),
                        'subtitle'      => array('fontSize' => 12, 'fontWeight' => 400, 'lineHeight' => 1.3, 'color' => '#9ca3af'),
                        'avatarSize'    => 42,
                        'avatarRadius'  => 14,
                        'avatarIconSize'=> 22,
                    ),
                    'action' => $onboarding['headerAction'],
                    'search' => array(
                        'enabled'          => true,
                        'initiallyVisible' => false,
                        'icon'             => 'search',
                        'placeholder'      => 'Search devices',
                        'matchField'       => 'product_name',
                        'ariaLabel'        => 'Search devices by name',
                        'button' => array(
                            'id'        => 'searchDevices',
                            'type'      => 'iconButton',
                            'icon'      => 'search',
                            'ariaLabel' => 'Search devices',
                            'title'     => 'Search devices',
                            'placement' => array(
                                'zone'   => 'page.header',
                                'align'  => 'right',
                                'before' => 'addDevice',
                            ),
                            'onTap' => array(
                                'action' => 'toggle',
                                'target' => 'page.header.search',
                            ),
                        ),
                    ),
                    'avatar' => array(
                        'type'       => 'actionButton',
                        'icon'       => 'addDevice',
                        'control'    => 'addDeviceButton',
                        'background' => 'linear-gradient(145deg,#2563eb,#1d4ed8)',
                        'color'      => '#ffffff',
                        'ariaLabel'  => 'Scan QR to add device',
                        'onTap'      => array(
                            'action'    => 'openOverlay',
                            'overlayId' => 'qrScannerModal',
                        ),
                    ),
                ),

                'summary' => array(
                    'total'   => $total,
                    'online'  => $online,
                    'offline' => $offline,
                    'componentId' => 'iot-summary-card',
                ),
            ),

            'dataSources' => array(
                'getDevices' => array(
                    'id'       => 'getDevices',
                    'label'    => 'Fetch live device list',
                    'method'   => 'GET',
                    'url'      => $v8Base,
                    'query'    => array(
                        'action'       => 'get_iot_devices',
                        // Token binding — clients that cannot send headers use this query param fallback.
                        // The server accepts Authorization header OR ?access_token= query param.
                        'access_token' => $tokenBinding,
                    ),
                    'auth'     => $authQuery,
                    'response' => array(
                        'root' => 'devices',
                        'idField' => 'product_id',
                        'controlIdField' => 'node_id',
                        'sort' => 'onlineFirst',
                    ),
                ),
                'getAllNodesStatus' => array(
                    'id'       => 'getAllNodesStatus',
                    'label'    => 'Fetch live node connectivity and power states',
                    'method'   => 'GET',
                    'url'      => $v8Base,
                    'query'    => array(
                        'action'       => 'getAllNodesStatus',
                        'access_token' => $tokenBinding,
                    ),
                    'auth'     => $authQuery,
                    'response' => array(
                        'root'             => 'all_nodes',
                        'nodeIdField'      => 'node_id',
                        'connectivityPath' => 'status.connectivity.connected',
                        'devicesPath'      => 'devices',
                        'deviceIdField'    => 'id',
                        'devicePowerField' => 'power',
                        'mergeInto'        => 'devices',
                    ),
                ),
                'getSchema' => array(
                    'id'     => 'getSchema',
                    'label'  => 'Fetch saved dashboard schema',
                    'method' => 'GET',
                    'url'    => $v8Base,
                    'query'    => array(
                        'action'       => 'get_iot_schema',
                        'access_token' => $tokenBinding,
                    ),
                    'auth'   => $authQuery,
                    'response' => array('root' => 'schema', 'devicesPath' => 'schema.devices'),
                ),
                'cardClick' => array(
                    'id'     => 'cardClick',
                    'label'  => 'Device card tap event',
                    'method' => 'POST',
                    'url'    => $v8Base,
                    'query'  => array(
                        'action'       => 'device_card_click',
                        'access_token' => $tokenBinding,
                    ),
                    'auth'   => $authQuery,
                    'body'   => array('node_id' => '{{device.node_id}}'),
                ),
                'powerToggle' => array(
                    'id'     => 'powerToggle',
                    'label'  => 'Device power toggle command',
                    'kind'   => 'mutation',
                    'method' => 'POST',
                    'url'    => rtrim($ciBase, '/') . '/device-power-toggle',
                    'query'  => array(),
                    'headers' => array(
                        'Content-Type' => 'application/json',
                    ),
                    'auth'   => array(
                        'type' => 'session',
                        'note' => 'Web clients POST to logged-in session proxy with server-resolved token.',
                    ),
                    'body'   => array(
                        'node_id' => '{{device.node_id}}',
                        'command' => '{{toggle_next|off:on}}',
                        'power'   => '{{toggle_next}}',
                    ),
                    'response' => array(
                        'successField' => 'success',
                        'powerField'   => 'power',
                        'commandField' => 'command',
                        'nodeIdField'  => 'node_id',
                    ),
                    'webProxy' => array(
                        'method'        => 'POST',
                        'path'          => 'device-power-toggle',
                        'preferSession' => true,
                        'dashboardBase' => rtrim($ciBase, '/'),
                        'appHomeBase'   => $appHomeBase,
                    ),
                    'nativeApi' => array(
                        'method' => 'POST',
                        'url'    => $v8Base,
                        'query'  => array(
                            // Issue #7: Authorization header is used — no need for access_token in query.
                            'action' => 'device_power_toggle',
                        ),
                        'headers' => array(
                            'Content-Type'  => 'application/json',
                            'Authorization' => $tokenBinding,
                        ),
                        'body' => array(
                            'node_id' => '{{device.node_id}}',
                            'command' => '{{toggle_next|off:on}}',
                            'power'   => '{{toggle_next}}',
                        ),
                        'note' => 'Flutter native: Bearer token in Authorization header. JSON body is node_id, command, and power.',
                    ),
                    'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
                    'optimisticUpdate' => array(
                        'statePath' => 'device.power',
                        'alias'     => 'device.powerOn',
                        'mode'      => 'toggle',
                    ),
                    'bindContract' => array(
                        'statePath' => 'device.power',
                        'alias' => 'device.powerOn',
                        'valueType' => 'boolean',
                        'trueValues' => array(true, 1, '1', '01', 'true', 'yes', 'on'),
                        'falseValues' => array(false, 0, '0', 'false', 'no', 'off'),
                        'uiOnClass' => 'is-on',
                        'enabledWhen' => '{{device.node_id != ""}}',
                    ),
                    'enabledWhen' => '{{device.node_id != ""}}',
                    'deviceActionFallback' => 'device.actions.onPowerToggle',
                ),
                'checkDevice'    => $onboarding['dataSources']['checkDevice'],
                'addWifiNode'    => $onboarding['dataSources']['addWifiNode'],
                'refreshDevices' => $onboarding['dataSources']['refreshDevices'],
                // removeDevice — POST to the CI session proxy which resolves the token server-side,
                // calls v8/iot_api_creation.php?action=remove_node, and returns the refreshed device list.
                // Token goes in Authorization header only — never in URL or body.
                'removeDevice' => array(
                    'id'     => 'removeDevice',
                    'label'  => 'Remove device node from account',
                    'kind'   => 'mutation',
                    'method' => 'POST',
                    'url'    => rtrim($appHomeBase, '/') . '/remove-node',
                    'headers' => array(
                        'Content-Type' => 'application/json',
                    ),
                    'auth' => array(
                        'type'        => 'session',
                        'headerName'  => 'Authorization',
                        'credentials' => 'same-origin',
                        'note'        => 'CI session proxy — resolves token server-side from logged-in user.',
                    ),
                    'body' => array(
                        // Issue #2: node_id must be the runtime node of the device being removed,
                        // not any embedded device from schema generation time.
                        'node_id' => '{{device.node_id}}',
                    ),
                    // Machine-readable request contract: Flutter reads this to know what to send.
                    'requestSchema' => array(
                        'type'       => 'object',
                        'required'   => array('node_id'),
                        'properties' => array(
                            'node_id' => array(
                                'type'        => 'string',
                                'description' => 'The node_id of the device to remove. Must be the runtime device.node_id — never a schema-time constant.',
                                'binding'     => 'device.node_id',
                            ),
                        ),
                    ),
                    'response' => array(
                        'successField' => 'success',
                        'messageField' => 'message',
                        'statusField'  => 'status',
                        'nodeIdField'  => 'node_id',
                        'devicesField' => 'devices',
                        'totalField'   => 'total',
                        'onlineField'  => 'online',
                        'offlineField' => 'offline',
                    ),
                    'confirmBeforeExecute' => true,
                    'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
                    // Issue #3: webProxy for web/session clients; nativeApi for Flutter with token auth.
                    'webProxy' => array(
                        'method'        => 'POST',
                        'path'          => 'remove-node',
                        'preferSession' => true,
                        'appHomeBase'   => rtrim($appHomeBase, '/'),
                        'body'          => array('node_id' => '{{device.node_id}}'),
                    ),
                    'nativeApi' => array(
                        'method'  => 'POST',
                        'url'     => $deviceApiBase,
                        'query'   => array('action' => 'remove_node'),
                        'headers' => array(
                            'Content-Type'  => 'application/json',
                            // Token resolved at runtime from session.iotAccessToken — never hardcoded.
                            'Authorization' => $tokenBinding,
                        ),
                        'body'    => array('node_id' => '{{device.node_id}}'),
                        'note'    => 'Flutter native: Bearer token in Authorization header, node_id in JSON body only.',
                    ),
                    'note' => 'POST app-home-page/api/remove-node (web proxy) or v8/iot_api_creation.php?action=remove_node (native). Authorization header, JSON body {node_id}. Response includes refreshed devices[].',
                ),
            ),

            'components' => array(
                $this->buildSduiSummaryComponent($online, $total),
                $this->buildSduiDeviceGridComponent($v8Base, $tokenBinding),
            ),

            'devices' => $this->finalizeDevices($devices, $v8Base),

            // ── Device Info / Info Page ──────────────────────────────────────
            // The app opens this when the user taps the info button on any device card.
            // Flutter deep-merges per-device overrides from device-ui-schema/{model_no}/{node_id}.
            // All bindings are runtime-resolved — this block must never contain a hardcoded device id.
            'infoPage' => $this->buildInfoPageSpec($ciBase, $tokenBinding, $authQuery),
        );

        return $this->applyLiveMetrics($schema, $devices, $v8Base);
    }

    /**
     * Device info page spec — schema-driven detail screen opened when the user
     * taps the info button on any device card.
     *
     * Bindings use {{device.*}} — Flutter resolves them from the tapped device at runtime.
     * The per-device UI schema is fetched from device-ui-schema/{model_no}/{node_id}.
     * This block must never contain a hardcoded device id, node id, or access token.
     *
     * @param array<string, mixed> $tokenBinding
     * @param array<string, mixed> $authQuery
     */
    private function buildInfoPageSpec(string $ciBase, array $tokenBinding, array $authQuery): array
    {
        $ciBase = rtrim($ciBase, '/');

        return array(
            // Presentation mode for the info sheet — bottom sheet on phone, side panel on tablet.
            'presentation'   => 'bottomSheet',
            'entryScreen'    => 'gatewayDetails',
            'safeArea'       => true,

            // Responsive sizing — Flutter caps the sheet to available window.
            'responsiveLayout' => array(
                'narrow'   => array('initialChildSize' => 0.72, 'maxChildSize' => 0.95, 'minChildSize' => 0.4,  'maxWidth' => 'fill'),
                'compact'  => array('initialChildSize' => 0.65, 'maxChildSize' => 0.92, 'minChildSize' => 0.35, 'maxWidth' => 'fill'),
                'medium'   => array('initialChildSize' => 0.55, 'maxChildSize' => 0.88, 'minChildSize' => 0.3,  'maxWidth' => 540),
                'expanded' => array('initialChildSize' => 0.50, 'maxChildSize' => 0.85, 'minChildSize' => 0.25, 'maxWidth' => 600),
            ),

            'theme' => array(
                'sheetRadius'    => 24,
                'headerHeight'   => 56,
                'sectionGap'     => 12,
                'sectionRadius'  => 16,
                'contentPadding' => array('horizontal' => 16, 'vertical' => 12),
                'trailingMaxWidth' => 200,
            ),

            // Header shown at the top of the info sheet.
            'header' => array(
                'title'   => array('bind' => 'device.product_name', 'fallback' => 'Device Info'),
                'eyebrow' => array('bind' => 'device.brand_name',   'fallback' => ''),
                'avatar'  => array(
                    'type'          => 'image',
                    'src'           => '{{device.brandLogo}}',
                    'fallbackIcon'  => 'device',
                    'width'         => 36,
                    'height'        => 36,
                    'borderRadius'  => 10,
                ),
                'closeIcon' => 'x',
            ),

            // The per-device UI schema is fetched live from this URL.
            // Flutter substitutes {{device.model_no}} and {{device.node_id}} at runtime.
            'dataUrl' => $ciBase . '/device-ui-schema/{{device.model_no}}/{{device.node_id}}',

            // Web-session dataSource (same-origin cookie auth — for web clients).
            // Flutter must use nativeApi instead — it has no web session cookie.
            'dataSource' => array(
                'id'     => 'deviceInfo',
                'kind'   => 'query',
                'method' => 'GET',
                'url'    => $ciBase . '/device-ui-schema/{{device.model_no}}/{{device.node_id}}',
                'auth'   => array('type' => 'session', 'credentials' => 'same-origin'),
                'response' => array('successField' => 'success', 'schemaField' => 'schema'),
                'caching' => array('enabled' => true, 'ttlSeconds' => 120, 'strategy' => 'network-first'),
                // Issue #5: explicit platform selection — Flutter reads nativeApi, web reads the root entry.
                'nativeApi' => array(
                    'id'     => 'deviceInfoNative',
                    'kind'   => 'query',
                    'method' => 'GET',
                    'url'    => $ciBase . '/device-ui-schema/{{device.model_no}}/{{device.node_id}}',
                    'headers' => array(
                        'Authorization' => $tokenBinding,
                    ),
                    'auth'   => $authQuery,
                    'response' => array('successField' => 'success', 'schemaField' => 'schema'),
                    'caching' => array('enabled' => true, 'ttlSeconds' => 120, 'strategy' => 'network-first'),
                    'note'   => 'Flutter native: Bearer token in Authorization header. No session cookie required.',
                ),
            ),

            // Field bindings for the detail rows — resolved from the tapped device at runtime.
            'fieldBindings' => array(
                'deviceId'    => array('bind' => 'device.node_id',      'label' => 'Device Id',    'trailingAction' => 'removeDevice'),
                'productName' => array('bind' => 'device.product_name', 'label' => 'Name'),
                'brandName'   => array('bind' => 'device.brand_name',   'label' => 'Brand'),
                'deviceType'  => array('bind' => 'device.device_type',  'label' => 'Type'),
                'modelNumber' => array('bind' => 'device.model_number', 'label' => 'Model'),
                'status'      => array('bind' => 'device.status',       'label' => 'Status'),
                'onlineState' => array('bind' => 'device.online',       'label' => 'Online'),
            ),

            // Issue #3: all removeDevice locations must share the same body.node_id binding,
            // requestSchema, Content-Type, and native Authorization.
            'actions' => array(
                'removeDevice' => array(
                    'type'               => 'callApi',
                    'dataSource'         => 'removeDevice',
                    'confirmBeforeExecute'=> true,
                    'confirmDialog' => array(
                        'title'        => 'Delete Device',
                        'message'      => 'Remove this device from your account? This cannot be undone.',
                        'confirmLabel' => 'Delete',
                        'cancelLabel'  => 'Cancel',
                        'confirmStyle' => 'destructive',
                    ),
                    'method'             => 'POST',
                    'headers'            => array(
                        'Content-Type'  => 'application/json',
                        'Authorization' => $tokenBinding,
                    ),
                    'body' => array(
                        // Runtime binding — resolved from the tapped device, never a schema-time constant.
                        'node_id' => '{{device.node_id}}',
                    ),
                    'requestSchema' => array(
                        'type'       => 'object',
                        'required'   => array('node_id'),
                        'properties' => array(
                            'node_id' => array(
                                'type'    => 'string',
                                'binding' => 'device.node_id',
                            ),
                        ),
                    ),
                    'response' => array(
                        'successField' => 'success',
                        'statusField'  => 'status',
                        'messageField' => 'message',
                        'nodeIdField'  => 'node_id',
                        'devicesField' => 'devices',
                    ),
                    'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
                    // After successful delete: show alert and navigate back to IoT Devices page.
                    'onSuccess' => array(
                        'showAlert' => array(
                            'title'   => 'Device Deleted',
                            'message' => 'Device has been removed from your account.',
                            'style'   => 'success',
                        ),
                        'navigate' => array(
                            'action' => 'popToRoute',
                            'target' => 'iotDevices',
                            'route'  => '/iot/devices',
                        ),
                    ),
                ),
            ),

            // Screens published so the app can render without a per-device schema fetch.
            'screens' => array(
                'gatewayDetails' => array(
                    'id'    => 'gatewayDetails',
                    'title' => '{{device.product_name}}',
                    'sections' => array(
                        array(
                            'id'     => 'identity',
                            'title'  => 'Device',
                            'rows'   => array(
                                array('id' => 'deviceId',    'label' => 'Device Id',  'bind' => 'device.node_id',      'trailingAction' => 'removeDevice'),
                                array('id' => 'productName', 'label' => 'Name',       'bind' => 'device.product_name'),
                                array('id' => 'brandName',   'label' => 'Brand',      'bind' => 'device.brand_name'),
                                array('id' => 'deviceType',  'label' => 'Type',       'bind' => 'device.device_type',  'showWhen' => '{{device.device_type != ""}}'),
                                array('id' => 'modelNumber', 'label' => 'Model',      'bind' => 'device.model_number', 'showWhen' => '{{device.model_number != ""}}'),
                            ),
                        ),
                        array(
                            'id'    => 'connectivity',
                            'title' => 'Connectivity',
                            'rows'  => array(
                                array('id' => 'status',      'label' => 'Status', 'bind' => 'device.status'),
                                array('id' => 'onlineState', 'label' => 'Online', 'bind' => 'device.online', 'valueMap' => array('true' => 'Yes', 'false' => 'No', '1' => 'Yes', '0' => 'No')),
                            ),
                        ),
                    ),
                    // Footer actions — each action references infoPage.actions for the full contract.
                    'footerActions' => array(
                        array(
                            'id'          => 'device_info_delete_button',
                            'label'       => 'Delete Device',
                            'icon'        => 'trash-2',
                            'action'      => 'removeDevice',
                            'actionRef'   => 'infoPage.actions.removeDevice',
                            'style'       => 'destructive',
                            'confirm'     => true,
                            // Explicit confirmation dialog — shown before the delete API is called.
                            'confirmDialog' => array(
                                'title'         => 'Delete Device',
                                'message'       => 'Remove this device from your account? This cannot be undone.',
                                'confirmLabel'  => 'Delete',
                                'cancelLabel'   => 'Cancel',
                                'confirmStyle'  => 'destructive',
                            ),
                            // After successful delete: show alert and navigate back to IoT Devices page.
                            'onSuccess' => array(
                                'showAlert' => array(
                                    'title'   => 'Device Deleted',
                                    'message' => 'Device has been removed from your account.',
                                    'style'   => 'success',
                                ),
                                'navigate' => array(
                                    'action' => 'popToRoute',
                                    'target' => 'iotDevices',
                                    'route'  => '/iot/devices',
                                ),
                            ),
                        ),
                    ),
                ),
            ),

            // Controls wired to dataSources.
            'controlBindings' => array(
                'deleteInlineActionId'  => 'device_info_delete_inline',
                'deleteFooterButtonId'  => 'device_info_delete_button',
                'removeDeviceAction'    => 'removeDevice',
                'headerButtonId'        => 'device_info_button',
                'dataSource'            => 'deviceInfo',
            ),
        );
    }

    private function finalizeDevices(array $devices, string $apiBaseUrl): array
    {
        $final = array();
        foreach ($devices as $device) {
            if (!is_array($device)) {
                continue;
            }
            $device = \App\Libraries\IotDeviceNormalizer::finalize($device, $apiBaseUrl);
            // Issue #7: real access tokens must never appear in schema.devices[].
            // Authentication is handled exclusively through secure-state token bindings.
            unset($device['access_token']);
            $final[] = $device;
        }

        return $this->attachResolvedIconIds($this->sortDevicesOnlineFirst($final));
    }

    public function hydrateDevices(array $devices, string $apiBaseUrl = ''): array
    {
        if ($apiBaseUrl === '') {
            $apiBaseUrl = $this->detectPublicApiBaseUrl();
        }

        return $this->finalizeDevices($devices, $apiBaseUrl);
    }

    /**
     * Recompute totals/online/offline from the live devices list and patch schema UI binds.
     */
    public function applyLiveMetrics(array $schema, array $devices, string $apiBaseUrl = '', array $allNodes = array(), bool $mergeNodes = true): array
    {
        if ($mergeNodes && $allNodes !== array()) {
            $devices = (new \App\Libraries\IotNodesStatusBuilder())->mergeIntoDevices($devices, $allNodes);
        }

        if ($allNodes !== array()) {
            $schema['nodesSummary'] = array(
                'total'   => count($allNodes),
                'online'  => count(array_filter($allNodes, function ($node) {
                    if (!is_array($node)) {
                        return false;
                    }
                    $connected = $node['status']['connectivity']['connected'] ?? 'false';

                    return in_array(strtolower((string) $connected), array('1', 'true', 'yes', 'online', 'connected'), true);
                })),
            );
            $schema['nodesSummary']['offline'] = max(0, $schema['nodesSummary']['total'] - $schema['nodesSummary']['online']);
        }

        unset($schema['all_nodes']);

        $stats = $this->countDeviceStats($devices);
        $total = $stats['total'];
        $online = $stats['online'];
        $offline = $stats['offline'];

        $schema['deviceCount'] = $total;
        $schema['metrics'] = array(
            'total' => array('bind' => 'page.summary.total', 'computeFrom' => 'devices', 'compute' => 'countAll'),
            'online' => array('bind' => 'page.summary.online', 'computeFrom' => 'devices', 'compute' => 'countOnline'),
            'offline' => array('bind' => 'page.summary.offline', 'computeFrom' => 'devices', 'compute' => 'countOffline'),
        );

        if (!isset($schema['page']) || !is_array($schema['page'])) {
            $schema['page'] = array();
        }
        if (!isset($schema['page']['summary']) || !is_array($schema['page']['summary'])) {
            $schema['page']['summary'] = array();
        }
        $schema['page']['summary']['total'] = $total;
        $schema['page']['summary']['online'] = $online;
        $schema['page']['summary']['offline'] = $offline;

        if (!isset($schema['page']['header']) || !is_array($schema['page']['header'])) {
            $schema['page']['header'] = array();
        }
        $schema['page']['header']['subtitle'] = $total . ' products · ' . $online . ' online';
        $schema['page']['header']['subtitleBind'] = array(
            'template' => '{{page.summary.total}} products · {{page.summary.online}} online',
            'computeFrom' => 'devices',
        );

        if (isset($schema['components']) && is_array($schema['components'])) {
            foreach ($schema['components'] as $index => $component) {
                if (!is_array($component) || ($component['type'] ?? '') !== 'summaryCard') {
                    continue;
                }
                $schema['components'][$index]['data'] = array(
                    'total'  => $total,
                    'online' => $online,
                );
                if (isset($schema['components'][$index]['zones']) && is_array($schema['components'][$index]['zones'])) {
                    foreach ($schema['components'][$index]['zones'] as $zoneIndex => $zone) {
                        if (!is_array($zone) || !isset($zone['fields']) || !is_array($zone['fields'])) {
                            continue;
                        }
                        foreach ($zone['fields'] as $fieldIndex => $field) {
                            if (!is_array($field) || ($field['id'] ?? '') !== 'onlineValue') {
                                continue;
                            }
                            $schema['components'][$index]['zones'][$zoneIndex]['fields'][$fieldIndex]['bind'] = 'page.summary.online';
                            $schema['components'][$index]['zones'][$zoneIndex]['fields'][$fieldIndex]['computeFrom'] = 'devices';
                            $schema['components'][$index]['zones'][$zoneIndex]['fields'][$fieldIndex]['compute'] = 'countOnline';
                            unset($schema['components'][$index]['zones'][$zoneIndex]['fields'][$fieldIndex]['fallback']);
                        }
                    }
                }
            }
        }

        $schema['devices'] = $this->hydrateDevices($devices, $apiBaseUrl);

        return $schema;
    }

    /**
     * Merge missing structural sections from a fresh template into a saved schema.
     */
    public function ensureCompleteSchema(array $schema, array $template): array
    {
        if ($template === array()) {
            return $schema;
        }

        $structural = array(
            'schemaVersion', 'kind', 'auth', 'layout', 'theme', 'typography', 'icons', 'ui', 'overlays',
            'deviceFields', 'nodeFields', 'schemaContract', 'page', 'dataSources', 'components', 'metrics',
        );

        foreach ($structural as $key) {
            if (!isset($template[$key])) {
                continue;
            }
            if (!isset($schema[$key]) || $schema[$key] === array() || $schema[$key] === null) {
                $schema[$key] = $template[$key];
            }
        }

        if (($schema['schemaVersion'] ?? '') !== ($template['schemaVersion'] ?? '')) {
            foreach ($structural as $key) {
                if (isset($template[$key])) {
                    $schema[$key] = $template[$key];
                }
            }
        }

        $schema = $this->ensureManualNodeIdOverlay($schema, $template);

        return $schema;
    }

    /**
     * Remove large runtime payloads before rendering the web dashboard.
     *
     * @param bool $keepDevices Keep hydrated devices for mobile/API schema responses.
     * @param bool $stripIconMarkup Strip inline SVG/icon objects from devices (web preview only).
     */
    public function stripHeavyPayload(array $schema, bool $keepDevices = true, bool $stripIconMarkup = false): array
    {
        unset($schema['all_nodes']);

        if (!$keepDevices) {
            unset($schema['devices']);
        } elseif ($stripIconMarkup && isset($schema['devices']) && is_array($schema['devices'])) {
            foreach ($schema['devices'] as $index => $device) {
                if (!is_array($device)) {
                    continue;
                }
                unset($schema['devices'][$index]['resolvedIcon'], $schema['devices'][$index]['resolvedIconSvg']);
            }
        }

        // Last-mile patch for cached schemas still publishing narrow.columns=1.
        $schema = $this->patchNarrowDeviceGridColumns($schema);

        return $schema;
    }

    /**
     * Force iot-device-grid narrow breakpoint to 2 columns without changing keys.
     */
    private function patchNarrowDeviceGridColumns(array $schema): array
    {
        $targets = array();
        if (isset($schema['components']) && is_array($schema['components'])) {
            $targets[] =& $schema['components'];
        }
        foreach ($targets as &$components) {
            foreach ($components as $key => &$component) {
                if (!is_array($component)) {
                    continue;
                }
                $id = (string) ($component['id'] ?? $key);
                $type = (string) ($component['type'] ?? '');
                if ($id !== 'iot-device-grid' && $type !== 'deviceGrid') {
                    continue;
                }
                if (!isset($component['responsiveLayout']) || !is_array($component['responsiveLayout'])) {
                    $component['responsiveLayout'] = array();
                }
                if (!isset($component['responsiveLayout']['narrow']) || !is_array($component['responsiveLayout']['narrow'])) {
                    $component['responsiveLayout']['narrow'] = array('columns' => 2, 'gap' => 10);
                } else {
                    $component['responsiveLayout']['narrow']['columns'] = 2;
                    if (!isset($component['responsiveLayout']['narrow']['gap'])) {
                        $component['responsiveLayout']['narrow']['gap'] = 10;
                    }
                }
            }
            unset($component);
        }
        unset($components);

        return $schema;
    }

    /**
     * Identity of the shared IoT UI template plus this user's device roster.
     * A template file change makes every saved schema stale. The next
     * get_iot_schema rebuilds it. No new schema keys are added.
     *
     * @param array<int, array<string, mixed>> $devices
     */
    public function sourceFingerprint(array $devices): string
    {
        $items = array();
        foreach ($devices as $device) {
            if (!is_array($device)) {
                continue;
            }
            $items[] = array(
                'node_id' => trim((string) ($device['node_id'] ?? $device['id'] ?? '')),
                'model' => trim((string) ($device['model_number'] ?? $device['model_name'] ?? $device['model'] ?? '')),
                'name' => trim((string) ($device['name'] ?? $device['device_name'] ?? '')),
                'product_id' => trim((string) ($device['product_id'] ?? $device['efactor_id'] ?? '')),
            );
        }
        usort($items, static function ($a, $b) {
            return strcmp($a['node_id'] . '|' . $a['product_id'], $b['node_id'] . '|' . $b['product_id']);
        });

        $payload = array(
            'builderVersion' => $this->templateIdentity(),
            'devices' => $items,
        );
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $encoded === false ? '' : hash('sha256', $encoded);
    }

    /**
     * Changes when the shared IoT screen template changes.
     * Device data is not part of this identity.
     */
    public function templateIdentity(): string
    {
        $files = array(__FILE__);
        $siblings = array(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IotDeviceNormalizer.php',
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'IotDashboardModel.php',
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'v8' . DIRECTORY_SEPARATOR . 'iot_api_creation.php',
        );
        foreach ($siblings as $path) {
            if (is_file($path)) {
                $files[] = $path;
            }
        }

        $parts = array('iot-ui-20261012');
        foreach ($files as $file) {
            $hash = @hash_file('sha256', $file);
            if (is_string($hash) && $hash !== '') {
                $parts[] = substr($hash, 0, 16);
            }
        }

        return implode(':', $parts);
    }

    public function countDeviceStats(array $devices): array
    {
        $total = count($devices);
        $online = 0;
        foreach ($devices as $device) {
            if (is_array($device) && !empty($device['online'])) {
                $online++;
            }
        }

        return array(
            'total'   => $total,
            'online'  => $online,
            'offline' => max(0, $total - $online),
        );
    }

    /**
     * Pre-resolve icon id per device so SDUI clients can render fallbacks without re-matching.
     */
    private function attachResolvedIconIds(array $devices): array
    {
        foreach ($devices as $index => $device) {
            if (!is_array($device)) {
                continue;
            }
            $typeHint = (string) ($device['device_type'] ?? $device['devicetype'] ?? $device['product_name'] ?? $device['productName'] ?? $device['name'] ?? '');
            $devices[$index]['resolvedIconId'] = $this->resolveDeviceIconId($typeHint);
            $resolvedIcon = $this->getSduiDeviceIconCatalog()[$devices[$index]['resolvedIconId']] ?? null;
            $devices[$index]['resolvedIcon'] = $resolvedIcon;
            $devices[$index]['resolvedIconSvg'] = is_array($resolvedIcon) ? (string) ($resolvedIcon['svgMarkup'] ?? '') : '';
        }

        return $devices;
    }

    private function resolveDeviceIconId(string $typeHint): string
    {
        $hint = strtolower(trim($typeHint));
        foreach ($this->getSduiDeviceIconMap() as $rule) {
            $pattern = '/' . ($rule['match'] ?? '.*') . '/i';
            if (@preg_match($pattern, $hint) === 1) {
                return (string) ($rule['iconId'] ?? 'device');
            }
        }

        return 'device';
    }

    private function getSduiThemeTokens(): array
    {
        return array(
            'background'     => '#ececec',
            'surface'        => '#ffffff',
            'surfaceOnline'  => '#1c1c1e',
            'surfaceOffline' => '#ffffff',
            'textPrimary'    => '#111827',
            'textSecondary'  => '#9ca3af',
            'textOnDark'     => '#ffffff',
            'online'         => '#84cc16',
            'onlineAccent'   => '#a3e635',
            'offline'        => '#94a3b8',
            'accent'         => '#111827',
            'accentOrange'   => '#fb923c',
            'danger'         => '#dc2626',
            'cardRadius'     => 24,
            'gridGap'        => 12,
            'fontFamily'     => 'Inter, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, sans-serif',
            'deviceSort'     => 'onlineFirst',
            'shadowOnline'   => '0 12px 28px rgba(0,0,0,.18)',
            'shadowOffline'  => '0 8px 24px rgba(0,0,0,.07)',
            'borderOffline'  => '1.5px solid #e5e7eb',
            // Card flip animation tokens — consumed by the deviceGrid card.flip spec.
            'cardFlip' => array(
                'enabled'          => true,
                'durationMs'       => 420,
                'curve'            => 'easeInOutCubic',
                'perspectiveDepth' => 1200,
                'axis'             => 'y',
            ),
        );
    }

    private function getSduiTypographyTokens(): array
    {
        return array(
            'productTitle' => array(
                'fontSize' => 13.5, 'fontWeight' => 800, 'lineHeight' => 1.28, 'letterSpacing' => -0.01,
                'colorOnline' => '#ffffff', 'colorOffline' => '#111827', 'lineClamp' => 2,
            ),
            'brandText' => array(
                'fontSize' => 11, 'fontWeight' => 600, 'lineHeight' => 1.25, 'letterSpacing' => 0.01,
                'colorOnline' => 'rgba(255,255,255,.62)', 'colorOffline' => '#9ca3af',
            ),
            'deviceTypeText' => array(
                'fontSize' => 10, 'fontWeight' => 600, 'lineHeight' => 1.2,
                'colorOnline' => 'rgba(255,255,255,.42)', 'colorOffline' => '#cbd5e1',
            ),
            'statusText' => array(
                'fontSize' => 10.5, 'fontWeight' => 800, 'lineHeight' => 1.2,
                'colorOnline' => '#a3e635', 'colorOffline' => '#94a3b8',
            ),
            'powerLabel' => array(
                'fontSize' => 12, 'fontWeight' => 800, 'lineHeight' => 1.2,
                'colorOnline' => '#ffffff', 'colorOffline' => '#111827',
            ),
            'summaryDate' => array('fontSize' => 11, 'fontWeight' => 700, 'lineHeight' => 1.2, 'color' => '#9ca3af'),
            'summaryTitle' => array('fontSize' => 13, 'fontWeight' => 800, 'lineHeight' => 1.25, 'color' => '#111827'),
            'summaryValue' => array('fontSize' => 28, 'fontWeight' => 900, 'lineHeight' => 1.05, 'color' => '#84cc16'),
        );
    }

    private function getSduiSmartHomeIconSpec(): array
    {
        return $this->enrichSduiIconSpec('smart-home', array(
            'viewBox' => '0 0 24 24',
            'width' => 22, 'height' => 22, 'strokeWidth' => 1.8,
            'strokeLinecap' => 'round', 'strokeLinejoin' => 'round', 'fill' => 'none',
            'paths' => array(
                'M4 10.5 12 4l8 6.5',
                'M6 9.5V20h12V9.5',
                'M10 20v-5h4v5',
                'M12 4v2',
                'M16 7h2',
            ),
            'circles' => array(
                array('cx' => 17, 'cy' => 7, 'r' => 1.2, 'fill' => 'currentColor', 'stroke' => 'none'),
            ),
        ));
    }

    private function getSduiWifiIconSpec(array $overrides = array()): array
    {
        $spec = array_merge(array(
            'viewBox' => '0 0 24 24',
            'containerSize' => 18, 'iconSize' => 16, 'strokeWidth' => 2,
            'strokeLinecap' => 'round', 'strokeLinejoin' => 'round', 'fill' => 'none',
            'placement' => array('zone' => 'topRow', 'align' => 'right'),
            'colorOnline' => '#ffffff', 'colorOffline' => '#111827',
            'opacityOnline' => 0.55, 'opacityOffline' => 0.28,
            'paths' => array(
                'M5 12.55a11 11 0 0 1 14.08 0',
                'M8.53 16.11a6 6 0 0 1 6.95 0',
            ),
            'circles' => array(
                array('cx' => 12, 'cy' => 20, 'r' => 1.5, 'fill' => 'currentColor', 'stroke' => 'none'),
            ),
        ), $overrides);

        $iconId = (string) ($overrides['id'] ?? 'wifi-signal');

        return $this->enrichSduiIconSpec($iconId, $spec);
    }

    private function getSduiDeviceIconMap(): array
    {
        return array(
            array('match' => 'fan|air|ventil|exhaust', 'iconId' => 'fan', 'label' => 'Fan'),
            array('match' => 'light|lamp|bulb|led|tube', 'iconId' => 'light', 'label' => 'Light'),
            array('match' => 'plug|socket|switch|outlet|relay', 'iconId' => 'switch', 'label' => 'Switch'),
            array('match' => 'camera|cam|cctv|security|monitor', 'iconId' => 'camera', 'label' => 'Camera'),
            array('match' => 'lock|door|gate|access', 'iconId' => 'lock', 'label' => 'Lock'),
            array('match' => 'thermo|temp|heat|cool|hvac|ac|climate', 'iconId' => 'thermo', 'label' => 'Climate'),
            array('match' => 'sensor|motion|pir|detect', 'iconId' => 'sensor', 'label' => 'Sensor'),
            array('match' => 'speaker|audio|sound|music|alarm', 'iconId' => 'speaker', 'label' => 'Audio'),
            array('match' => 'tv|tele|screen|display|projector', 'iconId' => 'tv', 'label' => 'Display'),
            array('match' => 'cook|rice|oven|micro|kitchen|grill|toaster|kettle|coffee', 'iconId' => 'kitchen', 'label' => 'Kitchen'),
            array('match' => 'wash|dryer|laundry|machine', 'iconId' => 'washer', 'label' => 'Appliance'),
            array('match' => 'router|gateway|hub|wifi|network|bridge', 'iconId' => 'router', 'label' => 'Network'),
            array('match' => 'scan|barcode|qr', 'iconId' => 'scanner', 'label' => 'Scanner'),
            array('match' => 'battery|energy|meter|power|watt', 'iconId' => 'energy', 'label' => 'Energy'),
            array('match' => '.*', 'iconId' => 'device', 'label' => 'Device', 'default' => true),
        );
    }

    /**
     * Full SVG icon catalog — used when brand/product images are missing (matches web preview).
     */
    private function getSduiDeviceIconCatalog(): array
    {
        $stroke = array(
            'viewBox' => '0 0 24 24',
            'strokeWidth' => 1.6,
            'strokeLinecap' => 'round',
            'strokeLinejoin' => 'round',
            'fill' => 'none',
        );

        $catalog = array(
            'fan' => array_merge($stroke, array(
                'paths' => array(
                    'M12 9c0-3-2.5-6-5-5.5C4 4 3 7 5 9c1 1 4 1 4 1s-1 3 1 5c1.5 2 4.5 2 5.5.5C17 14 15 11 12 9z',
                    'M12 15c0 3 2.5 6 5 5.5C20 20 21 17 19 15c-1-1-4-1-4-1s1-3-1-5c-1.5-2-4.5-2-5.5-.5C7 10 9 13 12 15z',
                ),
                'circles' => array(array('cx' => 12, 'cy' => 12, 'r' => 3)),
            )),
            'light' => array_merge($stroke, array(
                'paths' => array(
                    'M9 21h6',
                    'M12 3a6 6 0 0 1 4.24 10.24C15.5 14 15 15 15 16H9c0-1-.5-2-1.24-2.76A6 6 0 0 1 12 3z',
                    'M9 16v1a3 3 0 0 0 6 0v-1',
                ),
            )),
            'switch' => array_merge($stroke, array(
                'paths' => array(
                    'M9 7v3M15 7v3',
                    'M12 16v5M8 21h8',
                ),
                'rects' => array(array('x' => 6, 'y' => 3, 'width' => 12, 'height' => 13, 'rx' => 2)),
            )),
            'camera' => array_merge($stroke, array(
                'paths' => array(
                    'M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z',
                ),
                'circles' => array(array('cx' => 12, 'cy' => 13, 'r' => 3)),
            )),
            'lock' => array_merge($stroke, array(
                'paths' => array('M7 11V7a5 5 0 0 1 10 0v4'),
                'rects' => array(array('x' => 3, 'y' => 11, 'width' => 18, 'height' => 11, 'rx' => 2)),
                'circles' => array(array('cx' => 12, 'cy' => 16, 'r' => 1.5, 'fill' => 'currentColor', 'stroke' => 'none')),
            )),
            'thermo' => array_merge($stroke, array(
                'paths' => array('M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z'),
            )),
            'sensor' => array_merge($stroke, array(
                'paths' => array(
                    'M16.24 7.76a6 6 0 0 1 0 8.49M7.76 16.24a6 6 0 0 1 0-8.49',
                    'M19.07 4.93a10 10 0 0 1 0 14.14M4.93 19.07a10 10 0 0 1 0-14.14',
                ),
                'circles' => array(array('cx' => 12, 'cy' => 12, 'r' => 2)),
            )),
            'speaker' => array_merge($stroke, array(
                'rects' => array(array('x' => 4, 'y' => 2, 'width' => 16, 'height' => 20, 'rx' => 2)),
                'circles' => array(
                    array('cx' => 12, 'cy' => 14, 'r' => 4),
                    array('cx' => 12, 'cy' => 14, 'r' => 1.5, 'fill' => 'currentColor', 'stroke' => 'none'),
                    array('cx' => 12, 'cy' => 6, 'r' => 1),
                ),
            )),
            'tv' => array_merge($stroke, array(
                'paths' => array('M8 21h8M12 17v4'),
                'rects' => array(array('x' => 2, 'y' => 3, 'width' => 20, 'height' => 14, 'rx' => 2)),
            )),
            'kitchen' => array_merge($stroke, array(
                'paths' => array(
                    'M7 5V3M12 5V3M17 5V3',
                    'M13 10h5M13 14h5',
                ),
                'rects' => array(array('x' => 2, 'y' => 5, 'width' => 20, 'height' => 14, 'rx' => 2)),
                'circles' => array(array('cx' => 8, 'cy' => 12, 'r' => 2.5)),
            )),
            'washer' => array_merge($stroke, array(
                'paths' => array('M8 6h.01M11 6h.01M14 6h.01'),
                'rects' => array(array('x' => 3, 'y' => 3, 'width' => 18, 'height' => 18, 'rx' => 2)),
                'circles' => array(array('cx' => 12, 'cy' => 13, 'r' => 4)),
            )),
            'router' => array_merge($stroke, array(
                'paths' => array(
                    'M5 12.55a11 11 0 0 1 14.08 0',
                    'M1.42 9a16 16 0 0 1 21.16 0',
                    'M8.53 16.11a6 6 0 0 1 6.95 0',
                ),
                'circles' => array(array('cx' => 12, 'cy' => 20, 'r' => 1.5, 'fill' => 'currentColor', 'stroke' => 'none')),
            )),
            'scanner' => array_merge($stroke, array(
                'paths' => array(
                    'M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2',
                    'M7 8v8M12 8v8M17 8v8',
                ),
            )),
            'energy' => array_merge($stroke, array(
                'paths' => array('M22 11v2', 'M7 11v2M11 11v2'),
                'rects' => array(array('x' => 2, 'y' => 7, 'width' => 16, 'height' => 10, 'rx' => 2)),
            )),
            'device' => array_merge($stroke, array(
                'paths' => array(
                    'M9 3v2M12 3v2M15 3v2M9 19v2M12 19v2M15 19v2M3 9h2M3 12h2M3 15h2M19 9h2M19 12h2M19 15h2',
                ),
                'rects' => array(array('x' => 7, 'y' => 7, 'width' => 10, 'height' => 10, 'rx' => 1)),
            )),
        );

        $enriched = array();
        foreach ($catalog as $iconId => $spec) {
            $enriched[$iconId] = $this->enrichSduiIconSpec((string) $iconId, $spec);
        }

        return $enriched;
    }

    private function enrichSduiIconSpec(string $iconId, array $spec): array
    {
        $spec['id'] = $iconId;
        $spec['type'] = 'svg';
        $spec['svgMarkup'] = $this->buildSduiSvgMarkup($spec);

        return $spec;
    }

    private function buildSduiSvgMarkup(array $spec): string
    {
        $viewBox = (string) ($spec['viewBox'] ?? '0 0 24 24');
        $strokeWidth = (string) ($spec['strokeWidth'] ?? 1.6);
        $strokeLinecap = (string) ($spec['strokeLinecap'] ?? 'round');
        $strokeLinejoin = (string) ($spec['strokeLinejoin'] ?? 'round');
        $defaultFill = (string) ($spec['fill'] ?? 'none');

        $parts = array(
            '<svg viewBox="' . $viewBox . '" xmlns="http://www.w3.org/2000/svg"'
            . ' fill="' . $defaultFill . '" stroke="currentColor"'
            . ' stroke-width="' . $strokeWidth . '"'
            . ' stroke-linecap="' . $strokeLinecap . '"'
            . ' stroke-linejoin="' . $strokeLinejoin . '">',
        );

        foreach ($spec['paths'] ?? array() as $pathData) {
            $parts[] = '<path d="' . htmlspecialchars((string) $pathData, ENT_QUOTES, 'UTF-8') . '"/>';
        }

        foreach ($spec['rects'] ?? array() as $rect) {
            if (!is_array($rect)) {
                continue;
            }
            $attrs = array(
                'x' => (string) ($rect['x'] ?? 0),
                'y' => (string) ($rect['y'] ?? 0),
                'width' => (string) ($rect['width'] ?? 0),
                'height' => (string) ($rect['height'] ?? 0),
            );
            if (isset($rect['rx'])) {
                $attrs['rx'] = (string) $rect['rx'];
            }
            if (isset($rect['fill'])) {
                $attrs['fill'] = (string) $rect['fill'];
            }
            if (isset($rect['stroke'])) {
                $attrs['stroke'] = (string) $rect['stroke'];
            }
            $attrString = '';
            foreach ($attrs as $key => $value) {
                $attrString .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
            $parts[] = '<rect' . $attrString . '/>';
        }

        foreach ($spec['circles'] ?? array() as $circle) {
            if (!is_array($circle)) {
                continue;
            }
            $attrs = array(
                'cx' => (string) ($circle['cx'] ?? 0),
                'cy' => (string) ($circle['cy'] ?? 0),
                'r' => (string) ($circle['r'] ?? 0),
            );
            if (isset($circle['fill'])) {
                $attrs['fill'] = (string) $circle['fill'];
            }
            if (isset($circle['stroke'])) {
                $attrs['stroke'] = (string) $circle['stroke'];
            }
            $attrString = '';
            foreach ($attrs as $key => $value) {
                $attrString .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
            $parts[] = '<circle' . $attrString . '/>';
        }

        $parts[] = '</svg>';

        return implode('', $parts);
    }

    private function buildSduiSummaryComponent(int $online, int $total): array
    {
        return array(
            'type' => 'summaryCard',
            'id'   => 'iot-summary-card',
            'style' => array(
                'padding'      => array('top' => 14, 'right' => 16, 'bottom' => 14, 'left' => 16),
                'borderRadius' => 24,
                'background'   => '#ffffff',
                'shadow'       => '0 8px 24px rgba(0,0,0,.06)',
                'marginBottom' => 14,
                'gap'          => 12,
            ),
            'layout' => array('type' => 'row', 'justify' => 'space-between', 'align' => 'center'),
            // Summary card stays full-width on all breakpoints — no column changes needed.
            // Padding adjusts so it breathes on wider viewports.
            'responsiveLayout' => array(
                'narrow'   => array('style' => array('padding' => array('top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12))),
                'compact'  => array('style' => array('padding' => array('top' => 14, 'right' => 16, 'bottom' => 14, 'left' => 16))),
                'medium'   => array('style' => array('padding' => array('top' => 16, 'right' => 20, 'bottom' => 16, 'left' => 20))),
                'expanded' => array('style' => array('padding' => array('top' => 18, 'right' => 24, 'bottom' => 18, 'left' => 24))),
            ),
            'zones' => array(
                array(
                    'id' => 'left',
                    'fields' => array(
                        array('id' => 'dateLabel', 'type' => 'text', 'bind' => 'generatedDate', 'typography' => 'summaryDate'),
                        array('id' => 'title', 'type' => 'text', 'value' => 'Connected Devices', 'typography' => 'summaryTitle'),
                        array(
                            'id' => 'onlineValue', 'type' => 'metric',
                            'bind' => 'page.summary.online', 'suffix' => 'online',
                            'typography' => 'summaryValue',
                            'computeFrom' => 'devices',
                            'compute' => 'countOnline',
                        ),
                    ),
                ),
                array(
                    'id' => 'right',
                    'fields' => array(
                        array(
                            'id' => 'connectionIcon', 'type' => 'icon',
                            'icon' => 'connectionArt',
                            'container' => array(
                                'width' => 58, 'height' => 58, 'borderRadius' => 18,
                                'background' => 'linear-gradient(145deg,#ecfccb,#d9f99d)',
                                'color' => '#65a30d',
                            ),
                        ),
                    ),
                ),
            ),
            'data' => array(
                'total'  => $total,
                'online' => $online,
            ),
        );
    }

    private function buildSduiDeviceGridComponent(string $apiBase = '', array $tokenBinding = array()): array
    {
        unset($apiBase, $tokenBinding);
        return array(
            'type'       => 'deviceGrid',
            'id'         => 'iot-device-grid',
            'columns'    => 2,
            'gap'        => 12,
            'sort'       => 'onlineFirst',
            'dataSource' => 'getDevices',
            'liveStatusSource' => 'getAllNodesStatus',
            'refreshWith'      => array('getDevices', 'getAllNodesStatus'),
            // Breakpoint-specific grid columns and gap.
            // Flutter reads these and picks the matching entry from its LayoutBuilder.
            // The base columns/gap above remain the compact default.
            'responsiveLayout' => array(
                // 360dp phones lose ~24dp to page padding and fall into narrow;
                // keep 2 columns so cards stay a grid instead of full-width banners.
                'narrow'   => array('columns' => 2, 'gap' => 10),
                'compact'  => array('columns' => 2, 'gap' => 12),
                'medium'   => array('columns' => 3, 'gap' => 14),
                'expanded' => array('columns' => 4, 'gap' => 16),
            ),

            'card' => array(
                'clickable'   => true,
                'minHeight'   => 168,
                'borderRadius'=> 24,
                'padding'     => array('top' => 14, 'right' => 12, 'bottom' => 12, 'left' => 12),
                'accessLabel' => '{{device.product_name}}, {{device.brand_name}}, {{device.status}}',

                // ── Card flip / rotation animation ──────────────────────────
                // Flutter renders a 3-D card-flip on tap. The card rotates 180 ° on the
                // Y-axis to reveal its back face. Tapping again flips back to the front.
                // Do NOT change action/binding/API contracts — only the presentation layer.
                'flip' => array(
                    'enabled'         => true,
                    'axis'            => 'y',               // Y-axis rotation
                    'durationMs'      => 420,
                    'curve'           => 'easeInOutCubic',
                    'perspectiveDepth'=> 1200,              // Flutter Matrix4 perspective (logical px)
                    'trigger'         => 'onTap',           // front-face tap triggers flip
                    'backTrigger'     => 'onTap',           // back-face tap flips back
                    // Back face — shown after 180° rotation. Contains quick-action controls.
                    'backFace' => array(
                        'borderRadius' => 24,
                        'padding'      => array('top' => 14, 'right' => 12, 'bottom' => 12, 'left' => 12),
                        'background'   => array(
                            'online'  => 'linear-gradient(145deg,#1a2035,#0f172a)',
                            'offline' => 'linear-gradient(145deg,#f0f4f8,#e2e8f0)',
                        ),
                        'layout' => array('type' => 'column', 'justify' => 'space-between', 'gap' => 8),
                        'zones' => array(
                            array(
                                'id'     => 'backHeader',
                                'layout' => array('type' => 'row', 'justify' => 'space-between', 'align' => 'center'),
                                'fields' => array('backNodeId', 'backStatus'),
                            ),
                            array(
                                'id'     => 'backCenter',
                                'layout' => array('type' => 'column', 'align' => 'center', 'gap' => 10),
                                'fields' => array('backDeviceIcon', 'backPowerToggle'),
                            ),
                            array(
                                'id'     => 'backFooter',
                                'layout' => array('type' => 'row', 'justify' => 'center', 'gap' => 12),
                                'fields' => array('backFlipHint'),
                            ),
                        ),
                        'fields' => array(
                            'backNodeId' => array(
                                'type'       => 'text',
                                'value'      => '{{device.node_id_plain}}',
                                'typography' => array(
                                    'fontSize'    => 9,
                                    'fontWeight'  => 700,
                                    'letterSpacing' => 0.04,
                                    'colorOnline' => 'rgba(255,255,255,.45)',
                                    'colorOffline'=> '#94a3b8',
                                ),
                            ),
                            'backStatus' => array(
                                'type'   => 'status',
                                'value'  => '{{device.status}}',
                                'typography' => 'statusText',
                                'dot'    => array(
                                    'diameter' => 6, 'borderRadius' => 3,
                                    'online'   => array('color' => '#84cc16', 'glow' => '0 0 0 3px rgba(132,204,22,.22)'),
                                    'offline'  => array('color' => '#cbd5e1'),
                                ),
                            ),
                            'backDeviceIcon' => array(
                                'type'        => 'deviceIcon',
                                'resolveFrom' => 'device_type|product_name',
                                'map'         => 'icons.deviceTypeMap',
                                'catalog'     => 'icons.catalog',
                                'iconSize'    => 36,
                                'strokeOnline' => '#a3e635',
                                'strokeOffline'=> '#94a3b8',
                                'container'   => array(
                                    'width' => 64, 'height' => 64, 'borderRadius' => 20,
                                    'online'  => array('background' => 'rgba(132,204,22,.12)'),
                                    'offline' => array('background' => 'rgba(148,163,184,.10)'),
                                ),
                            ),
                            'backPowerToggle' => array(
                                'type'             => 'toggle',
                                'role'             => 'button',
                                'control'          => 'powerApiToggle',
                                'linkedDataSource' => 'powerToggle',
                                'interactive'      => true,
                                'enabledWhen'      => '{{device.node_id != ""}}',
                                'bind'             => '{{device.power}}',
                                'width'  => 52, 'height' => 30, 'borderRadius' => 999,
                                'thumb'  => array('diameter' => 24, 'offset' => 3, 'translateXOn' => 22, 'shadow' => '0 2px 6px rgba(0,0,0,.25)'),
                                'track'  => array(
                                    'onlineOn'  => array('background' => '#84cc16'),
                                    'onlineOff' => array('background' => 'rgba(255,255,255,.18)'),
                                    'offline'   => array('background' => '#e2e8f0', 'disabled' => false),
                                ),
                                'thumbColors' => array(
                                    'onlineOn' => '#ffffff', 'onlineOff' => '#ffffff',
                                    'offlineOn'=> '#ffffff', 'offlineOff'=> '#cbd5e1',
                                ),
                                'stopPropagation' => true,
                                'action' => array(
                                    'type'            => 'callApi',
                                    'source'          => 'powerToggle',
                                    'stopPropagation' => true,
                                    'refreshAfter'    => array('getDevices', 'getAllNodesStatus'),
                                ),
                            ),
                            'backFlipHint' => array(
                                'type'       => 'text',
                                'value'      => 'Tap to flip back',
                                'typography' => array(
                                    'fontSize'    => 9,
                                    'fontWeight'  => 600,
                                    'colorOnline' => 'rgba(255,255,255,.28)',
                                    'colorOffline'=> '#cbd5e1',
                                ),
                            ),
                        ),
                    ),
                ),
                // ── Front face zones (existing, unchanged) ───────────────────

                'zones' => array(
                    array(
                        'id' => 'topRow', 'marginBottom' => 10,
                        'layout' => array('type' => 'row', 'justify' => 'space-between', 'align' => 'center'),
                        'fields' => array('brandLogo', 'wifiSignal'),
                    ),
                    array(
                        'id' => 'titleBlock', 'marginBottom' => 6,
                        'fields' => array('product_name', 'brandName', 'model_no', 'device_type'),
                    ),
                    array(
                        'id' => 'productImage', 'paddingTop' => 4, 'paddingBottom' => 6, 'minHeight' => 48,
                        'fields' => array('productImage'),
                    ),
                    array(
                        'id' => 'statusRow', 'marginBottom' => 8,
                        'fields' => array('statusPill'),
                    ),
                    array(
                        'id' => 'footer', 'paddingTop' => 2, 'gap' => 8,
                        'layout' => array('type' => 'row', 'justify' => 'space-between', 'align' => 'center'),
                        'fields' => array('powerLabel', 'powerToggle'),
                    ),
                ),

                'fields' => array(
                    'brandLogo' => array(
                        'type' => 'image', 'src' => '{{device.brandLogo}}',
                        'width' => 38, 'height' => 38, 'borderRadius' => 19,
                        'objectFit' => 'cover',
                        'container' => array(
                            'online'  => array('background' => '#ffffff'),
                            'offline' => array('background' => '#F8FAFC', 'border' => '1px solid #E5E7EB'),
                        ),
                        'fallbackChain' => array(
                            array(
                                'type' => 'deviceIcon',
                                'resolveFrom' => 'device_type|product_name|brand_name',
                                'map' => 'icons.deviceTypeMap',
                                'catalog' => 'icons.catalog',
                                'iconSize' => 20,
                            ),
                            array(
                                'type' => 'initial',
                                'resolveFrom' => 'brand_name',
                                'typography' => array('fontSize' => 14, 'fontWeight' => 900),
                            ),
                        ),
                    ),
                    'wifiSignal' => $this->getSduiWifiIconSpec(),
                    'product_name' => array(
                        'type' => 'text', 'value' => '{{device.product_name}}',
                        'typography' => 'productTitle',
                    ),
                    'brandName' => array(
                        'type' => 'text', 'value' => '{{device.brand_name}}',
                        'typography' => 'brandText',
                    ),
                    'model_no' => array(
                        'type' => 'text',
                        'bind' => 'device.model_no',
                        'value' => '{{device.model_no}}',
                        'fallback' => '{{device.model_number}}',
                        'showWhen' => '{{device.model_no != "" || device.model_number != ""}}',
                        'typography' => 'deviceTypeText',
                    ),
                    'device_type' => array(
                        'type' => 'text', 'value' => '{{device.device_type}}',
                        'showWhen' => '{{device.device_type != device.product_name}}',
                        'typography' => 'deviceTypeText',
                    ),
                    // src stays device.productImage. The URL is vendorproducts.model_logo.
                    'productImage' => array(
                        'type' => 'image', 'src' => '{{device.productImage}}',
                        'height' => 48, 'objectFit' => 'contain', 'borderRadius' => 8,
                        'container' => array(
                            'width' => 48, 'height' => 48, 'borderRadius' => 14,
                            'online'  => array('background' => 'rgba(255,255,255,.1)'),
                            'offline' => array('background' => '#f3f4f6', 'border' => '1px dashed #e5e7eb'),
                        ),
                        'fallback' => array(
                            'type' => 'deviceIcon',
                            'resolveFrom' => 'device_type|product_name',
                            'map' => 'icons.deviceTypeMap',
                            'catalog' => 'icons.catalog',
                            'iconSize' => 24,
                            'strokeOnline' => '#a3e635',
                            'strokeOffline' => '#9ca3af',
                        ),
                    ),
                    'statusPill' => array(
                        'type' => 'status', 'value' => '{{device.status}}',
                        'typography' => 'statusText',
                        'dot' => array(
                            'diameter' => 6, 'borderRadius' => 3,
                            'online'  => array('color' => '#84cc16', 'glow' => '0 0 0 3px rgba(132,204,22,.22)'),
                            'offline' => array('color' => '#cbd5e1'),
                        ),
                    ),
                    'powerLabel' => array(
                        'type' => 'text',
                        'valueOnline' => 'On', 'valueOffline' => 'Off',
                        'bindPower' => '{{device.power}}',
                        'typography' => 'powerLabel',
                    ),
                    'powerToggle' => array(
                        'type' => 'toggle',
                        'role' => 'button',
                        'control' => 'powerApiToggle',
                        'linkedDataSource' => 'powerToggle',
                        'interactive' => true,
                        'enabledWhen' => '{{device.node_id != ""}}',
                        'bind' => '{{device.power}}',
                        'bindContract' => array(
                            'statePath' => 'device.power',
                            'alias' => 'device.powerOn',
                            'valueType' => 'boolean',
                            'trueValues' => array(true, 1, '1', '01', 'true', 'yes', 'on'),
                            'falseValues' => array(false, 0, '0', 'false', 'no', 'off'),
                            'uiOnClass' => 'is-on',
                            'enabledWhen' => '{{device.node_id != ""}}',
                        ),
                        'mutation' => array(
                            'dataSource' => 'powerToggle',
                            'action' => 'device_power_toggle',
                            'method' => 'POST',
                            'bodyTemplate' => array(
                                'node_id' => '{{device.node_id}}',
                                'command' => '{{toggle_next|off:on}}',
                                'power' => '{{toggle_next}}',
                            ),
                            'responsePowerField' => 'power',
                            'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
                        ),
                        'width' => 44, 'height' => 26, 'borderRadius' => 999,
                        'thumb' => array(
                            'diameter' => 20, 'offset' => 3, 'translateXOn' => 18,
                            'shadow' => '0 2px 6px rgba(0,0,0,.18)',
                        ),
                        'track' => array(
                            'onlineOn'  => array('background' => 'rgba(255,255,255,.92)'),
                            'onlineOff' => array('background' => 'rgba(255,255,255,.92)'),
                            'offline'   => array('background' => '#e5e7eb', 'disabled' => false),
                        ),
                        'thumbColors' => array(
                            'onlineOn' => '#1c1c1e', 'onlineOff' => '#ffffff',
                            'offlineOn' => '#ffffff', 'offlineOff' => '#ffffff',
                        ),
                        'stopPropagation' => true,
                        'action' => array(
                            'type'            => 'callApi',
                            'source'          => 'powerToggle',
                            'stopPropagation' => true,
                            'refreshAfter'    => array('getDevices', 'getAllNodesStatus'),
                        ),
                        'onTap' => array(
                            'action'          => 'callApi',
                            'source'          => 'powerToggle',
                            'stopPropagation' => true,
                            'refreshAfter'    => array('getDevices', 'getAllNodesStatus'),
                        ),
                        'deviceActionFallback' => 'device.actions.onPowerToggle',
                    ),
                ),

                'states' => array(
                    'online' => array(
                        'class' => 'is-online', 'background' => '#1c1c1e', 'color' => '#ffffff',
                        'shadow' => '0 12px 28px rgba(0,0,0,.18)',
                    ),
                    'offline' => array(
                        'class' => 'is-offline', 'background' => '#ffffff', 'color' => '#111827',
                        'shadow' => '0 8px 24px rgba(0,0,0,.07)', 'border' => '1.5px solid #e5e7eb',
                        'imageFilter' => 'grayscale(.35)', 'imageOpacity' => 0.72,
                    ),
                    'selected' => array(
                        'class' => 'is-selected', 'outline' => '3px solid rgba(132,204,22,.55)', 'outlineOffset' => 2,
                    ),
                ),

                'onTap' => array('action' => 'callApi', 'source' => 'cardClick'),
            ),
        );
    }

    /**
     * Add-device header action, QR scanner overlay, and v8 onboarding APIs.
     *
     * @return array{ui: array<string, mixed>, overlays: array<string, mixed>, dataSources: array<string, mixed>, interaction: array<string, mixed>, manualInteraction: array<string, mixed>, headerAction: array<string, mixed>}
     */
    private function buildDeviceOnboardingSpec(string $deviceApiBase, string $ciBase): array
    {
        $tokenBinding = array('$secureState' => 'session.iotAccessToken');
        $authQuery = array(
            'type'       => 'header',
            'headerName' => 'Authorization',
            'binding'    => $tokenBinding,
            // Token goes in Authorization header only — no query param, no body field.
        );
        $appHomeBase = $this->detectAppHomeApiBaseUrl($ciBase);

        $headerAction = array(
            'id'          => 'addDevice',
            'type'        => 'iconButton',
            'control'     => 'addDeviceButton',
            'icon'        => 'addDevice',
            'label'       => 'Add device',
            'ariaLabel'   => 'Scan QR to add device',
            'title'       => 'Scan QR to add device',
            'placement'   => array('zone' => 'page.header', 'align' => 'right', 'replacesAvatar' => true),
            'style'       => array(
                'width'        => 42,
                'height'       => 42,
                'borderRadius' => 14,
                'background'   => 'linear-gradient(145deg,#2563eb,#1d4ed8)',
                'color'        => '#ffffff',
                'boxShadow'    => '0 8px 20px rgba(37,99,235,.28)',
            ),
            'onTap' => array(
                'action'    => 'openOverlay',
                'overlayId' => 'qrScannerModal',
            ),
        );

        $checkDevice = array(
            'id'     => 'checkDevice',
            'label'  => 'Verify scanned device node',
            'kind'   => 'mutation',
            'method' => 'POST',
            'url'    => rtrim($deviceApiBase, '/'),
            'query'  => array(
                'action' => 'check_device',
                // access_token must NOT appear in the URL — token is sent via Authorization header only.
            ),
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => $tokenBinding,
            ),
            'auth' => $authQuery,
            'body' => array(
                // node_id format expected by server: "UID:<hex> EF:V1" or bare hex — passed as scanned.
                'node_id'   => '{{scan.node_id}}',
                'latitude'  => '{{location.latitude}}',
                'longitude' => '{{location.longitude}}',
                // access_token must NOT be in the body — it belongs in the Authorization header only.
            ),
            'response' => array(
                // Issue #4: match the real check_device response fields exactly.
                'successField'     => 'success',
                'messageField'     => 'message',
                'statusField'      => 'status',
                // Additional fields returned by the real check_device handler:
                'nodeIdField'      => 'node_id',
                'deviceTypeField'  => 'device_type',
                'modelNumberField' => 'model_number',
                // status values: "success" = device is available to claim;
                // "already_claimed" (409) = owned by another user.
                'statusValues' => array(
                    'available'     => 'success',
                    'alreadyClaimed'=> 'already_claimed',
                    'notFound'      => 'not_found',
                ),
            ),
            'note' => 'POST https://efactor.tech/v8/iot_api_creation.php?action=check_device — Authorization header, JSON body {node_id, latitude, longitude}.',
        );

        $addWifiNode = array(
            'id'     => 'addWifiNode',
            'label'  => 'Add verified WiFi node to account',
            'kind'   => 'mutation',
            'method' => 'POST',
            'url'    => rtrim($deviceApiBase, '/'),
            'query'  => array(
                'action'  => 'add_wifi_node',
                // node_id is required as a query param for add_wifi_node per API contract.
                'node_id' => '{{scan.node_id}}',
                // access_token must NOT appear in the URL — sent via Authorization header only.
            ),
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => $tokenBinding,
            ),
            'auth' => $authQuery,
            'body' => array(
                'node_id'   => '{{scan.node_id}}',
                'latitude'  => '{{location.latitude}}',
                'longitude' => '{{location.longitude}}',
                // access_token must NOT be in the body — it belongs in the Authorization header only.
            ),
            'response' => array(
                // P0: success is only true after DB ownership is confirmed AND node visible in get_iot_devices.
                // The server returns status:"success" (not success:true) as the primary indicator.
                'successField'       => 'success',
                'statusField'        => 'status',
                'messageField'       => 'message',
                'successStatusValue' => 'success',
                // Added node context:
                'nodeIdField'        => 'node_id',
                'deviceNameField'    => 'device_name',
                // Diagnostic: server confirms node is visible in immediate get_iot_devices result.
                'visibleAfterAdd'    => 'visibleAfterAdd',
                // Refreshed device list returned in the same response — apply immediately.
                'devicesField'       => 'devices',
                'totalField'         => 'total',
                'onlineField'        => 'online',
                'offlineField'       => 'offline',
            ),
            // P0/Remove duplicate refresh: getDevices is the authoritative list — do NOT also call refreshDevices.
            'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
            'note' => 'POST https://efactor.tech/v8/iot_api_creation.php?action=add_wifi_node&node_id={node_id} — Authorization header, JSON body {node_id, latitude, longitude}.',
        );

        $refreshDevices = array(
            'id'     => 'refreshDevices',
            'label'  => 'Reload devices after QR add',
            'kind'   => 'query',
            'method' => 'GET',
            // Issue #6: mobile clients must NOT call the CI session-auth /iot-devices endpoint
            // because they have no web session cookie. They must call the v8 API with their
            // Bearer token — same endpoint as getDevices.
            'url'    => rtrim($deviceApiBase, '/'),
            'query'  => array(
                'action'       => 'get_iot_devices',
                'access_token' => $tokenBinding,
            ),
            'headers' => array(
                'Authorization' => $tokenBinding,
            ),
            'auth'   => $authQuery,
            'response' => array(
                'successField' => 'success',
                'root'         => 'devices',
                'devicesField' => 'devices',
                'idField'      => 'product_id',
                'sort'         => 'onlineFirst',
            ),
            // Web clients may use the CI session proxy for a faster, always-fresh list.
            'webProxy' => array(
                'method'        => 'GET',
                'path'          => 'iot-devices',
                'preferSession' => true,
                'appHomeBase'   => rtrim($appHomeBase, '/'),
                'dashboardBase' => rtrim($ciBase, '/') . '/devices',
                'note'          => 'Web-session proxy — do NOT use for mobile clients without a session cookie.',
            ),
        );

        $overlay = array(
            'id'           => 'qrScannerModal',
            'type'         => 'qrScanner',
            'presentation' => 'appScannerSheet',
            'title'        => 'Add Device',
            'subtitle'     => 'Align the QR code on your device inside the frame. We verify it, then register the WiFi node.',
            'layout'       => array(
                'variant'          => 'appScannerSheet',
                'maxWidth'         => 420,
                'sheetRadius'      => 24,
                'scannerMinHeight' => 340,
                'align'            => 'bottomOnMobile',
            ),
            // Breakpoint overrides for the scanner sheet.
            // Flutter caps the sheet to the actual available window — backend values
            // can never overflow a smaller device.
            // The scan→checkDevice→addWifiNode flow is NOT affected by these layout fields.
            'responsiveLayout' => array(
                'narrow'   => array(
                    'layout' => array(
                        'maxWidth'         => 'fill',
                        'sheetRadius'      => 20,
                        'scannerMinHeight' => 260,
                    ),
                    'scanner' => array(
                        'qrbox' => array('width' => 220, 'height' => 220),
                    ),
                ),
                'compact'  => array(
                    'layout' => array(
                        'maxWidth'         => 420,
                        'sheetRadius'      => 24,
                        'scannerMinHeight' => 300,
                    ),
                    'scanner' => array(
                        'qrbox' => array('width' => 250, 'height' => 250),
                    ),
                ),
                'medium'   => array(
                    'layout' => array(
                        'maxWidth'         => 520,
                        'sheetRadius'      => 28,
                        'scannerMinHeight' => 360,
                        'align'            => 'center',
                    ),
                    'scanner' => array(
                        'qrbox' => array('width' => 280, 'height' => 280),
                    ),
                ),
                'expanded' => array(
                    'layout' => array(
                        'maxWidth'         => 560,
                        'sheetRadius'      => 32,
                        'scannerMinHeight' => 400,
                        'align'            => 'center',
                    ),
                    'scanner' => array(
                        'qrbox' => array('width' => 300, 'height' => 300),
                    ),
                ),
            ),
            'theme'        => array(
                'accent'       => '#2563eb',
                'scannerBg'    => '#05070c',
                'frameAccent'  => '#3b82f6',
                'backdrop'     => 'rgba(15,23,42,.62)',
                'heroGradient' => array('#eff6ff', '#f8fafc'),
            ),
            'header'       => array(
                'title'     => 'Scan Device',
                'closeIcon' => 'x',
                'showBack'  => true,
                'rightAction' => array(
                    'id'        => 'manualNodeId',
                    'type'      => 'iconButton',
                    'control'   => 'manualNodeIdButton',
                    'icon'      => 'qrcode',
                    'placement' => 'header.right',
                    'label'     => "I don't have QR",
                    'ariaLabel' => "I don't have QR",
                    'title'     => "I don't have QR — enter node ID",
                    'style'     => array(
                        'background'   => '#f1f5f9',
                        'color'        => '#2563eb',
                        'borderRadius' => 12,
                        'padding'      => array('top' => 6, 'right' => 10, 'bottom' => 6, 'left' => 10),
                        'minHeight'    => 36,
                        'gap'          => 6,
                        'fontSize'     => 11,
                        'fontWeight'   => 800,
                    ),
                    'onTap'     => array(
                        'action'   => 'openPrompt',
                        'promptId' => 'manualNodeId',
                    ),
                ),
            ),
            'manualEntry'  => array(
                'id'       => 'manualNodeId',
                'type'     => 'prompt',
                'enabled'  => true,
                'title'    => 'Enter Node ID',
                'subtitle' => 'Type the device node ID to connect. We verify it, then register the node — same as scanning the QR code.',
                'statusMessage' => 'Node ID entered. Verifying device…',
                'input'    => array(
                    'id'             => 'nodeId',
                    'name'           => 'node_id',
                    'type'           => 'text',
                    'label'          => 'Node ID',
                    'placeholder'    => 'Enter node ID',
                    'required'       => true,
                    'uppercase'      => true,
                    'errorMessage'   => 'Please enter a Node ID.',
                    'autocapitalize' => 'off',
                    'autocomplete'   => 'off',
                    'spellcheck'     => false,
                ),
                'actions'  => array(
                    array(
                        'id'    => 'cancel',
                        'label' => 'Cancel',
                        'style' => 'ghost',
                        'onTap' => array('action' => 'closePrompt'),
                    ),
                    array(
                        'id'    => 'connect',
                        'label' => 'Connect',
                        'style' => 'primary',
                        'onTap' => array(
                            'action'     => 'submitNodeId',
                            'source'     => 'manualEntry.input',
                            'flow'       => array('checkDevice', 'addWifiNode'),
                            'sameAsScan' => true,
                        ),
                    ),
                ),
            ),
            'hero'         => array(
                'kicker'   => 'Add WiFi Node',
                'subtitle' => 'Align the QR code on your device inside the frame. We verify it, then register the node.',
                'icon'     => 'qrFrame',
            ),
            'scannerFrame' => array(
                'cornerRadius' => 28,
                'cornerSize'   => 28,
                'cornerBorder' => 3,
                'scanLine'     => true,
                'vignette'     => true,
                'aspectRatio'  => '1',
            ),
            'steps'        => array(
                array('id' => 'scan', 'label' => 'Scan'),
                array('id' => 'checkDevice', 'label' => 'Verify'),
                array('id' => 'addWifiNode', 'label' => 'Add'),
            ),
            'statusPill'   => array(
                'showDot'      => true,
                'idleClass'    => '',
                'errorClass'   => 'is-error',
                'successClass' => 'is-success',
            ),
            'scanner' => array(
                'engine'     => 'html5-qrcode',
                'libraryUrl' => 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js',
                'facingMode' => 'environment',
                'fps'        => 10,
                'qrbox'      => array('width' => 250, 'height' => 250),
                'hideLibraryFrame' => true,
                'normalize'  => array(
                    'trim'      => true,
                    'jsonField' => 'node_id',
                ),
            ),
            'location' => array(
                'provider' => 'geolocation',
                'required' => false,
                'fields'   => array('latitude', 'longitude'),
                'options'  => array(
                    'enableHighAccuracy' => true,
                    'timeout'            => 12000,
                    'maximumAge'         => 60000,
                ),
            ),
            'flow' => array(
                array(
                    'step'           => 'scan',
                    'label'          => 'Scan',
                    'statusMessage'  => 'Point your camera at the device QR code…',
                ),
                array(
                    'step'           => 'checkDevice',
                    'label'          => 'Verify',
                    'dataSource'     => 'checkDevice',
                    'statusMessage'  => 'QR scanned. Verifying device…',
                ),
                array(
                    'step'           => 'addWifiNode',
                    'label'          => 'Add',
                    'dataSource'     => 'addWifiNode',
                    'statusMessage'  => 'Verified. Adding WiFi node…',
                    'successMessage' => 'Device added successfully.',
                    // Issue #1: only refresh the authoritative device list. Do NOT call
                    // refreshDevices (redundant — same endpoint as getDevices) or getSchema.
                    'refreshAfter'   => array('getDevices', 'getAllNodesStatus'),
                    'closeDelayMs'   => 900,
                    // After successful add: show a success alert and navigate back to the
                    // IoT Devices page so the user sees the new device in the list.
                    'onSuccess' => array(
                        'showAlert' => array(
                            'title'   => 'Device Added',
                            'message' => 'Device added successfully. You can now control it from My Devices.',
                            'style'   => 'success',
                        ),
                        'navigate' => array(
                            'action' => 'popToRoute',
                            'target' => 'iotDevices',
                            'route'  => '/iot/devices',
                        ),
                    ),
                ),
            ),
            'actions' => array(
                array(
                    'id'    => 'cancel',
                    'label' => 'Cancel',
                    'style' => 'ghost',
                    'onTap' => array('action' => 'closeOverlay'),
                ),
            ),
            'status' => array(
                'errorClass'   => 'is-error',
                'successClass' => 'is-success',
            ),
        );

        $interaction = array(
            'type'        => 'openOverlay',
            'trigger'     => 'ui.headerActions.addDevice.onTap',
            'overlayId'   => 'qrScannerModal',
            'control'     => 'addDeviceButton',
            'flow'        => array('scan', 'checkDevice', 'addWifiNode'),
            // Issue #1: only refresh the authoritative device list after the overlay completes.
            // Do NOT call refreshDevices (redundant — same endpoint as getDevices) or getSchema.
            'refreshAfter'=> array('getDevices', 'getAllNodesStatus'),
            'note'        => 'Scan QR → checkDevice → addWifiNode. Replaces decorative header house icon.',
        );

        $manualInteraction = array(
            'type'        => 'openPrompt',
            'trigger'     => 'overlays.qrScannerModal.header.rightAction.onTap',
            'overlayId'   => 'qrScannerModal',
            'promptId'    => 'manualNodeId',
            'control'     => 'manualNodeIdButton',
            'flow'        => array('checkDevice', 'addWifiNode'),
            'refreshAfter'=> array('getDevices', 'getAllNodesStatus'),
            'note'        => 'Header QR button opens a node ID prompt. Submit runs the same checkDevice → addWifiNode flow as a scan.',
        );

        return array(
            'ui' => array(
                'matchPreview'      => true,
                'visibleComponents' => array('iot-summary-card', 'iot-device-grid'),
                'headerActions'     => array($headerAction),
                'overlayIds'        => array('qrScannerModal'),
            ),
            'overlays' => array(
                'qrScannerModal' => $overlay,
            ),
            'dataSources' => array(
                'checkDevice'     => $checkDevice,
                'addWifiNode'     => $addWifiNode,
                'refreshDevices'  => $refreshDevices,
            ),
            'interaction'  => $interaction,
            'manualInteraction' => $manualInteraction,
            'headerAction' => $headerAction,
        );
    }

    private function detectAppHomeApiBaseUrl(string $ciBase = ''): string
    {
        if (function_exists('site_url')) {
            return rtrim(site_url('app-home-page/api'), '/');
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host !== '') {
            return $scheme . '://' . $host . '/index.php/app-home-page/api';
        }

        if ($ciBase !== '') {
            return preg_replace('#/iot-dashboard/api$#', '/app-home-page/api', rtrim($ciBase, '/')) ?: $ciBase;
        }

        return 'https://efactor.tech/index.php/app-home-page/api';
    }

    private function getSearchIconSpec(): array
    {
        return array(
            'viewBox'        => '0 0 24 24',
            'width'          => 22,
            'height'         => 22,
            'strokeWidth'    => 2.2,
            'strokeLinecap'  => 'round',
            'strokeLinejoin' => 'round',
            'fill'           => 'none',
            'paths'          => array('M20 20l-3.5-3.5'),
            'circles'        => array(array('cx' => 11, 'cy' => 11, 'r' => 7)),
            'id'             => 'search',
            'type'           => 'svg',
            'svgMarkup'      => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>',
        );
    }

    private function getAddDeviceIconSpec(): array
    {
        return array(
            'viewBox'         => '0 0 24 24',
            'width'           => 22,
            'height'          => 22,
            'strokeWidth'     => 2.2,
            'strokeLinecap'   => 'round',
            'strokeLinejoin'  => 'round',
            'fill'            => 'none',
            'paths'           => array('M12 5v14', 'M5 12h14'),
            'id'              => 'add-device',
            'type'            => 'svg',
            'svgMarkup'       => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>',
        );
    }

    private function getQrCodeIconSpec(): array
    {
        return array(
            'viewBox'         => '0 0 24 24',
            'width'           => 18,
            'height'          => 18,
            'strokeWidth'     => 2,
            'strokeLinecap'   => 'round',
            'strokeLinejoin'  => 'round',
            'fill'            => 'none',
            'paths'           => array(
                'M4 4h6v6H4z',
                'M14 4h6v6h-6z',
                'M4 14h6v6H4z',
                'M14 14h3v3h-3z',
                'M19 14h2v2h-2z',
                'M14 19h2v2h-2z',
                'M18 18h3v3h-3z',
            ),
            'id'              => 'qrcode',
            'type'            => 'svg',
            'svgMarkup'       => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3z"/><path d="M19 14h2v2h-2z"/><path d="M14 19h2v2h-2z"/><path d="M18 18h3v3h-3z"/></svg>',
        );
    }

    /**
     * Keep saved schemas current with the Scan Device header QR → node ID prompt.
     */
    private function ensureManualNodeIdOverlay(array $schema, array $template): array
    {
        $tplOverlay = $template['overlays']['qrScannerModal'] ?? null;
        if (!is_array($tplOverlay)) {
            return $schema;
        }

        if (!isset($schema['overlays']) || !is_array($schema['overlays'])) {
            $schema['overlays'] = array();
        }

        $savedOverlay = $schema['overlays']['qrScannerModal'] ?? null;
        if (!is_array($savedOverlay) || $savedOverlay === array()) {
            $schema['overlays']['qrScannerModal'] = $tplOverlay;
            return $schema;
        }

        if (empty($savedOverlay['header']) || !is_array($savedOverlay['header'])) {
            $savedOverlay['header'] = $tplOverlay['header'] ?? array();
        } elseif (empty($savedOverlay['header']['rightAction']) && isset($tplOverlay['header']['rightAction'])) {
            $savedOverlay['header']['rightAction'] = $tplOverlay['header']['rightAction'];
        }

        if (empty($savedOverlay['manualEntry']) && isset($tplOverlay['manualEntry'])) {
            $savedOverlay['manualEntry'] = $tplOverlay['manualEntry'];
        }

        $savedOverlay = $this->syncManualNodeIdOverlayCopy($savedOverlay, $tplOverlay);
        $schema['overlays']['qrScannerModal'] = $savedOverlay;

        $contract = $schema['schemaContract']['interactions'] ?? null;
        $tplManual = $template['schemaContract']['interactions']['manualNodeId'] ?? null;
        if (is_array($contract) && empty($contract['manualNodeId']) && is_array($tplManual)) {
            $schema['schemaContract']['interactions']['manualNodeId'] = $tplManual;
        }

        if (empty($schema['icons']['qrcode']) && isset($template['icons']['qrcode'])) {
            $schema['icons']['qrcode'] = $template['icons']['qrcode'];
        }

        return $schema;
    }

    /**
     * Patch Scan Device copy/style/validation onto an existing overlay without
     * replacing the overlay object or renaming any keys.
     *
     * @param array<string, mixed> $savedOverlay
     * @param array<string, mixed> $tplOverlay
     * @return array<string, mixed>
     */
    private function syncManualNodeIdOverlayCopy(array $savedOverlay, array $tplOverlay): array
    {
        $tplRight = $tplOverlay['header']['rightAction'] ?? null;
        if (is_array($tplRight)) {
            $savedRight = $savedOverlay['header']['rightAction'] ?? array();
            if (!is_array($savedRight)) {
                $savedRight = array();
            }

            $staleCopy = array('Enter Node ID', 'Enter node ID');
            foreach (array('label', 'ariaLabel', 'title') as $copyKey) {
                $current = trim((string) ($savedRight[$copyKey] ?? ''));
                if ($current === '' || in_array($current, $staleCopy, true)) {
                    if (isset($tplRight[$copyKey])) {
                        $savedRight[$copyKey] = $tplRight[$copyKey];
                    }
                }
            }

            $tplStyle = $tplRight['style'] ?? null;
            if (is_array($tplStyle)) {
                $savedStyle = $savedRight['style'] ?? array();
                if (!is_array($savedStyle)) {
                    $savedStyle = array();
                }
                foreach ($tplStyle as $styleKey => $styleVal) {
                    if (!isset($savedStyle[$styleKey]) || $savedStyle[$styleKey] === '' || $savedStyle[$styleKey] === null) {
                        $savedStyle[$styleKey] = $styleVal;
                    }
                }
                $savedRight['style'] = $savedStyle;
            }

            if (!isset($savedOverlay['header']) || !is_array($savedOverlay['header'])) {
                $savedOverlay['header'] = array();
            }
            $savedOverlay['header']['rightAction'] = $savedRight;
        }

        $tplInput = $tplOverlay['manualEntry']['input'] ?? null;
        if (is_array($tplInput) && isset($savedOverlay['manualEntry']) && is_array($savedOverlay['manualEntry'])) {
            $savedInput = $savedOverlay['manualEntry']['input'] ?? array();
            if (!is_array($savedInput)) {
                $savedInput = array();
            }
            if (!array_key_exists('uppercase', $savedInput) && array_key_exists('uppercase', $tplInput)) {
                $savedInput['uppercase'] = $tplInput['uppercase'];
            }
            if ((empty($savedInput['errorMessage']) || !is_string($savedInput['errorMessage'])) && isset($tplInput['errorMessage'])) {
                $savedInput['errorMessage'] = $tplInput['errorMessage'];
            }
            $savedOverlay['manualEntry']['input'] = $savedInput;
        }

        return $savedOverlay;
    }

}
