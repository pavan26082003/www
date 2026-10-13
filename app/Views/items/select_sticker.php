<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            padding: 20px;
            margin: 10px;
            font-size: 18px;
            min-width: 200px;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-container {
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <div class="header">
                <h1>Sticker Management System</h1>
                <p class="text-muted">Select an option below</p>
            </div>
            
            <div class="btn-container">
                <!-- Sticker Form Button -->
                <a href="<?php echo base_url('sticker/form'); ?>" class="btn btn-primary btn-custom">
                    <i class="bi bi-file-earmark-text"></i> Sticker Form
                </a>
                
                <!-- QR Form Button -->
                <a href="<?php echo base_url('qr/form'); ?>" class="btn btn-success btn-custom">
                    <i class="bi bi-qr-code"></i> QR Form
                </a>
                
                <!-- Sticker Print Button -->
                <a href="<?php echo base_url('sticker/print'); ?>" class="btn btn-warning btn-custom">
                    <i class="bi bi-printer"></i> Sticker Print
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>