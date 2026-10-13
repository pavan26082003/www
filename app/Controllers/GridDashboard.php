<?php

namespace App\Controllers;

use App\Libraries\GridSduiSchemaBuilder;
use App\Models\AppHomePageModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Grid New studio — same 3-column pattern as IoT Dashboard:
 * left: generate schema, center: mobile preview, right: schema JSON.
 */
class GridDashboard extends Security_Controller
{
    protected AppHomePageModel $appHomePageModel;

    public function __construct()
    {
        parent::__construct();
        $this->appHomePageModel = new AppHomePageModel();
    }

    public function index()
    {
        $userId = (int) $this->login_user->id;
        $userEmail = (string) (session()->get('email') ?? ($this->login_user->email ?? ''));
        $payload = $this->buildSchemaPayload($userId, $userEmail);
        $content = is_array($payload['content'] ?? null) ? $payload['content'] : array();
        $defaults = \App\Libraries\GridWebSso::defaultCardUrls();
        $proUrl = \App\Libraries\GridWebSso::resolveCardUrl(
            'professional-space',
            (string) ($payload['professionalSpaceUrl'] ?? '')
        );
        $circleUrl = \App\Libraries\GridWebSso::resolveCardUrl(
            'circle-connect',
            (string) ($payload['circleConnectUrl'] ?? '')
        );

        return $this->template->rander('grid_dashboard/index', array(
            'schema' => $payload['schema'] ?? array(),
            'content' => $content,
            'professionalSpaceUrl' => $proUrl !== '' ? $proUrl : $defaults['professional-space'],
            'circleConnectUrl' => $circleUrl !== '' ? $circleUrl : $defaults['circle-connect'],
            'userId' => $userId,
            'apiBase' => site_url('grid-dashboard/api'),
            'dashboardUrl' => site_url(''),
            'gridApiUrl' => $payload['gridApiUrl'] ?? '',
            'gridApiBase' => $this->appHomePageModel->getPublicGridApiBaseUrl(),
            'csrfTokenName' => function_exists('csrf_token') ? csrf_token() : '',
            'csrfHash' => function_exists('csrf_hash') ? csrf_hash() : '',
        ));
    }

    public function api(string $action = ''): ResponseInterface
    {
        $userId = (int) $this->login_user->id;
        $userEmail = (string) (session()->get('email') ?? ($this->login_user->email ?? ''));
        $action = strtolower(trim($action));

        if ($action === 'schema') {
            return $this->jsonResponse($this->buildSchemaPayload($userId, $userEmail));
        }

        if ($action === 'generate-schema') {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
            }

            try {
                $body = $this->request->getJSON(true);
                if (!is_array($body)) {
                    $body = $this->request->getPost();
                }
                if (!is_array($body)) {
                    $body = array();
                }

                $payload = $this->buildSchemaPayload(
                    $userId,
                    $userEmail,
                    \App\Libraries\GridWebSso::resolveCardUrl(
                        'professional-space',
                        trim((string) ($body['professionalSpaceUrl'] ?? ''))
                    ),
                    \App\Libraries\GridWebSso::resolveCardUrl(
                        'circle-connect',
                        trim((string) ($body['circleConnectUrl'] ?? ''))
                    ),
                    true
                );
                $payload['success'] = true;
                $payload['message'] = 'Grid schema generated and card settings saved.';
                $payload['savedAt'] = date('c');
                $payload['savedTime'] = date('H:i:s');
                return $this->jsonResponse($payload);
            } catch (\Throwable $e) {
                log_message('error', 'Grid schema generate failed: ' . $e->getMessage());
                return $this->jsonResponse(array(
                    'success' => false,
                    'message' => 'Could not generate Grid schema: ' . $e->getMessage(),
                ), 500);
            }
        }

        return $this->jsonResponse(array('success' => false, 'message' => 'Unknown action'), 404);
    }

    private function extractFeatureMeta(array $content): array
    {
        $pro = '';
        $circle = '';
        $cards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        foreach ($cards as $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
            if ($id === 'professional-space') {
                $pro = $url;
            }
            if ($id === 'circle-connect') {
                $circle = $url;
            }
        }
        return array(
            'professionalSpaceUrl' => $pro,
            'circleConnectUrl' => $circle,
        );
    }

    private function buildSchemaPayload(
        int $userId,
        string $userEmail,
        ?string $professionalSpaceUrl = null,
        ?string $circleConnectUrl = null,
        bool $persist = false
    ): array {
        $layout = $this->appHomePageModel->mergeWithDefaults(
            $this->appHomePageModel->getDefaultLayout($userEmail, $userId),
            $this->appHomePageModel->getLayout($userId)
        );
        $content = is_array($layout['gridPage'] ?? null) ? $layout['gridPage'] : array();

        if ($professionalSpaceUrl !== null || $circleConnectUrl !== null) {
            $meta = $this->extractFeatureMeta(is_array($content) ? $content : array());
            $pro = \App\Libraries\GridWebSso::resolveCardUrl(
                'professional-space',
                $professionalSpaceUrl !== null ? $professionalSpaceUrl : $meta['professionalSpaceUrl']
            );
            $circle = \App\Libraries\GridWebSso::resolveCardUrl(
                'circle-connect',
                $circleConnectUrl !== null ? $circleConnectUrl : $meta['circleConnectUrl']
            );
            $content = GridSduiSchemaBuilder::applyFeatureCardUrls($content, $pro, $circle);
            $layout['gridPage'] = $content;
            if ($persist) {
                $this->appHomePageModel->saveLayout($userId, $layout);
            }
        } else {
            $content = \App\Libraries\GridWebSso::canonicalizeFeatureCardUrls(
                is_array($content) ? $content : array()
            );
        }

        $gridApiUrl = $this->appHomePageModel->getGridApiUrl($userId);
        $ssoToken = $userId > 0 ? \App\Libraries\GridWebSso::issue($userId) : '';
        $schema = GridSduiSchemaBuilder::build(array(
            'userId' => $userId,
            'content' => $content,
            'endpoint' => $gridApiUrl,
            'ssoToken' => $ssoToken,
        ));
        $meta = $this->extractFeatureMeta(is_array($schema['content'] ?? null) ? $schema['content'] : $content);

        return array(
            'success' => true,
            'user_id' => $userId,
            'pageId' => 'grid',
            'routeId' => 'grid',
            'schema_version' => $schema['schemaVersion'] ?? '3.0.0',
            'gridApiUrl' => $gridApiUrl,
            'professionalSpaceUrl' => \App\Libraries\GridWebSso::stripToken((string) ($meta['professionalSpaceUrl'] ?? '')),
            'circleConnectUrl' => \App\Libraries\GridWebSso::stripToken((string) ($meta['circleConnectUrl'] ?? '')),
            'content' => $schema['content'] ?? $content,
            'schema' => $schema,
        );
    }

    private function jsonResponse(array $data, int $status = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setContentType('application/json')
            ->setBody(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
