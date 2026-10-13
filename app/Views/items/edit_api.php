<?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">PRODUCT INFORMATION</h4>
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
          <input type="text" class="form-control" id="email" placeholder="productname" name="productname">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="email">Model No:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="email" placeholder="modelno" name="modelno">
        </div>
      </div>


          <div class="form-group">
        <label class=" col-sm-2" for="email">Model Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="email" placeholder="modelname" name="modelname">
        </div>
      </div>

           <div class="form-group">
        <label class=" col-sm-2" for="email">Description:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="description" placeholder="description" name="description">
        </div>
      </div> -->


   
      <div class="form-group">
        <label class=" col-sm-2" for="email">Product Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="productname" name="productname">
        </div>
      </div>





      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Brand Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="brandname" name="brandname">
        </div>
      </div>



        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Date:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="date" name="date">
        </div>
       </div>



        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Model Name:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="modelname" name="modelname">
        </div>
      </div>
      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Number of pages:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="numberofpages" name="numberofpages">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Page names:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="pagenames" name="pagenames">
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
        <textarea class="form-control" id="productdescription" placeholder="Product Description" name="productdescription" rows="4"></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>


 





<!--?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?-->
<div id="items-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">COMMAND FRAME</h4>


 <h2>Command Frame Information</h2>



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
          <input type="text" class="form-control" id="cmd_parametername" name="cmd_parametername">
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Command Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="cmd_data_flow_direction" name="cmd_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametype" name="cmd_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" oninput="updateTable()">
    </div>
</div>

      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_frametrigger" name="cmd_frametrigger">
        </div>
      </div>


       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Voice Command</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="cmd_voicecommand" name="cmd_voicecommand">
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
        <textarea class="form-control" id="cmd_appnotification" placeholder="Product Description" name="cmd_appnotification" rows="4"></textarea>
        <small class="text-muted">Max 200 words</small>
    </div>
</div>

</div>




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
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>
    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_1">1</td>
        <td><input type="text" id="startBytePosition_cmd" name="startBytePosition_cmd" placeholder="1"></td>
        <td><input type="text" id="endBytePosition_cmd" name="endBytePosition_cmd" placeholder="1"></td>
        

         <td>
            <select id="fieldDescription_cmd" name="fieldDescription_cmd">
                <option value="CommandId" selected>CommandId</option>
                <option value="Function Code" >Function Code</option>
                <option value="Checksum">Checksum</option>
                <option value="Terminator">Terminator</option>
                <option value="ActionId">ActionId</option>
                <option value="Data">Data</option>
                <option value="Error Code">Error Code</option>
                <option value="Operation Code">Operation Code</option>
            </select>
        </td>
        <td><input type="text" id="numberOfBytes_cmd" name="numberOfBytes_cmd" placeholder="1"></td>
        <td><input type="text" id="bitSize_cmd" name="bitSize_cmd" placeholder="1"></td>
        <td>
            <select id="dataType_cmd" name="dataType_cmd">
                <option value="ASCII" selected>ASCII</option>
                <option value="Binary" >Binary</option>
                <option value="SignedInt">Signed Int</option>
                <option value="Float">Float</option>
                <option value="UnsignedInt">Unsigned Int</option>
            </select>
        </td>
        <td><input type="text" id="valueDefinition_cmd" name="valueDefinition_cmd" placeholder="1"></td>
        <td><input type="text" id="displayValueMin_cmd" name="displayValueMin_cmd" placeholder="1"></td>
        <td><input type="text" id="displayValueMax_cmd" name="displayValueMax_cmd" placeholder="1"></td>
        <td><input type="text" id="valueInHexMin_cmd" name="valueInHexMin_cmd" placeholder="1"></td>
        <td><input type="text" id="valueInHexMax_cmd" name="valueInHexMax_cmd" placeholder="1"></td>
        <td>
            <select id="uiType_cmd" name="uiType_cmd">
                <option value="ToggleSwitch" selected>Toggle Switch</option>
                <option value="Slider">Slider</option>
                <option value="BrightnessSlider">Brightness Slider</option>
                <option value="CCTSlider">CCT Slider</option>
                <option value="SaturationSlider">Saturation Slider</option>
                <option value="HueSlider">Hue Slider</option>
                <option value="HueCircle">Hue Circle</option>
                <option value="PushButton">Push Button (Big)</option>
            </select>
        </td>
        <td><input type="text" id="uiId_cmd" name="uiId_cmd" placeholder="1"></td>
        <td><input type="text" id="uiPageNo_cmd" name="uiPageNo_cmd" placeholder="1"></td>
        <td><input type="text" id="uiDescriptionInOut_cmd" name="uiDescriptionInOut_cmd" placeholder="1"></td>
        <td><input type="text" id="sliderStepValue_cmd" name="sliderStepValue_cmd" placeholder="1"></td>
        <td><input type="text" id="developerNotes_cmd" name="developerNotes_cmd" placeholder="1"></td>
    </tr>
    <tr>
        <td colspan="18"><button onclick="addRow()">+</button></td>
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
          <input type="text" class="form-control" id="resp_parametername" name="resp_parametername">
        </div>
      </div>

      <div class="form-group">
        <label class=" col-sm-2" for="pwd">Response Data Flow Direction</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="resp_data_flow_direction" name="resp_data_flow_direction">
        </div>
      </div>

        <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Type</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_frametype" name="resp_frametype">
        </div>
       </div>

        <div class="form-group">
    <label class=" col-sm-2" for="pwd">Response Frame size in bytes</label>
    <div class="col-sm-2">          
        <input type="text" class="form-control" id="resp_framesizeinbytes" name="resp_framesizeinbytes" oninput="respupdateTable()">
    </div>
</div>

      

       <div class="form-group">
        <label class=" col-sm-2" for="pwd">Frame Trigger</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="resp_frametrigger" name="resp_frametrigger">
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
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>
    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_1">1</td>
        <td><input type="text" id="startBytePosition_resp" name="startBytePosition_resp" placeholder="1"></td>
        <td><input type="text" id="endBytePosition_resp" name="endBytePosition_resp" placeholder="1"></td>
        

         <td>
            <select id="fieldDescription_resp" name="fieldDescription_resp">
                <option value="CommandId" selected>CommandId</option>
                <option value="Function Code" >Function Code</option>
                <option value="Checksum">Checksum</option>
                <option value="Terminator">Terminator</option>
                <option value="ActionId">ActionId</option>
                <option value="Data">Data</option>
                <option value="Error Code">Error Code</option>
                <option value="Operation Code">Operation Code</option>
            </select>
        </td>
        <td><input type="text" id="numberOfBytes_resp" name="numberOfBytes_resp" placeholder="1"></td>
        <td><input type="text" id="bitSize_resp" name="bitSize_resp" placeholder="1"></td>
        <td>
            <select id="dataType_resp" name="dataType_resp">
                <option value="ASCII" selected>ASCII</option>
                <option value="Binary" >Binary</option>
                <option value="SignedInt">Signed Int</option>
                <option value="Float">Float</option>
                <option value="UnsignedInt">Unsigned Int</option>
            </select>
        </td>
        <td><input type="text" id="valueDefinition_resp" name="valueDefinition_resp" placeholder="1"></td>
        <td><input type="text" id="displayValueMin_resp" name="displayValueMin_resp" placeholder="1"></td>
        <td><input type="text" id="displayValueMax_resp" name="displayValueMax_resp" placeholder="1"></td>
        <td><input type="text" id="valueInHexMin_resp" name="valueInHexMin_resp" placeholder="1"></td>
        <td><input type="text" id="valueInHexMax_resp" name="valueInHexMax_resp" placeholder="1"></td>
        <td>
            <select id="uiType_resp" name="uiType_resp">
                <option value="ToggleSwitch" selected>Toggle Switch</option>
                <option value="Slider">Slider</option>
                <option value="BrightnessSlider">Brightness Slider</option>
                <option value="CCTSlider">CCT Slider</option>
                <option value="SaturationSlider">Saturation Slider</option>
                <option value="HueSlider">Hue Slider</option>
                <option value="HueCircle">Hue Circle</option>
                <option value="PushButton">Push Button (Big)</option>
            </select>
        </td>
        <td><input type="text" id="uiId_resp" name="uiId_resp" placeholder="1"></td>
        <td><input type="text" id="uiPageNo_resp" name="uiPageNo_resp" placeholder="1"></td>
        <td><input type="text" id="uiDescriptionInOut_resp" name="uiDescriptionInOut_resp" placeholder="1"></td>
        <td><input type="text" id="sliderStepValue_resp" name="sliderStepValue_resp" placeholder="1"></td>
        <td><input type="text" id="developerNotes_resp" name="developerNotes_resp" placeholder="1"></td>
    </tr>
    <tr>
        <td colspan="18"><button onclick="respaddRow()">+</button></td>
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
        <input type="text" class="form-control" id="error_framesizeinbytes" name="error_framesizeinbytes" oninput="errorupdateTable()">
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
            <th>UI Description In/Out</th>
            <th>Slider Step Value</th>
            <th>Developer Notes (If Any)</th>
        </tr>
    </thead>
     <tbody>
        <tbody>
    <!-- Initial row with input fields and select dropdowns -->
    <tr id="row_1">
        <!-- Individual fields with IDs -->
        <td id="sno_1">1</td>
        <td><input type="text" id="startBytePosition_error" name="startBytePosition_error" placeholder="1"></td>
        <td><input type="text" id="endBytePosition_error" name="endBytePosition_error" placeholder="1"></td>
        

         <td>
            <select id="fieldDescription_error" name="fieldDescription_error">
                <option value="CommandId" selected>CommandId</option>
                <option value="Function Code" >Function Code</option>
                <option value="Checksum">Checksum</option>
                <option value="Terminator">Terminator</option>
                <option value="ActionId">ActionId</option>
                <option value="Data">Data</option>
                <option value="Error Code">Error Code</option>
                <option value="Operation Code">Operation Code</option>
            </select>
        </td>
        <td><input type="text" id="numberOfBytes_error" name="numberOfBytes_error" placeholder="1"></td>
        <td><input type="text" id="bitSize_error" name="bitSize_error" placeholder="1"></td>
        <td>
            <select id="dataType_error" name="dataType_error">
                <option value="ASCII" selected>ASCII</option>
                <option value="Binary" >Binary</option>
                <option value="SignedInt">Signed Int</option>
                <option value="Float">Float</option>
                <option value="UnsignedInt">Unsigned Int</option>
            </select>
        </td>
        <td><input type="text" id="valueDefinition_error" name="valueDefinition_error" placeholder="1"></td>
        <td><input type="text" id="displayValueMin_error" name="displayValueMin_error" placeholder="1"></td>
        <td><input type="text" id="displayValueMax_error" name="displayValueMax_error" placeholder="1"></td>
        <td><input type="text" id="valueInHexMin_error" name="valueInHexMin_error" placeholder="1"></td>
        <td><input type="text" id="valueInHexMax_error" name="valueInHexMax_error" placeholder="1"></td>
        <td>
            <select id="uiType_error" name="uiType_error">
                <option value="ToggleSwitch" selected>Toggle Switch</option>
                <option value="Slider">Slider</option>
                <option value="BrightnessSlider">Brightness Slider</option>
                <option value="CCTSlider">CCT Slider</option>
                <option value="SaturationSlider">Saturation Slider</option>
                <option value="HueSlider">Hue Slider</option>
                <option value="HueCircle">Hue Circle</option>
                <option value="PushButton">Push Button (Big)</option>
            </select>
        </td>
        <td><input type="text" id="uiId_error" name="uiId_error" placeholder="1"></td>
        <td><input type="text" id="uiPageNo_error" name="uiPageNo_error" placeholder="1"></td>
        <td><input type="text" id="uiDescriptionInOut_error" name="uiDescriptionInOut_error" placeholder="1"></td>
        <td><input type="text" id="sliderStepValue_error" name="sliderStepValue_error" placeholder="1"></td>
        <td><input type="text" id="developerNotes_error" name="developerNotes_error" placeholder="1"></td>
    </tr>
    <tr>
        <td colspan="18"><button onclick="erroraddRow()">+</button></td>
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



/*function respaddRow() {

    var table = document.getElementById("responseTable");    
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
    buttonCell.innerHTML = '<td colspan="18"><button onclick="respremoveRow()">-</button></td>';
}*/



function respaddRow() {
    var table = document.getElementById("responseTable");    
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
    buttonCell.innerHTML = '<td colspan="18"><button onclick="respremoveRow()">-</button></td>';

    
}




/*function erroraddRow() {

    var table = document.getElementById("errorTable");    
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
    buttonCell.innerHTML = '<td colspan="18"><button onclick="errorremoveRow()">-</button></td>';
}*/



function erroraddRow() {
    var table = document.getElementById("errorTable");    
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





function respupdateTable() {
        var frameSize = parseInt(document.getElementById('resp_framesizeinbytes').value);
        var table = document.getElementById('responseTable');
        var rowCount = table.rows.length;

        // Remove existing rows (except headers and buttons)
        for (var i = rowCount - 3; i > 0; i--) {
            table.deleteRow(i);
        }

        // Add new rows based on frameSize
        for (var i = 0; i < frameSize; i++) {
            respaddRow();
        }
    }



function errorupdateTable() {
        var frameSize = parseInt(document.getElementById('error_framesizeinbytes').value);
        var table = document.getElementById('errorTable');
        var rowCount = table.rows.length;

        // Remove existing rows (except headers and buttons)
        for (var i = rowCount - 3; i > 0; i--) {
            table.deleteRow(i);
        }

        // Add new rows based on frameSize
        for (var i = 0; i < frameSize; i++) {
            erroraddRow();
        }
    }





/*
function saveRowData(row) {
   var rowData = {};
    var inputs = row.querySelectorAll('input[type="text"]');
    for (var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        var fieldName = input.getAttribute('placeholder');
        var value = input.value;
        rowData[fieldName] = value;
    }
    
    // Log rowData to the console for testing
    //console.log('Row Data:', rowData);



for (var key in rowData) {
        if (rowData.hasOwnProperty(key)) {
            console.log(key + ': ' + rowData[key]);
        }
    }



    
    // Now, you have the data in rowData object
    // Send it to your backend to save into the database
   // sendDataToBackend(rowData);
}
*/







/*

function saveRowData() {
   
    var rowData = {};
    rowData.startBytePosition = document.getElementById('startBytePosition_1').value;
    rowData.endBytePosition = document.getElementById('endBytePosition_1').value;
    rowData.fieldDescription = document.getElementById('fieldDescription_1').value;
    rowData.numberOfBytes = document.getElementById('numberOfBytes_1').value;
    rowData.bitSize = document.getElementById('bitSize_1').value;
    rowData.dataType = document.getElementById('dataType_1').value;
    rowData.valueDefinition = document.getElementById('valueDefinition_1').value;
    rowData.displayValueMin = document.getElementById('displayValueMin_1').value;
    rowData.displayValueMax = document.getElementById('displayValueMax_1').value;
    rowData.valueInHexMin = document.getElementById('valueInHexMin_1').value;
    rowData.valueInHexMax = document.getElementById('valueInHexMax_1').value;
    rowData.uiType = document.getElementById('uiType_1').value;
    rowData.uiId = document.getElementById('uiId_1').value;
    rowData.uiPageNo = document.getElementById('uiPageNo_1').value;
    rowData.uiDescriptionInOut = document.getElementById('uiDescriptionInOut_1').value;
    rowData.sliderStepValue = document.getElementById('sliderStepValue_1').value;
    rowData.developerNotes = document.getElementById('developerNotes_1').value;


var rowData = {
        startBytePosition: 1,
        endBytePosition: 10,
        fieldDescription: "Test hi Description",
        numberOfBytes: 5,
        bitSize: 8,
        dataType: "ASCII",
        valueDefinition: "Test Value Definition",
        displayValueMin: 0,
        displayValueMax: 100,
        valueInHexMin: "0x00",
        valueInHexMax: "0xFF",
        uiType: "Slider",
        uiId: "ui1",
        uiPageNo: 1,
        uiDescriptionInOut: "Test UI Description",
        sliderStepValue: 1,
        developerNotes: "Test Developer Notes"
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


*/




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



</script>


<?php echo form_close(); ?>