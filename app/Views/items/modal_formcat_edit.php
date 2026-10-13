<?php
$product_id = isset($itemid)
    ? (int) $itemid
    : (isset($model_info->id) ? (int) $model_info->id : 0);

$product_name = isset($name)
    ? (string) $name
    : (isset($model_info->name) ? (string) $model_info->name : '');

$current_image = isset($prodimg) ? (string) $prodimg : '';
?>

<?php echo form_open(
    get_uri('items/updatecat'),
    array(
        'id' => 'product-edit-form',
        'class' => 'general-form',
        'role' => 'form',
        'enctype' => 'multipart/form-data'
    )
); ?>

<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $product_id; ?>">

    <div class="form-group">
        <div class="row">
            <label for="edit-product-name" class="col-md-3">
                Product Name
            </label>
            <div class="col-md-9">
                <input
                    type="text"
                    id="edit-product-name"
                    name="name"
                    class="form-control"
                    value="<?php echo esc($product_name); ?>"
                    placeholder="Product name"
                    autocomplete="off"
                    required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <label for="edit-product-image" class="col-md-3">
                Product Image
            </label>
            <div class="col-md-9">
                <?php if ($current_image !== ''): ?>
                    <div style="margin-bottom:12px;">
                        <img
                            id="edit-product-preview"
                            src="<?php echo esc($current_image); ?>"
                            alt="<?php echo esc($product_name); ?>"
                            style="width:120px;height:120px;object-fit:contain;"
                            onerror="this.style.display='none';">
                    </div>
                <?php else: ?>
                    <img
                        id="edit-product-preview"
                        src=""
                        alt="Product preview"
                        style="display:none;width:120px;height:120px;object-fit:contain;margin-bottom:12px;">
                <?php endif; ?>

                <input
                    type="file"
                    id="edit-product-image"
                    name="prodimg"
                    class="form-control"
                    accept="image/jpeg,image/png,image/gif,image/webp">

                <small class="text-muted">
                    Leave this empty to keep the current image.
                </small>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        Close
    </button>
    <button type="submit" class="btn btn-primary">
        <i data-feather="check-circle" class="icon-16"></i>
        Save Changes
    </button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
$(document).ready(function () {
    $('#product-edit-form').appForm({
        isModal: true,
        onSuccess: function (result) {
            // Reloading directly is compatible with both Bootstrap modal
            // versions and the custom appTable wrapper. It also guarantees
            // that the card is rebuilt from the newly saved database row.
            window.location.reload();
        }
    });

    $('#edit-product-image').on('change', function () {
        var file = this.files && this.files[0];
        if (!file) {
            return;
        }

        var reader = new FileReader();
        reader.onload = function (event) {
            $('#edit-product-preview')
                .attr('src', event.target.result)
                .show();
        };
        reader.readAsDataURL(file);
    });

    if (window.feather) {
        feather.replace();
    }
});
</script>
