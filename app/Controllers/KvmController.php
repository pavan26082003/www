<?php

namespace App\Controllers;

use App\Services\KvmService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class KvmController extends Security_Controller
{
    private KvmService $kvm;

    public function __construct()
    {
        parent::__construct();
        $this->kvm = new KvmService();
    }

    public function index()
    {
        return view('kvm/index', [
            'vmServerIp' => $this->kvm->getServerIp(),
            'hostConsoleUrl' => $this->kvm->getHostConsoleUrl(),
        ]);
    }

    public function ping(): ResponseInterface
    {
        try {
            return $this->jsonResponse($this->kvm->testConnection());
        } catch (Throwable $e) {
            log_message('error', 'KVM ping failed: {message}', ['message' => $e->getMessage()]);
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function api(): ResponseInterface
    {
        $method = strtolower($this->request->getMethod());
        if (!in_array($method, ['get', 'post'], true)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Only GET and POST are allowed.',
            ], 405);
        }

        $input = $method === 'get'
            ? (array) $this->request->getGet()
            : (array) $this->request->getPost();

        $action = trim((string) ($input['action'] ?? ''));
        if ($action === '') {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'KVM action is required.',
            ], 400);
        }

        // Never allow browser input to choose production SSH credentials/host.
        unset(
            $input['server_ip'],
            $input['username'],
            $input['password'],
            $input['ssh_username'],
            $input['ssh_password'],
            $input['ssh_port']
        );

        try {
            return $this->jsonResponse($this->kvm->dispatch($action, $input));
        } catch (Throwable $e) {
            log_message('error', 'KVM API failed: {message}', ['message' => $e->getMessage()]);
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        if (function_exists('csrf_token') && function_exists('csrf_hash')) {
            $data['_csrf'] = [
                'name' => csrf_token(),
                'hash' => csrf_hash(),
            ];
        }

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }
}
