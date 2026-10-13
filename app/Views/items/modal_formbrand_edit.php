<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== EDIT BRAND MODAL — synced with site teal theme ===== */
.edit-brand-modal {
    font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
    color: #134e4a;
    -webkit-font-smoothing: antialiased;
}
.edit-brand-modal .modal-header-band {
    background: linear-gradient(135deg, #0d5e56 0%, #0f766e 52%, #0ea5a4 100%);
    padding: 24px 26px;
    border-radius: 0;
    display: flex;
    align-items: center;
    gap: 14px;
    margin: -15px -15px 22px;
    position: relative;
    overflow: hidden;
}
.edit-brand-modal .modal-header-band::after {
    content: "";
    position: absolute;
    inset: auto -40px -60px auto;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(153,246,228,.3) 0, transparent 68%);
    pointer-events: none;
}
.edit-brand-modal .modal-header-band .header-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,.18);
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.28rem;
    color: #fff;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.edit-brand-modal .modal-header-band h4 {
    color: #fff;
    font-size: 1.28rem;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.02em;
    position: relative;
    z-index: 1;
}
.edit-brand-modal .modal-header-band p {
    color: rgba(255,255,255,.9);
    font-size: .92rem;
    font-weight: 500;
    margin: 4px 0 0;
    position: relative;
    z-index: 1;
}
.edit-brand-modal .form-group label {
    font-weight: 600;
    color: #334155;
    font-size: .92rem;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.edit-brand-modal .form-group label i {
    color: #0f766e;
    font-size: .85rem;
}
.edit-brand-modal .form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: 11px;
    padding: 11px 14px;
    font-size: .95rem;
    font-family: inherit;
    transition: border-color .2s, box-shadow .2s;
    color: #134e4a;
}
.edit-brand-modal .form-control:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 4px rgba(15,118,110,.12);
    outline: none;
}
.edit-brand-modal .post-dropzone,
.edit-brand-modal .dropzone {
    border: 1.5px dashed #99f6e4 !important;
    border-radius: 12px !important;
    background: #f0fdfa !important;
}
.edit-brand-modal .modal-footer {
    background: #f8fafc;
    border-top: 1.5px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-radius: 0 0 12px 12px;
    margin: 22px -15px -15px;
}
.edit-brand-modal .btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: linear-gradient(135deg, #0d5e56, #0f766e);
    color: #fff;
    border: none;
    border-radius: 11px;
    font-weight: 700;
    font-size: .95rem;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 6px 18px rgba(15,118,110,.24);
    font-family: inherit;
}
.edit-brand-modal .btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 26px rgba(15,118,110,.32);
}
.edit-brand-modal .btn-save:focus {
    outline: 3px solid rgba(15,118,110,.28);
    outline-offset: 2px;
}
.edit-brand-modal .btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: #f1f5f9;
    color: #475569;
    border: 1.5px solid #e2e8f0;
    border-radius: 11px;
    font-weight: 700;
    font-size: .95rem;
    cursor: pointer;
    transition: background .2s, border-color .2s;
    font-family: inherit;
}
.edit-brand-modal .btn-cancel:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
}
.edit-brand-modal .modal-footer {
    justify-content: space-between;
}
.edit-brand-modal .modal-footer .brand-modal-foot-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
}
</style>

<?php echo form_open(get_uri("items/savebrand_edit"), array("id" => "brand-edit-form", "class" => "general-form edit-brand-modal", "role" => "form")); ?>

<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">

            <!-- HEADER BAND -->
            <div class="modal-header-band">
                <div class="header-icon" aria-hidden="true">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h4>Edit Brand</h4>
                    <p>Update the brand name and logo below</p>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo isset($itemid) ? $itemid : ''; ?>" />

            <div class="form-group">
                <label for="name">
                    <i class="fa-solid fa-tag" aria-hidden="true"></i>
                    Brand Name <span style="color:#dc2626;">*</span>
                </label>
                <?php
                echo form_input(array(
                    "id"               => "name",
                    "name"             => "name",
                    "value"            => isset($name) ? $name : '',
                    "class"            => "form-control validate-hidden",
                    "placeholder"      => "Enter brand name",
                    "autofocus"        => true,
                    "data-rule-required" => true,
                    "data-msg-required"  => app_lang("field_required"),
                    "aria-required"    => "true",
                ));
                ?>
            </div>

            <?php echo view("includes/dropzone_preview"); ?>

        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn-cancel brand-modal-cancel" aria-label="Cancel editing">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            Cancel
        </button>
        <div class="brand-modal-foot-actions">
            <button type="submit" class="btn-save" aria-label="Save brand changes">
                <i class="fa-solid fa-check" aria-hidden="true"></i>
                <?php echo app_lang('save'); ?>
            </button>
        </div>
    </div>
</div>

<?php echo form_close(); ?>
<!-- Submit handled by brand.php delegated AJAX handler (modal inline scripts do not run after ajax load) -->
