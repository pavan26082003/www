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
        <div class="page-title clearfix">
            <h1> <?php echo 'Products'; //echo $client_id; ?></h1>
        <!--     <div class="title-button-group">
                <?php echo modal_anchor(get_uri("forms/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . 'Add Product', array("class" => "btn btn-default", "title" => 'Add Product')); ?>
            </div> -->
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
            source: '<?php echo_uri("forms/list_data1") ?>',
            order: [[0, 'desc']],
            filterDropdown: [
                {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>}
            ],
            columns: [
                {title: "<?php echo 'Serial No'//app_lang('title') ?> ", "class": "w20p"},
                {title: "<?php echo 'Purchased Date'//app_lang('description') ?>"},
                 {title: "<?php echo 'UID'//app_lang('description') ?>"},
                {title: "<?php echo app_lang('category') ?>"},
              //  {title: "<?php echo app_lang('unit_type') ?>", "class": "w100"},
                {title: "<?php echo app_lang('rate') ?>", "class": "text-right w100"}

              //  {title: "<?php echo 'Mapped Product Status'//app_lang('description') ?>"}
            
               // {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>