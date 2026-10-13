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

        .mdc-switch__icon--on,
        .mdc-switch__icon--off {
            display: none;
        }

        .mdc-switch__focus-ring-wrapper,
        .mdc-switch__focus-ring {
            display: none !important;
        }

        /*toggle switch*/
        .toggle-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toggle-label {
            flex-grow: 1;
            margin-right: 20px;
        }

        .mdc-top-app-bar {
            background-color: #6200ee;
            color: white;
        }

        .main-content {
            padding-top: 64px;
        }

        .mdc-slider {
            width: 80%;
        }

        .my-custom-media {
            position: relative;
            width: 100%;
            padding-top: 200%;
            background-size: cover;
            background-position: center;
        }

        .label-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .mdc-select {
            flex-grow: 2;
        }

        .mdc-card #colorPaletteBar {
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000, #ffffff);
            position: relative;
        }

        .mdc-card .colorPaletteBar {
            position: relative;
            width: 100%;
            height: 20px;
            background: linear-gradient(to right, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000, #ffffff);
        }

        .mdc-card .colorPaletteBar .selector {
            width: 35px;
            height: 35px;
            position: absolute;
            border-radius: 50%;
            background-color: #ff0000;
            top: 9px;
            left: 0;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }

        .button-wrapper {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 8px;
            border-radius: 50%;
            background: rgba(100, 100, 255, 0.2);
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
            transition: transform 0.1s;
        }

        .round-button:active {
            transform: scale(0.9);
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
                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
                    <div class="mdc-card" style="padding:16px;">
                        <div class="toggle-wrapper">
                            <p></p>
                            <span class="spacer" style="display: inline-block; width:10px;"></span>
                            <label class="toggle-label" for="ui_name">UI Name</label>
                            <div class="mdc-switch" data-switch-id="ui_name">
                                <div class="mdc-switch__track"></div>
                                <div class="mdc-switch__thumb-underlay">
                                    <div class="mdc-switch__thumb">
                                        <input type="checkbox" id="ui_name" class="mdc-switch__native-control" role="switch">
                                    </div>
                                </div>
                                <div class="mdc-switch__ripple"></div>
                            </div>
                            <span class="switch-status" style="margin-left: 16px;">OFF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>



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

	


function addComponentDynamically(componentType) {
    
}

</script>

