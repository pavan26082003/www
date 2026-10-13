

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
        <h1><?php echo 'Products'; ?></h1>

        <!-- Add a button to open a new page -->
        <a href="your_new_page.php" class="btn btn-primary">Open New Page</a>

        <table id="item-table" class="display" cellspacing="0" width="100%">
            <!-- Table content goes here -->
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#item-table").appTable({
            source: '<?php echo_uri("items/list_data") ?>',
            order: [[0, 'desc']],
            columns: [
               {title: "<?php echo 'ID'//app_lang('title') ?> ", "class": "w20p"},
                {title: "<?php echo 'Node ID'//app_lang('description') ?>"},
                {title: "<?php echo 'Category' ?>"},
                {title: "<?php echo 'Device_Time' ?>", "class": "text-right w100"},
                {title: "<?php echo 'Module Type' ?>", "class": "text-right w100"},
                {title: "<?php echo 'Device Type' ?>", "class": "text-right w100"},   
                 {title: "<?php echo 'model_number' ?>", "class": "text-right w100"}, 
                 {title: "<?php echo 'User ID' ?>", "class": "text-right w100"},
                {title: "<?php echo 'Vendor ID' ?>", "class": "text-right w100"},  
                {title: "<?php echo 'mac' ?>", "class": "text-right w100"},
                {title: "<?php echo 'payload' ?>", "class": "text-right w100"}
            ],
        });
    });
</script>