<?php

namespace App\Libraries;

/**
 * Normalizes efactorproduct rows into IoT SDUI device payloads.
 */
class IotDeviceNormalizer
{
    public static function deviceFieldCatalog(): array
    {
        return array(
            'identity' => array(
                'product_id' => 'Unique efactorproduct row id (primary list key)',
                'id'         => 'Same as product_id — stable device list id',
                'node_id'    => 'Hardware node id — use for control APIs (power, card click)',
                'user_id'    => 'Owner user id',
            ),
            'model' => array(
                'model_id'     => 'Model identifier (model_number, else model_name)',
                'model_no'     => 'Model number alias used by mobile apps (same as model_number)',
                'modelName'    => 'Model display name from DB model_name',
                'modelNumber'  => 'Model number from DB model_number',
                'model_name'   => 'Snake_case alias of modelName',
                'model_number' => 'Snake_case alias of modelNumber',
                'moduleType'   => 'Connectivity module e.g. WIFI',
            ),
            'node' => array(
                'node.node_id'     => 'Hardware node id',
                'node.gateway_id'  => 'Gateway id (add_gateway)',
                'node.module_type' => 'Module type',
                'node.is_added'    => 'Whether device is added to gateway',
            ),
            'product' => array(
                'product_name' => 'User-facing product/display name',
                'productName'  => 'Device type label from device_type column',
                'device_type'  => 'Device type string',
                'devicetype'   => 'Alias of device_type',
                'name'         => 'Alias of product_name',
                'prod_category'=> 'Product category string',
                'category_id'  => 'Category id',
                'product_category_name' => 'Resolved category name',
            ),
            'brand' => array(
                'brand_name' => 'Brand name',
                'brandName'  => 'Alias of brand_name',
                'brandLogo'  => 'Brand logo URL',
                'brand_logo' => 'Alias of brandLogo',
                'vendor_id'  => 'Vendor/brand id',
                'vendor_name'=> 'Vendor name from DB',
            ),
            'media' => array(
                'productImage'  => 'Model image URL from vendorproducts.model_logo in files/model_logo. Category image is used only when the model has no logo.',
                'product_image' => 'Alias of productImage',
            ),
            'status' => array(
                'online'  => 'Boolean online state',
                'power'   => 'Boolean power on state',
                'status'  => 'Online or Offline label',
            ),
            'icons' => array(
                'resolvedIconId'  => 'Matched icon id from icons.catalog',
                'resolvedIcon'    => 'Full icon spec object',
                'resolvedIconSvg' => 'Inline SVG markup for fallback',
            ),
            'actions' => array(
                'actions.onCardClick'   => 'POST device_card_click with node_id',
                'actions.onPowerToggle' => 'POST device_power_toggle with node_id',
            ),
        );
    }

    /**
     * Normalize a raw DB row into a full device object.
     */
    public static function fromRow(array $row, string $apiBaseUrl = ''): array
    {
        $online      = self::isOnline($row['internet'] ?? null);
        $power       = self::isPowerOn($row['power'] ?? null);
        $productType = self::cleanText($row['device_type'] ?? '');
        $displayName = self::cleanText($row['name'] ?? '');
        $brandName   = self::cleanText($row['brand_name'] ?? $row['vendor_name'] ?? $row['vendor_type'] ?? '');
        $brandLogo   = trim((string) ($row['brand_logo'] ?? $row['device_brand_logo'] ?? ''));
        if ($brandLogo === '') {
            $brandLogo = self::probeBrandLogoFile($brandName);
        }
        $modelName   = self::cleanText($row['model_name'] ?? '');
        $modelNumber = self::cleanText($row['model_number'] ?? '');
        $modelId     = $modelNumber !== '' ? $modelNumber : $modelName;
        $productId   = (string) ($row['id'] ?? '');
        $nodeId      = self::cleanText($row['node_id'] ?? '');
        $gatewayId   = self::sanitizeDbValue($row['add_gateway'] ?? '');
        $moduleType  = self::sanitizeDbValue($row['module_type'] ?? '');
        $isAdded     = self::sanitizeDbValue($row['is_added'] ?? '');
        $accessToken = self::sanitizeDbValue($row['access_token'] ?? '');

        $device = array(
            'product_id' => $productId,
            'id'         => $productId !== '' ? $productId : ($nodeId !== '' ? $nodeId : 'device-' . uniqid()),
            'user_id'      => isset($row['user_id']) ? (int) $row['user_id'] : null,
            'node_id'      => $nodeId,
            'access_token' => $accessToken,

            'vendor_name'  => self::cleanText($row['vendor_name'] ?? ''),
            'vendor_id'    => self::sanitizeDbValue($row['vendor_id'] ?? ''),
            'category_id'  => self::sanitizeDbValue($row['category_id'] ?? ''),
            'prod_category'=> self::cleanText($row['prod_category'] ?? ''),
            'product_category_name' => self::cleanText($row['product_category_name'] ?? ''),

            'devicetype'   => $productType !== '' ? $productType : 'Device',
            'device_type'  => $productType !== '' ? $productType : 'Device',
            'productName'  => $productType !== '' ? $productType : 'Device',
            'product_name' => $displayName !== '' ? $displayName : ($productType !== '' ? $productType : 'Device'),
            'name'         => $displayName !== '' ? $displayName : ($productType !== '' ? $productType : 'Device'),

            'brandName'    => $brandName !== '' ? $brandName : 'Efactor',
            'brand_name'   => $brandName !== '' ? $brandName : 'Efactor',
            'brandLogo'    => self::assetUrl($brandLogo),
            'brand_logo'   => self::assetUrl($brandLogo),

            'productImage' => self::assetUrl($row['product_image'] ?? ''),
            'product_image'=> self::assetUrl($row['product_image'] ?? ''),

            'status'       => $online ? 'Online' : 'Offline',
            'online'       => $online,
            'power'        => $power,
            'powerOn'      => $power,

            'model_id'     => $modelId,
            'model_no'     => $modelNumber,
            'modelName'    => $modelName,
            'modelNumber'  => $modelNumber,
            'model_name'   => $modelName,
            'model_number' => $modelNumber,
            'moduleType'   => $moduleType,
            'gatewayId'    => $gatewayId,
            'isAdded'      => $isAdded,

            'node' => array(
                'node_id'     => $nodeId,
                'gateway_id'  => $gatewayId,
                'module_type' => $moduleType,
                'is_added'    => $isAdded,
            ),
        );

        return self::attachActions($device, $apiBaseUrl);
    }

    /**
     * Ensure an already-built device array has all canonical keys and node block.
     */
    public static function finalize(array $device, string $apiBaseUrl = ''): array
    {
        if (isset($device['id']) && !isset($device['product_id'])) {
            $device['product_id'] = (string) $device['id'];
        }
        if (isset($device['product_id']) && empty($device['id'])) {
            $device['id'] = (string) $device['product_id'];
        }

        $modelNumber = self::cleanText($device['modelNumber'] ?? $device['model_number'] ?? '');
        $modelName   = self::cleanText($device['modelName'] ?? $device['model_name'] ?? '');
        if (empty($device['model_id'])) {
            $device['model_id'] = $modelNumber !== '' ? $modelNumber : $modelName;
        }
        if (empty($device['model_name'])) {
            $device['model_name'] = $modelName;
        }
        if (empty($device['model_number'])) {
            $device['model_number'] = $modelNumber;
        }
        if (empty($device['model_no'])) {
            $device['model_no'] = $modelNumber !== '' ? $modelNumber : $modelName;
        }
        if (empty($device['modelNumber'])) {
            $device['modelNumber'] = $modelNumber;
        }

        $nodeId = self::cleanText($device['node_id'] ?? '');
        $gatewayId = self::sanitizeDbValue($device['gatewayId'] ?? $device['gateway_id'] ?? '');
        $moduleType = self::sanitizeDbValue($device['moduleType'] ?? $device['module_type'] ?? '');
        $isAdded = self::sanitizeDbValue($device['isAdded'] ?? $device['is_added'] ?? '');

        $device['node_id'] = $nodeId;
        $device['gatewayId'] = $gatewayId;
        $device['isAdded'] = $isAdded;
        $device['moduleType'] = $moduleType;
        $device['access_token'] = self::sanitizeDbValue($device['access_token'] ?? '');
        $device['node'] = array(
            'node_id'     => $nodeId,
            'gateway_id'  => $gatewayId,
            'module_type' => $moduleType,
            'is_added'    => $isAdded,
        );

        $online = array_key_exists('online', $device)
            ? self::isOnline($device['online'])
            : self::isOnline($device['internet'] ?? ($device['status'] ?? null));
        $power = self::isPowerOn($device['power'] ?? null);

        $device['online'] = $online;
        $device['power'] = $power;
        $device['powerOn'] = $power;
        $device['status'] = $online ? 'Online' : 'Offline';

        return self::attachActions($device, $apiBaseUrl);
    }

    public static function uniqueRows(array $rows): array
    {
        $seen   = array();
        $unique = array();
        foreach ($rows as $row) {
            $key = (string) ($row['id'] ?? ('row-' . count($unique)));
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[]   = $row;
        }

        return $unique;
    }

    private static function attachActions(array $device, string $apiBaseUrl): array
    {
        $base = rtrim($apiBaseUrl !== '' ? $apiBaseUrl : self::detectApiBaseUrl(), '/');
        $nodeId = (string) ($device['node_id'] ?? '');

        $device['actions'] = array(
            'onCardClick' => array(
                'method' => 'POST',
                'url'    => $base . '?action=device_card_click',
                'body'   => array('node_id' => $nodeId),
            ),
            'onPowerToggle' => array(
                'method' => 'POST',
                'url'    => $base . '?action=device_power_toggle',
                'headers' => array(
                    'Content-Type'       => 'application/json',
                    'X-IOT-Access-Token' => '{{auth.access_token}}',
                ),
                'body'   => array(
                    'node_id' => $nodeId,
                    'command' => '{{toggle_next|off:on}}',
                    'power'   => '{{toggle_next}}',
                ),
                'refreshAfter' => array('getDevices', 'getAllNodesStatus'),
                'responsePowerField' => 'power',
            ),
        );

        return $device;
    }

    public static function isOnline($value): bool
    {
        return in_array(strtolower(trim((string) $value)), array('1', 'true', 'yes', 'online', 'connected'), true);
    }

    public static function isPowerOn($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (int) $value === 1;
        }

        $normalized = strtolower(trim((string) $value));
        if ($normalized === '' || in_array($normalized, array('0', '00', 'false', 'no', 'off', 'null', 'undefined'), true)) {
            return false;
        }

        return in_array($normalized, array('1', '01', 'true', 'yes', 'on'), true);
    }

    private static function sanitizeDbValue($value): string
    {
        $value = trim((string) $value);
        if ($value === '' || strtoupper($value) === 'NULL') {
            return '';
        }

        return $value;
    }

    private static function cleanText($value): string
    {
        $value = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        $value = str_replace(array(chr(34) . '>', chr(39) . '>', '>'), ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = trim((string) $value);
        if (strtoupper($value) === 'NULL') {
            return '';
        }

        return $value;
    }

    public static function assetUrl(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return self::preferHttps($path);
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $scheme = self::publicScheme($host);
        if (strpos($path, '/') !== false) {
            return $host ? self::preferHttps($scheme . '://' . $host . '/' . ltrim($path, '/')) : '/' . ltrim($path, '/');
        }

        $url = $host ? $scheme . '://' . $host . '/files/brand_logo/' . rawurlencode($path) : '/files/brand_logo/' . rawurlencode($path);

        return self::preferHttps($url);
    }

    private static function publicScheme(string $host): string
    {
        $hostName = strtolower((string) preg_replace('/:\d+$/', '', $host));
        if (preg_match('/(^|\.)efactor\.tech$/', $hostName)) {
            return 'https';
        }
        $forwarded = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        if ($forwarded === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
            return 'https';
        }

        return 'http';
    }

    private static function preferHttps(string $url): string
    {
        $url = trim($url);
        if ($url === '' || stripos($url, 'http://') !== 0) {
            return $url;
        }
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        // Android/iOS block cleartext http for logos/avatars/QR — upgrade all non-local hosts.
        if ($host === '' || in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
            return $url;
        }

        return 'https://' . substr($url, 7);
    }

    /**
     * Last resort when the brand row has no logo column: use a file already
     * stored under files/brand_logo for that brand name.
     */
    private static function probeBrandLogoFile(string $brandName): string
    {
        $brandName = trim($brandName);
        if ($brandName === '') {
            return '';
        }
        $root = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
        if ($root === '') {
            return '';
        }
        $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '', $brandName));
        $names = array_values(array_unique(array_filter(array(
            $slug,
            strtolower(str_replace(' ', '', $brandName)),
        ))));
        $dirs = array('brand_logo', 'brand_logo_dark');
        $exts = array('png', 'jpg', 'jpeg', 'webp');
        foreach ($dirs as $dir) {
            foreach ($names as $name) {
                if ($name === '') {
                    continue;
                }
                foreach ($exts as $ext) {
                    $relative = 'files/' . $dir . '/' . $name . '.' . $ext;
                    $absolute = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
                    if (is_file($absolute)) {
                        return $relative;
                    }
                }
            }
        }

        return '';
    }

    private static function detectApiBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '' && is_file($docRoot . '/iot_api_creation.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/iot_api_creation.php';
        }
        if ($docRoot !== '' && is_file($docRoot . '/v8/iot_api_creation.php')) {
            return ($host ? $scheme . '://' . $host : '') . '/v8/iot_api_creation.php';
        }

        return 'https://efactor.tech/v8/iot_api_creation.php';
    }
}
