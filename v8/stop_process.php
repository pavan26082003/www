<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $model_number = $data['model_number'] ?? '';

    if ($model_number) {
        // Stop backend processing logic for this model
        // Example: Stop a running process, clear session, update database, etc.
        file_put_contents("stopped_models.log", "$model_number stopped\n", FILE_APPEND);
        echo json_encode(["status" => "success", "message" => "Process stopped for $model_number"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Model number missing"]);
    }
}
?>
