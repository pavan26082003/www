<?php
/**
 * 2-bit Image to Hex Converter (Image2Lcd-style header) for 128x250 output
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '256M');

if (!extension_loaded('gd')) {
    die('<h2>Error: GD library not installed</h2><p>Install php-gd.</p>');
}

function convertToImage2LcdStyleHex($imagePath) {
    $width = 128;
    $height = 250;

    if (!file_exists($imagePath)) {
        throw new Exception("Image file not found");
    }

    $info = getimagesize($imagePath);
    if (!$info) {
        throw new Exception("Invalid image file");
    }

    switch ($info[2]) {
        case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($imagePath); break;
        case IMAGETYPE_PNG:  $source = imagecreatefrompng($imagePath);  break;
        case IMAGETYPE_GIF:  $source = imagecreatefromgif($imagePath);  break;
        default: throw new Exception("Unsupported image type");
    }

    $resized = imagecreatetruecolor($width, $height);
    imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));



  $brightFactor = 0.95;  // Slightly reduce brightness
$contrastFactor = 1.2; // Slightly increase contrast

$brightnessAdjust = (int)(($brightFactor - 1) * 255);
$contrastAdjust   = (int)(-($contrastFactor - 1) * 100);

imagefilter($resized, IMG_FILTER_BRIGHTNESS, $brightnessAdjust);
imagefilter($resized, IMG_FILTER_CONTRAST, $contrastAdjust);

    $output = [];
    $byte = 0;
    $bitPosition = 6;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($resized, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

          /*  $colorDistances = [
                0 => $r + $g + $b,                          // Black
                1 => (255 - $r) + (255 - $g) + (255 - $b),  // White
                2 => (255 - $r) + ($g * 2) + ($b * 2),      // Red
                3 => (255 - $r) + (255 - $g) + ($b * 3)     // Yellow
            ];

            $colorIndex = array_keys($colorDistances, min($colorDistances))[0];*/

      
            $palette = [
   
    [255, 255, 255], // white → 0x00
    [255, 255, 0],   // yellow → 0x01
    [255, 0, 0],     // red → 0x02
     [0, 0, 0],       // black → 0x03
    
];




$minDist = PHP_INT_MAX;
$colorIndex = 0;

foreach ($palette as $i => [$pr, $pg, $pb]) {
    $dist = ($r - $pr) ** 2 + ($g - $pg) ** 2 + ($b - $pb) ** 2;
    if ($dist < $minDist) {
        $minDist = $dist;
        $colorIndex = $i;
    }
}





            $byte |= ($colorIndex << $bitPosition);

            if ($bitPosition == 0) {
                $output[] = sprintf("0X%02X", $byte);
                $byte = 0;
                $bitPosition = 6;
            } else {
                $bitPosition -= 2;
            }
        }
    }

    if ($bitPosition != 6) {
        $output[] = sprintf("0X%02X", $byte);
    }

    imagedestroy($source);
    imagedestroy($resized);

    // HEADER
    $arraySize = count($output);
    $commentHeader = "/* 0X00,0X02,0X80,0X00,0XFA,0X00, */"; // 128 (0x80), 250 (0xFA)

    $name = preg_replace('/[^a-zA-Z0-9_]/', '_', pathinfo($imagePath, PATHINFO_FILENAME));
    $result = "const unsigned char gImage_{$name}[{$arraySize}] = {\n    {$commentHeader}\n";

    foreach (array_chunk($output, 16) as $line) {
        $result .= "    " . implode(',', $line) . ",\n";
    }

    $result .= "};\n";

    return $result;
}

// Upload handler
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed with error code: {$file['error']}");
        }

        if (!getimagesize($file['tmp_name'])) {
            throw new Exception("Uploaded file is not a valid image");
        }

        $hexData = convertToImage2LcdStyleHex($file['tmp_name']);


// Step 1: Remove the comment header line and array declaration
$cleanHexLines = preg_replace([
    '/\/\*.*?\*\//s',                    // Remove /* ... */ comment block
    '/const\s+unsigned\s+char\s+.*?=\s*{/', // Remove array declaration
    '/};/',                              // Remove closing brace
], '', $hexData);

// Step 2: Extract hex values like 0XAA
preg_match_all('/0X([0-9A-F]{2})/i', $cleanHexLines, $matches);

// Step 3: Flatten into a continuous string
$flatHexString = implode('', $matches[1]);

// Output in HTML
echo "<!DOCTYPE html><html><head><title>Conversion Result</title></head><body>";
echo "<h1>Flat Hex Output</h1>";
echo "<textarea style='width:100%; height:300px;'>" . htmlspecialchars($flatHexString) . "</textarea><br><br>";
echo "<a href='" . $_SERVER['PHP_SELF'] . "'>Convert another image</a>";
echo "</body></html>";
exit;


    }
} catch (Exception $e) {
    http_response_code(500);
    echo '<h2>Error</h2><p style="color:red">' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image2Lcd-Style Converter (128x250)</title>
</head>
<body>
    <h1>Convert Image to Image2Lcd C Array (128x250)</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Select Image (JPEG, PNG, GIF):</label><br>
        <input type="file" name="image" required><br><br>
        <button type="submit">Convert</button>
    </form>
</body>
</html>
