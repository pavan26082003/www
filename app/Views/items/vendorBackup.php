<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.4/dist/tailwind.min.css" rel="stylesheet">

<style>
body {
    background: radial-gradient(circle at top, #eef2ff 0%, #f8fafc 48%, #f4f6f9 100%);
    font-family: 'Poppins', sans-serif;
}

/* Card */
.card {
    border-radius: 32px;
    box-shadow: 0 28px 70px rgba(15,23,42,0.08);
    border: 1px solid rgba(148,163,184,0.18);
    background: rgba(255, 255, 255, 0.98);
    padding: 32px;
}

/* Page Title */
.page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 28px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(148,163,184,0.16);
}

.page-title h2 {
    font-weight: 700;
    color: #0f172a;
    font-size: 1.8rem;
    letter-spacing: -0.03em;
}

.page-title small {
    display: block;
    margin-top: 6px;
    color: #475569;
    font-weight: 500;
}

/* Buttons */
.btn-gradient,
.btn-success-modern,
.btn-dark-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 11px 22px;
    border-radius: 14px;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    text-decoration: none;
}

.btn-gradient {
    background: linear-gradient(135deg, #4e73df 0%, #10b981 100%);
    color: #ffffff;
    box-shadow: 0 18px 36px rgba(79,70,229,0.18);
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 22px 44px rgba(79,70,229,0.24);
}

.btn-success-modern {
    background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
    color: #ffffff;
    box-shadow: 0 18px 36px rgba(16,185,129,0.18);
}

.btn-success-modern:hover {
    transform: translateY(-2px);
}

.btn-dark-modern {
    background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
    color: #ffffff;
    box-shadow: 0 18px 36px rgba(15,23,42,0.16);
}

.btn-dark-modern:hover {
    transform: translateY(-2px);
}

/* Table */
.table-responsive {
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid rgba(148,163,184,0.16);
    background: #ffffff;
}

#item-table thead {
    background: linear-gradient(90deg, #4e73df 0%, #10b981 100%);
    color: #ffffff;
}

#item-table thead th {
    padding: 16px 18px !important;
    font-weight: 700;
    border-bottom: none;
}

#item-table tbody tr {
    transition: background 0.2s ease, transform 0.2s ease;
}

#item-table tbody tr:hover {
    background: #f0f9ff !important;
    transform: translateX(1px);
}

#item-table tbody td {
    padding: 16px 18px;
    vertical-align: middle;
}

.dataTables_filter input {
    border-radius: 999px !important;
    padding: 10px 16px !important;
    border: 1px solid rgba(148,163,184,0.24) !important;
    box-shadow: 0 8px 20px rgba(15,23,42,0.06) !important;
}

.alert {
    border-radius: 18px;
    padding: 16px 18px;
    font-weight: 600;
    margin-bottom: 20px;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
}

.dropdown-menu {
    border-radius: 20px;
    box-shadow: 0 24px 56px rgba(15,23,42,0.16);
    border: none;
    padding: 10px;
    z-index: 9999 !important;
}

.dropdown-menu a {
    padding: 10px 14px;
    border-radius: 12px;
    transition: background 0.2s ease;
}

.dropdown-menu a:hover {
    background: #eff6ff;
}

@media (max-width: 991px) {
    .page-title {
        flex-direction: column;
        align-items: flex-start;
    }
    .page-title .d-flex {
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    .btn-gradient,
    .btn-success-modern,
    .btn-dark-modern {
        width: 100%;
    }
}
</style>

<?php
$user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;
?>

<div id="page-content" class="page-wrapper clearfix px-4 py-6">
    <div class="card">

        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger"><?= session('error'); ?></div>
        <?php endif; ?>

        <?php if(session()->has('success')): ?>
            <div class="alert alert-success"><?= session('success'); ?></div>
        <?php endif; ?>

        <!-- Header -->
        <div class="page-title">
            <div>
                <h2>Efactor Model Management</h2>
                <small class="text-muted">Manage Brands, Products & Models</small>
            </div>

            <div class="d-flex gap-3 flex-wrap items-center">
                <a href="<?= site_url('items/brand') ?>" class="btn btn-gradient">Add Brand</a>
                <a href="<?= site_url('items/product') ?>" class="btn btn-gradient">Add Product</a>

                <?php if($user_id !== '1'){ ?>
                    <?= modal_anchor(
                        get_uri("items/modal_vendorproduct"),
                        'Add Model',
                        ["class" => "btn btn-success-modern"]
                    ); ?>
                <?php } else { ?>
                    <?= modal_anchor(
                        get_uri("items/modal_vendor_admin_product"),
                        'Assign Model',
                        ["class" => "btn btn-success-modern"]
                    ); ?>
                <?php } ?>

                <a href="<?= site_url('items/copy_api') ?>" class="btn btn-dark-modern">Copy API</a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="item-table" class="display" width="100%"></table>
        </div>

    </div>
</div>

<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function () {

    $("#item-table").appTable({
        source: '<?php echo_uri("items/list_data1vendor") ?>',
        order: [[0, 'desc']],
        filterDropdown: [
            {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>}
        ],
        columns: [
            {title: "Model Name"},
            {title: "Brand Name"},
            {title: "Product Name"},
            {title: "Model No"},
            {title: "Version"},      // ✅ NEW
            {title: "Batch"},        // ✅ NEW
            {title: "<i class='fa fa-cogs'></i>", class: "text-center option w100"}
        ],
        printColumns: [0,1,2,3,4,5,6,7],
        xlsColumns: [0,1,2,3,4,5,6,7]
    });

    /* PERFECT DROPDOWN CONTROL */
    $(document).on("click", ".dropdown-toggle", function (e) {
        e.stopPropagation();
        $(".dropdown-menu").removeClass("show");
        $(this).next(".dropdown-menu").addClass("show");
    });

    $(document).on("click", function () {
        $(".dropdown-menu").removeClass("show");
    });

});
</script>


<!-- backup style -->

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    background: #F8F5F2;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
}

html, body {
    scroll-behavior: smooth !important;
}

/* Main Container */
#page-content {
    background: #FFFFFF;
    border-radius: 24px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 24px rgba(27, 30, 43, 0.08);
    margin: 0;
    padding: 0;
}

/* Card */
.card {
    border-radius: 24px;
    box-shadow: 0 8px 24px rgba(43, 45, 66, 0.08);
    border: 1px solid #E5DDD5;
    background: #FFFFFF;
    padding: 20px;
    margin: 0;
}

/* Page Title */
.page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
    margin-top: 0;
    padding-bottom: 16px;
    border-bottom: 1px solid #E5DDD5;
}

.page-title h2 {
    font-weight: 700;
    color: #2B2D42;
    font-size: 2rem;
    letter-spacing: -0.03em;
    background: none;
    -webkit-background-clip: unset;
    -webkit-text-fill-color: unset;
    background-clip: unset;
}

.page-title small {
    display: block;
    margin-top: 6px;
    color: #6B7280;
    font-weight: 500;
    font-size: 0.95rem;
}

/* Buttons */
.btn-gradient,
.btn-success-modern,
.btn-dark-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 14px;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    text-decoration: none;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-gradient {
    background: linear-gradient(135deg, #C08457 0%, #6B8F71 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 30px rgba(192, 132, 87, 0.2);
}

.btn-gradient:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 45px rgba(192, 132, 87, 0.3);
    background: linear-gradient(135deg, #D4A373 0%, #E6B980 100%);
}

.btn-success-modern {
    background: linear-gradient(135deg, #6B8F71 0%, #3A3F58 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 30px rgba(107, 143, 113, 0.2);
}

.btn-success-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 45px rgba(107, 143, 113, 0.3);
    background: linear-gradient(135deg, #6B8F71 0%, #6B8F71 100%);
}

.btn-dark-modern {
    background: linear-gradient(135deg, #2B2D42 0%, #3A3F58 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 30px rgba(43, 45, 66, 0.2);
}

.btn-dark-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 45px rgba(43, 45, 66, 0.3);
    background: linear-gradient(135deg, #3A3F58 0%, #2B2D42 100%);
}

/* Cards Grid Layout */
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

/* Individual Card */
.model-card {
    background: #FFFFFF;
    border-radius: 20px;
    padding: 18px;
    box-shadow: 0 4px 16px rgba(27, 30, 43, 0.06);
    border: 1px solid #E5DDD5;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.model-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #C08457, #6B8F71, #D4A373);
    transition: left 0.4s ease;
}

.model-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 40px rgba(192, 132, 87, 0.18);
    border-color: #D4A373;
}

.model-card:hover::before {
    left: 100%;
}

.card-header {
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    margin-bottom: 12px;
    gap: 12px;
}

.card-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #2B2D42 0%, #3A3F58 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #E6B980;
    font-size: 20px;
    flex-shrink: 0;
    border: 1px solid rgba(192, 132, 87, 0.22);
}

.card-actions {
    position: relative;
}



.card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1B1E2B;
    margin-bottom: 4px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}

.card-subtitle {
    font-size: 0.85rem;
    color: #6B7280;
    margin-bottom: 10px;
    font-weight: 500;
}

.card-content {
    margin-bottom: 10px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #E5DDD5;
    font-size: 0.8rem;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #6B7280;
    font-weight: 600;
}

.info-value {
    color: #1B1E2B;
    font-weight: 700;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 8px;
}

.badge-primary {
    background: rgba(43, 45, 66, 0.08);
    color: #2B2D42;
    border: 1px solid rgba(210, 178, 128, 0.2);
}

.badge-success {
    background: rgba(107, 143, 113, 0.12);
    color: #6B8F71;
    border: 1px solid rgba(107, 143, 113, 0.18);
}

.card-footer {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #E5DDD5;
    position: relative;
}

.btn-sm {
    flex: 1;
    padding: 8px 12px;
    font-size: 0.8rem;
    border-radius: 9px;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-action {
    flex: 1;
    background: linear-gradient(135deg, #2B2D42 0%, #3A3F58 100%) !important;
    color: #FFFFFF !important;
    padding: 8px 12px !important;
    border-radius: 9px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    transition: all 0.2s ease !important;
    font-size: 0.8rem !important;
}

.btn-action:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(192, 132, 87, 0.18) !important;
    background: linear-gradient(135deg, #3A3F58 0%, #2B2D42 100%) !important;
}

.btn-edit {
    background: linear-gradient(135deg, #2B2D42 0%, #3A3F58 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(43, 45, 66, 0.18);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(43, 45, 66, 0.22);
}

.btn-delete {
    background: linear-gradient(135deg, #C08457 0%, #D4A373 100%);
    color: #1B1E2B;
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(192, 132, 87, 0.18);
}

/* Dropdown Menu */
.dropdown-menu {
    border-radius: 14px;
    box-shadow: 0 12px 32px rgba(43, 45, 66, 0.14);
    border: 1px solid #E5DDD5;
    padding: 8px;
    z-index: 10001 !important;
    background: #FFFFFF;
    min-width: 220px;
    max-height: 420px;
    overflow-y: auto;
    overflow-x: hidden;
}

.dropdown-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}

.dropdown-menu::-webkit-scrollbar-thumb {
    background: #D4A373;
    border-radius: 3px;
}

.dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: #C08457;
}

.dropdown-menu a {
    padding: 8px 12px;
    border-radius: 10px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #1B1E2B;
    cursor: pointer;
    white-space: nowrap;
}

.dropdown-menu a:hover {
    background: rgba(192, 132, 87, 0.18);
    color: #2B2D42;
    transform: translateX(2px);
}

/* Alerts */
.alert {
    border-radius: 18px;
    padding: 16px 20px;
    font-weight: 600;
    margin-bottom: 20px;
    border: none;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-danger {
    background: rgba(192, 132, 87, 0.1);
    color: #C08457;
    border-left: 4px solid #C08457;
}

.alert-success {
    background: rgba(107, 143, 113, 0.1);
    color: #6B8F71;
    border-left: 4px solid #6B8F71;
}

.alert::before {
    font-size: 1.2rem;
}

/* Search & Filter */
.dataTables_filter {
    margin-bottom: 20px;
}

.dataTables_filter input {
    border-radius: 16px !important;
    padding: 12px 18px !important;
    border: 2px solid #E5DDD5 !important;
    box-shadow: 0 4px 12px rgba(192, 132, 87, 0.08) !important;
    font-size: 0.95rem;
    color: #1B1E2B;
    transition: all 0.3s ease;
}

.dataTables_filter input::placeholder {
    color: #6B7280;
}

.dataTables_filter input:focus {
    border-color: #C08457 !important;
    box-shadow: 0 8px 20px rgba(192, 132, 87, 0.18) !important;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    font-size: 4rem;
    color: rgba(192, 132, 87, 0.2);
    margin-bottom: 20px;
}

.empty-state-text {
    color: #6B7280;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }
}

@media (max-width: 768px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
    }

    .page-title {
        flex-direction: column;
        align-items: flex-start;
    }

    .page-title h2 {
        font-size: 1.5rem;
    }

    .page-title .d-flex {
        width: 100%;
        flex-direction: column;
    }

    .btn-gradient,
    .btn-success-modern,
    .btn-dark-modern {
        width: 100%;
        justify-content: center;
    }

    .card {
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .cards-container {
        grid-template-columns: 1fr;
    }

    .model-card {
        padding: 18px;
    }

    .card-title {
        font-size: 1rem;
    }

    .info-row {
        flex-direction: column;
        gap: 4px;
    }
}

/* Loading Animation */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(43, 45, 66, 0.18);
    border-radius: 50%;
    border-top-color: #C08457;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.search-input {
    border-radius: 16px !important;
    padding: 12px 18px !important;
    border: 2px solid #E5DDD5 !important;
    width: 300px !important;
    max-width: 100% !important;
    box-shadow: 0 4px 12px rgba(192, 132, 87, 0.08) !important;
    color: #1B1E2B !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
}

.search-input::placeholder {
    color: #6B7280 !important;
}

.search-input:focus {
    border-color: #C08457 !important;
    box-shadow: 0 8px 20px rgba(192, 132, 87, 0.18) !important;
    outline: none !important;
}

.model-card.active {
    border-color: #D4A373;
    background: #F4ECE2;
    box-shadow: 0 18px 44px rgba(192, 132, 87, 0.18);
}

.model-card.active .btn-action {
    background: linear-gradient(135deg, #3A3F58 0%, #2B2D42 100%) !important;
    box-shadow: 0 8px 24px rgba(192, 132, 87, 0.18) !important;
}

/* Dropdown positioning */
.card-footer {
    position: relative;
    overflow: visible;
}

.dropdown-menu {
    position: absolute !important;
    bottom: auto !important;
    top: 100% !important;
    left: 0 !important;
    right: auto !important;
    margin-top: 6px !important;
}

.dropdown-menu.show {
    display: flex !important;
    flex-direction: column !important;
}

.dropdown-menu a.dropdown-item {
    display: flex !important;
}

</style>



