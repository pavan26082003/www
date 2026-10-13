<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Kvm extends BaseConfig
{
    public bool $testMode;
    public string $testStateFile;
    public int $testCpuCores;
    public int $testMemoryMb;
    public float $testStorageTotalGb;
    public float $testStorageUsedGb;

    public string $serverIp;
    public int $sshPort;
    public string $sshUsername;
    public string $sshPassword;
    public string $sshPublicKey;
    public string $sshPrivateKey;
    public string $sshKeyPassphrase;
    public int $sshTimeout;

    /** @var string[] */
    public array $allowedStoragePaths;

    public string $networkBridge;
    public int $applicationPort;
    public string $applicationUrlTemplate;
    public string $consoleUrlTemplate;
    public string $hostConsoleUrl;
    public string $backupPath;

    /** @var array<string,array<string,string>> */
    public array $operatingSystems;

    public function __construct()
    {
        parent::__construct();

        $this->testMode = filter_var((string) env('KVM_TEST_MODE', 'false'), FILTER_VALIDATE_BOOLEAN);
        $this->testStateFile = trim((string) env('KVM_TEST_STATE_FILE', 'kvm_test_state.json'));
        $this->testCpuCores = max(1, (int) env('KVM_TEST_CPU_CORES', 16));
        $this->testMemoryMb = max(512, (int) env('KVM_TEST_MEMORY_MB', 65536));
        $this->testStorageTotalGb = max(1, (float) env('KVM_TEST_STORAGE_TOTAL_GB', 1000));
        $this->testStorageUsedGb = max(0, (float) env('KVM_TEST_STORAGE_USED_GB', 300));

        $this->serverIp = trim((string) env('KVM_SERVER_IP', ''));
        $this->sshPort = max(1, (int) env('KVM_SSH_PORT', 22));
        $this->sshUsername = trim((string) env('KVM_SSH_USERNAME', ''));
        $this->sshPassword = (string) env('KVM_SSH_PASSWORD', '');
        $this->sshPublicKey = trim((string) env('KVM_SSH_PUBLIC_KEY', ''));
        $this->sshPrivateKey = trim((string) env('KVM_SSH_PRIVATE_KEY', ''));
        $this->sshKeyPassphrase = (string) env('KVM_SSH_KEY_PASSPHRASE', '');
        $this->sshTimeout = max(5, (int) env('KVM_SSH_TIMEOUT', 30));

        $storage = (string) env(
            'KVM_STORAGE_PATHS',
            '/home/vmimages,/var/lib/libvirt/images,/var/lib/libvirt/backup'
        );
        $this->allowedStoragePaths = array_values(array_filter(array_map(
            static fn (string $path): string => rtrim(trim($path), '/'),
            explode(',', $storage)
        )));

        $this->networkBridge = trim((string) env('KVM_NETWORK_BRIDGE', 'virbr0'));
        $this->applicationPort = max(1, (int) env('KVM_APPLICATION_PORT', 8080));
        $this->applicationUrlTemplate = trim((string) env('KVM_APPLICATION_URL_TEMPLATE', 'http://{ip}:{port}/'));
        $this->consoleUrlTemplate = trim((string) env('KVM_CONSOLE_URL_TEMPLATE', ''));
        $this->hostConsoleUrl = trim((string) env('KVM_HOST_CONSOLE_URL', ''));
        $this->backupPath = rtrim(trim((string) env('KVM_BACKUP_PATH', '/var/lib/libvirt/backup')), '/');

        $this->operatingSystems = [
            'almalinux9' => [
                'label' => 'AlmaLinux 9.8',
                'iso_path' => trim((string) env('KVM_ALMALINUX9_ISO', '/var/lib/libvirt/images/AlmaLinux-9.8-x86_64-dvd.iso')),
                'os_variant' => trim((string) env('KVM_ALMALINUX9_VARIANT', 'almalinux9')),
                'virtio_iso' => '',
                'boot' => '',
            ],
            'windows10' => [
                'label' => 'Windows 10',
                'iso_path' => trim((string) env('KVM_WINDOWS10_ISO', '/var/lib/libvirt/images/Windows.iso')),
                'os_variant' => trim((string) env('KVM_WINDOWS10_VARIANT', 'win10')),
                'virtio_iso' => trim((string) env('KVM_WINDOWS_VIRTIO_ISO', '/var/lib/libvirt/images/virtio-win.iso')),
                'boot' => trim((string) env('KVM_WINDOWS10_BOOT', 'uefi')),
            ],
        ];
    }
}