<!-- Bootstrap CSS-->
    <!--link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/bootstrap/css/bootstrap.min.css"-->
    
    
    
    
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/dashboard/1-4-5/vendor/font-awesome/css/font-awesome.min.css">
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







<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo session('error'); ?>
            </div>
        <?php endif; ?>
        <div class="page-title clearfix">
            <h1> <?php echo 'Brand'; //echo $client_id; ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("items/modal_formbrand"), "<i data-feather='plus-circle' class='icon-16'></i> " . 'Add Brand', array("class" => "btn btn-default", "title" => 'Add Brand')); ?>
            </div> 
        </div>
        <div class="table-responsive">
            <table id="item-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>












<script type="text/javascript">
    $(document).ready(function () { 
        $("#item-table").appTable({
            source: '<?php echo_uri("items/list_data30") ?>',
            order: [[0, 'desc']],
            filterDropdown: [
                {name: "category_id", class: "w200"}
            ],
            columns: [
    { title: "<?php echo 'Brand Name' ?>", class: "w20p", data: "brand_name" },

    {
        title: "<?php echo 'Brand Logo' ?>",
        class: "w20p",
        data: "brandlogo_url",
        render: function (data, type, row) {
            if (data) {
                return '<img src="' + data + '" width="60" height="60" alt="Brand Logo" onerror="this.src=\'/files/no-image.png\'">';
            } else {
                return '<div style="width:60px;height:60px;border:1px solid #ccc;text-align:center;line-height:60px;">No Image</div>';
            }
        }
    },

    { title: "<i data-feather='menu' class='icon-16'></i>", class: "text-center option w100", data: "options" }
],

            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>
