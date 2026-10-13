<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['c_file'])) {
    $uploadDir = 'uploads/';
    $allowedExtensions = ['c'];
    $file = $_FILES['c_file'];

    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading file. Code: " . $file['error']);
    }

    // Check file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExtensions)) {
        die("Error: Only .c files are allowed.");
    }

    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file to the uploads directory
    $uploadedFilePath = $uploadDir . basename($file['name']);
    if (!move_uploaded_file($file['tmp_name'], $uploadedFilePath)) {
        die("Error saving uploaded file.");
    }

    // Generate image from the uploaded .c file
    $outputImagePath = $uploadDir . pathinfo($file['name'], PATHINFO_FILENAME) . '.png';
    cFileToImage($uploadedFilePath, $outputImagePath);

    // Display the generated image
    echo "<h2>Conversion Successful!</h2>";
    echo "<img src='" . $outputImagePath . "' alt='Generated Code Image'>";
    echo "<p><a href='" . $outputImagePath . "' download>Download Image</a></p>";
} else {
    die("Invalid request.");
}

function cFileToImage($cFilePath, $outputImagePath) {
    // (Use the same function as in the previous example)
    // ... [Previous PHP code for `cFileToImage` here] ...
}
?>