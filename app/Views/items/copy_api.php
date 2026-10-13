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
                    <span class="ca-kicker"><i class="fas fa-cog"></i> API configuration</span>
                    <div class="ca-title-row">
                        <h4><i class="fas fa-code-branch"></i> Copy API</h4>
                        <button type="button" class="ca-hero-back-btn" data-bs-dismiss="modal" data-dismiss="modal" onclick="copyApiGoBack(event)" aria-label="Back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-4 ca-picker-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email2" class="control-label"><i class="fas fa-building"></i> Brand <span class="text-danger">*</span></label>
                        <select class="form-control ca-native-select" id="email2" name="brand" required>
                            <?php render_copy_api_options($brand_options); ?>
                        </select>
                        <div class="ca-custom-select" data-select-id="email2">
                            <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false"><i class="fas fa-building"></i> &mdash; Select Brand &mdash;</button>
                            <div class="ca-custom-options" role="listbox">
                                <?php render_copy_api_custom_options($brand_options); ?>
                            </div>
                        </div>
                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please select a brand.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="control-label"><i class="fas fa-tag"></i> Product <span class="text-danger">*</span></label>
                        <select class="form-control ca-native-select" id="email" name="category" required>
                            <?php render_copy_api_options($category_options); ?>
                        </select>
                        <div class="ca-custom-select" data-select-id="email">
                            <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false"><i class="fas fa-tag"></i> &mdash; Select Product &mdash;</button>
                            <div class="ca-custom-options" role="listbox">
                                <?php render_copy_api_custom_options($category_options); ?>
                            </div>
                        </div>
                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please select a product.</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 ca-section-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-exchange-alt"></i> Model Mapping</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_model" class="control-label"><i class="fas fa-arrow-right"></i> From Model Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fas fa-database"></i></span>
                                    <select name="from_model" id="from_model" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($model_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="from_model">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false"><i class="fas fa-database"></i> &mdash; Select Model &mdash;</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($model_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please select source model.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_model" class="control-label"><i class="fas fa-arrow-left"></i> To Model Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fas fa-arrow-right"></i></span>
                                    <select name="to_model" id="to_model" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($model_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="to_model">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false"><i class="fas fa-arrow-right"></i> &mdash; Select Model &mdash;</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($model_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please select destination model.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 ca-section-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-sliders-h"></i> Parameter Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="existing_param" class="control-label"><i class="fas fa-search"></i> Existing Parameter <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-text-field">
                                    <span class="ca-field-icon"><i class="fas fa-search"></i></span>
                                    <input type="text" name="existing_param" id="existing_param" class="form-control ca-control" placeholder="Enter existing parameter name" required>
                                </div>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please enter existing parameter.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_param" class="control-label"><i class="fas fa-plus-circle"></i> New Parameter Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-text-field">
                                    <span class="ca-field-icon"><i class="fas fa-plus-circle"></i></span>
                                    <input type="text" name="new_param" id="new_param" class="form-control ca-control" placeholder="Enter new parameter name" required>
                                </div>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please enter new parameter name.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_product" class="control-label"><i class="fas fa-cube"></i> New Product Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fas fa-cube"></i></span>
                                    <select name="new_product" id="new_product" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($product_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="new_product">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false"><i class="fas fa-cube"></i> None</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($product_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please select new product.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="page_name" class="control-label"><i class="fas fa-file-alt"></i> Page Name <span class="text-danger">*</span></label>
                                <div class="ca-field-shell ca-select-field">
                                    <span class="ca-field-icon"><i class="fas fa-file-alt"></i></span>
                                    <select name="page_name" id="page_name" class="form-control ca-native-select" required>
                                        <?php render_copy_api_options($page_options); ?>
                                    </select>
                                    <div class="ca-custom-select ca-icon-select" data-select-id="page_name">
                                        <button type="button" class="ca-custom-select-btn" aria-haspopup="listbox" aria-expanded="false"><i class="fas fa-file-alt"></i> &mdash; Select Page &mdash;</button>
                                        <div class="ca-custom-options" role="listbox">
                                            <?php render_copy_api_custom_options($page_options); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> Please select page.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 ca-section-card ca-options-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-check-double"></i> Advanced Copy Behavior</h5>
                </div>
                <div class="card-body">
                    <div class="ca-options-note">
                        <i class="fas fa-info-circle"></i>
                        <span>Default is safe: copy the parameter and its child fields without replacing existing destination data.</span>
                    </div>
                    <div class="ca-option-grid">
                        <label class="ca-check-option">
                            <input type="checkbox" name="overwrite_existing" value="1">
                            <span class="ca-check-ui"><i class="fas fa-sync-alt"></i></span>
                            <span class="ca-check-option-content">
                                <strong><i class="fas fa-overwrite"></i> Replace if already exists <em>Optional</em></strong>
                                <small>Turn this on only when the destination model already has the same parameter and you want to update it.</small>
                            </span>
                        </label>
                        <label class="ca-check-option">
                            <input type="checkbox" name="include_subparams" value="1" checked>
                            <span class="ca-check-ui"><i class="fas fa-sitemap"></i></span>
                            <span class="ca-check-option-content">
                                <strong><i class="fas fa-child"></i> Copy child fields <em>Recommended</em></strong>
                                <small>Keeps sub-parameters and nested values with the copied parameter.</small>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3 ca-section-card ca-preview-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-eye"></i> Preview</h5>
                </div>
                <div class="card-body">
                    <div class="ca-preview-grid">
                        <div class="ca-preview-item"><strong><i class="fas fa-building"></i> Brand</strong><span id="ca-preview-brand">&mdash;</span></div>
                        <div class="ca-preview-item"><strong><i class="fas fa-tag"></i> Product</strong><span id="ca-preview-product">&mdash;</span></div>
                        <div class="ca-preview-item"><strong><i class="fas fa-arrow-right"></i> From</strong><span id="ca-preview-from">&mdash;</span></div>
                        <div class="ca-preview-item"><strong><i class="fas fa-arrow-left"></i> To</strong><span id="ca-preview-to">&mdash;</span></div>
                        <div class="ca-preview-item"><strong><i class="fas fa-search"></i> Parameter</strong><span id="ca-preview-param">&mdash;</span></div>
                        <div class="ca-preview-item"><strong><i class="fas fa-plus-circle"></i> New Name</strong><span id="ca-preview-new-param">&mdash;</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary ca-submit-btn" id="submitBtn">
            <i class="fas fa-copy"></i> Copy Configuration
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<style>
/* ===== MODERN CSS WITH RICH STYLES ===== */

/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

.copy-api-modal-ui {
    --ca-primary: #1a1a2e;
    --ca-secondary: #16213e;
    --ca-accent: #0f3460;
    --ca-gold: #e94560;
    --ca-success: #2ecc71;
    --ca-info: #3498db;
    --ca-warning: #f39c12;
    --ca-danger: #e74c3c;
    --ca-light: #f8f9fa;
    --ca-dark: #2c3e50;
    --ca-gray: #95a5a6;
    --ca-shadow: 0 20px 60px rgba(0,0,0,0.15);
    --ca-radius: 12px;
    --ca-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* ===== NEW COLOR PALETTE ===== */
    --ca-peach: #ffb6a6;
    --ca-cream: #ffebd3;
    --ca-mint: #9bcec1;
    --ca-sky: #67a2c5;
    --ca-peach-dark: #e89a8a;
    --ca-cream-dark: #e8d4be;
    --ca-mint-dark: #7eb8ab;
    --ca-sky-dark: #4a8aaa;
    
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    width: min(calc(100% - 32px), 1120px);
    margin: 28px auto;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--ca-radius);
    color: var(--ca-dark);
    overflow: hidden;
    box-shadow: var(--ca-shadow);
}

.copy-api-modal-ui,
.copy-api-modal-ui * {
    box-sizing: border-box;
}

.copy-api-modal-ui .fa,
.copy-api-modal-ui .fas,
.copy-api-modal-ui .far {
    display: inline-block;
    font-family: 'Font Awesome 5 Free';
    font-style: normal;
    font-weight: 900;
    line-height: 1;
}

.copy-api-modal-ui .modal-body {
    padding: clamp(16px, 2vw, 28px);
    background: 
        radial-gradient(circle at top left, rgba(103, 162, 197, 0.06), transparent 50%),
        radial-gradient(circle at bottom right, rgba(255, 182, 166, 0.05), transparent 40%),
        linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
}

.copy-api-modal-ui .container-fluid {
    width: 100%;
    max-width: 1060px;
    margin: 0 auto;
    padding-left: clamp(8px, 1.2vw, 16px);
    padding-right: clamp(8px, 1.2vw, 16px);
}

/* ===== HERO SECTION ===== */
.ca-modal-hero {
    display: block;
    margin-bottom: 20px;
    padding: clamp(18px, 2vw, 24px) clamp(18px, 2.5vw, 28px);
    border: none;
    border-radius: var(--ca-radius);
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    box-shadow: 0 10px 40px rgba(26, 26, 46, 0.3);
    color: #ffffff;
    position: relative;
    overflow: hidden;
}

.ca-modal-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 60%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 182, 166, 0.12), transparent 70%);
    pointer-events: none;
}

.ca-modal-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 40%;
    height: 150%;
    background: radial-gradient(circle, rgba(103, 162, 197, 0.1), transparent 60%);
    pointer-events: none;
}

.ca-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    margin-bottom: 10px;
    padding: 6px 14px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    background: linear-gradient(135deg, rgba(255, 182, 166, 0.15), rgba(155, 206, 193, 0.1));
    color: #ffebd3;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
}

.ca-kicker i {
    color: #ffb6a6;
    font-size: 14px;
}

.ca-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    position: relative;
    z-index: 1;
}

.ca-modal-title h4 {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    color: #ffffff;
    font-size: clamp(24px, 2.4vw, 32px);
    font-weight: 800;
    letter-spacing: -0.02em;
}

.ca-modal-title h4 i {
    background: linear-gradient(135deg, #ffb6a6, #67a2c5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: clamp(20px, 2vw, 28px);
}

.ca-hero-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-height: 38px;
    padding: 8px 18px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    transition: var(--ca-transition);
    backdrop-filter: blur(10px);
}

.ca-hero-back-btn:hover,
.ca-hero-back-btn:focus {
    transform: translateY(-2px);
    background: linear-gradient(135deg, rgba(255, 182, 166, 0.2), rgba(155, 206, 193, 0.15));
    color: #ffffff;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    outline: none;
    border-color: rgba(255, 182, 166, 0.3);
}

.ca-hero-back-btn i {
    font-size: 14px;
    transition: transform 0.3s ease;
}

.ca-hero-back-btn:hover i {
    transform: translateX(-3px);
}

/* ===== DROPDOWN ROW ===== */
.ca-picker-row {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    align-items: start;
    margin: 0 0 16px !important;
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

/* ===== FORM GROUP ===== */
.copy-api-modal-ui .form-group {
    margin-bottom: 14px;
}

.copy-api-modal-ui label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: var(--ca-dark);
    font-size: 14px;
    font-weight: 700;
    letter-spacing: -0.01em;
}

.copy-api-modal-ui label i {
    color: #67a2c5;
    font-size: 14px;
    width: 18px;
    transition: color 0.3s ease;
}

.copy-api-modal-ui label:hover i {
    color: #ffb6a6;
}

.copy-api-modal-ui .text-danger {
    color: #e74c3c !important;
    font-weight: 700;
}

/* ===== CUSTOM SELECT ===== */
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

.ca-custom-select-btn {
    width: 100%;
    min-height: 48px;
    padding: 12px 44px 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: var(--ca-radius);
    background: #ffffff;
    color: var(--ca-dark);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.4;
    text-align: left;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    transition: var(--ca-transition);
    cursor: pointer;
}

.ca-custom-select-btn i {
    margin-right: 10px;
    color: #67a2c5;
    font-size: 14px;
    transition: color 0.3s ease;
}

.ca-custom-select-btn:hover i {
    color: #ffb6a6;
}

.ca-custom-select-btn::after {
    content: '\f078';
    position: absolute;
    top: 50%;
    right: 16px;
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    color: var(--ca-gray);
    transform: translateY(-50%);
    pointer-events: none;
    transition: transform 0.3s ease;
}

.ca-custom-select.is-open .ca-custom-select-btn::after {
    transform: translateY(-50%) rotate(180deg);
}

.ca-custom-select-btn:hover {
    border-color: #67a2c5;
    background: linear-gradient(135deg, #ffffff, rgba(255, 235, 211, 0.3));
    box-shadow: 0 4px 15px rgba(103, 162, 197, 0.1);
}

.ca-custom-select.is-open .ca-custom-select-btn {
    border-color: #67a2c5;
    box-shadow: 0 4px 20px rgba(103, 162, 197, 0.15);
}

/* ===== FIELD SHELL ===== */
.ca-field-shell {
    position: relative;
}

.ca-field-icon {
    position: absolute;
    top: 50%;
    left: 16px;
    z-index: 2;
    color: #67a2c5;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 16px;
    transition: color 0.3s ease;
}

.ca-field-shell:hover .ca-field-icon {
    color: #ffb6a6;
}

.ca-field-shell .ca-custom-select-btn {
    padding-left: 46px;
}

/* ===== INPUT FIELDS ===== */
.ca-control {
    width: 100%;
    min-height: 48px;
    padding: 12px 16px 12px 46px;
    border: 2px solid #e2e8f0;
    border-radius: var(--ca-radius);
    background: #ffffff;
    color: var(--ca-dark);
    font-size: 14px;
    font-weight: 500;
    transition: var(--ca-transition);
}

.ca-control:focus {
    border-color: #67a2c5;
    box-shadow: 0 0 0 4px rgba(103, 162, 197, 0.1);
    outline: none;
}

.ca-control:hover {
    border-color: #9bcec1;
}

.ca-control::placeholder {
    color: #a0aec0;
    font-weight: 400;
}

/* ===== CUSTOM OPTIONS DROPDOWN ===== */
.ca-custom-options {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    display: none;
    max-height: 240px;
    overflow: auto;
    padding: 8px;
    border: 2px solid #e2e8f0;
    border-radius: var(--ca-radius);
    background: #ffffff;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    z-index: 50;
}

.ca-custom-options::-webkit-scrollbar {
    width: 6px;
}

.ca-custom-options::-webkit-scrollbar-track {
    background: #ffebd3;
    border-radius: 10px;
}

.ca-custom-options::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #ffb6a6, #67a2c5);
    border-radius: 10px;
}

.ca-custom-select.is-open .ca-custom-options {
    display: grid;
    gap: 4px;
}

.ca-custom-option {
    width: 100%;
    padding: 10px 14px;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: var(--ca-dark);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.4;
    text-align: left;
    overflow-wrap: anywhere;
    transition: var(--ca-transition);
    cursor: pointer;
}

.ca-custom-option:hover {
    background: linear-gradient(135deg, rgba(155, 206, 193, 0.08), rgba(255, 182, 166, 0.05));
    color: var(--ca-dark);
}

.ca-custom-option.is-selected {
    background: linear-gradient(135deg, rgba(103, 162, 197, 0.12), rgba(155, 206, 193, 0.08));
    color: #0f3460;
    font-weight: 600;
    border-left: 3px solid #67a2c5;
}

.ca-custom-option.is-placeholder {
    color: #a0aec0;
}

/* ===== CARDS ===== */
.ca-section-card {
    position: relative;
    border: none;
    border-radius: var(--ca-radius);
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    overflow: visible;
    margin-bottom: 16px !important;
    transition: var(--ca-transition);
}

.ca-section-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.ca-section-card:focus-within {
    z-index: 120;
}

.ca-section-card .card-header {
    padding: 14px 20px;
    border-bottom: 2px solid rgba(103, 162, 197, 0.08);
    background: linear-gradient(135deg, rgba(155, 206, 193, 0.05), rgba(255, 235, 211, 0.1));
    border-radius: var(--ca-radius) var(--ca-radius) 0 0;
}

.ca-section-card .card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--ca-dark);
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}

.ca-section-card .card-title i {
    color: #67a2c5;
    font-size: 18px;
    transition: color 0.3s ease;
}

.ca-section-card:hover .card-title i {
    color: #ffb6a6;
}

.ca-section-card .card-body {
    padding: clamp(16px, 1.8vw, 24px);
}

.ca-section-card .row {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
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
    margin-top: 4px !important;
}

/* ===== MODERN CHECKBOX CARDS ===== */
.ca-option-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.ca-check-option {
    display: grid !important;
    grid-template-columns: 56px minmax(0, 1fr);
    gap: 16px !important;
    align-items: center !important;
    min-height: 80px;
    margin: 0 !important;
    padding: 18px 22px;
    border: 2px solid #e8ecf0;
    border-radius: 16px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* ===== CARD HOVER EFFECT ===== */
.ca-check-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(155, 206, 193, 0.05), rgba(255, 182, 166, 0.03));
    opacity: 0;
    transition: opacity 0.4s ease;
    border-radius: 16px;
    pointer-events: none;
}

.ca-check-option:hover::before {
    opacity: 1;
}

.ca-check-option:hover {
    border-color: #67a2c5;
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(103, 162, 197, 0.12);
}

/* ===== CHECKBOX UI ===== */
.ca-check-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.ca-check-ui {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border: 2px solid #e8ecf0;
    border-radius: 14px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    color: #a0aec0;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 24px;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

/* ===== CHECKBOX CHECKED STATE ===== */
.ca-check-option input:checked + .ca-check-ui {
    background: linear-gradient(135deg, #1a1a2e, #0f3460);
    color: #ffffff;
    border-color: #0f3460;
    box-shadow: 0 4px 20px rgba(15, 52, 96, 0.25);
    transform: scale(1.05);
}

.ca-check-option input:checked + .ca-check-ui i {
    animation: checkPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes checkPop {
    0% { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

/* ===== CHECKBOX CONTENT ===== */
.ca-check-option-content {
    position: relative;
    z-index: 1;
}

.ca-check-option-content strong {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    color: var(--ca-dark);
    font-size: 14px;
    font-weight: 700;
    transition: color 0.3s ease;
}

.ca-check-option-content strong i {
    color: #67a2c5;
    font-size: 14px;
    transition: color 0.3s ease;
}

.ca-check-option-content strong em {
    display: inline-flex;
    align-items: center;
    min-height: 22px;
    padding: 2px 12px;
    border-radius: 50px;
    background: linear-gradient(135deg, rgba(255, 182, 166, 0.12), rgba(155, 206, 193, 0.08));
    color: #67a2c5;
    font-size: 10px;
    font-style: normal;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.ca-check-option-content small {
    display: block;
    margin-top: 4px;
    color: var(--ca-gray);
    font-size: 12px;
    line-height: 1.4;
    transition: color 0.3s ease;
}

/* ===== CHECKED STATE CONTENT ===== */
.ca-check-option input:checked ~ .ca-check-option-content strong {
    color: #0f3460;
}

.ca-check-option input:checked ~ .ca-check-option-content strong i {
    color: #ffb6a6;
}

.ca-check-option input:checked ~ .ca-check-option-content strong em {
    background: linear-gradient(135deg, rgba(255, 182, 166, 0.2), rgba(103, 162, 197, 0.1));
    color: #ffb6a6;
}

.ca-check-option input:checked ~ .ca-check-option-content small {
    color: #0f3460;
}

/* ===== SHIMMER EFFECT ON HOVER ===== */
.ca-check-option::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 30% 50%, rgba(103, 162, 197, 0.04), transparent 60%);
    opacity: 0;
    transition: opacity 0.6s ease;
    pointer-events: none;
    border-radius: 50%;
}

.ca-check-option:hover::after {
    opacity: 1;
}

/* ===== OPTIONS NOTE ===== */
.ca-options-note {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
    padding: 14px 18px;
    border-left: 4px solid #67a2c5;
    border-radius: var(--ca-radius);
    background: linear-gradient(135deg, rgba(155, 206, 193, 0.05), rgba(255, 235, 211, 0.08));
    color: var(--ca-dark);
    font-size: 13px;
    font-weight: 500;
    line-height: 1.5;
}

.ca-options-note i {
    flex: 0 0 auto;
    color: #67a2c5;
    font-size: 18px;
    margin-top: 1px;
}

/* ===== PREVIEW CARD ===== */
.ca-preview-card {
    border: 2px solid rgba(103, 162, 197, 0.1);
}

.ca-preview-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.ca-preview-item {
    min-width: 0;
    padding: 14px 16px;
    border: 1px solid #e2e8f0;
    border-radius: var(--ca-radius);
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    transition: var(--ca-transition);
}

.ca-preview-item:hover {
    border-color: #9bcec1;
    box-shadow: 0 4px 15px rgba(155, 206, 193, 0.1);
}

.ca-preview-item strong,
.ca-preview-item span {
    display: block;
}

.ca-preview-item strong {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--ca-gray);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.ca-preview-item strong i {
    color: #67a2c5;
    font-size: 12px;
}

.ca-preview-item span {
    margin-top: 6px;
    overflow: hidden;
    color: var(--ca-dark);
    font-size: 15px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ===== INVALID STATE ===== */
.copy-api-modal-ui .is-invalid,
.copy-api-modal-ui .ca-custom-select.is-invalid .ca-custom-select-btn {
    border-color: #e74c3c !important;
    box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1) !important;
}

.copy-api-modal-ui .invalid-feedback {
    display: none;
    margin-top: 8px;
    color: #e74c3c;
    font-size: 13px;
    font-weight: 600;
}

.copy-api-modal-ui .invalid-feedback i {
    margin-right: 6px;
}

.copy-api-modal-ui .ca-has-error .invalid-feedback {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ===== MODAL FOOTER ===== */
.copy-api-modal-ui .modal-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: flex-end;
    padding: 16px clamp(20px, 2.5vw, 32px);
    border-top: 2px solid rgba(103, 162, 197, 0.08);
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
}

.copy-api-modal-ui .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 48px;
    padding: 12px 24px;
    border: none;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
    transition: var(--ca-transition);
    cursor: pointer;
}

.copy-api-modal-ui .btn:hover {
    transform: translateY(-2px);
}

.copy-api-modal-ui .btn-primary {
    background: linear-gradient(135deg, #1a1a2e, #0f3460);
    color: #ffffff;
    box-shadow: 0 8px 25px rgba(15, 52, 96, 0.3);
    position: relative;
    overflow: hidden;
}

.copy-api-modal-ui .btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 182, 166, 0.1), rgba(155, 206, 193, 0.1), transparent);
    transition: left 0.6s ease;
}

.copy-api-modal-ui .btn-primary:hover::before {
    left: 100%;
}

.copy-api-modal-ui .btn-primary:hover,
.copy-api-modal-ui .btn-primary:focus {
    background: linear-gradient(135deg, #0f3460, #1a1a2e);
    box-shadow: 0 12px 35px rgba(15, 52, 96, 0.4);
    transform: translateY(-3px);
}

.copy-api-modal-ui .btn-primary i {
    font-size: 16px;
    color: #ffb6a6;
}

.copy-api-modal-ui .btn-secondary {
    border: 2px solid #e2e8f0;
    background: #ffffff;
    color: var(--ca-dark);
}

.copy-api-modal-ui .btn-secondary:hover,
.copy-api-modal-ui .btn-secondary:focus {
    background: #f8f9fa;
    border-color: #9bcec1;
}

/* ===== TOAST ===== */
.ca-toast {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: min(420px, calc(100vw - 48px));
    padding: 16px 20px;
    border: none;
    border-radius: var(--ca-radius);
    background: #ffffff;
    color: var(--ca-dark);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    transform: translateX(24px);
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.ca-toast.is-visible {
    transform: translateX(0);
    opacity: 1;
}

.ca-toast i {
    font-size: 20px;
}

.ca-toast .fa-check-circle {
    color: #9bcec1;
}

.ca-toast .fa-exclamation-circle {
    color: #ffb6a6;
}

/* ===== RESPONSIVE ===== */
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
        padding: 16px;
    }

    .ca-modal-hero {
        margin-bottom: 16px;
    }

    .ca-preview-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .ca-option-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 860px) {
    .ca-section-card .row {
        grid-template-columns: 1fr;
    }

    .ca-section-card .card-body {
        padding: 16px;
    }

    .ca-options-note {
        align-items: flex-start;
    }

    .ca-check-option {
        min-height: 70px;
        padding: 14px 16px;
    }

    .ca-check-ui {
        width: 48px;
        height: 48px;
        font-size: 20px;
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
        max-height: 200px;
    }

    .ca-option-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .copy-api-modal-ui {
        width: min(calc(100% - 12px), 100%);
        margin: 12px auto;
    }

    .copy-api-modal-ui .modal-body {
        padding: 12px;
    }

    .ca-modal-hero {
        padding: 16px;
    }

    .ca-modal-title h4 {
        font-size: 22px;
    }

    .ca-title-row {
        flex-direction: column;
        gap: 12px;
    }

    .ca-hero-back-btn {
        width: 100%;
        justify-content: center;
    }

    .ca-section-card .card-header {
        padding: 12px 16px;
    }

    .ca-custom-select-btn,
    .ca-control {
        min-height: 46px;
        font-size: 14px;
    }

    .copy-api-modal-ui .modal-footer {
        flex-direction: column-reverse;
        align-items: stretch;
    }

    .copy-api-modal-ui .btn {
        width: 100%;
    }

    .ca-check-option {
        grid-template-columns: 44px minmax(0, 1fr);
        gap: 12px !important;
        padding: 12px 14px;
        min-height: 60px;
    }

    .ca-check-ui {
        width: 44px;
        height: 44px;
        font-size: 18px;
    }

    .ca-check-option-content strong {
        font-size: 13px;
    }

    .ca-check-option-content small {
        font-size: 11px;
    }
}

@media (max-width: 420px) {
    .copy-api-modal-ui {
        width: 100%;
        margin: 0 auto;
        border-radius: 0;
    }

    .copy-api-modal-ui .modal-body {
        padding: 10px;
    }

    .ca-modal-hero,
    .ca-section-card,
    .ca-preview-item,
    .ca-check-option {
        border-radius: 8px;
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

<!-- Font Awesome 6 (Free) for modern icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        toast.innerHTML = '<i class="fas ' + (type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i><strong>' + message + '</strong>';
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
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
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