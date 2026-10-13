<?php if ($page_view) { ?>
    <div id="page-content" class="page-wrapper clearfix">
        <div class="card clearfix">
        <?php } ?>

        <div class="card rounded-0">
            <div class="tab-title clearfix">
                <h4>End Device<?php //echo app_lang('files'); ?></h4>
                <div class="title-button-group">
                    <?php
                    if ($login_user->user_type == "staff" || ($login_user->user_type == "client" && get_setting("client_can_add_files"))) {
                        echo modal_anchor(get_uri("clients/file_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_files'), array("class" => "btn btn-default", "title" => app_lang('add_files'), "data-post-client_id" => $client_id));
                    }
                    ?>
                </div>
            </div>

            <div class="table-responsive">
                <table id="client-filesn" class="display" width="100%">            
                </table>
            </div>
        </div>

        <?php if ($page_view) { ?>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#client-filesn").appTable({
            source: '<?php echo_uri("user_details/enddevice_list_data/" . $client_id) ?>',
            order: [[0, "desc"]],
            columns: [
                
                {title: '<?php echo app_lang("id") ?>'},
                {title:'device name'}
,{title:'device type'},{title:'device var'},
{title:'Gateway id'},{title:'Device Id'},{title:'Room'},{title:'Gateway type'}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });
    });
</script>