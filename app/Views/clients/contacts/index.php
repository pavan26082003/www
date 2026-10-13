
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









<?php if (!$view_type == "list_view") { ?>
    <div class="card rounded-0">
        <div class="tab-title clearfix">
            <h4><?php echo app_lang('contacts'); ?></h4>
            <div class="title-button-group">
                <?php
                if ($can_edit_clients) {
                    echo modal_anchor(get_uri("clients/invitation_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_invitation'), array("class" => "btn btn-default", "title" => app_lang('send_invitation'), "data-post-client_id" => $client_id));

                    echo modal_anchor(get_uri("clients/add_new_contact_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_contact'), array("class" => "btn btn-default", "title" => app_lang('add_contact'), "data-post-client_id" => $client_id));
                }
                ?>
            </div>
        </div>

        <div class="table-responsive">
            <table id="contact-table" class="display" width="100%">            
            </table>
        </div>
    </div>
<?php } else { ?>
    <div class="table-responsive">
        <table id="contact-table" class="display" width="100%">            
        </table>
    </div>
    <?php
}
?>



<script type="text/javascript">
    $(document).ready(function () {

        var showCompanyName = true;
        if ("<?php echo $client_id ?>") {
            showCompanyName = false;
        }

        var showOptions = true;
        if (!"<?php echo $can_edit_clients; ?>") {
            showOptions = false;
        }

        $("#contact-table").appTable({
            source: '<?php echo_uri("user_details/list_data") ?>',
            order: [[1, "asc"]],
            columns: [
                {title: "<?php echo app_lang("id") ?>", "class": "w20p"},
                {title: "<?php echo app_lang("email") ?>", "class": "w100"},                
                {title: "<?php echo app_lang("phone") ?>", "class": "w20p"},
                {title: "<?php echo 'longitude' ?>", "class": "w100"},
                {title: "<?php echo 'longitude' ?>", "class": "w100"},
                {title: "<?php echo 'reg_time' ?>", "class": "w100"},

                          ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5], '<?php echo $custom_field_headers; ?>')
        });
    });
</script>