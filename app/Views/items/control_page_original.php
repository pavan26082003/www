<?php 
$data = json_decode($response, true);
$data2 = json_decode($jsonResponse, true);

$additionalData = $components['additionalData'];

$node_id = $additionalData['node_id'];
$modelNumber = $additionalData['modelNumber'];
$pageNo = $additionalData['pageNo'];
$userID = $additionalData['userID'];

// Print values
/*echo "Node ID: " . $node_id . "<br>";
echo "Model Number: " . $modelNumber . "<br>";
echo "Page Number: " . $pageNo . "<br>";
echo "User ID: " . $userID . "<br>";*/



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
            --mdc-theme-primary: #6200ee;
            --mdc-theme-secondary: #6200ee;
            --card-background: #f8f9ff;
            --card-border: 1px solid #e0e0e0;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --section-spacing: 24px;
            --element-spacing: 16px;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .main-content {
            padding: 80px 16px 32px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .mdc-card {
            background-color: var(--card-background);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 8px;
            margin-bottom: var(--element-spacing);
            padding: var(--element-spacing);
            transition: all 0.3s ease;
        }
        
        .mdc-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .control-section {
            margin-bottom: var(--section-spacing);
        }
        
        .control-title {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 12px;
            color: #444;
        }
        
        /* Toggle Switch Styles */
        .toggle-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
        }
        
        .toggle-label {
            flex-grow: 1;
            margin-right: 20px;
            font-size: 1rem;
        }
        
        /* Slider Styles */
        .slider-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin: 16px 0;
        }
        
        .slider-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .slider-value {
            font-weight: 500;
            min-width: 40px;
            text-align: right;
        }
        
        /* Dropdown Styles */
        .dropdown-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 12px 0;
        }
        
        /* Color Palette Styles */
        .color-palette-container {
            margin: 16px 0;
        }
        
        .colorPaletteBar {
            position: relative;
            width: 100%;
            height: 24px;
            border-radius: 12px;
            background: linear-gradient(to right, 
                #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000, #ffffff);
            margin: 12px 0;
            cursor: pointer;
        }
        
        .colorPaletteBar .selector {
            width: 32px;
            height: 32px;
            position: absolute;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            top: -4px;
            transform: translateX(-50%);
            cursor: pointer;
            z-index: 2;
        }
        
        /* Button Styles */
        .button-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        
        .mdc-button {
            border-radius: 24px;
            padding: 0 24px;
            height: 48px;
            font-weight: 500;
        }
        
        /* Device Info */
        .device-info {
            background-color: white;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 24px;
            font-size: 1.1rem;
            font-weight: 500;
            box-shadow: var(--card-shadow);
        }
        
        /* Responsive Grid */
        .mdc-layout-grid {
            padding: 0;
        }
        
        .mdc-layout-grid__inner {
            margin: 0;
        }
        
        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            margin-left: 12px;
            font-size: 0.9rem;
        }
        
        .status-indicator.on {
            color: #4CAF50;
        }
        
        .status-indicator.off {
            color: #F44336;
        }
        
        /* Fan Speed Icons */
        .fan-speed-icons {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 8px 0;
        }
        
        .fan-speed-icon {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        /* Text Inputs */
        .text-input-container {
            margin: 16px 0;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 600px) {
            .main-content {
                padding: 72px 8px 24px;
            }
            
            .mdc-card {
                padding: 12px;
            }
            
            .toggle-label, .control-title {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="mdc-drawer-app-content">
        <header class="mdc-top-app-bar">
            <div class="mdc-top-app-bar__row">
                <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
                    <span class="material-icons mdc-top-app-bar__navigation-icon">menu</span>
                    <span class="mdc-top-app-bar__title">Efactor SmartThings</span>
                </section>
            </div>
        </header>
        
        <!-- Main content starts here -->
        <div class="main-content">
            <div class="device-info">
                Device ID: <?php echo isset($node2) ? $node2 : 'N/A'; ?>
            </div>
            
            <div class="mdc-layout-grid">
                <div class="mdc-layout-grid__inner">
                    <?php foreach ($components as $comp): ?>
                        <?php 
                        $node2 = $comp['node_id'];
                        $page = $comp['pageNo'];
                        $modelNumber = $comp['modelNumber'];
                        ?>
                    <?php endforeach; ?>
                    
                    <?php foreach ($data['node_details'] as $node): ?>
                        <?php if (isset($node2) && $node2 === $node['id']): ?>
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
                                                            <div class="toggle-wrapper">
                                                                <label class="toggle-label" for="<?= esc($component['default_name']); ?>">
                                                                    <?= esc($component['default_name']); ?>
                                                                </label>
                                                                <div class="mdc-switch" data-switch-id="<?= esc($component['default_name']); ?>">
                                                                    <div class="mdc-switch__track"></div>
                                                                    <div class="mdc-switch__thumb-underlay">
                                                                        <div class="mdc-switch__thumb">
                                                                            <input type="checkbox" id="<?= esc($component['default_name']);?>" 
                                                                                   class="mdc-switch__native-control" role="switch" 
                                                                                   <?= $powerValue ? 'checked' : ''; ?>>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span id="switchValueDisplay-<?= esc($component['default_name']); ?>" 
                                                                      class="status-indicator <?= $powerValue ? 'on' : 'off'; ?>">
                                                                    <?= $powerValue ? 'ON' : 'OFF'; ?>
                                                                </span>
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
                                                                    <img class="fan-speed-icon" src="http://localhost/factor_17-05/files/home/lowspd.png" alt="Low Speed">
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
                                                            <div class="text-input-container">
                                                                <label class="control-title" for="<?= esc($component['default_name']); ?>">
                                                                    <?= esc($component['default_name']); ?>
                                                                </label>
                                                                <?php if(isset($params[$component['default_name']])): ?>
                                                                    <?php if ($component['ui_description'] === 'Read/Write'): ?>
                                                                        <input type="text" class="text-input" 
                                                                               name="hex_value[]" 
                                                                               value="<?= esc($params[$component['default_name']]); ?>" />
                                                                    <?php else: ?>
                                                                        <div class="text-display">
                                                                            <?= esc($params[$component['default_name']]); ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <?php if ($component['ui_description'] === 'Read/Write'): ?>
                                                                        <input type="text" class="text-input" 
                                                                               name="hex_value[]" 
                                                                               value="<?= esc($component['asperui_value']); ?>" />
                                                                    <?php else: ?>
                                                                        <div class="text-display">
                                                                            <?= esc($component['asperui_value']); ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        
                                                        <!-- Trigger -->
                                                        <?php elseif ($ui_type === 'Trigger'): ?>
                                                            <div class="button-container">
                                                                <div class="button-wrapper">
                                                                    <button class="round-button trigger-button" 
                                                                            data-trigger-name="<?= esc($component['default_name']); ?>" 
                                                                            data-switch-id="08">
                                                                        <?= esc($component['default_name']); ?>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        
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
                </div>
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
console.log("Debug: userId =", userId);
console.log("Debug: modelname =", modelname);
console.log("Debug: nodeid =", nodeid);

</script>




    
    <script>
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
        }

        function initializeTopAppBar() {
            const topAppBarElement = document.querySelector('.mdc-top-app-bar');
            if (topAppBarElement) {
                mdc.topAppBar.MDCTopAppBar.attachTo(topAppBarElement);
            }
        }

        function initializeSwitches() {
            document.querySelectorAll('.mdc-switch').forEach((sw) => {
                if (!sw.classList.contains('mdc-switch--checked')) {
                    const switchControl = new mdc.switchControl.MDCSwitch(sw);
                }
                
                sw.querySelector('.mdc-switch__native-control').addEventListener('change', function() {
                    const switchId = sw.getAttribute('data-switch-id');
                    console.log(`${switchId}  :${this.checked ? 'ON' : 'OFF'}`);
                    
                    // Update status display
                    const statusDisplay = document.getElementById(`switchValueDisplay-${switchId}`);
                    if (statusDisplay) {
                        statusDisplay.textContent = this.checked ? 'ON' : 'OFF';
                        statusDisplay.className = this.checked ? 'status-indicator on' : 'status-indicator off';
                    }

                    // Send data to server
                    const data = {
                        value: this.checked,
                        parameterName: switchId,
                        user_id: userId,
                        modelName: modelname,
                        node_id: nodeid
                    };

                    console.log("Data object:", data);
                    
                    sendDataToServer(data);
                });
            });
        }

        function initializeSliders() {
            document.querySelectorAll('.mdc-slider').forEach((sliderElement) => {
                const slider = new mdc.slider.MDCSlider(sliderElement);
                const paramName = sliderElement.id.replace('slider-', '');
                
                // Set initial value
                const initialValue = parseFloat(document.getElementById(`sliderValueDisplay-${paramName}`).textContent.trim());
                slider.setValue(initialValue);

                slider.listen('MDCSlider:input', (event) => {
                    const value = slider.getValue();
                    const displayElement = document.getElementById(`sliderValueDisplay-${paramName}`);
                    
                    if (displayElement) {
                        displayElement.textContent = value;
                    }

                    const data = {
                        value: value,
                        parameterName: paramName,
                        user_id: userId,
                        modelName: modelname,
                        node_id: nodeid

                    };
                    console.log("Data object:", data);
                    
                    sendDataToServer(data);
                });
            });
        }

        function initializeSelects() {
            document.querySelectorAll('.mdc-select').forEach((selectElement) => {
                const select = mdc.select.MDCSelect.attachTo(selectElement);
                
                select.listen('MDCSelect:change', () => {
                    const paramName = selectElement.id.replace('select-', '');
                    
                    const data = {
                        value: select.value,
                        parameterName: paramName,
                        user_id: userId,
                        modelName: modelname,
                        node_id: nodeid

                    };

                    console.log("Data object:", data);
                    
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
            document.querySelectorAll('.trigger-button').forEach((button) => {
                button.addEventListener('mousedown', function() {
                    let holdTimer = setTimeout(() => {
                        console.log('Hold action triggered for', button.dataset.triggerName);
                    }, 1000); 

                    button.addEventListener('mouseup', function() {
                        clearTimeout(holdTimer);
                        console.log('Tap action triggered');

                        const data = {
                            value: button.dataset.switchId,
                            parameterName: button.dataset.triggerName,
                            user_id: userId,
                            modelName: modelname,
                        node_id: nodeid

                        };

                       console.log("Data object:", data);
                        
                        sendDataToServer(data);
                    });

                    button.addEventListener('mouseleave', function() {
                        clearTimeout(holdTimer);
                    });
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
                value: color,
                parameterName: sliderid,
                user_id: userId,
                modelName: modelname,
                        node_id: nodeid

            };
            
            sendDataToServer(data);
        }

        function initializeDynamicActionButtons() {
            document.querySelectorAll('.dynamic-action-button').forEach((button) => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    
                    if (action && action.startsWith('http')) {
                        window.location.href = action;
                    } else if (action) {
                        if (typeof window[action] === 'function') {
                            window[action]();
                        } else {
                            console.error('Function not found:', action);
                        }
                    }
                });
            });
        }

        function exampleFunction() {
            console.log('Example function called by dynamic button');
            const displayInputs = document.querySelectorAll('.display-input');
            
            displayInputs.forEach((input, index) => {
                console.log(`Value from ${input.id} (${index + 1}):`, input.value);
            });
        }

        function sendDataToServer(data) {
            fetch("https://efactor.tech/v6/studio_mqtt.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(responseText => console.log(responseText))
            .catch(error => console.error('Fetch error:', error));
        }

        function updateSliderValue(value, paramName) {
            const displayElement = document.getElementById(`sliderValueDisplay-${paramName}`);
            if (displayElement) {
                displayElement.textContent = value;
            }
        }

        mdc.autoInit();
    </script>
</body>
</html>