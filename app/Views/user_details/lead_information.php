<div class="bg-off-white p15 pt0">
    <span class="font-16"><?php echo app_lang("past_lead_information"); ?></span>

    <div class="mt5">
        <?php if ($client_info->created_at) { ?>
            <?php echo app_lang("lead_created_at") . ": " . format_to_date($client_info->created_at, false); ?>
        <?php } ?>
                <?php if ($client_info->last_online) { ?>
            <br /><?php echo app_lang("last_status") . ": " . $client_info->last_online; ?>
        <?php } ?>
        
    </div>
</div>