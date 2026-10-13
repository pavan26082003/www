<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ApiErrors extends Controller
{
    public function notFound()
    {
        helper('api_response');

        return api_error_response(
            'API_ROUTE_NOT_FOUND',
            'The requested API resource was not found.',
            404
        );
    }

    public function methodNotAllowed()
    {
        helper('api_response');

        return api_error_response(
            'METHOD_NOT_ALLOWED',
            'Only GET requests are allowed for this endpoint.',
            405
        );
    }

    public function preflight()
    {
        return $this->response
            ->setStatusCode(204)
            ->setContentType('application/json', 'utf-8')
            ->setBody('');
    }
}
