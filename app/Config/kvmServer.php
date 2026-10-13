<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Kvm extends BaseConfig
{
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

    /** @var string[] */
    public array $isoSearchPaths;

    /** @var string[] */
    public array $osTemplatePaths;

    /** @var string[] */
    public array $databaseTemplatePaths;

    public string $defaultStoragePath;
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
            '/vm_storage/images,/home/vmimages,/var/lib/libvirt/images,/var/lib/libvirt/backup'
        );
        $this->allowedStoragePaths = array_values(array_filter(array_map(
            static fn (string $path): string => rtrim(trim($path), '/'),
            explode(',', $storage)
        )));
        $this->defaultStoragePath = rtrim(trim((string) env(
            'KVM_DEFAULT_STORAGE_PATH',
            $this->allowedStoragePaths[0] ?? '/vm_storage/images'
        )), '/');

        $isoPaths = (string) env(
            'KVM_ISO_PATHS',
            '/vm_storage/iso,/var/lib/libvirt/images/iso,/var/lib/libvirt/boot,/var/lib/libvirt/images'
        );
        $this->isoSearchPaths = $this->csvPaths($isoPaths);

        $osTemplates = (string) env(
            'KVM_OS_TEMPLATE_PATHS',
            '/vm_storage/templates/os,/var/lib/libvirt/images/templates/os'
        );
        $this->osTemplatePaths = $this->csvPaths($osTemplates);

        $dbTemplates = (string) env(
            'KVM_DATABASE_TEMPLATE_PATHS',
            '/vm_storage/database-packages'
        );
        $this->databaseTemplatePaths = $this->csvPaths($dbTemplates);

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

        foreach ($this->operatingSystems as $os) {
            foreach (['iso_path', 'virtio_iso'] as $key) {
                $path = trim((string) ($os[$key] ?? ''));
                if ($path !== '') {
                    $this->isoSearchPaths[] = rtrim(dirname($path), '/');
                }
            }
        }
        $this->isoSearchPaths = array_values(array_unique(array_filter($this->isoSearchPaths)));
    }

    /** @return string[] */
    private function csvPaths(string $value): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (string $path): string => rtrim(trim($path), '/'),
            explode(',', $value)
        ))));
    }
}
