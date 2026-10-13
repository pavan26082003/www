<?php
header('Content-Type: application/json');

require_once 'vendor/autoload.php'; // Include QR code library
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// API endpoint: /qr-generator.php?data=YOUR_DATA

// Get input data
$data = $_GET['data'] ?? '';

echo $data;
//$outputFormat = $_REQUEST['format'] ?? 'base64'; // 'base64' or 'direct'

$outputFormat='direct';



// Generate QR codes in 3 sizes
$sizes = [
    'small' => 200,
    'medium' => 300,
    'large' => 400
];

$result = [];
$writer = new PngWriter();

try {
    foreach ($sizes as $sizeName => $size) {
        $qrCode = QrCode::create($data)
            ->setSize($size)
            ->setMargin(10)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
        
        $qrCodeImage = $writer->write($qrCode);
        
        if ($outputFormat === 'direct') {
            echo 'Output as direct image';
            header("Content-Type: {$qrCodeImage->getMimeType()}");
            echo $qrCodeImage->getString();
            exit;
        } else {
            echo 'Store as base64';
            $result[$sizeName] = [
                'size' => $size,
                'image' => base64_encode($qrCodeImage->getString()),
                'mime_type' => $qrCodeImage->getMimeType()
            ];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'qr_codes' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'QR generation failed: ' . $e->getMessage()]);
}
?>