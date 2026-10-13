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
// Start session and set user_id in the session
session_start();
$user_id = $login_user->id;
$_SESSION['user_id'] = $user_id;

$node_id = isset($node_id) ? $node_id : 'default_node_id';
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <h1>Products</h1>
        <div style="overflow: auto;">
            <table id="item-table2" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>


<?php
// Output the node_id value for debugging
//echo "<p>Node ID: $node_id</p>";
//$node_id='FFFFFFFFFFFFFFFF7EC2000000000200';

echo "<p>Node ID: $node_id</p>";

?>

<script type="text/javascript">
   $(document).ready(function () {
       // Assume node_id is available globally or passed to the script
       var node_id = '<?php echo $node_id; ?>'; 
       
       $("#item-table2").appTable({
           source: '<?php echo_uri("items/list_data_product") ?>',
           order: [[0, 'desc']],
           columns: [
               {title: "<?php echo 'Category' ?>"}, 
               {title: "<?php echo 'Module Type'?>", "class": "text-right w100"},
               {title: "<?php echo 'Vendor Name' ?>", "class": "text-right w100"},  
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
           params: {
               node_id: node_id // Pass the dynamic node_id here
           }
       });
   });
</script>