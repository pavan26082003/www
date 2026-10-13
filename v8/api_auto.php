<?php

$startTime = microtime(true);

$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

header('Content-Type: application/x-www-form-urlencoded');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/


/*-----------------------------------------------------*/
$req = file_get_contents ( 'php://input' );
$rawdata = $HTTP_RAW_POST_DATA;
//$json_data = json_decode($req);

header('Content-Type: application/json');
//header('Authorization: Bearer {token}');
header('Authorization: API Key');
//$not_found = false; // change it to true if you wish device not found response
//$user_check_faild = false; // change to true if you wish user credential error response

/*-----------------------MQTT--------------------------*/

include('jwt_utils.php');
//include('phpMQTT.php');
/*$server = 'efactor.tech';     // change if necessary
$port = 1883;                     // change if necessary
$username = 'mqttusr';                   // set your username
$password = '123456';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()*/
$timestamp=round(microtime(true) * 1000);



/************************************************************/
$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');
$efactor = 'Efactor';
$things = 'things';
$command = 'command';


if (isset($_GET['action']) && $_GET['action']=='test_details')
{

$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';
$parameter_name = 'Speed';
$query = "SELECT * FROM product_api WHERE node_id = '$node_id' AND parameter_name = '$parameter_name'";

// Execute the query once
$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result[0]; // Get the first row of the results





// Print the results

/*
echo "Number of result: " . $num . "\n";
if ($num > 0) {
    echo "First result: \n";
    foreach ($row as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No results found.\n";
}

*/



/*------------------------------------------------------------*/

$frames = array();

        // Iterate through each row in the result set
        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
                "type" => ($row["cmd_valueDefinition"] == "fixed value") ? "fixed" : "variable",
                "data_type" => $row["cmd_dataType"],
                "no_of_bytes" => (int)$row["cmd_numberOfBytes"],
                "byte_position" => array(
                    "start" => (int)$row["cmd_startBytePosition"],
                    "end" => (int)$row["cmd_endBytePosition"]
                ),
                "bit_size" => (int)$row["cmd_bitSize"],
                "display_value" => array(
                    "min" => ($row["cmd_displayValueMin"] !== null) ? $row["cmd_displayValueMin"] : "",
                    "max" => ($row["cmd_displayValueMax"] !== null) ? $row["cmd_displayValueMax"] : ""
                ),
                "value_in_hex" => array(
                    "min" => $row["cmd_valueInHexMin"],
                    "max" => $row["cmd_valueInHexMax"]
                )
            );
        }

        // Assign each field to a variable
        $brand_name = $row['brand_name'];
        $brand_logo = $row['brand_logo'];
        $product_name = $row['product_name'];
        $model_name = $row['model_name'];
        $product_description = $row['product_description'];
        $number_of_pages = $row['number_of_pages'];
        $page_names = $row['page_names'];
        $date = $row['date'];
        $itemid = $row['itemid'];
        $filename = $row['filename'];
        $apidate = $row['apidate'];
        $parameter_name = $row['parameter_name'];
        $cmd_dataflow = $row['cmd_dataflow'];
        $cmd_framesize = $row['cmd_framesize'];
        $cmd_frametrigger = $row['cmd_frametrigger'];
        $resp_dataflow = $row['resp_dataflow'];
        $resp_framesize = $row['resp_framesize'];
        $resp_frametrigger = $row['resp_frametrigger'];
        $error_dataflow = $row['error_dataflow'];
        $error_framesize = $row['error_framesize'];
        $error_frametrigger = $row['error_frametrigger'];
         $cmd_startbytePosition = explode(',', $row['cmd_startbytePosition']);
            $cmd_endbytePosition = explode(',', $row['cmd_endbytePosition']);
            $cmd_fielddescription = explode(',', $row['cmd_fielddescription']);
            $cmd_bitsize = explode(',', $row['cmd_bitsize']);
            $cmd_datatype = explode(',', $row['cmd_datatype']);
            $cmd_valuedefinition = explode(',', $row['cmd_valuedefinition']);
            $cmd_displayvaluemin = explode(',', $row['cmd_displayvaluemin']);
            $cmd_displayvaluemax = explode(',', $row['cmd_displayvaluemax']);
            $cmd_valueinhexmin = explode(',', $row['cmd_valueinhexmin']);
            $cmd_valueinhexmax = explode(',', $row['cmd_valueinhexmax']);
            $cmd_uitype = explode(',', $row['cmd_uitype']);
            $cmd_uiid = explode(',', $row['cmd_uiid']);
            $cmd_uipageno = explode(',', $row['cmd_uipageno']);
            $cmd_uidescriptioninout = explode(',', $row['cmd_uidescriptioninout']);
            $cmd_sliderstepvalue = explode(',', $row['cmd_sliderstepvalue']);
            $cmd_developernotes = explode(',', $row['cmd_developernotes']);
            $cmd_numberOfbytes = explode(',', $row['cmd_numberOfbytes']);
            
            $resp_startBytePosition = explode(',', $row['resp_startBytePosition']);
            $resp_endBytePosition = explode(',', $row['resp_endBytePosition']);
            $resp_fieldDescription = explode(',', $row['resp_fieldDescription']);
            $resp_numberOfBytes = explode(',', $row['resp_numberOfBytes']);
            $resp_bitSize = explode(',', $row['resp_bitSize']);
            $resp_dataType = explode(',', $row['resp_dataType']);
            $resp_valueDefinition = explode(',', $row['resp_valueDefinition']);
            $resp_displayValueMin = explode(',', $row['resp_displayValueMin']);
            $resp_displayValueMax = explode(',', $row['resp_displayValueMax']);
            $resp_valueInHexMin = explode(',', $row['resp_valueInHexMin']);
            $resp_valueInHexMax = explode(',', $row['resp_valueInHexMax']);
            $resp_uiType = explode(',', $row['resp_uiType']);
            $resp_uiId = explode(',', $row['resp_uiId']);
            $resp_uiPageNo = explode(',', $row['resp_uiPageNo']);
            $resp_uiDescriptionInOut = explode(',', $row['resp_uiDescriptionInOut']);
            $resp_sliderStepValue = explode(',', $row['resp_sliderStepValue']);
            $resp_developerNotes = explode(',', $row['resp_developerNotes']);

            $error_startBytePosition = explode(',', $row['error_startBytePosition']);
            $error_endBytePosition = explode(',', $row['error_endBytePosition']);
            $error_fieldDescription = explode(',', $row['error_fieldDescription']);
            $error_numberOfBytes = explode(',', $row['error_numberOfBytes']);
            $error_bitSize = explode(',', $row['error_bitSize']);
            $error_dataType = explode(',', $row['error_dataType']);
            $error_valueDefinition = explode(',', $row['error_valueDefinition']);
            $error_displayValueMin = explode(',', $row['error_displayValueMin']);
            $error_displayValueMax = explode(',', $row['error_displayValueMax']);
            $error_valueInHexMin = explode(',', $row['error_valueInHexMin']);
            $error_valueInHexMax = explode(',', $row['error_valueInHexMax']);
            $error_uiType = explode(',', $row['error_uiType']);
            $error_uiId = explode(',', $row['error_uiId']);
            $error_uiPageNo = explode(',', $row['error_uiPageNo']);
            $error_uiDescriptionInOut = explode(',', $row['error_uiDescriptionInOut']);
            $error_sliderStepValue = explode(',', $row['error_sliderStepValue']);
            $error_developerNotes = explode(',', $row['error_developerNotes']);
        $user_id = $row['user_id'];
        $cmd_numberOfbytes = $row['cmd_numberOfbytes'];
        $ui_sequence_no = $row['ui_sequence_no'];
        $ui_options = $row['ui_options'];
        $ui_type = $row['ui_type'];
        $ui_name = $row['ui_name'];
        $ui_cmd_value = $row['ui_cmd_value'];
        $ui_range_min = $row['ui_range_min'];
        $ui_data_type = $row['ui_data_type'];
        $ui_range_max = $row['ui_range_max'];
        $ui_permission = $row['ui_permission'];
        $ui_action = $row['ui_action'];
        $ui_step = $row['ui_step'];
        $page_no = $row['page_no'];
        $cmd_noofrows = $row['cmd_noofrows'];
        $resp_noofrows = $row['resp_noofrows'];
        $error_noofrows = $row['error_noofrows'];
        $cmd_asperui = $row['cmd_asperui'];
        $resp_asperui = $row['resp_asperui'];
        $error_asperui = $row['error_asperui'];
        $selected = $row['selected'];
        $node_id=$row['node_id'];




echo $cmd_fielddescription[0];
echo $cmd_fielddescription[1];

echo $cmd_fielddescription[2];

echo $cmd_fielddescription[3];


        // Display the variables for verification
      /*  echo "<pre>";
        print_r(get_defined_vars());
        echo "</pre>";*/


$parameterArray = [];
            



for ($i = 0; $i < $cmd_framesize; $i++) {
                $field = [
                    "name" => $cmd_fielddescription[$i],
                    "type" => $cmd_uitype[$i] == 'Slider' ? 'variable' : 'fixed',
                    "data_type" => $cmd_datatype[$i],
                    "no_of_bytes" => (int)$cmd_numberOfbytes[$i],
                    "byte_position" => [
                        "start" => (int)$cmd_startbytePosition[$i],
                        "end" => (int)$cmd_endbytePosition[$i]
                    ],
                    "bit_size" => (int)$cmd_bitsize[$i]
                ];

                if ($cmd_uitype[$i] == 'Slider') {
                    $field["display_value"] = [
                        "min" => $cmd_displayvaluemin[$i],
                        "max" => $cmd_displayvaluemax[$i]
                    ];
                    $field["value_in_hex"] = [
                        "min" => $cmd_valueinhexmin[$i],
                        "max" => $cmd_valueinhexmax[$i]
                    ];
                } else {
                    $field["value_in_hex"] = [
                        "value" => $cmd_valueinhexmin[$i]
                    ];
                }

                $fields[] = $field;
            }

/*
for ($j = 0; $j < $noofparameters; $j++) {
               

$parameterArray[] = array(
                    "name" => $parameter_name,
                    "page_no" => 1,
                    "multiple_ui" => false,
                    "trigger_ui" => true,
                    "data_type" => "bool",
                    "ui_type" => "efactor.ui.toggle",
                    "ui_ids" => array(1),
                    "ui_page_no" => 1,
                    "ui_description" => array("read", "write"),
                    "frame" => array(
                        "commands" => $fields
                    )
                );

}
*/






$noofparameters =1;









/*-------------------------------------------------------------*/
/*************************************************************/


$node_details = array(
    "id" => $node_id,
    "pages" => array(
        "fan_82000020" => array(
            "page1" => array(
                "Direction" => 0,
                "led colour" => 0,
                "output" => true,
                "power" => true,
                "Speed" => 1,
                "led" => true,
                "name" => "fan",
                "current(A)" => "1.3860000371933",
                "voltage(V)" => "25.375",
                "rpm" => "394",
                "buzzer" => false
            ),
            "page2" => array(
                "resistance" => "4.2",
                "lmin" => "7.3",
                "lmax" => "8",
                "frequency" => "16000",
                "pole pairs" => "8",
                "open loop current" => "1.5",
                "power limit" => "44",
                "torque limit" => "7000",
                "speed1" => "6",
                "speed2" => "10",
                "speed3" => "16",
                "speed4" => "24",
                "speed5" => "32",
                "speed6" => "35",
                "speed7" => "38",
                "button" => null
            )
        )
    ),
    "config" => array(
        "attributes" => array(
            array(
                "name" => "serial_num",
                "value" => "1345"
            ),
            array(
                "name" => "model",
                "value" => "wifi"
            )
        ),
        "config_version" => "2020-03-20",
        "devices" => array(
            array(
                "name" => "fan_82000020",
                "total_pages" => $number_of_pages,
                "attributes" => array(
                    array(
                        "name" => "Device Id",
                        "value" => $node_id
                    ),
                    array(
                        "name" => "mac",
                        "value" => "CC:50:E3:16:1D:6F"
                    )
                ),
                "pages" => array(
                    "page2" => $frames,
                    "page1" => array(
                        array(
                            "name" => "name",
                            "data_type" => "String",
                            "properties" => array("read", "write"),
                            "type" => "efactor.param.name",
                            "page_no" => 1
                        ),
                                       
                $parameterArray[] = array(
                    "name" => $parameter_name,
                    "page_no" => 1,
                    "multiple_ui" => false,
                    "trigger_ui" => true,
                    "data_type" => "bool",
                    "ui_type" => "efactor.ui.toggle",
                    "ui_ids" => array(1),
                    "ui_page_no" => 1,
                    "ui_description" => array("read", "write"),
                    "frame" => array(
                        "commands" => $fields
                    
                ),


                        
                "primary" => "power",
                "type" => "efactor.device.fan",
                "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"
            )
        ),
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => null,
            "name" => "NULL",
            "type" => "WIFI"
        ),
        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => "false",
            "timestamp" => 1717898131809
        )
    )
);

echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));




/************************************************************/

}








?>
