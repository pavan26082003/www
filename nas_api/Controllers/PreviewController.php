<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;

/**
 * Mobile/Postman preview transport.
 *
 * Business rules stay in the existing App\Services\NAS layer:
 * - FileService owns permission-checked file lookup/preview.
 * - OfficeConversionService owns DOC/DOCX/PPT/PPTX -> PDF conversion.
 * - XLS/XLSX/CSV are NOT converted to PDF; clients receive the original
 *   permission-checked bytes and render them with an XLSX/SheetJS-compatible
 *   spreadsheet renderer.
 */
final class PreviewController
{
    /** GET /v1/preview/info?file_id={id} */
    public function info(): void
    {
        try {
            $fileId = $this->requiredFileId();
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];

            $fileService = SharedNasFactory::fileService();
            $permissionService = SharedNasFactory::permissionService();

            // Permission-checked canonical file lookup.
            $file = $fileService->getFileInfo($userId, $fileId);
            $name = (string) ($file['name'] ?? '');
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $mimeType = trim((string) ($file['mime_type'] ?? $file['file_type'] ?? ''));

            $canDownload = $permissionService->canDownload($userId, 'file', $fileId);
            $descriptor = $this->descriptor($fileId, $extension, $mimeType);

            $converterAvailable = null;
            $hasCachedPdf = false;

            if ($descriptor['preview_type'] === 'office') {
                $sharedInfo = SharedNasFactory::officeConversionService()->getPreviewInfo($fileId, $userId);
                $converterAvailable = (bool) ($sharedInfo['has_conversion'] ?? false);
                $hasCachedPdf = (bool) ($sharedInfo['has_cached_pdf'] ?? false);
            }

            JsonResponse::send([
                'success' => true,
                'message' => 'Preview info retrieved',
                'file_id' => $fileId,
                'name' => $name,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'can_preview' => $descriptor['can_preview'],
                'preview_type' => $descriptor['preview_type'],
                'delivery_type' => $descriptor['delivery_type'],
                'client_renderer' => $descriptor['client_renderer'],
                'conversion_required' => $descriptor['conversion_required'],
                'converter_available' => $converterAvailable,
                'has_cached_pdf' => $hasCachedPdf,
                'preview_endpoint' => $descriptor['preview_endpoint'],
                'download_endpoint' => $canDownload ? '/v1/files/' . $fileId . '/download' : null,
                'can_download' => $canDownload,
            ]);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /**
     * GET /v1/preview/convert?file_id={id}&force=true
     *
     * PDF: streamed directly.
     * DOC/DOCX/PPT/PPTX: existing OfficeConversionService -> PDF -> stream.
     * XLS/XLSX/CSV: rejected here by design; use /v1/files/{id}/preview and
     * render the original bytes with the XLSX-compatible client renderer.
     */
    public function convert(): void
    {
        try {
            $fileId = $this->requiredFileId();
            $context = AuthenticatedNasContext::resolve();
            $userId = (int) $context['nas_user_id'];
            $fileService = SharedNasFactory::fileService();

            // Canonical permission gate through existing FileService.
            $file = $fileService->getFileInfo($userId, $fileId);
            $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $this->streamPdf($fileService->previewFile($userId, $fileId));
            }

            if (in_array($extension, ['xls', 'xlsx', 'csv'], true)) {
                throw new ApiException(
                    'Spreadsheet files are previewed from their original bytes. Use /v1/files/' . $fileId . '/preview and render with the XLSX client library.',
                    400,
                    'spreadsheet_conversion_not_required'
                );
            }

            if (!in_array($extension, ['doc', 'docx', 'ppt', 'pptx'], true)) {
                throw new ApiException(
                    'This file type does not use Office-to-PDF conversion.',
                    400,
                    'conversion_not_supported'
                );
            }

            $forceRefresh = $this->booleanValue($_GET['force'] ?? false);
            $result = SharedNasFactory::officeConversionService()->convertToPdf(
                $fileId,
                $userId,
                $forceRefresh
            );

            if (empty($result['success'])) {
                $message = (string) ($result['error'] ?? 'Office conversion failed');
                $status = strpos(strtolower($message), 'libreoffice') !== false ? 503 : 500;
                throw new ApiException(
                    $message,
                    $status,
                    $status === 503 ? 'office_converter_unavailable' : 'office_conversion_failed'
                );
            }

            $this->streamPdf($result);
        } catch (ApiException $e) {
            $this->apiError($e);
        } catch (\Throwable $e) {
            $this->serviceError($e);
        }
    }

    /** @return array{can_preview:bool,preview_type:string,delivery_type:string,client_renderer:string,conversion_required:bool,preview_endpoint:?string} */
    private function descriptor(int $fileId, string $extension, string $mimeType): array
    {
        $office = ['doc', 'docx', 'ppt', 'pptx'];
        $spreadsheets = ['xls', 'xlsx', 'csv'];
        $images = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
        $videos = ['mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv'];
        $audio = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma', 'm4a'];
        $text = ['txt', 'md', 'js', 'css', 'html', 'htm', 'json', 'xml', 'php', 'sql', 'py', 'java', 'cpp', 'c', 'h', 'sh', 'bash', 'rb', 'go', 'rs', 'ts', 'jsx', 'tsx', 'vue', 'yaml', 'yml', 'log'];
        $archives = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'];

        if ($extension === 'pdf' || $mimeType === 'application/pdf') {
            return $this->makeDescriptor(true, 'pdf', 'binary', 'pdf', false, '/v1/files/' . $fileId . '/preview');
        }

        if (in_array($extension, $office, true)) {
            return $this->makeDescriptor(true, 'office', 'pdf', 'pdf', true, '/v1/preview/convert?file_id=' . $fileId);
        }

        if (in_array($extension, $spreadsheets, true)) {
            return $this->makeDescriptor(true, 'spreadsheet', 'binary', 'xlsx', false, '/v1/files/' . $fileId . '/preview');
        }

        if (strncmp($mimeType, 'image/', 6) === 0 || in_array($extension, $images, true)) {
            return $this->makeDescriptor(true, 'image', 'binary', 'image', false, '/v1/files/' . $fileId . '/preview');
        }

        if (strncmp($mimeType, 'video/', 6) === 0 || in_array($extension, $videos, true)) {
            return $this->makeDescriptor(true, 'video', 'binary', 'video', false, '/v1/files/' . $fileId . '/preview');
        }

        if (strncmp($mimeType, 'audio/', 6) === 0 || in_array($extension, $audio, true)) {
            return $this->makeDescriptor(true, 'audio', 'binary', 'audio', false, '/v1/files/' . $fileId . '/preview');
        }

        if (strncmp($mimeType, 'text/', 5) === 0 || in_array($extension, $text, true)) {
            return $this->makeDescriptor(true, 'text', 'text', 'text', false, '/v1/files/' . $fileId . '/content');
        }

        if (in_array($extension, $archives, true)) {
            return $this->makeDescriptor(false, 'archive', 'download', 'none', false, null);
        }

        return $this->makeDescriptor(false, 'unknown', 'download', 'system', false, null);
    }

    /** @return array{can_preview:bool,preview_type:string,delivery_type:string,client_renderer:string,conversion_required:bool,preview_endpoint:?string} */
    private function makeDescriptor(
        bool $canPreview,
        string $previewType,
        string $deliveryType,
        string $renderer,
        bool $conversionRequired,
        ?string $endpoint
    ): array {
        return [
            'can_preview' => $canPreview,
            'preview_type' => $previewType,
            'delivery_type' => $deliveryType,
            'client_renderer' => $renderer,
            'conversion_required' => $conversionRequired,
            'preview_endpoint' => $endpoint,
        ];
    }

    /** @param array<string,mixed> $result */
    private function streamPdf(array $result): void
    {
        $path = (string) ($result['pdf_path'] ?? $result['path'] ?? '');
        if ($path === '' || !is_file($path) || !is_readable($path)) {
            throw new ApiException('Converted PDF was not found.', 404, 'converted_pdf_not_found');
        }

        $name = $this->safeFilename((string) ($result['name'] ?? 'preview.pdf'));
        $size = filesize($path);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (headers_sent()) {
            throw new \RuntimeException('Unable to stream PDF because response headers were already sent.', 500);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . $this->contentDisposition('inline', $name));
        if ($size !== false) {
            header('Content-Length: ' . (string) $size);
        }
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');

        readfile($path);
        exit;
    }

    private function requiredFileId(): int
    {
        $raw = $_GET['file_id'] ?? null;
        if ($raw === null || !is_scalar($raw) || !ctype_digit((string) $raw) || (int) $raw <= 0) {
            throw new ApiException('File ID required.', 400, 'file_id_required');
        }

        return (int) $raw;
    }

    private function booleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
        }
        return false;
    }

    private function safeFilename(string $name): string
    {
        $name = str_replace(["\r", "\n", "\0"], '', $name);
        $name = basename(str_replace('\\', '/', $name));
        $name = trim($name);
        return $name !== '' ? $name : 'preview.pdf';
    }

    private function contentDisposition(string $disposition, string $name): string
    {
        $ascii = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name) ?: 'preview.pdf';
        $ascii = str_replace(['"', '\\'], '_', $ascii);

        return $disposition
            . '; filename="' . $ascii . '"'
            . "; filename*=UTF-8''" . rawurlencode($name);
    }

    private function apiError(ApiException $e): void
    {
        JsonResponse::send([
            'success' => false,
            'error' => $e->errorCode(),
            'message' => $e->getMessage(),
        ], $e->statusCode());
    }

    private function serviceError(\Throwable $e): void
    {
        $status = (int) $e->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        error_log('[nas_api] PreviewController: ' . get_class($e) . ': ' . $e->getMessage());

        JsonResponse::send([
            'success' => false,
            'message' => ($status === 500 && !NAS_API_DEBUG)
                ? 'Internal server error.'
                : $e->getMessage(),
        ], $status);
    }
}
