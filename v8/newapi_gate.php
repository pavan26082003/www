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

/************************************************************/

if (isset($_GET['action']) && $_GET['action']=='test_details')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {

                      
$node='{
    "url": "action=test_details",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4",
    "next_id": null,
    "total": 1,
    "node_details": [
        {
            "id": "FFFFFFFFFFFFFFFF7EC100000000A300",
            "config": {
                "attributes": [
                    {
                        "name": "serial_num",
                        "value": "1345"
                    },
                    {
                        "name": "model",
                        "value": "wifi"
                    }
                ],
                "config_version": "2020-03-20",
                "devices": [
                    {
                        "name": Gateway_A3,
                        "total_pages": "1",
                        "attributes": [
                            {
                                "name": "Device Id",
                                "value": "FFFFFFFFFFFFFFFF7EC100000000A300"
                            },
                            {
                                "name": "mac",
                                "value": "CC:50:E3:16:1D:6F"
                            }
                        ],
                        "pages": {
                            "Page0": [
                                {
                                    "frame": {
                                        "name": "BAUDRATE",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "BC",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "BAUDRATE",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 1,
                                                "asperui": [
                                                    "4800",
                                                    "9600",
                                                    "57600",
                                                    "38400",
                                                    "115200",
                                                    "230400"
                                                ],
                                                "sno_asperui": [],
                                                "asperui_values": [
                                                    "30",
                                                    "31",
                                                    "32",
                                                    "33",
                                                    "34",
                                                    "35"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "Uart msg ID",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "D6",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "Uart msg id",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 2,
                                                "asperui": [
                                                    "disable",
                                                    "enable"
                                                ],
                                                "sno_asperui": [],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "Error Code",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "E3",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "Error Code",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 3,
                                                "asperui": [
                                                    "disable",
                                                    "enable"
                                                ],
                                                "sno_asperui": [],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                }
                            ],
                            "Page1": [
                                {
                                    "frame": {
                                        "name": "name",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "name",
                                                "asperui_value": "null",
                                                "value": "0000",
                                                "Field_description": "Data",
                                                "type": "Range Value",
                                                "data_type": "String",
                                                "no_of_bytes": 2,
                                                "byte_start_position": 1,
                                                "ui_type": "Text",
                                                "ui_ids": 1,
                                                "asperui": [],
                                                "sno_asperui": [],
                                                "asperui_values": [],
                                                "ui_description": "Read",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "power",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "A1",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "Function Code",
                                                "value": "C2",
                                                "Field_description": "Function Code",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "power",
                                                "asperui_value": "",
                                                "value": "",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "Toggle Switch",
                                                "ui_ids": 2,
                                                "asperui": [
                                                    "false",
                                                    "true"
                                                ],
                                                "sno_asperui": [],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 4,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                }
                            ]
                        },
                        "primary": "power",
                        "type": "efactor.device.Gateway",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    },

                   

                


                     {
                        "name": fan_9A,
                        "total_pages": "0",
                        "attributes": [
                            {
                                "name": "Device Id",
                                "value": "FFFFFFFFFFFFFFFF7EC2000000009A00"
                            },
                            {
                                "name": "mac",
                                "value": "CC:50:E3:16:1D:6F"
                            }
                        ],
                        "pages": {
                            "Device Communication": [
                                {
                                    "frame": {
                                        "name": "BAUDRATE",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "BC",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "baudrate",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 1,
                                                "asperui": [
                                                    "4800",
                                                    "9600",
                                                    "57600",
                                                    "38400",
                                                    "115200",
                                                    "230400"
                                                ],
                                                "sno_asperui": [
                                                    "00",
                                                    "01",
                                                    "02",
                                                    "03",
                                                    "04",
                                                    "05"
                                                ],
                                                "asperui_values": [
                                                    "30",
                                                    "31",
                                                    "32",
                                                    "33",
                                                    "34",
                                                    "35"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "Uart msg ID",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "D6",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "uartmsgid",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 2,
                                                "asperui": [
                                                    "disable",
                                                    "enable"
                                                ],
                                                "sno_asperui": [
                                                    "00",
                                                    "01"
                                                ],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "Error Code",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "E3",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "errorcode",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 3,
                                                "asperui": [
                                                    "disable",
                                                    "enable"
                                                ],
                                                "sno_asperui": [
                                                    "00",
                                                    "01"
                                                ],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                }
                            ]
                        },
                        "primary": "power",
                        "type": "efactor.device.HBMDNC",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    },


                 {
                        "name": fan_9B,
                        "total_pages": "0",
                        "attributes": [
                            {
                                "name": "Device Id",
                                "value": "FFFFFFFFFFFFFFFF7EC2000000009B00"
                            },
                            {
                                "name": "mac",
                                "value": "CC:50:E3:16:1D:6F"
                            }
                        ],
                        "pages": {
                            "Device Communication": [
                                {
                                    "frame": {
                                        "name": "BAUDRATE",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "BC",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "baudrate",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 1,
                                                "asperui": [
                                                    "4800",
                                                    "9600",
                                                    "57600",
                                                    "38400",
                                                    "115200",
                                                    "230400"
                                                ],
                                                "sno_asperui": [
                                                    "00",
                                                    "01",
                                                    "02",
                                                    "03",
                                                    "04",
                                                    "05"
                                                ],
                                                "asperui_values": [
                                                    "30",
                                                    "31",
                                                    "32",
                                                    "33",
                                                    "34",
                                                    "35"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "Uart msg ID",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "D6",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "uartmsgid",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 2,
                                                "asperui": [
                                                    "disable",
                                                    "enable"
                                                ],
                                                "sno_asperui": [
                                                    "00",
                                                    "01"
                                                ],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                },
                                {
                                    "frame": {
                                        "name": "Error Code",
                                        "multiple_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "trigger_ui": [
                                            "false",
                                            "false",
                                            "false"
                                        ],
                                        "commands": [
                                            {
                                                "default_name": "CommandId",
                                                "value": "E3",
                                                "Field_description": "CommandId",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 1,
                                                "ui_type": "None"
                                            },
                                            {
                                                "default_name": "errorcode",
                                                "asperui_value": "null",
                                                "value": "00",
                                                "Field_description": "Data",
                                                "type": "Fixed Value",
                                                "data_type": "UnsignedInt",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 2,
                                                "ui_type": "Drop Down",
                                                "ui_ids": 3,
                                                "asperui": [
                                                    "disable",
                                                    "enable"
                                                ],
                                                "sno_asperui": [
                                                    "00",
                                                    "01"
                                                ],
                                                "asperui_values": [
                                                    "00",
                                                    "01"
                                                ],
                                                "ui_description": "Read/Write",
                                                "slider_step_value": 0,
                                                "display_value": {
                                                    "min": 0,
                                                    "max": 0
                                                }
                                            },
                                            {
                                                "default_name": "Terminator",
                                                "value": "0A",
                                                "Field_description": "Terminator",
                                                "no_of_bytes": 1,
                                                "byte_start_position": 3,
                                                "ui_type": "None"
                                            }
                                        ],
                                        "response": [],
                                        "error": []
                                    }
                                }
                            ]
                        },
                        "primary": "power",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }





                ],
                "info": {
                    "fw_version": "1.5.1",
                    "model": "EFGATEW V1.0",
                    "name": "Gateway_7E0000A3",
                    "type": "WIFI",
                    "SSID": "",
                    "Access_Point": "EWIFI_7E0000A3",
                    "Guarantee_Card": "https://efactor.tech/v8/api.php?action=guarantee_card_display&model_name=EFGATEW V1.0&user_id=177",
                    "Video": "https://efactor.tech/v8/api.php?action=app_video&model_name=EFGATEW V1.0&user_id=177",
                    "Auto_Test": "https://efactor.tech/v8/auto_test.php?model_name=EFGATEW V1.0&user_id=177&node_id=FFFFFFFFFFFFFFFF7EC100000000A300"
                },
                "node_id": "FFFFFFFFFFFFFFFF7EC100000000A300"
            },
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": "false",
                    "timestamp": 1735624111781
                }
            }
        }
    ],
    "nodes": "FFFFFFFFFFFFFFFF7EC100000000A300"
}';



echo $node;

exit;



/************************************************************/

    }

}








/**************************test_nodes**********************************/
if (isset($_GET['action']) && $_GET['action']=='test_nodes')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {




                         if(!empty($_GET["start_id"]))
                      {

                      /*-----------------------if start_id start----------------------*/

                         $query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];

                         
                        $arr=array();
			foreach ($doctors as $key => $doctor) 
			{
			  array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type']));
			}
  
			 $device_id=$arr[0]['node_id'];	  
			 $next_id_2=$arr[1]['node_id'];
		         $nextid = $next_id_2;

                        $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors_node = $db->get_results($query_node); 
			$num_node = $db->num_rows($query_node);
                        $row_node = $db->get_row($query_node);    
		        $nodeid=$row_node['node_id'];
                        $user_id=$row_node['user_id'];
                        $model_name=$row_node['model_number'];

                        $moduletype= $row_node['module_type'];
		        $name=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		        $direction=$row_node['direction'];
		        //$power=$row_node['power'];
			$baudrate=$row_node['baudrate'];
			//$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                        $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
                        $devicename=$row_node['device_name']; 
                        $curr_devicetype=$row_node['current_devicetype'];
                        $recipe=$row_node['recipe'];
                        $user_priority=$row_node['user_priority'];
                        $cookermode=$row_node['cookermode'];
                        $cooktime=$row_node['cook_time'];
                        $cook_run_temp=$row_node['cook_run_temp'];
                        $current=$row_node['current'];
                        $voltage=$row_node['ac_float'];
                        $bus_voltage=$row_node['bus_voltage'];
                        $rpm=$row_node['rpm'];
                        $timer=$row_node['timer'];
                        $watt=$row_node['power_watt'];
                        $rem_time=$row_node['rem_time'];
                        $hood_autoclean=$row_node['hood_autoclean'];
                        $hood_speed=$row_node['hood_speed'];
                        $hood_lamp=$row_node['hood_lamp'];




                         /*--------------------------if start_id end---------------------------------------------*/


                       }

                     else
{






 $query_dev="SELECT node_id FROM efactorproduct where access_token='$val' AND is_added='1'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];


                        
                        
                        
                        $query_node_id="SELECT node_id FROM `efactorproduct` WHERE access_token='$val' AND add_gateway='$node_id'";
			$doctors_node_id = $db->get_results($query_node_id); 
			$num_node_id = $db->num_rows($query_node_id);
                        $row_node_id = $db->get_row($query_node_id);    
		      //  $nodeid=$row_node_id['node_id'];



$arr=array();


for ($pos = 0; $pos < $num_node_id; $pos++) {  
   $link_dev = $doctors_node_id[$pos]['node_id'];
    
 

  $query_node = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$link_dev'";
		    $num_node = $db->num_rows($query_node);
		    $row_node = $db->get_row($query_node); 



                        $user_id=$row_node['user_id'];
                        $model_name_link=$row_node['model_number'];
                        $moduletype= $row_node['module_type'];
                        $device_type= $row_node['device_type'];
		        $name=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		        $direction=$row_node['direction'];
		        //$power=$row_node['power'];
			$baudrate=$row_node['baudrate'];
			//$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                        $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
                        $devicename=$row_node['device_name']; 
                        $curr_devicetype=$row_node['current_devicetype'];
                        $recipe=$row_node['recipe'];
                        $user_priority=$row_node['user_priority'];
                        $cookermode=$row_node['cookermode'];
                        $cooktime=$row_node['cook_time'];
                        $cook_run_temp=$row_node['cook_run_temp'];
                        $current=$row_node['current'];
                        $voltage=$row_node['ac_float'];
                        $bus_voltage=$row_node['bus_voltage'];
                        $rpm=$row_node['rpm'];
                        $timer=$row_node['timer'];
                        $watt=$row_node['power_watt'];
                        $rem_time=$row_node['rem_time'];
                        $hood_autoclean=$row_node['hood_autoclean'];
                        $hood_speed=$row_node['hood_speed'];
                        $hood_lamp=$row_node['hood_lamp'];
                        $user_id=$row_node['user_id'];











//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name_link' AND user_id = '177'";
  $params = $db->get_results($query_param);
        $num_param = $db->num_rows($query_param);
		    $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];





//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];
$page0 = [];
$page2 = [];
$page3 = [];

 
foreach ($params as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];

//echo $param1;

//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177' AND parameter_name='$param1'";

$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND user_id = '$user_id' AND parameter_name='$param1'";



        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];

        $brand_name = $row_param['brand_name'];
        $brand_logo = $row_param['brand_logo'];
        $product_name = $row_param['product_name'];
        $model_name = $row_param['model_name'];
        $product_description = $row_param['product_description'];
        $number_of_pages = $row_param['number_of_pages'];
        $page_names = $row_param['page_names'];
        $date = $row_param['date'];
        $itemid = $row_param['itemid'];
        $filename = $row_param['filename'];
        $apidate = $row_param['apidate'];
        $parameter_name = $row_param['parameter_name'];
        $cmd_dataflow = $row_param['cmd_dataflow'];
        $cmd_framesize = $row_param['cmd_framesize'];
        $cmd_frametrigger = $row_param['cmd_frametrigger'];
        $resp_dataflow = $row_param['resp_dataflow'];
        $resp_framesize = $row_param['resp_framesize'];
        $resp_frametrigger = $row_param['resp_frametrigger'];
        $error_dataflow = $row_param['error_dataflow'];
        $error_framesize = $row_param['error_framesize'];
        $error_frametrigger = $row_param['error_frametrigger'];

            $cmd_startbytePosition = explode(',', $row_param['cmd_startbytePosition']);
            $cmd_endbytePosition = explode(',', $row_param['cmd_endbytePosition']);
            $cmd_fielddescription = explode(',', $row_param['cmd_fielddescription']);
            $cmd_bitsize = explode(',', $row_param['cmd_bitsize']);
            $cmd_datatype = explode(',', $row_param['cmd_datatype']);
            $cmd_valuedefinition = explode(',', $row_param['cmd_valuedefinition']);
            $cmd_displayvaluemin = explode(',', $row_param['cmd_displayvaluemin']);
            $cmd_displayvaluemax = explode(',', $row_param['cmd_displayvaluemax']);
            $cmd_valueinhexmin = explode(',', $row_param['cmd_valueinhexmin']);
            $cmd_valueinhexmax = explode(',', $row_param['cmd_valueinhexmax']);
            $cmd_uitype = explode(',', $row_param['cmd_uitype']);
            $cmd_uiid = explode(',', $row_param['cmd_uiid']);
            $cmd_uipageno = explode(',', $row_param['cmd_uipageno']);
            $cmd_uidescriptioninout = explode(',', $row_param['cmd_uidescriptioninout']);
            $cmd_sliderstepvalue = explode(',', $row_param['cmd_sliderstepvalue']);
            $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
            $cmd_developernotes = explode(',', $row_param['cmd_developernotes']);
            $cmd_noofbytes = explode(',', $row_param['cmd_numberOfbytes']);
           // $cmd_asperui = explode(',', $row_param['cmd_asperui']);
           // $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);

            

             
            $resp_startBytePosition = explode(',', $row_param['resp_startBytePosition']);
            $resp_endBytePosition = explode(',', $row_param['resp_endBytePosition']);
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_numberOfBytes = explode(',', $row_param['resp_numberOfBytes']);
            $resp_bitSize = explode(',', $row_param['resp_bitSize']);
            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_valueDefinition = explode(',', $row_param['resp_valueDefinition']);
            $resp_displayValueMin = explode(',', $row_param['resp_displayValueMin']);
            $resp_displayValueMax = explode(',', $row_param['resp_displayValueMax']);
            $resp_valueInHexMin = explode(',', $row_param['resp_valueInHexMin']);
            $resp_valueInHexMax = explode(',', $row_param['resp_valueInHexMax']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_uiId = explode(',', $row_param['resp_uiId']);
            $resp_uiPageNo = explode(',', $row_param['resp_uiPageNo']);
            $resp_uiDescriptionInOut = explode(',', $row_param['resp_uiDescriptionInOut']);
            $resp_sliderStepValue = explode(',', $row_param['resp_sliderStepValue']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_developerNotes = explode(',', $row_param['resp_developerNotes']);
          //  $resp_asperui = explode(',', $row_param['resp_asperui']);
          //  $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

            $error_startBytePosition = explode(',', $row_param['error_startBytePosition']);
            $error_endBytePosition = explode(',', $row_param['error_endBytePosition']);
            $error_fieldDescription = explode(',', $row_param['error_fieldDescription']);
            $error_numberOfBytes = explode(',', $row_param['error_numberOfBytes']);
            $error_bitSize = explode(',', $row_param['error_bitSize']);
            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_valueDefinition = explode(',', $row_param['error_valueDefinition']);
            $error_displayValueMin = explode(',', $row_param['error_displayValueMin']);
            $error_displayValueMax = explode(',', $row_param['error_displayValueMax']);
            $error_valueInHexMin = explode(',', $row_param['error_valueInHexMin']);
            $error_valueInHexMax = explode(',', $row_param['error_valueInHexMax']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_uiId = explode(',', $row_param['error_uiId']);
            $error_uiPageNo = explode(',', $row_param['error_uiPageNo']);
            $error_uiDescriptionInOut = explode(',', $row_param['error_uiDescriptionInOut']);
            $error_sliderStepValue = explode(',', $row_param['error_sliderStepValue']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_developerNotes = explode(',', $row_param['error_developerNotes']);
         //   $error_asperui = explode(',', $row_param['error_asperui']);
         //   $error_asperui_values = explode(',', $row_param['error_asperui_values']);



                        


        $user_id = $row_param['user_id'];
        $ui_sequence_no = $row_param['ui_sequence_no'];
        $ui_options = $row_param['ui_options'];
        $ui_type = $row_param['ui_type'];
        $ui_name = $row_param['ui_name'];
        $ui_cmd_value = $row_param['ui_cmd_value'];
        $ui_range_min = $row_param['ui_range_min'];
        $ui_data_type = $row_param['ui_data_type'];
        $ui_range_max = $row_param['ui_range_max'];
        $ui_permission = $row_param['ui_permission'];
        $ui_action = $row_param['ui_action'];
        $ui_step = $row_param['ui_step'];
        $page_no = $row_param['page_no'];
        $cmd_noofrows = $row_param['cmd_noofrows'];
        $resp_noofrows = $row_param['resp_noofrows'];
        $error_noofrows = $row_param['error_noofrows'];



    /*    $cmd_asperui= array($row_param['cmd_asperui']);

 
        $resp_asperui = $row_param['resp_asperui'];
        $error_asperui = $row_param['error_asperui'];

        $cmd_asperui_values = $row_param['cmd_asperui_values'];*/


        
 

if ($param1 == 'BAUDRATE') {
   $cmd_asperui_val = array('', '30', '');
   $cmd_asui_val = array('', '4800', '');
    $cmd_asperui = [[], ["4800", "9600", "57600", "38400", "115200", "230400"], [], []];
    $cmd_asperui_values = [[], ["30", "31", "32", "33", "34", "35"], [], []];
}
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

       



        $selected = $row_param['selected'];





if($param1 =='Speed')
{
 $param_value="4";
$param_val='speed';


$cmd_asperui_val = array('', '', '', '');
 $cmd_asui_val =  array('', '', '', '');
$cmd_asperui = [[], [], [], []];
    $cmd_asperui_values = [[], [], [], []];

}






else if($param1 =='BAUDRATE')
{
 $param_value="9600";
}


else if($param1 =='power')
{
 $param_value="on";
$param_val="power";

 $cmd_asperui_val = array('', '', '00', '');
$cmd_asui_val =  array('', '', 'false', '');
$cmd_asperui = [[], [], ["false", "true"], []];
    $cmd_asperui_values = [[], [] ,["00", "01"], []];;

}

else if($param1 =='Buzzer')
{
 $param_value="true";
 $cmd_asperui_val = array('', '', '00', '');
$cmd_asui_val =  array('', '', 'false', '');

$cmd_asperui = [[], [], ["false", "true"], []];
    $cmd_asperui_values = [[], [] ,["00", "01"], []];;


}

else if($param1 =='Direction')
{
 $param_value="1";
$cmd_asperui_val = array('', '', '08', '');
$cmd_asui_val =  array('', '', '0', '');

$cmd_asperui = [[], [], ["0", "1"], []];
    $cmd_asperui_values = [[], [] ,["08", "08"], []];

}





else if($param1 =='Uart msg ID')
{
 $param_value="00";
$cmd_asperui_val = array('', '00', '');
$cmd_asui_val =  array('', 'disable', '');

$cmd_asperui = [[], [ "disable", "enable"], []];
    $cmd_asperui_values = [[] ,["00", "01"], []];

}




else if($param1 =='Get status')
{
 $param_value="00";
$resp_asperui_val = array('','','00','','08','','','','','','','','','','','','00','');
$resp_asui_val =  array('','','false','','00','','','','','','','','','','','','false','');       
$resp_asperui = [[],[],["false","true"],[],["00","01"],[],[],[],[],[],[],[],[],[],[],[],["false","true"],[]];
$resp_asperui_values = [[],[],["00","01"],[],["08","08"],[],[],[],[],[],[],[],[],[],[],[],["00","01"],[]];

}


else if($param1 =='Command Get Status')
{
 $param_value="00";
$resp_asperui_val = array('','','00','','08','','','','','','','','','','','','00','');
$resp_asui_val =  array('','','false','','00','','','','','','','','','','','','false','');       
$resp_asperui = [[],[],["false","true"],[],["00","01"],[],[],[],[],[],[],[],[],[],[],[],["false","true"],[]];
$resp_asperui_values = [[],[],["00","01"],[],["08","08"],[],[],[],[],[],[],[],[],[],[],[],["00","01"],[]];

}



else if($param1 =='Tuning')
{
 $param_value="00";
$cmd_asperui_val = array('','','','','','','','','','','','','','','','','','','');
$cmd_asui_val =  array('','','','','','','','','','','','','','','','','','','');       
$cmd_asperui = [[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[]];
$cmd_asperui_values = [[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[]];

}





else if($param1 =='Error Code')
{
 $param_value="00";
$cmd_asperui_val = array('', '00', '');
$cmd_asui_val =  array('', 'disable', '');

$cmd_asperui = [[], [ "disable", "enable"], []];
    $cmd_asperui_values = [[], ["00", "01"], []];;

}


else {
    $cmd_asperui_val = '';
$cmd_asui_val =  '';       
$cmd_asperui = [];
$cmd_asperui_values = [];

}







 



 if($cmd_framesize < 1)
{

$fields_cmd=array();


}

else{



for ($i = 0; $i < $cmd_framesize; $i++) {

if($param1 == 'Tuning')
{
$cmd_default_name= array();
$cmd_default_name=array('','','resistance', 'lmin', 'lmax', 'frequency', 'pole pairs', 'open loop current', 'power limit', 'torque limit', 'speed1', 'speed2', 'speed3', 'speed4', 'speed5', 'speed6', 'speed7');




}
else{
$cmd_default_name="";
}


if($cmd_defaultvalue[$i] == '')
{


$cmd_defaultvalue[$i]= $cmd_fielddescription[$i];
}
else{
$cmd_defaultvalue[$i]= $cmd_defaultvalue[$i];
}


    if($cmd_fielddescription[$i] == 'Data')
{


       if($cmd_uitype[$i] == 'Text(Default)')

         {


              
                $field_cmd = [
            "default name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "type" => $cmd_valuedefinition[$i],
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }


else{

            $field_cmd = [
            "default name" =>$cmd_defaultvalue[$i],
            "as per ui value" => $cmd_asui_val[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "type" => $cmd_valuedefinition[$i],
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   => (int)$cmd_uiid[$i],
            "as per ui" => $cmd_asperui[$i],
            "asperui values" => $cmd_asperui_values[$i],
            "ui_description" => $cmd_uidescriptioninout[$i],
            "slider step value"=> $cmd_sliderstepvalue[$i]
            ];

            $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];


         if ($cmd_datatype[$i] == 'UnsignedInt') {
    $field_cmd["display_value"] = [
        "min" => (int)$cmd_displayvaluemin[$i],
        "max" => (int)$cmd_displayvaluemax[$i]
    ];
} elseif ($cmd_datatype[$i] == 'Float') {
    $field_cmd["display_value"] = [
        "min" => (float)$cmd_displayvaluemin[$i],
        "max" => (float)$cmd_displayvaluemax[$i]
    ];
} else {
    $field_cmd["display_value"] = [
        "min" => $cmd_displayvaluemin[$i],
        "max" => $cmd_displayvaluemax[$i]
    ];
}

       // $fields_cmd[] = $field_cmd;


}

        $fields_cmd[] = $field_cmd;

}

else
{

           $field_cmd = [
                         
            "default name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
           
                  ];

$fields_cmd[] = $field_cmd;


}





    }


}

 

 

 
 if ($resp_framesize <= 1) {
    $fields_resp = array();
} else {
    // Loop through $resp_framesize and build $fields_resp
    for ($i = 0; $i < $resp_framesize; $i++) {
        if ($param1 == 'get status') {
            $resp_default_name = array('', '', 'power on/off', 'speed', 'Direction', 'color', 'lamp', 'timer', 'dimming level', 'fan enable/disable', 'Remote lock/unlock', 'rpm', 'power', 'current', 'voltage', 'vacrms', 'buzzer');
        } else {
            $resp_default_name = "";
        }


        if ($param1 == 'Tuning') {
           $resp_asperui= [[],[],["successfully set tuning parameters", "failed to set tuning parameters"],[]];
           $resp_asui_val = array('', '', 'successfully set tuning parameters', '');
     
    $resp_asperui_values = [[], [], ["00", "01"], []];
        } else {
            $resp_asperui= "";
            $resp_asperui_values= "";
        }





        if ($resp_defaultvalue[$i] == '') {
            $resp_defaultvalue[$i] = $resp_fieldDescription[$i];
        } else {
            $resp_defaultvalue[$i] = $resp_defaultvalue[$i];
        }


  

        if ($resp_fieldDescription[$i] == 'Data') {
            if ($resp_uiType[$i] == 'Text(Default)') {
                $field_resp = [
                    "default name" => $resp_defaultvalue[$i],
                    "value" => $resp_valueInHexMin[$i],
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i]
                ];
            } else {
                // Construct $field_resp
                $field_resp = [
                    "default name" => $resp_defaultvalue[$i],
                    "as per ui value" => $resp_asui_val[$i],
                    "value" => $resp_valueInHexMin[$i],
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "as per ui" => $resp_asperui[$i],
                    "asperui values" => $resp_asperui_values[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider step value" => $resp_sliderStepValue[$i]
                ];

                $field_resp["value_in_hex"] = [
                    "min" => $resp_valueInHexMin[$i],
                    "max" => $resp_valueInHexMax[$i]
                ];

                if ($resp_dataType[$i] == 'UnsignedInt') {
                    $field_resp["display_value"] = [
                        "min" => (int)$resp_displayValueMin[$i],
                        "max" => (int)$resp_displayValueMax[$i]
                    ];
                } elseif ($resp_dataType[$i] == 'Float') {
                    $field_resp["display_value"] = [
                        "min" => (float)$resp_displayValueMin[$i],
                        "max" => (float)$resp_displayValueMax[$i]
                    ];
                } else {
                    $field_resp["display_value"] = [
                        "min" => $resp_displayValueMin[$i],
                        "max" => $resp_displayValueMax[$i]
                    ];
                }
            }
            $fields_resp[] = $field_resp;
        } else {
            $field_resp = [
                "default name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
            ];
            $fields_resp[] = $field_resp;
        }
    }
}



 

 if($error_framesize <= 1)
{

$fields_error=array();


}


else{


    for ($i = 0; $i < $error_framesize; $i++) {







        // Construct $field_error
        $field_error = [
              "default name" =>$error_defaultvalue[$i],
               "as per ui value" => $error_asui_val[$i],
            "value" => $error_valueInHexMin[$i],
            "Field_description" => $error_fieldDescription[$i],
            "type" => $error_valueDefinition[$i],
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int) $error_numberOfBytes[$i],
            "byte_start_position" => (int) $error_startBytePosition[$i],
            "ui_type"  => $error_uiType[$i],
            "ui_ids"   => (int) $error_uiId[$i],
             "as per ui" => $error_asperui[$i],
            "asperui values" => $error_asperui_values[$i],
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider step value"=> $error_sliderStepValue[$i]
        ];

       
            $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
            ];



  if ($error_dataType[$i] == 'UnsignedInt') {
    $field_error["display_value"] = [
        "min" => (int)$error_displayValueMin[$i],
        "max" => (int)$error_displayValueMax[$i]
    ];
} elseif ($error_dataType[$i] == 'Float') {
    $field_error["display_value"] = [
        "min" => (float)$error_displayValueMin[$i],
        "max" => (float)$error_displayValueMax[$i]
    ];
} else {
    $field_error["display_value"] = [
        "min" => $error_displayValueMin[$i],
        "max" => $error_displayValueMax[$i]
    ];
}

  

        $fields_error[] = $field_error;
    }


}



 
  
  if ($param['parameter_name'] == "name" || $param['parameter_name'] == "power") {
    $name = $param['parameter_name'];
    
        $para = [
            "name" => $name,  
            "page_no" => $number_of_pages,
            "multiple_ui" => "bool", 
            "trigger_ui" => "bool",
       
            "frame" => [
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];  
// $result[] = $field_name;
        $result[] = $para;
     
}
 
    
      
    
    


  
 



}

 


$devices = array(
    "name" => $device_type,
    "total_pages" => $number_of_pages,
    "attributes" => array(
        array(
            "name" => "Device Id",
            "value" => $link_dev
        ),
        array(
            "name" => "mac",
            "value" => "CC:50:E3:16:1D:6F"
        )
    ),
    "pages" => array(
        "page0" => $page0,
        "page1" => $result,
        "page2" => $page2,
        "page3" => $page3
    ),

    "primary" => "power",
                "type" => "efactor.device.fan",
                "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"

);
      $devices_list[]=$devices;
 
}

 
 


$query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND is_added = '1' AND node_id > '$node_id' ORDER BY node_id ASC LIMIT 1";
$row = $db->get_row($query_next_id);

if ($row) {
    
    $device_id_next = $row['node_id'];
} else {
   
    $query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND node_id > '$node_id' ORDER BY node_id ASC LIMIT 1";
    $row = $db->get_row($query_next_id);

    if ($row) {
        $device_id_next = $row['node_id'];
    } else {
        
        $device_id_next = null; // or any other desired action
    }
}



$nextid = $device_id_next;












/*------------------else start_id end-------------------*/
}



/**********************************************************************************/







			
			

$Baud_rate = 9600;

$uart_msg_id = 'null';

$error_code = 'null';


$comm_mode = 'null';



$query_inter = "SELECT * FROM efactorproduct where node_id='$node_id'";
$doctors_inter = $db->get_results($query_inter); 
$row_inter = $db->get_row($query_inter);
$model_number=$row_inter['model_number'];
$devicename=$row_inter['device_name'];
$vendor_name=$row_inter['vendor_name'];
$moduletype=$row_inter['module_type'];
$net_conn_1=$row_inter['internet'];



$node_details = array(
    "id" => $node_id,
    
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
        "devices" =>  $devices_list,
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => "NULL",
            "type" => "WIFI"
        ),
        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => "true",
            "timestamp" => 1717898131809
        )
    )
);




$access_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4';







$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>1,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));


echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

//echo $node;

exit;



/************************************************************/

    }


}


/************************************************************/

//Output
echo json_encode($response);
//echo json_data;
exit;



?>
