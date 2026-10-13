<?php

usort($components, function($a, $b) { return $a['ui_sequence_no'] <=> $b['ui_sequence_no']; });

?>






<?php 
$jsonResponse = $response;


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
 
//$params = [];

/*$data = json_decode($jsonResponse, true);

if (isset($data['node_id']) && $data['node_id'] === $component['node_id']) {

   // $params = [];

    foreach ($data as $key => $value) {
        if (is_array($value) && isset($value[$page])) {
            $params = $value[$page];
            break;  
        }
    }

} else {
    echo " ";
}*/




if (!isset($previousParams)) {
    $previousParams = $params; // Initialize $previousParams with the current $params value
}

$data = json_decode($jsonResponse, true);

if (isset($data['node_id']) && $data['node_id'] === $component['node_id']) {
    foreach ($data as $key => $value) {
        if (is_array($value) && isset($value[$page])) {
            $params = $value[$page];
            break;
        }
    }
} else {
    $params = $previousParams; // Use the previous $params value if node_id doesn't match
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



 <?php elseif ($ui_type === 'Slider'||$ui_type === 'Brightness Slider'||$ui_type === 'CCT Slider'||$ui_type === 'Saturation Slider'): ?>
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
  <!----------------------------------------------------------------------------------------------------------------------->
								<!-- Slider Component -->
<div class="mdc-slider" style="flex-grow: 1; margin: 16px;" id="slider-<?= esc($component['parameter_name']); ?>">
    <input class="mdc-slider__input" type="range" min="<?= esc($ui_range_min); ?>" max="<?= esc($ui_range_max); ?>"
           value="<?= esc($ui_cmd_value); ?>" step="<?= esc($ui_step); ?>"
           aria-labelledby="label-<?= esc($component['parameter_name']); ?>"
            oninput="updateSliderValue(this.value,'<?= esc($component['parameter_name']); ?>')"> <!-- Call JavaScript function on input event -->
 <?php if(isset($params[$component['parameter_name']])): ?>
 
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


<span id="sliderValueDisplay-<?= esc($component['parameter_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($params[$component['parameter_name']]); ?></span>
 
					   <?php else: ?>
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


<span id="sliderValueDisplay-<?= esc($component['parameter_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($ui_cmd_value); ?></span>
 
					     <?php endif; ?>
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
						  <div style="display: flex; align-items: center; justify-content: space-between;">
						 <div class="label-container">
                         <label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
						  </div>
						   <div class="colorPaletteBar">
							<div class="selector"></div>
						   </div>
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
					 <?php elseif ($ui_type === 'trigger' && $multi_ui != 'true'): ?>
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
             <?php elseif ($multi_ui === 'true' || $trigger_ui === 'true' ): ?>
        
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
            </div>
        </div>
      </div>
</div>
<div style="width:80%;height:40px;text-align:center;padding:15px;background-color:#f0f1ff;font-size:20px;margin-left:5px;border-bottom:0.5px solid grey;border-radius:10px">
 <?php echo "Device ID : ".$component['node_id']; ?>
</div>
<script>
    var userId = <?php echo json_encode($userID); ?>;
    console.log(userId);
</script>







</script>

<script>
 function updateSliderValue(value) {
      const sliderElement = document.getElementById('slider-' + paramName);
    if (sliderElement) {
        const sliderInput = sliderElement.querySelector('.mdc-slider__input');
        const valueDisplay = sliderElement.querySelector('.current-value'); // Assuming you have a display element with this class
        if (sliderInput && valueDisplay) {
            sliderInput.value = value;
            valueDisplay.textContent = value; // Update the display element
            console.log(`Slider Value for ${paramName}: ${value}`); // Log the updated value with parameter name
        }
    }
}



 
</script>





	    <!-- Include Material Design switch JavaScript -->
<script src="https://unpkg.com/@material/switch@latest/dist/mdc.switch.js"></script>
<!-- Required Material Web JavaScript library -->
<script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/11.0.0/material-components-web.min.js"></script>
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


/*function initializeSliders() {
    document.querySelectorAll('.mdc-slider').forEach((sliderElement) => {
        const slider = new mdc.slider.MDCSlider(sliderElement);

        // Attach input event listeners
        slider.listen('MDCSlider:input', (event) => {
            let value = slider.getValue();
            const paramName = sliderElement.id.replace('slider-', ''); // Extract the unique parameter name
            const displayElement = document.getElementById(`sliderValueDisplay-${paramName}`);
            console.log(`${paramName}: ${value}`);

          
            if (displayElement) {
                displayElement.textContent = value; // Update the display with the slider's current value
            }

   
        });
    });
}
 */

function initializeSwitches() {
    document.querySelectorAll('.mdc-switch').forEach((sw) => {
        if (!sw.classList.contains('mdc-switch--checked')) { // Check if not already initialized
            const switchControl = new mdc.switchControl.MDCSwitch(sw);
        }
        // Event listener to update switch status text
        sw.querySelector('.mdc-switch__native-control').addEventListener('change', function() {
			  const switchId = sw.getAttribute('data-switch-id');
            console.log(`${switchId}  :${this.checked ? 'ON' : 'OFF'}`);
            const switchStatusDisplay = sw.nextElementSibling; // Assuming the status span immediately follows the switch
            switchStatusDisplay.textContent = this.checked ? 'ON' : 'OFF';


           // Construct the data to send
            const data = {
                value: this.checked,
                parameterName: switchId,
                user_id:userId
            };

            // Make the fetch request
            fetch("https://efactor.tech/v6/studio_mqtt.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                // Check if the response is ok
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                // Return the text of the response
                return response.text();
            })
            .then(responseText => {
               