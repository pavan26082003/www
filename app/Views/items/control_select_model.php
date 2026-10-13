<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.css">
<style>
:root {
    --primary-color: #1a73e8;
    --background: #f0f5fa;
    --card-bg: #ffffff;
    --text-primary: #1a202c;
    --green-accent: #1b5e20;
    --green-bg: #e8f5e9;
    --pink-btn: #f06292;
    --hover-blue: #2196f3;
}


body {
    background-color: var(--background);
    color: var(--text-primary);
    font-family: 'Inter', 'Segoe UI', sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* TOP BAR */
.mdc-top-app-bar {
    background-color: #EBF4F5 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.mdc-top-app-bar__title {
    color: var(--primary-color) !important;
    font-weight: 800;
    font-size:26px;
}

/* CONTENT SPACING */
.content {
    max-width: 900px; /* Constrained for better vertical flow */
    margin: 0 auto;
    padding: 80px 24px 60px; 
    flex: 1;
}

/* SECTION HEADER */
.model-section h3 {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 20px; /* Decreased padding below title */
    color: #2d3748;
    text-align: center;
}

/* MODEL CARD - INCREASED WIDTH */
.model-item {
    background: #ffffff;
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 40px;
    width:800px;
    height:100%;
    box-shadow: 0 15px 35px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
}

/* HEADER - STACKED IN ROWS */
.model-header {
    background: var(--green-bg);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    border: 1px solid #c8e6c9;
}

.model-info {
    display: flex;
    flex-direction: column; /* Forces vertical stacking */
    gap: 15px;
}

.model-label {
    font-size: 14px; /* Increased label size */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--green-accent);
    display: inline-block;
    margin-right: 8px;
}

.model-value {
    font-family: 'Consolas', monospace;
    font-weight: 700;
    font-size: 18px; /* Increased ID font size */
    color: #0d47a1;
    word-break: break-all; /* Ensures long Node IDs don't break layout */
    display: inline-block;
}

/* VERTICAL BUTTONS - INCREASED WIDTH */
.page-buttons {
    display: flex;
    flex-direction: column; /* Vertical alignment */
    gap: 15px;
    align-items: center;
}

.mdc-button--raised {
    background-color: #F2D0D8 !important;
    height: 65px !important; /* Taller box */
    width: 100% !important; /* Wider box */
    max-width: 500px; /* Prevents it from being too wide on desktop */
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 10px rgba(240, 98, 146, 0.3) !important;
}

.mdc-button--raised:hover {
    background-color: #8FD5EB !important;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(33, 150, 243, 0.4) !important;
}

.mdc-button__label {
    text-transform: uppercase !important;
    font-weight: 500 !important;
    color: #42383B !important;
    font-size: 16px; /* Increased button font size */
}

/* FOOTER */
.footer {
    padding: 10px;
    background: #F5EFE6;
    color: #80307a;
    text-align: center;
}
</style>


</head>
<body>

<div class="mdc-top-app-bar">
    <div class="mdc-top-app-bar__row">
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <span class="mdc-top-app-bar__title">Efactor SmartThings</span>
        </section>
    </div>
</div>

<div class="mdc-top-app-bar-fixed-adjust content">
    <div class="mdc-typography">
        
        
       
        <?php if (!empty($modelsWithPages)): ?>
            <div class="model-section">
                <h3>Available Models</h3>
                <div class="model-list">
                    <?php foreach ($modelsWithPages as $modelNumber => $pages): ?>
                        <div class="model-item">
                            <div class="model-header">
                                <div class="model-info">
                                    <div>
                                        <span class="model-label">Model Number:</span>
                                        <span class="model-value"><?= $modelNumber ?></span>
                                    </div>
                                    <div>
                                        <span class="model-label">Node ID:</span>
                                        <span class="model-value"><?= $node_id ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="page-buttons">
                                <?php foreach ($pages as $pageNo): ?>
                                    <a class="mdc-button mdc-button--raised" 
                                       href="<?= site_url("items/controlpageFunction/{$modelNumber}/{$pageNo}?node_id={$node_id}") ?>">
                                        <span class="mdc-button__label">Page <?= $pageNo ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="footer">
    <p>Efactor SmartThings Control Panel © <?= date('Y') ?></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.js"></script>
<script>mdc.autoInit();</script>
</body>
</html> 
</div>
