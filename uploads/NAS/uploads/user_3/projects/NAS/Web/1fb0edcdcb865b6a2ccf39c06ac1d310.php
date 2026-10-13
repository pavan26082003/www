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
        | CHECK NAS SESSION
        |--------------------------------------------------------------------------
        */

        $userId =
            (int) $session->get('nas_user_id');


        /*
        |--------------------------------------------------------------------------
        | NAS session missing
        |--------------------------------------------------------------------------
        |
        | If the user is already logged into Efactor,
        | automatically create/link the NAS session.
        |
        */

        if ($userId <= 0) {

            $efactorUserId =
                (int) $session->get('user_id');


            /*
            |--------------------------------------------------------------------------
            | Efactor is logged in
            |--------------------------------------------------------------------------
            */

            if ($efactorUserId > 0) {

                $authService =
                    new AuthService();

                $result =
                    $authService
                        ->ensureEfactorNasSession();


                if (
                    !isset($result['success'])
                    || !$result['success']
                ) {

                    log_message(
                        'error',
                        'Automatic Efactor -> NAS login failed for user '
                            . $efactorUserId
                            . ': '
                            . ($result['message'] ?? 'Unknown error')
                    );

                    session()->setFlashdata(
                        'error',
                        $result['message']
                            ?? 'Unable to open Efactor NAS'
                    );

                    return redirect()->to(
                        site_url('dashboard')
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NAS session should now exist
                |--------------------------------------------------------------------------
                */

                $userId =
                    (int) $session->get(
                        'nas_user_id'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Neither Efactor nor NAS authenticated
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {
            return redirect()->to(
                site_url('NAS/login')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | View
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
        | Render NAS dashboard
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
                    (string)
                        (
                            $session->get(
                                'nas_username'
                            )
                            ?: 'User'
                        ),

                'userEmail' =>
                    (string)
                        (
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