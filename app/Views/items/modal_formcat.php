<?php echo form_open_multipart(get_uri("items/savecat"), array("id" => "item-form", "class" => "general-form add-product-form", "role" => "form")); ?>
<style>
.add-product-modal {
    --apm-bg: #FFFFFF;
    --apm-surface: #F9F8F6;
    --apm-border: #D9CFC7;
    --apm-accent: #C9B59C;
    --apm-accent-dark: #b09d84;
    --apm-text: #3d3834;
    --apm-muted: #6b6560;
    --apm-success: #2e7d32;
    --apm-success-bg: #edf7ee;
    font-family: 'Roboto', sans-serif;
    color: var(--apm-text);
}
.add-product-modal .modal-body { padding: 0; background: var(--apm-bg); }
.add-product-hero {
    padding: 20px 24px 16px;
    background: linear-gradient(135deg, #EFE9E3 0%, #D9CFC7 100%);
    border-bottom: 1px solid var(--apm-border);
    display: flex; align-items: center; gap: 14px;
}
.add-product-hero-icon {
    width: 46px; height: 46px; border-radius: 14px;
    background: #fff; border: 1px solid var(--apm-border);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(61,56,52,0.08); flex-shrink: 0;
}
.add-product-hero-icon i { font-size: 20px; color: var(--apm-accent-dark); }
.add-product-hero-text h4 { margin: 0 0 4px; font-size: 1.05rem; font-weight: 800; }
.add-product-hero-text p { margin: 0; font-size: 0.8rem; color: var(--apm-muted); line-height: 1.45; }
.add-product-fields { padding: 20px 24px 6px; }
.add-product-field { margin-bottom: 16px; }
.add-product-field label {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.82rem; font-weight: 700; margin-bottom: 8px;
}
.add-product-field label i { color: var(--apm-accent-dark); width: 16px; text-align: center; }
.add-product-field .form-control {
    height: 46px; border-radius: 12px; border: 1px solid var(--apm-border);
    background: var(--apm-surface); padding: 10px 14px; font-size: 0.92rem;
}
.add-product-field .form-control:focus {
    border-color: var(--apm-accent);
    box-shadow: 0 0 0 3px rgba(201,181,156,0.25); background: #fff;
}

/* Upload zone */
.add-product-upload {
    border: 2px dashed var(--apm-border);
    border-radius: 16px;
    padding: 20px 16px;
    text-align: center;
    background: var(--apm-surface);
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    position: relative;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.add-product-upload:hover { border-color: var(--apm-accent); background: #fff; }
.add-product-upload.is-dragover { border-color: var(--apm-accent); background: #fff; box-shadow: 0 0 0 4px rgba(201,181,156,0.2); }
.add-product-upload.has-file {
    border-color: var(--apm-success);
    border-style: solid;
    background: var(--apm-success-bg);
    cursor: default;
    min-height: auto;
    padding: 16px;
}
.add-product-upload input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; z-index: 2;
}
.add-product-upload.has-file input[type="file"] { height: 36px; bottom: 12px; top: auto; width: 140px; left: 50%; transform: translateX(-50%); }

.upload-empty-state { width: 100%; pointer-events: none; }
.upload-empty-state .upload-icon {
    width: 52px; height: 52px; margin: 0 auto 10px; border-radius: 50%;
    background: #fff; border: 1px solid var(--apm-border);
    display: flex; align-items: center; justify-content: center;
}
.upload-empty-state .upload-icon i { font-size: 22px; color: var(--apm-accent-dark); }
.upload-empty-state .upload-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 4px; }
.upload-empty-state .upload-hint { font-size: 0.78rem; color: var(--apm-muted); }

.upload-preview-state {
    display: none;
    width: 100%;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    pointer-events: none;
}
.add-product-upload.has-file .upload-empty-state { display: none; }
.add-product-upload.has-file .upload-preview-state { display: flex; }

.upload-preview-state img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 14px;
    border: 3px solid #fff;
    box-shadow: 0 6px 20px rgba(61,56,52,0.15);
    background: #fff;
}
.upload-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border: 1px solid #b7dfc0;
    color: var(--apm-success);
    font-size: 0.82rem;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 999px;
}
.upload-status-badge i { font-size: 14px; }
.upload-file-name {
    font-size: 0.78rem;
    color: var(--apm-muted);
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding: 0 8px;
}
.upload-change-btn {
    position: relative;
    z-index: 3;
    pointer-events: auto;
    margin-top: 4px;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--apm-accent-dark);
    background: #fff;
    border: 1px solid var(--apm-border);
    border-radius: 999px;
    padding: 5px 14px;
    cursor: pointer;
}
.upload-change-btn:hover { background: var(--apm-surface); }

.add-product-modal .modal-footer {
    padding: 14px 24px 20px;
    border-top: 1px solid var(--apm-border);
    background: var(--apm-bg);
    display: flex; justify-content: flex-end; gap: 10px;
}
.add-product-modal .btn-apm-cancel {
    border-radius: 999px; padding: 9px 18px; font-weight: 600;
    border: 1px solid var(--apm-border); background: #fff; color: var(--apm-text);
}
.add-product-modal .btn-apm-save {
    border-radius: 999px; padding: 9px 22px; font-weight: 700; border: none;
    background: linear-gradient(135deg, var(--apm-accent) 0%, var(--apm-accent-dark) 100%);
    color: #fff; box-shadow: 0 6px 18px rgba(201,181,156,0.45);
    min-width: 148px;
}
.add-product-modal .btn-apm-save:disabled { opacity: 0.85; cursor: wait; }

/* Lighter modal overlay during save — feels faster */
#ajaxModalContent .modal-mask {
    opacity: 0.25 !important;
    background: #fff !important;
}
@media (max-width: 576px) {
    .add-product-hero, .add-product-fields { padding-left: 16px; padding-right: 16px; }
    .add-product-modal .modal-footer { padding-left: 16px; padding-right: 16px; }
}
</style>

<div class="add-product-modal">
    <div class="modal-body clearfix">
        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger m-3" role="alert"><?php echo session('error'); ?></div>
        <?php endif; ?>

        <div class="add-product-hero">
            <div class="add-product-hero-icon"><i class="fas fa-box-open"></i></div>
            <div class="add-product-hero-text">
                <h4>Add New Product</h4>
                <p>Fill in the details below. A preview appears as soon as you select an image.</p>
            </div>
        </div>

        <div class="add-product-fields">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <div class="add-product-field">
                <label for="name"><i class="fas fa-tag"></i> Product Name</label>
                <?php
                echo form_input(array(
                    "id" => "name",
                    "name" => "name",
                    "value" => $model_info->name,
                    "class" => "form-control",
                    "placeholder" => "e.g. Smart Rice Cooker",
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>

            <div class="add-product-field">
                <label><i class="fas fa-image"></i> Product Image</label>
                <div class="add-product-upload" id="productUploadZone">
                    <input type="file" id="prodimg" name="prodimg" accept="image/jpeg,image/png,image/gif,image/webp,image/*" required>

                    <div class="upload-empty-state">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-title">Click to choose an image</div>
                        <div class="upload-hint">or drag &amp; drop here · JPG, PNG, GIF, WEBP</div>
                    </div>

                    <div class="upload-preview-state">
                        <img id="prodimg-preview" src="" alt="Product preview">
                        <div class="upload-status-badge"><i class="fas fa-check-circle"></i> Image ready</div>
                        <div class="upload-file-name" id="productFileName"></div>
                        <button type="button" class="upload-change-btn" id="changeImageBtn">Change image</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-apm-cancel" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancel
        </button>
        <button type="submit" class="btn btn-apm-save" id="saveProductBtn">
            <i class="fas fa-check-circle"></i> Save Product
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
(function ($) {
    var saveBtnHtml = '<i class="fas fa-check-circle"></i> Save Product';

    function clearModalLoading() {
        $('#ajaxModal .modal-body').removeClass('hide');
        $('#ajaxModal .modal-mask').remove();
        $('#ajaxModal').find('[type="submit"]').prop('disabled', false);
    }

    function setSaveLoading(isLoading) {
        var $btn = $('#saveProductBtn');
        if (!$btn.length) return;
        if (isLoading) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        } else {
            $btn.prop('disabled', false).html(saveBtnHtml);
        }
    }

    function handleProductSaveSuccess(result) {
        if (!result || !result.success) return;

        var productName = $('#name').val() || 'Product';
        var message = result.message || ('Product "' + productName + '" saved successfully!');

        $(document).trigger('productSaved', [{ message: message, name: productName, id: result.id }]);

        if (typeof appAlert !== 'undefined') {
            appAlert.success(message, {duration: 5000});
        }

        if ($('#item-table').length) {
            if (result.data && result.id) {
                $('#item-table').appTable({newData: result.data, dataId: result.id});
            } else {
                $('#item-table').appTable({reload: true});
            }
        }
    }

    function updateImagePreview(file) {
        var $zone = $('#productUploadZone');
        var $preview = $('#prodimg-preview');
        var $fileName = $('#productFileName');

        if (!file || !file.type || file.type.indexOf('image/') !== 0) {
            $zone.removeClass('has-file');
            $preview.attr('src', '');
            $fileName.text('');
            return;
        }

        $fileName.text(file.name);

        var reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result);
            $zone.addClass('has-file');
        };
        reader.onerror = function () {
            if (typeof appAlert !== 'undefined') {
                appAlert.error('Could not preview this image. Try another file.', {container: '#ajaxModal .modal-body', animate: false});
            }
        };
        reader.readAsDataURL(file);
    }

    function initAddProductForm() {
        var $form = $('#item-form');
        var $input = $('#prodimg');
        var $zone = $('#productUploadZone');

        if (!$form.length || $form.data('apm-initialized')) return;
        $form.data('apm-initialized', true);
        clearModalLoading();

        $input.off('change.apm').on('change.apm', function () {
            updateImagePreview(this.files && this.files[0] ? this.files[0] : null);
        });

        $('#changeImageBtn').off('click.apm').on('click.apm', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $input.val('');
            $zone.removeClass('has-file');
            $input.trigger('click');
        });

        $zone.off('dragenter.apm dragover.apm').on('dragenter.apm dragover.apm', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $zone.addClass('is-dragover');
        });
        $zone.off('dragleave.apm drop.apm').on('dragleave.apm drop.apm', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $zone.removeClass('is-dragover');
        });
        $zone.off('drop.apm').on('drop.apm', function (e) {
            var files = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
            if (files && files[0]) {
                var file = files[0];
                try {
                    if (typeof DataTransfer !== 'undefined') {
                        var dt = new DataTransfer();
                        dt.items.add(file);
                        $input[0].files = dt.files;
                    }
                } catch (err) { /* preview still shown; click save may need re-select on old browsers */ }
                updateImagePreview(file);
            }
        });

        $form.off('submit.apm').on('submit.apm', function (e) {
            e.preventDefault();

            var name = $.trim($('#name').val());
            if (!name) {
                if (typeof appAlert !== 'undefined') {
                    appAlert.error('Product name is required.', {container: '#ajaxModal .add-product-fields', animate: false});
                }
                $('#name').focus();
                return false;
            }

            var fileInput = $input[0];
            if (!fileInput || !fileInput.files || !fileInput.files.length) {
                if (typeof appAlert !== 'undefined') {
                    appAlert.error('Please select a product image before saving.', {container: '#ajaxModal .add-product-fields', animate: false});
                }
                return false;
            }

            setSaveLoading(true);

            var fd = new FormData();
            fd.append('name', name);
            fd.append('id', $form.find('input[name="id"]').val() || '');
            fd.append('prodimg', fileInput.files[0]);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                timeout: 120000,
                success: function (result) {
                    if (result && result.success) {
                        handleProductSaveSuccess(result);
                        $('#ajaxModal').modal('hide');
                    } else {
                        var msg = (result && result.message) ? result.message : 'Could not save product.';
                        if (typeof appAlert !== 'undefined') {
                            appAlert.error(msg, {container: '#ajaxModal .add-product-fields', animate: false});
                        }
                    }
                },
                error: function (xhr, status) {
                    var msg = 'Could not save product. Please try again.';
                    if (status === 'timeout') {
                        msg = 'Save timed out. Check your connection and try again.';
                    } else if (xhr.responseText) {
                        try {
                            var parsed = JSON.parse(xhr.responseText);
                            if (parsed.message) msg = parsed.message;
                        } catch (err) {
                            if (xhr.responseText.length < 300) {
                                msg = xhr.responseText.replace(/<[^>]*>/g, '').trim() || msg;
                            }
                        }
                    }
                    if (typeof appAlert !== 'undefined') {
                        appAlert.error(msg, {container: '#ajaxModal .add-product-fields', animate: false});
                    }
                },
                complete: function () {
                    clearModalLoading();
                    setSaveLoading(false);
                }
            });

            return false;
        });

        if (window.feather) feather.replace();
    }

    $(document).ready(initAddProductForm);
    $(document).on('shown.bs.modal', '#ajaxModal', initAddProductForm);
})(jQuery);
</script>
