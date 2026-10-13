<?php
$curr_devicetype = "light_7E0000A4";
$new_prefix = "EWIFI";

// Split the value at the underscore
$parts = explode("_", $curr_devicetype, 2);

// Concatenate the new prefix with the part after the underscore
$updated_devicetype = $new_prefix . "_" . $parts[1];

echo $updated_devicetype; // Outputs: EWIFI_7E0000A4
?>