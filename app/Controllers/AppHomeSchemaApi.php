<?php

namespace App\Controllers;

use App\Models\AppHomePageModel;
use CodeIgniter\Controller;
use Throwable;

class AppHomeSchemaApi extends Controller
{
    public function show($userKey = '')
    {
        helper('api_response');

        $userId = $this->normalizeUserId($userKey);
        $validationError = $this->validateUserId($userId, $userKey);

        if ($validationError !== null) {
            return api_error_response('INVALID_USER_ID', $validationError, 400);
        }

        try {
            $model = new AppHomePageModel();
            $schema = $model->getSchema($userId);
        } catch (Throwable $e) {
            log_message('error', 'App Home schema API failed: ' . $e->getMessage());

            return api_error_response(
                'INTERNAL_SERVER_ERROR',
                'Unable to load the app home schema.',
                500
            );
        }

        if (!is_array($schema) || empty($schema)) {
            return api_error_response(
                'APP_HOME_SCHEMA_NOT_FOUND',
                'No saved App Home schema was found for this user. Open App Home Page studio and click Save first.',
                404,
                ['user_id' => $userId]
            );
        }

        $this->response->removeHeader('Cache-Control');

        return $this->response
            ->setHeader('Cache-Control', 'no-store, max-age=0')
            ->setContentType('application/json', 'utf-8')
            ->setJSON($schema);
    }

    private function normalizeUserId($userKey): int
    {
        $key = trim(rawurldecode((string) $userKey));

        return (int) $key;
    }

    private function validateUserId(int $userId, $rawKey): ?string
    {
        $raw = trim(rawurldecode((string) $rawKey));

        if ($raw === '' || !ctype_digit($raw)) {
            return 'User id is required and must be a number (e.g. /api/app-home-schema/1).';
        }

        if ($userId < 1) {
            return 'User id must be a positive integer.';
        }

        return null;
    }
}
