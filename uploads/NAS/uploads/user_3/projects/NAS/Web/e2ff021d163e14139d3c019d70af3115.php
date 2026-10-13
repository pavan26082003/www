<?php

namespace App\Controllers\NAS\Web;

use CodeIgniter\Controller;

class AuthController extends Controller
{
    private function isLoggedIn(): bool
    {
        return (int) session()->get('nas_user_id') > 0;
    }

    public function showLogin()
    {
        /*
        |--------------------------------------------------------------------------
        | Existing NAS session
        |--------------------------------------------------------------------------
        */

        if ($this->isLoggedIn()) {
            return redirect()->to(
                site_url('NAS/dashboard')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Efactor user already logged in
        |--------------------------------------------------------------------------
        */

        $efactorUserId =
            (int) session()->get('user_id');

        if ($efactorUserId > 0) {

            $authService =
                new \App\Services\NAS\AuthService();

            $result =
                $authService
                    ->ensureEfactorNasSession();

            if (
                isset($result['success'])
                && $result['success']
            ) {
                return redirect()->to(
                    site_url('NAS/dashboard')
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Standalone NAS login
        |--------------------------------------------------------------------------
        */

        return view(
            'NAS/auth/login',
            [
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

    public function showRegister()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to(site_url('NAS/home'));
        }

        return view('NAS/auth/register', [
            'basePath' => rtrim(site_url('NAS'), '/'),
            'apiBase' => rtrim(site_url('NAS/api/v1'), '/'),
            'assetsBase' => rtrim(base_url('assets/NAS'), '/'),
        ]);
    }

    public function showDashboard()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to(site_url('NAS/login'));
        }

        return redirect()->to(site_url('NAS/dashboard'));
    }

    public function logout()
    {
         $session = session();

        $session->destroy();

        return redirect()->to(
            site_url('signin')
        );
    }
}
