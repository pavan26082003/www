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



<?php

 $user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;


 ?>




<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <!--div class="page-title clearfix"-->
            <!--div class="fixed-menu"-->
            <h1> <?php echo 'Products'; ?></h1>
            <!--div class="title-button-group"-->
                <!--?php echo modal_anchor(get_uri("items/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . 'Add Product', array("class" => "btn btn-default", "title" => 'Add Product')); ?>
            </div-->
        </div>
        <!--div class="table-responsive"-->
           
            <table id="item-table2" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<style>
width:100%;
</style>
  

<script type="text/javascript">
    $(document).ready(function () {
        $("#item-table2").appTable({
            source: '<?php echo_uri("items/modal_productinfo") ?>',
            order: [[0, 'desc']],
            columns: [
              
                {title: "<?php echo 'Category' ?>"}, 
                {title: "<?php echo 'Module Type' ?>", "class": "text-right w100"},
                {title: "<?php echo 'Vendor ID' ?>", "class": "text-right w100"},  
                {title: "<?php echo 'mac' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'requst_id' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'ipaddress' ?>", "class": "text-right w100"},                
                 {title: "<?php echo 'time' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'phone' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'product_status' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'current_devicetype' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'share_user' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'user_priority' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'email' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'app_version' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'share_req_id' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'connect_type' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'share_req_status' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'add_node_time' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'ssid' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'wifi_pwd' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'access_token' ?>", "class": "text-right w100"}
                 


                 
            ],
        });
    });
    
   

</script>



