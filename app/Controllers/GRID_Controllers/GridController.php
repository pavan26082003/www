<?php

namespace App\Controllers\GRID_Controllers;

class GridController extends \App\Controllers\Security_Controller
{
    public function __construct()
    {
        parent::__construct(false);
    }

    private function gridView(string $view, array $view_data = [])
    {
        $view_data['login_user'] = $this->login_user;
        return $this->response
            ->setHeader('X-Frame-Options', 'SAMEORIGIN')
            ->setBody(view($view, $view_data));
    }

    public function index()
    {
        // App webview handoff: ?grid_sso= must establish session before Professional Space renders.
        // Security_Controller already consumes the token; re-apply here so index stays resilient
        // if the constructor path was skipped or session was not yet hydrated.
        $sso = trim((string) ($this->request->getGet('grid_sso') ?? $_GET['grid_sso'] ?? ''));
        if ($sso !== '' && class_exists(\App\Libraries\GridWebSso::class)) {
            $userId = \App\Libraries\GridWebSso::verify($sso);
            if ($userId) {
                \App\Libraries\GridWebSso::establishSession($userId);
                try {
                    $this->login_user = $this->Users_model->get_access_info($userId);
                } catch (\Throwable $e) {
                    log_message('error', 'Grid SSO login_user refresh failed: ' . $e->getMessage());
                }
            }
        }

        // Professional Space shell (GRID/index) — do not change schema/SDUI structure.
        // private, no-cache lets the device keep a copy for offline without marking it public.
        return $this->gridView('GRID/index')->setHeader('Cache-Control', 'private, no-cache');
    }

    public function profile()
    {
        return $this->gridView('GRID/profile');
    }

    public function messaging()
    {
        return $this->gridView('GRID/index');
    }

    public function team()
    {
        return $this->gridView('GRID/team');
    }

    public function demo_loader()
    {
        return view('GRID/demo_loader');
    }

    public function network()
    {
        return $this->gridView('GRID/index');
    }

    public function jobs()
    {
        return $this->gridView('GRID/index');
    }
}