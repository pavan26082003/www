<?php echo form_open(get_uri("items/savevendorproduct"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="177" />

            <?php if ("177") { ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 text-off"> <!--?php echo app_lang('item_edit_instruction'); ?--></div>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <div class="row">
                    <label for="brand_id" class=" col-md-3"><!--?php echo app_lang('category'); ?-->Brand</label>
                    <div class=" col-md-9">
                        <select class="form-control" id="email2" name="brand">
                            <option value="">Select</option>
                            <?php
                            foreach ($brand_dropdown as $value) {
                                ?>
                                <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
         
            <div class="form-group">
                <div class="row">
                    <label for="category_id" class=" col-md-3"><!--?php echo app_lang('category'); ?-->Product</label>
                    <div class=" col-md-9">
                        <select class="form-control" id="email" name="category">
                            <option value="">Select</option>
                            <?php
                            foreach ($categories_dropdown as $value) {
                                ?>
                                <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="model_name" class="col-md-3">Model Name</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="model_name" name="modelname" pattern="^[a-zA-Z0-9_-]+$" title="Only alphanumeric characters, hyphens (-) and underscores (_) are allowed. No spaces.">
                        <small class="form-text text-muted">Format: Alphanumeric with hyphens/underscores (e.g., EF-002FMN-02)</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="model_no" class="col-md-3">Model No.</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="model_no" name="modelno">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="description" class="col-md-3">Product Description</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        // Client-side validation for model_name
        $('#model_name').on('input', function() {
            var input = $(this);
            var regex = /^[a-zA-Z0-9_-]*$/;
            var isValid = regex.test(input.val());
            
            if (!isValid) {
                input[0].setCustomValidity("Only alphanumeric characters, hyphens (-) and underscores (_) are allowed. No spaces.");
            } else {
                input[0].setCustomValidity("");
            }
        });

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