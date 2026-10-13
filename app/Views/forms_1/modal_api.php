<?php echo form_open(get_uri("forms/saveapi"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <h4 style="text-align: center; color: green;">COMMAND API</h4>
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
        <label class=" col-sm-2" for="email">Product Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="email" placeholder="Product Name" name="productname">
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
        <label class=" col-sm-2" for="email">Api Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="email" placeholder="api name" name="apiname">
        </div>
      </div>
      <div class="form-group">
        <label class=" col-sm-2" for="pwd">CMD:</label>

        <label class=" col-sm-2" for="pwd">Voice Tag:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="pwd" placeholder="command" name="cmd_command">
        </div>

         <div class="col-sm-2">          
          <input type="text" class="form-control" id="pwd" placeholder="Voice Tag" name="voicetag">
        </div>

        <label class=" col-sm-2" for="pwd">Voice Tagalong:</label>
        <div class="col-sm-2">          
          <input type="text" class="form-control" id="pwd" placeholder="data" name="cmd_data">
        </div>

        <div class="col-sm-2">          
          <input type="text" class="form-control" id="pwd" placeholder="Voice TagAlong" name="voicetagalong">
        </div>

      </div>
      
   

</div>


<br>



     <div class="row">
    
       <div class="col-sm-2">
        <div class="radio">
            <label><input type="radio" name="optradio" onclick="linearbutton(this);"  id="linear" checked="checked">Linear</label>
          </div>
       </div>
       <div id="linearform" style="visibility: visible;">

       <div class="col-sm-2">
        <input type="number" class="form-control" id="email1" placeholder="min number" name="linear_min">
      </div>
      

      <div class="col-sm-2">
        <input type="number" class="form-control" id="email2" placeholder="max number" name="linear_max">
      </div>
      <div class="col-sm-6">
        
    </div>
</div>
     </div>

     <br>
     <div class="row">


        <div class="col-sm-2">
            <div class="radio">
                <label><input type="radio" name="optradio" onclick="binarybutton(this);" id="binary">binary</label>
              </div>
           </div>
           <div id="binaryform" style="visibility: hidden;">

           <div class="col-sm-2">
            <input type="number" class="form-control" id="email" placeholder="min number" name="binary_min">
          </div>
        </div>
    </div>





<br>

 <div class="info">
        <div class="row">
    
            <div class="col-sm-2">
             <div class="radio">
                 <label><input type="radio" name="optradio" onclick="infobutton();" id="info" checked="checked">INFO :</label>
               </div>
            </div>
     
            <div class="col-sm-10" id="infoform" style="visibility: visible;">
                
                    <div class="form-group">
                
                      <div class="col-sm-4">          
                        <input type="text" class="form-control" id="pwd" placeholder="return command" name="info_returncommand">
                      </div>
              
                      
                      <div class="col-sm-4">          
                        <input type="text" class="form-control" id="pwd" placeholder="return data" name="info_returndata">
                      </div>
                    </div>
                    
                 
                </div>
          </div>



          <div class="row">
    
            <div class="col-sm-2">
             
            </div>
     
            <div class="col-sm-10" id="infoform2" style="visibility: visible;">
                
                    
                    <div class="form-group">
                
                      <div class="col-sm-4">          
                        <input type="text" class="form-control" id="pwd" placeholder="Error command" name="info_errorcommand">
                      </div>
              
                      
                      <div class="col-sm-4">          
                        <input type="text" class="form-control" id="pwd" placeholder="Error data" name="info_errordata">
                      </div>
                    </div>
                    
                  
                </div>
          </div>
        </div>


<br>

<div class="cmd">
            <div class="row">
    
                <div class="col-sm-2">
                 <div class="radio">
                     <label><input type="radio" name="optradio" onclick="cmdbutton();" id="cmd">CMD Return:</label>
                   </div>
                </div>
         
            
                    <div class="col-sm-10" id="cmdform" style="visibility: hidden;">
                      
                            
                            <div class="form-group">
                        
                              <div class="col-sm-4">          
                                <input type="text" class="form-control" id="pwd" placeholder="command" name="infocmd_command">
                              </div>
                      
                              
                              <div class="col-sm-4">          
                                <input type="text" class="form-control" id="pwd" placeholder="data" name="infocmd_data">
                              </div>
                            </div>
                            
                         
                        </div>
              
               
         
              
              </div>
              </div>
            
           
         
     

        </div>
    </div>

    <!--div class="modal-footer">
    
     Upload File : <input type="file" name="item_files">

       < <button class="btn btn-default upload-file-button float-start btn-sm round me-auto" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_image"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button> >
        
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div-->

      <div class="modal-footer">
    
     
        
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>





</div>
<?php echo form_close(); ?>





  <script>

      function linearbutton(){
        if (document.getElementById('linear').checked) {
            document.getElementById('binaryform').style.visibility = 'hidden';
        document.getElementById('linearform').style.visibility = 'visible';
    }

      }

      function binarybutton(){
        if (document.getElementById('binary').checked) {
            document.getElementById('linearform').style.visibility = 'hidden';
        document.getElementById('binaryform').style.visibility = 'visible';
        }
      }

      function infobutton(){
        if (document.getElementById('info').checked) {
            document.getElementById('cmdform').style.visibility = 'hidden';
        document.getElementById('infoform').style.visibility = 'visible';
        document.getElementById('infoform2').style.visibility = 'visible';

        }
      }

      function cmdbutton(){
        if (document.getElementById('cmd').checked) {
            document.getElementById('infoform2').style.visibility = 'hidden';

            document.getElementById('infoform').style.visibility = 'hidden';
        document.getElementById('cmdform').style.visibility = 'visible';
        }
      }
  </script>

<script type="text/javascript">
    $(document).ready(function () {
        var uploadUrl = "<?
                 php echo get_uri("items/upload_file"); 
              ?>";
        var validationUri = "<?php echo get_uri("items/validate_items_file"); ?>";

        var dropzone = attachDropzoneWithForm("#items-dropzone", uploadUrl, validationUri);

        $("#item-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#item-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#item-form .select2").select2();
    });
</script>