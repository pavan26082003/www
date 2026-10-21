<?php

namespace App\Controllers;

use App\Libraries\GridWebSso;
use CodeIgniter\Controller;

/**
 * Signs the in-app browser into Professional Space or Circle Connect
 * without showing the web login form.
 *
 * The response is HTTP 200 so the WebView keeps the session cookie.
 * The pass is then carried onto the module URL, which already accepts it.
 */
class AppWebEntry extends Controller
{
    public function open()
    {
        $query = $this->request->getGet();
        $query = is_array($query) ? $query : array();
        $token = trim((string) ($query[GridWebSso::QUERY_PARAM] ?? $_GET[GridWebSso::QUERY_PARAM] ?? ''));
        $target = GridWebSso::requestedDestinationUrl($query);
        $userId = $token !== '' ? GridWebSso::verify($token) : null;
        $ready = $target !== ''
            && $userId !== null
            && GridWebSso::establishSession((int) $userId);

        $body = $ready
            ? GridWebSso::handoffPage(GridWebSso::appendToken($target, $token))
            : GridWebSso::retryPage();

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Robots-Tag', 'noindex')
            ->setContentType('text/html')
            ->setBody($body);
    }
}
