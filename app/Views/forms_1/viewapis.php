<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo 'APIS'; //echo $itemid; ?></h1>
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
            source: '<?php echo_uri("forms/list_data2/".$itemid) ?>',
            order: [[0, 'desc']],
           
            columns: [
                {title: "<?php echo 'API Name'//app_lang('title') ?> ", "class": "w20p"},
                {title: "<?php echo 'Cmd Command'//app_lang('description') ?>"},
                 {title: "<?php echo 'Cmd Data' ?>"},
                  {title: "<?php echo 'Voice Tag' ?>"},
                   {title: "<?php echo 'Voice Tagalong' ?>"},

                 {title: "<?php echo 'Linear Min' ?>"},
                 {title: "<?php echo 'Linear Max' ?>"},

                 {title: "<?php echo 'Binary Min' ?>"},

                 {title: "<?php echo 'Info Return Cmd' ?>"},
                 {title: "<?php echo 'Info Return Data' ?>"},

                  {title: "<?php echo 'Info Error Cmd' ?>"},
                 {title: "<?php echo 'Info Error Data' ?>"},

                  {title: "<?php echo 'Infocmd Command' ?>"},
                 {title: "<?php echo 'Infocmd Data' ?>"},
                  {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
              
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });
    });
</script>