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
    <!-- Favicon-->
    <link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/favicon.ico">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Frame Information</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>


<body>






    <h2>Command Parameter Information</h2>

<form id="commandForm">

     <table>
        <tr>
            <th>Operation Name</th>
            <td><input type="text" value="Power on/off"></td>
        </tr>
        <tr>
            <th>Command FRAME</th>
            <td><input type="text" value=""></td>
        </tr>
        <tr>
            <th>Parameter Definition</th>
            <td><input type="text" value=""></td>
        </tr>
        <tr>
            <td>Parameter Name</td>
            <td><input type="text" value="Power"></td>
        </tr>
        <tr>
            <td>Comments</td>
            <td><input type="text" value=""></td>
        </tr>
        <tr>
            <td>Developer Notes (If Any)</td>
            <td><input type="text" value=""></td>
        </tr>
        <tr>
            <th>Parameter Name</th>
            <td><input type="text" value="Power"></td>
        </tr>
        <tr>
            <th>Data Flow Direction</th>
            <td><input type="text" value="App/Server to Device"></td>
        </tr>
        <tr>
            <th>Frame Type</th>
            <td><input type="text" value="Command Frame"></td>
        </tr>
        <tr>
            <th>Frame Size in Bytes</th>
            <td><input type="text" value="4"></td>
        </tr>
        <tr>
            <th>Frame Trigger</th>
            <td><input type="text" value="Toggle Switch"></td>
        </tr>
        <tr>
            <th>Voice Command</th>
            <td><input type="text" value=""></td>
        </tr>
        <tr>
            <th>Voice Control Instruction Text</th>
            <td><input type="text" value="Ex: Turn on Fan"></td>
        </tr>
        <tr>
            <th>App Notification</th>
            <td><input type="text" value=""></td>
        </tr>
    </table>


    <h2>Command Frame Information</h2>

    <table id="additionalTable">
        <tr>
            <th>S.No</th>
            <th>Start Byte position</th>
            <th>End Byte position</th>
            <th>Field Description</th>
            <th>Number of Bytes</th>
            <th>Bit Size</th>
            <th>data type</th>
            <th>Value definition</th>
            <th>display value (Min)</th>
            <th>display value (Max)</th>
            <th>Value in hex (Min)</th>
            <th>Value in hex (Max)</th>
            <th>UI Type</th>
            <th>UI id</th>
            <th>UI Page No</th>
            <th>UI description in/out</th>
            <th>slider step value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
        
        <tr>
            <td colspan="18"><button onclick="addRow()">+</button></td>
        </tr>

        <tr>
            <td colspan="18"><button onclick="submitForm()">Submit</button></td>
        </tr>
    </table>



<button type="button" id="nextButton" onclick="nextPage()">Next</button>
</form>

    







    <script>

function nextPage() {
        // Add logic to navigate to the next page
       // window.location.href = 'http://192.168.2.115/response_frame.php';

window.location.href = '<?php echo site_url("forms/ResponseFrame"); ?>';
    }



    function addRow() {
    var table = document.getElementById("additionalTable");

    // Remove the existing "+" button row
    table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button

    // Create a new row
    var newRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)

    // Your existing cells creation logic goes here
    var cells = [
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select><option value="CommandID">CommandID</option><option value="FunctionCode">Function Code</option><option value="Data">Data</option><option value="Terminator">Terminator</option><option value="ActionID">Action ID</option><option value="ErrorCode">Error Code</option><option value="OperationCode">Operation Code</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select><option value="ASCII">ascii</option><option value="Binary">binary</option><option value="Signed Int">Signed Int</option><option value="float">float</option><option value="unsigned int">unsigned int</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select id="selectWithImages">' +
            '<option value="toggle switch" data-image="toggle_switch_image.jpg">Toggle Switch</option>' +
            '<option value="slider" data-image="slider_image.jpg">Slider</option>' +
            '<option value="Brightness slider" data-image="brightness_slider_image.jpg">Brightness Slider</option>' +
            '<option value="CCT slider" data-image="cct_slider_image.jpg">CCT Slider</option>' +
            '<option value="saturation slider" data-image="saturation_slider_image.jpg">Saturation Slider</option>' +
            '<option value="Hue slider" data-image="hue_slider_image.jpg">Hue Slider</option>' +
            '<option value="Hue circle" data-image="hue_circle_image.jpg">Hue Circle</option>' +
            '<option value="push button" data-image="push_button_image.jpg">Push Button (Big)</option>' +
        '</select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select><option value="Write">Write</option><option value="read">read</option><option value="read/write">read/write</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">'
    ];

    // Insert cells into the new row
    for (var i = 0; i < cells.length; i++) {
        var cell = newRow.insertCell(i);
        cell.innerHTML = cells[i];
    }

    // Add a new "+" button to the end of the table
    var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
    var buttonCell = buttonRow.insertCell(0);
    buttonCell.innerHTML = '<td colspan="18"><button onclick="removeRow()">+</button></td>';
}

    function removeRow() {
        var table = document.getElementById("additionalTable");

        // Remove the last row
        table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button

        // Add a new "-" button to the end of the table
        var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
        var buttonCell = buttonRow.insertCell(0);
        buttonCell.innerHTML = '<td colspan="18"><button onclick="addRow()">+</button></td>';
    }

    function submitForm() {
        // Add logic to handle form submission
        alert("Form Submitted!");
    }






    const selectElement = document.getElementById('selectWithImages');
    const imageElement = document.getElementById('selectedImage');

    selectElement.addEventListener('change', function () {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const imageUrl = selectedOption.getAttribute('data-image');
        imageElement.src = imageUrl;
    });







</script>


























</body>
</html>
