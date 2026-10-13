<!--?php
echo $user_id." ";
echo $modal_id." ";
echo $modelno." ";
print_r($params);
echo "===========================================================================";
print_r($pages);
?-->
<?php
 
$combinedInput = "";

// Check if form is submitted
if (isset($_POST['submit_button'])) {
    // Handle form submission if needed
    $combinedInput = "Form submitted!";
}
?>

<!-- Dropdowns with Select Button and Input Box -->
<form action="" method="post">
    <div>
        <!-- Dropdown for parameter_name -->
        <label for="parameter_name">Select Parameter:</label>
        <select name="parameter_name" id="parameter_name">
            <?php foreach ($params as $param): ?>
                <option value="<?= htmlspecialchars($param['parameter_name']) ?>">
                    <?= htmlspecialchars($param['parameter_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <!-- Dropdown for page_names -->
        <label for="page_names">Select Page:</label>
        <select name="page_names" id="page_names">
            <?php 
            // Flattening the 'page_names' string into an array
            $pageNames = explode(', ', $pages[0]['page_names']);
            foreach ($pageNames as $pageName): ?>
                <option value="<?= htmlspecialchars($pageName) ?>">
                    <?= htmlspecialchars($pageName) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <!-- Select Button -->
        <button type="button" onclick="combineSelection()">Select</button>
    </div>
    
    <!-- Display the selected values with cross marks -->
    <div id="selectedValues" class="selected-values-container">
        <!-- Selected values will appear here with cross marks -->
    </div>

    <!-- Hidden Input Box to store multiple values (for form submission) -->
    <input type="hidden" name="large_input" id="large_input">

    <!-- Separate Submit Button for form submission -->
    <div>
        <button type="submit" name="submit_button">Submit</button>
    </div>
</form>

<script>
// Store selected values
let selectedValues = [];

// Function to combine selected values
function combineSelection() {
    // Get selected values from the dropdowns
    var paramValue = document.getElementById("parameter_name").value;
    var pageValue = document.getElementById("page_names").value;

    // Combine the values with a period (.)
    var combinedValue = paramValue + "." + pageValue;

    // Check if the value is already added
    if (!selectedValues.includes(combinedValue)) {
        selectedValues.push(combinedValue); // Add the new combined value to the array

        // Update the display with the selected value
        updateSelectedValues();
    }
}

// Function to update the display of selected values with cross marks
function updateSelectedValues() {
    var selectedDiv = document.getElementById("selectedValues");
    selectedDiv.innerHTML = ''; // Clear the current displayed values

    selectedValues.forEach(function(value, index) {
        var valueDiv = document.createElement("div");
        valueDiv.classList.add("selected-value-box");

        // Add the value text
        var valueText = document.createElement("span");
        valueText.classList.add("selected-value-text");
        valueText.innerHTML = value;

        // Create the cross button
        var crossButton = document.createElement("span");
        crossButton.innerHTML = " &#10006;"; // Cross mark (X)
        crossButton.classList.add("crossButton");
        crossButton.onclick = function() {
            removeValue(index); // Remove value when cross is clicked
        };

        valueDiv.appendChild(valueText);
        valueDiv.appendChild(crossButton);
        selectedDiv.appendChild(valueDiv);
    });

    // Update the hidden input field with all selected values (useful for form submission)
    document.getElementById("large_input").value = selectedValues.join(', ');
}

// Function to remove selected value by index
function removeValue(index) {
    // Remove the value from the array
    selectedValues.splice(index, 1);

    // Update the display and the hidden input field
    updateSelectedValues();
}
</script>

<style>
/* Container for the selected values */
.selected-values-container {
    margin-top: 20px;
}

/* Style for the individual selected value boxes */
.selected-value-box {
    display: inline-block;
    background-color: #f0f0f0;
    margin: 5px;
    padding: 5px 15px;
    border-radius: 5px;
    position: relative;
    font-size: 14px;
    line-height: 20px;
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Styling for the text inside the selected value box */
.selected-value-text {
    display: inline-block;
    padding-right: 20px;
}

/* Style for the cross button */
.crossButton {
    color: red;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 5px;
    top: 2px;
}

/* Change cross button color on hover */
.selected-value-box:hover .crossButton {
    color: darkred;
}
</style>

