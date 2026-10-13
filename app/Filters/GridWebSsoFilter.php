<?php

namespace App\Filters;

use App\Libraries\GridWebSso;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Accepts a short-lived Grid web SSO token and starts the app session
 * on this same request, so the in-app webview does not bounce to login.
 */
class GridWebSsoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = trim((string) $request->getGet(GridWebSso::QUERY_PARAM));
        if ($token === '') {
            return null;
        }

        $userId = GridWebSso::verify($token);
        if ($userId === null) {
            return null;
        }

        // Stay on this request. A redirect drops the new session cookie in the
        // app webview, and the next hit is the efactor.tech login page.
        GridWebSso::establishSession($userId);
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
