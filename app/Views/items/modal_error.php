<?php echo form_open(get_uri("items/saveerrorapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">ERROR FRAME</h4>


 <h2>Error Frame Information</h2>



<br><br>
<div class="commandname">

    <!--  <div class="form-group">
        <label class=" col-sm-2" for="email">Categories:</label>
        <div class="col-sm-4">
          <select  class="form-control" id="email" name="category">

            <option value="">Select</option>
            <?php
            foreach ($categories_dropdown as $value) {
               ?>

               <option><?php echo $value->name; ?></option>

               <?php
            }
            ?>
          </select>
        </div>
      </div>


<div class="form-group">
        <label class=" col-sm-2" for="email">Product Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="productname" placeholder="productname" name="productname">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="email">Model No:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="modelno" placeholder="modelno" name="modelno">
        </div>
      </div>


          <div class="form-group">
        <label class=" col-sm-2" for="email">Model Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="modelname" placeholder="modelname" name="modelname">
        </div>
      </div>

           <div class="form-group">
        <label class=" col-sm-2" for="email">Description:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="description" placeholder="description" name="description">
        </div>
      </div> -->
   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="error_parametername" name="error_parametername">
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Command Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="error_data_flow_direction" name="error_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_frametype" name="error_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Error Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" oninput="updateTable()">
    </div>
</div>

      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_frametrigger" name="error_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_voicecommand" name="error_voicecommand">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice control Instruction text</label>
        <div class="col-sm-2">          
          <input type="file" class="form-control" id="error_voice_control_instruction_text" name="error_voice_control_instruction_text" accept="image/*">
        </div>
      </div>


       <div class="form-group">
    <label class="col-sm-2" for="productdescription">App Notification</label>
    <div class="col-sm-4">
        <textarea class="form-control" id="error_appnotification" name="error_appnotification" rows="4"></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>
</div>

</div>


<div class="table-responsive">
<div style="overflow-x: auto;">

    <table id="additionalTable" class="table">
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
        <!-- Initial row with input fields and select dropdowns -->
        <tr>
            <!-- ... (Your existing row HTML) ... -->
        </tr>
        <tr>
            <td colspan="18"><button onclick="addRow()">+</button></td>
        </tr>

        <tr>
            <td colspan="18"><button onclick="submitForm()">Submit</button></td>
        </tr>
    </table>



    <!--div class="modal-footer">
    
     Upload File : <input type="file" name="item_files">

       < <button class="btn btn-default upload-file-button float-start btn-sm round me-auto" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_image"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button> >
        
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div-->

      <!--div class="modal-footer">
    
     
        
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div-->






       <div class="modal-footer">   
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>





</div>
</div>
</div>







<?php echo form_close(); ?>

     
<script type="text/javascript">
  

 function addRow() {

    var table = document.getElementById("additionalTable");    
    table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button   
    var newRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)    
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
    buttonCell.innerHTML = '<td colspan="18"><button onclick="removeRow()">-</button></td>';
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



function updateTable() {
        var frameSize = parseInt(document.getElementById('cmd_framesizeinbytes').value);
        var table = document.getElementById('additionalTable');
        var rowCount = table.rows.length;

        // Remove existing rows (except headers and buttons)
        for (var i = rowCount - 3; i > 0; i--) {
            table.deleteRow(i);
        }

        // Add new rows based on frameSize
        for (var i = 0; i < frameSize; i++) {
            addRow();
        }
    }





</script>