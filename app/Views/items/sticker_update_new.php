<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker Management</title>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at top left, #eef2ff, #f8fafc 60%);
        }

        /* ===== Main Container ===== */
        .container {
            max-width: 1050px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            padding: 40px;
            border-radius: 32px;
            box-shadow:
                0 25px 70px rgba(15, 23, 42, 0.08),
                0 8px 20px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        /* ===== Title ===== */
        h1 {
            font-size: 42px;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
            margin-bottom: 50px;
            margin-top: 0;
            letter-spacing: -0.6px;
            position: relative;
            background: linear-gradient(90deg, #0f172a, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container::before {
            content: "";
            position: absolute;
            top: 92px;
            left: 50%;
            transform: translateX(-50%);
            width: 390px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #6366f1, transparent);
        }


        /* ===== Product Info Card ===== */
        .info-panel {
            background: linear-gradient(145deg, #f8fafc, #ffffff);
            border-radius: 22px;
            padding: 35px 40px;
            margin-bottom: 50px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .info-panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.07);
        }

        .info-panel h3 {
            margin-bottom: 22px;
            font-size: 19px;
            font-weight: 600;
            color: #0f172a;
        }

        .info-panel p {
            margin: 12px 0;
            font-size: 15px;
            color: #475569;
        }

        /* ===== Dropdown Section ===== */
        .form-group {
            margin-bottom: 45px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
            display: block;
        }

        .form-group select {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            font-size: 15px;
            background: #ffffff;
            transition: all 0.25s ease;
        }

        .form-group select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.12);
        }

        /* ===== Buttons Layout ===== */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 28px;
            margin-top: 10px;
        }

        /* ===== Base Button ===== */
        .action-button {
            padding: 12px 30px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            min-width: 220px;
            text-align: center;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Sticker Form (Subtle Neutral) */
        .action-button:not(.qr-button):not(.print-button) {
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #e2e8f0;
        }

        .action-button:not(.qr-button):not(.print-button):hover {
            background: #e2e8f0;
            transform: translateY(-4px);
        }

        /* QR Form (Primary Gradient) */
        .qr-button {
            background: linear-gradient(135deg, #6366f1, #2563eb);
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.25);
        }

        .qr-button:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(99, 102, 241, 0.35);
        }

        /* Sticker Print (Dark Premium CTA) */
        .print-button {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
        }

        .print-button:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.35);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Sticker Management</h1>

        <div class="info-panel">
            <h3>Product Information</h3>
            <p><strong>Model Name:</strong> <?= htmlspecialchars($modelname) ?></p>
            <p><strong>Model No:</strong> <?= htmlspecialchars($modelno) ?></p>
            <p><strong>Product Name:</strong> <?= htmlspecialchars($category) ?></p>
        </div>

        <div class="button-container">

            <!-- Sticker Form -->
            <a href="<?= site_url(
                            'items/sticker_form/' . $id .
                                '?modelno=' . urlencode($modelno) .
                                '&modelname=' . urlencode($modelname) .
                                '&category=' . urlencode($category)
                        ) ?>"
                class="action-button">

                <!-- Bootstrap Icon: card-text -->
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14 4.5V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4.5h12z" />
                    <path d="M0 3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v1H0V3z" />
                    <path d="M3 7h10v1H3V7zm0 2h10v1H3V9zm0 2h7v1H3v-1z" />
                </svg>

                Sticker Form
            </a>


            <!-- QR Form -->
            <a href="<?= site_url(
                            'items/qr_form/' . $id .
                                '?modelno=' . urlencode($modelno) .
                                '&modelname=' . urlencode($modelname) .
                                '&category=' . urlencode($category)
                        ) ?>"
                class="action-button qr-button">

                <!-- Bootstrap Icon: qr-code -->
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16">
                    <path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" />
                    <path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" />
                    <path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" />
                    <path d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" />
                    <path d="M12 9h2V8h-2z" />
                </svg>

                QR Form
            </a>


            <!-- Label Designer -->
            <a href="<?= site_url(
                            'items/sticker_print/' . $id .
                                '?modelno=' . urlencode($modelno) .
                                '&modelname=' . urlencode($modelname) .
                                '&category=' . urlencode($category)
                        ) ?>"
                class="action-button print-button"
                id="stickerPrintBtn">

                <!-- Bootstrap Icon: pencil-square -->
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                </svg>
                Label Designer
            </a>

        </div>

</body>

</html>