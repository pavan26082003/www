
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

    
    
    <?php echo form_open(get_uri("items/efactprod"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
   <?php
$user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;
 
// Assuming $ui_data is part of $view_data passed to the view
$node_id = $ui_data['node_id'] ?? '';
$fan_properties = $ui_data['fan_properties'] ?? '';
$cooker_properties = $ui_data['cooker_properties'] ?? '';
$swpcp_1ph_properties = $ui_data['swpcp_1ph_properties'] ?? '';
$TZ_properties = $ui_data['TZ_properties'] ?? '';
$BR_properties = $ui_data['BR_properties'] ?? '';
$UMI_properties = $ui_data['UMI_properties'] ?? '';
$EC_properties = $ui_data['EC_properties'] ?? '';
$CM_properties = $ui_data['CM_properties'] ?? '';
$TU_properties = $ui_data['TU_properties'] ?? '';
$LC_properties = $ui_data['LC_properties'] ?? '';
$display_properties = $ui_data['display_properties'] ?? '';
$_SESSION['node_id'] = $node_id;
$_SESSION['fan_properties'] = $fan_properties;
$_SESSION['cooker_properties'] = $cooker_properties;
$_SESSION['swpcp_1ph_properties'] = $swpcp_1ph_properties;
$_SESSION['TZ_properties'] = $TZ_properties;
$_SESSION['BR_properties'] = $BR_properties;
$_SESSION['UMI_properties'] = $UMI_properties;
$_SESSION['EC_properties'] = $EC_properties;
$_SESSION['CM_properties'] = $CM_properties;
$_SESSION['TU_properties'] = $TU_properties;
$_SESSION['LC_properties'] = $LC_properties;
$_SESSION['display_properties'] = $display_properties;
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <h1>User & API Details</h1>
         
   <div>
    <?php foreach ($api_details as $key => $value): ?>
        <?php if ($key === 'fan_properties' || $key === 'cooker_properties' || $key === 'swpcp_1ph_properties'): ?>
            <div><strong><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</strong>
                <input type="checkbox" name="<?php echo $key; ?>" <?php echo ($value === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
            </div>
            <input type="hidden" name="<?php echo $key; ?>_value" value="<?php echo htmlspecialchars($value); ?>">
        <?php else: ?>
            <div><strong><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</strong> <?php echo htmlspecialchars($value); ?><br></div>
        <?php endif; ?>
    <?php endforeach; ?>

    
     <input type="hidden" name="fan_properties_value" value="<?php echo htmlspecialchars($fan_properties); ?>">  
      <input type="hidden" name="cooker_properties_value" value="<?php echo htmlspecialchars($cooker_properties); ?>">  
      <input type="hidden" name="swpcp_1ph_properties_value" value="<?php echo htmlspecialchars($swpcp_1ph_properties); ?>">  
</div>
             
            <input type="hidden" id="node_id" name="node_id" value="<?php echo htmlspecialchars($node_id); ?>">
          <div><strong>TZ Properties:</strong> 
    <input type="checkbox" name="TZ_properties" <?php echo ($TZ_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div><strong>BR Properties:</strong> 
    <input type="checkbox"  name="BR_properties" <?php echo ($BR_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div><strong>UMI Properties:</strong> 
    <input type="checkbox"  name="UMI_properties" <?php echo ($UMI_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div><strong>EC Properties:</strong> 
    <input type="checkbox" name="EC_properties"  <?php echo ($EC_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div><strong>CM Properties:</strong> 
    <input type="checkbox"  name="CM_properties" <?php echo ($CM_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div><strong>TU Properties:</strong> 
    <input type="checkbox"  name="TU_properties" <?php echo ($TU_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div><strong>LC Properties:</strong> 
    <input type="checkbox"  name="LC_properties" <?php echo ($LC_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<div>
    <strong>Display Properties:</strong> 
    <input type="checkbox"  name="display_properties" <?php echo ($display_properties === 'read,write') ? 'checked' : 'NULL="disabled"'; ?>>
</div>
<input style="margin-top: 20px; color: white; border: none; background-color: green;" type="submit" value="upload" name="submit">	
        </div>
        <div style="overflow: auto;display:flex;flex-direction:row">
            <table id="item-table2" class="display" cellspacing="0" width="0%"></table>
            <table id="item-table6" class="display" cellspacing="0" width="100%" style="font-size:16px;border:2px solid grey;"></table>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    var columnTitles = [];

    var columns = [];
    $.each(columnTitles, function (index, title, value) {
        var newRow = $("<tr></tr>");
        newRow.append($("<th></th>").text("<?php echo '"+title+":' ?>"));
        newRow.append($("<td></td>").text(value));
        columns.push(newRow);
    });

    $.each(columns, function (index, row) {
        $('#item-table2').append(row);
    });
});
</script>
<?php echo form_close(); ?>
