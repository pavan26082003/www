<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Device Model</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.css">

<style>
body{ background-color:#F9FBFD; margin:0; padding:0; font-family:Arial,sans-serif; }

/* TOP BAR */
.mdc-top-app-bar{ background:#292929; color:#FFF;height:60px; padding:10px; margin-bottom:1px; position:sticky; top:0; z-index:100; }
.app-bar-title{ font-size:25px; font-weight:600; }
.mdc-top-app-bar-fixed-adjust{ padding-top:10px; }
.content{ margin:10px; }

/* MODEL ROW */
.model-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:40px;
    margin:20px 0;
}
.model-info{ display:flex; gap:6px; font-weight:bold; }
.model-row img{ max-width:90px; }

/* PAGE BUTTON GRID */
.page-buttons-grid{
    display:grid;
    grid-template-columns:repeat(2, auto);
    gap:18px 30px;
    justify-content:center;
}

/* BUTTON COLORS */
.page-btn{ background:#FFA500 !important; color:#FFF !important; min-width:200px; }
.bottom-btn{ background:#1E90FF !important; color:#FFF !important; }
.extra-btn{ background:#8BF527 !important; color:#FFF !important; }

/* OUTER WHITE BOX */
.btn-box{
    background:#FFF;
    padding:12px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.18);
    transition:.25s;
    display:inline-flex;
}
.btn-box:hover{
    box-shadow:0 10px 26px rgba(0,0,0,0.3);
    transform:translateY(-2px);
}

/* CENTER WRAPPER FOR BOXES */
.center-box-wrapper{
    display:flex;
    justify-content:center;
    width:100%;
    margin-bottom:20px;
}

/* BUTTON ROWS */
.bottom-buttons,.extra-buttons{
    display:flex;
    justify-content:center;
    gap:25px;
    flex-wrap:wrap;
    margin:30px 0;
}

/* TEXT */
.label-text{ font-weight:bold; }
.value-text{ font-weight:bold; color:#00008B; }

.page-heading{
    font-size:22px;
    font-weight:600;
    background:#E3F2FD;
    padding:10px 14px;
    
    border-radius:4px;
    margin-top:0px;
    margin-bottom:10px;
    width:fit-content;
    position:sticky;
    
    
}
</style>
</head>

<body>

<div class="mdc-top-app-bar">
 <div class="mdc-top-app-bar__row">
  <section class="mdc-top-app-bar__section--align-start">
   <span class="mdc-top-app-bar__title app-bar-title">Efactor SmartThings</span>
  </section>
 </div>
</div>

<div class="mdc-top-app-bar-fixed-adjust content">

<h2 class="page-heading">Select a Device Model and Subpage</h2>

<?php if (!empty($modelsWithPages)): ?>
    <?php foreach ($modelsWithPages as $modelNumber => $pages): ?>

        <?php
        // Image logic
        if (empty($cat_image)) {
            $normalizedCategory = strtolower($category);
            switch ($normalizedCategory) {
                case 'fan':     $cat_image2 = 'fan.png'; break;
                case 'cooker':  $cat_image2 = 'cooker.png'; break;
                case 'swpcp':   $cat_image2 = 'swpcp.png'; break;
                default:        $cat_image2 = 'blank.jpeg'; break;
            }
        } else {
            $cat_image2 = $cat_image;
        }
        ?>

        <!-- MODEL ROW -->
        <div class="model-row">
            <div class="model-info">
                <span class="label-text">Model Number :</span>
                <span class="value-text"><?= $modelNumber ?></span>
            </div>
            <div class="model-info">
                <span class="label-text">Product Name :</span>
                <span class="value-text"><?= $category ?></span>
            </div>
            <img src="https://efactor.tech/files/home/<?= $cat_image2 ?>" alt="Product Image">
        </div>

        <!-- PAGE BUTTONS -->
        <div class="center-box-wrapper">
            <div class="btn-box" style="width:600px; padding:20px;">
                <div class="page-buttons-grid">
                    <?php foreach ($pages as $pageNo): ?>
                        <div class="btn-box">
                            <a class="mdc-button mdc-button--raised page-btn"
                               href="<?= site_url("items/controlpageFunction/$modelNumber/$pageNo?category=$category") ?>">
                               <?= $pageNo ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- BOTTOM BUTTONS -->
        <div class="center-box-wrapper">
            <div class="btn-box" style="width:800px; padding:10px;">
                <div class="bottom-buttons">
                    <div class="btn-box">
                        <a class="mdc-button mdc-button--raised bottom-btn"
                           href="https://efactor.tech/v8/api.php?action=guarantee_card_display&model_name=<?= $modelNumber ?>">
                           Guarantee Card
                        </a>
                    </div>
                    <div class="btn-box">
                        <a class="mdc-button mdc-button--raised bottom-btn"
                           href="https://efactor.tech/v8/api.php?action=app_video&model_name=<?= $modelNumber ?>">
                           Videos & Specs
                        </a>
                    </div>
                    <div class="btn-box">
                        <a class="mdc-button mdc-button--raised bottom-btn"
                           href="https://efactor.tech/v8/auto_test.php?model_number=<?= $modelNumber ?>">
                           Auto Test
                        </a>
                    </div>
                    <div class="btn-box">
                        <a class="mdc-button mdc-button--raised bottom-btn"
                           href="https://efactor.tech/v8/call_services.php?model_number=<?= $modelNumber ?>">
                           Call Services
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- EXTRA BUTTONS -->
        <div class="center-box-wrapper">
            <div class="btn-box" style="width:600px; padding:10px;">
                <div class="extra-buttons">
                    <?php foreach (['Operation','Config','Pairing','Test'] as $page): ?>
                        <div class="btn-box">
                            <a class="mdc-button mdc-button--raised extra-btn"
                               href="<?= site_url('items/modal_config?modelNumber='.$modelNumber.'&pageNo='.$page) ?>">
                               <?= $page ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
<?php endif; ?>

</div> <!-- content -->

<script src="https://cdn.jsdelivr.net/npm/material-components-web@latest/dist/material-components-web.min.js"></script>
<script>mdc.autoInit()</script>

</body>
</html>
