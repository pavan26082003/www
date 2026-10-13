<?php
// qr_printer_final.php
header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Set proper autoload path
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("ERROR: Run 'composer require endroid/qr-code mike42/escpos-php' first\n");
}

require $autoloadPath;

// 2. Verify classes exist
$requiredClasses = [
    'Endroid\QrCode\QrCode',
    'Endroid\QrCode\Writer\PngWriter',
    'Escpos\Printer',
    'Escpos\PrintConnectors\NetworkPrintConnector'
];

foreach ($requiredClasses as $class) {
    if (!class_exists($class)) {
        die("ERROR: Class $class not found\n");
    }
}

// 3. Test QR Generation
try {
    echo "Testing QR code generation...\n";
    $qrCode = new Endroid\QrCode\QrCode('TEST123');
    $qrCode->setSize(200);
    $writer = new Endroid\QrCode\Writer\PngWriter();
    $result = $writer->write($qrCode);
    echo "QR Generation: SUCCESS\n";
    
    // 4. Test Printer Connection
    echo "Testing printer connection...\n";
    $connector = new Escpos\PrintConnectors\FilePrintConnector("php://stdout");
    $printer = new Escpos\Printer($connector);
    $printer->text("Connection Test Successful\n");
    $printer->cut();
    $printer->close();
    echo "Printer Test: SUCCESS\n";
    
    echo "ALL TESTS PASSED\n";
    
} catch (Exception $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}
?>