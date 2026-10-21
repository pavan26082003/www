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

        // Stay on this request when it is already the right screen.
        // A 302 drops the new session cookie in the app webview.
        GridWebSso::establishSession($userId);

        $path = (string) $request->getUri()->getPath();
        if (stripos($path, '/api/') !== false) {
            return null;
        }

        $query = $request->getGet();
        $wanted = GridWebSso::requestedModule(is_array($query) ? $query : array());
        $current = GridWebSso::moduleKeyFromPath($path);
        if ($wanted === '' || $current === '' || $wanted === $current) {
            return null;
        }

        $dest = GridWebSso::appendToken(GridWebSso::requestedDestinationUrl(), $token);
        if ($dest === '') {
            return null;
        }

        return service('response')
            ->setStatusCode(200)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Robots-Tag', 'noindex')
            ->setContentType('text/html')
            ->setBody(GridWebSso::handoffPage($dest));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
