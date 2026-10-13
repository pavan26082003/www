<?php

namespace App\Controllers\NAS\Web;

use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $session = session();
        $userId = (int) $session->get('nas_user_id');

        if ($userId <= 0) {
            return redirect()->to(site_url('NAS/login'));
        }

        $view = trim((string) service('request')->getGet('view'));

        if ($view === '') {
            $view = 'my-drive';
        }

        return view('NAS/dashboard/index', [
            'view' => $view,
            'nasUserId' => $userId,
            'username' => (string) ($session->get('nas_username') ?: 'User'),
            'userEmail' => (string) ($session->get('nas_email') ?: ''),
            'basePath' => rtrim(site_url('NAS'), '/'),
            'apiBase' => rtrim(site_url('NAS/api/v1'), '/'),
            'assetsBase' => rtrim(base_url('assets/NAS'), '/'),
        ]);
    }
}
