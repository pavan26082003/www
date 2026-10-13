<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efactor Modules</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .checkbox-group {
            margin: 20px 0;
            text-align: center;
        }
        .checkbox-group input {
            margin-right: 10px;
        }
        select, input[type="text"] {
            width: auto;
            padding: 10px;
            margin: 10px 1%;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        select[multiple] {
            height: 150px;
        }
        .select-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .select-container label {
            font-weight: bold;
        }
        .hidden {
            display: none;
        }
        .btn {
            background-color: #4cae4c;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: 100%;
        }
        .btn:hover {
            background-color: #4cae4c;
        }
        .input-box {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 10px;
            background-color: #fff;
        }
        .input-box span {
            background-color: #f1f1f1;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .input-box span .remove {
            cursor: pointer;
            color: #d9534f;
            font-weight: bold;
        }
     
.custom-dropdown {
    position: relative;
    width: 100%;
    max-width: 1000px;  
    margin: 0 auto;
    font-family: 'Arial', sans-serif;
    box-sizing: border-box;
}
 
#searchInput {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 15px;
    box-sizing: border-box;
    outline: none;
}
 
#searchInput:focus {
    border-color: #007bff;
}
 
.assigned-list {
    border: 1px solid #ddd;
    border-radius: 5px;
    max-height: 520px;
    overflow-y: auto;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
 
.assigned-item.heading {
     background-color: #007bff;
    color: white;
    font-weight: bold;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky; /* Make the heading sticky */
    top: 0; /* Stick the heading to the top of the container */
    z-index: 10; /* Ensure the heading stays above other content */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
 
.left-half,
.left-right-half,
.right-half,
.right-left-half,
.right-right-half {
    padding: 10px;
    font-size: 14px;
    text-align: left;
    overflow: hidden;  
    white-space: nowrap;  
    text-overflow: ellipsis;  
}
 
.left-half {
    width: 35%;  
}

.left-right-half {
    width: 15%; 
}

.right-half {
    width: 10%;  
}

.right-left-half {
    width: 20%;  
}

.right-right-half {
    width: 20%; 
}
 
.assigned-item {
    display: flex;
    padding: 10px;
    border-bottom: 1px solid #ddd;
    align-items: center;
    width: 100%;
    box-sizing: border-box;
} 
.left-half,
.left-right-half,
.right-half,
.right-left-half,
.right-right-half {
    padding: 10px;
    font-size: 14px;
    text-align: left;
}
 
@media (max-width: 768px) {
    .assigned-item.heading {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 0;
    }

    .assigned-item {
        flex-direction: column;
        padding: 10px 0;
    }
 
    .left-half,
    .left-right-half,
    .right-half,
    .right-left-half,
    .right-right-half {
        width: 100%;  
        text-align: left;
        padding: 5px 10px;
    }
}
 
.node-id,
.user-type,
.user-id,
.email,
.phone {
    font-weight: normal;
    color: #333;
}
 
.assigned-item:hover .node-id,
.assigned-item:hover .user-type,
.assigned-item:hover .user-id,
.assigned-item:hover .email,
.assigned-item:hover .phone {
    color: #007bff;
}


    </style>
</head>
<body>

<div class="container">
    <h1>Efactor Modules</h1>
<?php echo form_open_multipart(get_uri("settings/save_modules"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
    <div class="select-container">
        <div>
            <label for="userType">User Type</label>
            <select id="userType" name="userType">
                <option value="None">None</option>
                <option value="User">User</option>
                <option value="Client">Client</option>
                <option value="Developer">Developer</option>
                <option value="Manufacturer">Manufacturer</option>
            </select>
        </div>
        <div>
            <label for="userID">ID</label>
            <input type="text" id="userID" name="userID" placeholder="Enter ID" />
        </div>
    </div>

    <div class="checkbox-group">
        <input type="checkbox" id="serialSelection" name="serialSelection" onclick="toggleSelection('serial')" />
        <label for="serialSelection">Serial Selection</label>

        <input type="checkbox" id="manualSelection" name="manualSelection" onclick="toggleSelection('manual')" />
        <label for="manualSelection">Manual Selection</label>
    </div>

    <div id="serialDropdowns" class="select-container hidden">
        <div>
            <label for="node_id_1">From</label>
            <select name="node_id_1" id="node_id_1">
                <?php foreach ($nodes as $node): ?>
                    <option value="<?php echo htmlspecialchars($node['node_id']); ?>">
                        <?php echo htmlspecialchars($node['node_id']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="node_id_2">To</label>
            <select name="node_id_2" id="node_id_2">
                <?php foreach ($nodes as $node): ?>
                    <option value="<?php echo htmlspecialchars($node['node_id']); ?>">
                        <?php echo htmlspecialchars($node['node_id']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div id="manualDropdown" class="select-container hidden">
        <div>
            <label for="manual_node_id">Select Nodes</label>
            <select name="manual_node_id[]" id="manual_node_id" multiple onchange="updateInputBox()">
                <?php foreach ($nodes as $node): ?>
                    <option value="<?php echo htmlspecialchars($node['node_id']); ?>">
                        <?php echo htmlspecialchars($node['node_id']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-box" id="selectedNodes"></div>
    </div>

    <button class="btn">Submit</button>
    <?php echo form_close(); ?>
</div>

<script>
    let selectedNodes = [];

    function toggleSelection(selectionType) {
        if (selectionType === 'serial') {
            document.getElementById('manualSelection').checked = false;
        } else if (selectionType === 'manual') {
            document.getElementById('serialSelection').checked = false;
        }

        document.getElementById('serialDropdowns').classList.add('hidden');
        document.getElementById('manualDropdown').classList.add('hidden');

        if (selectionType === 'serial' && document.getElementById('serialSelection').checked) {
            document.getElementById('serialDropdowns').classList.remove('hidden');
        } else if (selectionType === 'manual' && document.getElementById('manualSelection').checked) {
            document.getElementById('manualDropdown').classList.remove('hidden');
        }
    }

    function updateInputBox() {
        const selectedOptions = document.getElementById('manual_node_id').selectedOptions;
        const newSelectedNodes = Array.from(selectedOptions).map(option => option.value);

        newSelectedNodes.forEach(node => {
            if (!selectedNodes.includes(node)) {
                selectedNodes.push(node);
            }
        });

        const inputBox = document.getElementById('selectedNodes');
        inputBox.innerHTML = '';   

        selectedNodes.forEach(node => {
            const nodeSpan = document.createElement('span');
            nodeSpan.textContent = node;
            
            const removeButton = document.createElement('span');
            removeButton.textContent = '×';
            removeButton.classList.add('remove');
            removeButton.onclick = function() {
                removeNode(node);
            };

            nodeSpan.appendChild(removeButton);
            inputBox.appendChild(nodeSpan);
        });
    }

    function removeNode(nodeToRemove) {
        selectedNodes = selectedNodes.filter(node => node !== nodeToRemove);
        
        const dropdown = document.getElementById('manual_node_id');
        for (let i = 0; i < dropdown.options.length; i++) {
            if (dropdown.options[i].value === nodeToRemove) {
                dropdown.options[i].selected = false;
                break;
            }
        }

        updateInputBox();
    }
</script>
<!--?php print_r($assigned_node); ?-->
<div class="custom-dropdown"> 
    <input type="text" id="searchInput" placeholder="Search" onkeyup="searchList()" />
 
    <div class="assigned-list"> 
        <div class="assigned-item heading">
            <div class="left-half"><strong>Node ID</strong></div>
            <div class="left-right-half"><strong>User Type</strong></div>
            <div class="right-half"><strong>User ID</strong></div>
            <div class="right-left-half"><strong>Email</strong></div>
            <div class="right-right-half"><strong>Phone</strong></div>
        </div>
 
        <?php foreach ($assigned_node as $assigned): ?>
            <div class="assigned-item">
                <div class="left-half">
                    <span class="node-id"><?php echo htmlspecialchars($assigned['node_id']); ?></span>
                </div>
                 <div class="left-right-half">
                    <span class="user-type"><?php echo htmlspecialchars($assigned['user_type']); ?></span>
                </div>
                <div class="right-half">
                    <span class="user-id"><?php echo htmlspecialchars($assigned['user_id']); ?></span>
                </div>
                 <div class="right-left-half">
                    <span class="email"><?php echo htmlspecialchars($assigned['email']); ?></span>
                </div>
                 <div class="right-right-half">
                    <span class="phone"><?php echo htmlspecialchars($assigned['phone']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
 
<script>
function searchList() { 
    var input = document.getElementById("searchInput").value.toLowerCase();
     
    var items = document.querySelectorAll(".assigned-item");
 
    items.forEach(function(item) {
        var nodeId = item.querySelector(".node-id") ? item.querySelector(".node-id").textContent.toLowerCase() : '';
        var userType = item.querySelector(".user-type") ? item.querySelector(".user-type").textContent.toLowerCase() : '';
        var userId = item.querySelector(".user-id") ? item.querySelector(".user-id").textContent.toLowerCase() : '';
        var email = item.querySelector(".email") ? item.querySelector(".email").textContent.toLowerCase() : '';
        var phone = item.querySelector(".phone") ? item.querySelector(".phone").textContent.toLowerCase() : '';
         
        if (nodeId.includes(input) || userType.includes(input) || userId.includes(input) || email.includes(input) || phone.includes(input)) {
            item.style.display = "";  
        } else {
            item.style.display = "none";   
        }
    });
}
</script>
  
</body>
</html>

