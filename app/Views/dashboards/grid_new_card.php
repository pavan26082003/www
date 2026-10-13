<?php
/**
 * Grid New dashboard card — include beside IoT card.
 * Usage: <?php echo view('dashboards/grid_new_card'); ?>
 */
?>
<div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile" onclick="location.href='<?= site_url('grid-dashboard'); ?>'">
        <div class="icon-circle" style="background:linear-gradient(135deg,#5b6cff,#7c3aed);color:#fff;">
            <i class="fas fa-border-all" aria-hidden="true"></i>
        </div>
        <div class="action-label">GRID NEW</div>
    </div>
</div>
