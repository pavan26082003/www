<?php
/**
 * Mapped devices dashboard card — include beside App Settings card.
 * Usage: <?php echo view('dashboards/mapped_card'); ?>
 */
?>
<div class="col-xl-2 col-md-4 col-6 mb-4">
    <div class="action-tile" onclick="location.href='<?= site_url('items/mapped'); ?>'">
        <div class="icon-circle" style="background: linear-gradient(135deg, #778873, #A1BC98); color: #fff;">
            <i class="fas fa-sitemap"></i>
        </div>
        <div class="action-label">Mapped</div>
    </div>
</div>
