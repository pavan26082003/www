
    <div class="table-responsive">
        <table id="contact-table" class="display" width="100%">            
        </table>
    </div>
 



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
            source: '<?php echo_uri("user_details/contacts_list_data/" . $client_id) ?>',
            order: [[1, "asc"]],
            columns: [
                {title: '', "class": "w50 text-center"},
                {title: "<?php echo app_lang("name") ?>", "class": "w150"},
              
                {title: "Gateway List<?php //echo app_lang("email") ?>", "class": "w20p"},
                {title: "<?php echo app_lang("phone") ?>", "class": "w100"},
                
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5], '<?php echo $custom_field_headers; ?>')
        });
    });
</script>