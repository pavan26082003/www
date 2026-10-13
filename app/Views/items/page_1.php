<?php
// Sort the $components array based on 'ui_sequence_no'
usort($components, function($a, $b) {
    return $a['ui_sequence_no'] <=> $b['ui_sequence_no'];
});
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

    // Command to find, dynamically based on application logic
    $command_to_find = "Data"; // Change this as needed

    // Find the position of the command in the command list
    $position = array_search($command_to_find, $cmd_names);

    // Initialize variables to store UI type and other parameters, defaulting to an empty string if not found
    $ui_type = '';
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







	
  <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
    <div class="mdc-card" style="padding:16px;">
        <?php if ($ui_type === 'Toggle Switch'): ?>
            <div class="toggle-wrapper">
                <p></p>
                <span class="spacer" style="display: inline-block; width:10px;"></span> <!-- Spacer element -->
                <label class="toggle-label" for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
                <div class="mdc-switch" data-switch-id="<?= esc($component['parameter_name']); ?>">
                    <div class="mdc-switch__track"></div>
                    <div class="mdc-switch__thumb-underlay">
                        <div class="mdc-switch__thumb">
                            <input type="checkbox" id="<?= esc($component['parameter_name']); ?>" class="mdc-switch__native-control" role="switch" <?= $powerValue ? 'checked' : ''; ?>>
                            
                        </div>
                    </div>
                    <div class="mdc-switch__ripple"></div>
                </div>
                <span class="switch-status" style="margin-left: 16px;"><?= $powerValue ? 'ON' : 'OFF'; ?></span>
            </div>


                    <?php elseif ($ui_type === 'Slider'||$ui_type === 'Brightness Slider'||$ui_type === 'CCT Slider'||$ui_type === 'Saturation Slider'): ?>
						<?php
							 			 $ui_step_value=explode(',',$component['cmd_sliderstepvalue']);
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


<span id="sliderValueDisplay-<?= esc($component['parameter_name']); ?>" class="mdc-typography--subtitle2" style="margin-left: 23px;"> <?= esc($ui_cmd_value); ?></span>
 
						</div>

						
                    <?php elseif ($ui_type === 'Drop Down'): ?>
					  <div style="display: flex; align-items: center; justify-content: space-between;">
					    <div class="label-container">
                        <label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
						</div>
							<div class="mdc-select mdc-select--filled">
								<div class="mdc-select__anchor"role="button" aria-haspopup="listbox" aria-expanded="false">
									<span class="mdc-select__ripple"></span>
									<span class="mdc-select__selected-text"></span>
									<span class="mdc-select__dropdown-icon">
										<svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5"focusable="false">
										  <polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>
										 <polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>
										</svg>
									</span>
									<span class="mdc-line-ripple"></span>
								</div>
								<div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
								<?php
							   // Assuming $component['ui_options'] is a string of options separated by commas
								$options = explode(',', $component['ui_options']);
								$index = 0; // Initialize a counter variable
								 $selcted_index=0;
								?>
							   <ul class="mdc-list" role="listbox">
							   <?php foreach ($options as $option): ?>
										<li class="mdc-list-item <?= ($index === $selcted_index) ? 'mdc-list-item--selected' : ''; ?>" role="option" aria-selected="<?= ($index ===  $selcted_index) ? 'true' : 'false'; ?>" data-value="<?= esc($option); ?>">
											<span class="mdc-list-item__ripple"></span>
											<span class="mdc-list-item__text"><?= esc($option); ?></span>
										</li>
										<?php $index++; // Increment the counter ?>
									<?php endforeach; ?>
								</ul>
								</div>
							 </div>
						 </div>
						 
				    <?php elseif ($ui_type === 'Hue Slider'): ?>
						  <div style="display: flex; align-items: center; justify-content: space-between;">
						 <div class="label-container">
                         <label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
						  </div>
						   <div class="colorPaletteBar">
							<div class="selector"></div>
						   </div>
						  </div>

					<?php elseif ($ui_type === 'display'): ?>
						<div class="mdc-text-field mdc-text-field--filled <?= $ui_permission== 'Read' ? 'mdc-text-field--disabled' : ''; ?>">
							<span class="mdc-text-field__ripple"></span>
							<input class="mdc-text-field__input display-input" 
								   type="text" 
								   id="<?= esc($component['parameter_name']); ?>" 
								   aria-labelledby="<?= esc($component['parameter_name']); ?>-label" 
								   <?= $ui_permission != 'Write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?> 
								   value="<?= esc($ui_cmd_value); ?>">
							<span class="mdc-floating-label" id="<?= esc($component['parameter_name']); ?>-label"><?= esc($component['parameter_name']); ?></span>
							<span class="mdc-line-ripple"></span>
						</div>
					<?php elseif ($ui_type === 'Text(Default)'): ?>
						<div class="mdc-text-field mdc-text-field--textarea mdc-text-field--filled<?= $ui_permission == 'Read' ? 'mdc-text-field--disabled' : ''; ?>" style="height: auto;">
							 <span class="mdc-text-field__ripple"></span>
						   <textarea class="mdc-text-field__input" 
									  id="<?= esc($component['parameter_name']); ?>" 
									  aria-labelledby="<?= esc($component['parameter_name']); ?>-label" 
									  <?= $ui_permission != 'write' && $ui_permission != 'Read/Write' ? 'disabled' : ''; ?>
									  rows="3"><?= esc($ui_cmd_value); ?></textarea>
							<span class="mdc-floating-label" id="<?= esc($component['parameter_name']); ?>-label"><?= esc($component['parameter_name']); ?></span>
							<span class="mdc-line-ripple"></span>
						</div>
					 
					 <?php elseif ($ui_type === 'trigger'): ?>
						  <div style="display: flex; align-items: center; justify-content: space-between;">
						 <div class="label-container">
                        <label class="mdc-typography--subtitle2"  for="<?= esc($component['parameter_name']); ?>"><?= esc($component['parameter_name']); ?></label>
						</div>
							
							<div class="button-wrapper">
							<button class="round-button trigger-button" data-trigger-name="<?= esc($component['parameter_name']); ?>"></button>
						</div>
						</div>

					 <?php elseif ($ui_type === 'button'): ?>
                         <div class="button-container" style="margin-top: 20px;">
                             <button class="mdc-button mdc-button--raised dynamic-action-button" data-action="<?= esc($component['ui_action']); ?>">
                             <span class="mdc-button__label"><?= esc($component['parameter_name']); ?></span>
                             </button>
                         </div>											 
                    <?php endif; ?>	
                </div>
            </div>
                <?php endforeach; ?>
            </div>
        </div>
      </div>
</div>










</script>
 /*function updateSliderValue(value) {
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
}*/

/*-----------------------------------------------------------*/
  function updateSliderValue(value, paramName) {
    const sliderElement = document.getElementById('slider-' + paramName);
    if (sliderElement) {
        const sliderInput = sliderElement.querySelector('.mdc-slider__input');
        const valueDisplay = sliderElement.querySelector('.current-value');
        if (sliderInput && valueDisplay) {
            sliderInput.value = value;
            valueDisplay.textContent = value;

            // Send a GET request to ping the location
            fetch('https://efactor.tech/mqtt.php')
                .then(response => {
                    if (response.ok) {
                        console.log('Ping successful');
                    } else {
                        console.error('Ping failed');
                    }
                })
                .catch(error => {
                    console.error('Error during ping:', error);
                });
        }
    }
}


/*---------------------------------------------------------*/
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



function initializeSwitches() {
    document.querySelectorAll('.mdc-switch').forEach((sw) => {
        if (!sw.classList.contains('mdc-switch--checked')) { // Check if not already initialized
            const switchControl = new mdc.switchControl.MDCSwitch(sw);
        }
        // Event listener to update switch status text
        sw.querySelector('.mdc-switch__native-control').addEventListener('change', function() {
			  const switchId = sw.getAttribute('data-switch-id');
            console.log(`Switch with ID ${switchId} is now ${this.checked ? 'ON' : 'OFF'}`);
            const switchStatusDisplay = sw.nextElementSibling; // Assuming the status span immediately follows the switch
            switchStatusDisplay.textContent = this.checked ? 'ON' : 'OFF';
        });
		
    });
}
function initializeTriggerButtons() {
    document.querySelectorAll('.trigger-button').forEach((button) => {
        button.addEventListener('mousedown', function() {
            // Example logic for a hold action
            let holdTimer = setTimeout(() => {
                console.log('Hold action triggered for', button.dataset.triggerName);
                // Perform the hold action here
            }, 1000); // Adjust time as needed

            // Clear the timer and recognize a tap if mouseup happens before the hold time
            button.addEventListener('mouseup', function() {
                clearTimeout(holdTimer);
				 console.log('Tap action triggered');
            });

            button.addEventListener('mouseleave', function() {
                clearTimeout(holdTimer);
            });
        });

        // If you need a tap action, it can be recognized here
        // Note: Adjust logic as needed to prevent both tap and hold actions from firing for the same interaction
    });
}




function initializeColorPaletteBar() {
 
    document.querySelectorAll('.colorPaletteBar').forEach(function(bar) {
        bar.addEventListener('click', function(e) {
            const rect = bar.getBoundingClientRect();
            const x = e.clientX - rect.left; // x position within the element
            const width = rect.width;
            const selector = bar.querySelector('.selector');

            // Calculate new position for selector
            const newLeft = Math.max(0, Math.min(x, width - selector.offsetWidth / 2));
            selector.style.left = `${newLeft}px`;
            // Change the selector's color based on its new position
            changeSelectorColor(selector, x, width);
            // Optional: Change selector color based on position
            // This part requires a function to determine the color based on position
        });
    });


}
function changeSelectorColor(selector, x, width) {

let color;
   const colorTransitionEnd = width * 0.895;
    if (x > colorTransitionEnd) {
       color= '#FFFFFF';
    } else {
        const hue = (x / colorTransitionEnd) * 360;
        color= `hsl(${hue}, 100%, 50%)`;
    }
    // Set the selector's background color
    selector.style.backgroundColor = color;
	 console.log('Color set to:', color); // Debugging
}

function getColorAtPosition(x, width) {
    const colorTransitionEnd = width * 0.895;
    if (x > colorTransitionEnd) {
        return '#FFFFFF';
    } else {
        const hue = (x / colorTransitionEnd) * 360;
        return `hsl(${hue}, 100%, 50%)`;
    }
}
/*function initializeSliders() {
    document.querySelectorAll('.mdc-slider').forEach((sliderElement) => {
        const slider = new mdc.slider.MDCSlider(sliderElement);
        // Attach input event listeners or other event listeners here
		   // Listen for input events on the slider
        slider.listen('MDCSlider:input', (event) => {
            // Use the value from the event detail if direct access isn't working
			console.log(`Slider Value: ${slider.getValue()}`);
            const value = event.detail.value;
            sliderValueDisplay.textContent = value; // Update the display with the slider's current value
        });
		
    });
}

*/
function initializeSliders() {
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

function initializeSelects() {
    document.querySelectorAll('.mdc-select').forEach((selectElement) => {
        const select = mdc.select.MDCSelect.attachTo(selectElement);
        
        // Listen for change events on the select
        select.listen('MDCSelect:change', () => {
            console.log(`Selected value: ${select.value}`);
            // If you also need the selected index
            console.log(`Selected index: ${select.selectedIndex}`);
        });
    });
}


function initializeTextFields() {
    document.querySelectorAll('.mdc-text-field').forEach((textField) => {
        const mdcField = mdc.textField.MDCTextField.attachTo(textField);
        // Check if this is necessary for your use case; it forces a layout update
        // which might only be necessary under certain conditions.
        if (textField.querySelector('.mdc-text-field__input').value) {
            mdcField.layout();
        }
    });
}



function initializeDynamicActionButtons() {
    const dynamicActionButtons = document.querySelectorAll('.dynamic-action-button');

    dynamicActionButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const action = this.dataset.action; // Using 'this' to ensure we're getting the dataset of the clicked button
            
            // Handling URL redirections
            if (action && action.startsWith('http')) {
                window.location.href = action;
            } 
            // Handling function calls
            else if (action) {
                if (typeof window[action] === 'function') {
                    window[action]();
                } else {
                    console.error('Function not found:', action);
                }
            } else {
                console.error('Action is undefined or not set.');
            }
        });
    });
}


function exampleFunction() {
    console.log('Example function called by dynamic button');
	 
    // Select all input elements marked as display inputs
   const displayInputs = document.querySelectorAll('.display-input');
    
    displayInputs.forEach((input, index) => {
        console.log(`Value from ${input.id} (${index + 1}):`, input.value);
        // Additional processing can be done here, such as sending these values to a server
    });
}

	


/*----------------------------------------------------------------*/
mdc.autoInit();

    document.addEventListener('DOMContentLoaded', function () {
        // Event listeners for toggles
        document.querySelectorAll('.mdc-switch').forEach(function (element) {
            element.addEventListener('change', function (event) {
                const switchId = event.target.getAttribute('data-switch-id');
                const isChecked = event.target.checked;
                const statusText = isChecked ? 'ON' : 'OFF';
                event.target.closest('.toggle-wrapper').querySelector('.switch-status').innerText = statusText;

                // Save the toggle state to the database
                saveState(switchId, isChecked);
            });
        });

        // Event listeners for sliders
        document.querySelectorAll('.mdc-slider__input').forEach(function (element) {
            element.addEventListener('input', function (event) {
                const sliderId = event.target.closest('.mdc-slider').id.replace('slider-', '');
                const value = event.target.value;
                document.getElementById(`sliderValueDisplay-${sliderId}`).innerText = value;

                // Save the slider value to the database
                saveState(sliderId, value);
            });
        });

        // Event listeners for dropdowns
        document.querySelectorAll('.mdc-select').forEach(function (element) {
            element.addEventListener('MDCSelect:change', function (event) {
                const selectId = event.target.getAttribute('data-select-id');
                const selectedValue = event.detail.value;

                // Save the selected value to the database
                saveState(selectId, selectedValue);
            });
        });

        // Function to save state to the database
        function saveState(id, value) {
            fetch('items/savepage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id, value: value })
            }).then(response => response.json())
              .then(data => console.log('Success:', data))
              .catch(error => console.error('Error:', error));
        }
    });




/*----------------------------------------------------------------*/
</script>
</body>
</html>
