<?php

namespace App\Controllers\NAS\Web;

use App\Services\NAS\AuthService;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService =
            new AuthService();
    }

    /**
     * Old /NAS/login compatibility route.
     *
     * There is no separate NAS login anymore.
     */
    public function showLogin()
    {
        return $this->enterNas();
    }

    /**
     * Old /NAS/register compatibility route.
     *
     * There is no separate NAS registration anymore.
     */
    public function showRegister()
    {
        return $this->enterNas();
    }

    private function enterNas()
    {
        $session = session();

        /*
        |--------------------------------------------------------------------------
        | Efactor authentication is required
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
        | Create/restore NAS session
        |--------------------------------------------------------------------------
        */

        $result =
            $this->authService
                ->ensureEfactorNasSession();

        if (
            !isset($result['success'])
            || !$result['success']
        ) {
            $message =
                $result['message']
                ?? 'Unable to open Efactor Drive';

            log_message(
                'error',
                'NAS bridge failed for Efactor user '
                . $efactorUserId
                . ': '
                . $message
            );

            session()->setFlashdata(
                'error',
                $message
            );

            return redirect()->to(
                site_url('dashboard')
            );
        }

        return redirect()->to(
            site_url('NAS/home')
        );
    }

    /**
     * Integrated logout.
     *
     * Logout from Efactor + NAS.
     */
    public function logout()
    {
        $session = session();

        $session->destroy();

        return redirect()->to(
            site_url('signin')
        );
    }
}