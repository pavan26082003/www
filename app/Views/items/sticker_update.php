<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker Management | Print & Label</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e9f0f5 0%, #d9e2ec 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        /* header area */
        .page-header {
            background: #1e2a3e;
            padding: 28px 32px;
            border-bottom: 5px solid #f4b942;
        }

        .page-header h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.3px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1:before {
            content: "🏷️";
            font-size: 32px;
        }

        /* product card */
        .info-panel {
            background: #fef9e8;
            margin: 24px 28px 12px 28px;
            padding: 20px 28px;
            border-radius: 24px;
            border-left: 8px solid #f4b942;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .info-panel h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1e2a3e;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-panel h3:after {
            content: "";
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, #f4b942, transparent);
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 8px;
        }

        .product-grid p {
            background: white;
            padding: 8px 18px;
            border-radius: 40px;
            font-size: 15px;
            font-weight: 500;
            color: #1f2d3d;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin: 0;
        }

        .product-grid strong {
            color: #c26b1a;
            margin-right: 8px;
        }

        /* action section title */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 28px 28px 12px 28px;
            color: #1e2a3e;
            border-left: 4px solid #f4b942;
            padding-left: 16px;
        }

        /* BUTTONS LAYOUT — individual separated groups */
        .buttons-wrapper {
            padding: 0 28px 28px 28px;
        }

        .button-group {
            background: #f8fafc;
            border-radius: 24px;
            padding: 20px 24px;
            margin-bottom: 28px;
            transition: all 0.2s;
            border: 1px solid #e2edf7;
        }

        .button-group:last-child {
            margin-bottom: 0;
        }

        .group-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            border-bottom: 2px dashed #cbdbe0;
            padding-bottom: 10px;
        }

        .group-header span {
            font-size: 24px;
        }

        .group-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1e2a3e;
            letter-spacing: -0.2px;
        }

        .badge {
            background: #eef2ff;
            color: #1f4a8c;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 30px;
            margin-left: 12px;
        }

        .button-row {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            align-items: center;
        }

        /* button styles */
        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            letter-spacing: 0.3px;
        }

        /* sticker form (blue) */
        .sticker-form-btn {
            background: #2c5f8a;
            color: white;
            border: 1px solid #1f4a6e;
        }
        .sticker-form-btn:hover {
            background: #1f4a6e;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px rgba(44, 95, 138, 0.4);
        }

        /* qr form (purple style) */
        .qr-form-btn {
            background: #6c5ce7;
            color: white;
            border: 1px solid #5a4ac7;
        }
        .qr-form-btn:hover {
            background: #5a49cf;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px #6c5ce7;
        }

        /* sticker print (green) */
        .sticker-print-btn {
            background: #2c7a47;
            color: white;
            border: 1px solid #1e5a36;
        }
        .sticker-print-btn:hover {
            background: #1e5f3a;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px #2c7a47;
        }

        /* display label print (orange/amber) - SEPARATE BUTTON */
        .display-label-btn {
            background: #e67e22;
            color: white;
            border: 1px solid #c95f0b;
        }
        .display-label-btn:hover {
            background: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px #e67e22;
        }

        /* extra subtle separator */
        .info-note {
            background: #f1f5f9;
            margin: 12px 28px 24px 28px;
            padding: 12px 20px;
            border-radius: 20px;
            font-size: 13px;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-note:before {
            content: "💡";
            font-size: 16px;
        }

        hr {
            margin: 10px 28px;
            border: none;
            border-top: 2px dotted #dce5ef;
        }

        @media (max-width: 650px) {
            .button-row {
                flex-direction: column;
                align-items: stretch;
            }
            .action-button {
                justify-content: center;
            }
            .info-panel {
                margin: 16px;
                padding: 16px;
            }
            .buttons-wrapper {
                padding: 0 20px 24px 20px;
            }
        }

        /* print preview subtle style */
        .action-button:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>Sticker Management</h1>
    </div>

    <!-- product info panel -->
    <div class="info-panel">
        <h3>📦 Product Details</h3>
        <div class="product-grid">
            <p><strong>Model Name:</strong> <?= htmlspecialchars($modelname ?? 'N/A') ?></p>
            <p><strong>Model No:</strong> <?= htmlspecialchars($modelno ?? 'N/A') ?></p>
            <p><strong>Product Name:</strong> <?= htmlspecialchars($category ?? 'N/A') ?></p>
            <!-- ID is hidden as per original but kept optional -->
        </div>
    </div>

    <div class="buttons-wrapper">
        <!-- ========= SECTION 1: STICKER GROUP (original sticker actions) ========= -->
        <div class="button-group">
            <div class="group-header">
                <span>🖨️</span>
                <h2>Sticker Operations <span class="badge">standard stickers</span></h2>
            </div>
            <div class="button-row">
                <!-- Sticker Form Button (original, kept functional) -->
                <a href="<?= site_url(
                    'items/sticker_form/' . $id . 
                    '?modelno=' . urlencode($modelno ?? '') . 
                    '&modelname=' . urlencode($modelname ?? '') . 
                    '&category=' . urlencode($category ?? '')
                ) ?>" 
                   class="action-button sticker-form-btn">
                    📄 Sticker Form
                </a>

                <!-- QR Form Button (original) -->
                <a href="<?= site_url("items/qr_form/{$id}?modelno=" . urlencode($modelno ?? '') . "&modelname=" . urlencode($modelname ?? '') . "&category=" . urlencode($category ?? '')) ?>" 
                   class="action-button qr-form-btn">
                   🔲 QR Form
                </a>

                <!-- STICKER PRINT BUTTON (standalone, for regular stickers) -->
                <a href="<?= site_url("items/sticker_print/{$id}?modelno=" . urlencode($modelno ?? '') . "&modelname=" . urlencode($modelname ?? '') . "&category=" . urlencode($category ?? '')) ?>" 
                   class="action-button sticker-print-btn">
                   🏷️ Sticker Print
                </a>
            </div>
        </div>

        <!-- ========= SECTION 2: DISPLAY LABEL SECTION (separate individual button) ========= -->
        <div class="button-group" style="background: #fff6ea; border-color: #ffe0b5;">
            <div class="group-header">
                <span>🖼️</span>
                <h2>Display Label <!--span class="badge" style="background:#ffe0b5; color:#b45f1b;">shelf & exhibit</span--></h2>
            </div>
            <div class="button-row">
                <!-- DISPLAY LABEL PRINT BUTTON - fully separated, no dropdown needed -->
                <a href="<?= site_url("items/sticker_display/{$id}?modelno=" . urlencode($modelno ?? '') . "&modelname=" . urlencode($modelname ?? '') . "&category=" . urlencode($category ?? '')) ?>" 
                   class="action-button display-label-btn">
                   🎯 Display Label Print
                </a>
                <!-- Optional extra note: you can also add a separate Display Label Form if needed, 
                     but based on requirement: just separate display label section with individual button -->
            </div>
            <!--div style="font-size: 12px; color: #b85c00; margin-top: 12px; margin-left: 8px;">
                ⚡ One-click print for display labels — perfect for retail / showcase
            </div-->
        </div>
    </div>

    <!-- extra info: quick tips -->
    <!--div class="info-note">
        <span>✨ <strong>No dropdown needed</strong> — Sticker Print and Display Label Print are now separate buttons. Click directly.</span>
    </div-->
    <hr>
    <div style="padding: 6px 28px 24px 28px; text-align: center; font-size: 12px; color: #6c757d;">
        Sticker Management System | Choose appropriate action
    </div>
</div>

<!-- optional JavaScript for any extra confirm or tracking (no dropdown needed, but ensures links are robust) -->
<script>
    (function() {
        // Ensure all dynamic links maintain correct parameters if needed (already static generated)
        // In case any URL encoding missing or for additional logging? not needed but safe.
        // Just to ensure no accidental default behavior, but we already built hrefs server-side.
        // Additional client-side check for missing id? (graceful)
        console.log("Sticker & Display Label UI — individual buttons ready");
        // optional: add click confirm for print actions? (optional but user friendly)
        const printButtons = document.querySelectorAll('.sticker-print-btn, .display-label-btn');
        printButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                // you can add optional confirmation, but we keep direct navigation for speed.
                // if needed: uncomment for confirmation dialog
                // if(!confirm(`Proceed to print ${btn.innerText.trim()}?`)) e.preventDefault();
            });
        });
    })();
</script>
</body>
</html>
