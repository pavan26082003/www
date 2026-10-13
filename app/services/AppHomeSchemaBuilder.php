<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * Builds App Home SDUI schema by delegating to assets/js/provision_app_home_schema.cjs (Node).
 * Keeps schema output identical to the studio JS builder.
 */
class AppHomeSchemaBuilder
{
    public static function build(array $layout, array $cfg = []): array
    {
        $script = self::resolveScriptPath();
        $node = self::resolveNodeBinary();
        if ($script === null || $node === null) {
            log_message('error', 'App home schema builder: Node or provision script not found.');
            return [];
        }

        $payload = json_encode(
            ['layout' => $layout, 'cfg' => $cfg],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($payload === false) {
            log_message('error', 'App home schema builder: layout json_encode failed.');
            return [];
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $cwd = dirname($script);
        $process = @proc_open([$node, $script], $descriptors, $pipes, $cwd);
        if (!is_resource($process)) {
            log_message('error', 'App home schema builder: proc_open failed.');
            return [];
        }

        fwrite($pipes[0], $payload);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            log_message(
                'error',
                'App home schema builder failed (exit ' . $exitCode . '): ' . trim((string) $stderr)
            );
            return [];
        }

        $schema = json_decode((string) $stdout, true);
        if (!is_array($schema) || empty($schema)) {
            log_message('error', 'App home schema builder returned invalid JSON.');
            return [];
        }

        return AppHomeSchemaSigner::attachSignature($schema);
    }

    public static function resolveScriptPath(): ?string
    {
        $candidates = [];

        // Prefer canonical Home SDUI V3 provisioner (assets/js) so Flutter gets schemaType=server-driven-mobile-app.
        if (defined('FCPATH')) {
            $candidates[] = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'provision_app_home_schema.cjs';
        }
        if (defined('ROOTPATH')) {
            $candidates[] = rtrim(ROOTPATH, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'provision_app_home_schema.cjs';
        }
        $candidates[] = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'provision_app_home_schema.cjs';

        // Legacy flat SDUI provisioner (kind=efactor.app-home.sdui) kept as fallback.
        $candidates[] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'provision_app_home_schema.cjs';

        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '') {
            $candidates[] = $docRoot . '/assets/js/provision_app_home_schema.cjs';
            $candidates[] = $docRoot . '/app/assets/js/provision_app_home_schema.cjs';
        }

        foreach (array_unique($candidates) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    public static function resolveNodeBinary(): ?string
    {
        $envNode = trim((string) (getenv('APP_HOME_NODE_BIN') ?: ''));
        if ($envNode !== '' && self::isUsableBinary($envNode)) {
            return $envNode;
        }

        // Absolute paths first (PHP/Apache often has a reduced PATH).
        foreach (self::commonNodePaths() as $path) {
            if (self::isUsableBinary($path)) {
                return $path;
            }
        }

        $candidates = ['node', 'nodejs'];
        if (DIRECTORY_SEPARATOR === '\\') {
            $candidates[] = 'node.exe';
        }

        foreach ($candidates as $binary) {
            $path = self::which($binary);
            if ($path !== null && self::isUsableBinary($path)) {
                return $path;
            }
            // `where`/`command -v` may return a name that resolves via PATH at proc_open time.
            if ($path !== null) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function commonNodePaths(): array
    {
        $paths = [
            '/usr/bin/node',
            '/usr/local/bin/node',
            '/opt/nodejs/bin/node',
            '/opt/node/bin/node',
            '/home/linuxbrew/.linuxbrew/bin/node',
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
            'C:\\nodejs\\node.exe',
        ];

        $localAppData = (string) (getenv('LOCALAPPDATA') ?: '');
        if ($localAppData !== '') {
            $paths[] = $localAppData . '\\Programs\\node\\node.exe';
            // Cursor helper binary (dev machines without a system Node install).
            $paths[] = $localAppData . '\\Programs\\cursor\\resources\\app\\resources\\helpers\\node.exe';
        }

        return $paths;
    }

    private static function isUsableBinary(string $path): bool
    {
        if ($path === '' || !is_file($path)) {
            return false;
        }

        // Windows often reports non-executable for .exe under is_executable(); file presence is enough.
        if (DIRECTORY_SEPARATOR === '\\') {
            return true;
        }

        return is_executable($path);
    }

    private static function which(string $binary): ?string
    {
        $command = (DIRECTORY_SEPARATOR === '\\')
            ? 'where ' . escapeshellarg($binary) . ' 2>nul'
            : 'command -v ' . escapeshellarg($binary) . ' 2>/dev/null';

        $output = @shell_exec($command);
        if (!is_string($output) || trim($output) === '') {
            return null;
        }

        $line = trim(strtok($output, "\r\n"));
        return ($line !== '' && (is_executable($line) || $binary === 'node' || $binary === 'nodejs' || $binary === 'node.exe'))
            ? $line
            : $binary;
    }
}
