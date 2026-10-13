<?php echo form_open(get_uri("items/saveapi_edit"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">PRODUCT INFORMATION</h4>
<br><br>
<div class="commandname">

    
      <div class="form-group">
        <label class=" col-sm-2" for="email">Product Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="productname" value="<?php echo $productname; ?>" name="productname">
        </div>
      </div>





      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Brand Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="brandname" value="<?php echo $brandname; ?>" name="brandname">
        </div>
      </div>



        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Date:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="date" value="<?php echo $date; ?>" name="date">
        </div>
       </div>



        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Model Name:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="modelname" value="<?php echo $modelname; ?>" name="modelname">
        </div>
      </div>
      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Number of pages:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="numberofpages" value="<?php echo $numberofpages; ?>" name="numberofpages">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Page names:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="pagenames" value="<?php echo $pagenames; ?>" name="pagenames">
        </div>
      </div>



        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Brand Logo:</label>
        <div class="col-sm-2">          
          <input type="file" class="form-control" id="brandlogo" name="brandlogo" accept="image/*">
        </div>
      </div>


       <div class="form-group">
    <label class="col-sm-2" for="productdescription">Product Description:</label>
    <div class="col-sm-4">
        <textarea class="form-control" id="productdescription" value="<?php echo $productdescription; ?>" name="productdescription" rows="4"></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>





<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">COMMAND FRAME</h4>


 <h2>Command Frame Information</h2>



<br><br>
<div class="commandname">


   
      <!--div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_parametername" value="<?php echo $cmd_parametername; ?>" name="cmd_parametername">
        </div>
      </div-->


    <div class="form-group">
    <div class="col-sm-4">
        <input type="text" class="form-control" id="cmd_parametername" value="<?php echo $cmd_parametername; ?>" name="cmd_parametername" readonly>
        <small class="text-muted" style="color: red !important;">Note: Please do not change the parameter name.</small>
    </div>
</div>


      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Command Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_data_flow_direction" value="<?php echo $cmd_dataflow; ?>" name="cmd_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametype" value="<?php echo $cmd_frametype; ?>" name="cmd_frametype">
        </div>
       </div>


 





<div class="form-group">
    <label class="col-sm-2" for="pwd">Frame size in bytes</label>
    <div class="col-sm-2">          
       <input type="text" class="form-control" id="cmd_framesizeinbytes" value="<?php echo $cmd_framesizeinbytes; ?>" name="cmd_framesizeinbytes">
    </div>
</div>



<div class="form-group">
    <label class="col-sm-2" for="pwd">no of rows</label>
    <div class="col-sm-2">          
       <input type="text" class="form-control" id="cmd_noofrows" name="cmd_noofrows" value="<?php echo $cmd_noofrows; ?>" oninput="updateTable()" >
    </div>
</div>


      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametrigger" value="<?php echo $cmd_frametrigger; ?>" name="cmd_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_voicecommand" value="<?php echo $cmd_voicecommand; ?>" name="cmd_voicecommand">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice control Instruction text</label>
        <div class="col-sm-2">          
          <input type="file" class="form-control" id="cmd_voice_control_instruction_text" name="cmd_voice_control_instruction_text" accept="image/*">
        </div>
      </div>


       <div class="form-group">
    <label class="col-sm-2" for="productdescription">App Notification</label>
    <div class="col-sm-4">
        <textarea class="form-control" id="cmd_appnotification" value="<?php echo $cmd_appnotification; ?>" name="cmd_appnotification" rows="4"></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>
</div>

</div>




<script>
    // Pass startBytePosition_cmd values to JavaScript
    var startBytePositions_cmd = <?php echo json_encode($startBytePosition_cmd); ?>;
    var endBytePositions_cmd = <?php echo json_encode($endBytePosition_cmd); ?>;   
    var numberOfBytes_cmd = <?php echo json_encode($numberOfBytes_cmd); ?>;
    var bitSizes_cmd = <?php echo json_encode($bitSize_cmd); ?>;
    var valueDefinitions_cmd = <?php echo json_encode($valueDefinition_cmd); ?>;   
    var displayValueMins_cmd = <?php echo json_encode($displayValueMin_cmd); ?>;
    var displayValueMaxs_cmd = <?php echo json_encode($displayValueMax_cmd); ?>;
    var valueInHexMins_cmd = <?php echo json_encode($valueInHexMin_cmd); ?>;
    var valueInHexMaxs_cmd = <?php echo json_encode($valueInHexMax_cmd); ?>;
    var uiIds_cmd = <?php echo json_encode($uiId_cmd); ?>;
    var uiPageNos_cmd = <?php echo json_encode($uiPageNo_cmd); ?>;
    var fieldDescriptions_cmd = <?php echo json_encode($fieldDescription_cmd); ?>;


    var startBytePositions_resp = <?php echo json_encode($startBytePosition_resp); ?>;
    var endBytePositions_resp = <?php echo json_encode($endBytePosition_resp); ?>;   
    var numberOfBytes_resp = <?php echo json_encode($numberOfBytes_resp); ?>;
    var bitSizes_resp = <?php echo json_encode($bitSize_resp); ?>;
    var valueDefinitions_resp = <?php echo json_encode($valueDefinition_resp); ?>;   
    var displayValueMins_resp = <?php echo json_encode($displayValueMin_resp); ?>;
    var displayValueMaxs_resp = <?php echo json_encode($displayValueMax_resp); ?>;
    var valueInHexMins_resp = <?php echo json_encode($valueInHexMin_resp); ?>;
    var valueInHexMaxs_resp = <?php echo json_encode($valueInHexMax_resp); ?>;
    var uiIds_resp = <?php echo json_encode($uiId_resp); ?>;
    var uiPageNos_resp = <?php echo json_encode($uiPageNo_resp); ?>;
    var fieldDescriptions_resp = <?php echo json_encode($fieldDescription_resp); ?>;




    var startBytePositions_error = <?php echo json_encode($startBytePosition_error); ?>;
    var endBytePositions_error = <?php echo json_encode($endBytePosition_error); ?>;   
    var numberOfBytes_error = <?php echo json_encode($numberOfBytes_error); ?>;
    var bitSizes_error = <?php echo json_encode($bitSize_error); ?>;
    var valueDefinitions_error = <?php echo json_encode($valueDefinition_error); ?>;   
    var displayValueMins_error = <?php echo json_encode($displayValueMin_error); ?>;
    var displayValueMaxs_error = <?php echo json_encode($displayValueMax_error); ?>;
    var valueInHexMins_error = <?php echo json_encode($valueInHexMin_error); ?>;
    var valueInHexMaxs_error = <?php echo json_encode($valueInHexMax_error); ?>;
    var uiIds_error = <?php echo json_encode($uiId_error); ?>;
    var uiPageNos_error = <?php echo json_encode($uiPageNo_error); ?>;
    var fieldDescriptions_error = <?php echo json_encode($fieldDescription_error); ?>;



    
</script>



<?php
// Manually initialize the $fieldDescription_cmd array for testing
//$fieldDescription_cmd = array("CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data", "Error Code", "Operation Code");
// Rest of your code
//$fieldDescription_cmd = ["CommandID", "Error_Code", "Terminator", "CommandId"];
?>

   <table id="additionalTable" class="table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Start Byte position</th>
            <th>End Byte position</th>
            <th>Field Description</th>
            <th>Number of Bytes</th>
            <th>Bit Size</th>
            <th>Data Type</th>
            <th>Value Definition</th>
            <th>Display Value (Min)</th>
            <th>Display Value (Max)</th>
            <th>Value in Hex (Min)</th>
            <th>Value in Hex (Max)</th>
            <th>UI Type</th>
            <th>UI ID</th>
            <th>UI Page No</th>
            <th>AS PER UI</th>
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
<tbody>
<tbody>



<?php
for ($i = 0; $i < $cmd_noofrows; $i++) {
?>

<!-- Initial row with input fields and select dropdowns -->
<tr id="row_<?php echo $i + 1; ?>">
    <!-- Individual fields with IDs -->
    <td id="sno_<?php echo $i + 1; ?>"><?php echo $i + 1; ?></td>
    <td><input type="text" id="startBytePosition_cmd[]" name="startBytePosition_cmd[]" value="<?php echo $startBytePosition_cmd[$i]; ?>"></td>
    <td><input type="text" id="endBytePosition_cmd[]" name="endBytePosition_cmd[]" value="<?php echo $endBytePosition_cmd[$i]; ?>"></td>        


<td>
    <select id="fieldDescription_cmd[]" name="fieldDescription_cmd[]">

        <?php       
        $options = array("None", "CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data", "Error Code", "Operation Code");
        foreach ($options as $option) {
            $selected = ($fieldDescription_cmd[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $fieldDescription_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>

    </select>
</td>


        <td><input type="text" id="numberOfBytes_cmd[]" name="numberOfBytes_cmd[]" value="<?php echo $numberOfBytes_cmd[$i]; ?>"></td>
        <td><input type="text" id="bitSize_cmd[]" name="bitSize_cmd[]" value="<?php echo $bitSize_cmd[$i]; ?>"></td>


        <td>
        <select id="dataType_cmd[]" name="dataType_cmd[]">
        <?php       
        $options = array("None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt");
        foreach ($options as $option) {
            $selected = ($dataType_cmd[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $dataType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
       </select>
       </td>

        
      <td>
        <select id="valueDefinition_cmd[]" name="valueDefinition_cmd[]">
        <?php       
        $options = array("None", "Fixed value", "Range value", "Mode value", "ASCII value");
        foreach ($options as $option) {
            $selected = ($valueDefinition_cmd[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $dataType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
       </select>
       </td>



        <td><input type="text" id="displayValueMin_cmd[]" value="<?php echo $displayValueMin_cmd[$i]; ?>" name="displayValueMin_cmd[]"></td>
        <td><input type="text" id="displayValueMax_cmd[]" value="<?php echo $displayValueMax_cmd[$i]; ?>" name="displayValueMax_cmd[]"></td>
        <td><input type="text" id="valueInHexMin_cmd[]" value="<?php echo $valueInHexMin_cmd[$i]; ?>" name="valueInHexMin_cmd[]"></td>
        <td><input type="text" id="valueInHexMax_cmd[]" value="<?php echo $valueInHexMax_cmd[$i]; ?>" name="valueInHexMax_cmd[]"></td>

      <td>
    <select id="uiType_cmd[]" name="uiType_cmd[]">
        <?php       
        $options = array("None", "Text(Default)", "Toggle Switch", "Slider", "Brightness Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button");
        //  $options = array("ToggleSwitch", "Slider", "BrightnessSlider", "CCTSlider", "SaturationSlider", "HueSlider", "HueCircle", "PushButton");
        foreach ($options as $option) {
            $selected = ($uiType_cmd[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  


        <td><input type="text" id="uiId_cmd[]" value="<?php echo $uiId_cmd[$i]; ?>" name="uiId_cmd[]"></td>
        

<td>
    <select id="uiPageNo_cmd[]" name="uiPageNo_cmd[]">
        <?php       
        $options = array("None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10");
        foreach ($options as $option) {
            $selected = ($uiPageNo_cmd[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  


       <td><input type="text" id="cmd_asperui[]" value="<?php echo $cmd_asperui[$i]; ?>" name="cmd_asperui[]"></td>


<td>
    <select id="uiDescriptionInOut_cmd[]" name="uiDescriptionInOut_cmd[]">
        <?php       
        $options = array("None", "Read/Write", "Read", "Write");
        foreach ($options as $option) {
            $selected = ($uiDescriptionInOut_cmd[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  



        <td><input type="text" id="sliderStepValue_cmd[]" value="<?php echo $sliderStepValue_cmd[$i]; ?>" name="sliderStepValue_cmd[]"></td>
        <td><input type="text" id="developerNotes_cmd[]" value="<?php echo $developerNotes_cmd[$i]; ?>" name="developerNotes_cmd[]"></td>
    </tr>


<?php
}
?>
<tr>
<td colspan="18"><button onclick="addRow(parseInt(document.getElementById('cmd_noofrows').value))">+</button></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>




<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">Response FRAME</h4>


 <h2>Response Frame Information</h2>



<br><br>
<div class="commandname">

   
   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="resp_parametername" value="<?php echo $cmd_parametername; ?>" name="resp_parametername" readonly>
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Response Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="resp_data_flow_direction" value="<?php echo $resp_dataflow; ?>" name="resp_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_frametype" value="<?php echo $resp_frametype; ?>" name="resp_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Response Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="resp_framesizeinbytes" value="<?php echo $resp_framesizeinbytes; ?>" name="resp_framesizeinbytes">
    </div>
</div>

     


 <div class="form-group">
    <label class=" col-sm-2" for="pwd">Response Frame no of rows</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="resp_noofrows" name="resp_noofrows" value="<?php echo $resp_noofrows; ?>" oninput="respupdateTable()">
    </div>
</div>



       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_frametrigger" value="<?php echo $resp_frametrigger; ?>" name="resp_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_voicecommand" name="resp_voicecommand">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice control Instruction text</label>
        <div class="col-sm-2">          
          <input type="file" class="form-control" id="resp_voice_control_instruction_text" name="resp_voice_control_instruction_text" accept="image/*">
        </div>
      </div>


       <div class="form-group">
    <label class="col-sm-2" for="productdescription">App Notification</label>
    <div class="col-sm-4">
        <textarea class="form-control" id="resp_appnotification" placeholder="Product Description" name="resp_appnotification" rows="4"></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>
</div>
</div>





<div class="table-responsive">
<div style="overflow-x: auto;">

<table id="responseTable" class="table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Start Byte position</th>
            <th>End Byte position</th>
            <th>Field Description</th>
            <th>Number of Bytes</th>
            <th>Bit Size</th>
            <th>Data Type</th>
            <th>Value Definition</th>
            <th>Display Value (Min)</th>
            <th>Display Value (Max)</th>
            <th>Value in Hex (Min)</th>
            <th>Value in Hex (Max)</th>
            <th>UI Type</th>
            <th>UI ID</th>
            <th>UI Page No</th>
            <th>AS PER UI</th>
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>


<?php
for ($i = 0; $i < $resp_noofrows; $i++) {
?>

<!-- Initial row with input fields and select dropdowns -->
<tr id="row_<?php echo $i + 1; ?>">
    <!-- Individual fields with IDs -->
    <td id="sno_<?php echo $i + 1; ?>"><?php echo $i + 1; ?></td>
    <td><input type="text" id="startBytePosition_resp[]" name="startBytePosition_resp[]" value="<?php echo $startBytePosition_resp[$i]; ?>"></td>
    <td><input type="text" id="endBytePosition_resp[]" name="endBytePosition_resp[]" value="<?php echo $endBytePosition_resp[$i]; ?>"></td>
        

        
<td>
    <select id="fieldDescription_resp[]" name="fieldDescription_resp[]">

        <?php       
        $options = array("None","CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data", "Error Code", "Operation Code");
        foreach ($options as $option) {
            $selected = ($fieldDescription_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $fieldDescription_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>

    </select>
</td>




        <td><input type="text" id="numberOfBytes_resp[]" name="numberOfBytes_resp[]" value="<?php echo $numberOfBytes_resp[$i]; ?>"></td>
        <td><input type="text" id="bitSize_resp[]" name="bitSize_resp[]" value="<?php echo $bitSize_resp[$i]; ?>"></td>
        
 <td>
        <select id="dataType_resp[]" name="dataType_resp[]">
        <?php       
        $options = array("None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt");
        foreach ($options as $option) {
            $selected = ($dataType_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $dataType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
       </select>
       </td>

        
      <td>
        <select id="valueDefinition_resp[]" name="valueDefinition_resp[]">
        <?php       
        $options = array("None", "Fixed value", "Range value", "Mode value", "ASCII value");
        foreach ($options as $option) {
            $selected = ($valueDefinition_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $dataType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
       </select>
       </td>


                
        <td><input type="text" id="displayValueMin_resp[]" value="<?php echo $displayValueMin_resp[$i]; ?>" name="displayValueMin_resp[]"></td>
        <td><input type="text" id="displayValueMax_resp[]" value="<?php echo $displayValueMax_resp[$i]; ?>" name="displayValueMax_resp[]"></td>
        <td><input type="text" id="valueInHexMin_resp[]" value="<?php echo $valueInHexMin_resp[$i]; ?>" name="valueInHexMin_resp[]"></td>
        <td><input type="text" id="valueInHexMax_resp[]" value="<?php echo $valueInHexMax_resp[$i]; ?>" name="valueInHexMax_resp[]"></td>



        <td>
    <select id="uiType_resp[]" name="uiType_resp[]">
        <?php       
        $options = array("None", "Text(Default)", "Toggle Switch", "Slider", "Brightness Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button");
        foreach ($options as $option) {
            $selected = ($uiType_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  


        <td><input type="text" id="uiId_resp[]" value="<?php echo $uiId_resp[$i]; ?>" name="uiId_resp[]"></td>
        
<td>
    <select id="uiPageNo_resp[]" name="uiPageNo_resp[]">
        <?php       
        $options = array("None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10");
        foreach ($options as $option) {
            $selected = ($uiPageNo_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  



     <td><input type="text" id="resp_asperui[]" value="<?php echo $resp_asperui[$i]; ?>" name="resp_asperui[]"></td>


<td>
    <select id="uiDescriptionInOut_resp[]" name="uiDescriptionInOut_resp[]">
        <?php       
        $options = array("None", "Read/Write", "Read", "Write");
        foreach ($options as $option) {
            $selected = ($uiDescriptionInOut_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  


        <td><input type="text" id="sliderStepValue_resp[]" value="<?php echo $sliderStepValue_resp[$i]; ?>" name="sliderStepValue_resp[]"></td>
        <td><input type="text" id="developerNotes_resp[]" value="<?php echo $developerNotes_resp[$i]; ?>" name="developerNotes_resp[]"></td>
    </tr>


<?php
}
?>


<tr>
<td colspan="18"><button onclick="respaddRow(parseInt(document.getElementById('resp_noofrows').value))">+</button></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>



<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">ERROR FRAME</h4>


 <h2>Error Frame Information</h2>



<br><br>
<div class="commandname">

   
   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="error_parametername" name="error_parametername" value="<?php echo $cmd_parametername; ?>" readonly>
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Error Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="error_data_flow_direction" name="error_data_flow_direction" value="<?php echo $error_dataflow; ?>">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_frametype" name="error_frametype" value="<?php echo $error_frametype; ?>">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Error Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="error_framesizeinbytes"  value="<?php echo $error_framesizeinbytes; ?>" name="error_framesizeinbytes">
    </div>
</div>



<div class="form-group">
    <label class=" col-sm-2" for="pwd">Error Frame no of rows</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="error_noofrows"  value="<?php echo $error_noofrows; ?>" name="error_noofrows" oninput="errorupdateTable()">
    </div>
</div>


      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_frametrigger" name="error_frametrigger" value="<?php echo $error_frametrigger; ?>">
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


<table id="errorTable" class="table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Start Byte position</th>
            <th>End Byte position</th>
            <th>Field Description</th>
            <th>Number of Bytes</th>
            <th>Bit Size</th>
            <th>Data Type</th>
            <th>Value Definition</th>
            <th>Display Value (Min)</th>
            <th>Display Value (Max)</th>
            <th>Value in Hex (Min)</th>
            <th>Value in Hex (Max)</th>
            <th>UI Type</th>
            <th>UI ID</th>
            <th>UI Page No</th>
            <th>AS PER UI</th>
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>

<?php
for ($i = 0; $i < $error_noofrows; $i++) {
?>

    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_<?php echo $i + 1; ?>"><?php echo $i + 1; ?></td>
        <td><input type="text" id="startBytePosition_error[]" name="startBytePosition_error[]" value="<?php echo $startBytePosition_error[$i]; ?>"></td>
        <td><input type="text" id="endBytePosition_error[]" name="endBytePosition_error[]" value="<?php echo $endBytePosition_error[$i]; ?>"></td>
        
<td>
    <select id="fieldDescription_error[]" name="fieldDescription_error[]">

        <?php       
        $options = array("None", "CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data", "Error Code", "Operation Code");
        foreach ($options as $option) {
            $selected = ($fieldDescription_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $fieldDescription_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>

    </select>
</td>


        <td><input type="text" id="numberOfBytes_error[]" name="numberOfBytes_error[]" value="<?php echo $numberOfBytes_error[$i]; ?>"></td>
        <td><input type="text" id="bitSize_error[]" name="bitSize_error[]" value="<?php echo $bitSize_error[$i]; ?>"></td>


        <td>
        <select id="dataType_error[]" name="dataType_error[]">
        <?php       
        $options = array("None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt");
        foreach ($options as $option) {
            $selected = ($dataType_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $dataType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
       </select>
       </td>

        
      <td>
        <select id="valueDefinition_error[]" name="valueDefinition_error[]">
        <?php       
        $options = array("None", "Fixed value", "Range value", "Mode value", "ASCII value");
        foreach ($options as $option) {
            $selected = ($valueDefinition_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $dataType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
       </select>
       </td>


        <td><input type="text" id="displayValueMin_error[]" name="displayValueMin_error[]" value="<?php echo $displayValueMin_error[$i]; ?>"></td>
        <td><input type="text" id="displayValueMax_error[]" name="displayValueMax_error[]" value="<?php echo $displayValueMax_error[$i]; ?>"></td>
        <td><input type="text" id="valueInHexMin_error[]" name="valueInHexMin_error[]" value="<?php echo $valueInHexMin_error[$i]; ?>"></td>
        <td><input type="text" id="valueInHexMax_error[]" name="valueInHexMax_error[]" value="<?php echo $valueInHexMax_error[$i]; ?>"></td>



     <td>
    <select id="uiType_error[]" name="uiType_error[]">
        <?php       
        $options = array("None", "Text(Default)", "Toggle Switch", "Slider", "Brightness Slider", "CCT Slider", "Saturation Slider", "Hue Slider", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button");
        foreach ($options as $option) {
            $selected = ($uiType_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  


        <td><input type="text" id="uiId_error[]" value="<?php echo $uiId_error[$i]; ?>" name="uiId_error[]"></td>
       

<td>
    <select id="uiPageNo_error[]" name="uiPageNo_error[]">
        <?php       
        $options = array("None", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10");
        foreach ($options as $option) {
            $selected = ($uiPageNo_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td> 



      <td><input type="text" id="error_asperui[]" value="<?php echo $error_asperui[$i]; ?>" name="error_asperui[]"></td> 




<td>
    <select id="uiDescriptionInOut_error[]" name="uiDescriptionInOut_error[]">
        <?php       
        $options = array("None", "Read/Write", "Read", "Write");
        foreach ($options as $option) {
            $selected = ($uiDescriptionInOut_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  


        <td><input type="text" id="sliderStepValue_error[]" name="sliderStepValue_error[]" value="<?php echo $sliderStepValue_error[$i]; ?>"></td>
        <td><input type="text" id="developerNotes_error[]" name="developerNotes_error[]" value="<?php echo $developerNotes_error[$i]; ?>"></td>
    </tr>


<?php
}
?>


<tr>
<td colspan="18"><button onclick="erroraddRow(parseInt(document.getElementById('error_noofrows').value))">+</button></td>
</tr>
</tbody>
</table>

<div class="modal-footer">   
<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
</div>
</div>
</div>


     
<script type="text/javascript">


/************************************/

function addRows() {

currentSno = 0;
    // Get the value of cmd_framesizeinbytes
    var frameSize = parseInt(document.getElementById('cmd_noofrows').value);
    
    // Loop to add rows
    for (var i = 0; i < frameSize; i++) {
        addRow(i + 1); // Pass the S.No value to the addRow function
    }
}


var currentSno = 0; // Initialize S.No to 1


function addRow(index) {
    var table = document.getElementById("additionalTable");
    table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button
    var newRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)

    var cells = [
        '<td>' + index + '</td>', // S.No
        '<input type="text" placeholder="1" value="' + startBytePositions_cmd[] + '">',
        '<input type="text" placeholder="1" value="' + endBytePositions_cmd[] + '">',
        
'<td>' +
        '<select id="fieldDescription_cmd[]" name="fieldDescription_cmd[]">';
    
    <?php foreach ($options as $option): ?>
        <?php $selected = (in_array($option, $fieldDescription_cmd)) ? "selected" : ""; ?>
        newRow += '<option value="<?= htmlspecialchars($option) ?>" <?= $selected ?>><?= $option ?></option>';
    <?php endforeach; ?>
    
    newRow += '</select>' +
        '</td>'

        '<input type="text" placeholder="1" value="' + numberOfBytes_cmd[currentSno - 1] + '">',
        '<input type="text" placeholder="1" value="' + bitSizes_cmd[currentSno - 1] + '">',
        '<select><option value="None">None</option><option value="ASCII">ascii</option><option value="Binary">binary</option><option value="Signed Int">Signed Int</option><option value="float">float</option><option value="unsigned int">unsigned int</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select id="selectWithImages">' +
        '<option value="None">None</option>' +
        '<option value="Text(Default)">Text(Default)</option>' +
        '<option value="toggle switch">Toggle Switch</option>' +
        '<option value="slider">Slider</option>' +
        '<option value="Brightness slider">Brightness Slider</option>' +
        '<option value="CCT slider">CCT Slider</option>' +
        '<option value="saturation slider">Saturation Slider</option>' +
        '<option value="Hue slider">Hue Slider</option>' +
        '<option value="Hue circle">Hue Circle</option>' +
        '<option value="push button">Push Button (Big)</option>' +
        '<option value="Drop Down">Drop Down</option>' +
            '<option value="Trigger">Trigger</option>' +
            '<option value="Display">Display</option>' +
            '<option value="Button">Button</option>' +

        '</select>',
        '<input type="text" placeholder="1">',
        '<select><option value="None">None</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>',

        '<input type="text" placeholder="1">',
        '<select><option value="None">None</option><option value="Write">Write</option><option value="read">read</option><option value="read/write">read/write</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">'
    ];

    // Insert cells into the new row
    for (var i = 0; i < cells.length; i++) {
        var cell = newRow.insertCell(i);
        cell.innerHTML = cells[i];
    }

    currentSno++; // Increment S.No for the next row

    // Add a new "+" button to the end of the table
    var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
    var buttonCell = buttonRow.insertCell(0);
    buttonCell.innerHTML = '<td colspan="18"><button onclick="removeRow()">-</button></td>';
}




addRows();

/*************************************/

function respaddRow(index) {
    var table = document.getElementById("responseTable");
    table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button
    var newRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)

    var cells = [
        '<td>' + index + '</td>', // S.No
        '<input type="text" placeholder="1" value="' + startBytePositions[] + '">',
        '<input type="text" placeholder="1" value="' + endBytePositions[] + '">',
        
'<td>' +
        '<select id="fieldDescription_resp[]" name="fieldDescription_resp[]">';
    
    <?php foreach ($options as $option): ?>
        <?php $selected = (in_array($option, $fieldDescription_resp)) ? "selected" : ""; ?>
        newRow += '<option value="<?= htmlspecialchars($option) ?>" <?= $selected ?>><?= $option ?></option>';
    <?php endforeach; ?>
    
    newRow += '</select>' +
        '</td>'

        '<input type="text" placeholder="1" value="' + numberOfBytes[currentSno - 1] + '">',
        '<input type="text" placeholder="1" value="' + bitSizes[currentSno - 1] + '">',
        '<select><option value="None">None</option><option value="ASCII">ascii</option><option value="Binary">binary</option><option value="Signed Int">Signed Int</option><option value="float">float</option><option value="unsigned int">unsigned int</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select id="selectWithImages">' +
        '<option value="None">None</option>' +
        '<option value="Text(Default)">Text(Default)</option>' +
        '<option value="toggle switch">Toggle Switch</option>' +
        '<option value="slider">Slider</option>' +
        '<option value="Brightness slider">Brightness Slider</option>' +
        '<option value="CCT slider">CCT Slider</option>' +
        '<option value="saturation slider">Saturation Slider</option>' +
        '<option value="Hue slider">Hue Slider</option>' +
        '<option value="Hue circle">Hue Circle</option>' +
        '<option value="push button">Push Button (Big)</option>' +
         '<option value="Drop Down">Drop Down</option>' +
            '<option value="Trigger">Trigger</option>' +
            '<option value="Display">Display</option>' +
            '<option value="Button">Button</option>' +
        '</select>',
        '<input type="text" placeholder="1">',
        '<select><option value="None">None</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>',
         '<input type="text" placeholder="1">',
        '<select><option value="None">None</option><option value="Write">Write</option><option value="read">read</option><option value="read/write">read/write</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">'
    ];

    // Insert cells into the new row
    for (var i = 0; i < cells.length; i++) {
        var cell = newRow.insertCell(i);
        cell.innerHTML = cells[i];
    }

    currentSno++; // Increment S.No for the next row

    // Add a new "+" button to the end of the table
    var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
    var buttonCell = buttonRow.insertCell(0);
    buttonCell.innerHTML = '<td colspan="18"><button onclick="respremoveRow()">-</button></td>';
}









/******************************************/
/*
function respaddRow(index) {
    var table = document.getElementById("responseTable");
    var newRow = table.insertRow(-1); // Insert at the end of the table
    var cells = [
        '<td>' + index + '</td>', // S.No
        '<td><input type="text" name="startBytePosition_resp_' + index + '"></td>',
        '<td><input type="text" name="endBytePosition_resp_' + index + '"></td>',
        '<td><select name="fieldDescription_resp_' + index + '">' +
            '<option value="CommandId">CommandId</option>' +
            '<option value="Function Code">Function Code</option>' +
            '<option value="Checksum">Checksum</option>' +
            '<option value="Terminator">Terminator</option>' +
            '<option value="ActionId">ActionId</option>' +
            '<option value="Data">Data</option>' +
            '<option value="Error Code">Error Code</option>' +
            '<option value="Operation Code">Operation Code</option>' +
            '</select></td>',
        '<td><input type="text" name="numberOfBytes_resp_' + index + '"></td>',
        '<td><input type="text" name="bitSize_resp_' + index + '"></td>',
        '<td><select name="dataType_resp_' + index + '">' +
            '<option value="ASCII">ASCII</option>' +
            '<option value="Binary">Binary</option>' +
            '<option value="SignedInt">Signed Int</option>' +
            '<option value="Float">Float</option>' +
            '<option value="UnsignedInt">Unsigned Int</option>' +
            '</select></td>',

          '<td><select name="valueDefinition_resp_' + index + '">' +
            '<option value="Fixed Value">Fixed Value</option>' +
            '<option value="Range Value">Range Value</option>' +
            '<option value="Mode Value">Mode Value</option>' +
            '<option value="ASCII">ASCII</option>' +
           '</select></td>',


        '<td><input type="text" name="displayValueMin_resp_' + index + '"></td>',
        '<td><input type="text" name="displayValueMax_resp_' + index + '"></td>',
        '<td><input type="text" name="valueInHexMin_resp_' + index + '"></td>',
        '<td><input type="text" name="valueInHexMax_resp_' + index + '"></td>',
        '<td><select name="uiType_resp_' + index + '">' +
            '<option value="None">None</option>' +
            '<option value="Text(Default)">Text(Default)</option>' +
            '<option value="Toggle Switch">Toggle Switch</option>' +
            '<option value="Slider">Slider</option>' +
            '<option value="Brightness Slider">Brightness Slider</option>' +
            '<option value="CCT Slider">CCT Slider</option>' +
            '<option value="Saturation Slider">Saturation Slider</option>' +
            '<option value="Hue Slider">Hue Slider</option>' +
            '<option value="Hue Circle">Hue Circle</option>' +
            '<option value="Push Button">Push Button (Big)</option>' +
            '<option value="Drop Down">Drop Down</option>' +
            '<option value="Trigger">Trigger</option>' +
            '<option value="Display">Display</option>' +
            '<option value="Button">Button</option>' +
            '</select></td>',
        '<td><input type="text" name="uiId_resp_' + index + '"></td>',
        '<td><input type="text" name="uiPageNo_resp_' + index + '"></td>',


       '<td><select name="uiDescriptionInOut_resp_' + index + '">' +
            '<option value="Read/Write">Read/Write</option>' +
            '<option value="Read">Read</option>' +
            '<option value="Write">Write</option>' +
           '</select></td>',


        '<td><input type="text" name="sliderStepValue_resp_' + index + '"></td>',
        '<td><input type="text" name="developerNotes_resp_' + index + '"></td>'
    ];

    // Insert cells into the new row
    for (var i = 0; i < cells.length; i++) {
        var cell = newRow.insertCell(i);
        cell.innerHTML = cells[i];
    }

    // Add a remove button to delete the row if needed
    var removeCell = newRow.insertCell(-1);
    removeCell.innerHTML = '<button onclick="removeRow(this)">Remove</button>';
}

*/












function erroraddRow(index) {
    var table = document.getElementById("errorTable");
    table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button
    var newRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)

    var cells = [
        '<td>' + index + '</td>', // S.No
        '<input type="text" placeholder="1" value="' + startBytePositions[] + '">',
        '<input type="text" placeholder="1" value="' + endBytePositions[] + '">',
        
'<td>' +
        '<select id="fieldDescription_error[]" name="fieldDescription_error[]">';
    
    <?php foreach ($options as $option): ?>
        <?php $selected = (in_array($option, $fieldDescription_error)) ? "selected" : ""; ?>
        newRow += '<option value="<?= htmlspecialchars($option) ?>" <?= $selected ?>><?= $option ?></option>';
    <?php endforeach; ?>
    
    newRow += '</select>' +
        '</td>'

        '<input type="text" placeholder="1" value="' + numberOfBytes[currentSno - 1] + '">',
        '<input type="text" placeholder="1" value="' + bitSizes[currentSno - 1] + '">',
        '<select><option value="None">None</option><option value="ASCII">ascii</option><option value="Binary">binary</option><option value="Signed Int">Signed Int</option><option value="float">float</option><option value="unsigned int">unsigned int</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">',
        '<select id="selectWithImages">' +
        '<option value="None">None</option>' +
        '<option value="Text(Default)">Text(Default)</option>' +
        '<option value="toggle switch">Toggle Switch</option>' +
        '<option value="slider">Slider</option>' +
        '<option value="Brightness slider">Brightness Slider</option>' +
        '<option value="CCT slider">CCT Slider</option>' +
        '<option value="saturation slider">Saturation Slider</option>' +
        '<option value="Hue slider">Hue Slider</option>' +
        '<option value="Hue circle">Hue Circle</option>' +
        '<option value="push button">Push Button (Big)</option>' +
         '<option value="Drop Down">Drop Down</option>' +
            '<option value="Trigger">Trigger</option>' +
            '<option value="Display">Display</option>' +
            '<option value="Button">Button</option>' +
        '</select>',
        '<input type="text" placeholder="1">',
        '<select><option value="None">None</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>',
        '<input type="text" placeholder="1">',
        '<select><option value="None">None</option><option value="Write">Write</option><option value="read">read</option><option value="read/write">read/write</option></select>',
        '<input type="text" placeholder="1">',
        '<input type="text" placeholder="1">'
    ];

    // Insert cells into the new row
    for (var i = 0; i < cells.length; i++) {
        var cell = newRow.insertCell(i);
        cell.innerHTML = cells[i];
    }

    currentSno++; // Increment S.No for the next row

    // Add a new "+" button to the end of the table
    var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
    var buttonCell = buttonRow.insertCell(0);
    buttonCell.innerHTML = '<td colspan="18"><button onclick="errorremoveRow()">-</button></td>';
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



function respremoveRow() {
        var table = document.getElementById("responseTable");

        // Remove the last row
        table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button

        // Add a new "-" button to the end of the table
        var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
        var buttonCell = buttonRow.insertCell(0);
        buttonCell.innerHTML = '<td colspan="18"><button onclick="respaddRow()">+</button></td>';
    }


function errorremoveRow() {
        var table = document.getElementById("responseTable");

        // Remove the last row
        table.deleteRow(table.rows.length - 2); // -2 to skip the last row with the submit button

        // Add a new "-" button to the end of the table
        var buttonRow = table.insertRow(table.rows.length - 1); // Insert before the last row (submit button)
        var buttonCell = buttonRow.insertCell(0);
        buttonCell.innerHTML = '<td colspan="18"><button onclick="respaddRow()">+</button></td>';
    }






    /*function submitForm() {
        // Add logic to handle form submission
        alert("Form Submitted!");
    }


    const selectElement = document.getElementById('selectWithImages');
    const imageElement = document.getElementById('selectedImage');

    selectElement.addEventListener('change', function () {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const imageUrl = selectedOption.getAttribute('data-image');
        imageElement.src = imageUrl;
    });*/



var debounceTimer;

function updateTable() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function() {
        var frameSizeInput = document.getElementById('cmd_noofrows');
        var frameSize = parseInt(frameSizeInput.value) || 0; // Get the value or default to 0 if empty or NaN
        var table = document.getElementById('additionalTable');

        // Clear previous rows
        while (table.rows.length > 1) {
            table.deleteRow(1);
        }

        // Add new rows
        for (var i = 0; i < Math.max(1, frameSize); i++) {
            addRow(i + 1);
        }
    }, 500);
}


function respupdateTable() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function() {
        var frameSizeInput = document.getElementById('resp_noofrows');
        var frameSize = parseInt(frameSizeInput.value) || 0; // Get the value or default to 0 if empty or NaN
        var table = document.getElementById('responseTable');

        // Clear previous rows
        while (table.rows.length > 1) {
            table.deleteRow(1);
        }

        // Add new rows
        for (var i = 0; i < Math.max(1, frameSize); i++) {
            respaddRow(i + 1);
        }
    }, 500);
}



function errorupdateTable() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(function() {
        var frameSizeInput = document.getElementById('error_noofrows');
        var frameSize = parseInt(frameSizeInput.value) || 0; // Get the value or default to 0 if empty or NaN
        var table = document.getElementById('errorTable');

        // Clear previous rows
        while (table.rows.length > 1) {
            table.deleteRow(1);
        }

        // Add new rows
        for (var i = 0; i < Math.max(1, frameSize); i++) {
            erroraddRow(i + 1);
        }
    }, 500);
}


function saveRowData() {
    var newRow = document.getElementById("additionalTable").querySelector("tbody").lastElementChild;

    var rowData = {
        startBytePosition: newRow.querySelector("[name='startBytePosition_1']").value,
        endBytePosition: newRow.querySelector("[name='endBytePosition_1']").value,
        fieldDescription: newRow.querySelector("[name='fieldDescription_1']").value,
        numberOfBytes: newRow.querySelector("[name='numberOfBytes_1']").value,
        bitSize: newRow.querySelector("[name='bitSize_1']").value,
        dataType: newRow.querySelector("[name='dataType_1']").value,
        valueDefinition: newRow.querySelector("[name='valueDefinition_1']").value,
        displayValueMin: newRow.querySelector("[name='displayValueMin_1']").value,
        displayValueMax: newRow.querySelector("[name='displayValueMax_1']").value,
        valueInHexMin: newRow.querySelector("[name='valueInHexMin_1']").value,
        valueInHexMax: newRow.querySelector("[name='valueInHexMax_1']").value,
        uiType: newRow.querySelector("[name='uiType_1']").value,
        uiId: newRow.querySelector("[name='uiId_1']").value,
        uiPageNo: newRow.querySelector("[name='uiPageNo_1']").value,
        uiDescriptionInOut: newRow.querySelector("[name='uiDescriptionInOut_1']").value,
        sliderStepValue: newRow.querySelector("[name='sliderStepValue_1']").value,
        developerNotes: newRow.querySelector("[name='developerNotes_1']").value
    };

    // Send the data to backend using fetch API
    fetch('/items/saveapi', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(rowData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Data saved successfully:', data);
    })
    .catch(error => {
        console.error('Error saving data:', error);
    });
}






function sendDataToBackend(data) {
    
    fetch('your_backend_endpoint_url', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Data saved successfully:', data);
    })
    .catch(error => {
        console.error('Error saving data:', error);
    });
}






// Function to call updateTable() initially and on input change
    function callUpdateTable() {
        var frameSize = parseInt(document.getElementById('cmd_noofrows').value);
        updateTable(frameSize);
    }

    // Call the function initially
    callUpdateTable();

    // Update table on input change
    document.getElementById('cmd_noofrows').addEventListener('input', function() {
        callUpdateTable();
    });


</script>


<?php echo form_close(); ?>
