<!-- Bootstrap CSS-->
<!-- Font Awesome CSS-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Fontastic Custom icon font-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/fontastic.css">
<!-- Google fonts - Roboto -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
<!-- jQuery Circle-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/grasp_mobile_progress_circle-1.0.0.min.css">
<!-- Custom Scrollbar-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">
<!-- theme stylesheet-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/style.default.css" id="theme-stylesheet">
<!-- Custom stylesheet - for your changes-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/custom.css">
<!-- Favicon-->
<link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/img/favicon.ico">

<!-- ===== REQUIRED SCRIPTS FOR EXCEL & PRINT ===== -->
<!-- DataTables core -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons core -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<!-- HTML5 export buttons (Excel, CSV, PDF) -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<!-- Print button -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<!-- Buttons styling -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<div id="page-content" class="page-wrapper clearfix">
    <div class="product-page-shell">
        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo session('error'); ?>
            </div>
        <?php endif; ?>

        <div id="productSavedBanner" class="product-saved-banner" style="display:none;" role="status" aria-live="polite">
            <div class="product-saved-banner-icon"><i class="fas fa-check-circle"></i></div>
            <div class="product-saved-banner-text">
                <strong id="productSavedTitle">Product saved</strong>
                <span id="productSavedMessage">Your product was added successfully.</span>
            </div>
            <button type="button" class="product-saved-banner-close" onclick="hideProductSavedBanner()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="page-title clearfix">
            <div class="page-title-content">
                <div class="product-icon-wrapper">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="page-title-text">
                    <h1><?php echo 'Products'; ?></h1>
                    <p class="page-title-caption">A cleaner way to browse and manage your products.</p>
                </div>
            </div>

<div class="title-button-group">
    <?php
    echo modal_anchor(
        get_uri("items/modal_formcat"),
        "<i data-feather='plus-circle' class='icon-16'></i> Add product",
        array(
            "class" => "btn btn-default",
            "title" => "Add Product"
        )
    );
    ?>

    <a href="<?php echo site_url('items/vendor'); ?>" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
            
            <!-- <div class="title-button-group">
                <?php echo modal_anchor(get_uri("items/modal_formcat"), "<i data-feather='plus-circle' class='icon-16'></i> " . 'Add product', array("class" => "btn btn-default", "title" => 'Add Product')); ?>
                <a href="<?php echo site_url('dashboard'); ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div> -->
        </div>

        <div class="card product-panel">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="item-table" class="display product-card-table" cellspacing="0" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ------------script -->
<script type="text/javascript">
$(document).ready(function () {
    // Initialize the table with appTable
    $("#item-table").appTable({
        source: '<?php echo_uri("items/list_data20") ?>',
        order: [[0, 'asc']],
        responsive: false,
        filterDropdown: [
            {name: "category_id", class: "w200"}
        ],
        columns: [
            {title: "Product", data: 0, class: "product-col"},
            {title: "Image", data: 1, visible: false, searchable: false, orderable: false},
            {title: "Actions", data: 2, visible: false, searchable: false, orderable: false}
        ],
        rowCallback: function(row, data, dataIndex) {
            var name = data && data[0] ? data[0] : "";
            var imageData = data && data[1] ? data[1] : "";
            var actions = data && data[2] ? data[2] : "";
            var nameText = $('<div>').html(name).text().trim() || "Untitled product";

            var defaultImage = '<div class="product-card-placeholder"><i class="fas fa-image"></i><span>No Image</span></div>';

            var image = defaultImage;
            if (imageData) {
                var str = imageData.toString();

                if (str.indexOf('No Image') !== -1) {
                    image = defaultImage;
                } else if (str.indexOf('<img') !== -1) {
                    var $tmp = $('<div>').html(str);
                    var $img = $tmp.find('img').first();
                    if ($img.length && $img.attr('src')) {
                        image = '<img src="' + $img.attr('src') + '" alt="' + nameText.replace(/"/g, '&quot;') + '">';
                    }
                } else {
                    var srcMatch = str.match(/src=["']([^"']+)["']/i);
                    if (srcMatch && srcMatch[1]) {
                        image = '<img src="' + srcMatch[1] + '" alt="' + nameText.replace(/"/g, '&quot;') + '">';
                    } else {
                        var filenameMatch = str.match(/([\w-]+\.(?:png|jpg|jpeg|gif|webp))/i);
                        var urlMatch = str.match(/https?:\/\/[^\s\"]+/i);
                        if (urlMatch) {
                            image = '<img src="' + urlMatch[0] + '" alt="' + nameText.replace(/"/g, '&quot;') + '">';
                        } else if (filenameMatch) {
                            image = '<img src="/files/brand_logo/' + filenameMatch[1] + '" alt="' + nameText.replace(/"/g, '&quot;') + '">';
                        }
                    }
                }
            }

            var actionsNode = $('<div>').html(actions);
            actionsNode.find('a').each(function() {
                var $link = $(this);
                if ($link.hasClass('delete')) {
                    $link.addClass('delete');
                } else {
                    $link.addClass('edit');
                }
            });
            actions = actionsNode.html();

            var cardHtml = '<div class="product-card">' +
                '<div class="product-card-media">' + image + '</div>' +
                '<div class="product-card-body">' +
                    '<div class="product-card-title">' + nameText + '</div>' +
                    '<div class="product-card-actions">' + actions + '</div>' +
                '</div>' +
            '</div>';

            $(row).addClass('product-grid-item');
            $('td:eq(0)', row).html(cardHtml).addClass('product-card-cell');
        },
        onInitComplete: function() {
            // Replace Feather icons
            if (window.feather) {
                feather.replace();
            }
        },
        printColumns: [0],
        xlsColumns: [0]
    });

    // Additional feather icon replacement after a short delay
    setTimeout(function () {
        if (window.feather) {
            feather.replace();
        }
    }, 200);

    // Product saved event listener
    $(document).on("productSaved", function (e, data) {
        showProductSavedBanner(data);
    });
});

// Banner functions
function showProductSavedBanner(data) {
    data = data || {};
    var title = "Product saved";
    var message = data.message || "Your product was added successfully.";
    if (data.name) {
        title = "\"" + data.name + "\" saved";
    }
    $("#productSavedTitle").text(title);
    $("#productSavedMessage").text(message);
    var $banner = $("#productSavedBanner");
    $banner.stop(true, true).fadeIn(200);
    clearTimeout(window._productSavedBannerTimer);
    window._productSavedBannerTimer = setTimeout(function () {
        hideProductSavedBanner();
    }, 6000);
}

function hideProductSavedBanner() {
    $("#productSavedBanner").stop(true, true).fadeOut(200);
}
</script>

<style>
:root {
    --color-bg: rgb(250, 245, 242);
    --color-surface: rgb(252, 248, 210);
    --color-surface-alt: rgb(238, 238, 238);
    --color-border: rgb(151, 134, 106);
    --color-accent: rgb(115, 85, 87);
    --color-accent-dark: rgb(96, 70, 82);
    --color-text: #3d3834;
    --color-text-muted: #6b6560;
    --color-text-light: #8a8279;
    --shadow-sm: 0 2px 8px rgba(61, 56, 52, 0.06);
    --shadow-md: 0 8px 24px rgba(61, 56, 52, 0.08);
    --shadow-lg: 0 16px 40px rgba(61, 56, 52, 0.10);
    --radius-sm: 12px;
    --radius-md: 18px;
    --radius-lg: 24px;
}
html, body {
    min-height: 100%;
}

#page-content.page-wrapper,
.page-wrapper {
    padding: 24px;
    background: var(--color-bg);
    min-height: 100%;
}

.product-page-shell {
    max-width: 1280px;
    margin: 0 auto;
    font-family: 'Roboto', sans-serif;
    color: var(--color-text);
}

/* ---- Saved product banner ---- */
.product-saved-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    padding: 14px 18px;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, #edf7ee 0%, #e3f2e6 100%);
    border: 1px solid #b7dfc0;
    box-shadow: var(--shadow-sm);
    animation: productBannerIn 0.35s ease;
}
.product-saved-banner-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid #b7dfc0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.product-saved-banner-icon i {
    color: #2e7d32;
    font-size: 20px;
}
.product-saved-banner-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.product-saved-banner-text strong {
    font-size: 0.95rem;
    color: #1b5e20;
}
.product-saved-banner-text span {
    font-size: 0.82rem;
    color: #388e3c;
}
.product-saved-banner-close {
    border: none;
    background: transparent;
    color: #2e7d32;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    flex-shrink: 0;
}
.product-saved-banner-close:hover {
    background: rgba(46, 125, 50, 0.1);
}
@keyframes productBannerIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ---- Page header ---- */
.page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 22px;
    margin-bottom: 18px;
    border-radius: var(--radius-lg);
    background: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(220, 207, 192) 100%);
    border: 1px solid var(--color-border);
    color: var(--color-text);
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.page-title::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(162, 175, 155, 0.25), transparent 50%);
    pointer-events: none;
}

.page-title-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
}

.page-title-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    min-width: 0;
}

.page-title h1 {
    display: block;
    width: 100%;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--color-text);
    line-height: 1.25;
    letter-spacing: -0.01em;
    padding: 0;
}

.page-title-caption {
    display: block;
    width: 100%;
    margin: 0;
    color: var(--color-text-muted);
    line-height: 1.4;
    max-width: 640px;
    font-size: 0.8125rem;
    font-weight: 400;
}

.page-title p {
    margin: 0;
    color: var(--color-text-muted);
    line-height: 1.55;
    max-width: 640px;
    font-size: 0.95rem;
}

.product-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-sm);
    flex-shrink: 0;
}

.product-icon-wrapper i {
    font-size: 18px;
    color: var(--color-accent);
}

.title-button-group {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: flex-end;
    flex-shrink: 0;
}

.title-button-group .btn,
.title-button-group .btn-back {
    border: 0;
    border-radius: 999px;
    padding: 8px 16px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.25s ease;
    font-size: 0.8125rem;
    white-space: nowrap;
}

.title-button-group .btn-back {
    background: var(--color-bg);
    color: var(--color-text);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-sm);
}

.title-button-group .btn-back:hover {
    background: var(--color-surface);
    border-color: var(--color-accent);
    transform: translateY(-2px);
    color: var(--color-text);
}

.title-button-group .btn-default {
    background: var(--color-accent);
    color: #fff;
    box-shadow: 0 4px 14px rgba(162, 175, 155, 0.35);
}

.title-button-group .btn-default:hover {
    background: var(--color-accent-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(162, 175, 155, 0.4);
    color: #fff;
}

/* ---- Main panel ---- */
.product-panel {
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--color-bg);
    box-shadow: var(--shadow-sm);
}

.product-panel .card-body {
    padding: 20px 24px 28px;
    background: var(--color-bg);
}

.product-panel .table-responsive {
    overflow: visible;
}

/* ---- Product grid (DataTable as cards) ---- */
.product-page-shell #item-table {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    background: transparent;
    margin: 0 !important;
}

.product-page-shell #item-table thead {
    display: none !important;
}

.product-page-shell #item-table tbody {
    display: flex !important;
    flex-wrap: wrap;
    gap: 20px;
    width: 100%;
}

.product-page-shell #item-table tbody tr {
    display: block !important;
    flex: 1 1 calc(25% - 15px);
    max-width: calc(25% - 15px);
    min-width: 200px;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    float: none !important;
}

.product-page-shell #item-table tbody td {
    display: block !important;
    border: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    min-height: 0 !important;
    box-sizing: border-box !important;
    text-align: left;
    float: none !important;
}

.product-page-shell #item-table tbody td.product-cell-hidden,
.product-page-shell #item-table tbody td:nth-child(n+2) {
    display: none !important;
}

.product-page-shell #item-table tbody td:first-child::before {
    display: none !important;
    content: none !important;
}

.product-page-shell #item-table tbody td.product-card-cell,
.product-page-shell #item-table tbody td.product-card-cell .product-card {
    width: 100%;
    height: 100%;
}

.product-page-shell table.dataTable tbody tr {
    background-color: transparent !important;
}

.product-page-shell table.dataTable tbody td {
    vertical-align: top !important;
    background: transparent !important;
}

.product-page-shell table.dataTable.no-footer {
    border-bottom: none;
}

/* ---- Product card ---- */
.product-card {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    height: 100%;
    text-align: left;
    gap: 0;
    padding: 0;
    background: var(--color-bg) !important;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--color-accent);
}

.product-card-media {
    width: 100%;
    height: 180px;
    overflow: hidden;
    background: var(--color-surface-alt);
    border-bottom: 1px solid var(--color-border);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.product-card-media img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    transition: transform 0.35s ease;
}

.product-card:hover .product-card-media img {
    transform: scale(1.03);
}

.product-card-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 100%;
    background: var(--color-surface-alt);
    color: var(--color-accent);
    font-size: 2rem;
}

.product-card-placeholder span {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--color-text-light);
    letter-spacing: 0.02em;
}

.product-card-body {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: 12px;
    padding: 14px 16px 16px;
    flex: 1;
    min-width: 0;
    background: var(--color-bg);
}

.product-card-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--color-text);
    line-height: 1.35;
    word-break: break-word;
    min-height: 2.7em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card-actions {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid var(--color-surface-alt);
}

.product-card-actions a {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 999px;
    font-size: 0.9375rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    min-height: 42px;
    line-height: 1.2;
}

.product-card-actions a.edit {
    background: var(--color-surface-alt);
    color: var(--color-text);
    border: 1px solid var(--color-border);
}

.product-card-actions a.edit:hover {
    background: var(--color-accent);
    border-color: var(--color-accent);
    color: #fff;
}

.product-card-actions a.delete {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    min-width: 42px;
    padding: 10px 12px;
}

.product-card-actions a.delete:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #fff;
}

.product-card-actions a.delete svg,
.product-card-actions a.delete i {
    width: 18px;
    height: 18px;
    stroke: currentColor;
}

/* ---- DataTables toolbar (single row) ---- */
.product-page-shell .dataTables_wrapper {
    background: transparent;
    padding: 0;
    margin-bottom: 0;
}

.product-page-shell .datatable-tools,
.product-page-shell .datatable-tools.row {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    gap: 10px !important;
    width: 100% !important;
    margin: 0 0 20px !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding: 12px 16px !important;
    background: var(--color-surface) !important;
    border: 1px solid var(--color-border) !important;
    border-radius: var(--radius-md) !important;
    box-shadow: var(--shadow-sm) !important;
    position: relative !important;
    overflow: visible !important;
    min-height: 56px;
}

.product-page-shell .datatable-tools::before,
.product-page-shell .datatable-tools::after,
.product-page-shell .datatable-tools.row::before,
.product-page-shell .datatable-tools.row::after {
    display: none !important;
}

.product-page-shell .datatable-tools > .toolbar-left-top,
.product-page-shell .datatable-tools > .custom-toolbar,
.product-page-shell .datatable-tools-left,
.product-page-shell .datatable-tools-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: nowrap !important;
    width: auto !important;
    max-width: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.product-page-shell .datatable-tools > .toolbar-left-top,
.product-page-shell .datatable-tools-left {
    flex: 0 0 auto !important;
}

.product-page-shell .datatable-tools > .custom-toolbar,
.product-page-shell .datatable-tools-right {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    justify-content: flex-end !important;
    margin-left: auto !important;
}

.product-page-shell .dataTables_length,
.product-page-shell .DTTT_container,
.product-page-shell .datatable-export,
.product-page-shell .dt-buttons,
.product-page-shell .dataTables_filter {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 0 !important;
    flex: 0 0 auto !important;
    float: none !important;
}

.product-page-shell .dataTables_filter {
    flex: 1 1 200px !important;
    min-width: 160px !important;
    max-width: 260px !important;
    margin-left: auto !important;
}

.product-page-shell .dataTables_filter label {
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px;
    font-weight: 600;
    color: var(--color-text-muted);
    font-size: 13px;
    width: 100%;
}

.product-page-shell .dataTables_filter input {
    width: 100% !important;
    max-width: none !important;
    height: 38px;
    padding: 0 16px 0 40px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    background: var(--color-bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23A2AF9B' viewBox='0 0 24 24'%3E%3Cpath d='M21 20l-5.2-5.2a7 7 0 10-1 1L20 21l1-1zM4 10a6 6 0 1112 0A6 6 0 014 10z'/%3E%3C/svg%3E") no-repeat 14px center;
    background-size: 16px;
    transition: all 0.3s ease;
    font-size: 13px;
    font-family: inherit;
    color: var(--color-text);
    outline: none;
}

.product-page-shell .dataTables_filter input:focus {
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(162, 175, 155, 0.2);
}

.product-page-shell .dataTables_length {
    position: relative;
    padding-left: 0;
    margin: 0;
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
}

.product-page-shell .dataTables_length label {
    margin: 0 !important;
    font-weight: 600;
    color: var(--color-text-muted);
    font-size: 13px;
    white-space: nowrap;
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
}

.product-page-shell .dataTables_length select {
    height: 38px;
    min-width: 64px;
    border-radius: 999px !important;
    border: 1px solid var(--color-border) !important;
    background: var(--color-bg);
    color: var(--color-text);
    padding: 0 12px !important;
    font-size: 13px;
    box-shadow: none;
    cursor: pointer;
}

.product-page-shell .dataTables_length select:focus {
    border-color: var(--color-accent) !important;
    box-shadow: 0 0 0 3px rgba(162, 175, 155, 0.2);
}

.product-page-shell .dt-buttons {
    display: none !important;
}

.product-page-shell .w200,
.product-page-shell .w200 + .select2-container {
    width: 150px !important;
    min-width: 130px !important;
    max-width: 180px !important;
    flex: 0 0 auto !important;
}

.product-page-shell .select2-container .select2-choice {
    height: 38px !important;
    line-height: 36px !important;
    border-radius: 999px !important;
    border: 1px solid var(--color-border) !important;
    background: var(--color-bg) !important;
    color: var(--color-text) !important;
    padding: 0 12px !important;
    font-size: 13px;
    box-shadow: none;
}

.product-page-shell .column-show-hide-popover,
.product-page-shell .btn-default.column-show-hide-popover {
    height: 38px !important;
    min-width: 38px;
    padding: 0 12px !important;
    border-radius: 999px !important;
    border: 1px solid var(--color-border) !important;
    background: var(--color-bg) !important;
    color: var(--color-text) !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
}

.product-page-shell .dataTables_filter label .filter-label {
    display: none;
}

.product-page-shell .page-title h1 {
    float: none !important;
}

.product-page-shell .page-title.clearfix::after,
.product-page-shell .page-title.clearfix::before {
    display: none;
}

.product-page-shell .ml15,
.product-page-shell .mr15 {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.product-page-shell .dataTables_info {
    color: var(--color-text-muted) !important;
    font-size: 13px;
    padding: 12px 4px 0;
}

.product-page-shell .dataTables_paginate {
    padding: 12px 4px 0;
}

.product-page-shell .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    border: 1px solid var(--color-border) !important;
    background: var(--color-bg) !important;
    color: var(--color-text) !important;
    margin: 0 2px;
}

.product-page-shell .dataTables_paginate .paginate_button.current,
.product-page-shell .dataTables_paginate .paginate_button:hover {
    background: var(--color-accent) !important;
    border-color: var(--color-accent) !important;
    color: #fff !important;
}

/* ---- Alerts & misc ---- */
.product-page-shell .alert-danger {
    border-radius: var(--radius-sm);
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #b91c1c;
    margin-bottom: 16px;
}

.dataTables_processing {
    background: rgba(255, 255, 255, 0.92) !important;
    color: var(--color-text) !important;
    border: 1px solid var(--color-border) !important;
    border-radius: var(--radius-sm);
    backdrop-filter: blur(4px);
    box-shadow: var(--shadow-md);
}

.product-panel.card {
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.product-panel.card:hover {
    transform: none;
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-thumb {
    background: var(--color-border);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--color-accent);
}

::-webkit-scrollbar-track {
    background: var(--color-surface);
}

/* ---- Responsive ---- */
@media (max-width: 1200px) {
    .product-page-shell #item-table tbody tr {
        flex: 1 1 calc(33.333% - 14px);
        max-width: calc(33.333% - 14px);
    }
}

@media (max-width: 992px) {
    .product-page-shell #item-table tbody tr {
        flex: 1 1 calc(50% - 10px);
        max-width: calc(50% - 10px);
    }

    .page-title {
        flex-direction: column;
        align-items: stretch;
    }

    .title-button-group {
        width: 100%;
        justify-content: flex-start;
    }

    .product-page-shell .datatable-tools,
    .product-page-shell .datatable-tools.row {
        flex-wrap: wrap !important;
    }

    .product-page-shell .datatable-tools > .custom-toolbar,
    .product-page-shell .datatable-tools-right {
        flex: 1 1 100% !important;
        justify-content: flex-start !important;
        margin-left: 0 !important;
    }

    .product-page-shell .dataTables_filter {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
    }
}

@media (max-width: 768px) {
    .page-wrapper {
        padding: 16px;
    }

    .page-title {
        /* padding: 14px 16px; */
    }

    .product-panel .card-body {
        padding: 16px;
    }

    .product-page-shell #item-table tbody tr {
        flex: 1 1 100%;
        max-width: 100%;
        min-width: 100%;
    }

    .product-card-media {
        height: 170px;
    }

    .product-page-shell .datatable-tools,
    .product-page-shell .datatable-tools.row {
        flex-direction: column;
        align-items: stretch !important;
        flex-wrap: wrap !important;
    }

    .product-page-shell .datatable-tools > .toolbar-left-top,
    .product-page-shell .datatable-tools > .custom-toolbar,
    .product-page-shell .datatable-tools-left,
    .product-page-shell .datatable-tools-right {
        width: 100% !important;
        flex-wrap: wrap !important;
    }

    .product-page-shell .w200,
    .product-page-shell .w200 + .select2-container {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
    }

    .product-page-shell .dt-buttons {
        width: 100%;
        flex-wrap: wrap !important;
    }

    .product-page-shell .dt-button,
    .product-page-shell .dt-buttons .btn {
        flex: 1;
        min-width: 0;
    }
}

@media (max-width: 480px) {
    .page-title-content {
        flex-direction: row;
        align-items: flex-start;
    }

    .title-button-group {
        flex-direction: column;
        align-items: stretch;
    }

    .title-button-group .btn,
    .title-button-group .btn-back {
        width: 100%;
        justify-content: center;
    }

    .product-card-media {
        height: 160px;
    }
}
</style>