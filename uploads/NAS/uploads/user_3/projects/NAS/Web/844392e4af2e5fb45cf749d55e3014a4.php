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
        if ($this->isLoggedIn()) {
            return redirect()->to(site_url('NAS/home'));
        }

        return view('NAS/auth/login', [
            'basePath' => rtrim(site_url('NAS'), '/'),
            'apiBase' => rtrim(site_url('NAS/api/v1'), '/'),
            'assetsBase' => rtrim(base_url('assets/NAS'), '/'),
        ]);
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
        session()->remove([
            'nas_user_id',
            'nas_username',
            'nas_email',
        ]);

        return redirect()->to(site_url('NAS/login'));
    }
}
