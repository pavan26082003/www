<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


<style>
body {
    background: #f4f6f9;
    font-family: 'Poppins', sans-serif;
}


/* Card */
.card {
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border: none;
    padding: 25px;
}

/* Page Title */
.page-title h2 {
    font-weight: 600;
    color: #2c3e50;
}

/* Buttons */
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-weight: 500;
}

.btn-success-modern {
    background: #28a745;
    color: white;
    border-radius: 8px;
    padding: 8px 18px;
    border: none;
}

.btn-dark-modern {
    background: #343a40;
    color: white;
    border-radius: 8px;
    padding: 8px 18px;
}

/* Table */
#item-table thead {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: white;
}

#item-table thead th {
    padding: 14px !important;
    font-weight: 600;
}

#item-table tbody tr:hover {
    background: #f1f5ff; /* NO transform */
}

/* DataTable search */
.dataTables_filter input {
    border-radius: 8px !important;
    padding: 6px 10px !important;
}

/* IMPORTANT FIXES */
.table-responsive,
.dataTables_wrapper {
    overflow: visible !important;
}

#item-table td {
    position: relative;
}

/* Dropdown */
.dropdown-menu {
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    border: none;
    padding: 8px;
    z-index: 9999 !important;
}

.dropdown-menu a {
    padding: 8px 12px;
    border-radius: 6px;
}

.dropdown-menu a:hover {
    background: #f1f3f9;
}
</style>

<?php
$user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card">

        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger"><?= session('error'); ?></div>
        <?php endif; ?>

        <?php if(session()->has('success')): ?>
            <div class="alert alert-success"><?= session('success'); ?></div>
        <?php endif; ?>

        <!-- Header -->
        <div class="page-title d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Efactor Model Management</h2>
                <small class="text-muted">Manage Brands, Products & Models</small>
            </div>

            <div class="d-flex gap-2 flex-wrap">
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


