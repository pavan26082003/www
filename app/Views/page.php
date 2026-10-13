<?php 
 
$data = json_decode($response, true);
//$data = json_decode($jsonResponse, true);



?>
 
<?php 
 
$data2 = json_decode($jsonResponse, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device UI Components</title>
    <!-- Material CSS & other stylesheets -->
	<!-- Include Material Components for the Web CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/11.0.0/material-components-web.min.css">
	<link rel="stylesheet" href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css">
	    <style>
		:root {
		--mdc-theme-secondary: #6200ee;
		}
		.mdc-switch__icon--on, .mdc-switch__icon--off {
			display: none;
		}
		.mdc-switch__focus-ring-wrapper, .mdc-switch__focus-ring {
			display: none !important;
		}
		
		
		/*toggle switch*/
		.toggle-wrapper {
		display: flex;
		align-items: center; /* Vertically centers the label and toggle */
		justify-content: space-between; /* Puts maximum space between label and switch */
		}

		.toggle-label {
			flex-grow: 1; /* Allows the label to take up available space */
			margin-right: 20px; /* Ensures some space between the label and the switch */
		}

        /* Ensure top app bar has enough contrast */
        .mdc-top-app-bar {
            background-color: #6200ee;
            color: white;
        }
        /* Add padding to the main content to avoid being hidden by the app bar */
        .main-content {
            padding-top: 64px; /* Adjust based on the height of your app bar */
        }
		   .mdc-slider {
            width: 80%; /* Adjust this to fit your layout */
        }
		.my-custom-media {
			position: relative;
			width: 100%;
			padding-top: 200%; /* 4:3 Aspect Ratio */
			background-size: cover; /* Cover the entire area of the element */
			background-position: center; /* Center the background image */
		  }
       /* Custom CSS for spacing */
        .label-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            margin-right: 20px; /* Adjust this value to increase or decrease the distance */
        }
        .mdc-select {
            flex-grow: 2; /* Allows the select to fill the remaining space */
        }
		
		.mdc-card #colorPaletteBar {
			/* Your styles */
			
			width: 100%;
			height: 10px;
			background: linear-gradient(to right, 
				#ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000, #ffffff);
			position: relative;


		}

		 .mdc-card .colorPaletteBar {
			position: relative;
			width: 100%;
			height: 20px;
			background: linear-gradient(to right, 
				#ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000,  #ffffff);
		}

		.mdc-card .colorPaletteBar .selector {
			width: 35px;
			height: 35px;
			position: absolute;
			border-radius: 50%;
			background-color: #ff0000; /* Initial color */
			top: 9px;
			left: 0;
			transform: translate(-50%, -50%);
			cursor: pointer;
		}

	 /* Add the .button-wrapper and .round-button styles here */
				.button-wrapper {
			display: inline-flex;
			justify-content: center;
			align-items: center;
			padding: 8px; /* Controls the size of the outer circle */
			border-radius: 50%;
			background: rgba(100, 100, 255, 0.2); /* Adjust the background color and opacity as needed */
		}

		.round-button {
			/* Add your existing styles here, without the box-shadow */
			transition: transform 0.1s; /* Keep the transform transition */
		}

		.round-button:active {
			transform: scale(0.9);
			/* No need to adjust the box-shadow here since we're using a wrapper */
		}
		.round-button {
			display: inline-flex;
			justify-content: center;
			align-items: center;
			width: 30px;
			height: 30px;
			border-radius: 50%;
			background-color: #6200ee;
			color: white;
			border: none;
			cursor: pointer;
			outline: none;
			user-select: none;
			transition: transform 0.1s; /* Transition for the transform property */
		}

		.round-button:active {
			transform: scale(0.9); /* Slightly reduce the size when active/pressed */
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
		<div class="mdc-layout-grid">
		   <div class="mdc-layout-grid__inner">
				<!--?php
			usort($components, function($a, $b) {
				return $a['ui_sequence_no'] <=> $b['ui_sequence_no'];
			});
			?-->
			
			<?php foreach ($compt as $comp){
			 $node2 = $comp['node_id'];
			 $page = $comp['page_names'];
             $modelNumber = $comp['model_name'];
			// echo $page;
			// echo $node2;
            // echo $modelNumber;
			} ?>
			
            <?php if (!empty($node2)): ?>
	 <?php foreach ($data['node_details'] as $node): ?>
	  <?php if ($node2 === $node['id']): ?>
 
      <?php  $pages_array = $node['config']['devices'][0]['pages'];
        $params = []; ?>

          <?php      foreach ($pages_array as $pageKey => $pageData) : ?>
           <?php if ($page === $pageKey): ?>
         <?php  foreach ($pageData as $frame) :  ?>
         <?php foreach ($frame['frame']['commands'] as $component) : ?>
          <?php if ($component['Field_description'] === "Data" && $component['ui_type'] !== "None"): ?>
    <?php
   
  $cmd_names = explode(',', $component['Field_description']);
   
    $ui_types = explode(',', $component['ui_type']);
    $ui_permissions = explode(',', $component['ui_description']);
    $ui_datatypes = explode(',', $component['data_type']);
   // $multi_ui =  $component['cmd_multipleui'];
   // $trigger_ui =  $component['cmd_triggerui'];
  //  $page =  $component['page_names'];

    // Command to find, dynamically based on application logic
    $command_to_find = "Data"; // Change this as needed

    // Find the position of the command in the command list
    $position = array_search($command_to_find, $cmd_names);
 
    // Initialize variables to store UI type and other parameters, defaulting to an empty string if not found
    $ui_type = '';
    $ui_permission = '';
    $default_name = '';
    $parameter_id = ''; // Assuming this is a unique identifier for each parameter
    $slider_step = '';
    $range_min = '';
    $range_max = '';

    // Retrieve the corresponding UI type and other parameters using the position
    if ($position !== false) {
        $ui_type = isset($ui_types[$position]) ? $ui_types[$position] : '';
        $default_name = isset($component['default_name']) ? $component['default_name'] : '';
        $parameter_id = isset($component['parameter_id']) ? $component['parameter_id'] : '';
        $slider_step = isset($component['cmd_sliderstepvalue']) ? $component['cmd_sliderstepvalue'] : '';
        $range_min = isset($component['display_value']['min']) ? $component['display_value']['min'] : '';
        $range_max = isset($component['display_value']['max']) ? $component['display_value']['max']: '';
    }
    
    ?>
<?php

 if($data2['url'] == 'action=auto_get_nodes_params'){
$params = [];
 
if (isset($data2['node_id']) && $data2['node_id'] === $node2) {
    $params = [];

    foreach ($data2 as $key => $value) {
        if (is_array($value) && isset($value[$page])) {
            $params = $value[$page];
            break;  
        }
    }

} else {
    echo " ";
}
  
}else{
$pages_array = null;
foreach ($data['node_details'] as $node) {
        $pages_array = $node['config']['devices'][0]['pages'];
        $params = [];
foreach ($pages_array as $page) {
    foreach ($page as $frame) {
        foreach ($frame['frame']['commands'] as $command) {
            if ($command['Field_description'] === "Data" && $command['ui_type'] === "Drop Down") {
                $params[$command['default_name']] = $command['value'] ?? null;
            }elseif($command['Field_description'] === "Data" && $command['ui_type'] === "Toggle Switch"){
            $params[$command['default_name']] = $command['value'] ?? null;
            }elseif($command['Field_description'] === "Data" && $command['ui_type'] === "Trigger"){
                $params[$command['default_name']] = $command['value'] ?? null;
                }elseif($command['Field_description'] === "Data" && $command['ui_type'] !== "Drop Down" && $command['ui_type'] !== "Toggle Switch"  && $command['ui_type'] !== "Triggert"){
                $params[$command['default_name']] = $command['asperui_value'] ?? null;
                }else{
            echo "";
            }
        }
    }
}
//print_r($params);
        break; 
}
}
 ?>
 

	
                 <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
    <div class="mdc-card" style="margin-top:20px;margin-bottom:20px;background-color:#f1f0ff;padding:16px;">
        

<!----------------------------------------------------------------------------------------------------------------------->

 <?php if ($ui_type === 'Toggle Switch'): ?>
            <div class="toggle-wrapper">
             <?php
$ui_dropdown = $component['asperui'];  

$ui_dropdown_value = $component['asperui_values'];  

$dropdown_map = array_combine($ui_dropdown_value,$ui_dropdown);
 
$value2 = isset($params[$component['default_name']]) ? $params[$component['default_name']] : '';
if (empty($value2)) {
    $value3 = "00";
} else {
    $value3 =   $value2;
}

$value = isset($dropdown_map[$value3]) ? $dropdown_map[$value3] : ' ';
$powerValue = ($value === 'true') ? 'checked' : '';
  // echo $value;
   //print_r($dropdown_map);
 // echo $value3;
?>
                 <p></p>
                 <span class="spacer" style="display: inline-block; width:10px;"></span> <!-- Spacer element -->
                <label class="toggle-label" for="<?= esc($component['default_name']); ?>"><?= esc($component['default_name']); ?></label>
                <div class="mdc-switch" data-switch-id="<?= esc($component['default_name']); ?>">
                    <div class="mdc-switch__track"></div>
                    <div class="mdc-switch__thumb-underlay">
                        <div class="mdc-switch__thumb">
                            <input type="checkbox" id="<?= esc($component['default_name']);?>" class="mdc-switch__native-control" role="switch" <?= isset($powerValue) &&$powerValue ? 'checked' : ''; ?> 
                             >
                        </div>
                    </div>
                    <div class="mdc-switch__ripple"></div>
                </div>
                <span id="switchValueDisplay-<?= esc($component['default_name']); ?>" class="switch-status" style="margin-left: 16px;">
                 <?php echo isset($powerValue) &&  $powerValue ? 'ON' : 'OFF'; ?></span>
            </div>


<!----------------------------------------------------------------------------------------------------------------------->


 <?php elseif ($ui_type === 'Slider'||$ui_type === 'Brightness Slider'||$ui_type === 'CCT Slider'||$ui_type === 'Saturation Slider' ): ?>
			<?php $ui_step_value=explode(',',$component['slider_step_value']);
				$ui_range_min_value=explode(',', $component['display_value']['min']);
                                $ui_range_max_value=explode(',', $component['display_value']['max']);
									
		// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_step_value[$position])) {
			$ui_step = $ui_step_value[$position];
		}
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_min_value[$position])) {
			$ui_range_min = $ui_range_min_value[$position];
		}
		
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_max_value[$position])) {
			$ui_range_max = $ui_range_max_value[$position];
                   
			}
			/*$ui_step =0.01;
			$ui_cmd_value = 0.41;
			$ui_range_min = 0.4;
			$ui_range_max = 0.7;*/

/*$ui_step = 1; // 0.01 scaled by 100
$ui_cmd_value = 41; // 0.41 scaled by 100
$ui_range_min = 40; // 0.4 scaled by 100
$ui_range_max = 70; // 0.7 scaled by 100*/

$ui_cmd_value =$ui_range_min ;
				
							?>
							
							
			<div style="display: flex; align-items: center; justify-content: space-between;">
			<div style="flex-grow: 1; display: flex; align-items: center;">
				<!-- Label for the slider -->
			<label class="mdc-typography--subtitle2"  for="<?= esc($component['default_name']); ?>"><?= esc($component['default_name']); ?></label>
   
								<!-- Slider Component -->
<div class="mdc-slider" style="flex-grow: 1; margin: 16px;" id="slider-<?= esc($component['default_name']); ?>">
    <input class="mdc-slider__input" type="range" min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
           value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
           aria-labelledby="label-<?= esc($component['default_name']); ?>"
            oninput="updateSliderValue(this.value,'<?= esc($component['default_name']); ?>')"> <!-- Call JavaScript function on input event -->
 
 
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

<?php
 
$value = isset($params[$component['default_name']]) ? $params[$component['default_name']] : '';
?>

<span id="sliderValueDisplay-<?= esc($component['default_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($value); ?></span>
						</div>
						<!----------------------------------------------------------------------------------------------------------------------->

 <?php elseif ($ui_type === 'Airflow Slider' ): ?>
			<?php $ui_step_value=explode(',',$component['slider_step_value']);
				$ui_range_min_value=explode(',', $component['display_value']['min']);
                                $ui_range_max_value=explode(',', $component['display_value']['max']);
									
		// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_step_value[$position])) {
			$ui_step = $ui_step_value[$position];
		}
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_min_value[$position])) {
			$ui_range_min = $ui_range_min_value[$position];
		}
		
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_max_value[$position])) {
			$ui_range_max = $ui_range_max_value[$position];
                   
			}
			/*$ui_step =0.01;
			$ui_cmd_value = 0.41;
			$ui_range_min = 0.4;
			$ui_range_max = 0.7;*/

/*$ui_step = 1; // 0.01 scaled by 100
$ui_cmd_value = 41; // 0.41 scaled by 100
$ui_range_min = 40; // 0.4 scaled by 100
$ui_range_max = 70; // 0.7 scaled by 100*/

$ui_cmd_value =$ui_range_min ;
							?>
							
							
			<div style="display: flex; align-items: center; justify-content: space-between;">
			<div style="flex-grow: 1; display: flex; align-items: center;">
				<!-- Label for the slider -->
			<label class="mdc-typography--subtitle2"  for="<?= esc($component['default_name']); ?>"><?= esc($component['default_name']); ?></label>
   
								<!-- Slider Component -->
	 <img style="margin-left:10px;width:80px;height:80px" src="http://localhost/factor_17-05/files/home/lowspd.png">
<div class="mdc-slider" style="flex-grow: 1; margin: 16px;" id="slider-<?= esc($component['default_name']); ?>">
    <input class="mdc-slider__input" type="range" min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
           value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
           aria-labelledby="label-<?= esc($component['default_name']); ?>"
            oninput="updateSliderValue(this.value,'<?= esc($component['default_name']); ?>')"> <!-- Call JavaScript function on input event -->
 <?php
 
$value = isset($params[$component['default_name']]) ? $params[$component['default_name']] : '';
?>
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
 
 <img style="margin-left:10px;width:80px;height:80px" src="http://localhost/factor_17-05/files/home/highspd.png">
<span id="sliderValueDisplay-<?= esc($component['default_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($value); ?></span>
						</div>
<!----------------------------------------------------------------------------------------------------------------------->
						
 <?php elseif ($ui_type === 'Drop Down'): ?>
                  <?php
$ui_dropdown = $component['asperui'];  

$ui_dropdown_value = $component['sno_asperui'];  

$dropdown_map = array_combine($ui_dropdown_value,$ui_dropdown);
 
$value2 = isset($params[$component['default_name']]) ? $params[$component['default_name']] : '';
$value3 = $value2; 

$value = isset($dropdown_map[$value3]) ? $dropdown_map[$value3] : ' ';
   
// echo $value3;  
 // print_r($dropdown_map);
?>
				 <div style="display: flex; align-items: center; justify-content: space-between;">
				  <div class="label-container">
                        <label class="mdc-typography--subtitle2"  for="<?= esc($component['default_name']); ?>"><?= esc($component['default_name']); ?></label>
						</div>
		<div class="mdc-select mdc-select--filled" id="select-<?= esc($component['default_name']); ?>">
		<div style="background-color:lightgrey" class="mdc-select__anchor"role="button" aria-haspopup="listbox" aria-expanded="false">
									<span class="mdc-select__ripple"></span>
<span class="mdc-select__selected-text"></span>
					 <span class="mdc-select__dropdown-icon">
		<svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5"focusable="false">$params[$component['default_name']]
	 <polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>
	<polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>
				</svg>
				</span>
						</div>
		<div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
								<?php
							   // Assuming $component['ui_options'] is a string of options separated by commas
								$options = explode(',','ui_dropdown');
								$index = 0; // Initialize a counter variable
								 $selcted_index=0;
								?>
								 
							<ul class="mdc-list" role="listbox">
    <?php $index = 0; ?>
    <?php foreach ($dropdown_map as $option): ?>
        <?php   $isSelected = ($option === $value); ?>
        <li  class="mdc-list-item <?= $isSelected ? 'mdc-list-item--selected' : ''; ?>"   role="option"   aria-selected="<?= $isSelected ? 'true' : 'false'; ?>"  data-value="<?= esc($option); ?>"  >
            <span class="mdc-list-item__ripple"></span>
    <span  class="mdc-list-item__text"><?= esc($option); ?></span>
        </li>
        <?php $index++; ?>
    <?php endforeach; ?>
</ul>
								 
								</div>
							 </div>
						 
						 <!----------------------------------------------------------------------------------------------------------------------->
				    <?php elseif ($ui_type === 'Hue Slider Custom' ||$ui_type === 'Hue Slider') : ?>
				   <?php 
$ui_step_value = explode(',', $component['slider_step_value']);
$ui_range_min_value = explode(',', $component['display_value']['min']);
$ui_range_max_value = explode(',', $component['display_value']['max']);

$ui_step = isset($position) && $position !== false ? $ui_step_value[$position] : '';
$ui_range_min = isset($position) && $position !== false ? $ui_range_min_value[$position] : '';
$ui_range_max = isset($position) && $position !== false ? $ui_range_max_value[$position] : '';
$ui_cmd_value = $ui_range_min;

// Ensure $value is within the range
$value = isset($params[$component['default_name']]) ? $params[$component['default_name']] : $ui_range_min;
$value = max($ui_range_min, min($ui_range_max, $value));
?>
<div style="display: flex; align-items: center; justify-content: space-between;">
    <div class="label-container">
        <label class="mdc-typography--subtitle2" for="<?= esc($component['default_name']); ?>">
            <?= esc($component['default_name']); ?>
        </label>
    </div>
    <div class="colorPaletteBar" 
         data-min="<?= esc($ui_range_min); ?>" 
         data-max="<?= esc($ui_range_max); ?>" 
         data-value="<?= esc($value); ?>">
        <div class="selector" data-slider-id="<?= esc($component['default_name']); ?>"></div>
    </div>
    <span style="margin:15px"> </span>
</div>
<!----------------------------------------------------------------------------------------------------------------------->
					<?php elseif ($ui_type === 'display'): ?>
		<div class="mdc-text-field mdc-text-field--filled <?= $ui_permission== 'Read' ? 'mdc-text-field--disabled' : ''; ?>">
							<span class="mdc-text-field__ripple"></span>
							<input class="mdc-text-field__input display-input" 
								   type="text" 
								   id="<?= esc($component['default_name']); ?>" 
								   aria-labelledby="<?= esc($component['default_name']); ?>-label" 
								   <?= $ui_permission != 'Write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?> 
								   value="<?= esc($ui_cmd_value); ?>">
							<span class="mdc-floating-label" id="<?= esc($component['default_name']); ?>-label"><?= esc($component['default_name']); ?></span>
						</div>
						<!----------------------------------------------------------------------------------------------------------------------->
					<?php elseif ($ui_type === 'Text'): ?>
					 
<span style="z-index:5;margin:20px;margin-right:8px" class="mdc-floating-label" id="<?= esc($component['default_name']); ?>-label"><?= esc($component['default_name']); ?></span>
        <div style="background-color:#f0f1ff" class="mdc-text-field mdc-text-field--textarea mdc-text-field--filled<?= $ui_permission == 'Read' ? 'mdc-text-field--disabled' : ''; ?>"  >
							 <span class="mdc-text-field__ripple"></span>
			  <?php if(isset($params[$component['default_name']])): ?>
			 <?php if ($component['ui_description'] === 'Read/Write'): ?>
				<input type="text" style="background-color:lightgrey; width: 45%; height: 60px; box-sizing: border-box; border: none; padding: 10px; font-size: 16px;" name="hex_value[]" value="<?php echo $params[$component['default_name']]; ?>" />			
			 <?php else: ?>
		     <text style="z-index:1;margin-left:30%" class="mdc-text-field__input" 
				id="<?= esc($component['default_name']); ?>" 
			aria-labelledby="<?= esc($component['default_name']); ?>-label" 
			 <?= $ui_permission != 'write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?>
					 rows="3"><?= esc($params[$component['default_name']]); ?></text>
					  <?php endif; ?>
					   <?php else: ?>
				  <?php if ($component['ui_description'] === 'Read/Write'): ?>
				<input type="text" style="background-color:lightgrey; width: 45%; height: 60px; box-sizing: border-box; border: none; padding: 10px; font-size: 16px;" name="hex_value[]" value="<?php echo $component['asperui_value']; ?>" />	
			 <?php else: ?>
		     <text style="z-index:1;margin-left:30%" class="mdc-text-field__input" 
				id="<?= esc($component['default_name']); ?>" 
			aria-labelledby="<?= esc($component['default_name']); ?>-label" 
			 <?= $ui_permission != 'write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?>
					 rows="3"><?= esc($component['asperui_value']); ?></text>
					  <?php endif; ?>
					    <?php endif; ?>
						</div>
					  <!----------------------------------------------------------------------------------------------------------------------->
                      <?php elseif ($ui_type === 'Trigger'): ?>
<div style="display: flex; align-items: center; justify-content: space-between;">
    <div class="label-container">
        <label class="mdc-typography--subtitle2" for="<?= esc($component['default_name']); ?>">
            <?= esc($component['default_name']); ?>
        </label>
    </div>
    <div class="button-wrapper">
    <?php
    if(empty($component['asperui_values'])){
        $as_per_ui = " ";
    }else{
     $as_per_ui = $component['asperui_values'][0];
     //$as_per_ui_value = $as_per_ui[0];
    }
      ?>  
        <button class="round-button trigger-button" data-trigger-name="<?= esc($component['default_name']); ?>" data-switch-id=<?= $as_per_ui; ?>></button>
    </div>
</div>
<!----------------------------------------------------------------------------------------------------------------------->
					 <?php elseif ($ui_type === 'Button'): ?>
                         <div class="button-container" style="margin-top: 20px;">
                             <button class="mdc-button mdc-button--raised dynamic-action-button" data-action="<?= esc($component['asperui_value']); ?>">
                             <span class="mdc-button__label"><?= esc($component['default_name']); ?></span>
                             </button>
                         </div>	
          
          
   <!----------------------------------------------------------------------------------------------------------------------->
          
          
         
    								 
                    <?php endif; ?>	
                </div>
            </div>
           <?php elseif ($component['Field_description'] === "Trigger"): ?>
     
          <?php
$cmd_defaultvalue = explode(',', $component['default_name']);
$cmd_uitype = explode(',', $component['ui_type']);
      
 
foreach ($cmd_defaultvalue as $index => $default_value) {
    
    if (trim($default_value) === '') {
        continue;  

    }
    $default_value = isset($cmd_defaultvalue[$index]) ? $cmd_defaultvalue[$index] : '';
    $uitype = isset($cmd_uitype[$index]) ? $cmd_uitype[$index] : '';
                    
    switch ($uitype) {

        case 'Button':
         echo ' <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">';
   echo '  <div class="mdc-card" style="width:100%;margin-top:30px;margin-bottom:30px;background-color:#f1f0ff;padding:16px;">';
            echo '<button  type="button" class="mdc-button mdc-button--raised dynamic-action-button" style="width: 100px; height: 50px;font-size:15px;border:none">' . htmlspecialchars(trim($default_value)) . '</button>';
             echo '</div>';
              echo '</div>';
            break;

            case 'Auto Trigger with Timer':
           
            break;

        default:
            echo ' ';
            break;
    }
    
    
}
?>
        </text>
    </div>
    		 
                 
               <?php endif; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
             <?php endif; ?>	
                <?php endforeach; ?>
                <?php endif; ?>	
                <?php endforeach; ?>
                <!--div style="width:80%;height:40px;text-align:center;padding:15px;background-color:#f0f1ff;font-size:20px;margin-left:5px;border-bottom:0.5px solid grey;border-radius:10px"-->
 <!--?php echo "Device ID : ".$node2; ?>
</div-->
                <?php else: ?>	
                    
                    <?php
			usort($components, function($a, $b) {
				return $a['ui_sequence_no'] <=> $b['ui_sequence_no'];
			});
			?>
 
      <?php foreach ($components as $component): ?>
    <?php
    // Extract the field descriptions and UI types
    $cmd_names = explode(',', $component['cmd_fielddescription']);
    $ui_types = explode(',', $component['cmd_uitype']);
    $ui_permissions = explode(',', $component['cmd_uidescriptioninout']);
    $ui_datatypes = explode(',', $component['cmd_datatype']);
    $multi_ui =  $component['cmd_multipleui'];
    $trigger_ui =  $component['cmd_triggerui'];
    $page =  $component['page_names'];

    // Command to find, dynamically based on application logic
    $command_to_find = "Data"; // Change this as needed

    // Find the position of the command in the command list
    $position = array_search($command_to_find, $cmd_names);

    // Initialize variables to store UI type and other parameters, defaulting to an empty string if not found
    $ui_type = '';
    $ui_permission = '';
    $parameter_name = '';
    $parameter_id = ''; // Assuming this is a unique identifier for each parameter
    $slider_step = '';
    $range_min = '';
    $range_max = '';

    // Retrieve the corresponding UI type and other parameters using the position
    if ($position !== false) {
        $ui_type = isset($ui_types[$position]) ? $ui_types[$position] : '';
        $parameter_name = isset($component['parameter_name']) ? $component['parameter_name'] : '';
        $parameter_id = isset($component['parameter_id']) ? $component['parameter_id'] : '';
        $slider_step = isset($component['cmd_sliderstepvalue']) ? $component['cmd_sliderstepvalue'] : '';
        $range_min = isset($component['cmd_displayvaluemin']) ? $component['cmd_displayvaluemin'] : '';
        $range_max = isset($component['cmd_displayvaluemax']) ? $component['cmd_displayvaluemax'] : '';
    }
    ?>
<?php
 
$params = [];

if (isset($data['node_id']) && $data['node_id'] === $component['node_id']) {
    $params = [];

    foreach ($data as $key => $value) {
        if (is_array($value) && isset($value[$page])) {
            $params = $value[$page];
            break;  
        }
    }

} else {
    echo " ";
}
 ?>


	
                 <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
    <div class="mdc-card" style="margin-top:20px;margin-bottom:20px;background-color:#f1f0ff;padding:16px;">
        <?php if ($ui_type === 'Toggle Switch'): ?>
            <div class="toggle-wrapper">
                <p></p>
                <span class="spacer" style="display: inline-block; width:10px;"></span> <!-- Spacer element -->
                <label class="toggle-label" for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
                <div class="mdc-switch" data-switch-id="<?= esc($component['parameter_name']); ?>">
                    <div class="mdc-switch__track"></div>
                    <div class="mdc-switch__thumb-underlay">
                        <div class="mdc-switch__thumb">
                  <input type="checkbox" id="<?= esc($component['parameter_name']); ?>" class="mdc-switch__native-control" role="switch"
            <?= isset($params[$component['parameter_name']]) && $params[$component['parameter_name']] ? 'checked' : ''; ?>>
                        </div>
                    </div>
                    <div class="mdc-switch__ripple"></div>
                </div>
                <span id="switchValueDisplay-<?= esc($component['parameter_name']); ?>" class="switch-status" style="margin-left: 16px;">
                    <?php
                     
                    if (isset($params[$component['parameter_name']])) {
                       echo isset($params[$component['parameter_name']]) &&  $params[$component['parameter_name']] ? 'ON' : 'OFF'; 
                    } else {
                        echo isset($powerValue) &&  $powerValue ? 'ON' : 'OFF';  
                    }
                    ?>
                </span>
            </div>

<!----------------------------------------------------------------------------------------------------------------------->

 <?php elseif ($ui_type === 'Slider'||$ui_type === 'Brightness Slider'||$ui_type === 'CCT Slider'||$ui_type === 'Saturation Slider' ): ?>
			<?php $ui_step_value=explode(',',$component['cmd_sliderstepvalue']);
				$ui_range_min_value=explode(',',$component['cmd_displayvaluemin']);
                                $ui_range_max_value=explode(',',$component['cmd_displayvaluemax']);
									
		// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_step_value[$position])) {
			$ui_step = $ui_step_value[$position];
		}
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_min_value[$position])) {
			$ui_range_min = $ui_range_min_value[$position];
		}
		
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_max_value[$position])) {
			$ui_range_max = $ui_range_max_value[$position];
                   
			}
			/*$ui_step =0.01;
			$ui_cmd_value = 0.41;
			$ui_range_min = 0.4;
			$ui_range_max = 0.7;*/

/*$ui_step = 1; // 0.01 scaled by 100
$ui_cmd_value = 41; // 0.41 scaled by 100
$ui_range_min = 40; // 0.4 scaled by 100
$ui_range_max = 70; // 0.7 scaled by 100*/

$ui_cmd_value =$ui_range_min ;
				
							?>
							
							
			<div style="display: flex; align-items: center; justify-content: space-between;">
			<div style="flex-grow: 1; display: flex; align-items: center;">
				<!-- Label for the slider -->
			<label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
   
								<!-- Slider Component -->
<div class="mdc-slider" style="flex-grow: 1; margin: 16px;" id="slider-<?= esc($component['parameter_name']); ?>">
    <input class="mdc-slider__input" type="range" min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
           value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
           aria-labelledby="label-<?= esc($component['parameter_name']); ?>"
            oninput="updateSliderValue(this.value,'<?= esc($component['parameter_name']); ?>')"> <!-- Call JavaScript function on input event -->
 
 
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

<?php
 
$value = isset($params[$component['parameter_name']]) ? $params[$component['parameter_name']] : '';
?>

<span id="sliderValueDisplay-<?= esc($component['parameter_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($value); ?></span>
						</div>
						<!----------------------------------------------------------------------------------------------------------------------->

 <?php elseif ($ui_type === 'Airflow Slider' ): ?>
			<?php $ui_step_value=explode(',',$component['cmd_sliderstepvalue']);
				$ui_range_min_value=explode(',',$component['cmd_displayvaluemin']);
                                $ui_range_max_value=explode(',',$component['cmd_displayvaluemax']);
									
		// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_step_value[$position])) {
			$ui_step = $ui_step_value[$position];
		}
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_min_value[$position])) {
			$ui_range_min = $ui_range_min_value[$position];
		}
		
			// Retrieve the corresponding UI type using the position
		if ($position !== false && isset($ui_range_max_value[$position])) {
			$ui_range_max = $ui_range_max_value[$position];
                   
			}
			/*$ui_step =0.01;
			$ui_cmd_value = 0.41;
			$ui_range_min = 0.4;
			$ui_range_max = 0.7;*/

/*$ui_step = 1; // 0.01 scaled by 100
$ui_cmd_value = 41; // 0.41 scaled by 100
$ui_range_min = 40; // 0.4 scaled by 100
$ui_range_max = 70; // 0.7 scaled by 100*/

$ui_cmd_value =$ui_range_min ;
							?>
							
							
			<div style="display: flex; align-items: center; justify-content: space-between;">
			<div style="flex-grow: 1; display: flex; align-items: center;">
				<!-- Label for the slider -->
			<label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
   
								<!-- Slider Component -->
	 <img style="margin-left:10px;width:80px;height:80px" src="http://localhost/factor_17-05/files/home/lowspd.png">
<div class="mdc-slider" style="flex-grow: 1; margin: 16px;" id="slider-<?= esc($component['parameter_name']); ?>">
    <input class="mdc-slider__input" type="range" min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
           value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
           aria-labelledby="label-<?= esc($component['parameter_name']); ?>"
            oninput="updateSliderValue(this.value,'<?= esc($component['parameter_name']); ?>')"> <!-- Call JavaScript function on input event -->
 <?php
 
$value = isset($params[$component['parameter_name']]) ? $params[$component['parameter_name']] : '';
?>
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
 
 <img style="margin-left:10px;width:80px;height:80px" src="http://localhost/factor_17-05/files/home/highspd.png">
<span id="sliderValueDisplay-<?= esc($component['parameter_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($value); ?></span>
						</div>
<!----------------------------------------------------------------------------------------------------------------------->
						
                    <?php elseif ($ui_type === 'Drop Down' && $multi_ui != 'true'): ?>
                    <?php
                    
                    $ui_dropdown2 = $component['cmd_asperui'];  
                    $as_per_ui_array = explode('][', $ui_dropdown2);  
                    $as_per_ui = str_replace(['[', ']'], '', $as_per_ui_array);
                    $ui_dropdown3 = implode(',', $as_per_ui);  
                    $ui_dropdown = explode(',', $ui_dropdown3); 
      
		
	 if ($position !== false && isset($ui_dropdown[$position])) {
            $ui_drop = $ui_dropdown[$position];
			
        }
		 ?>
		 
				 <div style="display: flex; align-items: center; justify-content: space-between;">
				  <div class="label-container">
                        <label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
						</div>
		<div class="mdc-select mdc-select--filled" id="select-<?= esc($component['parameter_name']); ?>">
		<div style="background-color:lightgrey" class="mdc-select__anchor"role="button" aria-haspopup="listbox" aria-expanded="false">
									<span class="mdc-select__ripple"></span>
<span class="mdc-select__selected-text"></span>
					 <span class="mdc-select__dropdown-icon">
		<svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5"focusable="false">$params[$component['parameter_name']]
	 <polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>
	<polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>
				</svg>
				</span>
						</div>
						<?php
 
$value = isset($params[$component['parameter_name']]) ? $params[$component['parameter_name']] : '';
?>
		<div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
								<?php
							   // Assuming $component['ui_options'] is a string of options separated by commas
								$options = explode(',', $component['ui_options']);
								$index = 0; // Initialize a counter variable
								 $selcted_index=0;
								?>
								 
							<ul class="mdc-list" role="listbox">
    <?php $index = 0; ?>
    <?php foreach ($ui_dropdown as $option): ?>
        <?php 
            $isSelected = ($option === $value);
        ?>
        <li  class="mdc-list-item <?= $isSelected ? 'mdc-list-item--selected' : ''; ?>"   role="option"   aria-selected="<?= $isSelected ? 'true' : 'false'; ?>"  data-value="<?= esc($option); ?>"  >
            <span class="mdc-list-item__ripple"></span>
    <span id="selectValueDisplay-<?= esc($component['parameter_name']); ?>" class="mdc-list-item__text"><?= esc($option); ?></span>
        </li>
        <?php $index++; ?>
    <?php endforeach; ?>
</ul>
								 
								</div>
							 </div>
						 
						 <!----------------------------------------------------------------------------------------------------------------------->
				    <?php elseif ($ui_type === 'Hue Slider' && $multi_ui != 'true'): ?>
				   <?php 
$ui_step_value = explode(',', $component['cmd_sliderstepvalue']);
$ui_range_min_value = explode(',', $component['cmd_displayvaluemin']);
$ui_range_max_value = explode(',', $component['cmd_displayvaluemax']);

$ui_step = isset($position) && $position !== false ? $ui_step_value[$position] : '';
$ui_range_min = isset($position) && $position !== false ? $ui_range_min_value[$position] : '';
$ui_range_max = isset($position) && $position !== false ? $ui_range_max_value[$position] : '';
$ui_cmd_value = $ui_range_min;

// Ensure $value is within the range
$value = isset($params[$component['parameter_name']]) ? $params[$component['parameter_name']] : $ui_range_min;
$value = max($ui_range_min, min($ui_range_max, $value));
?>
<div style="display: flex; align-items: center; justify-content: space-between;">
    <div class="label-container">
        <label class="mdc-typography--subtitle2" for="<?= esc($component['parameter_name']); ?>">
            <?= esc($component['parameter_name']); ?>
        </label>
    </div>
    <div class="colorPaletteBar" 
         data-min="<?= esc($ui_range_min); ?>" 
         data-max="<?= esc($ui_range_max); ?>" 
         data-value="<?= esc($value); ?>">
        <div class="selector"></div>
    </div>
    <span><?= esc($value); ?></span>
</div>
<!----------------------------------------------------------------------------------------------------------------------->
					<?php elseif ($ui_type === 'display' && $multi_ui != 'true'): ?>
						<div class="mdc-text-field mdc-text-field--filled <?= $ui_permission== 'Read' ? 'mdc-text-field--disabled' : ''; ?>">
							<span class="mdc-text-field__ripple"></span>
							<input class="mdc-text-field__input display-input" 
								   type="text" 
								   id="<?= esc($component['parameter_name']); ?>" 
								   aria-labelledby="<?= esc($component['parameter_name']); ?>-label" 
								   <?= $ui_permission != 'Write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?> 
								   value="<?= esc($ui_cmd_value); ?>">
							<span class="mdc-floating-label" id="<?= esc($component['parameter_name']); ?>-label"><?= esc($component['parameter_name']); ?></span>
						</div>
						<!----------------------------------------------------------------------------------------------------------------------->
					<?php elseif ($ui_type === 'Text' && $multi_ui != 'true'): ?>
					 
<span style="z-index:5;margin:20px;margin-right:8px" class="mdc-floating-label" id="<?= esc($component['parameter_name']); ?>-label"><?= esc($component['parameter_name']); ?></span>
        <div style="background-color:#f0f1ff" class="mdc-text-field mdc-text-field--textarea mdc-text-field--filled<?= $ui_permission == 'Read' ? 'mdc-text-field--disabled' : ''; ?>"  >
							 <span class="mdc-text-field__ripple"></span>
			  <?php if(isset($params[$component['parameter_name']])): ?>
			 <?php if ($component['cmd_uidescriptioninout'] === 'Read/Write'): ?>
				<input type="text" style="background-color:lightgrey; width: 45%; height: 60px; box-sizing: border-box; border: none; padding: 10px; font-size: 16px;" name="hex_value[]" value="<?php echo $params[$component['parameter_name']]; ?>" />			
			 <?php else: ?>
		     <text style="z-index:1;margin-left:30%" class="mdc-text-field__input" 
				id="<?= esc($component['parameter_name']); ?>" 
			aria-labelledby="<?= esc($component['parameter_name']); ?>-label" 
			 <?= $ui_permission != 'write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?>
					 rows="3"><?= esc($params[$component['parameter_name']]); ?></text>
					  <?php endif; ?>
					   <?php else: ?>
				  <?php if ($component['cmd_uidescriptioninout'] === 'Read/Write'): ?>
				<input type="text" style="background-color:lightgrey; width: 45%; height: 60px; box-sizing: border-box; border: none; padding: 10px; font-size: 16px;" name="hex_value[]" value="<?php echo $component['cmd_valueinhexmin']; ?>" />	
			 <?php else: ?>
		     <text style="z-index:1;margin-left:30%" class="mdc-text-field__input" 
				id="<?= esc($component['parameter_name']); ?>" 
			aria-labelledby="<?= esc($component['parameter_name']); ?>-label" 
			 <?= $ui_permission != 'write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?>
					 rows="3"><?= esc($component['cmd_valueinhexmin']); ?></text>
					  <?php endif; ?>
					    <?php endif; ?>
						</div>
					  <!----------------------------------------------------------------------------------------------------------------------->
					 <?php elseif ($ui_type === 'Trigger' && $multi_ui != 'true'): ?>
						  <div style="display: flex; align-items: center; justify-content: space-between;">
						 <div class="label-container">
                      <label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
						</div>
							
							<div class="button-wrapper">
							<button class="round-button trigger-button" data-trigger-name="<?= esc($component['parameter_name']); ?>"></button>
						</div>
						</div>
<!----------------------------------------------------------------------------------------------------------------------->
					 <?php elseif ($ui_type === 'Button' && $multi_ui != 'true'): ?>
                         <div class="button-container" style="margin-top: 20px;">
                             <button class="mdc-button mdc-button--raised dynamic-action-button" data-action="<?= esc($component['ui_action']); ?>">
                             <span class="mdc-button__label"><?= esc($component['parameter_name']); ?></span>
                             </button>
                         </div>	
          
          
   <!----------------------------------------------------------------------------------------------------------------------->
          
          
          <?php elseif ($trigger_ui === 'true' && $multi_ui !== 'true'): ?>
        
     
          <?php
$cmd_defaultvalue = explode(',', $component['cmd_defaultvalue']);
$cmd_uitype = explode(',', $component['cmd_uitype']);
      
 
foreach ($cmd_defaultvalue as $index => $default_value) {
    
    if (trim($default_value) === '') {
        continue;  

    }
    $default_value = isset($cmd_defaultvalue[$index]) ? $cmd_defaultvalue[$index] : '';
    $uitype = isset($cmd_uitype[$index]) ? $cmd_uitype[$index] : '';
                    
    echo '<div style="margin-bottom: 15px;">';
    switch ($uitype) {

        case 'Button':
            echo '<button  type="button" class="mdc-button mdc-button--raised dynamic-action-button" style="width: 100px; height: 50px;font-size:15px;border:none">' . htmlspecialchars(trim($default_value)) . '</button>';
            break;

            case 'Auto Trigger With Timer':
           
            break;

        default:
            echo 'No UI';
            break;
    }
    
    echo '</div>';
}
?>
        </text>
    </div>
    		  <!----------------------------------------------------------------------------------------------------------------------->
             <?php elseif ($multi_ui === 'true'): ?>
        
    <span style="z-index:5;margin:20px;margin-right:8px" class="mdc-floating-label" id="<?= esc($component['parameter_name']); ?>-label"><?= esc($component['parameter_name']); ?></span>
    <div style="background-color:#f0f1ff" class="mdc-text-field mdc-text-field--textarea mdc-text-field--filled<?= $ui_permission == 'Read' ? 'mdc-text-field--disabled' : ''; ?>" >
        <span class="mdc-text-field__ripple"></span>
        <text style="z-index:1;margin-left:35%" class="mdc-text-field__input" 
            id="<?= esc($component['parameter_name']); ?>" 
            aria-labelledby="<?= esc($component['parameter_name']); ?>-label" 
            <?= $ui_permission != 'write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?>
            rows="3">
          <?php
$cmd_defaultvalue = explode(',', $component['cmd_defaultvalue']);
$cmd_valueinhexmin = explode(',', $component['cmd_valueinhexmin']);
$cmd_displayvaluemin = explode(',', $component['cmd_displayvaluemin']);
$cmd_displayvaluemax = explode(',', $component['cmd_displayvaluemax']);
$cmd_uitype = explode(',', $component['cmd_uitype']);
$ui_dropdown2 = $component['cmd_asperui'];  
$uidescription = explode(',',$component['cmd_uidescriptioninout']);
                    
                    $as_per_ui = explode(',', $ui_dropdown2); 
                    $ui_dropdown3 = implode(',', $as_per_ui);  
                    $ui_dropdown = explode(',', $ui_dropdown3); 
                    
      
 
foreach ($cmd_defaultvalue as $index => $default_value) {
    
    if (trim($default_value) === '') {
        continue;  
    }
    $default_value = isset($cmd_defaultvalue[$index]) ? $cmd_defaultvalue[$index] : '';
    $displaymin_value = isset($cmd_displayvaluemin[$index]) ? $cmd_displayvaluemin[$index] : '';
    $displaymax_value = isset($cmd_displayvaluemax[$index]) ? $cmd_displayvaluemax[$index] : '';
    $hex_value = isset($cmd_valueinhexmin[$index]) ? $cmd_valueinhexmin[$index] : '';
    $uitype = isset($cmd_uitype[$index]) ? $cmd_uitype[$index] : '';
    $cmd_ui_dropdown2 = isset($ui_dropdown[$index]) ? $ui_dropdown[$index] : '';
     $cmd_uidescription = isset($uidescription[$index]) ? $uidescription[$index] : '';
      
      $as_per_ui_array = explode('][', $cmd_ui_dropdown2);  
                    $cmd_ui_dropdown = str_replace(['[', ']'], '', $as_per_ui_array);
                    
                    
    echo '<div style="margin-bottom: 15px;">';
 
    switch ($uitype) {
       case 'Text':
           echo '<div style="display: flex; align-items: flex-start;">';
echo '<div style="width: 220px; height: 60px; background-color: grey; display: flex; align-items: center; justify-content: center; box-sizing: border-box; overflow: hidden;">';
echo '<span style="padding: 5px; display: block; width: 100%; box-sizing: border-box; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; font-size: 16px;">' . htmlspecialchars(trim($default_value)) . ' (' . htmlspecialchars(trim($displaymin_value)) . '-' . htmlspecialchars(trim($displaymax_value)) . ')</span>';
echo '</div>';
if(htmlspecialchars(trim($cmd_uidescription)) === 'Read/Write'){
echo '<div style="margin-left: 10px;">';
echo '<input type="text" style="width: 45%; height: 60px; box-sizing: border-box; border: none; padding: 10px; font-size: 16px;" name="hex_value[]" value="' . htmlspecialchars($hex_value) . '" />';
echo '</div>';    
}else{
echo '<div style="margin-left: 10px;margin-top:15px">';
echo '<span style="width: 45%; height: 60px;align-items: center; justify-content: center;background-color:white; border:none; padding: 15px; ">' . htmlspecialchars(trim($hex_value)) . '</span>';
echo '</div>';    
}
echo '</div>';
            break;
 
        case 'Button':
            echo '<button  type="button" class="mdc-button mdc-button--raised dynamic-action-button" style="width: 100px; height: 50px;font-size:15px;border:none">' . htmlspecialchars(trim($default_value)) . '</button>';
            break;

        case 'Toggle Switch':
 echo '<style>
    .toggle-switch-container { display: flex; align-items: center; gap: 10px; }
    .toggle-switch { position: relative; display: inline-block; width: 50px; height: 25px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 25px; }
    .slider:before { position: absolute; content: ""; height: 21px; width: 21px; border-radius: 50%; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
    input:checked + .slider {  background-color: #651FFF; }
    input:checked + .slider:before { transform: translateX(25px); }
    .label-box { padding: 5px; display: block;box-sizing: border-box; width:20% }
</style>';
      $isChecked = true;
    $labelText = htmlspecialchars(trim($default_value));  
    echo '<div class="toggle-switch-container">';
    echo '<label class="label-box">' . $labelText . '</label>';
    echo '<div class="toggle-switch"><input type="checkbox" id="toggleSwitch" ' . ($isChecked ? 'checked' : '') . ' /><label for="toggleSwitch" class="slider"></label></div>';
    echo '</div>';
    break;
    
    
       case 'Slider':
    echo '<div style="display: flex; align-items: center; justify-content: space-between; width: 40%;">';
     echo '<label style="padding: 5px; display: block; width: 100%; box-sizing: border-box; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; font-size: 16px;">' .htmlspecialchars(trim($default_value)) . '</label>';
    echo '<span style="flex: 0 0 auto;">' .  htmlspecialchars(trim($displaymin_value)). '</span>'; 
    echo '<input type="range" min="' .  htmlspecialchars(trim($displaymin_value)). '" max="' . htmlspecialchars(trim($displaymax_value)) . '" value="' . htmlspecialchars(trim($hex_value)) . '" style="flex: 1;"/>';

    echo '<span style="flex: 0 0 auto;">' .htmlspecialchars(trim($displaymax_value)) . '</span>'; 
    echo '</div>';
    break;

        case 'Drop Down':
            echo '<label style="padding: 5px; display: block; width: 100%; box-sizing: border-box; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; word-wrap: break-word; font-size: 16px;">' . htmlspecialchars(trim($default_value)) . ':</label>';
            echo '<select style="width:30%;height:30px">';
            foreach ($cmd_ui_dropdown as $option) {
                echo '<option>' . htmlspecialchars(trim($option)) . '</option>';
            }
            echo '</select>';
            break;

        case 'hue-slider':
            echo '<label>' . htmlspecialchars(trim($default_value)) . ':</label>';
            echo '<input type="color" value="' . htmlspecialchars($hex_value) . '" />';
            break;
            
            case 'Auto Trigger With Timer':
           
            break;

        default:
            echo 'No UI';
            break;
    }
    
    echo '</div>';
}
?>
        </text>
    </div>
    								 
                    <?php endif; ?>	
                </div>
            </div>
                <?php endforeach; ?>                
                <?php endif; ?>	
            </div>
        </div>
      </div>
</div>
<div style="width:80%;height:40px;text-align:center;padding:15px;background-color:#f0f1ff;font-size:20px;margin-left:5px;border-bottom:0.5px solid grey;border-radius:10px">
<?php 
    if (isset($node2) && !empty($node2)) {
        echo "Device ID : " . $node2; 
    } else {
        echo "Device ID : Node ID is not selected";  
    }
 ?>
</div>



<script>
const userId = <?php echo isset($userID) ? json_encode($userID) : 'null'; ?>;
console.log("Debug: userId =", userId);
</script>




<script>
// Ensure userId, deviceId, modelName are defined first
//var userId = <?php echo isset($userID) ? json_encode($userID) : 'null'; ?>;
const deviceId = "<?php echo isset($node2) ? $node2 : ''; ?>";
const modelName = "<?php echo isset($modelNumber) ? $modelNumber : ''; ?>";

document.addEventListener('DOMContentLoaded', function() {
        console.log("User ID:", userId);
        console.log("Device ID:", deviceId);
        console.log("Model Name:", modelName);

   // initializeAllComponents();

    initializeTopAppBar();
    initializeSwitches();
    initializeSliders();
    initializeSelects();
    initializeTextFields();
    initializeTriggerButtons();
    initializeColorPaletteBar();
    initializeDynamicActionButtons();


});

/*function initializeAllComponents() {
    initializeTopAppBar();
    initializeSwitches();
    initializeSliders();
    initializeSelects();
    initializeTextFields();
    initializeTriggerButtons();
    initializeColorPaletteBar();
    initializeDynamicActionButtons();
}
*/





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

        const switchInput = sw.querySelector('.mdc-switch__native-control');
        if (!switchInput) return;

        switchInput.addEventListener('change', function() {
            const switchId = sw.getAttribute('data-switch-id');
            const switchStatusDisplay = document.getElementById(`switchValueDisplay-${switchId}`);

            if (switchStatusDisplay) {
                switchStatusDisplay.textContent = this.checked ? 'ON' : 'OFF';
            }

            sendDataToServer({
                value: this.checked ? '1' : '0',
                parameterName: switchId,
                user_id: userId,
                device_id: deviceId,
                modelName: modelName
            });
        });
    });
}

function initializeSliders() {
    document.querySelectorAll('.mdc-slider').forEach((sliderElement) => {
        const slider = new mdc.slider.MDCSlider(sliderElement);
        const paramName = sliderElement.id.replace('slider-', '');

        slider.listen('MDCSlider:input', () => {
            const value = slider.getValue();
            const displayElement = document.getElementById(`sliderValueDisplay-${paramName}`);
            if (displayElement) displayElement.textContent = value;

            sendDataToServer({
                value: value,
                parameterName: paramName,
                user_id: userId,
                device_id: deviceId,
                modelName: modelName
            });
        });
    });
}

function initializeSelects() {
    document.querySelectorAll('.mdc-select').forEach((selectElement) => {
        const select = mdc.select.MDCSelect.attachTo(selectElement);
        const paramName = selectElement.id.replace('select-', '');

        select.listen('MDCSelect:change', () => {
            const displayElement = document.getElementById(`selectValueDisplay-${paramName}`);
            if (displayElement) displayElement.textContent = select.value;

            sendDataToServer({
                value: select.value,
                parameterName: paramName,
                user_id: userId,
                device_id: deviceId,
                modelName: modelName
            });
        });
    });
}

function initializeTextFields() {
    document.querySelectorAll('.mdc-text-field').forEach((textField) => {
        mdc.textField.MDCTextField.attachTo(textField);
    });
}

function initializeTriggerButtons() {
    document.querySelectorAll('.trigger-button').forEach((button) => {
        button.addEventListener('click', function() {
            const value = button.dataset.switchId;
            const triggerid = button.dataset.triggerName;

            sendDataToServer({
                value: value,
                parameterName: triggerid,
                user_id: userId,
                device_id: deviceId,
                modelName: modelName
            });
        });
    });
}

function initializeColorPaletteBar() {
    document.querySelectorAll('.colorPaletteBar').forEach(function(bar) {
        const selector = bar.querySelector('.selector');
        if (!selector) return;

        const min = parseFloat(bar.getAttribute('data-min')) || 0;
        const max = parseFloat(bar.getAttribute('data-max')) || 100;
        const value = parseFloat(bar.getAttribute('data-value')) || min;
        const sliderId = selector.getAttribute('data-slider-id');
        let isDragging = false;

        function updateSelectorPosition(newLeft) {
            const maxLeft = bar.offsetWidth - selector.offsetWidth;
            const clampedLeft = Math.max(0, Math.min(newLeft, maxLeft));
            selector.style.left = `${clampedLeft}px`;
            changeSelectorColor(selector, clampedLeft, bar.offsetWidth, sliderId);
        }

        function getPositionFromValue(val) {
            const width = bar.offsetWidth;
            return ((val - min) / (max - min)) * width;
        }

        updateSelectorPosition(getPositionFromValue(value));

        bar.addEventListener('click', function(e) {
            if (!isDragging) {
                const rect = bar.getBoundingClientRect();
                const x = e.clientX - rect.left;
                updateSelectorPosition(x);
            }
        });

        selector.addEventListener('mousedown', function(e) {
            isDragging = true;
            e.preventDefault();

            const startX = e.clientX;
            const startLeft = parseFloat(selector.style.left || '0');

            function onMouseMove(e) {
                const dx = e.clientX - startX;
                updateSelectorPosition(startLeft + dx);
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

function changeSelectorColor(selector, x, width, sliderId) {
    const colorTransitionEnd = width * 0.895;
    let color;

    if (x > colorTransitionEnd) {
        color = '00';
    } else {
        const hue = (x / colorTransitionEnd) * 360;
        if (hue < 30) color = '03';
        else if (hue < 60) color = '02';
        else if (hue < 160) color = '06';
        else if (hue < 190) color = '04';
        else if (hue < 270) color = '05';
        else if (hue < 300) color = '01';
        else if (hue < 350) color = '03';
        else color = '00';
    }

    selector.style.backgroundColor = color;

    if (sliderId) {
        sendDataToServer({
            value: color,
            parameterName: sliderId,
            user_id: userId,
            device_id: deviceId,
            modelName: modelName
        });
    }
}

function initializeDynamicActionButtons() {
    document.querySelectorAll('.dynamic-action-button').forEach((button) => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            if (action && action.startsWith('http')) {
                window.location.href = action;
            } else if (action && typeof window[action] === 'function') {
                window[action]();
            }
        });
    });
}

function sendDataToServer(data) {
    if (!data.parameterName || !userId || !deviceId) {
        console.error('Missing required data for server request');
        return;
    }

    console.log('Sending data to server:', data);

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
    .then(responseText => {
        console.log('Server response:', responseText);
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}

// Initialize all MDC components
mdc.autoInit();
</script>




</body>
</html>
