<?php echo form_open_multipart(get_uri("items/savecat"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<form id="myForm" action="items.php" name="myForm" method="post" enctype="multipart/form-data">
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
    <?php if(session()->has('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo session('error'); ?>
            </div>
        <?php endif; ?>
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo 'Product Name';//app_lang('title'); ?></label>
                    <div class="col-md-9">
                    <?php
echo form_input(array(
    "id" => "name",
    "name" => "name",
    "value" => $model_info->name,
    "class" => "form-control validate-hidden",
    "placeholder" => 'Category Name',
    "autofocus" => true,
    "data-rule-required" => true,
    "data-msg-required" => app_lang("field_required"),
    "pattern" => "[A-Za-z0-9]+", 
    "title" => "Only alphabets and numbers are allowed"  
));
?>
                    </div>
                    <label class=" col-md-3" for="brandlogo">Product Image:</label>
        <!--small>Ex : Logo of brand</small-->
        <div class="col-md-9">
            <input type="file" class="form-control" id="prodimg" name="prodimg" accept="image/*" onchange="showImagePreview(this)">
            <img id="prodimg-preview" src="#" alt="Product Image Preview" style="display: none; max-width: 100px; max-height: 100px;">
        </div>
                </div>
            </div>
      

         

            <?php echo view("includes/dropzone_preview"); ?>

        </div>
    </div>

    <div class="modal-footer">
        
        <button type="submit" value="upload" name="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
</form>
<?php echo form_close(); ?>
<script>
 function showImagePreview(input) {
                            var preview = document.getElementById('prodimg-preview');
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
                        
</script>
<script type="text/javascript">
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
