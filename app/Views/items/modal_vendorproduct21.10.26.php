<?php echo form_open(get_uri("items/savevendorproduct"), array("id" => "item-form", "class" => "general-form", "role" => "form", "novalidate" => "novalidate", "enctype" => "multipart/form-data")); ?>
<div id="items-dropzone" class="post-dropzone vendor-product-modal-ui">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id ?? ""; ?>" />

            <div class="vp-modal-hero">
                <div class="vp-modal-title">
                    <span class="vp-kicker">Product setup</span>
                    <div class="vp-title-row">
                        <h4><i class="fa fa-plus-circle"></i> ADD modal</h4>
                        <div class="vp-title-actions">
                            <button type="button" class="vp-hero-demo-btn" onclick="openVendorProductDemoVideo(event)" aria-label="Demo Video">
                                <i class="fa fa-play-circle"></i>
                                <span>Demo Video</span>
                            </button>
                            <button type="button" class="vp-hero-back-btn" data-bs-dismiss="modal" data-dismiss="modal" onclick="vendorProductGoBack(event)" aria-label="Back">
                                <i class="fa fa-arrow-left"></i>
                                <span>Back</span>
                            </button>
                        </div>
                    </div>
                    <p>Create a clean vendor product profile with brand, model, batch and preview details.</p>
                </div>
            </div>

            <?php if ("177") { ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 text-off"><!--?php echo app_lang('item_edit_instruction'); ?--></div>
                    </div>
                </div>
            <?php } ?>

            <!-- Brand & Product Row -->
            <div class="row mb-4 vp-picker-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="brand_id" class="control-label">
                            Brand <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2" id="email2" name="brand" required style="width: 100%;">
                            <option value="">&mdash; Select Brand &mdash;</option>
                            <?php
                            $rendered_brands = array();
                            foreach ($brand_dropdown as $value) {
                                $brand_name = trim($value->name);
                                $brand_key = strtolower($brand_name);
                                if ($brand_name === "" || isset($rendered_brands[$brand_key])) {
                                    continue;
                                }
                                $rendered_brands[$brand_key] = $brand_name;
                                ?>
                                <option value="<?php echo htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php } ?>
                        </select>
                        <div class="vp-custom-select" data-select-id="email2">
                            <button type="button" class="vp-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Brand &mdash;</button>
                            <div class="vp-custom-options" role="listbox">
                                <button type="button" class="vp-custom-option is-placeholder is-selected" data-value="" role="option">&mdash; Select Brand &mdash;</button>
                                <?php
                                foreach ($rendered_brands as $brand_name) {
                                    ?>
                                    <button type="button" class="vp-custom-option" data-value="<?php echo htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8'); ?>" role="option"><?php echo htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8'); ?></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="invalid-feedback">Please select a brand.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id" class="control-label">
                            Product <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2" id="email" name="category" required style="width: 100%;">
                            <option value="">&mdash; Select Product &mdash;</option>
                            <?php foreach ($categories_dropdown as $value) { ?>
                                <option value="<?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php } ?>
                        </select>
                        <div class="vp-custom-select" data-select-id="email">
                            <button type="button" class="vp-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Product &mdash;</button>
                            <div class="vp-custom-options" role="listbox">
                                <button type="button" class="vp-custom-option is-placeholder is-selected" data-value="" role="option">&mdash; Select Product &mdash;</button>
                                <?php foreach ($categories_dropdown as $value) { ?>
                                    <button type="button" class="vp-custom-option" data-value="<?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?>" role="option"><?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="invalid-feedback">Please select a product.</div>
                    </div>
                </div>
            </div>
            

            <!-- Model Information Card -->
            <div class="card mb-4 vp-section-card">
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
                                        oninput="syncVendorProductModelNo(this.value)"
                                        onkeyup="syncVendorProductModelNo(this.value)"
                                        onchange="syncVendorProductModelNo(this.value)"
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
            <div class="card mb-4 vp-section-card">
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
                                        style="background-color: #F1F3E0;">
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

            <!-- Model Logo: functional generate/upload buttons and image preview. -->
            <div class="card mb-4 vp-section-card vp-logo-card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fa fa-picture-o me-2"></i>Model Logo</h5>
                </div>
                <div class="card-body vp-logo-layout">
                    <div class="vp-logo-fields">
                        <label class="control-label">Model Logo <span class="text-danger">*</span></label>
                        <input type="hidden" id="vp-model-logo-mode" name="model_logo_mode"
                            value="<?php echo !empty($model_info->model_logo) ? 'keep' : 'generate'; ?>">
                        <div class="vp-logo-buttons" role="group" aria-label="Model logo source">
                            <?php if (!empty($model_info->model_logo)): ?>
                                <button type="button" class="vp-logo-action is-active" data-mode="keep"
                                    aria-pressed="true" onclick="vpChooseLogo('keep')">
                                    <i class="fa fa-check-circle"></i> Keep Current
                                </button>
                            <?php endif; ?>
                            <button type="button" class="vp-logo-action <?php echo empty($model_info->model_logo) ? 'is-active' : ''; ?>"
                                data-mode="generate" aria-pressed="<?php echo empty($model_info->model_logo) ? 'true' : 'false'; ?>"
                                onclick="vpChooseLogo('generate')">
                                <i class="fa fa-magic"></i> Create from Model Name
                            </button>
                            <button type="button" class="vp-logo-action" data-mode="upload" aria-pressed="false"
                                onclick="vpChooseLogo('upload', true)">
                                <i class="fa fa-upload"></i> Upload Image
                            </button>
                        </div>
                        <div id="vp-logo-upload-details" hidden>
                            <div class="vp-logo-upload-row">
                                <label for="vp-model-logo-file" class="control-label">Selected Logo</label>
                                <div class="vp-logo-file-row">
                                    <input type="file" id="vp-model-logo-file" name="model_logo_file"
                                        accept="image/png,image/jpeg,image/webp,.png,.jpg,.jpeg,.webp"
                                        class="form-control" onchange="vpModelLogoFileChanged()">
                                </div>
                            </div>
                            <p id="vp-logo-filename" class="vp-logo-hint">Choose a PNG, JPG, or WEBP image (max. 2 MB).</p>
                        </div>
                        <div id="vp-logo-error" class="vp-logo-error" role="alert" aria-live="polite"></div>
                        <p class="vp-logo-hint">Create a logo from the model name or upload your own. The logo will be saved with the model.</p>
                    </div>
                    <div class="vp-logo-preview">
                        <div class="vp-logo-preview-box" aria-label="Live model logo preview">
                            <span id="vp-logo-letters">M</span>
                            <img id="vp-logo-image" alt="Model logo" hidden
                                data-current-url="<?php echo !empty($model_info->model_logo) ? esc(base_url('files/model_logo/' . rawurlencode(basename($model_info->model_logo))), 'attr') : ''; ?>">
                        </div>
                        <span>Live Logo Preview</span>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mb-3 border-primary vp-section-card vp-preview-card">
                <div class="card-header bg-primary">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-eye me-2"></i>Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded vp-preview-panel">
                                <div class="row vp-preview-grid">
                                    <div class="col-md-3 vp-preview-item">
                                        <strong>Brand:</strong>
                                        <span id="preview-brand" class="ms-2">&mdash;</span>
                                    </div>
                                    <div class="col-md-3 vp-preview-item">
                                        <strong>Product:</strong>
                                        <span id="preview-product" class="ms-2">&mdash;</span>
                                    </div>
                                    <div class="col-md-3 vp-preview-item">
                                        <strong>Model Name:</strong>
                                        <span id="preview-model" class="ms-2">&mdash;</span>
                                    </div>
                                    <div class="col-md-3 vp-preview-item">
                                        <strong>Model Number:</strong>
                                        <span id="preview-modelno" class="ms-2">&mdash;</span>
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
        <button type="button" class="btn btn-secondary vp-back-btn" id="back-btn" data-bs-dismiss="modal" data-dismiss="modal" onclick="vendorProductGoBack(event)">
            <i class="fa fa-arrow-left me-1"></i>Back
        </button>
        <button type="submit" class="btn btn-primary" id="submit-btn">
            <i class="fa fa-save me-1"></i><?php echo app_lang('save'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<!-- Demo Video Popup -->
<div id="vpDemoVideoOverlay" class="vp-demo-video-overlay" aria-hidden="true">
    <div class="vp-demo-video-dialog" role="dialog" aria-modal="true" aria-labelledby="vpDemoVideoTitle">
        <div class="vp-demo-video-header">
            <div class="vp-demo-video-heading">
                <i class="fa fa-play-circle"></i>
                <span id="vpDemoVideoTitle">Demo Video</span>
            </div>
            <button type="button" class="vp-demo-video-close" onclick="closeVendorProductDemoVideo()" aria-label="Close demo video">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="vp-demo-video-body">
            <video id="vpDemoVideoPlayer" controls playsinline preload="metadata">
                <source src="<?php echo htmlspecialchars(base_url('assets/Demo_Videos/modal_demo.mp4') . '?v=' . filemtime(FCPATH . 'assets/Demo_Videos/modal_demo.mp4') . '-' . filesize(FCPATH . 'assets/Demo_Videos/modal_demo.mp4'), ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</div>

<style>
.vendor-product-modal-ui {
    /* Match copy_api.php palette */
    --vp-black: #161716;
    --vp-ink: #283028;
    --vp-olive: #778873;
    --vp-sage: #A1BC98;
    --vp-p1: #F1F3E0;
    --vp-p2: #D2DCB6;
    --vp-white: #ffffff;
    --vp-gold: #d7a441;
    --vp-danger: #c0392b;
    --vp-muted: #667064;
    --vp-border: rgba(119, 136, 115, .28);
    --vp-text: var(--vp-black);
    --vp-icon: var(--vp-olive);
    --vp-danger-soft: rgba(192, 57, 43, .12);
    --vp-shadow: 0 24px 70px rgba(22, 23, 22, .16);
    --vp-focus-ring: 0 0 0 4px rgba(161, 188, 152, .24);
    --vp-icon-glow: none;
    --vp-icon-glow-soft: none;

    background: linear-gradient(135deg, rgba(241, 243, 224, .96), rgba(210, 220, 182, .82));
    border-radius: 8px;
    color: var(--vp-ink);
    overflow: hidden;
}

.vendor-product-modal-ui .fa {
    display: inline-block;
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    line-height: 1;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
}

.vendor-product-modal-ui .me-1 {
    margin-right: .25rem;
}

.vendor-product-modal-ui .me-2 {
    margin-right: .5rem;
}

.vendor-product-modal-ui,
.vendor-product-modal-ui * {
    box-sizing: border-box;
}

.vendor-product-modal-ui .modal-body {
    padding: clamp(16px, 2vw, 26px);
    background:
        radial-gradient(circle at top left, rgba(161, 188, 152, .36), transparent 31%),
        linear-gradient(180deg, #ffffff 0%, var(--vp-p1) 100%);
    animation: vpFadeIn .28s ease;
}

.vendor-product-modal-ui .container-fluid {
    max-width: 1180px;
    padding-left: clamp(8px, 1.3vw, 16px);
    padding-right: clamp(8px, 1.3vw, 16px);
}

.vp-modal-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    align-items: center;
    gap: 18px;
    padding: clamp(18px, 2.5vw, 28px);
    margin-bottom: 22px;
    border: 1px solid rgba(210, 220, 182, .42);
    border-radius: 8px;
    background:
        linear-gradient(135deg, rgba(22, 23, 22, .96), rgba(119, 136, 115, .92)),
        var(--vp-black);
    color: var(--vp-white);
    box-shadow: var(--vp-shadow);
}

.vp-title-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex: 0 0 auto;
}

.vp-hero-demo-btn {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 8px;
    min-height: 36px;
    padding: 8px 12px;
    border: 1px solid rgba(210, 220, 182, .58);
    border-radius: 8px;
    background: var(--vp-p2);
    color: var(--vp-black);
    font-size: 13px;
    font-weight: 850;
    line-height: 1;
    cursor: pointer;
    transition: background .16s ease, color .16s ease, transform .16s ease, border-color .16s ease;
}

.vp-hero-demo-btn .fa {
    color: var(--vp-black);
}

.vp-hero-demo-btn:hover,
.vp-hero-demo-btn:focus {
    transform: translateY(-1px);
    border-color: var(--vp-white);
    background: var(--vp-white);
    color: var(--vp-black);
    outline: none;
}

.vp-hero-back-btn {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 8px;
    min-height: 36px;
    padding: 8px 12px;
    border: 1px solid rgba(210, 220, 182, .58);
    border-radius: 8px;
    background: rgba(255, 255, 255, .08);
    color: var(--vp-white);
    font-size: 13px;
    font-weight: 850;
    line-height: 1;
    transition: background .16s ease, color .16s ease, transform .16s ease, border-color .16s ease;
}

.vp-hero-back-btn .fa {
    color: var(--vp-p2);
}

.vp-hero-back-btn:hover,
.vp-hero-back-btn:focus {
    transform: translateY(-1px);
    border-color: var(--vp-p2);
    background: var(--vp-p2);
    color: var(--vp-black);
    outline: none;
}

.vp-hero-back-btn:hover .fa,
.vp-hero-back-btn:focus .fa {
    color: var(--vp-black);
}

.vp-kicker {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 5px 10px;
    margin-bottom: 10px;
    border: 1px solid rgba(241, 243, 224, .3);
    border-radius: 999px;
    background: rgba(241, 243, 224, .12);
    color: var(--vp-p2);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.vp-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

@media (max-width: 680px) {
    .vp-title-row {
        align-items: flex-start;
        flex-direction: column;
    }

    .vp-title-actions {
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
}

.vp-modal-title h4 {
    min-width: 0;
    margin: 0;
    color: var(--vp-white);
    font-size: clamp(22px, 2.6vw, 34px);
    font-weight: 900;
    line-height: 1.12;
    letter-spacing: 0;
}

.vp-modal-title h4 .fa {
    color: var(--vp-p2);
    margin-right: 10px;
}

.vp-modal-title p {
    max-width: 680px;
    margin: 9px 0 0;
    color: rgba(255, 255, 255, .82);
    font-size: 14px;
    line-height: 1.5;
}

.vendor-product-modal-ui .vp-picker-row {
    padding: 18px 10px 2px;
    margin-left: 0;
    margin-right: 0;
    border: 1px solid var(--vp-border);
    border-radius: 8px;
    background: rgba(255, 255, 255, .9);
    box-shadow: 0 12px 32px rgba(22, 23, 22, .08);
}

.vendor-product-modal-ui .vp-section-card {
    overflow: hidden;
    border: 1px solid rgba(22, 23, 22, .12);
    border-radius: 8px;
    background: rgba(255, 255, 255, .9);
    box-shadow: 0 16px 38px rgba(22, 23, 22, .08);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}

.vendor-product-modal-ui .vp-section-card:hover {
    transform: translateY(-2px);
    border-color: rgba(119, 136, 115, .42);
    box-shadow: 0 10px 24px rgba(119, 136, 115, .12);
}

.vendor-product-modal-ui .card-header {
    padding: 15px 18px;
    border-bottom: 1px solid rgba(119, 136, 115, .18);
    background: linear-gradient(135deg, rgba(241, 243, 224, .98), rgba(210, 220, 182, .72)) !important;
}

.vendor-product-modal-ui .card-header.bg-primary {
    background: linear-gradient(135deg, rgba(241, 243, 224, .98), rgba(210, 220, 182, .72)) !important;
    border-bottom: 1px solid rgba(119, 136, 115, .18);
}

.vendor-product-modal-ui .card-header.bg-primary .card-title {
    color: var(--vp-black);
}

.vendor-product-modal-ui .card-title {
    color: var(--vp-black);
    font-size: 15px;
    font-weight: 900;
    letter-spacing: 0;
}
.vendor-product-modal-ui .card-title .fa {
    color: var(--vp-olive);
    margin-right: 8px;
}

.vendor-product-modal-ui .card-body {
    padding: clamp(16px, 2vw, 22px);
}

.vendor-product-modal-ui .form-group {
    margin-bottom: 18px;
}

.vendor-product-modal-ui .control-label {
    display: block;
    margin-bottom: 8px;
    color: var(--vp-black);
    font-size: 13px;
    font-weight: 850;
}

.vendor-product-modal-ui .text-danger {
    color: var(--vp-danger) !important;
    font-weight: 900;
}

.vendor-product-modal-ui .form-control,
.vendor-product-modal-ui .select2-container--default .select2-selection--single {
    width: 100%;
    min-height: 46px;
    border: 1px solid var(--vp-border) !important;
    border-radius: 8px !important;
    background: var(--vp-white);
    color: var(--vp-text);
    font-size: 14px;
    font-weight: 650;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}

.vendor-product-modal-ui .form-control {
    padding: 11px 13px;
}

.vendor-product-modal-ui .form-control::placeholder {
    color: var(--vp-muted);
    font-weight: 650;
    opacity: .85;
}

.vendor-product-modal-ui .form-control:focus,
.vendor-product-modal-ui .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--vp-olive) !important;
    box-shadow: var(--vp-focus-ring);
    outline: none;
}

.vendor-product-modal-ui .input-group {
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
}

.vendor-product-modal-ui .input-group-text {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    min-width: 64px;
    padding: 0;
    border: 1px solid rgba(119, 136, 115, .32);
    border-right: 0;
    border-radius: 8px 0 0 8px;
    background: var(--vp-white) !important;
    color: var(--vp-olive);
}

.vendor-product-modal-ui .input-group-text .fa {
    margin: 0;
    font-size: 17px;
    color: var(--vp-olive);
}

.vendor-product-modal-ui .input-group .form-control {
    min-width: 0;
    padding-left: 18px !important;
    padding-right: 16px !important;
    border-left: 0 !important;
    border-radius: 0 8px 8px 0 !important;
}

.vendor-product-modal-ui .form-text {
    display: block;
    margin-top: 7px;
    color: var(--vp-muted) !important;
    font-size: 12px;
    line-height: 1.35;
}

.vendor-product-modal-ui .form-text .fa {
    color: var(--vp-olive);
    margin-right: 5px;
}

.vendor-product-modal-ui .invalid-feedback {
    display: none;
    margin-top: 6px;
    color: var(--vp-danger);
    font-size: 12px;
    font-weight: 700;
}

.vendor-product-modal-ui .form-group.vp-has-error .invalid-feedback {
    display: block !important;
}

.vendor-product-modal-ui .form-group.vp-has-error .control-label {
    color: var(--vp-danger);
}

.vendor-product-modal-ui .form-control.is-invalid {
    border-color: var(--vp-danger) !important;
    background-image: none;
    box-shadow: 0 0 0 4px var(--vp-danger-soft);
}

.vendor-product-modal-ui .input-group.vp-is-invalid .input-group-text {
    border-color: var(--vp-danger);
    background: linear-gradient(135deg, #7a1f18, var(--vp-danger));
    color: var(--vp-white);
}

.vendor-product-modal-ui .input-group.vp-is-invalid .form-control {
    border-color: var(--vp-danger) !important;
}

.vendor-product-modal-ui .select2-container--default .select2-selection--single {
    display: flex;
    align-items: center;
    padding: 0 42px 0 18px !important;
}

.vendor-product-modal-ui .select2-container--default .select2-selection--single .select2-selection__rendered {
    width: 100%;
    padding: 0;
    color: var(--vp-text);
    line-height: 44px;
    font-weight: 650;
    overflow: hidden;
    text-overflow: ellipsis;
}

.vendor-product-modal-ui .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--vp-muted);
    opacity: .85;
}

.vendor-product-modal-ui .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px !important;
    right: 10px !important;
}

.vendor-product-modal-ui select.form-control {
    appearance: none;
    background-image:
        linear-gradient(45deg, transparent 50%, var(--vp-olive) 50%),
        linear-gradient(135deg, var(--vp-olive) 50%, transparent 50%);
    background-position:
        calc(100% - 20px) 21px,
        calc(100% - 14px) 21px;
    background-size: 6px 6px, 6px 6px;
    background-repeat: no-repeat;
    padding-left: 18px;
    padding-right: 44px;
}

.vendor-product-modal-ui select.form-control option {
    color: var(--vp-text);
    background: var(--vp-white);
    font-weight: 700;
}

.vendor-product-modal-ui #model_no[readonly] {
    background: linear-gradient(135deg, var(--vp-p1), var(--vp-p2)) !important;
    color: var(--vp-text);
}

.vendor-product-modal-ui .vp-preview-panel {
    border: 1px solid rgba(119, 136, 115, .38);
    border-radius: 8px !important;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(241, 243, 224, .86)),
        var(--vp-white) !important;
}

.vendor-product-modal-ui .vp-preview-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin: 0;
}

.vendor-product-modal-ui .vp-preview-item {
    width: auto;
    max-width: none;
    padding: 13px 14px;
    border: 1px solid rgba(119, 136, 115, .18);
    border-radius: 8px;
    background: linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(241, 243, 224, .86));
    min-width: 0;
}

.vendor-product-modal-ui .vp-preview-item strong {
    display: block;
    margin-bottom: 5px;
    color: var(--vp-muted);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
}

.vendor-product-modal-ui #preview-brand,
.vendor-product-modal-ui #preview-product,
.vendor-product-modal-ui #preview-model,
.vendor-product-modal-ui #preview-modelno {
    display: block;
    margin-left: 0 !important;
    color: var(--vp-text);
    font-size: 14px;
    font-weight: 900;
    overflow-wrap: anywhere;
}

.vendor-product-modal-ui .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px clamp(18px, 2.5vw, 30px);
    border-top: 1px solid rgba(119, 136, 115, .22);
    background: linear-gradient(135deg, #ffffff, var(--vp-p1));
}

.vendor-product-modal-ui .btn {
    min-height: 44px;
    padding: 10px 22px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 900;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
}

.vendor-product-modal-ui .btn-primary {
    border: 1px solid var(--vp-black);
    background: var(--vp-black);
    color: var(--vp-p1);
    box-shadow: 0 12px 28px rgba(22, 23, 22, .18);
}

.vendor-product-modal-ui .btn .fa {
    text-shadow: none;
}

.vendor-product-modal-ui .btn-primary .fa {
    color: var(--vp-p1);
}

.vendor-product-modal-ui .btn-primary:hover,
.vendor-product-modal-ui .btn-primary:focus {
    transform: translateY(-1px);
    background: var(--vp-olive);
    border-color: var(--vp-olive);
    color: var(--vp-white);
    box-shadow: 0 12px 28px rgba(119, 136, 115, .22);
}

.vendor-product-modal-ui .btn-primary:hover .fa,
.vendor-product-modal-ui .btn-primary:focus .fa {
    color: var(--vp-white);
}

.vendor-product-modal-ui .vp-back-btn {
    border: 1px solid rgba(119, 136, 115, .34);
    background: var(--vp-white);
    color: var(--vp-black);
    box-shadow: 0 10px 22px rgba(22, 23, 22, .08);
}

.vendor-product-modal-ui .vp-back-btn .fa {
    color: var(--vp-olive);
}

.vendor-product-modal-ui .vp-back-btn:hover .fa,
.vendor-product-modal-ui .vp-back-btn:focus .fa {
    color: var(--vp-black);
}

.vendor-product-modal-ui .vp-back-btn:hover,
.vendor-product-modal-ui .vp-back-btn:focus {
    transform: translateY(-1px);
    border-color: rgba(119, 136, 115, .34);
    background: var(--vp-p2);
    color: var(--vp-black);
    box-shadow: 0 10px 24px rgba(119, 136, 115, .12);
}

.vp-swal-popup {
    border-radius: 8px !important;
    border: 1px solid var(--vp-border) !important;
    box-shadow: 0 28px 70px rgba(22, 23, 22, .16) !important;
}

.vp-swal-title {
    color: var(--vp-text) !important;
    font-weight: 900 !important;
}

.vp-swal-confirm {
    color: var(--vp-p1) !important;
    font-weight: 900 !important;
    border-radius: 8px !important;
    background: var(--vp-black) !important;
    box-shadow: 0 12px 28px rgba(22, 23, 22, .18) !important;
}

.vp-save-toast {
    position: fixed;
    top: 22px;
    right: 22px;
    z-index: 99999;
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr);
    gap: 12px;
    align-items: center;
    width: min(360px, calc(100vw - 32px));
    padding: 14px 16px;
    border: 1px solid var(--vp-border);
    border-radius: 8px;
    background: var(--vp-white);
    color: var(--vp-text);
    box-shadow: 0 24px 60px rgba(22, 23, 22, .16);
    transform: translateY(-12px);
    opacity: 0;
    pointer-events: none;
    transition: opacity .18s ease, transform .18s ease;
}

.vp-save-toast.is-visible {
    transform: translateY(0);
    opacity: 1;
}

.vp-save-toast-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 8px;
    background: var(--vp-black);
    color: var(--vp-p1);
    font-size: 18px;
}

.vp-save-toast-title {
    margin: 0 0 3px;
    color: var(--vp-text);
    font-size: 15px;
    font-weight: 900;
    line-height: 1.2;
}

.vp-save-toast-text {
    margin: 0;
    color: var(--vp-muted);
    font-size: 13px;
    font-weight: 650;
    line-height: 1.35;
}

.vp-select2-dropdown,
.select2-dropdown {
    overflow: hidden;
    border: 1px solid var(--vp-border) !important;
    border-radius: 8px !important;
    background: var(--vp-white);
    box-shadow: 0 24px 54px rgba(22, 23, 22, .16);
}

.vp-select2-dropdown .select2-results {
    background: linear-gradient(180deg, var(--vp-white) 0%, #fffffb 100%);
}

.vp-select2-dropdown .select2-results__options {
    max-height: min(330px, 46vh);
    padding: 8px;
}

.vp-select2-dropdown .select2-results__option {
    margin: 3px 0;
    padding: 11px 14px;
    border-radius: 8px;
    color: var(--vp-text);
    font-size: 14px;
    font-weight: 750;
    line-height: 1.25;
    transition: background .14s ease, color .14s ease, transform .14s ease;
}

.vp-select2-dropdown .select2-results__option--highlighted,
.select2-results__option--highlighted {
    background: linear-gradient(135deg, var(--vp-p1), var(--vp-p2)) !important;
    color: var(--vp-text) !important;
    transform: translateX(2px);
}

.vp-select2-dropdown .select2-results__option[aria-selected=true] {
    background: var(--vp-p1);
    color: var(--vp-text);
}

.vp-select2-dropdown .select2-results__option:first-child {
    color: var(--vp-muted);
    font-style: italic;
    font-weight: 700;
}

.vendor-product-modal-ui select.select2,
.vendor-product-modal-ui select.vp-customized-select {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

.vendor-product-modal-ui .select2-container {
    display: none !important;
}

.vp-custom-select {
    position: relative;
    width: 100%;
}

.vp-custom-select-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    width: 100%;
    min-height: 64px;
    padding: 14px 20px;
    border: 1px solid rgba(119, 136, 115, .32);
    border-radius: 8px;
    background: #fffffb;
    color: var(--vp-text);
    font-size: 16px;
    font-weight: 850;
    line-height: 1.2;
    text-align: left;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.86);
    transition: border-color .16s ease, box-shadow .16s ease;
}

.vp-custom-select-btn::after {
    content: "";
    width: 0;
    height: 0;
    border-left: 9px solid transparent;
    border-right: 9px solid transparent;
    border-top: 9px solid var(--vp-olive);
    flex: 0 0 auto;
}

.vp-custom-select.is-open .vp-custom-select-btn {
    border-color: var(--vp-sage);
    background: #fbfcf4;
    box-shadow: var(--vp-focus-ring);
}

.vp-custom-select.is-invalid .vp-custom-select-btn {
    border-color: var(--vp-danger) !important;
    background: linear-gradient(180deg, #fffafa 0%, #fff5f3 100%);
    box-shadow: 0 0 0 5px var(--vp-danger-soft) !important;
}

.vp-custom-select.is-invalid .vp-custom-select-btn::after {
    border-top-color: var(--vp-danger);
}

.vp-custom-select.is-open .vp-custom-select-btn::after {
    border-top: 0;
    border-bottom: 9px solid var(--vp-olive);
}

.vp-custom-options {
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + 8px);
    z-index: 10050;
    display: none;
    max-height: min(340px, 48vh);
    overflow-y: auto;
    padding: 8px;
    border: 1px solid var(--vp-border);
    border-radius: 8px;
    background: #fffffb;
    box-shadow: 0 18px 42px rgba(22, 23, 22, .16);
}

.vp-custom-select.is-open .vp-custom-options {
    display: block;
}

.vp-custom-option {
    display: flex;
    align-items: center;
    min-height: 44px;
    padding: 11px 14px;
    margin: 3px 0;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: var(--vp-text);
    font-size: 14px;
    font-weight: 800;
    line-height: 1.3;
    text-align: left;
    width: 100%;
    transition: background .14s ease, color .14s ease, transform .14s ease;
}

.vp-custom-option:hover,
.vp-custom-option:focus {
    background: var(--vp-p1);
    color: var(--vp-black);
    outline: none;
    transform: translateX(2px);
}

.vp-custom-option.is-selected {
    background: var(--vp-p1);
    color: var(--vp-black);
}

.vp-custom-option.is-placeholder {
    color: var(--vp-muted);
    font-style: italic;
}


/* Demo video popup */
.vp-demo-video-overlay {
    position: fixed;
    inset: 0;
    z-index: 100000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(22, 23, 22, .72);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

.vp-demo-video-overlay.is-open {
    display: flex;
}

.vp-demo-video-dialog {
    width: min(900px, 96vw);
    max-height: 92vh;
    overflow: hidden;
    border: 1px solid rgba(210, 220, 182, .45);
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 30px 90px rgba(0, 0, 0, .36);
}

.vp-demo-video-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #161716, #778873);
    color: #ffffff;
}

.vp-demo-video-heading {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-size: 15px;
    font-weight: 900;
}

.vp-demo-video-heading .fa {
    color: #D2DCB6;
}

.vp-demo-video-close {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, .25);
    border-radius: 8px;
    background: rgba(255, 255, 255, .08);
    color: #ffffff;
    cursor: pointer;
    transition: background .16s ease, transform .16s ease;
}

.vp-demo-video-close:hover,
.vp-demo-video-close:focus {
    background: rgba(255, 255, 255, .18);
    transform: translateY(-1px);
    outline: none;
}

.vp-demo-video-body {
    padding: 12px;
    background: #101110;
}

#vpDemoVideoPlayer {
    display: block;
    width: 100%;
    max-height: calc(92vh - 78px);
    border-radius: 8px;
    background: #000;
}

body.vp-demo-video-open {
    overflow: hidden;
}

@keyframes vpFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 991.98px) {
    .vendor-product-modal-ui .vp-preview-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .vp-modal-hero {
        grid-template-columns: 1fr;
        align-items: start;
    }

    .vendor-product-modal-ui .card-body .row > div,
    .vendor-product-modal-ui .vp-picker-row > div {
        margin-bottom: 12px;
    }

    .vendor-product-modal-ui .modal-footer {
        padding: 14px 16px;
    }

    .vendor-product-modal-ui .modal-footer .btn {
        width: 100%;
    }
}

@media (max-width: 575.98px) {
    .vendor-product-modal-ui .modal-body {
        padding: 12px;
    }

    .vendor-product-modal-ui .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .vp-modal-hero {
        padding: 16px;
    }

    .vendor-product-modal-ui .vp-preview-grid {
        grid-template-columns: 1fr;
    }

    .vendor-product-modal-ui .card-header,
    .vendor-product-modal-ui .card-body {
        padding: 14px;
    }
}

/* Responsive layout overrides: use the modal width, not Bootstrap's page grid. */
.vendor-product-modal-ui {
    width: 100%;
    max-width: min(1180px, 100%);
    margin: 0 auto;
    container-type: inline-size;
}

.vendor-product-modal-ui .container-fluid {
    width: 100%;
    max-width: 1180px;
}

.vendor-product-modal-ui .vp-picker-row {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    align-items: start;
    padding: 18px;
}

.vendor-product-modal-ui .vp-picker-row::before,
.vendor-product-modal-ui .vp-picker-row::after,
.vendor-product-modal-ui .card-body > .row::before,
.vendor-product-modal-ui .card-body > .row::after,
.vendor-product-modal-ui .vp-preview-grid::before,
.vendor-product-modal-ui .vp-preview-grid::after {
    display: none !important;
    content: none !important;
}

.vendor-product-modal-ui .vp-picker-row > [class*="col-"],
.vendor-product-modal-ui .card-body > .row > [class*="col-"],
.vendor-product-modal-ui .vp-preview-grid > [class*="col-"] {
    float: none !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0;
    padding-left: 0;
    padding-right: 0;
}

.vendor-product-modal-ui .vp-picker-row .form-group,
.vendor-product-modal-ui .card-body > .row > [class*="col-"] .form-group {
    margin-bottom: 0;
}

.vendor-product-modal-ui .vp-section-card .card-body > .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px 24px;
    align-items: start;
    margin-left: 0;
    margin-right: 0;
}

.vendor-product-modal-ui .vp-section-card .card-body > .row.mt-3 {
    margin-top: 20px !important;
}

.vendor-product-modal-ui .vp-section-card .card-body > .row > .col-md-12 {
    grid-column: 1 / -1;
}

.vendor-product-modal-ui .vp-section-card .card-body > .row > .col-md-6 {
    grid-column: span 2;
}

.vendor-product-modal-ui .vp-preview-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin: 0;
}

.vendor-product-modal-ui.vp-layout-md .vp-section-card .card-body > .row {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
}

.vendor-product-modal-ui.vp-layout-md .vp-preview-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
}

.vendor-product-modal-ui.vp-layout-sm .vp-picker-row,
.vendor-product-modal-ui.vp-layout-sm .vp-section-card .card-body > .row,
.vendor-product-modal-ui.vp-layout-sm .vp-preview-grid {
    grid-template-columns: 1fr !important;
    gap: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .vp-section-card .card-body > .row > .col-md-6,
.vendor-product-modal-ui.vp-layout-sm .vp-section-card .card-body > .row > .col-md-12 {
    grid-column: 1 / -1 !important;
}

.vendor-product-modal-ui.vp-layout-sm .modal-body {
    padding: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .container-fluid {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.vendor-product-modal-ui.vp-layout-sm .vp-modal-hero {
    padding: 14px !important;
    margin-bottom: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .vp-picker-row {
    padding: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .card-header,
.vendor-product-modal-ui.vp-layout-sm .card-body {
    padding: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .vp-custom-select-btn,
.vendor-product-modal-ui.vp-layout-sm .form-control,
.vendor-product-modal-ui.vp-layout-sm .select2-container--default .select2-selection--single {
    min-height: 52px !important;
    font-size: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .input-group-text {
    width: 54px !important;
    min-width: 54px !important;
}

.vendor-product-modal-ui.vp-layout-sm .modal-footer {
    flex-direction: column-reverse !important;
    align-items: stretch !important;
    padding: 14px !important;
}

.vendor-product-modal-ui.vp-layout-sm .modal-footer .btn {
    width: 100% !important;
}

@container (max-width: 980px) {
    .vendor-product-modal-ui .vp-section-card .card-body > .row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .vendor-product-modal-ui .vp-preview-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@container (max-width: 680px) {
    .vendor-product-modal-ui .modal-body {
        padding: 14px;
    }

    .vendor-product-modal-ui .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .vendor-product-modal-ui .vp-modal-hero {
        padding: 14px;
        margin-bottom: 14px;
    }

    .vendor-product-modal-ui .vp-title-row {
        flex-wrap: wrap;
    }

    .vendor-product-modal-ui .vp-modal-title h4 {
        font-size: 24px;
    }

    .vendor-product-modal-ui .vp-modal-title p {
        font-size: 13px;
    }

    .vendor-product-modal-ui .vp-picker-row,
    .vendor-product-modal-ui .vp-section-card .card-body > .row,
    .vendor-product-modal-ui .vp-preview-grid {
        grid-template-columns: 1fr !important;
        gap: 14px;
    }

    .vendor-product-modal-ui .vp-section-card .card-body > .row > .col-md-6,
    .vendor-product-modal-ui .vp-section-card .card-body > .row > .col-md-12 {
        grid-column: 1 / -1;
    }

    .vendor-product-modal-ui .vp-picker-row {
        padding: 14px;
    }

    .vendor-product-modal-ui .vp-section-card {
        margin-bottom: 14px !important;
    }

    .vendor-product-modal-ui .card-header {
        padding: 13px 14px;
    }

    .vendor-product-modal-ui .card-body {
        padding: 14px;
    }

    .vendor-product-modal-ui .vp-custom-select-btn,
    .vendor-product-modal-ui .form-control,
    .vendor-product-modal-ui .select2-container--default .select2-selection--single {
        min-height: 52px;
        font-size: 14px;
    }

    .vendor-product-modal-ui .input-group-text {
        width: 54px;
        min-width: 54px;
    }

    .vendor-product-modal-ui .modal-footer {
        flex-direction: column-reverse;
        align-items: stretch;
        padding: 14px;
    }

    .vendor-product-modal-ui .modal-footer .btn {
        width: 100%;
    }
}

@media (max-width: 980px) {
    .vendor-product-modal-ui .vp-section-card .card-body > .row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .vendor-product-modal-ui .vp-preview-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 680px) {
    .vendor-product-modal-ui .modal-body {
        padding: 14px;
    }

    .vendor-product-modal-ui .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .vendor-product-modal-ui .vp-picker-row,
    .vendor-product-modal-ui .vp-section-card .card-body > .row,
    .vendor-product-modal-ui .vp-preview-grid {
        grid-template-columns: 1fr !important;
        gap: 14px;
    }

    .vendor-product-modal-ui .vp-section-card .card-body > .row > .col-md-6,
    .vendor-product-modal-ui .vp-section-card .card-body > .row > .col-md-12 {
        grid-column: 1 / -1;
    }

    .vendor-product-modal-ui .modal-footer {
        flex-direction: column-reverse;
        align-items: stretch;
    }

    .vendor-product-modal-ui .modal-footer .btn {
        width: 100%;
    }
}

/* Standalone functional Model Logo section */
.vendor-product-modal-ui .vp-logo-layout {
    display: grid; grid-template-columns: minmax(0, 1fr) 190px; gap: 24px;
    align-items: center; padding: 20px;
}
.vendor-product-modal-ui .vp-logo-buttons {
    display: flex; flex-wrap: wrap; gap: 8px; margin: 8px 0 12px;
}
.vendor-product-modal-ui .vp-logo-action {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 13px; min-height: 42px; border-radius: 8px;
    border: 1px solid #d2dabc; background: #fff; color: #283028;
    font-size: 13px; font-weight: 750; cursor: pointer;
    transition: background .16s, border-color .16s, box-shadow .16s;
    position: relative; z-index: 1; pointer-events: auto;
}
.vendor-product-modal-ui .vp-logo-action:hover,
.vendor-product-modal-ui .vp-logo-action:focus-visible {
    border-color: #778873; background: #f1f3e0;
    outline: 2px solid transparent; box-shadow: 0 0 0 3px rgba(119,136,115,.23);
}
.vendor-product-modal-ui .vp-logo-action.is-active {
    border-color: #778873; background: #f1f3e0;
    box-shadow: 0 0 0 3px rgba(119,136,115,.18);
}
.vendor-product-modal-ui .vp-logo-action .fa { color: #546b41; }
.vendor-product-modal-ui .vp-logo-preview { display: flex; flex-direction: column; align-items: center; gap: 8px; color: #546b41; font-size: 12px; font-weight: 750; }
.vendor-product-modal-ui .vp-logo-preview-box {
    width: 112px; height: 112px; display: flex; align-items: center; justify-content: center;
    border-radius: 17px; overflow: hidden;
    background: linear-gradient(135deg, #283028, #778873); color: white;
    border: 1px solid #d2dabc; box-shadow: 0 12px 25px rgba(40,48,40,.16);
    font-weight: 850; font-size: 42px;
}
.vendor-product-modal-ui .vp-logo-preview-box img { width: 100%; height: 100%; object-fit: contain; background: #fff; }
.vendor-product-modal-ui .vp-logo-preview-box img[hidden] { display: none !important; }
.vendor-product-modal-ui .vp-logo-preview-box span[hidden] { display: none !important; }
.vendor-product-modal-ui .vp-logo-file-row .form-control { max-width: 480px; padding: 9px; min-height: auto; }
.vendor-product-modal-ui #vp-logo-upload-details { padding: 10px 0; }
.vendor-product-modal-ui #vp-logo-upload-details[hidden] { display: none !important; }
.vendor-product-modal-ui .vp-logo-hint { font-size: 12px; line-height: 1.5; color: #667064; margin: 8px 0 0; }
.vendor-product-modal-ui .vp-logo-error { color: #c0392b; font-size: 12px; font-weight: 750; margin-top: 6px; }
@media (max-width: 680px) {
  .vendor-product-modal-ui .vp-logo-layout { grid-template-columns: 1fr; }
  .vendor-product-modal-ui .vp-logo-preview { align-items: flex-start; }
}

</style>

<script type="text/javascript">

/* Logo picker + multipart Save; pure browser JavaScript so no dependency on jQuery/appForm. */
(function () {
    'use strict';
    var fileObjectUrl = null;
    var form = document.getElementById('item-form');
    if (!form || form.dataset.logoUploadBound === '1') return;
    form.dataset.logoUploadBound = '1';

    var root = form.querySelector('.vendor-product-modal-ui');
    var modeInput = form.querySelector('#vp-model-logo-mode');
    var uploadInfo = form.querySelector('#vp-logo-upload-details');
    var fileInput = form.querySelector('#vp-model-logo-file');
    var image = form.querySelector('#vp-logo-image');
    var letters = form.querySelector('#vp-logo-letters');
    var error = form.querySelector('#vp-logo-error');
    var fileLabel = form.querySelector('#vp-logo-filename');
    var modelName = form.querySelector('#model_name');
    var submitButton = form.querySelector('#submit-btn');

    function setError(message) { error.textContent = message || ''; }
    function makeInitials() {
        var name = modelName ? modelName.value.trim() : '';
        var chunks = name.split(/[\s_-]+/).filter(Boolean);
        var val = chunks.length >= 2 ? chunks.slice(0, 2).map(function(t) { return t.charAt(0); }).join('') : name.slice(0, 2);
        letters.textContent = (val || 'M').toUpperCase();
    }
    function clearObjectUrl() {
        if (fileObjectUrl) { URL.revokeObjectURL(fileObjectUrl); fileObjectUrl = null; }
    }
    function preview(src) {
        if (src) { image.src = src; image.hidden = false; letters.hidden = true; }
        else { image.hidden = true; image.removeAttribute('src'); letters.hidden = false; makeInitials(); }
    }
    function validFile(showMessage) {
        if (modeInput.value !== 'upload') return true;
        var file = fileInput.files && fileInput.files[0];
        if (!file) { if (showMessage) setError('Please select an image before saving.'); return false; }
        if (['image/png','image/jpeg','image/webp'].indexOf(file.type) === -1) {
            if (showMessage) setError('Only PNG, JPG, or WEBP images are allowed.'); return false;
        }
        if (file.size > 2 * 1024 * 1024) {
            if (showMessage) setError('Logo image must be 2 MB or smaller.'); return false;
        }
        return true;
    }
    function refreshLogo() {
        var mode = modeInput.value;
        uploadInfo.hidden = mode !== 'upload';
        fileInput.required = mode === 'upload';
        root.querySelectorAll('.vp-logo-action').forEach(function(button) {
            var on = button.dataset.mode === mode;
            button.classList.toggle('is-active', on);
            button.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        clearObjectUrl();
        if (mode === 'keep' && image.dataset.currentUrl) preview(image.dataset.currentUrl);
        else if (mode === 'upload' && validFile(false)) {
            fileObjectUrl = URL.createObjectURL(fileInput.files[0]); preview(fileObjectUrl);
        } else preview(null);
        fileLabel.textContent = fileInput.files && fileInput.files[0]
          ? 'Selected: ' + fileInput.files[0].name : 'Choose a PNG, JPG, or WEBP image (max. 2 MB).';
        if (mode !== 'upload') setError('');
    }

    window.vpChooseLogo = function(mode, showPicker) {
        if (['generate', 'upload', 'keep'].indexOf(mode) === -1) return false;
        modeInput.value = mode;
        setError('');
        refreshLogo();
        // Open the operating-system image chooser directly from the Upload button.
        if (mode === 'upload' && showPicker) fileInput.click();
        return false;
    };
    window.vpModelLogoFileChanged = function() {
        setError('');
        modeInput.value = 'upload';
        refreshLogo();
        validFile(true);
    };
    if (modelName) modelName.addEventListener('input', function() {
        if (modeInput.value === 'generate') makeInitials();
    });
    refreshLogo();

    // Must register in capture phase before legacy form submit handlers.
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (form.dataset.logoSaving === '1') return;
        if (typeof validateVendorProductForm === 'function' && !validateVendorProductForm(true)) return;
        if (!validFile(true)) { fileInput.focus(); return; }

        var data = new FormData(form);
        var original = submitButton ? submitButton.innerHTML : '';
        form.dataset.logoSaving = '1';
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
        }
        fetch(form.action, {
            method: 'POST',
            body: data,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(async function(response) {
            var raw = await response.text();
            var parsed;
            try { parsed = JSON.parse(raw); }
            catch (_) { throw new Error('Server returned an unexpected response (HTTP ' + response.status + '). Check controller and server logs.'); }
            var ok = parsed && (parsed.success === true || parsed.success === 1 || parsed.success === '1');
            if (!response.ok || !ok) throw new Error(parsed.message || 'Unable to save model.');
            return parsed;
        }).then(function(result) {
            if (window.Swal && typeof Swal.fire === 'function') {
                return Swal.fire({icon:'success', title:'Model saved', text:result.message || 'Model and logo saved successfully.', confirmButtonColor:'#546B41'});
            }
            alert(result.message || 'Model and logo saved successfully.');
        }).then(function() {
            window.location.reload();
        }).catch(function(err) {
            console.error('[Model Logo Save]', err);
            if (window.Swal && typeof Swal.fire === 'function') {
                Swal.fire({icon:'error',title:'Save failed',text:err.message,confirmButtonColor:'#546B41'});
            } else { alert('Save failed: ' + err.message); }
        }).finally(function() {
            form.dataset.logoSaving = '0';
            if (submitButton) { submitButton.disabled = false; submitButton.innerHTML = original; }
        });
    }, true);
})();
</script>

<script type="text/javascript">
function ensureVendorProductViewport() {
    var viewport = document.querySelector('meta[name="viewport"]');

    if (!viewport) {
        viewport = document.createElement('meta');
        viewport.name = 'viewport';
        document.head.appendChild(viewport);
    }

    viewport.setAttribute('content', 'width=device-width, initial-scale=1');
}

function applyVendorProductResponsiveLayout() {
    var modalUi = document.querySelector('.vendor-product-modal-ui');

    if (!modalUi) {
        return;
    }

    var width = modalUi.getBoundingClientRect().width || modalUi.offsetWidth || window.innerWidth;

    modalUi.classList.remove('vp-layout-sm', 'vp-layout-md', 'vp-layout-lg');

    if (width <= 680) {
        modalUi.classList.add('vp-layout-sm');
    } else if (width <= 980) {
        modalUi.classList.add('vp-layout-md');
    } else {
        modalUi.classList.add('vp-layout-lg');
    }
}

ensureVendorProductViewport();
applyVendorProductResponsiveLayout();
window.addEventListener('resize', applyVendorProductResponsiveLayout);
window.addEventListener('orientationchange', applyVendorProductResponsiveLayout);

function syncVendorProductModelNo(value) {
    var modelNoInput = document.getElementById('model_no');

    if (modelNoInput) {
        modelNoInput.value = value || '';
    }

    updateVendorProductPreview();
}

function vendorProductGoBack(event) {
    if (event) {
        event.preventDefault();
    }

    var modal = document.querySelector('.modal.show');

    if (modal && window.jQuery && jQuery.fn.modal) {
        jQuery(modal).modal('hide');
        return false;
    }

    if (window.bootstrap && modal) {
        var modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
        modalInstance.hide();
        return false;
    }

    if (window.history.length > 1) {
        window.history.back();
        return false;
    }

    window.location.href = '<?php echo get_uri("items"); ?>';
    return false;
}

function openVendorProductDemoVideo(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    var overlay = document.getElementById('vpDemoVideoOverlay');
    var player = document.getElementById('vpDemoVideoPlayer');

    if (!overlay || !player) {
        return false;
    }

    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.classList.add('vp-demo-video-open');

    try {
        player.currentTime = 0;
        var playPromise = player.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(function () {
                // Browser may require the user to press play manually.
            });
        }
    } catch (e) {}

    return false;
}

function closeVendorProductDemoVideo() {
    var overlay = document.getElementById('vpDemoVideoOverlay');
    var player = document.getElementById('vpDemoVideoPlayer');

    if (player) {
        try {
            player.pause();
            player.currentTime = 0;
        } catch (e) {}
    }

    if (overlay) {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
    }

    document.body.classList.remove('vp-demo-video-open');
}

document.addEventListener('click', function (event) {
    var overlay = document.getElementById('vpDemoVideoOverlay');
    if (overlay && overlay.classList.contains('is-open') && event.target === overlay) {
        closeVendorProductDemoVideo();
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        var overlay = document.getElementById('vpDemoVideoOverlay');
        if (overlay && overlay.classList.contains('is-open')) {
            closeVendorProductDemoVideo();
        }
    }
});

function updateVendorProductPreview() {
    var emptyPlaceholder = '\u2014';
    var brandSelect = document.getElementById('email2');
    var productSelect = document.getElementById('email');
    var modelInput = document.getElementById('model_name');
    var modelNoInput = document.getElementById('model_no');
    var brandPreview = document.getElementById('preview-brand');
    var productPreview = document.getElementById('preview-product');
    var modelPreview = document.getElementById('preview-model');
    var modelNoPreview = document.getElementById('preview-modelno');
    var brandText = brandSelect && brandSelect.value ? brandSelect.options[brandSelect.selectedIndex].text : emptyPlaceholder;
    var productText = productSelect && productSelect.value ? productSelect.options[productSelect.selectedIndex].text : emptyPlaceholder;
    var modelText = modelInput && modelInput.value ? modelInput.value : emptyPlaceholder;
    var modelNoText = modelNoInput && modelNoInput.value ? modelNoInput.value : (modelInput && modelInput.value ? modelInput.value : emptyPlaceholder);

    if (brandPreview) {
        brandPreview.textContent = brandText;
    }

    if (productPreview) {
        productPreview.textContent = productText;
    }

    if (modelPreview) {
        modelPreview.textContent = modelText;
    }

    if (modelNoPreview) {
        modelNoPreview.textContent = modelNoText;
    }
}

function setVendorProductValidationState(fieldId, isInvalid) {
    var field = document.getElementById(fieldId);

    if (!field) {
        return;
    }

    var formGroup = field.closest('.form-group');
    var inputGroup = field.closest('.input-group');
    var customSelect = document.querySelector('.vp-custom-select[data-select-id="' + fieldId + '"]');

    field.classList.toggle('is-invalid', isInvalid);
    field.setAttribute('aria-invalid', isInvalid ? 'true' : 'false');

    if (formGroup) {
        formGroup.classList.toggle('vp-has-error', isInvalid);
    }

    if (inputGroup) {
        inputGroup.classList.toggle('vp-is-invalid', isInvalid);
    }

    if (customSelect) {
        customSelect.classList.toggle('is-invalid', isInvalid);
    }
}

function isVendorProductPatternValid(fieldId) {
    var field = document.getElementById(fieldId);

    if (!field || !field.value || !field.getAttribute('pattern')) {
        return true;
    }

    return new RegExp(field.getAttribute('pattern')).test(field.value.trim());
}

function validateVendorProductForm(showAlert) {
    var errors = [];
    var requiredFields = [
        { id: 'email2', label: 'Brand' },
        { id: 'email', label: 'Product' },
        { id: 'model_name', label: 'Model Name', pattern: true },
        { id: 'version', label: 'Version', pattern: true },
        { id: 'batch', label: 'Batch/Lot No.', pattern: true }
    ];

    requiredFields.forEach(function(item) {
        var field = document.getElementById(item.id);
        var value = field ? field.value.trim() : '';
        var invalid = !value;

        if (!invalid && item.pattern && !isVendorProductPatternValid(item.id)) {
            invalid = true;
            errors.push(item.label + ' format');
        } else if (invalid) {
            errors.push(item.label);
        }

        setVendorProductValidationState(item.id, invalid);
    });

    if (errors.length && showAlert && typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Please check required fields',
            html: '<strong>Fix these fields:</strong><br>' + errors.join('<br>'),
            confirmButtonColor: '#161716'
        });
    }

    return errors.length === 0;
}

function showVendorProductSavedAlert(options) {
    var settings = options || {};
    var existingToast = document.querySelector('.vp-save-toast');

    if (existingToast) {
        existingToast.remove();
    }

    var toast = document.createElement('div');
    toast.className = 'vp-save-toast';
    toast.innerHTML = '<div class="vp-save-toast-icon"><i class="fa fa-check"></i></div>' +
        '<div><p class="vp-save-toast-title">Saved successfully</p>' +
        '<p class="vp-save-toast-text">Vendor product model has been saved.</p></div>';
    document.body.appendChild(toast);

    window.setTimeout(function() {
        toast.classList.add('is-visible');
    }, 20);

    window.setTimeout(function() {
        toast.classList.remove('is-visible');
        window.setTimeout(function() {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 220);
    }, settings.timer || 2200);

    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'success',
            title: 'Saved successfully',
            text: 'Vendor product model has been saved.',
            color: '#161716',
            background: '#ffffff',
            iconColor: '#778873',
            confirmButtonColor: '#161716',
            confirmButtonText: 'Done',
            timer: settings.timer || 1800,
            timerProgressBar: true,
            showConfirmButton: settings.showConfirmButton || false,
            customClass: {
                popup: 'vp-swal-popup',
                title: 'vp-swal-title',
                confirmButton: 'vp-swal-confirm'
            }
        });
    }

    return Promise.resolve();
}

function initVendorProductNativeDropdowns() {
    var selects = document.querySelectorAll('.vp-custom-select');
    var previewInputs = document.querySelectorAll('#email2, #email, #model_name, #model_no, #version, #batch');

    function closeAll(except) {
        selects.forEach(function(selectUi) {
            if (selectUi !== except) {
                selectUi.classList.remove('is-open');
                var btn = selectUi.querySelector('.vp-custom-select-btn');
                if (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    selects.forEach(function(selectUi) {
        if (selectUi.dataset.vpBound === '1') {
            return;
        }

        selectUi.dataset.vpBound = '1';
        var btn = selectUi.querySelector('.vp-custom-select-btn');

        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var willOpen = !selectUi.classList.contains('is-open');
                closeAll(selectUi);
                selectUi.classList.toggle('is-open', willOpen);
                btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        selectUi.querySelectorAll('.vp-custom-option').forEach(function(option) {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                var nativeSelect = document.getElementById(selectUi.dataset.selectId);

                if (nativeSelect) {
                    nativeSelect.value = option.dataset.value || '';
                    nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (btn) {
                    btn.innerHTML = option.innerHTML;
                    btn.setAttribute('aria-expanded', 'false');
                }

                selectUi.querySelectorAll('.vp-custom-option').forEach(function(item) {
                    item.classList.remove('is-selected');
                });
                option.classList.add('is-selected');
                selectUi.classList.remove('is-open');
                setVendorProductValidationState(selectUi.dataset.selectId, !option.dataset.value);
                updateVendorProductPreview();
            });
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.vp-custom-select')) {
            closeAll();
        }
    });

    previewInputs.forEach(function(input) {
        if (input.dataset.vpPreviewBound === '1') {
            return;
        }

        input.dataset.vpPreviewBound = '1';
        input.addEventListener('input', updateVendorProductPreview);
        input.addEventListener('change', updateVendorProductPreview);
        input.addEventListener('input', function() {
            if (input.id) {
                var invalid = !input.value.trim() || !isVendorProductPatternValid(input.id);
                setVendorProductValidationState(input.id, invalid);
            }
        });
        input.addEventListener('change', function() {
            if (input.id) {
                var invalid = !input.value.trim() || !isVendorProductPatternValid(input.id);
                setVendorProductValidationState(input.id, invalid);
            }
        });
    });

    var form = document.getElementById('item-form');

    if (form && form.dataset.vpValidationBound !== '1') {
        form.dataset.vpValidationBound = '1';
        form.addEventListener('submit', function(e) {
            if (!validateVendorProductForm(true)) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return;
            }

        }, true);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        applyVendorProductResponsiveLayout();
        initVendorProductNativeDropdowns();
        updateVendorProductPreview();
    });
} else {
    applyVendorProductResponsiveLayout();
    initVendorProductNativeDropdowns();
    updateVendorProductPreview();
}

if (window.jQuery && window.jQuery.fn) {
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
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%',
            minimumResultsForSearch: 5,
            dropdownParent: $('body') // Ensure dropdown is attached to body
        });
    }

    // Function to copy Model Name to Model No.
    function copyModelNameToModelNo() {
        var modelName = $('#model_name').val() || '';
        syncVendorProductModelNo(modelName);
        return modelName;
    }

    // Update Preview
    function updatePreview() {
        var brand = $('#email2 option:selected').text();
        var product = $('#email option:selected').text();
        var model = $('#model_name').val();
        var modelNumber = $('#model_no').val() || model;
        var emptyPlaceholder = '\u2014';
        
        $('#preview-brand').text($('#email2').val() ? brand : emptyPlaceholder);
        $('#preview-product').text($('#email').val() ? product : emptyPlaceholder);
        $('#preview-model').text(model || emptyPlaceholder);
        $('#preview-modelno').text(modelNumber || emptyPlaceholder);
    }

    function setVendorProductFieldState(selector, isInvalid) {
        var $field = $(selector);
        var $group = $field.closest('.form-group');
        var $inputGroup = $field.closest('.input-group');
        var $customSelect = $('.vp-custom-select[data-select-id="' + $field.attr('id') + '"]');

        $field.toggleClass('is-invalid', isInvalid).attr('aria-invalid', isInvalid ? 'true' : 'false');
        $group.toggleClass('vp-has-error', isInvalid);
        $inputGroup.toggleClass('vp-is-invalid', isInvalid);
        $customSelect.toggleClass('is-invalid', isInvalid);
    }

    function isPatternValid(selector) {
        var $field = $(selector);
        var value = ($field.val() || '').trim();
        var pattern = $field.attr('pattern');

        if (!value || !pattern) {
            return true;
        }

        return new RegExp(pattern).test(value);
    }

    function refreshVendorProductCustomSelects() {
        $('.vp-custom-select').each(function() {
            var $selectUi = $(this);
            var $nativeSelect = $('#' + $selectUi.data('select-id'));
            var value = $nativeSelect.val();
            var $selectedOption = $nativeSelect.find('option:selected');
            var selectedText = $selectedOption.text();

            $selectUi.find('.vp-custom-select-btn').text(selectedText);
            $selectUi.find('.vp-custom-option').removeClass('is-selected').each(function() {
                if ($(this).data('value') === value) {
                    $(this).addClass('is-selected');
                }
            });
        });
    }

    $(document).off('click.vendorProductCustomSelect').on('click.vendorProductCustomSelect', '.vp-custom-select-btn', function(e) {
        e.preventDefault();
        var $selectUi = $(this).closest('.vp-custom-select');
        var isOpen = $selectUi.hasClass('is-open');

        $('.vp-custom-select').removeClass('is-open').find('.vp-custom-select-btn').attr('aria-expanded', 'false');

        if (!isOpen) {
            $selectUi.addClass('is-open');
            $(this).attr('aria-expanded', 'true');
        }
    });

    $(document).off('click.vendorProductCustomOption').on('click.vendorProductCustomOption', '.vp-custom-option', function(e) {
        e.preventDefault();
        var $option = $(this);
        var $selectUi = $option.closest('.vp-custom-select');
        var selectId = $selectUi.data('select-id');
        var value = $option.data('value');
        var text = $option.text();
        var $nativeSelect = $('#' + selectId);

        $nativeSelect.val(value).trigger('change');
        $selectUi.find('.vp-custom-select-btn').text(text).attr('aria-expanded', 'false');
        $selectUi.find('.vp-custom-option').removeClass('is-selected');
        $option.addClass('is-selected');
        $selectUi.removeClass('is-open');
        setVendorProductFieldState('#' + selectId, !value);
    });

    $(document).off('click.vendorProductCloseCustomSelect').on('click.vendorProductCloseCustomSelect', function(e) {
        if (!$(e.target).closest('.vp-custom-select').length) {
            $('.vp-custom-select').removeClass('is-open').find('.vp-custom-select-btn').attr('aria-expanded', 'false');
        }
    });

    // Form validation
    function validateForm() {
        var isValid = true;
        var errorMessages = [];
        
        // Brand validation
        if (!$('#email2').val()) {
            setVendorProductFieldState('#email2', true);
            errorMessages.push('Brand');
            isValid = false;
        } else {
            setVendorProductFieldState('#email2', false);
        }
        
        // Product validation
        if (!$('#email').val()) {
            setVendorProductFieldState('#email', true);
            errorMessages.push('Product');
            isValid = false;
        } else {
            setVendorProductFieldState('#email', false);
        }
        
        // Model Name validation
        if (!$('#model_name').val().trim()) {
            setVendorProductFieldState('#model_name', true);
            errorMessages.push('Model Name');
            isValid = false;
        } else if (!isPatternValid('#model_name')) {
            setVendorProductFieldState('#model_name', true);
            errorMessages.push('Model Name format');
            isValid = false;
        } else {
            setVendorProductFieldState('#model_name', false);
        }
        
        // Version validation
        if (!$('#version').val().trim()) {
            setVendorProductFieldState('#version', true);
            errorMessages.push('Version');
            isValid = false;
        } else if (!isPatternValid('#version')) {
            setVendorProductFieldState('#version', true);
            errorMessages.push('Version format');
            isValid = false;
        } else {
            setVendorProductFieldState('#version', false);
        }
        
        // Batch validation
        if (!$('#batch').val().trim()) {
            setVendorProductFieldState('#batch', true);
            errorMessages.push('Batch/Lot No.');
            isValid = false;
        } else if (!isPatternValid('#batch')) {
            setVendorProductFieldState('#batch', true);
            errorMessages.push('Batch/Lot No. format');
            isValid = false;
        } else {
            setVendorProductFieldState('#batch', false);
        }
        
        if (!isValid) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please check required fields',
                    html: '<strong>Fix these fields:</strong><br>' + errorMessages.join('<br>'),
                    confirmButtonColor: '#161716'
                });
            }
        }
        
        return isValid;
    }

    // Input event handlers
    $(document).off('input.vendorProductModelName').on('input.vendorProductModelName', '#model_name', function() {
        var modelName = copyModelNameToModelNo();
        updatePreview();
        
        if (modelName) {
            setVendorProductFieldState('#model_name', !isPatternValid('#model_name'));
        }
    });
    
    $('#version, #batch').on('input', function() {
        updatePreview();
        
        if ($(this).val().trim()) {
            setVendorProductFieldState('#' + this.id, !isPatternValid('#' + this.id));
        }
    });

    $('#email2, #email').on('change', function() {
        updatePreview();
        setVendorProductFieldState('#' + this.id, !$(this).val());
    });

    $('#model_name, #version, #batch').on('blur', function() {
        if (this.id === 'model_name') {
            copyModelNameToModelNo();
            updatePreview();
        }

        if (!$(this).val().trim()) {
            setVendorProductFieldState('#' + this.id, true);
        } else {
            setVendorProductFieldState('#' + this.id, !isPatternValid('#' + this.id));
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
                refreshVendorProductCustomSelects();
                updatePreview();
                $('.is-invalid').removeClass('is-invalid');
                $('.vp-has-error').removeClass('vp-has-error');
                $('.vp-is-invalid').removeClass('vp-is-invalid');
                
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

    // The native logo-aware FormData handler below owns Save. No duplicate submit listener.

    // Initialize preview and model no
    updatePreview();
    copyModelNameToModelNo();

    // Do not attach Dropzone or appForm to this form; they can consume/omit model_logo_file.

});
}
</script>

<script type="text/javascript">
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

<!-- SweetAlert2 for beautiful alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap CSS & JS (if not already loaded) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome 4, matching the existing fa fa-* icon classes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
