<?php
// resolve_url.php

// Define base URLs
$base_urls = array(
    "guarantee_card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display",
    "app_video" => "https://efactor.tech/v8/api.php?action=app_video",
    "auto_test" => "https://efactor.tech/v8/auto_test.php"
);

// Get the action and params
$action = $_GET['action'] ?? null;
$params = $_GET['params'] ?? ''; // Encoded query string

if (array_key_exists($action, $base_urls)) {
    // Build the resolved URL
    $resolved_url = $base_urls[$action];
    if (!empty($params)) {
        $resolved_url .= "&" . $params; // Append query string
    }

    // Return the resolved URL
    header('Content-Type: application/json');
    echo json_encode(["url" => $resolved_url]);
} else {
    // Return error for invalid actions
    header('Content-Type: application/json', true, 400);
    echo json_encode(["error" => "Invalid action specified."]);
}
?>
