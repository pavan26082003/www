<?php

namespace App\Controllers;

use App\Models\VirtualDiskModel;
use App\Models\VirtualMachineModel;
use App\Services\KvmService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class KvmController extends Security_Controller
{
    private KvmService $kvm;
    private VirtualMachineModel $vmModel;
    private VirtualDiskModel $diskModel;

    public function __construct()
    {
        parent::__construct();

        $this->kvm = new KvmService();
        $this->vmModel = new VirtualMachineModel();
        $this->diskModel = new VirtualDiskModel();
    }

    public function index()
    {
        return view('kvm/index', [
            'vmServerIp' => $this->kvm->getServerIp(),
            'hostConsoleUrl' => $this->kvm->getHostConsoleUrl(),
            'defaultStoragePath' => $this->kvm->getDefaultStoragePath(),
        ]);
    }

    public function ping(): ResponseInterface
    {
        $result = $this->kvm->testConnection();
        return $this->response->setJSON($result);
    }

    public function api(): ResponseInterface
    {
        $method = strtolower($this->request->getMethod());
        if (!in_array($method, ['get', 'post'], true)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Only GET and POST are allowed.'], 405);
        }

        $input = $method === 'get' ? $this->request->getGet() : $this->request->getPost();
        $action = trim((string) ($input['action'] ?? ''));

        if ($action === '') {
            return $this->jsonResponse(['success' => false, 'message' => 'KVM action is required.'], 400);
        }

        // GET is restricted to read-only discovery/status calls. The existing UI may
        // still POST these actions; both are supported for compatibility.
        $readOnlyActions = [
            'system_overview', 'dashboard_data', 'list_vms', 'list_disks', 'storage_info',
            'get_vnc_port', 'get_iso_list', 'get_storage_pools', 'get_networks',
            'get_host_resources', 'get_creation_status', 'get_os_variants', 'list_bridges',
            'get_storage_usage', 'get_os_templates', 'get_database_templates', 'get_templates',
        ];
        if ($method === 'get' && !in_array($action, $readOnlyActions, true)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => "Action '{$action}' requires POST.",
            ], 405);
        }

        // Production security boundary:
        // the browser is never allowed to select SSH hosts or credentials.
        unset(
            $input['server_ip'],
            $input['username'],
            $input['password'],
            $input['ssh_username'],
            $input['ssh_password'],
            $input['ssh_port']
        );

        try {
            $result = $this->kvm->dispatch($action, $input);

            // Libvirt is authoritative. The DB is a non-blocking metadata mirror.
            if (!empty($result['success']) && in_array($action, ['system_overview', 'dashboard_data'], true)) {
                $this->syncLiveOverviewToDatabase($result);
            }

            return $this->jsonResponse($result);
        } catch (Throwable $e) {
            log_message('error', 'Unhandled KVM controller error: {message}', [
                'message' => $e->getMessage(),
            ]);

            return $this->jsonResponse([
                'success' => false,
                'message' => 'KVM backend request failed. Check the application logs for details.',
            ], 500);
        }
    }


    private function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        $data['_csrf'] = [
            'name' => csrf_token(),
            'hash' => csrf_hash(),
        ];

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }

    private function syncLiveOverviewToDatabase(array $overview): void
    {
        try {
            $serverIp = $this->kvm->getServerIp();
            $vmIdByName = [];

            foreach (($overview['vms'] ?? []) as $vm) {
                $name = trim((string) ($vm['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $diskPath = (string) ($vm['disk_path'] ?? '');
                $payload = [
                    'name' => $name,
                    'os_type' => (string) ($vm['os_type'] ?? 'Unknown'),
                    'cpu_cores' => (int) ($vm['cpu'] ?? 1),
                    'memory_mb' => (int) ($vm['mem'] ?? 1024),
                    'disk_size_gb' => (float) ($vm['disk_size_gb'] ?? $vm['allocated_size_gb'] ?? 0),
                    'disk_path' => $diskPath,
                    'storage_path' => $diskPath !== '' ? dirname($diskPath) : '',
                    'server_ip' => $serverIp,
                    'ip_address' => (string) ($vm['ip_address'] ?? ''),
                    'state' => (string) ($vm['state'] ?? 'stopped'),
                    'application_url' => '',
                ];

                $existing = $this->vmModel
                    ->where('name', $name)
                    ->where('server_ip', $serverIp)
                    ->first();

                if ($existing) {
                    $this->vmModel->update($existing['id'], $payload);
                    $vmIdByName[$name] = (int) $existing['id'];
                } else {
                    $id = $this->vmModel->insert($payload, true);
                    if ($id) {
                        $vmIdByName[$name] = (int) $id;
                    }
                }
            }

            foreach (($overview['disks'] ?? []) as $disk) {
                $diskPath = trim((string) ($disk['disk_path'] ?? ''));
                if ($diskPath === '') {
                    continue;
                }

                $vmName = trim((string) ($disk['vm_name'] ?? ''));
                $payload = [
                    'vm_id' => $vmName !== '' && isset($vmIdByName[$vmName]) ? $vmIdByName[$vmName] : null,
                    'name' => (string) ($disk['name'] ?? pathinfo($diskPath, PATHINFO_FILENAME)),
                    'disk_path' => $diskPath,
                    'storage_path' => (string) ($disk['storage_path'] ?? dirname($diskPath)),
                    'format' => (string) ($disk['format'] ?? 'qcow2'),
                    'allocated_gb' => (float) ($disk['allocated_gb'] ?? 0),
                    'used_gb' => (float) ($disk['used_gb'] ?? 0),
                    'status' => !empty($disk['is_attached']) ? 'attached' : 'unattached',
                ];

                $existing = $this->diskModel->where('disk_path', $diskPath)->first();
                if ($existing) {
                    $this->diskModel->update($existing['id'], $payload);
                } else {
                    $this->diskModel->insert($payload);
                }
            }
        } catch (Throwable $e) {
            // Database synchronization must never make live KVM management fail.
            log_message('warning', 'KVM DB mirror sync skipped: {message}', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
