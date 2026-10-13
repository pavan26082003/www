<?php
namespace App\Services;

use App\Repositories\FileRepository;
use App\Repositories\UserRepository;

class OfficeConversionService
{
    private FileRepository $fileRepository;
    private UserRepository $userRepository;
    private StorageService $storageService;
    private string $tempDir;
    private bool $isWindows;
    private ?string $libreOfficePath;

    public function __construct(
        FileRepository $fileRepository,
        UserRepository $userRepository,
        StorageService $storageService
    ) {
        $this->fileRepository = $fileRepository;
        $this->userRepository = $userRepository;
        $this->storageService = $storageService;
        
        $this->tempDir = sys_get_temp_dir() . '/office_previews/';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->libreOfficePath = $this->findLibreOffice();
        
        error_log("🔍 LibreOffice found: " . ($this->libreOfficePath ? 'Yes at ' . $this->libreOfficePath : 'No'));
    }

    private function findLibreOffice(): ?string
    {
        if ($this->isWindows) {
            $paths = [
                'C:\Program Files\LibreOffice\program\soffice.exe',
                'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
                'C:\Program Files\LibreOffice\program\soffice.bin',
            ];
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
            
            // Check if soffice is in PATH
            exec('where soffice 2>&1', $output, $returnCode);
            if ($returnCode === 0 && !empty($output)) {
                return $output[0];
            }
            
            return null;
        }

        // Linux/Mac
        $commands = ['libreoffice', 'soffice', 'libreoffice7.0', 'libreoffice6.4'];
        foreach ($commands as $cmd) {
            exec('which ' . $cmd . ' 2>&1', $output, $returnCode);
            if ($returnCode === 0 && !empty($output)) {
                return $cmd;
            }
        }
        return null;
    }

    public function isLibreOfficeAvailable(): bool
    {
        return $this->libreOfficePath !== null;
    }

    /**
     * Convert Office file to PDF
     */
    public function convertToPdf(int $fileId, int $userId, bool $forceRefresh = false): array
    {
        $file = $this->fileRepository->find($fileId);
        if (!$file) {
            return ['success' => false, 'error' => 'File not found'];
        }

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $supportedTypes = ['ppt', 'pptx', 'doc', 'docx'];
        
        if (!in_array($extension, $supportedTypes)) {
            return ['success' => false, 'error' => 'Unsupported file type for conversion'];
        }

        $previewFile = $this->tempDir . 'converted_' . $fileId . '_' . $userId . '.pdf';

        // Check cache (1 hour)
        if (!$forceRefresh && file_exists($previewFile) && (time() - filemtime($previewFile) < 3600)) {
            return [
                'success' => true,
                'pdf_path' => $previewFile,
                'cached' => true,
                'name' => pathinfo($file['original_name'], PATHINFO_FILENAME) . '.pdf'
            ];
        }

        // Try LibreOffice
        if ($this->isLibreOfficeAvailable()) {
            $result = $this->convertWithLibreOffice($file, $previewFile);
            if ($result['success']) {
                return $result;
            }
        }

        // Final fallback: Google Docs Viewer
        return [
            'success' => false,
            'error' => 'Conversion failed',
            'fallback_url' => $this->getGoogleDocsViewerUrl($fileId),
            'message' => 'Using Google Docs Viewer as fallback'
        ];
    }

    /**
     * Convert using LibreOffice
     */
    private function convertWithLibreOffice(array $file, string $outputFile): array
    {
        $sourcePath = $this->storageService->getFullPath($file['file_path']);
        if (!file_exists($sourcePath)) {
            return ['success' => false, 'error' => 'Source file not found: ' . $sourcePath];
        }

        // Clean old file
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $tempDir = dirname($outputFile);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Build command
        if ($this->isWindows) {
            $command = '"' . $this->libreOfficePath . '" --headless --convert-to pdf --outdir "' . $tempDir . '" "' . $sourcePath . '" 2>&1';
        } else {
            $command = $this->libreOfficePath . ' --headless --convert-to pdf --outdir "' . $tempDir . '" "' . $sourcePath . '" 2>&1';
        }

        error_log("🔧 Running LibreOffice command: " . $command);
        
        exec($command, $output, $returnCode);
        
        error_log("📤 LibreOffice return code: " . $returnCode);
        error_log("📤 LibreOffice output: " . implode("\n", $output));

        if ($returnCode === 0) {
            $pdfName = pathinfo($file['original_name'], PATHINFO_FILENAME) . '.pdf';
            $generatedPath = $tempDir . '/' . $pdfName;
            
            error_log("📄 Looking for PDF at: " . $generatedPath);
            
            if (file_exists($generatedPath)) {
                rename($generatedPath, $outputFile);
                error_log("✅ PDF generated successfully: " . $outputFile);
                return [
                    'success' => true,
                    'pdf_path' => $outputFile,
                    'cached' => false,
                    'name' => $pdfName,
                    'converter' => 'LibreOffice'
                ];
            } else {
                // Try to find any PDF in the temp directory
                $files = glob($tempDir . '/*.pdf');
                if (!empty($files)) {
                    rename($files[0], $outputFile);
                    error_log("✅ PDF found and renamed: " . $outputFile);
                    return [
                        'success' => true,
                        'pdf_path' => $outputFile,
                        'cached' => false,
                        'name' => basename($files[0]),
                        'converter' => 'LibreOffice'
                    ];
                }
            }
        }

        error_log("❌ LibreOffice conversion failed. Output: " . implode("\n", $output));
        return ['success' => false, 'error' => 'LibreOffice conversion failed'];
    }

    /**
     * Get Google Docs Viewer URL
     */
    private function getGoogleDocsViewerUrl(int $fileId): string
    {
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/api/v1/files/' . $fileId . '/download?preview=1';
        return 'https://docs.google.com/viewer?url=' . urlencode($baseUrl);
    }

    /**
     * Get preview info (for frontend)
     */
    public function getPreviewInfo(int $fileId, int $userId): array
    {
        $file = $this->fileRepository->find($fileId);
        if (!$file) {
            return ['success' => false, 'error' => 'File not found'];
        }

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        
        // Check if we have a cached PDF
        $pdfFile = $this->tempDir . 'converted_' . $fileId . '_' . $userId . '.pdf';
        $hasCachedPdf = file_exists($pdfFile) && (time() - filemtime($pdfFile) < 3600);

        return [
            'success' => true,
            'file_id' => $fileId,
            'extension' => $extension,
            'has_conversion' => $this->isLibreOfficeAvailable(),
            'has_cached_pdf' => $hasCachedPdf,
            'pdf_path' => $hasCachedPdf ? $pdfFile : null,
            'can_preview' => in_array($extension, ['ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'pdf']),
            'preview_type' => $this->getPreviewType($extension)
        ];
    }

    private function getPreviewType(string $extension): string
    {
        $types = [
            'ppt' => 'office',
            'pptx' => 'office',
            'doc' => 'office',
            'docx' => 'office',
            'xls' => 'spreadsheet',
            'xlsx' => 'spreadsheet',
            'pdf' => 'pdf'
        ];
        return $types[$extension] ?? 'unknown';
    }
}