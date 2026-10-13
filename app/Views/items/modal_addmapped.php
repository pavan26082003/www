<?php echo form_open(get_uri("items/savemapped"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <input type="hidden" name="testing" value="1" />

         

            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo 'QR Code/Serial No'; ?></label>
                    <div class="col-md-9">
                        <input type="text" name="serialno" id="serialno" onkeyup="fun1(this.value)" class="form-control" placeholder="QR Code/Serial No">
                      
                    </div>
                </div>

                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo 'UID'; ?></label>
                    <div class="col-md-9">
                       <input type="text" name="uid" id="uid" class="form-control">
                    </div>
                </div>

                <!-- <div class="row">
                    <label for="title" class=" col-md-3"><?php echo 'Vendor Products'; ?></label>
                    <div class="col-md-9">
                       <select name="deviceapi" class="form-control" onchange="fun(this.value)">

                        <option>Select</option>
                         <?php
            foreach ($categories_dropdown as $value) {
               ?>

               <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>

               <?php
            }
            ?>

                       </select>
                    </div>
                </div>-->


                 <div class="row">
                    <label for="title" class=" col-md-3"><?php echo 'Model No'; ?></label>
                    <div class="col-md-9">
                       <input type="text" name="modelno" id="modelno" class="form-control">
                    </div>
                </div> 


               <!-- <div class="row">
                    <label for="title" class=" col-md-3"><?php echo 'Unique ID'; ?></label>
                    <div class="col-md-9">
                       <input type="text" name="uniqueid" class="form-control">
                    </div>
                </div>-->


            </div>
            

        </div>
    </div>

    <div class="modal-footer">
    <!--     <button class="btn btn-default upload-file-button float-start btn-sm round me-auto" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_image"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button> -->
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">


    function fun(a)
    {
        $.ajax({
           url: '/index.php/Items/ajax',
           type: 'POST',
           data: {title: a},
           error: function() {
              alert('Something is wrong');
           },
           success: function(data) {
                $("#modelno").val(data);
                  
           }
        });

    }

    //fun1
     function fun1(a)
    {
        $.ajax({
           url: '/index.php/Items/ajax1',
           type: 'POST',
           data: {title: a},
           error: function() {
              alert('Something is wrong');
           },
           success: function(data) {
                $("#uid").val(data);
                  
           }
        });

    }

    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("items/upload_file"); ?>";
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