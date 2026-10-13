<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

while (@ob_end_flush());
ob_implicit_flush(true);

echo "data: TEST STARTED......\n\n";
echo str_pad('', 4096) . "\n"; // Force immediate output
flush();

$node_ids = [1, 2, 3]; // Example node IDs

foreach ($node_ids as $node_id) {
    echo "data: Processing node ID: $node_id\n\n";
    echo str_pad('', 4096) . "\n";
    flush();
    sleep(10); // Reduce sleep time for testing
}

echo "data: TEST COMPLETED......\n\n";
flush();
?>
