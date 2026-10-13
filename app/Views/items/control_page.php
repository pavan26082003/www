<?php 
// 1. INITIAL DATA
$data = json_decode($response, true);
echo "<pre>";
print_r($data);
exit;
$data2 = json_decode($jsonResponse, true);

// 2. GET UNIQUE PARAMETERS
$inf_id      = trim($_GET['node_id'] ?? 'N/A'); 
$model_name  = $components['additionalData']['modelNumber'] ?? '';
$user_id     = $components['additionalData']['userID'] ?? '';

// 3. DEFAULT TEXT VALUES
$inf_name = 'Device';
$inf_type = 'WIFI';
$inf_ssid = 'Not Connected';
$inf_fw   = 'N/A';

/**
 * 4. DYNAMIC URL CONSTRUCTION
 */
$inf_guarant = "https://efactor.tech/v8/api.php?action=guarantee_card_display&model_name=" . urlencode($model_name) . "&user_id=" . urlencode($user_id);
$inf_video   = "https://efactor.tech/v8/user_guide_support.php";
$inf_help    = "https://efactor.tech/v8/call_services.php?node_id=" . urlencode($inf_id);
$inf_track   = "https://efactor.tech/v8/auto_test.php?model_name=" . urlencode($model_name) . "&user_id=" . urlencode($user_id) . "&node_id=" . urlencode($inf_id);

/**
 * 5. SEARCH LOGIC
 */
if (isset($data['node_details']) && is_array($data['node_details'])) {
    foreach ($data['node_details'] as $node) {
        
        $current_id = trim($node['node_id'] ?? $node['id'] ?? '');

        if (!empty($inf_id) && !empty($current_id) && (stripos($inf_id, $current_id) !== false || stripos($current_id, $inf_id) !== false)) {
            
            $inf_name = $node['info']['name'] ?? 
                        ($node['config']['devices'][0]['info']['name'] ?? 
                        ($node['config']['devices'][0]['name'] ?? 'Smart Fan'));

            $info = $node['config']['devices'][0]['info'] ?? ($node['info'] ?? null);

            if ($info) {
                $inf_type = $info['type'] ?? 'WIFI';
                $inf_ssid = $info['SSID'] ?? 'Not Connected';
                $inf_fw   = $info['fw_version'] ?? 'N/A';
                
                if (!empty($info['Guarantee_Card'])) $inf_guarant = $info['Guarantee_Card'];
                if (!empty($info['Video']))          $inf_video   = $info['Video'];
                if (!empty($info['Need_Help']))      $inf_help    = $info['Need_Help'];
                if (!empty($info['Auto_Test']))      $inf_track   = $info['Auto_Test'];
            }
            break; 
        }
    }
}

// ===============================
// STATUS SECTION (unchanged variables)
// ===============================

$additionalData = $components['additionalData'];

$node_id = $additionalData['node_id'];
$modelNumber = $additionalData['modelNumber'];
$pageNo = $additionalData['pageNo'];
$userID = $additionalData['userID'];

$statusDisplayData = '';
$deviceStatus = 'OFFLINE';

if ($pageNo === 'Status') {
    try {
        $apiUrl = "https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id=$node_id&device_id=$node_id";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        
        $response_get = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('API request failed: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("API returned HTTP status $httpCode");
        }
        
        $data_get = json_decode($response_get, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        
        $deviceDataForConsole = $data_get;

        if (isset($data_get['connected']) && $data_get['connected'] === 'true') {
            $deviceStatus = 'ONLINE';
            
            foreach ($data_get as $key => $value) {
                if (is_array($value) && isset($value['Status'])) {

                    $statusData = $value['Status'];
                    $statusDisplayData = "Device Status: ONLINE<br>";

                    foreach ($statusData as $param => $val) {
                        if ($param !== 'name') {
                            $statusDisplayData .= ucfirst(str_replace('_', ' ', $param)) . ": " . htmlspecialchars($val) . "<br>";
                        }
                    }
                    break;
                }
            }
        }
        
    } catch (Exception $e) {
        $deviceDataForConsole = [];
        $statusDisplayData = "Error fetching status: " . $e->getMessage();
    }
}

/**
 *  AJAX POLLING ENDPOINT
 *  (NO VARIABLE CHANGES)
 */
if (isset($_GET['poll'])) {
    echo json_encode([
        "deviceStatus"   => $deviceStatus,
        "statusDisplayData" => $statusDisplayData
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device UI Components</title>
    <!-- Material CSS & other stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/11.0.0/material-components-web.min.css">
    <link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">
    <style>
      
:root {
    /* MODERATED & FABULOUS TEAL */
    --mdc-theme-primary: #0d9488;   /* Balanced Slate Teal */
    --mdc-theme-secondary: #0f766e; /* Rich Dark Teal */
    --body-bg: #f1f5f9;             /* Cool light-grey background */
    --card-background: #ffffff;
    --header-height: 48px;          /* Tight header height */
}

body {
    background-color:var(--body-bg);
    font-family: 'Segoe UI', system-ui, sans-serif;
    margin: 0;
    /* This fixes the heavy top space gap */
    padding-top: 0px; 
    color: #000000;
}

/* EDIT TEXT STYLING */
.control-edit, .mdc-card span[onclick] {
    color: var(--mdc-theme-primary) !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    font-size: 0.85rem;
    cursor: pointer;
}



header, .app-bar { 
    height: var(--header-height);
    background-color: var(--mdc-theme-primary);
    color: white;
    display: flex;
    align-items: center;
    padding: 0 16px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
}

.main-content {
    padding-top: 60px !important; /* This keeps it below the green header */
    padding-bottom: 10px;
    max-width: 450px !important; /* Changed from 400px to look better */
    
    /* THE FIX FOR CENTERING: */
    margin-left: auto !important;
    margin-right: auto !important;
    
    /* Remove the individual paddings that cause the 'right-side' lean */
    padding-left: 0px !important; 
    padding-right: 0px !important;
    
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* --- DEVICE ID: ROUNDED ALL AROUND --- */
.device-info {
    background: #e1f5fe;
    color: #01579b;
    padding: 10px;
    /* This now applies 8px to top-left, top-right, bottom-right, and bottom-left */
    border-radius: 8px; 
    text-align: center;
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 0; /* Keeps it touching the card */
    border: 1px solid #b3e5fc; /* Restored the full border so you see the bottom curve */
}

/* --- CARDS: NARROW & COMPACT --- */
.mdc-card {
    background: var(--card-background);
    border-radius: 8px;
    padding: 10px 10px;
    
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #d1d9e0;
}

/* Connects the first card directly to the Device ID box */
.device-info + .mdc-card {
    border-radius: 0 0 8px 8px; /* Flat top, rounded bottom */
}

/* --- TIGHTER INPUTS --- */
.mode-selection-wrapper {
    background: #f8fafc;
    padding: 6px 10px;
    border-radius: 4px;
    margin-bottom: 8px;
    display: flex;
    justify-content: center;
    gap: 15px;
    font-size: 0.85rem;
}

.mdc-text-field--outlined {
    width: 100%;
    height: 34px;
    margin-top: 2px;
}

/* --- SLIM BUTTONS --- */

/* 1. Base styles for all buttons */
.btn-send-data, .mdc-button {
    height: 35px !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    font-size: 0.8rem !important;
    text-transform: uppercase;
    
}

/* 2. Specific style for Send Data (50% width and centered) */
.btn-send-data {
    width: 50% !important;
    display: block !important; /* Ensures it respects margins */
    margin-left: auto !important; /* Centers the button */
    margin-right: auto !important; /* Centers the button */
}

/* 3. The other buttons remain full width */
.mdc-button:not(.btn-send-data) {
    width: 100% !important;
}


/* --- COMPACT CONSOLE --- */
.console-window {
    border-radius: 8px;
    margin-top: 4px;
    border: 1px solid #37474f;
}

.console-header {
    background-color: #263238;
    color: #80d8ff;
    padding: 6px 12px;
    font-size: 0.75rem;
}

.console-content {
    background-color: #1c262b;
    color: #cfd8dc;
    height: 120px; /* Shortened to save vertical space */
    padding: 8px;
    font-size: 0.75rem;
    font-family: 'Consolas', monospace;
}

/* --- STATUS DISPLAY STYLES --- */
.status-display-container {
    background-color: #f5f5f5;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
    border: 1px solid #e0e0e0;
}

.status-display-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-display-content {
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 13px;
    line-height: 1.6;
    white-space: pre-wrap;
    word-break: break-word;
    color: #424242;
    background-color: white;
    padding: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    min-height: 100px;
    overflow-y: auto;
}

.status-online {
    color: #4CAF50;
    font-weight: bold;
}

.status-offline {
    color: #f44336;
    font-weight: bold;
}

/* Status display parameter styling */
.status-param {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px solid #f0f0f0;
}

.status-param:last-child {
    border-bottom: none;
}

.param-name {
    color: #616161;
    font-weight: 500;
}

.param-value {
    color: #212121;
    font-weight: 600;
}



/* --- COMPACT GRID OVERRIDES --- */

/* You likely don't find this in your file because it's a default MDC class. 
   Adding it here will force the vertical gap between cards to shrink. */
.mdc-layout-grid__inner {
    grid-gap: 4px !important; /* Forces vertical space between cards to 4px */
    display: grid !important;
}

/* Removes the extra padding that MDC cells usually have */
.mdc-layout-grid__cell {
    margin-bottom: 0 !important;
    padding: 0 !important;
}

/* This makes the white boxes (cards) sit closer to each other */
/* --- Add this right after your Responsive Column code --- */

.mdc-card {
    width: 100% !important;
    min-height: 52px !important; /* Thumb-friendly size for mobile */
    background-color: var(--card-background) !important;
    border-radius: 12px !important; /* Matches modern smart home app styles */
    box-shadow: 0 2px 5px rgba(0,0,0,0.08) !important; /* Adds subtle depth */
    border: 1px solid #e0e0e0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    padding: 10px 16px !important;
    box-sizing: border-box !important;
    margin-bottom: 5px !important; /* Controls spacing between cards in the column */
}




/* --- YOUR ORIGINAL CODE WITH UPGRADES --- */

/* 1. Added: Centering and width limit for Laptops */
.mdc-layout-grid__inner {
    display: flex !important;
    flex-direction: column !important;
    grid-gap: 8px !important; 
    width: 100% !important;
    max-width: 450px !important; 
    margin: 0 auto !important; /* This centers the box */
    padding: 0 !important;     /* Set this to 0 to stop the right-side push */
}

/* 2. Your Cell Logic - Unchanged */
.mdc-layout-grid__cell, 
.mdc-layout-grid__cell--span-4, 
.mdc-layout-grid__cell--span-12 {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 0 5px 0 !important;
    padding: 0 !important;
}

/* 3. NEW: Sticky Header Rule */
.sticky-header {
    position: -webkit-sticky; 
    position: sticky;
    top: 0;
    z-index: 100;
    background-color: var(--body-bg); /* Prevents overlapping cards from showing through */
    padding-bottom: 5px;
}

/* 4. Your Card Logic - Unchanged */
.mdc-card {
    width: 100% !important;
    box-sizing: border-box !important;
}

/* --- REPLACE SECTION 5 --- */
@media screen and (max-width: 480px) {
    .main-content {
        max-width: 100% !important;
        /* Equal padding (12px on BOTH sides) keeps it centered */
        padding-left: 12px !important; 
        padding-right: 12px !important;
        /* Keeps content below your green header */
        padding-top: 80px !important; 
    }
    
    .mdc-layout-grid__inner {
        max-width: 100% !important;
        margin: 0 auto !important;
    }
}


.mdc-top-app-bar {
    background-color: #0f766e !important; /* Your Moderate Teal */
}

.mdc-top-app-bar__navigation-icon {
    color: white !important;
}




/* 1. CENTER EVERYTHING IN THE ANCHOR */
.mdc-select__anchor {
    display: flex !important;
    height: 32px !important;      /* Moderate height so it doesn't disappear */
    background: transparent !important;
    align-items: center !important; /* Forces vertical centering */
    justify-content: flex-end !important; /* Keeps text and icon on the right */
    border: none !important; 
width:150px;   
padding-left:20px;  /* Removes grey border lines */
}

/* 2. FIX TEXT ALIGNMENT */
.mdc-select__selected-text {
    font-size: 16px !important;   /* Balanced size */
    line-height: normal !important; /* Resetting this prevents it from sinking */
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important; /* Centers text internally */
    margin-right: 10px !important;  /* Pulls icon closer */
}

/* 3. CENTER THE ICON */
.mdc-select__dropdown-icon {
    width: 18px !important;
    height: 100% !important;      /* Takes full height of anchor to stay centered */
    margin: 0 !important;
    display: flex !important;
    align-items: center !important; /* Vertical center for the arrow */
}

/* 4. SLIM THE EXTERNAL BOX */
.dropdown-container {
    min-height: 40px !important;  /* Reduces the 'heavy' card look */
    padding: 4px 12px !important; /* Tightens the overall card space */
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* This hides the scrollbar visually but keeps the auto-scrolling working */
.console-content::-webkit-scrollbar {
    display: hidden;
}



/* 1. Target both html and body for all screen sizes */
    html, body {
        scrollbar-width: none !important;     /* Firefox */
        -ms-overflow-style: none !important;  /* IE and Edge */
        overflow-y: overlay;                  /* Chrome/Safari trick */
    }

    /* 2. Hide scrollbar for Chrome, Safari and Opera */
    html::-webkit-scrollbar, 
    body::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
    }

    /* 3. Force hide on Mobile/Phone screens */
    @media only screen and (max-width: 600px) {
        body {
            overflow-y: scroll !important; /* Keep scrolling active */
        }
        body::-webkit-scrollbar {
            display: none !important;
        }
    }


    </style>
</head>
<body>

  <div class="mdc-drawer-app-content">
   <header class="mdc-top-app-bar" style="background-color: #4caf50;">
    <div class="mdc-top-app-bar__row" style=" height: 50px;">
        
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <a href="javascript:history.back()" style="text-decoration: none; color: white; display: flex; align-items: center;">
                <span class="material-icons mdc-top-app-bar__navigation-icon">arrow_back</span>
            </a>
            <span class="mdc-top-app-bar__title">Efactor SmartThings</span>
        </section>

       
<section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end">
  <button class="mdc-icon-button material-icons" 
    onclick="openDetails(
        '<?php echo $inf_id; ?>',
        '<?php echo $inf_name; ?>',
        '<?php echo $inf_type; ?>',
        '<?php echo $inf_ssid; ?>',
        '<?php echo $inf_fw; ?>',
        '<?php echo $inf_guarant; ?>', 
        '<?php echo $inf_video; ?>', 
        '<?php echo $inf_help; ?>', 
        '<?php echo $inf_track; ?>'
    )"
    style="color: white; font-size: 30px; border: none; background: none; cursor: pointer;">
    info
</button>

</section>

        
    </div>
</header>
    
    <!-- Main content starts here -->
 <div class="main-content" style="background-color: #f8f9fa; min-height: 100vh;">


    
  <?php 
    $target_node_id = '';
    $page = '1';
    foreach ($components as $comp) {
        $target_node_id = $comp['node_id'];
        $page = $comp['pageNo'];
    }
    ?>



<div class="mdc-layout-grid" style="padding-top: 5px;"> 
        <div class="mdc-layout-grid__inner">
            
            <?php foreach ($data['node_details'] as $node): ?>
                <?php if (isset($target_node_id) && $target_node_id === $node['id']): ?>
                    
                    <?php 
                    $device = $node['config']['devices'][0];
                    $fanName = $device['name'] ?? 'Device';
                    $longId = $node['id'] ?? '';
                    $brandLogo = $device['brand_logo'] ?? '';
                    $deviceImg = $device['device_image'] ?? '';
                    $isConnected = $node['status']['connectivity']['connected'] ?? false;

        ?>

     <div style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #ffffff; border-radius: 8px 8px 0 0 ; box-shadow: 0 1px 5px rgba(0,0,0,0.05); margin-bottom: 10px; box-sizing: border-box; overflow: hidden;">
    
    <div style="flex: 0 0 50px; display: flex; align-items: center; justify-content: flex-start;">
        <?php if ($brandLogo): ?>
            <img src="<?php echo $brandLogo; ?>" style="max-width: 40px; height: auto; object-fit: contain;">
        <?php endif; ?>
    </div>

    <div style="flex: 1; text-align: center; min-width: 0; padding: 0 4px;">
        <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
            <h2 style="margin: 0; font-size: 14px; font-weight: 700; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?php echo htmlspecialchars($fanName); ?>
            </h2>
            <div style="flex-shrink: 0; width: 6px; height: 6px; border-radius: 50%; background-color: <?php echo $isConnected ? '#4CAF50' : '#F44336'; ?>;"></div>
        </div>
        
        <div style="font-size: 9px; color: #999; font-family: monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
            <?php echo htmlspecialchars($longId); ?>
        </div>
        
        <div style="font-size: 8px; color: #666; font-weight: 500; opacity: 0.7; white-space: nowrap;">
            <?php echo htmlspecialchars($page); ?> 
        </div>
    </div>

    <div style="flex: 0 0 50px; display: flex; align-items: center; justify-content: flex-end; padding-right: 2px;">
        <?php if ($deviceImg): ?>
            <img src="<?php echo $deviceImg; ?>" style="width: 35px; height: 35px; object-fit: contain;">
        <?php endif; ?>

    </div>
</div>





 <?php 
                        $pages_array = $node['config']['devices'][0]['pages'];
                        $params = []; 
                        ?>
                        
                        <?php foreach ($pages_array as $pageKey => $pageData): ?>
                            <?php if ($page === $pageKey): ?>
                                <?php foreach ($pageData as $frame): ?>
                                    <?php foreach ($frame['frame']['commands'] as $component): ?>
                                        <?php if ($component['Field_description'] === "Data" && $component['ui_type'] !== "None"): ?>
                                            <?php
                                            $cmd_names = explode(',', $component['Field_description']);
                                            $ui_types = explode(',', $component['ui_type']);
                                            $ui_permissions = explode(',', $component['ui_description']);
                                            $ui_datatypes = explode(',', $component['data_type']);
                                            
                                            $command_to_find = "Data";
                                            $position = array_search($command_to_find, $cmd_names);
                                            
                                            $ui_type = '';
                                            $ui_permission = '';
                                            $default_name = '';
                                            $parameter_id = '';
                                            $slider_step = '';
                                            $range_min = '';
                                            $range_max = '';
                                            
                                            if ($position !== false) {
                                                $ui_type = $ui_types[$position] ?? '';
                                                $default_name = $component['default_name'] ?? '';
                                                $parameter_id = $component['parameter_id'] ?? '';
                                                $slider_step = $component['cmd_sliderstepvalue'] ?? '';
                                                $range_min = $component['display_value']['min'] ?? '';
                                                $range_max = $component['display_value']['max'] ?? '';
                                            }
                                            
                                            if($data2['url'] == 'action=auto_get_nodes_params'){
                                                $params = [];
                                                if (isset($data2['node_id']) && $data2['node_id'] === $node2) {
                                                    foreach ($data2 as $key => $value) {
                                                        if (is_array($value) && isset($value[$page])) {
                                                            $params = $value[$page];
                                                            break;  
                                                        }
                                                    }
                                                }
                                            } else {
                                                foreach ($data['node_details'] as $node) {
                                                    $pages_array = $node['config']['devices'][0]['pages'];
                                                    $params = [];
                                                    foreach ($pages_array as $page) {
                                                        foreach ($page as $frame) {
                                                            foreach ($frame['frame']['commands'] as $command) {
                                                                if ($command['Field_description'] === "Data") {
                                                                    if ($command['ui_type'] === "Drop Down") {
                                                                        $params[$command['default_name']] = $command['value'] ?? null;
                                                                    } elseif ($command['ui_type'] === "Toggle Switch") {
                                                                        $params[$command['default_name']] = $command['value'] ?? null;
                                                                    } else {
                                                                        $params[$command['default_name']] = $command['asperui_value'] ?? null;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    break;
                                                }
                                            }
                                            ?>


                                            
                                            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                                                <div class="mdc-card control-section">
                                                    <!-- Toggle Switch -->
                                                    <?php if ($ui_type === 'Toggle Switch'): ?>
                                                     

   <?php
                                                        $ui_dropdown = $component['asperui'];  
                                                        $ui_dropdown_value = $component['asperui_values'];  
                                                        $dropdown_map = array_combine($ui_dropdown_value,$ui_dropdown);
                                                        
                                                        $value2 = $params[$component['default_name']] ?? '';
                                                        $value3 = empty($value2) ? "00" : $value2;
                                                        $value = $dropdown_map[$value3] ?? ' ';
                                                        $powerValue = ($value === 'true') ? 'checked' : '';
                                                        ?>
                                                      




<div class="toggle-wrapper" style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 2px; box-sizing: border-box;">
    
    <div style="display: flex; flex-direction: column; gap: 2px; padding-left: 0;">
        <label class="toggle-label" for="<?= esc($component['default_name']); ?>" style="color: #9e9e9e; font-size: 13px; font-weight: 400; margin: 0; padding: 0; text-transform: capitalize; line-height: 1.2;">
            <?= esc($component['default_name']); ?>
        </label>
        
        <span id="switchValueDisplay-<?= esc($component['default_name']); ?>" 
              class="status-indicator <?= $powerValue ? 'on' : 'off'; ?>" 
              style="color: #333; font-size: 15px; font-weight: 400; text-align: left; margin: 0; padding: 0; line-height: 1.2;">
            <?= $powerValue ? 'ON' : 'OFF'; ?>
        </span>
    </div>

    <div class="mdc-switch" data-switch-id="<?= esc($component['default_name']); ?>" style="margin-right: 4px;">
        <div class="mdc-switch__track"></div>
        <div class="mdc-switch__thumb-underlay">
            <div class="mdc-switch__thumb">
                <input type="checkbox" id="<?= esc($component['default_name']);?>" 
                       class="mdc-switch__native-control" role="switch" 
                       <?= $powerValue ? 'checked' : ''; ?>>
            </div>
        </div>
    </div>
</div>
                                                    
                                                    <!-- Slider -->
                                                    <?php elseif ($ui_type === 'Slider' || $ui_type === 'Brightness Slider' || $ui_type === 'CCT Slider' || $ui_type === 'Saturation Slider'): ?>
                                                        <?php 
                                                        $ui_step_value = explode(',', $component['slider_step_value']);
                                                        $ui_range_min_value = explode(',', $component['display_value']['min']);
                                                        $ui_range_max_value = explode(',', $component['display_value']['max']);
                                                        
                                                        $ui_step = $ui_step_value[$position] ?? '';
                                                        $ui_range_min = $ui_range_min_value[$position] ?? '';
                                                        $ui_range_max = $ui_range_max_value[$position] ?? '';
                                                        $ui_cmd_value = $ui_range_min;
                                                        
                                                        $value = $params[$component['default_name']] ?? '';
                                                        ?>
                                                        <div class="slider-container">
                                                            <div class="slider-header">


                                                         

       <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                    <?= esc($component['default_name']); ?>
                                                                </label>



                                                                <span id="sliderValueDisplay-<?= esc($component['default_name']); ?>" class="slider-value">
                                                                    <?= esc($value); ?>
                                                                </span>
                                                            </div>
                                                            <div class="mdc-slider" id="slider-<?= esc($component['default_name']); ?>">
                                                                <input class="mdc-slider__input" type="range" 
                                                                       min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
                                                                       value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
                                                                       aria-labelledby="label-<?= esc($component['default_name']); ?>"
                                                                       oninput="updateSliderValue(this.value,'<?= esc($component['default_name']); ?>')">
                                                                <div class="mdc-slider__track">
                                                                    <div class="mdc-slider__track--inactive"></div>
                                                                    <div class="mdc-slider__track--active">
                                                                        <div class="mdc-slider__track--active_fill"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="mdc-slider__thumb">
                                                                    <div class="mdc-slider__thumb-knob"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    
                                                    <!-- Airflow Slider -->
                                                    <?php elseif ($ui_type === 'Airflow Slider'): ?>
                                                        <?php 
                                                        $ui_step_value = explode(',', $component['slider_step_value']);
                                                        $ui_range_min_value = explode(',', $component['display_value']['min']);
                                                        $ui_range_max_value = explode(',', $component['display_value']['max']);
                                                        
                                                        $ui_step = $ui_step_value[$position] ?? '';
                                                        $ui_range_min = $ui_range_min_value[$position] ?? '';
                                                        $ui_range_max = $ui_range_max_value[$position] ?? '';
                                                        $ui_cmd_value = $ui_range_min;
                                                        
                                                        $value = $params[$component['default_name']] ?? '';
                                                        ?>
                                                        <div class="slider-container">
                                                            <div class="slider-header">
                                                                <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                    <?= esc($component['default_name']); ?>
                                                                </label>
                                                                <span id="sliderValueDisplay-<?= esc($component['default_name']); ?>" class="slider-value">
                                                                    <?= esc($value); ?>
                                                                </span>
                                                            </div>
                                                            <div class="fan-speed-icons">
                                                                <img class="fan-speed-icon" src="https://efactor.tech/factor_17-05/files/home/lowspd.png" alt="Low Speed">
                                                                <div class="mdc-slider" id="slider-<?= esc($component['default_name']); ?>" style="flex-grow: 1; margin: 0 16px;">
                                                                    <input class="mdc-slider__input" type="range" 
                                                                           min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
                                                                           value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
                                                                           aria-labelledby="label-<?= esc($component['default_name']); ?>"
                                                                           oninput="updateSliderValue(this.value,'<?= esc($component['default_name']); ?>')">
                                                                    <div class="mdc-slider__track">
                                                                        <div class="mdc-slider__track--inactive"></div>
                                                                        <div class="mdc-slider__track--active">
                                                                            <div class="mdc-slider__track--active_fill"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mdc-slider__thumb">
                                                                        <div class="mdc-slider__thumb-knob"></div>
                                                                    </div>
                                                                </div>
                                                                <img class="fan-speed-icon" src="http://localhost/factor_17-05/files/home/highspd.png" alt="High Speed">
                                                            </div>
                                                        </div>



                                                    
                                               <!-- Drop Down -->
                                                    <?php elseif ($ui_type === 'Drop Down' && $component['default_name'] !== 'Recipe' && $component['default_name'] !== 'direction'): ?>
                                                        <?php
                                                        $ui_dropdown = $component['asperui'];  
                                                        $ui_dropdown_value = $component['asperui_values'];  
                                                        $dropdown_map = array_combine($ui_dropdown_value,$ui_dropdown);
                                                        
                                                        $value2 = $params[$component['default_name']] ?? '';
                                                        $value3 = $value2; 
                                                        $value = $dropdown_map[$value3] ?? ' ';
                                                        ?>
                                                        <div class="dropdown-container">
                                                            <div class="label-container">
                                                                <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                    <?= esc($component['default_name']); ?>
                                                                </label>
                                                            </div>
                                                            <div class="mdc-select mdc-select--filled" id="select-<?= esc($component['default_name']); ?>">
                                                                <div class="mdc-select__anchor" role="button" aria-haspopup="listbox" aria-expanded="false">
                                                                    <span class="mdc-select__ripple"></span>
                                                                    <span class="mdc-select__selected-text"><?= esc($value); ?></span>
                                                                    <span class="mdc-select__dropdown-icon">
                                                                        <svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5" focusable="false">
                                                                            <polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>
                                                                            <polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>
                                                                        </svg>
                                                                    </span>
                                                                </div>
                                                                <div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
                                                                    <ul class="mdc-list" role="listbox">
                                                                        <?php foreach ($dropdown_map as $option): ?>
                                                                            <?php $isSelected = ($option === $value); ?>
                                                                            <li class="mdc-list-item <?= $isSelected ? 'mdc-list-item--selected' : ''; ?>" 
                                                                                role="option" aria-selected="<?= $isSelected ? 'true' : 'false'; ?>" 
                                                                                data-value="<?= esc($option); ?>">
                                                                                <span class="mdc-list-item__ripple"></span>
                                                                                <span class="mdc-list-item__text"><?= esc($option); ?></span>
                                                                            </li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    
                                                    <!-- Recipe Drop Down -->
                                                    <?php elseif ($ui_type === 'Drop Down' && $component['default_name'] === 'Recipe'): ?>
                                                        <?php
                                                        $ui_dropdown = $component['asperui'];  
                                                        $asperui_values = $component['asperui_values'];  
                                                        $third_bytes = [];
                                                        
                                                        foreach ($asperui_values as $val) {
                                                            $third_byte = substr($val, 4, 2);
                                                            $third_bytes[] = $third_byte;
                                                        }
                                                        
                                                        $ui_dropdown_value = $third_bytes;
                                                        $dropdown_map = array_combine($ui_dropdown_value,$ui_dropdown);
                                                        
                                                        $value2 = $params[$component['default_name']] ?? '';
                                                        
                                                        if (is_numeric($value2)) {
                                                            $value22 = substr($value2, 4, 2);
                                                            $value3 = $value22; 
                                                            $value = $dropdown_map[$value3] ?? ' ';
                                                        } else {
                                                            $value = $value2;
                                                        }
                                                        ?>
                                                        <div class="dropdown-container">
                                                            <div class="label-container">
                                                                <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                    <?= esc($component['default_name']); ?>
                                                                </label>
                                                            </div>
                                                            <div class="mdc-select mdc-select--filled" id="select-<?= esc($component['default_name']); ?>">
                                                                <div class="mdc-select__anchor" role="button" aria-haspopup="listbox" aria-expanded="false">
                                                                    <span class="mdc-select__ripple"></span>
                                                                    <span class="mdc-select__selected-text"><?= esc($value); ?></span>
                                                                    <span class="mdc-select__dropdown-icon">
                                                                        <svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5" focusable="false">
                                                                            <polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>
                                                                            <polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>
                                                                        </svg>
                                                                    </span>
                                                                </div>
                                                                <div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
                                                                    <ul class="mdc-list" role="listbox">
                                                                        <?php foreach ($dropdown_map as $option): ?>
                                                                            <?php $isSelected = ($option === $value); ?>
                                                                            <li class="mdc-list-item <?= $isSelected ? 'mdc-list-item--selected' : ''; ?>" 
                                                                                role="option" aria-selected="<?= $isSelected ? 'true' : 'false'; ?>" 
                                                                                data-value="<?= esc($option); ?>">
                                                                                <span class="mdc-list-item__ripple"></span>
                                                                                <span class="mdc-list-item__text"><?= esc($option); ?></span>
                                                                            </li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    
                                                    <!-- Hue Slider -->
                                                    <?php elseif ($ui_type === 'Hue Slider Custom' || $ui_type === 'Hue Slider'): ?>
                                                        <?php 
                                                        $ui_step_value = explode(',', $component['slider_step_value']);
                                                        $ui_range_min_value = explode(',', $component['display_value']['min']);
                                                        $ui_range_max_value = explode(',', $component['display_value']['max']);
                                                        
                                                        $ui_step = $ui_step_value[$position] ?? '';
                                                        $ui_range_min = $ui_range_min_value[$position] ?? '';
                                                        $ui_range_max = $ui_range_max_value[$position] ?? '';
                                                        $value = $params[$component['default_name']] ?? $ui_range_min;
                                                        $value = max($ui_range_min, min($ui_range_max, $value));
                                                        ?>
                                                        <div class="color-palette-container">
                                                            <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                <?= esc($component['default_name']); ?>
                                                            </label>
                                                            <div class="colorPaletteBar" 
                                                                 data-min="<?= esc($ui_range_min); ?>" 
                                                                 data-max="<?= esc($ui_range_max); ?>" 
                                                                 data-value="<?= esc($value); ?>">
                                                                <div class="selector" data-slider-id="<?= esc($component['default_name']); ?>"></div>
                                                            </div>
                                                        </div>
                                                    
                                                    <!-- Display -->
                                                    <?php elseif ($ui_type === 'display'): ?>
                                                        <div class="text-input-container">
                                                            <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                <?= esc($component['default_name']); ?>
                                                            </label>
                                                            <div class="mdc-text-field mdc-text-field--filled <?= $ui_permission == 'Read' ? 'mdc-text-field--disabled' : ''; ?>">
                                                                <span class="mdc-text-field__ripple"></span>
                                                                <input class="mdc-text-field__input display-input" 
                                                                       type="text" 
                                                                       id="<?= esc($component['default_name']); ?>" 
                                                                       aria-labelledby="<?= esc($component['default_name']); ?>-label" 
                                                                       <?= $ui_permission != 'Write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?> 
                                                                       value="<?= esc($ui_cmd_value); ?>">
                                                            </div>
                                                        </div>
                                                    
                                                    <!-- Text -->
                                    <?php elseif ($ui_type === 'Text'): ?>
    <?php 
        $displayName = trim($component['default_name']);
        $currentValue = isset($params[$displayName]) ? $params[$displayName] : $component['asperui_value'];
        
        // ONLY apply the new card style if the name is "name"
        if (strtolower($displayName) === 'name'): 
    ?>
       <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; height: 30px; padding: 0 2px; background: #ffffff; border-radius: 10px; margin-bottom: 5px;  box-sizing: border-box;">
            <div style="display: flex; flex-direction: column; justify-content: center; padding-left: 0px;">
                <span style="font-size: 15px; color: #999; text-transform: lowercase; margin-bottom: -2px;">name</span>
                <span style="font-size: 15px; color: #666; font-weight: 500; line-height: 1.2;">
                    <?= esc($currentValue); ?>
                </span>
            </div>


           <div style="padding-right: 4px;">
        <span style="color: #2196F3; font-size: 13px; font-weight: 600; cursor: pointer;" 
              onclick="handleRename('<?= esc($currentValue); ?>')">
            Edit
        </span>
    </div>
</div>
    <?php else: ?>
     <div class="text-input-container">
    <label class="control-title"><?= esc($component['default_name']); ?></label>
    <div id="<?= esc($component['default_name']); ?>" class="text-display">
        <?= esc($ui_cmd_value); ?>
    </div>
</div>
    <?php endif; ?>




<!-- Enter Text -->  
                                      <?php elseif ($ui_type === 'Enter Text'): ?>
    <div class="text-input-container">
        <div class="mode-selection-wrapper">
            <div class="mdc-form-field">
                <div class="mdc-radio">
                    <input class="mdc-radio__native-control" type="radio" id="hexa-mode" name="input-mode" value="Hexa" checked>
                    <div class="mdc-radio__background">
                        <div class="mdc-radio__outer-circle"></div>
                        <div class="mdc-radio__inner-circle"></div>
                    </div>
                </div>
                <label for="hexa-mode" class="mode-label hexa">Hexa</label>

                <div class="mdc-radio">
                    <input class="mdc-radio__native-control" type="radio" id="text-mode" name="input-mode" value="Text">
                    <div class="mdc-radio__background">
                        <div class="mdc-radio__outer-circle"></div>
                        <div class="mdc-radio__inner-circle"></div>
                    </div>
                </div>
                <label for="text-mode" class="mode-label">Text</label>
            </div>
        </div>
        
        <div class="mdc-text-field mdc-text-field--outlined text-field-fullwidth">
            <input type="text" 
                   class="mdc-text-field__input" 
                   id="<?= esc($component['default_name']); ?>" 
                   value="<?= isset($params[$component['default_name']]) ? esc($params[$component['default_name']]) : '00'; ?>"
                   <?= $component['ui_description'] === 'Read' ? 'disabled' : ''; ?>>
            <div class="mdc-notched-outline">
                <div class="mdc-notched-outline__leading"></div>
                <div class="mdc-notched-outline__notch">
                    <label for="<?= esc($component['default_name']); ?>" class="mdc-floating-label">Input</label>
                </div>
                <div class="mdc-notched-outline__trailing"></div>
            </div>
        </div>

        <div class="button-container" style="margin-top: 15px; text-align: center;">
            <button type="button" class="mdc-button mdc-button--raised dynamic-action-button btn-send-data">
                <span class="mdc-button__label">SEND DATA</span>
            </button>
        </div>
    </div>


                          <?php elseif ($ui_type === 'console'): ?>
    <div class="console-container">
        <label class="control-title" style="font-family: 'Segoe UI', sans-serif; font-weight: 600; color: #333; margin-bottom: 4px; display: block;" for="<?= esc($component['default_name']); ?>">
            <?= esc($component['default_name']); ?>
        </label>
        
        <div class="mdc-card console-window" style="background-color: #1a1d21; border: 1px solid #333; overflow: hidden; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
            
            <div class="console-header" style="background-color: #1a1d21; border-bottom: 1px solid #333; padding: 6px 12px; display: flex; justify-content: space-between; align-items: center;">
                <span class="console-title" style="font-family: 'Segoe UI', sans-serif; font-size: 15px; color: #0d9488; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                    Console Output
                </span>
                <button class="mdc-icon-button clear-console" style="color: #666; width: 28px; height: 28px; padding: 0;">
                    <i class="material-icons" style="font-size: 18px;">clear</i>
                </button>
            </div>

            <div class="console-content" id="console-<?= esc($component['default_name']); ?>" 
                 style="background-color: #1a1d21; padding: 10px 12px; height: 180px; max-height: 200px; overflow-y: auto; scrollbar-width: thin;">
                
                <?php if (isset($deviceDataForConsole) && !empty($deviceDataForConsole)): ?>
                    <?php 
                        $isConnected = isset($deviceDataForConsole['connected']) && $deviceDataForConsole['connected'] === 'true';
                        $accentColor = $isConnected ? '#00e676' : '#ff5252';
                    ?>
                    <div style="display: flex; align-items: center; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="height: 8px; width: 8px; background-color: <?= $accentColor ?>; border-radius: 50%; display: inline-block; margin-right: 8px; box-shadow: 0 0 5px <?= $accentColor ?>;"></span>
                        <span style="color: <?= $accentColor ?>; font-weight: bold; font-size: 12px; font-family: 'Segoe UI', sans-serif;">
                            <?= $isConnected ? 'ONLINE' : 'OFFLINE' ?>
                        </span>
                    </div>

                    <?php 
                    $deviceData = null;
                    foreach ($deviceDataForConsole as $key => $value) {
                        if (strpos($key, 'fan_') === 0 || strpos($key, 'device_') === 0) {
                            $deviceData = $value;
                            break;
                        }
                    }
                    
                    if ($deviceData && isset($deviceData['Page1.Operation'])): 
                        $operationData = $deviceData['Page1.Operation'];
                    ?>
                        <div style="font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; line-height: 1.4;">
                            <?php foreach ($operationData as $key => $value): ?>
                                <div style="display: flex; justify-content: space-between; padding: 2px 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                                    <span style="color: #94a3b8; font-size: 11px;"><?= strtoupper(str_replace('_', ' ', htmlspecialchars($key))) ?></span>
                                    <span style="color: #ffffff; font-weight: bold; font-size: 14px;"><?= htmlspecialchars($value) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="color: #ff5252; font-size: 12px; text-align: center; padding: 10px;">No data</div>
                    <?php endif; ?>

                <?php else: ?>
                    <div style="color: #8b949e; font-family: sans-serif; text-align: center; padding: 15px; font-size: 12px; font-style: italic;">
                        Waiting for status...
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($component['ui_description'] === 'Read/Write'): ?>
                <div class="console-input-container" style="background-color: #1a1d21; padding: 8px; border-top: 1px solid #333; display: flex; gap: 8px;">
                    <div class="mdc-text-field mdc-text-field--outlined mdc-theme--dark" style="flex-grow: 1; height: 36px;">
                        <input type="text" class="mdc-text-field__input console-input" style="color: white; font-size: 13px; padding-top: 0; padding-bottom: 0;"
                               placeholder="Command..."
                               id="console-input-<?= esc($component['default_name']); ?>">
                        <div class="mdc-notched-outline">
                            <div class="mdc-notched-outline__leading"></div>
                            <div class="mdc-notched-outline__trailing"></div>
                        </div>
                    </div>
                    <button class="mdc-button mdc-button--raised send-command" style="background-color: #007bff; height: 36px; min-width: 60px; padding: 0 12px;">
                        <span class="mdc-button__label" style="font-size: 12px;">Send</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
                                                                               
                                                    <!-- Trigger -->
                                   <?php elseif ($ui_type === 'Trigger'): ?>
    <?php if ($component['default_name'] === 'Direction'): ?>
        <style>
            /* The 'Active' state happens the millisecond you touch the button */
            .direction-btn:active .inner-dot-anim {
                transform: scale(0.2) !important; /* Shrinks deep */
                opacity: 0.2 !important; /* Blinks out */
                transition: none !important; /* No delay on the way down */
            }
            
            /* The 'Release' state happens immediately when you let go */
            .inner-dot-anim {
                transition: transform 0.08s ease-out, opacity 0.08s ease-out; /* Very fast pop back */
                pointer-events: none;
            }
        </style>

        <div style="display: flex; justify-content: space-between; align-items: center;  width: 100%; box-sizing: border-box;">
            
            <span style="color: #607d8b; font-size: 14px; font-weight: 500;">
                <?= esc($component['default_name']); ?>
            </span>
            
            <div class="trigger-button direction-btn" 
                 data-trigger-name="<?= esc($component['default_name']); ?>" 
                 data-switch-id="<?= esc($component['asperui_values'][0]); ?>"
                 style="width: 30px; height: 30px; border-radius: 50%; background-color: #0d9488; border: 2.5px solid #BBDEFB; cursor: pointer; display: flex; align-items: center; justify-content: center; -webkit-tap-highlight-color: transparent; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                
                <div class="inner-dot-anim" 
                     style="width: 18px; height: 18px; border-radius: 50%; background-color: white; transform: scale(1); opacity: 0.95;">
                </div>
            </div>
            
        </div>


<?php else: 
    $name = isset($component['default_name']) ? trim($component['default_name']) : '';
    // Pulling the value from the schema (e.g., 0000803F)
    $action_val = isset($component['asperui_value']) ? $component['asperui_value'] : '';

    if ($name === 'Skipmode'): ?>
        <style>
            .skipmode-btn:active .inner-dot-anim {
                transform: scale(0.2) !important;
                opacity: 0.2 !important;
                transition: none !important;
            }
            .inner-dot-anim {
                transition: transform 0.08s ease-out, opacity 0.08s ease-out;
                pointer-events: none;
            }
        </style>

        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; height: 35px; padding: 0 10px; background: #fff; border-radius: 10px; margin: 5px 0; overflow: hidden; box-sizing: border-box;">
            <span style="color: #607d8b; font-size: 14px; font-weight: 500;">Skipmode</span>
            
            <div class="trigger-button skipmode-btn" 
                 data-trigger-name="Skipmode"
                 data-action="<?= esc($action_val); ?>" 
                 data-switch-id="08"
                 style="width: 30px; height: 30px; border-radius: 50%; background-color: #0d9488;    border: 2.5px solid #BBDEFB; cursor: pointer; display: flex; align-items: center; justify-content: center; -webkit-tap-highlight-color: transparent; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex-shrink: 0; margin-right: 2px;">
                
                <div class="inner-dot-anim" 
                     style="width: 18px; height: 18px; border-radius: 50%; background-color: white; transform: scale(1); opacity: 0.95;">
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="button-container" style="padding: 5px;">
            <button class="round-button trigger-button" 
                    data-trigger-name="<?= esc($name); ?>" 
                    data-switch-id="08">
                <?= esc($name); ?>
            </button>
        </div>
    <?php endif; ?>
<?php endif; ?>


        <!-- Button -->
               
                                                    <?php elseif ($ui_type === 'Button'): ?>
                                                        <div class="button-container">
                                        
                    <button class="mdc-button mdc-button--raised dynamic-action-button" 
                                                                    data-action="<?= esc($component['asperui_value']); ?>">
                                                                <span class="mdc-button__label"><?= esc($component['default_name']); ?></span>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>



                                        
                                        <!-- Trigger Components -->
                                                                                                                  <?php elseif ($component['Field_description'] === "Trigger"): ?>
                                            <?php
                                            $cmd_defaultvalue = explode(',', $component['default_name']);
                                            $cmd_uitype = explode(',', $component['ui_type']);


                                          

  
                                            foreach ($cmd_defaultvalue as $index => $default_value) {
                                                if (trim($default_value) === '') continue;
                                                
                                                $default_value = $cmd_defaultvalue[$index] ?? '';
                                                $uitype = $cmd_uitype[$index] ?? '';
                                                
                                                if ($uitype === 'Button') {
                                                    echo '<div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">';
                                                    echo '<div class="mdc-card button-container">';
                                                   echo '<button type="button" class="mdc-button mdc-button--raised">' . htmlspecialchars(trim($default_value)) . '</button>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                
             


<div id="infoOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="width: 100%; max-width: 450px; margin: 0 auto; background: #f5f5f5; min-height: 100vh;">
        <div style="background: white; padding: 16px; display: flex; align-items: center; border-bottom: 1px solid #eee;">
            <span class="material-icons" onclick="closeInfo()" style="cursor: pointer; margin-right: 20px; color: #0d9488;">arrow_back</span>
            <span style="font-weight: 500;">Device Details</span>
        </div>

    <div style="margin-top: 10px;">
        <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 14px; font-weight: 500;">Device Id</span>
                <span id="display_node_id" style="font-size: 12px; color: #888; margin-top: 4px;"><?php echo $inf_id; ?></span>
            </div>
            <span class="material-icons" style="color: #bbb;">content_copy</span>
        </div>

        <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
            <span style="font-size: 14px; font-weight: 500;">Name</span>
            <span id="display_name" style="color: #888; font-size: 14px;"><?php echo $inf_name; ?></span>
        </div>

        <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
            <span style="font-size: 14px; font-weight: 500;">Type</span>
            <span id="display_type" style="color: #888; font-size: 14px;"><?php echo $inf_type; ?></span>
        </div>

        <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 14px; font-weight: 500;">Network Connection</span>
                <span id="display_ssid" style="font-size: 12px; color: #888; margin-top: 4px;"><?php echo $inf_ssid; ?></span>
            </div>
            <span class="material-icons" style="color: #333;">wifi</span>
        </div>

        <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
            <span style="font-size: 14px; font-weight: 500;">Firmware Version</span>
            <span id="display_fw" style="color: #888; font-size: 14px;"><?php echo $inf_fw; ?></span>
        </div>



      <a id="link_guarantee" href="javascript:void(0)" onclick="openInPage(event, this, 'Guarantee Card')" style="text-decoration: none; color: inherit;">
    <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 14px; font-weight: 500;">Guarantee Card</span>
            <span style="font-size: 12px; color: #888;">Terms & Conditions</span>
        </div>
        <span class="material-icons" style="color: #bbb;">assignment</span>
    </div>
</a>

      <a id="link_video" href="javascript:void(0)" onclick="openInPage(event, this, 'User Guide')" style="text-decoration: none; color: inherit;">
    <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 14px; font-weight: 500;">User Guide</span>
            <span style="font-size: 12px; color: #888;">Troubleshooting</span>
        </div>
        <span class="material-icons" style="color: #4caf50;">chevron_right</span>
    </div>
</a>

   <a id="link_help" href="javascript:void(0)" onclick="openInPage(event, this, 'Chat Support')" style="text-decoration: none; color: inherit;">
    <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 14px; font-weight: 500;">Need Help?</span>
            <span style="font-size: 12px; color: #888;">chat with us</span>
        </div>
        <span class="material-icons" style="color: #bbb;">face</span>
    </div>
</a>

      <a id="link_track" href="javascript:void(0)" onclick="openInPage(event, this, 'Track Tickets')" style="text-decoration: none; color: inherit;">
    <div style="background: white; padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 14px; font-weight: 500;">Track tickets</span>
            <span style="font-size: 12px; color: #888;">view status</span>
        </div>
        <span class="material-icons" style="color: #bbb;">search</span>
    </div>
</a>
    </div>
</div>




<div id="iframeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000;">
    <div style="width: 100%; max-width: 450px; margin: 0 auto; height: 100%; background: white; display: flex; flex-direction: column; overflow: hidden;">
        
        <div style="background: white; padding: 16px; display: flex; align-items: center; border-bottom: 1px solid #eee; flex-shrink: 0;">
            <span class="material-icons" onclick="closeIframe()" style="cursor: pointer; margin-right: 20px; color: #0d9488;">arrow_back</span>
            <span id="modalTitle" style="font-weight: 500; flex-grow: 1;">Page View</span>
            <span class="material-icons" onclick="closeIframe()" style="cursor: pointer; color: #ccc;">close</span>
        </div>

        <div style="flex-grow: 1; overflow: hidden; width: 100%;">
            <iframe id="pageFrame" src="" 
                style="width: calc(100% + 20px); height: 100%; border: none; padding-right: 20px; box-sizing: content-box; overflow-y: scroll; scrollbar-width: none; -ms-overflow-style: none;">
            </iframe>
        </div>
    </div>
</div>

    <!-- JavaScript Libraries -->
    <script src="https://unpkg.com/@material/switch@latest/dist/mdc.switch.js"></script>
    <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/11.0.0/material-components-web.min.js"></script>

<script>
const userId = <?php echo isset($userID) ? json_encode($userID) : 'null'; ?>;
const modelname = <?php echo isset($modelNumber) ? json_encode($modelNumber) : 'null'; ?>;
const nodeid = <?php echo isset($node_id) ? json_encode($node_id) : 'null'; ?>;
const currentPage = <?php echo isset($pageNo) ? json_encode($pageNo) : '"Page1.Operation"'; ?>;
const isStatusPage = <?php echo isset($pageNo) && $pageNo === 'Status' ? 'true' : 'false'; ?>;

console.log("Debug: userId =", userId);
console.log("Debug: modelname =", modelname);
console.log("Debug: nodeid =", nodeid);
console.log("Debug: currentPage =", currentPage);
console.log("Debug: isStatusPage =", isStatusPage);

</script>





    
 
<script>

let isTableVisible = false;
let isUserInteracting = false;
let interactionTimer = null;


        document.addEventListener('DOMContentLoaded', function () {
            initializeAllComponents();
        });

      function initializeAllComponents() {
    initializeTopAppBar();
    initializeSwitches();
    initializeSliders();
    initializeSelects();
    initializeTextFields();
    initializeTriggerButtons();
    initializeColorPaletteBar();
    initializeDynamicActionButtons();
    initializeTextInputs();

    // ADD THIS LINE HERE:
    // This triggers the data fetch immediately on page load
  autoStartPayloads();
  
   // ADD THIS LINE for backend polling
    startBackendPolling(); // Add this line
}


function triggerUserLock() {
    isUserInteracting = true;
    clearTimeout(interactionTimer);
    interactionTimer = setTimeout(() => {
        isUserInteracting = false;
    }, 3000); // Locks UI for 3 seconds to let backend update
}


function startBackendPolling() {
    if (!nodeid) {
        setTimeout(startBackendPolling, 2000);
        return;
    }
    
    setInterval(() => {
        // nodeid comes from your URL (e.g., FFFF...33F00 or FFFF...4E200)
        const apiUrl = `https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id=${nodeid}&device_id=${nodeid}&_=${Date.now()}`;
        
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                console.log("Polling Result:", data);

                let hardwareData = null;

                // 1. SAFE CHECK: Look for the device key directly
                // This handles fan_XXXX or cooker_XXXX automatically
                const allKeys = Object.keys(data);
                const deviceKey = allKeys.find(k => data[k] && data[k]['Page1.Operation']);

                if (deviceKey) {
                    hardwareData = data[deviceKey]['Page1.Operation'];
                }

                // 2. ONLY sync if we actually found data to avoid errors
                if (hardwareData && typeof hardwareData === 'object') {
                    console.log("Hardware Data Found:", hardwareData);
                    syncAllUIComponents(hardwareData); 
                } else {
                    console.log("Waiting for Page1.Operation data...");
                }
            })
            .catch(err => {
                console.error("Polling Error:", err);
            });
    }, 2000);
}




        function initializeTopAppBar() {
            const topAppBarElement = document.querySelector('.mdc-top-app-bar');
            if (topAppBarElement) {
                mdc.topAppBar.MDCTopAppBar.attachTo(topAppBarElement);
            }
        }

       function initializeSwitches() {
    document.querySelectorAll('.mdc-switch').forEach((sw) => {
        // Initialize the MDC component only if not done
        if (!sw.dataset.mdcInitialized) {
            new mdc.switchControl.MDCSwitch(sw);
            sw.dataset.mdcInitialized = "true";
        }
        
        const nativeControl = sw.querySelector('.mdc-switch__native-control');
        
        nativeControl.addEventListener('change', function() {
            const switchId = sw.getAttribute('data-switch-id');
            
            // 1. UPDATE UI ONLY
            const statusDisplay = document.getElementById(`switchValueDisplay-${switchId}`);
            if (statusDisplay) {
                statusDisplay.textContent = this.checked ? 'ON' : 'OFF';
                statusDisplay.className = this.checked ? 'status-indicator on' : 'status-indicator off';
            }

            // --- REMOVED: sendDataToServer(data) ---
            // We removed this to stop the "Sending toggle data" 01 payload.
            // The hardware frame is now handled only by initializeDynamicActionButtons.
        });
    });
}

        function initializeTextInputs() {
            document.querySelectorAll('.mdc-text-field--outlined').forEach((textFieldEl) => {
                const textField = new mdc.textField.MDCTextField(textFieldEl);
                const container = textFieldEl.closest('.text-input-container') || textFieldEl.parentElement;
                
                // Find the "COMMAND GET STATUS" button
                const statusBtn = Array.from(container.querySelectorAll('button'))
                               .find(b => b.textContent.includes('STATUS'));

                if (statusBtn) {
                    statusBtn.onclick = function() {
                        // Send A1E70A for status request as specified
                        const data = {
                            node_id: nodeid,
                            device_id: nodeid,
                            page_name: "Status",
                            "Command Get Status": 1.0,
                            payload: "A1E70A"
                        };
                        
                        console.log("Sending Status Request:", data);
                        sendDataToServer(data, true);
                    };
                }
            });
        }

   function initializeSliders() {
    document.querySelectorAll('.mdc-slider').forEach((sliderElement) => {
        const slider = new mdc.slider.MDCSlider(sliderElement);
        const paramName = sliderElement.id.replace('slider-', '');
        
        // Set initial value
        const displayElementId = `sliderValueDisplay-${paramName}`;
        const initialValueElement = document.getElementById(displayElementId);
        if (initialValueElement) {
            const initialValue = parseFloat(initialValueElement.textContent.trim());
            slider.setValue(initialValue);
        }

        slider.listen('MDCSlider:input', (event) => {
            const value = slider.getValue();
            const displayElement = document.getElementById(displayElementId);
            
            if (displayElement) {
                displayElement.textContent = value;
            }

            // --- PAYLOAD LOGIC CHANGE START ---
            let finalPayload = value.toString(); // Default behavior

            // If this is the speed slider, use the A1C0...0A format
            if (paramName.toLowerCase().includes("speed")) {
                const hexVal = parseInt(value).toString(16).padStart(2, '0').toUpperCase();
                finalPayload = `A1C0${hexVal}0A`;
            }
            // --- PAYLOAD LOGIC CHANGE END ---

            const data = {
                node_id: nodeid,
                device_id: nodeid,
                page_name: currentPage,
                payload: finalPayload, // Now sends A1C0060A instead of "6"
                [paramName]: value
            };
            
            console.log("Sending slider data:", data);
            sendDataToServer(data);
        });
    });
}
        function initializeSelects() {
            document.querySelectorAll('.mdc-select').forEach((selectElement) => {
                const select = mdc.select.MDCSelect.attachTo(selectElement);
                
                select.listen('MDCSelect:change', () => {
                    const paramName = selectElement.id.replace('select-', '');
                    const selectedValue = select.value;
                    
                    const data = {
                        node_id: nodeid,
                        device_id: nodeid,
                        page_name: currentPage,
                        payload: selectedValue,
                        [paramName]: selectedValue
                    };

                    console.log("Sending dropdown data:", data);
                    
                    sendDataToServer(data);
                });
            });
        }

        function initializeTextFields() {
            document.querySelectorAll('.mdc-text-field').forEach((textField) => {
                const mdcField = mdc.textField.MDCTextField.attachTo(textField);
                if (textField.querySelector('.mdc-text-field__input').value) {
                    mdcField.layout();
                }
            });
        }

     function initializeTriggerButtons() {
    document.querySelectorAll('.trigger-button').forEach((btn) => {
        btn.addEventListener('click', function() {
            const param = btn.dataset.triggerName || this.textContent.trim();
            const paramNameLower = param.toLowerCase();

            let finalPayload = "01"; // Default

            // --- LOGIC FOR DIRECTION ---
            if (paramNameLower.includes("direction")) {
                finalPayload = "A1C0080A"; 
            }
            // --- LOGIC FOR SKIPMODE (A4 01 01 0A) ---
            else if (paramNameLower.includes("skipmode")) {
                const header = "A4";      // CommandId from JSON
                const func = "01";        // Function Code from JSON
                const dataByte = "01";    // Value for 'true' trigger
                const term = "0A";        // Terminator
                finalPayload = `${header}${func}${dataByte}${term}`;
            }

            const data = {
                node_id: nodeid,
                device_id: nodeid,
                page_name: currentPage,
                payload: finalPayload,
                [param]: 1.0
            };

            console.log(`Sending trigger (${param}):`, finalPayload);
            sendDataToServer(data);
        });
    });
}



        function initializeColorPaletteBar() {
            document.querySelectorAll('.colorPaletteBar').forEach(function(bar) {
                const selector = bar.querySelector('.selector');
                const min = parseFloat(bar.getAttribute('data-min'));
                const max = parseFloat(bar.getAttribute('data-max'));
                const value = parseFloat(bar.getAttribute('data-value'));
                let isDragging = false;

                function updateSelectorPosition(newLeft) {
                    selector.style.left = `${newLeft}px`;
                    changeSelectorColor(selector, newLeft, bar.offsetWidth);
                }

                function getPositionFromValue(val) {
                    const width = bar.offsetWidth;
                    return ((val - min) / (max - min)) * width;
                }

                // Set initial position
                const initialLeft = getPositionFromValue(value);
                updateSelectorPosition(initialLeft);

                bar.addEventListener('click', function(e) {
                    if (!isDragging) {
                        const rect = bar.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const newLeft = Math.max(0, Math.min(x, bar.offsetWidth - selector.offsetWidth));
                        updateSelectorPosition(newLeft);
                    }
                });

                bar.addEventListener('mousedown', function(e) {
                    isDragging = true;
                    const rect = bar.getBoundingClientRect();

                    function onMouseMove(e) {
                        const x = e.clientX - rect.left;
                        const newLeft = Math.max(0, Math.min(x, bar.offsetWidth - selector.offsetWidth));
                        updateSelectorPosition(newLeft);
                    }

                    function onMouseUp() {
                        isDragging = false;
                        document.removeEventListener('mousemove', onMouseMove);
                        document.removeEventListener('mouseup', onMouseUp);
                    }
                    
                    document.addEventListener('mousemove', onMouseMove);
                    document.addEventListener('mouseup', onMouseUp);
                });
            });
        }

        function changeSelectorColor(selector, x, width) {
            const colorTransitionEnd = width * 0.895;
            let color;
            
            if (x > colorTransitionEnd) {
                color = '00';
            } else {
                const hue = (x / colorTransitionEnd) * 360;
                if(hue < 30) color = '03';
                else if(hue >= 30 && hue < 60) color = '02';
                else if(hue >= 60 && hue < 160) color = '06';
                else if(hue >= 101 && hue < 190) color = '04';
                else if(hue >= 190 && hue < 270) color = '05';
                else if(hue >= 270 && hue < 300) color = '01';
                else if(hue >= 300 && hue < 350) color = '03';
                else color = '00';
            }
            
            selector.style.backgroundColor = color;
            
            const sliderid = selector.getAttribute('data-slider-id'); 
            const data = {
                node_id: nodeid,
                device_id: nodeid,
                page_name: currentPage,
                payload: color,
                [sliderid]: color
            };
            
            console.log("Sending color data:", data);
            sendDataToServer(data);
        }


function initializeDynamicActionButtons() {
    document.querySelectorAll('.mdc-switch__native-control').forEach((element) => {
        // We add a listener ONLY if we haven't added one before
        if (!element.dataset.hardwareLogicAttached) {
            
            element.addEventListener('change', function(e) {
                // 1. Tell the other function to STOP
                element.dataset.skipBasicSend = "true"; 

                const parameterName = (this.id || "").toLowerCase();
                const isChecked = this.checked;
                
                // 2. Hardware Frame Constants
                const header = "A1";      
                const terminator = "0A";  
                let functionCode = "C0";  
                let dataByte = isChecked ? "01" : "00";

                if (parameterName.includes("power")) {
                    functionCode = "C2";
                } else if (parameterName.includes("buzzer")) {
                    functionCode = "BB";
                }

                const finalPayload = `${header}${functionCode}${dataByte}${terminator}`;

                const data = {
                    node_id: nodeid,
                    device_id: nodeid,
                    page_name: currentPage,
                    payload: finalPayload,
                    [parameterName]: isChecked ? 1.0 : 0.0
                };

                console.log("--- Hardware Command Sent ---");
                console.log("Component:", parameterName, "| Payload:", finalPayload);
                
                sendDataToServer(data);

                // 3. Reset the flag after a short delay so it can be used again
                setTimeout(() => { delete element.dataset.skipBasicSend; }, 100);
            });

            element.dataset.hardwareLogicAttached = "true";
        }
    });
}



        function exampleFunction() {
            console.log('Example function called by dynamic button');
            const displayInputs = document.querySelectorAll('.display-input');
            
            displayInputs.forEach((input, index) => {
                console.log(`Value from ${input.id} (${index + 1}):`, input.value);
            });
        }

        // Function to fetch device status
    function fetchDeviceStatus() {
    if (!nodeid) return;
    
    fetch(`https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id=${nodeid}&device_id=${nodeid}&_=${Date.now()}`)
        .then(res => res.json())
        .then(data => {
            // ONLY draw the table if the user actually clicked the STATUS button
            if (isTableVisible) {
                updateConsoleWithDeviceData(data);
            } else {
                // If we are in LOG mode (isTableVisible = false), 
                // just log to browser console and let the other setInterval handle the UI
                console.log("Payload data received:", data.payload);
            }
        })
        .catch(error => {
            console.error('Error fetching device status:', error);
        });
}




function updateConsoleWithDeviceData(data) {
    const consoleBox = document.querySelector('.console-content');
    if (!consoleBox) return;

    // 1. DYNAMIC CONTAINER: No fixed height, very tight padding
    const bgColor = '#1a1d21'; 
    consoleBox.style.backgroundColor = bgColor;
    consoleBox.style.padding = '10px 15px'; 
    consoleBox.style.height = 'auto';      
    consoleBox.style.maxHeight = 'none';   
    consoleBox.style.overflowY = 'visible'; 

    // 2. DATA EXTRACTION
    let deviceParams = null;
    for (const key in data) {
        if (typeof data[key] === 'object' && data[key] !== null) {
            deviceParams = data[key]['Page1.Operation'] || data[key]['Status'] || data[key];
            if (deviceParams && typeof deviceParams === 'object') break;
        }
    }

    const isConnected = data.connected === 'true' || data.connected === true || (deviceParams !== null);
    const accentColor = isConnected ? '#00e676' : '#ff5252';
    
    // 3. HEADER: Clean look without a heavy bottom border
    let html = `
        <div style="display: flex; align-items: center; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <span style="height: 8px; width: 8px; background-color: ${accentColor}; border-radius: 50%; display: inline-block; margin-right: 10px; box-shadow: 0 0 6px ${accentColor};"></span>
            <span style="color: ${accentColor}; font-weight: 800; font-size: 14px; font-family: 'Segoe UI', sans-serif; letter-spacing: 0.5px;">
                STATUS: ${isConnected ? 'ONLINE' : 'OFFLINE'}
            </span>
        </div>`;

    // 4. DATA ROWS: No borders and very tight spacing
    if (deviceParams) {
        // Line-height reduced to 1.2 to bring text closer together
        html += `<div style="font-family: 'Consolas', 'Monaco', monospace; line-height: 1.2;">`;
        for (const [key, value] of Object.entries(deviceParams)) {
            if (typeof value !== 'object' && key !== 'name') {
                const cleanKey = key.replace(/_/g, ' ').toUpperCase();
                
                // padding set to 2px to minimize space between items
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 2px 0;">
                        <span style="color: #94a3b8; font-size: 14px; font-weight: 500;">${cleanKey}</span>
                        
                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">${value}</span>
                    </div>`;
            }
        }
        html += `</div>`;
    } else {
        html += `<div style="color: #8b949e; font-size: 15px; text-align: center; padding: 15px;">NO DATA RECEIVED</div>`;
    }
    
    consoleBox.innerHTML = html;
}


     


        // Function to send data to server using the new API format
       function sendDataToServer(data, isStatusRequest = false) {
    const apiUrl = `https://efactor.tech/v8/api.php?action=user_update_nodes_params&node_id=${nodeid}`;
    
    console.log("Sending to API:", apiUrl);
    console.log("Data:", data);
    
    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(result => {
        console.log("API Response:", result);
        
        if (isStatusRequest) {
            // When Status is requested, we show the table
            isTableVisible = true; 
            setTimeout(() => fetchDeviceStatus(), 2000);
        } else {
            // 1. Show the blue Success Toast
            showSuccessToast();

            // 2. SWITCH UI TO LOG MODE
            isTableVisible = false; 
            const consoleBox = document.querySelector('.console-content');
            if (consoleBox) {
                // We clear old table data and set the box for red payloads
                consoleBox.innerHTML = '<div style="color: #0d9488; padding: 10px;">Command Accepted. Waiting for payloads...</div>';
                consoleBox.style.backgroundColor = '#000000';
                consoleBox.style.height = '350px'; // Crucial for scrolling
                consoleBox.style.overflowY = 'auto';
            }
        }
    })
    .catch((error) => {
        console.error('Send data error:', error);
    });
}

  function addConsoleMessage(type, message) {
    const box = document.querySelector('.console-content');
    if (!box) return;

    const msgDiv = document.createElement('div');
    
    // Console styling
    msgDiv.style.cssText = `
        padding: 8px 10px; 
        font-family: 'Consolas', 'Monaco', monospace; 
        font-size: 13px; 
        border-bottom: 1px solid #222; 
        line-height: 1.5;
    `;
    
    // Capture Date and Time (YYYY-MM-DD HH:MM:SS)
    const now = new Date();
    const datePart = now.toISOString().split('T')[0];
    const timePart = now.toTimeString().split(' ')[0]; // Gets HH:MM:SS
    
    // Content: Red Date & Time on top, White payload below
    msgDiv.innerHTML = `
        <div style="color: #ff5252; font-weight: bold; letter-spacing: 0.5px;">
            ${datePart} ${timePart}
        </div>
        <div style="color: #ffffff; word-break: break-all; margin-top: 2px;">
            ${message}
        </div>
    `;
    
    box.appendChild(msgDiv);
    
    // Auto-scroll to show the latest entry
    box.scrollTop = box.scrollHeight;
}
 function showSuccessToast() {
            const toast = document.createElement('div');
            toast.innerHTML = `<div style="display: flex; align-items: center; gap: 10px;">
                                <img src="https://efactor.tech/files/brand_logo/EFACTOR_IOE.png" style="width:20px; height:20px; filter: brightness(0) invert(1);">
                                <span style="font-weight: bold;">Success</span>
                              </div>`;
            
            toast.style.cssText = `
                position: fixed; bottom: 15%; left: 50%; transform: translateX(-50%);
                background: #0d9488;   
                color: white; padding: 12px 24px; border-radius: 50px; 
                z-index: 10000; opacity: 0; transition: opacity 0.3s;
                box-shadow: 0 8px 25px rgba(3, 169, 244, 0.4); 
                border: 1px solid rgba(255,255,255,0.2);
            `;

            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = "1"; }, 10);
            setTimeout(() => {
                toast.style.opacity = "0";
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

       // Handle button clicks for STATUS and LOG
// Handle button clicks for STATUS, LOG, and SEND DATA
document.addEventListener('click', (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;
    
    const text = btn.innerText.toUpperCase().trim();
    const isSendDataBtn = btn.classList.contains('btn-send-data');

    // 1. SEND DATA Button (Captures the 24 from the input box)
    if (isSendDataBtn) {
        e.preventDefault();
        
        // Find the input field near the button
        const inputField = document.querySelector('.mdc-text-field__input');
        let userValue = inputField ? inputField.value.trim() : "0"; 

        // Convert the input value to a Hex string
        // Example: If user types 24, it converts to '18'
        let hexData = "00";
        if (!isNaN(userValue) && userValue !== "") {
            hexData = parseInt(userValue).toString(16).padStart(2, '0').toUpperCase();
        }

        const data = {
            node_id: nodeid,
            device_id: nodeid,
            page_name: "Status",
            "Send Data": 1.0,
            payload: `A1C0${hexData}0A` // Dynamically creates the payload
        };

        console.log(`--- Sending Dynamic Payload ---`);
        console.log(`Input Value: ${userValue} -> Hex: ${hexData}`);
        console.log(`Final Payload: ${data.payload}`);
        
        sendDataToServer(data, false); // false = stay in LOG mode
    } 
    // 2. COMMAND GET STATUS (Fixed check)
    else if (text.includes("STATUS")) {
        e.preventDefault();
        isTableVisible = true; 
        
        const consoleBox = document.querySelector('.console-content');
        if (consoleBox) consoleBox.innerHTML = ''; 

        const data = {
            node_id: nodeid,
            device_id: nodeid,
            page_name: "Status",
            "Command Get Status": 1.0,
            payload: "A1C0010A" 
        };
        
        sendDataToServer(data, true); // true = show Table
    } 
    // 3. DISPLAY LOG Button
    else if (text.includes("LOG")) { 
        e.preventDefault();
        isTableVisible = false; 
        
        const consoleBox = document.querySelector('.console-content');
        if (consoleBox) consoleBox.innerHTML = ''; 

        const data = {
            node_id: nodeid,
            device_id: nodeid,
            page_name: "Status",
            payload: "00",
            "DisplayLog": 1.0
        };
        sendDataToServer(data, false);
    }
});





        // Helper function to update slider value display
        function updateSliderValue(value, paramName) {
            const displayElement = document.getElementById(`sliderValueDisplay-${paramName}`);
            if (displayElement) {
                displayElement.textContent = value;
            }
        }
    



// This loop forces the red payloads into the custom display box
setInterval(() => {
    // 1. THE STOP SIGN: If the table is open, do absolutely nothing.
    if (isTableVisible === true || !nodeid) return;

    fetch(`https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id=${nodeid}&_=${Date.now()}`)
        .then(res => res.json())
        .then(data => {
            // 2. THE DOUBLE CHECK: Check again in case the user clicked while waiting for the server.
            if (isTableVisible === true) return;

            const consoleBox = document.querySelector('.console-content');
            if (!consoleBox) return;

            if (consoleBox.innerHTML.includes("connection")) {
                consoleBox.innerHTML = '';
            }

            let displayValue = data.payload;

            // 3. ONLY PRINT IF: We are NOT in table mode AND there is real data.
            if (displayValue && displayValue !== "null" && displayValue !== "" && displayValue !== "00") {
                addConsoleMessage('error', displayValue); 
            }
        })
        .catch(err => console.error("Fetch Error:", err));
}, 2000);

function autoStartPayloads() {
    if (typeof nodeid !== 'undefined' && nodeid && nodeid !== "") {
        console.log("Kickstarting Log Mode for:", nodeid);
        
        isTableVisible = false; // Set mode to Log mode

        // Reset the box styling to "Log Mode" (Black background, scrolling enabled)
        const consoleBox = document.querySelector('.console-content');
        if (consoleBox) {
            consoleBox.innerHTML = '<div style="color: #00bcd4; padding: 10px;">Establishing connection...</div>';
            consoleBox.style.backgroundColor = '#000000';
            consoleBox.style.height = '300px'; // Give it a fixed height for scrolling
            consoleBox.style.overflowY = 'auto';
            consoleBox.style.display = 'block';
        }
        
        const data = {
            node_id: nodeid,
            device_id: nodeid,
            page_name: "Status",
            payload: "00",
            "DisplayLog": 1.0
        };
        
        sendDataToServer(data);
        
    } else {
        setTimeout(autoStartPayloads, 500);
    }
}

function handleRename(oldName) {
    // 1. Pop up a box to type the new name
    let newName = prompt("Change Device Name:", oldName);
    
    // 2. Only proceed if the user typed something and didn't hit cancel
    if (newName !== null && newName.trim() !== "") {
        
        // 3. Update the text on the screen immediately
        document.getElementById('device-name-text').innerText = newName;
        
        // 4. Send the data to the server
        // This uses the same logic as your other buttons
        console.log("Sending Trigger Data for name update...");
        
        // Replace 'yourSubmitFunction' with the actual function name 
        // you use for triggers (like sendTrigger or updateNode)
        if (typeof sendTriggerData === "function") {
            sendTriggerData("name", newName);
        } else {
            console.warn("Function 'sendTriggerData' not found. Data updated locally only.");
        }
    }
}



// We receive the data directly from the button click now
function openDetails(id, name, type, ssid, fw, guarant, video, help, track) {
    // 1. Text updates
    document.getElementById('display_node_id').textContent = id;
    document.getElementById('display_name').textContent = name;
    document.getElementById('display_type').textContent = type;
    document.getElementById('display_ssid').textContent = ssid;
    document.getElementById('display_fw').textContent = fw;

    // 2. Store URLs (Check these IDs match your HTML <a> tags)
    if(document.getElementById('link_guarantee')) document.getElementById('link_guarantee').setAttribute('data-url', guarant);
    if(document.getElementById('link_video'))     document.getElementById('link_video').setAttribute('data-url', video);
    if(document.getElementById('link_track'))     document.getElementById('link_track').setAttribute('data-url', track);
    
    // Safety check for the help link
    var helpLink = document.getElementById('link_help');
    if (helpLink) {
        helpLink.setAttribute('data-url', help);
    } else {
        console.error("Could not find the element with ID 'link_help'!");
    }

    document.getElementById('infoOverlay').style.display = 'block';
}

function openInPage(event, element, title) {
    event.preventDefault(); 
    var url = element.getAttribute('data-url');
    
    // Debugging: This will tell us if the URL is actually there
    console.log("Clicked:", title, "URL is:", url);

    if (url && url !== '#' && url !== '') {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('pageFrame').src = url;
        document.getElementById('iframeModal').style.display = 'flex';
    } else {
        alert("Sorry, the link for " + title + " is empty or not found.");
    }
}

function closeIframe() {
    document.getElementById('iframeModal').style.display = 'none';
    document.getElementById('pageFrame').src = ''; 
}

function closeInfo() {
    document.getElementById('infoOverlay').style.display = 'none';
}



setInterval(function() {
    // 1. Grab the data (Working in F12)
    const payload = <?php echo json_encode($deviceDataForConsole); ?>;
    const currentPage = "<?php echo $pageNo; ?>";
    
    const now = new Date();
    const dateStr = now.toLocaleDateString(); 
    const timeStr = now.toLocaleTimeString();

    // 2. Print to Browser Console (F12)
    console.log(currentPage + " [" + dateStr + " " + timeStr + "]:", payload);

    // 3. SEND TO BLACK BOX (The missing part)
    // We look for the hex string inside the payload object
    let hexCode = (payload && payload.payload) ? payload.payload : null;

    // Only send to the black box if it's a real hex string (not null/empty)
    if (hexCode && hexCode !== "null" && hexCode !== "" && hexCode !== "--- NO DATA ---") {
        addConsoleMessage('error', hexCode);
    }

}, 2000);




function syncAllUIComponents(data) {
    // Stop update if the user is currently touching a control
    if (!data || isUserInteracting) return; 

    Object.keys(data).forEach(key => {
        let val = data[key];
        const el = document.getElementById(key);

        if (el) {
            // --- DATA TYPE HANDLING ---
            if (val !== null && !isNaN(val) && typeof val !== "boolean") {
                
                // 1. Handle UnsignedInt (rpm)
                if (key.toLowerCase() === 'rpm') {
                    val = Math.round(parseFloat(val)); // Force to whole number
                } 
                // 2. Handle Floats (Current, Voltage)
                else {
                    val = parseFloat(val).toFixed(2); // Keep 2 decimal places
                }
            }

            // --- UI UPDATE ---
            if (el.classList.contains('text-display')) {
                if (el.textContent !== String(val)) {
                    el.textContent = String(val);
                    console.log(`Auto-Updated ${key} (${typeof val}): ${val}`);
                }
            }
            
            // Handle Switches (Power, Buzzer)
            else if (el.classList.contains('mdc-switch')) {
                const control = el.querySelector('.mdc-switch__native-control');
                const isOn = (val === 'true' || val === true || val === "ON");
                if (control && control.checked !== isOn) {
                    control.checked = isOn;
                    isOn ? el.classList.add('mdc-switch--checked') : el.classList.remove('mdc-switch--checked');
                }
            }
        }
    });
}

    </script>



</body>
</html>
