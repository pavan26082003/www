<?php
require 'vendor/autoload.php';
use WebSocket\Client;

if (isset($_POST['connect'])) {
    try {
        $client = new Client("ws://efactor.tech:33060/");
        $client->send("hi");
       /* $client->send(json_encode([
            "url" => "action=node_details",
            "token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4",
            "startId" => ""
        ]));*/
        while (true) {
            $message = $client->receive();
            echo "Received from server: " . $message . "\n";
            flush();
            ob_flush();
            sleep(1);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<html>
    <body>
        <form method="post">
            <button name="connect">Connect to WebSocket</button>
        </form>
        <div id="status"></div>

        <script>
            const statusDiv = document.getElementById('status');

            document.forms[0].onsubmit = function() {
                statusDiv.innerText = 'Connected to WebSocket!';
                return true;
            };
        </script>
    </body>
</html>
