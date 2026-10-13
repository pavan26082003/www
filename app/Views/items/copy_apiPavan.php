<?php
/* -----------------------
   Model Dropdown
------------------------ */
$model_options = ['' => '&mdash; Select Model &mdash;'];

if (!empty($models)) {
    foreach ($models as $m) {
        if (!empty($m['modelname'])) {
            $model_options[$m['modelname']] = $m['modelname'];
        }
    }
}

/* -----------------------
     Brand Dropdown   
------------------------ */
$brand_options = ['' => '&mdash; Select Brand &mdash;'];
$seen_brands = [];

if (!empty($brand_dropdown)) {
    foreach ($brand_dropdown as $value) {
        $brand = trim(is_object($value) ? ($value->name ?? '') : ($value['name'] ?? ''));
        $key = strtolower($brand);

        if ($brand === '' || isset($seen_brands[$key])) {
            continue;
        }

        $seen_brands[$key] = true;
        $brand_options[$brand] = $brand;
    }
}

/* -----------------------
   Product Dropdown
------------------------ */
$category_options = ['' => '&mdash; Select Product &mdash;'];
$product_options = ['None' => 'None'];
$seen_categories = [];
$seen_products = [];

if (!empty($categories_dropdown)) {
    foreach ($categories_dropdown as $value) {
        $category = trim(is_object($value) ? ($value->name ?? '') : ($value['name'] ?? ''));
        $key = strtolower($category);

        if ($category === '' || isset($seen_categories[$key])) {
            continue;
        }

        $seen_categories[$key] = true;
        $category_options[$category] = $category;
    }
}

if (!empty($Products)) {
    foreach ($Products as $p) {
        $product = trim($p['product_type'] ?? '');
        $key = strtolower($product);

        if ($product === '' || isset($seen_products[$key])) {
            continue;
        }

        $seen_products[$key] = true;
        $product_options[$product] = $product;

        if (!isset($seen_categories[$key])) {
            $seen_categories[$key] = true;
            $category_options[$product] = $product;
        }
    }
}

/* -----------------------
   Page Name Dropdown
------------------------ */
$page_options = ['' => '&mdash; Select Page &mdash;'];
$seen_pages = [];

if (!empty($page_name)) {
    foreach ($page_name as $pg) {
        $page = trim($pg['page_names'] ?? '');
        $key = strtolower($page);

        if ($page === '' || $page === '1' || isset($seen_pages[$key])) {
            continue;
        }

        $seen_pages[$key] = true;
        $page_options[$page] = $page;
    }
}

if (!function_exists('render_copy_api_options')) {
    function render_copy_api_options($options) {
        foreach ($options as $value => $label) {
            $safe_label = $value === '' ? $label : htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
            ?>
            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $safe_label; ?></option>
            <?php
        }
    }
}

if (!function_exists('render_copy_api_custom_options')) {
    function render_copy_api_custom_options($options) {
        $is_first = true;

        foreach ($options as $value => $label) {
            $classes = 'ca-custom-option';
            $safe_label = $value === '' ? $label : htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

            if ($is_first) {
                $classes .= ' is-placeholder is-selected';
            }
            ?>
            <button type="button" class="<?php echo $classes; ?>" data-value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" role="option"><?php echo $safe_label; ?></button>
            <?php
            $is_first = false;
        }
    }
}
?>

<?php
echo form_open_multipart(get_uri("items/saveconfig"), [
    "id" => "copy-api-form",
    "class" => "general-form",
    "role" => "form",
    "novalidate" => "novalidate"
]);
?>
<div class="copy-api-modal-ui">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <div class="ca-modal-hero">
                <div class="ca-modal-title">
                    <span class="ca-kicker">API configuration</span>
                    <div class="ca-title-row">
                        <h4><i class="fa fa-copy"></i> Copy API</h4>
                        <button type="button" class="ca-hero-back-btn" data-bs-dismiss="modal" data-dismiss="modal" onclick="copyApiGoBack(event)" aria-label="Back">
                            <i class="fa fa-arrow-left"></i>
                            <span>Back</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-4 ca-picker-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email2" class="control-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-control ca-native-select" id="email2" name="brand" required>
                            <?php render_copy_api_options($brand_options); ?>
                        </select>
                        <div class="ca-custom-select" data-select-id="email2">
                            <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Brand &mdash;</button>
                            <div class="ca-custom-options" role="listbox">
                                <?php render_copy_api_custom_options($brand_options); ?>
                            </div>
                        </div>
                        <div class="invalid-feedback">Please select a brand.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="control-label">Product <span class="text-danger">*</span></label>
                        <select class="form-control ca-native-select" id="email" name="category" required>
                            <?php render_copy_api_options($category_options); ?>
                        </select>
                        <div class="ca-custom-select" data-select-id="email">
                            <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Product &mdash;</button>
                            <div class="ca-custom-options" role="listbox">
                                <?php render_copy_api_custom_options($category_options); ?>
                            </div>
                        </div>
                        <div class="invalid-feedback">Please select a product.</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 ca-section-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-random"></i> Model Mapping</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_model" class="control-label">From Model Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fa fa-database"></i></span>
                                    <select name="from_model" id="from_model" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($model_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="from_model">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Model &mdash;</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($model_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback">Please select source model.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_model" class="control-label">To Model Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fa fa-arrow-right"></i></span>
                                    <select name="to_model" id="to_model" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($model_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="to_model">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Model &mdash;</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($model_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback">Please select destination model.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 ca-section-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-sliders"></i> Parameter Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="existing_param" class="control-label">Existing Parameter <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-text-field">
                                    <span class="ca-field-icon"><i class="fa fa-search"></i></span>
                                    <input type="text" name="existing_param" id="existing_param" class="form-control ca-control" placeholder="Enter existing parameter name" required>
                                </div>
                                <div class="invalid-feedback">Please enter existing parameter.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_param" class="control-label">New Parameter Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-text-field">
                                    <span class="ca-field-icon"><i class="fa fa-plus-circle"></i></span>
                                    <input type="text" name="new_param" id="new_param" class="form-control ca-control" placeholder="Enter new parameter name" required>
                                </div>
                                <div class="invalid-feedback">Please enter new parameter name.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_product" class="control-label">New Product Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fa fa-cube"></i></span>
                                    <select name="new_product" id="new_product" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($product_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="new_product">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">None</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($product_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback">Please select new product.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="page_name" class="control-label">Page Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fa fa-file-text-o"></i></span>
                                    <select name="page_name" id="page_name" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($page_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="page_name">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false">&mdash; Select Page &mdash;</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($page_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback">Please select page.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 ca-section-card ca-options-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-check-square-o"></i> Advanced Copy Behavior</h5>
                </div>
                <div class="card-body">
                    <div class="ca-options-note">
                        <i class="fa fa-info-circle"></i>
                        <span>Default is safe: copy the parameter and its child fields without replacing existing destination data.</span>
                    </div>
                    <div class="ca-option-grid">
                        <label class="ca-check-option">
                            <input type="checkbox" name="overwrite_existing" value="1">
                            <span class="ca-check-ui"><i class="fa fa-refresh"></i></span>
                            <span>
                                <strong>Replace if already exists <em>Optional</em></strong>
                                <small>Turn this on only when the destination model already has the same parameter and you want to update it.</small>
                            </span>
                        </label>
                        <label class="ca-check-option">
                            <input type="checkbox" name="include_subparams" value="1" checked>
                            <span class="ca-check-ui"><i class="fa fa-sitemap"></i></span>
                            <span>
                                <strong>Copy child fields <em>Recommended</em></strong>
                                <small>Keeps sub-parameters and nested values with the copied parameter.</small>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3 ca-section-card ca-preview-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-eye"></i> Preview</h5>
                </div>
                <div class="card-body">
                    <div class="ca-preview-grid">
                        <div class="ca-preview-item"><strong>Brand</strong><span id="ca-preview-brand">&mdash;</span></div>
                        <div class="ca-preview-item"><strong>Product</strong><span id="ca-preview-product">&mdash;</span></div>
                        <div class="ca-preview-item"><strong>From</strong><span id="ca-preview-from">&mdash;</span></div>
                        <div class="ca-preview-item"><strong>To</strong><span id="ca-preview-to">&mdash;</span></div>
                        <div class="ca-preview-item"><strong>Parameter</strong><span id="ca-preview-param">&mdash;</span></div>
                        <div class="ca-preview-item"><strong>New Name</strong><span id="ca-preview-new-param">&mdash;</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary ca-submit-btn" id="submitBtn">
            <i class="fa fa-copy"></i> Copy Configuration
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<style>
.copy-api-modal-ui {
    --ca-black: #161716;
    --ca-ink: #283028;
    --ca-olive: #778873;
    --ca-sage: #A1BC98;
    --ca-mist: #D2DCB6;
    --ca-cream: #F1F3E0;
    --ca-white: #ffffff;
    --ca-gold: #d7a441;
    --ca-coral: #ce6f55;
    --ca-blue: #536f88;
    --ca-danger: #c0392b;
    --ca-muted: #667064;
    --ca-border: rgba(22, 23, 22, .12);
    --ca-shadow: 0 24px 70px rgba(22, 23, 22, .16);
    width: min(calc(100% - 32px), 1120px);
    margin: 28px auto;
    background: linear-gradient(135deg, rgba(241, 243, 224, .96), rgba(210, 220, 182, .82));
    border-radius: 8px;
    color: var(--ca-ink);
    overflow: hidden;
}

.copy-api-modal-ui,
.copy-api-modal-ui * {
    box-sizing: border-box;
}

.copy-api-modal-ui .fa {
    display: inline-block;
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    line-height: 1;
}

.copy-api-modal-ui .modal-body {
    padding: clamp(12px, 1.6vw, 20px);
    background:
        radial-gradient(circle at top left, rgba(161, 188, 152, .36), transparent 31%),
        linear-gradient(180deg, #ffffff 0%, var(--ca-cream) 100%);
}

.copy-api-modal-ui .container-fluid {
    width: 100%;
    max-width: 1060px;
    margin: 0 auto;
    padding-left: clamp(6px, 1vw, 12px);
    padding-right: clamp(6px, 1vw, 12px);
}

.ca-modal-hero {
    display: block;
    margin-bottom: 14px;
    padding: clamp(14px, 1.8vw, 18px) clamp(14px, 2vw, 22px);
    border: 1px solid rgba(210, 220, 182, .42);
    border-radius: 8px;
    background:
        linear-gradient(135deg, rgba(22, 23, 22, .96), rgba(119, 136, 115, .92)),
        var(--ca-black);
    box-shadow: var(--ca-shadow);
    color: var(--ca-white);
}

.ca-hero-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 34px;
    padding: 8px 13px;
    border: 1px solid rgba(210, 220, 182, .58);
    border-radius: 8px;
    background: rgba(255, 255, 255, .08);
    color: var(--ca-white);
    font-size: 13px;
    font-weight: 800;
    transition: transform .16s ease, background .16s ease, color .16s ease;
}

.ca-hero-back-btn:hover,
.ca-hero-back-btn:focus {
    transform: translateY(-1px);
    background: var(--ca-mist);
    color: var(--ca-black);
    outline: none;
}

.ca-kicker {
    display: inline-flex;
    width: fit-content;
    margin-bottom: 8px;
    padding: 5px 9px;
    border: 1px solid rgba(241, 243, 224, .3);
    border-radius: 8px;
    background: rgba(241, 243, 224, .12);
    color: var(--ca-mist);
    font-size: 12px;
    font-weight: 850;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.ca-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.ca-modal-title h4 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: var(--ca-white);
    font-size: clamp(22px, 2.2vw, 30px);
    font-weight: 900;
    letter-spacing: 0;
}

.ca-modal-title h4 .fa {
    color: var(--ca-mist);
}

.ca-picker-row {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    align-items: start;
    margin: 0 0 14px !important;
    position: relative;
    z-index: 80;
}

.ca-picker-row > [class*="col-"] {
    width: auto !important;
    max-width: none !important;
    flex: none !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.copy-api-modal-ui .form-group {
    margin-bottom: 12px;
}

.copy-api-modal-ui label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    color: var(--ca-black);
    font-size: 13px;
    font-weight: 850;
}

.copy-api-modal-ui .text-danger {
    color: var(--ca-danger) !important;
}

.ca-native-select {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

.ca-custom-select {
    position: relative;
    z-index: 20;
}

.ca-custom-select.is-open {
    z-index: 250;
}

.ca-custom-select-btn,
.ca-control {
    width: 100%;
    min-height: 42px;
    border: 1px solid rgba(119, 136, 115, .32);
    border-radius: 8px;
    background: var(--ca-white);
    color: var(--ca-black);
    font-size: 14px;
    font-weight: 650;
    line-height: 1.25;
    transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
}

.ca-custom-select-btn {
    position: relative;
    padding: 10px 42px 10px 13px;
    overflow: hidden;
    text-align: left;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.ca-custom-select-btn::after {
    content: "\f107";
    position: absolute;
    top: 50%;
    right: 15px;
    font-family: FontAwesome;
    color: var(--ca-olive);
    transform: translateY(-50%);
    pointer-events: none;
}

.ca-field-shell {
    position: relative;
}

.ca-field-icon {
    position: absolute;
    top: 50%;
    left: 14px;
    z-index: 2;
    color: var(--ca-olive);
    transform: translateY(-50%);
    pointer-events: none;
}

.ca-control {
    height: auto;
    padding: 10px 40px 10px 40px;
    appearance: none;
}

.ca-control option,
.ca-native-select option {
    background: #fffffb;
    color: var(--ca-black);
    font-size: 14px;
    font-weight: 650;
    padding: 10px 12px;
}

input.ca-control {
    padding-right: 14px;
}

.ca-icon-select .ca-custom-select-btn {
    padding-left: 40px;
}

.ca-custom-select-btn:hover,
.ca-control:hover,
.ca-custom-select.is-open .ca-custom-select-btn {
    border-color: var(--ca-sage);
    background: #fbfcf4;
    box-shadow: 0 10px 24px rgba(119, 136, 115, .12);
}

.ca-custom-select-btn:focus,
.ca-control:focus {
    border-color: var(--ca-olive);
    box-shadow: 0 0 0 4px rgba(161, 188, 152, .24);
    outline: none;
}

.ca-custom-options {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    display: none;
    max-height: 220px;
    overflow: auto;
    padding: 8px;
    border: 1px solid rgba(119, 136, 115, .28);
    border-radius: 8px;
    background: #fffffb;
    box-shadow: 0 18px 42px rgba(22, 23, 22, .16);
    z-index: 50;
}

.ca-custom-options::-webkit-scrollbar {
    width: 8px;
}

.ca-custom-options::-webkit-scrollbar-track {
    background: var(--ca-cream);
    border-radius: 999px;
}

.ca-custom-options::-webkit-scrollbar-thumb {
    background: var(--ca-olive);
    border-radius: 999px;
}

.ca-custom-select.is-open .ca-custom-options {
    display: grid;
    gap: 4px;
}

.ca-custom-option {
    width: 100%;
    padding: 9px 11px;
    border: 0;
    border-radius: 7px;
    background: transparent;
    color: #20251f;
    font-size: 14px;
    font-weight: 650;
    line-height: 1.28;
    text-align: left;
    overflow-wrap: anywhere;
}

.ca-custom-option:hover,
.ca-custom-option.is-selected {
    background: var(--ca-cream);
    color: var(--ca-black);
}

.ca-custom-option.is-placeholder {
    color: var(--ca-muted);
}

.ca-section-card {
    position: relative;
    border: 1px solid var(--ca-border);
    border-radius: 8px;
    background: rgba(255, 255, 255, .9);
    box-shadow: 0 16px 38px rgba(22, 23, 22, .08);
    overflow: visible;
    margin-bottom: 14px !important;
}

.ca-section-card:focus-within {
    z-index: 120;
}

.ca-section-card .card-header {
    padding: 11px 15px;
    border-bottom: 1px solid rgba(119, 136, 115, .18);
    background: linear-gradient(135deg, rgba(241, 243, 224, .98), rgba(210, 220, 182, .72));
}

.ca-section-card .card-title {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--ca-black);
    font-size: 15px;
    font-weight: 900;
}

.ca-section-card .card-title .fa {
    color: var(--ca-olive);
}

.ca-section-card .card-body {
    padding: clamp(12px, 1.5vw, 16px);
}

.ca-section-card .row {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.ca-section-card .row > [class*="col-"] {
    width: auto !important;
    max-width: none !important;
    flex: none !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.ca-section-card .mt-3 {
    margin-top: 2px !important;
}

.ca-option-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.ca-options-note {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 10px;
    padding: 10px 12px;
    border: 1px solid rgba(119, 136, 115, .18);
    border-radius: 8px;
    background: rgba(241, 243, 224, .74);
    color: var(--ca-ink);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.35;
}

.ca-options-note .fa {
    flex: 0 0 auto;
    color: var(--ca-olive);
}

.ca-preview-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.ca-check-option {
    display: grid !important;
    grid-template-columns: 42px minmax(0, 1fr);
    gap: 12px !important;
    align-items: center !important;
    min-height: 62px;
    margin: 0 !important;
    padding: 11px;
    border: 1px solid rgba(119, 136, 115, .2);
    border-radius: 8px;
    background: linear-gradient(135deg, #ffffff, rgba(241, 243, 224, .72));
    cursor: pointer;
    transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
}

.ca-check-option:hover {
    border-color: rgba(119, 136, 115, .42);
    box-shadow: 0 10px 24px rgba(119, 136, 115, .12);
    transform: translateY(-1px);
}

.ca-check-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.ca-check-ui {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border: 1px solid rgba(119, 136, 115, .28);
    border-radius: 8px;
    background: var(--ca-white);
    color: var(--ca-olive);
}

.ca-check-option input:checked + .ca-check-ui {
    background: var(--ca-black);
    color: var(--ca-mist);
}

.ca-check-option strong,
.ca-check-option small {
    display: block;
}

.ca-check-option strong {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
    color: var(--ca-black);
    font-size: 13px;
}

.ca-check-option strong em {
    display: inline-flex;
    align-items: center;
    min-height: 20px;
    padding: 3px 7px;
    border-radius: 999px;
    background: rgba(119, 136, 115, .12);
    color: var(--ca-olive);
    font-size: 10px;
    font-style: normal;
    font-weight: 900;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.ca-check-option input:checked ~ span strong em {
    background: rgba(22, 23, 22, .1);
    color: var(--ca-black);
}

.ca-check-option small {
    margin-top: 3px;
    color: var(--ca-muted);
    font-size: 12px;
    line-height: 1.35;
}

.ca-preview-card {
    border-color: rgba(119, 136, 115, .38);
}

.ca-preview-item {
    min-width: 0;
    padding: 13px 14px;
    border: 1px solid rgba(119, 136, 115, .18);
    border-radius: 8px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(241, 243, 224, .86));
}

.ca-preview-item strong,
.ca-preview-item span {
    display: block;
}

.ca-preview-item strong {
    color: var(--ca-muted);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.ca-preview-item span {
    margin-top: 5px;
    overflow: hidden;
    color: var(--ca-black);
    font-size: 14px;
    font-weight: 800;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.copy-api-modal-ui .is-invalid,
.copy-api-modal-ui .ca-custom-select.is-invalid .ca-custom-select-btn {
    border-color: var(--ca-danger) !important;
    box-shadow: 0 0 0 4px rgba(192, 57, 43, .12);
}

.copy-api-modal-ui .invalid-feedback {
    display: none;
    margin-top: 6px;
    color: var(--ca-danger);
    font-size: 12px;
    font-weight: 700;
}

.copy-api-modal-ui .ca-has-error .invalid-feedback {
    display: block;
}

.copy-api-modal-ui .modal-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
    padding: 12px clamp(18px, 2.4vw, 28px);
    border-top: 1px solid rgba(119, 136, 115, .22);
    background: linear-gradient(135deg, #ffffff, var(--ca-cream));
}

.copy-api-modal-ui .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 850;
    transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
}

.copy-api-modal-ui .btn:hover {
    transform: translateY(-1px);
}

.copy-api-modal-ui .btn-primary {
    border: 1px solid var(--ca-black);
    background: var(--ca-black);
    color: var(--ca-cream);
    box-shadow: 0 12px 28px rgba(22, 23, 22, .18);
}

.copy-api-modal-ui .btn-primary:hover,
.copy-api-modal-ui .btn-primary:focus {
    background: var(--ca-olive);
    border-color: var(--ca-olive);
    color: var(--ca-white);
}

.copy-api-modal-ui .btn-secondary {
    border: 1px solid rgba(119, 136, 115, .34);
    background: var(--ca-white);
    color: var(--ca-black);
}

.copy-api-modal-ui .btn-secondary:hover,
.copy-api-modal-ui .btn-secondary:focus {
    background: var(--ca-mist);
}

.ca-toast {
    position: fixed;
    top: 18px;
    right: 18px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: min(360px, calc(100vw - 36px));
    padding: 13px 15px;
    border: 1px solid rgba(119, 136, 115, .22);
    border-radius: 8px;
    background: var(--ca-white);
    color: var(--ca-black);
    box-shadow: 0 18px 46px rgba(22, 23, 22, .18);
    transform: translateX(18px);
    opacity: 0;
    transition: opacity .18s ease, transform .18s ease;
}

.ca-toast.is-visible {
    transform: translateX(0);
    opacity: 1;
}

.ca-toast .fa {
    color: var(--ca-olive);
}

@media (max-width: 1180px) {
    .copy-api-modal-ui {
        width: min(calc(100% - 24px), 1040px);
        margin: 20px auto;
    }

    .copy-api-modal-ui .container-fluid {
        max-width: 100%;
    }
}

@media (max-width: 991px) {
    .copy-api-modal-ui {
        width: min(calc(100% - 18px), 900px);
        margin: 14px auto;
    }

    .copy-api-modal-ui .modal-body {
        padding: 12px;
    }

    .ca-modal-hero {
        margin-bottom: 12px;
    }

    .ca-preview-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 860px) {
    .ca-section-card .row,
    .ca-option-grid {
        grid-template-columns: 1fr;
    }

    .ca-section-card .card-body {
        padding: 12px;
    }

    .ca-options-note {
        align-items: flex-start;
    }

    .ca-check-option {
        min-height: 58px;
    }
}

@media (max-width: 720px) {
    .ca-picker-row,
    .ca-preview-grid {
        grid-template-columns: 1fr;
    }

    .ca-title-row {
        align-items: flex-start;
    }

    .ca-custom-options {
        max-height: 190px;
    }
}

@media (max-width: 640px) {
    .copy-api-modal-ui {
        width: min(calc(100% - 12px), 100%);
        margin: 12px auto;
    }

    .copy-api-modal-ui .modal-body {
        padding: 10px;
    }

    .ca-modal-hero {
        padding: 13px;
    }

    .ca-modal-title h4 {
        font-size: 22px;
    }

    .ca-title-row {
        flex-direction: column;
        gap: 10px;
    }

    .ca-hero-back-btn {
        width: 100%;
        justify-content: center;
    }

    .ca-section-card .card-header {
        padding: 10px 12px;
    }

    .ca-custom-select-btn,
    .ca-control {
        min-height: 44px;
        font-size: 14px;
    }

    .copy-api-modal-ui .modal-footer {
        flex-direction: column-reverse;
        align-items: stretch;
    }

    .copy-api-modal-ui .btn {
        width: 100%;
    }
}

@media (max-width: 420px) {
    .copy-api-modal-ui {
        width: 100%;
        margin: 0 auto;
        border-radius: 0;
    }

    .copy-api-modal-ui .modal-body {
        padding: 8px;
    }

    .ca-modal-hero,
    .ca-section-card,
    .ca-preview-item,
    .ca-check-option {
        border-radius: 7px;
    }

    .ca-kicker {
        max-width: 100%;
        white-space: normal;
    }

    .ca-modal-title h4 {
        font-size: 20px;
    }

    .ca-preview-item span,
    .ca-custom-select-btn {
        white-space: normal;
    }
}
</style>

<script>
(function() {
    function getField(id) {
        return document.getElementById(id);
    }

    function fieldLabel(field) {
        if (!field) {
            return '';
        }

        if (field.tagName === 'SELECT') {
            var selected = field.options[field.selectedIndex];
            return field.value && selected ? selected.text : '';
        }

        return field.value || '';
    }

    function setFieldState(field, isInvalid) {
        if (!field) {
            return;
        }

        var group = field.closest('.form-group');
        var customSelect = document.querySelector('.ca-custom-select[data-select-id="' + field.id + '"]');

        field.classList.toggle('is-invalid', isInvalid);

        if (group) {
            group.classList.toggle('ca-has-error', isInvalid);
        }

        if (customSelect) {
            customSelect.classList.toggle('is-invalid', isInvalid);
        }
    }

    function updatePreview() {
        var previewMap = {
            'ca-preview-brand': 'email2',
            'ca-preview-product': 'email',
            'ca-preview-from': 'from_model',
            'ca-preview-to': 'to_model',
            'ca-preview-param': 'existing_param',
            'ca-preview-new-param': 'new_param'
        };

        Object.keys(previewMap).forEach(function(previewId) {
            var preview = getField(previewId);
            var field = getField(previewMap[previewId]);

            if (preview) {
                preview.textContent = fieldLabel(field) || '\u2014';
            }
        });
    }

    function closeCustomSelects(except) {
        document.querySelectorAll('.ca-custom-select').forEach(function(selectUi) {
            if (selectUi !== except) {
                selectUi.classList.remove('is-open');
                var btn = selectUi.querySelector('.ca-custom-select-btn');

                if (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    function syncCustomButton(selectUi) {
        var nativeSelect = getField(selectUi.dataset.selectId);
        var btn = selectUi.querySelector('.ca-custom-select-btn');

        if (!nativeSelect || !btn) {
            return;
        }

        btn.innerHTML = nativeSelect.options[nativeSelect.selectedIndex] ? nativeSelect.options[nativeSelect.selectedIndex].innerHTML : '';

        selectUi.querySelectorAll('.ca-custom-option').forEach(function(option) {
            option.classList.toggle('is-selected', option.dataset.value === nativeSelect.value);
        });
    }

    function initCustomSelects() {
        document.querySelectorAll('.ca-custom-select').forEach(function(selectUi) {
            if (selectUi.dataset.caBound === '1') {
                return;
            }

            selectUi.dataset.caBound = '1';
            syncCustomButton(selectUi);

            var btn = selectUi.querySelector('.ca-custom-select-btn');
            var nativeSelect = getField(selectUi.dataset.selectId);

            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var willOpen = !selectUi.classList.contains('is-open');
                    closeCustomSelects(selectUi);
                    selectUi.classList.toggle('is-open', willOpen);
                    btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                });
            }

            selectUi.querySelectorAll('.ca-custom-option').forEach(function(option) {
                option.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (nativeSelect) {
                        nativeSelect.value = option.dataset.value || '';
                        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    syncCustomButton(selectUi);
                    selectUi.classList.remove('is-open');

                    if (btn) {
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ca-custom-select')) {
                closeCustomSelects();
            }
        });
    }

    function showCopyApiToast(message, type) {
        var existing = document.querySelector('.ca-toast');

        if (existing) {
            existing.remove();
        }

        var toast = document.createElement('div');
        toast.className = 'ca-toast';
        toast.innerHTML = '<i class="fa ' + (type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i><strong>' + message + '</strong>';
        document.body.appendChild(toast);

        window.setTimeout(function() {
            toast.classList.add('is-visible');
        }, 20);

        window.setTimeout(function() {
            toast.classList.remove('is-visible');
            window.setTimeout(function() {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 220);
        }, 2400);
    }

    function validateCopyApiForm() {
        var form = getField('copy-api-form');
        var isValid = true;

        if (!form) {
            return true;
        }

        form.querySelectorAll('[required]').forEach(function(field) {
            var invalid = !String(field.value || '').trim();
            setFieldState(field, invalid);

            if (invalid) {
                isValid = false;
            }
        });

        var fromModel = getField('from_model');
        var toModel = getField('to_model');

        if (fromModel && toModel && fromModel.value && toModel.value && fromModel.value === toModel.value) {
            setFieldState(fromModel, true);
            setFieldState(toModel, true);
            showCopyApiToast('Source and destination models cannot be the same.', 'error');
            return false;
        }

        if (!isValid) {
            showCopyApiToast('Please check all required fields.', 'error');
        }

        return isValid;
    }

    function initCopyApiForm() {
        var form = getField('copy-api-form');
        var submitBtn = getField('submitBtn');

        initCustomSelects();
        updatePreview();

        document.querySelectorAll('#copy-api-form input, #copy-api-form select').forEach(function(field) {
            field.addEventListener('input', function() {
                setFieldState(field, field.hasAttribute('required') && !String(field.value || '').trim());
                updatePreview();
            });
            field.addEventListener('change', function() {
                setFieldState(field, field.hasAttribute('required') && !String(field.value || '').trim());
                updatePreview();
            });
        });

        if (form && form.dataset.caSubmitBound !== '1') {
            form.dataset.caSubmitBound = '1';
            form.addEventListener('submit', function(e) {
                if (!validateCopyApiForm()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                }

                return true;
            }, true);
        }
    }

    window.copyApiGoBack = function(e) {
        if (e) {
            e.preventDefault();
        }

        var modal = document.querySelector('.copy-api-modal-ui') ? document.querySelector('.copy-api-modal-ui').closest('.modal') : null;

        if (modal) {
            if (window.bootstrap && bootstrap.Modal) {
                var modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                modalInstance.hide();
                return;
            }

            if (window.jQuery && jQuery.fn.modal) {
                jQuery(modal).modal('hide');
                return;
            }
        }

        if (window.history.length > 1) {
            window.history.back();
            return;
        }

        return false;
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCopyApiForm);
    } else {
        initCopyApiForm();
    }
})();
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
