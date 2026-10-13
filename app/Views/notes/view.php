<div class="modal-body clearfix">
    <div class="container-fluid">
        <h4><?php echo $model_info->title; ?></h4>
        <div class="mt15">
            <?php echo $model_info->description ? nl2br($model_info->description) : app_lang('no_description'); ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
</div>
