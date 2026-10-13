<?php
$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

header('Content-Type: application/x-www-form-urlencoded');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


/*-----------------------------------------------------*/
$req = file_get_contents( 'php://input' );
//$rawdata = file_get_contents('php://input');
//$rawdata = $HTTP_RAW_POST_DATA;
//echo $req; 
$json_data = json_decode($req);
//echo $json_data;
header('Content-Type: application/json');
//header('Authorization: Bearer {token}');
header('Authorization: API Key');


/*-----------------------MQTT--------------------------*/



//include('jwt_utils.php');

$server = 'efactor.tech';     // change if necessary
$port = 1883;                     // change if necessary
$username = 'efactorpw';                   // set your username
$password = '123456';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()
/*----------------------------mail-------------------------*/
require_once 'vendor/autoload.php';



$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');
$efactor = 'Efactor';
$things = 'things';
$command = 'command';
function convertToImage2LcdStyleHexFlat($imagePath) {
    $width = 128;
    $height = 250;

    if (!file_exists($imagePath)) {
        throw new Exception("Image file not found: $imagePath");
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

    $brightFactor = 0.95;
    $contrastFactor = 1.2;
    $brightnessAdjust = (int)(($brightFactor - 1) * 255);
    $contrastAdjust = (int)(-($contrastFactor - 1) * 100);
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
                $output[] = sprintf("%02X", $byte);
                $byte = 0;
                $bitPosition = 6;
            } else {
                $bitPosition -= 2;
            }
        }
    }

    if ($bitPosition != 6) {
        $output[] = sprintf("%02X", $byte);
    }

    imagedestroy($source);
    imagedestroy($resized);

    return implode('', $output); // return flat hex string
}

try {



$server = 'efactor.tech';
    $port = 1883;
    $username = 'efactorpw';
    $password = '123456';
    $client_id = 'phpMQTT-' . uniqid();
    $deviceAddress = "FFFFFFFFFFFFFFFF7EC100000000A900";
    $deviceId = "FFFFFFFFFFFFFFFF7EC2000000005200";
    $mqttTopic = "Efactor/things/$deviceAddress/command";


//$query = "SELECT * FROM efactorproduct where node_id='$deviceId'";SELECT * FROM `efactorproduct` WHERE node_id='FFFFFFFFFFFFFFFF7EC2000000005200'

$query = "SELECT * FROM efactorproduct WHERE node_id='$deviceId'";
							   
$row = $db->get_row($query);

/*
if ($row) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "No product found.";
}

*/


$model_name=$row['model_name'];



 $lable_path_1=$row['label_path'];
//echo $model_name;

//$lable_path='/var/www/html/files/brand_logo/necklace.jpg';

//echo $lable_path_1;

    $imagePath = $lable_path_1;
    $imageData = convertToImage2LcdStyleHexFlat($imagePath);

    

    $chunkSize = 64; // 64 bytes = 128 hex characters
    $totalFrames = ceil(strlen($imageData) / ($chunkSize * 2));
    $frameIndex = 1;

    for ($i = 0; $i < strlen($imageData); $i += ($chunkSize * 2)) {
        $payloadData = substr($imageData, $i, $chunkSize * 2);
        $payload = sprintf("CD%02X%02X%s", $totalFrames, $frameIndex, $payloadData);

        $mqttMessage = json_encode([
            "command_type" => "GET_DEVICE_STATUS",
            "address" => $deviceAddress,
            "device_id" => $deviceId,
            "payload" => $payload
        ]);

        $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
        if (!$mqtt->connect(true, NULL, $username, $password)) {
            throw new Exception("MQTT connection failed for frame $frameIndex");
        }
        $mqtt->publish($mqttTopic, $mqttMessage);
        $mqtt->close();

        $frameIndex++;
        usleep(120 * 1000); // 100ms
    }

    // Send stop frame
    $mqttMessageStop = json_encode([
        "command_type" => "GET_DEVICE_STATUS",
        "address" => $deviceAddress,
        "device_id" => $deviceId,
        "payload" => "CDFFFA0A"
    ]);
    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
    if (!$mqtt->connect(true, NULL, $username, $password)) {
        throw new Exception("MQTT connection failed for stop frame");
    }
    $mqtt->publish($mqttTopic, $mqttMessageStop);
    $mqtt->close();

    // Optional DB update
    $db->query("UPDATE `efactorproduct` SET display_start='false' WHERE node_id='$deviceAddress'");

    echo json_encode(["status" => "success", "message" => "Image converted and sent via MQTT"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
