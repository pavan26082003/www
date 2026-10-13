<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Device Model</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.css">
    <style>
        .mdc-top-app-bar-fixed-adjust {
            padding-top: 64px; /* Adjust this value if your app bar height is different */
        }
        .content {
            margin: 2px; /* Add some spacing around the content */
        }
        .model-with-pages {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
			 margin-bottom: 20px; /* Increase spacing between each model */
        }
        .model-with-pages .mdc-button {
            margin-left: 20px; /* Spacing between buttons */
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

<!-- Use this div to adjust content below the app bar -->
<div class="mdc-top-app-bar-fixed-adjust content">
    <div class="mdc-typography">
        <h2>Select a Device Model and Subpage</h2>

        <?php if (!empty($modelsWithPages)): ?>
            <ul class="mdc-list">
            <?php foreach ($modelsWithPages as $modelNumber => $pages): ?>
                <li class="mdc-list-item model-with-pages">
                    <span>Model Number: <?= $modelNumber; ?></span>
                    <div>
                        <?php foreach ($pages as $pageNo): ?>
                            

                            <a class="mdc-button mdc-button--raised" href="<?= site_url("items/callpageFunction/{$modelNumber}/{$pageNo}") ?>">
    <span class="mdc-button__label">Page <?= $pageNo ?></span>
</a>



                        <?php endforeach; ?>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No models found for this user.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.js"></script>
<script>mdc.autoInit()</script>
</body>
</html>
