<?php

namespace App\Controllers\NAS\Web;

use App\Services\NAS\AuthService;
use CodeIgniter\Controller;

class HomeController extends Controller
{
    public function showHome()
    {
        $session = session();


        /*
        |--------------------------------------------------------------------------
        | EFACTOR SESSION IS REQUIRED
        |--------------------------------------------------------------------------
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
            (int) $session->get(
                'nas_user_id'
            );


        /*
        |--------------------------------------------------------------------------
        | AUTOMATIC EFACTOR -> NAS BRIDGE
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {

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
                    'Automatic Efactor -> NAS home login failed for user '
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
                'NAS home bridge completed but nas_user_id is still missing for Efactor user '
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
        | USER DATA
        |--------------------------------------------------------------------------
        */

        $username =
            (string) (
                $session->get(
                    'nas_username'
                )
                ?: 'User'
            );

        $email =
            (string) (
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


        /*
        |--------------------------------------------------------------------------
        | RENDER HOME
        |--------------------------------------------------------------------------
        */

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