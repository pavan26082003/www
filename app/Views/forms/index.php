
<!-- Bootstrap CSS-->
    <!--link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/css/bootstrap.min.css"-->
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/fontastic.css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/grasp_mobile_progress_circle-1.0.0.min.css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/custom.css">
    
    <link rel="shortcut icon" href="https://efactor.tech/files/system/_file60ed85245779a-favicon.png">

    












<?php
// Initialize $components as an empty array
$components = [];

// Fetch data from the server and populate $components
// Assuming you have some logic to fetch data from the server and store it in $components

// Example data
$components = [
    ['ui_sequence_no' => 3, 'ui_type' => 'toggle', 'ui_name' => 'Toggle 1'],
    ['ui_sequence_no' => 1, 'ui_type' => 'slider', 'ui_name' => 'Slider 1'],
    ['ui_sequence_no' => 2, 'ui_type' => 'dropdown', 'ui_name' => 'Dropdown 1'],
    // Add more components as needed
];

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
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
                <div class="mdc-card" style="padding:16px;">
                    <?php if ($component['ui_type'] === 'toggle'): ?>
                        <div class="toggle-wrapper">
							<p>
							</p>
						    <span class="spacer" style="display: inline-block; width:10px;"></span> <!-- Spacer element -->
                            <label class="toggle-label" for="<?= esc($component['ui_name']); ?>"><?= esc($component['ui_name']); ?></label>
                            <div class="mdc-switch" data-switch-id="<?= esc($component['ui_name']); ?>">
                                <div class="mdc-switch__track"></div>
                                    <div class="mdc-switch__thumb-underlay">
                                        <div class="mdc-switch__thumb">
                                        <input type="checkbox" id="<?= esc($component['ui_name']); ?>" class="mdc-switch__native-control" role="switch">
                                        </div>
                                    </div>
							<div class="mdc-switch__ripple"></div>
                            </div>
							<span class="switch-status" style="margin-left: 16px;">OFF</span>
                            
                        </div>
                    <?php elseif ($component['ui_type'] === 'slider'): ?>
						<div style="display: flex; align-items: center; justify-content: space-between;">
							<div style="flex-grow: 1; display: flex; align-items: center;">
								<!-- Label for the slider -->
								<label class="mdc-typography--subtitle2"  for="<?= esc($component['ui_name']); ?>"><?= esc($component['ui_name']); ?></label>
								<!-- Slider Component -->
								<div class="mdc-slider" style="flex-grow: 1; margin: 16px;">
									<div class="mdc-slider__track">
										<div class="mdc-slider__track--inactive"></div>
										<div class="mdc-slider__track--active">
											<div class="mdc-slider__track--active_fill"></div>
										</div>
									</div>
									<div class="mdc-slider__thumb">
										<div class="mdc-slider__thumb-knob"></div>
										<input class="mdc-slider__input" id="<?= esc($component['ui_name']); ?>" type="range" min="<?= esc($component['ui_range_min']); ?>" max="<?= esc($component['ui_range_max']); ?>"  value="<?= esc($component['ui_cmd_value']); ?>" step="<?= esc($component['ui_step']); ?>" aria-label="Continuous slider demo">
									</div>
								</div>
							</div>
							<!-- Display for slider value, positioned at the end with controlled spacing -->
							<span id="sliderValueDisplay" class="mdc-typography--subtitle2" style="margin-left: 23px;"><?= esc($component['ui_cmd_value']); ?></span>
						</div>

						
                    <?php elseif ($component['ui_type'] === 'dropdown'): ?>
					  <div style="display: flex; align-items: center; justify-content: space-between;">
					    <div class="label-container">
                        <label class="mdc-typography--subtitle2"  for="<?= esc($component['ui_name']); ?>"><?= esc($component['ui_name']); ?></label>
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
						 
				    <?php elseif ($component['ui_type'] === 'color'): ?>
						  <div style="display: flex; align-items: center; justify-content: space-between;">
						 <div class="label-container">
                         <label class="mdc-typography--subtitle2"  for="<?= esc($component['ui_name']); ?>"><?= esc($component['ui_name']); ?></label>
						  </div>
						   <div class="colorPaletteBar">
							<div class="selector"></div>
						   </div>
						  </div>

					<?php elseif ($component['ui_type'] === 'display'): ?>
						<div class="mdc-text-field mdc-text-field--filled <?= $component['ui_permission'] == 'read' ? 'mdc-text-field--disabled' : ''; ?>">
							<span class="mdc-text-field__ripple"></span>
							<input class="mdc-text-field__input display-input" 
								   type="text" 
								   id="<?= esc($component['ui_name']); ?>" 
								   aria-labelledby="<?= esc($component['ui_name']); ?>-label" 
								   <?= $component['ui_permission'] != 'write' && $component['ui_permission'] != 'read/write' ? 'disabled' : ''; ?> 
								   value="<?= esc($component['ui_cmd_value']); ?>">
							<span class="mdc-floating-label" id="<?= esc($component['ui_name']); ?>-label"><?= esc($component['ui_name']); ?></span>
							<span class="mdc-line-ripple"></span>
						</div>
					<?php elseif ($component['ui_type'] === 'textarea'): ?>
						<div class="mdc-text-field mdc-text-field--textarea mdc-text-field--filled<?= $component['ui_permission'] == 'read' ? 'mdc-text-field--disabled' : ''; ?>" style="height: auto;">
							 <span class="mdc-text-field__ripple"></span>
						   <textarea class="mdc-text-field__input" 
									  id="<?= esc($component['ui_name']); ?>" 
									  aria-labelledby="<?= esc($component['ui_name']); ?>-label" 
									  <?= $component['ui_permission'] != 'write' && $component['ui_permission'] != 'read/write' ? 'disabled' : ''; ?>
									  rows="3"><?= esc($component['ui_cmd_value']); ?></textarea>
							<span class="mdc-floating-label" id="<?= esc($component['ui_name']); ?>-label"><?= esc($component['ui_name']); ?></span>
							<span class="mdc-line-ripple"></span>
						</div>
					 
					 <?php elseif ($component['ui_type'] === 'trigger'): ?>
						  <div style="display: flex; align-items: center; justify-content: space-between;">
						 <div class="label-container">
                        <label class="mdc-typography--subtitle2"  for="<?= esc($component['ui_name']); ?>"><?= esc($component['ui_name']); ?></label>
						</div>
							
							<div class="button-wrapper">
							<button class="round-button trigger-button" data-trigger-name="<?= esc($component['ui_name']); ?>"></button>
						</div>
						</div>

					 <?php elseif ($component['ui_type'] === 'button'): ?>
                         <div class="button-container" style="margin-top: 20px;">
                             <button class="mdc-button mdc-button--raised dynamic-action-button" data-action="<?= esc($component['ui_action']); ?>">
                             <span class="mdc-button__label"><?= esc($component['ui_name']); ?></span>
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
function initializeSliders() {
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


function redirectToForms() {
        window.location.href = "https://efactor.tech/index.php/forms";
    }

	


function addComponentDynamically(componentType) {
    
}

</script>
</body>
</html>


