<?php
/**
 * Circular initials image for Recent Activity rows.
 * The app loads this the same way it loads the Grid QR image.
 *
 * GET /v8/grid_initials.php?t=SJ&c=7c6bf2
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$letters = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['t'] ?? 'S')) ?: 'S', 0, 2));
if ($letters === '') {
    $letters = 'S';
}
$hex = strtolower((string) preg_replace('/[^a-f0-9]/i', '', (string) ($_GET['c'] ?? '5b6cff')));
if (strlen($hex) !== 6) {
    $hex = '5b6cff';
}

if (!function_exists('imagecreatetruecolor')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'GD is required';
    exit;
}

$size = 128;
$image = imagecreatetruecolor($size, $size);
imagealphablending($image, true);
imagesavealpha($image, true);
$clear = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $clear);
$fill = imagecolorallocate(
    $image,
    hexdec(substr($hex, 0, 2)),
    hexdec(substr($hex, 2, 2)),
    hexdec(substr($hex, 4, 2))
);
imagefilledellipse($image, (int) ($size / 2), (int) ($size / 2), $size - 1, $size - 1, $fill);
$white = imagecolorallocate($image, 255, 255, 255);

$font = '';
foreach (array(
    'C:/Windows/Fonts/arialbd.ttf',
    'C:/Windows/Fonts/arial.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
    '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
) as $candidate) {
    if (is_file($candidate)) {
        $font = $candidate;
        break;
    }
}

if ($font !== '' && function_exists('imagettftext')) {
    $fontSize = strlen($letters) > 1 ? 46 : 56;
    $box = imagettfbbox($fontSize, 0, $font, $letters);
    $textWidth = abs($box[2] - $box[0]);
    $textHeight = abs($box[7] - $box[1]);
    $x = (int) (($size - $textWidth) / 2);
    $y = (int) (($size + $textHeight) / 2) - 6;
    imagettftext($image, $fontSize, 0, $x, $y, $white, $font, $letters);
} else {
    $fontId = 5;
    $x = (int) (($size - imagefontwidth($fontId) * strlen($letters)) / 2);
    $y = (int) (($size - imagefontheight($fontId)) / 2);
    imagestring($image, $fontId, $x, $y, $letters, $white);
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
imagepng($image);
imagedestroy($image);
