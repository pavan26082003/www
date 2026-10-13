<?php
require '/var/www/html/vendor/autoload.php';
use WebSocket\Client;

$websocketUrl = "ws://efactor.tech:33060/";

$client = new Client($websocketUrl);

// Send a message to the server
/*$client->send(json_encode([
    'url' => 'action=node_details',
    'token' => 'your_token',
    'startId' => 1,
]));*/

// Receive messages from the server
while (true) {
    $message = $client->receive();
    echo "Received from server: " . $message . "\n";
    flush(); // Flush the output buffer to ensure immediate output
    ob_flush(); // Flush the output buffer to ensure immediate output
    sleep(1); // Wait for 1 second before receiving the next message
}
?>






<!--script type="text/javascript">
    try {
        var ws = new WebSocket('ws://efactor.tech:33060');
        var output = document.getElementById('output');

        ws.onopen = function(event) {
            var connected = document.createElement("div");
            connected.textContent = 'Client connected';
            output.appendChild(connected);
        };

        ws.onmessage = function(event) {
            var jsonResponse = JSON.parse(event.data);
            var message = document.createElement("div");
            message.textContent = event.data;
            output.appendChild(message);
        };

        ws.onerror = function(event) {
            var error = document.createElement("div");
            error.textContent = 'Error: ' + event;
            output.appendChild(error);
        };

        ws.onclose = function(event) {
            var disconnected = document.createElement("div");
            disconnected.textContent = 'Client disconnected';
            output.appendChild(disconnected);
        };
    } catch (error) {
        var error = document.createElement("div");
        error.textContent = 'Error: ' + error;
        output.appendChild(error);
    }
</script-->
