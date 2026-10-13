<div class="page-title clearfix no-border no-border-top-radius no-bg">
    <h1>
        <?php echo 'User Details' . " - " . $client_info->name ?>
        <span id="star-mark">
            <?php
            // if ($is_starred) {
            //     echo view('user_details/star/starred', array("client_id" => $client_info->id));
            // } else {
            //     echo view('user_details/star/not_starred', array("client_id" => $client_info->id));
          //  }
            ?>
        </span>
    </h1>
</div>

<div id="page-content" class="clearfix">


    <?php
   // if ($client_info->lead_status_id) {
        echo view("user_details/lead_information");
    //}
    ?>

    <div class="client-widget-section">
        <?php echo view("user_details/info_widgets/index"); ?>
    </div>

    <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs no-border-top-radius" role="tablist">
        <li><a  role="presentation" href="<?php echo_uri("user_details/rooms/" . $client_info->id); ?>" data-bs-target="#client-contacts"> Rooms List <?php //echo app_lang('contacts'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("user_details/company_info_tab/" . $client_info->id); ?>" data-bs-target="#client-info"> <?php echo app_lang('client_info'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("user_details/gateway/" . $client_info->id); ?>" data-bs-target="#client-projects">Gateway Device List <?php //echo app_lang('projects'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("user_details/enddevice/" . $client_info->id); ?>" data-bs-target="#client-invoices">End Device List <?php //echo app_lang('projects'); ?></a></li>

        
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="client-projects"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-files"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-contacts"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-invoices"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-payments"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-estimates"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-orders"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-estimate-requests"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-tickets"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-notes"></div>
        <div role="tabpanel" class="tab-pane" id="client-events" style="min-height: 300px"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-expenses"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "info") {
                $("[data-bs-target='#client-info']").trigger("click");
            } else if (tab === "projects") {
                $("[data-bs-target='#client-projects']").trigger("click");
            } else if (tab === "invoices") {
                $("[data-bs-target='#client-invoices']").trigger("click");
            } else if (tab === "payments") {
                $("[data-bs-target='#client-payments']").trigger("click");
            }
        }, 210);

    });
</script>
