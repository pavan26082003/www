<?php
/**
 * IoT dashboard card - include beside mapped card.
 * Usage: <?php echo view('dashboards/iot_card'); ?>
 */
?>
<div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile" onclick="location.href='<?= site_url('iot-dashboard'); ?>'">
        <div class="icon-circle" style="background: linear-gradient(135deg, #0f766e, #2563eb); color: #fff;">
            <i class="fas fa-house-signal"></i>
        </div>
        <div class="action-label">IOT</div>
    </div>
</div>
