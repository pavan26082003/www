<?php

namespace App\Controllers\NAS\Web;

use CodeIgniter\Controller;

class HomeController extends Controller
{
    public function showHome()
    {
        $session = session();
        $userId = (int) $session->get('nas_user_id');

        if ($userId <= 0) {
            return redirect()->to(site_url('NAS/login'));
        }

        $username = (string) ($session->get('nas_username') ?: 'User');
        $email = (string) ($session->get('nas_email') ?: '');

        $userData = [
            'user_id' => $userId,
            'username' => $username,
            'email' => $email,
            'initial' => strtoupper(substr($username ?: 'U', 0, 1)),
        ];

        return view('NAS/home/index', [
            'userData' => $userData,
            'basePath' => rtrim(site_url('NAS'), '/'),
            'apiBase' => rtrim(site_url('NAS/api/v1'), '/'),
            'assetsBase' => rtrim(base_url('assets/NAS'), '/'),
        ]);
    }
}
