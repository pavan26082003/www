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

<style>
    #item-table th {
        position: sticky;
        top: 0;         
        z-index: 1; 
    }
</style>


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
            
            <table id="item-table" class="display" cellspacing="0" width="100%">     
                    
            </table>
            </table>
            
        </div>
    </div>
</div>


  

<script type="text/javascript">
    $(document).ready(function () {
        $("#item-table").appTable({
            source: '<?php echo_uri("items/list_data") ?>',
            order: [[0, 'desc']],
            columns: [
                 {title: "<?php echo 'mode' ?>", "class": "text-right w100"},   
                {title: "<?php echo 'Node ID'//app_lang('description') ?>"}, 
                {title: "<?php echo 'Brand' ?>", "class": "text-right w100"}, 
                 {title: "<?php echo 'Brand logo' ?>", "class": "text-right w100"},     
                {title: "<?php echo 'Device Type' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'model_number' ?>", "class": "text-right w100"}, 
                  {title: "<?php echo 'Product Name' ?>", "class": "text-right w100"}, 
                {title: "<?php echo 'Device' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'User ID' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'Internet' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'power' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'lattitude' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'longitude' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'signin_latitude' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'signin_longitude' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'node_latitude' ?>", "class": "text-right w100"},
                 {title: "<?php echo 'node_longitude' ?>", "class": "text-right w100"},
                  {title: "<?php echo 'Product Info' ?>", "class": "text-right w100"},
                   {title: "<?php echo 'APIs Info' ?>", "class": "text-right w100"},
                  
                 
            ],
        });
    });
    
   

</script>



 
