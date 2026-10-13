<?php

/*
|--------------------------------------------------------------------------
| NAS / File Manager Routes
|--------------------------------------------------------------------------
|
| Efactor is the only authentication system.
|
| Authentication flow:
|
| Efactor login
|      ↓
| session user_id
|      ↓
| NAS Dashboard / Home
|      ↓
| AuthService::ensureEfactorNasSession()
|      ↓
| session nas_user_id
|
| Important:
|
| user_id     = Efactor user ID
| nas_user_id = NAS/File Manager user ID
|
| These IDs must remain separate.
|
| There is NO standalone NAS login or registration flow.
|
*/


if (ENVIRONMENT === 'development') {

/*
|--------------------------------------------------------------------------
| Diagnostic Routes
|--------------------------------------------------------------------------
|
| These are useful during LOCAL development.
| We can remove/restrict them before production deployment.
|
*/

$routes->get(
    'NAS/route-check',
    static function () {

        return service('response')->setJSON([
            'success' => true,
            'message' => 'NAS routes loaded successfully',
        ]);
    }
);


/*
|--------------------------------------------------------------------------
| NAS Database Check
|--------------------------------------------------------------------------
*/

$routes->get(
    'NAS/db-check',
    static function () {

        try {

            $repository =
                new \App\Repositories\NAS\UserRepository();

            $pdo =
                $repository->getPdo();

            $statement =
                $pdo->query('SELECT 1');

            $result =
                $statement->fetchColumn();

            return service('response')->setJSON([
                'success' => true,
                'message' => 'NAS database connection successful',
                'result'  => $result,
            ]);

        } catch (\Throwable $e) {

            return service('response')
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);
        }
    }
);


/*
|--------------------------------------------------------------------------
| NAS Runtime Check
|--------------------------------------------------------------------------
*/

$routes->get(
    'NAS/runtime-check',
    static function () {

        $response =
            service('response');

        $checks = [];


        $runCheck =
            static function (
                string $name,
                callable $callback
            ) use (&$checks) {

                try {

                    $callback();

                    $checks[$name] = [
                        'success' => true,
                        'message' => 'OK',
                    ];

                } catch (\Throwable $e) {

                    $checks[$name] = [
                        'success' => false,
                        'type'    => get_class($e),
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                    ];
                }
            };


        /*
        |--------------------------------------------------------------------------
        | Path Information
        |--------------------------------------------------------------------------
        */

        $storageService =
            new \App\Services\NAS\StorageService();

        $storageRoots =
            $storageService->getStorageRoots();

        $checks['paths'] = [

            'FCPATH' =>
                defined('FCPATH') ? FCPATH : null,

            'ROOTPATH' =>
                ROOTPATH,

            'WRITEPATH' =>
                WRITEPATH,

            // All NEW NAS files are written below this environment-derived root.
            'canonical_nas_write_root' =>
                $storageRoots['write_root'],

            // Existing user_{nasId}/... records remain readable from this root.
            'legacy_nas_read_root' =>
                $storageRoots['dms_compatibility_root']
                ?? ($storageRoots['legacy_root'] ?? null),
        ];


        /*
        |--------------------------------------------------------------------------
        | Storage Service
        |--------------------------------------------------------------------------
        */

        $runCheck(
            'StorageService',
            static function () {

                new \App\Services\NAS\StorageService();
            }
        );


        $runCheck(
            'StorageWriteRoot',
            static function () {

                $storage =
                    new \App\Services\NAS\StorageService();

                $writeRoot =
                    rtrim(
                        $storage->getUploadRoot(),
                        '/\\'
                    );

                if (is_dir($writeRoot)) {
                    if (!is_writable($writeRoot)) {
                        throw new \RuntimeException(
                            'Canonical NAS write root is not writable: '
                            . $writeRoot
                        );
                    }

                    return;
                }

                /*
                 * Do not create anything from a diagnostic request. Walk up to
                 * the nearest existing parent and verify whether PHP can create
                 * the missing files/user/... hierarchy there.
                 */
                $parent =
                    dirname($writeRoot);

                while (
                    !is_dir($parent)
                    && dirname($parent) !== $parent
                ) {
                    $parent =
                        dirname($parent);
                }

                if (
                    !is_dir($parent)
                    || !is_writable($parent)
                ) {
                    throw new \RuntimeException(
                        'PHP cannot create the canonical NAS write root. '
                        . 'Nearest existing parent is not writable: '
                        . $parent
                    );
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | File Repository
        |--------------------------------------------------------------------------
        */

        $runCheck(
            'FileRepository',
            static function () {

                new \App\Repositories\NAS\FileRepository();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Sync Repository
        |--------------------------------------------------------------------------
        */

        $runCheck(
            'SyncRepository',
            static function () {

                new \App\Repositories\NAS\SyncRepository();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | File Controller
        |--------------------------------------------------------------------------
        */

        $runCheck(
            'FileController',
            static function () {

                new \App\Controllers\NAS\Api\V1\FileController();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Sync Controller
        |--------------------------------------------------------------------------
        */

        $runCheck(
            'SyncController',
            static function () {

                new \App\Controllers\NAS\Api\V1\SyncController();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Overall Runtime Result
        |--------------------------------------------------------------------------
        */

        $allOk = true;

        foreach ($checks as $name => $result) {

            if (
                $name !== 'paths'
                && isset($result['success'])
                && $result['success'] === false
            ) {

                $allOk = false;
                break;
            }
        }


        return $response->setJSON([
            'success' => $allOk,
            'checks'  => $checks,
        ]);
    }
);


}


/*
|--------------------------------------------------------------------------
| NAS Web Routes
|--------------------------------------------------------------------------
|
| There are NO NAS login/register pages.
|
| DashboardController and HomeController are responsible for:
|
| 1. Checking Efactor session user_id
| 2. Redirecting unauthenticated users to Efactor /signin
| 3. Automatically creating/restoring nas_user_id
| 4. Loading NAS after the bridge succeeds
|
*/


/*
|--------------------------------------------------------------------------
| Main NAS Entry
|--------------------------------------------------------------------------
*/

$routes->get(
    'NAS',
    'NAS\Web\HomeController::showHome'
);

$routes->get(
    'NAS/',
    'NAS\Web\HomeController::showHome'
);

/*
|--------------------------------------------------------------------------
| NAS Home
|--------------------------------------------------------------------------
*/

$routes->get(
    'NAS/home',
    'NAS\Web\HomeController::showHome'
);


/*
|--------------------------------------------------------------------------
| NAS Dashboard
|--------------------------------------------------------------------------
*/

$routes->get(
    'NAS/dashboard',
    'NAS\Web\DashboardController::showDashboard'
);


$routes->get(
    'NAS/storage-plans',
    'NAS\\Web\\StoragePlanController::index'
);

$routes->get(
    'NAS/storage-plans/check',
    'NAS\\Web\\StoragePlanController::check'
);

$routes->post(
    'NAS/storage-plans/purchase',
    'NAS\\Web\\StoragePlanController::purchase'
);

/*
|--------------------------------------------------------------------------
| NAS Logout
|--------------------------------------------------------------------------
|
| This is the integrated/global logout.
|
| AuthController::logout() should:
|
| session()->destroy();
| redirect to site_url('signin');
|
*/

$routes->get(
    'NAS/logout',
    'NAS\Web\AuthController::logout'
);


/*
|--------------------------------------------------------------------------
| NAS Bridge Test
|--------------------------------------------------------------------------
*/

if (ENVIRONMENT === 'development') {
    $routes->get(
        'NAS/test',
        'NAS\Api\V1\AuthController::test'
    );
}


/*
|--------------------------------------------------------------------------
| NAS API V1
|--------------------------------------------------------------------------
|
| There is intentionally NO:
|
| POST auth/login
| POST auth/register
| POST auth/logout
|
| Efactor is the authentication provider.
|
*/

$routes->group(
    'NAS/api/v1',
    static function ($routes) {


        /*
        |--------------------------------------------------------------------------
        | Current Integrated User
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'auth/me',
            'NAS\Api\V1\AuthController::me'
        );


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'dashboard',
            'NAS\Api\V1\DashboardController::index'
        );

        $routes->get(
            'dashboard/recent',
            'NAS\Api\V1\DashboardController::recent'
        );

        $routes->get(
            'dashboard/stats',
            'NAS\Api\V1\DashboardController::stats'
        );


        /*
        |--------------------------------------------------------------------------
        | Files
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'files/download-folder',
            'NAS\Api\V1\FileController::downloadFolder'
        );

        $routes->get(
            'files',
            'NAS\Api\V1\FileController::index'
        );

        $routes->post(
            'files',
            'NAS\Api\V1\FileController::store'
        );

        $routes->get(
            'files/(:num)/download',
            'NAS\Api\V1\FileController::download/$1'
        );

        $routes->get(
            'files/(:num)/preview',
            'NAS\Api\V1\FileController::download/$1'
        );

        $routes->get(
            'files/(:num)/info',
            'NAS\Api\V1\FileController::info/$1'
        );

        $routes->get(
            'files/(:num)/content',
            'NAS\Api\V1\FileController::contentPreview/$1'
        );

        $routes->put(
            'files/(:num)/restore',
            'NAS\Api\V1\FileController::restore/$1'
        );

        $routes->put(
            'files/(:num)',
            'NAS\Api\V1\FileController::update/$1'
        );

        $routes->delete(
            'files/(:num)',
            'NAS\Api\V1\FileController::destroy/$1'
        );


        /*
        |--------------------------------------------------------------------------
        | Folders
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'folders',
            'NAS\Api\V1\FolderController::index'
        );

        $routes->post(
            'folders',
            'NAS\Api\V1\FolderController::store'
        );

        $routes->get(
            'folders/(:num)/info',
            'NAS\Api\V1\FolderController::info/$1'
        );

        $routes->put(
            'folders/(:num)/restore',
            'NAS\Api\V1\FolderController::restore/$1'
        );

        $routes->put(
            'folders/(:num)',
            'NAS\Api\V1\FolderController::update/$1'
        );

        $routes->delete(
            'folders/(:num)',
            'NAS\Api\V1\FolderController::destroy/$1'
        );


        /*
        |--------------------------------------------------------------------------
        | Sharing
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'share',
            'NAS\Api\V1\ShareController::index'
        );

        $routes->post(
            'share',
            'NAS\Api\V1\ShareController::store'
        );

        $routes->put(
            'share/(:num)',
            'NAS\Api\V1\ShareController::update/$1'
        );

        $routes->delete(
            'share/(:num)',
            'NAS\Api\V1\ShareController::destroy/$1'
        );


        /*
        |--------------------------------------------------------------------------
        | Clusters
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'clusters',
            'NAS\Api\V1\ClusterController::index'
        );


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'users/search',
            'NAS\Api\V1\UserController::search'
        );


        /*
        |--------------------------------------------------------------------------
        | Sync
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'sync/status',
            'NAS\Api\V1\SyncController::status'
        );

        $routes->post(
            'sync/init',
            'NAS\Api\V1\SyncController::init'
        );

        $routes->post(
            'sync/start',
            'NAS\Api\V1\SyncController::start'
        );

        // Browser/client sync endpoints. These never access a local filesystem
        // on the web server; the user's browser performs local I/O.
        $routes->get(
            'sync/manifest',
            'NAS\Api\V1\SyncController::manifest'
        );

        $routes->post(
            'sync/folders/ensure',
            'NAS\Api\V1\SyncController::ensureFolders'
        );

        $routes->post(
            'sync/upload',
            'NAS\Api\V1\SyncController::upload'
        );

        $routes->post(
            'sync/complete',
            'NAS\Api\V1\SyncController::complete'
        );

        $routes->post(
            'sync/download',
            'NAS\Api\V1\SyncController::download'
        );

        $routes->post(
            'sync/uploaded',
            'NAS\Api\V1\SyncController::uploaded'
        );

        $routes->post(
            'sync/repair',
            'NAS\Api\V1\SyncController::repair'
        );

        $routes->get(
            'sync/history',
            'NAS\Api\V1\SyncController::history'
        );

        $routes->post(
            'sync/cancel',
            'NAS\Api\V1\SyncController::cancel'
        );

        $routes->get(
            'sync/folders',
            'NAS\Api\V1\SyncController::folders'
        );

        $routes->get(
            'sync/unsynced',
            'NAS\Api\V1\SyncController::unsynced'
        );

        $routes->get(
            'sync/logs',
            'NAS\Api\V1\SyncController::logs'
        );

        $routes->post(
            'sync/pause',
            'NAS\Api\V1\SyncController::pause'
        );

        $routes->post(
            'sync/resume',
            'NAS\Api\V1\SyncController::resume'
        );


        /*
        |--------------------------------------------------------------------------
        | Preview / Office Conversion
        |--------------------------------------------------------------------------
        */

        $routes->get(
            'preview/convert',
            'NAS\Api\V1\PreviewController::convert'
        );

        $routes->get(
            'preview/info',
            'NAS\Api\V1\PreviewController::info'
        );

        $routes->get(
            'preview/ppt/(:num)',
            'NAS\Api\V1\PptPreviewController::preview/$1'
        );

        $routes->get(
            'preview/docx/(:num)',
            'NAS\Api\V1\DocxPreviewController::preview/$1'
        );
    }
);