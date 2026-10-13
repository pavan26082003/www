<!-- Font Awesome CSS-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">
<!-- Fontastic Custom icon font-->
<link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/css/fontastic.css">
<!-- Google fonts - Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<style>
/* ===== BRAND PAGE — CHILD-FRIENDLY & ACCESSIBLE ===== */
#brand-page-wrapper {
    font-family: 'Inter', 'Roboto', sans-serif;
    padding: 24px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* ---- PAGE HEADER ---- */
.brand-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 28px;
    padding: 24px 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    box-shadow: 0 12px 32px -8px rgba(102, 126, 234, 0.45);
    position: relative;
    overflow: hidden;
}
.brand-header::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
}
.brand-header::after {
    content: '';
    position: absolute;
    bottom: -50px; left: 60px;
    width: 140px; height: 140px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.brand-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
    z-index: 1;
}
.brand-header-icon {
    width: 60px; height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
    color: white;
    backdrop-filter: blur(4px);
    border: 2px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
}
.brand-header-text h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: white;
    margin: 0;
    letter-spacing: -0.3px;
    line-height: 1.2;
}
.brand-header-text p {
    color: rgba(255,255,255,0.82);
    font-size: 0.88rem;
    margin: 4px 0 0;
    font-weight: 400;
}
.brand-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 26px;
    background: white;
    color: #667eea;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    z-index: 1;
    white-space: nowrap;
}
.brand-add-btn:hover, .brand-add-btn:focus {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 14px 28px rgba(0,0,0,0.2);
    color: #5a67d8;
    outline: 3px solid rgba(255,255,255,0.6);
    outline-offset: 2px;
}
.brand-add-btn i { font-size: 1.1rem; }

/* ---- ALERT ---- */
.brand-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 1.5px solid #f87171;
    border-radius: 16px;
    color: #b91c1c;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 20px;
    animation: slideDown 0.3s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ---- STATS BAR ---- */
.brand-stats-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 24px;
}
.brand-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: white;
    border-radius: 50px;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 4px 10px -4px rgba(0,0,0,0.07);
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
    transition: all 0.2s;
}
.brand-stat-pill:hover {
    border-color: #a5b4fc;
    box-shadow: 0 8px 18px -6px rgba(102,126,234,0.2);
    transform: translateY(-2px);
}
.brand-stat-pill .pill-icon {
    width: 34px; height: 34px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    color: white;
    flex-shrink: 0;
}
.pill-purple { background: linear-gradient(135deg, #667eea, #764ba2); }
.pill-green  { background: linear-gradient(135deg, #10b981, #34d399); }
.pill-orange { background: linear-gradient(135deg, #f97316, #fb923c); }

/* ---- SEARCH & FILTER BAR ---- */
.brand-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding: 16px 20px;
    background: white;
    border-radius: 18px;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 4px 12px -6px rgba(0,0,0,0.06);
}
.brand-search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.brand-search-wrap i {
    position: absolute;
    left: 14px; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
    pointer-events: none;
}
#brand-search-input {
    width: 100%;
    padding: 10px 14px 10px 38px;
    border: 1.5px solid #e2e8f0;
    border-radius: 50px;
    font-size: 0.88rem;
    font-family: inherit;
    color: #334155;
    background: #f8fafc;
    transition: all 0.2s;
    outline: none;
}
#brand-search-input:focus {
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
}
.brand-view-toggle {
    display: flex;
    gap: 6px;
}
.view-btn {
    width: 38px; height: 38px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    color: #94a3b8;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
}
.view-btn.active, .view-btn:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 10px rgba(102,126,234,0.3);
}
.view-btn:focus {
    outline: 3px solid rgba(102,126,234,0.4);
    outline-offset: 2px;
}

/* ---- TABLE CARD ---- */
.brand-table-card {
    background: white;
    border-radius: 22px;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 8px 24px -10px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.2s;
}

/* DataTable overrides */
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input {
    border: 1.5px solid #e2e8f0;
    border-radius: 50px;
    padding: 7px 14px;
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.2s;
}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
}
#brand-page-wrapper table.dataTable thead th {
    background: linear-gradient(135deg, #f8fafc, #eef2ff);
    color: #475569;
    font-weight: 700;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}
#brand-page-wrapper table.dataTable tbody tr {
    transition: background 0.15s;
}
#brand-page-wrapper table.dataTable tbody tr:hover {
    background: #f5f3ff !important;
}
#brand-page-wrapper table.dataTable tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    font-size: 0.88rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}

/* brand logo in table */
.brand-logo-cell img {
    width: 56px; height: 56px;
    object-fit: contain;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    background: #f8fafc;
    padding: 4px;
    transition: transform 0.2s;
}
.brand-logo-cell img:hover { transform: scale(1.15); }
.brand-logo-placeholder {
    width: 56px; height: 56px;
    border-radius: 12px;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    border: 2px dashed #a5b4fc;
    display: flex; align-items: center; justify-content: center;
    color: #818cf8;
    font-size: 1.3rem;
}

/* brand name badge */
.brand-name-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #1e293b;
    font-size: 0.92rem;
}
.brand-name-badge .brand-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    flex-shrink: 0;
}

/* ---- GRID VIEW ---- */
#brand-grid-view {
    display: none;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 18px;
    padding: 20px;
}
#brand-grid-view.active { display: grid; }
.brand-grid-card {
    background: white;
    border-radius: 20px;
    border: 1.5px solid #e2e8f0;
    padding: 20px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    box-shadow: 0 4px 12px -6px rgba(0,0,0,0.06);
    position: relative;
    overflow: hidden;
}
.brand-grid-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    opacity: 0;
    transition: opacity 0.2s;
}
.brand-grid-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 32px -12px rgba(102,126,234,0.25);
    border-color: #a5b4fc;
}
.brand-grid-card:hover::before { opacity: 1; }
.brand-grid-card:focus-within {
    outline: 3px solid #667eea;
    outline-offset: 2px;
}
.brand-grid-logo {
    width: 80px; height: 80px;
    margin: 0 auto 14px;
    border-radius: 18px;
    border: 2px solid #e2e8f0;
    background: #f8fafc;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    padding: 6px;
}
.brand-grid-logo img {
    width: 100%; height: 100%;
    object-fit: contain;
}
.brand-grid-logo .no-logo-icon {
    font-size: 2rem;
    color: #a5b4fc;
}
.brand-grid-name {
    font-weight: 700;
    font-size: 0.9rem;
    color: #1e293b;
    margin-bottom: 10px;
    word-break: break-word;
}
.brand-grid-actions {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* ---- EMPTY STATE ---- */
.brand-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.brand-empty-state .empty-icon {
    font-size: 4rem;
    margin-bottom: 16px;
    display: block;
    animation: float 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
}
.brand-empty-state h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 8px;
}
.brand-empty-state p {
    font-size: 0.88rem;
    max-width: 300px;
    margin: 0 auto;
}

/* ---- LOADING SKELETON ---- */
.brand-skeleton {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px;
}
.skeleton-row {
    height: 60px;
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    border-radius: 12px;
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* ---- RESPONSIVE ---- */
@media (max-width: 640px) {
    .brand-header { padding: 18px 16px; }
    .brand-header-text h1 { font-size: 1.4rem; }
    .brand-add-btn { padding: 11px 18px; font-size: 0.85rem; }
    #brand-grid-view { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
}

/* ---- ACCESSIBILITY: focus ring ---- */
a:focus-visible, button:focus-visible {
    outline: 3px solid #667eea;
    outline-offset: 3px;
}
</style>

<div id="brand-page-wrapper">

    <?php if(session()->has('error')): ?>
    <div class="brand-alert" role="alert" aria-live="assertive">
        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
        <?php echo session('error'); ?>
    </div>
    <?php endif; ?>

    <!-- PAGE HEADER -->
    <div class="brand-header" role="banner">
        <div class="brand-header-left">
            <div class="brand-header-icon" aria-hidden="true">
                <i class="fas fa-tags"></i>
            </div>
            <div class="brand-header-text">
                <h1><?php echo 'Brands'; ?></h1>
                <p>Manage all your product brands in one place</p>
            </div>
        </div>
        <?php echo modal_anchor(get_uri("items/modal_formbrand"),
            '<i class="fas fa-plus" aria-hidden="true"></i> Add Brand',
            array("class" => "brand-add-btn", "title" => 'Add Brand', "aria-label" => "Add a new brand")
        ); ?>
    </div>

    <!-- STATS PILLS -->
    <div class="brand-stats-bar" role="region" aria-label="Brand statistics">
        <div class="brand-stat-pill">
            <div class="pill-icon pill-purple" aria-hidden="true"><i class="fas fa-layer-group"></i></div>
            <span>Total Brands: <strong id="brand-total-count">—</strong></span>
        </div>
        <div class="brand-stat-pill">
            <div class="pill-icon pill-green" aria-hidden="true"><i class="fas fa-image"></i></div>
            <span>With Logo: <strong id="brand-logo-count">—</strong></span>
        </div>
        <div class="brand-stat-pill">
            <div class="pill-icon pill-orange" aria-hidden="true"><i class="fas fa-clock"></i></div>
            <span id="brand-last-updated">Last updated just now</span>
        </div>
    </div>

    <!-- TOOLBAR -->
    <div class="brand-toolbar" role="toolbar" aria-label="Brand view controls">
        <div class="brand-search-wrap">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="search"
                   id="brand-search-input"
                   placeholder="Search brands..."
                   aria-label="Search brands"
                   autocomplete="off">
        </div>
        <div class="brand-view-toggle" role="group" aria-label="View mode">
            <button class="view-btn active" id="btn-table-view" title="Table view" aria-pressed="true" aria-label="Switch to table view">
                <i class="fas fa-list" aria-hidden="true"></i>
            </button>
            <button class="view-btn" id="btn-grid-view" title="Grid view" aria-pressed="false" aria-label="Switch to grid view">
                <i class="fas fa-th" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <!-- TABLE VIEW -->
    <div class="brand-table-card" id="brand-table-view" role="region" aria-label="Brands table">
        <div class="table-responsive">
            <table id="item-table" class="display" cellspacing="0" width="100%" aria-label="Brands list">
            </table>
        </div>
    </div>

    <!-- GRID VIEW (populated by JS after DataTable loads) -->
    <div id="brand-grid-view" role="region" aria-label="Brands grid" aria-live="polite">
        <!-- cards injected by JS -->
    </div>

</div><!-- /#brand-page-wrapper -->

<script type="text/javascript">
$(document).ready(function () {

    /* =============================================
       BACKEND CALL — UNTOUCHED
    ============================================= */
    $("#item-table").appTable({
        source: '<?php echo_uri("items/list_data30") ?>',
        order: [[0, 'desc']],
        filterDropdown: [
            {name: "category_id", class: "w200"}
        ],
        columns: [
            {
                title: "<?php echo 'Brand Name' ?>",
                class: "w20p",
                data: "brand_name",
                render: function(data) {
                    return '<span class="brand-name-badge"><span class="brand-dot"></span>' + (data || '—') + '</span>';
                }
            },
            {
                title: "<?php echo 'Brand Logo' ?>",
                class: "w20p",
                data: "brandlogo_url",
                render: function (data) {
                    if (data) {
                        return '<div class="brand-logo-cell"><img src="' + data + '" alt="Brand Logo" loading="lazy" onerror="this.parentElement.innerHTML=\'<div class=\\\"brand-logo-placeholder\\\"><i class=\\\"fas fa-image\\\"></i></div>\'"></div>';
                    }
                    return '<div class="brand-logo-placeholder" aria-label="No logo"><i class="fas fa-image"></i></div>';
                }
            },
            {
                title: "<i data-feather='menu' class='icon-16' aria-hidden='true'></i><span class='sr-only'>Actions</span>",
                class: "text-center option w100",
                data: "options"
            }
        ],
        printColumns: [0, 1, 2, 3],
        xlsColumns: [0, 1, 2, 3]
    });

    /* =============================================
       STATS COUNTER — reads from rendered table
    ============================================= */
    function updateStats() {
        var table = $('#item-table').DataTable();
        if (!table) return;
        var allData = table.rows().data();
        var total = allData.length;
        var withLogo = 0;
        allData.each(function(row) {
            if (row.brandlogo_url && row.brandlogo_url.trim() !== '') withLogo++;
        });
        $('#brand-total-count').text(total);
        $('#brand-logo-count').text(withLogo);
        var now = new Date();
        $('#brand-last-updated').text('Updated ' + now.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}));
        buildGridView(allData);
    }

    // hook into DataTable draw event
    $('#item-table').on('draw.dt', function() {
        updateStats();
        if (typeof feather !== 'undefined') feather.replace();
    });

    /* =============================================
       GRID VIEW BUILDER
    ============================================= */
    function buildGridView(allData) {
        var grid = $('#brand-grid-view');
        grid.empty();
        if (!allData || allData.length === 0) {
            grid.html('<div class="brand-empty-state"><span class="empty-icon" aria-hidden="true">🏷️</span><h3>No brands yet</h3><p>Click "Add Brand" to create your first brand.</p></div>');
            return;
        }
        allData.each(function(row) {
            var logoHtml = row.brandlogo_url
                ? '<img src="' + row.brandlogo_url + '" alt="' + (row.brand_name || 'Brand') + ' logo" loading="lazy" onerror="this.parentElement.innerHTML=\'<span class=\\\"no-logo-icon\\\"><i class=\\\"fas fa-image\\\"></i></span>\'">'
                : '<span class="no-logo-icon" aria-hidden="true"><i class="fas fa-image"></i></span>';

            var card = $('<div class="brand-grid-card" tabindex="0" role="article" aria-label="' + (row.brand_name || 'Brand') + '">'
                + '<div class="brand-grid-logo">' + logoHtml + '</div>'
                + '<div class="brand-grid-name">' + (row.brand_name || '—') + '</div>'
                + '<div class="brand-grid-actions">' + (row.options || '') + '</div>'
                + '</div>');
            grid.append(card);
        });
    }

    /* =============================================
       VIEW TOGGLE (Table ↔ Grid)
    ============================================= */
    $('#btn-table-view').on('click', function() {
        $('#brand-table-view').show();
        $('#brand-grid-view').removeClass('active');
        $(this).addClass('active').attr('aria-pressed', 'true');
        $('#btn-grid-view').removeClass('active').attr('aria-pressed', 'false');
    });

    $('#btn-grid-view').on('click', function() {
        $('#brand-table-view').hide();
        $('#brand-grid-view').addClass('active');
        $(this).addClass('active').attr('aria-pressed', 'true');
        $('#btn-table-view').removeClass('active').attr('aria-pressed', 'false');
        // rebuild grid with latest data
        var table = $('#item-table').DataTable();
        if (table) buildGridView(table.rows().data());
    });

    /* =============================================
       LIVE SEARCH (filters DataTable)
    ============================================= */
    $('#brand-search-input').on('input', function() {
        var val = $(this).val();
        if ($.fn.DataTable.isDataTable('#item-table')) {
            $('#item-table').DataTable().search(val).draw();
        }
    });

    /* =============================================
       KEYBOARD: Enter on grid card triggers first action
    ============================================= */
    $(document).on('keydown', '.brand-grid-card', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).find('a, button').first().trigger('click');
        }
    });

});
</script>
