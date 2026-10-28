<?php

namespace App\Filters;

use App\Libraries\DevicePushService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Lets the existing chat send/read routes accept the mobile background request:
 * a JSON body, and a Bearer access token when the session cookie is absent.
 */
class MobilePushRequestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request instanceof IncomingRequest) {
            $this->mergeJsonBody($request);
        }
        $this->acceptBearerSession($request);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function mergeJsonBody(IncomingRequest $request): void
    {
        $raw = $request->getBody();
        if (!is_string($raw) || trim($raw) === '' || $raw[0] !== '{') {
            return;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return;
        }

        $post = $request->getPost();
        if (!is_array($post)) {
            $post = [];
        }

        foreach ($decoded as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $post[$key] = $value === null ? '' : (string) $value;
        }

        $request->setGlobal('post', $post);
    }

    private function acceptBearerSession(RequestInterface $request): void
    {
        $session = \Config\Services::session();
        if ((int) ($session->get('user_id') ?? 0) > 0) {
            return;
        }

        $token = $this->bearerToken($request);

        if ($token === '') {
            return;
        }

        $userId = (new DevicePushService())->userIdForAccessToken($token);
        if ($userId <= 0) {
            return;
        }

        $session->set('user_id', $userId);
    }

    private function bearerToken(RequestInterface $request): string
    {
        $candidates = [
            $request->getHeaderLine('Authorization'),
            (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ''),
            (string) ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''),
            (string) ($_SERVER['Authorization'] ?? ''),
            (string) (getenv('HTTP_AUTHORIZATION') ?: ''),
            (string) (getenv('REDIRECT_HTTP_AUTHORIZATION') ?: ''),
        ];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $key => $value) {
                    if (strcasecmp((string) $key, 'Authorization') === 0) {
                        $candidates[] = (string) $value;
                    }
                }
            }
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (is_array($headers)) {
                foreach ($headers as $key => $value) {
                    if (strcasecmp((string) $key, 'Authorization') === 0) {
                        $candidates[] = (string) $value;
                    }
                }
            }
        }

        $header = '';
        foreach ($candidates as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $header = $value;
                break;
            }
        }

        if (preg_match('/Bearer\s+(\S+)/i', $header, $match)) {
            return $match[1];
        }

        $header = trim($header);
        if ($header !== '' && stripos($header, 'Basic ') !== 0) {
            return $header;
        }

        if ($request instanceof IncomingRequest) {
            foreach (array('access_token', 'accesstoken', 'token') as $key) {
                $posted = trim((string) ($request->getPost($key) ?? ''));
                if ($posted !== '') {
                    return $posted;
                }
                $query = trim((string) ($request->getGet($key) ?? ''));
                if ($query !== '') {
                    return $query;
                }
            }
        }

        return '';
    }
}
