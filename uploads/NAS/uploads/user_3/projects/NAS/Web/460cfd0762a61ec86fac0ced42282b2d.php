<?php

namespace App\Controllers\NAS\Web;

use App\Services\NAS\AuthService;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $session = session();

        /*
        |--------------------------------------------------------------------------
        | EFACTOR SESSION IS REQUIRED
        |--------------------------------------------------------------------------
        |
        | Efactor is now the only authentication system.
        |
        */

        $efactorUserId =
            (int) $session->get('user_id');

        if ($efactorUserId <= 0) {
            return redirect()->to(
                site_url('signin')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK NAS SESSION
        |--------------------------------------------------------------------------
        */

        $userId =
            (int) $session->get('nas_user_id');


        /*
        |--------------------------------------------------------------------------
        | AUTOMATIC EFACTOR -> NAS BRIDGE
        |--------------------------------------------------------------------------
        */

        if (
            $userId <= 0
            || (int) $session->get('nas_efactor_user_id') !== $efactorUserId
        ) {

            $authService =
                new AuthService();

            $result =
                $authService
                    ->ensureEfactorNasSession();

            if (
                !isset($result['success'])
                || !$result['success']
            ) {

                $message =
                    $result['message']
                    ?? 'Unable to open Efactor NAS';

                log_message(
                    'error',
                    'Automatic Efactor -> NAS login failed for user '
                        . $efactorUserId
                        . ': '
                        . $message
                );

                $session->setFlashdata(
                    'error',
                    $message
                );

                return redirect()->to(
                    site_url('dashboard')
                );
            }


            /*
            |--------------------------------------------------------------------------
            | NAS SESSION SHOULD NOW EXIST
            |--------------------------------------------------------------------------
            */

            $userId =
                (int) $session->get(
                    'nas_user_id'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FINAL NAS SESSION CHECK
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {

            log_message(
                'error',
                'NAS bridge completed but nas_user_id is still missing for Efactor user '
                    . $efactorUserId
            );

            $session->setFlashdata(
                'error',
                'Unable to establish Efactor Drive session.'
            );

            return redirect()->to(
                site_url('dashboard')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        $view =
            trim(
                (string)
                    service('request')
                        ->getGet('view')
            );

        if ($view === '') {
            $view = 'my-drive';
        }


        /*
        |--------------------------------------------------------------------------
        | RENDER NAS DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'NAS/dashboard/index',
            [
                'view' =>
                    $view,

                'nasUserId' =>
                    $userId,

                'username' =>
                    (string) (
                        $session->get(
                            'nas_username'
                        )
                        ?: 'User'
                    ),

                'userEmail' =>
                    (string) (
                        $session->get(
                            'nas_email'
                        )
                        ?: ''
                    ),

                'basePath' =>
                    rtrim(
                        site_url('NAS'),
                        '/'
                    ),

                'apiBase' =>
                    rtrim(
                        site_url(
                            'NAS/api/v1'
                        ),
                        '/'
                    ),

                'assetsBase' =>
                    rtrim(
                        base_url(
                            'assets/NAS'
                        ),
                        '/'
                    ),
            ]
        );
    }
}
