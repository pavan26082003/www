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
            padding: 10px;
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
        .deselect-button {
            margin-left:10px;
            background-color: blue;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .deselect-button:hover {
            background-color: darkblue;
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
                <option value="">None</option>
                <?php if (!empty($modelIds)): ?>
                    <?php foreach ($modelIds as $modelId): ?>
                        <option value="<?php echo $modelId; ?>" <?php echo isset($selected_values['model_id']) && $selected_values['model_id'] == $modelId ? 'selected' : ''; ?>><?php echo $modelId; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="group">
            <h3>MODEL No</h3>
            <select name="model_no" id="model_no" onchange="updateProductName()">
                <option value="">None</option>
                <?php 
                if (!empty($models)) {
                    foreach (array_keys($models) as $modelno) {
                        echo '<option value="' . htmlspecialchars($modelno) . '">' . htmlspecialchars($modelno) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="group">
            <h3>Corresponding Product Name</h3>
            <input type="text" id="product_name" value="" readonly>
            <input type="hidden" name="product_name" id="hidden_product_name">
        </div>

        <button type="button" class="save-button" onclick="confirmAction('assign')">Select</button>
        <button type="button" class="deselect-button" onclick="confirmAction('deselect')">Deselect</button>
    </form>
</div>

<!-- Display the selected values -->
<div class="selected-values">
    <h3>Selected Values</h3>
    <p><strong>DEVICE ID:</strong> <span name="selected_model_id" id="selected_model_id"><?php echo $selected_values['model_id'] ?? ''; ?></span></p>
    <p><strong>MODEL No:</strong> <span name="selected_model_no" id="selected_model_no"><?php echo $selected_values['model_no'] ?? ''; ?></span></p>
    <p><strong>PRODUCT NAME:</strong> <span name="selected_product_name" id="selected_product_name"><?php echo $selected_values['product_name'] ?? ''; ?></span></p>
</div>
 
            </div>
</div>
<script>
function updateProductName() {
    var modelSelect = document.getElementById("model_no");
    var selectedModel = modelSelect.value;
    var productInput = document.getElementById("product_name");
    var selectedProductNameSpan = document.getElementById("selected_product_name");
    var hiddenProductName = document.getElementById("hidden_product_name");

    var modelsToProducts = <?php echo json_encode($models); ?>;

    var productName = modelsToProducts[selectedModel] || ""; 
    productInput.value = productName;
    selectedProductNameSpan.textContent = productName;
    hiddenProductName.value = productName; 
}
</script>
<script>
function confirmAction(action) {
    var message = action === 'assign' ? 'Are you sure you want to select this item?' : 'Are you sure you want to deselect this item?';
    if (confirm(message)) {
        document.forms[0].action = "<?php echo get_uri('items/selected_data'); ?>"; // Set the form action
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'action';
        input.value = action;
        document.forms[0].appendChild(input);
        document.forms[0].submit(); // Submit the form
    }
}
</script>
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
    
</script>
</body>
</html>

