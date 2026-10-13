<?php

namespace App\Controllers\NAS\Web;

use App\Services\NAS\AuthService;
use CodeIgniter\Controller;

class HomeController extends Controller
{
    public function showHome()
    {
        $session = session();

        $userId =
            (int) $session->get('nas_user_id');


        /*
        |--------------------------------------------------------------------------
        | Try Efactor -> NAS automatic bridge
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {

            $efactorUserId =
                (int) $session->get('user_id');

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
                        'Automatic Efactor -> NAS home login failed: '
                            . ($result['message'] ?? 'Unknown error')
                    );

                    return redirect()->to(
                        site_url('dashboard')
                    );
                }


                $userId =
                    (int) $session->get(
                        'nas_user_id'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | No authentication
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {
            return redirect()->to(
                site_url('NAS/login')
            );
        }


        $username =
            (string)
                (
                    $session->get(
                        'nas_username'
                    )
                    ?: 'User'
                );

        $email =
            (string)
                (
                    $session->get(
                        'nas_email'
                    )
                    ?: ''
                );


        $userData = [
            'user_id' =>
                $userId,

            'username' =>
                $username,

            'email' =>
                $email,

            'initial' =>
                strtoupper(
                    substr(
                        $username ?: 'U',
                        0,
                        1
                    )
                ),
        ];


        return view(
            'NAS/home/index',
            [
                'userData' =>
                    $userData,

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