<?php echo "efactprod "; ?> 
<?php 
if($user_type !== "User"){
$keys_to_remove = [
    "id", "device_type", "brandlogo", "product_image", "auto_testing", "model_number", "developer_name", "developer_id", "number_of_pages", 
    "page_names", "timestamp", "payload", "node_id", "module_type", "client_name", "client_id", 
    "user_name", "access_token", "request_id", "mac", "add_gateway", "is_added", 
    "secret_key", "req_status", "mob_device_token", "ipaddress", "current_devicetype", 
    "ping", "latitude", "longitude", "signin_latitude", "signin_longitude", "node_latitude", 
    "node_longitude", "phone", "email", "app_version", "fw_version", "add_node_time", 
    "ssid", "wifi_pwd", "auto_test", "selected", "SUID", "DUID", "UDP_CMD", "UDP_ACK", "MEDIA", 
    "Frame", "FID", "UserID", "SDUID_Enable", "UID_DATA"
];
}else{
$keys_to_remove = [
    "id", "device_type", "brandlogo", "product_image", "auto_testing", "model_number", "developer_name", "developer_id", "number_of_pages", 
    "page_names", "timestamp", "payload", "node_id", "module_type", "client_name", "client_id", 
    "user_name", "access_token", "request_id", "mac", "add_gateway", "is_added", 
    "secret_key", "req_status", "mob_device_token", "ipaddress", "current_devicetype", 
    "ping", "latitude", "longitude", "signin_latitude", "signin_longitude", "node_latitude", 
    "node_longitude", "phone", "email", "app_version", "fw_version", "add_node_time", 
    "ssid", "wifi_pwd", "auto_test", "TZ_properties", "BR_properties", "UMI_properties", "EC_properties", "CM_properties", 
    "TU_properties", "LC_properties", "display_properties", "selected", "SUID", "DUID", "UDP_CMD", "UDP_ACK", "MEDIA", 
    "Frame", "FID", "UserID", "SDUID_Enable", "UID_DATA"
];
 }
foreach ($keys_to_remove as $key) {
    unset($api_info[0][$key]);
}
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" >
    <title>API Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f1ff;
        }
        .api-info {
            background-color: #f0f1ff;
            padding: 20px;
            border-radius: 8px; 
            max-width: 600px;
            margin: 0 auto;
        }
        .api-info .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .api-info .row .key {
            font-weight: bold;
            color: #333;
            width: 150px;
        }
        .api-info .row .value {
            color: #555;  
        }
        .api-info .checkbox-row {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #ccc;
        }
        .api-info .checkbox-row .key {
            width: 50%;
        }
        .api-info .checkbox-row .value {
            width: 50%;
        }
        .api-info .submit-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .api-info .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<style>

.device-workspace{
    display:flex;
    gap:24px;
    align-items:flex-start;
    padding:20px;
}

.device-workspace-main{
    flex:1;
    min-width:0;
}

@media(max-width:991px){

    .device-workspace{
        display:block;
    }

}

</style>

<div class="device-workspace">

    <?= view('includes/device_sidebar', [
        'node_id' => $node_id ?? '',
        'model_number' => $model_number ?? '',
        'user_id' => $user_id ?? ''
    ]); ?>

    <div class="device-workspace-main">

        <?php if (empty($api_info)) { ?>
            <h3 style="text-align:center; margin-top:50px;">
                No API Information Available
            </h3>
        <?php } ?>

        <div class="api-info">

            <?php  
            if($user_type == "User"){

                if (!empty($api_info) && isset($api_info[0])) {

                    foreach ($api_info[0] as $key => $value) {

                        echo "<div class='row'>";
                        echo "<div class='key'>" . htmlspecialchars($key) . ":</div>";
                        echo "<div class='value'>" . htmlspecialchars($value) . "</div>";
                        echo "</div>";

                    }

                }

            } else {

                $property_keys = ["TZ_properties", "BR_properties", "UMI_properties", "EC_properties", "CM_properties", "TU_properties", "LC_properties", "display_properties"];

                $regular_values = [];  

                if (!empty($api_info) && isset($api_info[0])) {

                    foreach ($api_info[0] as $key => $value) {

                        if (in_array($key, $property_keys)) {
                            continue;
                        }

                        $regular_values[] = [
                            'key' => htmlspecialchars($key),
                            'value' => htmlspecialchars($value)
                        ];

                    }

                }

                foreach ($regular_values as $item) {

                    echo "<div class='row'>";
                    echo "<div class='key'>" . $item['key'] . ":</div>";
                    echo "<div class='value'>" . $item['value'] . "</div>";
                    echo "</div>";

                }

            }
            ?> 
        
            <?php if ($user_type != "User"): ?>

            <?php echo form_open_multipart(get_uri("items/efactprod"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>

            <form id="myForm" action="items.php" name="myForm" onsubmit="return validateForm()" method="post" enctype="multipart/form-data">

                <div class="checkbox-row">

                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                    <input type="hidden" name="node_id" value="<?php echo $node_id; ?>" />
                    <input type="hidden" name="model_number" value="<?php echo $model_number; ?>" />

                    <?php

                    $property_keys = ["TZ_properties", "BR_properties", "UMI_properties", "EC_properties", "CM_properties", "TU_properties", "LC_properties", "display_properties"];

                    if (!empty($api_info) && isset($api_info[0])) {

                        foreach ($api_info[0] as $key => $value) {

                            if (in_array($key, $property_keys)) {

                                echo "<div class='row'>";
                                echo "<div class='key'>" . htmlspecialchars($key) . ":</div>"; 

                                $is_checked = ($value == "read/write") ? "checked" : ""; 

                                echo "<div class='value'>
                                        <input type='checkbox' name='" . htmlspecialchars($key) . "' class='property-checkbox' value='read/write' $is_checked onclick='updateStatus(this, \"$key\")'>
                                        <input type='hidden' name='" . htmlspecialchars($key) . "' value='" . ($value == "read/write" ? "read/write" : "") . "'>
                                    </div>";

                                echo "</div>";

                            }

                        }

                    }

                    ?>

                </div>

                <button type="submit" class="submit-btn">Submit</button>

            </form>

            <?php echo form_close(); ?>

            <script> 

                function updateStatus(checkbox, key) { 

                    var hiddenInput = checkbox.parentElement.querySelector("input[type='hidden']");
                    hiddenInput.value = checkbox.checked ? "read/write" : "";

                }

            </script>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
