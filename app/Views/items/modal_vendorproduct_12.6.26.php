<?php echo form_open(get_uri("items/savevendorproduct"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
      <input type="hidden" name="id" value="<?php echo $model_info->id ?? ""; ?>" />

            <?php if ("177") { ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 text-off"><!--?php echo app_lang('item_edit_instruction'); ?--></div>
                    </div>
                </div>
            <?php } ?>

            <!-- Brand & Product Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="brand_id" class="control-label">
                            Brand <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2" id="email2" name="brand" required style="width: 100%;">
                            <option value="">— Select Brand —</option>
                            <?php foreach ($brand_dropdown as $value) { ?>
                                <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">Please select a brand.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id" class="control-label">
                            Product <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2" id="email" name="category" required style="width: 100%;">
                            <option value="">— Select Product —</option>
                            <?php foreach ($categories_dropdown as $value) { ?>
                                <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">Please select a product.</div>
                    </div>
                </div>
            </div>

            <!-- Model Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-cube me-2"></i>Model Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Model Name -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="model_name" class="control-label">
                                    Model Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-tag"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control border-start-0 ps-0" 
                                        id="model_name" 
                                        name="modelname" 
                                        placeholder="e.g., EF-002 FMN-02"
                                        pattern="^[a-zA-Z0-9_ -]+$" 
                                        title="Only alphanumeric characters, hyphens (-), underscores (_) and spaces are allowed."
                                        required>
                                </div>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fa fa-info-circle"></i> Alphanumeric with - _ space
                                </small>
                                <div class="invalid-feedback">Model name is required.</div>
                            </div>
                        </div>
                        
                        <!-- Version -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="version" class="control-label">
                                    Version <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-code-fork"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control border-start-0 ps-0" 
                                        id="version" 
                                        name="version" 
                                        placeholder="e.g., v1.0, 2.1, A.5"
                                        pattern="^[a-zA-Z0-9_.-]+$"
                                        title="Only alphanumeric characters, dots (.), hyphens (-) and underscores (_) are allowed."
                                        required>
                                </div>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fa fa-info-circle"></i> e.g., v1.0, 2.1, A.5
                                </small>
                                <div class="invalid-feedback">Version is required.</div>
                            </div>
                        </div>
                        
                        <!-- Batch/Lot No. -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="batch" class="control-label">
                                    Batch/Lot No. <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-cubes"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control border-start-0 ps-0" 
                                        id="batch" 
                                        name="batch" 
                                        placeholder="e.g., BATCH001, LOT-2024"
                                        pattern="^[a-zA-Z0-9_-]+$"
                                        title="Only alphanumeric characters, hyphens (-) and underscores (_) are allowed."
                                        required>
                                </div>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fa fa-info-circle"></i> Alphanumeric with - _
                                </small>
                                <div class="invalid-feedback">Batch/Lot number is required.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generated Model No. & Description Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-barcode me-2"></i>Product Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Model No. (Auto-generated from Model Name) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="model_no" class="control-label">
                                    Model No. <span class="text-muted">(Same as Model Name)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control border-start-0 ps-0 bg-light" 
                                        id="model_no" 
                                        name="modelno" 
                                        readonly 
                                        placeholder="Same as Model Name"
                                        style="background-color: #f9f9f9;">
                                </div>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fa fa-magic"></i> Automatically matches Model Name
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <!-- Product Description (Optional) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description" class="control-label">
                                    Product Description <span class="text-muted">(Optional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fa fa-file-text"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control border-start-0 ps-0" 
                                        id="description" 
                                        name="description" 
                                        placeholder="Enter product description (optional)">
                                </div>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fa fa-info-circle"></i> Additional product details
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mb-3 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-eye me-2"></i>Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Brand:</strong>
                                        <span id="preview-brand" class="ms-2">—</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Product:</strong>
                                        <span id="preview-product" class="ms-2">—</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Model Name:</strong>
                                        <span id="preview-model" class="ms-2">—</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Model Number:</strong>
                                        <span id="preview-modelno" class="ms-2">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-footer">
        
        <button type="submit" class="btn btn-primary" id="submit-btn">
            <i class="fa fa-save me-1"></i><?php echo app_lang('save'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<style>
/* Modern UI Styling */
:root {
    --primary-color: #4361ee;
    --success-color: #06d6a0;
    --danger-color: #ef476f;
    --warning-color: #ffd166;
    --dark-color: #2b2d42;
    --light-color: #f8f9fa;
}

/* Card Styling */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(to right, #f8f9fa, #e9ecef);
    border-bottom: 1px solid rgba(0,0,0,.125);
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1rem 1.25rem;
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.card-title {
    font-weight: 600;
    font-size: 1rem;
}

/* Form Controls */
.form-control, .select2-container--default .select2-selection--single {
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    height: 45px;
    transition: all 0.2s ease;
}

.form-control:focus, .select2-container--default .select2-selection--single:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.input-group-text {
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem 0 0 0.375rem;
    background: white;
    color: #6c757d;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 0.375rem 0.375rem 0;
}

.input-group .form-control:focus {
    border-left: none;
}

/* Labels */
.control-label {
    font-weight: 600;
    font-size: 0.875rem;
    color: #4a5568;
    margin-bottom: 0.5rem;
    display: block;
}

.text-danger {
    color: var(--danger-color) !important;
    font-weight: 600;
}

/* Helper Text */
.form-text {
    font-size: 0.75rem;
    color: #718096;
    margin-top: 0.25rem;
}

/* Invalid Feedback */
.invalid-feedback {
    display: none;
    font-size: 0.75rem;
    color: var(--danger-color);
    margin-top: 0.25rem;
}

.form-control.is-invalid {
    border-color: var(--danger-color);
    background-image: none;
}

.form-control.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 71, 111, 0.1);
}

/* Select2 Customization */
.select2-container--default .select2-selection--single {
    border: 1px solid #e2e8f0 !important;
    height: 45px !important;
    padding: 0.5rem 0.75rem !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 43px !important;
    right: 10px !important;
}

/* Preview Section */
.bg-light.rounded {
    background: linear-gradient(145deg, #ffffff, #f1f5f9) !important;
    border: 1px solid rgba(0,0,0,.05);
}

#preview-brand, #preview-product, #preview-model, #preview-modelno {
    font-weight: 600;
    color: var(--primary-color);
}

/* Buttons */
.btn {
    padding: 0.5rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
}

.btn-light {
    background: white;
    border: 1px solid #e2e8f0;
    color: #4a5568;
}

.btn-light:hover {
    background: #f8f9fa;
    border-color: #cbd5e0;
}

/* Icons */
.fa, .fas, .far {
    margin-right: 0.25rem;
}

/* Modal Footer */
.modal-footer {
    border-top: 1px solid #e2e8f0;
    padding: 1.25rem;
    background: #f8fafc;
    border-radius: 0 0 0.5rem 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .card-body .row > div {
        margin-bottom: 1rem;
    }
    
    .modal-footer .btn {
        width: 100%;
        margin: 0.25rem 0;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-body {
    animation: fadeIn 0.3s ease;
}

/* Select2 Dropdown */
.select2-dropdown {
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.select2-results__option--highlighted {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}
</style>

<script type="text/javascript">
$(document).ready(function () {
    // PREVENT MODAL CLOSURE ON KEYBOARD EVENTS
    $(document).on('keydown', function(e) {
        // Prevent Escape key from closing modal when inside form
        if (e.key === 'Escape') {
            // Check if focus is inside the form or modal
            if ($('#item-form').is(':visible') || $('.modal').is(':visible')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
        
        // Prevent arrow keys from triggering page navigation
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            // Allow navigation in select dropdowns
            if ($('.select2-dropdown--open').length === 0 && 
                !$(e.target).is('input, select, textarea')) {
                e.preventDefault();
            }
        }
    });

    // Fix for Bootstrap modal if you're using one
    if (typeof $.fn.modal !== 'undefined') {
        $(document).on('show.bs.modal', '.modal', function() {
            $(this).data('bs.modal')._config.keyboard = false;
        });
    }

    // Prevent Select2 from closing modal
    $(document).on('select2:open', function(e) {
        var select2Dropdown = $('.select2-dropdown');
        if (select2Dropdown.length) {
            select2Dropdown.off('keydown').on('keydown', function(e) {
                if (e.key === 'Escape') {
                    e.stopPropagation();
                }
            });
        }
    });

    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        minimumResultsForSearch: 5,
        dropdownParent: $('body') // Ensure dropdown is attached to body
    });

    // Function to copy Model Name to Model No.
    function copyModelNameToModelNo() {
        var modelName = $('#model_name').val().trim();
        $('#model_no').val(modelName);
        return modelName;
    }

    // Update Preview
    function updatePreview() {
        var brand = $('#email2 option:selected').text();
        var product = $('#email option:selected').text();
        var model = $('#model_name').val();
        var modelNumber = $('#model_no').val(); // Changed from #model_number to #model_no
        
        $('#preview-brand').text(brand !== '— Select Brand —' ? brand : '—');
        $('#preview-product').text(product !== '— Select Product —' ? product : '—');
        $('#preview-model').text(model || '—');
        $('#preview-modelno').text(modelNumber || '—');
    }

    // Form validation
    function validateForm() {
        var isValid = true;
        var errorMessages = [];
        
        // Brand validation
        if (!$('#email2').val()) {
            $('#email2').addClass('is-invalid');
            errorMessages.push('Brand');
            isValid = false;
        } else {
            $('#email2').removeClass('is-invalid');
        }
        
        // Product validation
        if (!$('#email').val()) {
            $('#email').addClass('is-invalid');
            errorMessages.push('Product');
            isValid = false;
        } else {
            $('#email').removeClass('is-invalid');
        }
        
        // Model Name validation
        if (!$('#model_name').val().trim()) {
            $('#model_name').addClass('is-invalid');
            errorMessages.push('Model Name');
            isValid = false;
        } else {
            $('#model_name').removeClass('is-invalid');
        }
        
        // Version validation
        if (!$('#version').val().trim()) {
            $('#version').addClass('is-invalid');
            errorMessages.push('Version');
            isValid = false;
        } else {
            $('#version').removeClass('is-invalid');
        }
        
        // Batch validation
        if (!$('#batch').val().trim()) {
            $('#batch').addClass('is-invalid');
            errorMessages.push('Batch/Lot No.');
            isValid = false;
        } else {
            $('#batch').removeClass('is-invalid');
        }
        
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                html: '<strong>Please fill in all required fields:</strong><br>' + errorMessages.join('<br>'),
                confirmButtonColor: '#4361ee'
            });
        }
        
        return isValid;
    }

    // Input event handlers
    $('#model_name').on('input', function() {
        copyModelNameToModelNo();
        updatePreview();
        
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
        }
    });
    
    $('#version, #batch').on('input', function() {
        updatePreview();
        
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
        }
    });

    $('#email2, #email').on('change', function() {
        updatePreview();
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });

    $('#model_name, #version, #batch').on('blur', function() {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid');
        }
    });

    // Reset button
    $('#reset-btn').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Reset Form?',
            text: 'All entered data will be cleared.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef476f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reset it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#item-form')[0].reset();
                $('.select2').val('').trigger('change');
                $('#model_no').val('');
                updatePreview();
                $('.is-invalid').removeClass('is-invalid');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Reset Successful',
                    text: 'Form has been reset.',
                    confirmButtonColor: '#4361ee',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    // Form submission
    $("#item-form").on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        $('#submit-btn').html('<i class="fa fa-spinner fa-spin me-1"></i>Saving...').prop('disabled', true);
        
        return true;
    });

    // Initialize preview and model no
    updatePreview();
    copyModelNameToModelNo();

    // Check if Dropzone is available
    if (typeof attachDropzoneWithForm === 'function') {
        var uploadUrl = "<?php echo get_uri("items/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("items/validate_items_file"); ?>";
        var dropzone = attachDropzoneWithForm("#items-dropzone", uploadUrl, validationUri);
    }

    // Form submission with appForm (check if available)
    if ($.fn.appForm) {
        $("#item-form").appForm({
            onSuccess: function (result) {
                $('#submit-btn').html('<i class="fa fa-save me-1"></i><?php echo app_lang('save'); ?>').prop('disabled', false);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Product saved successfully.',
                    confirmButtonColor: '#4361ee',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else if ($("#item-table").appTable) {
                    $("#item-table").appTable({newData: result.data, dataId: result.id});
                }
            },
            onError: function() {
                $('#submit-btn').html('<i class="fa fa-save me-1"></i><?php echo app_lang('save'); ?>').prop('disabled', false);
                validateForm();
            }
        });
    }
});


<script>
// Emergency fix - prevent all modal closures
if (typeof bootstrap !== 'undefined') {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var modals = document.querySelectorAll('.modal.show');
            if (modals.length > 0) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
    });
}
</script>
</script>

<!-- SweetAlert2 for beautiful alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jQuery (if not already loaded) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap CSS & JS (if not already loaded) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome 5 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
