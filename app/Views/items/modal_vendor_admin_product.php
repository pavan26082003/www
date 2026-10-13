<?php echo form_open(get_uri("items/savevendoradminproduct"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="177" />

           

   <div class="form-group">
    <div class="row">
        <label for="brand_id" class=" col-md-3">Select Model Name</label>
        <div class=" col-md-9">
            <select class="form-control" id="email2" name="SelectModel">
                <option value="">Select</option>
                <?php
                foreach ($ids as $value) {
                ?>
                    <option value="<?php echo $value->name; ?>" data-modelname="<?php echo $value->modelname; ?>" data-id="<?php echo $value->id; ?>" data-userid="<?php echo $value->user_id; ?>"><?php echo $value->modelname; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>
 
  <div class="form-group">
    <div class="row">
        <label for="selected_model" class="col-md-3">Selected Model Name</label>
        <div class="col-md-9">
            <input type="text" id="selected_model" name="selected_model" class="form-control" readonly>
        </div>
    </div>
</div>

 <div class="form-group">
    <div class="row">
        <label for="selected_id" class="col-md-3">Selected Model ID</label>
        <div class="col-md-9">
            <input type="text" id="selected_id" name="selected_id" class="form-control" readonly>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="user_id" class="col-md-3">User ID</label>
        <div class="col-md-9">
            <input type="text" id="user_id" name = "user_id"class="form-control" readonly>
        </div>
    </div>
</div>
         
           <div class="form-group">
    <div class="row">
        <label for="category_id" class="col-md-3">Reference Model Name</label>
        <div class="col-md-9">
            <select class="form-control" id="email" name="ReferenceModel">
                <option value="">Select</option>
                <?php
                foreach ($ids as $value) {
                ?>
                    <!-- Store the model's ID in a data-id attribute -->
                    <option value="<?php echo $value->name; ?>" data-modelname="<?php echo $value->modelname; ?>" data-id="<?php echo $value->id; ?>"><?php echo $value->modelname; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="reference_name" class="col-md-3">Reference Model Name</label>
        <div class="col-md-9">
            <input type="text" id="reference_name" name="reference_name" class="form-control" readonly>
        </div>
    </div>
</div>
<!-- Hidden or Display Element for the selected ID -->
<div class="form-group">
    <div class="row">
        <label for="reference_id" class="col-md-3">Reference Model ID</label>
        <div class="col-md-9">
            <input type="text" id="reference_id" name="reference_id" class="form-control" readonly>
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
<script>
    document.getElementById('email2').addEventListener('change', function() { 
        var selectedOption = this.options[this.selectedIndex]; 
        var userId = selectedOption.getAttribute('data-userid'); 
        document.getElementById('user_id').value = userId;
    });
    
     document.getElementById('email2').addEventListener('change', function() { 
        var selectedOption = this.options[this.selectedIndex]; 
        var selectModel = selectedOption.getAttribute('data-modelname'); 
        document.getElementById('selected_model').value = selectModel;
    });
    
     document.getElementById('email2').addEventListener('change', function() { 
        var selectedOption = this.options[this.selectedIndex]; 
        var selectId = selectedOption.getAttribute('data-id'); 
        document.getElementById('selected_id').value = selectId;
    });
    
     document.getElementById('email').addEventListener('change', function() { 
        var selectedOption = this.options[this.selectedIndex]; 
        var ref_model = selectedOption.getAttribute('data-modelname'); 
        document.getElementById('reference_name').value = ref_model;
    });
    
    document.getElementById('email').addEventListener('change', function() { 
        var selectedOption = this.options[this.selectedIndex]; 
        var modelId = selectedOption.getAttribute('data-id'); 
        document.getElementById('reference_id').value = modelId;
    });
    
</script>
