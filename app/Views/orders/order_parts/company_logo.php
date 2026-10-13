<?php
$order_logo = "order_logo";
if (!get_setting($order_logo)) {
    $order_logo = "invoice_logo";
}
?>

<!-- <img src="<?php echo get_file_from_setting($order_logo, get_setting('only_file_path')); ?>" /> -->

<img src="http://sensacore.karyin.in/files/system/_file60696511713b2-site-logo.png">