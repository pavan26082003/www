<?php
$models = [];
foreach ($pageNames as $page) {
    if (!in_array($page['model_name'], $models)) {
        $models[] = $page['model_name'];
    }
}

foreach ($nodeIds as $node_id) {
    if (!in_array($node_id['model_number'], $models)) {
        $models[] = $node_id['model_number'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Config</title>
 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
body {
    font-family: Arial, sans-serif;
    /*background-color: #343A40;*/
    background-color: #fff;
    color: #333;
    margin: 0;
    padding: 0;
    margin: 50px;
}

.container {
    width: 70%;
    margin: 0 auto;
    padding: 20px;
    background-color: #f0f1ff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin-top: 30px;
}

h1 {
    text-align: center;
    font-size: 2em;
    color: #4b6bfb;
    margin-bottom: 20px;
}

label {
    font-size: 1.1em;
    font-weight: bold;
    color: #555;
    margin-bottom: 5px;
    display: inline-block;
}

select, input[type="text"] {
    width: 100%;  
    padding: 10px;
    margin-bottom: 20px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
    color: #333;
    background-color: #f0f1ff;
}

select:focus, input[type="text"]:focus {
    border-color: #4b6bfb;
    outline: none;
}

option {
    padding: 10px;
}

 
.row {
    display: flex;
    flex-wrap: wrap;  
    gap: 20px;
}

.row div {
    flex: 1;
    min-width: calc(50% - 10px);  
    box-sizing: border-box; 
}
 
#nodeSelect {
    width: 100%;
}
 
.form-section {
    margin-bottom: 25px;
}

input[type="submit"] {
    background-color: #005f75;
    color: white;
    font-size: 18px;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 20%;
    margin-top:4%;
}

input[type="submit"]:hover {
    background-color: green;
}

.choices__inner {
    display: inline-block;
    vertical-align: top;
    width: 100%;
    background-color: #f0f1ff;
    padding: 7.5px 7.5px 3.75px;
    border: 2px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
    min-height: 50px;
    overflow: hidden;
}

.choices__list--multiple .choices__item {
    display: inline-block;
    vertical-align: middle;
    border-radius: 20px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 500;
    margin-right: 3.75px;
    margin-bottom: 3.75px;
    background-color: #005f75;
    border: 1px solid #004a5c;
    color: #fff;
    word-break: break-all;
    box-sizing: border-box; 
}

.choices__input {
    background-color: #f0f1ff;
}
 
select, input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 25px;
    border: 2px solid #ddd;
    border-radius: 5px;
}
#paramSelect {
    border: 1px solid #ccc;
    padding: 10px;
    max-height: 150px;
    overflow-y: auto;
    font-family: Arial, sans-serif;
}

#paramSelect ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

#paramSelect li {
    padding: 5px 0;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #ddd;
}

#paramSelect li:last-child {
    border-bottom: none;  
}
#configureSelect {
    padding: 10px;
    overflow-y: auto;
    width: 100%;
    background-color: #f1f0ff;
    max-height: 250px;
}
 #qrImage {
      width: 125px;
      height: 125px;
      display: none;  
    }
    #inputBox {
      display: none; 
    }
    </style>
<!--?php print_r($params); ?-->
    <script>
    
    var selectedPage = "";   
var previousModel = "";

 function updatePageDropdown() {
    var modelSelect = document.getElementById("modelSelect");
    var pageSelect = document.getElementById("pageSelect");
    var nodeSelect = document.getElementById("nodeSelect");
    var paramSelect = document.getElementById("paramSelect");
    var configureSelect = document.getElementById("configureSelect");    
    var selectedModel = modelSelect.value; 
    var addedPages = [];
 
 
 if (selectedModel !== previousModel) {
       
        pageSelect.innerHTML = "<option value=''>Select Page</option>"; 
        
    <?php if (!empty($pageNames)): ?>
        <?php foreach ($pageNames as $page): ?> 
            if (selectedModel === "<?php echo $page['model_name']; ?>") { 
            <?php 
$pageNoExists = (strpos($page['page_names'], $pageNo) !== false);
?>
if (<?php echo $pageNoExists ? 'true' : 'false'; ?>) {
                if (addedPages.indexOf("<?php echo $page['page_names']; ?>") === -1) {
                    addedPages.push("<?php echo $page['page_names']; ?>");

                    var option = document.createElement("option");
                    option.value = "<?php echo $page['page_names']; ?>";
                    option.text = "<?php echo $page['page_names']; ?>";
                    pageSelect.appendChild(option);
                }
                }
            }
        <?php endforeach; ?>
    <?php else: ?>
        console.log('No page names found');
    <?php endif; ?>
    
      previousModel = selectedModel;
    }
 
    //pageSelect.addEventListener("change", function () {
     //   selectedPage = pageSelect.value;
     //   console.log("Selected Page Updated to:", selectedPage);  
   // });
    
    
      <?php if (!empty($router_Ids)): ?>
        <?php foreach ($router_Ids as $router): ?> 
           // if (selectedModel === "<?php echo $router['model_number']; ?>") { 
                if (addedPages.indexOf("<?php echo $router['node_id']; ?>") === -1) {
                    addedPages.push("<?php echo $router['node_id']; ?>");

                    var option = document.createElement("option");
                    option.value = "<?php echo $router['node_id']; ?>";
                    option.text = "<?php echo $router['node_id']; ?>";
                    routerSelect.appendChild(option);
                }
           // }
        <?php endforeach; ?>
    <?php else: ?>
        console.log('No page names found');
    <?php endif; ?>
    
  pageSelect.addEventListener("change", function () {
        selectedPage = pageSelect.value;
         console.log("Selected Page Updated to:", selectedPage);  
         
<?php if (!empty($defaults)): ?>
    <?php foreach ($defaults as $default): ?> 
        if (selectedModel === "<?php echo $default['model_name']; ?>") { 
         if (selectedPage === "<?php echo $default['page_names']; ?>") { 
            var div = document.createElement("div");
            div.style.marginBottom = "10px";  
            var label = document.createElement("label"); 
            label.style.marginRight = "10px";  
            var values = "<?php echo $default['cmd_defaultvalue']; ?>".split(','); 
            var inputContainer = document.createElement("div");

            values.forEach(function(value, index) {
                value = value.trim();
                 
                if (value === "") return;

                var inputDiv = document.createElement("div");
                inputDiv.style.marginBottom = "5px"; 

                var valueLabelText = value ? value : "Default";   
                var input = document.createElement("input");
                input.type = "text";  
                input.name = "<?php echo $default['cmd_defaultvalue']; ?>[" + index + "]";  
                input.value = "";   // Default to empty initially
                input.placeholder = "";   

                // Set the default values based on the key
                var defaultValues = {
                    "Resistance": "2.2",
                    "Imin": "2.5",
                    "Imax": "3.5",
                    "Frequence": "16000",
                    "PolePairs": "8",
                    "Open_Loop_Current": "1.5",
                    "PowerLimit": "44",
                    "TorqueLimit": "7000",
                    "Speed1": "4",
                    "Speed2": "7",
                    "Speed3": "11",
                    "Speed4": "17",
                    "Speed5": "23",
                    "Speed6": "26",
                    "Speed7": "26"
                };

                // Check if the current value is in the defaultValues object
                if (defaultValues[value]) {
                    input.value = defaultValues[value];  // Set the default value
                }

                var valueLabel = document.createElement("label");
                valueLabel.textContent = valueLabelText + ":";  
                valueLabel.style.marginRight = "10px";  
                inputDiv.appendChild(valueLabel);
                inputDiv.appendChild(input); 
                inputContainer.appendChild(inputDiv);
            }); 
            
            div.appendChild(label);  
            div.appendChild(inputContainer);  
            configureSelect.appendChild(div);
        }
        }
    <?php endforeach; ?>
<?php else: ?>
    configureSelect.innerHTML = '<p>No Default Values available</p>';
<?php endif; ?>
      
      
    paramSelect.innerHTML = ''; 
    
    <?php if (!empty($params)): ?>
        <?php foreach ($params as $param): ?> 
            if (selectedModel === "<?php echo $param['model_name']; ?>") { 
             if (selectedPage === "<?php echo $param['page_names']; ?>") { 
                var li = document.createElement("li");

                li.textContent = "<?php echo $param['parameter_name']; ?>";  
                
                paramSelect.appendChild(li);
            }
            }
        <?php endforeach; ?>
    <?php else: ?>
        paramSelect.innerHTML = '<li>No parameters available</li>';
    <?php endif; ?>
     });
}
        
    document.addEventListener('DOMContentLoaded', function() {
    var modeSelect = document.getElementById("ModeSelect");
    var levelSelect = document.getElementById("LevelSelect");
    var paramSelect = document.getElementById("paramSelect");
    var configureSelect = document.getElementById("configureSelect");

    function updateParameterVisibility() {
        var modeValue = modeSelect.value;
        var levelValue = levelSelect.value;
  
        if (modeValue === "Auto test" && levelValue === "Test 1") {
            paramSelect.style.display = "inline-block";   
        } else {
            paramSelect.style.display = "none";   
        }
 
        if (modeValue === "Configuration" && levelValue === "Test 1") {
            configureSelect.style.display = "inline-block";   
        } else {
            configureSelect.style.display = "none";   
        }
    }
 
    modeSelect.addEventListener('change', updateParameterVisibility);
    levelSelect.addEventListener('change', updateParameterVisibility);
 
    updateParameterVisibility();
});
    </script>
</head>
<body>
 
<?php echo form_open_multipart(get_uri("items/saveconfig"), array("id" => "file-form", "class" => "general-form", "role" => "form", "onsubmit" => "return handleSubmit()")); ?>
<!--?php print_r($params); ?-->
<!--?php echo $pageNo; ?-->
<div class="row">
    <div>
        <label for="modelSelect">Select Model:</label>
        <select id="modelSelect" name="modelSelect" onchange="updatePageDropdown()">
            <option value="">Select Model</option>
            <?php
            $selectedModelArray = array($selectedmodelNumber);
             if (!empty($selectedModelArray)): ?>
                <?php foreach ($selectedModelArray as $model): ?>
                    <option value="<?php echo $model; ?>"><?php echo $model; ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No Models Available</option>
            <?php endif; ?>
        </select>
    </div>
    <div>
        <label for="pageSelect">Page Name:</label>
        <select id="pageSelect" name="pageSelect">
            <option value="">Select Page</option>
        </select>
    </div>
</div>

 <?php if($pageNo != "Pairing"){ ?>
<div class="row">
    <div>
        <label for="ModeSelect">Mode:</label>
        <select id="ModeSelect" name="ModeSelect">
            <option value="None">None</option>
            <option value="Auto test">Auto Test</option>
            <option value="Configuration">Configuration</option>
        </select>
    </div>
    <div>
        <label for="LevelSelect">Level:</label>
        <select id="LevelSelect" name="LevelSelect">
            <option value="None">None</option>
            <option value="Test 1">Test 1</option>
            <option value="Test 2">Test 2</option>
            <option value="Test 3">Test 3</option>
        </select>
    </div>
</div>
<?php } ?>

<div class="row">
    <div>
        <label for="routerSelect">Router ID:</label>
        <select id="routerSelect" name="routerSelect">
            <option value="">Select Router ID</option>
        </select>
    </div>
    <div>
        <label for="statusSelect">Status:</label>
        <select id="statusSelect" name="statusSelect">
            <option value="None">None</option>
            <option value="Online">Online</option>
            <option value="Peer to Peer">Peer to Peer</option>
        </select>
    </div>
</div>

<div class="row">
    <div>
        <label for="nodeSelect">Node ID:</label>
        <select id="nodeSelect" name="nodeSelect[]" multiple>
            <option value=""></option>
        </select>
    </div>
     <div>   
      <?php if($pageNo != "Pairing"){ ?>
        <div id="paramSelect" style="padding: 10px;overflow-y: auto;width:100%;background-color:#f1f0ff">
            <ul id="paramSelect" style="list-style-type: none; padding: 0;"></ul>
        </div> 
  <div id="configureSelect">
    <ul id="configureList" style="list-style-type: none; padding: 0;"></ul>
</div>
<?php } ?>
    </div>
    </div>
    <div class="row" style="width:49%">
    <?php if($pageNo == "Pairing"){ ?>
  <div style="margin-top:20px;">
   <label for="remoteaddress">Remote Address:</label>
   <select id="remoteaddress" onchange="handleSelectionChange()">
    <option value="None">None</option>
    <option value="qr">QR Code</option>
    <option value="manual">Enter Manually</option>
    <option value="deviceApi">Device/API</option>
  </select>
  
  <div id="qrImageContainer">
    <!--img id="qrImage" src="http://192.168.0.130/files/home/QR.png" alt="QR Code"-->
    <img id="qrImage" src="https://efactor.tech/files/home/QR.png" alt="QR Code">
  </div>

  <div id="inputBoxContainer">
    <input type="text" id="inputBox" name="inputBox" placeholder="Enter Here">
  </div>
  </div>
<?php } ?>
    </div>
</div>
 
<input type="submit" value="Submit" name="submit">

<?php echo form_close(); ?>

<script>
    function handleSelectionChange() {
      const selectedValue = document.getElementById('remoteaddress').value;
      const qrImage = document.getElementById('qrImage');
      const inputBox = document.getElementById('inputBox');
 
      qrImage.style.display = 'none';
      inputBox.style.display = 'none';
 
      if (selectedValue === 'qr') {
        qrImage.style.display = 'block';
      }
 
      if (selectedValue === 'manual' || selectedValue === 'deviceApi') {
        inputBox.style.display = 'block';
      }
    }
 
    handleSelectionChange();
  </script>
  
<script>
function handleSubmit() {
    var modelSelect = document.getElementById("modelSelect");
    var modeSelect = document.getElementById("ModeSelect");
    var modelNumber = modelSelect.value;
    var mode = modeSelect.value;

    if (!modelNumber) {
        alert("Please select a model.");
        return false;
    }

    var form = document.getElementById("file-form");

    // Step 1: Remove any previously added hidden input fields
    var existingConfigInputs = document.querySelectorAll('input[name="configValues[]"], input[name="configKeys[]"]');
    existingConfigInputs.forEach(function(input) {
        input.remove();
    });

    // Step 2: Collect entered key-value pairs
    var configValues = [];
    var configKeys = [];
    var processedLabels = new Set(); // To ensure keys are unique

    // Look for divs inside configureSelect that have input fields and labels
    var configDivs = document.querySelectorAll('#configureSelect div');
    configDivs.forEach(function(div) {
        var input = div.querySelector('input');
        var label = div.querySelector('label');

        // Ensure input and label exist
        if (input && label) {
            var value = input.value.trim(); // Get the entered value
            var key = label.textContent.trim(); // Get the label text
            key = key.replace(/:$/, "").trim(); // Clean the label (remove colon)

            // Only add if the key is not empty and hasn't been processed already
            if (key !== "" && value !== "" && !processedLabels.has(key)) {
                configKeys.push(key);
                configValues.push(value);
                processedLabels.add(key); // Mark this key as processed
            }
        }
    });

    // Step 3: Add the collected keys and values as hidden inputs in the form
    configKeys.forEach(function(key, index) {
        var configKeyInput = document.createElement('input');
        configKeyInput.type = 'hidden';
        configKeyInput.name = 'configKeys[]';  // Ensure these are the correct names
        configKeyInput.value = key;
        form.appendChild(configKeyInput);

        var configValueInput = document.createElement('input');
        configValueInput.type = 'hidden';
        configValueInput.name = 'configValues[]';  // Ensure these are the correct names
        configValueInput.value = configValues[index];
        form.appendChild(configValueInput);
    });

    // Step 4: Determine the API endpoint based on the selected mode
  /* var baseUrl = "https://efactor.tech/v8/product_config.php?action=";
    var action = "";

    if (mode === "Configuration") {
        action = "Product_config";
    } else if (mode === "Auto test") {
        action = "Auto_test";
    } else {
        alert("Please select a valid mode.");
        return false;
    }

*/

var baseUrl = "https://efactor.tech/v8/index.html";

   // var apiLink = baseUrl + action + "&model_number=" + encodeURIComponent(modelNumber);
  var apiLink = baseUrl;
    window.open(apiLink, "_blank");

    // Step 5: Submit the form with only the entered keys and values
    form.submit();
    return false; // Prevent default form submission
}

</script>


 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var nodeSelect = document.getElementById('nodeSelect');
        var choices = new Choices(nodeSelect, {
            removeItemButton: true,  
            searchEnabled: false,      
            itemSelectText: '',     
            placeholder: true,     
            placeholderValue: 'Search Node IDs',  
        });
        
        function updateNodeDropdown() {
            var modelSelect = document.getElementById("modelSelect");
            var selectedModel = modelSelect.value;
             
            choices.clearChoices();

            <?php if (!empty($nodeIds)): ?>
                <?php foreach ($nodeIds as $node): ?>
                    if (selectedModel === "<?php echo $node['model_number']; ?>") {
                        choices.setChoices([
                            { value: "<?php echo $node['node_id']; ?>", label: "<?php echo $node['node_id']; ?>" }
                        ], 'value', 'label', false);
                    }
                <?php endforeach; ?>
            <?php else: ?>
                console.log('No node IDs found');
            <?php endif; ?>
        }
 
        var modelSelect = document.getElementById("modelSelect");
        modelSelect.addEventListener('change', updateNodeDropdown);
    });
</script>

</body>
</html>

