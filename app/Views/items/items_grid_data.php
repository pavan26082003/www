 <?php
if ($items) {
    foreach ($items as $item) {
        ?>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body cart-grid-item p0">
                    <div class="cart-grid-item-image">
                         <?php echo $item->image_url; ?> 
                    </div>
                    <div class="p15">
                        <small class="d-block mb-2">UID: <?php echo $item->node_id; ?></small>
                        <div class="text-wrap-ellipsis mt5 mb10"><strong>Device Type:</strong> <?php echo $item->device_type ?: '--'; ?></div>
                        <?php
                        $internet = isset($item->internet) ? strtolower($item->internet) : '';
                        $internetLabel = ($internet === 'false' || $internet === '0') ? '🔴 Offline' : '🟢 Online';
                        $internetClass = ($internet === 'false' || $internet === '0') ? 'text-danger' : 'text-success';
                        $rssiValue = (!empty($item->RSSI) && strtolower($item->RSSI) !== 'null') ? $item->RSSI : '--';
                        $fwValue = (!empty($item->wifi_version) && strtolower($item->wifi_version) !== 'null') ? $item->wifi_version : '--';
                        ?>
                        <div class="mb10 <?php echo $internetClass; ?>">
                            <strong><?php echo $internetLabel; ?></strong>
                        </div>
                        <div class="device-specs">
                            <div class="mb5">📶 <strong>RSSI:</strong> <?php echo $rssiValue; ?></div>
                            <div>⚙ <strong>FW:</strong> <?php echo $fwValue; ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer no-border text-center p10">
                    <a href="<?= site_url('items/device_details/' . $item->node_id) ?>" class="btn btn-xs btn-primary text-white p15 w100p">View Details</a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="text-center">
        <?php
        if ($result_remaining > 0) {
            echo ajax_anchor(get_uri("items/grid_view/" . $next_page_offset . "/20/" . $category_id . "/" . $search), app_lang("load_more"), array("class" => "btn btn-default mt15 mb15 round pl15 spinning-btn", "title" => app_lang("load_more"), "data-inline-loader" => "1", "data-closest-target" => "#items-container", "data-append" => true));
        }
        ?>
    </div>

    <?php
} else {
    ?>
    <div class="text-center box" style="height: 400px;">
        <div class="box-content" style="vertical-align: middle"> 
            <div class="mb15"><?php echo app_lang("item_empty_message"); ?></div>
            <span data-feather="frown" height="8rem" width="8rem" style="color:#d8d8d8"></span>
        </div>
    </div>  
<?php } ?>
