<?php

namespace App\Controllers;

use App\Models\AppHomePageModel;
use App\Models\IotDashboardModel;
use CodeIgniter\HTTP\ResponseInterface;

class AppHomePageController extends Security_Controller
{
    protected AppHomePageModel $appHomePageModel;

    /** Resolved public web root used for the upload, e.g. /var/www/html */
    private ?string $faceImageFilesRoot = null;

    /** Public URL path under web root, e.g. uploads/app_home_page_images/655 */
    private ?string $faceImagePublicRel = null;

    public function __construct()
    {
        $uri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));
        $isApi = strpos($uri, 'app-home-page/api') !== false;
        parent::__construct(! $isApi);
        $this->appHomePageModel = new AppHomePageModel();
    }

    public function index()
    {
        helper(array('url'));

        $userId = (int) $this->login_user->id;
        $userEmail = $this->resolveUserEmail();
        $this->appHomePageModel->ensureUserDefaults($userId, $userEmail);
        $defaultLayout = $this->appHomePageModel->getDefaultLayout($userEmail, $userId);
        $savedLayout = $this->appHomePageModel->getLayout($userId);
        $layout = $this->appHomePageModel->mergeWithDefaults($defaultLayout, $savedLayout);
        $layout = $this->applyDbUserToProfile($layout, $userId);
        $loginUserProfile = $this->appHomePageModel->getUserProfile($userId);
        $schema = $this->appHomePageModel->getSchema($userId);
        $settingsMeta = $this->appHomePageModel->getSettingsMeta($userId);
        $iotDashboardModel = new IotDashboardModel();
        $iotDevices = array();
        $iotAccessToken = '';
        // New users get a default IoT schema on first App Home open (no manual Skema Generate).
        try {
            $iotDashboardModel->ensureSchemaForUser($userId, false);
        } catch (\Throwable $e) {
            log_message('error', 'App Home IoT schema ensure failed for user ' . $userId . ': ' . $e->getMessage());
        }
        try {
            $iotDevices = $iotDashboardModel->getDevicesForUser($userId);
        } catch (\Throwable $e) {
            log_message('error', 'App Home IoT devices failed for user ' . $userId . ': ' . $e->getMessage());
        }
        try {
            $iotAccessToken = $iotDashboardModel->resolveOnboardingAccessToken($userId, false, $iotDevices);
        } catch (\Throwable $e) {
            log_message('error', 'App Home IoT token failed for user ' . $userId . ': ' . $e->getMessage());
        }

        return view('app_home_page/index', array(
            'layout' => $layout,
            'defaultLayout' => $defaultLayout,
            'schema' => $schema,
            'settingsMeta' => $settingsMeta,
            'loginUserProfile' => $loginUserProfile,
            'userEmail' => $userEmail,
            'userId' => $userId,
            'apiBase' => site_url('app-home-page/api'),
            'iotApiUrl' => $iotDashboardModel->getPublicApiBaseUrl(),
            'iotAccessToken' => $iotAccessToken,
            'schemaApiUrl' => $this->appHomePageModel->getSchemaApiUrl($userId),
            'gridApiUrl' => $this->appHomePageModel->getGridApiUrl($userId),
            'pageUrl' => site_url('app-home-page'),
            'dashboardUrl' => site_url(''),
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ));
    }

    public function api(string $action = ''): ResponseInterface
    {
        if ((int) ($this->login_user->id ?? 0) <= 0) {
            return $this->jsonResponse(array('success' => false, 'message' => 'Unauthorized'), 401);
        }

        switch ($action) {
            case 'layout':
                if ($this->request->getMethod() === 'get') {
                    return $this->jsonResponse($this->getLayoutPayload());
                }
                if (in_array($this->request->getMethod(), array('post', 'put', 'patch'), true)) {
                    return $this->jsonResponse($this->saveLayoutPayload());
                }
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);

            case 'profile':
                if ($this->request->getMethod() !== 'get') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $userId = (int) $this->login_user->id;
                $profile = $this->appHomePageModel->getUserProfile($userId);
                return $this->jsonResponse(array(
                    'success' => true,
                    'profile' => $profile,
                ));

            case 'profile-preferences':
                if ($this->request->getMethod() !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                return $this->jsonResponse($this->saveProfilePreferencesPayload());

            case 'reset':
                if ($this->request->getMethod() !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $userId = (int) $this->login_user->id;
                $userEmail = $this->resolveUserEmail();
                $defaultLayout = $this->appHomePageModel->getDefaultLayout($userEmail, $userId);
                $defaultLayout = $this->applyDbUserToProfile($defaultLayout, $userId);
                $defaultLayout = $this->appHomePageModel->sanitizeLayoutForSchema($defaultLayout);
                $schema = \App\Libraries\AppHomeSchemaBuilder::build(
                    $defaultLayout,
                    $this->appHomePageModel->buildSchemaConfig($userId, $userEmail)
                );
                $this->appHomePageModel->saveSettings($userId, $defaultLayout, $schema ?: array());
                return $this->jsonResponse(array(
                    'success' => true,
                    'layout' => $defaultLayout,
                    'schema' => $schema ?: array(),
                    'schemaApiUrl' => $this->appHomePageModel->getSchemaApiUrl($userId),
                ));

            case 'schema':
                if ($this->request->getMethod() === 'get') {
                    $userId = (int) $this->login_user->id;
                    $schema = $this->appHomePageModel->getSchema($userId);
                    return $this->jsonResponse(array(
                        'success' => true,
                        'schema' => $schema,
                        'schemaApiUrl' => $this->appHomePageModel->getSchemaApiUrl($userId),
                        'gridApiUrl' => $this->appHomePageModel->getGridApiUrl($userId),
                        'meta' => $this->appHomePageModel->getSettingsMeta($userId),
                    ));
                }
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);

            case 'grid':
                if ($this->request->getMethod() !== 'get') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $userId = (int) $this->login_user->id;
                $layout = $this->appHomePageModel->mergeWithDefaults(
                    $this->appHomePageModel->getDefaultLayout(
                        (string) (session()->get('email') ?? ''),
                        $userId
                    ),
                    $this->appHomePageModel->getLayout($userId)
                );
                $content = is_array($layout['gridPage'] ?? null) ? $layout['gridPage'] : array();
                $gridApiUrl = $this->appHomePageModel->getGridApiUrl($userId);
                $ssoToken = $userId > 0 ? \App\Libraries\GridWebSso::issue($userId) : '';
                $schema = \App\Libraries\GridSduiSchemaBuilder::build(array(
                    'userId' => $userId,
                    'content' => $content,
                    'endpoint' => $gridApiUrl,
                    'ssoToken' => $ssoToken,
                ));
                return $this->jsonResponse(array(
                    'success' => true,
                    'pageId' => 'grid',
                    'routeId' => 'grid',
                    'gridApiUrl' => $gridApiUrl,
                    'content' => $schema['content'] ?? $content,
                    'schema' => $schema,
                ));

            case 'iot-access-token':
                if ($this->request->getMethod() !== 'get') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $iotModel = new IotDashboardModel();
                $userId = (int) $this->login_user->id;
                $devices = $iotModel->getDevicesForUser($userId);
                return $this->jsonResponse(array(
                    'success' => true,
                    'access_token' => $iotModel->resolveOnboardingAccessToken($userId, false, $devices),
                    'iotApiUrl' => $iotModel->getPublicApiBaseUrl(),
                ));

            case 'iot-devices':
                if ($this->request->getMethod() !== 'get') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                try {
                    $iotModel = new IotDashboardModel();
                    $userId = (int) ($this->login_user->id ?? 0);
                    if ($userId <= 0) {
                        return $this->jsonResponse(array(
                            'success' => false,
                            'message' => 'Not signed in',
                        ), 401);
                    }
                    return $this->jsonResponse($iotModel->fetchRemoteIotDevices($userId));
                } catch (\Throwable $e) {
                    log_message('error', 'iot-devices endpoint failed: ' . $e->getMessage());
                    return $this->jsonResponse(array(
                        'success' => false,
                        'message' => 'IoT devices request failed: ' . $e->getMessage(),
                        'devices' => array(),
                    ), 200);
                }

            case 'iot-schema':
                if ($this->request->getMethod() !== 'get') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                try {
                    $iotModel = new IotDashboardModel();
                    $userId = (int) ($this->login_user->id ?? 0);
                    if ($userId <= 0) {
                        return $this->jsonResponse(array(
                            'success' => false,
                            'message' => 'Not signed in',
                        ), 401);
                    }
                    $payload = $iotModel->fetchRemoteIotSchemaForStudio($userId);
                    // fetchRemoteIotSchema already calls ensureSchemaForUser; keep a hard fallback.
                    if (empty($payload['success']) || empty($payload['schema'])) {
                        try {
                            $ensured = $iotModel->ensureSchemaForUser($userId);
                            if (!empty($ensured['schema'])) {
                                log_message('info', 'IoT schema auto-generated via iot-schema API for user ' . $userId);
                                $payload = $iotModel->fetchRemoteIotSchemaForStudio($userId);
                            }
                        } catch (\Throwable $genErr) {
                            log_message('error', 'IoT schema auto-gen in iot-schema API failed: ' . $genErr->getMessage());
                        }
                    }
                    return $this->jsonResponse($payload);
                } catch (\Throwable $e) {
                    log_message('error', 'iot-schema endpoint failed: ' . $e->getMessage());
                    return $this->jsonResponse(array(
                        'success' => false,
                        'message' => 'IoT schema request failed: ' . $e->getMessage(),
                    ), 200);
                }

            case 'check-device':
                if (strtolower($this->request->getMethod()) !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $iotModel = new IotDashboardModel();
                $userId = (int) ($this->login_user->id ?? 0);
                if ($userId <= 0) {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Not signed in'), 401);
                }
                $payload = $this->request->getJSON(true);
                if (!is_array($payload)) {
                    $payload = $this->request->getPost();
                }
                if (!is_array($payload)) {
                    $payload = array();
                }
                $nodeId = $iotModel->extractNodeIdFromPayload($payload);
                if ($nodeId === '') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'node_id is required.'), 400);
                }
                $devices = $iotModel->getDevicesForUser($userId);
                $accessToken = $iotModel->resolveOnboardingAccessToken($userId, false, $devices);
                $payload['node_id'] = $nodeId;
                $payload['access_token'] = $accessToken;
                $result = $iotModel->proxyV8DeviceApi('check_device', $payload, $accessToken, $nodeId);
                if (!isset($result['success'])) {
                    $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
                }
                $result['node_id'] = $nodeId;
                return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);

            case 'add-wifi-node':
                if (strtolower($this->request->getMethod()) !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $iotModel = new IotDashboardModel();
                $userId = (int) ($this->login_user->id ?? 0);
                if ($userId <= 0) {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Not signed in'), 401);
                }
                $payload = $this->request->getJSON(true);
                if (!is_array($payload)) {
                    $payload = $this->request->getPost();
                }
                if (!is_array($payload)) {
                    $payload = array();
                }
                $nodeId = $iotModel->extractNodeIdFromPayload($payload);
                if ($nodeId === '') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'node_id is required.'), 400);
                }
                $devices = $iotModel->getDevicesForUser($userId);
                $accessToken = $iotModel->resolveOnboardingAccessToken($userId, false, $devices);
                $payload['node_id'] = $nodeId;
                $payload['access_token'] = $accessToken;
                $result = $iotModel->proxyV8DeviceApi('add_wifi_node', $payload, $accessToken, $nodeId);
                if (!isset($result['success'])) {
                    $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
                }
                $result['node_id'] = $nodeId;
                // After a successful add, return the refreshed device list so the app
                // can update "My Devices" immediately without a separate round-trip.
                if (!empty($result['success'])) {
                    try {
                        $refreshed = $iotModel->fetchRemoteIotDevices($userId);
                        $result['devices'] = $refreshed['devices'] ?? array();
                        $result['total']   = $refreshed['total']   ?? 0;
                        $result['online']  = $refreshed['online']  ?? 0;
                        $result['offline'] = $refreshed['offline'] ?? 0;
                    } catch (\Throwable $e) {
                        log_message('error', 'add-wifi-node post-refresh failed: ' . $e->getMessage());
                    }
                }
                return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);

            case 'device-power-toggle':
                if (strtolower($this->request->getMethod()) !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $iotModel = new IotDashboardModel();
                $userId = (int) ($this->login_user->id ?? 0);
                if ($userId <= 0) {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Not signed in'), 401);
                }
                $payload = $this->request->getJSON(true);
                if (!is_array($payload)) {
                    $payload = $this->request->getPost();
                }
                if (!is_array($payload)) {
                    $payload = array();
                }
                $result = $iotModel->toggleDevicePower($userId, $payload);
                return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);

            case 'remove-node':
                if (strtolower($this->request->getMethod()) !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                $iotModel = new IotDashboardModel();
                $userId = (int) ($this->login_user->id ?? 0);
                if ($userId <= 0) {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Not signed in'), 401);
                }
                $payload = $this->request->getJSON(true);
                if (!is_array($payload)) {
                    $payload = $this->request->getPost();
                }
                if (!is_array($payload)) {
                    $payload = array();
                }
                $nodeId = $iotModel->extractNodeIdFromPayload($payload);
                if ($nodeId === '') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'node_id is required.'), 400);
                }
                $devices = $iotModel->getDevicesForUser($userId);
                $accessToken = $iotModel->resolveOnboardingAccessToken($userId, false, $devices);
                $result = $iotModel->removeDeviceNode($nodeId, $accessToken);
                if (!isset($result['success'])) {
                    $result['success'] = !isset($result['status']) || strtolower((string) $result['status']) !== 'failure';
                }
                $result['node_id'] = $nodeId;
                // After a successful remove, return the refreshed device list so the app
                // can update "My Devices" immediately without a separate round-trip.
                if (!empty($result['success'])) {
                    try {
                        $refreshed = $iotModel->fetchRemoteIotDevices($userId);
                        $result['devices'] = $refreshed['devices'] ?? array();
                        $result['total']   = $refreshed['total']   ?? 0;
                        $result['online']  = $refreshed['online']  ?? 0;
                        $result['offline'] = $refreshed['offline'] ?? 0;
                    } catch (\Throwable $e) {
                        log_message('error', 'remove-node post-refresh failed: ' . $e->getMessage());
                    }
                }
                return $this->jsonResponse($result, !empty($result['success']) ? 200 : 422);

            case 'upload-face-image':
                if ($this->request->getMethod() !== 'post') {
                    return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
                }
                try {
                    $result = $this->uploadFaceImagePayload();
                    $status = !empty($result['success']) ? 200 : 422;
                    return $this->jsonResponse($result, $status);
                } catch (\Throwable $e) {
                    log_message('error', 'upload-face-image failed: ' . $e->getMessage());
                    return $this->jsonResponse(array(
                        'success' => false,
                        'message' => 'Upload failed: ' . $e->getMessage(),
                    ), 200);
                }

            default:
                return $this->jsonResponse(array('success' => false, 'message' => 'Unknown action'), 404);
        }
    }

    private function getLayoutPayload(): array
    {
        $userId = (int) $this->login_user->id;
        $userEmail = $this->resolveUserEmail();
        $this->appHomePageModel->ensureUserDefaults($userId, $userEmail);
        $defaultLayout = $this->appHomePageModel->getDefaultLayout($userEmail, $userId);
        $saved = $this->appHomePageModel->getLayout($userId);
        $layout = $this->appHomePageModel->mergeWithDefaults($defaultLayout, $saved);
        $layout = $this->applyDbUserToProfile($layout, $userId);
        $schema = $this->appHomePageModel->getSchema($userId);

        return array(
            'success' => true,
            'layout' => $layout,
            'profile' => $this->appHomePageModel->getUserProfile($userId),
            'schema' => $schema,
            'schemaApiUrl' => $this->appHomePageModel->getSchemaApiUrl($userId),
            'meta' => $this->appHomePageModel->getSettingsMeta($userId),
        );
    }

    private function saveLayoutPayload(): array
    {
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        if (!is_array($payload) || empty($payload['layout']) || !is_array($payload['layout'])) {
            return array('success' => false, 'message' => 'Invalid layout payload');
        }

        if (empty($payload['schema']) || !is_array($payload['schema'])) {
            return array('success' => false, 'message' => 'Schema is required. Generate schema from the mobile preview before saving.');
        }

        $userId = (int) $this->login_user->id;
        $layout = $this->sanitizeLayoutForSave($payload['layout']);
        $layout = $this->applyDbUserToProfile($layout, $userId);
        $layout = $this->appHomePageModel->sanitizeLayoutForSchema($layout);
        // Rebuild from V3 provisioner so get_schema matches Flutter's server-driven-mobile-app contract
        // (including static UI templates when selected).
        $schema = \App\Libraries\AppHomeSchemaBuilder::build(
            $layout,
            $this->appHomePageModel->buildSchemaConfig($userId, $this->resolveUserEmail())
        );
        if (empty($schema) || !is_array($schema)) {
            // Fall back to client schema only if Node provision fails.
            $schema = $payload['schema'];
        }
        try {
            $saved = $this->appHomePageModel->saveSettings($userId, $layout, $schema);
        } catch (\Throwable $e) {
            log_message('error', 'App home saveSettings exception: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Failed to save layout and schema to database: ' . $e->getMessage(),
            );
        }

        if (!$saved) {
            return array(
                'success' => false,
                'message' => 'Failed to save layout and schema to database',
            );
        }

        $storedLayout = $this->appHomePageModel->getLayout($userId);
        if (!empty($storedLayout)) {
            $layout = $storedLayout;
        }

        return array(
            'success' => true,
            'message' => 'Layout and schema saved to database',
            'layout' => $layout,
            'profile' => $this->appHomePageModel->getUserProfile($userId),
            'schema' => $this->appHomePageModel->getSchema($userId),
            'schemaApiUrl' => $this->appHomePageModel->getSchemaApiUrl($userId),
            'meta' => $this->appHomePageModel->getSettingsMeta($userId),
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
    }

    private function sanitizeLayoutForSave(array $layout): array
    {
        unset($layout['activeFacePage']);

        if (!isset($layout['activePage']) || $layout['activePage'] === '') {
            $layout['activePage'] = 'home';
        }
        if ($layout['activePage'] === 'settings') {
            $layout['activePage'] = 'profile';
        }

        // Home, IoT, Grid, Profile for every user. Drop a saved Alerts/Reports third tab.
        $layout = \App\Models\AppHomePageModel::applySharedBottomNav($layout);

        // Keep UI template selection for schema rebuild.
        if (isset($layout['theme']) && is_array($layout['theme']) && !empty($layout['theme']['staticUiTemplate']) && empty($layout['staticUiTemplate'])) {
            $layout['staticUiTemplate'] = $layout['theme']['staticUiTemplate'];
        }
        if (!empty($layout['staticUiTemplate']) && isset($layout['theme']) && is_array($layout['theme'])) {
            $layout['theme']['staticUiTemplate'] = $layout['staticUiTemplate'];
        }

        return $layout;
    }

    private function resolveUserEmail(): string
    {
        if ($this->login_user && !empty($this->login_user->email)) {
            return (string) $this->login_user->email;
        }

        if ($this->login_user && !empty($this->login_user->first_name)) {
            return (string) $this->login_user->first_name;
        }

        return '';
    }

    /**
     * Overlay live users-table fields onto layout profile (name, email, phone, member ID).
     * Language and notification prefs remain from saved layout.
     */
    private function applyDbUserToProfile(array $layout, int $userId): array
    {
        $profile = $this->appHomePageModel->getUserProfile($userId);

        if (!isset($layout['profilePage']) || !is_array($layout['profilePage'])) {
            $layout['profilePage'] = array();
        }

        $language = $layout['profilePage']['language'] ?? 'English (India)';
        $notificationsEnabled = array_key_exists('notificationsEnabled', $layout['profilePage'])
            ? (bool) $layout['profilePage']['notificationsEnabled']
            : true;
        $languageOptions = $layout['profilePage']['languageOptions'] ?? null;
        $privacyPolicyText = $layout['profilePage']['privacyPolicyText'] ?? ($layout['profilePage']['privacyPolicy'] ?? null);

        $layout['profilePage']['name'] = $profile['name'];
        $layout['profilePage']['email'] = $profile['email'];
        $layout['profilePage']['phone'] = $profile['phone'];
        $layout['profilePage']['memberId'] = $profile['memberId'];
        $layout['profilePage']['avatarUrl'] = $profile['avatarUrl'] ?? '';
        $layout['profilePage']['language'] = $language;
        $layout['profilePage']['notificationsEnabled'] = $notificationsEnabled;
        if (is_array($languageOptions) && $languageOptions !== array()) {
            $layout['profilePage']['languageOptions'] = $languageOptions;
        }
        if (is_string($privacyPolicyText) && trim($privacyPolicyText) !== '') {
            $layout['profilePage']['privacyPolicyText'] = trim($privacyPolicyText);
        }

        if ($profile['email'] !== '') {
            if (!isset($layout['welcome']) || !is_array($layout['welcome'])) {
                $layout['welcome'] = array();
            }
            $layout['welcome']['email'] = $profile['email'];
        }

        return $layout;
    }

    /**
     * Persist Profile tab preferences (language / notifications) and rebuild schema.
     */
    private function saveProfilePreferencesPayload(): array
    {
        $userId = (int) ($this->login_user->id ?? 0);
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Not signed in');
        }

        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }
        if (!is_array($payload)) {
            $payload = array();
        }

        $userEmail = $this->resolveUserEmail();
        $layout = $this->appHomePageModel->mergeWithDefaults(
            $this->appHomePageModel->getDefaultLayout($userEmail, $userId),
            $this->appHomePageModel->getLayout($userId)
        );
        $layout = $this->applyDbUserToProfile($layout, $userId);
        if (!isset($layout['profilePage']) || !is_array($layout['profilePage'])) {
            $layout['profilePage'] = array();
        }

        $changed = false;
        if (array_key_exists('notificationsEnabled', $payload)) {
            $layout['profilePage']['notificationsEnabled'] = filter_var(
                $payload['notificationsEnabled'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
            if ($layout['profilePage']['notificationsEnabled'] === null) {
                $layout['profilePage']['notificationsEnabled'] = !empty($payload['notificationsEnabled']);
            }
            $changed = true;
        }
        if (isset($payload['language']) && is_string($payload['language'])) {
            $language = trim($payload['language']);
            if ($language !== '' && mb_strlen($language) <= 64) {
                $allowed = $layout['profilePage']['languageOptions'] ?? array(
                    'English (India)', 'English (US)', 'Hindi', 'Arabic', 'French', 'Spanish',
                );
                if (!is_array($allowed) || $allowed === array() || in_array($language, $allowed, true)) {
                    $layout['profilePage']['language'] = $language;
                    $changed = true;
                }
            }
        }
        if (isset($payload['privacyPolicyText']) && is_string($payload['privacyPolicyText'])) {
            $text = trim($payload['privacyPolicyText']);
            if ($text !== '' && mb_strlen($text) <= 8000) {
                $layout['profilePage']['privacyPolicyText'] = $text;
                $changed = true;
            }
        }

        if (!$changed) {
            return array(
                'success' => false,
                'message' => 'No valid profile preferences supplied',
            );
        }

        $layout = $this->appHomePageModel->sanitizeLayoutForSchema($layout);
        $schema = \App\Libraries\AppHomeSchemaBuilder::build(
            $layout,
            $this->appHomePageModel->buildSchemaConfig($userId, $userEmail)
        );
        $this->appHomePageModel->saveSettings($userId, $layout, $schema ?: array());

        return array(
            'success' => true,
            'message' => 'Profile preferences saved',
            'profilePage' => $layout['profilePage'],
            'schema' => $schema ?: array(),
        );
    }

    private function uploadFaceImagePayload(): array
    {
        $userId = (int) ($this->login_user->id ?? 0);
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Not signed in');
        }

        if (empty($_FILES) && strtoupper($this->request->getMethod()) === 'POST') {
            $postMax = ini_get('post_max_size') ?: 'unknown';
            $uploadMax = ini_get('upload_max_filesize') ?: 'unknown';
            return array(
                'success' => false,
                'message' => 'Upload rejected by server limits (post_max_size=' . $postMax . ', upload_max_filesize=' . $uploadMax . ')',
            );
        }

        $file = $this->request->getFile('image');

        if (!$file || !$file->isValid()) {
            $err = 'missing file field "image"';
            if ($file) {
                try {
                    $err = $file->getErrorString() . ' (code ' . $file->getError() . ')';
                } catch (\Throwable $e) {
                    $err = 'invalid upload';
                }
            }
            return array('success' => false, 'message' => 'No valid image uploaded (' . $err . ')');
        }

        $allowed = array('image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg', 'image/pjpeg');
        $mime = strtolower((string) $file->getMimeType());
        $clientMime = strtolower((string) $file->getClientMimeType());
        if (!in_array($mime, $allowed, true) && !in_array($clientMime, $allowed, true)) {
            $ext = strtolower((string) $file->getClientExtension());
            $allowedExt = array('jpg', 'jpeg', 'png', 'webp', 'gif');
            if (!in_array($ext, $allowedExt, true)) {
                return array('success' => false, 'message' => 'Please upload a JPG, PNG, WEBP, or GIF image');
            }
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return array('success' => false, 'message' => 'Image is too large (max 5 MB)');
        }

        $uploadDir = $this->resolveFaceImageUploadDir($userId);
        if ($uploadDir === null) {
            return array(
                'success' => false,
                'message' => $this->faceImageUploadFolderError($userId),
            );
        }

        $ext = strtolower((string) $file->getExtension());
        if ($ext === '') {
            $ext = strtolower((string) $file->getClientExtension());
        }
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        if ($ext === '') {
            $extMap = array(
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/pjpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
            );
            $ext = $extMap[$mime] ?? $extMap[$clientMime] ?? 'jpg';
        }

        try {
            $suffix = bin2hex(random_bytes(4));
        } catch (\Throwable $e) {
            $suffix = substr(md5(uniqid('', true)), 0, 8);
        }
        $newName = 'face_' . time() . '_' . $suffix . '.' . preg_replace('/[^a-z0-9]+/', '', $ext);

        try {
            if (!$file->move($uploadDir, $newName, true)) {
                return array(
                    'success' => false,
                    'message' => 'Failed to save uploaded image. Check folder permissions for uploads/app_home_page_images/',
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'Face image move failed: ' . $e->getMessage());
            return array('success' => false, 'message' => 'Failed to save uploaded image: ' . $e->getMessage());
        }

        $publicRel = $this->faceImagePublicRel
            ?: ('uploads/app_home_page_images/' . $userId);
        $publicUrl = rtrim(base_url($publicRel), '/') . '/' . rawurlencode($newName);

        $cardId = trim((string) ($this->request->getPost('card_id') ?? ''));
        $faceIndexRaw = $this->request->getPost('face_index');
        $faceIndex = ($faceIndexRaw === null || $faceIndexRaw === '') ? null : max(0, min(3, (int) $faceIndexRaw));

        $savedToDb = false;
        try {
            $savedToDb = $this->appHomePageModel->recordFaceImage(
                $userId,
                $newName,
                rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $newName,
                $publicUrl,
                $cardId !== '' ? $cardId : null,
                $faceIndex,
                $mime !== '' ? $mime : $clientMime,
                (int) $file->getSize()
            );
        } catch (\Throwable $e) {
            log_message('error', 'Face image DB log failed: ' . $e->getMessage());
        }

        return array(
            'success' => true,
            'message' => $savedToDb ? 'Image uploaded' : 'Image saved (database log skipped)',
            'url' => $publicUrl,
            'fileName' => $newName,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
    }

    /**
     * Preferred public folders under www (first writable wins).
     * Prefer uploads/ — files/ is often owned by root and not writable by PHP.
     *
     * @return list<string>
     */
    private function faceImageRelativeBases(): array
    {
        return array(
            'uploads/app_home_page_images',
            'assets/uploads/app_home_page_images',
            'files/app_home_page_images',
        );
    }

    /**
     * Resolve a writable upload directory under a public www folder.
     */
    private function resolveFaceImageUploadDir(int $userId): ?string
    {
        $this->faceImageFilesRoot = null;
        $this->faceImagePublicRel = null;
        $errors = array();

        foreach ($this->resolvePublicFilesRootCandidates() as $root) {
            foreach ($this->faceImageRelativeBases() as $relBase) {
                $relBase = trim(str_replace('\\', '/', $relBase), '/');
                $parent = $this->normalizePath($root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relBase));
                $dir = $this->normalizePath($parent . DIRECTORY_SEPARATOR . $userId);

                if (!$this->ensureWritableDirectory($dir, $errors)) {
                    continue;
                }
                if (!is_writable($dir)) {
                    @chmod($dir, 0777);
                }
                if (!is_writable($dir)) {
                    $errors[] = $dir;
                    continue;
                }

                $this->faceImageFilesRoot = $root;
                $this->faceImagePublicRel = $relBase . '/' . $userId;
                $this->ensurePublicFilesAccess($parent);
                $this->ensureUploadGuardFiles($dir);

                return $dir . DIRECTORY_SEPARATOR;
            }
        }

        if (!empty($errors)) {
            log_message('error', 'Face image upload folder not writable. Tried: ' . implode('; ', array_unique($errors)));
        }

        return null;
    }

    /**
     * Candidate web roots where a public files/uploads folder may live.
     *
     * @return list<string>
     */
    private function resolvePublicFilesRootCandidates(): array
    {
        $roots = array();
        $envRoot = trim((string) (getenv('APP_HOME_FILES_ROOT') ?: ''));
        if ($envRoot !== '') {
            $roots[] = $envRoot;
        }

        $baseCandidates = array();
        if (defined('FCPATH')) {
            $baseCandidates[] = rtrim(FCPATH, '/\\');
        }
        if (defined('ROOTPATH')) {
            $baseCandidates[] = dirname(rtrim(ROOTPATH, '/\\'));
            $baseCandidates[] = rtrim(ROOTPATH, '/\\');
        }
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '') {
            $baseCandidates[] = $docRoot;
        }
        $cwd = getcwd();
        if (is_string($cwd) && $cwd !== '') {
            $baseCandidates[] = rtrim($cwd, '/\\');
        }

        $baseCandidates = array_values(array_unique(array_filter($baseCandidates)));

        // Prefer roots that already have a writable uploads/ or files/ tree.
        foreach ($baseCandidates as $root) {
            foreach (array('uploads', 'files/brand_logo', 'files/brandLogo', 'files') as $probe) {
                $path = $this->normalizePath($root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $probe));
                if (is_dir($path) && is_writable($path)) {
                    $roots[] = $root;
                    break;
                }
            }
        }

        foreach ($baseCandidates as $root) {
            $roots[] = $root;
        }

        return array_values(array_unique(array_filter($roots)));
    }

    /**
     * Create each segment of a directory path and attempt to make it writable.
     */
    private function ensureWritableDirectory(string $targetDir, array &$errors = array()): bool
    {
        $targetDir = $this->normalizePath($targetDir);
        if ($targetDir === '') {
            return false;
        }

        $normalized = str_replace('\\', '/', $targetDir);
        $isWindowsDrive = (bool) preg_match('/^[A-Za-z]:/', $normalized);
        $parts = explode('/', trim($normalized, '/'));
        $current = $isWindowsDrive ? (array_shift($parts) . '/') : '/';

        // Linux absolute paths must keep leading slash; Windows keeps "C:/".
        if (!$isWindowsDrive && isset($normalized[0]) && $normalized[0] === '/') {
            $current = '/';
        }

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $current .= $part . '/';
            $segment = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $current), DIRECTORY_SEPARATOR);
            if (is_dir($segment)) {
                if (!is_writable($segment)) {
                    @chmod($segment, 0775);
                    if (!is_writable($segment)) {
                        @chmod($segment, 0777);
                    }
                }
                continue;
            }
            if (!@mkdir($segment, 0775) && !is_dir($segment)) {
                @mkdir($segment, 0777, true);
            }
            if (!is_dir($segment)) {
                $errors[] = $segment;
                return false;
            }
            @chmod($segment, 0775);
            if (!is_writable($segment)) {
                @chmod($segment, 0777);
            }
        }

        return is_dir($targetDir) && is_writable($targetDir);
    }

    private function normalizePath(string $path): string
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, trim($path));
        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    private function ensureUploadGuardFiles(string $dir): void
    {
        $index = $dir . DIRECTORY_SEPARATOR . 'index.html';
        if (!is_file($index)) {
            @file_put_contents($index, '');
        }
    }

    private function faceImageUploadFolderError(int $userId): string
    {
        $hintPaths = array();
        foreach ($this->resolvePublicFilesRootCandidates() as $root) {
            foreach ($this->faceImageRelativeBases() as $relBase) {
                $hintPaths[] = $this->normalizePath(
                    $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relBase) . DIRECTORY_SEPARATOR . $userId
                ) . DIRECTORY_SEPARATOR;
            }
        }
        $hintPaths = array_values(array_unique(array_filter($hintPaths)));
        $primary = $hintPaths[0] ?? ('uploads/app_home_page_images/' . $userId . '/');
        $primaryParent = dirname(rtrim($primary, '/\\'));

        return 'Unable to create upload folder. On the server run (SSH): '
            . 'sudo mkdir -p ' . $primaryParent . ' '
            . '&& sudo chown -R www-data:www-data ' . $primaryParent . ' '
            . '&& sudo chmod -R 775 ' . $primaryParent
            . ' — preferred path is uploads/app_home_page_images/ (under www). '
            . 'Or create ' . $primary . ' with owner/group matching PHP (www-data) and mode 775.';
    }

    private function ensurePublicFilesAccess(string $filesRoot): void
    {
        if (!is_dir($filesRoot)) {
            @mkdir($filesRoot, 0775, true);
        }
        if (is_dir($filesRoot) && !is_writable($filesRoot)) {
            @chmod($filesRoot, 0775);
            if (!is_writable($filesRoot)) {
                @chmod($filesRoot, 0777);
            }
        }
        $htaccess = $filesRoot . DIRECTORY_SEPARATOR . '.htaccess';
        if (!is_file($htaccess)) {
            @file_put_contents($htaccess, "Options -Indexes\n<IfModule mod_authz_core.c>\nRequire all granted\n</IfModule>\n");
        }
        $index = $filesRoot . DIRECTORY_SEPARATOR . 'index.html';
        if (!is_file($index)) {
            @file_put_contents($index, '');
        }
    }

    private function jsonResponse(array $data, int $status = 200): ResponseInterface
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) {
            $json = json_encode(array(
                'success' => false,
                'message' => 'JSON encode failed: ' . json_last_error_msg(),
            ));
            $status = 500;
        }

        return $this->response
            ->setStatusCode($status)
            ->setContentType('application/json')
            ->setBody($json);
    }
}
