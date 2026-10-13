<?php echo form_open_multipart(get_uri("items/saveapi_edit"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
           

            <h4 style="text-align: center; color: green;">PRODUCT INFORMATION</h4>
<br><br>
<div class="commandname">

    
      <div class="form-group">
        <label class=" col-sm-2" for="email">Product Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="productname" value="<?php echo $productname; ?>" name="productname" readonly>
        </div>
      </div>
      
       <!--div class="form-group">
        <label class="col-sm-2" for="imageUpload">Device Image:</label>
        <small>Ex : Image of Device</small>
        <div class="col-sm-2">
            <input type="file" class="form-control" id="imageUpload" name="imageUpload" accept="image/*" onchange="changeImage(this)">
            <img id="imageUpload-preview" src="#" alt="imageUpload Preview" alt="Displayed Image" style="display: none; max-width: 100px; max-height: 100px;">
            <?php if (!empty($displayedImage)): ?>
    <img src="<?php echo $displayedImage; ?>" alt="Displayedd Image" style="max-width: 100px; max-height: 100px;">
<?php endif; ?>
        </div>
    </div-->
    



      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Brand Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="brandname" value="<?php echo $brandname; ?>" name="brandname" readonly>
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Model Number:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="modelname" value="<?php echo $modelname; ?>" name="modelname" readonly>
        </div>
      </div>
      
         <div class="form-group">
        <label class=" col-sm-2" for="pwd">Date:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="date" value="<?php echo $date; ?>" name="date">
        </div>
       </div>
      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Page Number:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="numberofpages" value="<?php echo $numberofpages; ?>" name="numberofpages" readonly>
        </div>
      </div>
      
      <div class="form-group">
        <label class=" col-sm-2" for="Sequence">Sequence no:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="Sequence" value="<?php echo $Sequence; ?>" name="Sequence" readonly>
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Page names:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="pagenames" value="<?php echo $pagenames; ?>" name="pagenames" readonly>
        </div>
      </div>



           <!--div class="form-group">
        <label class="col-sm-2" for="brandlogo">Brand Logo:</label>
        <small>Ex : Logo of brand</small>
        <div class="col-sm-2">
            <input type="file" class="form-control" id="brandlogo" name="brandlogo" accept="image/*" onchange="showImagePreview(this)">
            <img id="brandlogo-preview" src="#" alt="Brand Logo Preview" style="display: none; max-width: 100px; max-height: 100px;">
            <?php if (!empty($brandlogo)): ?>
    <img src="<?php echo $brandlogo; ?>"  style="max-width: 100px; max-height: 100px;">
    <?php elseif (empty($brandlogo)): ?>
    
<?php endif; ?>
        </div>
    </div-->


       <div class="form-group">
    <label class="col-sm-2" for="product_description">Product Description:</label>
    <div class="col-sm-4">
        <textarea class="form-control" id="product_description" value="<?php echo $product_description; ?>"  name="product_description" rows="4" readonly></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>





<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
          

            <h4 style="text-align: center; color: green;">COMMAND FRAME</h4>


 <h2>Command Frame Information</h2>



<br><br>
<div class="commandname">


   
      <!--div class="form-group">
        <label class=" col-sm-2" for="email">Parameter Name</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_parametername" value="<!?php echo $cmd_parametername; ?>" name="cmd_parametername">
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
          <input type="text" class="form-control" id="cmd_data_flow_direction" value="<?php echo $cmd_dataflow; ?>" name="cmd_data_flow_direction" readonly>
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametype"  name="cmd_frametype" readonly>
        </div>
       </div>


 





<div class="form-group">
    <label class="col-sm-2" for="pwd">Frame size in bytes</label>
    <div class="col-sm-2">          
       <input type="text" class="form-control" id="cmd_framesizeinbytes" value="<?php echo $cmd_framesizeinbytes; ?>" name="cmd_framesizeinbytes" readonly>
    </div>
</div>



<div class="form-group">
                    <label class=" col-sm-2" for="cmd_noofrows">Number of Rows:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="cmd_noofrows" value="<?php echo $cmd_noofrows; ?>" name="cmd_noofrows" oninput="updateTable()" readonly>
                    </div>
                </div>

 


      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametrigger" value="<?php echo $cmd_frametrigger; ?>" name="cmd_frametrigger" readonly>
        </div>
      </div>


<div class="form-group">
        <label class=" col-sm-2" for="pwd">MultipleUI</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_multipleui" value="<?php echo $cmd_multipleui; ?>" name="cmd_multipleui" readonly>
        </div>
      </div>

 <div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Trigger UI</label>
                                                    <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_triggerui" value="<?php echo $cmd_triggerui; ?>" name="cmd_triggerui" readonly>
                                                    </div>
                                                </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_voicecommand"   name="cmd_voicecommand">
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
        <textarea class="form-control" id="cmd_appnotification"  name="cmd_appnotification" rows="4"></textarea>
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
<div class="table-responsive">
<div style="overflow-x: auto;">
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
           <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
            <th>AS PER UI VALUES</th>
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Default Values</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
<tbody>
<tbody>
 <?php for ($i = 0; $i < $cmd_noofrows; $i++) { ?>




<!-- Initial row with input fields and select dropdowns -->
<tr id="row_<?php echo $i + 1; ?>">
    <!-- Individual fields with IDs -->
    <td id="sno_1"><?php echo $i + 1; ?></td>
<td><input type="text" id="startBytePosition_cmd[]" name="startBytePosition_cmd[]" value="<?php echo $startBytePosition_cmd[$i]; ?>" readonly></td>
    <td><input type="text" id="endBytePosition_cmd[]" name="endBytePosition_cmd[]" value="<?php echo $endBytePosition_cmd[$i]; ?>" readonly></td>        

 
<td><input type="text" id="fieldDescription_cmd[]" name="fieldDescription_cmd[]" value="<?php echo $fieldDescription_cmd[$i]; ?>" readonly></td>       

        <td><input type="text" id="numberOfBytes_cmd[]" name="numberOfBytes_cmd[]" value="<?php echo $numberOfBytes_cmd[$i]; ?>" readonly></td>
        <td><input type="text" id="bitSize_cmd[]" name="bitSize_cmd[]" value="<?php echo $bitSize_cmd[$i]; ?>" readonly></td>
  
       <td><input type="text" id="dataType_cmd[]" name="dataType_cmd[]" value="<?php echo $dataType_cmd[$i]; ?>" readonly></td>
       <td><input type="text" id="valueDefinition_cmd[]" name="valueDefinition_cmd[]" value="<?php echo $valueDefinition_cmd[$i]; ?>" readonly></td>
       

        <td><input type="text" id="displayValueMin_cmd[]" value="<?php echo $displayValueMin_cmd[$i]; ?>" name="displayValueMin_cmd[]" readonly></td>
        <td><input type="text" id="displayValueMax_cmd[]" value="<?php echo $displayValueMax_cmd[$i]; ?>" name="displayValueMax_cmd[]" readonly></td>
        <td><input type="text" id="valueInHexMin_cmd[]" value="<?php echo $valueInHexMin_cmd[$i]; ?>" name="valueInHexMin_cmd[]" readonly></td>
        <td><input type="text" id="valueInHexMax_cmd[]" value="<?php echo $valueInHexMax_cmd[$i]; ?>" name="valueInHexMax_cmd[]" readonly></td>
   
<td><input type="text" id="uiType_cmd[]" name="uiType_cmd[]" value="<?php echo $uiType_cmd[$i]; ?>" readonly></td>

        <td><input type="text" id="uiId_cmd[]" value="<?php echo $uiId_cmd[$i]; ?>" name="uiId_cmd[]" readonly></td>
        <td><input type="text" id="uiPageNo_cmd[]" value="<?php echo $uiPageNo_cmd[$i]; ?>" name="uiPageNo_cmd[]" readonly></td>
       

       <?php
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
?>

<td>
    <input type="text" id="cmd_asperui[]" value="<?php echo htmlspecialchars($formatted_as_per_ui); ?>" name="cmd_asperui[]">
</td>

<?php
$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$sno_formatted_as_per_ui = implode(',', $sno_as_per_ui_array);
?>

<td>
    <input type="text" id="cmd_sno_asperui[]" value="<?php echo htmlspecialchars($sno_formatted_as_per_ui); ?>" name="cmd_sno_asperui[]">
</td>


  <?php
$as_per_ui = $cmd_asperuivalue[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
?>

 <td><input type="text" id="cmd_asperuivalue[]" value="<?php echo htmlspecialchars($formatted_as_per_ui); ?>" name="cmd_asperuivalue[]"></td>
  
<td><input type="text" id="uiDescriptionInOut_cmd[]" value="<?php echo $uiDescriptionInOut_cmd[$i]; ?>" name="uiDescriptionInOut_cmd[]" readonly></td>


        <td><input type="text" id="sliderStepValue_cmd[]" value="<?php echo $sliderStepValue_cmd[$i]; ?>" name="sliderStepValue_cmd[]"></td>
        <td><input type="text" id="defaultvalue_cmd[]" value="<?php echo $defaultvalue_cmd[$i]; ?>" name="defaultvalue_cmd[]"></td>
        <td><input type="text" id="developerNotes_cmd[]" value="<?php echo $developerNotes_cmd[$i]; ?>" name="developerNotes_cmd[]"></td>
    </tr>


<?php
}
?>
</tbody>
</table>
  <tr>
                                                           <button type="button" onclick="addRow()">+</button>
                                                           </tr>
</div>
</div>
</div>




<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
           

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
          <input type="text" class="form-control" id="resp_frametype"  name="resp_frametype">
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
        <label class=" col-sm-2" for="pwd"> MultipleUI</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_multipleui" value="<?php echo $resp_multipleui; ?>" name="resp_multipleui">
        </div>
      </div>

<div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Trigger UI</label>
                                                    <div class="col-sm-4">
        <input type="text" class="form-control" id="resp_triggerui" value="<?php echo $resp_triggerui; ?>" name="resp_triggerui">
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
           <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
            <th>AS PER UI VALUES</th>
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Default Values</th>
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
        $options = array("None","CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data", "Error Code", "Operation Code","Trigger");
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
        $options = array("None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt","String");
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
        $options = array("None","Fixed Value", "Range Value", "Mode Value", "ASCII Value");
        foreach ($options as $option) {
            $selected = ($valueDefinition_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $fieldDescription_cmd[$i]
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
        $options = array("None", "Text", "Toggle Switch", "Slider", "Brightness Slider",  "Airflow Slider","CCT Slider", "Saturation Slider", "Hue Slider", "Hue Circle",  "Hue Slider Custom", "Push Button", "Drop Down", "Trigger", "Display", "Button","Toast Display","Auto Trigger with Timer");
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
        $options = array("None","0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10");
        foreach ($options as $option) {
            $selected = ($uiPageNo_resp[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td>  

   <?php
$as_per_ui = $resp_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
?>

<td><input type="text" id="resp_asperui[]" value="<?php echo htmlspecialchars($formatted_as_per_ui); ?>" name="resp_asperui[]"></td>

<?php
$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$sno_formatted_as_per_ui = implode(',', $sno_as_per_ui_array);
?>

<td><input type="text" id="resp_sno_asperui[]" value="<?php echo htmlspecialchars($sno_formatted_as_per_ui); ?>" name="resp_sno_asperui[]"></td>


 <?php
$as_per_ui = $resp_asperuivalue[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
?>

<td><input type="text" id="resp_asperuivalue[]"value="<?php echo htmlspecialchars($formatted_as_per_ui); ?>" name="resp_asperuivalue[]">  </td>

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
         <td><input type="text" id="defaultvalue_resp[]" value="<?php echo $defaultvalue_resp[$i]; ?>" name="defaultvalue_resp[]"></td>
        <td><input type="text" id="developerNotes_resp[]" value="<?php echo $developerNotes_resp[$i]; ?>" name="developerNotes_resp[]"></td>
    </tr>


<?php
}
?>


</tbody>
</table>
  <tr>
                                                           <button type="button" onclick="respaddRow()">+</button>
                                                           </tr>
</div>
</div>
</div>



<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            

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
          <input type="text" class="form-control" id="error_frametype" name="error_frametype"  >
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
        <label class=" col-sm-2" for="pwd">MultipleUI</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="error_multipleui" value="<?php echo $error_multipleui; ?>" name="error_multipleui">
        </div>
      </div>

 <div class="form-group">
                                                    <label class="col-sm-2" for="pwd">Trigger UI</label>
                                                    <div class="col-sm-4">
 <input type="text" class="form-control" id="error_triggerui" value="<?php echo $error_triggerui; ?>" name="error_triggerui">   
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
            <th>Serial Number of UI<small style="color:red">(ENTER FROM ZERO(0))</small></th>
            <th>AS PER UI VALUES</th>
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Default Values</th>
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
        $options = array("None", "CommandId", "Function Code", "Checksum", "Terminator", "ActionId", "Data", "Error Code", "Operation Code","Trigger");
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
        $options = array("None", "ASCII", "Binary", "SignedInt", "Float", "UnsignedInt","String");
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
        $options = array("None", "Fixed Value", "Range Value", "Mode Value", "ASCII Value");
        foreach ($options as $option) {
            $selected = ($valueDefinition_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $fieldDescription_cmd[$i]
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
        $options = array("None", "Text", "Toggle Switch", "Slider", "Brightness Slider", "Airflow Slider", "CCT Slider", "Saturation Slider", "Hue Slider Custom", "Hue Slider", "Hue Circle", "Push Button", "Drop Down", "Trigger", "Display", "Button","Toast Display","Auto Trigger with Timer");
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
        $options = array("None","0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10");
        foreach ($options as $option) {
            $selected = ($uiPageNo_error[$i] == $option) ? "selected" : ""; // Check if the current option matches the value from $uiType_cmd[$i]
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
</td> 

 <?php
$as_per_ui = $error_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
?>

  <td><input type="text" id="error_asperui[]" value="<?php echo htmlspecialchars($formatted_as_per_ui); ?>" name="error_asperui[]"></td> 

  <?php
$sno_as_per_ui = $error_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$sno_formatted_as_per_ui = implode(',', $sno_as_per_ui_array);
?>

  <td><input type="text" id="error_sno_asperui[]" value="<?php echo htmlspecialchars($sno_formatted_as_per_ui); ?>" name="error_sno_asperui[]"></td> 

 <?php
$as_per_ui = $error_asperuivalue[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
?>

  <td><input type="text" id="error_asperuivalue[]"value="<?php echo htmlspecialchars($formatted_as_per_ui); ?>" name="error_asperuivalue[]"></td>


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
         <td><input type="text"  value="<?php echo $defaultvalue_error[$i]; ?>" id="defaultvalue_error[]" name="defaultvalue_error[]"></td>   
        <td><input type="text" id="developerNotes_error[]" name="developerNotes_error[]" value="<?php echo $developerNotes_error[$i]; ?>"></td>
    </tr>


<?php
}
?>


</tbody>
</table>
  <tr>
                                                           <button type="button" onclick="erroraddRow()">+</button>
                                                           </tr>
<div class="modal-footer">   
<input style="margin-top: 20px; color: white; border: none; background-color: green;" type="submit" value="upload" name="submit">
</div>
</div>
</div>
</div>


<script type="text/javascript">
 function addRow() {
    var table = document.getElementById("additionalTable");
    var index = table.rows.length; 
                            var newRow = table.insertRow(-1); // Insert at the end of the table
                            var cells = [
                                '<td>' + index + '</td>', // S.No
                                '<td><input type="text"   name="startBytePosition_cmd_' + index + '" ></td>',
                                '<td><input type="text"   name="endBytePosition_cmd_' + index + '"></td>',
                                '<td><select name="fieldDescription_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="CommandId">CommandId</option>' +
                                '<option value="Function Code">Function Code</option>' +
                                '<option value="Checksum">Checksum</option>' +
                                '<option value="Terminator">Terminator</option>' +
                                '<option value="ActionId">ActionId</option>' +
                                '<option value="Data">Data</option>' +
                                '<option value="Error Code">Error Code</option>' +
                                '<option value="Error details">Error details</option>'+
                                '<option value="Operation Code">Operation Code</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value=\"$option\" $selected>$option</option>'+
                                '</select></td>',
                                '<td><input type="text" name="numberOfBytes_cmd_' + index + '"></td>',
                                '<td><input type="text"  name="bitSize_cmd_' + index + '"></td>',

                                '<td><select name="dataType_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '<option value="Binary">Binary</option>' +
                                '<option value="SignedInt">Signed Int</option>' +
                                '<option value="Float">Float</option>' +
                                '<option value="UnsignedInt">Unsigned Int</option>' +
                                '<option value="String">String</option>'+
                                '</select></td>',


                                '<td><select name="valueDefinition_cmd' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Fixed Value">Fixed Value</option>' +
                                '<option value="Range Value">Range Value</option>' +
                                '<option value="Mode Value">Mode Value</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '</select></td>',


                                '<td><input type="text"  name="displayValueMin_cmd_' + index + '"></td>',
                                '<td><input type="text"  name="displayValueMax_cmd_' + index + '"></td>',
                                '<td><input type="text"  name="valueInHexMin_cmd_' + index + '"></td>',
                                '<td><input type="text"  name="valueInHexMax_cmd_' + index + '"></td>',
                                '<td><select name="uiType_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Text">Text</option>' +
                                '<option value="Toggle Switch">Toggle Switch</option>' +
                                '<option value="Slider">Slider</option>' +
                                '<option value="Brightness Slider">Brightness Slider</option>' +
                                '<option value="Airflow Slider">Airflow Slider</option>' +
                                '<option value="CCT Slider">CCT Slider</option>' +
                                '<option value="Saturation Slider">Saturation Slider</option>' +
                                '<option value="Hue Slider">Hue Slider</option>' +
                                '<option value="Hue Slider Custom">Hue Slider Custom</option>' +
                                '<option value="Hue Circle">Hue Circle</option>' +
                                '<option value="Push Button">Push Button (Big)</option>' +
                                '<option value="Drop Down">Drop Down</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value="Display">Display</option>' +
                                '<option value="Button">Button (Big)</option>' +  
                                '<option value="Toast Display">Toast Display</option>'+
                                '<option value=" Auto Trigger with Timer "> Auto Trigger with Timer </option>'+


                                '</select></td>',
                                '<td><input type="text"  name="uiId_cmd_' + index + '"></td>',



                                '<td><select name="uiPageNo_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="0">0</option>' +
                                '<option value="1">1</option>' +
                                '<option value="2">2</option>' +
                                '<option value="3">3</option>' +
                                '<option value="4">4</option>' +
                                '<option value="5">5</option>' +
                                '<option value="6">6</option>' +
                                '<option value="7">7</option>' +
                                '<option value="8">8</option>' +
                                '<option value="9">9</option>' +
                                '<option value="10">10</option>' +
                                '</select></td>',

 '<td><input type="text" name="asperui_cmd_' + index + '"></td>',
 '<td><input type="text" name="sno_asperui_cmd_' + index + '"></td>',
 '<td><input type="text" name="asperuivalue_cmd_' + index + '"></td>',


                                '<td><select name="uiDescriptionInOut_cmd_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Read/Write">Read/Write</option>' +
                                '<option value="Read">Read</option>' +
                                '<option value="Write">Write</option>' +
                                '</select></td>',


                                '<td><input type="text"   name="sliderStepValue_cmd_' + index + '"></td>',
                                '<td><input type="text"   name="defaultvalue_cmd_' + index + '"></td>',
                                '<td><input type="text"   name="developerNotes_cmd_' + index + '"></td>'
                            ];

                           // Insert cells into the new row
                            for (var i = 0; i < cells.length; i++) {
                                var cell = newRow.insertCell(i);
                                cell.innerHTML = cells[i];
                            }

                            
                            var removeCell = newRow.insertCell(-1);
                            removeCell.innerHTML = '<button onclick="removeRow(this)">Remove</button>';
                        }




                        var debounceTimer;

                       function updateTable() {
                            clearTimeout(debounceTimer);

                            debounceTimer = setTimeout(function () {
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


function removeRow(button) {
    var row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}















                        /******************************************************************/

                        /*********response table*******************************************/
 function respaddRow() {
    var table = document.getElementById("responseTable");
    var index = table.rows.length;  
                            var newRow = table.insertRow(-1); // Insert at the end of the table
                            var cells = [
                                '<td>' + index + '</td>', // S.No
                                '<td><input type="text"  name="startBytePosition_resp_' + index + '"></td>',
                                '<td><input type="text"   name="endBytePosition_resp_' + index + '"></td>',
                                '<td><select name="fieldDescription_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="CommandId">CommandId</option>' +
                                '<option value="Function Code">Function Code</option>' +
                                '<option value="Checksum">Checksum</option>' +
                                '<option value="Terminator">Terminator</option>' +
                                '<option value="ActionId">ActionId</option>' +
                                '<option value="Data">Data</option>' +
                                '<option value="Error Code">Error Code</option>' +
                                '<option value="Error details">Error details</option>'+
                                '<option value="Operation Code">Operation Code</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '</select></td>',
                                '<td><input type="text"   name="numberOfBytes_resp_' + index + '"></td>',
                                '<td><input type="text"   name="bitSize_resp_' + index + '"></td>',
                                '<td><select name="dataType_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '<option value="Binary">Binary</option>' +
                                '<option value="SignedInt">Signed Int</option>' +
                                '<option value="Float">Float</option>' +
                                '<option value="UnsignedInt">Unsigned Int</option>' +
                                '<option value="String">String</option>'+
                                '</select></td>',

                                '<td><select name="valueDefinition_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Fixed Value">Fixed Value</option>' +
                                '<option value="Range Value">Range Value</option>' +
                                '<option value="Mode Value">Mode Value</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '</select></td>',


                                '<td><input type="text"  name="displayValueMin_resp_' + index + '"></td>',
                                '<td><input type="text"  name="displayValueMax_resp_' + index + '"></td>',
                                '<td><input type="text"  name="valueInHexMin_resp_' + index + '"></td>',
                                '<td><input type="text"  name="valueInHexMax_resp_' + index + '"></td>',
                                '<td><select name="uiType_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Text">Text</option>' +
                                '<option value="Toggle Switch">Toggle Switch</option>' +
                                '<option value="Slider">Slider</option>' +
                                '<option value="Brightness Slider">Brightness Slider</option>' +
                                '<option value="Airflow Slider">Airflow Slider</option>' +
                                '<option value="CCT Slider">CCT Slider</option>' +
                                '<option value="Saturation Slider">Saturation Slider</option>' +
                                '<option value="Hue Slider">Hue Slider</option>' +
                                '<option value="Hue Slider Custom">Hue Slider Custom</option>' +
                                '<option value="Hue Circle">Hue Circle</option>' +
                                '<option value="Push Button">Push Button (Big)</option>' +
                                '<option value="Drop Down">Drop Down</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value="Display">Display</option>' +
                                '<option value="Button">Button (Big)</option>' +
                                '<option value="Toast Display">Toast Display</option>'+
                                '<option value=" Auto Trigger with Timer "> Auto Trigger with Timer </option>'+
                                
                                '</select></td>',


                                '<td><input type="text"   name="uiId_resp_' + index + '"></td>',



                                '<td><select name="uiPageNo_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="0">0</option>' +
                                '<option value="1">1</option>' +
                                '<option value="2">2</option>' +
                                '<option value="3">3</option>' +
                                '<option value="4">4</option>' +
                                '<option value="5">5</option>' +
                                '<option value="6">6</option>' +
                                '<option value="7">7</option>' +
                                '<option value="8">8</option>' +
                                '<option value="9">9</option>' +
                                '<option value="10">10</option>' +
                                '</select></td>',
                                
                                
                                 '<td><input type="text"   name="asperui_resp_' + index + '"></td>',
                                 '<td><input type="text"   name="sno_asperui_resp_' + index + '"></td>',
                                  '<td><input type="text"   name="asperuivalue_resp_' + index + '"></td>',



                                '<td><select name="uiDescriptionInOut_resp_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Read/Write">Read/Write</option>' +
                                '<option value="Read">Read</option>' +
                                '<option value="Write">Write</option>' +
                                '</select></td>',


                                '<td><input type="text"   name="sliderStepValue_resp_' + index + '"></td>',
                                 '<td><input type="text"   name="defaultvalue_resp_' + index + '"></td>',
                                '<td><input type="text"   name="developerNotes_resp_' + index + '"></td>'
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



                        /*
                        function respupdateTable() {
                        
                        clearTimeout(debounceTimer);
                        
                            var frameSize = parseInt(document.getElementById('resp_noofrows').value);
                            var table = document.getElementById('responseTable');
                        
                            debounceTimer = setTimeout(function() {
                                   
                            while (table.rows.length > 2) {
                                table.deleteRow(1);
                            }
                                for (var i = 0; i < frameSize; i++) {
                                respaddRow(i + 1);     }
                        
                                }, 500); 
                        }
                        */


                       function respupdateTable() {
                            clearTimeout(debounceTimer);

                            debounceTimer = setTimeout(function () {
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






                        /********************************************************************/
                        /**************error table******************************************/
function erroraddRow() {
    var table = document.getElementById("errorTable");
    var index = table.rows.length;  
                            var newRow = table.insertRow(-1); // Insert at the end of the table
                            var cells = [
                                '<td>' + index + '</td>', // S.No
                                '<td><input type="text" name="startBytePosition_error_' + index + '"></td>',
                                '<td><input type="text" name="endBytePosition_error_' + index + '"></td>',
                                '<td><select name="fieldDescription_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="CommandId">CommandId</option>' +
                                '<option value="Function Code">Function Code</option>' +
                                '<option value="Checksum">Checksum</option>' +
                                '<option value="Terminator">Terminator</option>' +
                                '<option value="ActionId">ActionId</option>' +
                                '<option value="Data">Data</option>' +
                                '<option value="Error Code">Error Code</option>' +
                                '<option value="Error details">Error details</option>'+
                                '<option value="Operation Code">Operation Code</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '</select></td>',
                                
                                '<td><input type="text" name="numberOfBytes_error_' + index + '"></td>',
                                '<td><input type="text" name="bitSize_error_' + index + '"></td>',
                                '<td><select name="dataType_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '<option value="Binary">Binary</option>' +
                                '<option value="SignedInt">Signed Int</option>' +
                                '<option value="Float">Float</option>' +
                                '<option value="UnsignedInt">Unsigned Int</option>' +
                                '<option value="String">String</option>'+
                                '</select></td>',


                                '<td><select name="valueDefinition_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Fixed Value">Fixed Value</option>' +
                                '<option value="Range Value">Range Value</option>' +
                                '<option value="Mode Value">Mode Value</option>' +
                                '<option value="ASCII">ASCII</option>' +
                                '</select></td>',

                                '<td><input type="text"   name="displayValueMin_error_' + index + '"></td>',
                                '<td><input type="text"   name="displayValueMax_error_' + index + '"></td>',
                                '<td><input type="text"  name="valueInHexMin_error_' + index + '"></td>',
                                '<td><input type="text"   name="valueInHexMax_error_' + index + '"></td>',
                                '<td><select name="uiType_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Text">Text</option>' +
                                '<option value="Toggle Switch">Toggle Switch</option>' +
                                '<option value="Slider">Slider</option>' +
                                '<option value="Brightness Slider">Brightness Slider</option>' +
                                '<option value="Airflow Slider">Airflow Slider</option>' +
                                '<option value="CCT Slider">CCT Slider</option>' +
                                '<option value="Saturation Slider">Saturation Slider</option>' +
                                '<option value="Hue Slider">Hue Slider</option>' +
                                '<option value="Hue Slider Custom">Hue Slider Custom</option>' +
                                '<option value="Hue Circle">Hue Circle</option>' +
                                '<option value="Push Button">Push Button (Big)</option>' +
                                '<option value="Drop Down">Drop Down</option>' +
                                '<option value="Trigger">Trigger</option>' +
                                '<option value="Display">Display</option>' +
                                '<option value="Button">Button (Big)</option>' +
                                '<option value="Toast Display">Toast Display</option>'+
                                '<option value=" Auto Trigger with Timer "> Auto Trigger with Timer </option>'+
                                '</select></td>',

                                '<td><input type="text"   name="uiId_error_' + index + '"></td>',


                                '<td><select name="uiPageNo_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="0">0</option>' +
                                '<option value="1">1</option>' +
                                '<option value="2">2</option>' +
                                '<option value="3">3</option>' +
                                '<option value="4">4</option>' +
                                '<option value="5">5</option>' +
                                '<option value="6">6</option>' +
                                '<option value="7">7</option>' +
                                '<option value="8">8</option>' +
                                '<option value="9">9</option>' +
                                '<option value="10">10</option>' +
                                '</select></td>',
                                
                                
                                '<td><input type="text"   name="asperui_error_' + index + '"></td>',
                                '<td><input type="text"   name="sno_asperui_error_' + index + '"></td>',
                                '<td><input type="text"   name="asperuivalue_error_' + index + '"></td>',



                                '<td><select name="uiDescriptionInOut_error_' + index + '">' +
                                '<option value="None">None</option>' +
                                '<option value="Read/Write">Read/Write</option>' +
                                '<option value="Read">Read</option>' +
                                '<option value="Write">Write</option>' +
                                '</select></td>',


                                '<td><input type="text"  name="sliderStepValue_error_' + index + '"></td>',
                                 '<td><input type="text"   name="defaultvalue_error_' + index + '"></td>',
                                '<td><input type="text"  name="developerNotes_error_' + index + '"></td>'
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





                        function errorupdateTable() {
                            clearTimeout(debounceTimer);

                            debounceTimer = setTimeout(function () {
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








                        /*****************************************************************/

                        function showImagePreview(input) {
                            var preview = document.getElementById('brandlogo-preview');
                            var file = input.files[0];
                            var reader = new FileReader();

                            reader.onloadend = function () {
                                preview.src = reader.result;
                                preview.style.display = 'block'; // Show the preview image
                            }

                            if (file) {
                                reader.readAsDataURL(file);
                            } else {
                                preview.src = '#'; // Clear the preview image
                                preview.style.display = 'none'; // Hide the preview image
                            }
                        }
                        
                           function changeImage(input) {
                            var preview = document.getElementById('imageUpload-preview');
                            var file = input.files[0];
                            var reader = new FileReader();

                            reader.onloadend = function () {
                                preview.src = reader.result;
                                preview.style.display = 'block'; // Show the preview image
                            }

                            if (file) {
                                reader.readAsDataURL(file);
                            } else {
                                preview.src = '#'; // Clear the preview image
                                preview.style.display = 'none'; // Hide the preview image
                            }
                        }










                        function checkWordCount(textarea) {
                            var maxWords = 300;
                            var wordCount = textarea.value.trim().split(/\s+/).length;

                            if (wordCount > maxWords) {
                                // If the word count exceeds the limit, truncate the content
                                var words = textarea.value.trim().split(/\s+/);
                                textarea.value = words.slice(0, maxWords).join(' ');
                                alert('Maximum word limit exceeded. Content has been truncated.');
                            }
                        }
                        /******************************************************************/
                        // Flag to track if the scrollbar is being interacted with
                        var scrollbarInteracted = false;

                        // Check if the form has unsaved changes
                        function formIsDirty() {
                            // Implement your logic here to check if the form is dirty
                            // For example, you can compare current form values with initial form values
                            // If there are unsaved changes, return true; otherwise, return false
                        }

                        // Attach event handler to the modal close event
                        $('#myModal').on('hide.bs.modal', function (e) {
                            // Check if the form is dirty and the scrollbar is not being interacted with
                            if (formIsDirty() && !scrollbarInteracted) {
                                // Prevent the modal from closing
                                e.preventDefault();

                                // Display a confirmation dialog
                                if (confirm("You have unsaved changes. Are you sure you want to close the form?")) {
                                    // If the user confirms, close the modal
                                    $('#myModal').modal('hide');
                                }
                            }
                        });

                        // Function to save the form data
                        function saveForm() {
                            // Perform form validation and save data to the server
                            // After successful save, close the modal
                            $('#myModal').modal('hide');
                        }

                        // Attach event handlers to track scrollbar interactions
                        $('#myModal').on('mousedown', function () {
                            scrollbarInteracted = true;
                        });

                        $(window).on('mouseup', function () {
                            scrollbarInteracted = false;
                        });
                        /******************************************************************/







                    </script>




<?php echo form_close(); ?>
