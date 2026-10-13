<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Device Model</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.css">
    <style>
        .mdc-top-app-bar-fixed-adjust {
            padding-top: 64px; /* Adjust this value if your app bar height is different */
        }
        .content {
            margin: 2px; /* Add some spacing around the content */
        }
        .model-with-pages {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px; /* Increase spacing between each model */
        }
        .model-with-pages .mdc-button {
            margin-left: 20px; /* Spacing between buttons */
        }

        .container {
            display: flex;
            flex-wrap: wrap; /* Allow groups to wrap to the next line if necessary */
        }

        .group {
            margin-right: 20px; /* Adjust the margin as per your requirement */
            margin-bottom: 20px; /* Add some spacing below each group */
        }

        .save-button {
            background-color: blue;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .save-button:hover {
            background-color: darkblue;
        }

        .selected-values {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

    </style>
</head>
<body>

<div class="mdc-top-app-bar">
    <div class="mdc-top-app-bar__row">
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <span class="mdc-top-app-bar__title">Efactor SmartThings</span>
        </section>
    </div>
</div>

<!-- Use this div to adjust content below the app bar -->
<div class="mdc-top-app-bar-fixed-adjust content">
    <div class="mdc-typography">
        <h2>Select a Device Model and Subpage</h2>
 <?php echo form_open(get_uri("items/selected_data"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
        <div class="container">
         <form name="myForm" onsubmit="return validateForm()" method="post">
            
                <div class="group">
                    <h3>DEVICE ID</h3>
                    <select name="model_id" id="model_id">
                        <?php if (!empty($modelIds)): ?>
                            <?php foreach ($modelIds as $modelId): ?>
                                <option value="<?php echo $modelId; ?>" <?php echo isset($selected_values['model_id']) && $selected_values['model_id'] == $modelId ? 'selected' : ''; ?>><?php echo $modelId; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="group">
                    <h3>MODEL No</h3>
                    <select name="model_no" id="model_no">
                        <?php 
                        if (!empty($modelnos)) {
                            $uniqueModelNos = array_unique($modelnos); // Remove duplicate model numbers
                            foreach ($uniqueModelNos as $modelno) {
                                echo '<option value="' . $modelno . '" ' . (isset($selected_values['model_no']) && $selected_values['model_no'] == $modelno ? 'selected' : '') . '>' . $modelno . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="group">
                    <h3>PRODUCT NAME</h3>
                    <select name="product_name" id="product_name">
                        <?php 
                        if (!empty($modelproducts)) {
                            $uniqueModelProducts = array_unique($modelproducts);
                            foreach ($uniqueModelProducts as $modelproduct) {
                                echo '<option value="' . $modelproduct . '" ' . (isset($selected_values['product_name']) && $selected_values['product_name'] == $modelproduct ? 'selected' : '') . '>' . $modelproduct . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="group">
                    <h3>PARAMETER NAME</h3>
                    <select name="parameter_name" id="parameter_name">
                        <?php 
                        if (!empty($modelparameters)) {
                            $uniqueModelParameters = array_unique($modelparameters);
                            foreach ($uniqueModelParameters as $modelparameter) {
                                echo '<option value="' . $modelparameter . '" ' . (isset($selected_values['parameter_name']) && $selected_values['parameter_name'] == $modelparameter ? 'selected' : '') . '>' . $modelparameter . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="save-button" value="submit">Save</button>
            </form>
        </div>

        <!-- Display the selected values -->
         
        <div class="selected-values">
            <h3>Selected Values</h3>
<p><strong>DEVICE ID:</strong> <span name="selected_model_id" id="selected_model_id"><?php echo $selected_values['model_id'] ?? ''; ?></span></p>

<p><strong>MODEL No:</strong> <span name="selected_model_no" id="selected_model_no"><?php echo $selected_values['model_no'] ?? ''; ?></span></p>

<p><strong>PRODUCT NAME:</strong> <span name="elected_product_name" id="selected_product_name"><?php echo $selected_values['product_name'] ?? ''; ?></span></p>

<p><strong>PARAMETER NAME:</strong> <span name="selected_parameter_name" id="selected_parameter_name"><?php echo $selected_values['parameter_name'] ?? ''; ?></span></p>
        </div>
 
            </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.js"></script>
<script>mdc.autoInit()</script>
<script>
    document.getElementById('model_id').addEventListener('change', function() {
        document.getElementById('selected_model_id').innerText = this.value;
    });
    document.getElementById('model_no').addEventListener('change', function() {
        document.getElementById('selected_model_no').innerText = this.value;
    });
    document.getElementById('product_name').addEventListener('change', function() {
        document.getElementById('selected_product_name').innerText = this.value;
    });
    document.getElementById('parameter_name').addEventListener('change', function() {
        document.getElementById('selected_parameter_name').innerText = this.value;
    });
</script>
</body>
</html>

